<?php
/*
 *
 * Copyright (c) 2017-2023. Torsten Lüders
 *
 * Part of the BPFW project. For documentation and support visit https://bpfw.org .
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 */

/** @noinspection PhpUnused */


class MaileroutboxModel extends BpfwModel
{

    const DELTYPE_TRASH = "trash";
    const DELTYPE_SENT = "sent";
    const DELTYPE_FAILED = "failed";


    var bool $translateDbModelLabels = true;
    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct();

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_INVALID; // open mailcreation instead
        $this->minUserrankForPrint = USERTYPE_INVALID;

        bpfw_add_filter(parent::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($this, "addCorrectBoxFilter"), 10, 5);

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "maileroutbox";
    }

    public function GetTitle(): string
    {
        return match (getorpost("box")) {
            "outbox" => __("Mails - outbox"),
            "sent" => __("Mails - sent"),
            "inbox" => __("Mails - inbox"),
            "deleted" => __("Mails - deleted"),
            default => __("Mails - unknown box"),
        };
    }

    /**
     * @throws Exception
     */
    function clearMailbox($deltype): string
    {

        switch ($deltype) {

            case MaileroutboxModel::DELTYPE_TRASH:

                //$this->DbSelect(" ");

                //$this->DbUpdate


                // Anhänge löschen ...


                $entries = $this->DbSelect(" status = 4");


                foreach ($entries as $entry) {

                    $key = $entry["mailerworkerId"];
                    //echo $key." ";
                    if ($entry["status"] == 4) {
                        $this->deleteMailWithAttachments($key);
                    }

                    //$key = $entry["mailerworkerId"];
                    //echo $key;
                    // $this->deleteAttachmentsOfMail($key);
                    //$this->DbDeleteByPrimaryKey($key);


                }

                return __("Deleted mails truncated");

            //return "Papierkorb gelöscht: ".$this->DbDeleteByWhere( " status = 4 ")." gelöscht";


            case MaileroutboxModel::DELTYPE_SENT:
                $count = bpfw_getDb()->makeQuery(" update maileroutbox set status = 4 where status = 3");
                return __("Moved to deleted");


            case MaileroutboxModel::DELTYPE_FAILED:
                $count = bpfw_getDb()->makeQuery("update maileroutbox set status = 4 where status = 2");
                return __("Moved to deleted");


            default:
                throw new Exception("invalid deltype $deltype");

        }

    }

    /**
     * @throws Exception
     */
    function deleteMailWithAttachments($key, $deleteMailOnSuccess = true, $temptable = false): bool
    {
        /*var_dump($key);
        echo getorpost("box");*/
        $entry = $this->DbSelectSingleOrNullByKey($key, $temptable);
        /* var_dump($entry);*/
        if (!is_array($entry)) {
            throw new Exception("key $key hat keinen Eintrag");
        }

        $attachments = $entry["attachments"];

        if (!empty($attachments)) {

            $attachments = json_decode($attachments, true);

            $success_delete = true;


            foreach ($attachments as $attachment) {

                if (isset($attachment["storeOnServer"]) && $attachment["storeOnServer"]) {

                    $path = $attachment["fullpath"];

                    $startfolder = OUTBOX_ATTACHMENTS; // check so that only attachments in the correct folder are deleted

                    if (str_starts_with($path, $startfolder)) {
                        $success_delete = $success_delete & (!file_exists($path) || unlink($path));
                    }

                }


            }

            if ($success_delete) {
                return parent::DeleteEntry($key, $temptable);
            }

        } else {

            return parent::DeleteEntry($key, $temptable);

        }

        // return parent::DeleteEntry($key, $temptable);

        // var_dump($entry);

        return false;

    }

    public function DeleteEntry(int $key, $temptable = false): bool
    {
        //echo "call 123";
        //echo bpfw_debug_string_backtrace();
        $entry = $this->DbSelectSingleOrNullByKey($key);

        if (!is_array($entry)) {
            return false;
        }

        if ($entry["status"] == 4) {
            //var_dump($entry);

            return $this->deleteMailWithAttachments($key, true, $temptable);


        } else {

            $statuschange = array($this->tryGetKeyName() => $key, "status" => 4);

            $this->DbUpdate($statuschange);

            return true;

        }

    }

    /**
     * Summary of createMail
     * @param string $subject
     * @param string $text
     * @param BpfwMailAddress[]|BpfwMailAddress $to
     * @param BpfwMailAddress[]|BpfwMailAddress|null $cc
     * @param BpfwMailAddress[]|BpfwMailAddress|null $bcc
     * @param BpfwMailAddress|null $from
     * @param BpfwMailAttachmentInterface[] $attachments
     * @param bool $sendDirectly
     * @param boolean $debug
     * @param bool $testmode
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    function createMail(string $subject, string $text, BpfwMailAddress|array $to, BpfwMailAddress|array $cc = null, BpfwMailAddress|array $bcc = null, BpfwMailAddress $from = null, array $attachments = array(), bool $sendDirectly = true, bool $debug = false, bool $testmode = false): bool
    {

        if (empty($from)) {
            $from = bpfw_getDefaultMailFrom();
        }


        // require_once("bpfw/mvc/models/mailerworkerModel.inc.php");

        $mailbox = bpfw_createModelByName("maileroutbox"); // new MailerworkerModel();

        /**
         * @var BpfwMailAttachmentInterface[] $attachments_saveable
         */
        $attachments_saveable = array();

        if (!empty($attachments)) {


            if (!is_array($attachments)) {
                $attachments = array($attachments);
            }

            if (is_array($attachments)) {

                foreach ($attachments as $attachment) {

                    $a_data = $attachment->getSavableData();

                    if (!empty($a_data)) {
                        $attachments_saveable[] = $a_data;
                    }

                }

            }

        }

        $values = array(
            "creatorUserId" => bpfw_getUserId(),
            //"userId"=>bpfw_getUserId(),
            //"customerId"=>-1,
            "to" => json_encode($to),
            "from" => json_encode($from),
            "bcc" => json_encode($bcc),
            "cc" => json_encode($cc),
            "status" => $testmode ? 3 : 0,
            "title" => $subject,
            "text" => $text,
            "box" => $testmode ? "sent" : "outbox",
            "created" => bpfw_TimestampToDateTimeString(time(), "Y-m-d H:i:s"),
            "attachments" => json_encode($attachments_saveable)
        );


        $success = true;

        if ($sendDirectly && !$testmode) {

            $success = bpfw_sendmail($subject, $text, $to, $cc, $bcc, $from, $attachments, $debug);

            if ($success) {
                $values["status"] = 3; // status sent
                $values["send"] = bpfw_TimestampToDateTimeString(time(), "Y-m-d H:i:s");
            }

        }

        $mailbox->DbInsert($values, null, true);


        // TODO: sendDirectly

        return $success;

    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    function sendMails($amount): bool|string
    {

        ob_start();

        echo "sende $amount mails" . "\r\n<br>";

        $emailsFound = $this->DbSelect(" status = 0 or status = 1 ", bpfw_getDb(), $amount);

        echo count($emailsFound) . " EMails gefunden, starte Sendevorgang " . "\r\n<br>";

        foreach ($emailsFound as $key => $email) {


            $text = $email["text"];

            if (empty($email["to"])) continue;

            $to = new BpfwMailAddress();
            $tojson = $email["to"];
            if (isJson($tojson)) {
                $tojson = json_decode($tojson);
                $to->set($tojson);
            }

            $from = null;
            $fromjson = $email["from"];
            if (isJson($fromjson)) {
                $from = new BpfwMailAddress();
                $fromjson = json_decode($fromjson);
                $from->set($fromjson);
            }

            echo "Sende Mail ID $key an " . $from->address . "\r\n<br>";


            $cc = null;
            $ccjson = $email["cc"];
            if (isJson($ccjson)) {
                $cc = new BpfwMailAddress();
                $ccjson = json_decode($ccjson);
                $cc->set($ccjson);
            }


            $bcc = null;
            $bccjson = $email["bcc"];
            if (isJson($bccjson)) {
                $bcc = new BpfwMailAddress();
                $bccjson = json_decode($bccjson);
                $bcc->set($bccjson);
            }

            // TODO: rcpt name? sender name?
            //$success = bpfw_sendmail($email["title"], $text, new BpfwMailAddress($email["rcpt"], $email["rcpt_name"]), new BpfwMailAddress($email["cc"]), new BpfwMailAddress($email["bcc"]), new BpfwMailAddress($email["sender"], $email["sender_name"]));
            $success = bpfw_sendmail($email["title"], $text, $to, $cc, $bcc, $from, array());

            $errortxt = "";
            if (!$success) {
                $errortxt = bpfw_get_mailer()->ErrorInfo;
                echo " -> Fehlgeschlagen: " . $errortxt . "\r\n<br>";

                $newstatus = $email["status"] + 1;
            } else {
                $newstatus = 3; // sent
                echo " -> Erfolg" . "\r\n<br>";
            }

            $newvals = array("mailerworkerId" => $key, "status" => $newstatus, "error" => $errortxt);

            if ($newstatus == 3) {
                $newvals["send"] = date("d.m.Y H:i:s");
            }

            $this->DbUpdate($newvals);

        }

        return ob_get_clean();

    }

    function addCorrectBoxFilter($where, $count, $offset, $sort, $join)
    {

        $status = "";

        if (empty($where)) $where = "1";

        if (empty(getorpost("box"))) {
            // return "0";
        } else {

            switch (getorpost("box")) {

                case "outbox":
                    $status = " < 3";
                    break;

                case "sent";
                    $status = " = 3";
                    break;

                case "deleted":
                    $status = " = 4 ";
                    break;

                default:
                    // return "0";
            }

        }


        if (!empty($status))
            $where = "(" . $where . ")" . " AND status $status";

        return $where;
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("mailerworkerId");

            $this->addTextField("title", "Subject", 'default', array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::DISABLED => true));

            $this->addComboBox("status", "Status", new EnumHandlerArray(array(0 => __("Waiting"), 1 => __("First try failed"), 2 => __("Failed permanently"), 3 => __("Sent"), 4 => __("Deleted"))), BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::POSITION => POSITION_RIGHT));

            $this->addMailAddressField("to", "To", array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));
            $this->addMailAddressField("from", "From", array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

            $this->addMailAddressField("cc", "Cc", array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));
            $this->addMailAddressField("bcc", "Bcc", array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

            $this->addTimestamp("created", "Created");
            $this->addTimestamp("send", "Sent", array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

            $this->addMailAttachmentField("attachments", "Attachments", array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::POSITION => POSITION_LEFT, FORMSETTING::DISABLED => true));

            $this->addTinyMceHtmlEditor("text", "Text", array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_LEFT, FORMSETTING::DISABLED => true));

            $this->addTextField("error", "Error", 512, array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

            $this->addTextField("box", "Box", 'default', array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

            $this->addComboBox("creatorUserId", "Creator", new EnumHandlerDb("user", "userId", 'CONCAT_WS(", ",lastname,firstname)', '', true), BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

        }

        return $this->dbModel;

    }


}
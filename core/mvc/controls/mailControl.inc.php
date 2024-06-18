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

/**
 * ajax commands - used by pdf generator
 *
 * used by pdf Generator
 *
 * @version 1.0
 * @author torst
 */
// used by pdf Generator
class MailControl extends DefaultControl
{


    function handleAjaxCommand(string $command): void
    {

        global $database;

        if ($command == "mail_send") {

            //var_dump($_POST);

            //var_dump($_FILES);

            /*
             * example attachment:
             *
             * 'attachment' =>
            array (size=5)
            'name' => string '210830_gamecity_guidelines_prototype_funding.pdf' (length=48)
            'type' => string 'application/pdf' (length=15)
            'tmp_name' => string 'D:\work\bpfw\srv\tmp\phpB6F7.tmp' (length=32)
            'error' => int 0
            'size' => int 1742195

            */


            // require_once("bpfw/mvc/models/mailerworkerModel.inc.php");

            // $mailbox = bpfw_createModelByName("maileroutbox"); // new MailerworkerModel();

            $text = $_POST["text"];
            $title = $_POST["title"];
            $rcpt = $_POST["rcpt"];

            $attachment = array();
            if (!empty($_FILES)) {
                if (!empty($_FILES["attachment"])) {
                    $file = $_FILES["attachment"];

                    if (!empty($file["tmp_name"]) && !empty($file["size"])) {

                        $attachment[] = new BpfwMailAttachment($file["tmp_name"], $file["name"], true, "base64", $file["type"]);
                    }
                }

            }

            if (!empty($_FILE)) {
                var_dump($_FILE);
            }

            $success = bpfw_sendmail_with_mailer_send_directly($title, $text, new BpfwMailAddress($rcpt), null, null, null, $attachment);

            /*


            $userdata = bpfw_getUser();
            // var_dump($userdata);


            $values = array(
                "creatorUserId"=>bpfw_getUserId(),
                "userId"=>bpfw_getUserId(),
                "customerId"=>-1,
                "sender"=>bpfw_loadSetting(SETTING_EMAIL_ABSENDER),
                "sender_name"=>bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME),
                "rcpt"=>$_POST["rcpt"],
                "bcc"=>"",
                "cc"=>"",
                "status"=>0,
                "title"=>$_POST["title"],
                "text"=>$text,
                "box"=>"outbox",
                "created"=>date("Y-m-d H:i:s")
                );*/

            /*
            $mailbox->DbInsert($values, null, true);*/

            if ($success) {
                echo "Mail an " . $_POST["rcpt"] . " versendet und gespeichert";
            } else {
                echo "Mail error: " . bpfw_get_last_mailerror();
            }


        } else {

            parent::handleAjaxCommand($command);
        }


    }


}
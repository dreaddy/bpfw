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

class MailerinboxModel extends BpfwModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct();

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_ADMIN;
    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "mailerinbox";
    }

    public function GetTitle(): string
    {
        return "Ausstehende Mails";
    }

    /**
     * @throws Exception
     */
    function printMails()
    {

        echo "empfange mails" . "\r\n<br>";


        $mailsettings = new stdClass();
        $mailsettings->server = bpfw_loadSetting(SETTING_MAILER_IMAP_HOST);
        $mailsettings->address = bpfw_loadSetting(SETTING_MAILER_IMAP_MAIL);
        $mailsettings->username = bpfw_loadSetting(SETTING_MAILER_IMAP_USERNAME);
        $mailsettings->port = bpfw_loadSetting(SETTING_MAILER_IMAP_PORT);
        $mailsettings->password = bpfw_loadSetting(SETTING_MAILER_IMAP_PASSWORD);
        $mailsettings->folder = bpfw_loadSetting(SETTING_MAILER_IMAP_FOLDER);
        $mailsettings->encryption = bpfw_loadSetting(SETTING_MAILER_IMAP_ENCRYPTION) == "ssl" ? "ssl" : "";


        $mbox = imap_open("$mailsettings->server:$mailsettings->port/$mailsettings->folder", $mailsettings->username, $mailsettings->password);

        echo "<h1>Postfächer</h1>\n";

        echo "<h1>Nachrichten in INBOX</h1>\n";

        $num = imap_num_msg($mbox);

        echo "<table style='border:3px solid black'>";

        $result = imap_fetch_overview($mbox, "1:$num");

        echo "<tr><th>uid</th><th>date</th><th>from</th><th>subject</th></tr>";

        foreach ($result as $overview) {

            echo "<tr>";
            echo "<td><a href='?showmailDetails=$overview->uid'>$overview->uid</a></td><td>$overview->date</td><td>" . $this->decodeTitle($overview->from) . "</td><td>" . $this->decodeTitle($overview->subject) . "</td>";
            echo "</tr>";

        }

        echo "</table>";

        imap_close($mbox);

    }

    public function decodeTitle($title)
    {
        return $title;
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

            $this->addComboBox("creatorUserId", "Ersteller", new EnumHandlerDb("user", "userId", 'CONCAT_WS(", ",lastname,firstname)', '', true), BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => true));

            $this->addComboBox("userId", "Berater", new EnumHandlerDb("user", "userId", 'CONCAT_WS(", ",lastname,firstname)', '', true), BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => false));
            $this->addComboBox("customerId", "Kunde", new EnumHandlerDb("customer", "customerId", 'CONCAT_WS(", ",lastname,firstname)', '', true), BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => false));

            $this->addTextField("sender", "sender", 'default', array(LISTSETTING::HIDDENONLIST => true));
            $this->addTextField("sender_name", "sender Name", 'default', array(LISTSETTING::HIDDENONLIST => true));

            $this->addTextField("rcpt", "rcpt", 'default', array(LISTSETTING::HIDDENONLIST => false));
            $this->addTextField("rcpt_name", "rcpt Name", 'default', array(LISTSETTING::HIDDENONLIST => false));

            $this->addTextField("cc", "cc", 'default', array(LISTSETTING::HIDDENONLIST => true));
            $this->addTextField("bcc", "bcc", 'default', array(LISTSETTING::HIDDENONLIST => true));

            $this->addTimestamp("created", "erstellt am");
            $this->addTimestamp("send", "gesendet am");

            $this->addTextField("title", "Betreff", 'default', array(LISTSETTING::HIDDENONLIST => true));
            $this->addTinyMceHtmlEditor("text", "Text", array(LISTSETTING::HIDDENONLIST => true));

            $this->addComboBox("status", "Status", new EnumHandlerArray(array(0 => "ausstehend", 1 => "1 Versuch fehlgeschlagen", 2 => "dauerhaft fehlgeschlagen", 3 => "gesendet")), BpfwDbFieldType::TYPE_INT, array(LISTSETTING::HIDDENONLIST => false));

            $this->addTextField("error", "Fehler", 512, array(LISTSETTING::HIDDENONLIST => true));

            $this->addTextField("box", "Box", 'default', array(LISTSETTING::HIDDENONLIST => true));


        }

        return $this->dbModel;

    }

}
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



use PHPMailer\PHPMailer\PHPMailer;

abstract class Settings2Model extends BpfwModel
{
    var bool $translateDbModelLabels=true;
    function __construct($values = null, $autocompleteVariables = true)
    {


        parent::__construct($values, $autocompleteVariables);

        $this->subtitle = "Einstellungen";
        $this->showdata = true;

//        define("PERMISSION_CHANGE_USER", "PERMISSION_CHANGE_USER");


        $this->minUserrankForEdit = USERTYPE_ADMIN;

        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_ADMIN;


        $this->sortColumn = 1;
        $this->sortOrder = "asc";

        $this->showCancelButton = false;
        $this->showSubmitButton = true;

        $this->ignoreOnSync = true;


        if (empty(bpfw_getDb()->makeSelect("select settingId from `" . SETTINGS_TABLENAME . "` where 1 limit 1"))) {

            $this->initializeSettings();

        }


    }

    function initializeSettings()
    {

        //$db = bpfw_getDb();

//        $db->makeInsert($this->submitvaluesFromDefault(), $this->)

        // var_dump($this->kvpFromDefaultValues());
        // die();
        try {
            if (empty(bpfw_getDb()->makeSelect("select settingId from `" . SETTINGS_TABLENAME . "` where 1 limit 1"))) {
                $this->DbInsert($this->kvpFromDefaultValues());
            }
        } catch (Exception $ex) {

        }


    }

    /**
     * @throws Exception
     */
    function kvpFromDefaultValues(): array
    {

        $retval = array();

        foreach ($this->getDbModel() as $k => $v) {

            $retval[$k] = $v->default;

        }


        return $retval;


    }

    /**
     * Summary of getContraints
     * @return DatabaseFKConstraint[]
     */
    public function getConstraints(): array
    {
        return array();
    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "settings2";
    }

    public function GetTitle(): string
    {
        return "Einstellungen 2";
    }

    /**
     * @throws Exception
     */
    function getTabName($editMode, $pageID): string
    {

        if ($pageID == 1) return __("General settings");
        if ($pageID == 2) return __("Mailsettings - send");
        if ($pageID == 3) return __("Mailsettings - receive");
        if ($pageID == 4) return __("Invoicedata default");
        if ($pageID == 5) return __("Invoicedata default");
        if ($pageID == 6) return __("Design");

        return parent::getTabName($editMode, $pageID);

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        $bpfw_defaultsettings = bpfw_defaultsettings();

        // $customerName = bpfw_getUser()["lastname"].", ".bpfw_getUser()["firstname"];

        if (empty($this->dbModel)) {

            $defaultlang = DEFAULT_LANGUAGE;

            $this->addPrimaryKey("settingId");

            /// $this->addTextField("SETTING_PLATFORM_SHARE_ADDRESS", "Adresse für Provision", "default",      array(FORMSETTING::PAGE=>1, LISTSETTING::HIDDENONLIST=>true, VIEWSETTING::DEFAULTVALUE=>$bpfw_defaultsettings[SETTING_PLATFORM_SHARE_ADDRESS] ) );


            $this->addTextField("SETTING_BACKUP_DIRECTORY", "Backup Folder", "default", array(FORMSETTING::PAGE => 1, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_BACKUP_DIRECTORY]));
            $this->addTextField("SETTING_EXTERNAL_CRONJOB_CALL_CODE", "External Cronjob Auth Key", "default", array(FORMSETTING::PAGE => 1, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_EXTERNAL_CRONJOB_CALL_CODE]));;
            $this->addTextField("SETTING_UNSUBSCRIBE_CALL_CODE", "Unsubscribe Auth Key", "default", array(FORMSETTING::PAGE => 1, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_EXTERNAL_CRONJOB_CALL_CODE]));

            //  $this->addTextFieldNumeric("SETTING_NEXT_INVOICE_NR", "nächste Rechnungsnummer", array(FORMSETTING::PAGE=>1, LISTSETTING::HIDDENONLIST=>true, VIEWSETTING::DEFAULTVALUE=>$bpfw_defaultsettings[SETTING_NEXT_INVOICE_NR] ) );
            //  $this->addTextFieldNumeric("SETTING_NEXT_CREDIT_NR", "nächste Rechnungsnummer", array(FORMSETTING::PAGE=>1, LISTSETTING::HIDDENONLIST=>true, VIEWSETTING::DEFAULTVALUE=>$bpfw_defaultsettings[SETTING_NEXT_CREDIT_NR] ) );

            $this->addTextField("SETTING_EMAIL_ABSENDER", "Sender EMail", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_EMAIL_ABSENDER]));
            $this->addTextField("SETTING_EMAIL_ABSENDER_NAME", "Sender Name", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_EMAIL_ABSENDER_NAME]));

            $this->addTextField("SETTING_EMAIL_BCC_EMPFAENGER", "BCC of all mails to", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_EMAIL_BCC_EMPFAENGER]));
            $this->addTextField("SETTING_EMAIL_INTERN", "Internal address for system mails", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_EMAIL_INTERN]));

            $this->addComboBox("SETTING_MAILER_TYPE", "Send mails with", array("phpmailer" => "phpmailer", "smtp" => "smtp"), BpfwDbFieldType::TYPE_STRING, array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_TYPE]));

            $this->addTextField("SETTING_MAILER_SMTP_HOST", "SMTP Host/Server", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_HOST]));
            $this->addTextFieldNumeric("SETTING_MAILER_SMTP_PORT", "SMTP Port", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_PORT]));
            $this->addComboBox("SETTING_MAILER_SMTP_AUTH", "SMTP Auth", array("ja" => "ja", "nein" => "nein"), BpfwDbFieldType::TYPE_STRING, array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_AUTH]));
            $this->addComboBox("SETTING_MAILER_SMTP_ENCRYPTION", "SMTP Encryption", array(PHPMailer::ENCRYPTION_STARTTLS => PHPMailer::ENCRYPTION_STARTTLS, PHPMailer::ENCRYPTION_SMTPS => PHPMailer::ENCRYPTION_SMTPS), BpfwDbFieldType::TYPE_STRING, array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_ENCRYPTION]));
            $this->addTextField("SETTING_MAILER_SMTP_USERNAME", "SMTP Username", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_USERNAME]));
            $this->addTextField("SETTING_MAILER_SMTP_PASSWORD", "SMTP Password", "default", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_PASSWORD]));

            $this->addTextField("SETTING_MAILER_IMAP_MAIL", "IMAP Mail address", "default", array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_IMAP_MAIL]));
            $this->addTextField("SETTING_MAILER_IMAP_USERNAME", "IMAP Username", "default", array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_IMAP_USERNAME]));
            $this->addTextFieldNumeric("SETTING_MAILER_IMAP_PORT", "IMAP Port", array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_IMAP_PASSWORD]));
            $this->addTextField("SETTING_MAILER_IMAP_PASSWORD", "IMAP Password", "default", array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_SMTP_ENCRYPTION]));
            $this->addTextField("SETTING_MAILER_IMAP_FOLDER", "IMAP Folder", "default", array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_IMAP_FOLDER]));
            $this->addComboBox("SETTING_MAILER_IMAP_ENCRYPTION", "IMAP Encryption", array("ssl" => "ssl", "keine" => "keine"), BpfwDbFieldType::TYPE_STRING, array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_IMAP_ENCRYPTION]));
            $this->addTextField("SETTING_MAILER_IMAP_HOST", "IMAP Host/Server", "default", array(FORMSETTING::PAGE => 3, LISTSETTING::HIDDENONLIST => true, VIEWSETTING::DEFAULTVALUE => $bpfw_defaultsettings[SETTING_MAILER_IMAP_HOST]));

            $this->addComboBox("SETTING_DEFAULT_LANGUAGE", "Default language", new EnumHandlerCallback("bpfw_get_active_languages"), BpfwDbFieldType::TYPE_STRING, array(VIEWSETTING::DEFAULTVALUE => $defaultlang, FORMSETTING::POSITION => POSITION_LEFT, LISTSETTING::HIDDENONLIST => true));


        }

        return $this->dbModel;

    }


}
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

require_once(BPFW_MVC_PATH . "models/settings2Model.inc.php");

class AppsettingsModel extends Settings2Model
{

    private $existingCheck = false;

    function __construct()
    {
        if(!$this->existingCheck) {
            $this->createTable(false, true, false);
            $this->existingCheck = true;
        }

        parent::__construct();

        $this->subtitle = "Einstellungen";
        $this->showdata = true;

//        define("PERMISSION_CHANGE_USER", "PERMISSION_CHANGE_USER");


        $this->minUserrankForEdit = USERTYPE_ADMIN;

        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_INVALID;


        $this->sortColumn = 1;
        $this->sortOrder = "asc";

        $this->showCancelButton = false;
        $this->showSubmitButton = true;

        $this->ignoreOnSync = false;

        $this->ignoreOnImportExport = true;

        $this->minUserrankForDuplicate = USERTYPE_INVALID;

        bpfw_add_filter(FILTER_FORMFIELD_VALIDATION, $this->GetTableName(), array($this, "validateForm"), 10, 8);

        $this->createTempTableIfNotExists();

        /*if(empty(bpfw_getDb()->makeSelect("select settingId from `".SETTINGS_TABLENAME."` where 1 limit 1"))){
            $this->DbInsert(array("settingId"=>1));
        }*/

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "appsettings";
    }


    /*
     *
     *  array(
    SETTING_NEXT_INVOICE_NR =>1,
    SETTING_NEXT_CREDIT_NR  =>1,
    SETTING_EMAIL_BCC_EMPFAENGER  => "verwaltung@erfolgspfad.de",
    SETTING_EMAIL_ZOHO_EMPFAENGER  => "tlueders@gmx.de",
    SETTING_EMAIL_ABSENDER  => "info@erfolgspfad.de",
    SETTING_EMAIL_ABSENDER_NAME  => 'Erfolgspfad GmbH',
    SETTING_EMAIL_INTERN => "info@erfolgspfad.de",
    SETTING_BACKUP_DIRECTORY => "d:\\ef_backups\\",
    SETTING_ZOHO_LINK => 'https://www.forms.zoho.eu/?id={event.eventId}&startdatum={event.start}&enddatum={event.end}',
    CREDIT_ERFOLGSPFADSERVICE_HRRATE_EUR=>75);
    */

    /**
     * Summary of getContraints
     * @return DatabaseFKConstraint[]
     */
    public function getConstraints(): array
    {

        return array();

    }

    /**
     * @throws Exception
     */
    function validateForm($errorsAlreadyFound, $type, $value, $hvalue, $formvalues, $key, $model, $component)
    {

        if ($type == "add") {


            if (!bpfw_error_hasErrors()) {

                if (!empty(bpfw_getDb()->makeSelect("select settingId from `" . SETTINGS_TABLENAME . "` where 1 limit 1"))) {
                    bpfw_error_add("Es existierten bereits Einstellungen, diese bitte editieren");
                }
            }

        }


        return $errorsAlreadyFound;

    }

    public function GetTitle(): string
    {
        return "Erfolgspfad Einstellungen";
    }

    function getTabName($editMode, $pageID): string
    {

        if ($pageID == 4) return "Automatische Mails";

        if ($pageID == 5) return "Sammelüberweisungen";

        return parent::getTabName($editMode, $pageID);

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        global $bpfw_defaultSettings;
        // $customerName = bpfw_getUser()["lastname"].", ".bpfw_getUser()["firstname"];

        if (empty($this->dbModel)) {

            parent::loadDbModel();

            // add settings as in settings2model here

        }

        return $this->dbModel;

    }


}
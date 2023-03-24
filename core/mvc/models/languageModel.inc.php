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
require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");


class LanguageModel extends BpfwModel
{

    private $existingCheck = false;

    var bool $translateDbModelLabels=true;
    function __construct()
    {
        parent::__construct();
        
        if(!$this->existingCheck) {
            $this->createTable(false, true, false);
            $this->existingCheck = true;
        }


        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_CONSULTANT;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;

        try {
            $this->checkInitRequired();
        } catch (Exception $ex) {
        }

    }
    /**
     * @throws Exception
     */
    function checkInitRequired()
    {


        $entries = $this->CountAllEntries();

        if (empty($entries)) {

            $this->AddEntry(array("languageId" => "en"));
            $this->AddEntry(array("languageId" => "de"));

            bpfw_createModelByName("translation")->initTranslation();

            bpfw_saveSetting(SETTING_DEFAULT_LANGUAGE, DEFAULT_LANGUAGE);

        }




    }

    function filterSelectEntriesWhereuser2($where, $count, $offset, $sort, $join)
    {

        if (!bpfw_isAdmin()) {
            $limitToUserid = bpfw_getUserId();
            $where = "(" . $where . ")" . " AND userId = '$limitToUserid'";
        }

        return $where; // was: join findme

    }

    function generateMailSignature($id): bool|string
    {

        ob_start();

        $filenameSignature = APP_MAIL_PATH . "common".DIRECTORY_SEPARATOR."signature.html";
        $signatureText = "";
        if (!file_exists($filenameSignature)) {
            echo "<b style='color:red'>" . $filenameSignature . " nicht gefunden!!!</b><br>";
        } else {
            //$signatureText = $this->insertVariables($variables, file_get_contents($filenameSignature));
            $signatureText = file_get_contents($filenameSignature);
        }

        echo $signatureText;

        return ob_get_clean();


    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "language";
    }

    public function GetTitle(): string
    {
        return "Sprachen";
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

            //$this->addPrimaryKey("languageId", BpfwDbFieldType::TYPE_STRING, 'text', array("required"=>true));

            $this->addComboBox("languageId", "Language", new EnumHandlerCallback("bpfw_getLanguagesArray"), new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, 80), array(VIEWSETTING::PRIMARYKEY => true, FORMSETTING::POSITION => POSITION_LEFT, LISTSETTING::HIDDENONLIST => false));


        }

        return $this->dbModel;


    }


}

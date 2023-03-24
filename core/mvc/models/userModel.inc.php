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


require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");


class UserModel extends BpfwModel
{

    var bool $translateDbModelLabels = true;
    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);

        $this->showdata = true;

        $this->minUserrankForEdit = USERTYPE_CONSULTANT;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;

        bpfw_add_filter(parent::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($this, "filterSelectEntriesWhereuser2"), 10, 5);


    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "user";
    }

    function filterSelectEntriesWhereuser2($where, $count, $offset, $sort, $join)
    {

        if (!bpfw_isAdmin()) {
            $limitToUserid = bpfw_getUserId();
            $where = "(" . $where . ")" . " AND userId = '$limitToUserid'";
        }

        return $where; // was: join findme

    }

    /**
     * Summary of getContraints
     * @return DatabaseFKConstraint[]
     * @throws Exception
     */
    public function getConstraints(): array
    {

        /*return array(
            new DatabaseFKConstraint("check_link_event_advisorids", "link_event_advisorids", "eventId", $this->getKeyName(), FK_CONTRAINT_RESTRICT),
            new DatabaseFKConstraint("check_userextrapermissions", "userextrapermissions", "userextrapermissionsId", $this->getKeyName(), FK_CONTRAINT_CASCADE),
        );*/

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

    public function GetTitle(): string
    {
        return "User";
    }

    public function CountAllEntries($where = '', $join = '', $temptable = false)
    {

        if (!bpfw_isAdmin()) {

            if (empty($where)) {
                $where = " 1";

            }

            $where .= " AND userId = " . bpfw_getUserId();

        }

        return parent::CountAllEntries($where, $join, $temptable);

    }


    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @noinspection DuplicatedCode
     */

    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("userId");

            $this->addField("username", "Login", BpfwDbFieldType::TYPE_STRING, "text", array("required" => true));
            $this->addField("password", "Password", BpfwDbFieldType::TYPE_STRING, "password");
            $this->addTextField("company", "Company");
            $this->addComboBox("salutation", "Salutation", new EnumHandlerCallback("bpfw_getSalutationArray"), BpfwDbFieldType::TYPE_STRING, array("hiddenOnList" => true));
            $this->addTextField("firstname", "First name");
            $this->addTextField("lastname", "Last name");
            $this->addTextField("email", "EMail", "default", array("hiddenOnList" => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::FORMFIELD_SUBTYPE => TextComponentSubtype::SUBTYPE_EMAIL));
            $this->addTextField("phone", "Telephone", "default", array("hiddenOnList" => true, FORMSETTING::POSITION => POSITION_RIGHT));
            $this->addTextField("mobile", "Mobile phone", "default", array("hiddenOnList" => true, FORMSETTING::POSITION => POSITION_RIGHT));
            $this->addTextField("street", "Street", "default", array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));
            $this->addTextField("zip", "Zip", 20, array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));
            $this->addTextField("city", "City", "default", array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));


            $this->addComboBox("language", "Default language", new EnumHandlerCallback("bpfw_getValidLanguagesArray"), BpfwDbFieldType::TYPE_STRING, array(VIEWSETTING::DEFAULTVALUE => DEFAULT_LANGUAGE, FORMSETTING::POSITION => POSITION_LEFT, LISTSETTING::HIDDENONLIST => true));


            $this->addCheckbox("newsletter_mail", "Receive massmails?", array(VIEWSETTING::DEFAULTVALUE => 1, FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));

            if (bpfw_isAdmin() || bpfw_creatingTables()) {
                 $this->addComboBox("type", "User rank", new EnumHandlerCallback("bpfw_getUsertypeArray"), BpfwDbFieldType::TYPE_INT, array(FORMSETTING::POSITION => POSITION_LEFT, "default" => USERTYPE_CONSULTANT));
            }


        }

        return $this->dbModel;


    }


}

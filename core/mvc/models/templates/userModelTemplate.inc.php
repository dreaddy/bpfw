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


class UserModelTemplate extends DbModelTemplate
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * Summary of getContraints
     * @return DatabaseFKConstraint[]
     * @throws Exception
     * @throws Exception
     */
    public function getConstraints(): array
    {

        return array(
            new DatabaseFKConstraint("check_link_event_advisorids", "link_event_advisorids", "eventId", $this->getKeyName(), FK_CONTRAINT_RESTRICT),
            new DatabaseFKConstraint("check_userextrapermissions", "userextrapermissions", "userextrapermissionsId", $this->getKeyName(), FK_CONTRAINT_CASCADE),
        );

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */

    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("userId");

            $this->addField("username", "Login", BpfwDbFieldType::TYPE_STRING, "text", array("required" => true));
            $this->addField("password", "Passwort", BpfwDbFieldType::TYPE_STRING, "password");
            $this->addTextField("company", "Firma");
            $this->addComboBox("salutation", "Anrede", new EnumHandlerCallback("bpfw_getSalutationArray"), BpfwDbFieldType::TYPE_STRING, array("hiddenOnList" => true));
            $this->addTextField("firstname", "Vorname");
            $this->addTextField("lastname", "Nachname");
            $this->addTextField("email", "E-Mail", "default", array("hiddenOnList" => true, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::FORMFIELD_SUBTYPE => TextComponentSubtype::SUBTYPE_EMAIL));
            $this->addTextField("phone", "Telefon", "default", array("hiddenOnList" => true, FORMSETTING::POSITION => POSITION_RIGHT));
            $this->addTextField("mobile", "Mobil", "default", array("hiddenOnList" => true, FORMSETTING::POSITION => POSITION_RIGHT));
            $this->addTextField("street", "Straße", "default", array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));
            $this->addTextField("zip", "PLZ", 20, array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));
            $this->addTextField("city", "Ort", "default", array(FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));


            $this->addCheckbox("newsletter_mail", "Rundmails erhalten?", array(VIEWSETTING::DEFAULTVALUE => 1, FORMSETTING::POSITION => POSITION_RIGHT, "hiddenOnList" => true));

            if (bpfw_isAdmin() || bpfw_creatingTables()) {
                $this->addComboBox("type", "Benutzerrang", new EnumHandlerCallback("bpfw_getUsertypeArray"), BpfwDbFieldType::TYPE_INT, array(FORMSETTING::POSITION => POSITION_LEFT, "default" => USERTYPE_CONSULTANT));
            }


        }

        return $this->dbModel;


    }


}

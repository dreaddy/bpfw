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


class ExamplecomplexModel extends BpfwModel
{

    public array $searchGroups = array();

    // enable if you want all the labels in loadDbModel to be translated
    // var bool $translateDbModelLabels = true;

    function __construct()
    {

        parent::__construct();

        // rights management with prededefined roles
        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_CONSULTANT;
        $this->minUserrankForDelete = USERTYPE_SUPERADMIN;

        // this way you can include inline scripts
        bpfw_register_js_inline("eventScripts", $this->getJs(), true);

        // define which column and order is default sort
        $this->sortColumn = 2;
        $this->sortOrder = "desc";

    }

    /**
     * must be the table name
     * @return string
     */
    public function GetTableName(): string
    {
        return "examplecomplex";
    }

    function getJs(): bool|string
    {
        ob_start();
        ?>

        <script>

            /**  get modules of selected event and display */

            jQuery(document).ready(function () {

                console.log("inline js test from examplecomplexmodel.inc.php");

            });

        </script>

        <?php

        return ob_get_clean();

    }

    /**
     * return the db model with all the fields
     * @return array
     * @throws Exception
     */
    protected function loadDbModel(): array
    {


        require_once bpfw_getIncludeFileForComponent("spoilercontainer");

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("examplecomplexId");

            $this->addTextField("test_text", "test_text_required", "default", array(LISTSETTING::MAXLENGTH => 20, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), FORMSETTING::REQUIRED => true));

            // add custom component with default custom attributes
            $this->addCustomComponent("test_customcomponent", "test_customcomponent", BpfwDbFieldType::TYPE_STRING, "redtextexample", array());

            // add custom component with custom attributes (color and color bg)
            $this->addCustomComponent("test_customcomponent_green", "test_customcomponent_green", BpfwDbFieldType::TYPE_STRING, "redtextexample", array("colortext_color" => "green", "colortext_bgcolor"=>"#007700") );


            // create multiselect combobox from database. This time, get Users
            $this->addComboBoxMultiselectLinktable("notify_changes", "Nofify on changes", new EnumHandlerDb("user", "userId", 'CONCAT_WS(", ",lastname,firstname)', ''), array(FORMSETTING::PAGE => 1, FORMSETTING::REQUIRED => false, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => false, LISTSETTING::AJAX_SORTABLE => false));

            $this->addComboBox("user", "Nofify on Responsible", new EnumHandlerDb("user", "userId", 'CONCAT_WS(", ",lastname,firstname)', ''), BpfwDbFieldType::TYPE_INT, array(FORMSETTING::PAGE => 1, FORMSETTING::REQUIRED => false, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => false, LISTSETTING::AJAX_SORTABLE => false));

            $this->addCheckbox("test_checkbox", "Checkbox test", array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_LEFT, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => !bpfw_isAdmin(), FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin()));

            // fill in php code at runtime. Good to calculate multiple fields
            $this->addTextFieldNumeric("test_numeric_1", "Number test", array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_RIGHT, VIEWSETTING::DEFAULTVALUE => 14, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addTextFieldNumeric("test_numeric_2", "Number test 2", array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_RIGHT, VIEWSETTING::DEFAULTVALUE => 7, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addCalculatedField("test_calculate", "test_calculate_sum_from_numeric", array(FORMSETTING::HIDDENONEDIT => true, FORMSETTING::HIDDENONADD => true));

            $this->addDatepicker("test_datepicker", "Datepicker test", array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addTimepicker("test_timepicker", "Timepicker test", array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addTextField("creditNr", "Gutschrift Nummer", "default", array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addTextFieldDecimal("test_decimal", "Decimal test", 2, array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addComboBox("payerId", "Kostenträger", new EnumHandlerCallback(array($this, "getComboboxValues")), BpfwDbFieldType::TYPE_INT, array(FORMSETTING::PAGE => 1, FORMSETTING::POSITION => POSITION_LEFT, FORMSETTING::REQUIRED => false, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), LISTSETTING::HIDDENONLIST => true));

            $this->addTimestamp("test_timestamp", "test_timestamp", array(VIEWSETTING::DEFAULTVALUE => "NOW()"));

            $this->addLabel("test_label", "just some label ....", array(FORMSETTING::POSITION => POSITION_RIGHT));

            // will do nothing by itself, but can be used in js files to do something
            $this->addButton("test_button", "test_button", "fa fa-edit", array(FORMSETTING::PAGE => 1, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), FORMSETTING::POSITION => POSITION_RIGHT));

            $externalValue  = $_SERVER['SERVER_PORT']; // auto saved in hidden field. Could also come from another table, be the ip address etc
            $this->addHiddenField("test_hidden", "test_hidden", BpfwDbFieldType::TYPE_STRING, $externalValue, array(VIEWSETTING::DEFAULTVALUE => 0, FORMSETTING::REQUIRED => false, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::HIDDENONEDIT => true));

            $this->addTextArea("test_textarea", "Textarea test", array(FORMSETTING::PAGE => 1, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), FORMSETTING::POSITION => POSITION_LEFT));
            $this->addTinyMceHtmlEditor("test_tinymce", "TinyMCE test", array(FORMSETTING::PAGE => 2, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONEDIT => !bpfw_isAdmin(), FORMSETTING::POSITION => POSITION_FULLWIDTH));

            $this->addModellistFiltered("test_modellistfiltered", "test_modellistfiltered", "examplecomplexsub", array( "firstname", "lastname", "anothervalue" ), array(LISTSETTING::AJAX_SORTABLE=>false, LISTSETTING::LABEL_LIST=>"test modelllist filtered", FORMSETTING::PAGE=>3, LISTSETTING::HIDDENONLIST=> true));

        }

        return $this->dbModel;

    }

    public function getComboboxValues(): array
    {
        return array(1=>"val1", 2=>"val2");
    }

    /**
     * @param array $data
     * @param bool $ignoreRightsManagement
     * @param bool $ignoreConversion
     * @param bool $temptable
     * @return void
     * @throws Exception
     */
    public function EditEntry(array $data, bool $ignoreRightsManagement = false, bool $ignoreConversion = false, bool $temptable = false): void
    {
        // manipulate submitvalues here
        parent::EditEntry($data, $ignoreRightsManagement, $ignoreConversion, $temptable);
    }

    /**
     * @param array|DbSubmitValue[] $data keyvalue or data array or DbModelEntry or array of values
     * @throws Exception
     */
    public function AddEntry(array $data, bool $ignoreRightsManagement = false, bool $ignoreConversion = false, bool $temptable = false): ?int
    {
        // manipulate submitvalues here
        return parent::AddEntry($data, $ignoreRightsManagement, $ignoreConversion, $temptable);
    }

    public function GetEntry(int|string|null $key, string $where = " 1", int $count = -1, int $offset = 0, string $join = "", bool $temptable = false, bool $disablePermissionCheck = false): ?DbModelEntry
    {

        $data = parent::GetEntry($key, $where, $count, $offset, $join, $temptable, $disablePermissionCheck);

        if (!is_numeric($data->test_numeric_1) || !is_numeric($data->test_numeric_2)) {
            $data->test_calculate = " - ";
        } else {
            $data->test_calculate = $data->test_numeric_1 + $data->test_numeric_2;
        }

        return $data;

    }

    public function GetEntries(string $where = " 1", int $count = -1, int $offset = 0, array $sort = array(), string $join = "", bool $temptable = false): array
    {

        $allDataOfPage = parent::GetEntries($where, $count, $offset, $sort, $join, $temptable);

        foreach ($allDataOfPage as $key => $data) {
            if (!is_numeric($data->test_numeric_1) || !is_numeric($data->test_numeric_2)) {
                $data->test_calculate = " - ";
            } else {
                $data->test_calculate = $data->test_numeric_1 + $data->test_numeric_2;
            }
        }

        return $allDataOfPage;

    }

    public function GetTitle(): string
    {
        return "A bit more complex example";
    }

    function getTabName($editMode, $pageID): string
    {

        if ($pageID == 1) return "Basic";
        if ($pageID == 2) return "Textareas";
        if ($pageID == 3) return "Subtables";

        return parent::getTabName($editMode, $pageID);

    }

    /**
     * Define your constraints here. They have to be in the parent model. So if your uses has addresses, define in user etc.
     * @return DatabaseFKConstraint[]
     */
    public function getConstraints(): array
    {
        return array(
            // FK_CONTRAINT_RESTRICT: show an error dialog if you want to delete an entry that still has uploaded attachments
            new DatabaseFKConstraint("preventdeleteifstillattachments", "exampleattachments", "exampleattachmentsId", $this->getKeyName(), FK_CONTRAINT_RESTRICT),
            //  FK_CONTRAINT_CASCADE: delete entries of submodel database when deleted
            new DatabaseFKConstraint("deleteSubIfParentDeleted", "examplecomplexsub", "examplecomplexsubId", $this->getKeyName(), FK_CONTRAINT_CASCADE),
        );

    }

}

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
 * base Component where all used components are derived from. Contains basic methods for processing/displaying values in lists and as form fields
 *
 *
 * @version 1.0
 * @author torst
 */
class DefaultComponent
{

    var bool $showOnList = true;
    var bool $defaultSortable = true;
    var bool $showOnEdit = true;
    var bool $showOnAdd = true;
    var bool $showOnDuplicate = true;
    var string $name = "default";
    var ComponentHandler $componentHandler;

    function __construct($name, $componentHandler)
    {

        $this->name = $name;
        $this->componentHandler = $componentHandler;

        $css = $this->getCss();
        $jsh = $this->getHeaderJs();
        $jsf = $this->getFooterJs();

        if (!empty($jsf)) {
            bpfw_register_js_inline($name . "ComponentJSFooter", $jsf, true);
        }

        if (!empty($jsh)) {
            bpfw_register_js_inline($name . "ComponentJSHeader", $jsh);
        }

        if (!empty($css)) {
            bpfw_register_css_inline($name . "ComponentCSS", $css);
        }

    }

    function getCss(): string
    {
        return "";
    }

    function getHeaderJs(): string
    {
        return "";
    }

    function getFooterJs(): string
    {
        return "";
    }

    /**
     * Summary of validateValue
     * @param array $errorsAlreadyFound
     * @param string $type add or edit
     * @param mixed $value
     * @param BpfwModelFormfield $headerValue
     * @param DbSubmitValue[] $formValues
     * @param string $key
     * @param BpfwModel $model
     * @return array
     * @throws Exception
     * @throws Exception
     */
    function validateValue(array $errorsAlreadyFound, string $type, mixed $value, BpfwModelFormfield $headerValue, array $formValues, string $key, BpfwModel $model): array
    {

        $params = array($errorsAlreadyFound, $type, $value, $headerValue, $formValues, $key, $model, $this);

        try {
            $errorsAlreadyFound = bpfw_do_filter(FILTER_FORMFIELD_VALIDATION, $model->GetTableName(), $params, $model); // must always be the real tableName, NOT the temptable tableName
        } catch (Exception $ex) {
            $errorsAlreadyFound[] = "Exception when using the validation filter";
        }

        if (empty($value) && $headerValue->required) {
            $errorsAlreadyFound[] = __("Input must not be empty");
        }

        return $errorsAlreadyFound;

    }

    /**
     * Summary of addCssClass
     * @param BpfwModelFormfield $headerValue
     * @param string $newClassName
     */
    function addCssClass(BpfwModelFormfield $headerValue, string $newClassName): void
    {


        if (!empty($headerValue->xtraFormClass)) {

            $cssClasses = explode(" ", $headerValue->xtraFormClass);
            if (in_array($newClassName, $cssClasses)) {
                return;
            }

            $headerValue->xtraFormClass .= " ";

        }

        $headerValue->xtraFormClass .= $newClassName;

    }

    /**
     * redraw after ajax added some components. No Script Tag!
     * @return string
     */
    function getRedrawJs(): string
    {
        return "";
    }

    /**
     * redraw after ajax added some components. No Script Tag!
     * @return string
     */
    function getCustomAttributes(): array
    {
        return array("test123" => 123); // array("customAttribute"=>123)
    }

    public function getDataHtml($data): string
    {

        $htmlData = " ";

        if (is_array($data)) {

            foreach ($data as $k => $v) {
                $htmlData = trim($htmlData) . " data-$k = '$v'";
            }
        }

        return $htmlData . " ";
    }

    function handleValueBeforeSave($value)
    {
        return $value;
    }

    /**
     * get html of displaying this element as a label (for example in a list/datatable)
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    public function GetDisplayLabelHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if ($this->conditionCheck($value, $fieldDbModel, $rowKey, $model)) {
            if ($fieldDbModel->display == "text" || $fieldDbModel->display == "textarea") { // no html content
                return htmlentities($this->displayAsLabel($value, $fieldName, $fieldDbModel, $rowKey, $model));
            }
            return $this->displayAsLabel($value, $fieldName, $fieldDbModel, $rowKey, $model);
        }
        return "";
    }

    /**
     * Checks the conditions that can be defined in the model
     * @param mixed $originalValue
     * @param BpfwModelFormfield $fieldDbModel
     * @param null|int|string $primaryKey
     * @param BpfwModel $model
     * @return boolean
     * @throws Exception
     */
    public function conditionCheck(mixed $originalValue, BpfwModelFormfield $fieldDbModel, null|int|string $primaryKey, BpfwModel $model): bool
    {

        if (isset($fieldDbModel->condition)) {
            if ($primaryKey === null) { // is adding or duplicate
                foreach ($fieldDbModel->condition as $key => $value) {
                    if ($value != 0) {
                        return false;
                    }
                }
            } else {
                $dbModelEntry = $model->GetEntry($primaryKey);
                foreach ($fieldDbModel->condition as $key => $value) {
                    if (isset($dbModelEntry->$key)) {
                        if ($dbModelEntry->$key != $value) {
                            return false;
                        }
                    } else {
                        throw new Exception("non existing condition Item: " . $key . " in " . $model->GetTableName());
                    }
                }
            }
        }

        return true;

    }

    /**
     * Summary of displayAsLabel
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if ($value == null) $value = "";

        if ($fieldDbModel->textfield_type == "date") {
            $value = bpfw_mysqlDateStringToFormat($value);
        }

        // combobox, multiselect combobox etc.
        if (!empty($fieldDbModel->entries)) {
            $entries = $fieldDbModel->entries->getValueArray();

            if (!is_array($value)) {
                return $entries[$value] ?? "-";
            } else {
                $first = true;
                $htmlArrayContent = "";
                foreach ($value as $k => $v) {

                    if (isset($entries[$k])) {
                        if (!$first) {
                            $htmlArrayContent .= ",";

                        }

                        $htmlArrayContent .= $entries[$k];

                        $first = false;
                    }
                }
                $value = $htmlArrayContent;
            }
        }

        if (isset($fieldDbModel->list_maxlength) && $fieldDbModel->list_maxlength < strlen($value)) {
            $value = mb_substr($value, 0, $fieldDbModel->list_maxlength);
        }

        if (!empty($fieldDbModel->currency_symbol)) {
            $value .= " " . $fieldDbModel->currency_symbol;
        }

        return $value;

    }

    /**
     * get html of displaying this element as a label (for example in a list/datatable)
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    public function GetDisplayFormattedPlainValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): mixed
    {
        if ($this->conditionCheck($value, $fieldDbModel, $rowKey, $model)) {
            return $this->displayAsLabel($value, $fieldName, $fieldDbModel, $rowKey, $model);
        }
        return "";
    }

    /**
     * get html of add element form html
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    public function GetDisplayAddHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {
        if ($this->conditionCheck($value, $fieldDbModel, null, $model)) {
            return $this->displayAsAdd($value, $fieldName, $fieldDbModel, $model);
        }
        return "";
    }

    /**
     * display the add form field. Internal function not wrapped in html
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {
        return $this->displayAsEdit($value, $fieldName, $fieldDbModel, $model, 0);
    }

    /**
     * display the edit form field. Internal function not wrapped in html
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param int|string $rowKey
     * @return string
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, int|string $rowKey): string
    {
        // echo "defaultComponent";
        return $value;
    }

    /**
     * Get Html of edit field form element
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     * @throws Exception
     */
    public function GetDisplayEditHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model, int|string $rowKey): string
    {
        if ($this->conditionCheck($value, $fieldDbModel, $rowKey, $model)) {
            return $this->displayAsEdit($value, $fieldName, $fieldDbModel, $model, $rowKey);
        }
        return "";
    }

    /**
     * Get Html of duplicate field form element
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     */
    public function GetDisplayDuplicateHtml(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model, int|string $rowKey): string
    {
        return $this->displayAsDuplicate($value, $fieldName, $fieldDbModel, $model, $rowKey);
    }

    /**
     * display the duplicate form field. Internal function not wrapped in html
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param int|string $rowKey
     * @return string
     */
    protected function displayAsDuplicate(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model, int|string $rowKey): string
    {
        if (!$fieldDbModel->primaryKey) {
            return $this->displayAsAdd($value, $fieldName, $fieldDbModel, $model);
        }
        return "";
    }

    /**
     * define a custom sort Order
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param null|int|string $rowKey
     * @param BpfwModel $model
     * @return string|int|null
     */
    public function getSortValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, null|int|string $rowKey, BpfwModel $model): string|int|null
    {
        return null;
    }

    public function getMysqlValue($value)
    {
        return $value;
    }

    /**
     * manipulate value before insert/update operations
     * @param mixed $value
     * @return mixed
     */
    public function preProcessBeforeSql(mixed $value): mixed
    {
        return $value;
    }


}
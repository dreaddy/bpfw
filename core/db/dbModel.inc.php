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
 * GroupchatGroup short summary.
 *
 * GroupchatGroup description.
 *
 * @version 1.0
 * @author torst
 */


const IMPORT_MODE_MERGE_ADD = 'merge_add';
const IMPORT_MODE_MERGE_OVERRIDE = 'merge_override';
const IMPORT_MODE_DELETE_OVERRIDE = 'delete_override';

abstract class DbModel
{

    const FILTER_GET_DBMODEL = "FILTER_GET_DBMODEL";
    const FILTER_EXTRAFIELDS = "FILTER_EXTRAFIELDS";


    public array $data;
    //public bool $translateAllFields = false;
    public bool $translateDbModelLabels = false;
    public string $translation_domain = "default";
    public string $language_used_in_code = "en";
    public bool $saveTimestampsInTable = false;
    /**
     * create and update an extra column with all searchable fields
     * @var mixed
     */
    public bool $createSearchIndex = false;
    var array $dbModel = array();
    var bool $ignoreConversion = false;
    private string $cachedWhere = "";
    private int $cachedcount = -1;
    private int $cachedoffset = -1;
    private string $cachedjoin = "";
    private string $cachedtemptable = "";
    private array $cachedsort = array();
    private array $templatesUsed = array();
    /**
     * Summary of $objectsCache
     * @var DbModelEntry[]
     */
    private array $objectsCache = array();
    private mixed $_dbmodelCache = null;

    /**
     * Summary of __construct
     */
    public function __construct()
    {

    }

    public function clearCache(): void
    {
        $this->cachedWhere = "deleted";
        $this->objectsCache = array();
    }

    /**
     * @throws Exception
     */
    function importData($values, $mode): void
    {
        $this->ignoreConversion = true;
        if ($mode == IMPORT_MODE_DELETE_OVERRIDE) {
            $this->DbDeleteByWhere(" 1");
        }

        if ($mode == IMPORT_MODE_MERGE_ADD) {

            foreach ($values as $keyValue => $value) {
                $this->DbInsertOrIgnore($value, bpfw_getDb(), $this->ignoreConversion, true);
            }

        } else {

            foreach ($values as $keyValue => $value) {
                $this->DbInsertOrUpdate($value, bpfw_getDb(), $this->ignoreConversion, true);
            }

        }

        $this->ignoreConversion = false;

    }

    /**
     * Datenbankeintrag anhand von WHERE löschen
     * @param $where
     * @param null $db (optional)
     * @param bool $temptable
     * @return mixed
     * @throws Exception
     */
    public function DbDeleteByWhere($where, $db = null, bool $temptable = false): mixed
    {


        if (empty($db)) {
            $db = $this->getDb();
        }

        $tablename = $this->GetTableName();

        if ($temptable) {
            $tablename = $this->GetTempTableName();
        }

        return $db->makeDeleteByWhere($tablename, $where);

    }

    /**
     * returns the database
     * @return database
     * @throws Exception
     */
    public function getDb(): database
    {
        return bpfw_getDb();
    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function getTablename() : string{
        return (strtolower(rtrim(get_class($this), "Model")));
    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTempTableName(): string
    {

        $tablename = $this->GetTableName();

        return "ztemptable_" . $tablename;

    }

    /**
     * Add new database entry with keyvalue array oder a fitting object - nothing is done if the key already exists
     * @param mixed $keyValueArrayorObject
     * @param null $db (optional)
     * @param bool $ignoreConversion
     * @param bool $ignoreRequiredFields
     * @param bool $temptable
     * @return int (neue autoincrement id)
     * @throws Exception
     */
    public function DbInsertOrIgnore(mixed $keyValueArrayorObject, $db = null, bool $ignoreConversion = false, bool $ignoreRequiredFields = false, bool $temptable = false): int
    {

        // TODO: test

        $tn = $this->GetTableName();

        if ($temptable) {
            $tn = $this->GetTempTableName();
        }

        $kvp = $keyValueArrayorObject;

        if (is_object($keyValueArrayorObject)) {
            $kvp = $this->GetKeyValueArrayFromObject($keyValueArrayorObject, false);
        }

        if (!$this->checkUnique($kvp, $temptable)) return -1;
        if (empty($db)) {
            $db = $this->getDb();
        }

        $model = $this->CreateArrayWithDataAndFieldinfoFromPlainKvpArray($kvp, true, $ignoreRequiredFields);

        try {
            // Insert

            if (!$ignoreConversion) {
                $model = $this->prepareForStrictMode($model);
            }
            foreach ($model as $dbskey => $dbsubmitvalue) {
                $dbsubmitvalue->data = bpfw_getComponentHandler()->getComponent($dbsubmitvalue->getDbField()->display)->preProcessBeforeSql($dbsubmitvalue->data);
            }

            $new_ID = $db->makeInsert($this->removeLinkedFields($model), $tn, $ignoreConversion);
            $model = $this->setLinkedTableValues($model, $new_ID);
            return $new_ID;

        } catch (Exception $e) {
            // or Ignore
            return -1;
        }

    }

    /**
     * Summary of GetKeyValueArrayFromObject
     * @param DbModelEntry $object
     * @param bool $removeKeyValue
     * @return array
     * @throws Exception
     */
    public function GetKeyValueArrayFromObject(DbModelEntry $object, bool $removeKeyValue = true, $enumValuesInsteadOfKeys = false): array
    {

        if (get_class($object) != "DbModelEntry") {
            throw new Exception("dbInsertObject expected Object of Type DbModelEntry but is " . get_class($object));
        }

        if (empty($db)) {
            $db = $this->getDb();
        }

        $keyValueArray = array();

        $keyname = $this->getKeyName();
        // unset($object->$keyname); // remove PK


        // TODO: $enumValuesInsteadOfKeys
        /*echo "<pre>";
        echo json_encode($this->getDbModel());
        echo "</pre>";*/
        foreach ($this->getDbModel() as $key => $values) {
            if ($key == $keyname && $removeKeyValue) continue; // PK würde Fehler auslösen
            else {
                if(!empty($values->entries) && $enumValuesInsteadOfKeys) {
                    $keyValueArray[$key] = $values->entries->getValueByKey($object->$key);
                }else {
                    $keyValueArray[$key] = $object->$key;
                }
            }
        }

        return $keyValueArray;

    }

    /**
     * ermittelt den Wert im dbmodel, der primarykey ist
     * @return string
     * @throws Exception
     */
    public function getKeyName(): string
    {

        if (!$this->showdata) {
            //echo "debug: ";
            //echo $this instanceof BpfwEmptyModel;
            // var_dump($this->view);

            throw new Exception("showdata is false, no datamodel existing..." . get_called_class() . " " . $this->GetTableName());
        }

        $key = $this->tryGetKeyName();
        if (empty($key)) {
            throw new Exception("no primary key found in " . get_called_class() . " showdata is " . ($this->showdata ? "true" : "false") . " ");
        }
        return $key;

    }

    /**
     * detect the primary key of the model if there is any
     * @return string|null
     * @throws Exception
     */
    public function tryGetKeyName(): ?string
    {

        if (!is_array($this->getDbModel()) && !is_object($this->getDbModel())) {
            throw new Exception("getDbModel() return of " . $this->GetTableName() . " is no object or array, it's :'" . print_r($this->getDbModel(), true) . "'");
        }

        foreach ($this->getDbModel() as $key => $value) {
            if ($value->primaryKey) {
                return $key;
            }
        }

        return null;

        //throw new Exception("no primary key found in ".get_called_class() );

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return BpfwModelFormfield[]
     * @throws Exception
     * @throws Exception
     */
    public function getDbModel($cache = true): array
    {

        if (!$cache || empty($this->_dbmodelCache)) {

            $modelfields = $this->loadDbModel();

            if (defined("SAVE_CREATED_MODIFIED") && SAVE_CREATED_MODIFIED && !empty($modelfields) || $this->saveTimestampsInTable === true || $this->createSearchIndex === true) {
                $this->addTimestamp("bpfw_created", "Created (local)", array(VIEWSETTING::DEFAULTVALUE => "NOW()", FORMSETTING::PAGE => 1, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::HIDDENONEDIT => true, FORMSETTING::POSITION => POSITION_RIGHT));
                $this->addTimestamp("bpfw_modified", "Last edited (local)", array(VIEWSETTING::DEFAULTVALUE => "NOW()", FORMSETTING::PAGE => 1, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::HIDDENONEDIT => true, FORMSETTING::POSITION => POSITION_RIGHT));
            }

            if ($this->createSearchIndex) {
                $this->addField("searchindex", "Index", BpfwDbFieldType::TYPE_TEXT, "tinymce", array(FORMSETTING::PAGE => 7, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONEDIT => true, FORMSETTING::HIDDENONADD => true, FORMSETTING::POSITION => POSITION_LEFT));
                $this->addTimestamp("searchindex_timestamp", "searchindex_timestamp", array(FORMSETTING::PAGE => 7, FORMSETTING::DISABLED => true, LISTSETTING::HIDDENONLIST => true, FORMSETTING::HIDDENONEDIT => true, FORMSETTING::HIDDENONADD => true));
            }


            /*if ($this->translateDbModelLabels) {

                if ($this->language_used_in_code != bpfw_getCurrentLanguageCode()) {

                    $domain = $this->translation_domain;

                    foreach ($modelfields as $key => $value) {
                        $value->label = __($value->label, $domain);
                        $modelfields[$key] = $value;

                    }

                }

            }*/

            // TODO: seems wrong?
            $this->_dbmodelCache = $modelfields;
            $this->_dbmodelCache = bpfw_do_filter(DbModel::FILTER_GET_DBMODEL, $this->GetTableName(), array($this->_dbmodelCache, $this, $this->GetTableName()), $this);
            $this->_dbmodelCache = $this->handleTemplateFields();

        }
        return $this->_dbmodelCache;

    }


    /**
     * loads dbmodel without cache
     * @return array
     */
    abstract protected function loadDbModel(): array;

    /**
     * Summary of addDateTimePicker
     * @param mixed $name
     * @param mixed $label
     * @param ?array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addTimestamp(mixed $name, mixed $label, ?array $attributes = array()): void
    {

        $attributes["autocomplete"] = false;
        $attributes[FORMSETTING::DISABLED] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_TIMESTAMP, "timestamp", $attributes);

    }

    /**
     * @throws Exception
     */
    function addCustomComponent($name, $label, $type, $display, $attributes = array()): void
    {
        $this->addField($name, $label, $type, $display, $attributes);
    }

    /**
     * @throws Exception
     */
    function addField($name, $label, $type, $display, $attributes = array()): void
    {
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), $type, $display, $attributes);
    }

    function handleTemplateFields(): array
    {

        foreach ($this->templatesUsed as $name => $template) {

            $template["model"]->addFieldsToModel($this, $template["ignorekey"], $template["ignorefields"]);

        }

        return $this->dbModel;

    }

    /**
     * add fields to another model
     * @param DbModel $dbmodel
     * @param bool $ignorePrimaryKey ignore primary KEy
     * @param array $ignorefields array of fieldnames to ignore
     * @param int $pageoffset tabpage offset for view
     * @throws Exception
     * @throws Exception
     */
    public function addFieldsToModel(DbModel $dbmodel, bool $ignorePrimaryKey = true, array $ignorefields = array(), int $pageoffset = 0): void
    {


        $templatefields = $this->loadDbModel();

        $key = $this->tryGetKeyName();

        foreach ($templatefields as $fieldname => $templatefield) {

            if ($fieldname == $key && $ignorePrimaryKey) continue;

            if (in_array($fieldname, $ignorefields)) continue;

            if ($pageoffset != 0) {
                $templatefield = clone($templatefield);
                $templatefield->editpage += $pageoffset;
                $templatefield->addpage += $pageoffset;
            }

            $dbmodel->addFieldByModelformfield($templatefield);

        }

    }

    /**
     * Summary of addFieldByModelformfield
     * @param BpfwModelFormfield $field
     * @param null $differentname
     */
    function addFieldByModelformfield(BpfwModelFormfield $field, $differentname = null): void
    {

        if ($differentname == null) $differentname = $field->name;

        $this->dbModel[$differentname] = $field;
    }

    /**
     * @throws Exception
     */
    function checkUnique($kvp, $temptable = false): bool
    {

        foreach ($this->getUniqueValues() as $error => $uniqueValues) {

            $where = "";
            $first = true;
            foreach ($uniqueValues as $uniquevalue) {

                if (empty($kvp[$uniquevalue]->data)) {
                    return true; // can't be checked
                }

                if (!$first) {
                    $where .= " and ";
                } else {
                    $first = false;
                }

                // var_dump($kvp);

                $where .= " $uniquevalue = " . $kvp[$uniquevalue]->data;


            }


            if (!empty($kvp[$this->tryGetKeyName()]->data)) {
                $where .= " and " . $this->tryGetKeyName() . " != " . $kvp[$this->tryGetKeyName()]->data;
            }

            // echo $where;
            //echo $where;

            $found = $this->DbSelectSingleOrNullByWhere($where, $temptable);

            if ($found) {
                if (empty($error)) {
                    bpfw_error_add("Fehler: Doppelter Wert gefunden $where");
                } else {
                    bpfw_error_add("$error");
                }

                return false;
                //throw new Exception (" Doppelter Wert gefunden $where");
            }

        }

        return true;

    }

    /**
     * Array of Arrays of Unique Values. Example array(array("val1","val2"), array("val2", "val3"));
     *
     * @return array
     */
    public function getUniqueValues(): array
    {
        return array();
    }

    /**
     * gibt Wert-Array zurück wenn wherestatement 1x existiert, sonst null(<0) oder Exception(>1)
     * @param string $where
     * @param bool $temptable
     * @return false|array|null
     * @throws Exception
     */
    public function DbSelectSingleOrNullByWhere(string $where = "", bool $temptable = false): false|array|null
    {

        $tn = $this->GetTableName();
        if ($temptable) {
            $tn = $this->GetTempTableName();
        }

        return $this->getDb()->makeSelectSingleOrNullByWhere($tn, $where);

    }

    /**
     * Create model with inserted values
     * @param array $data key=>value of the fields. key must be in getDbModel
     * @return DbSubmitValue[] data field array
     * @throws Exception
     */
    public function CreateArrayWithDataAndFieldinfoFromPlainKvpArray(array $data, $skipIgnoreFields = true, $ignoreRequiredFields = false): array
    {


        $alreadyOk = true;
        foreach ($data as $key => $value) {
            if ($value instanceof DbSubmitValue){ // empty($value->data) || !isset($value->key)) {

            }else{
                $alreadyOk = false;
            }
        }


        $model = $this->getDbModel();

        foreach ($model as $mk => $mv) {

            if ($mv->type->type == BpfwDbFieldType::TYPE_IGNORE && $skipIgnoreFields) {
                unset($data[$mk]);
            }

        }

        if ($alreadyOk) return $data;

        foreach ($model as $mk => $mv) {

            if ($mv->required && !$ignoreRequiredFields ) {
                //if (!isset($data[$mk]) || $data[$mk] !== 0 && $data[$mk] !== "0" && empty($data[$mk])) {
                if($mk == "file"){
                    ;
                }
                if(!isset($data[$mk]) || $data[$mk] === "" || $data[$mk] === array()){
                    bpfw_error_add_to_component(__("Required input field not set").": " . $mv->label . "/" . $mk . "/" . (is_array($data) && array_key_exists($mk, $data)!=null?$data[$mk]->data:"") . "/" . json_encode($mv), $mv);
                    // throw new Exception("CreateArrayWithDataAndFieldinfoFromPlainKvpArray Required Value not set: " . $mk . " in " . $mv->name . " " . $this->GetTableName() . " data:" . print_r($data, true));
                }
            }
        }

        $retval = array();

        foreach ($data as $key => $value) {

            if (!isset($model[$key])) {
                /*echo "not found: $key";
                 var_dump($this->getDbModel());
                 echo get_class($this);
                 var_dump($data);*/
                throw new Exception("Not found: DbModelEntry '" . get_class($this) . "/" . $key . "'" . bpfw_debug_string_backtrace());
            }

            $retval[$key] = new DbSubmitValue($key, $value, $this);

            //$retval[$key]["data"] = $value;
            //$retval[$key]["field"] = $model[$key];
            // $model[$key]["data"] = $value;

        }

        return $retval;

    }

    /**
     * Summary of prepareForStrictMode
     * @param DbSubmitValue[] $model
     * @return DbSubmitValue[]
     */
    function prepareForStrictMode(array $model): array
    {


        return $model;


    }

    /**
     * Summary of setLinkedTableValues
     * @param DbSubmitValue[] $model
     * @return DbSubmitValue[]
     * @throws Exception
     */
    public function removeLinkedFields(array $model): array
    {


        $linkfields = $this->getAllLinkTableFields();

        foreach ($linkfields as $linkfieldkey => $linkfieldvalue) {


            unset($model[$linkfieldkey]);

        }

        return $model;

    }

    /**
     * Returns all linktables
     * @return BpfwModelFormfield[]
     * @throws Exception
     * @throws Exception
     */
    function getAllLinkTableFields(): array
    {
        return $this->getAllModelFieldsWithType(BpfwDbFieldType::TYPE_LINK_TABLE);
    }

    /**
     * returns all Modelfields with specific type
     * @param $type
     * @return BpfwModelFormfield[]
     * @throws Exception
     * @throws Exception
     */
    function getAllModelFieldsWithType($type): array
    {

        $retval = array();

        foreach ($this->getDbModel() as $key => $value) {
            if ($value->type->type == $type) {
                $retval[$key] = $value;
            }
        }
        return $retval;

    }

    /**
     * Summary of setLinkedTableValues
     * @param DbSubmitValue[] $model
     * @param mixed|null $new_ID
     * @return DbSubmitValue[]
     * @throws Exception
     */
    public function setLinkedTableValues(array $model, mixed $new_ID = null): array
    {


        if (empty($db)) {
            $db = $this->getDb();
        }

        $linkfields = $this->getAllLinkTableFields();

        foreach ($linkfields as $linkfieldkey => $linkfieldvalue) {

            $keyname = $this->getKeyName();
            $keyvalue = $new_ID;

            $fkname = $linkfieldvalue->entries->key;

            if (!empty($linkfieldvalue->linktable_valuename)) {
                $fkname = $linkfieldvalue->linktable_valuename;
            }

            if (empty($model[$keyname]->data) && empty($new_ID)) {
                throw new Exception("Keyvalue is empty -> required before linkedTableUpdate");
            }

            if (!isset($model[$linkfieldkey])) {
                if ($this->dbModel[$keyname]->required) {
                    throw new Exception("required Linktablevalue not set: $linkfieldkey");
                } else {
                    //return $model;
                    continue;
                }
            }

            if (empty($new_ID)) {
                $keyvalue = $model[$keyname]->data;
            }

            //  var_dump( $model[$keyname]->data);
            //  var_dump( $model[$linkfieldkey]->data);

            $newdata = $model[$linkfieldkey]->data;

            $table = $this->getLinkTableName($model[$linkfieldkey]->key);

            // alte Werte löschen
            if (!empty($keyvalue)) {


                $extrawhere = "";

                if (is_array($newdata)) {
                    foreach ($newdata as $newvalue) {
                        $extrawhere .= " and " . $fkname . " != $newvalue";
                    }
                }

                $db->makeDeleteByWhere($table, " $keyname = $keyvalue $extrawhere");

                // echo " $keyname = $keyvalue $extrawhere";
            }

            $linkmodel = $this->generateLinkTableModel($linkfieldkey);

            if (is_array($newdata)) {

                foreach ($newdata as $newvalue) {
                    $newvalues = array($keyname => $keyvalue, $fkname => $newvalue);

                    //echo " where ".$linkfieldvalue->entries->key." = '$newvalue' and $keyname = '$keyvalue'";
                    $existing = $linkmodel->DbSelectSingleOrNullByWhere($fkname . " = '$newvalue' and $keyname = '$keyvalue'");

                    if ($existing == null)
                        $linkmodel->DbInsert($newvalues);
                }
            }

            unset($model[$linkfieldkey]);

        }

        return $model;

    }

    function getLinkTableName($field): string
    {
        return strtolower("link_" . $this->GetTableName() . "_" . "$field");
    }

    /**
     * Summary of generateLinkTableModel
     * @param mixed $field
     * @param bool $caseSensitive
     * @return BpfwModel|DbDynamicModel
     * @throws Exception
     */
    function generateLinkTableModel(mixed $field, bool $caseSensitive = true): BpfwModel|DbDynamicModel
    {
        $dbmodel = $this->getDbModel();
        $tablename = $this->GetTableName();

        if (empty($dbmodel)) {

            throw new Exception("Model leer: $tablename");

        } else {

            if (!isset($dbmodel[$field])) {

                if (!$caseSensitive) {
                    foreach ($dbmodel as $key => $value) {

                        if (strtolower($key) == strtolower($field)) {
                            $field = $key;

                        }
                    }
                }

                if (!isset($dbmodel[$field])) {

                    throw new Exception("linktable gibt es nicht im Model " . $this->getKeyName() . ": $field");

                }

            }


            $fieldinfo = $dbmodel[$field];

            $dbenum = $fieldinfo->entries;
            $table = $this->getLinkTableName($field);

            $key_n = $this->getKeyName();
            $key_m = $dbenum->key;

            //echo "key1 is '$key_m'<br>";
            //echo "key2 is '$key_n'";

            if (!empty($fieldinfo->linktable_valuename)) {
                $key_m = $fieldinfo->linktable_valuename;
            }


            $key = $this->getLinkTableKey($field);

            $newlinktablemodel = new DbDynamicModel($table);
            $newlinktablemodel->addPrimaryKey($key);
            $newlinktablemodel->addTextFieldNumeric($key_n, $key_n);
            $newlinktablemodel->addTextFieldNumeric($key_m, $key_m);

            return $newlinktablemodel;


            //  $table_m = $dbenum->table;


            // second model to get the type
            // $mdbModel= bpfw_createModelByName($table_m)->getDbModel();


            //  $newmodel = createLinkTableInDatabase();


            //  var_dump($mdbModel);

            /*if(empty($mdbModel[$key_m])){
            throw new Exception("There is no key named $key_m in $table_m (case sensitive!)");
            }*/

            //  $type_m = $mdbModel[$key_m]->type->type;
            //  $length_m = $mdbModel[$key_m]->type->length;


            //  $table_n = $model->GetTableName();

            //  $type_n = $dbmodel[$key_n]->type->type;
            //  $length_n = $dbmodel[$key_n]->type->length;
            // var_dump($dbmodel[$field]);


            //   $this->commandsExecuted[] = "TODO: creating link table $tablename $field - $table_n $key_n $type_n $length_n - $table_m $key_m $type_m $length_m";


        }
    }

    function getLinkTableKey($field): string
    {
        return $field . "Ids";
    }

    /**
     * Summary of addPrimaryKey
     * @param string $name
     * @param string $label
     * @param BpfwDbFieldType|string $type
     * @param string $display
     * @param array $attributes
     * @throws Exception
     */
    public function addPrimaryKey(string $name, string $label = "ID", mixed $type = BpfwDbFieldType::TYPE_INT, string $display = "hidden", array $attributes = array()): void
    {

        if (!is_array($attributes)) {
            throw new Exception("addPrimaryKey -> attributes is no array()");
        }

        if (!isset($attributes[FORMSETTING::DISABLED]))
            $attributes[FORMSETTING::DISABLED] = true;

        if (!isset($attributes[FORMSETTING::REQUIRED]))
            $attributes[FORMSETTING::REQUIRED] = false;

        $attributes[VIEWSETTING::PRIMARYKEY] = true;

        if (!isset($attributes[LISTSETTING::HIDDENONLIST]))
            $attributes[LISTSETTING::HIDDENONLIST] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $label, $type, $display, $attributes);

    }

    /**
     * @throws Exception
     */
    function addTextFieldNumeric(string $name, string $label, ?array $attributes = array()): void
    {
        $this->dbModel[$name] = new BpfwModelFormfield($name, $label, BpfwDbFieldType::TYPE_INT, "text", $attributes);

    }

    /**
     * neuen Datenbankeintrag einfügen mit keyvalue array oder einem passenden Object
     * @param mixed $keyValueArrayorObject
     * @param ?Database $db (optional)
     * @param bool|null $ignoreConversion
     * @param bool $temptable
     * @return null|int|string (neue autoincrement id)
     * @throws Exception
     */
    public function DbInsert(mixed $keyValueArrayorObject, ?Database $db = null, ?bool $ignoreConversion = null, bool $temptable = false): null|int|string
    {

        $tablename = $this->GetTableName();

        if ($temptable) {
            $tablename = $this->GetTempTableName();
        }

        if ($ignoreConversion === null) {
            $ignoreConversion = $this->ignoreConversion;
        }

        $kvp = $keyValueArrayorObject;

        if (is_object($keyValueArrayorObject)) {
            $kvp = $this->GetKeyValueArrayFromObject($keyValueArrayorObject, false);
        }

        if (!$this->checkUnique($kvp, $temptable)) return -1;

        if (empty($db)) {
            $db = $this->getDb();
        }

        $model = $this->CreateArrayWithDataAndFieldinfoFromPlainKvpArray($kvp);

        if (!$ignoreConversion) {
            $model = $this->prepareForStrictMode($model);
        }


        foreach ($model as $dbskey => $dbsubmitvalue) {

            if (is_string($dbsubmitvalue)) {
                throw new Exception("conversion to kvp array failed $dbskey => " . $dbsubmitvalue . " in " . print_r($model, true));
                //$dbsubmitvalue = new DbSubmitValue($dbskey, $dbsubmitvalue, $this);
            }

            $dbsubmitvalue->data =
                bpfw_getComponentHandler()->getComponent($dbsubmitvalue->getDbField()->display)
                    ->preProcessBeforeSql($dbsubmitvalue->data);
        }

        $new_ID = $db->makeInsert($this->removeLinkedFields($model), $tablename, $ignoreConversion/*, $this->getKeyName()*/);

        $this->updateLastUpdated($temptable, $new_ID);

        $model = $this->setLinkedTableValues($model, $new_ID);

        return $new_ID;

    }

    /**
     * update modified timestamp
     * @param mixed $temptable
     * @param mixed $key
     * @throws Exception
     * @throws Exception
     * @noinspection SqlResolve
     */
    protected function updateLastUpdated(mixed $temptable, mixed $key): void
    {


        if (defined("SAVE_CREATED_MODIFIED") && SAVE_CREATED_MODIFIED || $this->createSearchIndex === true || $this->saveTimestampsInTable === true) {

            $modelfields = $this->loadDbModel();

            if (!empty($modelfields)) {

                $tableName = $this->GetTableName();
                if ($temptable) $tableName = $this->GetTempTableName();

                if (is_object($key) && get_class($key) == "DatabaseKey") {
                    $key = $key->getValue();
                }

                $sql = "update `$tableName` set bpfw_modified = CURRENT_TIMESTAMP() WHERE " . $this->tryGetKeyName() . " = '" . $key . "'";

                if ($this->createSearchIndex) {
                    $sql = "update `$tableName` set bpfw_modified = CURRENT_TIMESTAMP(), searchindex=null, searchindex_timestamp=null WHERE " . $this->tryGetKeyName() . " = '" . $key . "'";
                }

                bpfw_getDb()->makeQuery($sql);

                if ($this->createSearchIndex) {
                    $this->createSearchIndexValues(100);
                }


            }

        }

    }

    /**
     * @throws Exception
     */
    function createSearchIndexValues(int $rowlimit): int
    {

        $affectedRows = 0;

        if (!$this->createSearchIndex) {
            throw new Exception("no Index existing, set createSearchIndex flag");
        } else {

            // TODO: update seachindex, dostuff limit $rowlimit

            $select = "";

            $first = true;
            $join = "";

            $dbmodel = $this->getDbModel();

            foreach ($dbmodel as $dbkey => $entry) {

                if (!$entry->is_searchable) continue;
                if ($dbkey == "searchindex") continue;

                //if($this->control == null || $this->control->checkSearchField($dbkey, $entry, getorpost("searchType"))){

                if (!empty($entry->sortsearch)) {
                    if (!$first) {
                        $select .= ", ";
                    }
                    $select .= $entry->sortsearch->field;
                    $select .= " AS ";
                    $select .= "`$dbkey`";

                    $join .= $entry->sortsearch->join;

                } else {

                    if ($entry->type->isSearchableType() || $entry->primaryKey) {


                        if (!$first) {
                            $select .= ", ";
                        }

                        $select .= $this->GetTableName() . "." . $dbkey;

                    }

                }

                if (!empty($select)) {
                    $first = false;
                }

            }

            $where = "searchindex is null || searchindex = ''";

            $join = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_JOIN, $this->GetTableName(), array($join, $where, $rowlimit, 0, null), $this); // muss immer richtiger tablename sein, unabhängig von temptable

            // echo $where;
            //$where = bpfw_do_filter(BpfwModel::FILTER_ENTRY_SELECT_WHERE, $this->GetTableName(), array($where, $count, $offset, $sort, $join), $this); // muss immer richtiger tablename sein, unabhängig von temptable

            $query = "select $select FROM `" . $this->GetTableName() . "` $join WHERE $where limit $rowlimit";

            $db = $this->getDb();

            $fullResult = $db->makeSelect($query, $this->tryGetKeyName());

            if (empty($fullResult)) {
                return 0;
            }

            foreach ($fullResult as $key => $row) {

                $searchstr = $db->escape_string($this->createSearchString($row));

                $update = "update `" . $this->GetTableName() . "` set searchindex = '" . $searchstr . "', searchindex_timestamp=CURRENT_TIMESTAMP() where " . $this->tryGetKeyName() . " = '" . $key . "' LIMIT 1";

                $db->makeQuery($update);

                //echo $update;
                //echo "<br><br>";

                $affectedRows++;

            }

            // echo $query;


            // $affectedRows = 1234;


        }

        return $affectedRows;


    }

    // TODO: wär super, wenn man hier ein callback angeben kann, um den Wert zu ermitteln

    function createSearchString(array $val_array): string
    {

        $retval = "";


        $first = true;
        foreach ($val_array as $key => $val) {

            if ($first) {
                $first = false;
            } else {
                $retval .= "|";
            }

            $retval .= $val;

        }

        return $retval;
    }

    /**
     * neuen Datenbankeintrag einfügen oder updaten mit keyvalue array oder einem passenden Object
     * @param mixed $keyValueArrayorObject
     * @param ?Database $db (optional)
     * @param bool|null $ignoreConversion
     * @param bool $ignoreRequiredFields
     * @param bool $temptable
     * @return int (neue autoincrement id)
     * @throws Exception
     */
    public function DbInsertOrUpdate(mixed $keyValueArrayorObject, ?Database $db = null, ?bool $ignoreConversion = false, bool $ignoreRequiredFields = false, bool $temptable = false): int
    {

        $tn = $this->GetTableName();
        if ($temptable) {
            $tn = $this->GetTempTableName();
        }

        if ($ignoreConversion === null) {
            $ignoreConversion = $this->ignoreConversion;
        }

        // var_dump($keyValueArrayorObject);

        // TODO: test
        $kvp = $keyValueArrayorObject;

        if (is_object($keyValueArrayorObject)) {
            $kvp = $this->GetKeyValueArrayFromObject($keyValueArrayorObject, false);
        }

        if (!$this->checkUnique($kvp, $temptable)) return -1;
        if (empty($db)) {
            $db = $this->getDb();
        }

        $model = $this->CreateArrayWithDataAndFieldinfoFromPlainKvpArray($kvp, true, $ignoreRequiredFields);


        $key = new DatabaseKey($this->getKeyName(), $kvp[$this->getKeyName()]);

        if (!$ignoreConversion) {
            $model = $this->prepareForStrictMode($model);
        }

        $newmodel = $this->removeLinkedFields($model);
        foreach ($model as $dbsubmitkey => $dbsubmitvalue) {
            $dbsubmitvalue->data = bpfw_getComponentHandler()->getComponent($dbsubmitvalue->getDbField()->display)->preProcessBeforeSql($dbsubmitvalue->data);
        }

        $new_ID = $db->makeInsertOrUpdate($newmodel, $tn, $key, $ignoreConversion);

        $this->updateLastUpdated($temptable, $new_ID);

        $model = $this->setLinkedTableValues($model, $new_ID);

        return $new_ID;

    }

    /**
     * Summary of DbCachedSelect
     * @param string $where
     * @param int|null $count
     * @param int|null $offset
     * @param DatabaseSortEntry[] $sort
     * @param string $join
     * @param bool $temptable
     * @return DbModelEntry[]
     * @throws Exception
     */
    public function DbCachedSelectAndCreateObjectArray(string $where = " 1", ?int $count = -1, ?int $offset = 0, array $sort = array(), string $join = "", bool $temptable = false): array
    {

        if (!is_array($sort)) $sort = array();

        $extrafields = array();

        //if(!is_array($extrafields)){
        //throw new Exception("Extrafields is no array before Filter: '$extrafields'");
        //}

        $extrafields = bpfw_do_filter(DbModel::FILTER_EXTRAFIELDS, $this->GetTableName(), array($extrafields, $this, $this->GetTableName()), $this);

        if (!is_array($extrafields)) {
            throw new Exception("Extrafields is no longer an array after Filter: '$extrafields'");
        }

        /*$db = static::getDatabase();

        $search = getorpost("search");
        $searchtxt = $db->escape_string($search["value"]);

        if(!empty($searchtxt)){

            foreach($this->dbModel as $name=>$entry){


                if($name != $sort){

                    if(!empty($entry->sortsearch)){

                        $selectname = $name."_sort";

                        $extrafields[$selectname] = $entry->sortsearch->field;

                       // $where = $entry->sortsearch->join." ".$where;

                    }
                }


            }


        }*/

        if (!empty($sort)) {
            foreach ($sort as $key => $databasesortentry) {

                $sortfield = $this->dbModel[$databasesortentry->fieldName];

                // var_dump($this->dbModel[$databasesortentry->fieldname]);

                if (!empty($sortfield->sortsearch)) {

                    $join .= " " . $sortfield->sortsearch->join;
                    $field = $sortfield->sortsearch->field;

                    // $where = $join." WHERE ".$where;

                    $sortname = $databasesortentry->fieldName . "_sort";

                    $extrafields[$sortname] = $field;

                    $databasesortentry->fieldName = $sortname; // override sort


                }

            }

        }


        if (!empty($this->objectsCache)
            && $where == $this->cachedWhere
            && $count == $this->cachedcount
            && $offset == $this->cachedoffset
            && $sort == $this->cachedsort
            && $join == $this->cachedjoin
            && $temptable == $this->cachedtemptable
            && bpfw_compare_sort($sort, $this->cachedsort)) {
            return $this->objectsCache;
        } else {


            $values = $this->DbCachedSelect($where, $count, $offset, $sort, $extrafields, $join, $temptable);

            $objects = $this->CreateDbModelEntryArrayFromKeyValueArray($values);

            $this->objectsCache = $objects;

            return $objects;

        }

    }

    /**
     * Summary of DbCachedSelect
     * @param string $where
     * @param int $count
     * @param int $offset
     * @param DatabaseSortEntry[] $sort
     * @param string[] $extrafields key value von extrafeldern etwa array("newval"=>"calc(2+2)", "xy"=>"date_created+38974");
     * @param string $join
     * @param bool $temptable
     * @return array
     */
    public function DbCachedSelect(string $where = " 1", int $count = -1, int $offset = 0, array $sort = array(), array $extrafields = array(), string $join = "", bool $temptable = false): array
    {

        if (!empty($this->data)) {

            if ($temptable == $this->cachedtemptable && $where == $this->cachedWhere && $count == $this->cachedcount && $offset == $this->cachedoffset && $join == $this->cachedjoin && empty(array_diff($sort, $this->cachedsort))) {
                return $this->data;
            }

        }

        $this->cachedWhere = $where;
        $this->cachedcount = $count;
        $this->cachedoffset = $offset;

        $this->cachedtemptable = $temptable;

        $this->cachedjoin = $join;
        $this->cachedsort = $sort;

        $this->data = $this->DbSelect($where, null, $count, $offset, $sort, $extrafields, $join, $temptable);

        return $this->data;

    }

    /**
     * baut aus einem select ein Array mit Arrays die Werte enthalten (keyvalue)
     * @param string $select
     * @param ?Database $db bestehende Datenbank-Klasse (optional, kann aus xpl neu erzeugt werden)
     * @param int $count
     * @param int $offset
     * @param DatabaseSortEntry|DatabaseSortEntry[] $sort
     * @param string[] $extrafields key value von extrafeldern etwa array("newval"=>"calc(2+2)", "xy"=>"date_created+38974");
     * @param string $join
     * @param bool $temptable
     * @return array[] ein Array
     */
    public function DbSelect(string $select = " 1", ?Database $db = null, int $count = -1, int $offset = 0, DatabaseSortEntry|array $sort = array(), array $extrafields = array(), string $join = "", bool $temptable = false): array
    {

        try {

            if (empty($db)) {
                $db = $this->getDb();
            }


            $tablename = $this->GetTableName();

            if ($temptable) {
                $tablename = $this->GetTempTableName();
            }

            $values = $db->makeSelectAll($tablename, $this->getKeyName(), $select, $count, $offset, $sort, $extrafields, $join);

            // todo: nur bei nicht temptable? den fall wird es aber wohl eh nicht geben. Also n:n mit linktable, daher eig. egal

            $linktablefields = $this->getAllLinkTableFields();

            foreach ($linktablefields as $linkfieldkey => $linkfieldvalue) {

                $foreignkey = $linkfieldvalue->entries->key;

                if (!empty($linkfieldvalue->linktable_valuename)) {
                    $foreignkey = $linkfieldvalue->linktable_valuename;
                }

                $linkvalues = $db->makeSelectAll($this->getLinkTableName($linkfieldkey), $this->getLinkTableKey($linkfieldkey));
                $keyname = $this->getKeyName();

                // set array value in case there is no value
                foreach ($values as $key => $val) {
                    $values[$key][$linkfieldkey] = array();
                }


                foreach ($linkvalues as $linkkey => $linkvalue) {

                    $thiskeyvalue = $linkvalue[$keyname]; // name of the rowkey
                    if (isset($values[$thiskeyvalue])) {

                        //

                        if (empty($values[$thiskeyvalue][$linkfieldkey][$linkvalue[$foreignkey]])) {
                            $values[$thiskeyvalue][$linkfieldkey][$linkvalue[$foreignkey]] = array();
                        }

                        $values[$thiskeyvalue][$linkfieldkey][$linkvalue[$foreignkey]] = $linkvalue[$foreignkey];

                        // var_dump($values[1]["advisorIds"]);

                    }

                }

            }

            return $values;

        } catch (Exception $e) {
            echo "dberror: " . $e;
            bpfw_error_add("dberror: " . $e);
            return array();
        }

    }

    /**
     * converts array of key value arrays to array of dbModelEntry
     * @param array $keyValueArray array of key=>value arrays. first layer key is the id. Example: array(1=>array("val1"=>"123", "val2"=>23), 2=>array("val1"=>"Hello World", "val2"=>123));
     * @return DbModelEntry[] immer DbModelEntry
     * @throws Exception
     */
    public function CreateDbModelEntryArrayFromKeyValueArray(array $keyValueArray): array
    {

        if (empty($keyValueArray)) return array();

        if (current($keyValueArray) instanceof DbModelEntry) {
            return $keyValueArray;
        }

        $dbModelEntryArray = array();

        foreach ($keyValueArray as $key => $values) {
            $dbModelEntryArray[$key] = new DbModelEntry($this, $values);
        }

        return $dbModelEntryArray;

    }

    /**
     * @throws Exception
     */
    function getLinkedTableValues($modelfieldname, $keyvalue, $returndbfieldname = null): array
    {

        global $database;

        if (empty($returndbfieldname)) {
            $returndbfieldname = $this->getLinkTableKey($modelfieldname);
        }

        $allData = $database->makeSelectAll($this->getLinkTableName($modelfieldname), $this->getLinkTableKey($modelfieldname), $this->getKeyName() . " = '$keyvalue'");

        $retval = array();

        foreach ($allData as $value) {

            $retval[] = $value[$returndbfieldname];

        }

        return $retval;

    }

    /**
     * @throws Exception
     */
    function clearSearchIndex(int $rowid): void
    {


        if (!$this->createSearchIndex) {
            throw new Exception("no Index existing, set createSearchIndex flag");
        }

        $db = bpfw_getDb();
        $db->makeQuery("update `" . $this->GetTableName() . "` set searchindex = NULL, searchindex_timestamp = NULL where `" . $this->tryGetKeyName() . "` = '$rowid'");

    }

    /**
     * @throws Exception
     */
    function clearAllSearchIndexValues(): mysqli_result|bool
    {

        if (!$this->createSearchIndex) {
            throw new Exception("no Index existing, set createSearchIndex flag");
        }


        $db = bpfw_getDb();

        return $db->makeQuery("update `" . $this->GetTableName() . "` set searchindex = NULL, searchindex_timestamp = NULL");

    }

    /**
     * @throws Exception
     */
    function addFieldsOfTemplateModel($name, $ignorefields = array(), $ignorekey = true): void
    {

        $this->templatesUsed["template_" . $name] = array("model" => bpfw_createTemplateModelByName($name), "ignorekey" => $ignorekey, "ignorefields" => $ignorefields);

    }

    /**
     * @throws Exception
     */
    function addFieldsOfModel($name, $ignorefields = array(), $ignorekey = true): void
    {

        $this->templatesUsed["model_" . $name] = array("model" => bpfw_createTemplateModelByName($name), "ignorekey" => $ignorekey, "ignorefields" => $ignorefields);

    }

    function handleTemplateConstraints($constraints)
    {

        foreach ($this->templatesUsed as $name => $template) {

            $constraints = $template->addConstraintsToArray($constraints);

        }

        return $constraints;

    }

    /**
     * add all containts of this dbmodel (template oder model) to another model
     * @param DatabaseFKConstraint[] $modelcontraints
     * @return array
     */
    public function addConstraintsToArray(array $modelcontraints): array
    {

        $constraintsTemplate = $this->getConstraints();

        $constNamesExisting = array();

        $existingConstraints = $this->getConstraints();

        foreach ($existingConstraints as $const) {
            $constNamesExisting[$const->constraintName] = $const->constraintName;
        }


        foreach ($modelcontraints as $constraitval) {
            if (!in_array($constraitval->constraintName, $constNamesExisting)) {
                $modelcontraints[] = $constraitval;
            }

        }

        return $modelcontraints;

    }

    /**
     * Summary of getContraints
     * @return DatabaseFKConstraint[]
     */
    public function getConstraints(): array
    {
        return array();
    }

    public function translate($word){

        if(bpfw_creatingTables()){
            return $word;
        }

        if ($this->translateDbModelLabels) {


            if($this->language_used_in_code =! "en"){
                if(DEBUG) {
                    throw new Exception("language has to be englisch to enable translations");
                }else{
                    return $word;
                }
            }


            if ($this->language_used_in_code != bpfw_getCurrentLanguageCode()) {
                try {

                    return __($word, array(), $this->translation_domain);
                } catch (Exception $ex) {
                    $error = $ex->getMessage();

                    if (DEBUG) {
                        echo $error;
                        return "error $word:" . $error;
                    }
                }
            }
        }

        return $word;

    }

    /**
     * Summary of addHiddenField
     * @param string $name
     * @param string $label
     * @param string $type
     * @param int|string|null $value
     * @param array $attributes
     * @return void
     * @throws Exception
     * @throws Exception
     */
    function addHiddenField(string $name, string $label, string $type, int|string|null $value, array $attributes = array()): void
    {

        /*if(!isset($attributes[FORMSETTING::HIDDENONEDIT]))
        $attributes[FORMSETTING::HIDDENONEDIT] = true;

        if(!isset($attributes[FORMSETTING::HIDDENONADD]))
        $attributes[FORMSETTING::HIDDENONADD] = true;       */

        if (!isset($attributes[LISTSETTING::HIDDENONLIST]))
            $attributes[LISTSETTING::HIDDENONLIST] = true;

        if (!isset($attributes[FORMSETTING::REQUIRED]))
            $attributes[FORMSETTING::REQUIRED] = false;

        $attributes[VIEWSETTING::DEFAULTVALUE] = $value;

        if (!isset($attributes[FORMSETTING::XTRAFORMCLASSWRAPPER])) $attributes[FORMSETTING::XTRAFORMCLASSWRAPPER] = "";
        $attributes[FORMSETTING::XTRAFORMCLASSWRAPPER] .= " " . HIDDEN_INPUT_CLASS . " ";

        // return $this->addComboBox( $name, $label, new EnumHandlerArray(array($value=>$value)), $type, $attributes );

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), $type, "hidden", $attributes);
    }

    /**
     * Summary of addDateTimePicker
     * @param mixed $name
     * @param mixed $label
     * @param ?array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addDateTimePicker(mixed $name, mixed $label, ?array $attributes = array()): void
    {

        $attributes["autocomplete"] = false;
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_DATETIME, "datetimepicker", $attributes);
    }

    /**
     * @throws Exception
     */
    function addTimepicker($name, $label, $attributes = array()): void
    {

        $attributes["autocomplete"] = false;
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_TIME, "timepicker", $attributes);
    }

    /**
     * @throws Exception
     */
    function addDatepicker($name, $label, $attributes = array()): void
    {

        $attributes["autocomplete"] = false;
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_DATE, "datepicker", $attributes);
    }

    /**
     * @throws Exception
     */
    function addDatepickerStringtype($name, $label, $attributes = array()): void
    {

        $attributes["autocomplete"] = false;
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_STRING, "datepicker", $attributes);

    }

    /**
     * @throws Exception
     */
    function addDatepickerYearMonth($name, $label, $attributes = array()): void
    {

        $attributes["autocomplete"] = false;

        $attributes[LISTSETTING::AJAX_SORTSEARCH] = new BpfwSortsearch("", 'STR_TO_DATE(' . $name . ', "%m\/%Y")');

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_STRING, "datepickeryearmonth", $attributes);
    }


    /*
    function addModellist($name, $label, $entries, $dataModel, $modelFields,  $attributes = array()){




          $attributes[VIEWSETTING::DATA_MODEL] = $dataModel;
          $attributes[VIEWSETTING::DATA_MODEL_FIELDS] = $modelFields;

          if(!isset($attributes[FORMSETTING::POSITION]))$attributes[FORMSETTING::POSITION]=POSITION_FULLWIDTH;
          if(!isset($attributes[VIEWSETTING::DATA_MODEL_ENABLE_SORTING]))$attributes[VIEWSETTING::DATA_MODEL_ENABLE_SORTING]=true;


          if(!isset($attributes[VIEWSETTING::DATA_MODEL_SORTFIELD]))$attributes[VIEWSETTING::DATA_MODEL_SORTFIELD]=1;
          if(!isset($attributes[VIEWSETTING::DATA_MODEL_SORTORDER]))$attributes[VIEWSETTING::DATA_MODEL_SORTORDER]="desc";
          if(!isset($attributes[LISTSETTING::HIDDENONLIST]))$attributes[LISTSETTING::HIDDENONLIST]=true;
          if(!isset($attributes[VIEWSETTING::DATA_FILTER]))$attributes[VIEWSETTING::DATA_FILTER]=(int)getorpost("id");



          $this->dbModel[$name]  = new BpfwModelFormfield($name, $label, BpfwDbFieldType::TYPE_IGNORE, "modellist", $attributes );



      }*/

    //    $this->addModellist("parent_id", "Übergeordnetes Produkt", new EnumHandlerDb("product", "productId", 'CONCAT_WS(" ", CONCAT_WS(" (", name, SKU), ")" )', '', true), BpfwDbFieldType::TYPE_STRING, array(FORMSETTING::POSITION=>POSITION_FULLWIDTH, FORMSETTING::PAGE=>8, LISTSETTING::HIDDENONLIST=>true));


    // $this->addTextField("post_password", "Post Passwort (prüfen!)", "default", array(FORMSETTING::PAGE=>10, LISTSETTING::HIDDENONLIST=>true) );


    /// Page 10

    // $this->addComboBoxMultiselectLinktable("shopIds", "Sichtbar in Shops", new EnumHandlerDb("shop", "shopId", 'name', '', false), array(FORMSETTING::POSITION=>POSITION_FULLWIDTH,LISTSETTING::HIDDENONLIST=>true, FORMSETTING::PAGE=>10)); // TODO: constrait

    // $this->addField("linked_shops", "Shopeinstellungen" , BpfwDbFieldType::TYPE_IGNORE, "modellist", array(FORMSETTING::POSITION=>POSITION_FULLWIDTH, VIEWSETTING::DATA_MODEL_ENABLE_SORTING=>true , FORMSETTING::PAGE=>10, VIEWSETTING::DATA_MODEL_LIST_FIELDS=>array( "shopId" ), VIEWSETTING::DATA_MODEL_FIELDS=>array( "shopId", "overwrite_price", "price" ), VIEWSETTING::DATA_MODEL=>"productshopid", VIEWSETTING::DATA_MODEL_SORTFIELD=>1,  VIEWSETTING::DATA_MODEL_SORTORDER=>"desc", FORMSETTING::POSITION=>POSITION_FULLWIDTH, "hiddenOnList"=>true, FORMSETTING::SHOWLABEL_IN_FORM=>true, FORMSETTING::HIDDENONADD=>false, VIEWSETTING::DATA_MODEL_SHOW_ADD=>true, VIEWSETTING::DATA_FILTER=>(int)getorpost("id")));
    //  $this->addModellist("linked_shops", "Shopeinstellungen");

    /**
     * @throws Exception
     */
    function addDatepickerWithIntervalCombobox($name, $label, $baseformfield, $datetimeIntervalCombobox = array(), $attributes = array()): void
    {

        $attributes["autocomplete"] = false;

        if (!empty($datetimeIntervalCombobox))
            $attributes["datetimeIntervalCombobox"] = $datetimeIntervalCombobox;

        if (!isset($attributes["datetimeIntervalCombobox"]) || !is_array($attributes["datetimeIntervalCombobox"])) {
            $attributes["datetimeIntervalCombobox"] = array("(Tage nach Start)" => null, "9 Tage nach Start" => 9, "13 Tage nach Start" => 13, "27 Tage nach Start" => 27, "40 Tage nach Start" => 40, "55 Tage nach Start" => 55);
        }

        $attributes[FORMSETTING::BASEFORMFIELD] = $baseformfield;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_DATE, "datepicker", $attributes);

    }

    /**
     * Summary of addTextField
     * @param mixed $name
     * @param mixed $label
     * @param int|string|array $length maximum length. may be skipped and used as $attributes
     * @param ?array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addMailAddressField(mixed $name, mixed $label, int|string|array $length = "default", ?array $attributes = array()): void
    {

        if (is_array($length) && empty($attributes)) {
            //die("ddTextField: $name length is array");
            $attributes = $length;
        }
        if (empty($length) || !is_numeric($length)) {
            $length = "default";
        }

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_TEXT, $length), "mailaddress", $attributes);

    }

    /**
     * Summary of addTextField
     * @param mixed $name
     * @param mixed $label
     * @param mixed $length maximum length. may be skipped and used as $attributes
     * @param ?array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addMailAttachmentField(mixed $name, mixed $label, mixed $length = "default", ?array $attributes = array()): void
    {

        if (is_array($length) && empty($attributes)) {
            //die("ddTextField: $name length is array");
            $attributes = $length;
        }
        if (empty($length) || !is_numeric($length)) {
            $length = "default";
        }

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_TEXT, $length), "mailattachment", $attributes);

    }

    /**
     * Summary of addTextField
     * @param string $name
     * @param string $label
     * @param ?array $attributes
     * @throws Exception
     */
    function addTextField2(string $name, string $label, ?array $attributes = array()): void
    {
        $this->addTextField($name, $label, "default", $attributes);
    }

    /**
     * Summary of addTextField
     * @param string $name
     * @param string $label
     * @param mixed $length maximum length. may be skipped and used as $attributes
     * @param ?array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addTextField(string $name, string $label, mixed $length = "default", ?array $attributes = array()): void
    {

        if (is_array($length) && empty($attributes)) {
            //die("ddTextField: $name length is array");
            $attributes = $length;
        }
        if (empty($length) || !is_numeric($length)) {
            $length = "default";
        }
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, $length), "text", $attributes);
    }

    /**
     * @throws Exception
     */
    function addCalculatedField(string $name, string $label, ?array $attributes = array()): void
    {

        $attributes[FORMSETTING::REQUIRED] = false; // never required

        if (!isset($attributes[FORMSETTING::HIDDENONEDIT]))
            $attributes[FORMSETTING::HIDDENONEDIT] = true;

        if (!isset($attributes[FORMSETTING::HIDDENONADD]))
            $attributes[FORMSETTING::HIDDENONADD] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_IGNORE, "text", $attributes);

    }

    /**
     * @throws Exception
     */
    function addSignatureField(string $name, string $label, ?array $attributes = array()): void
    {
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_TEXT, "signature", $attributes);
    }

    /**
     * @throws Exception
     */
    function addTextArea(string $name, string $label, ?array $attributes = array()): void
    {
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_TEXT, "textarea", $attributes);
    }

    /**
     * @throws Exception
     */
    function addTinyMceHtmlEditor(string $name, string $label, ?array $attributes = array()): void
    {
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_TEXT, "tinymce", $attributes);
    }

    /**
     * @throws Exception
     */
    function addTextFieldSimpleDate(string $name, string $label, ?array $attributes = array()): void
    {

        $attributes[FORMSETTING::TEXTFIELD_TYPE] = "date";

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_DATE, "text", $attributes);

    }

    /**
     * @throws Exception
     */
    function addTextFieldCurrency(string $name, string $label, ?array $attributes = array()): void
    {
        $attributes[LISTSETTING::CURRENCY_SYMBOL] = " EUR";
        $this->addTextFieldDecimal($name, $label, 4, $attributes);
    }

    /**
     * @throws Exception
     */
    function addTextFieldDecimal(string $name, string $label, int $digitsAfterSeparator = 2, ?array $attributes = array()): void
    {

        if (!isset($attributes[FORMSETTING::NUMBERFIELDS_STEP])) {
            $attributes[FORMSETTING::NUMBERFIELDS_STEP] = 1 / pow(10, $digitsAfterSeparator);
        }
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_DECIMAL, "20,$digitsAfterSeparator"), "text", $attributes);

    }

    /**
     * @throws Exception
     */
    function addFileField(string $name, string $label, bool $useExtendedUploader = true, ?array $attributes = array()): void
    {


        $attributes[FORMSETTING::USE_EXTENDED_UPLOADER] = $useExtendedUploader;
        $attributes[FORMSETTING::HAS_BLOBDATA] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, 2000), "file", $attributes);

    }

    /**
     * @throws Exception
     */
    function addQuickupload(string $name, string $label, bool $useExtendedUploader = true, ?array $attributes = array()): void
    {


        $attributes[FORMSETTING::USE_EXTENDED_UPLOADER] = $useExtendedUploader;
        $attributes[FORMSETTING::HAS_BLOBDATA] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, 2000), "quickupload", $attributes);

    }

    /**
     * @throws Exception
     */
    function addImageField(string $name, string $label, bool $useExtendedUploader = true, ?array $attributes = array()): void
    {

        $attributes[FORMSETTING::USE_EXTENDED_UPLOADER] = $useExtendedUploader;
        $attributes[FORMSETTING::HAS_BLOBDATA] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, 2000), "image", $attributes);
    }

    /**
     * @throws Exception
     */
    function addCheckbox(string $name, string $label, ?array $attributes = array()): void
    {

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_BOOLEAN, "checkbox", $attributes);


    }

    /**
     * @throws Exception
     */
    function addButton(string $name, string $label, ?string $icon, ?array $attributes = array()): void
    {

        if (empty($attributes[FORMSETTING::BUTTONICON_FORM])) {
            $attributes[FORMSETTING::BUTTONICON_FORM] = $icon;
        }

        if (empty($attributes[LISTSETTING::BUTTONICON_LIST])) {
            $attributes[LISTSETTING::BUTTONICON_LIST] = $icon;
        }

        if (empty($attributes[FORMSETTING::SHOWLABEL_IN_FORM])) {
            $attributes[FORMSETTING::SHOWLABEL_IN_FORM] = false;
        }

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_IGNORE, "button", $attributes);

    }

    /**
     * @throws Exception
     */
    function addForeignFieldAsLabel(string $name, string $label, string $displaytype = "label", ?array $attributes = array()): void
    {
        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_FOREIGN, $displaytype, $attributes);
    }

    /**
     * @throws Exception
     */
    function addGraphicalcombobox(string $name, string $label, ?array $attributes = array()): void
    {

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_INT, "graphicalcombobox", $attributes);


    }

    /**
     * Summary of addComboBoxIntkeybased
     * @param string $name
     * @param string $label
     * @param EnumHandlerInterface $entries
     * @param ?array $attributes
     * @return void
     * @throws Exception
     * @throws Exception
     */
    function addComboBoxIntkeybased(string $name, string $label, EnumHandlerInterface $entries, ?array $attributes = array()): void
    {
        $this->addComboBox($name, $this->translate($label), $entries, BpfwDbFieldType::TYPE_INT, $attributes);
    }

    /**
     * Summary of addComboBox
     * @param string $name
     * @param string $label
     * @param array|EnumHandlerInterface $entries
     * @param BpfwDbFieldType|string $type
     * @param ?array $attributes
     * @throws Exception
     */
    function addComboBox(string $name, string $label, array|EnumHandlerInterface $entries, BpfwDbFieldType|string $type = BpfwDbFieldType::TYPE_INT, ?array $attributes = array()): void
    {

        if (is_array($entries)) {
            $entries = new EnumHandlerArray($entries);
        }

        if (isset($attributes[VIEWSETTING::ENTRIES])) throw new Exception("set entries on addComboBox with entries parameter");

        $attributes[VIEWSETTING::ENTRIES] = $entries;

        //if($name == "productId")
        //throw new Exception("defaultvalue is '".$attributes[VIEWSETTING::DEFAULTVALUE]."' ".$this->prod);

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), $type, "combobox", $attributes);

    }

    /**
     * Summary of addComboBoxStringkeybased
     * @param string $name
     * @param string $label
     * @param EnumHandlerInterface $entries
     * @param ?array $attributes
     * @return void
     * @throws Exception
     * @throws Exception
     */
    function addComboBoxStringkeybased(string $name, string $label, EnumHandlerInterface $entries, ?array $attributes = array()): void
    {
        $this->addComboBox($name, $this->translate($label), $entries, BpfwDbFieldType::TYPE_STRING, $attributes);
    }

    /**
     * Summary of addModellist
     * @param string $name
     * @param string $label
     * @param string $modelname
     * @param array $modelfields
     * @param ?array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addModellist(string $name, string $label, string $modelname, array $modelfields, ?array $attributes = array()): void
    {

        if (!isset($attributes[VIEWSETTING::DATA_MODEL_FIELDS])) $attributes[VIEWSETTING::DATA_MODEL_FIELDS] = $modelfields;
        if (!isset($attributes[VIEWSETTING::DATA_MODEL])) $attributes[VIEWSETTING::DATA_MODEL] = $modelname;
        if (!isset($attributes[FORMSETTING::POSITION])) $attributes[FORMSETTING::POSITION] = POSITION_FULLWIDTH;
        if (!isset($attributes[LISTSETTING::HIDDENONLIST])) $attributes[LISTSETTING::HIDDENONLIST] = true;

        if (!isset($attributes[VIEWSETTING::DATA_MODEL_LIST_DISPLAYHANDLER])) {
            if (!isset($attributes[LISTSETTING::HIDDENONLIST])) $attributes[LISTSETTING::HIDDENONLIST] = true;
        } else {
            if (!isset($attributes[LISTSETTING::HIDDENONLIST])) $attributes[LISTSETTING::HIDDENONLIST] = false;
        }

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_IGNORE, "modellist", $attributes);

    }

    /**
     * Summary of addModellist
     * @param string $name
     * @param string $label
     * @param string $modelname
     * @param array $modelfields
     * @param array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addModellistFiltered(string $name, string $label, string $modelname, array $modelfields, array $attributes = array()): void
    {

        if (!isset($attributes[VIEWSETTING::DATA_MODEL_FIELDS])) $attributes[VIEWSETTING::DATA_MODEL_FIELDS] = $modelfields;
        if (!isset($attributes[VIEWSETTING::DATA_MODEL])) $attributes[VIEWSETTING::DATA_MODEL] = $modelname;
        if (!isset($attributes[FORMSETTING::POSITION])) $attributes[FORMSETTING::POSITION] = POSITION_FULLWIDTH;

        if (!isset($attributes[VIEWSETTING::DATA_MODEL_LIST_DISPLAYHANDLER])) {
            if (!isset($attributes[LISTSETTING::HIDDENONLIST])) $attributes[LISTSETTING::HIDDENONLIST] = true;
        } else {
            if (!isset($attributes[LISTSETTING::HIDDENONLIST])) $attributes[LISTSETTING::HIDDENONLIST] = false;
        }


        if (!isset($attributes[VIEWSETTING::DATA_MODEL_SORTFIELD])) $attributes[VIEWSETTING::DATA_MODEL_SORTFIELD] = 1;
        if (!isset($attributes[VIEWSETTING::DATA_MODEL_SORTORDER])) $attributes[VIEWSETTING::DATA_MODEL_SORTORDER] = "desc";
        if (!isset($attributes[VIEWSETTING::DATA_FILTER])) $attributes[VIEWSETTING::DATA_FILTER] = "formid";

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), BpfwDbFieldType::TYPE_IGNORE, "modellist", $attributes);

    }

    /**
     * Combobox consisting of a selection of images
     * @param string $name
     * @param string $label
     * @param array|EnumHandlerInterface $entries
     * @param BpfwDbFieldType|string $type
     * @param array $attributes
     * @throws Exception
     */
    function addImageComboBox(string $name, string $label, array|EnumHandlerInterface $entries, BpfwDbFieldType|string $type = BpfwDbFieldType::TYPE_INT, array $attributes = array()): void
    {

        if (is_array($entries)) {
            $entries = new EnumHandlerArray($entries);
        }

        if (isset($attributes[VIEWSETTING::ENTRIES])) throw new Exception("set entries on addComboBox with entries parameter");

        $attributes[VIEWSETTING::ENTRIES] = $entries;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), $type, "imagecombobox", $attributes);

    }

    /**
     * Label
     * @param string $name
     * @param string $label
     * @param array $attributes
     * @throws Exception
     */
    function addLabel(string $name, string $label, array $attributes = array()): void
    {


        $attributes[LISTSETTING::HIDDENONLIST] = true;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_NONE), "label", $attributes);

    }

    /**
     * Textfield with predifined combbox Values that can be used
     * @param string $name
     * @param string $label
     * @param array|EnumHandlerInterface $entries
     * @param string|int $length
     * @param array $attributes
     * @throws Exception
     * @throws Exception
     */
    function addTextfieldWidthComboboxInput(string $name, string $label, array|EnumHandlerInterface $entries, string|int $length = "default", array $attributes = array()): void
    {

        if (is_array($length)) {
            die($name . ': $length is array ');
        }

        if (empty($length) || !is_numeric($length)) {
            $length = "default";
        }

        $attributes[VIEWSETTING::ENTRIES] = $entries;

        if (!isset($attributes[FORMSETTING::COMBOBOX_PLACEHOLDER_SHOW])) {
            $attributes[FORMSETTING::COMBOBOX_PLACEHOLDER_SHOW] = true;
        }

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING, $length), "textwithcombobox", $attributes);

    }

    /**
     * Summary of addComboBoxMultiselect
     * @param string $name
     * @param string $label
     * @param EnumHandlerInterface|array $entries
     * @param string $type
     * @param array $attributes
     * @throws Exception
     */
    function addComboBoxMultiselect(string $name, string $label, EnumHandlerInterface|array $entries, string $type, array $attributes = array()): void
    {

        if (is_array($entries)) {
            $entries = new EnumHandlerArray($entries);
        }

        if (isset($attributes[VIEWSETTING::ENTRIES])) throw new Exception("set entries on addComboBoxMultiselect with entries parameter");

        $attributes[VIEWSETTING::ENTRIES] = $entries;
        $type = new BpfwDbFieldType(BpfwDbFieldType::TYPE_STRING); // handle multiple entries in one field require a string

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), $type, "comboboxmultiselect", $attributes);

    }

    /**
     * Summary of addComboBoxMultiselectLinktable
     * @param string $name
     * @param string $label
     * @param EnumHandlerDb $enumHandler
     * @param array $attributes
     * @throws Exception
     */
    function addComboBoxMultiselectLinktable(string $name, string $label, EnumHandlerDb $enumHandler, array $attributes = array()): void
    {

        global $database;

        /*
        if($rowdisplay == ''){
            $rowdisplay = $tablekey;
        }*/

        // "user", "userId", 'CONCAT_WS(", ",lastname,firstname)'
        //if(isset($attributes["entries"]))throw new Exception("please set entries on addComboBoxMultiselect with entries parameter");

        $attributes[VIEWSETTING::ENTRIES] = $enumHandler;

        $this->dbModel[$name] = new BpfwModelFormfield($name, $this->translate($label), new BpfwDbFieldType(BpfwDbFieldType::TYPE_LINK_TABLE, 'default', false), "comboboxmultiselect", $attributes);

    }

    public function setControl($control): void
    {
        $this->control = $control;
    }

    /**
     * wandelt ein array mit data und field einträgen in ein einfaches keyvalue array um
     * @param array $dataArray data field array
     * @return array kvp array
     */
    function CreatePlainKvpArrayFromArrayWithDataAndFieldinfo(array $dataArray): array
    {

        $retval = array();

        foreach ($dataArray as $key => $value) {
            if (is_array($value) && isset($value["data"])) {
                $retval[$key] = $value["data"];
            }
        }

        return $retval;

    }

    /**
     * get the name of the FilterValue Field in the model. The filter value separates groups from each other. for example a table orders might be filtered/grouped by UserId.
     * @return ?string
     * @throws Exception
     */
    public function getFilterName(): ?string
    {

        foreach ($this->getDbModel() as $key => $value) {
            if ($value->filterValue) {
                return $key;
            }
        }

        return null;

    }

    /**
     * get the parent model/The model this model is filtered by.
     *
     * @return string|null
     */
    public function tryGetFilterModelName(): ?string
    {

        if (isset($this->filteredModel)) {
            return $this->filteredModel;
        }

        return null;

    }

    /**
     * returns the primary key of the parent model by the primary key of the current model
     *
     * @param int $primaryKey primary key of this model
     * @return ?int
     * @throws Exception
     * @throws Exception
     */
    public function fetchParentIdByKey(int $primaryKey): ?int
    {

        $entry = $this->DbSelectSingleOrNullByKey($primaryKey);

        $parentkey = $this->tryGetParentKeyName();

        if (empty($entry) || empty($parentkey)) {
            return null;
        }

        return $parentkey[$parentkey];

    }

    /**
     * gibt Wert-Array zurück wenn primarykey=>value 1x existiert, sonst null(<0) oder Exception(>1)
     * @param mixed $keyvalue
     * @param bool $temptable
     * @return array|null
     * @throws Exception
     */
    public function DbSelectSingleOrNullByKey(mixed $keyvalue, bool $temptable = false): ?array
    {

        return $this->DbSelectSingleOrNull($this->getKeyName(), $keyvalue, $temptable);
    }

    /**
     * gibt Wert-Array zurück wenn key=>value 1x existiert, sonst null(<0) oder Exception(>1)
     * @param $key
     * @param $value
     * @param string $xtrawhere
     * @param bool $temptable
     * @return array|null
     * @throws Exception
     */
    public function DbSelectSingleOrNull($key, $value, string $xtrawhere = "", bool $temptable = false): ?array
    {

        $tn = $this->GetTableName();
        if ($temptable) {
            $tn = $this->GetTempTableName();
        }

        return $this->getDb()->makeSelectSingleOrNull($tn, $key, $value, $xtrawhere);
    }

    public function tryGetParentKeyName()
    {

        if (isset($this->filteredField)) {
            return $this->filteredField;
        }

        return null;

    }

    /**
     * converts array of key value arrays to array of dbModelEntry
     * @param array $keyValueArray array of key=>value arrays. first layer key is the id. Example: array(1=>array("val1"=>"123", "val2"=>23), 2=>array("val1"=>"Hello World", "val2"=>123));
     * @return DbSubmitValue[] immer DbModelEntry
     * @throws Exception
     */
    public function CreateDbSubmitValueArrayFromKeyValueArray(array $keyValueArray): array
    {

        if (empty($keyValueArray)) return array();

        if (current($keyValueArray) instanceof DbSubmitValue) {
            return $keyValueArray;
        }

        $dbModelEntryArray = array();

        foreach ($keyValueArray as $key => $value) {
            $dbModelEntryArray[$key] = new DbSubmitValue($key, $value, $this);
        }

        return $dbModelEntryArray;

    }

    /**
     * Creates an DbModelEntry object array. The object contains the values of all selected entries.
     * @param string $sqlAfterWhere SQL Content after WHERE
     * @param Database|null $db bestehende Datenbank-Klasse (optional, kann aus xpl neu erzeugt werden)
     * @param int $count
     * @param int $offset
     * @param DatabaseSortEntry[] $sort
     * @param string[] $extraFields key value von extrafeldern etwa array("newval"=>"calc(2+2)", "xy"=>"date_created+38974");
     * @param string $join
     * @param bool $temptable
     * @return DbModelEntry[]
     * @throws Exception
     */
    public function dbSelectAllAndCreateObjectArray(string $sqlAfterWhere = " 1", Database|null $db = null, int $count = -1, int $offset = 0, array $sort = array(), array $extraFields = array(), string $join = "", bool $temptable = false): array
    {
        $extraFields = bpfw_do_filter(DbModel::FILTER_EXTRAFIELDS, $this->GetTableName(), array($extraFields, $this, $this->GetTableName()), $this);
        if (empty($db)) {
            $db = static::getDb();
        }
        $values = $this->DbSelect($sqlAfterWhere, $db, $count, $offset, $sort, $extraFields, $join, $temptable);
        return $this->CreateDbModelEntryArrayFromKeyValueArray($values);
    }

    /**
     * baut aus einem select statement ein array aus get_called_class Objekten mit gefüllten Werten
     * @param string $query
     * @param Database|null $db bestehende Datenbank-Klasse (optional, kann aus xpl neu erzeugt werden)
     * @return DbModelEntry[] immer get_called_class()
     * @throws Exception
     */
    public function dbSelectAllFromPlainSqlAndCreateObjectArray(string $query, Database $db = null): array
    {

        if (empty($db)) {
            $db = $this->getDb();
        }

        return $this->CreateDbModelEntryArrayFromKeyValueArray($db->makeSelect($query));

    }

    /**
     * gibt Wert-Array zurück wenn primarykey=>value 1x existiert, sonst null(<0) oder Exception(>1)
     * @param $fkeyvalue
     * @param bool $temptable
     * @return array|null
     */
    public function DbSelectByForeignKey($fkeyvalue, bool $temptable = false): ?array
    {
        return $this->DbSelect($this->GetTableName() . "." . $this->tryGetParentKeyName() . " = '" . $fkeyvalue . "'", null, -1, 0, array(), array(), '', $temptable);
    }

    /**
     * gibt Key/Value-Array zurück, bei dem primarykey => $valueToSelect ist (nur ein wert)
     * @param $valueToSelect
     * @param $where
     * @param bool $temptable
     * @return array
     * @throws Exception
     */
    public function DbSelectKeyValueArray($valueToSelect, $where, bool $temptable = false): array
    {

        $tn = $this->GetTableName();
        if ($temptable) {
            $tn = $this->GetTempTableName();
        }

        return $this->getDb()->makeSelectValue($this->GetTableName(), $valueToSelect, $this->getKeyName(), $where);
    }

    /**
     * @throws Exception
     */
    public function updateLinkTableByKeyValueArray($kvp, $primaryKeyValue, $temptable = false): array|int
    {

        if (!is_object($kvp)) {
            //$kvp = $this->GetKeyValueArrayFromObject($kvp, false);

            $kvp = $this->CreateArrayWithDataAndFieldinfoFromPlainKvpArray($kvp, true, true);

        }

        if (!$this->checkUnique($kvp, $temptable)) return -1;


        // var_dump($kvp);
        // var_dump($primaryKeyValue);
        return $this->setLinkedTableValues($kvp, $primaryKeyValue);

    }

    /**
     * neuen Datenbankeintrag einfügen
     * @param array $keyValueArray
     * @param null $db (optional)
     * @param bool $ignoreConversion
     * @param bool $temptable
     * @return string|int
     * @throws Exception
     */
    public function DbUpdate(array $keyValueArray, $db = null, bool $ignoreConversion = false, bool $temptable = false): string|int
    {

        if (empty($db)) {
            $db = $this->getDb();
        }

        $model = $this->CreateArrayWithDataAndFieldinfoFromPlainKvpArray($keyValueArray);


        //$keyname = $this->getDbName();

        $keyname = $this->getKeyName();

        if (!isset($keyValueArray[$keyname])) {
            throw new Exception("Primary key '$keyname' not set on dbUpdate for " . $this->GetTableName());
        }

        if (!$this->checkUnique($keyValueArray, $temptable)) {
            return -1;
        }


        $keyvalue = $model[$keyname]->data; //$keyValueArray[$keyname];

        $model = $this->setLinkedTableValues($model);

        foreach ($model as $dbskey => $dbsubmitvalue) {
            $dbsubmitvalue->data = bpfw_getComponentHandler()->getComponent($dbsubmitvalue->getDbField()->display)->preProcessBeforeSql($dbsubmitvalue->data);
        }

        $key = new DatabaseKey($keyname, $keyvalue, $db->getSqlTypeFromFieldType($model[$keyname]->getDbField()->type));

        $tablename = $this->GetTableName();
        if ($temptable) {
            $tablename = $this->GetTempTableName();
        }

        $affected = $db->makeUpdate($model, $tablename, $key, $ignoreConversion);

        $this->updateLastUpdated($temptable, $key);

        return $affected;

    }

    /**
     * Datenbankeintrag anhand von Primärschlüssel löschen
     * @param int|string $keyvalue
     * @param int $temptable
     * @param database|null $db (optional)
     * @return int affected rows
     * @throws Exception
     */
    public function DbDeleteByPrimaryKey(int|string $keyvalue, bool $temptable = false, database $db = null): int
    {


        if (empty($db)) {
            $db = $this->getDb();
        }


        $contraints = $this->getConstraints();

        if (!$temptable && !empty($contraints)) {

            $db = $this->getDb();

            foreach ($contraints as $contraint) {

                $checktableModel = $this->createModelByName($contraint->checkTableName);

                $relevantValuesFound = $db->makeSelectAll($contraint->checkTableName, NULL, $contraint->checkTableForeignKeyName . " = '" . $keyvalue . "'");

                if (!empty($relevantValuesFound)) {


                    // Restrict wird bevorzugt, da sonst ggf. schon Veränderungen durchgeführt werden, die nicht sein sollen
                    if ($contraint->onDelete == FK_CONTRAINT_RESTRICT) {
                        foreach ($relevantValuesFound as $k => $v) {

                            bpfw_error_add(__("Can't be deleted, ") . $contraint->checkTableName . __(" is still using it with the id ") . $v[$checktableModel->getKeyName()]);

                        }
                    }


                }


            }

            foreach ($contraints as $contraint) {

                $checktableModel = $this->createModelByName($contraint->checkTableName);

                $relevantValuesFound = $db->makeSelectAll($contraint->checkTableName, NULL, $contraint->checkTableForeignKeyName . " = '" . $keyvalue . "'");

                if (!empty($relevantValuesFound)) {


                    switch ($contraint->onDelete) {

                        case FK_CONTRAINT_CASCADE:


                            foreach ($relevantValuesFound as $k => $v) {
                                if (!bpfw_error_hasErrors()) {
                                    $checktableModel->DbDeleteByPrimaryKey($v[$checktableModel->getKeyName()]);
                                }
                            }

                            break;

                        case FK_CONTRAINT_NULL:

                            $entriesDeletedCount = $db->makeQuery("UPDATE " . $contraint->checkTableName . " SET " . $contraint->checkTableForeignKeyName . " = NULL WHERE " . $contraint->checkTableForeignKeyName . " = '" . $keyvalue . "'");

                            $relevantValuesFound2 = $db->makeSelectAll($contraint->checkTableName, NULL, $contraint->checkTableForeignKeyName . " = '" . $keyvalue . "'");
                            if (!empty($relevantValuesFound2) || $entriesDeletedCount == 0) {
                                bpfw_error_add(__("Can't be deleted: error on FK_CONSTRAINT_NULL. Not all values could be deleted."));
                            }

                            break;

                    }


                }


            }


        }


        if (!bpfw_error_hasErrors()) {

            if (!$temptable)
                $this->deleteLinkedTableValues($keyvalue);


            // TODO: delete if temptable is not confirmed
            if (!$temptable) {
                $this->deleteAllAttachments($keyvalue, $temptable);
                $this->deleteAllImages($keyvalue, $temptable);
            }


            return $db->makeDelete($temptable ? $this->getTempTableName() : $this->GetTableName(), new DbSubmitValue($this->getKeyName(), $keyvalue, $this));

        }

        return 0;

    }

    /**
     * Summary of createModelByName
     * @param mixed $name
     * @param bool $cache
     * @return DbModel
     * @throws Exception
     */
    static function createModelByName(string $name, bool $cache = false): DbModel
    {

        return bpfw_createModelByName($name, $cache);

    }

    /**
     * @throws Exception
     */
    public function deleteLinkedTableValues($keyvalue): void
    {

        if (empty($db)) {
            $db = $this->getDb();
        }


        //$keyname = $this->getDbName();

        $keyname = $this->getKeyName();

        $linkfields = $this->getAllLinkTableFields();

        foreach ($linkfields as $linkfieldkey => $linkfieldvalue) {


            $table = $this->getLinkTableName($linkfieldkey);

            // alte Werte löschen
            $db->makeDeleteByWhere($table, " $keyname = $keyvalue");


        }

    }

    /**
     * @throws Exception
     */
    function deleteAllAttachments($keyvalue, $temptable): void
    {

        if (empty($keyvalue) || !is_numeric($keyvalue)) return;

        $this->getKeyName();
        $filefields = $this->getAllFieldsWithDisplayType("file");
        if (!empty($filefields)) {

            $data = $this->DbSelect($this->getKeyName() . " =  '" . $keyvalue . "'", bpfw_getDb(), -1, 0, array(), array(), '', $temptable);

            foreach ($filefields as $key => $values) {
                foreach ($data as $row) {

                    if (!empty($row[$key])) {

                        $fileinfos = json_decode($row[$key]);

                        if (file_exists(UPLOADS_PATH . $fileinfos->new_name)) {
                            unlink(UPLOADS_PATH . $fileinfos->new_name);
                        }

                    }

                }

            }

        }

    }

    /**
     * @throws Exception
     */
    function getAllFieldsWithDisplayType($type): array
    {

        $retval = array();

        foreach ($this->getDbModel() as $key => $value) {

            if ($value->display == $type) {
                $retval[$key] = $value;
            }

        }

        return $retval;

    }

    /**
     * @throws Exception
     */
    function deleteAllImages($keyvalue, $temptable): void
    {

        if (empty($keyvalue) || !is_numeric($keyvalue)) return;

        $this->getKeyName();
        $imgfields = $this->getAllFieldsWithDisplayType("image");
        if (!empty($imgfields)) {

            $data = $this->DbSelect($this->getKeyName() . " =  '" . $keyvalue . "'", bpfw_getDb(), -1, 0, array(), array(), '', $temptable);

            foreach ($imgfields as $key => $values) {
                foreach ($data as $row) {

                    if (!empty($row[$key])) {

                        $fileinfos = json_decode($row[$key]);

                        if (file_exists(UPLOADS_PATH . $fileinfos->new_name)) {
                            unlink(UPLOADS_PATH . $fileinfos->new_name);
                        }

                    }

                }

            }

        }

    }

    /**
     * Summary of findDbField
     * @param string $fieldid
     * @return BpfwModelFormfield|null
     * @throws Exception
     */
    function findDbField(string $fieldid): ?BpfwModelFormfield
    {

        $dbmodel = $this->getDbModel();

        if (!empty($dbmodel[$fieldid])) {
            return $dbmodel[$fieldid];
        }

        return null;

    }

    /**
     * @throws Exception
     */
    public function DBSetAutoincrement($value): void
    {
        $this->getDb()->setAutoincrement((int)$value, $this->GetTableName());
    }

    /**
     * Summary of createLinkTableInDatabase
     * @param string $field
     * @throws Exception
     */
    function createLinkTableInDatabase(string $field): void
    {

        $newlinktablemodel = $this->generateLinkTableModel($field);


        $newlinktablemodel->createTable();


    }

    /**
     * @throws Exception
     */
    function createTable($tempTable = false, $ifnotExists = true, $print = false): mysqli_result|bool
    {

        bpfw_setCreatingTables(true);

        $this->clearModelCache();

        $dbmodel = $this->getDbModel();

        $table = $this->GetTableName();

        if(empty($dbmodel)){
            if($print){
                echo "skipping $table because it has no modelentries";
            }
            return false;
        }


        if ($tempTable) {
            $table = $this->GetTempTableName();
        }


        $sql = "CREATE TABLE" . " ";

        if ($ifnotExists)
            $sql .= "IF NOT EXISTS ";

        $sql .= "`$table`(";


        // BpfwModelFormfield

        $first = true;
        $keyDefinitionSql = "";

        foreach ($dbmodel as $key => $field) {

            $newlength = $field->type->length;
            $newtype = $field->type->type;
            $name = $key;

            $typeForNew = $newtype;
            if (!empty($newlength) && $newlength != "default") {
                $typeForNew = "$newtype($newlength)";
            }

            if ($field->type->type == BpfwDbFieldType::TYPE_FOREIGN) continue;

            if ($field->type->type == BpfwDbFieldType::TYPE_IGNORE) continue;

            if ($field->type->type == BpfwDbFieldType::TYPE_LINK_TABLE) {

                $this->createLinkTableInDatabase($key);

            } else {

                if ($first) {
                    $first = false;
                } else {
                    $sql .= ", ";
                }

                $defaultvalue = $field->getDefaultForMysql();

                $sqldefault = "";

                if (!$field->primaryKey) {
                    $sqldefault = "NULL";
                }

                if(is_array($defaultvalue)){
                    // TODO: fix: should not happen, but happens on create table of link_examplecomplex_notify_changes

                    if(empty($defaultvalue)){
                        $defaultvalue="";
                    }else{
                        $defaultvalue = array_key_first($defaultvalue);
                    }
                }

                if (!empty($defaultvalue)) {
                    if ($defaultvalue == "NOW()") {
                        $sqldefault .= " DEFAULT NOW()";
                    } else if ($defaultvalue == "NULL") {
                        $sqldefault .= " DEFAULT NULL";
                    } else {
                        $sqldefault .= " DEFAULT '" . $this->getDb()->escape_string($defaultvalue) . "' ";
                    }
                }


                if (!$field->primaryKey) {
                    $sql .= "`$name` $typeForNew $sqldefault";
                } else {

                    if (!in_array($newtype, BpfwDbFieldType::STRING_TYPES) && !in_array($newtype, BpfwDbFieldType::DOUBLE_TYPES) && !in_array($newtype, BpfwDbFieldType::BLOB_TYPES)) {
                        $sql .= "`$name` $typeForNew NOT NULL AUTO_INCREMENT";
                    } else {
                        if ($newlength == "default") $newlength = "255";
                        $sql .= "`$name` $typeForNew NOT NULL";
                    }
                    $keyDefinitionSql = ", PRIMARY KEY (`$name`)";

                }

            }

        }

        $sql .= $keyDefinitionSql;

        $sql .= ") ENGINE=InnoDB CHARACTER SET utf8mb4;"; // utf8?

        if ($print)
            echo "executing" . $sql;

        bpfw_setCreatingTables(false);

        $retval = $this->getDb()->makeQuery($sql);

        $err = $this->getDb()->getLastError();
        if($err != "ok")echo $err;

        return $retval;

    }

    function clearModelCache(): void
    {
        $this->_dbmodelCache = null;
    }

    /**
     * @throws Exception
     */
    function deleteTable(): mysqli_result|bool
    {

        $table = $this->GetTableName();

        $sql = "DROP TABLE `$table`";

        return $this->getDb()->makeQuery($sql);

    }

    /**
     * @throws Exception
     */
    function deleteTempTable(): mysqli_result|bool
    {

        $table = $this->GetTempTableName();

        $sql = "DROP TABLE `$table`";

        return $this->getDb()->makeQuery($sql);

    }

    /**
     * @throws Exception
     */
    function createTempTableIfNotExists(): void
    {
        if (!$this->checkTableExists($this->GetTempTableName())) {
            ob_start();
            $this->createTempTable();
            ob_get_clean();
        }
    }

    /**
     * @throws Exception
     */
    function checkTableExists($tablename_search): bool
    {

        $db = $this->getDb();


        $tables = $db->getAllTables();

        if (in_array($tablename_search, $tables)) {
            return true;
        }

        // var_dump($retval);

        return false;

    }

    /**
     * @throws Exception
     */
    function createTempTable(): void
    {

        $this->createTable(true);

    }

    /**
     * @throws ReflectionException
     * @throws Exception
     * @throws Exception
     */
    function getAllDbModelsWithTable(): array
    {

        $dbdata = $this->getDbData();
        $modelobjects = dbModel::getAllModels();

        $retval = array();

        foreach ($modelobjects as $keyname => $modelobject) {

            $model = $modelobject->getDbModel();

            if (empty($model)) {
                // echo "skipping $keyname<br>";
                continue; // no db for this model, skip
            }

            if (empty($dbdata[$keyname])) {
                continue;
            }

            $retval[$keyname] = $model;

        }

        return $retval;

    }

    /**
     * @throws Exception
     */
    function getDbData(): array
    {

        $db = $this->getDb();

        $retval = array();

        $tables = $db->getAllTables();

        foreach ($tables as $tablename) {
            $tableinfo = $db->getTableInfo($tablename);

            $retval[$tablename] = $tableinfo;
        }

        // var_dump($retval);

        return $retval;
    }

    /**
     * Summary of getAllModels
     * @return BpfwModel[]
     * @throws ReflectionException
     */
    static function getAllModels($ignoreModelsArray = array()): array
    {

        // allow array of string values in ignore
        foreach($ignoreModelsArray as $k=> $v){
            if(is_string($v)) {
                $ignoreModelsArray[$v] = $v;
            }
        }

        $appmodels = dbModel::scanForModels(APP_MVC_PATH . "models".DIRECTORY_SEPARATOR, $ignoreModelsArray );
        $parentmodels = dbModel::scanForModels(PARENT_MVC_PATH . "models".DIRECTORY_SEPARATOR, array_merge($appmodels, $ignoreModelsArray));
        $bpfwmodels = dbModel::scanForModels(BPFW_MVC_PATH . "models".DIRECTORY_SEPARATOR, array_merge($appmodels, $parentmodels, $ignoreModelsArray));

        return array_merge($appmodels, $parentmodels, $bpfwmodels);

    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    static function scanForModels($path, $ignore = array()): array
    {

        $retval = array();

        if (!file_exists($path)) return array();

        foreach (scandir($path) as $key => $filename) {

            // echo "<br>".$filename;

            if (bpfw_strEndsWith(strtolower($filename), "model.inc.php")) {

                // echo " - found";

                // echo $path.$filename."<br>";

                $classidentifier = str_replace("model.inc.php", "", strtolower($filename));

                if (isset($ignore[$classidentifier])) continue;

                require_once($path . $filename);

                $classnames = array(ucwords($classidentifier) . "Model", strtolower($classidentifier) . "Model", strtolower($classidentifier . "Model"), $classidentifier . "Model");

                //var_dump($classnames);
                $classFound = NULL;
                foreach ($classnames as $val) {
                    if (class_exists($val)) {

                        $classFound = $val;


                    }
                }

                if (is_object($classFound) || is_string($classFound)) {

                    if (!(new ReflectionClass($classFound))->isAbstract()) {
                        $retval[$classidentifier] = new $classFound();
                    }

                } else {

                    throw new Exception("no valid class or string: key:$key filename:$filename '" . print_r($classFound, true) . "'");

                }

            }

        }

        return $retval;


    }

    /**
     * @throws ReflectionException
     * @throws Exception
     * @throws Exception
     */
    function getAllModelsWithTable(): array
    {

        $dbdata = $this->getDbData();
        $modelobjects = dbModel::getAllModels();

        $retval = array();

        foreach ($modelobjects as $keyname => $modelobject) {

            $dbmodel = $modelobject->getDbModel();

            if (empty($dbmodel)) {
                // echo "skipping $keyname<br>";
                continue; // no db for this model, skip
            }


            if (empty($dbdata[$keyname])) {
                continue;
            }

            $retval[$keyname] = $modelobject;

        }

        return $retval;

    }


}
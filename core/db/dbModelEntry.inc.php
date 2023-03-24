<?php /** @noinspection PhpUnused */

/**
 * can be seen as a lighter version of DbModel that is limited to contain the data of one Database row
 */
class DbModelEntry
{

    var DbModel $model;

    /**
     * @param DbModel $model
     * @param iterable $values class or kvp array to fill in variables.
     * @param bool $autocompleteVariables automatically add missing variables to the class if existing in the model
     * @throws Exception
     */
    public function __construct(DbModel $model, mixed $values = null, bool $autocompleteVariables = true)
    {

        $this->model = $model;

        foreach ($model->getDbModel() as $key => $value) {

            if (!property_exists($this, $key)) {
                $this->{$key} = null;
            }

        }

        if ($autocompleteVariables && $values != null) {
            $this->autofill($values);
        }

    }

    /**
     * autofill values by KVPArray or instance of DbModel
     * @param array|object $autofillData
     * @return bool
     * @throws Exception
     */
    public function autofill(array|object $autofillData): bool
    {


        if ($autofillData == null) return false;

        $dbm = $this->model->getDbModel();

        $didSomething = false;

        if (is_array($autofillData)) {

            foreach ($autofillData as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $this->valueCheckAndConvert($key, $value);
                    $didSomething = true;
                }
            }

        } else if (is_object($autofillData)) {
            foreach ($dbm as $key => $value) {
                if (property_exists($autofillData, $key) && property_exists($this, $key)) {
                    $this->$key = $this->valueCheckAndConvert($key, $value);
                    $didSomething = true;
                }
            }
        }

        return $didSomething;

    }

    /**
     * checks if the type matches dbModel
     * int strings will be converted to int, float strings to float
     * @param string $key name as in DbModel
     * @param mixed $value value
     * @return mixed converted $value
     * @throws Exception
     */
    function valueCheckAndConvert(string $key, mixed $value): mixed
    {

        if (!is_object($this->model)) {
            throw new Exception("model is no object: " . print_r($this->model, true));
        }

        $dbm = $this->model->getDbModel();

        if (!isset($dbm[$key]) || !isset($dbm[$key]->type)) {
            return $value;
        }

        $type = $dbm[$key]->type;

        if ($type == "long" || $type == "int") {
            if (is_numeric($value)) {
                if ($type == "int") {
                    return (int)$value;
                }

                return (float)$value;

            }
        }

        return $value;

    }

    /**
     * convert dbModelEntry values to kvp Array
     * @return array[]
     * @throws Exception
     * @var bool $removeKeyValueFromReturn remove key Value from return
     */
    function GetKeyValueArray(bool $removeKeyValueFromReturn = true, bool $enumValuesInsteadOfKeys = false): array
    {
        return $this->model->GetKeyValueArrayFromObject($this, $removeKeyValueFromReturn, $enumValuesInsteadOfKeys);
    }

    /**
     * Bekommt Primärschlüssel und setzt Werte der Klasse automatisch
     * @param mixed $keyValue primary key value
     * @return boolean
     * @throws Exception
     */
    public function DbLoadObjectByPrimaryKey(mixed $keyValue): bool
    {

        $data = $this->model->DbSelectSingleOrNullByKey($keyValue);

        $this->autofill($data);

        return $data != null;
    }

    /**
     * loads dbModelEntry by primary key into current object
     * @param string $key keyName
     * @param mixed $value keyValue
     * @return boolean
     * @throws Exception
     */
    public function DbLoadObjectByKeyValue(string $key, mixed $value): bool
    {

        $data = $this->model->DbSelectSingleOrNull($key, $value);

        $this->autofill($data);

        return $data != null;
    }

}
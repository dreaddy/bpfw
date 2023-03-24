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
/** @noinspection SqlWithoutWhere */
/** @noinspection SqlResolve */

const FK_CONTRAINT_CASCADE = "FK_CONTRAINT_CASCADE";
const FK_CONTRAINT_NULL = "FK_CONTRAINT_NULL";
const FK_CONTRAINT_RESTRICT = "FK_CONTRAINT_RESTRICT";

class DatabaseFKConstraint
{

    var string $constraintName;
    var string $checkTableName;
    var string $checkTablePrimaryKeyName;
    var string $checkTableForeignKeyName;
    var string $onDelete = FK_CONTRAINT_RESTRICT;

    //var $onupdate = FK_CONTRAINT_CASCADE;

    function __construct(string $constraintName, string $checkTableName, string $checkTablePrimaryKeyName, string $checkTableForeignKeyName, string $onDelete = FK_CONTRAINT_RESTRICT)
    {

        $this->constraintName = $constraintName;

        $this->checkTableName = $checkTableName;
        $this->checkTablePrimaryKeyName = $checkTablePrimaryKeyName;
        $this->checkTableForeignKeyName = $checkTableForeignKeyName;

        $this->onDelete = $onDelete;

    }

}

class DatabaseSortEntry
{

    const DIRECTION_ASC = "asc";
    const DIRECTION_DESC = "desc";
    public string $fieldName;
    public string $direction;
    public bool $addTableName;
    public bool $calculatedField;

    /**
     * @throws Exception
     */
    function __construct(string $fieldName, string $direction, bool $addTableName = true, bool $calculatedField = false)
    {

        $direction = strtolower($direction);

        if ($direction != DatabaseSortEntry::DIRECTION_ASC && $direction != DatabaseSortEntry::DIRECTION_DESC) {
            throw new Exception ("direction must be asc or desc");
        }

        $this->fieldName = $fieldName;
        $this->direction = $direction;
        $this->calculatedField = $calculatedField;
        $this->addTableName = $addTableName;

    }
}

class DatabaseKey
{

    var string $Name;
    var string $type;
    private mixed $value;

    function __construct($name, $value, $type = 'i')
    {
        $this->Name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @throws Exception
     */
    function GetValue()
    {

        if (is_object($this->value)) { //is_object($this->value)){

            if (get_class($this->value) == "DatabaseKey") { // TODO: hä?
                return $this->value->getValue();
            }

        }

        if (!is_array($this->value)) {

            return $this->value;

        } else if (isset($this->value["data"])) {

            return $this->value["data"];

        } else {
            throw new Exception("cant read Value as string or data");
        }

    }

    function SetValue(mixed $value): void
    {
        $this->value = $value;
    }


}

class Database
{

    /**
     * Summary of $connection
     * @var ?mysqli $connection
     */
    var ?mysqli $connection = null;
    var string $host;

    // TODO: besser machen, am besten typ und dbtyp trennen
    var string $username;
    var string $password;
    var string $database;

    /**
     * @throws Exception
     */
    function __construct(string $host = null, string $username = null, string $password = null, string $database = null)
    {

        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        if (!empty($host)) {
            $this->connection = $this->getConnection();
        }

    }

    /**
     * @throws Exception
     */
    function getAffectedRows(): int
    {
        return $this->getConnection()->affected_rows;
    }

    /**
     * @throws Exception
     */
    function getConnection(): mysqli
    {
        if ($this->connection == null) {
            $this->connection = $this->refreshConnection();
        }
        return $this->connection;
    }

    /**
     * @throws Exception
     */
    function refreshConnection(): mysqli
    {
        //echo "$this->host, $this->username, $this->password, $this->database";
        if(!empty($this->host) && !empty($this->username) && !empty($this->database)) {

            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

            $this->connection->set_charset("utf8mb4");
            if ($this->connection->connect_errno > 0) {
                $this->connection->close();
                throw new Exception('Unable to connect to database [' . $this->connection->connect_error . ']');
            }

            // $this->makeQuery("SET NAMES utf8");
        }

        return $this->connection;
    }

    function escape_string($string): string
    {

        if(is_array($string)){

            foreach($string as $k=> $v){
                $string[$k]=$this->connection->escape_string((string)$v);
            }
            return json_encode($string);
        }

        if($string !== null)
        return $this->connection->escape_string($string);

        return "";

    }

    function getValueByKey($tableName, $keyName, $keyValue, $select)
    {
        $query = "select $select as value from `$tableName` where $keyName = '$keyValue'";
        $result = $this->makeSelect($query);

        if (empty($result)) return null;
        return $result[0]["value"];
    }

    /**
     * Summary of makeSelect
     * @param string $sql
     * @param mixed $key
     * @return array
     */
    function makeSelect(string $sql, mixed $key = null): array
    {


        if (!$result = $this->connection->query($sql)) {
            // echo "<p class='error'>error on Query: ".$sql." ".$this->connection->error."</p>";die();
//            $trace = bpfw_debug_string_backtrace();
//            echo $trace;
//            die($trace);
            bpfw_error_add("<p class='error'>error on select Query: " . $sql . " " . $this->connection->error . "</p>");
            // echo "error".$this->connection->error;
            return array();
            // throw new \Exception('There was an error running the query [' . $this->connection->error . ']');
        }

        $data = array();

        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }

        bpfw_log_sql("makeSelect:" . $sql, "select");

        while ($row = $result->fetch_assoc()) {
            if ($key != null)
                $data[$row[$key]] = $row;
            else
                $data[] = $row;
        }

        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeSelect query took " . (microtime(true) - $microtime));
        }

        return $data;

    }

    function getTableInfo($tableName): array
    {

        $query = "DESCRIBE `$tableName`";
        $result = $this->makeSelect($query);

        $retval = array();

        foreach ($result as $value) {
            $retval[$value["Field"]] = $value;
        }


        /* $retval = array();
         if(is_array($result)){

             foreach($result as $value){
                 $retval[] = current($value);
             }

         }*/


        return $retval;

    }

    function getAllTables(): array
    {
        $query = "SHOW TABLES";
        $result = $this->makeSelect($query);

        $tableNames = array();
        if (!empty($result)) {

            foreach ($result as $value) {
                $entry = current($value);
                $tableNames[] = $entry;
            }

        }

        return $tableNames;
    }

    /**
     * @throws Exception
     */
    function setMysqliConnection($connection): void
    {
        if (!empty($connection)) {
            $this->connection = $connection;
        } else {
            throw new Exception("no Connection given");
        }
    }

    function fetchKeyValueArray($table, $key, $value, $where = "", $prettifyfunction = null)
    {

        if (!empty($where)) $where = " WHERE $where";

        $sql = "select $key as `key`, $value as value from `$table` $where";

        // if($table == "productgallery")
        // echo $sql;

        $assoc = $this->makeSelect($sql, "key");

        $retval = array();

        foreach ($assoc as $key => $value) {
            $retval[$value['key']] = $value['value'];
        }

        if (!empty($prettifyfunction)) {
            $retval = call_user_func($prettifyfunction, array($retval));
        }

        // if($table== "productcategory"){  echo($sql); var_dump($this); echo "findme"; }


        return $retval;

    }

    function fetchKeyValueArrayWithEmpty($table, $key, $value, $where = "", $nullText = "Nicht gesetzt", $prettifyfunction = null)
    {


        if (!empty($where)) $where = " WHERE $where";

        $sql = "select $key as `key`, $value as `value` from `$table` $where";

        $assoc = $this->makeSelect($sql, "key");

        $retval = array();

        $retval[""] = "$nullText";

        foreach ($assoc as $key => $value) {
            $retval[$value['key']] = $value['value'];

        }

        if (!empty($prettifyfunction)) {

            $retval = call_user_func($prettifyfunction, array($retval));

        }

        return $retval;

    }

    /**
     * @throws Exception
     */
    function makeSelectSingleOrNullBySql(string $sql): mixed//, $value = null /*,  $param = "", $paramType = ""*/)
    {
        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }

        $stmt = $this->connection->prepare($sql);
        if ($this->connection->error != "") {
            bpfw_log_sql("makeSelectSingleOrNullBySql error: " . $sql, "error");
            throw new Exception('makeSelectSingleOrNullBySql There was an error running the query [' . $this->connection->error . ']');
        }

        bpfw_log_sql("makeSelectSingleOrNullBySql:" . $sql . $this->connection->error);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($this->connection->error != "") {
            bpfw_log_sql("makeSelectSingleOrNullBySql error: " . $sql, "error");
            throw new Exception('makeSelectSingleOrNullBySql There was an error running the query [' . $this->connection->error . ']');
        }


        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        foreach ($data as $value) {
            $this->processDataFieldAfterSelect($value);
        }

        if (count($data) > 1) {
            bpfw_log_sql("makeSelectSingleOrNullBySql query has more than one result: " . $sql . print_r($data, true), "error");
            throw new Exception("query has more than one result: " . $sql . " " . print_r($data, true));
        }

        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeSelectSingleOrNullBySql query took " . (microtime(true) - $microtime));
        }

        if (!empty($data)) {
            return current($data);
        }

        return null;

    }

    function processDataFieldAfterSelect($field): void
    {
        //  var_dump($field);
    }

    /**
     * @throws Exception
     */
    function makeSelectSingleOrNull($tablename, $key, $value, $where = ""/*,  $param = "", $paramType = ""*/): bool|array|null
    {

        if (!empty($where)) {
            $where = " AND ($where)";
        }


        return $this->makeSelectSingleOrNullByWhere($tablename, "$key = ? $where ORDER BY $key", $value);

    }

    /**
     * @return false|array|null
     * @throws Exception
     */
    function makeSelectSingleOrNullByWhere($tableName, $where, $value = null /*,  $param = "", $paramType = ""*/): bool|array|null
    {
        if (!empty($where)) $where = " WHERE $where";

        $sql =
            "SELECT * FROM `$tableName` $where";

        $stmt = $this->connection->prepare($sql);
        if ($this->connection->error != "") {
            bpfw_log_sql('makeSelectSingleOrNullByWhere There was an error running the query [' . $this->connection->error . ']', "error");
            throw new Exception('makeSelectSingleOrNullByWhere There was an error running the query [' . $this->connection->error . ']');
        }

        $param = array();
        if ($value !== null) {

            $param = $value;

            $paramType = 's';

            if (is_numeric($value)) {
                $paramType = "i";
            }

            $stmt->bind_param($paramType, $param);
        }

        $microtime = microtime(true);
        bpfw_log_sql("makeSelectSingleOrNullByWhere:" . $sql . print_r($param, true));
        $stmt->execute();

        $result = $stmt->get_result();

        if ($this->connection->error != "") {
            bpfw_log_sql('makeSelectSingleOrNullByWhere There was an error running the query [' . $this->connection->error . ']', "error");
            throw new Exception('makeSelectSingleOrNullByWhere There was an error running the query [' . $this->connection->error . ']');
        }

        if (!$result) {
            bpfw_log_sql('makeSelectSingleOrNullByWhere There was an error running the query ' . $sql . " " . print_r($param, true) . '[' . $this->getLastError() . ']', "error");
            throw new Exception('makeSelectSingleOrNullByWhere There was an error running the query ' . $sql . " " . print_r($param, true) . '[' . $this->getLastError() . ']');
        }

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        foreach ($data as $value) {
            $this->processDataFieldAfterSelect($value);
        }

        bpfw_log_sql("makeSelectSingleOrNullByWhere query took " . (microtime(true) - $microtime));

        if (count($data) > 1) {
            bpfw_log_sql("makeSelectSingleOrNullByWhere query has more than one result: " . $sql . " " . print_r($data, true), "error");
            throw new Exception("makeSelectSingleOrNullByWhere query has more than one result: " . $sql . " " . print_r($data, true));
        }

        if (!empty($data)) {
            return current($data);
        }

        return null;

    }

    function getLastError(): string
    {
        if ($this->connection) {
            if (empty($this->connection->error)) {
                return "ok";
            }
            return $this->connection->error;
        }

        return "ok";
    }

    function setAutoincrement($value, $tablename): void
    {
        $value = (int)$value;
        $tablename = mysqli_real_escape_string($this->connection, $tablename);
        $sql = "ALTER TABLE `$tablename` AUTO_INCREMENT=$value;";
        bpfw_log_sql("setAutoincrement:" . $sql);
        $this->connection->real_query($sql);
    }

    /**
     * @throws Exception
     */
    function getUserLoginByAuth($username, $password): bool|array|null
    {

        if (empty($password)) return null;
        if (empty($username)) return null;

        $hash = md5($password);

        $sql = "select * from user where password like ? and ( username like ? or email like ? )";

        $stmt = $this->connection->prepare($sql);
        if ($this->connection->error != "") {

            throw new Exception('There was an error running the getUserLoginByAuth [' . $this->connection->error . ']');
        }
        $stmt->bind_param("sss", $hash, $username, $username);

        bpfw_log_sql("getUserLoginByAuth:" . $username . $sql);
        $stmt->execute();


        if ($this->connection->error != "") {

            throw new Exception('There was an error running the getUserLoginByAuth [' . $this->connection->error . ']');
        }


        //$result="";
        $result = $stmt->get_result();
        $retval = $result->fetch_assoc();
        //$stmt->bind_result($result);
        //$stmt->fetch();
        $stmt->close();

        return $retval;

    }

    /**
     * @throws Exception
     */
    function getCustomerLoginByAuth(string $username, string $password, array $fieldsToCheck = array("username", "email", "customerNr")): bool|array|null
    {

        if (empty($password)) return null;
        if (empty($username)) return null;

        $hash = md5($password);

        $checkfieldscount = count($fieldsToCheck);

        $querypart = "( ";
        $bindparamtypes = "s";
        $first = true;
        foreach ($fieldsToCheck as $name) {
            if (!$first) {
                $querypart .= " or ";
            }
            $first = false;
            $querypart .= "$name like ?";
            $bindparamtypes .= "s";
        }
        $querypart .= " )";

        $sql = "select * from customer where password like ? and $querypart";

        $stmt = $this->connection->prepare($sql);
        if ($this->connection->error != "") {

            throw new Exception('There was an error running the getUserCustomerByAuth [' . $this->connection->error . ']');
        }

        $i = 0;
        $referencedValues = array();
        $referencedValues[] = $bindparamtypes;
        $referencedValues[] = $hash;
        foreach ($fieldsToCheck as $fieldToCheck) {
            $referencedValues[] = $username;
        }
        foreach ($referencedValues as $referencedValue) {
            //  {$value_$i} = $keyobj->GetValue();
            $a_params[] = &$referencedValues[$i++];
        }

        // var_dump($referencedValue);

        // var_dump($a_params);


        call_user_func_array(array($stmt, 'bind_param'), $a_params);


        // $stmt->bind_param("ssss", $hash, $username, $username, $username);

        bpfw_log_sql("getCustomerLoginByAuth:" . $username . $sql);
        $stmt->execute();


        if ($this->connection->error != "") {

            throw new Exception('There was an error running the getUserCustomerByAuth [' . $this->connection->error . ']');
        }


        //$result="";
        $result = $stmt->get_result();
        $retval = $result->fetch_assoc();
        //$stmt->bind_result($result);
        //$stmt->fetch();
        $stmt->close();

        return $retval;

    }

    /**
     * @throws Exception
     */
    function countTableEntries($tablename, $key, $where = '', $join = '')
    {

        if (!empty($where)) {
            $where = " WHERE $where";
        }

        $sql = "Select COUNT(`$tablename`.`$key`) as count FROM `$tablename` $join $where";

        // echo $sql;
        $result = $this->makeSelect($sql, "count");

        if ($this->connection->error != "") {
            debug_print_backtrace();
            throw new Exception('There was an error running the countTableEntries [' . $this->connection->error . '] \r\n\r\n query:' . $sql);
        }

        return current($result)["count"];

    }

    /**
     * Summary of makeSelectAll
     * @param string $tablename
     * @param null $key
     * @param string $where
     * @param ?int $count
     * @param ?int $offset
     * @param ?DatabaseSortEntry|DatabaseSortEntry[] $sort
     * @param string[] $extrafields key value von extrafeldern etwa array("newval"=>"calc(2+2)", "xy"=>"date_created+38974");
     * @param string $join
     * @return array
     * @throws Exception
     */
    function makeSelectAll(string $tablename, $key = NULL, string $where = "", ?int $count = -1, ?int $offset = 0, null|DatabaseSortEntry|array $sort = array(), array $extrafields = array(), string $join = ""/*,  $param = "", $paramType = ""*/): array
    {

        if (!is_array($sort)) {
            $entry = $sort;
            $sort = array();

            if (!empty($entry)) {
                $sort[] = $entry;
            }
        }


        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }


        if (!empty($where)) $where = "WHERE $where";

        $param = "";
        $paramType = "";


        $extrafieldsstr = " ";

        if (!empty($extrafields)) {
            foreach ($extrafields as $name => $value) {

                $extrafieldsstr .= ",";

                $extrafieldsstr .= "$value as $name";

            }
            $extrafieldsstr .= " ";
        }


        $sql = "SELECT `$tablename`.* $extrafieldsstr FROM `$tablename` $join $where";


        if ($key != NULL) {
            //  $sql =
            //  "SELECT * FROM `$tablename` $where ORDER BY $key";
            $sort[] = new DatabaseSortEntry($key, DatabaseSortEntry::DIRECTION_ASC);

        }


        $fieldnames_used = array();

        if ($key != null)
            $sql .= " GROUP BY `$tablename`.$key ";

        if (!empty($sort)) {

            $sql .= " ORDER BY";

            $first = true;

            foreach ($sort as $orderpart) {

                if (isset($fieldnames_used[$orderpart->fieldName])) continue;

                if (!$first) {
                    $sql .= ",";
                } else {
                    $sql .= " ";
                    $first = false;
                }


                if (isset($orderpart->calculatedField)) {
                    $sql .= $orderpart->fieldName;
                } else {

                    if (isset($extrafields[$orderpart->fieldName])) {

                        $sql .= $orderpart->fieldName;
                    } else {
                        if ($orderpart->addTableName) {
                            $sql .= "`" . $tablename . "`.`" . $orderpart->fieldName . "`";
                        } else {
                            $sql .= "`" . $orderpart->fieldName . "`";
                        }
                    }

                }

                $sql .= " ";
                $sql .= $orderpart->direction;

                $fieldnames_used[$orderpart->fieldName] = true;

            }

        }


        if (!empty($count) && is_numeric($count) && $count > 0) {
            $sql .= " LIMIT $count ";
        }

        if (!empty($offset) && is_numeric($offset) && $offset != 0) {
            $sql .= " OFFSET $offset ";
        }


        //   echo $sql;

        //   echo "<\r\n\r\n\r\n\r\n";

        // echo $sql;

        //echo $sql.";";
        if ($paramType != "" && $param != "") {


            $stmt = $this->connection->prepare($sql);
            if ($this->connection->error != "") {

                throw new Exception('There was an error running the query [' . $this->connection->error . ']');
            }

            $stmt->bind_param($paramType, $param);

            bpfw_log_sql("makeSelectAll:" . $sql . print_r($param, true));
            $stmt->execute();


            $result = $stmt->get_result();

            if ($this->connection->error != "") {

                throw new Exception('There was an error running the query [' . $this->connection->error . ']');
            }

        } else {
            //  echo $sql;
            if (!$result = $this->connection->query($sql)) {
                throw new Exception('There was an error running the query "' . $sql . '" [' . $this->connection->error . ']');
            }

            //	$stmt->bind_param("", $param);

        }

        $data = array();

        bpfw_log_sql("makeSelectAll:" . $sql, "select");
        while ($row = $result->fetch_assoc()) {
            if ($key != NULL) {
                $data[$row[$key]] = $row;
            } else {
                $data[] = $row;
            }
        }

        foreach ($data as $key => $value) {
            $this->processDataFieldAfterSelect($value);
        }

        if (bpfw_is_debug_sql()) {


            bpfw_log_sql("makeSelectAll query took " . (microtime(true) - $microtime));
            if ((microtime(true) - $microtime) > 1) {
                bpfw_log_sql("makeSelectAll trace " . bpfw_debug_string_backtrace());
            }

        }

        return $data;

    }

    /**
     * @throws Exception
     */
    function makeSelectValue($tablename, $valueToSelect, $key = null, $where = ""/*,  $param = "", $paramType = ""*/): array
    {

        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }


        $param = "";
        $paramType = "";

        $sql =
            "SELECT $key,$valueToSelect FROM `$tablename` $where ORDER BY $key";

        //echo $sql.";";
        if ($paramType != "" && $param != "") {

            $stmt = $this->connection->prepare($sql);
            if ($this->connection->error != "")
                throw new Exception('There was an error running the query [' . $this->connection->error . ']');

            $stmt->bind_param($paramType, $param);
            bpfw_log_sql("makeSelectValue:" . $sql . print_r($param, true));
            $stmt->execute();

            $result = $stmt->get_result();

            if ($this->connection->error != "") {
                throw new Exception('There was an error running the query [' . $this->connection->error . ']');
            }

        } else {

            bpfw_log_sql("makeSelectValue:" . $sql);

            if (!$result = $this->connection->query($sql)) {
                throw new Exception('There was an error running the query [' . $this->connection->error . ']');
            }

            //	$stmt->bind_param("", $param);

        }

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[$row[$key]] = $row[$valueToSelect];
        }

        /*foreach($data as $key=>$value){
        $this->processDataFieldAfterSelect($value);
        }*/


        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeSelectValue query took " . (microtime(true) - $microtime));
        }

        return $data;
    }

    function unescape($str): string
    {
        //  $str;
        return stripslashes($str);
    }

    /**
     * gibt einen einzigen Wert zurück oder null. Benutzt mehrere DatabaseKey Objekte
     * @param string $tablename
     * @param string $search
     * @param int|string|array $searchValue
     * @param string $return
     * @return string|null
     */
    function findValueByKey(string $tablename, string $search, int|string|array $searchValue, string $return): ?string
    {

        if (is_array($searchValue)) {
            $searchValue = $searchValue["data"];
        }

        $select = $this->makeSelect("select $return as value from `$tablename` where $search = '$searchValue'");
        if (empty($select)) return NULL;
        return array_pop($select)["value"];
    }

    /**
     * Summary of makeInsertOrUpdate
     * @param array $data
     * @param string $tableName
     * @param array|DatabaseKey|null $keyOrKeys
     * @param bool $ignoreConversion
     * @return mixed
     * @throws Exception
     */
    function makeInsertOrUpdate(array $data, string $tableName, array|DatabaseKey $keyOrKeys = null, bool $ignoreConversion = false): mixed
    {

        // var_dump($keyOrKeys);

        $keys = array();

        if (!empty($keyOrKeys)) {


            if (!is_array($keyOrKeys)) {
                $keys[] = $keyOrKeys;
            } else {
                $keys = $keyOrKeys;
            }


            // TODO: function?
            foreach ($keys as $keyobj) {

                if ($keyobj instanceof DatabaseKey) {

                    if ($keyobj->GetValue() == null) {
                        if (isset($data[$keyobj->Name]->data))
                            $keyobj->SetValue($data[$keyobj->Name]->data);
                        else if (isset($data[$keyobj->Name])) {
                            $keyobj->SetValue($data[$keyobj->Name]);
                        } else {
                            throw new Exception("not a key provided");
                        }
                    }

                } else {
                    throw new Exception("Key is no Datebasekey: " . print_r($keyobj, true));
                }

            }

        }

        reset($keys);

        $existing = !empty($keys) &&
            $this->findValueByKeys($tableName, $keys, current($keys)->Name) !== NULL;

        if (!$existing) {
            return $this->makeInsert($data, $tableName, $ignoreConversion);
        } else {
            $this->makeUpdate($data, $tableName, $keys, $ignoreConversion); // $key, $keyvalue,$this->getSqlTypeFromFieldType($this->getFieldType($data[$key]), $keyvalue));
            if (count($keys) == 1) {
                return current($keys)->GetValue();
            } else {
                return $keys;
            }
        }

    }

    /**
     * gibt einen einzigen Wert zurück oder null
     * @param string $tableName
     * @param DatabaseKey|DatabaseKey[] $keyOrKeys
     * @param string $returnFieldName
     * @return string|null
     * @throws Exception
     */
    function findValueByKeys(string $tableName, array|DatabaseKey $keyOrKeys, string $returnFieldName): ?string
    {

        $keys = array();

        if (!is_array($keyOrKeys)) {
            $keys[] = $keyOrKeys;
        } else {
            $keys = $keyOrKeys;
        }

        $first = true;
        $search = "";
        foreach ($keys as $keyobj) {

            if (!$first) {
                $search .= " AND ";
            }

            if (is_object($keyobj->GetValue())) {
                $search .= $keyobj->Name . " = '" . $keyobj->GetValue()->data . "'";
            } else {
                $search .= $keyobj->Name . " = '" . $keyobj->GetValue() . "'";
            }


            $first = false;

        }

        $select = $this->makeSelect("select $returnFieldName as value from `$tableName` where $search");
        if (empty($select)) return NULL;
        return array_pop($select)["value"];

    }

    /**
     * Summary of makeInsert
     * @param array $data
     * @param string $tablename
     * @param bool $ignoreConversion
     * @return string
     * @throws Exception
     */
    function makeInsert(array $data, string $tablename, bool $ignoreConversion = false): string
    {


        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }

        /*echo "<pre>";
        echo "findme";
        var_dump($data);
        echo "</pre>";*/

        $error = $this->getLastError();
        if ($error != "ok") return $error;

        $queryKeys = "";
        $queryValues = "";
        $types = '';
        $count = 0;

        $first = true;

        foreach ($data as $datakey => $datavalue) {

            $value = $datavalue;
            if (is_array($datavalue)) {
                //$value = $datavalue->data;
                throw new Exception("error makeinsert database - array found");
            }

            $count++;
            //echo $datakey."->",$datavalues['data']."<br>";
            if ($this->checkIgnoreValueForSql($value, "insert")) {
                continue;
            }

            if (!$first) {
                $queryKeys = $queryKeys . ",";
                $queryValues = $queryValues . ",";
            }

            $first = false;


            if (!str_starts_with($datakey, "`")) {
                $queryKeys .= "`" . $datakey . "`";
            } else {
                $queryKeys .= $datakey;
            }

            $queryValues .= "?";

            $types .= $this->getSqlTypeFromFieldType($this->getFieldType($datavalue));

        }


        $query = "insert into `$tablename`(" . $queryKeys . ")VALUES(" . $queryValues . ");";

        $stmt = $this->connection->prepare($query);

        $a_params = array();

        $a_params[] = &$types;


        $count = 0;

        //foreach($data as $datakey => &$datavalue){
        foreach ($data as $datakey => $datavalue) // TODO: check
        {
            if ($this->checkIgnoreValueForSql($datavalue, "insert")) {
                continue;
            }
            if (!$ignoreConversion)
                $datavalue->data = $this->processDataFieldBeforeInsert($datavalue);
            if (is_array($datavalue->data)) $datavalue->data = json_encode($datavalue->data);
            $a_params[] = &$datavalue->data;

        }

        $error = $this->connection->error;
        if (!empty($error)) {

            bpfw_log_sql("makeInsert:" . $query . print_r($a_params, true));
            throw new Exception("mysql INSERT Error " . $error . "\r\nQuery:" . $query);
        }

        // echo $query;
        // var_dump($a_params);


        call_user_func_array(array($stmt, 'bind_param'), $a_params);
        bpfw_log_sql("makeInsert:" . $query . print_r($a_params, true));
        $stmt->execute();


        // $error = $this->connection->error;

        $newId = $this->connection->insert_id;


        /*  if($data["eventId"]->data == 3){
            var_dump($data);
            echo $this->connection->error;
            echo $query;
            var_dump($a_params);
        } */

        $stmt->close();


        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeInsert query took " . (microtime(true) - $microtime));
        }


        return $newId;

    }


    /*  function escape($str){
          return $this->connection->real_escape_string($str);
      }*/

    /**
     * Summary of checkIgnoreValueForSql
     * @param DbSubmitValue $value
     * @param mixed $sqltype
     * @return boolean
     */
    function checkIgnoreValueForSql(DbSubmitValue $value, mixed $sqltype): bool
    {
        // TODO: remove and test ...
        if ($sqltype == "insert") {

            if (!isset($value->data) /*|| $value->data === "NULL"*/) {
                //var_dump($value);
                return true;
            }

        }

        return false;
    }

    /**
     * Summary of getSqlTypeFromFieldType
     * @param BpfwDbFieldType|string $fieldType
     * @return string
     */
    function getSqlTypeFromFieldType(BpfwDbFieldType|string $fieldType): string
    {


        if (empty($fieldType)) return 's'; // bind_param with null + i is not working...

        if (is_string($fieldType)) {
            if (in_array($fieldType, BpfwDbFieldType::STRING_TYPES)) {
                return 's';
            } else {
                return 'i';
            }
        } else {

            // if(empty($fieldtype))return 's'; // bind_param with null + i is not working...

            if ($fieldType->isStringType()) {
                return 's';
            } else
                if ($fieldType->isBlobType()) {
                    return 'b';
                } else
                    if ($fieldType->isDoubleType()) {
                        return 'd';
                    } else {
                        return 'i';
                    }

        }

        /*
        if(!is_string($fieldtype)){

            $fieldtype = $fieldtype->type;
        }

		if($fieldtype == BpfwDbFieldType::"string" ||
            $fieldtype == "datetime" ||
            $fieldtype == "date" ||
            $fieldtype == "time" ||
            $fieldtype == "text" ||
            $fieldtype == "varchar" ||
            $fieldtype == "signature")
			return 's';
		else
			return 'i';*/

    }

    /**
     * Summary of getFieldType
     * @param DbSubmitValue $datavalue
     * @return BpfwDbFieldType
     * @throws Exception
     */
    public function getFieldType(DbSubmitValue $datavalue): BpfwDbFieldType
    {
        /* if(isset($datavalues['type'])){
             return $datavalues['type'];
         }*/
        /*if(isset($datavalues['field']['type'])){
            return $datavalues['field']['type'];
        }*/

        $field = $datavalue->getDbField();
        return $field->type;

        //  throw new \Exception ("field Type not set ".print_r($datavalues, true));

    }

    // TODO: sollte auch mit reinen keyvaluearrays funktionieren können

    /**
     * Summary of processDataFieldBeforeInsert
     * @param DbSubmitValue $dataValue
     * @return mixed
     * @throws Exception
     */
    function processDataFieldBeforeInsert(DbSubmitValue $dataValue): mixed
    {


        $value = '';


        if (isset($dataValue->data) || $dataValue->data === null) {
            $value = $dataValue->data;
        } else {
            throw new Exception("processDataFieldBeforeInsert expects complete datafield (array with data AND field)) got:" . print_r($dataValue, true));
        }


        /* if(isset($value['data'])){ // TODO: bug finden bei passwort ...
             $value = $value['data'];
         } */

        $field = $dataValue->getDbField();

        // TODO: das gehört in die Komponenten selbst / belongs inside the component code

        if (!empty($value) && isset($field->display)) {

            if ($field->display == "password") {

                /*  echo "<pre>";
                  var_dump($datavalues);
                  var_dump($value);
                  echo "</pre>";*/
                //echo $value;
                return md5($value);

            }

            if ($field->display == "datepicker") {

                if (is_numeric($value)) { // timestamp
                    return date("Y-m-d H:i", $value);
                }

                $dt = DateTime::createFromFormat("Y-m-d", $value);
                if (!empty($dt)) return $value;

                $dt = DateTime::createFromFormat("d.m.Y", $value);
                if (empty($dt)) return "";

                /*  if($dt === FALSE){
                      throw new \Exception($datavalues['name']."datetime has wrong format:".$value);
                  }*/

                return $dt->format("Y-m-d");

            }

            if ($field->display == "datetimepicker") {

                if (is_numeric($value)) { // timestamp
                    return date("Y-m-d H:i", $value);
                }

                $dt = DateTime::createFromFormat("Y-m-d H:i", $value);
                if (!empty($dt)) return $value;

                $dt = DateTime::createFromFormat("d.m.Y H:i", $value);
                if (empty($dt)) return "";
                /*if($dt === FALSE){
                    throw new \Exception($datavalues['name']."datetime has wrong format:".$value);
                }*/
                return $dt->format("Y-m-d H:i");
            }

            if ($field->display == "timestamp") {

                if (is_numeric($value)) { // timestamp
                    return date("Y-m-d H:i:s", $value);
                }

                $dt = DateTime::createFromFormat("Y-m-d H:i:s", $value);
                if (!empty($dt)) return $value;

                $dt = DateTime::createFromFormat("d.m.Y H:i:s", $value);
                if (empty($dt)) return "";
                /*if($dt === FALSE){
                throw new \Exception($datavalues['name']."datetime has wrong format:".$value);
                }*/
                return $dt->format("Y-m-d H:i:s");
            }

            if ($field->display == "timepicker") {

                $dt = DateTime::createFromFormat("H:i", $value);
                if (empty($dt)) return "";
                /*if($dt === FALSE){
                    throw new \Exception($datavalues['name']."datetime has wrong format:".$value);
                }*/
                return $dt->format("Y-m-d H:i");

            }

        }

        return $value;

    }

    /**
     * Summary of makeUpdate
     * @param array $data (array mit data und type)
     * @param string $tablename
     * @param DatabaseKey|DatabaseKey[] $keyOrKeys
     * @param bool $ignoreConversion
     * @return integer|string
     * @throws Exception
     */
    function makeUpdate(array $data, string $tablename, array|DatabaseKey $keyOrKeys, bool $ignoreConversion = false /* $key, $keyvalue, $keytype, $key2 = "", $keyvalue2 = "", $keytype2 = "" */): int|string
    {

        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }


        $keys = array();

        //  var_dump($keyOrKeys);

        if (!is_array($keyOrKeys)) {
            $keys[] = $keyOrKeys;
        } else {
            $keys = $keyOrKeys;
        }

        //    var_dump($data);

        $error = $this->getLastError();
        if ($error != "ok") return $error;

        $queryKeys = "";

        $types = '';
        $count = 0;

        foreach ($data as $datakey => $datavalue) {

            if (isset($datavalue->getDbField()->display) && $datavalue->getDbField()->display == "password" && empty($datavalue->data)) continue;

            // TODO: check
            /*
            foreach($keys as $keyobj){
                if($keyobj->Name == $datakey && $datavalue->data == '' ){
                    continue;
                }
            }*/

            if ($count != 0) {
                $queryKeys .= ",";
                //$queryValues.=",";
            }

            $queryKeys .= "`" . $datakey . "`=?";

            $types .= $this->getSqlTypeFromFieldType($this->getFieldType($datavalue));

            $count++;

        }

        foreach ($keys as $keyobj) {
            $types .= $keyobj->type;
        }

        $query = "UPDATE `$tablename` SET $queryKeys WHERE ";

        $first = true;

        foreach ($keys as $keyobj) {
            if (!$first) {
                $query .= " AND ";
            }

            $query .= $keyobj->Name . " = ?";

            $first = false;

        }

        $stmt = $this->connection->prepare($query);

        echo $this->connection->error;

        $a_params = array();

        $a_params[] = &$types;

        $count = 0;

        //foreach($data as $datakey => &$datavalue){ // TODO: check
        foreach ($data as $datakey => $datavalue) {

            $field = $datavalue->getDbField();

            if (isset($field->display) && $field->display == "password" && empty($datavalue->data)) continue;


            // TODO: check
            /*foreach($keys as $keyobj){
                if($keyobj->Name == $datakey && $datavalue->data == '' ){
                    continue;
                }
            }*/

            /* echo "<pre>";
             echo $data[$datakey]['data'];
             echo $datavalues["data"];
             echo "</pre>";
             */
            if (!$ignoreConversion)
                $datavalue->data = $this->processDataFieldBeforeInsert($datavalue);


            // var_dump($data[$datakey]);
            //echo $datakey;

            if (is_array($datavalue->data)) {
                $datavalue->data = json_encode($datavalue->data);
            }

            if ($field->display != "file" && $field->display != "image") // json, already parsed // has_blob_data
            {
                // $data[$datakey]->data = $this->connection->real_escape_string($data[$datakey]->data);
            }

            $a_params[] = &$datavalue->data;

        }


        $i = 0;
        $referencedValue = array();
        foreach ($keys as $keyobj) {

            if (is_object($keyobj->GetValue())) {
                $referencedValue[$i++] = $keyobj->GetValue()->data;
            } else {
                $referencedValue[$i++] = $keyobj->GetValue();
            }

        }


        $i = 0;
        foreach ($keys as $keyobj) {
            //  {$value_$i} = $keyobj->GetValue();
            $a_params[] = &$referencedValue[$i++];
        }

        // var_dump($keys);
        // var_dump($referencedValue);

        // var_dump($a_params);

        /*
		$a_params[] = & $keyvalue;
		if($key2 != "")
		{
		  $a_params[] = & $keyvalue2;
		}*/

        call_user_func_array(array($stmt, 'bind_param'), $a_params);
        bpfw_log_sql("makeUpdate:" . $query . print_r($a_params, true));
        $stmt->execute();

//        var_dump($this->connection);

        $error = $this->connection->error;
        echo $error;
        $affected = $this->connection->affected_rows;
        $stmt->close();

        if (!empty($error)) {
            throw new Exception("mysql UPDATE Error " . $error);
        }


        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeUpdate query took " . (microtime(true) - $microtime));
        }


        return $affected;

    }

    /**
     * Summary of makeDelete
     * @param string $tablename
     * @param DbSubmitValue $idvalue
     * @return integer affected rows
     * @throws Exception
     */
    function makeDelete(string $tablename, DbSubmitValue $idvalue): int
    {
        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }

        $sql = "delete from `$tablename` where " . $idvalue->key . " = ?";

        $stmt = $this->connection->prepare(
            $sql);


        if (is_int($idvalue->data)) {
            $stmt->bind_param('i',
                $idvalue->data
            );
        } else {
            $stmt->bind_param('s',
                $idvalue->data
            );
        }

        bpfw_log_sql("makeDelete:" . $sql . print_r($idvalue->data, true));
        $stmt->execute();

        $retval = $stmt->affected_rows;
        $error = $this->connection->error;

        if (!empty($error)) {
            throw new Exception("mysql DELETE Error " . $error);
        }

        $stmt->close();


        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeDelete query took " . (microtime(true) - $microtime));
        }


        return $retval;

    }

    /**
     * @throws Exception
     */
    function makeDeleteByWhere($tablename, $where)
    {


        $microtime = 0;
        if (bpfw_is_debug_sql()) {
            $microtime = microtime(true);
        }

        if (!empty($where)) {
            $where = " WHERE $where";
        }

        $sql = "delete from `$tablename` $where";
        $stmt = $this->connection->prepare(
            $sql);

        /*$stmt->bind_param('i',
        $idvalue
        );*/

        bpfw_log_sql("makeDeleteByWhere:" . $sql);
        $stmt->execute();

        $error = $this->connection->error;

        $affectedRows = $stmt->affected_rows;

        $stmt->close();

        if (!empty($error)) {
            throw new Exception("mysql DELETE Error " . $error);
        }


        if (bpfw_is_debug_sql()) {
            bpfw_log_sql("makeInsert query took " . (microtime(true) - $microtime));
        }

        return $affectedRows; // TODO: warum klappt affected rows hier nicht?


    }

    /**
     * @throws Exception
     */
    public function convertTableCharsetToUtf8($tableName): void
    {
        bpfw_log_sql("convertToUtf8:" . "ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8mb4");
        $this->makeQuery("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8mb4");
    }

    /**
     * @throws Exception
     */
    function makeQuery($sql): mysqli_result|bool
    {

        $result = $this->connection->query($sql);

        if (!$result) {
            throw new Exception("query failed: '$sql' " . $this->connection->error);
        }

        if (empty($this->connection->error)) {
            return $result;
        } else {
            throw new Exception("query failed: '$sql' " . $this->connection->error);
        }

    }


}

$database = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

/**
 * Summary of bpfw_getDb
 * @return Database
 * @throws Exception
 */
function bpfw_getDb(): Database
{

    global $database;

    if (empty($database)) {
        throw new Exception("no Database was found");
    }

    return $database;

}


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

/**
 * components short summary.
 *
 * components description.
 *
 * @version 1.0
 * @author torst
 */
abstract class EnumHandlerInterface
{

    function getValueByKey($key, $first = true): string
    {

        $retval = "";

        $entries = $this->getValueArray();

        if (is_array($key)) {

            // var_dump($key);

            foreach ($key as $k => $v) {

                if (!$first) {
                    $retval .= " | ";
                }

                $retval .= $this->getValueByKey($v, $first);

                $first = false;
            }
        } else {

            if (isset($entries[$key])) {
                $retval = $entries[$key];

            } else {
                if (empty($key) && isset($entries[""])) {
                    return $entries[""];
                }
            }


        }

        return $retval;

    }

    abstract function getValueArray($parameters = array());

}

class EnumHandlerCallback extends EnumHandlerInterface
{

    var array|string $functionName;
    var bool $caching = true;
    /**
     * Summary of $cache
     * @var array
     */
    private array $cache = array();
    private bool $cached = false;

    /**
     * Summary of __construct
     * @param array|string $functionName
     * @param boolean $caching
     * @throws Exception
     */
    function __construct(array|string $functionName, bool $caching = true)
    {


        $this->caching = $caching;
        $this->functionName = $functionName;


        if (!is_string($functionName) && count($this->functionName) != 2) {
            throw new Exception("'function not a function or an method(2 element array)" . print_r($functionName, true) . "'");
        }

        if (!is_array($functionName) && !function_exists($functionName)) {
            throw new Exception("function $functionName not existing in EnumHandlerCallback");
        }

        if (is_array($functionName) && !method_exists($functionName[0], $functionName[1])) {
            throw new Exception("function " . json_encode($functionName) . " not existing in EnumHandlerCallback");
        }

    }

    /**
     * @throws Exception
     */
    function getValueArray($parameters = array())
    {

        if ($this->caching) {

            if (!$this->cached) {
                $this->cache = $this->callFunction();
                $this->cached = true;
            }

            return $this->cache;

        }

        return $this->callFunction();

    }

    /**
     * @throws Exception
     */
    function callFunction()
    {

        if (!is_array($this->functionName) && function_exists($this->functionName)) {

            return call_user_func($this->functionName);

        }

        if (is_array($this->functionName) && method_exists($this->functionName[0], $this->functionName[1])) {

            return call_user_func($this->functionName);

        }

        throw new Exception("functioncall failed");

    }

}


class EnumHandlerArray extends EnumHandlerInterface
{

    var array $array;

    function __construct($array)
    {
        $this->array = $array;
    }

    function getValueArray($parameters = array()): array
    {
        return $this->array;
    }

}


class EnumHandlerDb extends EnumHandlerInterface
{

    var string $table;
    var string $key;
    var string $value;
    var string $where;
    var string $orderBy;
    var ?string $nullText;

    var bool $withemptyValue;
    var mixed $prettifyfunction;
    var array $cache = array();

    /**
     * Summary of __construct
     * @param string $table
     * @param string $key
     * @param string $value
     * @param string $where
     * @param boolean $withemptyValue
     * @param string|null $nullText
     * @param callable|null $prettifyfunction
     */
    function __construct(string $table, string $key, string $value, string $where = '', bool $withemptyValue = false, ?string $nullText = null, ?callable $prettifyfunction = null, $orderBy = "")
    {


        $this->table = $table;
        $this->key = $key;
        $this->value = $value;
        $this->where = $where;
        $this->nullText = $nullText;
        $this->orderBy = $orderBy;

        if (empty($nullText)) {
            $nullText = "nicht gesetzt";
        }

        $this->withemptyValue = $withemptyValue;

        $this->prettifyfunction = $prettifyfunction;

    }

    /**
     * @throws Exception
     */
    function getValueArray($parameters = array())
    {

        $database = bpfw_getDb();

        if (empty($this->cache) || empty($this->cache[$this->where])) {

            if ($this->withemptyValue) {
                $this->cache[$this->where] = $database->fetchKeyValueArrayWithEmpty($this->table, $this->key, $this->value, $this->where, $this->nullText, $this->prettifyfunction, $this->orderBy);

            } else {
                $this->cache[$this->where] = $database->fetchKeyValueArray($this->table, $this->key, $this->value, $this->where, $this->prettifyfunction, $this->orderBy);
            }

        }

        return $this->cache[$this->where];


    }

}
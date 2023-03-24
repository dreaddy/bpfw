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
 * BpfwModelFormfield short summary.
 *
 * BpfwModelFormfield description.
 *
 * @version 1.0
 * @author torst
 */
class BpfwDbFieldType
{

    const TYPE_INT = "int";
    const TYPE_TINYINT = "tinyint";
    const TYPE_BIGINT = "bigint";

    const TYPE_STRING = "varchar";
    const TYPE_VARCHAR = "varchar";

    const TYPE_TEXT = "text";

    const TYPE_BOOLEAN = "tinyint";
    const TYPE_DATE = "date";
    const TYPE_DATETIME = "datetime";
    const TYPE_TIMESTAMP = "timestamp";
    const TYPE_TIME = "time";
    const TYPE_FLOAT = "float";
    const TYPE_DECIMAL = "decimal";


    const TYPE_IGNORE = "ignore"; // has no value like a container
    const TYPE_NONE = "ignore"; // has no value like a container

    const TYPE_FOREIGN = "ignore"; // field of another table that has been joined

    const TYPE_LINK_TABLE = "link_table"; // nm Table

    const STRING_TYPES = array(BpfwDbFieldType::TYPE_STRING, BpfwDbFieldType::TYPE_VARCHAR, BpfwDbFieldType::TYPE_TEXT, BpfwDbFieldType::TYPE_DATE, BpfwDbFieldType::TYPE_TIME, BpfwDbFieldType::TYPE_DATETIME, BpfwDbFieldType::TYPE_TIMESTAMP); // saved as 's' by mysqli
    const DOUBLE_TYPES = array(BpfwDbFieldType::TYPE_FLOAT, BpfwDbFieldType::TYPE_DECIMAL); // saved as 'd' by mysqli
    const BLOB_TYPES = array(); // saved as 'b' by mysqli


    /**
     * Types that can be searched in lists
     */
    const SEARCHABLE_TYPES = array(BpfwDbFieldType::TYPE_STRING, BpfwDbFieldType::TYPE_VARCHAR, BpfwDbFieldType::TYPE_TEXT); // saved as 's' by mysqli

    var array $defaultValues = array(
        BpfwDbFieldType::TYPE_VARCHAR => array("length" => 255)
    );
    var bool $isKey = false;
    var string $type;
    var mixed $length;

    function __construct($type, $length = "default", $isKey = false)
    {
        $this->type = $type;
        $this->length = $length;
        $this->isKey = $isKey;

        if ($length == "default") {

            if (isset($this->defaultValues[$type]["length"])) {
                $this->length = $this->defaultValues[$type]["length"];
            }


        }
    }

    /** @noinspection PhpUnused */

    function isSearchableType(): bool
    {
        return in_array($this->type, BpfwDbFieldType::SEARCHABLE_TYPES);
    }

    function isIntType(): bool
    {
        return !($this->isStringType() || $this->isBlobType() || $this->isDoubleType());
    }

    function isStringType(): bool
    {
        return in_array($this->type, BpfwDbFieldType::STRING_TYPES);
    }

    function isBlobType(): bool
    {
        return in_array($this->type, BpfwDbFieldType::BLOB_TYPES);
    }

    function isDoubleType(): bool
    {
        return in_array($this->type, BpfwDbFieldType::DOUBLE_TYPES);
    }

}

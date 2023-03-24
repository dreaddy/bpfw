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



require_once(bpfw_getIncludeFileForComponent("text"));


/**
 * checkboxComponent short summary.
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */
class TimestampComponent extends TextComponent
{


    /**
     * @throws Exception
     */
    public function getMysqlValue($value)
    {
        if (empty($value)) return "NULL";
        $value = bpfw_DateStringToTimestamp($value);
        return bpfw_TimestampTodatestring($value, "Y-m-d H:i:s");
    }

    public function preProcessBeforeSql(mixed $value): mixed
    {

        if (empty($value)) return "NULL";

        return $value;

    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     * @throws Exception
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        //      echo $value;

        $dateformat = bpfw_get_defaultDateFormat() . " H:i:s";

        if (empty($value) ||
            $value == "0000-00-00" ||
            $value == "0000-00-00 00:00:00" ||
            $value == "1970-01-01" ||
            $value == "1970-01-01 00:00:00" ||
            $value == "00:00:00" ||
            str_contains($value, "01.01.1970")) {
            $value = "";
        } else {
            $value = bpfw_mysqlDateStringToFormat($value, $dateformat);
        }

        return parent::displayAsEdit($value, $fieldName, $fieldDbModel, $model, $rowKey);
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

        //$dateformat = "d.m.Y H:i";
        $dateformat = bpfw_get_defaultDateFormat() . " H:i:s";
        if ($fieldDbModel->display == "datepicker") {
            $dateformat = bpfw_get_defaultDateFormat();
        } else if ($fieldDbModel->display == "timepicker") {
            $dateformat = "H:i:s";
        }

        $unformattedValue = $value;

        if (empty($unformattedValue) || $unformattedValue == "0000-00-00" || $unformattedValue == "0000-00-00 00:00:00" || $unformattedValue == "1970-01-01" || $unformattedValue == "1970-01-01 00:00:00") return "";
        else {

            return bpfw_mysqlDateStringToFormat($unformattedValue, $dateformat);

            //$phpdate = bpfw_datestringToTimestamp( $unformattedValue );
            //$formattedValue = date( $dateformat, $phpdate );
            //return $formattedValue;

        }


    }


}
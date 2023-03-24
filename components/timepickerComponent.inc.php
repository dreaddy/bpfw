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



require_once(bpfw_getIncludeFileForComponent("datepicker"));

/**
 * checkboxComponent short summary.
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */
class TimepickerComponent extends DatepickerComponent
{

    public function getMysqlValue($value)
    {

        if (empty($value)) return "00:00:00";
        $value = bpfw_DateStringToTimestamp($value);
        return bpfw_TimestampTodatestring($value, "H:i:s");
    }

    function getFooterJs(): string
    {

        ob_start();
        ?>


        jQuery(document).ready(function () {

        jQuery.datetimepicker.setLocale('de');

        jQuery('.timepicker').datetimepicker({
        datepicker: false,
        format: 'H:i',
        step: 15,
        dayOfWeekStart: 1,
        inline: false,
        validateOnBlur: true,
        defaultDate: new Date()
        });

        });

        <?php

        return ob_get_clean();

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

        $dateformat = "H:i";

        $unformattedValue = $value;

        if (empty($value) || $value == "1970-01-01" || $value == "1970-01-01 00:00:00" || $value == "0000-00-00" || $value == "0000-00-00 00:00:00" || $value == "00:00:00") return "";
        else {
            $sortvalue = "<div style='display:none'>" . bpfw_DateStringToTimestamp($unformattedValue) . "</div>";
            $phpdate = strtotime($unformattedValue);
            return date($dateformat, $phpdate);

        }


    }

}
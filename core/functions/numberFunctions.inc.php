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
 * converts a float to eur. 12,21/12.21 becomes 12 Euro 21 Cents
 * @param $input ?float
 * @return string
 */
function bpfw_float_to_eur(?float $input, $decimal_separator = ",", $thousands_separator = "."): string
{

    if(empty($input))return "0{$decimal_separator}00";

    $floored = round($input, 2, PHP_ROUND_HALF_DOWN);
    return number_format($floored, 2, $decimal_separator, $thousands_separator);

}

/**
 * 122,222.11 to 122.222,11
 * @param string $value
 * @return string
 */
function bpfw_toUsNumberFormat(string $value): string
{

    $value = str_replace(",", "%", $value);

    $value = str_replace(".", ",", $value);
    return str_replace("%", ".", $value);

}

/**
 * converts 122,222.11 to 122.222,11
 * @param string $value
 * @return string
 */
function bpfw_fromUsNumberFormat(string $value): string
{

    $value = str_replace(".", "%", $value);

    $value = str_replace(",", ".", $value);
    return str_replace("%", ",", $value);
}


function bpfw_format_for_print($value)
{
    if($value == null){
        return "";
    }

    if (!is_array($value)) {
        return $value;
    } else {
        $first = true;
        $retval = "";
        foreach ($value as $arrvalue) {

            if (!$first) {
                $retval .= "|";
            }

            $retval .= $arrvalue;

            $first = false;
        }
    }

    return $retval;

}
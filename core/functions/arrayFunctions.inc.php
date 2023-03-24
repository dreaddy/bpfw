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
 *  inserts new Values into array, overwrites old... so its basically array_merge
 *
 * @throws Exception
 */
function bpfw_overwrite_array($oldValues, $newValues): array
{

    if (!is_array($newValues) || !is_array($oldValues)) {
        throw new Exception("bpfw_overwrite_array both values must be an array");
    }

    foreach ($newValues as $k => $v) {
        $oldValues[$k] = $v;
    }

    return $oldValues;

}

/**
 *  replaces all occurences of needle with replace in array
 * @param $needle string
 * @param $replace string
 * @param $subject array
 * @param $doEmptyCheck bool
 * @return array
 */
function bpfw_replaceArrValueWithArrValueIfExists($needle, $replace, $subject, bool $doEmptyCheck = true, bool $translateNewValue = false)
{

    if (isset($subject[$replace]) && (!$doEmptyCheck || !empty($subject[$replace]))) {
        $subject[$needle] = $translateNewValue?__($subject[$replace]):$subject[$replace];
    }

    return $subject;

}


function bpfw_arrayMakeValuesAsKeys($array): array
{

    $retval = array();

    foreach ($array as $key) {
        $retval[$key] = $key;
    }

    return $retval;

}



/**
 * true if array values are identical
 * @param mixed $array1
 * @param mixed $array2
 * @return bool
 */
function bpfw_compare_arrayvalues(mixed $array1, mixed $array2): bool
{

    if (is_array($array1) && is_array($array2)) {

        return
            (empty(array_diff($array1, $array2)) &&
                empty(array_diff($array2, $array1)));

    } else {

        return $array1 == $array2;

    }


}
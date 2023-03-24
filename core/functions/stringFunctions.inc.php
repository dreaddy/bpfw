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
 * cut a string to a specific length if it is too long
 * @param $string string string to cut
 * @param $maxLength int max length
 * @return string
 */
function bpfw_cutStringToLength(string $string, int $maxLength) : string
{

    if (strlen($string) <= $maxLength) return $string;
    else {
        return substr($string, 0, $maxLength - 3) . "...";
    }

}



function bpfw_strStartsWith($haystack, $needle): bool
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function bpfw_strEndsWith($haystack, $needle): bool
{
    $length = strlen($needle);

    return $length === 0 ||
        (substr($haystack, -$length) === $needle);
}

/**
 * sanitize string - can be used to make any string to a filename
 * @param $string string
 * @param $force_lowercase bool all chars lowercase
 * @param $onlyAlphabetAndNumbersAllowed bool only allow chars and numbers
 * @param $replaceSpaces bool no spaces
 * @return array|string|null
 */
function bpfw_sanitize(string $string, bool $force_lowercase = false, bool $onlyAlphabetAndNumbersAllowed = false, bool $replaceSpaces = false): array|string|null
{

    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
        "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
        "â€”", "â€“", ",", "<", ".", ">", "/", "?");

    $clean = trim(str_replace($strip, "", strip_tags($string)));

    if ($replaceSpaces) {
        $clean = preg_replace('/\s+/', "-", $clean);
    }

    $clean = ($onlyAlphabetAndNumbersAllowed) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;

    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;

}



/**
 * bool to string bool with "true" and "false"
 * @param boolean $boolVal
 * @return string
 */
function bpfw_boolToString(bool $boolVal): string
{
    if ($boolVal) return "true";
    return "false";

}

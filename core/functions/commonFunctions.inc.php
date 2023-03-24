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
 * @return bool is on windows?
 */
function bpfw_is_windows(): bool
{
    return DIRECTORY_SEPARATOR != "/";
}

/**
 * @param $name string key to search in get or post
 * @return mixed|null value or null
 */
function getorpost($name)
{

    if (isset($_POST[$name])) return $_POST[$name];
    if (isset($_GET[$name])) return $_GET[$name];

    return null;

}

/**
 * @param $string mixed json string (or not)
 * @return bool string is a json string
 */
function isJson(mixed $string): bool
{
    return is_string($string) &&
        (is_object(json_decode($string)) ||
            is_array(json_decode($string)));
}



/** replace \ with / and viceversa. remove double slashes/backslashes */
function bpfw_fix_dir($dirname)
{
    if ("/" != DIRECTORY_SEPARATOR) {
        $dirname = str_replace('/', DIRECTORY_SEPARATOR, $dirname);
        $dirname = str_replace('\\\\', DIRECTORY_SEPARATOR, $dirname);
    }
    if ("\\" != DIRECTORY_SEPARATOR) {
        $dirname = str_replace('\\', DIRECTORY_SEPARATOR, $dirname);
        $dirname = str_replace('//', DIRECTORY_SEPARATOR, $dirname);
    }
    return $dirname;
}

function bpfw_htmlentities($val): string
{

    if ($val === null || $val === "") return "";

    return htmlentities($val);
}

function bpfw_json_encode($val): string
{

    if ($val === null) return "";

    return bpfw_json_encode($val);
}

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle): bool
    {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle): bool
    {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function bpfw_clean_url($url): array|string
{

    $url = str_replace("\\", "/", $url);
    return str_replace("//", "/", $url);

}

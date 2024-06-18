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
 * Convert unix timestamp to date in given format. If no format is set, global format is used. If timestamp is "NOW()", it will return "NOW()"
 *
 * @param mixed $timestamp
 * @param string $format
 * @return string
 */
function bpfw_TimestampTodatestring(mixed $timestamp, string $format = "default"): string
{

    if ($timestamp == "NOW()") {
        return $timestamp;
    }

    if ($format == "default") {
        $format = bpfw_get_defaultDateFormat();
    }

    return date($format, $timestamp);
}

/**
 * Convert unix timestamp to date in given format. If no format is set, global format is used. If timestamp is "NOW()", it will return "NOW()"
 *
 * @param $timestamp2 mixed unix timestamp or NOW() or empty
 * @param $format string format to convert the datetime (identical to php date)
 * @param $allowEmptyTime boolean if timestamp is empty return "". throw exception otherwise
 * @return string formatted date
 * @throws Exception
 */
function bpfw_TimestampToDateTimeString(mixed $timestamp2, string $format = "d.m.Y H:i:s", bool $allowEmptyTime = false): string
{

    if (!$allowEmptyTime && empty($timestamp2)) {
        return "";
    }

    if (!is_numeric($timestamp2) && $timestamp2 != "NOW()") {
        throw new Exception("invalid timestamp (not numeric) in bpfw_TimestampToDateTimeString: '$timestamp2'");
    }

    $timestamp = $timestamp2;

    if ($timestamp2 == "NOW()") {
        $timestamp = time();
    }

    if (is_string($timestamp2)) {
        $timestamp = intval(trim($timestamp));
    }

    if ($timestamp2 == 0) {
        $timestamp = 0;
    }

    return date($format, $timestamp);

}

$_defaultDateFormat = "d.m.Y";

/**
 * set the default date format used in the system
 * @param string $format format as used in php date function
 * @return void
 */
function bpfw_set_defaultdateformat(string $format = "d.m.Y"): void
{

    global $_defaultDateFormat;

    if (empty($format)) {
        $_defaultDateFormat = "d.m.Y";
    } else {
        $_defaultDateFormat = $format;
    }

}

/**
 * @return string default format for BPFW - as used in php date function
 */
function bpfw_get_defaultDateFormat(): string
{

    global $_defaultDateFormat;

    return $_defaultDateFormat;

}


function bpfw_SecondsToDaysLeft($timestamp): float|int
{

    if ($timestamp < 0) return 0;

    $hoursLeft = (int)($timestamp / 60 / 60);
    $daysLeft = $hoursLeft / 24;

    return ceil($daysLeft);
}

/**
 * gets a datestring and returns a DateTime object
 * @param string|DbSubmitValue|null $dateString
 * @return DateTime
 * @throws Exception
 */
function bpfw_DateStringToDateTime(string|DbSubmitValue|null $dateString): DateTime
{

    if ($dateString == "-") $dateString = "";

    if ($dateString == "NOW()") return new DateTime(); // date(""));

    if ($dateString instanceof DbSubmitValue) {
        $dateString = $dateString->data;
    }

    $dateString = trim($dateString);
    $time = "00:00:00";

    if (empty($dateString)) {
        $date = "01.01.1970";
    } else {
        $date = $dateString;
    }

    // time only
    if (strlen($dateString) <= 10 && str_contains($dateString, ":")) {
        $date = "01.01.1970";
        $time = $dateString;
    }

    if (strlen($dateString) > 10) {
        $timeStart = strpos($dateString, " ");
        $date = substr($dateString, 0, $timeStart);

        $time = substr($dateString, $timeStart + 1);
    }

    if (str_contains($dateString, "/")) {
        // dd/mm/yy
        $date = implode('-', explode('/', $date));
    }

    if (str_contains($date, ".")) {
        $date = implode('-', array_reverse(explode('.', $date)));
    }
    return new DateTime($date . " " . $time);

}

/**
 * Summary of bpfw_datestringToTimestamp
 * @param string|DbSubmitValue|null $dateString
 * @return integer
 * @throws Exception
 */
function bpfw_DateStringToTimestamp(string|DbSubmitValue|null $dateString): int
{
    return bpfw_DateStringToDateTime($dateString)->getTimestamp();
}

/**
 * @param string|DbSubmitValue $dateString mysql date (format YYYY-mm-dd or YYYY-mm-dd hh:ii:ss)
 * @param string $newFormat format as in php date()
 * @param bool $replacePlaceholders replace NOW() or keep it?
 * @return string
 * @throws Exception
 */
function bpfw_mysqlDateStringToFormat(string|DbSubmitValue|null $dateString, string $newFormat = "d.m.Y", bool $replacePlaceholders = true): string
{

    if ($dateString == null) {
        return "";
    }

    if ($dateString == "0000-00-00") {
        return "";
    }

    if ($dateString instanceof DbSubmitValue) {
        $dateString = $dateString->data;
    }

    if (!$replacePlaceholders && $dateString == "NOW()") return $dateString;

    if ($replacePlaceholders && $dateString == "NOW()") {
        $dateString = "";
        $dt = new DateTime();
        return $dt->format($newFormat);
    }


    $dateString = trim($dateString);

    if ($dateString == "-") $dateString = "";


    $time = "00:00:00";
    $date = $dateString;


    if (empty($dateString)) {

        $date = "";

    }


    // time only
    if (strlen($dateString) <= 8 && str_contains($dateString, ":")) {
        $date = "01.01.1970";
        $time = $dateString;
    }

    if (strlen($dateString) > 8 && str_contains($dateString, ":")) {
        $timestart = strpos($dateString, " ");
        $date = substr($dateString, 0, $timestart);

        $time = substr($dateString, $timestart + 1);
    }

    if (str_contains($date, ".")) {
        $date = implode('-', array_reverse(explode('.', $date)));
    }

    // echo $date." ".$time;


    $retval = new DateTime(trim($date . " " . $time));

    return $retval->format($newFormat);

}

/**
 * Get current date as mysql format (Y-m-d H:i:s)
 * @return string current date as mysql format
 */
function bpfw_current_mysql_datetimestring(): string
{
    return date('Y-m-d H:i:s', time());
}

/**
 * d.m.y/d.m.Y to Y-m-d
 * @param ?string $dateString
 * @return string
 */
function bpfw_DateStringToMysqlDateString(?string $dateString): string
{

    if (empty($dateString)) return "1970-01-01";

    $datestring_parts = explode(".", $dateString);
    if (count($datestring_parts) != 3) return "invalid";

    if (strlen($datestring_parts[2]) < 4) $datestring_parts[2] = "20" . $datestring_parts[2];

    return $datestring_parts[2] . "-" . $datestring_parts[1] . "-" . $datestring_parts[0];

}

/**
 * @param mixed $timestampOrString timestamp or time string
 * @return bool is weekend?
 * @throws Exception
 */
function bpfw_isWeekend(mixed $timestampOrString): bool
{
    $timestamp = $timestampOrString;

    if (!is_numeric($timestampOrString)) {
        $timestamp = bpfw_DateStringToTimestamp($timestampOrString);
    }

    $weekDay = date('w', $timestamp);
    return ($weekDay == 0 || $weekDay == 6);
}


/**
 * check if date is empty. also checks for empty strings like 0000-00-00
 * @param $date mixed
 * @return bool
 */
function bpfw_emptydate(mixed $date): bool
{

    if (empty($date)) return true;

    if ($date == "0000-00-00") return true;
    if ($date == "1970-01-01") return true;

    if ($date == "0000-00-00 00:00:00") return true;
    if ($date == "1970-01-01 00:00:00") return true;


    return false;

}
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
 * @return string return a string with the current stacktrace inside
 */
function bpfw_debug_string_backtrace(): string
{

    ob_start();
    debug_print_backtrace();
    $trace = ob_get_contents();
    ob_end_clean();

    // Remove first item from backtrace as it's this function which
    // is redundant.
    // $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

    // Renumber backtrace items.
    // $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);

    return "<pre>" . $trace . "</pre>";

}

/**
 * Log data in datalog (can be viewed as an admin in datalog "?p=datalog")
 * @param string $modelName
 * @param mixed $key
 * @param string $logType
 * @param string $metadata
 * @throws Exception
 */
function bpfw_logDataEntry(string $modelName, mixed $key, string $logType, string $metadata = "{{{readEntries}}}"): void
{

    //  return bpfw_createModelByName("logdataactions")->removeLogEntryByKey($this->GetTableName(), $key);

    $datalog = bpfw_createModelByName("datalog");
    $datalog->addLogEntry($modelName, $key, $logType, $metadata);

}

/**
 * @return bool debug sql active?
 */
function bpfw_is_debug_sql(): bool
{
    return defined("DEBUG_SQL") && DEBUG_SQL == true;
}

$loghash = null;

function bpfw_log_sql($message, $type = "query"): void
{


    global $loghash;
    if (bpfw_is_debug_sql()) {

        $logfile = LOGS_PATH . "sql.log";
        $logfile = bpfw_fix_dir($logfile);

        if (!file_exists(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0777, true);
        }

        if (empty($loghash)) $loghash = md5(rand());
        error_log("[" . $loghash . "]" . $type . ":" . $message . "\r\n", 3, $logfile);

    }


}


/**
 * check if server is running locally, so we can disable or change some functionality
 * @return bool Server is local
 */
function bpfw_isLocalTestserver(): bool
{

    return strstr(BASE_URI, "localhost") || strstr(BASE_URI, "127.0.0.1");

}
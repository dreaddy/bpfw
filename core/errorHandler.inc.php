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



const ERRORTYPE_SUCCESS = "success";
const ERRORTYPE_INFO = "info";
const ERRORTYPE_WARNING = "warning";
const ERRORTYPE_ERROR = "error";

function bpfw_get_default_errorcolor($type): string
{

    return match ($type) {
        ERRORTYPE_SUCCESS => "green",
        ERRORTYPE_INFO => "black",
        ERRORTYPE_WARNING => "orange",
        default => "red",
    };

}

class BpfwError
{

    var string $msg;
    var mixed $detail;
    var mixed $type;

    function __construct($msg, mixed $detail, $type = ERRORTYPE_ERROR)
    {
        $this->msg = bpfw_htmlentities($msg); // , ENT_COMPAT | ENT_HTML5, null, false);
        $this->detail = $detail;
        $this->type = $type;
    }

}

class BpfwErrorHandler
{

    var array $errors = array();

    /**
     * Summary of bpfw_error_add
     * @param BpfwError $error
     */
    function bpfw_error_add(BpfwError $error): void
    {
        $this->errors[] = $error;
    }

    function bpfw_error_get(): array
    {
        return $this->errors;
    }


}

$bpfw_errors = new BpfwErrorHandler();

/**
 * add Error to general error output
 * @param string $msg
 * @param mixed $detail
 * @param string $type ERRORTYPE_ERROR|ERRORTYPE_WARNING|ERRORTYPE_INFO|ERRORTYPE_SUCCESS
 * @return BpfwErrorHandler
 */
function bpfw_error_add(string $msg, mixed $detail = null, string $type = ERRORTYPE_ERROR): BpfwErrorHandler
{
    global $bpfw_errors;
    $bpfw_errors->bpfw_error_add(new BpfwError($msg, $detail, $type));
    return $bpfw_errors;
}

/**
 * add error that is displayed below the selected component
 * @param string $error
 * @param mixed|null $val -> current value of this field. Only required for some component checks
 * @param BpfwModelFormfield $component
 * @param string $type ERRORTYPE_ERROR | ERRORTYPE_WARNING | ERRORTYPE_INFO | ERRORTYPE_SUCCESS
 * @return BpfwErrorHandler
 */
function bpfw_error_add_to_component(string $error, BpfwModelFormfield $component, string $type = ERRORTYPE_ERROR, mixed $val = null): BpfwErrorHandler
{
    //global $bpfw_errors;
    //return $bpfw_errors->bpfw_error_add(new BpfwError($msg, $detail, $type));
    return bpfw_error_add($error, array("type" => "formvalidation", "key" => $component->name, "value" => $val, "page" => $component->editpage));

}


/**
 * Summary of bpfw_error_get
 * @return BpfwError[]
 */
function bpfw_error_get(): array
{
    global $bpfw_errors;
    return $bpfw_errors->bpfw_error_get();
}

function bpfw_error_hasErrors(): bool
{
    global $bpfw_errors;
    return !empty($bpfw_errors->bpfw_error_get());
}
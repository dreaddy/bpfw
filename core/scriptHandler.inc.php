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



class ScriptHandler
{

    var array $bpfw_css_enqeued = array();
    var array $bpfw_js_header_enqeued = array();
    var array $bpfw_js_footer_enqeued = array();

    function bpfw_register_js($id, $src, $dependencies = array(), $version = false, $in_footer = false, $inline = false): void
    {

        $id = strtolower($id);

        if ($in_footer) {

            $this->bpfw_js_footer_enqeued[$id] = array("id" => $id, "dependencies" => $dependencies, "version" => $version, "in_footer" => $in_footer, "inline" => $inline);

            if ($inline) {
                $this->bpfw_js_footer_enqeued[$id]["src"] = $src;
            } else {
                $this->bpfw_js_footer_enqeued[$id]["includepath"] = $src;
            }

        } else {
            $this->bpfw_js_header_enqeued[$id] = array("id" => $id, "dependencies" => $dependencies, "version" => $version, "in_footer" => $in_footer, "inline" => $inline);

            if ($inline) {
                $this->bpfw_js_header_enqeued[$id]["src"] = $src;
            } else {
                $this->bpfw_js_header_enqeued[$id]["includepath"] = $src;
            }
        }

    }

    function bpfw_register_css($id, $src, $dependencies = array(), $version = false, $media = "all", $inline = false): void
    {

        $id = strtolower($id);

        $this->bpfw_css_enqeued[$id] = array("id" => $id, "dependencies" => $dependencies, "version" => $version, "media" => $media, "inline" => $inline);
        if ($inline) {
            $this->bpfw_css_enqeued[$id]["src"] = $src;
        } else {
            $this->bpfw_css_enqeued[$id]["includepath"] = $src;
        }
        // TODO: $dependencies, $version
    }

    function bpfw_get_header_js(): string
    {

        $revtal = "";

        foreach ($this->bpfw_js_header_enqeued as $id => $value) {

            if (!$value["inline"]) {
                $revtal .= "<script data-id='$id' src=\"" . $value["includepath"] . "\"></script>\r\n";
            }

        }

        // inline css last
        foreach ($this->bpfw_js_header_enqeued as $id => $value) {

            if ($value["inline"]) {
                if (!bpfw_strStartsWith(trim($value["src"]), "<script>")) {


                    $revtal = $revtal . "<script data-id='$id' >" . $value["src"] . "</script>";
                } else {
                    $revtal .= $value["src"] . "\r\n";
                }
            }
        }

        return $revtal;

    }

    function bpfw_get_footer_js(): string
    {
        $revtal = "";

        foreach ($this->bpfw_js_footer_enqeued as $id => $value) {
            if (!$value["inline"]) {
                $revtal .= "<script  data-id='$id' src=\"" . $value["includepath"] . "\"></script>\r\n";
            }
        }

        // inlinecss last
        foreach ($this->bpfw_js_footer_enqeued as $id => $value) {
            if ($value["inline"]) {
                if (!bpfw_strStartsWith(trim($value["src"]), "<script>")) {
                    $revtal .= "<script data-id='$id' >" . $value["src"] . "</script>\r\n";
                } else {
                    $revtal .= $value["src"] . "\r\n";
                }
            }
        }

        return $revtal;
    }

    function bpfw_get_css(): string
    {
        $revtal = "";

        foreach ($this->bpfw_css_enqeued as $id => $value) {
            if (!$value["inline"]) {
                $revtal .= '<link  data-id="' . $id . '" rel="stylesheet" type="text/css" href="' . $value["includepath"] . "\">\r\n";
            } else {
                if (!bpfw_strStartsWith(trim($value["src"]), "<style>")) {
                    $revtal .= "<style data-id='$id' >" . $value["src"] . "</style>\r\n";
                } else {
                    $revtal .= $value["src"] . "\r\n";
                }
            }
        }

        return $revtal;
    }

}

$bpfw_scriptHandler = new ScriptHandler();

function bpfw_register_js($id, $includepath, $in_footer = false, $dependencies = array(), $version = false): void
{

    global $bpfw_scriptHandler;
    $bpfw_scriptHandler->bpfw_register_js($id, $includepath, $dependencies, $version, $in_footer);
}

function bpfw_register_js_inline($id, $src, $in_footer = false, $dependencies = array(), $version = false): void
{

    global $bpfw_scriptHandler;
    $bpfw_scriptHandler->bpfw_register_js($id, $src, $dependencies, $version, $in_footer, true);
}

function bpfw_register_css($id, $includepath, $dependencies = array(), $version = false, $media = "all"): void
{
    global $bpfw_scriptHandler;
    $bpfw_scriptHandler->bpfw_register_css($id, $includepath, $dependencies, $version, $media);
}

function bpfw_register_css_inline($id, $src, $dependencies = array(), $version = false, $media = "all"): void
{
    global $bpfw_scriptHandler;
    $bpfw_scriptHandler->bpfw_register_css($id, $src, $dependencies, $version, $media, true);
}

function bpfw_get_css(): string
{
    global $bpfw_scriptHandler;
    return $bpfw_scriptHandler->bpfw_get_css();
}

function bpfw_get_footer_js(): string
{
    global $bpfw_scriptHandler;
    return $bpfw_scriptHandler->bpfw_get_footer_js();
}

function bpfw_get_header_js(): string
{
    global $bpfw_scriptHandler;
    return $bpfw_scriptHandler->bpfw_get_header_js();
}

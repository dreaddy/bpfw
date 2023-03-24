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

/**
 * checkboxComponent short summary.
 *
 * checkboxComponent description.
 *
 * @version 1.0
 * @author torst
 */

require_once(bpfw_getIncludeFileForComponent("text"));
require_once(bpfw_getIncludeFileForComponent("datepicker"));

class DatepickeryearmonthComponent extends TextComponent
{

    function __construct($name, $componentHandler)
    {

        bpfw_register_js("monthpicker_js", LIBS_URI . "simple_monthpicker/monthpicker.min.js");
        bpfw_register_css("monthpicker_css", LIBS_URI . "simple_monthpicker/monthpicker.css", array("jquery"));

        parent::__construct($name, $componentHandler);
    }


    function getFooterJs(): string
    {
        ob_start();
        return ob_get_clean();
    }


    function getRedrawJs(): string
    {

        ob_start();
        ?>

        jQuery('.datetimepicker_js_ym').Monthpicker(
        );


        <?php

        return ob_get_clean();

    }

    function getCss(): string
    {

        ob_start();

        ?>

        .monthpicker_input{
        min-height:35px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 17.3333px;
        font-family: 'Montserrat', Tahoma, Arial, sans-serif;
        --font-family-sans-serif: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        --font-family-monospace: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;

        margin-top: 10px;
        padding: 4px;
        margin-left:5px;
        margin-bottom:5px;

        }

        .monthpicker_input .placeholder{
        font-size: 17.3333px;
        font-family: 'Montserrat', Tahoma, Arial, sans-serif;
        color:grey;
        }

        <?php

        return ob_get_clean();
    }

    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {
        $this->addCssClass($fieldDbModel, "datetimepicker_js_ym");
        return parent::displayAsEdit($value, $fieldName, $fieldDbModel, $model, $rowKey);
    }

}

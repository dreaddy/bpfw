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



require_once(bpfw_getIncludeFileForComponent("combobox"));

/**
 * html checkbox component
 *
 * @version 1.0
 * @author torst
 */
class ImagecomboboxComponent extends ComboboxComponent
{


    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        // return print_r($value, true);

        $values = $this->displayAsFormattedPlainValue($value, $fieldName, $fieldDbModel, $rowKey, $model);
        $imghtml = "-";

        if (!empty($values) && isJson($values)) {

            $values = json_decode($this->displayAsFormattedPlainValue($value, $fieldName, $fieldDbModel, $rowKey, $model));

            $name = $values->name;
            $type = $values->type;
            $new_name = $values->new_name;
            $size = $values->size;
            $error = $values->error;

            $imghtml = "<img style='padding-left:5px;max-width:200px;max-height:200px;' class='listimage previewImage' src = '" . UPLOADS_URI . "$new_name'>";

        }

        return "<div class='imagecombobox_listview_label' data-value='$value' data-rowkey='$rowKey' >" .
            $imghtml .
            "</div>";

    }


}

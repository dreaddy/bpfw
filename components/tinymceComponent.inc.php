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
class TinymceComponent extends DefaultComponent
{

    function __construct($name, $componentHandler)
    {

        parent::__construct($name, $componentHandler);

        $this->showOnList = true;

        $jspath = VENDOR_URI . "tinymce/tinymce/tinymce.min.js";
        bpfw_register_js("Tinymce", $jspath);

        $jspath = VENDOR_URI . "tinymce/tinymce/jquery.tinymce.min.js";
        bpfw_register_js("Tinymce_jquery", $jspath, false, array("Tinymce"));

    }

    function getCss(): string
    {

        ob_start();


        /*
        div.tox-tinymce{
            max-width: 605px !important;
            min-width:100px !important;
        }*/

        return ob_get_clean();

    }

    /**
     * @throws Exception
     */
    function getRedrawJs(): string
    {

        ob_start();

        ?>

        tinymce.remove();

        <?php

        echo $this->getHeaderJs();

        return ob_get_clean();

    }

    /**
     * @throws Exception
     */
    function getHeaderJs(): string
    {

        $lang = bpfw_getCurrentLanguageCode();

        $lang_include = '';

        if ($lang == 'de') {
            $lang_include = BPFW_WWW_URI . 'tinymce_languages/langs/de.js';
        }

        ob_start();

        /*
         * mobile: {
                theme: 'silver'
              },
              */

        ?>
        tinymce.init({
        selector: '.bpfw_tinymce',
        skin: 'oxide',
        height: 300,

        init_instance_callback: function (editor) {
        editor.on('Keydown Change', function (e) {
        // console.log('Editor contents was changed.');

        if (typeof getIdOfCurrentDialog === 'function') {
        var dialogid = getIdOfCurrentDialog();
            jQuery("#" + dialogid + " .bpfwbutton.editAction").show();
            jQuery("#" + dialogid + " .bpfwbutton.cancelAction > div > div").html(__("Discard"));
        }

        });
        },

        /*theme_advanced_resizing_max_width : 300,*/
        menubar:false,
        statusbar:false,
        language_url : '<?php echo $lang_include; ?>',
        language: '<?php echo $lang; ?>',
        plugins: [
        'advlist autolink link image lists charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
        'save table directionality emoticons template paste'
        ],
        /* content_css: '<?php echo VENDOR_PATH; ?>tinymce/tinymce/skins/ui/oxide/content.css', */
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'
        });


        tinymce.init({
        selector: '.bpfw_tinymce_disabled',
        skin: 'oxide',
        height: 300,
        /*theme_advanced_resizing_max_width : 300,*/
        menubar:false,
        statusbar:false,
        /*language_url : '<?php echo BPFW_WWW_URI; ?>tinymce_languages/langs/de.js',*/
        language_url : '<?php echo $lang_include; ?>',
        language: '<?php echo $lang; ?>',
        readonly : 1,
        plugins: [
        'advlist autolink link image lists charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
        'save table directionality emoticons template paste'
        ],
        /* content_css: '<?php echo VENDOR_PATH; ?>tinymce/tinymce/skins/ui/oxide/content.css', */
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'
        });


        <?php

        return ob_get_clean();

    }

    /**
     * Summary of displayAsFormattedPlainValue
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     */
    public function GetDisplayFormattedPlainValue(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {
        if(empty($value))return "";
        return strip_tags($value);
    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        /*
        if($hvalue->hiddenOnEdit){
            return "";
        }*/

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;
        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");
        ob_start();

        //echo "<span class='tinymce_span' id='tinymce_" . $hvalue->name . "'>";

        echo "<span class='tinymce_span' id='tinymcewrap_" . $fieldDbModel->name . "'>";

        $prevalue = $value;
        if (is_array($prevalue)) {
            $prevalue = json_encode($prevalue);
        }

        $disabled = $fieldDbModel->disabled;

        $component_class = "bpfw_tinymce";
        if ($disabled) {
            $component_class = " bpfw_tinymce_disabled";
        }

        $parsedprevalue = bpfw_htmlentities($prevalue);
        if (empty($parsedprevalue)) $parsedprevalue = " ";

        echo "<textarea id='tinymce_" . $fieldDbModel->name . "' " . $this->getDataHtml($fieldDbModel->data) . ' ' . ' rows="10" name="' . $fieldName . '" class = "' . $xtraFormClass . ' ml_10px normal_admin_form_element admin_form_element ' . $component_class . '" placeholder="' . $fieldDbModel->getPlaceholder() . '"  >';
        //$prevalue= str_replace("\\r\\n", "\r\n", $prevalue); // TODO: Funktion suchen
        //$prevalue= str_replace("\\\"", '"', $prevalue); // TODO: Funktion suchen
        //$prevalue= str_replace("\\\\", "\\", $prevalue); // TODO: Funktion suchen
        //echo bpfw_htmlentities($prevalue);
        echo $parsedprevalue;
        echo "</textarea>";


        if (!empty($fieldDbModel->extrabutton_edit)) {

            $extrabutton_label = "(kein Label gesetzt)";
            if (!empty($fieldDbModel->extrabutton_edit["label"]))
                $extrabutton_label = $fieldDbModel->extrabutton_edit["label"];

            $extrabutton_icon = "";
            if (!empty($fieldDbModel->extrabutton_edit["icon"])) {
                $extrabutton_icon = $fieldDbModel->extrabutton_edit["icon"];
            }

            ?>

            <a data-rowkey='<?php echo $rowKey; ?>' data-buttonfor="<?php echo $fieldName; ?>"
               style='position:relative;left:-12px;padding-top:5px;'
               class="btn btn-secondary btn-default bpfwbutton button extrabutton extrabutton_edit">
                <div class="new-entry-button">
                    <i class="topbuttonicon <?php echo $extrabutton_icon; ?>"></i>
                    <div>
                        <?php echo $extrabutton_label; ?>
                    </div>
                </div>
            </a>

            <?php
        }

        echo "</span>";


        return ob_get_clean();

    }

    /**
     * Summary of displayAsAdd
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;
        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        ob_start();
        $prevalue = $value;
        if (is_array($prevalue)) {
            $prevalue = json_encode($prevalue);
        }
        echo "<span id='tinymcewrap_" . $fieldDbModel->name . "'>";

        $component_class = "bpfw_tinymce";


        $disabled = $fieldDbModel->disabled;

        if ($disabled) {
            $component_class = " bpfw_tinymce_disabled";
        }

        echo '<textarea ' . $this->getDataHtml($fieldDbModel->data) . ' ' . ' rows="10" name="' . $fieldName . '" class = "' . $xtraFormClass . ' normal_admin_form_element admin_form_element ml_10px ' . $component_class . '" placeholder="' . $fieldDbModel->getPlaceholder() . '"  >';
        //$prevalue= str_replace("\\r\\n", "\r\n", $prevalue); // TODO: Funktion suchen
        //$prevalue= str_replace("\\\"", '"', $prevalue); // TODO: Funktion suchen
        //$prevalue= str_replace("\\\\", "\\", $prevalue); // TODO: Funktion suchen
        echo bpfw_htmlentities($prevalue);
        echo "</textarea>";

        if (!empty($fieldDbModel->extrabutton_add)) {

            $extrabutton_label = "(kein Label gesetzt)";
            if (!empty($fieldDbModel->extrabutton_add["label"]))
                $extrabutton_label = $fieldDbModel->extrabutton_add["label"];

            $extrabutton_icon = "";
            if (!empty($fieldDbModel->extrabutton_add["icon"]))
                $extrabutton_icon = $fieldDbModel->extrabutton_add["icon"];

            ?>
            <a data-rowkey='-1' data-buttonfor="<?php echo $fieldName; ?>"
               style='position:relative;left:-12px;padding-top:5px;'
               class="btn btn-secondary btn-default bpfwbutton button extrabutton extrabutton_add">
                <div class="new-entry-button">
                    <i class="topbuttonicon <?php echo $extrabutton_icon; ?>"></i>
                    <div>
                        <?php echo $extrabutton_label; ?>
                    </div>
                </div>
            </a>
            <?php
        }

        echo "</span>";

        return ob_get_clean();

    }

}
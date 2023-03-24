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

require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");

require_once(BPFW_MVC_PATH . "views/defaultView.inc.php");


class MailerModel extends BpfwModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {

        parent::__construct($values, $autocompleteVariables);

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_ADMIN;

        $this->showdata = false;

        $jspath = VENDOR_URI . "tinymce/tinymce/tinymce.min.js";
        bpfw_register_js("Tinymce", $jspath);

        $jspath = VENDOR_URI . "tinymce/tinymce/jquery.tinymce.min.js";
        bpfw_register_js("Tinymce_jquery", $jspath, false, array("Tinymce"));

        bpfw_register_js_inline("mailtext_tinymce", $this->getHeaderJs());


    }


    function getHeaderJs(): string
    {

        ob_start();

        ?>

        <script>

            tinyMCE.init({
                selector: '.mailtext_tinymce',
                skin: 'oxide',
                width: 600,
                height: 300,
                menubar: false,
                statusbar: false,
                language_url : '<?php echo BPFW_WWW_URI . 'tinymce_languages/langs/de.js'; ?>',
                language: 'de',
                plugins: [
                    'advlist autolink link image lists charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                    'save table directionality emoticons template paste'
                ],
                /*content_css: 'css/content.css',*/
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'
            });

        </script>

        <?php

        return ob_get_clean();

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "mailer";
    }

    public function GetTitle(): string
    {
        return __("Mass mail");
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     */
    protected function loadDbModel(): array
    {
        return array();
    }

}
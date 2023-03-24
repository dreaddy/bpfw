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
class QuickuploadComponent extends DefaultComponent
{

    function __construct($name, $componentHandler)
    {

        parent::__construct($name, $componentHandler);

        $this->showOnList = true;

    }

    function getRedrawJs(): string
    {

        ob_start();

        echo $this->getHeaderJs(false);

        return ob_get_clean();

    }

    function getHeaderJs($ondocumentready = true): string
    {

        ob_start();


        ?>
        <?php if ($ondocumentready) {
        // https://plugins.krajee.com/file-input
        ?>
        jQuery(document).ready(function(){

    <?php } else {
        ?>
        jQuery(".bpfw_quickuploader_extended").fileinput( "destroy" );
        <?php
    } ?>


        jQuery(".bpfw_quickuploader_extended").each(function(i){

        var callid = "<?php echo getorpost('filter') ?>";
        ;
        if ( typeof get_quickupload_id == 'function' ) {
        callid = get_quickupload_id(jQuery(this).data("type"));
        }

        var crudtype = jQuery(this).data("crudtype");


        var uploadUrl = "?p=<?php echo getorpost('p'); ?>&callid="+callid+"&ajaxCall=true&command=submitQuickUpload&crudtype="+crudtype;
        // alert(uploadUrl);
        jQuery(this).fileinput({ "language": "en", "theme": "explorer-fa6", "uploadUrl": uploadUrl });

        });

        /*  jQuery(".bpfw_quickuploader_extended").fileinput({
        language: "en", theme: "explorer-fa6", showUpload: true
        });*/


        jQuery('.bpfw_quickuploader_extended').on('fileuploaded', function (event, previewId, index, fileId) {

        if (typeof bpfw_refreshPageAfterDataChange === "function")
        bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());

        });


        <?php if ($ondocumentready) { ?>
        });
    <?php } ?>


        <?php

        return ob_get_clean();

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

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        ob_start();

        echo "<span id='file_" . $fieldDbModel->name . "'>";

        $prevalue = $value;
        $hasValue = !empty($prevalue);

        if ($prevalue !== null)
            $vals = json_decode($prevalue);

        if ($hasValue && !empty($vals)) {
            echo "<a href='" . UPLOADS_URI . $vals->new_name . "' target='_blank' class='downloadAttachmentButton button'>" . bpfw_cutStringToLength($vals->name, 42) . " [" . ((int)($vals->size / 1024)) . " KB]" . "</a>";
        }

        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        $requiredTxt = ""; // TODO: only required when existing file is empty...

        if ($fieldDbModel->filefield_use_extended_uploader) {
            $xtraFormClass .= " bpfw_quickuploader_extended ";
        }

        echo "<span id='file_" . $fieldDbModel->name . "'>";

        echo '<input data-crudtype="edit" data-rowid="' . getorpost("rowid") . '" data-type="' . $fieldName . '" ' . $requiredTxt . " " . $this->getDataHtml($fieldDbModel->data) . ' accept="' . $fieldDbModel->file_filetypes . '" style="display: inline-block;" type="file" name="' . $fieldName . '" size="40" class="' . $xtraFormClass . ' normal_admin_form_element admin_form_element bpfw_fileinput bpfw_quickuploader" aria-required="false" aria-invalid="false" multiple>';

        echo "</span>";

        echo ob_get_clean();

        return ob_get_clean();

    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        ob_start();


        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        if ($fieldDbModel->filefield_use_extended_uploader) {
            $xtraFormClass .= " bpfw_quickuploader_extended ";
        }

        echo "<span id='file_" . $fieldDbModel->name . "'>";

        echo '<input data-crudtype="add" data-rowid="' . getorpost("rowid") . '" data-type="' . $fieldName . '" ' . $requiredTxt . " " . $this->getDataHtml($fieldDbModel->data) . ' accept="' . $fieldDbModel->file_filetypes . '" style="display: inline-block;" type="file" name="' . $fieldName . '" size="40" class="' . $xtraFormClass . ' normal_admin_form_element admin_form_element bpfw_fileinput bpfw_quickuploader" aria-required="false" aria-invalid="false" multiple>';

        echo "</span>";

        return ob_get_clean();

    }


    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if (empty($value) || !isJson($value)) return "";

        ob_start();

        //  {"name":"Steuerbescheid 2015.pdf","type":"application\/pdf","tmp_name":"D:\\work\\abdu\\stundenerfassung\\srv\\tmp\\phpCC5A.tmp","error":0,"size":1278798,"new_name":"\\customertimeline\\2\\2_1551360325_Steuerbescheid 2015.pdf"}
        $info = json_decode($value);

        echo "<a href = '" . UPLOADS_URI . $info->new_name . "' target='_blank'>";
        echo "<i class='fa fa-paperclip'>";
        echo bpfw_cutStringToLength($info->name, 25) . " [" . ((int)($info->size / 1024)) . " KB]";
        echo "</i>";
        echo "</a>";

        return ob_get_clean();

    }


}

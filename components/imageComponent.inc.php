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
 * Upload and show images with this component
 *
 * @version 1.0
 * @author torst
 */
class ImageComponent extends DefaultComponent
{

    function __construct($name, $componentHandler)
    {

        parent::__construct($name, $componentHandler);


        bpfw_register_js("croppie_js", LIBS_URI . "croppie-master/assets/js/croppie.min.js");
        bpfw_register_css("croppie_css", LIBS_URI . "croppie-master/assets/css/croppie.css", false);

        $this->showOnList = true;

    }

    function getCss(): string
    {

        ob_start();

        ?>


        .previewContainer, .snipButton, .showOnCopperOpened{
        display:none;
        margin:10px;
        }

        .showOnCopperOpened{
        }

        .previewImage{

        /* border: 1px solid grey; */

        margin: auto;
        /* text-align: center; */
        display: block;

        /*max-width:200px;*/
        max-height:75px;
        padding:5px;
        padding-top:20px;
        padding-bottom:0

        }

        .imagecomponent_wrapper > div
        {
        border: 1px solid #ccc;
        border-radius:5px;
        margin-bottom:10px;
        margin-top:10px;
        padding-top:5px;
        padding-bottom:5px;
        }

        .imagecomponent_wrapper > div
        {

        }


        <?php

        return ob_get_clean();

    }

    function getRedrawJs(): string
    {

        ob_start();

        ?>


        jQuery("#" + getIdOfCurrentDialog() +' .image_cropper').each(function (index) {


        if (jQuery(this).hasClass("croppie_initialized")) {

        return;

        }


        initCroppie(this, true);


        });

        <?php

        return ob_get_clean();

    }

    function getFooterJs($onDocumentReady = true): string
    {

        ob_start();
        ?>

        <script>

            function initCroppie(ithis, reinit = false) {

                let viewport_type = jQuery(ithis).data("viewport_type") !== "circle" ? "square" : "circle";
                let viewport_width = jQuery(ithis).data("viewport_width");
                let viewport_height = jQuery(ithis).data("viewport_height");
                if (viewport_type === "circle") {
                    viewport_height = jQuery(ithis).data("viewport_width");
                }

                let boundry_width = jQuery(ithis).data("boundry_width");
                let boundry_height = jQuery(ithis).data("boundry_height");


                let enable_resize = jQuery(ithis).data("enable_resize");
                let enable_zoom = jQuery(ithis).data("enable_zoom");

                //let optional_crop = jQuery(ithis).data("optional_crop");
                let enable_crop = jQuery(ithis).data("enable_crop");

                if (!enable_crop) return;

                if (jQuery(ithis).hasClass("croppie_initialized")) {

                    if (!reinit) return;
                    jQuery(ithis).croppie('destroy');

                } else {
                    jQuery(ithis).addClass("croppie_initialized");

                    imgCropperArray.push(ithis);
                }

                jQuery(ithis).croppie({
                    enableExif: true,
                    enableResize: enable_resize,
                    enableZoom: enable_zoom,

                    viewport: {
                        width: viewport_width,
                        height: viewport_height,
                        type: viewport_type
                    },
                    boundary: {
                        width: boundry_width,
                        height: boundry_height
                    }
                });

            }

            jQuery(document).ready(function () {


                jQuery(document).on("click", ".addEntryDialog  .saveImageButton", function (e) {

                        submitAddDialog(false);
                        e.preventDefault();

                    }
                );

                jQuery(document).on("click", ".editEntryDialog  .saveImageButton", function (e) {

                    submitEditDialog(false);
                    e.preventDefault();

                });


                jQuery(document).on("click", ".changeToCircle", function (e) {


                    e.preventDefault();

                    const iThis = this;

                    jQuery("#" + getIdOfCurrentDialog() + ' .image_cropper').each(function () {

                        if (jQuery(this).data("name") === jQuery(iThis).data("name")) {
                            jQuery(this).data("viewport_type", "circle");

                            initCroppie(this, true);
                            //alert(jQuery(this).data("name"));
                            reloadFile(jQuery(this).data("name"));
                        }

                    });


                });


                jQuery(document).on("click", ".changeToSquare", function (e) {

                    e.preventDefault();

                    const ithis = this;

                    jQuery("#" + getIdOfCurrentDialog() + ' .image_cropper').each(function () {

                        if (jQuery(this).data("name") === jQuery(ithis).data("name")) {

                            jQuery(this).data("viewport_type", "square");

                            initCroppie(this, true);

                            reloadFile(jQuery(this).data("name"));

                        }

                    });


                });


                function reloadFile(name) {

                    console.log("reloadfile " + "#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .image_cropper');

                    const reader = new FileReader();

                    const t = jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .upload_image');

                    reader.onload = function (event) {
                        const imagecropper = jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .image_cropper');

                        initCroppie(imagecropper, false);

                        imagecropper.croppie('bind', {
                            url: event.target.result
                        }).then(function () {
                            console.log('jQuery bind complete');


                        });

                    }

                    if (t[0].files[0] !== undefined) {
                        reader.readAsDataURL(t[0].files[0]);
                    }


                }


                jQuery("body").on("click", ".snipButton",

                    function (e) {

                        const name = jQuery(e.target).data("name");

                        console.log("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .previewContainer');
                        const previewContainer = jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .previewContainer');

                        const visible = previewContainer.is(":visible");


                        const input = jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .upload_image');

                        if (!visible) {

                            jQuery(e.target).html("Stop cropping");

                            previewContainer.show(0, function () {


                                const reader = new FileReader();

                                reader.onload = function (event) {
                                    const imagecropper = jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .image_cropper');

                                    initCroppie(imagecropper, false);

                                    imagecropper.croppie('bind', {
                                        url: event.target.result
                                    }).then(function () {
                                        console.log('jQuery bind complete');
                                    });

                                }

                                if (input[0].files[0] !== undefined) {
                                    reader.readAsDataURL(input[0].files[0]);
                                }

                            });

                        } else {
                            jQuery(e.target).html("Select image section");
                            previewContainer.hide();
                        }
                    }
                );

                jQuery("body").on("change", '.upload_image', function (e) {

                    const t = e.target;
                    // alert(jQuery(e.target).data("optional_crop") );

                    const optional_crop = jQuery(t).data("optional_crop");
                    const enable_crop = jQuery(t).data("enable_crop");

                    if (!enable_crop) return;


                    const name = jQuery(t).data("name");


                    const reader = new FileReader();

                    reader.onload = function (event) {

                        const imagecropper = jQuery("#" + getIdOfCurrentDialog() + ' .imagecomponent_wrapper#file_' + name + ' .image_cropper');

                        console.log("upload_image " + "#" + getIdOfCurrentDialog() + ' .imagecomponent_wrapper#file_' + name + ' .image_cropper');
                        console.log(jQuery('.imagecomponent_wrapper#file_' + name + '  .image_cropper').length);

                        if (imagecropper.length === 1) {
                            initCroppie(imagecropper, false);
                            imagecropper.croppie('bind', {
                                url: event.target.result
                            }).then(function () {
                                console.log('jQuery bind complete');
                            });

                        } else {
                            console.log(imagecropper.length + " imagecroppers found");
                        }


                    }

                    reader.readAsDataURL(t.files[0]);

                    if (!enable_crop) {
                        jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .snipButton').hide();
                        jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .previewContainer').hide();
                        // jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .showOnCopperOpened').hide();
                    } else if (optional_crop) {
                        jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .snipButton').show();
                        jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .previewContainer').hide();
                        // jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .showOnCopperOpened').hide();
                    } else {
                        jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .snipButton').hide();
                        jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .previewContainer').show();
                        // jQuery("#" + getIdOfCurrentDialog() + ' span[data-name="' + name + '"] .showOnCopperOpened').show();
                    }

                });


                // TODO: name specific
                jQuery(document).on("click", '.crop_image', function () {


                    jQuery("#" + getIdOfCurrentDialog() + '.image_cropper').each(function () {

                        const name = jQuery(this).data("name");

                        jQuery(this).croppie('result', {
                            type: 'canvas',
                            size: 'viewport'
                        }).then(function () {


                            jQuery('span[data-name="' + name + '"] .previewContainer').hide();
                            jQuery('span[data-name="' + name + '"] .uploaded_image').html(data);

                            /*
                            jQuery.ajax({
                                url: "upload.php",
                                type: "POST",
                                data: { "image": response },
                                success: function (data) {
                                    jQuery('span[data-name="'+name+'"] .previewContainer').hide();
                                    jQuery('span[data-name="' + name +'"] .uploaded_image').html(data);
                                }
                            });*/

                        });

                    });

                });


            });

        </script>


        <?php

        return ob_get_clean();

    }

    /**
     * @throws Exception
     */
    protected function displayAsAdd(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, BpfwModel $model): string
    {
        return $this->displayAsEdit($value, $fieldName, $fieldDbModel, $model, -1);
    }

    /**
     * Summary of displayAsEdit
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param BpfwModel $model
     * @param string|int $rowKey
     * @return string
     * @throws Exception
     */
    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        //echo "123".$value;
        //var_dump(json_decode(stripslashes($value)));


        $dataattributes = " data-viewport_width='" . $fieldDbModel->imagecomponent_viewport_width . "' ";
        $dataattributes .= " data-viewport_height='" . $fieldDbModel->imagecomponent_viewport_height . "' ";
        $dataattributes .= " data-viewport_type='" . $fieldDbModel->imagecomponent_viewport_type . "' ";
        $dataattributes .= " data-show_typeswitcher='" . $fieldDbModel->imagecomponent_show_typeswitcher . "' ";
        $dataattributes .= " data-boundry_width='" . $fieldDbModel->imagecomponent_boundry_width . "' ";
        $dataattributes .= " data-boundry_height='" . $fieldDbModel->imagecomponent_boundry_height . "' ";

        $dataattributes .= " data-enable_resize='" . bpfw_boolToString($fieldDbModel->imagecomponent_enable_resize) . "' ";
        $dataattributes .= " data-enable_zoom='" . bpfw_boolToString($fieldDbModel->imagecomponent_enable_zoom) . "' ";
        $dataattributes .= " data-optional_crop='" . bpfw_boolToString($fieldDbModel->imagecomponent_optional_crop) . "' ";

        $dataattributes .= " data-enable_crop='" . bpfw_boolToString($fieldDbModel->imagecomponent_enable_crop) . "' ";

        $dataattributes .= " data-rowkey='" . $rowKey . "' ";
        $dataattributes .= " data-changed='0' ";


        //     $imgdata = null;
//
        //      if(!empty($value))
        //          $imgdata = json_decode(stripslashes($value));

        $xtraFormClass = isset($fieldDbModel->xtraFormClass) ? $fieldDbModel->display . " " . $fieldDbModel->xtraFormClass : $fieldDbModel->display;

        ob_start();

        echo "<span " . $dataattributes . " class='imagecomponent_wrapper' data-name='$fieldName'  id='file_" . $fieldDbModel->name . "'><div style='display:inline-block;padding-top:10px;'>";

        $prevalue = $value;
        $hasValue = !empty($prevalue);


        if ($prevalue !== null)
            $vals = json_decode($prevalue);

        if ($hasValue && !empty($vals)) {
            //  echo "<a href='".UPLOADS_URI.$vals->new_name."' target='_blank' class='downloadAttachmentButton button'>".bpfw_cutStringToLength($vals->name,42)." [".((int)($vals->size/1024))." KB]"."</a>";

            echo "<img id='" . $fieldName . "_previewImage' class='previewImage " . ($fieldDbModel->disabled ? "img_disabled" : "img_not_disabled") . "' style='' src='" . UPLOADS_URI . $vals->new_name . "'>";


        } else {
            echo "<img id='" . $fieldName . "_previewImage' class='previewImage " . ($fieldDbModel->disabled ? "img_disabled" : "img_not_disabled") . "' style='' src=''>";

        }

        $required = isset($fieldDbModel->required) && $fieldDbModel->required == true;
        $requiredTxt = ($required ? "required" : "");

        $requiredTxt = ""; // TODO: only required when existing file is empty...

        if ($fieldDbModel->filefield_use_extended_uploader) {
            $xtraFormClass .= " bpfw_fileinput_extended ";
        }

        ?>

        <div class="uploaded_image"></div>
        <div data-name="<?php echo $fieldName; ?>"
             class='showCropper previewContainer <?php echo($fieldDbModel->disabled ? "img_disabled" : "img_not_disabled"); ?>'>


            <div <?php echo $dataattributes; ?> data-name="<?php echo $fieldName; ?>" class="image_cropper"
                                                style="margin-top:30px"></div> <?php /* width:350px;  */ ?>

            <?php if ($fieldDbModel->imagecomponent_show_typeswitcher) { ?>
                <a href='#' class='changeToCircle button' data-name="<?php echo $fieldName; ?>">Rund</a>
                <a href='#' class='changeToSquare button' data-name="<?php echo $fieldName; ?>">Eckig</a>
            <?php } ?>

            <?php if ($fieldDbModel->imagecomponent_show_savebutton) { ?>
                <a href='#' class='saveImageButton button' data-name="<?php echo $fieldName; ?>">Speichern</a>
            <?php } ?>

        </div>

        <?php

        echo '<div class="image_input_wrap" style="display:' . ($fieldDbModel->disabled ? "none" : "inline-block") . ';"><input style="border:0" ' . $dataattributes . ' ' . " " . $this->getDataHtml($fieldDbModel->data) . ' accept="image/*" style="display: inline-block;" type="file" data-name="' . $fieldName . '"  name="' . $fieldName . '" size="40" class="' . $xtraFormClass . ' normal_admin_form_element admin_form_element bpfw_fileinput upload_image" aria-required="false" aria-invalid="false">';
        // var_dump($hvalue->data);


        ?>

        <div data-name="<?php echo $fieldName; ?>" class="button snipButton">

            <?php echo __("Select image section"); ?>

        </div>

        <div class="showOnCopperOpened previewContainer">
            <div class="showOnCopperOpened_inner">
                <?php echo __("Click save below to submit image"); ?>
            </div>
        </div>
        <?php


        echo "</div>";
        echo "</div>";


        echo "</span>";

        return ob_get_clean();
    }

    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {

        if (empty($value)) return "";

        ob_start();

        $info = json_decode($value);

        if (is_object($info)) {
            echo "<img id='" . $fieldName . $rowKey . "_previewImage_label' class='previewImage' style='' src='" . UPLOADS_URI . $info->new_name . "'>";
        }
        return ob_get_clean();

    }


}

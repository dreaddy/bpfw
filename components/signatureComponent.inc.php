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
class SignatureComponent extends DefaultComponent
{

    function getRedrawJs(): string
    {
        ob_start();
        ?>

        jQuery('.createNewSignature').off();
        jQuery('.createNewSignature_required').off();


        <?php
        echo $this->getFooterJs();

        return ob_get_clean();

    }

    function getFooterJs(): string
    {
        ob_start(); ?>


        // signature
        jQuery(document).ready(function () {
        /********************** Capture and save a signature **********************/

        /*** Configure options for SignaturePad ***/
        var options_required = {
        defaultAction: 'drawIt',
        drawOnly: true, // error on empty -> false
        lineTop: 120,
        lineMargin: 0,
        penColour: '#00f',
        penWidth: 2,
        /* clear: ".undoSignatureButton" */
        };

        var options = {
        defaultAction: 'drawIt',
        drawOnly: false, // error on empty -> false
        lineTop: -10,
        lineMargin: 0,
        penColour: '#00f',
        penWidth: 2,
        /* clear: ".undoSignatureButton" */
        };

        /* Initialize the plugin with configured options to accept a signature ***/
        var newsig = jQuery('.createNewSignature').signaturePad(options);
        var newsig_required = jQuery('.createNewSignature_required').signaturePad(options_required);

        jQuery(".clearButton").click(function () {
        newsig.clearCanvas();
        newsig_required.clearCanvas();
        });


        });

        <?php
        return ob_get_clean();

    }

    /**
     * Summary of displayAsLabel
     * @param mixed $value
     * @param string $fieldName
     * @param BpfwModelFormfield $fieldDbModel
     * @param int|string $rowKey
     * @param BpfwModel $model
     * @return string
     */
    protected function displayAsLabel(mixed $value, string $fieldName, BpfwModelFormfield $fieldDbModel, int|string $rowKey, BpfwModel $model): string
    {
        ob_start();
        ?>

        <div class="signaturecomponent">

            <div class="signed">
                <div <?php echo $this->getDataHtml($fieldDbModel->data) ?>
                        class="signatureTop sigWrapper signature_<?php echo $rowKey; ?>">
                    <canvas class="pad" width="350" height="150"></canvas>
                </div>
            </div>

            <script>
                jQuery(document).ready(function () {

                    const signature = jQuery('.signature_<?php echo $rowKey; ?>').signaturePad({displayOnly: true});
                    if ('<?php echo $value; ?>' !== '')
                        signature.regenerate('<?php echo $value; ?>')
                });
            </script>

        </div>

        <?php
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
        $xtraFormClass = $fieldDbModel->xtraFormClass ?? "";
        $cssClass = $fieldDbModel->display;
        ob_start();


        $sigClass = "createNewSignature";
        if ($fieldDbModel->required) {
            $sigClass = "createNewSignature_required";
        }

        ?>

        <div class="createNewSignatureWrapper" style="width:360px;">


            <?php if (!empty($value)) { ?>

                <h7>Bestehende Signatur verwenden:</h7>

                <div <?php echo $this->getDataHtml($fieldDbModel->data) ?>
                        class="sig sigWrapper sigWrapper_duplicate <?php echo $sigClass; ?>"
                        style="border:1px solid black">

                    <canvas class="pad" width="350"
                            height="150"></canvas><?php echo '<input type="hidden" name="' . $fieldName . '_duplicate" class = "' . $cssClass . " " . $xtraFormClass . ' admin_form_element output" />'; ?>

                </div>


                <script>
                    jQuery(document).ready(function () {

                        const signature = jQuery('.sigWrapper_duplicate').signaturePad({displayOnly: true});

                        if ('<?php echo $value; ?>' !== '')
                            signature.regenerate('<?php echo $value; ?>')


                    });
                </script>
                <br/><br/>
                <h7>Oder neue Signatur:</h7>

            <?php } ?>


            <div class="sigPad">

                <div <?php echo $this->getDataHtml($fieldDbModel->data) ?>
                        class="sig sigWrapper sigWrapper_add <?php echo $sigClass; ?>">

                    <canvas class="pad" width="350"
                            height="150"></canvas><?php echo '<input  type="hidden" name="' . $fieldName . '" class = "' . $cssClass . " " . $xtraFormClass . ' admin_form_element output" />'; ?>

                </div>
                <div class="clearButton">zur&uuml;cksetzen</div>

            </div>


        </div>

        <?php
        return ob_get_clean();

    }

    protected function displayAsEdit(mixed $value, string $fieldName, BpfwModelFormField $fieldDbModel, BpfwModel $model, string|int $rowKey): string
    {

        if ($fieldDbModel->signature_EditableOnEdit) {

            return $this->displayAsEditEditable($value, $fieldName, $fieldDbModel, $model, $rowKey);

        }

        ob_start();


        ?>

        <div style="width:402px;height:auto;margin:50px auto;padding: 20px 0;background-color:#fff;border:1px dotted #999;">

        <div class="signed">
            <div class="signatureTop sigWrapper topsignature_<?php echo $rowKey; ?>">
                <canvas class="pad" width="350" height="150"></canvas>
            </div>

        </div>

        <script>
            jQuery(document).ready(function () {

                const signature = jQuery('.topsignature_<?php echo $rowKey; ?>').signaturePad({displayOnly: false});

                <?php

                $json = $value;

                ?>
                if ('<?php echo $json; ?>' !== '')
                    signature.regenerate('<?php echo $json; ?>')

            });

        </script>

        </div><?php


        return ob_get_clean();

    }

    /**
     * Display Signature field in a version that can be edited
     * @param mixed $value
     * @param string $hkey
     * @param BpfwModelFormfield $hvalue
     * @param BpfwModel $model
     * @param string|int $rowkey
     * @return string
     */
    protected function displayAsEditEditable(mixed $value, string $hkey, BpfwModelFormfield $hvalue, BpfwModel $model, string|int $rowkey): string
    {
        $xtraFormClass = $hvalue->xtraFormClass ?? "";
        $cssClass = $hvalue->display;
        ob_start();

        $sigClass = "createNewSignature";
        if ($hvalue->required) {
            $sigClass = "createNewSignature_required";
        }

        ?>

        <div class="createNewSignatureWrapper" style="width:360px;">


            <?php if (!empty($value)) { ?>

                <h7>Bestehende Signatur übernehmen:</h7>

                <div <?php echo $this->getDataHtml($hvalue->data) ?>
                        class="sig sigWrapper sigWrapper_duplicate <?php echo $sigClass; ?>"
                        style="border:1px solid black">

                    <canvas class="pad" width="350"
                            height="150"></canvas><?php echo '<input type="hidden" name="' . $hkey . '_duplicate" class = "' . $cssClass . " " . $xtraFormClass . ' admin_form_element output" />'; ?>

                </div>


                <script>
                    jQuery(document).ready(function () {

                        const signature = jQuery('.sigWrapper_duplicate').signaturePad({displayOnly: true});

                        if ('<?php echo $value; ?>' !== '')
                            signature.regenerate('<?php echo $value; ?>')

                        //signature.fromData('<?php echo $value; ?>')


                    });
                </script>
                <br/>
                <br/>
                <h7>Oder neue Signatur:</h7>

            <?php } ?>


            <div class="sigPad">

                <div <?php echo $this->getDataHtml($hvalue->data) ?>
                        class="sig sigWrapper sigWrapper_add <?php echo $sigClass; ?>">

                    <canvas class="pad" width="350"
                            height="150"></canvas><?php echo '<input  type="hidden" name="' . $hkey . '" class = "' . $cssClass . " " . $xtraFormClass . ' admin_form_element output" />'; ?>

                </div>
                <div class="clearButton">zur&uuml;cksetzen</div>

            </div>


        </div>

        <?php
        return ob_get_clean();

    }

}
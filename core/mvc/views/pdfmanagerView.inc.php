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



class PdfmanagerView extends DefaultView
{


    function __construct($model)
    {
        bpfw_register_js("pdfmanagerview_js", BPFW_JS_URI."pdfmanagerView.js", true);
        parent::__construct($model);
    }

    /**
     * Summary of renderDefaultListView
     * @return void
     * @throws Exception
     */
    function renderView(): void
    {

        $model = $this->model;

        global $database;

        $modelused = bpfw_getdb()->escape_string(getorpost('modelused'));
        $Id = bpfw_getdb()->escape_string(getorpost('filter'));

        $variables = $this->model->getVariablesForTextReplacement($Id, $modelused);

        if (!is_array($variables)) {
            die($variables);
        }

        $errors = "";
        $eventcheckSuccess = true;

        ?>

            <style>
                .buttonbar.buttonbar-top{
                    margin:0;
                }
            </style>

        <h3 class="content-headline">
            <div> <?php echo $model->getTitle(); ?></div>
        </h3>


        <?php

        $pages = $this->getPdfPages();


        ?>

        <div id="activePdfCategoryWrap">


            <select id="activePdfCategory" class="selectpicker">

                    <optgroup label="any">

                        <?php foreach ($pages as $catid => $category) {


                            ?>


                            <option value="<?php echo $catid; ?>"><?php echo $category->title; ?></option>

                        <?php } ?>

                    </optgroup>

            </select>

        </div>

        <style>
            .sendmail-content,
            .mailselect-content {
                max-width: 400px;
                display: inline-block;
                vertical-align: top;

            }

            .sendmail-content {
                max-width: 600px;
                width: 600px;

                float: left;
                margin-left: 40px;

            }

            .mailselect-content {


            }

        </style>

        <?php

        $emailattachments = $this->model->fetchAllMailAttachments();



        foreach ($pages as $catid => $category) { // PdfCategory

            $pdfAttachments = $this->model->getAllPdfPagesForCategory($catid);

            $hasGeneratedPdfAttachments = !empty($pdfAttachments);

            $rcpt = $category->recipient;

            $mail = "unknown";

            if ($rcpt == "user") {
                $mail = "No User set or user has no mail!";
                if (isset($variables["user.email"])) {
                    $mail = $variables["user.email"];
                }
            }

            if ($rcpt == "customer") {
                $mail = "No User set or user has no mail!";
                if (isset($variables["customer.email"])) {
                    $mail = $variables["customer.email"];
                }
            }

            if ($rcpt == "intern") {
                $mailaddress = bpfw_loadSetting(SETTING_EMAIL_INTERN);
                $mail = $mailaddress; // "info@erfolgspfad.de";
            }


            // payerInvoice

            ?>

            <div id="<?php echo $catid; ?>_form" class="edit-content-form">

                <div class="mailselect-content">

                    <!--<h5 style="padding-left:40px;font-weight:bold;text-decoration:underline;"><?php echo $category->title; ?></h5>-->

                    <?php if (!$eventcheckSuccess) { ?>

                        <h5 style="padding-left:40px;font-weight:bold;padding-top:20px;">Hinweise:</h5>
                        <div style=" margin-left:40px; font-weight:bold;margin-top:20px;">
                            <?php echo $errors; ?>
                        </div>

                    <?php } ?>


                    <h5 class="attachment_header" style="padding-left:40px;font-weight:bold;padding-top:20px;">
                        <?php echo __("Attachments"); ?>:</h5>


                    <?php if ($hasGeneratedPdfAttachments) { ?>

                        <div class="attachment_header_2" style="margin-left:40px; font-weight:bold;margin-top:20px;">

                            <b><?php echo __("Generated pdf attachment"); ?>:</b><br/><br/>

                            <input data-category="<?php echo $catid; ?>"
                                   class="selectAllCheckbox <?php echo $catid; ?>_all" id="<?php echo $catid; ?>_all"
                                   type="checkbox" name="<?php echo $catid; ?>_all" checked/><?php echo __("Select all"); ?>
                        </div>

                        <?php
                    } ?>

                    <ul class="attachment_list" style="list-style:none">

                        <?php

                        $lastgroup = "";


                        //$join = bpfw_add_filter(BpfwModel::FILTER_PDFMANAGER_ATTACHMENTS, $this->GetTableName(), array($join, $where, $count, $offset, $sort), $this); // must be real tablename, not temptable



                        if (!empty($pdfAttachments))
                        {

                            foreach ($pdfAttachments as $group => $attachmentgroupdata) {
                                foreach ($attachmentgroupdata as $docid => $document) {

                                    $group = $document->group;

                                    $checked = "checked";
                                    $xtraclass = "";

                                    if (!empty($group)) {

                                        $xtraclass = "groupedCheckbox";
                                        if ($lastgroup == $group) {
                                            $checked = "";
                                        } else {
                                            $lastgroup = $group;
                                        }

                                    }

                                    if (!$document->selected) {
                                        $checked = "";
                                    }
                                    ?>

                                    <li>
                                        <input data-group="<?php echo trim($document->group); ?>"
                                               data-category="<?php echo $catid; ?>" data-docid="<?php echo $docid; ?>"
                                               class="<?php echo $xtraclass; ?> documentSelectCheckbox category_<?php echo $document->category; ?>"
                                               id="<?php echo $document->id; ?>" type="checkbox"
                                               name="<?php echo $document->id; ?>" <?php echo $checked; ?> /><?php echo $document->title; ?>
                                    </li>

                                    <?php

                                }
                            }
                        }

                        $attachments = array();
                        if(!empty($emailattachments[$catid]))$attachments = empty($emailattachments[$catid]);
                        if(!empty($emailattachments["All"]))$attachments = array_merge($emailattachments["All"], $attachments);

                        if (!empty($attachments)) {

                            echo "<hr>";

                            echo "<b>".__("Attachments of category").": (<a href='?p=emailattachments'>".__('Manage attachments')."</a>)</b>";

                            foreach ($attachments as $attachment) {

                                $id = $attachment["emailattachmentId"];
                                $headline = $attachment["headline"];
                                $attachment_name = $attachment["attachment_name"];
                                $fileinfo = empty($attachment["file"]) ? "" : json_decode($attachment["file"]);
                                $xtraclass = "";

                                $fullpath = UPLOADS_URI . $fileinfo->new_name;

                                $checked = "";
                                if (isset($attachment["checked"]) && $attachment["checked"] === '1') {
                                    $checked = "checked";
                                }

                                $title = $headline;
                                if (empty($title)) $title = $attachment_name;
                                ?>

                                <li>
                                    <input data-category="<?php echo $catid; ?>"
                                           data-emailattachment="<?php echo $id; ?>"
                                           class="<?php echo "$xtraclass emailattachments_$catid attachmentSelectCheckbox " ?>"
                                           id="<?php echo $id ?>" type="checkbox"
                                           name="<?php echo "attachment_" . $id ?>" <?php echo $checked; ?> /><?php echo $title; ?>

                                    <a href="<?php echo $fullpath; ?>"><i class="fa fa-download" aria-hidden="true"></i></a>

                                </li>


                                <?php

                            }

                        }


                        ?>
                    </ul>
                </div>
                <div class="sendmail-content">

                    <?php

                    $filenameText = APP_MAIL_PATH . $catid . ".html";
                    $filenameTitle = APP_MAIL_PATH . $catid . "Title.html";
                    $filenameSignature = APP_MAIL_PATH . "common".DIRECTORY_SEPARATOR."signature.html";

                    $mailText = "";
                    $mailTitle = "";

                    $mailAttachmentTitle = "Seminarunterlagen";

                    if (!empty($category->attachmentTitle)) {
                        $mailAttachmentTitle = bpfw_sanitize($this->insertVariables($variables, $category->attachmentTitle));
                    }

                    // $signatureTitle = "";

                    if (!file_exists($filenameTitle)) {
                        echo "<b style='color:red'>" . $filenameTitle . " nicht gefunden!!!</b><br>";
                    } else {
                        $mailTitle = $this->insertVariables($variables, file_get_contents($filenameTitle));
                    }

                    if (!file_exists($filenameText)) {
                        echo "<b style='color:red'>" . $filenameText . " nicht gefunden!!!</b><br>";
                    } else {
                        $mailText = $this->insertVariables($variables, file_get_contents($filenameText));
                    }

                    $signatureText = "";

                    $signatureText = str_replace("\\\"", "\"", $signatureText);

                    if (empty($signatureText)) {
                        if (!file_exists($filenameSignature)) {
                            echo "<b style='color:red'>" . $filenameSignature . " nicht gefunden!!!</b><br>";
                        } else {
                            $signatureText = $this->insertVariables($variables, file_get_contents($filenameSignature));
                        }
                    }

                    $signatureText = bpfw_htmlentities($signatureText);
                    $mailText = bpfw_htmlentities($mailText);

                    ?>

                    <div style="width:100%;max-width:500px;">

                        <div style="width:100%;">
                            <label for="<?php echo $catid; ?>_receiver"><?php echo __("Recipient"); ?>:</label>
                            <input placeholder="<?php echo __("Recipient"); ?>" name="<?php echo $catid; ?>_receiver" id="<?php echo $catid; ?>_receiver" style="width:100%;"
                                   type="text" value="<?php echo $mail; ?>"/>
                            <br/>
                            <br/>
                            <label for="<?php echo $catid; ?>_title"><?php echo __("Mail title"); ?>:</label>
                            <input placeholder="<?php echo __("Mail title"); ?>" name="<?php echo $catid; ?>_title" id="<?php echo $catid; ?>_title" style="width:100%;"
                                   type="text" value="<?php echo $mailTitle; ?>"/>
                            <br/>
                            <br/>
                            <label style="<?php echo $hasGeneratedPdfAttachments ? "" : "display:none;"; ?>" for="<?php echo $catid; ?>_attachment_title"><?php echo __("Name of pdf attachment"); ?>:</label>
                            <input placeholder="<?php echo __("Name of pdf attachment"); ?>" id="<?php echo $catid; ?>_attachment_title"
                                   style="width:100%;<?php echo $hasGeneratedPdfAttachments ? "" : "display:none;"; ?>"
                                   type="text" value="<?php echo $mailAttachmentTitle; ?>"/>
                            <?php if ($hasGeneratedPdfAttachments) echo "<br /><br />"; ?>

                            <textarea class='bpfw_pdfmanager_tinymce' placeholder="<?php echo __("Mail text"); ?>"
                                                                                      id="<?php echo $catid; ?>_text"
                                                                                      style="width:100%;height:200px;"><?php echo $mailText; ?><?php echo $signatureText; ?></textarea>


                            <br/>
                            <br/>

                            <input id="<?php echo "documentstodisplay_" . $catid; ?>" name="<?php echo "documentstodisplay_" . $catid; ?>" class="documentstodisplay"
                                   style="width:100%;" type="hidden" value=""/>

                            <input name="<?php echo "filestodisplay_" . $catid; ?>" id="<?php echo "filestodisplay_" . $catid; ?>" class="filestodisplay"
                                   style="width:100%;" type="hidden" value=""/>

                            <input name ="<?php echo "emailattachments_" . $catid; ?>" id="<?php echo "emailattachments_" . $catid; ?>" class="emailattachments"
                                   style="width:100%;" type="hidden" value=""/>

                            <button data-category="<?php echo $catid; ?>" class="documentPreview"><?php echo __("Open pdf file in browser");?></button>

                            <button data-category="<?php echo $catid; ?>" id="<?php echo $catid; ?>_sendmail"
                                    class="documentSendMail"><?php echo __("Send as email"); ?>
                            </button>


                        </div>

                    </div>
                </div>


                <br/>

            </div>
            <?php

        }

        function insertVariables($variables, $text)
        {

            foreach ($variables as $key => $value) {
                $text = str_replace('{{{' . $key . '}}}', $value, $text);
            }

            return $text;
        }

        ?>


        <?php

    }

    /**
     * Summary of getPdfPages
     * @return PdfCategory[]
     */
    function getPdfPages(): array
    {
        return $this->model->pdfPages;
    }

    function insertVariables($variables, $text)
    {

        foreach ($variables as $key => $value) {
            $text = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $text);
        }

        return $text;

    }

}
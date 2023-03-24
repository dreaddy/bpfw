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


class MailView extends DefaultView
{

    /**
     * Summary of renderDefaultListView
     * @return void
     * @throws Exception
     */
    function renderView(): void
    {

        $audience = "mail";


        /*if(file_exists(APP_MVC_PATH."models/userModel.inc.php")){
            require_once(APP_MVC_PATH."models/userModel.inc.php");
        }else{
            require_once(BPFW_MVC_PATH."models/userModel.inc.php");
        }

        require_once(APP_MVC_PATH."models/customerModel.inc.php");*/

        global $database;

        ?>

        <h1><?php echo __("Send emails"); ?></h1>

        <br/>
        <br/>

        <style>

            .todolist-wrap {

            }

        </style>


        <script>


            jQuery("body").on("submit", "#mailsubmitform",

                function (e) {

                    e.preventDefault();

                    var formdata = new FormData(document.getElementById('mailsubmitform'));
                    //for (var x of formdata) console.log(x)

                    /*for (var [key, value] of formdata) {
                      alert(key, value);
                    }
                    alert(JSON.stringify(formdata));
                    alert(formdata);*/

                    var filter = getQueryVariable("filter");

                    var currentPage = getQueryVariable('p');

                    if (currentPage === false) currentPage = "";
                    var title_url = "?p=" + currentPage + "&ajaxCall=true&command=mail_send&filter=" + filter;

                    var audience = jQuery(this).data("audience");

                    tinyMCE.triggerSave();

//            var rcpt = jQuery(".testmail_rcpt").val();
//            var title = jQuery(".mailtitle").val();
//            var txt = jQuery(".mailtext_tinymce").val();


                    jQuery.ajax({
                        type: 'POST',
                        url: title_url,
                        data: formdata,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (data) {
                            alert(data);
                        }
                    });

                    // alert("test");

                }
            );

        </script>


        <style>

            .file-input {
                width: 600px;
            }

            .file-drop-zone {
                min-height: initial;
                height: 150px;
            }

            .file-drop-zone-title {
                padding: 53px 15px;
            }

            .block_600px {
                width: 650px;
                display: inline-block;
                vertical-align: top;
                padding: 30px;
            }

        </style>
        <br/>

        <form autocomplete="off" name="mailsubmitform" id="mailsubmitform" enctype="multipart/form-data" class=""
              method="post" action="">

            <div class="block_600px">
                <b><?php echo __("Recipient"); ?>:</b>
                <br/>
                <input name="rcpt" class="testmail_rcpt" type="text" style="width:600px"
                       value="<?php echo bpfw_loadSetting(SETTING_EMAIL_INTERN); ?>"/>

                <br/>

                <br/>
                <b>Titel:</b>
                <br/>
                <input name="title" placeholder="<?php echo __("Mail title"); ?>" class="mailtitle"
                       id="mailtitle_<?php echo $audience; ?>" style="width:600px" type="text"
                       value="<?php echo __("Mail from"); ?> <?php echo date("d.m.Y H:i:s"); ?>"/>
                <br/>
                <br/>
                <b>Text:</b>
                <textarea name="text" class='mailtext_tinymce' placeholder="<?php echo __("Mail text"); ?>"
                          id="mailtext_tinymce_<?php echo $audience; ?>" style="width:100%;height:200px;">


            <?php echo "

";
            $signature= "";
            if(file_exists(APP_NEWSLETTER_PATH . "signature.html"))
            $signature = APP_NEWSLETTER_PATH . "signature.html";
            echo bpfw_htmlentities(file_get_contents($signature));

            ?>


        </textarea>
                <br/>
            </div>
            <div class="block_600px">
                <b><?php echo __("Attachment"); ?></b>
                <br/>
                <input accept=".xls,.pdf,.png,.jpg,.doc,.docx,.xlsx" style="display: inline-block;" type="file"
                       name="attachment" size="40"
                       class="normal_admin_form_element admin_form_element bpfw_fileinput bpfw_fileinput_extended"
                       aria-required="false" aria-invalid="false"/>


                <br/>

                <button data-audience="<?php echo $audience; ?>" class="test_mail audience-<?php echo $audience; ?>">
                    <?php echo __("Send mail and save in outbox"); ?>
                </button>
            </div>
        </form>


        <?php

    }

}

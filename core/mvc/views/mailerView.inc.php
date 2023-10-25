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


class MailerView extends DefaultView
{

    /**
     * Summary of renderDefaultListView
     * @return void
     * @throws Exception
     */
    function renderView(): void
    {

        $tables = bpfw_getDb()->getAllTables();
        $hasCustomer = in_array("customer", $tables);

        /*if(file_exists(APP_MVC_PATH."models/userModel.inc.php")){
            require_once(APP_MVC_PATH."models/userModel.inc.php");
        }else{
            require_once(BPFW_MVC_PATH."models/userModel.inc.php");
        }

        require_once(APP_MVC_PATH."models/customerModel.inc.php");*/

        global $database;

        ?>

        <h1><?php echo __("Send massmails"); ?></h1>

        <br/>
        <br/>

        <style>

            #editmodeTabNavigation, .tab-content {
                padding-left:30px;
            }

        </style>


        <script>


        </script>

        <?php

        $targetAudience = array("user" => __("User"));
        if($hasCustomer){
            $targetAudience["customer"] = __("Customer");
        }
        $active = "user";

        $page = 1;

        ?>

        <ul class="nav nav-tabs dialogTabNavigation" id="editmodeTabNavigation" role="tablist">

            <?php foreach ($targetAudience as $audience => $audienceLabel) { ?>


                <li class="nav-item tab-<?php echo $audience; ?>">
                    <a data-page="<?php echo $page; ?>"
                       class="nav-link <?php echo $audience == $active ? "active" : ""; ?>"
                       id="tab-mailer-<?php echo $audience; ?>" data-toggle="tab"
                       href="#tab-mailer-<?php echo $audience; ?>-content" role="tab">
                        <?php echo $audienceLabel; ?>
                    </a>
                </li>


                <?php

                $page++;

            } ?>

        </ul>


        <div class="tab-content">

            <?php foreach ($targetAudience as $audience => $audienceLabel) {

                $values = array();

                switch ($audience) {

                    case "user":
                        $usermodel = bpfw_createModelByName("user");
                        $values = $usermodel->dbSelectAllAndCreateObjectArray(" email != ''");
                        break;

                    case "customer":
                        if($hasCustomer) {
                            $customermodel = bpfw_createModelByName("customer");
                            $values = $customermodel->dbSelectAllAndCreateObjectArray(" email != ''");
                        }
                        break;

                }

                ?>

                <div class="tab-pane fade <?php echo $audience == $active ? "active show" : ""; ?> tabmailer-content"
                     id="tab-mailer-<?php echo $audience; ?>-content" role="tabpanel">
                    <br/>
                    <br/>

                    <input class="testmail_rcpt" type="text"
                           value="<?php echo bpfw_loadSetting(SETTING_EMAIL_INTERN); ?>"/>
                    <button data-audience="<?php echo $audience; ?>"
                            class="test_mail audience-<?php echo $audience; ?>"><?php echo __("Save mail in outbox"); ?>
                    </button>
                    <br/>


                    <br/>
                    <b><?php echo __("Subject"); ?>:</b>
                    <br/>
                    <input placeholder="<?php echo __("Subject"); ?>" class="mailtitle" id="mailtitle_<?php echo $audience; ?>"
                           style="width:600px" type="text" value="Newsletter vom <?php echo date("d.m.Y"); ?>">
                    <br/>
                    <br/>
                    <b><?php echo __("Text"); ?>:</b>
                    <textarea class='mailtext_tinymce' placeholder="<?php echo __("Text"); ?>"
                              id="mailtext_tinymce_<?php echo $audience; ?>" style="width:100%;height:200px;">

    <?php echo "

{{{newsletter_Salutation}}},
<br /><br />
(".__("Write massmail text here").")
<br /><br />
";

    $signature= "";
    if(file_exists(APP_NEWSLETTER_PATH . "signature.html"))
        $signature = APP_NEWSLETTER_PATH . "signature.html";
    echo bpfw_htmlentities(file_get_contents($signature));


    ?>


</textarea>

                    <br/>

                    <button data-audience="<?php echo $audience; ?>"
                            class="send_mail audience-<?php echo $audience; ?>"><?php echo __("Save mass mail for sending in outbox"); ?>
                    </button>
                    <br/>
                    <br/>

                    <h5><?php echo __("Recipient"); ?>:</h5>
                    <button data-audience="<?php echo $audience; ?>"
                            class="activate_all_rcpt audience-<?php echo $audience; ?>"><?php echo __("activate all"); ?>
                    </button>
                    <button data-audience="<?php echo $audience; ?>"
                            class="deactivate_all_rcpt audience-<?php echo $audience; ?>"><?php echo __("deactivate all"); ?>
                    </button>
                    <br/><br/>
                    <div class="todolist-wrap">

                        <table id="<?php echo $audience; ?>listtable" style="border: 1px solid black;"
                               class="display <?php echo $audience; ?>listtable mailerlisttable">

                            <thead>
                            <tr style="background-color:#ece7f0">

                                <th><?php __("Name"); ?></th>
                                <th><?php __("Mail"); ?></th>
                                <th><?php __("Send Massmail"); ?></th>

                            </tr>
                            </thead>

                            <tbody>

                            <?php foreach ($values as $key => $value) { ?>

                                <tr>


                                    <td>
                                        <?php echo $value->lastname;
                                        echo ", ";
                                        echo $value->firstname; ?>
                                    </td>

                                    <td>
                                        <?php echo $value->email; ?>
                                    </td>

                                    <td>

                                        <?php if ($value->newsletter_mail) { ?>

                                            <div alt="" title="" class="newsletter_receiver disabled switchable"
                                                 data-userid="<?php echo $key; ?>">
                                                <div>
                                                    <div style="display:inline-block">
                                                        <i class="graphicalcomboxbox iconswitcher checkOff fa fa-times"></i>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } else {
                                            ?>
                                            <div alt="" title="" class="newsletter_receiver disabled nonswitchable"
                                                 data-userid="<?php echo $key; ?>">
                                                <div>
                                                    <div style="display:inline-block"
                                                         onclick="alert('<?php echo 'User deactivated newsletter'; ?>'); return false;">
                                                        <i class="graphicalcomboxbox iconswitcher checkOff fa fa-times"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                        }
                                        ?>

                                    </td>

                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>

                    </div>


                </div>


            <?php } ?>


        </div>


        <br/>

        <?php

    }

}

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
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */


require_once(BPFW_MVC_PATH . "views/defaultlistmodalView.inc.php");

class maileroutboxView extends DefaultListmodalView
{
    /**
     * Summary of $statusFilter
     * @var array
     */
    var array $statusFilter = array();

    /**
     * @var bool
     */
    var bool $makeArrow;

    /**
     * @var bool
     */
    var bool $makeDetail;

    function doBeforeForm()
    {

    if (empty(getorpost("box"))){
        // return "0";
    }else{

        ?>

<style>
    #adminTable_ajax_filter{
        top:-97px;
    }
</style>
    <br/><br/>

        <div id="filterTable" class="display" style="padding-left:18px;">

            <button class="mailboxnav <?php echo((getorpost("box") == "outbox") ? "active_nav_mail" : ""); ?>"
                    onclick="window.open('/?p=maileroutbox&box=outbox','_self')"><?php echo __("Outbox"); ?>
            </button>
            <button class="mailboxnav <?php echo((getorpost("box") == "sent") ? "active_nav_mail" : ""); ?>"
                    onclick="window.open('/?p=maileroutbox&box=sent','_self')"><?php echo __("Sent"); ?>
            </button>
            <button class="mailboxnav <?php echo((getorpost("box") == "deleted") ? "active_nav_mail" : ""); ?>"
                    onclick="window.open('/?p=maileroutbox&box=deleted','_self')"><?php echo __("Deleted"); ?>
            </button>

        </div>

    <br/>

        <div class="buttonbar buttonbar-top">
            <a data-hierachy="0" class="button addButton root_button"
               onclick='startIFrameDialog("outbox", "?p=mail&amp;type=single&amp;hideNavigation=true" );'>

                <div style="width:250px" data-hierachy="0" class="new-entry-button  root_button">
                    <i class="topbuttonicon fa fa-envelope"></i>
                    <div><?php echo __("New mail"); ?></div>
                </div>

            </a>


            <a data-hierachy="0" target="_blank" class="button printButton root_button"
               onclick='startIFrameDialog("outbox", "?p=mailer&amp;type=mass&amp;hideNavigation=true" );'>

                <div data-hierachy="0" class="new-entry-button  root_button">
                    <i class="topbuttonicon fas fa-mail-bulk"></i>
                    <div>Neuer Serienbrief</div>

                </div>

            </a>
        </div>

        <div id="filterTable" class="display" style="padding-left:18px;">


            <?php

            switch (getorpost("box")) {

                case "outbox":
                    ?>

                    <button class="mailsend_1 mailaction"><?php echo __("Send 1 mail"); ?></button>
                    <button class="mailsend_10 mailaction"><?php echo __("Send 10 mails"); ?></button>
                    <button class="mailsend_50 mailaction"><?php echo __("Send 50 mails"); ?></button>

                    <button class="deleteAllMailsFailed mailaction"><?php echo __("Delete failed mails"); ?></button>

                    <?php
                    break;
                case "sent";
                    ?>

                    <button class="deleteAllMailsSent mailaction"><?php echo __("Delete sent mails"); ?></button>

                    <?php
                    break;
                case "deleted":
                    ?>


                    <button class="deleteAllMailsTrash mailaction"><?php echo __("Truncate deleted"); ?></button>

                    <?php
                    break;
            }
            }


            ?>
        </div>

        <style>

            .mailboxnav {

                background-color: white;

            }

            .mailboxnav.active_nav_mail {

                background-color: grey;
                color: white;
            }

            .mailaction {

                background-color: lightgray;
            }

        </style>


        <script>

            jQuery(document).ready(function (e) {


                function sendMails(amount) {


                    var ajaxurl = "?p=ajax";

                    jQuery.ajax(
                        {
                            type: 'POST',
                            cache: false,
                            url: ajaxurl,
                            data:
                                {
                                    p: "ajax",
                                    ajaxCall: 1,
                                    command: "sendMails",
                                    amount: amount
                                },
                            async: true
                        }
                    ).done(
                        function (data) {
                            alert("Mailversand status:" + data);
                            bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
                        }
                    );


                }

                function clearMailbox(deltype) {
                    var ajaxurl = "?p=ajax";

                    jQuery.ajax(
                        {
                            type: 'POST',
                            cache: false,
                            url: ajaxurl,
                            data:
                                {
                                    p: "ajax",
                                    ajaxCall: 1,
                                    command: "clearMailbox",
                                    deltype: deltype
                                },
                            async: true
                        }
                    ).done(
                        function (data) {
                            alert(data);
                            bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
                        }
                    );

                }

                /* function deleteSentMails() {

                     var ajaxurl = "?p=ajax";

                     jQuery.ajax(
                         {
                             type: 'POST',
                             cache: false,
                             url: ajaxurl,
                             data:
                             {
                                 p: "ajax",
                                 ajaxCall: 1,
                                 command: "deleteSentMails"
                             },
                             async: true
                         }
                     ).done(
                         function (data) {
                             alert(data);
                             bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
                         }
                     );

                 }*/


                jQuery(document).on("click", ".deleteAllMailsSent", function (btn) {

                        if (confirm("Alle gesendeten Mails löschen?")) {
                            clearMailbox("sent");

                        }

                    }
                );


                jQuery(document).on("click", ".deleteAllMailsFailed", function (btn) {

                        if (confirm("Alle fehlgeschlagenen Mails aus dem Posteingang löschen?")) {
                            clearMailbox("failed");

                        }

                    }
                );


                jQuery(document).on("click", ".deleteAllMailsTrash", function (btn) {

                        if (confirm("Inhalte im Papierkorb endgültig löschen?")) {
                            clearMailbox("trash");

                        }

                    }
                );


                /*  jQuery(document).on("click", ".deleteSentMails", function (btn) {

    if (confirm("wirklich alle bereits gesendeten EMails im Postausgang löschen?")) {
                    deleteSentMails();
    }

}
);*/

                jQuery(document).on("click", ".mailsend_1", function (btn) {


                    sendMails(1);

                });

                jQuery(document).on("click", ".mailsend_10", function (btn) {

                    sendMails(10);

                });

                jQuery(document).on("click", ".mailsend_50", function (btn) {

                    sendMails(50);

                });

            });


        </script>
        <?php
    }

    /*
    function statusFilter($value){

        if(!isset($this->statusFilter[$value->status])){
            return false;
        }

        return true;
    }*/


    /*
    function extraButtons($key, $value){

        if ($this->makeArrow && $value->status != 0){
            if($value->status < 2 || bpfw_isAdmin())
            {

             echo '
        <a class="tableicon_size open_eventappointment" data-id='.$key.' title="Zur Anwesenheitsliste" href="?p=eventappointment&filter=' . $key . '">
            <i class="tableicon fa fa-list-ul"></i>
        </a>
        ';
            }
        }

        if($this->makeDetail && !empty($value->customerId) )
        {
            echo '
            <a class="tableicon_size open_eventmanager iframepopup" data-id='.$key.' title="Zur Verwaltung" href="?p=eventmanager&hideNavigation=true&filter=' . $key . '">
                <i class="tableicon fa fa-envelope"></i>
            </a>
            ';
        }

        echo '
        <a class="tableicon_size open_eventattachments iframepopup" data-id='.$key.' title="Anhänge" href="?p=eventtimeline&hideNavigation=true&filter=' . $key . '">
            <i class="tableicon fa fa-paperclip"></i>
        </a>
        ';

        
        //

       

        if(bpfw_isAdmin() && isset($_GET["zoho_enabled"]))
        {


            
            $zoholink = "/#";

            echo '
            <a class="tableicon_size open_eventattachments zoholink" data-id='.$key.' title="Zoho Link öffnen"  data-id='.$key.' href="'.$zoholink.'" target="_blank" >
                <i class="tableicon fa" style="background-color:green;color:white;padding-left:3px;padding-right:3px;font-family:inherit;font-weight:600;">Z</i>
            </a>
            ';


            echo '
            <a class="tableicon_size open_eventattachments zohomail" data-id='.$key.' title="Zoho" href="?p=event&zoho='.$key.'" >
                <i class="tableicon fa" style="background-color:red;color:white;padding-left:3px;padding-right:3px;font-family:inherit;font-weight:600;">Z</i>
            </a>
            ';




        }





    }



    function actionbeforecloseicon($rowid){

        if(bpfw_isAdmin())
        {

?>

            <span class="headerdialogbuttonrow datalist_button_wrapper list-enabled">

                <button type="button" class="tableicon_size open_eventmanager"     onclick="startIFrameDialog(<?php echo $rowid; ?>, '?p=eventmanager&amp;hideNavigation=true&amp;filter=<?php echo $rowid; ?>');" data-id="<?php echo $rowid; ?>">
                    <i class="tableicon fa fa-envelope"></i>
                </button>

                <button type="button" class="tableicon_size open_eventattachments" onclick="startIFrameDialog(<?php echo $rowid; ?>, '?p=eventtimeline&amp;hideNavigation=true&amp;filter=<?php echo $rowid; ?>');" data-id="<?php echo $rowid; ?>">
                    <i class="tableicon fa fa-paperclip"></i>
                </button>

            </span>

            <?php

        }

    }
    */

    function initializeVariables()
    {

        $this->makeArrow = !$this->print;
        $this->makeDetail = !$this->print && bpfw_isAdmin();

        $this->hasAnyButtons = $this->makeDetail || $this->makeEdit || $this->makeTrash || $this->makeDuplicate || $this->makeArrow || $this->makeSendPassword;

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "doBeforeForm"));

        /*
        bpfw_add_check(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_ENTRY, $this->model->GetTableName(), array($this, "statusFilter"), 10, 1 );

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2 );

        bpfw_add_action(DefaultlistView::ACTION_EDITDIALOG_BEFORE_HEADER_CLOSE_ICON, $this->model->GetTableName(), array($this, "actionbeforecloseicon"), 10, 1);
         */

        parent::initializeVariables();

    }


    function getAddTitle(): string
    {

        return "Neue Maßnahme erstellen";

    }

    /**
     * @throws Exception
     */
    function getEditTitle(int $id): string
    {
        return "Mail von " . $this->getIdentifierForDialogTitle($id) . " editieren";
    }

    /**
     * @throws Exception
     */
    function getIdentifierForDialogTitle($id): string
    {

        $db = bpfw_getDb();

        if (empty($this->entry)) {
            return $id;
        }

        if (empty($this->entry->to)) return "???";

        $userdata = json_decode($this->entry->to);

        if (empty($userdata->name)) {
            return $userdata->address;
        } else {
            return $userdata->name;
        }

        /*
        $entry = $db->makeSelectSingleOrNull("customer", "customerId", $custid);

        if(empty($entry))return "(nicht gesetzt)";

        return $entry["lastname"] . ", " . $entry["firstname"] ;*/

    }

    /**
     * @throws Exception
     */
    function getDuplicateTitle(int $id): string
    {
        return "Mail von " . $this->getIdentifierForDialogTitle($id) . " duplizieren";
    }

    /**
     * @throws Exception
     */
    function getDeleteTitle(int $id): string
    {
        $this->entry = $this->model->GetEntry($_GET['id']);
        return "Mail von " . $this->getIdentifierForDialogTitle($id) . " wirklich löschen";
    }

}

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

class mailerinboxView extends DefaultListmodalView
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

        ?>

        <div id="filterTable" class="display">

            <button class="mailreceive">Mails abholen</button>

        </div>


        <script>

            jQuery(document).ready(function (e) {


                function receiveMails() {


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
                                    command: "receiveMails"
                                },
                            async: true
                        }
                    ).done(
                        function (data) {
                            alert("Mailempfang status:" + data);
                            bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
                        }
                    );


                }

                jQuery(document).on("click", ".mailreceive", function (btn) {

                    receiveMails();

                });

            });


        </script>
        <?php
    }

    function initializeVariables()
    {

        $this->makeArrow = !$this->print;
        $this->makeDetail = !$this->print && bpfw_isAdmin();

        $this->hasAnyButtons = $this->makeDetail || $this->makeEdit || $this->makeTrash || $this->makeDuplicate || $this->makeArrow || $this->makeSendPassword;

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "doBeforeForm"));


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
        return "Maßnahme von " . $this->getIdentifierForDialogTitle($id) . " editieren";
    }

    /**
     * @throws Exception
     */
    function getIdentifierForDialogTitle($id): string
    {

        $db = bpfw_getDb();

        $custid = $this->entry->customerId;
        $entry = $db->makeSelectSingleOrNull("customer", "customerId", $custid);

        return $entry["lastname"] . ", " . $entry["firstname"];

    }

    /**
     * @throws Exception
     */
    function getDuplicateTitle(int $id): string
    {
        return "Maßnahme von " . $this->getIdentifierForDialogTitle($id) . " duplizieren";
    }

    /**
     * @throws Exception
     */
    function getDeleteTitle(int $id): string
    {
        $this->entry = $this->model->GetEntry($_GET['id']);
        return "Maßnahme von " . $this->getIdentifierForDialogTitle($id) . " wirklich löschen";
    }

}

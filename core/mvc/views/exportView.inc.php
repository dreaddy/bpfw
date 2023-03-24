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

require_once(BPFW_MVC_PATH . "views/defaultlistView.inc.php");

class exportView extends DefaultListView
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


    function extraButtons($key = "all", $value = "?")
    {


        if ($key == "all") {
            echo __("Export all").":";
        }


        echo '
        <a target="_blank" title="export as JSON" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=json">
            <i class="tableicon fa fa-arrow-up">'.__("JSON").'</i></a>';


        echo '
        <a target="_blank" title="export as CSV" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=csv">
            <i class="tableicon fa fa-arrow-up">'.__("CSV(plain)").'</i>
        </a>
        ';

        echo '
        <a target="_blank" title="export as CSV" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=csv2">
            <i class="tableicon fa fa-arrow-up">'.__("CSV(formatted)").'</i>
        </a>
        ';


        echo '
        <a target="_blank" title="export as XLS" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=xls">
            <i class="tableicon fa fa-arrow-up">'.__("EXCEL").'</i>
        </a>
        ';

        // TODO: move to specific app
        if ($key == "event") {

            echo '
            <a target="_blank" title="export as XLS" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=zohocsveventexport">
                <i class="tableicon fa fa-arrow-up">ZOHO CSV</i>
            </a>
            ';
        }


        if ($key == "customer") {
            echo '
            <a target="_blank" title="export as XLS" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=customerdata">
                <i class="tableicon fa fa-arrow-up">Kundendaten</i>
            </a>
            ';
        }

        if ($key == "user") {
            echo '
            <a target="_blank" title="export as XLS" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=userdata">
                <i class="tableicon fa fa-arrow-up">Beraterdaten</i>
            </a>
            ';
        }

        if ($key == "eventtemplatenumber") {
            echo '
            <a target="_blank" title="export as XLS" class="tableicon_size" href="?p=export&model=' . $key . '&ajaxCall=true&command=eventtemplatenumberdata">
                <i class="tableicon fa fa-arrow-up">Veranstaltungsmaßnahmen</i>
            </a>
            ';
        }

    }

    /**
     * Summary of renderView
     * @throws Exception
     */
    function initializeVariables()
    {
        $this->hasAnyButtons = true;
        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "extraButtons"));
        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2);
        parent::initializeVariables();

    }

}
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

class backupView extends DefaultListView
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
        <a target="_blank" title="export as JSON" class="tableicon_size" href="?p=backup&id=' . $key . '&ajaxCall=true&command=downloadDbBackup">
            <i class="tableicon fa fa-arrow-down">'.__("Download DB").'</i>
        </a>
        ';

        echo '
        <a target="_blank" title="direct import" class="tableicon_size restore_backup" href="?p=import&action=import1&restore_backup_id=' . $key . '" data-id=' . $key . ' href="">
            <i class="tableicon fa fa-arrow-up">'.__("Restore DB").'</i>
        </a>
        ';

        if ($value->backuptype == 0) {
            echo '
            <a target="_blank" title="export as CSV" class="tableicon_size" href="?p=backup&id=' . $key . '&ajaxCall=true&command=downloadFileBackup">
                <i class="tableicon fa fa-arrow-down">'.__("Download uploads").'</i>
            </a>
            ';
        }

    }


    function createBackupBar($key = "all", $value = "?")
    {


        echo '
        <a target="_blank" title="export as JSON" class="tableicon_size" href="?p=backup&ajaxCall=true&command=createBackupFull">
            <i class="tableicon fa fa-hdd">'.__("Create new backup").'</i>
        </a>
        ';

        echo '
        <a target="_blank" title="export as JSON" class="tableicon_size" href="?p=backup&ajaxCall=true&command=createBackupPartial">
            <i class="tableicon fa fa-hdd">'.__("New partial backup (Database only)").'</i>
        </a>
        ';

    }


    /**
     * Summary of renderView
     * @throws Exception
     */
    function initializeVariables()
    {
        $this->hasAnyButtons = true;

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "createBackupBar"));

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2);

        parent::initializeVariables();

    }


}
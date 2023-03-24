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
 * Summary of renderView
 * @param BpfwModel $model
 * @return void
 */
class dbsyncView extends DefaultView
{

    function renderView(): void
    {

        foreach ($this->model->control->commandsExecuted as $message) {
            echo "<p class='commandsExecutedInfo'>" . $message . "</p>";
        }

        ?>

        <h3><?php echo __("Sync database <-> models"); ?></h3>
        <p><?php echo __("This page is used to sync your Model.inc.php files with the database"); ?></p>
        <br/>
        <h4 style="color:red"><?php echo __("CREATING A BACKUP BEFORE EXECUTING CHANGES IS HIGHLY RECOMMENDED!"); ?></h4>
        <p>
            <a href="?p=export&model=all&ajaxCall=true&command=json" target="_blank"><?php echo __("Click here to download a backup"); ?></a>
        </p>
        <br/>

        <p>
            <a href="" class="syncAll" target="_blank"><?php echo __("Execute all sync processes"); ?></a>
        <div class="syncAllStatus"></div>
        </p>

        <br/>
        <?php

        $this->model->listSearchindexTables();

    }

}

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

class cronjobView extends DefaultListmodalView
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


    function renderView() : void{
        ?>
        <div style='height:120px; padding-left:30px;'>
            <?php echo __("Usually cronjob execution is done, when a page is loaded and the frequency check says so."); ?><br>
            <?php echo __("To execute with an external Cronjob, call this url"); ?>:
        <?php
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
        echo $protocol."://".$_SERVER['SERVER_NAME'].BASE_URI."?p=ajax&ajaxCall=1&command=handleCronjobs&sigauth=".bpfw_loadSetting(SETTING_EXTERNAL_CRONJOB_CALL_CODE); ?>
        </div>
<?php
        echo "";

         parent::renderView();
    }

    /**
     * @throws Exception
     */
    function extraButtons(string $key, mixed $value)
    {

        echo '
        <a class="tableicon_size open_eventattachments iframepopup" data-id=' . $key . ' title="' . __("Execute now") . '" href="?p=ajax&ajaxCall=1&command=handleCronjobs&sigauth='.bpfw_loadSetting(SETTING_EXTERNAL_CRONJOB_CALL_CODE).'&executeDirectly=' . $key . '">
            <i class="tableicon fa fa-cog">' . __("Execute now") . '</i>
        </a>
        ';

    }


    function initializeVariables()
    {

        $this->makeArrow = !$this->print;
        $this->makeDetail = !$this->print && bpfw_isAdmin();

        $this->hasAnyButtons = $this->makeDetail || $this->makeEdit || $this->makeTrash || $this->makeDuplicate || $this->makeArrow || $this->makeSendPassword;

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2);


        parent::initializeVariables();

    }


    function getAddTitle(): string
    {
        return __("Cronjobs");
    }

}

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

class ExamplecomplexView extends DefaultListmodalView
{

    function doBeforeForm()
    {

        ?>

        <div id="customcontent" class="display">

        (add Text, filter or custom functionality here)

        </div>

        <?php

    }


    function extraButtons($key, $value)
    {

        // custom entries to row menu.

        if (bpfw_isAdmin()) {
            // open as popup window
            echo '
            <a class="tableicon_size open_eventmanager iframepopup" data-id=' . $key . ' title="pdf generation and sending" href="?p=examplepdfmanager&amp;modelused=examplecomplex&amp;hideNavigation=true&amp;filter=' . $key . '">
                <i class="tableicon fas fa-file-pdf">Create .pdf</i>
            </a>
            ';

            echo '
            <a class="tableicon_size open_attachments iframepopup" data-id=' . $key . ' title="Attachments" href="?p=exampleattachments&amp;hideNavigation=true&filter=' . $key . '">
                <i class="tableicon fa fa-paperclip">Attachments</i>
            </a>
            ';

        }



    }

    /**
     * Icons that appear on the top left of the add/edit/duplicate dialog
     *
     * @param $rowId
     * @return void
     */
    function actionBeforeCloseIcon($rowId): void
    {

        if (bpfw_isAdmin()) {

            ?>

            <span class="headerdialogbuttonrow datalist_button_wrapper list-enabled">

                <button type="button" class="tableicon_size open_eventmanager"
                        onclick="startIFrameDialog(<?php echo $rowId; ?>, '?p=examplepdfmanager&amp;modelused=examplecomplex&amp;hideNavigation=true&amp;filter=<?php echo $rowId; ?>');"
                        data-id="<?php echo $rowId; ?>">
                    <i class="tableicon fas fa-file-pdf"></i>
                </button>

                   <button type="button" class="tableicon_size open_attachments"
                           onclick="startIFrameDialog(<?php echo $rowId; ?>, '?p=exampleattachments&amp;hideNavigation=true&amp;filter=<?php echo $rowId; ?>');"
                           data-id="<?php echo $rowId; ?>">
                    <i class="tableicon fa fa-paperclip"></i>
                </button>

            </span>

            <?php

        }

    }


    function initializeVariables()
    {

        parent::initializeVariables();

        $this->makeArrow = !$this->print;
        $this->makeDetail = !$this->print && bpfw_isAdmin();

        $this->hasAnyButtons = $this->makeDetail || $this->makeEdit || $this->makeTrash || $this->makeDuplicate || $this->makeArrow || $this->makeSendPassword;

        // add html on top of form
        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "doBeforeForm"));

        // add buttons to list
        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2);

        // before close button, add pdfmailer icon here
        bpfw_add_action(DefaultlistView::ACTION_EDITDIALOG_BEFORE_HEADER_CLOSE_ICON, $this->model->GetTableName(), array($this, "actionBeforeCloseIcon"), 10, 1);


    }

    // customize titles for dialogues
    function getAddTitle(): string
    {
        return "Create new entry custom title";
    }

    function getEditTitle(int $id): string
    {
        return "Edit entry custom title for id $id";
    }

    function getDuplicateTitle(int $id): string
    {
        return "Duplicate title for $id";
    }

    function getDeleteTitle(int $id): string
    {
        $this->entry = $this->model->GetEntry($_GET['id']);
        return "Delete entry $id?";
    }

}

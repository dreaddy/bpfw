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

class resetView extends DefaultListView
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


        ?>

        <script>


            jQuery("body").on("click", ".deactivate_all_tables",

                function () {

                    var audience = jQuery(this).data("audience");

                    var selector = "#adminTable_wrapper";
                    var elementselector = "i.iconswitcher";

                    jQuery(selector).find(elementselector).addClass("checkOff");
                    jQuery(selector).find(elementselector).removeClass("checkOn");

                    jQuery(selector).find(elementselector).removeClass("fa-check");
                    jQuery(selector).find(elementselector).addClass("fa-times");

                    // jQuery(selector).find(".newsletter_receiver.switchable").removeClass("enabled");
                    // jQuery(selector).find(".newsletter_receiver.switchable").addClass("disabled");

                }
            );

            jQuery("body").on("click", ".activate_all_tables",

                function () {

                    var selector = "#adminTable_wrapper";
                    var elementselector = "i.iconswitcher";

                    jQuery(selector).find(elementselector).removeClass("checkOff");
                    jQuery(selector).find(elementselector).addClass("checkOn");

                    jQuery(selector).find(elementselector).addClass("fa-check");
                    jQuery(selector).find(elementselector).removeClass("fa-times");

                    // jQuery(selector).find(".newsletter_receiver.switchable").addClass("enabled");
                    // jQuery(selector).find(".newsletter_receiver.switchable").removeClass("disabled");
                }
            );

        </script>


        <?php


        echo '


         <button class="activate_all_tables">'.__("activate all").'</button>
         <button class="deactivate_all_tables">'.__("deactivate all").'</button><br /><br />

        <a id="resetModels" title="reset selected Tables" class="tableicon_size" style="cursor:pointer" >
            <i class="tableicon fa fa-warning"> '.__("DELETE ALL SELECTED TABLES IN DATABASE").'</i><i class="tableicon fa fa-warning"></i>
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

        bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_BEFORE_RENDER_TABLE, $this->model->GetTableName(), array($this, "extraButtons"));

        // bpfw_add_action(DefaultlistView::ACTION_DEFAULTLISTVIEW_DISPLAY_BUTTONS, $this->model->GetTableName(), array($this, "extraButtons"), 10, 2 );

        parent::initializeVariables();

    }


}
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

global $model;
?>

<div class="navigationWrapper" style="">
    <div style="" class="fa fa-bars mobileHamburger"></div>

    <ul id="nav">


        <?php

        $usertype = bpfw_getCurrentUsertype();

        if ($usertype <= USERTYPE_ADMIN) {

            make_navigation_header("Data");

            bpfw_nav_make_link ( "Simple example", "examplesimple", $model->getSlug(), "fa fa-chart-pie" );
            bpfw_nav_make_link ( "Complex Example", "examplecomplex", $model->getSlug(), "fa fa-chart-bar" );
            bpfw_nav_make_link ( "Custom Content Example", "examplecustomcontent", $model->getSlug(), "fa fa-chart-area" );

            make_navigation_header(__("Settings"));

            $onclick_settings = "";
            try {
                $appsettings = bpfw_createModelByName(SETTINGS_TABLENAME);

                $appsettings->initializeSettings();

                try {
                    $settingsid = bpfw_getDb()->makeSelectSingleOrNullBySql("select settingId from `" . SETTINGS_TABLENAME . "` where 1 limit 1");

                    if ($settingsid != null) {
                        $onclick_settings = "return startEdit('" . $settingsid["settingId"] . "', 'appsettings', true, false);";
                    }

                    bpfw_nav_make_link("Settings", "appsettings", $model->getSlug(), "fa fa-cog", array(), $onclick_settings);

                } catch (Exception $e) {
                    if(BPFW_DEBUG_MODE)
                    echo "Exception loading settings: " . $e->getMessage() . " - " . $e->getTraceAsString();

                }

            } catch (Exception $e) {
                if(BPFW_DEBUG_MODE)
                echo "Exception: " . $e->getMessage() . " " . $e->getTraceAsString();
                //die();
            }

            bpfw_nav_make_link("User", "user", $model->getSlug(), "fa fa-user");

            make_navigation_header(__("Mails"), "mails", "fa fa-envelope");

            $adminElements = bpfw_getAllNavigationMailElements();

            foreach($adminElements as $adminElement){
                bpfw_nav_make_link ( __($adminElement->caption), $adminElement->id, $model->getSlug(), $adminElement->icon);
            }


            make_navigation_header("Admin");

            $adminElements = bpfw_getAllNavigationAdminElements();

            foreach($adminElements as $adminElement){
                bpfw_nav_make_link ( __($adminElement->caption), $adminElement->id, $model->getSlug(), $adminElement->icon);
            }

        }

        make_navigation_header(__("Logged in as").": " . bpfw_getUserName());
        bpfw_nav_make_link_with_url("Logout", "?logout", false, "fa fa-sign-out-alt");

        ?>

    </ul>

</div>
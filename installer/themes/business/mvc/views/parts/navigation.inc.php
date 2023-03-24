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

require_once("navigationfunctions.inc.php");


?>

<div class="navigationWrapper" style="">




    <ul id="nav">
        <li>
            <img class="navlogo" style="" src="<?php echo PARENT_IMGS_URI; ?>headerbar.png" />
    </li>

        <?php

        $usertype = bpfw_getCurrentUsertype();

        //make_minimize_button_wws (  );
        //make_maximize_button_wws (  );

        if ($usertype <= USERTYPE_ADMIN && $usertype >=0) {


            make_navigation_header_theme("Example", "test", "fa fa-chart-bar");
            make_link_theme ( "Simple example", "examplesimple", $model->getSlug(), "fa fa-chart-pie" );
            make_link_theme ( "Complex Example", "examplecomplex", $model->getSlug(), "fa fa-chart-bar" );
            make_link_theme ( "Custom Content Example", "examplecustomcontent", $model->getSlug(), "fa fa-chart-bar" );
            make_navigation_header_theme_end("test");
            //make_link_wws ( "offene Bestellungen", "openorder", $model->getSlug(), "fa fa-shipping-fast" );

            make_navigation_header_theme(__("Settings"), "settings", "fa fa-cogs");

            $onclick_settings = "";

            try{
                $appsettings = bpfw_createModelByName(SETTINGS_TABLENAME);
                $appsettings->initializeSettings();
                $settingsid = bpfw_getDb()->makeSelectSingleOrNullBySql("select settingId from `".SETTINGS_TABLENAME."` where 1 limit 1");

                if($settingsid != null){
                    $onclick_settings = "return startEdit('".$settingsid["settingId"]."', 'appsettings', true, false);";
                }
            }catch(Exception $e){
                echo "Exception ".$e->getMessage()." - ".$e->getTraceAsString();
                die();
            }

            make_link_theme ( __("Settings"), "appsettings", $model->getSlug(), "fa fa-cogs", $onclick_settings );
            make_link_theme ( __("User"), "user", $model->getSlug(), "fa fa-user" );
            make_navigation_header_theme_end(__("Settings"));


            make_navigation_header_theme(__("Mails"), "mails", "fa fa-envelope");

            $adminElements = bpfw_getAllNavigationMailElements();

            foreach($adminElements as $adminElement){
                make_link_theme ( __($adminElement->caption), $adminElement->id, $model->getSlug(), $adminElement->icon, "", $adminElement->params);
            }

            make_navigation_header_theme_end(__("Mails"));

            make_navigation_header_theme(__("Administrator"), "administrator", "fa fa-cog");

            $adminElements = bpfw_getAllNavigationAdminElements();

            foreach($adminElements as $adminElement){
                make_link_theme ( __($adminElement->caption), $adminElement->id, $model->getSlug(), $adminElement->icon, "", $adminElement->params);
            }

            make_navigation_header_theme_end(__("Administrator"));

		}

        make_navigation_header_theme(__("User") . ": " . bpfw_getUserName(), "user", "fa fa-user");
        make_link_with_url_theme("Logout", "?logout", false, "fa fa-sign-out-alt");
        make_navigation_header_theme_end(__("User") . ": " . bpfw_getUserName());
        ?>

    </ul>

</div>


<script>

        // alert("fertig");

        jQuery(".navelement").hide();

        var category = "<?php echo nav_getVisibleCategory(); ?>"; //jQuery(this).data("category");

        jQuery(".navelement[data-category='" + category + "']").show();


        jQuery(".navigationheader[data-category='" + category + "']").toggleClass("opened");

        jQuery(".navigationheader[data-category='" + category + "']").toggleClass("active");

</script>
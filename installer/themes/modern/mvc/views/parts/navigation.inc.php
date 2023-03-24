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
        <li class="navlogo_li">
            <img style="padding-bottom:20px;" class="navlogo" style="" src="<?php echo PARENT_IMGS_URI; ?>headerbar.png" />
        </li>






        <?php

        $usertype = bpfw_getCurrentUsertype();



        if ($usertype <= USERTYPE_ADMIN && $usertype >=0) {





            // make_navigation_header_wws("Basisdaten (nur Entwickler)", "settings", "fa fa-user");

            $onclick_settings = "";

            try{
                $appsettings = bpfw_createModelByName(SETTINGS_TABLENAME);
                $appsettings->initializeSettings();
                $settingsid = bpfw_getDb()->makeSelectSingleOrNullBySql("select settingId from `".SETTINGS_TABLENAME."` where 1 limit 1");

                if($settingsid != null){
                    $onclick_settings = "return startEdit('".$settingsid["settingId"]."', 'appsettings', true, false);";
                }
            }catch(Exception $ex){
                echo "Exception ".$ex->getMessage()." - ".$ex->getTraceAsString();
                die();
            }



            make_navigation_header_theme("Example", "test", "fa fa-chart-bar");
            make_link_theme ( "Simple example", "examplesimple", $model->getSlug(), "fa fa-chart-pie" );
            make_link_theme ( "Complex Example", "examplecomplex", $model->getSlug(), "fa fa-chart-bar" );
            make_link_theme ( "Custom Content Example", "examplecustomcontent", $model->getSlug(), "fa fa-chart-bar" );
            make_navigation_header_theme_end("test");

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


		if ($usertype == USERTYPE_CONSULTANT) {



		}



		if ($usertype == USERTYPE_CUSTOMER) {


            // bpfw_nav_make_link ( "Bürobuchung", "reservation", $model->getSlug(), "fas fa-home" );

		}


        make_navigation_header_theme("Logout", "logout", "fa-solid fa-right-from-bracket", "collection&logout");


        ?>

</ul>

</div>


<script>


        jQuery(".navelement").hide();

        var category = "<?php echo nav_getVisibleCategory(); ?>"; //jQuery(this).data("category");

        jQuery(".navelement[data-category='" + category + "']").show();


        jQuery(".navigationheader[data-category='" + category + "']").toggleClass("opened");

        jQuery(".navigationheader[data-category='" + category + "']").toggleClass("active");

        jQuery(".navcategorywrap[data-category='" + category + "']").toggleClass("opened");

        jQuery(".navcategorywrap[data-category='" + category + "']").toggleClass("active");

</script>
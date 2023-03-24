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



function replaceVar($var, $replace, $subject) : string{
    return str_replace("{{{".$var."}}}", $replace, $subject);
}

function createFormInput($label, $name, $required = true, $type = "text", $default = "", $options = array()): void
{

    $value = $default;
    if(!empty( $_POST[$name])){
        $value =  $_POST[$name];
    }else
    if(!empty( $_GET[$name])){
        $value =  $_GET[$name];
    }

    if($type == "select"){
        ?>
        <p>
            <label for="<?php echo $name; ?>"><?php echo $label; ?>

                <select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
                    <?php

                        foreach($options as $k=>$v){
                            echo "<option value='$k'>$v</option>";
                        }

                    ?>
                </select>

            </label>

        </p>
        <?php
    }else{

        $default = "value='".$value."'";

        if($type == "checkbox"){
            $default = empty($value)?"":"checked";
        }

    ?>
    <p>
        <label for="<?php echo $name; ?>"><?php echo $label; ?>
            <input id="<?php echo $name; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" <?php echo $default; ?> <?php if($required)echo "required"; ?>>
        </label>

    </p>
        <?php

    }
}

function runAppCreation(): void
{
    $appsFound = bpfw_scanForApps();

    $firstApp = empty($appsFound);

     // var_dump($_POST);

     if(!empty($_POST)){

        $success = true;

        echo "<p>checks running...</p>";

        if(!$firstApp){
            if($_POST["appPassword"] != MULTIPLE_APP_CREATION_PASSWORD){
                echo "<p>App Creation Password is wrong</p>";
                $success = false;
            }else{
                echo "<p>App Creation Password is correct</p>";
            }
        }

         if($success){
             if($_POST["adminPassword"] != $_POST["adminPassword2"]){
                 echo "<p>Admin Passwords do not match</p>";
                 $success = false;
             }else{
                 echo "<p>Admin Passwords match</p>";
             }
         }


         if($success) {

            if(file_exists(APP_BASE_PATH)){

                echo "<p>Folder already Existing: ".APP_BASE_PATH."</p>";

                $success = false;
            }else{
                echo "<p>Folder empty: ".APP_BASE_PATH.", continuing</p>";
            }

         }


         if($success){

             // require_once(BPFW_CORE_PATH . "bpfwconfig.inc.php");
             // require_once(BPFW_CORE_PATH . "includes.inc.php");

            try {
                $dbConnection = @new mysqli($_POST["dbHost"], $_POST["dbUser"], $_POST["dbPassword"], $_POST["dbName"]);

                if($dbConnection->connect_error != null){

                    echo "<p>DB Connect Error: ".$dbConnection->connect_error."</p>";
                    $success = false;
                }else{
                    echo "<p>DB connected</p>";

                    $db = bpfw_getDb();
                    $db->setMysqliConnection($dbConnection);
                    $tables = $db->getAllTables();

                    if(!empty($tables)){
                        echo "<p>DB Error: Database is not empty, clear, use another one or create app manually</p>";
                        $success = false;
                    }else{
                        echo "<p>DB is Empty, continue...</p>";


                    }

                    //echo "<p>DB connected, stats: ".print_r($db->(),true)."</p>";
                }


            }catch(Exception $ex){

                echo "<p>DB Connect Error: ".$ex->getMessage()."</p>";
                $success = false;


            }

        }

         if($success){

            echo "<p>Checks passed, starting creation....</p>";

            try {

                // create user tables etc.
                bpfw_setCreatingTables(true);
                require_once(BPFW_MVC_PATH . "bpfwDbFieldType.inc.php");
                require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");
                require_once(BPFW_MVC_PATH . "modelLoader.inc.php");
                require_once(BPFW_MVC_PATH . "controlHandler.inc.php");
                require_once(BPFW_MVC_PATH . "viewLoader.inc.php");
                //require_once(BPFW_BASE_PATH . "core" . DIRECTORY_SEPARATOR . "db" . DIRECTORY_SEPARATOR . "dbModel.inc.php");

                if(!file_exists(APPS_ROOT_PATH)) {
                    mkdir(APPS_ROOT_PATH);
                }

                mkdir(APP_BASE_PATH);

                $configName = APP_BASE_PATH.APP_NAME."config.inc.php";

                $bpfwInstallerPath = BPFW_BASE_PATH."installer".DIRECTORY_SEPARATOR;

                $configData = file_get_contents($bpfwInstallerPath."templateconfig.inc.php");

                $configData = replaceVar("APP_NAME", APP_NAME, $configData);
                $configData = replaceVar("APP_TITLE", htmlspecialchars($_POST["appName"]), $configData);
                $configData = replaceVar("DB_HOST", $_POST["dbHost"], $configData);
                $configData = replaceVar("DB_USER", $_POST["dbUser"], $configData);
                $configData = replaceVar("DB_PASSWORD", $_POST["dbPassword"], $configData);
                $configData = replaceVar("DB_DATABASE", $_POST["dbName"], $configData);
                $configData = replaceVar("STARTING_PAGE", "user", $configData);
                $configData = replaceVar("DEFAULT_LANGUAGE", $_POST["lang"], $configData);

                /*
"<?php

// config file for your app. All values in bpfw/core/bpfwconfig.inc.php can be overridden here

const APP_NAME = '".APP_NAME."'; // -> name of the folder the app is inside
const APP_TITLE = '".htmlspecialchars($_POST["appName"])."'; // Title of the app

// db credentials
const DB_HOST = '".htmlspecialchars($_POST["dbHost"])."';
const DB_USER = '".htmlspecialchars($_POST["dbUser"])."';
const DB_PASSWORD = '".htmlspecialchars($_POST["dbPassword"])."';
const DB_DATABASE = '".htmlspecialchars($_POST["dbName"])."';

// const DEBUG_SQL = false; // log debug data and sql statements into sql.log
// const BPFW_DEBUG_MODE = false; // enable debug mode / messages
// const LOG_CHANGES = true;  // -> changes will be logged in datalog table and visible in /?p=datalog
// const COPY_SIGNATURE = false;  // -> Customer Signatures can be cloned
// const STARTING_PAGE = 'user'; // -> page after login
// const DEFAULT_LANGUAGE = 'en'; // -> lang code of default language. Translations for 'de' and 'en' are existing, other languages have to be added manually

";*/
                
                if(FALSE === file_put_contents($configName, $configData)){
                    throw new Exception("Can't Create config File $configName");
                }

                echo "<p>Config Created ... </p>";

                if($_POST["appTemplate"] != "empty"){

                    $themedir = $bpfwInstallerPath."themes".DIRECTORY_SEPARATOR.$_POST["appTemplate"].DIRECTORY_SEPARATOR;

                    if(!file_exists($themedir)){
                        throw new Exception("Themedir '$themedir' not found, cancelling");
                    }

                    // copy core
                    @bpfw_copy_directory($bpfwInstallerPath."corefiles", APP_BASE_PATH);
                    echo "<p>Copied corefiles ... </p>";

                    // copy theme
                    @bpfw_copy_directory($themedir, APP_BASE_PATH);
                    echo "<p>Copied theme ... </p>";

                    // copy example
                    if($_POST["exampleModels"] == "on") {
                        @bpfw_copy_directory($bpfwInstallerPath."demodata", APP_BASE_PATH);
                        echo "<p>Copied examples ... </p>";
                    }

                }

                echo "<p>Files Copied ... </p>";

                bpfw_setCreatingTables(true);
                $appSettings = bpfw_createModelByName(SETTINGS_TABLENAME);
                bpfw_setCreatingTables(true);
                $appSettings->initializeSettings();
                bpfw_setCreatingTables(true);
                createAdminIfNotExisting($_POST["adminUser"], $_POST["adminPassword"]);
                $translationModel = bpfw_createModelByName("translation");
                $translationModel->createTable(false, true, false);
                bpfw_setCreatingTables(true);
                $models = dbModel::getAllModels();
                bpfw_setCreatingTables(true);
                foreach($models as $model){
                    if($model->GetTableName() != "user") {
                        bpfw_setCreatingTables(true);
                        $model->createTable(false, true, false);
                    }
                }
                bpfw_setCreatingTables(true);
                // var_dump($models);
                $languageModel = bpfw_createModelByName("language");
                $languageModel->AddEntry(array("languageId" => "en"), true);
                $languageModel->AddEntry(array("languageId" => "de"), true);

                $translationModel->initTranslation(true); // init translation
                // bpfw_createModelByName("translation")->initTranslation();
                // bpfw_createModelByName("translation")->initTranslation();



                bpfw_saveSetting(SETTING_DEFAULT_LANGUAGE, $_POST["lang"]);

                echo "<p>Tables Created ... </p>";


            }catch(Exception $ex){
                echo "<pre>".$ex->getTraceAsString()."</pre>";
                echo "Error on Creation:".$ex->getMessage();
            }

            $_SESSION["APP_NAME"] = APP_NAME;
            echo "<p>Created <a href='".BASE_URI."?app=".APP_NAME."'>open App</a></p>";

        }else{
            echo "<p>Aborted</p>";
        }


     }


    ?>

        <style>

            #createAppForm{
                border: 1px solid black;
                background-color:lightgray;
                display: inline-block;
                padding:20px;
                margin:auto;
                width:550px;
            }

            #createAppForm p label input, #createAppForm p label select{
                float:right;
            }

            #createAppForm select, #createAppForm input{
                width:200px;
            }

            #createAppForm p, #createAppForm hr{
                width:550px;
                text-align: left;
            }

            #createAppForm hr{
                float:left;
                margin:15px 0;
            }

            .createFormWrap{
                display: block;
                height: 100vh;
                width:100vw;
                padding-top: 100px;
                text-align: center;
            }

        </style>


<div class="createFormWrap">
    <form id='createAppForm' action='' method='post' accept-charset='UTF-8' autocomplete="off">

        <h1>Create new Bpfw App</h1>

        <hr />

        <?php
            createFormInput("App Name", "appName", );
            createFormInput("App Slug and Foldername<br>(Name without Spaces, Uppercase, Special Chars)", "appSlug" );
            createFormInput("App Template", "appTemplate", false, "select", "", array("business"=>"Business", "modern"=>"Modern", "simple"=>"Simple", "empty"=>"Empty, (only Database and config)",));
        ?><p>For a template overview, take a look at <a href="<?php echo BPFW_WWW_URI."design_comparison.jpg"; ?>" target="_blank">this image</a></p>
            <?php
            createFormInput("Default Language", "lang", false, "select", "", array("en"=>"English", "de"=>"German"));
            CreateFormInput("Install example models(recommended)", "exampleModels", false, "checkbox", "selected");
            echo "<hr />";
        createFormInput("DB Host", "dbHost", true, "text", "localhost" );
        createFormInput("DB Name", "dbName" );
        createFormInput("DB User", "dbUser" );
        createFormInput("DB Password", "dbPassword", false, "password");
        echo "<hr />";
        createFormInput("Admin Username", "adminUser" );
        createFormInput("Admin Password", "adminPassword", true, "password" );
        createFormInput("Admin Password repeat", "adminPassword2", true, "password" );
        if(!$firstApp){
            echo "<hr />";
            createFormInput("App Creation Password", "appPassword", true, "password" );
        }

?>
        <p>
            <input type="submit" value="Create new App">
        </p>

    </form>

</div>


    <?php


}
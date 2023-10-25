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


error_reporting (E_ALL | E_STRICT);

ini_set ('display_errors', 'On');

date_default_timezone_set('Europe/Berlin');
// ps_files_cleanup_dir opendir(/tmp/.priv) failed: Permission denied(13) in (use_bpfw.inc.php line 9)
session_start();

if (session_save_path() != '' && !is_writable(session_save_path())) {
    echo 'Session path "'.session_save_path().'" is not writable for PHP!';
}



/* App wurde gewechselt, logout */
if(isset($_GET["clean"])){

    killSessionData();
    //$_SESSION['activePage'] = "login";
    $usertype = 999999999;

}

require_once("core/appCreation.inc.php");

const BPFW_MANAGER_APP = "appmanager";

require_once("vendor/autoload.php"); // composer

function bpfw_scanForApps(): array
{


    $appsFound = array();

    $appsdir = "./apps";

    if(is_dir($appsdir)){

        $files = scandir($appsdir);

        foreach($files as $file){

            if(is_dir($appsdir."/".$file)){

                if($file != "." && $file != ".."){

                    if(file_exists($appsdir."/".$file."/".$file."config.inc.php")){

                        //if($file != "wws45"){ // parent app // TODO use TEMPLATE_THEME
                            $appsFound[$file]=$file;
                        //}
                    }

                }
            }


        }

    }

    return $appsFound;

}


function killSessionData(): void
{
 
    // throw new Exception("killsessiondata");

    $app = null;

    if(!empty($_SESSION['ACTIVE_APP']))
    $app = $_SESSION['ACTIVE_APP'];

    session_reset();

    $_SESSION['usertoken'] = null;

    $_SESSION['usertype'] = null;
    $_SESSION['userId'] = null;

    $_SESSION['userdata'] = null;


    $_SESSION["ACTIVE_APP"] = $app;

    session_write_close();

}

/**
 *
 * Extract defines from (config) file
 *
 * @param mixed $filename
 * @return array
 */
function parseDefines(mixed $filename): array
{

    /* echo $appsdir."/".$file."/".$file."config.php";
     */
    $configccode = file_get_contents($filename);

    $tokens = token_get_all($configccode);

    $key=null;

    $values=array();

    foreach ($tokens as $token) {
        if (is_array($token)) {
            //  echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;

            if(token_name($token[0]) == "T_CONSTANT_ENCAPSED_STRING" || token_name($token[0])  == "T_LNUMBER"){

                if($key == null){
                    $key=$token[1];

                    if ((str_starts_with($key, '\'')) && (str_ends_with($key, '\''))) {
                        $key = substr($key, 1, -1);
                    }
                    if ((str_starts_with($key, '"')) && (str_ends_with($key, '"'))) {
                        $key = substr($key, 1, -1);
                    }
                }
                else{
                    $val=$token[1];
                    if ((str_starts_with($val, '\'')) && (str_ends_with($val, '\''))) {
                        $val = substr($val, 1, -1);
                    }
                    if ((str_starts_with($val, '"')) && (str_ends_with($val, '"'))) {
                        $val = substr($val, 1, -1);
                    }


                    $values[$key]=$val;

                    $key=null;

                }
            }

        }

    }

    return $values;

}



/**
 *
 * returns first appfoltername with defined BASE_URI in config that matches the called domain. this way we can use xxx.tradist.de without any postparameters for customers
 *
 * @return string
 */
function findBestApp(): string
{

    $appsFound = array();

    $appsdir = "./apps";

    if(is_dir($appsdir)){

        $files = scandir($appsdir);

        foreach($files as $file){

            if(is_dir($appsdir."/".$file)){

                if($file != "." && $file != ".."){

                    if(file_exists($appsdir."/".$file."/".$file."config.inc.php")){

                        try{

                            $values = parseDefines($appsdir."/".$file."/".$file."config.inc.php");

                            if(isset($values["BASE_URI"])){


                                if(strstr($values["BASE_URI"], $_SERVER['SERVER_NAME'])){
                                    if($file != "wws45"){
                                        return $file;
                                    }
                                }

                            }

                        }catch(Exception $e){
                            echo "Exception ".$e->getMessage()." - ".$e->getTraceAsString();
                            die();
                        }

                        if($file != "wws45"){ // parent app, TODO: autodetect with define in config
                            $appsFound[$file]=$file;
                        }

                    }

                }
            }


        }

    }

    return current($appsFound);

}



error_reporting(E_ALL);
/** App wurde gewechselt, logout */

$appBefore = null;

if(!empty($_SESSION["ACTIVE_APP"])){
    $appBefore = $_SESSION["ACTIVE_APP"];
}
/*
$appsFound=null;

if(!isset($_SESSION["ACTIVE_APP"]) ) {
    $appsFound = bpfw_scanForApps();
}*/

$appsFound = bpfw_scanForApps();

if(isset($_GET["createApp"]) || empty($appsFound)){

    killSessionData();
    $_SESSION["ACTIVE_APP"] = "";
    $usertype = 999999999;


    $appsFound = bpfw_scanForApps();

    $appName = "";
    if(!empty($_POST["appSlug"])){
        $appName = htmlspecialchars($_POST["appSlug"]);
    }

    define("APP_NAME", $appName);

    require_once("core/bpfwconfig.inc.php");
//    echo BPFW_CORE_PATH . "includes.inc.php";
    require_once(BPFW_CORE_PATH . "includes.inc.php");

    if(ALLOW_MULTIPLE_APP_CREATION){

        if(empty(MULTIPLE_APP_CREATION_PASSWORD)){
            die("You need to define MULTIPLE_APP_CREATION_PASSWORD to create multiple apps");
        }

        runAppCreation();

    }else if(empty($appsFound)){
        runAppCreation();
    }


    die();

}

if(isset($_GET["app"]) || !isset($_SESSION["ACTIVE_APP"]) ){

    $appsFound = bpfw_scanForApps();

    if(empty($appsFound)){


        require_once("core/bpfwconfig.inc.php");

        runAppCreation();


        die();
    }





    if(isset($_GET["app"]) && isset($appsFound[$_GET["app"]])){
        $_SESSION["ACTIVE_APP"] = $_GET["app"];
    }else{

        if(in_array(BPFW_MANAGER_APP, $appsFound)){
            $_SESSION["ACTIVE_APP"] = $_GET["app"] = BPFW_MANAGER_APP;
        }else{

            $_SESSION["ACTIVE_APP"] = findBestApp();
        }

    }

}

if(empty($_SESSION["ACTIVE_APP"])){
    die("no app set");
}


/* App changed, logout */
if($appBefore != $_SESSION["ACTIVE_APP"]){
    $activeApp_rem = $_SESSION["ACTIVE_APP"];
    killSessionData();
    //$_SESSION['activePage'] = "login";
    $usertype = 999999999;
    $_SESSION["ACTIVE_APP"] = $activeApp_rem;
}


function checkConfigSet($name, $allowEmpty = false): void
{

    if(!defined($name) || !$allowEmpty && empty(constant($name)) ){
        die("required define not set in Config: $name");
    }

}

// define("ACTIVE_APP", "erfolgspfad");
// define("ACTIVE_APP", "wws45");
define("ACTIVE_APP", $_SESSION["ACTIVE_APP"]);
// define("ACTIVE_APP", "cvbuilder");

function defineIfNotDefined($key, $value): void
{
    if(!defined($key))
        define($key, $value);
}

if(!defined("ACTIVE_APP")){
    die("active app not set ".ACTIVE_APP);
}

if(!file_exists("apps/".ACTIVE_APP."/".ACTIVE_APP."config.inc.php")){
    $_SESSION['ACTIVE_APP'] = null;
    killSessionData();
    die("apps/".ACTIVE_APP."/".ACTIVE_APP."config.inc.php not found, resetting session");

}else {
    require_once("apps/" . ACTIVE_APP . "/" . ACTIVE_APP . "config.inc.php");
}


if(defined("PARENT_NAME")){
    require_once("apps/".PARENT_NAME."/".PARENT_NAME."config.inc.php");
}

checkConfigSet("ACTIVE_APP");
checkConfigSet("DB_USER");
checkConfigSet("DB_PASSWORD", true);
checkConfigSet("DB_DATABASE");
checkConfigSet("DB_HOST");
checkConfigSet("APP_TITLE");

if(!defined("ACTIVE_APP")){
    die("please define ACTIVE_APP in config file");
}


require_once("core/bpfwconfig.inc.php");


if(file_exists(APP_BASE_PATH."includes.inc.php")){
    require_once(APP_BASE_PATH."includes.inc.php");
}

if(defined("PARENT_NAME")){
    if(file_exists(PARENT_BASE_PATH."includes.inc.php")){
        require_once(PARENT_BASE_PATH."includes.inc.php");
    }
}

if(file_exists("apps/".ACTIVE_APP."/vendor/autoload.php")){
    require_once("apps/".ACTIVE_APP."/vendor/autoload.php");
}

if(file_exists(VENDOR_PATH."/autoload.php")){
    require_once(VENDOR_PATH."/autoload.php");
}

require_once(BPFW_CORE_PATH."configVariables.inc.php");

if(config()->TEMPLATE_THEME){
    die("don't use template Theme directly, create Child Theme");
}

require_once(BPFW_CORE_PATH . "includes.inc.php");


try {
    // add words that should be translated in JS
    bpfw_js_announce_translation("Discard");
    bpfw_js_announce_translation("Confirm deletion?");

} catch (Exception $e) {
    if(BPFW_DEBUG_MODE)
    echo "error: ".$e->getMessage()." ".$e->getTraceAsString();
}


if(BPFW_DEBUG_MODE)
{
    // var_dump($_SESSION);
}

//require_once("apps/".ACTIVE_APP."/db/".ACTIVE_APP."Database.php");

if(file_exists(APP_BASE_PATH."loginHandler.inc.php"))
    require_once(APP_BASE_PATH."loginHandler.inc.php");
else
    if(file_exists(PARENT_BASE_PATH."loginHandler.inc.php"))
        require_once(PARENT_BASE_PATH."loginHandler.inc.php");
    else
        if(file_exists(BPFW_CORE_PATH."loginHandler.inc.php"))
            require_once(BPFW_CORE_PATH . "loginHandler.inc.php");

require_once(BPFW_CORE_PATH . "pages.inc.php");

/**
 * @throws Exception
 */
function createAdminIfNotExisting($username = "admin", $password = "password"): void
{

    $db = bpfw_getDb();

    require_once(BPFW_MVC_PATH . "bpfwDbFieldType.inc.php");

    require_once(BPFW_MVC_PATH . "bpfwModelFormField.inc.php");
    require_once(BPFW_MVC_PATH . "modelLoader.inc.php");
    require_once(BPFW_MVC_PATH . "controlHandler.inc.php");
    require_once(BPFW_MVC_PATH . "viewLoader.inc.php");
    $tables = $db->getAllTables();
    if(!in_array("user", $tables)){
        $userModel = bpfw_createModelByName("user");
        $userModel->createTable(false, true, false);
    }

    if(null == $db->makeSelectSingleOrNull("user", "username", "$username")){

        if(null == $db->makeSelectSingleOrNull("user", "type", 0)) {

            if (!empty(getorpost("password"))) {
                $password = getorpost("password");
            }

            $query = "INSERT INTO user (username, password, type) VALUES ('$username', '" . md5($password) . "', 0);";

            $result = $db->makeQuery($query);

            if ($result != "1") {
                echo $result;
            }
        }else{
            throw new Exception("Can only create one superadmin with create admin");
        }

    }else{
        throw new Exception("username already given");
    }

}

if(isset($_GET["createadmin"])){

    try {

        createAdminIfNotExisting();
        echo "Admin created";
    } catch (Exception $e) {
        echo "Admin could not be created ".$e->getMessage();
    }
}

if(isset($_GET["unsubscribe"]) && !bpfw_isAjax()){
    try {
        $retval = bpfw_unsubscribe_newsletter(getorpost("unsubscribe"), getorpost("t"), getorpost("h"));
    } catch (Exception $e) {
        echo "error: ".$e->getMessage()." ".$e->getTraceAsString();
    }
    echo "<br><br>";
    if($retval){
        echo "<h2 style='text-align:center;color:white;margin:auto;display:inline-block;width:100%;'>Erfolgreich vom Newsletter abgemeldet.</h2>";
    }else{
        echo "<h2 style='text-align:center;color:white;margin:auto;display:inline-block;width:100%;'>Abmeldung nicht m&ouml;glich oder bereits abgemeldet.";
    }
}

if(isset($forceRefresh) && $forceRefresh && !bpfw_isAjax()){

    ?>
    <script>

        jQuery(document).ready(
            function () {
                location.reload();
                //   window.location.href = "https://mein-lebenslauf.de/lebenslaufgenerator/?p=user";
            }
        );

    </script>
    <?php

}
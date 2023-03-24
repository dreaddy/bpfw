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



class ModelLoader
{

    private static string $currentClassName = "";


    // private static $current = "user";

    /**
     * Summary of fetchData
     * @param $page
     * @param null $keyValue
     * @return BpfwModel
     * @throws Exception
     */
    public static function findAndCreateModel($page, $keyValue = null): BpfwModel
    {

        if($keyValue == "")$keyValue=null;

        $current = ModelLoader::getCurrentPage();

        $model = null;

        if (!empty($page)) {
            $current = $page;
        }

        if (file_exists(APP_MVC_PATH . "models/" . $current . "Model.inc.php")) {
            require_once(APP_MVC_PATH . "models/" . $current . "Model.inc.php");
        } else
            if (file_exists(PARENT_MVC_PATH . "models/" . $current . "Model.inc.php")) {
                require_once(PARENT_MVC_PATH . "models/" . $current . "Model.inc.php");
            } else if (file_exists(BPFW_MVC_PATH . "models/" . $current . "Model.inc.php")) {
                require_once(BPFW_MVC_PATH . "models/" . $current . "Model.inc.php");
            } else {

                /*echo APP_MVC_PATH."models/".$current."Model.inc.php";
                echo BPFW_MVC_PATH."models/".$current."Model.inc.php";
                echo PARENT_MVC_PATH."models/".$current."Model.inc.php";
                throw new Exception("model not existing for ".$current);*/
                //echo "Seite existiert nicht";
                //return new DefaultModel();

                return bpfw_createEmptyModel();
            }


        $classnames = array(ucwords($current) . "Model", strtolower($current) . "Model", strtolower($current . "Model"), $current . "Model");


        //var_dump($classnames);

        foreach ($classnames as $val) {

            if (class_exists($val)) {

                static::$currentClassName = $val;
                $model = new static::$currentClassName($keyValue);
                if (!bpfw_hasPermission($model->minUserrankForShow) && !bpfw_hasPermission($model->minUserrankForAdd)) {   // cant add or show

                    return $model;
                }

                $componentIncludes = bpfw_getComponentHandler()->getIncludeFiles($model);

                foreach ($componentIncludes as $value) {
                    require_once($value);
                    /*
                    $file = APP_COMPONENT_PATH.$value;
                    if(file_exists($file)){
                        require_once($file);
                    }else{
                        $file = PARENT_COMPONENT_PATH.$value;
                        if(file_exists($file)){
                            require_once($file);
                        }else {
                            $file = BPFW_COMPONENT_PATH . $value;
                            if (file_exists($file)) {
                                require_once($file);
                            }
                        }
                    }*/
                }

                bpfw_getComponentHandler()->AddComponentsOfModel($model);

                break;
            }
        }


        if ($model == null) {
            throw new Exception("no Model found");
        }

        return $model;

    }

    /*public static function reloadData(): BpfwModel
    {
            return static::findAndCreateModel(static::$currentClassName);
    }*/

    public static function getCurrentPage()
    {
        return bpfw_getActivePage(); //static::$current;
    }


    //var $model = null;

}

function bpfw_createEmptyModel(): BpfwModel
{

    return new class extends BpfwModel {

        function __construct()
        {

            $this->showTopButtons = false;

            parent::__construct();

        }

        function GetTitle(): string
        {
            return "404";
        }

        function loadDbModel(): array
        {
            return array();
        }

        function GetTableName(): string
        {
            return "empty";
        }

    };
}

/**
 * @throws Exception
 * @noinspection PhpUnused
 */
function bpfw_getModelValues($name, $keyValue): ?array
{

    $model = bpfw_createModelByName($name);

    if (!empty($model)) {
        return $model->DbSelectSingleOrNullByKey($keyValue);
    }

    return null;

}

/**
 * Summary of bpfw_getFromModelByKey
 * @param string $modelName
 * @param int $keyName
 * @return DbModelEntry
 * @throws Exception
 */
function bpfw_getEntryFromModelByKey(string $modelName, int $keyName): DbModelEntry
{

    $model = bpfw_createModelByName($modelName, true);
    return $model->GetEntry($keyName);

}


$_modelCache = array();


/**
 * check if the model exists
 * @param $name string name name of the model
 * @throws Exception
 */
function bpfw_modelExists(string $name): bool
{
    $model = bpfw_createModelByName($name, false, false);
    return $model != NULL;
}

/**
 * Create a model by name. Looks for modelfiles, includes them and returns an object. respects the hierarchy (look at app first, then parent, then bpfs core)
 * @param string $name name of the model
 * @param bool $cache enable caching
 * @param bool $throwExceptionOnNotExisting
 * @return ?BpfwModel model or null
 * @throws Exception
 */
function bpfw_createModelByName(string $name, bool $cache = false, bool $throwExceptionOnNotExisting = true): ?BpfwModel
{

    global $_modelCache;

try {
    // echo "cache is ".count($_modelcache);
    if (!empty($_modelCache[$name]) && $cache) {
        //echo "usedcache";
        return $_modelCache[$name];
    }

    $linkedTable = false;

    // debug_print_backtrace();echo "\r\n\r\n\r\n<br><br>\r\n\r\n";

    if (bpfw_strStartsWith($name, "link_")) {

        $linkedTable = true;
        $nameOfParent = str_replace("link_", "", $name);

        $fieldName = substr($nameOfParent, strpos($nameOfParent, "_") + 1);

        $nameOfParent = substr($nameOfParent, 0, strpos($nameOfParent, "_"));

        $parentModel = bpfw_createModelByName($nameOfParent);
        $linkModel = $parentModel->generateLinkTableModel($fieldName, false);
        if ($cache) {
            $_modelCache[$name] = $linkModel;
        }


        return $linkModel;

        /*
        if($parentModel != NULL){
            $linkModel = $parentModel->generateLinkTableModel($fieldName, false);
            if($cache)
            $_modelCache[$name] = $linkModel;
            return $linkModel;
        }else{
            throw new Exception("Linked Table $name -> parent table not found $nameOfParent");
        }*/

    }

    $filename = bpfw_getModelIncludeByName($name);
    require_once $filename;

    $classFound = bpfw_getModelClassname($name);


    if (!empty($classFound)) {

        if (class_exists($classFound)) {

            $classInstance = new $classFound();

            if ($cache)
                $_modelCache[$name] = $classInstance;


            return $classInstance;
        }
    }


    if ($throwExceptionOnNotExisting) {
        throw new Exception("Model not found: $name");
    }
}catch(Exception $ex){
    if ($throwExceptionOnNotExisting) {
        throw $ex;
    }
    return NULL;
}
    return NULL;

}

/**
 * get the classname of the model
 * @param string $name name of the model
 * @return mixed|string
 * @throws Exception
 */
function bpfw_getModelClassname(string $name)
{

    $classidentifier = $name; // str_replace("model.inc.php", "", strtolower($filename));
    $classnames = array(ucwords($classidentifier) . "Model", strtolower($classidentifier) . "Model", strtolower($classidentifier . "Model"), $classidentifier . "Model");

    //var_dump($classnames);
    $classFound = NULL;
    foreach ($classnames as $val) {
        if (class_exists($val)) {
            return $val;
        }
    }

    throw new Exception("Classname for $name not found");

}

function bpfw_getModelIncludeByName(string $name): string
{
    $filename = $name;

    // require_once(APP_MVC_PATH."models/".$filename);
    if (file_exists(APP_MVC_PATH . "models/" . $filename . "Model.inc.php")) {
        return (APP_MVC_PATH . "models/" . $filename . "Model.inc.php");
    } else if (file_exists(PARENT_MVC_PATH . "models/" . $filename . "Model.inc.php")) {
        return (PARENT_MVC_PATH . "models/" . $filename . "Model.inc.php");
    } else if (file_exists(BPFW_MVC_PATH . "models/" . $filename . "Model.inc.php")) {
        return (BPFW_MVC_PATH . "models/" . $filename . "Model.inc.php");
    } else {
        echo APP_MVC_PATH . "models/" . $filename . "Model.inc.php";
        echo BPFW_MVC_PATH . "models/" . $filename . "Model.inc.php";
        echo PARENT_MVC_PATH . "models/" . $filename . "Model.inc.php";
        throw new Exception("model not existing for " . $filename);
    }
}


/**
 * alternative way of creation you only need the model
 * @param string $name
 * @return DbModel
 * @throws Exception
 */
function bpfw_createTemplateModelByName(string $name): DbModel
{

    $linkedTable = false;

    if (bpfw_strStartsWith($name, "link_")) {

        $linkedTable = true;
        $name_parent = str_replace("link_", "", $name);

        $fieldname = substr($name_parent, strpos($name_parent, "_") + 1);

        $name_parent = substr($name_parent, 0, strpos($name_parent, "_"));

        return bpfw_createTemplateModelByName($name_parent);

        /*
        $parentmodel = bpfw_createTemplateModelByName($name_parent);

        if($parentmodel != NULL){
            return $parentmodel->generateLinkTableModel($fieldname, false);
        }else{
            throw new Exception("Linked Table $name -> parent table not found $name_parent");
        }*/

    }


    $filename = $name;

    // require_once(APP_MVC_PATH."models/".$filename);
    if (file_exists(APP_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php")) {
        require_once(APP_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php");
    } else if (file_exists(PARENT_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php")) {
        require_once(PARENT_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php");
    } else if (file_exists(BPFW_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php")) {
        require_once(BPFW_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php");
    } else {
        echo APP_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php";
        echo BPFW_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php";
        echo PARENT_MVC_PATH . "models/templates/" . $filename . "ModelTemplate.inc.php";
        throw new Exception("template not existing for " . $filename);
    }

    $classIdentifier = str_replace("modeltemplate.inc.php", "", strtolower($filename));
    $classnames = array(ucwords($classIdentifier) . "ModelTemplate", strtolower($classIdentifier) . "ModelTemplate", strtolower($classIdentifier . "ModelTemplate"), $classIdentifier . "ModelTemplate");

    $classFound = NULL;
    foreach ($classnames as $val) {
        if (class_exists($val)) {
            $classFound = $val;
        }
    }

    if (!empty($classFound)) {
        if (class_exists($classFound)) {
            return new $classFound();
        }
    }
    // throw new Exception("Linked Table $name -> parent table not found $name_parent");
    throw new Exception("Model '$name' not found");

}



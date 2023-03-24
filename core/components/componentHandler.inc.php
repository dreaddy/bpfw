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

/**
 * components short summary.
 *
 * components description.
 *
 * @version 1.0
 * @author torst
 */
class ComponentHandler
{

    public array $customComponentProperties = array();
    var array $components = array();
    var array $componentsToLoad = array(/*"datepicker"=>"datepicker"*/);
    var array $componentsAlreadyLoaded = array();

    /**
     * @throws Exception
     */
    function loadComponent($componentName): void
    {
        $this->componentsToLoad[] = $componentName;
        $this->AddComponentsFromArray(array($componentName => $componentName));
    }

    /**
     * @throws Exception
     */
    function AddComponentsFromArray($stringArrayOfComponentNames): void
    {

        $refreshJsInside = "";

        $types = $this->mergeWithExistingAndAddedFieldTypes($stringArrayOfComponentNames); // array_unique(array_merge($stringArrayOfComponentNames, $this->componentsAlreadyLoaded, $this->componentsToLoad));

        $this->componentsAlreadyLoaded = $types;

        foreach ($types as $k => $v) {

            $newComponent = null;
            $newComponent = $this->AddComponent($k);

            if ($newComponent != null) {

                $refreshJsInside .= "\r\n/* " . $newComponent->name . " */";

                $customAttributes = $newComponent->getCustomAttributes();

                foreach ($customAttributes as $name => $defaultvalue) {
                    $this->addCustomComponentProperty($name, $defaultvalue);
                }

                if (!empty($newComponent->getRedrawJs())) {

                    $code = $newComponent->getRedrawJs();

                    if (bpfw_strStartsWith(trim($code), "<script>")) {
                        $code = str_replace("<script>", "", $code);
                        $code = str_replace("</script>", "", $code);
                    }

                    $refreshJsInside .= trim($code);

                }

            }

        }

        if (!empty($refreshJsInside)) {

            $refreshJs = "function bpfw_refeshComponentScripts(currentDialogId){";
            $refreshJs .= $refreshJsInside;
            $refreshJs .= "}";

            bpfw_register_js_inline("componentsRefresh", $refreshJs);

        }

    }

    function mergeWithExistingAndAddedFieldTypes($stringArrayOfComponentNames): array
    {
        return array_unique(array_merge($stringArrayOfComponentNames, $this->componentsAlreadyLoaded, $this->componentsToLoad));
    }

    /**
     * @throws Exception
     */
    public function AddComponent($name, $makeInclude = false)
    {

        if (isset($this->components[$name])) return $this->components[$name];

        if ($makeInclude) {

            $includefilename = $this->getIncludeFileForComponent($name);

            require_once($includefilename);

            /*
            $includefileWithPath = "";
            if(file_exists(APP_COMPONENT_PATH.$includefilename)){
                $includefileWithPath =APP_COMPONENT_PATH.$includefilename;
            }else if(file_exists(BPFW_COMPONENT_PATH.$includefilename)){
                $includefileWithPath =BPFW_COMPONENT_PATH.$includefilename;
            }

            if(!empty($includefileWithPath)){
                require_once($includefileWithPath);
            }else{
                throw new Exception("component $name has no include");
            } */

        }

        $name = strtolower($name);

        $classnames = array(
            ucwords($name) . "Component",
            strtolower($name) . "Component",
            strtolower($name . "Component"),
            $name . "Component"
        );

        $foundname = "";
        foreach ($classnames as $val) {
            if (class_exists($val)) {


                $foundname = $val;

                break;
            }
        }

        if (!empty($foundname)) {
            $this->components[$name] = new $foundname($name, $this);
        }

        if (empty($this->components[$name])) {
            $this->components[$name] = new DefaultComponent($name, $this);
        }

        return $this->components[$name];

    }

    /**
     * @throws Exception
     */
    public function getIncludeFileForComponent($name): string
    {

        $file_app = APP_COMPONENT_PATH . strtolower($name) . "Component.inc.php";
        $file2_app = APP_COMPONENT_PATH . strtolower($name) . "component.inc.php";
        $file_parent = PARENT_COMPONENT_PATH . strtolower($name) . "Component.inc.php";
        $file2_parent = PARENT_COMPONENT_PATH . strtolower($name) . "component.inc.php";
        $file_bpfw = BPFW_COMPONENT_PATH . strtolower($name) . "Component.inc.php";
        $file2_bpfw = BPFW_COMPONENT_PATH . strtolower($name) . "component.inc.php";

        if (file_exists($file_app)) {
            if (file_exists($file2_app) && !bpfw_is_windows()) {
                throw new Exception("app Component $name exists as {$name}component.php and  {$name}Component.php");
            }
            return $file_app;
        }
        if (file_exists($file2_app)) {
            return $file2_app;
        }
        if (file_exists($file_parent)) {
            if (file_exists($file2_parent) && !bpfw_is_windows()) {
                throw new Exception("parent Component $name exists as {$name}component.php and  {$name}Component.php");
            }
            return $file_parent;
        }
        if (file_exists($file2_parent)) {
            return $file2_parent;
        }
        if (file_exists($file_bpfw)) {
            if (file_exists($file2_bpfw) && !bpfw_is_windows()) {
                throw new Exception("bpfw Component $name exists as {$name}component.php and  {$name}Component.php");
            }
            return $file_bpfw;
        }
        if (file_exists($file2_bpfw)) {
            return $file2_bpfw;
        }


        throw new Exception(strtolower($name) . "Component.php" . " is not existing anywhere");

    }

    public function addCustomComponentProperty($key, $defaultValue): void
    {
        $this->customComponentProperties[$key] = $defaultValue;
    }

    /*
    if(file_exists($file_app) || file_exists($file_bpfw) || file_exists($file_parent)  ){

        if(!bpfw_is_windows() && (file_exists(APP_COMPONENT_PATH.strtolower($name)."component.php") || file_exists(BPFW_COMPONENT_PATH.strtolower($name)."component.php"))){
            // no textComponent and textcomponent etc
            throw new Exception(strtolower($name)."Component.php" . " is existing as component and Component -> delete one");
        }

        return strtolower($name)."Component.php";

    }


    $file_app = APP_COMPONENT_PATH.strtolower($name)."component.php";
    $file_bpfw = BPFW_COMPONENT_PATH.strtolower($name)."component.php";
    $file_parent = PARENT_COMPONENT_PATH.strtolower($name)."component.php";
    if(file_exists($file_app) || file_exists($file_bpfw) || file_exists($file_parent)  ){
        return strtolower($name)."component.php";
    }

    throw new Exception(strtolower($name)."Component.php" . " is existing as component and Component -> delete one");



} */

    /**
     * Summary of AddComponentsOfModel
     * @param BpfwModel $model
     * @throws Exception
     */
    public function AddComponentsOfModel(BpfwModel $model): void
    {

        $types = $model->getUsedFieldTypes();
        $this->AddComponentsFromArray($types);

    }

    /**
     * @throws Exception
     */
    public function getIncludeFiles($model): array
    {

        $types = $this->mergeWithExistingAndAddedFieldTypes($model->getUsedFieldTypes());

        $retval = array();

        foreach ($types as $k => $v) {

            $retval[] = $this->getIncludeFileForComponent($k);

        }

        return $retval;

    }

    /**
     * Summary of getComponent
     * @param string $name
     * @param bool $makeInclude
     * @return DefaultComponent
     * @throws Exception
     */
    public function getComponent(string $name, bool $makeInclude = true): DefaultComponent
    {

        $name = strtolower($name);

        if (isset($this->components[$name])) {
            return $this->components[$name];
        }

        if (isset($this->components[strtolower($name)])) {
            return $this->components[strtolower($name)];
        }

        if ($makeInclude) {
            $this->AddComponent($name, $makeInclude);

            if (isset($this->components[$name])) {
                return $this->components[$name];
            }

            if (isset($this->components[strtolower($name)])) {
                return $this->components[strtolower($name)];
            }
        }

        throw new Exception("access to unknown Component: '" . $name . "' Components loaded: " . print_r($this->components, true));

    }

}

$_bpfw_componentHandler = null;

/**
 * Summary of bpfw_getComponentHandler
 * @return ComponentHandler
 */
function bpfw_getComponentHandler(): ComponentHandler
{
    global $_bpfw_componentHandler;
    if (empty($_bpfw_componentHandler)) {
        $_bpfw_componentHandler = new ComponentHandler();
    }

    return $_bpfw_componentHandler;
}


function bpfw_getIncludeFileForComponent($name): string
{
    try {
        return bpfw_getComponentHandler()->getIncludeFileForComponent($name);
    } catch (Exception $ex) {
        die("eror loading Component $name : " . $ex->getMessage());
    }
}


/*
function bpdf_component_call($componentName, $function, $parameters){
    global $bpfw_componentHandler;

    $component = $bpfw_componentHandler->getComponent($componentName);

    if(method_exists($component, $componentName)){
        return call_user_func_array(array($component, $function), $parameters);
    }else{
        throw new Exception("unknown Method ".$function);
    }

}*/

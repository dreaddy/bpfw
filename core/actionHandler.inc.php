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




class BpfwActionHandler
{

    const TYPE_ACTION = "action";
    const TYPE_FILTER = "filter";
    const TYPE_CHECK = "check";

    public array $hooks = array();

    function __construct()
    {

    }

    /**
     * @param string $tag
     * @param string|null $contextName
     * @param callable $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return void
     */
    function add_action(string $tag, string|null $contextName, callable $function_to_add, int $priority = 10, int $accepted_args = 0): void
    {

        if (empty($this->hooks[$tag])) {
            $this->hooks[$tag] = array();
        }

        $this->hooks[$tag][] = array("tag" => $tag, "function" => $function_to_add, "priority" => $priority, "accepted_args" => $accepted_args, "contextname" => $contextName);

    }

    /**
     * @param string $tag
     * @param string|null $contextName
     * @param array $params
     * @param string $type
     * @param object|null $contextClass
     * @return mixed
     * @throws Exception
     */
    function do_action(string $tag, string|null $contextName, array $params = array(), string $type = BpfwActionHandler::TYPE_ACTION, object|null $contextClass = null): mixed
    {

        $returnValue = "";

        if ($type == BpfwActionHandler::TYPE_FILTER) {
            if (!isset($params[0])) {
                var_dump($params);
                throw new Exception("Param 0 not set for filter in do_action $tag");
            }
            $returnValue = $params[0];
        }

        if ($type == BpfwActionHandler::TYPE_CHECK) {
            $returnValue = true;
        }

        if (!empty($this->hooks[$tag])) {

            // TODO: sort by priority
            foreach ($this->hooks[$tag] as $count => $action) {

                if (count($params) != $action["accepted_args"]) {
                    throw new Exception("do_action of $tag argument count not correct; Skipping. Expecting " . $action["accepted_args"] . " got " . count($params));
                } else {

                    if (!empty($contextClass)) {

                        if (!is_array($action["function"])) {
                            //  continue;
                        } else {

                            if (($action["contextname"] != null && $contextName != null) && $action["contextname"] != $contextName) {
                                continue;
                            }

                            if ($action["function"][0] === $contextClass) {  // TODO: raus oder oder typeof nehmen?

                            } else {
                                continue;
                            }
                        }

                    }

                    /* findme echo "\r\n\r\n<br>";
                     var_dump($action["function"][1]);
                     echo $count."->".$returnValue;
                     echo "before: $returnValue";*/

                    if ($type == BpfwActionHandler::TYPE_CHECK) {
                        $returnValue = call_user_func_array($action["function"], $params) && $returnValue;
                    } else {
                        $returnValue = call_user_func_array($action["function"], $params);
                    }

                    if ($type == BpfwActionHandler::TYPE_FILTER) {
                        $params[0] = $returnValue;
                    }
                }
            }
        }

        return $returnValue;

    }

}

$bpfw_actions = new BpfwActionHandler();

/**
 * execute previously registered Actions
 * @param string $tag
 * @param string|null $contextName
 * @param array $params
 * @param object|null $contextClass
 * @return mixed
 * @throws Exception
 */
function bpfw_do_action(string $tag, string|null $contextName, array $params = array(), object|null $contextClass = null): mixed
{
    global $bpfw_actions;
    return $bpfw_actions->do_action($tag, $contextName, $params, BpfwActionHandler::TYPE_ACTION, $contextClass);
}


/**
 * register a new Action (BEFORE do_action is called)
 * add an action. An action is a callback function that is called when the corresponding do_action is called
 * @param string $tag
 * @param string|null $contextName
 * @param callable $function_to_add
 * @param int $priority
 * @param int $accepted_args
 */
function bpfw_add_action(string $tag, string|null $contextName, callable $function_to_add, int $priority = 10, int $accepted_args = 0): void
{
    global $bpfw_actions;
    $bpfw_actions->add_action($tag, $contextName, $function_to_add, $priority, $accepted_args);
}


/**
 * register a new Filter (BEFORE do_action is called)
 * A filter is a special form of action. the first parameter is returned from the callback and can be manipulated. besides from that it behaves like an action.
 * @param string $tag
 * @param string|null $contextname
 * @param callable $function_to_add
 * @param int $priority
 * @param int $accepted_args
 * @throws Exception
 */
function bpfw_add_filter(string $tag, string|null $contextname, callable $function_to_add, int $priority = 10, int $accepted_args = 1): void
{

    if ($accepted_args < 1) throw new Exception("filter needs >= 1 Parameter");

    global $bpfw_actions;
    $bpfw_actions->add_action("filter_" . $tag, $contextname, $function_to_add, $priority, $accepted_args);

}

/**
 * execute previously registered Filter
 * @param string $tag
 * @param string|null $contextName
 * @param array $params
 * @param object|null $contextClass // only execute callbacks from this class
 * @return false|mixed
 * @throws Exception
 */
function bpfw_do_filter(string $tag, string|null $contextName, array $params = array(), object|null $contextClass = null): mixed
{

    global $bpfw_actions;

    $filtertag = "filter_" . $tag;

    if (empty($bpfw_actions->hooks[$filtertag])) {
        return current($params);
    }

    return $bpfw_actions->do_action($filtertag, $contextName, $params, BpfwActionHandler::TYPE_FILTER, $contextClass);

}


/**
 * register a new Filter (BEFORE do_action is called)
 * @param string $tag
 * @param string|null $contextName
 * @param callable $function_to_add
 * @param int $priority
 * @param int $accepted_args
 * @throws Exception
 */
function bpfw_add_check(string $tag, string|null $contextName, callable $function_to_add, int $priority = 10, int $accepted_args = 1): void
{

    if ($accepted_args < 1) throw new Exception("filter needs >= 1 Parameter");

    global $bpfw_actions;
    $bpfw_actions->add_action("check_" . $tag, $contextName, $function_to_add, $priority, $accepted_args);
}

/**
 * execute previously registered Filter
 * @param string $tag
 * @param string|null $contextName
 * @param array $params
 * @param object|null $contextClass
 * @return boolean
 * @throws Exception
 */
function bpfw_do_check(string $tag, string|null $contextName, array $params = array(), object|null $contextClass = null): bool
{
    global $bpfw_actions;
    return $bpfw_actions->do_action("check_" . $tag, $contextName, $params, BpfwActionHandler::TYPE_CHECK, $contextClass);
}

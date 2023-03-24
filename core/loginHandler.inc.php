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
 * @return bool
 */
function isLoginTry(): bool
{
    $usertype = USERTYPE_GUEST;

    if (isset($_SESSION['usertype'])) {
        $usertype = $_SESSION['usertype'];
    }

    if (isset($_SESSION['userdata']->user->type)) {
        $usertype = $_SESSION['userdata']->user->type;
    }

    return getorpost('login_username') != null && getorpost('login_password') != null;
}

$tryLogin = isLoginTry();


/**
 * @param Database $database
 * @param mixed $username
 * @param mixed $password
 * @return array|bool|null
 * @throws Exception
 */
function doUserOrCustomerLogin(Database $database, mixed $username, mixed $password, $hasCustomerTable = false): array|bool|null
{
    $userdata = $database->getUserLoginByAuth($username, $password);

    if ($userdata == null && $hasCustomerTable) {
        try {
            $userdata = $database->getCustomerLoginByAuth($username, $password);

            if ($userdata != null) {
                $userdata["type"] = USERTYPE_CUSTOMER;
                $userdata['userId'] = $userdata["customerId"];
            }
        } catch (Exception $e) {
            echo "Exception " . $e->getMessage() . " - " . $e->getTraceAsString();
            die();
        }
    }
    return $userdata;
}

if (!$tryLogin && isset($_GET['logout'])) {

    killSessionData();
    $_SESSION['activePage'] = "login";
    $usertype = USERTYPE_GUEST;

} else {

    $loginError = "";

    if (getorpost('login_username') != null && getorpost('login_password') == null) {
        $loginError = "Kein Benutzername angegeben";
    }

    if (getorpost('login_username') == null && getorpost('login_password') != null) {
        $loginError = "Kein Passwort angegeben";
    }

    //neu einloggen
    if (getorpost('login_username') != null && getorpost('login_password') != null) {

        $username = getorpost('login_username');
        $password = getorpost('login_password');

        try {
            $database = bpfw_getDb();
        } catch (Exception $e) {
            echo "database fail";
            die();
        }

        try {
            $userdata = doUserOrCustomerLogin($database, $username, $password);
        } catch (Exception $e) {
            echo "Login failed";
            die();
        }

        if ($userdata != null) {

            $_SESSION['usertype'] = $userdata["type"];
            $_SESSION['userdata'] = $userdata;
            $_SESSION['userId'] = $userdata["userId"];
            $loginError = "OK";

        } else {
            $loginError = __("Invalid login");
            killSessionData();
        }

        if (isset($_SESSION['usertype'])) {
            $usertype = $_SESSION['usertype'];
        }
    }
}

if (!bpfw_isLoggedIn()) {
    killSessionData();
    $usertype = USERTYPE_GUEST;
}

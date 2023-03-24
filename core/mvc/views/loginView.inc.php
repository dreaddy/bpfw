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


class LoginView extends DefaultView
{


    /**
     * Summary of renderView
     * @return void
     */
    function renderView(): void
    {

        //  error_reporting(0);

        global $database;
        global $loginError;

        ?>


        <div class="rowElement rowheader">


            <div class="tablecell" style="width: 100%;display: inline-block;">

                <div id="header-headline">
                    <?php echo APP_TITLE; ?>
                </div>


            </div>

        </div>


        <div class="login_background">

            <form id='login' action='<?php echo empty(BASE_URI) ? "/" : BASE_URI; ?>' method='post'
                  accept-charset='UTF-8'>

                <fieldset style="border:0px;">

                    <input type='hidden' name='submitted' id='submitted' value='1'/>

                    <div class="login_wrapper">

                        <?php


                        if (isset($loginError) && $loginError != "OK") {

                            ?>

                            <div class="login_formElement" style="color:red">
                                <?php echo $loginError; ?>
                            </div>

                            <br/>

                            <?php
                        }
                        ?>

                        <div class="login_element ">

                            <div class="input-group">

                        <span class="input-group-addon" id="basic-addon1">
                            <i class="fa fa-user"></i>
                        </span>
                                <input maxlength="50" type="text" class="form-control" placeholder="<?php echo __("Username or Email"); ?>>"
                                       name='login_username' id='login_username' required/>
                            </div>
                            <br/>
                            <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <i class="fa fa-lock"></i>
                        </span>
                                <input maxlength="50" type="password" class="form-control" placeholder="<?php echo __("Password"); ?>"
                                       name='login_password' id='login_password' required/>
                            </div>

                            <div>
                                <label for='login_password'></label>
                            </div>

                            <div class="login_formElement "></div>

                        </div>

                        <div class="login_element ">

                            <div class="login_formElement">
                                <!--  <input type="submit" name="Submit" value="submit" class="btn btn-default">Einloggen</button> -->

                                <input id='Submit' class="btn btn-default button" type='submit' name='Submit'
                                       value='<?php echo __("Login"); ?>'/>
                            </div>

                        </div>
                    </div>

                </fieldset>

            </form>

        </div>

        <div style="color:white"  class="copyright_login">powered by BPFW - <a style="color:white;text-decoration:underline" href="https://bpfw.org">bpfw.org</a></div>

        <?php

    }

}
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


class LoginView extends DefaultView{

    /**
     * Summary of renderView
     * @param BpfoModel $model
     * @return void
     */
    function renderView(): void
    {
        global $database;
        global $loginError;

?>

<style>
    body.login-page.login{
    }
</style>

<div class="loginboxWrap">
    <div class="logo_background">
        <img id='header_img_login' src='<?php echo PARENT_IMGS_URI; ?>loginbild_513_150.jpg' />
    </div>
    <div class="login_background">

        <div class="welcome1"><?php echo __("Welcome back!"); ?></div>
        <div class="welcome2"><?php echo __("Please login to continue"); ?></div>

        <div class="login_wrapper">

            <form id='login' action='<?php echo empty(BASE_URI)?"/":BASE_URI; ?>' method='post' accept-charset='UTF-8'>

                <fieldset style="border:0px;">

                    <input type='hidden' name='submitted' id='submitted' value='1' />
                    <input type='hidden' name="createNewCV" value="0" />

                    <div class="login_element login_part">

                        <div class="input-group">

                            <label for="login_username"><?php echo __("Username or Email"); ?></label>
                            <span class="input-group-addon" id="basic-addon1">
                                <i class="fa fa-user"></i>
                            </span>
                            <input maxlength="50" type="text" class="form-control" placeholder="<?php echo __("Username or Email"); ?>" name='login_username' id='login_username' required />
                        </div>


                        <div class="input-group">

                            <label for="login_password"><?php echo __("Password"); ?></label>
                            <span class="input-group-addon" id="basic-addon1">
                                <i class="fa fa-lock"></i>
                            </span>
                            <input maxlength="50" type="password" class="form-control" placeholder="<?php echo __("Password"); ?>" name='login_password' id='login_password' required />
                        </div>


                    </div>

                    <div class="login_element ">

                        <div class="login_formElement">
                            <!--  <input type="submit" name="Submit" value="submit" class="btn btn-default">Einloggen</button> -->

                            <input id='Submit' class="btn btn-default button" type='submit' name='Submit' value='<?php echo __("Login"); ?>' style="margin-bottom:30px;" />
                           
                        </div>

                    </div>

                    <?php
                    if(isset($loginError) && $loginError != "OK")
                    {
                    ?>
                    
                        <div class="login_formElement loginError">
                            <?php echo $loginError; ?>
                        </div>

                    <?php
                    }
                    ?>

                </fieldset>

            </form>

        </div>

    </div>

    <div class="copyright_login">powered by BPFW - <a href="https://bpfw.org">bpfw.org</a></div>

</div>










<?php

    }

}
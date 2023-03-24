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

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class UserControl extends DefaultControl
{

    /**
     * Summary of $model
     * @var BpfwModel
     */
    var mixed $model = null;


    function __construct($model)
    {
        parent::__construct($model);
        $this->model = $model;
    }


    function handleAjaxCommand(string $command): void
    {

        global $database;

        if ($command == "createMailSignature") {
            echo $this->model->generateMailSignature($_GET["id"]);
        } else {
            parent::handleAjaxCommand($command);
        }
    }


    /**
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    function handleActions(): void
    {

        //  $userToResetPassword = new UserModel();


        if (!bpfw_isAjax() && isset($_GET["resetPassword"]) && is_numeric($_GET["resetPassword"])) {

            $usermodel = new UserModel();

            $user = $usermodel->GetEntry($_GET["resetPassword"]);


            if (bpfw_getUserId() == $user->userId) {
                bpfw_error_add("das eigene Passwort bitte manuell setzen");
            } else {

                $newpass = bpfw_generatePassword();

                $mailAddress = $user->email;
                $topic = "Neues Passwort für den Administrationsbereich";


                $text = "Hiermit erhalten Sie ein (neues) Passwort für den internen Bereich<br><br>";
                $text .= "Benutzername: " . $user->username . "<br>";
                $text .= "Neues Passwort: $newpass<br><br>";
                $text .= "Mit freundlichen Grüßen<br>";
                $text .= "Christian Eckstein<br>";

                $mail = bpfw_create_mailer();


                $mail->addAddress($mailAddress);
                $mail->Subject = $topic;
                // $mail->isHTML(true);
                $mail->Body = $text;
                $mail->CharSet = "UTF-8";
                // $mail->AddAttachment($attachmentUrl);

                //            $mail->AddAttachment( $attachmentUrl, 'Seminarunterlagen.pdf', 'base64', 'application/pdf' );

                //   $mail->addStringAttachment( $pdfData, 'Seminarunterlagen.pdf', 'base64', 'application/pdf' );
                //     var_dump($user->GetKeyValueArray());
                if ($mail->send()) {

                    $user->password = $newpass;
                    $kva = $user->GetKeyValueArray(false);
                    // var_dump($kva);
                    $usermodel->DbUpdate($kva);

                    bpfw_error_add("Neues Passwort setzen und Senden der EMail erfolgreich");

                } else {
                    bpfw_error_add("Fehler beim senden der EMail, Vorgang wurde abgebrochen");
                }


            }

            /*$user = new DbModelEntry(new UserModel);
            $user->DbLoadObjectByPrimaryKey($_GET["resetPassword"]);
            echo "password reset";*/

        }

        parent::handleActions();

    }

}
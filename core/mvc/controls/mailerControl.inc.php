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
 * ajax commands - used by pdf generator
 *
 * used by pdf Generator
 *
 * @version 1.0
 * @author torst
 */
// used by pdf Generator
class MailerControl extends DefaultControl
{


    function handleAjaxCommand(string $command): void
    {

        global $database;

        if ($command == "testmail") {

            $mailbox = bpfw_createModelByName("maileroutbox");

            $text = $_POST["txt"];

            $userdata = bpfw_getUser();
            // var_dump($userdata);
            $salutation = bpfw_getSalutationFull($userdata["salutation"], $userdata["lastname"]); // ((SALUTATION_MALE == $payerInvoice->salutation)?"Sehr geehrter":"Sehr geehrte")    . " " . $payerInvoice->salutation . " " . $payerInvoice->lastname;

            $text = str_replace("{{{newsletter_Anrede}}}", $salutation, $text);
            $text = str_replace("{{{newsletter_Salutation}}}", $salutation, $text);
            $text = str_replace("{{{newsletter_Unsubscribe_link}}}", bpfw_get_unsubscribe_link("user", bpfw_getUserId()), $text);

            $sender = new BpfwMailAddress(bpfw_loadSetting(SETTING_EMAIL_ABSENDER), bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME), bpfw_getUserId());

            $title = $_POST["title"];
            $rcpt = $_POST["rcpt"];
            $success = bpfw_sendmail_with_mailer($title, $text, new BpfwMailAddress($rcpt), null, null, $sender, array());

            /*
            $values = array(
                "creatorUserId"=>bpfw_getUserId(),
                "userId"=>bpfw_getUserId(),
                "customerId"=>-1,
                "sender"=>bpfw_loadSetting(SETTING_EMAIL_ABSENDER),
                "sender_name"=>bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME),
                "rcpt"=>$_POST["rcpt"],
                "bcc"=>"",
                "cc"=>"",
                "status"=>0,
                "title"=>$_POST["title"],
                "text"=>$text,
                "box"=>"outbox",
                "created"=>date("Y-m-d H:i:s")
                );

            $mailbox->DbInsert($values, null, true);
            */

            if ($success) {
                echo "Testmail an " . $_POST["rcpt"] . " generiert und im Postausgang gespeichert";
            } else {
                echo "Fehlgeschlagen";
            }


        } else if ($command == "createNewsletterInOutbox") {


            $audience = getorpost("audience");
            $userids = json_decode(getorpost("userids"));
            $subject = getorpost("mailtitle");
            $text = getorpost("mailtext");


            $mailbox = bpfw_createModelByName("maileroutbox"); // new MailerworkerModel();


            // basisdaten einfügen in extra tabelle (mailtext und titel)

            $userdata = array();

            if ($audience == RCPT_ADVISOR || $audience == RCPT_USER) {
                $advmodel = bpfw_createModelByName("user");
            }

            $text_raw = $text;

            foreach ($userids as $id) {

                $datamodel = null;
                /** @var BpfwModel $userdata */
                $userdata = null;

                $customerid = -1;
                $userid = -1;


                if ($audience == RCPT_ADVISOR || $audience == RCPT_USER) {
                    $datamodel = bpfw_createModelByName("user");
                    $userdata = $datamodel->DbSelectSingleOrNullByKey($id);
                    $userid = $id;

                    $text = str_replace("{{{newsletter_Unsubscribe_link}}}", bpfw_get_unsubscribe_link("user", $userid), $text_raw);
                } else {
                    $datamodel = bpfw_createModelByName("customer");
                    $userdata = $datamodel->DbSelectSingleOrNullByKey($id);
                    $customerid = $id;
                    $text = str_replace("{{{newsletter_Unsubscribe_link}}}", bpfw_get_unsubscribe_link("customer", $customerid), $text_raw);
                }


                if (empty($userdata) || empty($userdata["email"])) continue;


                $salutation = bpfw_getSalutationFull($userdata["salutation"], $userdata["lastname"]); // ((SALUTATION_MALE == $payerInvoice->salutation)?"Sehr geehrter":"Sehr geehrte")    . " " . $payerInvoice->salutation . " " . $payerInvoice->lastname;

                $rcptusername = $userdata["lastname"] . ", " . $userdata["firstname"];


                $text = str_replace("{{{newsletter_Anrede}}}", $salutation, $text);
                $text = str_replace("{{{newsletter_Salutation}}}", $salutation, $text);

                $email = $userdata["email"];

                // $email = "tlueders@gmx.de"; // testing, option?

                $sender = new BpfwMailAddress(bpfw_loadSetting(SETTING_EMAIL_ABSENDER), bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME), bpfw_getUserId());

                $title = $subject;
                $rcpt = $email;
                $success = bpfw_sendmail_with_mailer($title, $text, new BpfwMailAddress($rcpt, $rcptusername, $id, $audience == "advisor" ? "user" : "customer"), null, null, $sender, array());

                /*
                $values = array(
                    "creatorUserId"=>bpfw_getUserId(),
                    "userId"=>$userid,
                    "customerId"=>$customerid,
                    "sender"=>bpfw_loadSetting(SETTING_EMAIL_ABSENDER),
                    "sender_name"=>bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME),
                    "rcpt"=>$email,
                    "bcc"=>"",
                    "cc"=>"",
                    "status"=>0,
                    "title"=>$subject,
                    "text"=>$text,
                    "box"=>"outbox",
                    "created"=>date("Y-m-d H:i:s")
                    );

                $mailbox->DbInsert($values, null, true);*/

            }


            echo "mails wurden in Outbox gespeichert und können gesendet werden";

        } else {
            parent::handleAjaxCommand($command);
        }


    }


}
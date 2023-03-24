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



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

const RCPT_USER = "user";
const RCPT_CUSTOMER = "customer";
const RCPT_INTERN = "intern";

/**
 * create a php mailer. you should prefer to use the bpfw_sendmail function if there is no reason againstit
 * @param string $sender
 * @param string $sender_name
 * @param bool $debug
 * @return PHPMailer
 * @throws \PHPMailer\PHPMailer\Exception
 * @throws Exception
 */
function bpfw_create_mailer(string $sender = "", string $sender_name = "", bool $debug = false): PHPMailer
{

    $mail = new PHPMailer();

    if (empty($sender)) {
        $sender = bpfw_loadSetting(SETTING_EMAIL_ABSENDER);
    }

    if (empty($sender_name)) {
        $sender_name = bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME);
    }

    if (bpfw_loadSetting(SETTING_MAILER_TYPE) == "smtp") {

        $mail->isSMTP();
        $mail->Host = bpfw_loadSetting(SETTING_MAILER_SMTP_HOST);
        $mail->SMTPAuth = bpfw_loadSetting(SETTING_MAILER_SMTP_AUTH) != "nein";
        $mail->Username = bpfw_loadSetting(SETTING_MAILER_SMTP_USERNAME);
        $mail->Password = bpfw_loadSetting(SETTING_MAILER_SMTP_PASSWORD);
        $mail->SMTPSecure = bpfw_loadSetting(SETTING_MAILER_SMTP_ENCRYPTION);
        $mail->Port = bpfw_loadSetting(SETTING_MAILER_SMTP_PORT);

    } else if (bpfw_loadSetting(SETTING_MAILER_TYPE) == "phpmailer") {
        $mail->isMail();
    }

    $mail->CharSet = "UTF-8";
    $mail->setFrom($sender, $sender_name); //bpfw_loadSetting(SETTING_EMAIL_ABSENDER), bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME));
    $mail->Debugoutput = "echo";
    $mail->isHTML();

    if ($debug)
        $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;                      // Enable verbose debug output


    return $mail;

}

//$_mailer = null;
//$_mailercheck = "";

/**
 * Summary of bpfw_getMailer
 * @param string $sender
 * @param string $sender_name
 * @param mixed|false $debug
 * @return PHPMailer
 * @throws \PHPMailer\PHPMailer\Exception
 */
function bpfw_get_mailer(string $sender = "", string $sender_name = "", bool $debug = false): PHPMailer
{
    //global $_mailer;
    //global $_mailercheck;
    //$_mailercheck_curr = $sender_name."_".$sender;  // NICHT Cachen, da dann bestehende Mailadressen nicht resettet werden
    //if(empty($_mailer) || $_mailercheck != $_mailercheck_curr){
    //}
    //$_mailercheck = $_mailercheck_curr;
    return bpfw_create_mailer($sender, $sender_name, $debug);
}


abstract class BpfwMailAttachmentInterface
{

    var string $encoding = 'base64';
    var string $doctype = "";
    var string $fullPath;
    var string $attachmentName; // autodetect

    /**
     * @return BpfwBinaryMailAttachment|BpfwMailAttachmentInterface|null
     */
    abstract function getSavableData(): BpfwBinaryMailAttachment|BpfwMailAttachmentInterface|null;

    /**
     * Summary of addAttachment
     * @param PHPMailer $mailer
     */
    abstract function addAttachmentToMail(PHPMailer $mailer);


}

class BpfwMailAttachment extends BpfwMailAttachmentInterface
{

    var bool $storeOnServer;

    function __construct(string $fullPath, string $attachmentName, bool $storeOnServer, string $encoding = 'base64', string $doctype = '')
    {


        $this->storeOnServer = $storeOnServer;

        $this->fullPath = $fullPath;
        $this->attachmentName = $attachmentName;
        $this->encoding = $encoding;
        $this->doctype = $doctype;

        if ($this->storeOnServer) {

            $this->fullPath = OUTBOX_ATTACHMENTS . time() . "_" . $attachmentName;


            $exploded = explode("/", $this->fullPath);
            array_pop($exploded);
            $directoryPathOnly = implode(DIRECTORY_SEPARATOR, $exploded);


            if (!file_exists($directoryPathOnly)) {
                mkdir($directoryPathOnly, 0775, true);
            }


            copy($fullPath, $this->fullPath);

        }


    }

    /**
     * @return BpfwMailAttachmentInterface
     */
    function getSavableData(): BpfwMailAttachmentInterface
    {

        // defined('OUTBOX_ATTACHMENTS')||define('OUTBOX_ATTACHMENTS', APP_BASE_PATH."/uploads/outbox_attachments/" );

        return $this;
    }

    /**
     * Summary of addAttachment
     * @param PHPMailer $mailer
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \PHPMailer\PHPMailer\Exception
     */
    function addAttachmentToMail(PHPMailer $mailer)
    {
        $mailer->addAttachment($this->fullPath, $this->attachmentName, $this->encoding, $this->doctype);
    }


}


class BpfwBinaryMailAttachment extends BpfwMailAttachmentInterface
{

    var mixed $output;
    var bool $storeOnServer;

    function __construct(mixed $output, $attachmentName, $encoding = 'base64', $doctype = '', $storeOnServer = true)
    {
        $this->output = $output;


        if ($storeOnServer) {

            if (!file_exists(UPLOADS_PATH)) {
                mkdir(UPLOADS_PATH, 0775, true);
            }


            $this->fullPath = OUTBOX_ATTACHMENTS . time() . "_" . $attachmentName;


            $exploded = explode(DIRECTORY_SEPARATOR, $this->fullPath);
            array_pop($exploded);
            $directoryPathOnly = implode(DIRECTORY_SEPARATOR, $exploded);
            // echo $directoryPathOnly;
            if (!file_exists($directoryPathOnly)) {
                mkdir($directoryPathOnly, 0775, true);
            }


            file_put_contents($this->fullPath, $output);

        }


        $this->attachmentName = $attachmentName;
        $this->encoding = $encoding;
        $this->doctype = $doctype;
        $this->storeOnServer = $storeOnServer;
    }

    /**
     * @return BpfwBinaryMailAttachment|BpfwMailAttachmentInterface|null
     */
    function getSavableData(): BpfwBinaryMailAttachment|BpfwMailAttachmentInterface|null
    {

        if ($this->storeOnServer) {

            $copy = clone($this);

            unset($copy->output); // do not save binary data in database. File has been saved locally
            return $copy;

        } else {
            return null;
        }

    }


    /**
     * Summary of addAttachment
     * @param PHPMailer $mailer
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \PHPMailer\PHPMailer\Exception
     */
    function addAttachmentToMail(PHPMailer $mailer)
    {
        if (!$this->storeOnServer) {
            $mailer->addStringAttachment($this->output, $this->attachmentName, $this->encoding, $this->doctype);
        } else {
            $mailer->addAttachment($this->fullPath, $this->attachmentName, $this->encoding, $this->doctype);
        }
    }

}


/**
 * save a mail to the outbox. unless $sendDirectly is true it is sent when the next sent interval is triggered (cronjob or button)
 * @param string $subject
 * @param string $text
 * @param $to
 * @param null $cc
 * @param null $bcc
 * @param null $from
 * @param array $attachments
 * @param boolean $sendDirectly
 * @param boolean $debug
 * @param bool $testmode
 * @return bool
 * @throws Exception
 */
function bpfw_sendmail_with_mailer(string $subject, string $text, $to, $cc = null, $bcc = null, $from = null, array $attachments = array(), bool $sendDirectly = false, bool $debug = false, bool $testmode = false): bool
{

    return bpfw_createModelByName("maileroutbox")->createMail($subject, $text, $to, $cc, $bcc, $from, $attachments, $sendDirectly, $debug, $testmode);

}

/**
 * save a mail in the outbox and send it without delay
 * @param string $subject
 * @param string $text
 * @param $to
 * @param null $cc
 * @param null $bcc
 * @param null $from
 * @param array $attachments
 * @param boolean $debug
 * @param bool $testmode
 * @return bool
 * @throws Exception
 */
function bpfw_sendmail_with_mailer_send_directly(string $subject, string $text, $to, $cc = null, $bcc = null, $from = null, array $attachments = array(), bool $debug = false, bool $testmode = false): bool
{

    return bpfw_createModelByName("maileroutbox")->createMail($subject, $text, $to, $cc, $bcc, $from, $attachments, true, $debug, $testmode);

}


$_mailerror = "";
/**
 * get the last error that occured when sending mails
 * @return string
 */
function bpfw_get_last_mailerror(): string
{
    global $_mailerror;
    return $_mailerror;
}

class BpfwMailAddress
{

    var ?string $address;
    var ?string $name = "";
    var ?int $bpfwId;
    var string $usertype;

    /**
     * Summary of __construct
     * @param ?string $address
     * @param ?string $name
     * @param int|null $bpfwId
     * @param string $usertype modelclassname where the bpfwid is from like "user" or "customer"
     */
    function __construct(?string $address = null, ?string $name = "", int $bpfwId = null, string $usertype = "user")
    {
        $this->address = $address;
        if($name != null)
        {
            $this->name = $name;
        }else{
            $this->name = $address;
        }

        $this->usertype = $usertype;
        $this->bpfwId = $bpfwId;
    }

    function set($class): void
    {
        $this->address = $class->address;
        $this->name = $class->name;
        $this->usertype = $class->usertype;
        $this->bpfwId = $class->bpfwId;
    }

    /**
     * Summary of getUserdata
     * @return array|null
     * @throws Exception
     */
    function getUserdata(): ?array
    {

        if (!empty($this->usertype)) {
            $model = bpfw_createModelByName($this->usertype);
            return $model->DbSelectSingleOrNullByKey($this->bpfwId);
        }

        return null;

    }
}

/**
 * Send a mail through the system. Not saved in Outbox.
 * @param string $subject
 * @param string $text
 * @param BpfwMailAddress|$to |BpfwMailAddress[] $to
 * @param BpfwMailAddress|null $cc |BpfwMailAddress[]||null $cc
 * @param BpfwMailAddress|null $bcc |BpfwMailAddress[]||null $bcc
 * @param BpfwMailAddress|null $from |null $from
 * @param array|BpfwMailAttachmentInterface $attachments |BpfwMailAttachmentInterface[]||null $attachments
 * @param boolean $debug
 * @return bool
 * @throws \PHPMailer\PHPMailer\Exception
 * @throws Exception
 */
function bpfw_sendmail(string $subject, string $text, BpfwMailAddress $to, BpfwMailAddress $cc = null, BpfwMailAddress $bcc = null, BpfwMailAddress $from = null, array|BpfwMailAttachmentInterface $attachments = array(), bool $debug = false): bool
{


    global $_mailerror;


    if (empty($from)) {
        $sender = bpfw_loadSetting(SETTING_EMAIL_ABSENDER);
    } else {
        $sender = $from->address;
    }

    if (empty($from) || empty($from->name)) {
        $sender_name = bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME);
    } else {
        $sender_name = $from->name;
    }

    if ($sender == null) $sender = "";
    if ($sender_name == null) $sender_name = "";
    $mailer = bpfw_get_mailer($sender, $sender_name, $debug);

    $mailer->clearAddresses();
    $mailer->clearBCCs();
    $mailer->clearCCs();

    if (empty($to)) {
        throw new Exception("sendmail: to is required");
    }

    if (!is_array($to)) {
        $mailer->addAddress($to->address, $to->name);
    } else {
        foreach ($to as $to_element) {
            $mailer->addAddress($to_element->address, $to_element->name);
        }
    }

    if (!empty($cc)) {

        if (!is_array($cc)) {
            $mailer->addCC($cc->address, $cc->name);
        } else {
            foreach ($cc as $cc_element) {
                $mailer->addCC($cc_element->address, $cc_element->name);
            }
        }

    }

    if (!empty($bcc)) {

        if (!is_array($bcc)) {
            $mailer->addBCC($bcc->address, $bcc->name);
        } else {
            foreach ($bcc as $bcc_element) {
                $mailer->addBCC($bcc_element->address, $bcc_element->name);
            }
        }

    }

    $mailer->Subject = $subject;
    $mailer->Body = $text;

    if (!empty($attachments)) {

        if (!is_array($attachments)) {
            $attachments = array($attachments);
        }

        foreach ($attachments as $attachment) {
            // var_dump($attachment);
            $attachment->addAttachmentToMail($mailer);
        }

    }

    $response = true;


    $response = $mailer->send();

    if (!$response) {  // if the user gets an error, do not save into outbox
        return $response;
    }

    $_mailerror = $mailer->ErrorInfo;

    return $response;

}

/**
 * Summary of bpfw_sendmail
 * @param string $subject
 * @param string $text
 * @param string $address
 * @param string $cc
 * @param string $bcc
 * @param string $rcpt_name
 * @param string $sender
 * @param string $sender_name
 * @param string $cc_name
 * @param BpfwMailAttachmentInterface[] $attachments
 * @param boolean $debug
 * @return bool
 * @throws \PHPMailer\PHPMailer\Exception
 */
function bpfw_sendmail_old(string $subject, string $text, string $address, string $cc = "", string $bcc = "", string $rcpt_name = "", string $sender = "", string $sender_name = "", string $cc_name = "", array $attachments = array(), bool $debug = false): bool
{

    global $_mailerror;

    $mailer = bpfw_get_mailer($sender, $sender_name, $debug);

    $mailer->clearAddresses();
    $mailer->clearBCCs();
    $mailer->clearCCs();

    $mailer->addAddress($address, $rcpt_name);

    if (!empty($cc)) {
        $mailer->addCC($cc, $cc_name);
    }

    if (!empty($bcc)) {
        $mailer->addBCC($bcc);
    }

    $mailer->Subject = $subject;
    $mailer->Body = $text;

    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {

            $attachment->addAttachmentToMail($mailer);

        }
    }

    $response = true;


    $response = $mailer->send();

    if (!$response) {  // if the user gets an error, saving in outbox will be confusing
        return $response;
    }

    $_mailerror = $mailer->ErrorInfo;

    return $response;

    /*
    echo "<pre>";
    print_r(bpfw_loadSettings());
    print_r($mailer);echo "ok123";
    echo "</pre>";
    die();*/

}


/**
 * Search for the mail address in users and customers and create a BpfwMailAddress with the correct user first and lastname
 * @param mixed $email
 * @param array|mixed $types array with user and/or customer
 * @return BpfwMailAddress|null
 * @throws Exception
 */
function bpfw_findMailAddress(mixed $email, array $types = array("user", "customer")): ?BpfwMailAddress
{

    if (empty($email)) return null;

    // throw new Exception("implement");

    if (in_array("user", $types)  && bpfw_modelExists("user")) {

        try {
            $userrows = bpfw_getDb()->makeSelect("select userId, lastname, firstname from user where email like '" . bpfw_getDb()->escape_string($email) . "'");
        } catch (Exception $e) {
        }

        if (!empty($userrows)) {

            $userdetail = current($userrows);
            return new BpfwMailAddress($email, $userdetail["lastname"] . ", " . $userdetail["firstname"], $userdetail["userId"], "user");

        }

    }

    if (in_array("customer", $types) && bpfw_modelExists("customer")) {

        try {
            $customer = bpfw_getDb()->makeSelect("select customerId, lastname, firstname from customer where email like '" . bpfw_getDb()->escape_string($email) . "'");
        } catch (Exception $e) {
        }

        if (!empty($userrows)) {

            $userdetail = current($userrows);
            return new BpfwMailAddress($email, $userdetail["lastname"] . ", " . $userdetail["firstname"], $userdetail["customerId"], "customer");

        }

    }

    return new BpfwMailAddress($email, '', null, "user");

}


/**
 * returns default Mail From Adress as bpfw class
 * @return BpfwMailAddress
 * @throws Exception
 */
function bpfw_getDefaultMailFrom(): BpfwMailAddress
{
    return new BpfwMailAddress(bpfw_loadSetting(SETTING_EMAIL_ABSENDER), bpfw_loadSetting(SETTING_EMAIL_ABSENDER_NAME), bpfw_getUserId(), "user");
}

/**
 * returns default Mail internal Adress as bpfw class
 * @return BpfwMailAddress
 * @throws Exception
 */
function bpfw_getDefaultMailIntern(): BpfwMailAddress
{
    return new BpfwMailAddress(bpfw_loadSetting(SETTING_EMAIL_INTERN), bpfw_loadSetting(SETTING_EMAIL_INTERN), null, "user");
}


/**
 * returns default Mail bcc Adress as bpfw class
 * @return BpfwMailAddress
 * @throws Exception
 */
function bpfw_getDefaultMailBcc(): BpfwMailAddress
{
    return new BpfwMailAddress(bpfw_loadSetting(SETTING_EMAIL_INTERN), bpfw_loadSetting(SETTING_EMAIL_BCC_EMPFAENGER), null, "user");
}


/**
 * adds htaccess if not existing to folders that should be unaccessible by the browser
 */
function bpfw_check_htaccess_files(): void
{

    if (!empty(OUTBOX_ATTACHMENTS)) { // only accessible through php download

        if (!file_exists(OUTBOX_ATTACHMENTS)) {

            mkdir(OUTBOX_ATTACHMENTS, 0600, true);

        }

        $filename = OUTBOX_ATTACHMENTS . ".htaccess";

        if (!file_exists($filename)) {

            $data = "Deny from all";

            file_put_contents($filename, $data);

        }

    }

    if (!empty(VIDEOCHAT_ATTACHMENTS)) {

        if (!file_exists(VIDEOCHAT_ATTACHMENTS)) {

            mkdir(VIDEOCHAT_ATTACHMENTS, 0600, true);

        }

    }

}
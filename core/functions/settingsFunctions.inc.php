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

const SETTING_NEXT_INVOICE_NR = "SETTING_NEXT_INVOICE_NR";
const SETTING_NEXT_CREDIT_NR = "SETTING_NEXT_CREDIT_NR";
const SETTING_EMAIL_INTERN = "SETTING_EMAIL_INTERN";
const SETTING_EMAIL_BCC_EMPFAENGER = "SETTING_EMAIL_BCC_EMPFAENGER";
const SETTING_EMAIL_ABSENDER = "SETTING_EMAIL_ABSENDER";
const SETTING_EMAIL_ABSENDER_NAME = "SETTING_EMAIL_ABSENDER_NAME";

const SETTING_BACKUP_DIRECTORY = "SETTING_BACKUP_DIRECTORY";
const SETTING_EXTERNAL_CRONJOB_CALL_CODE = "SETTING_EXTERNAL_CRONJOB_CALL_CODE";
const SETTING_UNSUBSCRIBE_CALL_CODE = "SETTING_UNSUBSCRIBE_CALL_CODE";


const SETTING_PLATFORM_SHARE_ADDRESS = "SETTING_PLATFORM_SHARE_ADDRESS";

const SETTING_MAILER_TYPE = "SETTING_MAILER_TYPE";
const SETTING_MAILER_SMTP_HOST = "SETTING_MAILER_SMTP_HOST";
const SETTING_MAILER_SMTP_PORT = "SETTING_MAILER_SMTP_PORT";
const SETTING_MAILER_SMTP_AUTH = "SETTING_MAILER_SMTP_AUTH";
// const SETTING_MAILER_SMTP_SERVER = "SETTING_MAILER_SMTP_SERVER";
const SETTING_MAILER_SMTP_ENCRYPTION = "SETTING_MAILER_SMTP_ENCRYPTION";
const SETTING_MAILER_SMTP_USERNAME = "SETTING_MAILER_SMTP_USERNAME";
const SETTING_MAILER_SMTP_PASSWORD = "SETTING_MAILER_SMTP_PASSWORD";

const SETTING_MAILER_IMAP_MAIL = "SETTING_MAILER_IMAP_MAIL";
const SETTING_MAILER_IMAP_USERNAME = "SETTING_MAILER_IMAP_USERNAME";
const SETTING_MAILER_IMAP_PORT = "SETTING_MAILER_IMAP_PORT";
const SETTING_MAILER_IMAP_PASSWORD = "SETTING_MAILER_IMAP_PASSWORD";
const SETTING_MAILER_IMAP_FOLDER = "SETTING_MAILER_IMAP_FOLDER";
const SETTING_MAILER_IMAP_ENCRYPTION = "SETTING_MAILER_IMAP_ENCRYPTION";
const SETTING_MAILER_IMAP_HOST = "SETTING_MAILER_IMAP_HOST";

const SETTING_DEFAULT_LANGUAGE = "SETTING_DEFAULT_LANGUAGE";


// comboboxwerte setzen
bpfw_addPredefinedOptionsForSetting(SETTING_MAILER_SMTP_ENCRYPTION, array(PHPMailer::ENCRYPTION_STARTTLS, PHPMailer::ENCRYPTION_SMTPS));
bpfw_addPredefinedOptionsForSetting(SETTING_MAILER_IMAP_ENCRYPTION, array("ssl", "keine"));


bpfw_addPredefinedOptionsForSetting(SETTING_MAILER_TYPE, array("phpmailer", "smtp"));
bpfw_addPredefinedOptionsForSetting(SETTING_MAILER_SMTP_AUTH, array("ja", "nein"));



$bpfw_defaultSettings = array(
    SETTING_MAILER_TYPE => "phpmailer",
    SETTING_MAILER_SMTP_HOST => "",
    SETTING_MAILER_SMTP_PORT => 587,
    SETTING_MAILER_SMTP_AUTH => "ja",
    SETTING_MAILER_SMTP_ENCRYPTION => PHPMailer::ENCRYPTION_STARTTLS,
    SETTING_MAILER_SMTP_USERNAME => "",
    SETTING_MAILER_SMTP_PASSWORD => "",
    SETTING_MAILER_IMAP_HOST => "",
    SETTING_MAILER_IMAP_MAIL => "",
    SETTING_MAILER_IMAP_USERNAME => "",
    SETTING_MAILER_IMAP_PORT => 143,
    SETTING_MAILER_IMAP_PASSWORD => "",
    SETTING_MAILER_IMAP_FOLDER => "INBOX",
    SETTING_MAILER_IMAP_ENCRYPTION => "ssl",
    SETTING_EMAIL_ABSENDER => "",
    SETTING_EMAIL_ABSENDER_NAME => "",
    SETTING_EMAIL_BCC_EMPFAENGER => "",
    SETTING_EMAIL_INTERN => "",
    SETTING_BACKUP_DIRECTORY => APP_BASE_PATH . "backups/",
    SETTING_DEFAULT_LANGUAGE => null,
    SETTING_EXTERNAL_CRONJOB_CALL_CODE => bpfw_generatePassword(25, false, "lu"),
    SETTING_UNSUBSCRIBE_CALL_CODE => bpfw_generatePassword(25, false, "lu")
);

try {
    bpfw_addSettingVariables($bpfw_defaultSettings
    );
} catch (Exception $e) {
}


// Settings


/**
 * @var null|SettingsModel $bpfw_settings ;
 */
$bpfw_settings = null;
/*
if(file_exists(APP_MVC_PATH."models/settingsModel.inc.php")){
require_once(APP_MVC_PATH."models/settingsModel.inc.php");
}else{
require_once(BPFW_MVC_PATH."models/settingsModel.inc.php");
}*/


function bpfw_defaultsettings(): array
{
    global $bpfw_defaultSettings;
    return $bpfw_defaultSettings;
}


function bpfw_getDefaultSettings(): ?array
{

    global $bpfw_currentSettings;

    if (!isset($bpfw_currentSettings)) {
        $bpfw_currentSettings = bpfw_defaultsettings();
    }

    return $bpfw_currentSettings;

}

/**
 * @throws Exception
 */
function bpfw_addSettingVariables($keyValueArray): void
{

    $settings = array();

    foreach ($keyValueArray as $key => $value) {

        if (!isset($settings[$key])) {
            $settings[$key] = $value;
        } else {
            throw new Exception("Option Value already Existing: $key -> $value");
        }

    }

    global $bpfw_currentSettings;
    $bpfw_currentSettings = $settings;

}


function bpfw_getPredefinedOptionsForSetting($setting)
{

    global $bpfw_currentSettings_options;

    if (isset($bpfw_currentSettings_options[$setting])) {
        return $bpfw_currentSettings_options[$setting];
    }

    return array();

}

function bpfw_addPredefinedOptionsForSetting($setting, $options): void
{

    global $bpfw_currentSettings_options;

    $bpfw_currentSettings_options[$setting] = array();

    foreach ($options as $option) {
        $bpfw_currentSettings_options[$setting][$option] = $option;
    }

}


/**
 * @throws Exception
 */
function bpfw_addSettingVariable(string $key, string $value): void
{
    bpfw_addSettingVariables(array($key => $value));
}

/**
 * @throws Exception
 */
function bpfw_saveSetting_old($key, $value)
{

    global $_settingsCache;

    global $bpfw_settings;
    if ($bpfw_settings == null) $bpfw_settings = bpfw_createModelByName("settings"); //new SettingsModel();

    $_settingsCache = array();

    $bpfw_settings->DbUpdate(array("settings_key" => new DbSubmitValue("settings_key", $key, $bpfw_settings), "settings_value" => new DbSubmitValue("settings_value", $value, $bpfw_settings)));

    return bpfw_loadSetting($key);

}

$_settingsCache = array();

/**
 * TODO: make more app specific
 * @throws Exception
 */
function bpfw_loadSettings($where = "1")
{
    global $_settingsCache;

    if (isset($_settingsCache[$where])) {
        return $_settingsCache[$where];
    }

    $settings = bpfw_getDefaultSettings();


    $db = bpfw_getDb();

    $results = $db->makeSelect("select * from `" . SETTINGS_TABLENAME . "` where $where limit 1");


    if (!empty($results)) {
        foreach ($results as $row => $values) {
            foreach ($values as $wKey => $value) {
                $settings[$wKey] = $value;
            }
        }
    }

    $_settingsCache[$where] = $settings;

    return $settings;
}


/**
 * @throws Exception
 */
function bpfw_saveSetting($key, $value, $where = 1)
{

    global $_settingsCache;

    $db = bpfw_getDb();

    $results = $db->makeQuery("update `" . SETTINGS_TABLENAME . "` set `$key` = '$value'");

    $_settingsCache = array();

    return bpfw_loadSetting($key, $where);

}


$settings_cache = null;
/**
 * load a setting variable.
 * @throws Exception
 */

/**
 * @param $settingValue string setting variable This is a name of a field in settingsModel. Default settings are also defined as SETTING_ constants in settingsFunctions.inc.php
 * @param $where string additional where clause if you have more than one set of settings
 * @return mixed|null
 * @throws Exception
 */
function bpfw_loadSetting(string $settingValue, string $where = "1"): mixed
{

    global $settings_cache;

    $settings = $settings_cache;

    if (empty($settings))
        $settings = bpfw_loadSettings($where);

    if (empty($settings[$settingValue])) {
        return null;
    }

    return $settings[$settingValue];

    //echo "return ".$settingscache[$where][$key];
    //echo "<br>$key";

    //return $settingscache[$where][$optionname];

}

/**
 * @throws Exception
 */
function bpfw_loadSetting_old($key)
{


    $defaultsettings = bpfw_getDefaultSettings();


    if (!isset($defaultsettings[$key])) {
        die("setting not defined. override bpfw_getSettingskeys first");
    }

    $defaultValue = $defaultsettings[$key];

    global $bpfw_settings;

    if ($bpfw_settings == null) $bpfw_settings = bpfw_createModelByName("settings"); //new SettingsModel();
    $value = $bpfw_settings->DbSelectKeyValueArray("settings_value", "WHERE settings_key = '$key'");

    if (empty($value)) {
        return $defaultValue;
    } else {
        if (count($value) > 1) die("too many Values in bpdfloadSetting");
        return current($value);
    }

}
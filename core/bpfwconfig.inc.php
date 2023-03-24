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



// default Config, you can replace the stuff in your own config file

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

date_default_timezone_set('Europe/Berlin');

$debug=false;

if(isset($_GET['debug']))
{
    $debug = true;
}


// defined('APP_NAME')||define("APP_NAME",     		"bpfwproject");

defined('APP_TITLE')||define("APP_TITLE",     		"Bpfw Project");

// can be overwritten in the app config
defined('STARTING_PAGE')||define("STARTING_PAGE",     		"user");
//defined('CURRENT_LANGUAGE')||define("CURRENT_LANGUAGE",     		"de_De"); // TODO: variable for user? userfield?

defined('BPFW_DEBUG_MODE')||define('BPFW_DEBUG_MODE', $debug);

defined('BASE_URI')||define('BASE_URI', '/');
defined('BASE_PATH')||define('BASE_PATH', getcwd().DIRECTORY_SEPARATOR); // '/.

defined('VENDOR_PATH')||define('VENDOR_PATH', BASE_PATH.'vendor'.DIRECTORY_SEPARATOR);
defined('VENDOR_URI')||define('VENDOR_URI', BASE_URI.'vendor/');

defined('BPFW_BASE_URI')||define('BPFW_BASE_URI', VENDOR_URI.'bpfw/bpfw/');

defined('DB_HOST')||define('DB_HOST', '');
defined('DB_USER')||define('DB_USER', '');
defined('DB_PASSWORD')||define('DB_PASSWORD', '');
defined('DB_DATABASE')||define('DB_DATABASE', '');

defined('USERTYPE_NOT_LOGGEDIN')||define("USERTYPE_NOT_LOGGEDIN",     		"100");
defined('USERTYPE_GUEST')||define("USERTYPE_GUEST",     		"30");
defined('USERTYPE_CUSTOMER')||define("USERTYPE_CUSTOMER",     		"20");
defined('USERTYPE_PREMIUM')||define("USERTYPE_PREMIUM",     		"10");
defined('USERTYPE_CONSULTANT')||define("USERTYPE_CONSULTANT",     		"10");
defined('USERTYPE_ADMIN')||define("USERTYPE_ADMIN",     		"5");
defined('USERTYPE_SUPERADMIN')||define("USERTYPE_SUPERADMIN",     	"0");
defined('USERTYPE_DEVELOPER')||define("USERTYPE_DEVELOPER",     	"0");
defined('USERTYPE_INVALID')||define("USERTYPE_INVALID",     		"-1");

defined('SALUTATION_NEUTRAL')||define("SALUTATION_NEUTRAL",     	"Dear Sir or Madam");
defined('SALUTATION_MALE')||define("SALUTATION_MALE",     		"Mr");
defined('SALUTATION_FEMALE')||define("SALUTATION_FEMALE",     	"Mrs");
defined('SALUTATION_NONE')||define("SALUTATION_NONE",     		"(None)");



defined('NODE_BASE_PATH')||define('NODE_BASE_PATH', BASE_PATH.'node_modules'.DIRECTORY_SEPARATOR);
defined('NODE_BASE_URI')||define('NODE_BASE_URI', BASE_URI.'node_modules/');

//die(dirname(__FILE__));
//echo BASE_PATH;
//die(str_replace(BASE_PATH, '',  dirname(__FILE__)));
defined('BPFW_BASE_PATH')||define('BPFW_BASE_PATH', VENDOR_PATH.'bpfw'.DIRECTORY_SEPARATOR.'bpfw'.DIRECTORY_SEPARATOR);
defined('BPFW_CORE_PATH')||define('BPFW_CORE_PATH', BPFW_BASE_PATH.'core'.DIRECTORY_SEPARATOR);
//defineIfNotDefined('BPFW_MVC_PATH', BPFW_BASE_PATH.'mvc/');
defined('BPFW_MVC_PATH')||define('BPFW_MVC_PATH', BPFW_CORE_PATH.'mvc'.DIRECTORY_SEPARATOR);

defined('BPFW_VIEWS_PATH')||define('BPFW_VIEWS_PATH', BPFW_CORE_PATH.'mvc'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR);
defined('BPFW_VIEWS_PARTS_PATH')||define('BPFW_VIEWS_PARTS_PATH', BPFW_VIEWS_PATH.'parts'.DIRECTORY_SEPARATOR);
defined('BPFW_COMPONENT_PATH')||define('BPFW_COMPONENT_PATH', BPFW_BASE_PATH.'components'.DIRECTORY_SEPARATOR);

defined('BPFW_PDF_PATH')||define('BPFW_PDF_PATH', BPFW_CORE_PATH.'pdf'.DIRECTORY_SEPARATOR);
defined('BPFW_MAIL_PATH')||define('BPFW_MAIL_PATH', BPFW_CORE_PATH.'mails'.DIRECTORY_SEPARATOR);



defined('BPFW_INCLUDE_PATH')||define('BPFW_INCLUDE_PATH', BPFW_CORE_PATH.'includes'.DIRECTORY_SEPARATOR);
defined('BPFW_ADMIN_PATH')||define('BPFW_ADMIN_PATH', BPFW_BASE_PATH.BPFW_INCLUDE_PATH.'admin'.DIRECTORY_SEPARATOR);
defined('BPFW_CONTENTPAGES_PATH')||define('BPFW_CONTENTPAGES_PATH', BPFW_CORE_PATH.BPFW_INCLUDE_PATH.BPFW_ADMIN_PATH.'subPages'.DIRECTORY_SEPARATOR);



defined('BPFW_WWW_URI')||define('BPFW_WWW_URI', BPFW_BASE_URI.'www/');
defined('BPFW_JS_URI')||define('BPFW_JS_URI',  BPFW_WWW_URI.'js/');
defined('BPFW_CSS_URI')||define('BPFW_CSS_URI', BPFW_WWW_URI.'css/');

defined('LIBS_URI')||define('LIBS_URI', BPFW_BASE_URI.'libs/');
defined('LIBS_PATH')||define('LIBS_PATH', BPFW_BASE_PATH."libs".DIRECTORY_SEPARATOR );

defined('BPFW_WWW_PATH')||define('BPFW_WWW_PATH', BPFW_BASE_PATH.'www'.DIRECTORY_SEPARATOR);
defined('BPFW_JS_PATH')||define('BPFW_JS_PATH', BPFW_WWW_PATH.'js'.DIRECTORY_SEPARATOR);
defined('BPFW_CSS_PATH')||define('BPFW_CSS_PATH', BPFW_WWW_PATH.'css'.DIRECTORY_SEPARATOR);

defined('APPS_ROOT_PATH')||define('APPS_ROOT_PATH', BASE_PATH.'apps'.DIRECTORY_SEPARATOR);
defined('APPS_ROOT_URI')||define('APPS_ROOT_URI', BASE_URI.'apps/');

defined('APP_BASE_URI')||define('APP_BASE_URI', APPS_ROOT_URI.APP_NAME.'/');


defined('APP_BASE_PATH')||define('APP_BASE_PATH', APPS_ROOT_PATH.APP_NAME.DIRECTORY_SEPARATOR);
defined('APP_WWW_URI')||define('APP_WWW_URI', APP_BASE_URI.'www/');
defined('APP_IMGS_URI')||define('APP_IMGS_URI', APP_WWW_URI.'imgs/');
defined('APP_FONTS_URI')||define('APP_FONTS_URI', APP_WWW_URI.'fonts/');
defined('APP_JS_URI')||define('APP_JS_URI', APP_WWW_URI.'js/');
defined('APP_CSS_URI')||define('APP_CSS_URI', APP_WWW_URI.'css/');

defined('APP_NODE_PATH')||define('APP_NODE_PATH', APP_BASE_PATH.'node_modules'.DIRECTORY_SEPARATOR);
defined('APP_NODE_URI')||define('APP_NODE_URI', APP_BASE_PATH.'node_modules'.DIRECTORY_SEPARATOR);

defined('APP_MVC_PATH')||define('APP_MVC_PATH', APP_BASE_PATH.'mvc'.DIRECTORY_SEPARATOR);
defined('APP_VIEWS_PATH')||define('APP_VIEWS_PATH', APP_BASE_PATH.'mvc'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR);
defined('APP_VIEWS_PARTS_PATH')||define('APP_VIEWS_PARTS_PATH', APP_VIEWS_PATH.'parts'.DIRECTORY_SEPARATOR);

defined('APP_PDF_PATH')||define('APP_PDF_PATH', APP_BASE_PATH.'pdf'.DIRECTORY_SEPARATOR);
defined('APP_MAIL_PATH')||define('APP_MAIL_PATH', APP_BASE_PATH.'mails'.DIRECTORY_SEPARATOR);


defined('APP_NEWSLETTER_PATH')||define('APP_NEWSLETTER_PATH', APP_BASE_PATH.'newsletter'.DIRECTORY_SEPARATOR);


defined('APP_WWW_PATH')||define('APP_WWW_PATH', APP_BASE_PATH.'www'.DIRECTORY_SEPARATOR);

defined('APP_IMGS_PATH')||define('APP_IMGS_PATH', APP_WWW_PATH.'imgs'.DIRECTORY_SEPARATOR);
defined('APP_JS_PATH')||define('APP_JS_PATH', APP_WWW_PATH.'js'.DIRECTORY_SEPARATOR);
defined('APP_CSS_PATH')||define('APP_CSS_PATH', APP_WWW_PATH.'css'.DIRECTORY_SEPARATOR);

defined('APP_FONTS_PATH')||define('APP_FONTS_PATH', APP_WWW_PATH.'fonts'.DIRECTORY_SEPARATOR);

defined('PARENT_NAME')||define('PARENT_NAME', APP_NAME);

// parent start
defined('PARENT_BASE_URI')||define('PARENT_BASE_URI', APPS_ROOT_URI.PARENT_NAME.'/');
defined('PARENT_BASE_PATH')||define('PARENT_BASE_PATH', APPS_ROOT_PATH.PARENT_NAME.DIRECTORY_SEPARATOR);

defined('PARENT_WWW_URI')||define('PARENT_WWW_URI', PARENT_BASE_URI.'www/');
defined('PARENT_IMGS_URI')||define('PARENT_IMGS_URI', PARENT_WWW_URI.'imgs/');
defined('PARENT_JS_URI')||define('PARENT_JS_URI', PARENT_WWW_URI.'js/');
defined('PARENT_CSS_URI')||define('PARENT_CSS_URI', PARENT_WWW_URI.'css/');


defined('PARENT_NODE_PATH')||define('PARENT_NODE_PATH', PARENT_BASE_PATH.'node_modules'.DIRECTORY_SEPARATOR);
defined('PARENT_NODE_URI')||define('PARENT_NODE_URI', PARENT_BASE_PATH.'node_modules'.DIRECTORY_SEPARATOR);

defined('PARENT_MVC_PATH')||define('PARENT_MVC_PATH', PARENT_BASE_PATH.'mvc'.DIRECTORY_SEPARATOR);
defined('PARENT_VIEWS_PATH')||define('PARENT_VIEWS_PATH', PARENT_BASE_PATH.'mvc'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR);
defined('PARENT_VIEWS_PARTS_PATH')||define('PARENT_VIEWS_PARTS_PATH', PARENT_VIEWS_PATH.'parts'.DIRECTORY_SEPARATOR);

defined('PARENT_PDF_PATH')||define('PARENT_PDF_PATH', PARENT_BASE_PATH.'pdf'.DIRECTORY_SEPARATOR);
defined('PARENT_MAIL_PATH')||define('PARENT_MAIL_PATH', PARENT_BASE_PATH.'mails'.DIRECTORY_SEPARATOR);


defined('PARENT_NEWSLETTER_PATH')||define('PARENT_NEWSLETTER_PATH', PARENT_BASE_PATH.'newsletter'.DIRECTORY_SEPARATOR);


defined('PARENT_WWW_PATH')||define('PARENT_WWW_PATH', PARENT_BASE_PATH.'www'.DIRECTORY_SEPARATOR);

defined('PARENT_IMGS_PATH')||define('PARENT_IMGS_PATH', PARENT_WWW_PATH.'imgs'.DIRECTORY_SEPARATOR);
defined('PARENT_JS_PATH')||define('PARENT_JS_PATH', PARENT_WWW_PATH.'js'.DIRECTORY_SEPARATOR);
defined('PARENT_CSS_PATH')||define('PARENT_CSS_PATH', PARENT_WWW_PATH.'css'.DIRECTORY_SEPARATOR);
// parent end



defined('UPLOADS_PATH')||define('UPLOADS_PATH', APP_BASE_PATH."uploads".DIRECTORY_SEPARATOR );
defined('LOGS_PATH')||define('LOGS_PATH', APP_BASE_PATH."logs".DIRECTORY_SEPARATOR );

defined('UPLOADS_URI')||define('UPLOADS_URI', APP_BASE_URI."uploads/" );


defined('TEMPLATES_PATH')||define('TEMPLATES_PATH', APP_BASE_PATH."templates".DIRECTORY_SEPARATOR );
defined('TEMPLATES_URI')||define('TEMPLATES_URI', APP_BASE_URI."templates/" );

defined('OUTBOX_ATTACHMENTS')||define('OUTBOX_ATTACHMENTS', APP_BASE_PATH."uploads".DIRECTORY_SEPARATOR."outbox_attachments".DIRECTORY_SEPARATOR );
defined('OUTBOX_ATTACHMENTS_URI')||define('OUTBOX_ATTACHMENTS_URI', APP_BASE_URI."uploads/outbox_attachments/" );

defined('VIDEOCHAT_ATTACHMENTS')||define('VIDEOCHAT_ATTACHMENTS', APP_BASE_PATH."temp".DIRECTORY_SEPARATOR."videochat_attachments".DIRECTORY_SEPARATOR );
defined('VIDEOCHAT_ATTACHMENTS_URI')||define('VIDEOCHAT_ATTACHMENTS_URI', APP_BASE_URI."temp/videochat_attachments/" );

defined('TEMP_PATH')||define('TEMP_PATH', APP_BASE_PATH."temp".DIRECTORY_SEPARATOR );
defined('TEMP_URI')||define('TEMP_URI', APP_BASE_URI."temp/" );

defined('APP_COMPONENT_PATH')||define('APP_COMPONENT_PATH', APP_BASE_PATH.'components'.DIRECTORY_SEPARATOR);
defined('PARENT_COMPONENT_PATH')||define('PARENT_COMPONENT_PATH', PARENT_BASE_PATH.'components'.DIRECTORY_SEPARATOR);



defined('DEFAULT_FULLSIZE_HEADERBAR')||define('DEFAULT_FULLSIZE_HEADERBAR', true );


// you can't login into this theme and just use it as a template
defined('TEMPLATE_THEME')||define('TEMPLATE_THEME', false);



defined("DEBUG_SQL") || define('DEBUG_SQL', false);


defined("DEBUG") || define('DEBUG', true);



defined("SETTINGS_TABLENAME") || define('SETTINGS_TABLENAME', "appsettings");


defined("SAVE_CREATED_MODIFIED") || define('SAVE_CREATED_MODIFIED', false);

defined("LOG_DATAACTIONS_TABLE") || define('LOG_DATAACTIONS_TABLE', false);

defined("LOG_CHANGES") || define('LOG_CHANGES', false);




if(DEBUG){

    error_reporting(E_ALL);
    ini_set("display_errors", 1);

}



defined("DEFAULT_LANGUAGE") || define('DEFAULT_LANGUAGE', "en");

defined("DEFAULT_SHOW_COLON_IN_FORM") || define('DEFAULT_SHOW_COLON_IN_FORM', true);

defined("ALLOW_MULTIPLE_APP_CREATION") || define('ALLOW_MULTIPLE_APP_CREATION', false);
defined("MULTIPLE_APP_CREATION_PASSWORD") || define('MULTIPLE_APP_CREATION_PASSWORD', null);

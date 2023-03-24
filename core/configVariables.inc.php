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
 * Class1 short summary.
 *
 * Class1 description.
 *
 * @version 1.0
 * @author torst
 */
class Config
{

    public string $APP_NAME;
    public string $PARENT_NAME;
    public string $APP_TITLE;
    public string $STARTING_PAGE;
    public bool $BPFW_DEBUG_MODE;
    public string $BASE_URI;

    //public $CURRENT_LANGUAGE;
    public string $BASE_PATH;
    public string $VENDOR_PATH;
    public string $VENDOR_URI;
    public string $LIBS_URI;
    public string $LIBS_PATH;
    public string $DB_HOST;
    public string $DB_USER;
    public string $USERTYPE_GUEST;
    public string $DB_DATABASE;

    // public $DB_PASSWORD;
    public string $USERTYPE_CUSTOMER;
    public string $USERTYPE_PREMIUM;
    public string $USERTYPE_CONSULTANT;
    public string $USERTYPE_ADMIN;
    public string $USERTYPE_SUPERADMIN;
    public string $USERTYPE_INVALID;
    public string $SALUTATION_NEUTRAL;
    public string $SALUTATION_MALE;
    public string $SALUTATION_FEMALE;
    public string $SALUTATION_NONE;
    public string $BPFW_BASE_PATH;
    public string $BPFW_CORE_PATH;
    public string $BPFW_MVC_PATH;
    public string $BPFW_VIEWS_PATH;
    public string $BPFW_VIEWS_PARTS_PATH;
    public string $BPFW_COMPONENT_PATH;
    public string $BPFW_PDF_PATH;
    public string $BPFW_MAIL_PATH;
    public string $BPFW_INCLUDE_PATH;
    public string $BPFW_ADMIN_PATH;
    public string $BPFW_CONTENTPAGES_PATH;
    public string $BPFW_BASE_URI;
    public string $BPFW_WWW_URI;
    public string $BPFW_JS_URI;
    public string $BPFW_CSS_URI;
    public string $APP_BASE_URI;
    public string $APP_BASE_PATH;
    public string $APP_WWW_URI;
    public string $APP_IMGS_URI;
    public string $APP_FONTS_URI;
    public string $APP_JS_URI;
    public string $APP_CSS_URI;
    public string $APP_MVC_PATH;
    public string $APP_VIEWS_PATH;
    public string $APP_VIEWS_PARTS_PATH;
    public string $APP_PDF_PATH;
    public string $APP_MAIL_PATH;
    public string $APP_NEWSLETTER_PATH;
    public string $APP_WWW_PATH;
    public string $APP_IMGS_PATH;
    public string $APP_FONTS_PATH;
    public string $APP_JS_PATH;
    public string $APP_CSS_PATH;
    public string $PARENT_BASE_URI;
    public string $PARENT_BASE_PATH;

    // parent
    public string $PARENT_WWW_URI;
    public string $PARENT_IMGS_URI;
    public string $PARENT_JS_URI;
    public string $PARENT_CSS_URI;
    public string $PARENT_MVC_PATH;
    public string $PARENT_VIEWS_PATH;
    public string $PARENT_VIEWS_PARTS_PATH;
    public string $PARENT_PDF_PATH;
    public string $PARENT_MAIL_PATH;
    public string $PARENT_NEWSLETTER_PATH;
    public string $PARENT_WWW_PATH;
    public string $PARENT_IMGS_PATH;
    public string $PARENT_JS_PATH;
    public string $PARENT_CSS_PATH;
    public string $UPLOADS_PATH;
    public string $UPLOADS_URI;

    // parent end
    public string $TEMPLATES_PATH;
    public string $TEMPLATES_URI;
    public string $TEMP_PATH;
    public string $TEMP_URI;
    public string $APP_COMPONENT_PATH;
    public bool $DEFAULT_FULLSIZE_HEADERBAR;
    public bool $TEMPLATE_THEME;

    function __construct()
    {

        $this->APP_NAME = APP_NAME;

        $this->PARENT_NAME = PARENT_NAME;

        $this->APP_TITLE = APP_TITLE;

        $this->STARTING_PAGE = STARTING_PAGE;

        //$this->CURRENT_LANGUAGE = CURRENT_LANGUAGE;

        $this->BPFW_DEBUG_MODE = BPFW_DEBUG_MODE;

        $this->BASE_URI = BASE_URI;
        $this->BASE_PATH = BASE_PATH;

        $this->VENDOR_PATH = VENDOR_PATH;
        $this->VENDOR_URI = VENDOR_URI;
        $this->LIBS_URI = LIBS_URI;
        $this->LIBS_PATH = LIBS_PATH;
        $this->DB_HOST = DB_HOST;
        $this->DB_USER = DB_USER;

        //$this->DB_PASSWORD = DB_PASSWORD;

        $this->DB_DATABASE = DB_DATABASE;
        $this->USERTYPE_GUEST = USERTYPE_GUEST;
        $this->USERTYPE_CUSTOMER = USERTYPE_CUSTOMER;
        $this->USERTYPE_PREMIUM = USERTYPE_PREMIUM;
        $this->USERTYPE_CONSULTANT = USERTYPE_CONSULTANT;
        $this->USERTYPE_ADMIN = USERTYPE_ADMIN;
        $this->USERTYPE_SUPERADMIN = USERTYPE_SUPERADMIN;
        $this->USERTYPE_INVALID = USERTYPE_INVALID;
        $this->SALUTATION_NEUTRAL = SALUTATION_NEUTRAL;
        $this->SALUTATION_MALE = SALUTATION_MALE;
        $this->SALUTATION_FEMALE = SALUTATION_FEMALE;
        $this->SALUTATION_NONE = SALUTATION_NONE;

        $this->BPFW_BASE_PATH = BPFW_BASE_PATH;
        $this->BPFW_MVC_PATH = BPFW_MVC_PATH;
        $this->BPFW_VIEWS_PATH = BPFW_VIEWS_PATH;
        $this->BPFW_VIEWS_PARTS_PATH = BPFW_VIEWS_PARTS_PATH;
        $this->BPFW_COMPONENT_PATH = BPFW_COMPONENT_PATH;
        $this->BPFW_CORE_PATH = BPFW_BASE_PATH;

        $this->BPFW_PDF_PATH = BPFW_PDF_PATH;
        $this->BPFW_MAIL_PATH = BPFW_MAIL_PATH;

        $this->BPFW_INCLUDE_PATH = BPFW_INCLUDE_PATH;
        $this->BPFW_ADMIN_PATH = BPFW_ADMIN_PATH;
        $this->BPFW_CONTENTPAGES_PATH = BPFW_CONTENTPAGES_PATH;

        $this->BPFW_BASE_URI = BPFW_BASE_URI;
        $this->BPFW_WWW_URI = BPFW_WWW_URI;
        $this->BPFW_JS_URI = BPFW_JS_URI;
        $this->BPFW_CSS_URI = BPFW_CSS_URI;

        $this->APP_BASE_URI = APP_BASE_URI;
        $this->APP_BASE_PATH = APP_BASE_PATH;
        $this->APP_WWW_URI = APP_WWW_URI;
        $this->APP_IMGS_URI = APP_IMGS_URI;
        $this->APP_FONTS_URI = APP_FONTS_URI;
        $this->APP_JS_URI = APP_JS_URI;
        $this->APP_CSS_URI = APP_CSS_URI;

        $this->APP_MVC_PATH = APP_MVC_PATH;
        $this->APP_VIEWS_PATH = APP_VIEWS_PATH;
        $this->APP_VIEWS_PARTS_PATH = APP_VIEWS_PARTS_PATH;

        $this->APP_PDF_PATH = APP_PDF_PATH;
        $this->APP_MAIL_PATH = APP_MAIL_PATH;

        $this->APP_NEWSLETTER_PATH = APP_NEWSLETTER_PATH;

        $this->APP_WWW_PATH = APP_WWW_PATH;
        $this->APP_IMGS_PATH = APP_IMGS_PATH;
        $this->APP_FONTS_PATH = APP_FONTS_PATH;
        $this->APP_JS_PATH = APP_JS_PATH;
        $this->APP_CSS_PATH = APP_CSS_PATH;


        $this->PARENT_BASE_URI = PARENT_BASE_URI;
        $this->PARENT_BASE_PATH = PARENT_BASE_PATH;
        $this->PARENT_WWW_URI = PARENT_WWW_URI;
        $this->PARENT_IMGS_URI = PARENT_IMGS_URI;
        $this->PARENT_JS_URI = PARENT_JS_URI;
        $this->PARENT_CSS_URI = PARENT_CSS_URI;

        $this->PARENT_MVC_PATH = PARENT_MVC_PATH;
        $this->PARENT_VIEWS_PATH = PARENT_VIEWS_PATH;
        $this->PARENT_VIEWS_PARTS_PATH = PARENT_VIEWS_PARTS_PATH;

        $this->PARENT_PDF_PATH = PARENT_PDF_PATH;
        $this->PARENT_MAIL_PATH = PARENT_MAIL_PATH;

        $this->PARENT_NEWSLETTER_PATH = PARENT_NEWSLETTER_PATH;

        $this->PARENT_WWW_PATH = PARENT_WWW_PATH;
        $this->PARENT_IMGS_PATH = PARENT_IMGS_PATH;
        $this->PARENT_JS_PATH = PARENT_JS_PATH;
        $this->PARENT_CSS_PATH = PARENT_CSS_PATH;


        $this->UPLOADS_PATH = UPLOADS_PATH;
        $this->UPLOADS_URI = UPLOADS_URI;

        $this->TEMPLATES_PATH = TEMPLATES_PATH;
        $this->TEMPLATES_URI = TEMPLATES_URI;

        $this->TEMP_PATH = TEMP_PATH;
        $this->TEMP_URI = TEMP_URI;

        $this->APP_COMPONENT_PATH = APP_COMPONENT_PATH;

        $this->DEFAULT_FULLSIZE_HEADERBAR = DEFAULT_FULLSIZE_HEADERBAR;
        $this->TEMPLATE_THEME = TEMPLATE_THEME;

    }

    function hasParent(): bool
    {
        return APP_NAME != PARENT_NAME;
    }

}


/**
 * @var Config $__config
 */
$__config = null;

/**
 * Summary of get_config
 * @return Config
 */
function config(): Config
{

    if (empty($__config)) {

        $__config = new Config();

    }

    return $__config;

}



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

/** @noinspection PhpPossiblePolymorphicInvocationInspection */

/** @noinspection PhpUnused */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$bpfw_currentSettings = null;
$bpfw_currentSettings_options = array();



require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."userFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."settingsFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."i18nFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."navigationFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."stringFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."arrayFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."mailerFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."commonFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."datetimeFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."fileFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."bpfwcoreFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."numberFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."cronjobFunctions.inc.php");
require_once(BPFW_CORE_PATH."functions".DIRECTORY_SEPARATOR."debugFunctions.inc.php");

























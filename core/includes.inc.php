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



require_once(BPFW_CORE_PATH."db/dbModel.inc.php");
require_once(BPFW_CORE_PATH."db/dbModelTemplate.inc.php");
require_once(BPFW_CORE_PATH."db/dbDynamicModel.inc.php");
require_once(BPFW_CORE_PATH."db/bpfwModel.inc.php");
require_once(BPFW_CORE_PATH."functions.inc.php");
require_once(BPFW_CORE_PATH."db/dbSubmitValue.inc.php");
require_once(BPFW_CORE_PATH."db/database.inc.php");
require_once(BPFW_CORE_PATH."components/enumHandler.inc.php");
require_once(BPFW_CORE_PATH."components/sortSearchHandler.inc.php");
require_once(BPFW_CORE_PATH."db/bpfwEmptyModel.inc.php");
require_once(BPFW_CORE_PATH."db/dbModelEntry.inc.php");
require_once(BPFW_CORE_PATH."scriptHandler.inc.php");
require_once(BPFW_CORE_PATH."errorHandler.inc.php");
require_once(BPFW_CORE_PATH."actionHandler.inc.php");
require_once(BPFW_COMPONENT_PATH."defaultComponent.inc.php");
require_once(BPFW_CORE_PATH."components/componentHandler.inc.php");
require_once(BPFW_MVC_PATH . "controls/defaultControl.inc.php");

bpfw_register_css("bootstrap-select", VENDOR_URI . "snapappointments/bootstrap-select/dist/css/bootstrap-select.min.css");
bpfw_register_css("bootstrap", VENDOR_URI . "twbs/bootstrap/dist/css/bootstrap.min.css");
bpfw_register_css("fontawesome", VENDOR_URI . "components/font-awesome/css/all.min.css?v=2"); // version 6
bpfw_register_css("bpfwStyles", BPFW_CSS_URI . "bpfwstyles.css?v=8");
bpfw_register_css("datatables", VENDOR_URI . "datatables/datatables/media/css/jquery.dataTables.min.css");
bpfw_register_css("datatables-responsive", VENDOR_URI . "drmonty/datatables-responsive/css/dataTables.responsive.min.css");
bpfw_register_css("datetimepicker", LIBS_URI . "datetimepicker/build/jquery.datetimepicker.min.css");
bpfw_register_css("bootstrap_fileinput_locale_explorer_fas", LIBS_URI . "kartik-v/bootstrap-fileinput/themes/explorer-fa6/theme.min.css", true);
bpfw_register_css("bootstrap_fileinput", LIBS_URI . "kartik-v/bootstrap-fileinput/css/fileinput.min.css", true);
bpfw_register_css("listStyles", BPFW_CSS_URI."themes/slimlist/slimlist.css?v=2", array("bpfwStyles") );

bpfw_register_js("jquery", VENDOR_URI . "components/jquery/jquery.min.js");
bpfw_register_js("piexif_js", LIBS_URI . "kartik-v/bootstrap-fileinput/js/plugins/sortable.min.js", true, array("bootstrap", "bootstrap_fileinput"));
bpfw_register_js("purify_js", LIBS_URI . "kartik-v/bootstrap-fileinput/js/plugins/piexif.min.js", true, array("bootstrap", "bootstrap_fileinput"));
bpfw_register_js("popper", BPFW_JS_URI . "popper.min.js", true);
bpfw_register_js("bootstrap", VENDOR_URI . "twbs/bootstrap/dist/js/bootstrap.min.js", true, array("jquery"/*, "popper"*/));
bpfw_register_js("bootstrap-select", VENDOR_URI . "snapappointments/bootstrap-select/dist/js/bootstrap-select.min.js", true, array("bootstrap"));
bpfw_register_js("datatables", VENDOR_URI . "datatables/datatables/media/js/jquery.dataTables.min.js", true);
bpfw_register_js("datatables-responsive", VENDOR_URI . "drmonty/datatables-responsive/js/dataTables.responsive.min.js", true);
bpfw_register_js("datetimepicker", LIBS_URI . "datetimepicker/build/jquery.datetimepicker.full.min.js", true);
bpfw_register_js("signaturepad", LIBS_URI . "signaturePad/jquery.signaturepad.js", true);
bpfw_register_js("bootstrap_fileinput", LIBS_URI . "kartik-v/bootstrap-fileinput/js/fileinput.min.js", true, array("bootstrap", "purify_js", "sortable_js", "piexif_js"));
bpfw_register_js("bootstrap_fileinput_locale_de", LIBS_URI . "kartik-v/bootstrap-fileinput/js/locales/de.js", true, array("bootstrap", "bootstrap_fileinput"));
bpfw_register_js("bootstrap_fileinput_locale_explorer_fas", LIBS_URI . "kartik-v/bootstrap-fileinput/themes/explorer-fa6/theme.min.js", true, array("bootstrap", "bootstrap_fileinput", "bootstrap_fileinput_locale_de"));
bpfw_register_js("common_js", BPFW_JS_URI . "bpfw_common.js?v=8", true);
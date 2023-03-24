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

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
// require_once(BPFW_MVC_PATH."models/tempfileModel.inc.php");

class PdfmanagerControl extends DefaultControl
{

    const FILE = 'F';
    const DOWNLOAD = 'D';
    const STRING_RETURN = 'S';
    const INLINE = 'I';


    var string $pdfOutputMode = PdfmanagerControl::STRING_RETURN; // 'I';

    //var $generatePdfPrint = true;

    function handleAjaxCommand(string $command): void
    {

        global $database;
        $model = $this->model;

        if ($command == "opentempfile") {

            $tempfileid = getorpost("tempfileid");

            if (empty($tempfileid)) {
                throw new Exception("tempfileid not set");
            } else {

                $tempfilemodel = bpfw_createModelByName("tempfile"); //new TempfileModel();

                $data = $tempfilemodel->DbSelectSingleOrNullByKey($tempfileid);

                $tempfilemodel->cleanTempfiles();

                if (!empty($data)) {

                    if ($data["createdByUser"] != bpfw_getUserId()) {
                        throw new Exception("invalid temp File");
                    } else {

                        $filepath = $data["path"];
                        $outputFilename = $data["outputFilename"];

                        header("Content-type:application/pdf");

                        header("Content-Disposition:inline;filename=\"$outputFilename\"");  // download

                        readfile($filepath);

                        die();

                        //  var_dump($data);


                    }

                } else {
                    throw new Exception("invalid tempfileid");
                }

            }


        }else if ($command == "generatePdf") {

            //  echo "formail is ".getorpost("formail");

            if (getorpost("formail") == "1") {
                $this->pdfOutputMode = PdfmanagerControl::STRING_RETURN; // 'I';
            }

            $variables = $model->variables;

            $mailtext = getorpost("mailtext");


            $entries = explode(",", getorpost("generatePdf"));

            $mailtextAsCover = $this->model->getMailtextAsCoverFromCategoryId(getorpost("category"));

            if ($mailtextAsCover && !empty($mailtext)) {


                //  var_dump($_POST);

                if (empty($entries)) {
                    $entries = array("mailtext");
                } else {

                    // $entries = explode( "," , getorpost( "generatePdf" ) );

                    array_unshift($entries, "mailtext");

                }

            }

            if (!is_array($variables)) {

                echo("Required variables not set: " . $variables);

            } else if (!empty($entries)) {

                if (!bpfw_isAdmin()) {
                    echo "only programmed for users with full access"; // no permission checks. we need to add this if rank != admin ...
                } else {

                    if (!defined('_MPDF_TTFONTPATH')) {
                        // path for custom fonts
                        define('_MPDF_TTFONTPATH', realpath(APP_FONTS_PATH . 'mpdf_fonts' . DIRECTORY_SEPARATOR . 'ttfonts' . DIRECTORY_SEPARATOR));
                    }

                    $defaultConfig = (new ConfigVariables())->getDefaults();

                    $fontDirs = $defaultConfig['fontDir'];

                    $defaultFontConfig = (new FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];

                    $fontData = $this->model->getCustomFonts($fontData);

                    $defaultFont = $this->model->getDefaultFont("default");

                    /*
                    // load custom fonts. see also mpdf doc
                    $customfontdata =
                        [
                            'avenir' => [
                                'R' => '12 AVENIR 65 MEDIUM 06173.TTF',
                                'B' => '12 AVENIR 85 HEAVY 08173.TTF',
                            ],
                        ];

                    $fontData = array_merge($defaultFontConfig['fontdata'], $customfontdata);
                    */
                    $fontsDirs = array_merge($fontDirs, [
                        _MPDF_TTFONTPATH
                    ], [realpath(APP_FONTS_PATH)]);



                    $mpdf = new Mpdf(
                        [

                            'margin_left' => 20,
                            'margin_right' => 15,
                            'margin_top' => 35,
                            'margin_bottom' => 25,
                            'margin_header' => 10,
                            'margin_footer' => 10,


                            'fontDir' => $fontsDirs,
                            'fontdata' => $fontData,
                            'default_font' => $defaultFont,

                            // 'debug' => true,
                            // 'debugfonts' => true
                        ]

                    );

                    $coreSuitable = false;

                    $mpdf->showImageErrors = false;

                    $mpdf->curlAllowUnsafeSslRequests = true;
                    $mpdf->debugfonts = false;

                    $mpdf->SetTitle("BPFW generated Pdf");
                    $mpdf->SetAuthor("BPFW generated Pdf");

                    $mpdf->debugfonts = false;
                    $mpdf->debug = false;


                    $mpdf->SetDisplayMode('fullpage');

                    $pdfCreated = false;
                    $notexistingerror = "";

                    foreach ($entries as $key => $classname) {

                        $classname = basename($classname);
                        $file = "classes/" . $classname . ".inc.php";
                        $file2 = "classes/" . $classname . ".php";

                        if (file_exists(APP_PDF_PATH . $file)) {
                            require_once(APP_PDF_PATH . $file);
                            $class = new $classname($classname);
                            $class->printPdf($mpdf, $variables);
                            $pdfCreated = true;
                        } else if (file_exists(APP_PDF_PATH . $file2)) {
                            require_once(APP_PDF_PATH . $file2);
                            $class = new $classname($classname);
                            $class->printPdf($mpdf, $variables);
                            $pdfCreated = true;
                        } else if (file_exists(PARENT_PDF_PATH . $file)) {
                            require_once(PARENT_PDF_PATH . $file);
                            $class = new $classname($classname);
                            $class->printPdf($mpdf, $variables);
                            $pdfCreated = true;
                        }else if (file_exists(PARENT_PDF_PATH . $file2)) {
                            require_once(PARENT_PDF_PATH . $file2);
                            $class = new $classname($classname);
                            $class->printPdf($mpdf, $variables);
                            $pdfCreated = true;
                        } else {
                            require_once(BPFW_CORE_PATH."pdf/pdfpage.inc.php");
                            $class = new PdfPage($classname);
                            $class->printPdf($mpdf, $variables);
                            $pdfCreated = true;

                            // $notexistingerror .= $file . "not found   ";

                        }

                    }

                    if ($pdfCreated) {

                        $pdftitle = APP_TITLE . "Documents " . date("d.m.Y") . ".pdf";

                        if (isset($_GET["category"])) {

                            if (isset($this->model->pdfPages[$_GET["category"]]) && !empty($this->model->pdfPages[$_GET["category"]]->attachmentTitle)) {
                                $pdftitle = $this->model->pdfPages[$_GET["category"]]->attachmentTitle;
                                foreach ($variables as $key => $value) {
                                    $pdftitle = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $pdftitle);
                                }
                            }

                            $pdftitle = $pdftitle . ".pdf";

                        }

                        $output = $mpdf->Output($pdftitle, $this->pdfOutputMode);

                        if (getorpost("formail") != "1") { // open document, do not send

                            $path = TEMP_PATH;

                            $filename = @tempnam($path, 'tmp_');

                            if ($filename !== FALSE) {

                                if (!file_exists($path)) {
                                    mkdir($path, 0600, true);
                                }

                                file_put_contents($filename, $output);

                                $attachmentName = 'Seminarunterlagen.pdf';

                                if (!empty(getorpost("category"))) {
                                    $attachmentName = $this->model->getAttachmentTitle(getorpost("category"));
                                }

                                bpfw_getDb()->refreshConnection(); // timeout may occure because it took too long to generate the pdfnote

                                /**
                                 * @var TempfileModel $tempfileModel
                                 */
                                $tempfileModel = bpfw_createModelByName("tempfile"); //new TempfileModel();
                                $tempfileid = $tempfileModel->addTempFile($filename, trim($attachmentName) . ".pdf", bpfw_getUserId(), "eventappointmentpdf");
                                //$tempfileid = $tempfileModel->DbInsert( array("path"=>$filename, "created"=>time(), "type"=>"eventappointmentpdf", "createdByUser"=>bpfw_getUserId(), "outputFilename"=>trim($attachmentName).".pdf" ));

                                header("Location: ?p=".getorpost("p")."&ajaxCall=true&command=opentempfile&tempfileid=$tempfileid&filter=" . getorpost("filter"));

                                die();

                            } else {
                                throw new Exception("temp filename failed");
                            }

                        }


                        echo $output;
                        return;

                    } else {

                        echo "Class not existing: " . $notexistingerror;
                    }

                }

            }

        } // TODO: verallgemeinern und in ajax?
        else if ($command == "PdfmanagerSendMail") {

            // error_reporting(E_ALL);
            // ini_set("display_errors", 1);

            $this->pdfOutputMode = 'S';
            //$this->generatePdfPrint = false;

            $mailAddress = getorpost("receiver");
            $topic = getorpost("title");
            $text = getorpost("text");


            $filenameHeader = APP_MAIL_PATH . "common/mailheader.html";
            if (!file_exists($filenameHeader))
                $filenameHeader = PARENT_MAIL_PATH . "common/mailheader.html";
            if (!file_exists($filenameHeader))
                $filenameHeader = BPFW_MAIL_PATH . "common/mailheader.html";


            $filenameFooter = APP_MAIL_PATH . "common/mailfooter.html";
            if (!file_exists($filenameFooter))
                $filenameFooter = PARENT_MAIL_PATH . "common/mailfooter.html";
            if (!file_exists($filenameFooter))
                $filenameFooter = BPFW_MAIL_PATH . "common/mailfooter.html";


            if (file_exists($filenameHeader)) {
                $text = file_get_contents($filenameHeader) . $text;
            }

            if (file_exists($filenameFooter)) {
                $text = $text . file_get_contents($filenameFooter);
            }


            $generatePdf = getorpost("generatePdf");
            $filter = getorpost("filter");


            if (!is_numeric($filter)) {
                echo "Filter not a Number";
            } else

                if (!filter_var($mailAddress, FILTER_VALIDATE_EMAIL)) {

                    echo "Invalid Mail";

                } else {

                   /* $eventdata = bpfw_createModelByName("event")->DbSelectSingleOrNullByKey($filter);

                    if (empty($eventdata)) {
                        echo "invalid filter event";
                    }

                    $customerdata = bpfw_createModelByName("customer")->DbSelectSingleOrNullByKey($eventdata["customerId"]);

                    if (empty($customerdata)) {
                        echo "invalid Customer for event";
                    }

                    $customerid = $eventdata["customerId"];
                    $customername = $customerdata["firstname"] . " " . $customerdata["lastname"];
*/

                    $attachments = array();

                    if (!empty(getorpost("generatePdf"))) {

                        $entries = explode(",", getorpost("generatePdf"));

                        if (!empty($entries)) {

                            $_GET["formail"] = true;
                            ob_start();
                            $this->handleAjaxCommand("generatePdf");
                            $output = ob_get_clean();
                            // $pdfDocumentSource = ob_get_clean();

                            $attachmentName = 'Seminarunterlagen.pdf';

                            if (!empty(getorpost("pdf_attachment_name"))) {
                                $attachmentName = getorpost("pdf_attachment_name") . ".pdf";
                            }

                            // $mail->addStringAttachment( $output, $attachmentName, 'base64', 'application/pdf' );

                            $attachments[] = new BpfwBinaryMailAttachment($output, $attachmentName, 'base64', 'application/pdf', true);

                        }

                    }

                    if (getorpost("emailattachments") === "0" || !empty(getorpost("emailattachments"))) {

                        $emailattachments = explode(",", getorpost("emailattachments"));

                        if (isset($emailattachments) && $emailattachments != null) {

                            foreach ($emailattachments as $file) {

                                $db = bpfw_getDb();

                                $data = $db->makeSelect("select emailattachmentId, file, headline, attachment_name from emailattachments where emailattachmentId = '$file'", "emailattachmentId");

                                if (isset($data[$file])) {

                                    $info = json_decode($data[$file]["file"]);
                                    $fullpath = APP_BASE_PATH . "uploads" . $info->new_name;

                                    if (file_exists($fullpath)) {

                                        $filename = $info->name;
                                        if (!empty($data[$file]["attachment_name"])) {
                                            $filename = $data[$file]["attachment_name"] . "." . pathinfo($info->name, PATHINFO_EXTENSION);
                                        }

                                        $attachments[] = new BpfwMailAttachment($fullpath, $filename, false);
                                        // $mail->addAttachment($fullpath, $filename);

                                    } else {
                                        echo "not found: $fullpath";
                                    }

                                }

                            }

                        }

                    }

                    $rcpt = bpfw_findMailAddress($mailAddress, array("customer", "user"));

                    $sendresult = bpfw_sendmail_with_mailer_send_directly($topic, $text, $rcpt, null, bpfw_getDefaultMailBcc(), null, $attachments);


                    $mpdfResponse = ob_get_clean();
                    if(!empty($mpdfResponse)){
                        $mpdfResponse="\r\n\r\nPDF Log:\r\n".$mpdfResponse;
                    }



                    if ($sendresult) // $mail->send())
                    {
                        if(!BPFW_DEBUG_MODE){
                            $mpdfResponse = "";
                        }
                        echo __("Mail sent")." ".$mpdfResponse;
                    } else {
                        echo __("Mail failed")." ".$mpdfResponse;
                    }

                }

        } else {
            parent::handleAjaxCommand($command);
        }

        die();

    }

}
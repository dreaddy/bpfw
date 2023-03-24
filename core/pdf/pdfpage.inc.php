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

use Mpdf\Mpdf;
use Mpdf\MpdfException;

class PdfPage
{

    var string $name;
    var array $config;

    function __construct(string $name)
    {

        $this->name = $name;

        $this->config =
            [

                'margin_left' => 20,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 25,
                'margin_header' => 10,
                'margin_footer' => 10,

                'default_font' => 'avenir'

            ];

    }

    /**
     * @param array $variables
     * @return array
     */
    function getCustomVariablesForPage(array $variables) : array
    {
        return $variables;
    }

    /**
     * Summary of printPdf
     * @param Mpdf $mpdf
     * @param array $variables
     * @param string $header
     * @param string $footer
     * @param array $config
     * @return Mpdf
     * @throws MpdfException
     * @throws Exception
     */
    function printPdf(Mpdf $mpdf, array $variables = array(), string $header = "defaultheader.html", string $footer = "defaultfooter.html", array $config = array(), $setHeaderFooterPerPage = true): Mpdf
    {

        if (!empty($config)) {

            foreach ($config as $key => $val) {

                $this->config[$key] = $val;

            }

        }

        $variables = $this->getCustomVariablesForPage($variables);

        $mailtext = getorpost("mailtext");
        $mailtitle = getorpost("mailtitle");

        $variables["common/mailtext"] = $mailtext;
        $variables["common/mailtitle"] = $mailtitle;

        $headerhtml = $this->getHeaderHtml($variables, $header);
        $footerhtml = $this->getFooterHtml($variables, $footer);
        //$mpdf = new \Mpdf\Mpdf();




        if ($setHeaderFooterPerPage && !empty(trim($headerhtml))) {
            $mpdf->SetHTMLHeader($headerhtml);
        } else {
            $mpdf->SetHTMLHeader();
        }


        if(!$setHeaderFooterPerPage){
            if(!empty(trim($headerhtml)))
            $headerhtml = $headerhtml.="<div style='margin-bottom: {$this->config["margin_header"]}px'></div>";
            if(!empty(trim($footerhtml)))
            $footerhtml = "<div style='margin-bottom: {$this->config["margin_footer"]}px'></div>".$footerhtml;
        }

        $html = $this->getMainContentHtml($variables, $headerhtml, $footerhtml); //"", "");

        // stick to bottom is best done this way...
        if ($setHeaderFooterPerPage && !empty(trim($footerhtml))) {
            $mpdf->SetHTMLFooter($footerhtml);
        }

        $mpdf->AddPage('', '', '', '', '', $this->config["margin_left"], $this->config["margin_right"], $this->config["margin_top"], $this->config["margin_bottom"], $this->config["margin_header"], $this->config["margin_footer"]);


        /*
                       'margin_left' => 0,
                       'margin_right' => 0,
                       'margin_top' => 0,
                       'margin_bottom' => 0,
                       'margin_header' => 0,
                       'margin_footer' => 0,*/

        $mpdf->WriteHTML($html, 0, true, false);

        return $mpdf;

    }

    /**
     * @throws Exception
     */
    function getMainContentHtml(array $variables, string &$headerHtml, string &$footerHtml): string
    {

        $file = $this->name;
        $file = "templates/" . $file . ".html";

        if (file_exists(APP_PDF_PATH . $file)) {
            $mainContent = file_get_contents(APP_PDF_PATH . $file);
        } else if (file_exists(PARENT_PDF_PATH . $file)) {
            $mainContent = file_get_contents(PARENT_PDF_PATH . $file);
        } else {
            throw new Exception("pdf not found: " . $file);
        }

        $mainContent = str_replace("{{{common/pdfstyles}}}", bpfw_format_for_print($this->getCss()), $mainContent);
        $mainContent = str_replace("{{{common/header}}}", bpfw_format_for_print($headerHtml), $mainContent);
        $mainContent = str_replace("{{{common/footer}}}", bpfw_format_for_print($footerHtml), $mainContent);

        $mailtext = getorpost("mailtext");
        $mailtitle = getorpost("mailtitle");

        //$mainContent = str_replace("{{{common/mailtext}}}", $mailtext, $mainContent); // $footerhtml
        //$mainContent = str_replace("{{{common/mailtitle}}}", $mailtitle, $mainContent); // $footerhtml

        $variables["common/mailtext"] = $mailtext;
        $variables["common/mailtitle"] = $mailtitle;
        // var_dump($_POST);die();


        foreach ($variables as $key => $value) {
            $headerHtml = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $headerHtml);
            $footerHtml = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $footerHtml);
            $mainContent = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $mainContent);
        }

        return "<div id='main' style='padding:35px;'>$mainContent</div>";

    }

    function getCss(): string
    {

        $cssCode = ""; // file_get_contents(APP_FONTS_PATH."AvenirNext/AvenirNext.css");

        if (file_exists(APP_PDF_PATH . "templates/common/pdfstyles.css")) {
            return $cssCode . file_get_contents(APP_PDF_PATH . "templates/common/pdfstyles.css");
        } else {
            return $cssCode . file_get_contents(PARENT_PDF_PATH . "templates/common/pdfstyles.css");
        }

    }

    function getHeaderHtml($variables, $header = "defaultheader.html"): string
    {


        if (empty($header)) return "";

        if (file_exists(APP_PDF_PATH . "templates/common/$header") || file_exists(strtolower(APP_PDF_PATH . "templates/common/$header"))) {

            $headerfile = APP_PDF_PATH . "templates/common/$header";
            if (!file_exists($headerfile)) {
                $headerfile = strtolower(APP_PDF_PATH . "templates/common/$header");

                if (!file_exists($headerfile)) {
                    $headerfile = BPFW_PDF_PATH . $headerfile;

                    if (!file_exists($headerfile)) {
                        $headerfile = strtolower(BPFW_PDF_PATH . $headerfile);

                        if (!file_exists($headerfile)) {
                            $headerfile = "";
                        }

                    }
                }
            }

        } else {

            $headerfile = PARENT_PDF_PATH . "templates/common/$header";
            if (!file_exists($headerfile)) {
                $headerfile = strtolower(PARENT_PDF_PATH . "templates/common/$header");

                if (!file_exists($headerfile)) {
                    $headerfile = BPFW_PDF_PATH . $headerfile;

                    if (!file_exists($headerfile)) {
                        $headerfile = strtolower(BPFW_PDF_PATH . $headerfile);

                        if (!file_exists($headerfile)) {
                            $headerfile = "";
                        }

                    }
                }
            }

        }

        if (empty($headerfile)) return "";
        $maincontant = file_get_contents($headerfile);

        foreach ($variables as $key => $value) {
            $maincontant = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $maincontant);
        }


        return "<div id='header'>$maincontant</div>";


    }


    /*var $margin_left = 20;
    var $margin_right = 15;
    var $margin_top = 35;
    var $margin_bottom = 25;

    var $margin_header = 10;
    var $margin_footer = 10;*/

    function getFooterHtml($variables, $footer = "defaultfooter.html"): string
    {

        if (empty($footer)) return "";

        if (file_exists(APP_PDF_PATH . "templates/common/$footer") || file_exists(strtolower(APP_PDF_PATH . "templates/common/$footer"))) {


            $footerfile = APP_PDF_PATH . "templates/common/$footer";
            if (!file_exists($footerfile)) {
                $footerfile = strtolower(APP_PDF_PATH . "templates/common/$footer");

                if (!file_exists($footerfile)) {
                    $footerfile = BPFW_PDF_PATH . $footerfile;

                    if (!file_exists($footerfile)) {
                        $footerfile = strtolower(BPFW_PDF_PATH . $footerfile);

                        if (!file_exists($footerfile)) {
                            $footerfile = "";
                        }

                    }
                }
            }

        } else {


            $footerfile = PARENT_PDF_PATH . "templates/common/$footer";
            if (!file_exists($footerfile)) {
                $footerfile = strtolower(PARENT_PDF_PATH . "templates/common/$footer");

                if (!file_exists($footerfile)) {
                    $footerfile = BPFW_PDF_PATH . $footerfile;

                    if (!file_exists($footerfile)) {
                        $footerfile = strtolower(BPFW_PDF_PATH . $footerfile);

                        if (!file_exists($footerfile)) {
                            $footerfile = "";
                        }

                    }
                }
            }

        }


        if (empty($footerfile)) return "";


        $maincontant = file_get_contents($footerfile);
        foreach ($variables as $key => $value) {
            $maincontant = str_replace('{{{' . $key . '}}}', bpfw_format_for_print($value), $maincontant);
        }


        return "<div id='footer'>$maincontant</div>";

    }

}






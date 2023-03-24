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

require_once(BPFW_MVC_PATH."models/pdfmanagerModel.inc.php");
class ExamplepdfmanagerModel extends PdfmanagerModel
{


    function __construct($values = null, $autocompleteVariables = true)
    {

        parent::__construct();


    }

    /**
     * Summary of getVariablesForTextReplacement
     * @param ?int $id
     * @param bool $cancelAndReturnErrorOnMissingValues
     * @return array|string
     * @throws Exception
     */
    function getVariablesForTextReplacement(int $id, string $modelUsed, bool $cancelAndReturnErrorOnMissingValues = true, $categoryId = null): array|string
    {

        return parent::getVariablesForTextReplacement($id, $modelUsed, $cancelAndReturnErrorOnMissingValues, $categoryId);

    }

    /**
     * @return string
     */
    public function GetTableName(): string
    {
        return "examplepdfmanager";
    }

    public function GetTitle(): string
    {

        $retval = "<div style='padding-left:30px;'>";
        $retval .= bpfw_cutStringToLength("Test Pdf Mailer", 80);
        $retval .= "</div>";
        return $retval;

    }


    protected function initializeCategories()
    {

        // define your pdfs / mails
        $this->addPdfCategory("invoice", "Invoice example", 0, RCPT_CUSTOMER, "Invoice", true);
        $this->addPdfCategory("customer_mail", "empty mail to customer", 0, RCPT_CUSTOMER, "info", true);

        // define the pdf parts
        $this->addPdfDocument("invoice_example", "Invoice", "invoice");
        $this->addPdfDocument("hours_made_pdf", "Hours made", "invoice");

    }

}
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



require_once(BPFW_CORE_PATH."pdf/pdfpage.inc.php");

// you can do some settings if you create a class with the same name as the html page here
class invoice_example extends PdfPage
{

    /**
     * @param array $variables
     * @return array
     * @throws Exception
     */
    function getCustomVariablesForPage(array $variables) : array
    {

        $useridRcpt = $variables["examplecomplex.user"];

        $usermodel = bpfw_createModelByName("user");
        $userdata = $usermodel->GetEntry($useridRcpt);

        $variables = bpfw_setVariables($userdata, "customer", $variables);

        ob_start();
        ?>
        <style>
        #invoiceContent{
            width:100%;
        }
        #invoiceContent th{
            font-weight: bold;
        }
        </style>

<table id="invoiceContent">
    <tr>
        <td>Article</td>
        <td>Amount</td>
        <td>Single Price</td>
        <td>Price</td>
    </tr>
    <tr>
        <td colspan="4"><hr></td>
    </tr>
    <tr>
        <td>Article 1</td>
        <td>2</td>
        <td>50 €</td>
        <td>100 €</td>
    </tr>
    <tr>
        <td>Article 2</td>
        <td>1</td>
        <td>150 €</td>
        <td>150 €</td>
    </tr>
    <tr>
        <td>Article 3</td>
        <td>3</td>
        <td>20 €</td>
        <td>60 €</td>
    </tr>

    <tr>
        <td colspan="4"><hr></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>Sum net</td>
        <td>310 €</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>Taxes</td>
        <td>61 €</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
        <td colspan="2"><hr></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td style="font-weight: bold">Total</td>
        <td style="font-weight: bold">371 €</td>
    </tr>
</table>

<?php
        $variables["invoiceContent"] = ob_get_clean();

        return parent::getCustomVariablesForPage($variables);
    }

}

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

require_once(BPFW_PDF_PATH . "pdfcategory.inc.php");

// require_once(APP_MVC_PATH . "models/parts/eventdatamodelpart.inc.php");

// require_once(BPFW_MVC_PATH."models/emailattachmentsModel.inc.php");


class PdfmanagerModel extends BpfwModel
{

    const FILTER_PDFMANAGER_EMAIL_ATTACHMENTS = "FILTER_PDFMANAGER_EMAIL_ATTACHMENTS";
    const FILTER_PDFMANAGER_PDF_ATTACHMENT_CONTENT = "FILTER_PDFMANAGER_PDF_ATTACHMENT_CONTENT";

    var string|array $variables;

    /**
     * @var PdfDocument[]
     */
    var array $pdfPages = array();

    function __construct($values = null, $autocompleteVariables = true)
    {

        $this->viewtemplate="pdfmanagerView";
        $this->controltemplate="pdfmanagerControl";
        parent::__construct();

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_ADMIN;

        $this->showdata = false;

        $this->initializeCategories();

        $modelused = bpfw_getdb()->escape_string(getorpost('modelused'));
        $filter = bpfw_getdb()->escape_string(getorpost('filter'));
        if(!empty($modelused)&&!empty($filter)) {



            /*
            if(getorpost("command") == "sendZohomail"){

                $dateformat = "d/m/y";

            }*/


            $this->variables = $this->getVariablesForTextReplacement($filter, $modelused);

            $jspath = VENDOR_URI . "tinymce/tinymce/tinymce.min.js";
            bpfw_register_js("Tinymce", $jspath);

            $jspath = VENDOR_URI . "tinymce/tinymce/jquery.tinymce.min.js";
            bpfw_register_js("Tinymce_jquery", $jspath, false, array("Tinymce"));

            bpfw_register_js_inline("pdfmanager_tinymce", $this->getHeaderJs());

        }

    }

    public array $getVariablesErrors = array();

    /**
     * Summary of getVariablesForTextReplacement
     * @param ?int $id
     * @param bool $cancelAndReturnErrorOnMissingValues
     * @return array|string
     * @throws Exception
     */
    function getVariablesForTextReplacement(int $id, string $modelUsed, bool $cancelAndReturnErrorOnMissingValues = true, $categoryId = null): array|string
    {

        if($categoryId === null){
            $categoryId = getorpost("category");
        }

        if(empty($categoryId) && $categoryId !== 0){
            $categoryId = current($this->pdfPages)->id;
        }

        if (empty($dateFormat)) {
            $dateFormat = bpfw_get_defaultDateFormat();
        }

        if(empty($modelUsed)){
            throw new Exception("specify model used parameter");
        }

        $model = bpfw_createModelByName($modelUsed);
        $variables = array();




        $primaryKeyOfModel = $model->tryGetKeyName();


        $variables["BASE_URI"] = BASE_URI;
        $variables["APP_IMGS_PATH"] = APP_IMGS_PATH;
        $variables["PARENT_IMGS_PATH"] = PARENT_IMGS_PATH;
        $variables["APP_IMGS_URI"] = APP_IMGS_URI;
        $variables["PARENT_IMGS_URI"] = PARENT_IMGS_URI;
        $variables["BPFW_WWW_URI"] = BPFW_WWW_URI;

        $variables["currentDate"] = date($dateFormat);

        foreach(bpfw_getUser() as $key=>$value){
            // for the mail signature etc
            $variables["currentuser.$key"] = $value;
        }



        if($id == null)return $variables;

        $modelData = $model->GetEntry($id);
        if($modelData == null){
            throw new Exception("no entry in $modelUsed with key $primaryKeyOfModel found");
        }

        $variables = bpfw_setVariables($modelData, $modelUsed, $variables);

        // set main Data
        /*foreach($modeldata as $key=>$value){
            $variables["$modelUsed.$key"] = $value;
        }*/

        if($categoryId){
            // fetch fetch all models that contain the primary key as foreign key
            // might be costly if you have a big application and is better done manually then
            $models = dbModel::getAllModels(array(getorpost("p"), "pdfmanager"));
            foreach($models as $modelName=>$model){


                if($modelName != $modelUsed) {
                    $dbfields = $model->getDbModel();

                    if(!empty($dbfields[$primaryKeyOfModel])){
                        $data  = $model->DbSelect(" $primaryKeyOfModel = $id");
                        $rowCount = 1;
                        if(!empty($data)){
                            foreach($data as $row) {
                                //foreach($row as $colname=>$col) {
                                $modelData = $model->GetEntry($row[$model->tryGetKeyName()]);
                                if ($rowCount == 1) {
                                    $variables = bpfw_setVariables($modelData, $modelUsed, $variables);
                                    //$variables["$modelName.$colname"] = $col;
                                }else{
                                    $variables = bpfw_setVariables($modelData, $modelUsed."_".$rowCount, $variables);
                                    //$variables["$modelName.{$colname}_{$rcount}"] = $col;
                                }
                                $rowCount++;
                                //}
                            }
                        }else {
                            // set empty value so that the variables will be replaced in pdf & mail
                            foreach ($dbfields as $colname => $info) {
                                $variables["$modelName.{$colname}"] = "";
                            }
                        }
                    }
                }
            }
        }

        $pages = $this->getDocumentsFromCategoryId($categoryId);

        foreach($pages as $document){
            $page = $document->createPdfPageFromPdfDocument();
            if($page != null){
                $variables = $page->getCustomVariablesForPage($variables);
            }

        }

        $rcpt = $this->getRcptFromCategoryId(getorpost("category"));



        $usermodel = bpfw_createModelByName("user");

        if ($rcpt == RCPT_USER) {

            $user = $usermodel->GetEntry($variables["user.userId"]);
            if (empty($user)) {
                $this->getVariablesErrors[] = "Not set: user";

                if ($cancelAndReturnErrorOnMissingValues)
                    throw new Exception ("Not set: user");
            }

            foreach ($user->GetKeyValueArray(false) as $key => $value) {
                $variables["rcpt" . "." . $key] = $variables["user" . "." . $key];
            }
            $variables["rcpt.salutation_n"] = $variables["user.salutation_n"];
            $variables["rcpt.salutationFull"] = $variables["user.salutationFull"];
            $variables["rcpt.salutation"] = $variables["user.salutation"];
        }

        if ($rcpt == RCPT_INTERN && bpfw_isLoggedIn()) {

            $loggedin_user = $usermodel->GetEntry(bpfw_getUserId());
            if (empty($loggedin_user)) {
                $this->getVariablesErrors[] = "Not set: currentuser";

                if ($cancelAndReturnErrorOnMissingValues)
                    throw new Exception ("Not set: currentuser");
            }

            foreach ($loggedin_user->GetKeyValueArray(false) as $key => $value) {
                $variables["rcpt" . "." . $key] = $variables["currentuser" . "." . $key];
            }
            $variables["rcpt.salutation_n"] = $variables["currentuser.salutation_n"];
            $variables["rcpt.salutationFull"] = $variables["currentuser.salutationFull"];
            $variables["rcpt.salutation"] = $variables["currentuser.salutation"];
        }

        if ($rcpt == RCPT_CUSTOMER) {


            $customer = $usermodel->GetEntry($variables["customer.userId"]);
            if (empty($customer)) {
                $this->getVariablesErrors[] = "Not set: customer";

                if ($cancelAndReturnErrorOnMissingValues)
                    throw new Exception ("Not set: customer");
            }

            foreach ($customer->GetKeyValueArray(false) as $key => $value) {
                $variables["rcpt" . "." . $key] = $variables["customer" . "." . $key];
            }
            if(!empty($variables["customer.salutation_n"]))
                $variables["rcpt.salutation_n"] = $variables["customer.salutation_n"];
            if(!empty($variables["customer.salutationFull"]))
                $variables["rcpt.salutationFull"] = $variables["customer.salutationFull"];
            $variables["rcpt.salutation"] = $variables["customer.salutation"];
        }

        return $variables;
    }

    /**
     * @param string $id
     * @paramstring  $title
     * @param int $status
     * @param string $recipient
     * @param string $attachmentTitle
     * @param bool $addMailtextAsCover
     * @param PdfDocument|PdfDocument[] $documents
     * @return void
     */
    function addPdfCategory(string $id, string $title, int $status, string $recipient, string $attachmentTitle = "Documents", bool $addMailtextAsCover = true, array $documents = array())
    {

        $this->pdfPages[$id] = new PdfCategory($id, $title, $status, $recipient, $attachmentTitle, $addMailtextAsCover, $documents);

    }

    /**
     * @throws Exception
     */
    function addPdfDocument($id, $title, string $category, $group = null, $selected = true)
    {

        if (!isset($this->pdfPages[$category])) {
            throw new Exception("Unknown pdf Category: " . $category);
        }

        $this->pdfPages[$category]->addDocument(new PdfDocument($id, $title, $category, $group, $selected));

    }

    function getHeaderJs(): string
    {

        ob_start();

        ?>
        <script>

            tinyMCE.init({
                selector: '.bpfw_pdfmanager_tinymce',
                skin: 'oxide',
                width: 600,
                height: 300,
                menubar: false,
                statusbar: false,
                language_url : '<?php echo BPFW_WWW_URI . 'tinymce_languages/langs/de.js'; ?>',
                language: 'de',
                plugins: [
                    'advlist autolink link image lists charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                    'save table directionality emoticons template paste'
                ],
                /*content_css: 'css/content.css',*/
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'
            });

        </script>

        <?php

        return ob_get_clean();

    }

    public function getAllPdfPagesForCategory($catid){

        // var_dump($entries);
        /**
         * @var PdfDocument[][] $pdfAttachments
         */
        $pdfAttachments = array();

        foreach ($this->pdfPages[$catid]->documents as $docid => $document) {

            /**
             * @var PdfDocument $attachment
             */
            $attachment = $document;

            $group = $document->group;
            if(empty($pdfAttachments[$group]))$pdfAttachments[$group] = array();

            $pdfAttachments[$document->group][$docid] = $document;

        }

        return bpfw_do_filter(PdfmanagerModel::FILTER_PDFMANAGER_PDF_ATTACHMENT_CONTENT, $this->getSlug(), array($pdfAttachments, $catid));

    }


    /**
     * @throws Exception
     */
    public function fetchAllMailAttachments(): array
    {

        $retval = array();

        // add global attachments
        $attachmentModel = bpfw_createModelByName("emailattachments"); // new EmailattachmentsModel();

        $entries = $attachmentModel->DbSelect();

        foreach ($entries as $key => $entry) {

            $categories = $entry["category"];

            if (!empty($categories)) {

                $json = stripslashes($categories);
                $entries[$key]["category"] = json_decode($json, true);

                foreach ($entries[$key]["category"] as $categoryname) {

                    if (empty($retval[$categoryname])) {
                        $retval[$categoryname] = array();
                    }

                    $retval[$categoryname][] = $entry;

                }

            }

        }

        $retval = bpfw_do_filter(PdfmanagerModel::FILTER_PDFMANAGER_EMAIL_ATTACHMENTS, $this->getSlug(), array($retval));

        return $retval;

    }



    /**
     * @return string
     */
    public function GetTableName(): string
    {
        return "pdfmanager";
    }

    public function GetTitle(): string
    {

        $retval = "<div style='padding-left:30px;'>";
        $retval .= "Pdf Mailer";
        $retval .= "</div>";
        return $retval;

    }

    /**
     * Summary of getPdfCategoriesAsStringStringArray
     * @return string[]
     */
    function getPdfCategoriesAsStringStringArray(): array
    {

        $retval = array();

        foreach ($this->pdfPages as $pdfcategory) {

            $retval[$pdfcategory->id] = $pdfcategory->title;
        }

        return $retval;

    }

    /**
     * @param $categoryId
     * @return PdfDocument[]
     */
    function getDocumentsFromCategoryId($categoryId): array
    {

        $categorydata = $this->getCategoryFromCategoryId($categoryId);
        if ($categorydata == null) return array();

        return $categorydata->documents;

    }


    function getRcptFromCategoryId($categoryId): string
    {


        $categorydata = $this->getCategoryFromCategoryId($categoryId);

        if ($categorydata == null) return "advisor";

        return $categorydata->recipient;

    }

    /**
     * Summary of getCategoryFromCategoryId
     * @param ?string $categoryId
     * @return ?PdfCategory
     */
    function getCategoryFromCategoryId(?string $categoryId): ?PdfCategory
    {

        if ($categoryId == null) return null;

        foreach ($this->pdfPages as $pdfcategory) {
            if ($pdfcategory->id == $categoryId) {
                return $pdfcategory;
            }
        }

        return null;


    }

    function getMailtextAsCoverFromCategoryId(?string $categoryId): bool
    {
        $categoryData = $this->getCategoryFromCategoryId($categoryId);
        if (empty($categoryData)) return "(category not set)";
        return $categoryData->addMailtextAsCover;

    }

    function getAttachmentTitle($categoryId): string
    {

        $categoryData = $this->getCategoryFromCategoryId($categoryId);

        if (empty($categoryData)) return "(category not set)";

        return $this->view->insertVariables($this->variables, $categoryData->attachmentTitle);

    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     */
    protected function loadDbModel(): array
    {
        return array();
    }

    protected function initializeCategories()
    {

        // define your pdfs / mails
        //$this->addPdfCategory("invoice", "Invoice example", 0, RCPT_USER, "Invoice", true);
        //$this->addPdfCategory("customer_mail", "empty mail to customer", 0, RCPT_USER, "info", true);

        // define the pdf parts
        //$this->addPdfDocument("invoice_example", "Invoice", "invoice");
        //$this->addPdfDocument("hours_made_pdf", "Hours made", "invoice");

    }

    public function getCustomFonts($fontData){
        return $fontData;
    }

    public function getDefaultFont(string $defaultFont) : string{
        return $defaultFont;
    }

}
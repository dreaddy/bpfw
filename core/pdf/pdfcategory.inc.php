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




require_once(BPFW_PDF_PATH . "pdfdocument.inc.php");


class PdfCategory
{

    var string $id;
    var string $title;
    var int $status;
    var string $attachmentTitle;
    var array $documents;
    var string $recipient;
    var bool $addMailtextAsCover;

    /**
     * Summary of __construct
     * @param string $id
     * @param string $title
     * @param int $status
     * @param string $recipient
     * @param string $attachmentTitle
     * @param bool $addMailtextAsCover
     * @param PdfDocument|PdfDocument[] $documents
     */
    function __construct(string $id, string $title, int $status, string $recipient, string $attachmentTitle, bool $addMailtextAsCover = true, PdfDocument|array $documents = array())
    {

        if (!bpfw_isAdmin() && getorpost("filter") != bpfw_getUserId() && !bpfw_creatingTables()) {
            die("not you");
        }

        $this->id = $id;
        $this->title = $title;

        $this->documents = array();
        $this->status = $status;

        $this->attachmentTitle = $attachmentTitle;

        $this->recipient = $recipient;

        $this->addMailtextAsCover = $addMailtextAsCover;

        if (!empty($documents) && is_array($documents)) {
            foreach ($documents as $key => $document) {
                $this->documents[$key] = $document;
            }
        }

    }

    /**
     * add pdfdocument
     * PdfDocument document
     */
    function addDocument(PdfDocument $document): void
    {
        $this->documents[] = $document;
    }




}




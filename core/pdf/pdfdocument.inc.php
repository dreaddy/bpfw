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



class PdfDocument
{

    var string $id;
    var string $title;
    var string $category;
    var ?string $group;
    var mixed $selected;

    /**
     * Summary of __construct
     * @param string $id
     * @param string $title
     * @param string $category
     * @param ?string $group
     * @param bool $selected
     */
    function __construct(string $id, string $title, string $category, ?string $group = "", bool $selected = true)
    {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->group = $group;
        $this->selected = $selected;
    }

    public function createPdfPageFromPdfDocument($nullIfNotCustomized = true) : ?PdfPage
    {
        if(file_exists(APP_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".inc.php")) {
            require_once APP_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".inc.php";
            return new $this->id($this->id);
        }
        else  if(file_exists(APP_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".php")) {
            require_once APP_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".php";
            return new $this->id($this->id);
        }
        else  if(file_exists(PARENT_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".inc.php")) {
            require_once PARENT_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".inc.php";
            return new $this->id($this->id);
        }
        else if(file_exists(PARENT_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".php")) {
            require_once PARENT_PDF_PATH."classes".DIRECTORY_SEPARATOR.$this->id.".php";
            return new $this->id($this->id);
        }

        if(!$nullIfNotCustomized){
            require_once BPFW_PDF_PATH."pdfpage.inc.php";
            return new PdfPage($this->id);
        }

        return null;

    }

}



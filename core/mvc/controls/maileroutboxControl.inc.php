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

use JetBrains\PhpStorm\NoReturn;

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class MaileroutboxControl extends DefaultControl
{


    #[NoReturn] function handleAjaxCommand(string $command): void
    {


        if ($command == "dl_mailattachment") {

            bpfw_check_htaccess_files();

            if (!bpfw_isAdmin()) die("no permission");

            // check path security here?

            $id = getorpost("filter");
            $att_nr = getorpost("att_nr");


            if (!is_numeric($id)) {
                echo "filter is invalid (nonum)";
                die();
            }

            $data = $this->model->DbSelectSingleOrNullByKey($id);

            if (empty($data)) {
                echo "filter is invalid" . die();
            }

            if (empty($data["attachments"])) {
                echo "no attachments";
                die();
            }

            $data = json_decode($data["attachments"]);

            $attachment = $data[$att_nr];


            //Read the filename
            $filename = BASE_PATH . $attachment->fullpath; // $_GET['path'];

            //Check the file exists or not
            if (file_exists($filename)) {

                //Define header information
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: 0");
                header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
                header('Content-Length: ' . filesize($filename));
                header('Pragma: public');

                //Clear system output buffer
                flush();

                //Read the size of the file
                readfile($filename);

                //Terminate from the script
                die();
            } else {
                echo "File does not exist.";
            }


        } else {
            parent::handleAjaxCommand($command);
        }


        die();

    }

}
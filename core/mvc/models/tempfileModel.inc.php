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




class TempfileModel extends BpfwModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);

        $this->minUserrankForShow = USERTYPE_CUSTOMER;
        $this->minUserrankForAdd = USERTYPE_ADMIN;
        $this->minUserrankForDuplicate = USERTYPE_INVALID;
        $this->minUserrankForDelete = USERTYPE_CONSULTANT;

        if (isset($_GET['cleantempfiles'])) {
            $this->cleanTempFiles();
        }

        $this->ignoreOnImportExport = true; //important, otherwise import will fail

    }


    /**
     * delete old Tempfiles (generated pdfs)
     * @throws Exception
     */
    function cleanTempFiles()
    {

        $entries = $this->DbSelect(" created < NOW() - INTERVAL 1480 MINUTE "); // 1480
        // var_dump($entries);
        foreach ($entries as $entry) {

            if (!empty($entry["path"]) && bpfw_strStartsWith($entry["path"], realpath(TEMP_PATH))) {

                if ($entry["needsCleanup"] == 1 || $entry["needsCleanup"]) {

                    if (file_exists($entry["path"]))
                        unlink($entry["path"]);
                }

                $this->DeleteEntry($entry["tempfileId"]);

            }

        }

    }

    /**
     * Summary of addTempFile
     * @param string $path
     * @param string $filename
     * @param int $userid
     * @param string $type
     * @param bool $needsCleanup
     * @return int
     * @throws Exception
     */
    function addTempFile(string $path, string $filename, int $userid, string $type, bool $needsCleanup = true): int
    {

        $values = array("path" => realpath($path), "outputFilename" => $filename, "created" => time(), "type" => $type, "createdByUser" => $userid, "needsCleanup" => $needsCleanup);

        return $this->DbInsert($values);

        // throw new Exception("not implemented yet");

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "tempfile";
    }

    public function GetTitle(): string
    {
        return "Temporäre Dateien";
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("tempfileId");
            $this->addTextField("path", "Pfad und Dateiname Server");
            $this->addTextField("outputFilename", "Dateiname");
            $this->addTextFieldNumeric("createdByUser", "User");
            $this->addTimestamp("created", "Timestamp");
            $this->addCheckbox("needsCleanup", "Datei manuell löschen", array(FORMSETTING::DISABLED => true));
            $this->addTextField("type", "Typ");

        }

        return $this->dbModel;

    }

}


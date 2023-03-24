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


class MetadataModel extends BpfwModel
{

    function __construct($values = null, $autocompleteVariables = true)
    {
        parent::__construct($values, $autocompleteVariables);

        $this->minUserrankForEdit = USERTYPE_ADMIN;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_ADMIN; // open mailcreation instead
        $this->minUserrankForPrint = USERTYPE_ADMIN;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "metadata";
    }

    /**
     * Summary of addMetadata
     * @param string $type
     * @param int $id
     * @param string|array|null|DateTime $json_data
     * @param bool $updateExisting
     * @return int|null new or old memoryId
     * @throws Exception
     */
    function setMetadata(string $type, int $id, DateTime|array|string|null $json_data, bool $updateExisting = true): ?int
    {

        $existing = $this->getMetadata($type, $id);

        if ($json_data !== null && !is_string($json_data)) {
            $json_data = json_encode($json_data);
        }

        if (empty($existing)) {

            return $this->DbInsert(array("type" => $type, "ID" => $id, "json_data" => $json_data, "created" => time()));

        } else {

            if ($updateExisting) {

                $affected = $this->DbUpdate(array("memoryId" => $existing['memoryId'], "type" => $type, "ID" => $id, "json_data" => $json_data, "created" => time()));

                if ($affected != 1) {
                    // throw new Exception("update failed for existing entry $type $id");
                }

                return $existing['memoryId'];

            }

        }

        return null;

    }

    /**
     * Summary of getMetadata
     * @param $type
     * @param $id
     * @return array|null
     * @throws Exception
     */
    function getMetadata($type, $id): ?array
    {

        $id = (int)$id;
        $type = $this->getDb()->escape_string($type);


        $vals = $this->DbSelect(" ID = '$id' and type = '$type'");

        if (count($vals) > 1) {
            throw new Exception("metadata added more than once: $id $type");
        } else if (!empty($vals)) {
            $data = current($vals);

            if (!empty($data['json_data']) && isJson($data['json_data'])) {
                $data['json_data'] = json_decode($data['json_data']);
            }

            return $data;

        }

        return null;

    }

    function GetTitle(): string
    {
        return "Metadaten";
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        if (empty($this->dbModel)) {

            $this->addPrimaryKey("memoryId");


            $this->addTimestamp("created", "erstellt am");

            $this->addTextField("type", "Typ", "default", array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));
            $this->addTextField("ID", "ID", "default", array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));

            $this->addTextField("json_data", "JSON", "default", array(LISTSETTING::HIDDENONLIST => false, FORMSETTING::POSITION => POSITION_RIGHT, FORMSETTING::DISABLED => true));


        }

        return $this->dbModel;

    }

}
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

// require_once(BPFW_CORE_PATH . "rest/restmethods.inc.php");

/**
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class BackupControl extends DefaultControl
{


    /*

    $this->addPrimaryKey("backupId", "ID", BpfwDbFieldType::TYPE_INT, 'text', array(LISTSETTING::HIDDENONLIST=>false) );

    $this->addDatepicker("backupdate", "Datum", array(LISTSETTING::HIDDENONLIST=>false) );

        $this->addTextFieldNumeric("files", "Dateien");

        $this->addTextField("pathJson", "JSON");

        $this->addTextField("pathFiles", "Dateien");

        $this->addTextField("size", "Dateigröße");

        $this->addComboBoxIntkeybased("type", "Type", new EnumHandlerArray(array(0=>"vollständiges Backup")));

    */


    function handleAjaxCommand(string $command): void
    {


        global $database;

        //    $model =$this->model;


        if ($command == "createBackupFull") {


            //  echo "execute backup";
            $this->createNewBackup();

        } else if ($command == "createBackupPartial") {

            // echo "execute partial backup";
            $this->createNewBackup(false);

        } else if ($command == "downloadFileBackup") {

            if (empty($_GET["id"])) {
                throw new Exception("no id set");
            }

            $entryid = $_GET["id"];

            $data = $this->model->GetEntry($entryid);

            $file = $data->pathFiles;

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($file));
            // ob_end_flush();
            @readfile($file);


        } else if ($command == "downloadDbBackup") {

            if (empty($_GET["id"])) {
                throw new Exception("no id set");
            }


            $entryid = $_GET["id"];

            $data = $this->model->GetEntry($entryid);

            $file = $data->pathJson;

            if (!file_exists($file)) {
                echo __("Backup not found").": $file";
                return;
            }

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($file));
            // ob_end_flush();
            @readfile($file);


        } else {
            parent::handleAjaxCommand($command);
        }


    }

    /**
     * @param bool $backupFiles
     * @return int
     * @throws ReflectionException
     * @throws Exception
     */
    function createNewBackup(bool $backupFiles = true): int
    {

        $backupDirectory = bpfw_loadSetting(SETTING_BACKUP_DIRECTORY);


        // files created

        if (empty($backupDirectory)) {
            throw new Exception("backup Directory not set");
        }

        // echo $backupDirectory;

        if (!file_exists($backupDirectory)) {
            if (mkdir($backupDirectory, 0777, true) === FALSE) {
                throw new Exception("can't create backup directory");
            }
        }

        // create db entry, create subfolder


        $submitValues = array(
            "files" => -1,
            "pathJson" => "",
            "pathFiles" => "",
            "size" => "",
            "backuptype" => $backupFiles ? 0 : 1,
            "backupdate" => date("Y-m-d H:i:s", time()),
        );


        $newID = $this->model->AddEntry($submitValues, true, true);
        // "$newID is the new id";

        if ($newID > 0) {

            if (!bpfw_strEndsWith($backupDirectory, DIRECTORY_SEPARATOR)) {
                $backupDirectory = $backupDirectory . DIRECTORY_SEPARATOR;
            }

            $pathBackup = $backupDirectory . $newID . " (" . date("Y-m-d") . ")" . DIRECTORY_SEPARATOR;
            $pathJson = $pathBackup . "db" . DIRECTORY_SEPARATOR;
            $pathFiles = $pathBackup . "files" . DIRECTORY_SEPARATOR;


            if (!file_exists($pathJson) && !file_exists($pathFiles)) {

                if (mkdir($pathJson, 0777, true) !== FALSE && mkdir($pathFiles, 0777, true) !== FALSE) {

                    // dirs existing, copy

                    $jsonfilename = $pathJson . "backup_db_" . date("Y-m-d") . "_" . bin2hex(random_bytes(15)) . "_json.zip";
                    $zippath = $pathFiles . "backup_files_" . date("Y-m-d") . "_" . bin2hex(random_bytes(15)) . ".zip";

                    // echo $jsonfilename;
                    // echo $zippath;




                    $filecount = 0;
                    if ($backupFiles) {

                        $filecount = bpfw_fileCount(UPLOADS_PATH);

                    }

                    $submitValues = array(

                        "backupId" => $newID,

                        //   "backupdate"=> date("Y-m-d H:i:s") ,
                        "files" => $filecount,
                        "pathJson" => $jsonfilename,
                        "pathFiles" => $zippath,
                        "pathBackup" => $pathBackup,
                        "size" => -1,
                        //"backuptype"=>$backupFiles?0:1

                    );

                    $this->model->EditEntry($submitValues, true);

                    // echo BASE_URI."?p=export&model=all&ajaxCall=true&command=json";
                    // echo $jsonfilename;

                    require_once(BPFW_MVC_PATH."controls/exportControl.inc.php");
                    $exportControl = new ExportControl();

                    /**
                     * @var ExportModel $model
                     */
                    $model = $exportControl->model;

                    if (empty($model)) {
                        $exportControl->model = bpfw_createModelByName("export");
                        $model = $exportControl->model;
                    }

                    $allModels = $model->getAllModelsWithTable();


                    //file_put_contents($jsonfilename, "");


                    $zip = new ZipArchive();

                    /*$tmp_location = @tempnam ( TEMP_PATH , 'tmp_' );

                    if(!file_exists(TEMP_PATH)){
                        mkdir(TEMP_PATH,0600,true);
                    }

                    while(!file_exists($tmp_location)){
                        $tmp_location = @tempnam ( TEMP_PATH , 'tmp_' );
                    }*/

                    if ($zip->open($jsonfilename, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
                        exit("cannot open <$jsonfilename>\n");
                    }


                    //$zip->setCompressionName('bar', ZipArchive::CM_DEFLATE);

                    foreach ($allModels as $modelname => $model) {

                        echo $modelname . ":" . memory_get_usage() . "<br>";

                        $jsoncontent = $exportControl->getExportData("json", false, $modelname);

                        if (!empty($jsoncontent)) {
                            $zip->addFromString($modelname . ".json", $jsoncontent);
                            // $zip->setCompressionName($modelname.".json", ZipArchive::CM_STORE); -> 30 werden zu 200 mb, dafür import ggf. schneller
                        }

                        $jsoncontent = null;

                        // file_put_contents($jsonfilename, $exportControl->getExportData("json", false, $modelname, true), FILE_APPEND );

                        // file_put_contents($jsonfilename, "<json_split \>", FILE_APPEND );

                    }

                    $zip->close();

                    // old file_put_contents($jsonfilename, $exportControl->getExportData("json", false, "all") );


                    //echo file_get_contents($jsonfilename);die();
                    echo "DB Backup ok<br><br>";

                    if ($backupFiles) {
                        bpfw_copy_dir_as_zip(UPLOADS_PATH, $zippath);
                        echo "Datei Backup ok<br><br>";
                    }


                    $filesize = bpfw_format_size(bpfw_foldersize($pathBackup));

                    $submitValues = array(
                        "backupId" => $newID,
                        "size" => $filesize
                    );

                    $this->model->EditEntry($submitValues, true);

                    echo __("Db Entry Complete")."<br><br>";

                    echo __("Backup Complete")."<br><br>";

                } else {
                    throw new Exception("can't create backup directory");
                }


            } else {
                throw new Exception("backup failed - path already existing");
            }


            // copy db json backup

            // copy all dl files


        } else {
            throw new Exception("invalid ID");
        }


        return $newID;


    }


}
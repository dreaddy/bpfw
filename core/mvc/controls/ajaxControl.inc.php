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

/**
 * ajax commands - used by pdf generator
 *
 * used by pdf Generator
 *
 * @version 1.0
 * @author torst
 */
// used by pdf Generator
class AjaxControl extends DefaultControl
{


    /**
     * @throws Exception
     */
    function handleActions(): void
    {

        global $database;

        if (bpfw_isAjax()) {
            $this->handleAjaxCommand(getorpost("command"));
        } else {
            throw new Exception("for common ajax calls only");
        }


    }

    function handleAjaxCommand(string $command): void
    {

        global $database;

        if ($command == "handleCronjobs") {


            $sigauth = getorpost("sigauth");

            $isJsExecution = getorpost("is_js_execution") == "1" || getorpost("is_js_execution") == 1 || getorpost("is_js_execution") == true || getorpost("is_js_execution") == "true";

            $auth_should = bpfw_loadSetting(SETTING_EXTERNAL_CRONJOB_CALL_CODE);
            if (bpfw_isLoggedIn() || isset($sigauth) && $sigauth == $auth_should) {

                $executeDirectly = (int)getorpost("executeDirectly");

                echo "running cronjobs ...";

                //require_once("bpfw/mvc/models/cronjobModel.inc.php");

                // müssen auch anonym laufen, damit von extern gestartet werden kann
                $cjmodel = bpfw_createModelByName("cronjob"); //new CronjobModel();

                $cronjobs = array();

                if ($executeDirectly) {
                    $cronjobs = $cjmodel->DbCachedSelectAndCreateObjectArray(" cronjobId = '$executeDirectly'");
                } else {
                    if ($isJsExecution) {
                        $cronjobs = $cjmodel->DbCachedSelectAndCreateObjectArray(" js_execution is not null");
                    } else {
                        $cronjobs = $cjmodel->DbCachedSelectAndCreateObjectArray();
                    }
                }

                $db = bpfw_getDb();

                foreach ($cronjobs as $key => $job) {

                    echo "checking job:" . $job->cronjobId . "<br><br>";

                    $execute = false;

                    if ($executeDirectly == $key) {
                        echo "direct call - timecheck ignored & executing...<br><br>";
                        $execute = true;
                    } else {
                        if (empty($job->lastExecuted)) {
                            $execute = true;
                        } else {

                            //echo $job->lastExecuted;
                            //echo "<br>";

                            //echo date("Y-m-d H:i:s", time());
                            //echo date("Y-m-d H:i:s", bpfw_datestringToTimestamp($job->lastExecuted));

                            $timestampLastExecuted = bpfw_DateStringToTimestamp($job->lastExecuted);
                            $timestampNow = time();

                            $minutespassedSinceLastExecution = ($timestampNow - $timestampLastExecuted) / 60;

                            // echo $secondsPassed;  echo "<br>";  echo "<br>";
                            //echo date("Y-m-d H:i:s", $job->lastExecuted);

                            if ($minutespassedSinceLastExecution > $job->frequency) {
                                $execute = true;
                            }

                        }

                    }

                    if ($execute) {
                        echo "<br>\r\n";

                        echo "doing job:" . $job->cronjobId . "<br><br>";

                        $cjkey = $job->cronjobId;

                        $data = array("cronjobId" => $cjkey, "lastExecuted" => time(), "lastExecutionMessage" => "(lock, still running)");

                        $cjmodel->DbUpdate($data, bpfw_getDb());

                        $response = "Cronjob ausgeführt!";
                        ob_start();
                        switch ($job->type) {

                            case CRONJOB_BACKUP_BASIC:

                                require_once(BPFW_MVC_PATH."controls/backupControl.inc.php");
                                //require_once("bpfw/mvc/models/backupModel.inc.php");

                                $backupmodel = bpfw_createModelByName("backup"); //new BackupModel();
                                $backupcontrol = new BackupControl($backupmodel);


                                $response = "";
                                try {
                                    $backupcontrol->createNewBackup(false);
                                } catch (Exception $exception) {
                                    $response = $exception->getMessage();
                                }
                                echo $response;


                                break;

                            case CRONJOB_BACKUP_FULL:
                                require_once(BPFW_MVC_PATH."controls/backupControl.inc.php");
                                // require_once("bpfw/mvc/models/backupModel.inc.php");

                                $backupmodel = bpfw_createModelByName("backup"); //new BackupModel();
                                $backupcontrol = new BackupControl($backupmodel);


                                $response = "";

                                try {
                                    $backupcontrol->createNewBackup();
                                } catch (Exception $exception) {
                                    $response = $exception->getMessage();
                                }
                                echo $response;

                                //  $cjmodel->DbUpdate(array("cronjobId"=>$job->cronjobId,"lastExecuted"=>time(),"lastExecutionMessage"=>"Backup Response:<br><br>  $response"), $db);
                                break;


                            case CRONJOB_DELETE_BACKUPS:

                                //require_once("bpfw/mvc/controls/backupControl.inc.php");
                                // require_once("bpfw/mvc/models/backupModel.inc.php");

                                $backupmodel = bpfw_createModelByName("cronjob"); // new BackupModel();

                                /// $entries = $backupmodel->DbDeleteByWhere("backupdate < (CURRENT_DATE() - INTERVAL 30 DAY)");

                                $entries = $backupmodel->DbSelect("backupdate < (CURRENT_DATE() - INTERVAL 30 DAY)");

                                foreach ($entries as $key2 => $entry) {
                                    $backupmodel->DeleteEntry($key2); //  delete one by one, so the files will also be deleted
                                }


                                //$backupmodel->DeleteEntry();
                                // $this->deleteBackupFolder($key);


                                // var_dump($entries);

                                echo "Backups gelöscht!";

                                break;

                            case CRONJOB_DELETE_TEMPFILES:

                                //require_once("bpfw/mvc/models/tempfileModel.inc.php");
                                $tempfilemodel = bpfw_createModelByName("tempfile"); //new TempfileModel();
                                $tempfilemodel->cleanTempfiles();

                                break;


                            case CRONJOB_CLEAN_DATABASE:

                                // TODO: temptables aufräumen

                                break;

                            default:
                                echo "nicht ausgeführt, Cronjobhandler für " . $job->cronjobId . " nicht vorhanden!";
                                break;


                        }

                        $response = ob_get_clean();

                        $cjmodel->DbUpdate(array("cronjobId" => $job->cronjobId, "lastExecuted" => time(), "lastExecutionMessage" => "Response: $response"), $db);

                    }

                }

            }

        } else {
            parent::handleAjaxCommand($command);
        }


    }


}
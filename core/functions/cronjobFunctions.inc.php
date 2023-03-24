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




function bpfw_getCronjobtimesArray(): array
{

    return array(
        5 => __("Every 5 minutes"),
        15 => __("Every 15 minutes"),
        60 => __("Hourly"),
        6 * 60 => __("Every 6 hours"),
        12 * 60 => __("Every 12 hours"),
        24 * 60 => __("Daily"),
        7 * 24 * 60 => __("Weekly"),
        30 * 24 * 60 => __("Monthly"),
    );

}


const CRONJOB_BACKUP_BASIC = "CRONJOB_BACKUP_BASIC";
const CRONJOB_BACKUP_FULL = "CRONJOB_BACKUP_FULL";
const CRONJOB_DELETE_BACKUPS = "CRONJOB_DELETE_BACKUPS";
const CRONJOB_DELETE_TEMPFILES = "CRONJOB_DELETE_TEMPFILES";
const CRONJOB_CLEAN_DATABASE = "CRONJOB_CLEAN_DATABASE";

const CRONJOB_SEND_MAILS_10 = "CRONJOB_SEND_MAILS_10";
const CRONJOB_SEND_MAILS_50 = "CRONJOB_SEND_MAILS_50";
const CRONJOB_SEND_MAILS_100 = "CRONJOB_SEND_MAILS_100";


function bpfw_getCronjobtasksArray(): array
{

    if (function_exists("custom_getCronjobtasksArray")) {
        return array_merge(getDefaultCronjobs(), custom_getCronjobtasksArray());
    }

    return getDefaultCronjobs();

}

function getDefaultCronjobs(): array{

    return array(

        CRONJOB_BACKUP_BASIC => __("Backup(Database only)"),
        CRONJOB_BACKUP_FULL => __("Backup(Complete)"),

        CRONJOB_DELETE_BACKUPS => __("Delete backups older than 1 month"),
        CRONJOB_DELETE_TEMPFILES => __("Delete database"),

        CRONJOB_CLEAN_DATABASE => __("Clean database"),

        CRONJOB_SEND_MAILS_10 => __("Send 10 mails from outbox"),
        CRONJOB_SEND_MAILS_50 => __("Send 50 mails from outbox"),
        CRONJOB_SEND_MAILS_100 => __("Send 100 mails from outbox"));

//    const CRONJOB_DELETE_BACKUPS = "backups";
//    const CRONJOB_DELETE_TEMPFILES = "tempfiles";

}

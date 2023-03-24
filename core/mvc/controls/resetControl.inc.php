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
 * defaultListView short summary.
 *
 * defaultListView description.
 *
 * @version 1.0
 * @author torst
 */
class ResetControl extends DefaultControl
{


    function __construct($model)
    {
        parent::__construct($model);
    }


    /**
     * @throws Exception
     */
    function handleActions(): void
    {

        // TODO: improve
        if (bpfw_isAdmin() && isset($_GET["command"])) {

            if (getorpost("command") == "performReset") {
                if (APP_NAME == "erfolgspfad") {
                    die("deaktiviert");
                }

                $models = getorpost("models");

                foreach ($models as $model) {

                    bpfw_createModelByName($model)->deleteTempTable();
                    bpfw_createModelByName($model)->deleteTable();

                    // TODO: linktables löschen

                    echo "gelöscht: $model, ";
                }

                die();
            }
        }

        parent::handleActions();

    }

}
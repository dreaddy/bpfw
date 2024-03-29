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

error_reporting(E_ALL);
ini_set("display_errors", 1);

const APP_NAME = '{{{APP_NAME}}}';
const APP_TITLE = '{{{APP_TITLE}}}';

const DB_HOST = '{{{DB_HOST}}}';
const DB_USER = '{{{DB_USER}}}';
const DB_PASSWORD = '{{{DB_PASSWORD}}}';
const DB_DATABASE = '{{{DB_DATABASE}}}';

defineIfNotDefined('DEFAULT_FULLSIZE_HEADERBAR', false );

const TEMPLATE_THEME = false;

const STARTING_PAGE = "{{{STARTING_PAGE}}}";

const DEBUG = false;

// const BPFW_DEBUG_MODE = true;
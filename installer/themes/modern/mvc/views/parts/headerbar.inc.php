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
?>

<?php
global $model;
?>


<div style="" class="fa fa-bars mobileHamburger"></div>

<div class="rowElement rowheader">



    <div class="tablecell" style="width: 100%;display: inline-block;">


        <div id="header-headline">


            <div class="topbarelement breadcrump"><?php echo theme_get_navigationBreadcrump($model);?></div>


        </div>

        <div id="AdminHeaderLogo">

            <!--<img style="margin-top:-20px;height:60px;display:inline-block" src="<?php echo PARENT_IMGS_URI; ?>EP_Logo_300px.png" />-->

            <div class="topbarelement userinfomenu"><?php echo theme_get_navigationUsermenu($model);?></div>

        </div>

    </div>
    <style>

    </style>

    <div id="info_messages"></div>

</div>

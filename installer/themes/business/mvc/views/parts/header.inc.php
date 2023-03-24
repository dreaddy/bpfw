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
?><!DOCTYPE html>
<html lang="<?php echo bpfw_getCurrentLanguageCode("en");?>">

<head>
 
  <title><?php echo APP_TITLE; ?></title>
    		
  <meta charset="UTF-8" />

  <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700,800" rel="stylesheet">
    <!-- <meta 
     name='viewport' 
     content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' 
/> -->
 <!--  <meta name="viewport" content="width=950, user-scalable=yes" />  -->
  <meta name="viewport" content="width=device-width, initial-scale=1">  

 
  <link rel="shortcut icon" href="<?php echo PARENT_IMGS_URI?>icons/icon-32.png" sizes="32x32" />
  <link rel="icon" href="<?php echo PARENT_IMGS_URI?>icons/icon-192.png" sizes="192x192" />
  <link rel="apple-touch-icon-precomposed" href="<?php echo PARENT_IMGS_URI?>icons/icon-180.png" />
  <meta name="msapplication-TileImage" content="<?php echo PARENT_IMGS_URI?>icons/icon-270.png" />

  <?php 
 
  if(file_exists(APP_VIEWS_PARTS_PATH."libraries.inc.php")){
      require_once(APP_VIEWS_PARTS_PATH."libraries.inc.php");
  }else{
     require_once(BPFW_VIEWS_PARTS_PATH."libraries.inc.php");
  }

  ?>
    
   <?php
   
   echo bpfw_do_translations_js();

   ?>
</head>

<?php



$xtraclass = "";
global $model;

if(!bpfw_isLoggedIn())$xtraclass = "login-page login";
else{
    $xtraclass = bpfw_getActivePage()."-page adminarea model-".get_parent_class($model);
}
?>


<body class="<?php echo $xtraclass; ?>" data-page="<?php echo bpfw_getActivePage(); ?>" data-bpfwpath="<?php echo htmlspecialchars(BPFW_BASE_URI);?>" data-current_lang="<?php echo bpfw_getCurrentLanguageCode(); ?>">



<!-- Full size modal view -->
<div class="modal fade fullsizeIframeDialog" id="fullsizeIframeDialog" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!--<h5 class="modal-title" id="ModalLongTitle"></h5>-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="margin:auto; padding:20px;">                

                <div class="h_contentframe">
                    <iframe class="contentframe" frameborder="0" allowfullscreen>
                    </iframe>
                </div>
                    
            </div>

          <!--  <div class="modal-footer defaultlistmodalview">
                
            </div> -->

        </div>
    </div>
</div>

<div class="contentWrapper <?php echo $xtraclass; ?>" style="" >

    
<?php
  global $model;
?>

<div class="rowElement rowheader">



    <div class="tablecell" style="width: 100%;display: inline-block;">

        <div id="header-headline">
            <div style="" class="fa fa-bars mobileHamburger"></div>
        <div class="topbarelement breadcrump"><?php echo theme_get_navigationBreadcrump($model);?></div>
        </div>

        <div id="AdminHeaderLogo">
            <!--<img style="margin-top:-20px;height:60px;display:inline-block" src="<?php echo PARENT_IMGS_URI; ?>EP_Logo_300px.png" />-->
                <div class="topbarelement userinfomenu"><?php echo theme_get_navigationUsermenu($model);?></div>
        </div>

    </div>

</div>

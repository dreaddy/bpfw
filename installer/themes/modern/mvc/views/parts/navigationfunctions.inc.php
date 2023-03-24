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



$_vis_category = "";

$_nav_category = "";

$_nav_categoryname = "";


$_vis_categoryname = "";

function nav_setCurrentCategory($category){
    global $_nav_category;
    $_nav_category = $category;
}



function nav_getCurrentCategory(){
    global $_nav_category;

    if(empty($_nav_category))return "single";

    return $_nav_category;
}


function nav_setCurrentCategoryName($categoryname){
    global $_nav_categoryname;
    $_nav_categoryname = $categoryname;
}



function nav_getCurrentCategoryName(){
    global $_nav_categoryname;
    return $_nav_categoryname;
}


function nav_setVisibleCategory($category){
    global $_vis_category;
    $_vis_category = $category;
}

function nav_getVisibleCategory(): ?string{
    global $_vis_category;
    return $_vis_category;
}



function nav_setVisibleCategoryName($categoryname){
    global $_vis_categoryname;
    $_vis_categoryname = $categoryname;
}



function nav_getVisibleCategoryName(): ?string{
    global $_vis_categoryname;
    return $_vis_categoryname;
}


function make_navigation_header_theme($name, $category, $icon = "", $link = null){

    nav_setCurrentCategory($category);
    nav_setCurrentCategoryName($name);
    $datalink = "data-link=''";;

    if($link != null){
        $datalink = "data-link='$link'";
    }


    $isactive ='';

    if($link != null && $link == getorpost("p")){

        $isactive = "active";

    }

    echo "<div class='navcategorywrap $isactive' data-category='".nav_getCurrentCategory()."'>";

    echo "<li $datalink class='navigationheader $isactive' data-category='".nav_getCurrentCategory()."' ><i style='' class=\"nav-icon_wws $icon\"></i>";
    echo "<h4>$name</h4>";
    echo "</li>";
}

function make_navigation_header_theme_end($name){
    echo "</div>";
}


function make_link_theme($caption, $navelement, $current,  $icon = "", $onclick = "", array $params = array()): void
{
    $isActive = $navelement == $current;
    foreach ($params as $k => $v) {
        if (!isset($_GET[$k]) || $_GET[$k] != $v) $isActive = false;
    }
    make_link_with_url_theme($caption, "?p=" . $navelement, $navelement == $current,  $onclick, "", $params);

}


function make_link_with_url_theme($caption, $navelement, $activeLink = false, $onclick_js="", $target="", array $getParams = array()): void
{
    $params = "";
    if (!empty($getParams)) {
        foreach ($getParams as $p => $v) {
            $params .= "&" . urlencode($p) . "=" . urlencode($v);
        }
    }

    if($activeLink){
        nav_setVisibleCategory(nav_getCurrentCategory());
        nav_setVisibleCategoryName(nav_getCurrentCategoryName());
    }

    $onclick ="";

    if(!empty($onclick_js)){
        $onclick = "onClick=\"$onclick_js\"";
    }

    echo '<li  data-category="'.nav_getCurrentCategory().'"  class="';

    if( $activeLink )
        echo ' current ';

    echo ' navelement" >';

    echo '<a  target="'.$target.'" '.$onclick.' alt="'.$caption.'" title="'.$caption.'"  href="'.$navelement.$params.'">';
    // echo '<a '.$onclick.' alt="'.$caption.'"  href="'.$navelement.'">';

    echo '<p class="navtext">'.$caption.'</p>';

    echo '</i></a></li>';

}

function theme_get_navigationBreadcrump($model){
    ob_start();
    echo nav_getVisibleCategoryName()."<br><div class='bc_detail'> > <div class='bc_active_element'>".$model->getTitle()."</div></div>";
    return ob_get_clean();
}


function theme_get_navigationUsermenu($model){

    ob_start();
    echo "<div class='openUsermenu'>".bpfw_getUserName()." <i class='fa fa-angle-down'> </i></div>";
    echo "<div class='bc_detail usermenuContent'>
    <a href='?logout'>".__("Logout")."</a>
    <a href='?p=user'>".__("Profile")."</a>
    </div>";
    return ob_get_clean();

}
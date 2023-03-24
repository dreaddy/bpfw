<?php





$_vis_category = "";

$_nav_category = "";

$_nav_categoryname = "";


$_vis_categoryname = "";

function nav_setCurrentCategory($category): void
{
    global $_nav_category;
    $_nav_category = $category;
}



function nav_getCurrentCategory(): string
{
    global $_nav_category;
    return $_nav_category;
}


function nav_setCurrentCategoryName($categoryname): void
{
    global $_nav_categoryname;
    $_nav_categoryname = $categoryname;
}



function nav_getCurrentCategoryName(): string
{
    global $_nav_categoryname;
    return $_nav_categoryname;
}


function nav_setVisibleCategory($category): void
{
    global $_vis_category;
    $_vis_category = $category;
}

function nav_getVisibleCategory(): ?string
{
    global $_vis_category;
    return $_vis_category;
}



function nav_setVisibleCategoryName($categoryname): void
{
    global $_vis_categoryname;
    $_vis_categoryname = $categoryname;
}



function nav_getVisibleCategoryName(): ?string
{
    global $_vis_categoryname;
    return $_vis_categoryname;
}


function make_navigation_header_theme($name, $category, $icon = ""): void
{

    nav_setCurrentCategory($category);
    nav_setCurrentCategoryName($name);

    echo "<li class='navigationheader' data-category='".nav_getCurrentCategory()."' ><i style='' class=\"nav-icon_wws $icon\"></i>";
    echo "<h4>$name</h4>";
    echo "</li>";
}

function make_navigation_header_theme_end($name): void
{

}


function make_link_theme($caption, $navelement, $current, $icon="", $onclick = "", array $params = array())
{

    $isActive = $navelement == $current;
    foreach ($params as $k => $v) {
        if (!isset($_GET[$k]) || $_GET[$k] != $v) $isActive = false;
    }
    make_link_with_url_theme($caption, "?p=" . $navelement, $isActive, $onclick, "", $params);

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
        $onclick = " onClick=\"$onclick_js\"";
    }

    echo '<li  data-category="'.nav_getCurrentCategory().'"  class="';

    if( $activeLink )
        echo ' current ';

    echo ' navelement" >';

    echo '<a  target="'.$target.'" '.$onclick.' alt="'.$caption.'" title="'.$caption.'"  href="'.$navelement.$params.'">';

    echo '<p class="navtext">'.$caption.'</p>';

    echo '</i></a></li>';

}

function theme_get_navigationBreadcrump($model): bool|string
{
    ob_start();
    echo nav_getVisibleCategoryName()."<br><div class='bc_detail'> > <div class='bc_active_element'>".$model->getTitle()."</div></div>";
    return ob_get_clean();
}


function theme_get_navigationUsermenu($model): bool|string
{

    ob_start();
    echo "<div class='openUsermenu'>".bpfw_getUserName()." <i class='fa fa-angle-down'> </i></div>";
    echo "<div class='bc_detail usermenuContent'>
    <a href='?logout'>".__("Logout")."</a>
    <a href='?profile'>".__("Profile")."</a>
    </div>";
    return ob_get_clean();

}
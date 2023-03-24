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




function make_navigation_header($name): void
{
    echo "<li class='navigationheader' >";
    echo "<h4>$name</h4>";
    echo "</li>";
}


function bpfw_nav_make_link($caption, $navElement, $current, $icon = "", $getParams = array(), $onClickJs = ""): void
{

    $isActive = true;

    if ($navElement != $current) $isActive = false;

    foreach ($getParams as $k => $v) {
        if (!isset($_GET[$k]) || $_GET[$k] != $v) $isActive = false;
    }

    foreach ($_GET as $k => $v) {

        if ($k == "p") continue;

        if (!isset($getParams[$k]) || $getParams[$k] != $v) $isActive = false;

    }


    bpfw_nav_make_link_with_url($caption, "?p=" . $navElement, $isActive, $icon, $getParams, $onClickJs);

}

function bpfw_nav_make_minimize_button($caption = "<<"): void
{
    echo '<li class="changesize-li minimize-li" >';
    echo '<a href="#" class="minimize-button changesize-button">' . $caption . '</a></li>';
}


function bpfw_nav_make_maximize_button($caption = ">>"): void
{
    echo '<li class="changesize-li maximize-li" style="display:none">';
    echo '<a href="#" class="maximize-button changesize-button">' . $caption . '</a></li>';
}

function bpfw_nav_make_link_with_url($caption, $navElement, $activeLink = false, $icon = "", $getParams = array(), $onclickjs = "", $target = ""): void
{

    echo '<li  class="';

    if ($activeLink)
        echo ' current ';

    echo ' navelement" >';

    $params = "";

    if (!empty($getParams)) {
        foreach ($getParams as $p => $v) {
            $params .= "&" . urlencode($p) . "=" . urlencode($v);
        }
    }

    $onclick = "";
    if (!empty($onclickjs)) {
        $onclick = "onClick=\"$onclickjs\"";
    }

    echo '<a target="' . $target . '" ' . $onclick . ' alt="' . $caption . '" title="' . $caption . '"  href="' . $navElement . $params . '"><i style="width:100%;" class="nav-icon ' . $icon . '">';
    echo '<p class="navtext" style="font-family:\'Montserrat\', Tahoma, Arial, sans-serif; display:inline-block;">' . $caption . '</p>';
    echo '</i></a></li>';

}


class BpfwNavElement{

    var string $caption;
    var string $id;
    var string $icon;
    var array $params;

    public function __construct(string $caption, string $id, string $icon, array $params = array())
    {
        $this->icon = $icon;
        $this->id = $id;
        $this->caption = $caption;
        $this->params = $params;
    }

}

function bpfw_getAllNavigationMailElements() : array{
    return array(
        new BpfwNavElement("New mail", "mail", "fa fa-envelope", array("type" => "single")),
        new BpfwNavElement("Massmail", "mailer", "fa fa-envelope", array("type" => "mass"))   ,
        new BpfwNavElement("Outbox", "maileroutbox", "fa fa-mail-bulk", array("box" => "outbox"))
    );
}

function bpfw_getAllNavigationAdminElements(): array{

    return array(


        //new BpfwAdminNavElement("Settings", "appsettings", "fa fa-cog"),

        new BpfwNavElement("Import", "import", "fa fa-arrow-down"),
        new BpfwNavElement("Export", "export", "fa fa-arrow-up"),
        new BpfwNavElement("Backup", "backup", "fa fa-hdd"),
        new BpfwNavElement("DbSync", "dbsync", "fas fa-exchange-alt"),
        new BpfwNavElement("Searchindex", "createsearchindex", "fas fa-database"),
        new BpfwNavElement("E-mail attachments", "emailattachments", "fa fa-paperclip"),
        new BpfwNavElement("Cronjobs", "cronjob", "fa fa-user"),
        new BpfwNavElement("Languages", "language", "fa fa-user"),
        new BpfwNavElement("Translations", "translation", "fa fa-user"),
        new BpfwNavElement("Crudaction log", "datalog", "fa fa-cog"),
        new BpfwNavElement("Reset models", "reset", "fa fa-cog"),
    );



}

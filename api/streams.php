<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/streams") -> find("ul[id=stream-list]", 0);
$stms = array();

foreach($page -> children as $child)
{
    $item = array();
    $item["Preview"] = $child -> find("img", 0) -> src;
    $item["Streamer"] = trim($child -> find("b[style=font-size: 12px; color: #007099;]", 0) -> plaintext);
    $item["Viewers"] = trim(str_replace("Viewers", "", $child -> find("span[style=color: #666;]", 0) -> plaintext));
    $item["Link"] = trim($child -> find("a[style=display: inline-block; float: right; color: #666;]", 0) -> href);
    $item["Title"] = trim(str_replace("Title", "", str_replace("&raquo;", "", $child -> find("div[style=padding: 6px; padding-left: 0; padding-right: 18px;]", 0) -> plaintext)));
    $item["Desc"] = trim(str_replace("Description", "", str_replace("&raquo;", "", $child -> find("div[style=padding: 3px; padding-left: 0; padding-right: 18px;]", 0) -> plaintext)));
    array_push($stms, $item);
}

echo json_encode($stms);
?>

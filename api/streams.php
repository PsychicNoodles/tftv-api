<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

$page = file_get_html("http://teamfortress.tv/streams") -> find("ul[id=stream-list]", 0);
if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("page not found");
}

header("Content-Type: application/json");

$stms = array();

foreach($page -> children as $child)
{
    $item = array();
    $item["Preview"] = $child -> find("img", 0) -> src;
    $item["Streamer"] = trim($child -> find("b[style=font-size: 12px; color: #007099;]", 0) -> plaintext);
    $item["Viewers"] = trim(str_replace("Viewers", "", $child -> find("span[style=color: #666;]", 0) -> plaintext));
    $item["Link"] = trim($child -> find("a[style=display: inline-block; float: right; color: #666;]", 0) -> href);
    $item["Title"] = trim(str_replace("Title", "", str_replace("&raquo;", "", $child -> find("div[style=padding: 6px; padding-left: 0; padding-right: 18px;]", 0) -> plaintext)));
    $item["Desc"] = $child -> find("div[style=padding: 3px; padding-left: 0; padding-right: 18px;]", 0) !== NULL ? trim(str_replace("Description", "", str_replace("&raquo;", "", $child -> find("div[style=padding: 3px; padding-left: 0; padding-right: 18px;]", 0) -> plaintext))) : NULL;
    if(isset($_GET["streamer"]))
    {
        if(strcasecmp($item["Streamer"], $_GET["streamer"]) === 0)
            echo json_encode($item);
    }
    else
        array_push($stms, $item);
}

if(isset($_GET["streamer"]))
    die(json_encode(array()));

echo json_encode($stms);
?>

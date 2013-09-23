<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(!isset($_GET["page"]))
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("\"page\" GET parameter required");
}

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/schedule/event/" . $_GET["page"]) -> find("table[id=calendar-table]", 0);
$evt = array();

$evt["Title"] = trim($page -> find("span[id=event-title]", 0) -> plaintext);
if(strpos($page -> find("span.event-meta", 0) -> plaintext, "Series") == 0)
{
    $evt["Region"] = trim($page -> find("span.event-meta", 0) -> find("i", 0) -> plaintext);
    if(($page -> find("div.e-upcoming", 0) !== NULL))
        $evt["Time"] = trim(str_replace("START", "", $page -> find("div.e-upcoming", 0) -> plaintext));
    else
        $evt["Time"] = trim(str_replace("START", "", $page -> find("div.e-completed", 0) -> plaintext));
    $evt["Series"] = null;
}
else
{
    $evt["Region"] = trim($page -> find("span.event-meta", 1) -> find("i", 0) -> plaintext);
    if(($page -> find("div.e-upcoming", 0) !== NULL))
        $evt["Time"] = trim(str_replace("START", "", $page -> find("div.e-upcoming", 0) -> plaintext));
    else
        $evt["Time"] = trim(str_replace("START", "", $page -> find("div.e-completed", 0) -> plaintext));}
$evt["Date"] = trim(substr($page -> find("div[id=event-date]", 0) -> plaintext, 0, strrpos($page -> find("div[id=event-date]", 0) -> plaintext, "Region")));
$evt["Stream Title"] = trim(str_replace("STREAM", "", $page -> find("div[style=padding: 8px;]", 1) -> plaintext));
if(strpos($page -> find("div[style=padding: 8px;]", 1) -> plaintext, "N/A") == 0)
    $evt["Stream Link"] = "http://teamfortress.tv" . $page -> find("div[style=padding: 8px;]", 1) -> find("a", 0) -> href;
else
    $evt["Stream Link"] = "N/A";
$evt["Mumble"] = trim(str_replace("MUMBLE", "", $page -> find("div[style=padding: 8px; border-top: 1px solid #ccc;]", 2) -> plaintext));
$evt["STV"] = trim(str_replace("STV", "", $page -> find("div[style=padding: 8px; border-top: 1px solid #ccc;]", 3) -> plaintext));
$evt["Desc"] = trim($page -> find("div[id=event-desc]", 0) -> plaintext);

echo json_encode($evt);
?>

<?php
if(!file_exists("../../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../../scripts/simple_html_dom.php";

if(!isset($_GET["page"]))
{
    http_response_code(400);
    die("\"page\" GET parameter required");
}

$page = file_get_html("http://teamfortress.tv/schedule/event/" . $_GET["page"]) -> find("table[id=calendar-table]", 0);
$evt = array();

$evt["Title"] = htmlentities(trim($page -> find("span[id=event-title]", 0) -> plaintext));
$evt["Series"] = htmlentities(trim($page -> find("span.event-meta", 0) -> find("b") -> plaintext));
$evt["Date"] = htmlentities(trim($page -> find("div[id=event-date]", 0) -> plaintext));
$evt["Flag"] = htmlentities(substr($page -> find("span[id=event-flag-align]", 0) -> class, 10));
$evt["Time"] = htmlentities(trim(str_replace("START", "", $page -> find("div.e-upcoming", 0) -> plaintext)));

?>

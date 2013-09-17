<?php
if(!file_exists("../../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../../scripts/simple_html_dom.php";

if(!isset($_GET["page"]))
{
    http_response_code(400);
    die("\"page\" GET parameter required");
}

header("Content-Type: application/xml");

$page = file_get_html("http://teamfortress.tv/schedule/event/" . $_GET["page"]) -> find("table[id=calendar-table]", 0);
$evt = new SimpleXMLElement("<event></event>");

$evt -> addChild("Title", trim($page -> find("span[id=event-title]", 0) -> plaintext));
$evt -> addChild("Series", trim($page -> find("span.event-meta", 0) -> find("b") -> plaintext));
$evt -> addChild("Date", trim($page -> find("div[id=event-date]", 0) -> plaintext));
$evt -> addChild("Flag", substr($page -> find("span[id=event-flag-align]", 0) -> class, 10));
$evt -> addChild("Time", trim(str_replace("START", "", $page -> find("div.e-upcoming", 0) -> plaintext)));
$evt -> addChild("Stream", trim(str_replace("STREAM", "", $page -> find("div[style=padding: 8px;]", 3) -> plaintext)));
$evt -> addChild("Mumble", trim(str_replace("MUMBLE", "", $page -> find("div[style=padding: 8px; border-top: 1px solid #ccc]", 0) -> plaintext)));
$evt -> addChild("STV", trim(str_replace("STV", "", $page -> find("div[style=padding: 8px; border-top: 1px solid #ccc]", 1) -> plaintext)));
$evt -> addChild("Desc", trim($page -> find("div[id=event-desc]") -> plaintext));

echo $evt -> asXML();
?>

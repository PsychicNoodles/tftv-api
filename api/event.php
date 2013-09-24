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

$page = file_get_html("http://teamfortress.tv/schedule/event/" . $_GET["page"]);
if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("page not found");
}

header("Content-Type: application/json");

$evtpage = $page -> find("table[id=calendar-table]", 0);
$cmtspage = $page -> find("div[id=thread-container]", 0);
$evt = array();
$cmts = array();

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

foreach($cmtspage -> children() as $child)
{
    $item = array();
    $item["ID"] = substr($child -> id, 8);
    $item["Number"] = trim($child -> find("span.post-num", 0) -> plaintext);
    $item["Author Name"] = trim($child -> find("a", 2) -> plaintext);
    $item["Author Link"] = $child -> find("a", 2) -> href;
    $item["Flag"] = substr($child -> find("span", 1) -> class, 10, 2);
    $item["Stars"] = (int) substr($child -> find("span", 4) -> class, 5) + (int) substr($child -> find("span", 5) -> class, 5) + (int) substr($child -> find("span", 6) -> class, 5) + (int) substr($child -> find("span", 7) -> class, 5);
    $item["Frags"] = trim($child -> find("[id=" . $child -> id . "]", 0) -> plaintext);
    foreach($child -> find("div.post-footer", 0) -> children() as $e)
        $child -> find("div.post-footer", 0) -> innertext = str_replace($e -> outertext, "", $child -> find("div.post-footer", 0) -> innertext);
    if(strpos($child -> find("div.post-footer", 0) -> plaintext, "&sdot;") === FALSE)
    {
        $item["Published"] = trim(str_replace("Posted", "", $child -> find("div.post-footer", 0) -> plaintext));
        $item["Edited"] = NULL;
    }
    else
    {
        $item["Published"] = trim(str_replace("Posted", "", substr($child -> find("div.post-footer", 0) -> plaintext, 0, strrpos($child -> find("div.post-footer", 0) -> plaintext, "&sdot;"))));
        $item["Edited"] = trim(str_replace("Posted", "", str_replace("&sdot;", "", str_replace($item["Published"], "", $child -> find("div.post-footer", 0) -> plaintext))));
    }
    $item["Body Raw"] = trim(htmlentities(str_replace($child -> find("div.post-body", 0) -> outertext, "", $child -> find("div.post-body", 0) -> plaintext)));
    $item["Body Plaintext"] = trim(htmlentities($child -> find("div.post-body", 0) -> plaintext));
    array_push($cmts, $item);
}

$evt["Comments"] = $cmts;

echo json_encode($evt);
?>

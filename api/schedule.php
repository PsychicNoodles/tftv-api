<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(isset($_GET["page"]))
    $page = file_get_html("http://teamfortress.tv/schedule/index/" . ((int) $_GET["page"] > 0 ? (int) $_GET["page"] : 1)) -> find("table[id=calendar-table]", 0);
else
    $page = file_get_html("http://teamfortress.tv/schedule") -> find("table[id=calendar-table]", 0);
if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("page not found");
}

header("Content-Type: application/json");

$evts = array();
$date = "";

foreach($page -> children() as $child)
{
    if($child -> plaintext == "")
    {
        //do nothing
    }
    else if(strpos(trim($child -> plaintext), "START") === 0)
    {
        $item = array(); //split up for easier reading
        $item["Title"] = trim($child -> find("a", 0) -> plaintext);
        $item["Link"] = str_replace("\\/", "/", $child -> find("a", 0) -> href);
        $item["Desc"] = trim($child -> find("div.event-desc", 0) -> plaintext);
        $item["Date"] = strpos(trim($date), "\t") ? substr(trim($date), 0, strpos(trim($date), "\t")) : trim($date); //because explode doesn't work on Heroku for some reason
        $item["Time"] = trim(str_replace("START", "", $child -> find("div[style=padding: 8px;]", 0) -> plaintext));
        $item["Stream"] = trim(str_replace("STREAM", "", $child -> find("td", 1) -> plaintext));
        $item["Flag"] = substr($child -> find("span", 0) -> class, 10); //because explode doesn't work on Heroku for some reason
        array_push($evts, $item);
    }
    else
    {
        $date = $child -> plaintext;
    }
}

echo json_encode($evts);
?>
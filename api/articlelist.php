<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

$page = file_get_html("http://teamfortress.tv/articles" . (isset($_GET["page"]) && is_numeric($_GET["page"]) ? "/" . $_GET["page"] : "")) -> find("table.list-table", 0);
if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("page not found");
}

header("Content-Type: application/json");

$arts = array();

foreach($page -> children() as $child)
{
    if(!$child -> find("th", 0))
    {
        $item = array();
        $item["Title"] = trim($child -> find("a", 0) -> plaintext);
        $item["Link"] = $child -> find("a", 0) -> href;
        $item["Author"] = trim(str_replace($item["Title"], "", str_replace("by ", "", $child -> find("td", 0) -> plaintext)));
        $item["Published"] = trim($child -> find("td", 1) -> plaintext);
        $item["Category Name"] = trim($child -> find("a", 1) -> plaintext);
        $item["Category Link"] = $child -> find("a", 1) -> href;
        $item["Series Name"] = $item["Series Link"] ? trim($child -> find("a", 2) -> plaintext) : null;
        $item["Series Link"] = $child -> find("a", 2) -> href == "/articles/series/" ? null : $child -> find("a", 2) -> href;
        array_push($arts, $item);
    }
}

echo json_encode($arts);
?>

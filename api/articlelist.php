<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/articles") -> find("table.list-table", 0);

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
        $item["Category Title"] = trim($child -> find("a", 1) -> plaintext);
        $item["Category Link"] = $child -> find("a", 1) -> href;
        $item["Series Title"] = $item["Series Link"] ? trim($child -> find("a", 2) -> plaintext) : null;
        $item["Series Link"] = $child -> find("a", 2) -> href == "/articles/series/" ? null : $child -> find("a", 2) -> href;
        array_push($arts, $item);
    }
}

echo str_replace("\\/", "/", json_encode($arts));
?>

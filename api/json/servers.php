<?php
if(!file_exists("../../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../../scripts/simple_html_dom.php";

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/servers") -> find("div[id=col-center-inner]");

$servertype;
$svrs = array();

foreach($page -> children() as $child)
{
    if($child -> class == "table-container")
    {
        foreach($child -> find("table.list-table server-table", 0) -> children() as $serv)
        {
            if($child -> find("th", 0))
                $servertype = trim($child -> find("th", 0) -> plaintext);
            else
            {
                $item = array();
                $item["Type"] = $servertype;
                $item["Link"] = $child -> find("a", 0) -> href;
                $item["Title"] = trim($child -> find("a", 0) -> plaintext);
                $item["Players"] = trim($child -> find("td", 1) -> plaintext);
                $item["Map"] = trim($child -> find("td", 2) -> plaintext);
                $item["Updated"] = trim($child -> find("td", 3) -> plaintext);
                array_push($svrs, $item);
            }
        }
    }
}

echo json_encode($svrs);
?>

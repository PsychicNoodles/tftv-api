<?php
if(!file_exists("../../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../../scripts/simple_html_dom.php";

$page = file_get_html("http://teamfortress.tv/articles") -> find("table.list-table");

$xml = new SimpleXMLElement("<articles></articles>");

foreach($page -> children() as $child)
{
    if(!$page -> find("th", 0))
    {
        $item = $xml -> addChild("Entry");
        $item -> addChild("Link", $child -> find("a", 0) -> href);
        $item -> addChild("Title", trim($child -> find("a", 0) -> plaintext));
        $item -> addChild("Author", trim(str_replace($item["Title"], "", str_replace("by ", "", $child -> find("td", 0)))));
        $item -> addChild("Published", trim($child -> find("td", 1)));
        $item -> addChild("Category Link", $child -> find("a", 1) -> href);
        $item -> addChild("Category Title", trim($child -> find("a", 1) -> plaintext));
        $item -> addChild("Series Link", $child -> find("a", 2) -> href == "/articles/series/" ? null : $child -> find("a", 2) -> href);
        $item -> addChild("Series Title", $item["Series Link"] ? trim($child -> find("a", 2) -> plaintext) : null);
    }
}

echo $xml -> asXML();
?>

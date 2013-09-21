<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/forum") -> find("div[id=col-center-inner]", 0);

$forumcat;
$frms = array();

foreach($page -> children() as $child)
{
    if($child -> class == "table-container")
    {
        $child = $child -> find("table.list-table", 0);
        foreach($child -> children() as $sub)
        {
            if($sub -> find("th", 0) !== NULL)
            {
                $forumcat = trim($sub -> find("a", 0) -> plaintext);
                array_push($frms, array("Title" => $forumcat, "Link" => $sub -> find("a", 0) -> href));
            }
            else
            {
                $item = array();
                $item["Title"] = trim($sub -> find("a", 0) -> plaintext);
                $item["Link"] = $sub -> find("a", 0) -> href;
                $item["Threads"] = trim($sub -> find("td", 1) -> plaintext);
                $item["Posts"] = trim($sub -> find("td", 2) -> plaintext);
                $item["Last Title"] = trim($sub -> find("a", 1) -> plaintext);
                $item["Last Link"] = $sub -> find("a", 1) -> href;
                $item["Last Author"] = trim(str_replace($item["Last Title"], "", substr($sub -> find("td", 4) -> plaintext, 0, strrpos($sub -> find("td", 4) -> plaintext, " posted"))));
                $item["Last Published"] = trim(substr($sub -> find("td", 4) -> plaintext, strrpos($sub -> find("td", 4) -> plaintext, " posted") + 7));
                array_push($frms, $item);
            }
        }
    }
}

echo str_replace("\\/", "/", json_encode($frms));
?>

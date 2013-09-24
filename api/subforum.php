<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(!isset($_GET["sub"]))
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("\"sub\" GET parameter required");
}

$page = file_get_html("http://teamfortress.tv/forum/" . (is_numeric($_GET["sub"]) ? "category/" . $_GET["sub"] : $_GET["sub"]) . (isset($_GET["page"]) && is_numeric($_GET["page"]) ? "/" . $_GET["page"] : "")) -> find("table.list-table", 0);
if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("page not found");
}

header("Content-Type: application/json");

$psts = array();

foreach($page -> children() as $child)
{
    if($child -> class === "") //the head of the table has no class attribute, all the posts have an empty string class
    {
        $item = array();
        $item["Title"] = trim($child -> find("a", 0) -> plaintext);
        $item["Link"] = $child -> find("a", 0) -> href;
        $item["Pages"] = sizeof($child -> find("td", 1) -> children()) - 4 == 0 ? 1 : sizeof($child -> find("td", 1) -> children()) - 4;
        foreach($child -> find("td", 1) -> children() as $e)
            $child -> find("td", 1) -> innertext = str_replace($e -> outertext, "", $child -> find("td", 1) -> innertext);
        $item["Author"] = trim(str_replace("&nbsp;", "", strtok(trim($child -> find("td", 1) -> plaintext), "\n")));
        $item["Author"] = trim(substr($item["Author"], 2, strrpos($item["Author"], "in") - 2));
        $item["Frags"] = trim($child -> find("td", 2) -> plaintext);
        $item["Posts"] = trim($child -> find("td", 3) -> plaintext);
        $item["Last Link"] = $child -> find("td", 4) -> find("a", 0) -> href;
        $item["Last Published"] = trim($child -> find("td", 4) -> find("a", 0) -> plaintext);
        $item["Last Author"] = trim(str_replace("by ", "", $child -> find("td", 4) -> find("div", 0) -> plaintext));
        $item["Status"] = !ctype_space($child -> find("span.thread-status", 0) -> plaintext) ? substr(trim($child -> find("span.thread-status", 0) -> plaintext), 1, strlen(trim($child -> find("span.thread-status", 0) -> plaintext)) - 2) : null;
        array_push($psts, $item);
    }
}

echo json_encode($psts);
?>

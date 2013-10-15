<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(!isset($_GET["art"]))
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("\"art\" GET parameter required");
}

$page = file_get_html("http://teamfortress.tv/articles/view/" . $_GET["art"] . (isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : ""));
if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("page not found");
}

header("Content-Type: application/json");

$artpage = $page -> find("div[id=article-container]", 0);
$cmtspage = $page -> find("div[id=thread-container]", 0);
$art = array();
$cmts = array();

$art["Title"] = trim($artpage -> find("div[id=article-title]", 0) -> plaintext);
$art["Author Name"] = trim($artpage -> find("a", 0) -> plaintext);
$art["Author Link"] = $artpage -> find("a", 0) -> href;
$art["Category Name"] = trim($artpage -> find("a", 1) -> plaintext);
$art["Category Link"] = $artpage -> find("a", 1) -> href;
$datetime = trim(str_replace("Posted by", "", str_replace("on", "", str_replace("— Category:", "", str_replace($art["Category Name"], "", str_replace($art["Author Name"], "", $artpage -> find("div[id=article-sub-title]", 0) -> plaintext))))));
$art["Date"] = trim(substr($datetime, 0, strpos($datetime, "at")));
$art["Time"] = trim(substr($datetime, strpos($datetime, "at") + 3, 9));
$art["Body Raw"] = htmlentities(str_replace($artpage -> find("div[id=article-body]", 0) -> outertext, "", $artpage -> find("div[id=article-body]", 0) -> innertext), ENT_QUOTES, "UTF-8");
$art["Body Plaintext"] = htmlentities($artpage -> find("div[id=article-body]", 0) -> plaintext, $encoding = "UTF-8");

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

$art["Comments"] = $cmts;

echo json_encode($art);
?>

<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(!isset($_GET["page"]))
{
    http_response_code(400);
    die("\"page\" GET parameter required");
}

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/articles/view/" . $_GET["page"]) -> find("div[id=article-container]", 0);
$art = array();

$art["Title"] = trim($page -> find("div[id=article-title]", 0) -> plaintext);
$art["Author Link"] = $page -> find("a", 0) -> href;
$art["Author Name"] = trim($page -> find("a", 0) -> plaintext);
$art["Category Link"] = $page -> find("a", 1) -> href;
$art["Category Name"] = trim($page -> find("a", 1) -> plaintext);
$datetime = trim(str_replace("Posted by", "", str_replace("on", "", str_replace("— Category:", "", str_replace($art["Category Name"], "", str_replace($art["Author Name"], "", $page -> find("div[id=article-sub-title]", 0) -> plaintext))))));
$art["Date"] = trim(substr($datetime, 0, strpos($datetime, "at")));
$art["Time"] = trim(substr($datetime, strpos($datetime, "at") + 3, 9));
$art["Body Raw"] = htmlentities(str_replace($page -> find("div[id=article-body]", 0) -> outertext, "", $page -> find("div[id=article-body]", 0) -> innertext), ENT_QUOTES);
$art["Body Plaintext"] = htmlentities($page -> find("div[id=article-body]", 0) -> plaintext);

echo json_encode($art);
?>

<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(!isset($_GET["thr"]))
{
    http_response_code(400);
    die("\"post\" GET parameter required");
}

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/forum/thread/" . $_GET["thr"] . (isset($_GET["page"]) && is_numeric($_GET["page"])? $_GET["page"] : "" )) -> find("div[id=thread-container]", 0);

$psts = array();

foreach($page -> children() as $child)
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
    array_push($psts, $item);
}

echo json_encode($psts);
?>

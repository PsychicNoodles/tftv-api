<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

if(!isset($_GET["type"]))
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("\"type\" GET parameter required");
}
if(!isset($_GET["path"]))
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("\"path\" GET parameter required");
}
if(!isset($_GET["id"]))
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    die("\"id\" GET parameter required");
}

switch(strtolower($_GET["type"]))
{
    case("article"):
        $type = "/articles/view/";
        break;
    case("forum"):
        $type = "/forum/thread/";
        break;
    case("schedule"):
        ;
    case("event"):
        $type = "/schedule/event/";
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        header("Status: 404 Not Found");
        die("\"type\" GET parameter invalid");
}
$url = "http://teamfortress.tv" . $type . $_GET["path"];
for($i = 1; ; $i++)
{
    $page = file_get_html($url . "/" . $i);
    if($page -> find("title", 0) -> plaintext == "Page Cannot be Displayed")
    {
        header("HTTP/1.0 404 Not Found");
        header("Status: 404 Not Found");
        if($i === 1)
        {
            die("page not found");
        }
        die("comment not found");
    }
    $cmts = $page -> find("div[id=thread-container]", 0);
    foreach($cmts -> children() as $child)
    {
        if(substr($child -> id, 8) == $_GET["id"])
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
            
            header("Content-Type: application/json");
            die(json_encode($item));
        }
    }
}

header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");
die("page not found");
?>

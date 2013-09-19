<?php
if(!file_exists("../scripts/simple_html_dom.php"))
    exit("<h1>Internal error, please try again later.</h1>");

require "../scripts/simple_html_dom.php";

header("Content-Type: application/json");

$page = file_get_html("http://teamfortress.tv/servers") -> find("div[id=col-center-inner]", 0);

$servertype;
$svrs = array();

foreach($page -> children() as $child)
{
    if($child -> class == "table-container")
    {
        $child = $child -> find("table[class=list-table server-table]", 0);
        foreach($child -> children() as $serv)
        {
            if($serv -> find("th", 0) !== NULL)
                $servertype = trim($serv -> find("th", 0) -> plaintext);
            else
            {
                $item = array();
                $item["Type"] = $servertype;
                $item["Link"] = $serv -> find("a", 0) -> href;
                $item["Title"] = trim($serv -> find("a", 0) -> plaintext);
                $item["Players"] = trim($serv -> find("td", 1) -> plaintext);
                $item["Map"] = trim($serv -> find("td", 2) -> plaintext);
                $item["Updated"] = trim($serv -> find("td", 3) -> plaintext);
                if(isset($_GET["type"]))
                {
                    if(strcasecmp($servertype, $_GET["type"]) === 0 || strcasecmp(substr($servertype, 0, strpos($servertype, " ")), $_GET["type"]) === 0)
                        echo json_encode($item);
                }
                else
                    array_push($svrs, $item);
            }
        }
    }
}

echo json_encode($svrs);
?>

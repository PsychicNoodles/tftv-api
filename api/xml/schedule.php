<?php
try
{
    if(!@include "../../scripts/simple_html_dom.php")
        exit("<h1>Internal error, please try again later.</h1>");
}
catch(Exception $e)
{
    exit("<h1>Internal error, please try again later.</h1>");
}


$page = file_get_html("http://teamfortress.tv/schedule") -> find("table[id=calendar-table]", 0);
$date = "";
$xml = new SimpleXMLElement("<events></events>");

foreach($page -> children() as $child)
{
    if($child -> plaintext == "")
    {
        //do nothing
    }
    else if(strpos(trim($child -> plaintext), "START") === 0)
    {
        $item = $xml -> addChild("entry"); //split up for easier reading
        $item -> addChild("Date", strpos(trim($date), "\t") ? substr(trim($date), 0, strpos(trim($date), "\t")) : trim($date)); //because explode doesn't work on Heroku for some reason
        $item -> addChild("Time", trim(str_replace("START", "", $child -> find("div[style=padding: 8px;]", 0) -> plaintext)));
        $item -> addChild("Stream", trim(str_replace("STREAM", "", $child -> find("td", 1) -> plaintext)));
        $item -> addChild("Link", $child -> find("a", 0) -> href);
        $item -> addChild("Title", htmlentities(trim($child -> find("a", 0) -> plaintext)));
        $item -> addChild("Flag", substr($child -> find("span", 0) -> class, 10)); //because explode doesn't work on Heroku for some reason
        $item -> addChild("Desc", htmlentities(trim($child -> find("div.event-desc", 0) -> plaintext)));
    }
    else
    {
        $date = $child -> plaintext;
    }
}

echo $xml -> asXML();
?>
<!--<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        
        <h1>Links</h1>
        
        <p><a href="api/json/events.php">Events</a></p>
        
        <?php ?>
    </body>
</html>-->

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <h1>hello</h1>
        
        <?php
        try
        {
            if(!@include "scripts/simple_html_dom.php")
                exit("<h1>Internal error, please try again later.</h1>");
        }
        catch(Exception $e)
        {
            exit("<h1>Internal error, please try again later.</h1>");
        }
        
        
        $page = file_get_html("http://teamfortress.tv/schedule") -> find("table[id=calendar-table]", 0);
        $evts = array();
        $date = "";
                
        foreach($page -> children() as $child)
        {
            if($child -> plaintext == "")
            {
                //do nothing
            }
            else if(strpos(trim($child -> plaintext), "START") === 0)
            {
                $item = array(); //split up for easier reading
//                $item["Date"] = explode("\t", trim($date))[0];
                $item["Time"] = trim(str_replace("START", "", $child -> find("div[style=padding: 8px;]", 0) -> plaintext));
                $item["Stream"] = trim(str_replace("STREAM", "", $child -> find("td", 1) -> plaintext));
                $item["Link"] = $child -> find("a", 0) -> href;
                $item["Title"] = trim($child -> find("a", 0) -> plaintext);
//                $item["Flag"] = explode("-", $child -> find("span", 0) -> class)[1];
                $item["Desc"] = trim($child -> find("div.event-desc", 0) -> plaintext);
                array_push($evts, $item);
            }
            else
            {
                $date = $child -> plaintext;
            }
        }
        
        echo json_encode($evts);
        ?>
    </body>
</html>

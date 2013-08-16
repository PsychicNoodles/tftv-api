<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        function pg_connection_string()
        {
            
        }
        
        $db = pg_connect(pg_connection_string());
        if(!db)
        {
            echo "Database connection error.";
            exit;
        }
        
        $result = pg_query($db, "SELECT thing");
        ?>
    </body>
</html>

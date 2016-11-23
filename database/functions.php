<?php
    function dbConnect()
    {
        $dbAddress = "localhost";
        $dbUser = "root";
        $dbPassword = "";
        $dbName = "postapp";
        $mysqli = new mysqli($dbAddress, $dbUser, $dbPassword, $dbName);
        $mysqli->set_charset("utf8");
        return $mysqli;
    }
    function dbClose($mysqli)
    {
        $mysqli->close();
    }
?>
<?php
    require_once("../functions.php");

    $dbConn = dbConnect();
    $query = "SELECT istat, comune from comune order by comune asc";
    $st = $dbConn->prepare($query);
    $st->execute();
    $st->bind_result($istat,$comune);
    $comuni = array();
    while($st->fetch())
    {
        array_push($comuni, array("istat"=>$istat, "comune"=>$comune));
    }
    $st->close();
    dbClose($dbConn);
    $content = json_encode($comuni);
    file_put_contents("comuni.json", $content);
?>
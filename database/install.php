<?php
    require_once("functions.php");
    createDatabase();
    createTables();
    caricaComuni();
	echo "Fine";

    function createDatabase()
    {
		echo "Creo il database<br>";
        //DROP Database
        //CREATE database
    }
    function createTables()
    {
		echo "Aggiungo le tabelle<br>";
        //CREATE TABLES
    }
    function caricaComuni()
    {
		echo "Carico i comuni<br>";
        $myfile = fopen("listacomuni.txt", "r") or die("File dei comuni assente!");
        while(!feof($myfile))
        {
            $line = utf8_encode(fgets($myfile));
            $params = explode(";",$line);
            $istat = $params[0];
            $comune = $params[1];
            $prov = $params[2];
            $prefisso = $params[4];
            $cap = $params[5];
            $codFis = $params[6];
            inserisciComune($istat, $comune, $prov, $prefisso, $cap, $codFis);
        }
    }
    function inserisciComune($istat, $comune, $prov, $prefisso, $cap, $codFis)
    {
		echo "$istat $comune $prov $prefisso $cap $codFis <br>";
        $query = "INSERT INTO comune (istat, comune, provincia, prefisso, cap, codFiscale) VALUES (?,?,?,?,?,?)";
        $dbConn = dbConnect();
        $st = $dbConn->prepare($query);
        $st->bind_param('ssssss', $istat, $comune, $prov, $prefisso, $cap, $codFis);
        $result = $st->execute();
        $st->close();
        dbClose($dbConn);
        return $result;
    }
?>
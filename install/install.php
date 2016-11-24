<?php
	require_once("../functions.php");
    echo "Creo il database<br>";
    createDatabase();
    echo "Aggiungo le tabelle del database<br>";
    createTables();
    echo "Carico i comuni<br>";
    caricaComuni();

	if(unlink("install.php"))
        echo "install.php rimosso con successo<br>";
    else
        echo "<b>ATTENZIONE: DEVI RIMUOVERE IL FILE install.php MANUALMENTE</b><br>";
        
	echo "Fine";

    function createDatabase()
    {
        //DROP Database
        //CREATE database
    }
    function createTables()
    {
        //CREATE TABLES
    }
    function caricaComuni()
    {
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
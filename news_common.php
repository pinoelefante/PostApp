<?php
    require_once("database.php");
    require_once("functions.php");
    function SegnaComeLetta($idNews, $tipo)
    {
        $idUtente = getIdUtenteFromSession();
        if($tipo == "scuola")
            $query = "INSERT INTO news_scuola_letta (id_utente, id_news) VALUES (?,?)";
        else if($tipo == "classe")
            $query = "INSERT INTO news_scuola_classe_letta (id_utente, id_news) VALUES (?,?)";
        else if($tipo == "editor")
            $query = "INSERT INTO news_editor_letta (id_utente, id_news) VALUES (?,?)";
        else
            return StatusCodes::NEWS_COMMON_TIPO_NEWS_INVALIDO;

        return dbUpdate($query, "ii", array($idUtente,$idNews)) ? StatusCodes::OK : StatusCodes::NEWS_GIA_LETTA;
    }
    function ThankYou($idNews, $tipo)
    {
        $idUtente = getIdUtenteFromSession();
        if($tipo == "scuola")
            $query = "INSERT INTO news_scuola_thankyou (id_utente, id_news) VALUES (?,?)";
        else if($tipo=="classe")
            $query = "INSERT INTO news_scuola_classe_thankyou (id_utente, id_news) VALUES (?,?)";
        else if($tipo == "editor")
            $query = "INSERT INTO news_editor_thankyou (id_utente, id_news) VALUES (?,?)";
        else
            return StatusCodes::NEWS_COMMON_TIPO_NEWS_INVALIDO;

        return dbUpdate($query,"ii",array($idUtente,$idNews)) ? StatusCodes::OK : StatusCodes::NEWS_GIA_RINGRAZIATO;
    }
    function NotificaLettura($idNews, $tipo)
    {
        $idUtente = getIdUtenteFromSession();
        if($tipo == "scuola")
            $query = "INSERT INTO news_scuola_confermalettura (id_utente, id_news) VALUES (?,?)";
        else if($tipo == "classe")
            $query = "INSERT INTO news_scuola_classe_confermalettura (id_utente, id_news) VALUES (?,?)";
        //else if($tipo == "editor")
        //    $query = "INSERT INTO news_editor_thankyou (id_utente, id_news) VALUES (?,?)";
        else
            return StatusCodes::NEWS_COMMON_TIPO_NEWS_INVALIDO;
        return dbUpdate($query,"ii",array($idUtente,$idNews)) ? StatusCodes::OK : StatusCodes::NEWS_LETTURA_GIA_CONFERMATA;
    }
?>
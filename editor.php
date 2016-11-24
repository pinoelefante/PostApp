<?php
    session_start();
    
    require_once("enums.php");
    require_once("functions.php");
    
    $action = getParameter("action", true);
    $responseCode = StatusCodes::FAIL;
    $responseContent = "";
    switch($action)
    {
		case "RegistraEditor":
            if(isLogged(true))
            {
                $nome = getParameter("nome", true);
                $categoria = getParameter("categoria", true);
                $email = getParameter("email", true);
                $telefono = getParameter("tel", true);
                $indirizzo = getParameter("indirizzo", true);
                $localita = getParameter("localita", true);
                $responseCode = RegistraEditor($nome, $categoria, $email, $telefono, $indirizzo, $localita);
                if($responseCode == StatusCodes::OK)
                {
                    //invio email all'amministratore'
                    $dataIscr = date("d/m/Y H:i");
                    sendEmailAdmin("Iscrizione nuovo editor: $nome", 
                        "L'editor $nome si è iscritto a PostApp il $dataIscr ed è in attessa di approvazione");

                    //TODO: registrazione a Super Reader
                }
            }
            break;
        //carica l'elenco degli editor per cui l'utente può creare dei post
        case "WriterEditors":
            if(isLogged(true))
            {
                $res = ListEditor();
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "WriterEditorsDaApprovare":
            if(isLogged(true))
            {
                $res = ListEditorDaApprovare();
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "ReaderEditors":
            if(isLogged(true))
            {
                $res = ListReaderEditors();
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "AddDescrizione":

            break;
        case "LoadEditorImage":

            break;
        case "Post":
            if(isLogged(true))
            {
                $idEditor = getParameter("editor", true);
                $responseCode = VerificaAutorizzazioneAPostare($idEditor);
                if($responseCode == StatusCodes::OK)
                {
                    $titolo = getParameter("titolo", true);
                    $corpo = getParameter("corpo", true);
                    $immagine = getParameter("img");
                    $posizione = getParameter("posizione");
                    $responseCode = PostEditor($idEditor, getIdUtenteFromSession(), $titolo, $corpo, $immagine, $posizione);
                }
            }
            break;
        case "FollowEditor":
            if(isLogged(true))
            {
                $idEditor = getParameter("idEditor", true);
                $responseCode = FollowEditor($idEditor);
            }
            break;
        case "UnfollowEditor":
            if(isLogged(true))
            {
                $idEditor = getParameter("idEditor", true);
                $responseCode = UnfollowEditor($idEditor);
            }
            break;
        case "GetNotifications":
            if(isLogged(true))
            {
                $from = getParameter("from", true); //la data della news deve essere maggiore a from
            }
            break;
        case "ThanksForNews":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews", true);
                $idUtente = getIdUtenteFromSession();
            }
            break;
        case "LeggiNews":
            $idNews = getParameter("idNews", true);
            $res = GetNews($idNews);
            $responseCode = is_array($res) ? StatusCodes::OK : $res;
            if(is_array($res))
                $responseContent = $res;
            if(isLogged())
            {
                SegnaComeLetta($idNews);
            }
            break;
        case "GetNewsEditor":
            $idEditor = getParameter("idEditor", true);
            //if(isLogged(true))
            //{
                $res = GetNewsEditor($idEditor);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            //}
            break;
        default:
            $responseCode = StatusCodes::METODO_ASSENTE;
            break;
    }
    sendResponse($responseCode, $responseContent);

    function RegistraEditor($nome, $categoria, $email, $telefono, $indirizzo, $localita)
    {
        $query = "INSERT INTO editor (nome,categoria,email,telefono,indirizzo,localita) VALUES (?,?,?,?,?,?)";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ssssss", $nome, $categoria, $email, $telefono, $indirizzo, $localita);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::EDITOR_ERRORE_CREAZIONE;
            if($result == StatusCodes::OK)
            {
                $idEditor = $dbConn->insert_id;
                $idUtente = getIdUtenteFromSession();
                if(!PartecipaEditor($idEditor, $idUtente, "admin"))
                {
                    DeleteEditor($idEditor);
                    $result = StatusCodes::EDITOR_IMPOSSIBILE_ASSEGNARE_AMMINISTRATORE;
                }
            }
			$st->close();
        }
        dbClose($dbConn);
		return $result;
    }
    //autorizza l'utente a postare a nome dell'editor
    function PartecipaEditor($idEditor, $idUtente, $ruolo = "editor")
    {
        $query = "INSERT INTO editor_gestione (id_utente,id_editor,ruolo) VALUES (?,?,?)";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iis", $idUtente,$idEditor,$ruolo);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function DeleteEditor($idEditor)
    {
        $query = "DELETE FROM editor WHERE id = ?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idEditor);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    //elenco editor per cui l'utente è autorizzato a postare
    function ListEditor()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id,e.nome,eg.ruolo FROM editor AS e JOIN editor_gestione AS eg ON e.id=eg.id_editor WHERE eg.id_utente = ? AND e.approvato = 1";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idUtente);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($editorId,$editorNome,$utenteRuolo);
                $result = array();
                while($st->fetch())
                {
                    $editor = array("id"=>$editorId,
                        "nome"=>$editorNome,
                        "ruolo"=>$utenteRuolo);
                    array_push($result, $editor);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function ListEditorDaApprovare()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id,e.nome,eg.ruolo FROM editor AS e JOIN editor_gestione AS eg ON e.id=eg.id_editor WHERE eg.id_utente = ? AND e.approvato = 0";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idUtente);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($editorId,$editorNome,$utenteRuolo);
                $result = array();
                while($st->fetch())
                {
                    $editor = array("id"=>$editorId,
                        "nome"=>$editorNome,
                        "ruolo"=>$utenteRuolo);
                    array_push($result, $editor);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function ListReaderEditors()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id,e.nome FROM editor AS e JOIN editor_follow AS ef ON e.id=ef.id_editor WHERE ef.id_utente = ? AND e.approvato = 1";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idUtente);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($editorId,$editorNome);
                $result = array();
                while($st->fetch())
                {
                    $editor = array("id"=>$editorId,
                        "nome"=>$editorNome);
                    array_push($result, $editor);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function PostEditor($idEditor, $idUtente, $titolo, $corpo, $immagine, $posizione)
    {
        $query = "INSERT INTO news_editor (pubblicataDaEditor, pubblicataDaUtente, titolo, corpo, immagine, posizione, notificabile) VALUES (?,?,?,?,?,?,1)";
        $image_path = SalvaImmagine($immagine);
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iissss", $idEditor,$idUtente, $titolo, $corpo, $image_path, $posizione);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function FollowEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO editor_follow (id_utente,id_editor) VALUES (?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idEditor);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::EDITOR_SEGUI_GIA;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function UnfollowEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "DELETE FROM editor_follow WHERE id_utente = ? AND id_editor = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idEditor);
            $st->execute();
            $result = StatusCodes::OK;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetNotifications($from)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id,e.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n WHERE ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor AND ef.id_utente = ? AND n.data > ? AND n.notificabile = 1";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente, $from);
            if($st->execute())
            {
                $st->bind_result($editorId,$editorNome, $newsId, $newsTitolo, $newsCorpo, $newsData, $newsImmagine, $newsPosizione);
                $result = array();
                while($st->fetch())
                {
                    $news = array("editorId"=>$editorId,
                        "editorNome" => $editorNome,
                        "newsId" => $newsId,
                        "titolo" => $newsTitolo,
                        //"corpo" => $newsCorpo,
                        "data" => $newsData,
                        "immagine" => $newsImmagine,
                        "posizione" => $newsPosizione);
                    array_push($result, $news);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function ThanksForNews($id_utente, $id_news)
    {
        $query = "INSERT INTO news_editor_thankyou (id_utente, id_news) VALUES (?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii",$id_utente,$id_news);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::EDITOR_NEWS_GIA_RINGRAZIATO;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetNews($idNews)
    {
        $query = "SELECT ed.id, ed.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.id=?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idNews);
            if($st->execute())
            {
                $st->bind_result($editorId,$editorNome, $newsId, $newsTitolo, $newsCorpo, $newsData, $newsImmagine, $newsPosizione);
                if($st->fetch())
                {
                    $result = array("editorId"=>$editorId,
                        "editorNome" => $editorNome,
                        "newsId" => $newsId,
                        "titolo" => $newsTitolo,
                        "corpo" => $newsCorpo,
                        "data" => $newsData,
                        "immagine" => $newsImmagine,
                        "posizione" => $newsPosizione);
                }
                else
                    $result = StatusCodes::EDITOR_NEWS_NON_TROVATA;
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function SegnaComeLetta($idNews)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO news_editor_letta (id_utente, id_news) VALUES (?,?)";
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idNews);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::EDITOR_NEWS_GIA_LETTA;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetNewsEditor($idEditor)
    {
        $query = "SELECT ed.id, ed.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.pubblicataDaEditor=? ORDER BY n.data DESC";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idEditor);
            if($st->execute())
            {
                $st->bind_result($editorId,$editorNome, $newsId, $newsTitolo, $newsCorpo, $newsData, $newsImmagine, $newsPosizione);
                $result = array();
                while($st->fetch())
                {
                    $news = array("editorId"=>$editorId,
                        "editorNome" => $editorNome,
                        "newsId" => $newsId,
                        "titolo" => $newsTitolo,
                        //"corpo" => $newsCorpo,
                        "data" => $newsData,
                        "immagine" => $newsImmagine,
                        "posizione" => $newsPosizione);
                    array_push($result, $news);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function VerificaAutorizzazioneAPostare($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT eg.ruolo FROM editor_gestione AS eg JOIN editor AS e ON e.id=eg.id_editor WHERE eg.id_utente = ? AND eg.id_editor = ? AND e.approvato = 1";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idEditor);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($ruolo);
                $result = $st->fetch() ? StatusCodes::OK : StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO;
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
?>
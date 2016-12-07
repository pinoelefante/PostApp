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
                    $dataIscr = date("d/m/Y H:i");
                    sendEmailAdmin("Iscrizione nuovo editor: $nome", 
                        "L'editor $nome si è iscritto a PostApp il $dataIscr ed è in attessa di approvazione");
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
            if(isLogged(true))
            {
                $descrizione = getParameter("descrizione", true);
                $idEditor = getParameter("idEditor", true);
                $responseCode = AddDescrizioneEditor($idEditor, $descrizione);
            }
            break;
        case "AddEditorImage":
            if(isLogged(true))
            {
                $image = getParameter("immagine", true);
                $idEditor = getParameter("idEditor", true);
                $responseCode = AddImmagineEditor($idEditor, $image);
            }
            break;
        case "Post":
            if(isLogged(true))
            {
                $idEditor = getParameter("editor", true);
                $auth = VerificaAutorizzazioneAPostare($idEditor);
                if($auth == StatusCodes::OK)
                {
                    $titolo = getParameter("titolo", true);
                    $corpo = getParameter("corpo", true);
                    $immagine = getParameter("img");
                    $posizione = getParameter("posizione");
                    $responseCode = PostEditor($idEditor, getIdUtenteFromSession(), $titolo, $corpo, $immagine, $posizione);
                    if($responseCode == StatusCodes::OK)
                    {
                        //TODO: invio notifica
                    }
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
                $res = GetNotifications($from);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "ThanksForNews":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews", true);
                $idUtente = getIdUtenteFromSession();
                $responseCode = ThanksForNews($idUtente, $idNews);
            }
            break;
        case "LeggiNews":
            $idNews = getParameter("idNews", true);
            $res = GetNews($idNews);
            $responseCode = is_array($res) ? StatusCodes::OK : $res;
            if(is_array($res))
                $responseContent = $res;
            if(isLogged())
                SegnaComeLetta($idNews);
            break;
        case "GetNewsEditor":
            $idEditor = getParameter("idEditor", true);
            $from = getParameter("from");
            //if(isLogged(true))
            //{
                $res = GetNewsEditor($idEditor, $from);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            //}
            break;
        case "GetNewsFromAllEditors":
            //per sito web
            $idFrom = getParameter("idFrom");
            $res = GetNewsDaTuttiGliEditor($idFrom);
            if(is_array($res))
            {
                $responseCode = StatusCodes::OK;
                $responseContent = $res;
            }
            else
                $responseCode = $res;
            break;
        case "GetEditorsByLocation":
            $location = getParameter("localita", true);
            $res = GetEditorsByLocation($location);
            if(is_array($res))
            {
                $responseCode = StatusCodes::OK;
                $responseContent = $res;
            }
            else
                $responseCode = $res;
            break;
        case "GetComuniConEditors":
            $res = GetComuniConEditors();
            if(is_array($res))
            {
                $responseCode = StatusCodes::OK;
                $responseContent = $res;
            }
            else
                $responseCode = $res;
            break;
        case "GetAllMyNewsFrom":
            if(isLogged(true))
            {
                $from = getParameter("lastId");
                $res = GetAllMyNewsFrom($from);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "GetAllMyNewsTo":
            if(isLogged(true))
            {
                $to = getParameter("to", true);
                $res = GetAllMyNewsTo($to);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "GetEditorInfo":
            if(isLogged(true))
            {
                $idEditor = getParameter("idEditor", true);
                $res = GetInfoEditor($idEditor);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "CercaEditor":
            if(isLogged(true))
            {
                $nomeCercare = getParameter("query", true);
                $res = CercaEditor($nomeCercare);
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
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
                if(PartecipaEditor($idEditor, $idUtente, "admin")!=StatusCodes::OK)
                {
                    DeleteEditor($idEditor);
                    $result = StatusCodes::EDITOR_IMPOSSIBILE_ASSEGNARE_AMMINISTRATORE;
                }
                else 
                {
                    if($categoria == 'Comune')
                        AutoFollowComune($idEditor, $localita);
                    AutoFollowSuperReader($idEditor);
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
            $result = $st->execute() && $dbConn->affected_rows>0 ? StatusCodes::OK : StatusCodes::FAIL;
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
        $query = "SELECT e.id,e.nome,e.immagine FROM editor AS e JOIN editor_follow AS ef ON e.id=ef.id_editor WHERE ef.id_utente = ? AND e.approvato = 1";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idUtente);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($editorId,$editorNome,$editorImg);
                $result = array();
                while($st->fetch())
                {
                    $editor = array("id"=>$editorId,
                        "nome"=>$editorNome,
                        "immagine"=>$editorImg);
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
        $query = "DELETE FROM editor_follow WHERE id_utente = ? AND id_editor = ? AND cancellabile = 1";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idEditor);
            $st->execute();
            $result = $dbConn->affected_rows > 0 ? StatusCodes::OK : StatusCodes::EDITOR_NON_SEGUITO;
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
        $query = "SELECT ed.id, ed.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione, (SELECT COUNT(*) FROM news_editor_thankyou WHERE id_news=n.id) AS thankyou FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.id=?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idNews);
            if($st->execute())
            {
                $st->bind_result($editorId,$editorNome, $newsId, $newsTitolo, $newsCorpo, $newsData, $newsImmagine, $newsPosizione,$thanks);
                if($st->fetch())
                {
                    $result = array("editorId"=>$editorId,
                        "editorNome" => $editorNome,
                        "newsId" => $newsId,
                        "titolo" => $newsTitolo,
                        "corpo" => $newsCorpo,
                        "data" => $newsData,
                        "immagine" => $newsImmagine,
                        "posizione" => $newsPosizione,
                        "thankyou" => $thanks);
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
        $dbConn = dbConnect();
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
    function GetNewsEditor($idEditor, $from)
    {
        $query = empty($from) ? 
        "SELECT ed.id, ed.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.pubblicataDaEditor=? AND ed.approvato=1 ORDER BY n.data DESC LIMIT 10":
        "SELECT ed.id, ed.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.pubblicataDaEditor=? AND n.id < ? AND ed.approvato=1 ORDER BY n.data DESC LIMIT 10";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            empty($from) ? $st->bind_param("i", $idEditor) : $st->bind_param("ii", $idEditor,$from);
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
                        "corpo" => $newsCorpo,
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
    //TODO: DA VERIFICARE SE BISOGNA ESEGUIRLO QUANDO VIENE APPROVATO L'EDITOR
    function AutoFollowComune($idEditor, $loc)
    {
        $query = "INSERT INTO editor_follow (id_utente, id_editor,cancellabile) SELECT id, $idEditor AS id_editor, 0 AS cancellabile FROM utente WHERE comune_residenza = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st= $dbConn->prepare($query))
        {
            $st->bind_param("s", $loc);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function AutoFollowSuperReader($idEditor)
    {
        if(SUPER_READER_ENABLED)
        {
            $idSuperReader = SUPER_READER_ID;
            $query = "INSERT INTO editor_follow (id_utente, id_editor) VALUES (?,?)";
            $result = StatusCodes::FAIL;
            $dbConn = dbConnect();
            if($st= $dbConn->prepare($query))
            {
                $st->bind_param("ii", $idSuperReader,$idEditor);
                $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
                $st->close();
            }
            dbClose($dbConn);
            return $result;
        }
    }
    function GetEditorsByLocation($location)
    {
        $query = "SELECT id,nome,categoria,immagine FROM editor WHERE localita = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("s",$location);
            if($st->execute())
            {
                $st->bind_result($editorId,$editorNome,$editorCategoria,$immagine);
                $result = array();
                while($st->fetch())
                {
                    $editor = array("editorId"=>$editorId,
                        "editorNome" => $editorNome,
                        "editorCategoria" => $editorCategoria,
                        "immagine" => $immagine);
                    array_push($result, $editor);
                }
            }
        }
        dbClose($dbConn);
        return $result;
    }
    function GetComuniConEditors()
    {
        $query = "SELECT istat, comune FROM comune WHERE istat IN (SELECT DISTINCT localita FROM editor) ORDER BY comune";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            if($st->execute())
            {
                $st->bind_result($istat, $nomeComune);
                $result = array();
                while($st-fetch())
                {
                    $comune = array("id"=>$istat, "comune"=>$nomeComune);
                    array_push($result, $comune);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetNewsDaTuttiGliEditor($idStart = NULL)
    {
        $query = $idStart == NULL ?
                "SELECT e.id, e.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM news_editor AS n JOIN editor AS e ON n.pubblicataDaEditor=e.id WHERE e.approvato = 1 ORDER BY n.data DESC LIMIT 50" :
                "SELECT e.id, e.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione FROM news_editor AS n JOIN editor AS e ON n.pubblicataDaEditor=e.id WHERE e.approvato = 1 AND n.id < $idStart ORDER BY n.data DESC LIMIT 50";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
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
    function AddDescrizioneEditor($idEditor, $descrizione)
    {
        if(VerificaAutorizzatoAModificareEditor($idEditor))
        {
            $query = "UPDATE editor SET descrizione = ? WHERE id = ?";
            $result = StatusCodes::FAIL;
            $dbConn = dbConnect();
            if($st = $dbConn->prepare($query))
            {
                $st->bind_param("si", $descrizione, $idEditor);
                $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
                $st->close();
            }
            dbClose($dbConn);
            return $result;
        }
        else
        {
            return StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO;
        }
    }
    function AddImmagineEditor($idEditor, $immagine)
    {
        $image_path = SalvaImmagine($immagine, "editors");
        if(VerificaAutorizzatoAModificareEditor($idEditor))
        {
            $query = "UPDATE editor SET immagine = ? WHERE id = ?";
            $result = StatusCodes::FAIL;
            $dbConn = dbConnect();
            if($st = $dbConn->prepare($query))
            {
                $st->bind_param("si", $image_path, $idEditor);
                $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
                $st->close();
            }
            dbClose($dbConn);
            return $result;
        }
        else
        {
            return StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO;
        }
    }
    function GetAllMyNewsFrom($lastId = NULL)
    {
        $idUtente = getIdUtenteFromSession();
        $query = $lastId == NULL ?
                 "SELECT e.id,e.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione, (SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news=n.id) AS letta FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n ON ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor WHERE ef.id_utente = ? ORDER BY n.data DESC LIMIT 10" :
                 "SELECT e.id,e.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione, (SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news=n.id) AS letta FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n ON ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor WHERE ef.id_utente = ? AND n.id < ? ORDER BY n.data DESC LIMIT 10";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $lastId == NULL ? $st->bind_param("ii", $idUtente,$idUtente) : $st->bind_param("iii", $idUtente,$idUtente,$from);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($enteId,$enteNome, $newsId,$newsTitolo,$newsCorpo,$newsData,$newsImmagine,$newsPosizione,$newsLetta);
                $result = array();
                while($st->fetch())
                {
                    $news = array(
                        "editorId"=>$enteId,
                        "editorNome"=>$enteNome,
                        "newsId"=>$newsId,
                        "titolo"=>$newsTitolo,
                        "corpo"=>$newsCorpo,
                        "data"=>$newsData,
                        "immagine"=>$newsImmagine,
                        "posizione"=>$newsPosizione,
                        "letta"=>$newsLetta
                    );
                    array_push($result, $news);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetAllMyNewsTo($to)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id,e.nome,n.id,n.titolo,n.corpo,n.data,n.immagine,n.posizione, (SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news=n.id) AS letta FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n ON ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor WHERE ef.id_utente = ? AND n.id > ? ORDER BY n.data DESC";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iii", $idUtente,$idUtente,$to);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SQL_FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($enteId,$enteNome, $newsId,$newsTitolo,$newsCorpo,$newsData,$newsImmagine,$newsPosizione,$newsLetta);
                $result = array();
                while($st->fetch())
                {
                    $news = array(
                        "editorId"=>$enteId,
                        "editorNome"=>$enteNome,
                        "newsId"=>$newsId,
                        "titolo"=>$newsTitolo,
                        "corpo"=>$newsCorpo,
                        "data"=>$newsData,
                        "immagine"=>$newsImmagine,
                        "posizione"=>$newsPosizione,
                        "letta"=>$newsLetta
                    );
                    array_push($result, $news);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetInfoEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT id,nome,localita,geo_coordinate,descrizione,immagine, (SELECT COUNT(*) FROM editor_follow WHERE id_editor=?) as followers,(SELECT COUNT(*) FROM editor_follow WHERE id_editor=? AND id_utente=?) as following FROM editor WHERE approvato = 1 AND id = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iiii", $idEditor,$idEditor,$idUtente,$idEditor);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SQL_FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($id,$nome,$localita,$geo,$descrizione,$immagine,$followers,$following);
                if($st->fetch())
                {
                    $result = array(
                        "id"=>$id,
                        "nome"=>$nome,
                        "localita"=>$localita,
                        "coordinate"=>$geo,
                        "descrizione"=>$descrizione,
                        "immagine"=>$immagine,
                        "followers"=>$followers,
                        "following"=>$following
                    );
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function CercaEditor($nomeCercare)
    {
        try
        {
            $query = "SELECT id,nome FROM editor WHERE approvato = 1 AND nome LIKE '%".$nomeCercare."%' ORDER BY CASE WHEN nome LIKE '".$nomeCercare."%' THEN 1 WHEN nome LIKE '%".$nomeCercare."' THEN 3 ELSE 2 END";
            $result = StatusCodes::FAIL;
            $dbConn = dbConnect();
            $res = $dbConn->query($query);
            $result = array();
            while($row = $res->fetch_array(MYSQLI_ASSOC))
            {
                $editor = array("id"=>$row["id"],"nome"=>$row["nome"]);
                array_push($result, $editor);
            }
            $res->close();
            dbClose($dbConn);
            return $result;
        }
        catch(Exception $e)
        {
            return StatusCodes::SQL_FAIL;
        }
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
    function VerificaAutorizzatoAModificareEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT eg.ruolo FROM editor_gestione AS eg JOIN editor AS e ON e.id=eg.id_editor WHERE eg.id_utente = ? AND eg.id_editor = ? AND e.approvato = 1 AND eg.ruolo ='admin'";
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
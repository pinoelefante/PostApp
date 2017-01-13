<?php
    session_start();
    
    require_once("enums.php");
    require_once("functions.php");
    require_once("database.php");
    require_once("news_common.php");
    require_once("push_notifications.php");
    require_once("logger.php");
    
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
                    $responseCode = $idNews = PostEditor($idEditor, getIdUtenteFromSession(), $titolo, $corpo, $immagine, $posizione);
                    if($idNews > 0)
                    {
                        $responseCode = StatusCodes::OK;
                        InviaNotificaPush($idEditor,$titolo,$corpo,$idNews);
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
                $responseCode = ThankYou($idNews,"editor");
            }
            break;
        case "LeggiNews":
            $idNews = getParameter("idNews", true);
            $res = GetNews($idNews);
            $responseCode = is_array($res) ? StatusCodes::OK : $res;
            if(is_array($res))
                $responseContent = $res;
            if(isLogged())
                SegnaComeLetta($idNews, "editor");
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
        case "GetEditorNewsFromTo":
            if(isLogged(true))
            {
                $editor = getParameter("idEditor", true);
                $from = getParameter("from", true);
                $to = getParameter("to", true);
                $res = GetEditorNewsFromTo($editor,$from,$to);
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
        $idEditor = dbUpdate($query,"ssssss",array($nome, $categoria, $email, $telefono, $indirizzo, $localita), DatabaseReturns::RETURN_INSERT_ID);
        
        $result = $idEditor > 0 ? StatusCodes::OK : StatusCodes::EDITOR_ERRORE_CREAZIONE;
        if($result == StatusCodes::OK)
        {
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
                FollowEditor($idEditor);
            }
        }
		return $result;
    }
    //autorizza l'utente a postare a nome dell'editor
    function PartecipaEditor($idEditor, $idUtente, $ruolo = "editor")
    {
        $query = "INSERT INTO editor_gestione (id_utente,id_editor,ruolo) VALUES (?,?,?)";
        return dbUpdate($query,"iis",array($idUtente,$idEditor,$ruolo)) ? StatusCodes::OK : StatusCodes::FAIL;
    }
    function DeleteEditor($idEditor)
    {
        $query = "DELETE FROM editor WHERE id = ?";
        return dbUpdate($query,"i",array($idEditor), DatabaseReturns::RETURN_AFFECTED_ROWS) ? StatusCodes::OK : StatusCodes::FAIL;
    }
    //elenco editor per cui l'utente è autorizzato a postare
    function ListEditor()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id as id,e.nome as nome,eg.ruolo as ruolo FROM editor AS e JOIN editor_gestione AS eg ON e.id=eg.id_editor WHERE eg.id_utente = ? AND e.approvato = 1";
        $result = dbSelect($query, "i", array($idUtente));
        return $result == null ? StatusCodes::FAIL : $result;
    }
    function ListEditorDaApprovare()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id as id,e.nome as nome,eg.ruolo as ruolo FROM editor AS e JOIN editor_gestione AS eg ON e.id=eg.id_editor WHERE eg.id_utente = ? AND e.approvato = 0";
        $result = dbSelect($query, "i", array($idUtente));
        return $result == null ? StatusCodes::FAIL : $result;
    }
    function ListReaderEditors()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id as id,e.nome as nome,e.immagine as immagine FROM editor AS e JOIN editor_follow AS ef ON e.id=ef.id_editor WHERE ef.id_utente = ? AND e.approvato = 1";
        $result = dbSelect($query, "i", array($idUtente));
        return $result == null ? StatusCodes::FAIL : $result;
    }
    function PostEditor($idEditor, $idUtente, $titolo, $corpo, $immagine, $posizione)
    {
        $query = "INSERT INTO news_editor (pubblicataDaEditor, pubblicataDaUtente, titolo, corpo, immagine, posizione, notificabile) VALUES (?,?,?,?,?,?,1)";
        $image_path = SalvaImmagine($immagine);
        $idNews = dbUpdate($query,"iissss",array($idEditor,$idUtente, $titolo, $corpo, $image_path, $posizione), $returnType = DatabaseReturns::RETURN_INSERT_ID);
        $result = $idNews > 0 ? $idNews: StatusCodes::FAIL;
        return $result;
    }
    function FollowEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO editor_follow (id_utente,id_editor) VALUES (?,?)";
        return dbUpdate($query,"ii",array($idUtente,$idEditor)) ? StatusCodes::OK : StatusCodes::SEGUI_GIA;
    }
    function UnfollowEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "DELETE FROM editor_follow WHERE id_utente = ? AND id_editor = ? AND cancellabile = 1";
        return dbUpdate($query, "ii",array($idUtente,$idEditor), DatabaseReturns::RETURN_AFFECTED_ROWS) > 0 ? StatusCodes::OK : StatusCodes::EDITOR_NON_SEGUITO;
    }
    function GetNotifications($from)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id as editorId,e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n WHERE ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor AND ef.id_utente = ? AND n.data > ? AND n.notificabile = 1";
        return dbSelect($query,"ii",array($idUtente, $from));
    }
    function GetNews($idNews)
    {
        $query = "SELECT ed.id as editorId,ed.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione, (SELECT COUNT(*) FROM news_editor_thankyou WHERE id_news=n.id) AS thankyou FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.id=?";
        $result = dbSelect($query,"i",array($idNews), true);
        return $result == null ? StatusCodes::EDITOR_NEWS_NON_TROVATA : $result;
    }
    function GetNewsEditor($idEditor, $from)
    {
        if(empty($from))
        {
            $query = "SELECT ed.id as editorId, ed.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.pubblicataDaEditor=? AND ed.approvato=1 ORDER BY n.data DESC LIMIT 10";
            return dbSelect($query, "i", array($idEditor));
        }
        else 
        {
            $query = "SELECT ed.id as editorId, ed.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione FROM editor AS ed JOIN news_editor AS n ON ed.id=n.pubblicataDaEditor WHERE n.pubblicataDaEditor=? AND n.id < ? AND ed.approvato=1 ORDER BY n.data DESC LIMIT 10";
            return dbSelect($query, "ii", array($idEditor,$from));
        }
    }
    //TODO: DA VERIFICARE SE BISOGNA ESEGUIRLO QUANDO VIENE APPROVATO L'EDITOR
    function AutoFollowComune($idEditor, $loc)
    {
        $query = "INSERT INTO editor_follow (id_utente, id_editor,cancellabile) SELECT id, $idEditor AS id_editor, 0 AS cancellabile FROM utente WHERE comune_residenza = ?";
        return dbUpdate($query, "s", array($loc)) ? StatusCodes::OK : StatusCodes::FAIL;
    }
    function AutoFollowSuperReader($idEditor)
    {
        if(SUPER_READER_ENABLED)
        {
            $idSuperReader = SUPER_READER_ID;
            $query = "INSERT INTO editor_follow (id_utente, id_editor) VALUES (?,?)";
            return dbUpdate($query, "ii", array($idSuperReader,$idEditor)) ? StatusCodes::OK : StatusCodes::FAIL;
        }
    }
    function GetEditorsByLocation($location)
    {
        $query = "SELECT id as editorId,nome as editorNome,categoria as editorCategoria,immagine as immagine FROM editor WHERE localita = ? AND approvato=1";
        return dbSelect($query,"s", array($location));
    }
    function GetComuniConEditors()
    {
        $query = "SELECT istat as id, comune as comune FROM comune WHERE istat IN (SELECT DISTINCT localita FROM editor) ORDER BY comune";
        return dbSelect($query);
    }
    function GetNewsDaTuttiGliEditor($idStart = NULL)
    {
        if($idStart == NULL)
        {
            $query = "SELECT e.id as editorId, e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione FROM news_editor AS n JOIN editor AS e ON n.pubblicataDaEditor=e.id WHERE e.approvato = 1 ORDER BY n.data DESC LIMIT 50";
            return dbSelect($query);
        }
        else 
        {
            $query = "SELECT e.id as editorId, e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione FROM news_editor AS n JOIN editor AS e ON n.pubblicataDaEditor=e.id WHERE e.approvato = 1 AND n.id < ? ORDER BY n.data DESC LIMIT 50";
            return dbSelect($query,"i",array($idStart));
        }
    }
    function AddDescrizioneEditor($idEditor, $descrizione)
    {
        if(VerificaAutorizzatoAModificareEditor($idEditor))
        {
            $query = "UPDATE editor SET descrizione = ? WHERE id = ?";
            return dbUpdate($query, "si", array($descrizione, $idEditor)) ? StatusCodes::OK : StatusCodes::FAIL;
        }
        return StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO;
    }
    function AddImmagineEditor($idEditor, $immagine)
    {
        if(VerificaAutorizzatoAModificareEditor($idEditor))
        {
            $image_path = SalvaImmagine($immagine, "editors");

            $query = "UPDATE editor SET immagine = ? WHERE id = ?";
            return dbUpdate($query, "si", array($image_path, $idEditor)) ? StatusCodes::OK : StatusCodes::FAIL;
        }
        return StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO;
    }
    function GetAllMyNewsFrom($lastId = NULL)
    {
        $idUtente = getIdUtenteFromSession();
        if($lastId == NULL)
        {
            $query = "SELECT e.id as editorId,e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione, (SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news=n.id) AS letta FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n ON ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor WHERE ef.id_utente = ? ORDER BY n.data DESC LIMIT 10";
            return dbSelect($query, "ii", array($idUtente,$idUtente));
        }
        else 
        {
            $query = "SELECT e.id as editorId,e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione, (SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news=n.id) AS letta FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n ON ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor WHERE ef.id_utente = ? AND n.id < ? ORDER BY n.data DESC LIMIT 10";
            return dbSelect($query, "iii", array($idUtente,$idUtente,$lastId));
        }
    }
    function GetAllMyNewsTo($to)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id as editorId,e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione, (SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news=n.id) AS letta FROM editor AS e JOIN editor_follow AS ef JOIN news_editor AS n ON ef.id_editor=e.id AND ef.id_editor=n.pubblicataDaEditor WHERE ef.id_utente = ? AND n.id > ? ORDER BY n.data DESC";
        return dbSelect($query, "iii", array($idUtente,$idUtente,$to));
    }
    function GetEditorNewsFromTo($idEditor,$from,$to) //'from' è maggiore di 'to'
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT e.id as editorId,e.nome as editorNome,n.id as newsId,n.titolo as titolo,n.corpo as corpo,n.data as data,n.immagine as immagine,n.posizione as posizione,(SELECT COUNT(*) FROM news_editor_letta WHERE id_utente = ? AND id_news = n.id) AS letta FROM news_editor AS n JOIN editor AS e ON n.pubblicataDaEditor=e.id WHERE e.id=? AND n.id < ? AND n.id > ? ORDER BY data DESC";
        return dbSelect($query, "iiii", array($idUtente,$idEditor,$from,$to));
    }
    function GetInfoEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT id,nome,localita,geo_coordinate as coordinate,descrizione,immagine, (SELECT COUNT(*) FROM editor_follow WHERE id_editor=?) as followers,(SELECT COUNT(*) FROM editor_follow WHERE id_editor=? AND id_utente=?) as following FROM editor WHERE approvato = 1 AND id = ?";
        return dbSelect($query, "iiii", array($idEditor,$idEditor,$idUtente,$idEditor));
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
        $result = dbSelect($query, "ii", array($idUtente,$idEditor));
        return $result == null ? StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO : StatusCodes::OK;
    }
    function VerificaAutorizzatoAModificareEditor($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT eg.ruolo FROM editor_gestione AS eg JOIN editor AS e ON e.id=eg.id_editor WHERE eg.id_utente = ? AND eg.id_editor = ? AND e.approvato = 1 AND eg.ruolo ='admin'";
        $result = dbSelect($query, "ii", array($idUtente,$idEditor));
        return $result == null ? StatusCodes::EDITOR_UTENTE_NON_AUTORIZZATO : StatusCodes::OK;
    }
    function GetUtentiSeguonoEditorNotificabili($idEditor)
    {
        $query = "SELECT dev.id_utente as user,dev.token as token,dev.deviceOS as deviceOS FROM editor_follow AS foll JOIN push_devices AS dev ON foll.id_utente=dev.id_utente WHERE foll.id_editor=? AND foll.notificabile=1";
        return dbSelect($query, "i", array($idEditor));
    }
    function GetEditorNomeById($idEditor)
    {
        $query = "SELECT nome FROM editor WHERE id = ?";
        $result = dbSelect($query, "i", array($idEditor), true);
        return $result == null ? "" : $result["nome"];
    }
    function InviaNotificaPush($idEditor,$titolo,$corpo,$id_news)
    {
        LogMessage("Mi preparo ad inviare notifica push per news id: $id_news");
        $devices = GetUtentiSeguonoEditorNotificabili($idEditor);
        LogMessage("Device count: "+count($devices));
        sendPushNotification($titolo,$corpo,GetEditorNomeById($idEditor),$id_news,$devices);
    }
?>
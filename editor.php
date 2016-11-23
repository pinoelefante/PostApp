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

            break;
        case "LoadEditorImage":

            break;
        case "Post":
            if(isLogged(true))
            {
                
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
    function VerificaAutorizzazioneAPostare($idEditor)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT ruolo FROM editor_gestione WHERE id_utente = ? AND id_editor = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idEditor);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->bind_result($ruolo);
            if($st->fetch())
                $result = StatusCodes::OK;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
?>
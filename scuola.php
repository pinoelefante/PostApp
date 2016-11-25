<?php
    session_start();
    
    require_once("enums.php");
    require_once("functions.php");

    define('RUOLO_PRESIDE', 'Preside');
    define('RUOLO_SEGRETERIA', 'Segreteria');
    
    $action = getParameter("action", true);
    $responseCode = StatusCodes::FAIL;
    $responseContent = "";
    switch($action)
    {
        case "RegistraScuola":
            if(isLogged(true))
            {
                $nomeScuola = getParameter("nomeScuola", true);
                $localitaScuola = getParameter("localitaScuola", true);
                $emailScuola = getParameter("emailScuola", true);
                $telefonoScuola = getParameter("telefonoScuola", true);
                $indirizzoScuola = getParameter("indirizzoScuola");
                $nomePreside = getParameter("nomePreside", true);
                $cognomePreside = getParameter("cognomePreside", true);
                $usernamePreside = getParameter("usernamePreside", true);
                $passwordPreside = getParameter("passwordPreside", true);
                $responseCode = RegistraScuola($nomeScuola, $localitaScuola, $emailScuola,$telefonoScuola, $indirizzoScuola, $nomePreside, $cognomePreside, $usernamePreside, $passwordPreside);
                if($responseCode == StatusCodes::OK)
                {
                    $dataIscr = date("d/m/Y H:i");
                    sendEmailAdmin("Iscrizione nuova scuola: $nomeScuole", 
                        "L'editor <b>$nome</b> si è iscritto a PostApp il <b>$dataIscr</b> ed è in attessa di approvazione<br><br><b>Contatti</b><br> email: <b>$emailScuola</b><br>telefono: <b>$telefonoScuola</b><br>preside: <b>$cognome $nome</b><br><br><a href=".PANNELLO_ADMIN.">Vai al pannello di amministrazione</a>");
                }
            }
            break;
        case "GetMieScuoleWriter":
            if(isLogged(true))
            {
                $res = GetMieScuoleWriter();
                if(is_array($res))
                {
                    $responseCode = StatusCodes::OK;
                    $responseContent = $res;
                }
                else
                    $responseCode = $res;
            }
            break;
        case "GetMieScuoleReader":
            //TODO
            break;
        case "VerificaAccesso":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                $responseCode = VerificaAutorizzazione($idScuola) ? StatusCodes::OK : StatusCodes::LOGIN_NON_LOGGATO;
            }
            break;
        case "AccessoScuola":
            if(isLogged(true))
            {
                $username = getParameter("username", true);
                $password = getParameter("password", true);
                $responseCode = AccessoScuola($username, $password);
            }
            break;
        case "AggiungiPlesso":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $nomePlesso = getParameter("nomePlesso", true);
                    $responseCode = AggiungiPlesso($idScuola, $nomePlesso);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "RimuoviPlesso":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $idPlesso = getParameter("idPlesso", true);
                    $responseCode = RimuoviPlesso($idPlesso);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "AggiungiSezione":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $idPlesso = getParameter("idPlesso", true);
                    $idGrado = getParameter("idGrado", true);
                    $classeInizio = getParameter("classeInizio", true);
                    $classeFine = getParameter("classeFine", true);
                    $letteraSezione = getParameter("sezione", true);
                    $responseCode = AggiungiSezione($idScuola, $idPlesso, $idGrado, $letteraSezione, $classeInizio, $classeFine);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "RimuoviSezione":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $idPlesso = getParameter("idPlesso", true);
                    $idGrado = getParameter("idGrado", true);
                    $letteraSezione = getParameter("sezione", true);
                    $responseCode = RimuoviSezione($idScuola, $idPlesso, $idGrado, $letteraSezione);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "AggiungiGrado":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $grado = getParameter("grado", true);
                    $responseCode = AggiungiGrado($idScuola, $grado);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "RimuoviGrado":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $idGrado = getParameter("idGrado", true);
                    $responseCode = RimuoviGrado($idGrado);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "AggiungiClasse":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $idPlesso = getParameter("idPlesso", true);
                    $idGrado = getParameter("idGrado", true);
                    $classe = getParameter("classe", true);
                    $letteraSezione = getParameter("sezione", true);
                    $responseCode = AggiungiClasse($idScuola, $idPlesso, $idGrado,$letteraSezione,$classe);
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "RimuoviClasse":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(VerificaRuolo($idScuola, RUOLO_PRESIDE))
                {
                    $idPlesso = getParameter("idPlesso", true);
                    $idGrado = getParameter("idGrado", true);
                    $classe = getParameter("classe", true);
                    $letteraSezione = getParameter("sezione", true);
                    $responseCode = RimuoviClasse($idScuola, $idPlesso, $idGrado,$letteraSezione,$classe);
                }
            }
            break;
        case "SbloccaCodice":
            if(isLogged(true))
            {
                
            }
            break;
        case "PostaNewsScuola":
            if(isLogged(true))
            {
                
            }
            break;
        case "PostaNewsClasse":
            if(isLogged(true))
            {
                
            }
            break;
        case "GetNewsScuola": //preside
            if(isLogged(true))
            {
                
            }
            break;
        case "GetNewsClassi": //preside
            if(isLogged(true))
            {
                
            }
            break;
        case "GetNewsMyScuola":
            if(isLogged(true))
            {
                
            }
            break;
        case "GetNewsMyClassi":
            if(isLogged(true))
            {
                
            }
            break;
        case "GetNotificheScuola":
            if(isLogged(true))
            {
                
            }
            break;
        case "GetNotificheClassi":
            if(isLogged(true))
            {
                
            }
            break;
        case "ThankYouNewsScuola":
            if(isLogged(true))
            {
                
            }
            break;
        case "ThankYouNewsClasse":
            if(isLogged(true))
            {
                
            }
            break;
        case "LeggiNewsScuola":
            if(isLogged(true))
            {
                
            }
            break;
        case "LeggiNewsClasse":
            if(isLogged(true))
            {
                
            }
            break;
        default:
            $responseCode = StatusCodes::METODO_ASSENTE;
            break;
    }
    sendResponse($responseCode, $responseContent);

    function RegistraScuola($nomeScuola, $localitaScuola, $emailScuola,$telefonoScuola, $indirizzoScuola, $nomePreside, $cognomePreside, $usernamePreside, $passwordPreside)
    {
        $query = "INSERT INTO scuola (nome,localita,email,telefono,indirizzo) VALUES (?,?,?,?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("sssss", $nomeScuola, $localitaScuola,$emailScuola, $telefonoScuola, $indirizzoScuola);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            if($result == StatusCodes::OK)
            {
                $idScuola = $dbConn->insert_id;
                $idUtente = getIdUtenteFromSession();
                $result = RegistraPresideScuola($idUtente,$idScuola,$nomePreside, $cognomePreside, $usernamePreside, $passwordPreside); 
                if($result!=StatusCodes::OK)
                    DeleteScuola($idScuola);
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function RegistraPresideScuola($idUtente,$idScuola,$nome, $cognome, $username, $password)
    {
        $query = "INSERT INTO scuola_gestione (id_utente,id_scuola,username,password,nome,cognome,ruolo) VALUES (?,?,?,?,?,?,'Preside')";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $pass_hash = hashPassword($password);
            $st->bind_param("iissss", $idUtente,$idScuola,$username,$pass_hash,$nome,$cognome);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SCUOLA_IMPOSSIBILE_ASSEGNARE_PRESIDE;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function DeleteScuola($idScuola)
    {
        $query = "DELETE FROM scuola WHERE id = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idScuola);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose();
        return $result;
    }
    function GetMieScuoleWriter()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT s.id,s.nome,sg.ruolo FROM scuola AS s JOIN scuola_gestione AS sg ON s.id=sg.id_scuola WHERE sg.id_utente = ? ORDER BY s.nome ASC";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idUtente);
            if($st->execute())
            {
                $st->bind_result($scuolaId, $scuolaNome, $userRuolo);
                $result = array();
                while($st->fetch())
                {
                    $scuola = array("scuolaId"=>$scuolaId, "scuolaNome"=>$scuolaNome, "userRuolo"=>$userRuolo);
                    array_push($result, $scuola);
                }
            }
        }
        dbClose($dbConn);
        return $result;
    }
    function AccessoScuola($username, $password)
    {
        $query = "SELECT id_scuola,password,ruolo,nome,cognome FROM scuola_gestione WHERE username = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("s",$username);
            if($st->execute())
            {
                $st->bind_result($idScuola,$pass_hash,$ruolo,$nome,$cognome);
                if($st->fetch())
                {
                    if(password_verify($password, $pass_hash))
                    {
                        $result = StatusCodes::OK;
                        $_SESSION["auth_scuola_$idScuola"]=$ruolo;
                    }
                    else
                        $result = StatusCodes::SCUOLA_PASSWORD_ERRATA;
                }
                else
                    $result = StatusCodes::SCUOLA_USERNAME_NON_VALIDO;
            } 
        }
        dbClose($dbConn);
        return $result;
    }
    function AggiungiPlesso($idScuola, $nomePlesso)
    {
        $query = "INSERT INTO scuola_plesso (id_scuola,nome_plesso) VALUES (?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("is", $idScuola,$nomePlesso);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SCUOLA_PLESSO_DUPLICATO;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function RimuoviPlesso($idPlesso)
    {
        $query = "DELETE FROM scuola_plesso WHERE id = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i",$idPlesso);
            $result = $st->execute() && $dbConn->affected_rows > 0 ? StatusCodes::OK : StatusCodes::SCUOLA_PLESSO_NON_PRESENTE;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function AggiungiGrado($idScuola, $grado)
    {
        $query = "INSERT INTO scuola_grado (id_scuola, grado) VALUES (?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("is", $idScuola,$grado);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SCUOLA_GRADO_DUPLICATO;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function RimuoviGrado($idGrado)
    {
        $query = "DELETE FROM scuola_grado WHERE id = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i",$idGrado);
            $result = $st->execute() && $dbConn->affected_rows > 0 ? StatusCodes::OK : StatusCodes::SCUOLA_PLESSO_NON_PRESENTE;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function AggiungiSezione($idScuola, $idPlesso, $idGrado, $letteraSezione, $classeInizio, $classeFine)
    {
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        for($classe = $classeInizio;$classe<=$classeFine;$classe++)
        {
            $result = AggiungiClasse($idScuola,$idPlesso,$idGrado,$letteraSezione,$classe,$dbConn);
            if($result!=StatusCodes::OK)
            {
                $result = StatusCodes::SCUOLA_ERRORE_INSERIMENTO_SEZIONE;
                DeleteClassi($idScuola, $idPlesso, $idGrado, $letteraSezione, $classeInizio, $classe);
                break;
            }
        }
        dbClose($dbConn);
        return $result;
    }
    function AggiungiClasse($idScuola, $idPlesso, $idGrado,$letteraSezione,$classe, $dbConn = NULL)
    {
        $query = "INSERT INTO scuola_classe (id_scuola,id_plesso,id_grado,classe,sezione) VALUES (?,?,?,?,?)";
        $toClose = false;
        if($dbConn==NULL)
        {
            $dbConn = dbConnect();
            $toClose = true;
        }
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iiiis", $idScuola,$idPlesso,$idGrado,$classe,$letteraSezione);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        if($toClose)
            dbClose($dbConn);
        return $result;
    }
    function DeleteClassi($idScuola, $idPlesso, $idGrado,$letteraSezione, $classeInizio, $classeFine)
    {
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        for($classe = $classeInizio;$classe<=$classeFine;$classe++)
        {
            $result = RimuoviClasse($idScuola, $idPlesso, $idGrado,$letteraSezione,$classe,$dbConn);
        }
        dbClose($dbConn);
        return $result;
    }
    function RimuoviClasse($idScuola, $idPlesso, $idGrado,$letteraSezione, $classe, $dbConn)
    {
        $query = "DELETE FROM scuola_classe WHERE id_scuola = ? AND id_plesso = ? AND id_grado = ? AND classe = ? AND sezione = ?";
        $toClose = false;
        if($dbConn==NULL)
        {
            $dbConn = dbConnect();
            $toClose = true;
        }
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iiiis", $idScuola,$idPlesso,$idGrado,$classe,$letteraSezione);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        if($toClose)
            dbClose($dbConn);
        return $result;
    }
    function RimuoviSezione($idScuola, $idPlesso, $idGrado, $letteraSezione)
    {
        $query = "DELETE FROM scuola_classe WHERE id_scuola = ? AND id_plesso = ? AND id_grado = ? AND sezione = ?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iiis", $idScuola,$idPlesso,$idGrado,$letteraSezione);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetNewsScuola($idScuola)
    {
        
    }
    function VerificaAutorizzazione($idScuola)
    {
        if(array_key_exists("auth_scuola_$idScuola",$_SESSION))
            return true;

        $idUtente = getIdUtenteFromSession();
        $query = "SELECT ruolo FROM scuola_gestione WHERE id_utente = ? AND id_scuola = ?";
        $result = false;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii",$idUtente, $idScuola);
            if($st->execute())
            {
                $st->bind_result($role);
                if($st->fetch())
                    $result = true;
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function VerificaRuolo($idScuola, $ruolo)
    {
        return true;
    }
?>
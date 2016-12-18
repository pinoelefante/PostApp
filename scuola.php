<?php
    session_start();
    
    require_once("enums.php");
    require_once("functions.php");
    require_once("news_common.php");

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
        case "GetMieScuoleWriterDaApprovare":
            if(isLogged(true))
            {
                $res = GetMieScuoleWriterDaApprovare();
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
            if(isLogged(true))
            {
                $responseCode = $responseContent = GetMieScuoleReader();
                if(is_array($res))
                    $responseCode = StatusCodes::OK;
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
                $codice = getParameter("codice", true);
                if(($responseCode = VerificaCodice($codice))==StatusCodes::OK)
                {
                    $nome = getParameter("nome", true);
                    $cognome = getParameter("cognome", true);
                    $nascita = getParameter("nascita", true);
                    $responseCode = SbloccaCodice($codice, $nome,$cognome,$nascita);
                }
            }
            break;
        case "PostaNewsScuola":
            if(isLogged(true))
            {
                $idScuola = getParameter("idScuola", true);
                if(UtentePuoPostarePerScuola($idScuola))
                {
                    $titolo = getParameter("titolo", true);
                    $corpoNews = getParameter("corpo", true);
                    $immagine = getParameter("image");
                    $destinatari = getParameter("destinatari", true);
                    $idNews = $responseCode = PostaNewsScuola($idScuola,$titolo,$corpoNews,$immagine,$destinatari);
                    if($idNews > 0)
                    {
                        $responseCode = StatusCodes::OK;
                        InviaNotificaPushScuola($idScuola,$idNews,$titolo,$corpoNews, $destinatari);
                    }
                }
                else
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "PostaNewsClasse":
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "GetNewsScuola": //preside
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "GetNewsClassi": //preside
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "GetNewsMyScuole":
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "GetNewsMyClassi":
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "GetNotificheScuola":
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "GetNotificheClassi":
            if(isLogged(true))
            {
                //TODO
            }
            break;
        case "ThankYouNewsScuola":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews", true);
                $responseCode = VerificaAutorizzazioneLetturaNewsScuola($idNews) == StatusCodes::OK ? ThankYou($idNews,"scuola") : StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "ThankYouNewsClasse":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews", true);
                $responseCode = VerificaAutorizzazioneLetturaNewsClasse($idNews) == StatusCodes::OK ? ThankYou($idNews,"classe") : StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "LeggiNewsScuola":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews",true);
                if(VerificaAutorizzazioneLetturaNewsScuola($idNews))
                {
                    $responseCode = $res = LeggiNewsScuola($idNews);
                    if(is_array($res))
                    {
                        $responseCode = StatusCodes::OK;
                        $responseContent = $res;
                        SegnaComeLetta($idNews, "scuola");
                    }
                }
                else
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "NotificaLetturaScuola":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews", true);
                $responseCode = VerificaAutorizzazioneLetturaNewsScuola($idNews) == StatusCodes::OK ? NotificaLettura($idNews, "scuola") : StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "LeggiNewsClasse":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews",true);
                if(VerificaAutorizzazioneLetturaNewsClasse($idNews))
                {
                    $responseCode = $res = LeggiNewsClasse($idNews);
                    if(is_array($res))
                    {
                        $responseCode = StatusCodes::OK;
                        $responseContent = $res;
                        SegnaComeLetta($idNews, "classe");
                    }
                }
                else 
                    $responseCode = StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            break;
        case "NotificaLetturaClasse":
            if(isLogged(true))
            {
                $idNews = getParameter("idNews", true);
                $responseCode = VerificaAutorizzazioneLetturaNewsClasse($idNews) == StatusCodes::OK ? NotificaLettura($idNews, "classe") : StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
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
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SQL_FAIL;
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
        $query = "SELECT s.id,s.nome,sg.ruolo FROM scuola AS s JOIN scuola_gestione AS sg ON s.id=sg.id_scuola WHERE sg.id_utente = ? AND s.approvata > 0 ORDER BY s.nome ASC";
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
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetMieScuoleWriterDaApprovare()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT s.id,s.nome,sg.ruolo FROM scuola AS s JOIN scuola_gestione AS sg ON s.id=sg.id_scuola WHERE sg.id_utente = ? AND s.approvata = 0 ORDER BY s.nome ASC";
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
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function AccessoScuola($username, $password)
    {
        $query = "SELECT id_utente,id_scuola,password,ruolo,nome,cognome FROM scuola_gestione WHERE username = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("s",$username);
            if($st->execute())
            {
                $st->bind_result($idUtente,$idScuola,$pass_hash,$ruolo,$nome,$cognome);
                if($st->fetch())
                {
                    if(password_verify($password, $pass_hash))
                    {
                        $result = StatusCodes::OK;
                        $_SESSION["auth_scuola_$idScuola"]=$ruolo;
                        $_SESSION["idUtente"]=$idUtente;
                    }
                    else
                        $result = StatusCodes::SCUOLA_PASSWORD_ERRATA;
                }
                else
                    $result = StatusCodes::SCUOLA_USERNAME_NON_VALIDO;
            }
            $st->close();
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
    function LeggiNewsScuola($idNews)
    {
        $query = "SELECT s.id,s.nome,n.id,n.titolo,n.corpo,n.data,n.immagine, (SELECT COUNT(*) FROM news_scuola_thankyou WHERE id_news=n.id) AS thankyou FROM scuola AS s JOIN news_scuola AS n ON s.id=n.pubblicataDaScuola WHERE n.id=?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idNews);
            if($st->execute())
            {
                $st->bind_result($scuolaId,$scuolaNome,$newsId,$newsTitolo,$newsCorpo,$newsData,$newsImmagine, $newsThankyou);
                if($st->fetch())
                {
                    $result = array("scuolaId"=>$scuolaId,
                                    "scuolaNome" => $scuolaNome,
                                    "newsId" => $newsId,
                                    "titolo" => $newsTitolo,
                                    "corpo" => $newsCorpo,
                                    "data" => $newsData,
                                    "immagine" => $newsImmagine,
                                    "thankyou" => $newsThankyou);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function LeggiNewsClasse($idNews)
    {
        $query = "SELECT news.id As newsId,news.titolo,news.corpo,news.immagine,news.`data`,cl.id_classe as classeId,cl.classe,cl.sezione,s.nome,s.id as scuolaId,pl.id As plessoId,pl.nome_plesso,gr.grado,(SELECT COUNT(*) FROM news_scuola_classe_thankyou WHERE id_news=news.id) AS thankyou FROM news_scuola_classe_destinatario AS dest JOIN news_scuola_classe AS news JOIN scuola_classe AS cl JOIN scuola AS s JOIN scuola_plesso AS pl JOIN scuola_grado AS gr ON cl.id_grado=gr.id AND cl.id_plesso=pl.id AND cl.id_classe=dest.id_classe AND dest.id_news=news.id AND cl.id_scuola=s.id WHERE news.id = ?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i", $idNews);
            if($st->execute())
            {
                $st->bind_result($newsId,$newsTitolo,$newsCorpo,$newsImmagine,$newsData,$classeId,$classeNum,$classeSezione,$scuolaNome,$scuolaId,$plessoId,$plessoNome,$grado,$thankyou);
                if($st->fetch())
                {
                    $result = array("classeId"=>$classeId,
                                    "classeNome" => "Classe $classeNum $classeSezione - Scuola $grado $scuolaNome - Plesso $plessoNome",
                                    "newsId" => $newsId,
                                    "titolo" => $newsTitolo,
                                    "corpo" => $newsCorpo,
                                    "data" => $newsData,
                                    "immagine" => $newsImmagine,
                                    "thankyou" => $thankyou);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetMieScuoleReader()
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT s.id,s.nome FROM scuola_follow AS sf JOIN scuola AS s ON sf.id_scuola=s.id WHERE sf.id_utente=? AND s.approvata=1";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i",$idUtente);
            if($st->execute())
            {
                $result = array();
                $st->bind_result($idScuola,$nomeScuola);
                while($st->fetch())
                {
                    $scuola = array("id"=>$idScuola,"nome"=>$nomeScuola);
                    array_push($result, $scuola);
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function PostaNewsScuola($idScuola,$titolo,$corpoNews,$immagine,$destinatari)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO news_scuola (titolo,corpo,immagine,pubblicataDaScuola,pubblicataDaUtente) VALUES (?,?,?,?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $imagePath = SalvaImmagine($immagine);
            $st->bind_param("sssii",$titolo,$corpoNews,$imagePath,$idScuola,$idUtente);
            if($st->execute())
            {
                $newsId = $result = $dbConn->insert_id;
                InserisciDestinatariNewsScuola($newsId,$idScuola, $destinatati);
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function InserisciDestinatariNewsScuola($idNews,$idScuola, $destinatati)
    {
        $query = "INSERT INTO news_scuola_destinatario (id_news,id_scuola,ruolo) VALUES (?,?,?)";
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            foreach($destinatari as $ruolo)
            {
                $st->bind_param("iis", $idNews,$idScuola,$ruolo);
                $st->execute();
            }
            $st->close();
        }
        dbClose($dbConn);
    }
    function FollowScuola($idScuola,$ruolo = "genitore")
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO scuola_follow (id_utente,id_scuola,ruolo) VALUES (?,?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iis",$idUtente,$idScuola,$ruolo);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SEGUI_GIA;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function FollowClasse($idClasse, $ruolo = "genitore")
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO scuola_classe_follow (id_utente,id_classe,ruolo) VALUES (?,?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iis",$idUtente,$idClasse,$ruolo);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SEGUI_GIA;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function UnfollowScuola($idScuola, $ruolo = "genitore")
    {
        $idUtente = getIdUtenteFromSession();
        $query = "DELETE FROM scuola_follow WHERE id_utente = ? AND id_scuola = ? AND ruolo = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iis",$idUtente,$idScuola,$ruolo);
            $result = $st->execute() && $dbConn->affected_rows > 0 ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function UnfollowClasse($idClasse, $ruolo = "genitore")
    {
        $idUtente = getIdUtenteFromSession();
        $query = "DELETE FROM scuola_classe_follow WHERE id_utente = ? AND id_scuola = ? AND ruolo = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iis",$idUtente,$idClasse,$ruolo);
            $result = $st->execute() && $dbConn->affected_rows > 0 ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function SbloccaCodice($codice, $nome,$cognome,$nascita)
    {
        $res = IsCodiceFamigliaGiaUtilizzato($codice);
        if(is_array($res))
        {
            if($res["usatoUtente"])
                return StatusCodes::CODICE_GIA_IN_USO;
            if(!$res["usato"])
            {
                if(!AggiornaCampoCodice($res["id"],$nome,$cognome,$nascita))
                    return StatusCodes::SQL_FAIL;
            }
            return UsaCodiceFamiglia($res["id"],$res["ruolo"],$nome,$cognome,$nascita);
        }
        return StatusCodes::CODICE_INVALIDO;
    }
    function UsaCodiceFamiglia($idCodice,$ruolo,$nome,$cognome,$data)
    {
        $querySelectCodice = "SELECT id_scuola,id_classe FROM scuola_codice_famiglia WHERE id_codice = ? AND alunno_nome = ? AND alunno_cognome = ? AND alunno_nascita = ?";
        $dbConn = dbConnect();
        $result = StatusCodes::FAIL;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("isss",$idCodice,$nome,$cognome,$data);
            if($st->execute())
            {
                $st->bind_result($idScuola,$idClasse);
                if($st->fetch()) //codice trovato e i nomi corrispondono
                {
                    $result = InserisciUtilizzoCodiceFamiglia($idCodice,$ruolo);
                    FollowScuola($idScuola, $ruolo);
                    FollowClasse($idClasse, $ruolo);
                }
                else
                    $result = StatusCodes::CODICE_CAMPI_INVALIDI;
            }
            $st->close();
        }
    }
    function InserisciUtilizzoCodiceFamiglia($idCodice,$ruolo)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "INSERT INTO scuola_codice_famiglia_uso (id_codice,id_utente,ruolo) VALUES (?,?,?)";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("iis", $idCodice,$idUtente,$ruolo);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::CODICE_GIA_IN_USO;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function GetRuoloDaSigla($sigla)
    {
        switch($sigla)
        {
            case "G":
                return "genitore";
            case "S":
                return "studente";
            case "D":
                return "docente";
            case "A":
                return "ata";
            case "P":
                return "preside";
        }
        die(); //non si può arrivare in questo punto
    }
    function AggiornaCampoCodice($idCodice,$nome,$cognome,$data)
    {
        $query = "UPDATE scuola_codice_famiglia SET alunno_nome = ?, alunno_cognome = ?, alunno_nascita = ? WHERE id_codice = ?";
        $result = false;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("sssi",$nome,$cognome,$data,$idCodice);
            $result = $st->execute();
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function VerificaCodice($codice) //controllo formale del codice
    {
        if(strlen($codice) == 17)
        {
            if(substr($codice,0,3)=="STU")
            {
                $tipoCodice = substr($codice, 16, 1);
                switch($tipoCodice)
                {
                    case "G": //genitore
                    case "S": //studente
                    case "D": //docente
                    case "A": //ata
                    case "P": //preside
                        return StatusCodes::OK;
                }
            }
        }
        return StatusCodes::CODICE_INVALIDO;
    }
    function IsCodiceFamigliaGiaUtilizzato($codice)
    {
        $idUtente = getIdUtenteFromSession();
        $tipo = substr($codice, 16, 1);
        switch($tipo)
        {
            case "G":
                $query = "SELECT id_codice, (!ISNULL(alunno_nome) AND !ISNULL(alunno_cognome) AND !ISNULL(alunno_nascita)) AS usato, (SELECT COUNT(*) FROM scuola_codice_famiglia_uso WHERE id_codice=cod.id_codice AND ruolo = ?) AS usatoUtente FROM scuola_codice_famiglia AS cod WHERE codice_genitore = ?";
                break;
            case "S":
                $query = "SELECT id_codice, (!ISNULL(alunno_nome) AND !ISNULL(alunno_cognome) AND !ISNULL(alunno_nascita)) AS usato, (SELECT COUNT(*) FROM scuola_codice_famiglia_uso WHERE id_codice=cod.id_codice AND ruolo = ?) AS usatoUtente FROM scuola_codice_famiglia AS cod WHERE codice_studente = ?";
                break;
            default:
                return StatusCodes::CODICE_INVALIDO;
        }
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ss",$tipo,$codice);
            if($st->execute())
            {
                $st->bind_result($idCodice, $usato, $usatoUtente);
                if($st->fetch())
                    $result = array("id"=>$idCodice, "usato"=>$usato, "usatoUtente"=>$usatoUtente,"ruolo"=>GetRuoloDaSigla($tipo));
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function VerificaRuolo($idScuola, $ruoli)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT ruolo FROM scuola_gestione WHERE id_scuola = ? AND id_utente = ?";
        $result = false;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii",$idScuola,$idUtente);
            if($st->execute())
            {
                $st->bind_result($ruoloFound);
                if($st->fetch())
                {
                    $found = array_filter($ruoli, function($item) {return $item==$ruoloFound;} );
                    $result = count($found) > 0;
                }
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function VerificaAutorizzazioneLetturaNewsScuola($idNews)
    {
        $idUtente = getIdUtenteFromSession();
        $query = "SELECT COUNT(*) FROM news_scuola AS ns JOIN scuola_follow AS sf ON ns.pubblicataDaScuola=sf.id_scuola WHERE sf.id_utente=? AND ns.id=?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii", $idUtente,$idNews);
            if($st->execute())
            {
                $st->bind_result($count);
                if($st->fetch())
                    $result = $count > 0 ? StatusCodes::OK : StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function VerificaAutorizzazioneLetturaNewsClasse($idNews)
    {
        $idUtente = getIdUtenteFromSession();
        $q = "SELECT COUNT(*) AS access_granted FROM news_scuola_classe_destinatario AS perm JOIN scuola_classe_follow AS foll ON perm.id_classe = foll.id_classe AND perm.ruolo = foll.ruolo WHERE foll.id_utente = ? AND perm.id_news = ?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("ii",$idUtente,$idNews);
            if($st->execute())
            {
                $st->bind_result($access);
                if($st->fetch())
                    $result = $access > 0 ? StatusCodes::OK : StatusCodes::SCUOLA_PERMESSI_INSUFFICIENTI;
            }
            $st->close();
        }
        dbClose($dbConn);
        return $result;
    }
    function SegueScuola($idScuola)
    {
        return true;
    }
    function SegueClasse($idClasse)
    {
        return true;
    }
    function GetFollowerScuolaDevices($idScuola, $destinatari)
    {
        $query = "SELECT dev.id_utente,dev.token,dev.deviceOS.foll.ruolo FROM scuola_follow AS foll JOIN push_devices AS dev ON foll.id_utente=dev.id_utente WHERE foll.id_scuola=?";
        $result = StatusCodes::FAIL;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i",$idScuola);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::SQL_FAIL;
            if($result == StatusCodes::OK)
            {
                $st->bind_result($idUtente,$token,$deviceOS,$ruolo);
                $result = array();
                while($st->fetch())
                {
                    if(count(array_filter($destinatari, function($item) { $item==$ruolo; })) > 0)
                    {
                        $device = array("user"=>$idUtente, "token"=>$token,"deviceOS"=>$deviceOS);
                        array_push($result, $device);
                    }
                }
            }
            $st->close();
        }
        dbClose();
        return $result;
    }
    function GetNomeScuolaById($idScuola)
    {
        $query = "SELECT nome FROM scuola WHERE id = ?";
        $result = "";
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("i",$idScuola);
            if($st->execute())
            {
                $st->bind_result($nomeScuola);
                if($st->fetch())
                    $result = $nomeScuola;
            }
            $st->close();
        }
        dbClose();
        return $result;
    }
    function InviaNotificaPushScuola($idScuola,$idNews,$titolo,$corpo,$destinatari)
    {
        $devices = GetFollowerScuolaDevices($idScuola, $destinatari);
        sendPushNotification($titolo,$corpo,GetNomeScuolaById($idScuola),$idNews,$devices);
    }
    function UtentePuoPostarePerScuola($idScuola)
    {
        return VerificaRuolo($idScuola, array(RUOLO_PRESIDE,RUOLO_SEGRETERIA));
    }
?>
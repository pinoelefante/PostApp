<?php
    session_start();
    
    require_once("enums.php");
    require_once("functions.php");
    
    $action = getParameter("action", true);
    $responseCode = StatusCodes::FAIL;
    $responseContent = "";
    switch($action)
    {
		case "RequestAccessCode":
			$code = GeneraAccessCode();
			if($code != FALSE)
			{
				$responseContent = $code;
				$responseCode = StatusCodes::OK;
			}
			break;
		case "RegisterAccessCode":
			$code = getParameter("code", true);
			$localita = getParameter("loc", true);
			$mail = getParameter("mail");
			$responseCode = RegisterUser($code, $localita, $mail);
			if($responseCode == StatusCodes::OK)
			{
				Access($code);
				RegistraComuneResidenza($localita);
			}
			break;
		case "Access":
			$code = getParameter("code", true);
			$responseCode = Access($code);
			break;
		case "CambiaLocalita":
			if(isLogged(true))
			{
				$localita = getParameter("localita", true);
				//TODO: cambia localita di residenza dell'utente
			}
			break;
		case "RipristinaUtente": //eliminare se si passa ad autenticazione con username e password
			//TODO: cancellare utente corrente
			//		effettuare accesso con nuovo utente
			break;
		case "RegistraPush":
			if(isLogged(true))
			{
				$token = getParameter("token", true);
				$deviceType = getParameter("deviceOS", true);
				$deviceId = getParameter("deviceId", true);
				$responseCode = RegistraDevice($token, $deviceType,$deviceId);
			}
			break;
		case "UnregisterPush":
			if(isLogged(true))
			{
				$token = getParameter("token", true);
				$deviceType = getParameter("deviceOS", true);
				$deviceId = getParameter("deviceId", true);
				$responseCode = UnRegistraDevice($token, $deviceType,$deviceId);
			}
			break;
        default:
            $responseCode = StatusCodes::METODO_ASSENTE;
            break;
    }
    sendResponse($responseCode, $responseContent);
    
	function GeneraAccessCode()
	{
		do 
		{
			$code = uniqid("USER", false);
			usleep(2);
		}
		while(IsCodiceUtenteEsiste($code));
		
		return $code;
	}
	function RegisterUser($code, $localita, $mail = NULL)
	{
		$query = "INSERT INTO utente (codice_utente, comune_residenza, email) VALUES (?,?,?)";
		$dbConn = dbConnect();
		$result = StatusCodes::FAIL;
		if($st = $dbConn->prepare($query))
		{
			$st->bind_param("sss", $code,$localita,$mail);
			$result = $st->execute() ? StatusCodes::OK : StatusCodes::REG_CODICE_IN_USO;
			$st->close();
		}
		dbClose($dbConn);
		return $result;
	}
	function Access($accessCode)
	{
		$query = "SELECT id FROM utente WHERE codice_utente = ?";
		$dbConn = dbConnect();
		$result = StatusCodes::LOGIN_ERROR;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("s",$accessCode);
            $st->execute();

            $st->bind_result($id);
            if($st->fetch())
			{
                $result = StatusCodes::OK;
				$_SESSION["idUtente"] = $id;
			}
            $st->close();
        }
        dbClose($dbConn);
        return $result;
	}
	function IsCodiceUtenteEsiste($codice)
	{
		$query = "SELECT id FROM utente WHERE codice_utente = ?";
		$dbConn = dbConnect();
		$result = FALSE;
        if($st = $dbConn->prepare($query))
        {
            $st->bind_param("s",$codice);
            $st->execute();

            $st->bind_result($id);
            if($st->fetch())
                $result = TRUE;
            $st->close();
        }
        dbClose($dbConn);
        return $result;
	}
	function RegistraComuneResidenza($loc)
	{
		$idUtente = getIdUtenteFromSession();
		$query = "INSERT INTO editor_follow (id_utente, id_editor,cancellabile) SELECT $idUtente AS id_utente, id, 0 AS cancellabile FROM editor WHERE localita = ? AND categoria = 'Comune' AND approvato=1";
		$dbConn = dbConnect();
		$result = StatusCodes::FAIL;
		if($st = $dbConn->prepare($query))
		{
			$st->bind_param("s",$loc);
            $result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;

            $st->close();
		}
		dbClose($dbConn);
		return $result;
	}
	function RegistraDevice($token, $device, $deviceId)
	{
		$idUtente = getIdUtenteFromSession();
		$query = "INSERT INTO push_devices (id_utente,token,deviceOS,deviceId) VALUES (?,?,?,?)";
		$dbConn = dbConnect();
		$result = StatusCodes::FAIL;
		if($st = $dbConn->prepare($query))
		{
			$st->bind_param("isis",$idUtente,$token,$device,$deviceId);
			$result = $st->execute() ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
		}
		dbClose($dbConn);
		return $result;
	}
	function UnRegistraDevice($token, $device,$deviceId)
	{
		$idUtente = getIdUtenteFromSession();
		$query = "DELETE FROM push_devices WHERE id_utente=? AND token=? AND deviceOS=? AND deviceId = ?";
		$dbConn = dbConnect();
		$result = StatusCodes::FAIL;
		if($st = $dbConn->prepare($query))
		{
			$st->bind_param("isis",$idUtente,$token,$device,$deviceId);
			$result = $st->execute() && $dbConn->affected_rows>0 ? StatusCodes::OK : StatusCodes::FAIL;
            $st->close();
		}
		dbClose($dbConn);
		return $result;
	}
?>
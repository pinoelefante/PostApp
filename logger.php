<?php
    //SELECT * FROM log_request as req join log_response as resp ON req.id=resp.request_id 
    require_once("config.php");
    require_once("functions.php");
    function LogRequest()
    {
        if(DEBUG_ENABLE && DEBUG_SAVE_REQUEST)
        {
            $server = GetArrayToString($_SERVER);
            $get = GetArrayToString($_GET);
            $post = GetArrayToString($_POST);
            $session = GetArrayToString($_SESSION);
            $query = "INSERT INTO log_request (_SERVER,_POST,_GET,_SESSION) VALUES (?,?,?,?)";
            $dbConn = dbConnect();
            $idRequest = -1;
            if($st = $dbConn->prepare($query))
            {
                $st->bind_param("ssss", $server,$post,$get,$session);
                $st->execute();
                $idRequest = $dbConn->insert_id;
                $st->close();
            }
            dbClose($dbConn);
            return $idRequest;
        }
    }
    function LogResponse($responseJson, $requestId)
    {
        if(DEBUG_ENABLE && DEBUG_SAVE_RESPONSE)
        {
            $query = "INSERT INTO log_response (request_id,response) VALUES (?,?)";
            $dbConn = dbConnect();
            if($st = $dbConn->prepare($query))
            {
                $st->bind_param("is", $requestId, $responseJson);
                $st->execute();
                $st->close();
            }
            dbClose($dbConn);
        }
    }
    function LogMessage($messaggio, $file = "log_error.log")
    {
        if(DEBUG_LOG_MESSAGE)
        {
            $timestamp = date("d/m/Y - H:i:s");
            $line = "$timestamp: $messaggio\n";
            file_put_contents ("./logs/$file", $line, FILE_APPEND | LOCK_EX);
        }
    }
    function GetArrayToString($array)
    {
        $content = "";
        foreach($array as $key=>$value)
            $content = $content."$key = $value\n";
        return $content;
    }
    function GetDebugMessage()
    {
        $idUtente = getIdUtenteFromSession();
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        $server = GetArrayToString($_SERVER);
        $get = GetArrayToString($_GET);
        $post = GetArrayToString($_POST);
        $session = GetArrayToString($_SESSION);
        $message = "RequestId: ".$GLOBALS['requestId']."\n<br>ID Utente: $idUtente<br>IP Address: $remoteAddr\n<br>SERVER:\n<br>$server\n<br>POST:\n<br>$post\n<br>GET:\n<br>$get\n<br>SESSION:\n<br>$session";
        return $message;
    }
?>
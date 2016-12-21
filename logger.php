<?php
    require_once("config.php");
    function LogRequest()
    {
        if(DEBUG_ENABLE && DEBUG_SAVE_REQUEST)
        {
            //ritorna id richiesta
        }
    }
    function LogResponse($responseJson, $requestId)
    {
        if(DEBUG_ENABLE && DEBUG_SAVE_RESPONSE)
        {

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
?>
<?php
    require_once("config.php");
    function LogRequest()
    {
        if(DEBUG_ENABLE && DEBUG_SAVE_REQUEST)
        {

        }
    }
    function LogResponse($responseJson)
    {
        if(DEBUG_ENABLE && DEBUG_SAVE_RESPONSE)
        {

        }
    }
    function LogMessage($messaggio)
    {
        $timestamp = date("d/m/Y - H:i:s");
        $line = "$timestamp: $messaggio\n";
        file_put_contents ("./logs/log_error.log", $line, FILE_APPEND | LOCK_EX);
    }
?>
<?php
    require_once("config.php");
    require_once("enums.php");

    function isLogged($required = false)
    {
        $sessionVer = sessionVerification();
        if($required && !$sessionVer)
        {
            sendResponse(StatusCodes::NON_LOGGATO, "");
            exit;
        }
        return $sessionVer;
    }
    function getIdUtenteFromSession()
    {
        if(isset($_SESSION["idUtente"]))
            return $_SESSION["idUtente"];
        return NULL;
    }
    function getParameter($par,$required = false)
    {
        if(isset($_POST[$par]) && !empty($_POST[$par]))
            return $_POST[$par];
        else if(isset($_GET[$par]) && !empty($_GET[$par]))
            return $_GET[$par];
        if($required)
        {
            sendResponse(StatusCodes::RICHIESTA_MALFORMATA, "$par is required");
            die();
        }
        return NULL;
    }
    function dbConnect()
    {
        $mysqli = new mysqli(DBADDR, DBUSER, DBPASS, DBNAME);
        $mysqli->set_charset("utf8");
        return $mysqli;
    }
    function dbClose($mysqli)
    {
        $mysqli->close();
    }
    function sendResponse($response, $content = "")
    {
        $array = array('response' => $response, 
                       'time' => date("Y-m-d H:i:s"),
                       'content' => $content);
        header('Content-Type: application/json');
        echo json_encode($array);
    }
    function sendPOSTRequest($url, $data)
    {
        //$url = 'http://server.com/path';
        //$data = array('key1' => 'value1', 'key2' => 'value2');

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) 
        { 
            /* Handle error */ 
        }

        var_dump($result);
    }
    function sendEmail($destinatario, $oggetto, $corpo)
    {
        //mail($destinatario, $oggetto, $corpo);
    }
    function sendEmailAdmin($oggetto, $corpo)
    {
        //mail(ADMIN_EMAIL, $oggetto, $corpo);
    }
    function sessionVerification()
    {
        if(isset($_SESSION["idUtente"]) &&!empty($_SESSION["idUtente"]))
            return true;
        return false;
    }
    //ritorna percorso salvataggio immagine
    function SalvaImmagine($immagine, $folder = "images")
    {
        if($immagine==NULL)
            return NULL;
        //TODO: salvataggio immagine su disco
    }
    function hashPassword($password)
    {
        $options = array(
            'cost' => 10,
        );
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
    function costTimeHashPassword($timeTarget = 0.05 /*50ms*/)
    {
        if($timeTarget == NULL)
            $timeTarget = 0.05;

        $cost = 5;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("testtest", PASSWORD_BCRYPT, array("cost" => $cost));
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        return $cost;
    }
?>
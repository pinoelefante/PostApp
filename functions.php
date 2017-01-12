<?php
    require_once("config.php");
    require_once("enums.php");
    require_once("logger.php");

    $requestId = LogRequest();
    checkUserAgent();

    function isLogged($required = false)
    {
        $sessionVer = sessionVerification();
        if($required && !$sessionVer)
        {
            sendResponse(StatusCodes::LOGIN_NON_LOGGATO, "");
            exit;
        }
        return $sessionVer;
    }
    function getIdUtenteFromSession($failIfNotLogged = true)
    {
        if(isset($_SESSION["idUtente"]))
            return $_SESSION["idUtente"];
            
        if($failIfNotLogged)
        {
            sendResponse(StatusCodes::LOGIN_NON_LOGGATO, "");
            die();
        }
        return -1; //utente non loggato
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
            $action = getParameter("action");
            $debug = GetDebugMessage();
            $corpoMail = "Il server ha ricevuto una richiesta malformata. Ecco la richiesta:\n\n<br><br>$debug";
            sendEmailAdmin("[PostApp] Richiesta malformata - action = $action",$corpoMail);
            die();
        }
        return NULL;
    }
    function sendResponse($response, $content = "")
    {
        $array = array('response' => $response, 
                       'time' => date("Y-m-d H:i:s"),
                       'content' => empty($content) ? "" : $content );
        header('Content-Type: application/json');
        $responseJson = json_encode($array);
        LogResponse($responseJson, $GLOBALS['requestId']);
        echo $responseJson;

        if($response<0)
        {
            $debug = GetDebugMessage();
            $corpoMail = "E' stata rilevata una richiesta fallita al server ($response). Ecco la richiesta\n\n<br><br>$debug";
            sendEmailAdmin("[PostApp] Richiesta fallita",$corpoMail);
        }
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
    function SalvaImmagine($immagineBytes, $folder = "images")
    {
        if(empty($immagineBytes))
            return NULL;

        if(empty($folder) || strlen($folder) > 34) //nome file 30 + 34 path
            $folder = "images";
            
        $result = NULL;
        $closed = false;
        if(!empty($immagineBytes))
        {
            @mkdir($folder, 0664, true); // 0664 = lettura/scrittura proprietario&gruppo, lettura utenti
            $filename = GeneraUniqueFileName($folder, "IMG");
            $fp = fopen("./$folder/$filename", "wb");
            if(fwrite($fp, $immagineBytes))
            {
                $closed = fclose($fp);
                $result = "$folder/$filename";
                $ext = GetFileExtension("./$folder/$filename");
                if(IsImage($ext) && rename("./$folder/$filename", "./$folder/$filename$ext"))
                {
                    $result = "$result$ext";
                    $thumbSave = SalvaThumb($folder,$filename, $ext);
                    LogMessage("thumb salvata ($filename$ext): $thumbSave","thumbs.log");
                }
                else //il file non è un'immagine valida
                {
                    if(!unlink("./$result"))
                        sendEmailAdmin("[PostApp] File non valido","E' stato caricato un file che non è un'immagine ma non è stato possibile cancellarlo\n<br>Nome file: $result");
                    $result = NULL;
                }
            }
            if(!$closed)
                fclose($fp);
            
        }
        return $result;
    }
    function SalvaThumb($folder,$filename, $ext)
    {
        switch($ext)
        {
            case ".jpg";
                $image = imagecreatefromjpeg("./$folder/$filename$ext");
                break;
            case ".png":
                $image = imagecreatefrompng("./$folder/$filename$ext");
                break;
            case ".gif":
                $image = imagecreatefromgif("./$folder/$filename$ext");
                break;
            default:
                return false;
        }
        $size = getimagesize("./$folder/$filename$ext");
        $newHeight = (64*$size[1])/$size[0];
        $imageResized = imagescale($image, 64, $newHeight);
        switch($ext)
        {
            case ".jpg";
                return imagejpeg($imageResized, "./$folder/thumb.$filename$ext");
            case ".png":
                return imagepng($imageResized, "./$folder/thumb.$filename$ext");
            case ".gif":
                return imagegif($imageResized, "./$folder/thumb.$filename$ext");
        }
        return false;
    }
    function GetFileExtension($filepath)
    {
        $mime = mime_content_type($filepath);
        switch($mime)
        {
            case "image/jpeg":
                return ".jpg";
            case "image/png":
                return ".png";
            case "image/gif":
                return ".gif";
            /* Non c'è il supporto per la creazione del thumb
            case "image/bmp":
                return ".bmp";
            */
            case "application/pdf":
                return ".pdf";
            /*
            case "":
                return "";
            */
        }
    }
    function IsImage($extension)
    {
        switch($extension)
        {
            case ".jpg":
            case ".png":
            case ".gif":
            case ".bmp":
                return true;
        }
        return false;
    }
    function GeneraUniqueFileName($folder, $prefix = "IMG")
	{
		do 
		{
			$filename = uniqid("IMG", true);
			usleep(2);
		}
        while(file_exists($folder."/".$filename));
		return $filename;
	}
    function hashPassword($password)
    {
        $options = array('cost' => HASH_COST_TIME);
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
    function GetQRCode($code, $dim = "360x360")
    {
        return "https://chart.googleapis.com/chart?cht=qr&chl=$code&chs=$dim&choe=UTF-8&chld=L|2";
    }
    function GeneraCodice($prefix = "", $appendix = "")
    {
        $code = uniqid($prefix, false).$appendix;
        return $code;
    }
    function checkUserAgent()
    {
        if(CHECK_USER_AGENT)
        {
            if($_SERVER['HTTP_USER_AGENT']=="PostAppClient")
                return true;
            sendResponse(StatusCodes::INVALID_CLIENT);
            exit();
        }
        return true;
    }
?>
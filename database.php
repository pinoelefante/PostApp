<?php
    require_once("config.php");
    require_once("enums.php");
    require_once("logger.php");
    require_once("functions.php");

    function dbConnect()
    {
        $mysqli = new mysqli(DBADDR, DBUSER, DBPASS, DBNAME);
        if ($mysqli->connect_errno) 
        {
            LogMessage("(".$mysqli->connect_errno.") ".$mysqli->connect_error, "mysql.log");
            sendResponse(StatusCodes::SQL_FAIL);
            exit();
        }
        $mysqli->set_charset("utf8");
        return $mysqli;
    }
    function dbClose($mysqli)
    {
        if($mysqli->errno)
            LogMessage("(".$mysqli->errno.") ".$mysqli->error, "mysql.log");
        $mysqli->close();
    }
    function dbUpdate($query,$parametersType,$parameters)
    {
        $res = false;
        $dbConn = dbConnect();
        if($st = $dbConn->prepare($query))
        {
            call_user_func_array(array($st, 'bind_param'), array_merge(array($parametersType), makeValuesReferenced($parameters)));
            $res = $st->execute();
            $st->close();
        }
        dbClose($dbConn);
        return $res;
    }
	function makeValuesReferenced(&$arr)
	{ 
		$refs = array(); 
		foreach($arr as $key => $value) 
			$refs[$key] = &$arr[$key]; 
		return $refs;
	}
?>
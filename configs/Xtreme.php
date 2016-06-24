<?php

/**
 * @uses to call databse connection
 * @param 
 * @used in pages : every page
 * 
 */
//require_once(SERVER_PATH."/classes/DataBase.php");
class Xtreme{
	private $Conn;
	
	function __construct($para=""){
		$this->Conn=Database::getConnectDb($para);

	}
	function GetConnection()
	{		
		return $this->Conn;	
	}
}
?>

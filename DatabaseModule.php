<?php
/**
 * Database Module stores the default connection information
 * Which is used to connect to the database 
 **/
class DatabaseModule
{
	var $host = "";
	var $username = "";
	var $password = "";
	var $db_name = "";
	var $currLink;
	/**
	 * This loads the default MySQL login
	 * into the relevent variables.
	 **/
	function DatabaseModule()
	{
		$this->host = "";
		$this->username = "";
		$this->password = "";
		$this->db_name = "";
		$this->currLink = new mysqli();
	}
	
	/**
	 * Connects to the MySQL server using default connect data
	 **/
	function Connect()
	{
		$result = $this->currLink->Connect($this->host, $this->username, $this->password, $this->db_name);
		
		if(!$this->currLink->connect_errno) 
		{
			return $this->currLink;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Connects to the MySQL server using custom data
	 **/
	function ConnectUnique($host, $username, $password, $db_name)
	{
		$dbLink = new mysqli("$host", "$username", "$password", "$db_name");
		
		if ( $dbLink->connect_errno)
		{
			return false;
		}
		else
		{
			return $dbLink;
		}
	}
}

?>
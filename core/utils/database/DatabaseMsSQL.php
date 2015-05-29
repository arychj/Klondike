<?php
	/**
	 *	Klondike Content Management System
	 *	http://www.arych.com
	 *	©2009 Erik J. Olson
	 *
	 *	-----------------------------------------------------------------------------
	 *	"THE BEER-WARE LICENSE" (Revision 42):
	 *	<git@arych.com> wrote this file. As long as you retain this notice you
	 *	can do whatever you want with this stuff. If we meet some day, and you think
	 *	this stuff is worth it, you can buy me a beer in return. Erik J. Olson.
	 *	-----------------------------------------------------------------------------
	 *
	 *	core.utils.Database.DatabaseMsSQL
	 *
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.06.26
	 *		Split out from core.utils.Database
	 */
	
	PackageManager::import('core.utils.database.DatabaseInterface');
	
	class DatabaseMsSQL extends DatabaseInterface{
		function open(){
			$config = $this->config;
			
			$this->db_link = mssql_connect($this->config->database_host . (isset($config->database_port) ? ",$config->database_port" : ""), $this->config->database_user, $this->config->database_pass)
								or die ($this->generateError("", 'Error connecting to database'));
								
			mssql_select_db($this->config->database_schema, $this->db_link)
				or die ($this->generateError("", "Error using schema ($this->schema)"));
		}
		
		function close(){
			mssql_close($this->db_link);
		}
		
		function query($query, $override = ''){
			if(@mysql_stat($this->db_link) == NULL)
				return false;
			
			$sql = mssql_query($query, $this->db_link)
					or die($this->generateError($query, 'Query error'));
			
			if($override == '' && ereg("^(select)|(SELECT)", $query) == true){
				if(mssql_num_rows($sql) == false)
					$result = NULL;
				else
					for($i = 0; $i < mssql_num_rows($sql); $i++)
						$result[$i] = mssql_fetch_array($sql, MSSQL_ASSOC); 
				
				return $result;
			}
			else
				return $sql;
		}
	}
?>
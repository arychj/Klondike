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
	 *	core.utils.database.DatabaseInterface
	 *	Abstract class for Database connections
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2008.11.12
	 *		Changed location
	 *	2008.11.04
	 *		Support for multiple databases
	 */

	abstract class DatabaseInterface{
		var $config,
			$db_link;
		
		/**
		 *	constructor
		 *
		 *	@author Erik J. Olson
		 */
		function __construct($config){
			$this->config		= $config;
			$this->open();
		}
		
		/**
		 *	function sanitize
		 *	Sanitizes a string or an array for use in a query
		 *
		 *	@author Erik J. Olson
		 *
		 *	@param	value	the value or values to sanitize
		 *	@return			the sanitized string
		 */
		function sanitize($value){
			if(is_array($value))
				$value = array_walk_recursive($value, $this->sanitize_value);
			else
				$value = $this->sanitize_value($value);
				
			return $value;
		}
		
		/**
		 *	function generateError
		 *	Generates an error and either displays or emails that error
		 *
		 *	@author Erik J. Olson
		 */
		function generateError($query, $error){
			$message  = "SQL Error Notification\n";
			$message .= "--------------------------\n";
			$message .= "    Time: " . date('Y.m.d H:i:s') . "\n";
			$message .= "     URL: $_SERVER[REQUEST_URI]?"."$_SERVER[QUERY_STRING]\n";
			$message .= "\n";
			$message .= "Error as follows\n";
			$message .= "--------------------------\n";
			$message .= "Error:\n" . $error . "\n";
			$message .= "\n";
			$message .= "Query:\n" . $query . "\n";
			
			//hide the error message if debuging is not turned on (ie. being sent to a user)
			if($this->config->debug)
				echo('<div style = "font-weight: bold;">SQL Query Failed:</div><hr/><div style = "font-weight: bold;">Error:</div>' . $error . '<br/><br/><div style = "font-weight: bold;">SQL:</div>' . $query);
			else{
				mail($this->config->email_admin, "SQL Error Notification", $message, "From: $this->config->name_server Web Server <$this->config->email_admin>");
				//return "&gt;&gt; A SQL error has occured. An error ticket has been filed and we will address this issue as soon as possible.<br/>&gt;&gt;" . date('Y.m.d H:i:s');
			}
		}
		
		/**
		 *	function sanitize_value
		 *	Sanitizes a value for use in a query
		 *
		 *	@author Erik J. Olson
		 *
		 *	@param	value	the value to sanitize
		 *	@return			the sanitized value
		 */
		private function sanitize_value($value){
			return mysql_real_escape_string($value);
		}
		
		abstract function open();
		abstract function close();
		abstract function query($query, $override = '');
	}
?>

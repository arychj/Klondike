<?php
	/**
	 *	Klondike Content Management System
	 *	http://www.arych.com
	 *	2008 Erik J. Olson
	 *
	 *	-----------------------------------------------------------------------------
	 *	"THE BEER-WARE LICENSE" (Revision 42):
	 *	<git@arych.com> wrote this file. As long as you retain this notice you
	 *	can do whatever you want with this stuff. If we meet some day, and you think
	 *	this stuff is worth it, you can buy me a beer in return. Erik J. Olson.
	 *	-----------------------------------------------------------------------------
	 *
	 *	core.utils.security.Authenticate
	 *	Provides functions to authenticate the user and session against users
	 *	in the database.
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.09.30
	 *		Implemented dynamic tables, removing static references
	 *			to hard coded authentication tables
	 *	2010.06.26
	 *		Reimplemented for Klondike integration
	 *	2009.03.25
	 *		Support for authentication with pre-hashed passwords
	 *	2009.03.22
	 *		Fixed 'isAuthenticated()' fuckup
	 *	2008.12.11
	 *		Thought it would be a good idea to let a user log out
	 *	2008.11.16
	 *		Removed session validation
	 *	2008.11.12
	 *		Changed location
	 *	2008.11.11
	 *		Created
	 */
	
	PackageManager::import('core.utils.database.DatabaseFactory');
	PackageManager::import('core.utils.Link');
	
	class Authenticate{
		var $config, $database, $tables;
		
		/**
		 *	function	SecurityHeader
		 *	CONSTRUCTOR
		 *
		 *	@author	Erik J. Olson
		 */
		function __construct($config = false, $database = false){
			$this->config	= ($config == false ? $GLOBALS['config'] : $config);
			$this->database	= ($database == false ? DatabaseFactory::newInstance($this->config->database_type, $this->config) : $database);
			$this->tables	= new DatabaseTables();
		}
		
		/**
		 *	function	authenticate
		 *				Authenticates the user
		 *
		 *	@author	Erik J. Olson
		 *	@return		true if credentials are valid, error code otherwise
		 */
		function authenticate($username, $password){
			$auth = Error::auth_credentialsInvalid;
			
			if($this->config->site_requireSecure && !$this->isSecureConnection())
				$auth = Error::auth_connectionNotSecure;
			else{
				if(isset($username) && isset($password)){
					$user = $this->checkCredentials($username, $password);
					
					if($user == NULL)
						$auth = Error::auth_credentialsInvalid;
					elseif($user['active'] == 0)
						$auth = Error::auth_credentialsInactive;
					else{
						 @session_start();
						 
						 $_SESSION['k_userId']			= $user['id'];
						 $_SESSION['k_userNameLast']	= $user['name_last'];
						 $_SESSION['k_userNameFirst']	= $user['name_first'];
						 $_SESSION['k_userGroups']		= $this->getUserGroups($user['id']);
						 $auth = true;
					}
				}
			}
			
			return $auth;
		}
		
		/**
		 *	function	logout
		 *				Logs the user out and destroys the associated session
		 *
		 *	@author		Erik J. Olson
		 */
		function logout(){
			session_unset();
			session_destroy();
		}
		
		/**
		 *	function	isSecureConnections
		 *				Checks if the connections is running over HTTPS
		 *
		 *	@author		Erik J. Olson
		 *	@return		true if HTTPS, false otherwise
		 */
		function isSecureConnection(){
			return ($_SERVER['SERVER_PORT'] == '443' ? true : false);
		}
		
		/**
		 *	function	checkCredentials
		 *	PRIVATE		Checks the supplied credentials against the database
		 *
		 *	@author		Erik J. Olson
		 *	@param		username	the user's username
		 *	@param		password	the user's password
		 *	@return		the credentials, errorcode otherwise
		 */
		private function checkCredentials($username, $password){
			$username = $this->database->sanitize($username);
			$password = $this->database->sanitize($password);
			
			$user = $this->database->query("SELECT * FROM {$this->tables->k_authenticationUsers} WHERE username = '$username' AND password = '" . md5($password) . "'");
			return $user[0];
		}
		
		/**
		 *
		 */
		private function getUserGroups($id){
			$groups = $this->database->query("SELECT name FROM {$this->tables->k_authenticationGroups} AS g RIGHT JOIN {$this->tables->k_authenticationMemberships} AS m ON g.id = m.id_group WHERE id_user = '$id'");
			
			$group_list = array();
			foreach($groups as $group)
				$group_list[] = $group['name'];
				
			return $group_list;
		}
	}
?>

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
	 *	core.utils.security.SecurityHeader
	 *	
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.06.26
	 *		Created
	 */
	
	PackageManager::import('core.utils.Link');

	class SecurityHeader{
		
		/**
		 *	function	checkAuthentiction
		 *				Checks if the user is authenticated and sends them to the
		 *				login page if not.
		 *
		 *	@author		Erik J. Olson
		 *	
		 *	@param		$groups	
		 */
		function checkAuthentication($groups = NULL){
			$loginpage = new Link($GLOBALS['config']->auth_loginPage);

			if($groups != NULL && $this->isAuthenticated()){
				if(!$this->checkUserGroups($groups))
					header("Location: $loginpage?ref=$_GET[kid]&na=true");
			}
			else
				header("Location: $loginpage?ref=$_GET[kid]");
		}

		/**
		 *
		 */
		function checkUserGroups($authedGroups){
			$userGroups = $_SESSION['k_userGroups'];

			foreach($userGroups as $group)
				if(in_array($group, $authedGroups))
					return true;
			
			return false;
		}
		
		
		/**
		 *	function	isAuthenticated
		 *				Determines if the user has been authenticated
		 *
		 *	@author		Erik J. Olson
		 *	@return		true if the user has been authenticated, false otherwise
		 */
		function isAuthenticated(){
			if(isset($_SESSION['k_userId']))
				return true;
			else
				return false;
		}
	}
?>

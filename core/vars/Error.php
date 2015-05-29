<?php
	/**
	 *	Klondike Content Management System
	 *	http://arych.com
	 *	©2009 Erik J. Olson
	 *		
	 *	-----------------------------------------------------------------------------
	 *	"THE BEER-WARE LICENSE" (Revision 42):
	 *	<git@arych.com> wrote this file. As long as you retain this notice you
	 *	can do whatever you want with this stuff. If we meet some day, and you think
	 *	this stuff is worth it, you can buy me a beer in return. Erik J. Olson.
	 *	-----------------------------------------------------------------------------
	 *
	 *	/vars/error.inc.php
	 *	Contains error codes and descriptions
	 *
	 *	@changelog
	 *	2008.11.11
	 *		Created
	 */
	 
	class Error{
		/*** Generic Error ***/
			const	DNE					= 001,
				unknown_error				= 000;
			
		/*** Authentication ***/
			const	auth_credentialsInactive		= 105,
				auth_credentialsInvalid			= 104,
				auth_credentialsDNE			= 103,
				auth_credentialsIncorrectFormat		= 102,
				auth_connectionNotSecure		= 101;
		
		/*** Database ***/
			const	database_error				= 200;
			
		/*** XML ***/
			const	xml_badParams				= 304,
				xml_noAction				= 303,
				xml_notSet				= 302,
				xml_error				= 301;
					
		/*** Users ***/
			const	user_alreadyExists			= 502,
				user_DNE				= 501;
	}
?>

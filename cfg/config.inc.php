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
	 *	/cfg/config.inc.php
	 *	Contains configuration variables for the site. Duh?
	 *
	 *	@changelog
	 */

	class Config{
		var $debug				= true;
		
	/*** Server Settings ***/
		var $server_name			= 'example.com';
		var $server_admin			= 'admin@example.com';
		var $server_path			= '/var/www/example.com';

	/*** Site Settings ***/	
		var $site_title				= 'Example Site';
		var $site_path				= '/';
		var $site_pathStatic		= '/k';
		var $site_dynamicPage		= 'index.php';
		var $site_staticLinks		= true;
		var $site_requireSecure		= false;
		var $site_customCode		= '/klondike-data/site_custom_code.inc.php';

	/*** Nav Settings ***/
		var $nav_list				= array(
										'Home'		=> 'index',
										'Contact'	=> 'main:contact',
										'About'		=> 'main:about',
										'External'	=> 'http://external.com'
									);

	/*** Klondike Settings ***/
		var $k_default_id			= 'index';
		var $k_searchpath			= "";
		
	/*** Javascript ***/
		var $javascript_path		= '/scripts/js';
		var $javascript				= array(
										'jquery/jquery.js'
									);
		
	/*** Templating settings ***/
		var $repository_dir			= '/klondike-data/pages';
		var $repository_hidden		= 'hidden';
		var $repository_suffix		= 'kldk';
		var $repository_reserved_ids= array(
										'directory'
									);
		
		var $template_dir			= '/klondike-data/templates';
		var $template_current		= 'v1';
		var $template_customCode	= 'custom_code.inc.php';

		
	/*** Database settings ***/
		var $database_type			= 'MySQL';
		var $database_host			= 'localhost';
		var $database_port			= 3306;
		var $database_schema		= 'name';
		var $database_user			= 'user';
		var $database_pass			= 'pass';
    
	/*** Enhanced error reporting ***/
		var $error_enhanced			= false;
		var $error_logfile			= 'error.log';
		var $error_bitmask			= E_ALL;
	
	/*** Authentictions ***/
		var $auth_loginPage			= 'system:authenticate';
		var $auth_loginPrompt		= '<a href = "/k/system/authenticate.kldk">Login</a>';
		var $auth_logoutPrompt		= '<a href = "/k/system/authenticate.kldk?logout=true">Logout ({name_first} {name_last})</a>';
		
	/*** Authentictions ***/
		var $auth_loginPage			= 'system:authenticate';
		var $auth_loginPrompt		= '';
		var $auth_logoutPrompt		= '<a href = "/k/system/authenticate.kldk?logout=true">Logout ({name_first} {name_last})</a>';

	/*** Other ***/
		var $compress_buffer		= false;
		var $date_format			= 'Y.m.d H:i:s';
	}
?>

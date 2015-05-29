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
	 *	core.utils.Database.DatabaseFactory
	 *	Generates and returns a new Database connection for the specified DBMS
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.06.26
	 *		Split out from core.utils.Database
	 */
	
	PackageManager::import('core.utils.database.DatabaseInterface');
	PackageManager::import('core.utils.database.DatabaseMySQL');
	PackageManager::import('core.utils.database.DatabaseMsSQL');
	
	class DatabaseFactory{
		public static function newInstance($type, $config){
			if($type == 'MsSQL')
				return new DatabaseMsSQL($config);
			elseif($type == 'MySQL')
				return new DatabaseMySQL($config);
			else
				return false;
		}
	}
?>
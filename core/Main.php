<?php
	/**
	 *	Klondike Content Management System
	 *	http://arych.com
	 *	©2010 Erik J. Olson
	 *
	 *	-----------------------------------------------------------------------------
	 *	"THE BEER-WARE LICENSE" (Revision 42):
	 *	<git@arych.com> wrote this file. As long as you retain this notice you
	 *	can do whatever you want with this stuff. If we meet some day, and you think
	 *	this stuff is worth it, you can buy me a beer in return. Erik J. Olson.
	 *	-----------------------------------------------------------------------------
	 *
	 *	core.Main.php
	 *	Entry page. All requests are passed through to the Controller.
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.10.07
	 *		URL page id changed from 'id' to 'kid'
	 *	2010.09.30
	 *		Implemented dynamic database table names
	 *	2010.05.20
	 *		Created
	 *		Replaces functionality previously contained in 'index.php'
	 *		'index.php' is now a dummy page
	 */
	
	@session_start();

	require_once('core/PackageManager.php');
	require_once('cfg/config.inc.php');

	 //include if exists, otherwise ignore
	@include_once('cfg/database_tables.inc.php');

	$config = new Config();

	@date_default_timezone_set($config->timezone);

	PackageManager::addSearchpath($config->k_searchpath, true);
	PackageManager::Import('core.utils.Controller');	
	
	$controller = new Controller($config);

	if(!isset($_GET['kid']))
		$id = $config->k_default_id;
	else
		$id = str_replace(array('\.', '/', ' ', '	'), '', trim($_GET['kid']));
	
	$controller->generatePOM($id);
	$controller->sendPage();
?>

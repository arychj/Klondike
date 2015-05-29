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
	 *	/cfg/site_custom_code.inc.php
	 *	Is executed at the end of 'Contoller::generatePOM()'
	 *
	 *	@changelog
	 */
	
	PackageManager::import('core.lib.system.pom.ExtendedPageVars');
	
	ExtendedPageVars::enable($k_pom, $config);
?>

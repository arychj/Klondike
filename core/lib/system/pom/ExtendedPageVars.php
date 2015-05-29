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
	 *	core.system.pom.ExtendedPageVars
	 *	Extends the standard variables available for reference from the page
	 *
	 *	@changelog
	 *	2010.03.10
	 *		Created
	 */
	
	PackageManager::import('core.utils.PageObjectModel');
	
	class ExtendedPageVars{
		static function enable($pom, $config = false){
			$pom->set('page',	'date', 		date(($config == false || !isset($config->date_format) ? 'Y.m.d' : $config->date_format)));
			$pom->set('page',	'time',			date(($config == false || !isset($config->time_format) ? 'H:i:s' : $config->time_format)));
			$pom->set('page',	'year',			date('Y'));
			$pom->set('page',	'client_ip',	$_SERVER['REMOTE_ADDR']);
			$pom->set('page',	'port',			$_SERVER['SERVER_PORT']);
			$pom->set('page',	'user_agent',	$_SERVER['HTTP_USER_AGENT']);
		}
		
		//requires GeoIP C library version 1.4.0 or higher
		static function enableGeoIP($pom){
			$geoInfo = geoip_record_by_name($_SERVER['REMOTE_ADDR']);
			
			if($geoInfo !== FALSE)
				foreach($geoInfo as $key => $val)
					$pom->set('page',	$key, $val);		
		}
	}
?>

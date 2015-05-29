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
	 *	core.lib.system.Analytics.php
	 *	Provides functions for analyzing site traffic, etc
	 *
	 *	@author Erik J. Olson
	 *
	 *	@changelog
	 *	2010.05.26
	 *		Implemented Google Analytics
	 *	2009.11.13
	 *		Created
	 */
	 
	class Analytics{
		/**
		 *	function	getGoogleTag
		 *			Gets the google analytics javascript code
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$ua	the google user account code
		 *	@param		$domain	the domain to track (if the account tracks more than one
		 *
		 *	@return		the javascript code
		 */
		function getGoogleTag($ua, $domain = ""){
			$gTag = "<script type=\"text/javascript\">var _gaq = _gaq || [];_gaq.push(['_setAccount', '{ua}']);{domain}_gaq.push(['_trackPageview']); (function(){var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>";

			$gTag = str_replace('{ua}', "$ua", $gTag);
			$gTag = str_replace('{domain}', "_gaq.push(['_setDomainName', '$domain']);", $gTag);

			return $gTag;
		}
	}
?>

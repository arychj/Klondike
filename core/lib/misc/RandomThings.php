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
	 *	core.lib.misc.RandomThings
	 *	Contains random neat little functions
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.03.05
	 *		Created
	 */
	 
	class RandomThings{
		/**
		 *	function	getRandomBanner
		 *	STATIC		Gets the details for a random page banner
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$dir		the direcotry containing the banners
		 *
		 *	@return		path	=> the path
		 *				title	=> the title
		 */
		static function getRandomBanner($dir){
			$return = false;
			
			$directory 	= scandir($dir);
			if($directory == FALSE || (sizeof($directory) - 2 <= 0))
				return false;
			else{
				$ran = (rand() % (sizeof($directory) - 2)) + 2;
				
				$return				= array();
				$temp				= split('\.', ucwords(str_replace('_', ' ', $directory[$ran])));
				
				$return['path']		= $directory[$ran];
				$return['title']	= $temp[0]; 
			}
			
			return $return;
		}
	}
?>

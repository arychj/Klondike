<?php
	/**
	 *	Klondike Content Management System
	 *	http://www.arych.com
	 *	©2010 Erik J. Olson
	 *
	 *	-----------------------------------------------------------------------------
	 *	"THE BEER-WARE LICENSE" (Revision 42):
	 *	<git@arych.com> wrote this file. As long as you retain this notice you
	 *	can do whatever you want with this stuff. If we meet some day, and you think
	 *	this stuff is worth it, you can buy me a beer in return. Erik J. Olson.
	 *	-----------------------------------------------------------------------------
	 *
	 *	core.lib.system.Filesystem
	 *	Provides functions for dealing with files
	 *
	 *	@author Erik J. Olson
	 *
	 *	@changelog
	 *	2010.07.26
	 *		Corrected template listing bug in 'lsdir()'
	 *	2010.07.23
	 *		Corrected 'constructLink()' error caused by
	 *			transition to Link ids
	 *	2010.05.25
	 *		Added comments
	 *		Corrected functionality of lsdir
	 *	2010.03.11
	 *		Created
	 */
	 
	PackageManager::import('core.utils.Link');

	class Filesystem{

		/**
		 *	function	lsdir
		 *			Lists the contents of a directory
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$name	the name of the caller
		 *	@param		$loc	the location of the caller
		 *	@param		$self	display the calling page int he listing
		 *	@param		$parent	the calling page is the parent dir (index)
		 *
		 *	@return		the HTML listing of the directory
		 */
		function lsdir($name, $loc, $self = false, $parent = true){
			$dir	= @opendir($loc);
//			if($dir === FALSE){
//				$p = substr($loc, strpos($loc, '/') + 1);
//				if($p == 'index' || $p == 'index.kldk')
//					opendir(substr($loc, 0, strpos('/')));
//			}
			
			if($dir == FALSE)
				$dir_contents = false;
			else{
				$dir_contents = array();
				
				while($file = readdir($dir)){
					if(substr($file, 0, 1) != '.' && !self::isHidden($file)){
						$pa = substr($name, 0, strrpos($name, ':'));
						$me = substr($name, (($m = strrpos($name, ':')) !== FALSE ? $m + 1 : $m));
						$my_id = substr($file, 0, strpos($file, '.'));
						$my_ext = substr($file, strrpos($file, '.') + 1);
						if(($my_ext != 'tpl') && ($self || $my_id != $me) && !in_array($my_id, $GLOBALS['config']->repository_reserved_ids))
							$dir_contents[] = self::constructLink($file, $loc, ($parent ? $name : $pa));
					}
				}
				closedir($dir);
			}
			
			sort($dir_contents);
			$dir_contents = (implode($dir_contents));
			return $dir_contents;
		}
		
		/**
		 *	function	mapDir
		 *				Recursively retruns the contents of a directory (a directory map_
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$dir		the directory to map
		 *	@param		$showHidden	include hidden files in the listing
		 *
		 *	@return		an array of the directory map
		 */
		function mapDir($dir, $showHidden = false, $parent = NULL, $contents = NULL){
			if($contents == NULL)
				$contents = array();
			
			if($parent == NULL){
				$parent = $GLOBALS['config']->server_path . "/$dir";
				$contents = self::mapDir("", $showHidden, $parent, $contents);
			}
			else{
				if(($handle = opendir("$parent/$dir"))){
					while(false !== ($file = readdir($handle))){
						$path = "$parent/$dir/$file";
						
						if((substr($file, 0, 1) != '.') && ($showHidden || !self::isHidden($path))){
							if(is_dir("$path")){
								$contents[] = "$dir/$file";
								$contents = self::mapDir("$dir/$file", $showHidden, $parent, $contents);
							}
							else{
								$split = explode('.', $file);
								if(count($split) >= 3 && (strlen($dir) == 0 || $split[0] != 'index') && $split[count($split) - 1] != 'tpl')
									$contents[] = "$dir/$file";
							}
						}
					}
					
					closedir($handle);
				}
			}
			
			return $contents; 
		}

		/**
		 *	function	constructLink
		 *	PRIVATE		Helper function for lsdir, construct a link
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$file	the file to construct a link for
		 *	@param		$loc	the location of the file (path to)
		 *	@param		$name	the name
		 *
		 *	@return		the HTML link
		 */
		private function constructLink($file, $loc, $name){
			$name		= $name->getId();
			$file_base	= (is_dir("$loc/$file") ? $file : substr($file, 0, strpos($file, ".")));
			$title		= ucwords(str_replace('_', ' ', $file_base));
			
			//if($file_base == 'index')
			//	$file_base = '';

			$link = new Link($file_base == '' ? $name : "$name:$file_base");

			return "<a href = \"$link\">$title</a><br/>\r\n";	
		}
		
		/**
		 *	function	isHidden
		 *			Determines if a file is hidden
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$file	the file to check
		 *
		 *	@return		true if hidden, false otherwise
		 */
		function isHidden($file){
			$config = $GLOBALS['config'];
			
			$nodes = preg_split("/\./", $file);
			$length	= sizeof($nodes);
			
			if($length >= 3 && $nodes[$length - 3] == $config->repository_hidden)
				return true;
			else
				return false;
		}
		
		/**
		 *	function	seachDir
		 *			Searches a directory for files matchin a pattern
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$dir		the directory to search
		 *	@param		$pattern	the pattern to searhc for
		 *
		 *	@return		an array of matching filename
		 */
		function searchDir($dir, $pattern){
			$matches = array();
			
			if(is_dir($dir)){
				if($dh = opendir($dir)){
					while(($filename = readdir($dh)) !== false)
						if(fnmatch($pattern, $filename))
							$matches[] = $filename;
					
					closedir($dh);
				}
			}
			
			return $matches;
		}
	}
?>

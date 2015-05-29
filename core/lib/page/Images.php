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
	 *	core.lib.page.Images
	 *	Provides functions for dealing with images
	 *
	 *	@author Erik J. Olson
	 *
	 *	@changelog
	 *	2009.03.13
	 *		Changed location, changed name
	 *		Modified to contain methods in class
	 *	2009.12.04
	 *		Deprecated createGallery in favor of new funtion that takes a page variable and returns nothing
	 */
	 
	class Images{
		static function appendGallery($pom, $path, $ppr, $lightbox = true, $order = 'n', $ordered = false){
			if($lightbox){
				$pom->append('page', 'javascript', '<script type = "text/javascript" src = "/scripts/js/lightbox/js/effects.js"></script>');
				$pom->append('page', 'javascript', '<script type = "text/javascript" src = "/scripts/js/lightbox/js/builder.js"></script>');
				$pom->append('page', 'javascript', '<script type = "text/javascript" src = "/scripts/js/lightbox/lightbox.js"></script>');
				$pom->append('page', 'stylesheets', '<link rel = "stylesheet" href = "/scripts/js/lightbox/css/lightbox.css" type = "text/css" media = "screen"/>');
			}
			
			$pom->append('page', 'content', self::createGallery($path, $ppr, $order, $ordered));
		}
		 
		/**
		 *	function	createGallery
		 *	DEPRECATED	Creates an image gallery
		 *	2009.12.04
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$path		the path to the gallery location
		 *	@param		$ppr		the number of pictures per row
		 *	@param		$order		ascending or descending order
		 *	@param		$ordered	
		 */
		static function createGallery($path, $ppr, $order = 'n', $ordered = false){
			$dir		= "{$GLOBALS[config]->server_path}/$path";
			$folder 	= opendir($dir);
			$files		= array();
			$counter	= 1;
			$gallery	= '<table style = "width: 100%;">' . chr(13) . chr(10);
			$gallery	.= '	<tr>' . chr(13) . chr(10);
			
			if($folder != FALSE){
				while($file = readdir($folder)){
					if(substr($file, 0, 1) != '.')
						$files[] = $file;
				}
				
				if($order == 'r')
					rsort($files);
				else
					sort($files);
				
				foreach($files as $file){
					if(is_file("$dir/$file")){
						$title = ($ordered ? substr(substr($file, 0, strpos($file, '.')), strpos($file, '-') + 1) : substr($file, 0, strpos($file, '.')));
						$title = ucwords(str_replace('_', ' ', $title));
						/*if(is_dir($dir . '/' . $file)){
							$gallery .= '	<tr>' . chr(13) . chr(10). '		<td style = "font-size: 18px; font-weight: bold; font-style: italic;">' . chr(13) . chr(10);
							$gallery .= '			' . $title . chr(13) . chr(10);
							$gallery .= '		</td>' . chr(13) . chr(10) . '	</tr>' . chr(13) . chr(10);
							$gallery .= createGallery($dir . '/' . $file, $ppr, $order);
						}*/
						
						$gallery .= '		<td style = "padding-bottom: 15px; text-align: center; vertical-align: top;"><a href = "/' . $path . '/' . $file . '" rel = "lightbox[gallery]" title = "' . $title . '"><img src = "/scripts/phpThumb/phpThumb.php?src=../../' . $path . '/' . $file . '&amp;w=150" class = "thumbnail" alt = "' . $title . '"/></a><br/>' . (strlen($title) >= 20 ? substr($title, 0, 20) . '...' : $title) . '</td>' . chr(13) . chr(10);
						
						if($counter % $ppr == 0)
							$gallery .= '	</tr>' . chr(13) . chr(10) . '	<tr>' . chr(13) . chr(10);
						
						$counter++;
					}
				}
				
				if($counter % $ppr != 0)
					$gallery .= '	</tr>' . chr(13) . chr(10);
				
				closedir($folder);
			}
			
			$gallery .= '</table>';
			
			return $gallery;
		}
	}
?>

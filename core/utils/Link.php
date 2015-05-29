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
	 *	core.utils.Id
	 *	Manages internal Klondike IDs
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.10.05
	 *		Implemented handling of external links
	 *	2010.09.29
	 *		Added generateMenu function
	 *	2010.06.04
	 *		Implemented new functions
	 *	2010.05.29
	 *		Enhancements to deal with taking in a dynamic page link
	 *	2010.05.21
	 *		Modified to use $GLOBALS['config'] instead of storing it
	 *	2010.05.16
	 *		Created
	 */
	 
	class Link{
		private	$nodes,
			$dynamicPath,
			$staticPath,
			$external = false;
		
		function __construct($id = ""){
			$this->nodes = array();

			$this->staticPath = "{$GLOBALS['config']->site_pathStatic}/";
			$this->dynamicPath = "{$GLOBALS['config']->site_dynamicPage}/?id=";

			if($id != "")
				$this->set($id);
		}
		
		function add($node){
			if(!$this->external)
				$this->nodes[] = $node;
		}

		function remove($node){
			if(!$this->external)
				for($i = 0; $i < count($this->nodes); $i++)
					if($this->nodes[$i] == $node){
						removeIndex($i);
						break;
					}
		}

		function removeNode($i){
			if(!$this->external)
				if($i >= 0 && $i < count($this->nodes)){
					unset($this->nodes[$i]);
					array_merge($this->nodes);
				}
		}

		function removeLastNode(){
			if(!$this->external)
				$this->removeNode(count($this->nodes) - 1);
		}

		function set($link){
			$type = substr($link, 0, 4);
			if($type == "http" || $type == "java"){
				$this->nodes = $link;
				$this->external = true;
			}
			else{
				$link = $this->sanitize($link);

				$temp = explode('.', $link);
				$link = $temp[0];
				
				$temp = substr($link, 0, 1);
				if($temp == ':' || $temp == '/')
					$link = substr($link, 1);
				
				$this->nodes = preg_split("/[:\/]/", $link);

				$this->checkForBlankNodes();

				if(count($this->nodes) > 1 && $this->getLastNode() == 'index')
					$this->removeLastNode();
			}
		}

		private function checkForBlankNodes(){
			if(!$this->external){
				for($i = 0; $i < count($this->nodes); $i++)
					if(strlen($this->nodes[$i]) == 0)
						unset($this->nodes[$i]);

				array_merge($this->nodes);
			}
		}

		function getNodeCount(){
			return ($this->external ? FALSE : count($this->nodes));
		}

		function getNode($i){
			if($this->external || $i < 0 || $i > count($this->nodes) - 1)
				return FALSE;
			else
				return $this->nodes[$i];
		}

		function getAllButLastNode($delimeter = ':'){
			if($this->external)
				return FALSE;
			else{
				$node = $this->nodes;
				unset($node[count($node) - 1]);
				array_merge($node);
				return implode($delimeter, $node);
			}
		}

		function getLastNode(){
			if($this->external)
				return FALSE;
			else{
				if(count($this->nodes) == 0)
					$this->add('index');

				return $this->nodes[count($this->nodes) - 1];
			}
		}

		function getStatic(){
			if($this->external)
				return $this->nodes;
			else{
				if(count($this->nodes) == 0)
					$this->add('index');

				return "$this->staticPath" . implode('/', $this->nodes) . '.' . $GLOBALS['config']->repository_suffix;
			}
		}

		function getDynamic(){
			if($this->external)
				return $this->nodes;
			else{
				if(count($this->nodes) == 0)
					$this->add('index');

				return "$this->dynamicPath" . $this->getId();
			}
		}

		function getId($delimeter = ':'){
			return ($this->external ? FALSE : implode($delimeter, $this->nodes));
		}

		/**
		 *	function	generateMenu
		 *			Generates the navigation menu specified in the config file
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$links	an array of title => id pairs to convert to an HTML menu
		 *
		 *	@return		the HTML menu
		 */
		function generateMenu($links){
			$currentId = $this->getId();
			$menu = '';
			
			if(is_array($links))
				foreach($links as $title => $l){
					$current = ($l == $currentId ? " class = \"current\"" : "");

					$link = new Link($l);
					$menu .= "\r\n\t<li><a href = \"$link\"$current>$title</a></li>";
				}

			return "<ul>$menu\r\n</ul>";
		}
		
		function toArray(){
			return ($this->external ? FALSE : $this->nodes);
		}

		function __toString(){
			return ($this->external ? $this->nodes : ($GLOBALS['config']->site_staticLinks ? $this->getStatic() : $this->getDynamic()));
		}
		
		/**
		 *	function	sanitize
		 *	PRIVATE		Sanitizes the id to make it file safe
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$id	the id to sanitize
		 *
		 *	@return		the sanitized id
		 */
		private function sanitize($id){
			if(($pos = strpos($id, ".{$GLOBALS['config']->repository_suffix}")) != FALSE)
				$id = substr($id, 0, $pos);

			$strip = array(	'~', '`', '!', '@', '#', '$', '%', '^', '&', 
					'*', '(', ')', '_', '=', '+', '[', '{', ']',
					'}', '|', ';', ',', '<', '.', '>', '?',
					'\\', '\'', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8211;', '&#8212;');

			$id = str_replace($strip, '', $id);

			return $id;
		}
	}
?>

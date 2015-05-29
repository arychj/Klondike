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
	 *	core.utils.PageObjectModel
	 *	Contains all data required to build the page
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.09.22
	 *		Moved template variable into POM
	 *	2010.03.15
	 *		Implemented prepend function
	 *	2010.03.05
	 *		Created (Deprecates Page class)
	 */
	 
	PackageManager::import('core.utils.Struct');
	
	class PageObjectModel{
		private	$elements;
		public $template;
		
		/**
		 *	function	__construct
		 *	CONSTRUCTOR
		 *
		 *	@author		Erik J. Olson
		 */
		function __construct(){
			$this->init(array(	'headers',
					  	'javascript',
						'stylesheets',
						'page',
						'vars'
					));
			
			$this->elements['page']->init(array(	'auth',
								'menu',
								'date',
								'title',
								'content',
								'javascript',
								'stylesheets',
								'banner_path',
								'banner_title',
								'onload',
								'onunload'
						));
		}
		
		/**
		 *	function	init
		 *	PRIVATE		Initializes the elements stuctures
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		elements	an array of the elements to create
		 */
		private function init($elements){
			foreach($elements as $key)
				$this->elements[$key] = new Struct();
		}
		
		/**
		 *	function	init
		 *				Initializes the members of an element struct
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element to initialize
		 *	@param		members		an array of the elements to create
		 *
		 *	@return		true:	if success
		 *				FALSE:	if fail
		 */
		function initElement($key, $members){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else{
				$element->init($members);
				return true;
			}
		}
		
		/**
		 *	function	getElement
		 *				Get an element struct
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *
		 *	@return		the element struct:	if valid
		 *				FALSE:				if fail
		 */
		function getElement($ref){
			if(array_key_exists($ref, $this->elements))
				return $this->elements[$ref];
			else
				return FALSE;
		}
		
		/**
		 *	function	set
		 *				Set a struct value
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		ref		the reference in the stuct to set
		 *	@param		val		the value to set it to
		 *
		 *	@return		true:	if success
		 *				FALSE:	if fail
		 */
		function set($key, $ref, $val){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else{
				$element->set($ref, $val);
				return true;
			}
		}
		
		/**
		 *	function	append
		 *				Append to a struct value
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		ref		the reference in the stuct to append to
		 *	@param		val		the value to append
		 *
		 *	@return		true:	if success
		 *				FALSE:	if fail
		 */
		function append($key, $ref, $val){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else{
				$element->append($ref, $val);
				return true;
			}
		}
		
		/**
		 *	function	prepend
		 *				Prepend to a struct value
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		ref		the reference in the stuct to append to
		 *	@param		val		the value to prepend
		 *
		 *	@return		true:	if success
		 *				FALSE:	if fail
		 */
		function prepend($key, $ref, $val){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else{
				$element->prepend($ref, $val);
				return true;
			}
		}
		
		/**
		 *	function	push
		 *				Push value
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		val		the value to push
		 */
		function push($key, $val){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else{
				$element->push($val);
				return true;
			}
		}
		
		/**
		 *	function	get
		 *				Get a struct value
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		ref		the reference in the stuct to set
		 *
		 *	@return		the value:	if valid
		 				FALSE:		if element DNE
		 */
		function get($key, $ref){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else
				return $element->get($ref);
		}
		
		/**
		 *	function	is_set
		 *				Checks to see if a stuct reference is set
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		ref		the reference in the stuct to set
		 *
		 *	@return		true:	if it is set
		 *				FALSE:	if not or if struct DNE
		 */
		function is_set($key, $ref){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else
				return $element->is_set($ref);
		}
		
		/**
		 *	function	setIfExists
		 *				Sets a a struct refenence if the value to
		 *				set it to exists
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *	@param		ref		the refencce in the stuct to set
		 *	@param		val		the vale to set it to
		 *
		 *	@return		true:	if success
		 *				FALSE:	if fail
		 */
		function setIfExists($key, $ref, $val){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else{
				if(strlen($val) > 0)
					$element->set($ref, $val);
					
				return true;
			}
		}
		
		/**
		 *	function	prep
		 *				Preps the PageObjectModel for output
		 *
		 *	@author		Erik J. Olson
		 */
		function prep(){
			$stylesheets	= $this->getElement('stylesheets');
			$javascript	= $this->getElement('javascript');
			$page		= $this->getElement('page');
			
			$page->set('stylesheets', $stylesheets->toString("<link rel = \"stylesheet\" href = \"", "", "\"/>\r\n", false));
			$page->set('javascript', $javascript->toString("<script type = \"text/javascript\" src = \"", "", "\"></script>\r\n", false));
		}
		
		/**
		 *	function	elementToArray
		 *				Generates an array representation of an 
		 *				element struct
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		key		the key of the element
		 *
		 *	@return		the array representaion
		 */
		function elementToArray($key){
			if(($element = $this->getElement($key)) === FALSE)
				return FALSE;
			else
				return $element->toArray();
		}
		
		/**
		 *	function	toArray
		 *				Generates an array representation of this PageObjectModel
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@return		the array representation
		 */
		function toArray(){
			return $this->elements;
		}
		
		/**
		 *	function	__toString
		 *				Generates a string representation of this PageObjectModel
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@return		the string represention
		 */
		public function __toString(){
			return str_replace("\n", "<br/>\n", print_r($this->elements, true));
		}
	}
?>

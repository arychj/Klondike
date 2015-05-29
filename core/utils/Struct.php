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
	 *	core.utils.Struct
	 *	Basic class for handling large amounts of referenced data
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.09.27
	 *		Fixed bug where pushed values are returned in reversed order
	 *	2010.03.04
	 *		Created
	 */
	 
	class Struct{
		private $elements;
		
		function __construct(){
			$this->elements = array();
		}
		
		function init($refs){
			foreach($refs as $ref)
				$this->elements[$ref] = '';
		}
		
		function set($ref, $val){
			$this->elements[$ref] = $val;
		}
		
		function setIfSet($ref, $val){
			if(strlen($val) > 0)
				$this->elements[$ref] = $val;
		}
		
		function append($ref, $val){
			$this->elements[$ref] .= "\n$val";
		}
		
		function prepend($ref, $val){
			$this->elements[$ref] = "$val\n" . $this->elements[$ref];
		}
		
		function get($ref){
			return $this->elements[$ref];
		}
		
		function push($val){
			$this->elements[] = $val;
		}
		
		function is_set($ref){
			if(isset($this->elements[$ref]) && strlen($this->elements[$ref]) > 0)
				return TRUE;
			else
				return FALSE;
		}
		
		function toArray(){
			return $this->elements;
		}
		
		function toString($prefix = '', $midfix = '', $postfix = '', $showRef = true){
			$str = "";
			
			$e = array_reverse($this->elements, true);

			foreach($e as $ref => $val)
				$str = "$prefix" . ($showRef ? "$ref" : "") . "$midfix$val$postfix$str";
			
			return $str;
		}

		function __toString(){
			$this->toString();
		}
	}
?>

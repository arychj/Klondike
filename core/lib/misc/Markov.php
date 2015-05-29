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
	 *	.core.lib.misc.Markov
	 *	Implements Markov Chains (http://en.wikipedia.org/wiki/Markov_chain)
	 *
	 *	@author Erik J. Olson
	 *
	 *	@changelog
	 *	2009.10.16
	 *		Created
	 */
	 
	class Markov_NextStep{
		var $words;
		
		function next_step(){
			$this->words = array();
		}
		
		function add($word){
			$this->words[] = $word;
		}
		
		function get_random(){
			return $this->words[rand(0, sizeof($this->words) - 1)]; 
		}
	}

	class Markov{	
		var $tuples;
		
		function Markov(){
			$this->tuples = array();
		}
		
		function load($filename){
			$first = "<START>";
			$second = "<START>";
			
			$file = fopen($filename, 'r');
			if($file != 0){
				while(!feof($file)){
					if(($line = fgets($file)) != FALSE){
						$words = split(" ", $line);
						foreach($words as $word){
							$key = $this->strip($first . $second);
							
							if(!isset($this->tuples[$key]))
								$this->tuples[$key] = new Markov_NextStep();
								
							$this->tuples[$key]->add(trim($word));
							
							$first = $second;
							$second = $word;
						}
					}
				}
				
				fclose($file);
			}
			else
				die("unable to open file");
		}
		
		function generate($length){
			$tell = "";
				
			$first = "<START>";
			$second = "<START>";
			
			for($i = 0; $i < $length; $i++){
				$key = $this->strip($first . $second);
				
				if(($next = $this->tuples[$key]) == NULL)
					break;
					
				$word = $next->get_random();
				$tell .= "$word ";
				
				$first = $second;
				$second = $word;
			}
			
			return $tell;
		}
		
		function strip($key){
			return str_replace(array('.', ',', ':'), array(), trim($key));
		}
	}
?>

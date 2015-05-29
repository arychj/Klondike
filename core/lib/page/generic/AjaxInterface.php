<?php
	
	class AjaxInterface{
		var	$pom,
			$name,
			$data;
			
		function __construct(&$pom, $name, $data){
			$this->pom = $pom;
			$this->name = $name;
			$this->data = $data;

			$this->go();
		}

		private function go(){
			$xml = new SimpleXMLElement("<klondike></klondike>");

			if($this->data != NULL){
				foreach($this->data as $data){
					$obj = $xml->addChild("$this->name");
					foreach($data as $var => $val){
						$this->parseSpecialTuple($var, $val);
						$obj->addChild($var, htmlspecialchars($val));
					}
				}
			}
			
			$this->pom->template = 'blank';
			$this->pom->set('page', 'content', @$xml->asXML());
			$this->pom->set('headers', 'content-type', 'text/xml');
		}

		private function parseSpecialTuple(&$var, &$val){
			if(substr($var, -4) == 'Date')
				$val = date($GLOBALS['config']->date_format, $val);
			elseif($var == 'password')
				$val = '********';
		}
	}
?>

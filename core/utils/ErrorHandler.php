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
	 *	core.utils.ErrorHandler
	 *	Custom error handling to provide 'pretty' failure for production
	 *	code and for detailed errors when in debug.
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.03.05
	 *		Created
	 */
	
	require_once('klondike/core/vars/errors.inc.php');
	
	class ErrorHandler{
		/**
		 *	function	errorHandler
		 *	STATIC		Custom error handler for Klondike
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$type	
		 *	@param		$msg	
		 *	@param		$file	
		 *	@param		$line	
		 */
		 static function handler($type, $msg, $file, $line, $context){
			$config = &$GLOBALS['config'];
			
			if($config->debug){
				ob_end_flush();
				$errorType = array(	E_ERROR				=> 'ERROR',
									E_WARNING			=> 'WARNING',
									E_PARSE				=> 'PARSING ERROR',
									E_NOTICE			=> 'NOTICE',
									E_CORE_ERROR		=> 'CORE ERROR',
									E_CORE_WARNING		=> 'CORE WARNING',
									E_COMPILE_ERROR		=> 'COMPILE ERROR',
									E_COMPILE_WARNING	=> 'COMPILE WARNING',
									E_USER_ERROR		=> 'USER ERROR',
									E_USER_WARNING		=> 'USER WARNING',
									E_USER_NOTICE		=> 'USER NOTICE',
									E_STRICT			=> 'STRICT NOTICE',
									E_RECOVERABLE_ERROR	=> 'RECOVERABLE ERROR'
								);
				
				$error = "<div style = 'font-family: \"Courier New\", Courier, monospace;'>";
				$error .= "Klondike Framework / CMS<br/>\n";
				$error .= "------------------------<br/>\n";
				$error .= "Unhandled Error...<br/>\n";
				$error .= "<br/>\n";
				$error .= "<div style = 'color: #FF0000;'>";
				$error .= "&lt;$errorType[$type]&gt; in $file($line)<br/>\n";
				$error .= "$msg<br/>\n";
				$error .= "</div><br/>\n";
				$error .=  "Context:<br/>\n" . ErrorHandler::print_rRedact($context, "Config");
				$error .= "<br/>\n";
				$error .= "</div>";
			}
			else{
				ob_end_clean();
				$error = "Oops...";
				//logError($config, $type, $msg, $file, $line, $context);
			}
			
			echo($error);
			exit();
		}
		
		static function print_rRedact($array, $redaction, $level = 0){
			$printr = "";
			
			$nest = "";
			for($i = 0; $i <= $level; $i++)
				$nest .= "&nbsp;&nbsp;&nbsp;";
			
			foreach($array as $key => $val){
				if($key != "searchspace"){
					$class = get_class($val);
					
					$printr .= "$nest [$key] => ";
					
					if(strpos($class, $redaction) !== FALSE){
						$printr .= "REDACTED<br/>\n";
					}
					elseif(is_array($val)){
						$printr .= "Array(<br/>\n";
						$printr .= ErrorHandler::print_rRedact($val, $redaction, $level + 1);
						$printr .= ")<br/>\n";
					}
					elseif(is_object($val)){
						$printr .= "$class(";
						$printr .= (method_exists($val, "__toString") ? strval($val) : "");
						$printr .= ")<br/>\n";
					}
					else
						$printr .= "$val<br/>\n";
				}
			}
			
			return $printr;
		}
		
		static function logError($config, $type, $msg, $file, $line, $context){
			$errorString = "";
			$errorString .= "Date: " . date($config->date_format, mktime()) . "\n";
			$errorString .= "Error type: $type\n";
			$errorString .= "Error message: $msg\n";
			$errorString .= "Script: $file($line)\n";
			$errorString .= "Host: $HTTP_HOST\n";
			$errorString .= "Client: $HTTP_USER_AGENT\n";
			$errorString .= "Client IP: $REMOTE_ADDR\n";
			$errorString .= "Request URI: $REQUEST_URI\n\n";
			
			error_log($errorString, 3, $config->error_logfile);
			//email();
		}
	}
?>

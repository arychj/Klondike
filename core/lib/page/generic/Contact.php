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
	 *	core.lib.page.generic.Contact
	 *	<class_description>
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.05.21
	 *		Created
	 */
	
	PackageManager::import('core.utils.Link');

	class Contact{
		var $messages	= array(
					'success'	=> "Sucess",
					'fail'		=> "Fail"
					);
		/**
		 *	function	__construct
		 *	CONSTRUCTOR
		 *
		 *	@author		Erik J. Olson
		 */
		function __construct(){
			$this->setFailMessage("Mail failed to send, the administrator has been informed.<br/>I appologize for the inconvenience, please try again later.");
			$this->setSucessMessage("Thanks. I'll try to get back to you as soon as I can.<br/>To return to the index, click <a href = \"" . (new Link(main:index) . "\">HERE</a>.");
			$k_pom->initElement('vars', array('name', 'email', 'subject', 'body'));
		}
		
		/**
		 *	function	process
		 *			<f_description>
		 *
		 *	@author		Erik J. Olson
		 */
		private function process(){	
			if(isset($_POST['submitted'])){
				$k_pom->prepend('page', 'content', '<div class = "alert">{message}</div>');

				if(($message = $this->isValidContactData($_POST)) == NULL){
					if(mail($k_config->site_admin, "{$GLOBALS[config]->site_title} Contact: $_POST[subject]", "From: $_POST[name] <$_POST[email]>\r\n\r\n$_POST[body]", "From: $_POST[name] <$_POST[email]>\r\n")){
						$k_pom->set('page', 'content', '<div class = "alert">{message}</div>');
						$k_pom->set('vars', 'message', $this->messages['success']);
					}
					else{
						$k_pom->set('vars', 'message', $this->messages['fail']);
					}
				}
				else{
					$k_pom->set('vars', 'name',    $_POST['name']);
					$k_pom->set('vars', 'email',   $_POST['email']);
					$k_pom->set('vars', 'subject', $_POST['subject']);
					$k_pom->set('vars', 'body',    $_POST['body']);
					$k_pom->set('vars', 'message', $message);
				}
			}
			elseif(isset($_GET['subject']) || isset($_GET['body'])){
				$k_pom->set('vars', 'subject', $_GET['subject']);
				$k_pom->set('vars', 'body',    $_GET['body']);
			}
		}

		function isValidContactData(&$form){
			if(strlen($form['name']) == 0)
				return "Please tell me your name so I know who I'm talking to.<br/>(The name field may not be left blank)";
			elseif(strlen($form['email']) == 0)
				return "I need your email address if you expect me to reply to you...<br/>(The Email field may not be left blank)";
			elseif(!preg_match("^[a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})$^", $form['email']))
				return "You tried to trick me... You gave me a bad email address.<br/>(Invalid address in email field)";
			elseif(strlen($form['subject']) == 0)
				return "It'd be nice if you told me why you were contacting me...<br/>(The subject field may not be left blank)";
			elseif(strlen($form['body']) == 0)
				return "Why are you contacting me if you're not going to say anything?<br/>(The comments field may not be left empty)";

			//escape form
			$form['name'] = mysql_escape_string($form['name']);
			$form['subject'] = mysql_escape_string($form['subject']);
			$form['body'] = mysql_escape_string($form['body']);

			return NULL;
		}

		function setSuccessMessage($message){
			$this->messages['success'] = $message;
		}

		function setFailMessage($message){
			$this->messages['fail'] = $message;
		}
	}
?>

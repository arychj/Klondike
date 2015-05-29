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
	 *	core.utils.Controller
	 *	Controls the entire functionality of the Klondike Framework, constructs the page,
	 *	loads the template, etc, etc.
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2011.08.03
	 *		Removed default site title prepending
	 *	2010.12.08
	 *		Corrected bug in header generation
	 *	2010.11.05
	 *		Changed order of template / site custom code processing
	 *		Corrected glitch which was causing the 404 title to not be displayed 
	 *			($k_id was not being passed by reference to 'fetchFile()')
	 *	2010.09.30
	 *		Added functionality to override directory templates using $k_directoryTemplate
	 *		changed priority of directory.kldk.php to execute first
	 *	2010.09.29
	 *		Moved getNav() functionality to core.utils.Link
	 *		Modified generatePOM to reflect getNav() move
	 *	2010.09.27
	 *		Added functionality for directory code
	 *	2010.09.22
	 *		Added functionality to programatically override page template
	 *	2010.09.21
	 *		Corrected issue in fetchStylesheets() causing directories to be generated incorrectly
	 *	2010.08.27
	 *		Corrected page>template_path issue
	 *	2010.06.28
	 *		Changed the way files are searched for by starting the search at $config->server_path
	 *		Corrected getStylesheet logic error to allow for root index stylesheets
	 *		Cleaned up depracated / replicated functionality
	 *	2010.06.26
	 *		Fixed authentication prompt issue
	 *	2010.06.06
	 *		Implemented directory templating
	 *	2010.06.04
	 *		Moved 'applytemplate()' to 'applySitetemplate()'
	 *		Re-implemented $id as a Link
	 *	2010.05.29
	 *		Changes to nav to allow javascript
	 *	2010.05.26
	 *		Improved id sanitation
	 *		Corrected stylesheet searcher
	 *		Implemented 'template_path' page var
	 *	2010.05.20
	 *		Implemented PackageManager
	 *	2010.05.19
	 *		Fixed 404 id not found issue to allow for php 404's
	 *	2010.05.18
	 *		Implemented custom template code
	 *	2010.05.16
	 *		Modified function flow to move $config to $GLOBALS instead of being passed
	 *			by reference
	 *		Implemented 'Link' class to support 'static' linking
	 *		Implemented 'static' linking.
	 *	2010.02.15
	 *		Implemented auto-templating
	 *		Deprecats 'loadTemplate()' and '/templates/pages' dir
	 *	2010.03.10
	 *		Cleaned out all site-specific code, moved to user editable
	 *			$config->site_custom_location
	 *	2010.03.05
	 *		Implemented 'PageObjectModel' class, replaces 'Page' class
	 *	2010.03.04
	 *		Implemented custom error handling
	 *		Changed variable scope to private
	 *	2010.03.02
	 *		Mover header information into Page class
	 *	2010.02.26
	 *		Implemented output buffer processing and compression
	 *		Re-implemented page generation flow
	 *	2010.02.22
	 *		Fixed template carry-on issue where a non-default template is specified
	 *			by the user but is not maintained through subsequent page hops
	 *	2010.02.09
	 *		Added menu support for external links
	 *	2009.12.04
	 *		Added config functionality for global javascript
	 *	2009.09.18
	 *		Corrected divide by zero error in getRandomBanner()
	 *	2009.09.03
	 *		Moved $pageVars into $page. Now accessable via $page->var_***
	 *		Defined global convention $k_*** to mean the current whatever
	 *	2009.09.02
	 *		Allow 'directory/index.ajax.html' to be processed for id = 'directory'
	 *		Separated the file fetching from the 'getData()' function
	 *	2009.08.26
	 *		Implemented Page class
	 *		Removed prepPage
	 *	2008.11.12
	 *		Added comment header
	 */
	
	PackageManager::import('core.vars.Error');
	PackageManager::import('core.utils.ErrorHandler');
	PackageManager::import('core.utils.PageObjectModel');
	PackageManager::import('core.utils.Link');
	PackageManager::import('core.lib.system.Filesystem');
	
	class Controller{
		var	$pom;
		
		/**
		 *	function	Controller
		 *	CONSTRUCTOR
		 *
		 *	@author		Erik J. Olson
		 *	@param		$database	the database object to use
		 *	@oaram		$tables		an array of the tables used by the controller
		 */
		function __construct($config, $database = false){
			ob_start(); //start output buffering (completed in 'sendPage')
			
			if(isset($config->error_enhanced) && $config->error_enhanced){
				error_reporting($config->error_bitmask);
				set_error_handler("ErrorHandler::handler");
			}
			
			$this->pom	= new PageObjectModel();

			$GLOBALS['config']	= $config;
			$GLOBALS['database']	= $database;
		}
		
		/**
		 *	function	generatePOM
		 *			The main fucntion which constructs the page
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$id		the id of the page to get
		 *	@param		$pageRequest	OPTIONAL
		 */
		function generatePOM($id, $pageRequest = true){
			$config	= &$GLOBALS['config'];
			$k_pom	= $this->pom;
			$k_id = new Link($id);

			foreach($config->javascript as $script)
				$k_pom->push('javascript', "$config->javascript_path/$script");
		
			if(isset($_GET['template'])){
				$_SESSION['kldk_template'] = $_GET['template'];
				$k_pom->template = $_GET['template'];
			}
			elseif(isset($_SESSION['kldk_template']))
				$k_pom->template = $_SESSION['kldk_template'];
			else
				$k_pom->template = $config->template_current;

			//build the pom
			$this->fetchFile($k_id, $pageRequest, $k_pom);
			$this->fetchStylesheets($k_id);

			$k_pom->set('page',	'auth'			, (isset($_SESSION['k_userId']) ? str_replace('{name_last}', $_SESSION['k_userNameLast'], str_replace('{name_first}', $_SESSION['k_userNameFirst'], $config->auth_logoutPrompt)) : $config->auth_loginPrompt));
			$k_pom->set('page',	'menu'			, $k_id->generateMenu($config->nav_list));
			$k_pom->set('page',	'date'			, date($config->date_format));
			$k_pom->set('page',	'content'		, $this->compile($k_pom->get('page', 'content'), $k_pom->elementToArray('vars')));
			$k_pom->set('page',	'template_path'		, ($config->site_path == '/' ? '' : "{$config->site_path}/") . "$config->template_dir/$k_pom->template");
			$k_pom->set('page',	'javascript_path'	, "$config->javascript_path");
			$k_pom->set('page',	'onload'		, '');
			$k_pom->set('page',	'onunload'		, '');

			if(file_exists("$config->server_path$config->template_dir/$k_pom->template/$config->template_customCode"))
				include_once("$config->server_path$config->template_dir/$k_pom->template/$config->template_customCode");
			if(file_exists("$config->server_path$config->site_customCode"))
				include_once("$config->server_path$config->site_customCode");
		}

		/**
		 *	function	fetchFile
		 *	PRIVATE		Determines which file to fetch from disk
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$k_id			the id Link to fetch
		 *	@param		$k_pageRequest		is this a page request (ajax)
		 *	@param		$k_page			the page object
		 */
		private function fetchFile(&$k_id, $k_pageRequest, &$k_pom){
			$id = $k_id->getId();
			$k_directoryTemplate = NULL;

			$k_config = &$GLOBALS['config'];
			$k_loc = $k_id->getId('/');
				
			$abl = ($k_id->getNodeCount() == 1 || is_dir("$k_config->server_path$k_config->repository_dir/" . $k_id->getId('/')) ? $k_id->getId('/') : $k_id->getAllButLastNode('/'));

			//if a directory code exists, run it
			if(file_exists(($path = "$k_config->server_path$k_config->repository_dir/$abl/directory.$k_config->repository_suffix.php"))){
				include_once($path);
			}

			//if a directory template has not been overridden, and exists
			if($k_directoryTemplate == NULL && file_exists(($path = "$k_config->server_path$k_config->repository_dir/$abl/directory.$k_config->repository_suffix.tpl")))
				$k_directoryTemplate = $path;

			//if a directory template is set, apply it
			if($k_directoryTemplate != NULL)
				$k_pom->set('page', 'content', file_get_contents($k_directoryTemplate));

			$k_found	= false;
			$searchspace	= array("", "/index", ".$k_config->repository_hidden", "$k_config->repository_hidden/index");
			foreach($searchspace as $search){
				if(file_exists(($path = "$k_config->server_path$k_config->repository_dir/$k_loc$search.$k_config->repository_suffix.php"))){ //if the dynamic page exists, use it
					if(file_exists(($tpl = "$k_config->server_path$k_config->repository_dir/$k_loc$search.$k_config->repository_suffix.tpl"))) //apply template if exists
						if(isset($k_directoryTemplate))
							$k_pom->set('vars', 'content', file_get_contents($tpl));
						else
							$k_pom->set('page', 'content', file_get_contents($tpl));
					
					include_once($path);
					$k_found = true;
				}
				elseif(file_exists(($path = "$k_config->server_path$k_config->repository_dir/$k_loc$search.$k_config->repository_suffix.html"))){ //look for the static page
					$k_found = true;

					if(isset($k_directoryTemplate))
						$k_pom->set('vars', 'content', file_get_contents($path));
					else
						$k_pom->set('page', 'content', file_get_contents($path));
				}
			}
			if(!$k_found){ //if a static or dynamic page is not foud corosponding to the id, get the 404
				if($id != 'system:404') //prevents infinite loop if 404 page DNE
					$this->fetchFile(($k_id = new Link('system:404')), $k_pageRequest, $k_pom);
				else
					die("404 page does not exist.");
			}
		}
		
		/*
		 *	function	fetchStylesheets
		 *	PRIVATE		Gets the stylesheets for the current id
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$id	the current id LInk
		 */
		private function fetchStylesheets($id){
			$config = &$GLOBALS['config'];
			$stylesheet = "";
				
			$name = $id->getLastNode();
			$dir = $id->getAllButLastNode('/');
			$dir = ($dir == "" ? "" : "$dir/");
				
			$stylesheets = Filesystem::searchDir("$config->server_path$config->template_dir/{$this->pom->template}/styles/$dir", "$name*.css");
			foreach($stylesheets as $sheet)
				$this->pom->push('stylesheets', "$config->template_dir/{$this->pom->template}/styles/$dir$sheet");
		}
		
		/**
		 *	function	applyPageTemplate
		 *			Applies a page template
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$id	the id Link of the template to apply
		 *	@param		$append	'a': append the template
		 *				'p': prepend the template
		 *				'r': replace with template
		 */
		function applyPageTemplate($id, $append = 'r'){
			$pom = $this->pom;
			$loc = $id->getId('/');

			if(isset($pom)){
				if(($template = file_get_contents("{$GLOBALS[config]->server_path}{$GLOBALS[config]->repository_dir}/$loc.{$GLOBALS[config]->repository_suffix}.tpl")) != FALSE)
					if($append == 'a')
						$pom->set('page', 'content', $pom->get('page', 'content') . $template);
					elseif($append == 'p')
						$pom->set('page', 'content', $template . $pom->get('page', 'content'));
					else
						$pom->set('page', 'content', $template);
				else
					$pom->set('page', 'content', "ERROR: Template load failure.<br/>");
			}
			else{
				//error
			}
		}

		/**
		 *	function	applySiteTemplate
		 *	PRIVATE		Applys the appropriate template to the page and retuns the output buffer
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@return		the output buffer
		 */
		private function applySiteTemplate(){
			$config = &$GLOBALS['config'];
			
			if(file_exists("$config->server_path$config->template_dir/{$this->pom->template}/template.tpl"))
				return $this->compile(file_get_contents("$config->server_path$config->template_dir/{$this->pom->template}/template.tpl"), $this->pom->elementToArray('page'));
			else
				return trigger_error("Could not load template <strong>'{$this->pom->template}'</strong>. Template does not exist.<br/>", E_USER_ERROR);
		}
		
		/**
		 *	function	processBuffer
		 *	PRIVATE		Compresses the output buffer
		 *	
		 *	@author		Erik J. Olson
		 *
		 *	@param		$buffer		the buffer to compress
		 */
		private function processBuffer($buffer){
			$config = &$GLOBALS['config'];
			
			//if conpress output and the browser accepts gzip
			if($config->compress_buffer && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE){
				$this->pom->set('header', 'Content-Encoding', 'gzip');
				$buffer = gzencode($buffer);
			}
			
			return $buffer;
		}
		
		/**
		 *	function	sendPage
		 *			Sends the buffer to the client
		 *
		 *	@author		Erik J. Olson
		 */
		function sendPage(){
			//$this->pom->append('page', 'stylesheets', strval($this->pom->getElement('stylesheets')));
			$this->pom->prep();
			$buffer = $this->processBuffer($this->applySiteTemplate());
			
			$headers = $this->pom->getElement('headers')->toArray();
			foreach($headers as $var => $val)
				header("$var: $val");
			
			echo($buffer);
			ob_end_flush(); //end buffering (started in constuctor)
		}
		
		/**
		 *	function	compile
		 *	PRIVATE		Compiles an associative-array onto a template
		 * 
		 *	@author		Erik J. Olson
		 *
		 *	@param		$template	the HTML tpl file
		 *	@param		$vars		the variables to parse into the template
		 *
		 *	@return		a formatted HTML page
		 */
		private function compile($template, $vars){
			if(count($vars) > 0)
				foreach($vars as $key => $value)
					$template = str_replace('{' . $key .'}', trim($value), $template);
			
			return $template;
		}
	}
?>

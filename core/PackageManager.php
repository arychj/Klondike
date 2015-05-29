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
	 *	core.utils.PackageManager
	 *	Manages loading and unloading of Klondike Packages / Classes
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.05.20
	 *		Created
	 */

	class PackageManager{
		private	$searchpath;
		public $repository;

		/**
		 *	function	__construct
		 *	PRIVATE		Constructor
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$searchpath		the path to use to search for classes
		 */
		private function __construct($searchpath = null){
			$this->repository = array();
			$this->searchpath = array(	dirname(__FILE__),
							dirname($_SERVER['SCRIPT_FILENAME']),
							$_SERVER['DOCUMENT_ROOT']
						);

			if(!is_null($searchpath))
				$this->__addsearchpath($searchpath);
		}
		
		/**
		 *	function	__import
		 *	PRIVATE		Imports a class
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$class		the class to import
		 *
		 *	@return		true if success, false otherwise
		 */
		private function __import($class){
			$success = false;
			$searched = array();
			foreach($this->searchpath as $path)
				if($this->__search($class, $searched[] = (($path == "" || $path == "." ? "$path" : "$path")))){
					$success = true;
					break;
				}

			if($success)
				return $success;
			else{
				$back = debug_backtrace();
				return trigger_error("Could not find Package <strong>'$class'</strong><br/>Imported from <strong>'{$back[1]['file']}:{$back[1]['line']}'</strong><br/>&nbsp;&nbsp;&nbsp;Searched for " . str_replace('.', '/', $class) . ".php<br/>&nbsp;&nbsp;&nbsp;Using searchpath: " . implode($this->searchpath, '; ') . ".<br/>", E_USER_WARNING);
			}
		}

		/**
		 *	function	search
		 *			Searches for the class to import into the repository
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$import		the class to import
		 *	@return		true if success, false otherwise
		 */
		private function __search($import, $path = '') {
			//seperate import into a package and a class
			$lastDot = strrpos($import, '.');
			$class = $lastDot ? substr($import, $lastDot + 1) : $import;
			$package = substr($import, 0, $lastDot);

			if(isset($this->repository[$class]) || isset($this->repository["$package.*"])) //has already been imported
				return true;

			//construct folder path
			$folder = $path . ($package ? str_replace('.', '/', $package) : '');
			$file = "$folder/$class.php";

			if(!file_exists($folder))
				return false;
			elseif(($class != '*') && (!file_exists($file)))
				return false;

			if($class != '*'){
				$this->repository[$class] = $file;
			}
			else{
				$this->repository["$package.*"] = 1; //note that * has been immported

				$dir = opendir($folder);
				while(($file = readdir($dir)) !== false){
					if(strrpos($file, '.php')){
						$class = str_replace('.php', '', $file);
						$this->repository[$class] = "$folder/$file";
					}
				}
			}

			return true;
		}

		/**
		 *	function	__addSearchpath
		 *	PRIVATE		Adds a search path
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$path		the path to add
		 *	@param		$replace	replace all searchpaths if true
		 */
		private function __addSearchpath($path, $replace = false){
			if($replace)
				$this->searchpath = array();
			
			$this->searchpath[] = "$path";
		}

		/**
		 *	function	__importedPackages
		 *	PRIVATE		Gets a list of the currently imported packages
		 *
		 *	@author		Erik J. Olson
		 */
		private function __importedPackages(){
			return implode($this->repository, ',');
		}

		/**
		 *	function	init
		 *	PRIVATE		initializes the singlton class
		 *
		 *	@author		Erik J. Olson
		 */
		private static function init(){
			if(!isset($_SERVER['k_package_manager']) || empty($_SERVER['k_package_manager']))
				$_SERVER['k_package_manager'] = new PackageManager();
		}

		/**
		 *	function	__destruct
		 *	DESRUCTOR	Destructor
		 *
		 *	@author		Erik J. Olson
		 */
		public static function __descruct(){
			unset($_SERVER['k_package_manager']);
		}

		/**
		 *	function	import
		 *	STATIC		Import a class
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$class	the class to import
		 */
		public static function import($class){
			PackageManager::init();
			return $_SERVER['k_package_manager']->__import($class);
		}

		/**
		 *	function	addSearchpath
		 *	STATIC		Adds a searchpath
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$path		the path to import
		 *	@param		$replace	replace all searchpaths if true
		 */
		public static function addSearchpath($path, $replace = false){
			PackageManager::init();
			return $_SERVER['k_package_manager']->__addSearchpath($path, $replace);
		}
	}

	/**
	 *	class	KPM
	 *		KPM is a shortcut class for PackageManager
	 *
	 *	@author Erik J. Olson
	 */
	if(!class_exists("KPM")){
		class KPM extends PackageManager{}
	}
	else{
		trigger_error("KPM::*() PackageManager shortcuts not available, KPM already defined.", E_USER_NOTICE);
	}

	/**
	 *	function	__autoload
	 *	OVERRIDE	Overrides the __autoload function to dynamically
	 *			load a class.
	 *
	 *	@author		Erik J. olson
	 *
	 *	@param		$class		the class to load
	 */
	if(!function_exists("__autoload")){
		function  __autoload($class){
			if (isset($_SERVER['k_package_manager']->repository[$class])) {
				require_once($_SERVER['k_package_manager']->repository[$class]);
			}
		}
	}
	else{
		trigger_error("__autoload in already defined.", E_USER_NOTICE);
	}
?>

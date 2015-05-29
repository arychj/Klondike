<?php
	/**
	 *	class		PicasaWebAlbum
	 *	@author		Erik J. Olson
	 *
	 *	@pomElement	title		the web album ititle
	 *	@pomElement	album		the web album
	 *	@pomElement	pagiation	the page navigation
	 */
	class PicasaWebGallery{
		var	$templates,
			$albums,
			$userid,
			$pom,
			$pid,
			$tgalleryTitle;

		var $feeds = array(	'front' => 'https://picasaweb.google.com/data/feed/base/user/{userid}?alt=rss&kind=album&hl=en_US',
					'album' => 'https://picasaweb.google.com/data/feed/base/user/{userid}/albumid/{albumid}?alt=rss&kind=photo&hl=en_US'
				);

		var $DEFAULT_PAGE_SIZE = 16;
		var $DEFAULT_PAGIATION_WINDOW = 3;
		var $DEFAULT_GALLERY_TITLE = 'Galleries';

		/**
		 *	function	__construct
		 *	CONSTRUCTOR
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$pom		the PageObjectModel to write into
		 *	@param		$pid		the currnet page's link id
		 *	@param		$userid		the user id of the picasa account
		 *	@param		$albums		the array of albuls to display
		 *						(must index by album id, may contain additional specs for: thumbsize (88, 144, 288), 
		 *						pagesize, pagiationwindow and title
		 *	@param		$templates	the tempaltes to use to construct the album
		 *						(must specify the front and album templates)
		 */
		function __construct(&$pom, &$pid, &$userid, &$albums, &$templates, $title = null){
			$this->pom = $pom;
			$this->pid = $pid;
			$this->templates = $templates;
			$this->albums = $albums;
			$this->userid = $userid;
			$this->galleryTitle = $title;

			$this->feeds = str_replace('{userid}', $this->userid, $this->feeds);

			$this->buildAlbum($_GET['aid'], $_GET['page']);
		}

		/**
 		 *	function	buildAlbum
		 *	PRIVATE		Builds the appropriate web album
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$aid	the album id to display (if not set or null, displays top level gallery)
		 *	#param		$page	the page of the album to display
		 */
		function buildAlbum($aid, $page){
			if(strlen($aid) > 0){
				$feed = str_replace('{albumid}', $aid, $this->feeds['album']);
				$xml = @simplexml_load_file($feed);

				if($xml !== FALSE){
					$albumDetails = $this->albums[$aid];
					$template = file_get_contents($this->templates['album']);

					$xml->registerXPathNamespace('os', 'http://a9.com/-/spec/opensearchrss/1.0/');
					$xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');

					$vidpath = './/media:group/media:content[@medium="video"][@type="application/x-shockwave-flash"][@width=480]';

					$thumbpath = (isset($albumDetails['thumbsize'])	? ".//media:group/media:thumbnail[@width=$albumDetails[thumbsize]]/@url | 
												.//media:group/media:thumbnail[@height=$albumDetails[thumbsize]]/@url"
											: './/media:group/media:thumbnail/@url');

					$albumTitle = (isset($albumDetails['title'])	? $albumDetails['title']
											: $this->flattenXPath($xml->xpath('//channel/title')));

					$pageSize = (isset($albumDetails['pagesize'])	? $albumDetails['pagesize']
											: $this->DEFAULT_PAGE_SIZE);

					$window = (isset($albumDetails['pagiation'])	? $albumDetails['pagiation']
											: $this->DEFAULT_PAGIATION_WINDOW);

					$totalItems = $this->flattenXPath($xml->xpath('//channel/openSearch:totalResults'));
					$maxPage = (int)($totalItems / $pageSize) - ($totalItems % $pageSize == 0 ? 1 : 0);

					if(!isset($page) || $page > $maxPage)
						$page = 0;

					$start = (ctype_digit($page) && $page <= $maxPage ? $page * $pageSize : 0);
					$end = ($page < $maxPage ? $start + $pageSize : $totalItems);

					$counter = 0;
					$items = $xml->xpath('//channel/item');
					for($i = $start; $i < $end; $i++){
						$item = $items[$i];

						$id = $this->parseId((string)$item->guid);

						$pic = array(	'i'	=> $counter++,
								'id'	=> $id,
								'title'	=> (string)$item->title,
								'desc'	=> $this->flattenXPath($item->xpath('.//media:group/media:description')),
								'thumb'	=> $this->flattenXPath($item->xpath($thumbpath)),
								'img'	=> $this->flattenXPath($item->xpath('.//media:group/media:content/@url'))
							);

						if(($vid = $item->xpath($vidpath)) != FALSE)
							$pic['vid'] = $vid;

						$this->pom->append('vars', 'album', $this->buildItem($pic, $template));
					}

					$this->pom->set('page', 'title', $albumTitle);
					$this->pom->set('vars', 'title', $albumTitle);
					$this->pom->set('vars', 'pagiation', $this->buildPagiationWindow($page, $window, $maxPage, "{$this->pid}?aid=$aid"));
					$this->pom->set('vars', 'galleryLink', "<a href = \"{$this->pid}\">&lt;-- Back to Albums</a>");
					$this->pom->set('vars', 'maxPage', $maxPage);
					$this->pom->set('vars', 'maxScreen', $pageSize - 1);
				}
				else{
					$this->pom->append('vars', 'album', 'unable to open feed');
				}
			}
			else{
				$albumTitle = (isset($this->galleryTitle) ? $this->galleryTitle : $this->DEFAULT_GALLERY_TITLE);

				$template = file_get_contents($this->templates['front']);
				$xml = @simplexml_load_file($this->feeds['front']);
			
				if($xml !== FALSE){
					$xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');

					foreach($xml->xpath('//channel/item') as $item){
						$id = $this->parseId((string)$item->guid);

						if(array_key_exists($id, $this->albums)){
							$item = array(	'id'	=> $id,
									'title'	=> (string)$item->title,
									'desc'	=> $this->flattenXPath($item->xpath('.//media:group/media:description')),
									'thumb'	=> $this->flattenXPath($item->xpath('.//media:group/media:thumbnail/@url')),
									'link'	=> "{$this->pid}?aid=$id"
								);
							
							$this->pom->append('vars', 'album', $this->buildItem($item, $template));
						}
					}
				}
				else{
					$this->pom->append('vars', 'album', 'unable to open feed');
				}

				$this->pom->set('page', 'title', $albumTitle);
				$this->pom->set('vars', 'title', $albumTitle);
				$this->pom->set('vars', 'pagiation', '');
				$this->pom->set('vars', 'galleryLink', '<a href = "..">&lt;-- Back to Gallery List</a>');
			}
		}

		/**
		 *	function	buildPariationWindow
		 *	PRIVATE		Builds the sliding pagiation window for the album
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$current	the current page
		 *	@param		$window 	the size of the window (pages on either side of the current page)
		 *	@param		$max		the maximum page
		 *	@param		$url		the base url for the gallery
		 *
		 *	@return		the album's id
		 */
		private function buildPagiationWindow($current, $window, $max, $url){
			if($current == 0)
				$pagiation = "<span>&laquo;</span><span>&lt;</span>";
			else
				$pagiation = "<a href = \"$url\">&laquo;</a><a href = \"$url&page=" . ($current - 1) . "\">&lt;</a>";

			if($current <= $window){
				$start = 0;
				$end = $window * 2;
			}
			elseif($current > $max - $window){
				$start = $max - ($window * 2);
				$end = $max;
			}
			else{
				$start = $current - $window;
				$end = $current + $window;
			}

			//make sure the computed window boundries are within the item page set
			if($start < 0) $start = 0;
			if($end > $max) $end = $max;

			for($i = $start; $i <= $end; $i++)
				$pagiation .= "<a href = \"$url&page=$i\"" . ($i == $current ? 'class = "current"' : '') . ">$i</a>";

			if($current == $max)
				$pagiation .= "<span>&gt;</span><span>&raquo;</span>";
			else
				$pagiation .= "<a href = \"$url&page=" . ($current + 1) . "\">&gt;</a><a href = \"$url&page=$max\">&raquo;</a>";

			return $pagiation;
		}

		/**
		 *	function	parseId
		 *	PRIVATE		Parses an album's id from its GUID
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$guid	the guid to parse
		 *
		 *	@return		the album's id
		 */
		private function parseId($guid){
			$start = strrpos($guid, '/') + 1;
			$end = strrpos($guid, '?');

			return substr($guid, $start, $end - $start);

		}
	
		/**
		 *	function	buildItem
		 *	PRIVATE		Builds an item into a template
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$item		an array conaining the item detail to build
		 *	@param		$template	the tempalte to build into
		 */
		private function buildItem($item, $template){
			if($item['desc'] == '')
				$item['desc'] = $item['title'];

			foreach($item as $var => $val)
				$template = str_replace('{' . $var . '}', $val, $template);

			return $template;
		}

		/**
		 *	function	flattenXPath
		 *	PRIVATE		Flattens the output from a single return XPath
		 *
		 *	@author		Erik J. Olson
		 *
		 *	@param		$xpath	the XPath to flatten
		 */
		private function flattenXPath($xpath){
			return (string)$xpath[0];
		}
	}
?>

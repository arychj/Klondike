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
	 *	core.utils.lib.page.Lists
	 *	desc
	 *
	 *	@author	Erik J. Olson
	 *
	 *	@changelog
	 *	2010.03.10
	 *		Created
	 */
	 
	class Lists{
		const GALLERY_ITEM_TEMPLATE = '<div style = "height: 185px; text-align: {side}; vertical-align: middle; padding-top: 5px; border-bottom: 1px solid #CCCCCC;">
	<div class = "picture" style = "width: 30%; float: {side};">
		<div class = "head" style = "text-align: {side}; padding-bottom: 5px;">{name}</div>
		<a href = "{link}" target = "_blank">
			<div style = "padding-{side}: 5px;">{link}</div>
			<br/>
			<div style = "text-align: center;"><img src = "{img}" class = "pic" alt = "{name}"/></div>
		</a>
	</div>
	<table style = "height: 100%;"><tr><td style = "height: 100%; vertical-align: middle; text-align: justify;">
		{desc}
	</td></tr></table>
</div>';
		
		static function generateListGallery(&$pom, $list, $template = false){
			$pom->set('page', 'content', "<!--The following is ugly as all hell, but I was lazy and didn't have the time to figure out the proper way of doing it...-->\r\n<div style = \"width: 80%; margin: auto;\">");
			for($i = 0; $i < count($list); $i++){
				if($i % 2 == 0)
					$list[$i]['side'] = 'left';
				else
					$list[$i]['side'] = 'right';
			
				$pom->append('page', 'content', self::compile(($template == false ? self::GALLERY_ITEM_TEMPLATE : $template), $list[$i]));
			}
				
			$pom->append('page', 'content', "</div>");
		}
		
		static function compile($template, $vars){
			if(count($vars) > 0)
				foreach($vars as $key => $value)
					$template = str_replace('{' . $key .'}', trim($value), $template);
			
			return $template;
		}
	}
?>

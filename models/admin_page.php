<?php
class StaticPage
{
	var $area_name;
	var $name;
	var $display_name;
	var $path;
	var $template;
	var $parent_page_id;
	
	function FindByAreaAndName($area, $name)
	{
		//if($area != "admin")
		//{
			//die("admin page tried to be a normal page");
		//}
		$page_has_parent = array_shift(Pages::FindBySql("Pages", "SELECT * FROM pages WHERE parent_page_id AND name = '$name' LIMIT 1;"));
		if ($page_has_parent) { $parent_page_id = $page_has_parent->parent_page_id; } else { $parent_page_id = null; }

		$return_page = new StaticPage();
		$return_page->name = $name;
		$return_page->area_name = $area;
		$return_page->path = "/".$area."/".$name;
		$return_page->parent_page_id = $parent_page_id;
		return $return_page;
	}
	
	function hasContentFile()
	{
		// All Admin pages are currently run as static page templates, so this should always be true. 
		return true;
	}
	
	function initialize()
	{
		$route = $GLOBALS['ROUTES'][$this->path];
		
		if( isset($route) )
		{
			$this->template = $route["template"];
			if( $this->hasContentFile() )
			{
				require_once(pagePath($route["file"]));
				if ( function_exists('initialize_page') ) initialize_page(); 
			}
		}
	}
	
	function getContent($htmlize = true)
	{
		$route = $GLOBALS['ROUTES'];
		
		if( isset($route) )
		{
			if( $this->hasContentFile() )
			{
				require_once(pagePath($route[$this->path]["file"]));
				if ( function_exists('display_page_content') ) display_page_content();
			}
		}
	}
	
	function get_children()
	{
		return array();
	}
	
	function get_all_children()
	{
	    return array();
	}
	
	function get_parent()
	{
		return MyActiveRecord::FindById('Pages', $this->parent_page_id); 
	}
	
	function getRelativeLinkPath()
	{
		return "/" . $this->area_name . "/" . $this->name;
	}
	
	function DisplayAdminPageEditLink( $id )
	{
		$page = Pages::FindById( $id ); 
		return "<a href=".get_link( "admin/edit_page/".$page->id ).">Edit $page->display_name</a>\n"; 
	}
	
	function isInPrivateArea()
	{
		return $this->area_name == "admin";
	}
	
}
?>
<?php
class Areas extends ModelBase
{
	function FindByName($name)
	{
		//return MyActiveRecord::FindFirst('Areas', "name like '" . $name . "'");
		return array_shift(MyActiveRecord::FindBySql('areas', "SELECT a.* FROM areas a WHERE a.name like '" . $name . "'"));
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('areas', $id);
	}
	
	function FindAll()
	{
		return MyActiveRecord::FindAll('areas', "name NOT LIKE '%-portfolio' AND name NOT LIKE '%_blog' ", "display_order, id DESC");
	}
	
	function FindPortAreas( $public=false )
	{
		$public_condition = ( $public ) ? ' AND public = 1' : ''; 
		return MyActiveRecord::FindAll('areas', "name LIKE '%-portfolio' AND name NOT LIKE '%_blog'$public_condition", "display_order, id DESC");
	}
	
	/* Added May 6, 2012 */
	function is_portfolioarea() {
	   if ( substr( $this->name, -10 ) == "-portfolio" ) :
	       return true; 
	   else :
	       return false; 
       endif; 
	}
	
	/* Added July 5, 2013 */
	function is_blogarea() {
	   if ( $this->name == "site_blog" ) :
	       return true; 
	   else :
	       return false; 
       endif; 
	}
	
	function FindAdminListAreas() 
	{
		$not_like = "name NOT LIKE 'orphans_portfolio'";
		if (!BLOG_INSTALL) { $not_like .= " AND name NOT LIKE 'site_blog'"; }
		return MyActiveRecord::FindAll('areas',$not_like,"display_order, id DESC");
	}
	
	function FindPublicAreas()
	{
		return MyActiveRecord::FindAll('areas', "public = 1", "display_order, id DESC");
	}
	
	function findPages($include_private = false, $include_all_children = false)
	{
		$public_condition = ( $include_private ) ? "(pages.public = 1 or pages.public = 0)" : "(pages.public = 1)";
		$children_condition = ( $include_all_children ) ? "(pages.parent_page_id is null or pages.parent_page_id > 0)" : "(pages.parent_page_id is null or pages.parent_page_id = 0)";
		$pages = $this->find_linked('pages', "$public_condition AND $children_condition AND pages.content_file != 'draft'", "areas_pages.display_order ASC");
		
		if($include_all_children)
		{
		    $all_pages = array();
		    foreach($pages as $page)
		    {
		        $all_pages[] = $page;
		        
		        foreach($page->get_all_children() as $child)
		        {
		            $all_pages[] = $child;
		        }
		    }
		    return $all_pages;
		}
		
		return $pages;
	}
	
	function getRelativeLinkPath()
	{
		if($this->name == "")
		{
			return "/";
		}
		else
		{
			return "/" . $this->name . "/";
		}
	}
	
	function checkAlias($name)
	{
		$aliases = Alias::FindBySql('alias', "SELECT * FROM alias WHERE path LIKE '/{$name}%' OR path LIKE '{$name}%'");
		if (count($aliases))
		{
			foreach ($aliases as $alias)
			{
				$pathParts = explode("/", $alias->path);
				if (substr($alias->path, 0, 1) == "/")
				{	$path = "/{$this->name}/".$pathParts[2];	}
				else { $path = "/{$this->name}/".$pathParts[1]; }
				$alias->path = $path;
				$alias->save();
			}
		}
		return false;
	}
	
	function getSections( $include_private = false )
	{
		$public_condition = "(sections.public=1)";
		if($include_private)
		{
			$public_condition = "(sections.public=1 or sections.public=0)";
		}
		
		if ( strstr( $this->name, "-portfolio" ) )
		{
			$sections = $this->find_linked('sections', $public_condition, "sections.display_order ASC"); 
		}
		return $sections; 
	}
	
	// Added Feb 1 for Lumetta (but it was a good idea) – Portfolio Areas get an optional textarea for content. Therefore, we need to display it.
	// This function is an alias for a larger function now... 
	function getContent($htmlize = false)
	{
		$this->the_content(); 
	}
}
?>
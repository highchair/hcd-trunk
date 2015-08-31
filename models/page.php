<?php
class Pages extends ModelBase
{
	function FindAll( $orderby = " id ASC" )
	{
		return MyActiveRecord::FindAll('Pages', NULL, $orderby);
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Pages', $id);
	}
	
	function FindByAreaAndName($area, $name)
	{
		$result = array_shift(MyActiveRecord::FindBySql('Pages', "SELECT p.* FROM pages p INNER JOIN areas_pages ap ON ap.pages_id = p.id INNER JOIN areas a ON a.id = ap.areas_id WHERE p.name like '" . $name . "' AND a.name like '" . $area->name . "' ORDER BY id ASC"));
		return ( ! empty($result) ) ? $result : false; 
	}
	
	function FindPageOrIndex($area, $name, $isdraft=false)
	{
		$draftcond = "";
		if ($isdraft) { $draftcond = "AND p.content_file = 'draft'"; }
		
		// if the page name is index and a specific "index" page isn't found, return the first page in the area as the index
		$found_pages = MyActiveRecord::FindBySql('Pages', "SELECT p.* FROM pages p INNER JOIN areas_pages ap ON ap.pages_id = p.id INNER JOIN areas a ON a.id = ap.areas_id WHERE p.name like '" . $name . "' $draftcond AND a.name like '" . $area->name . "' ORDER BY id ASC");
		
		// index not found. select all pages ordered by display_order
		if($name == "index" && count($found_pages) == 0)
		{
			$found_pages = Pages::FindByArea($area);
		}
			else if(count($found_pages) == 0)
		{
		    $all_area_pages = MyActiveRecord::FindBySql('Pages', "SELECT p.* FROM pages p INNER JOIN areas_pages ap ON ap.pages_id = p.id INNER JOIN areas a ON a.id = ap.areas_id WHERE a.name like '" . $area->name . "' ORDER BY id ASC");
		    
		    if(SUB_PAGES) 
		    {
			    foreach($all_area_pages as $page)
			    {
			        $all_children = $page->get_all_children();
			        foreach($all_children as $child)
			        {
			            if($child->name == $name) { return $child; }
			        }
			    }
		    }
		}
		$page = array_shift($found_pages);
		
		// Now that we have a page, allow users logged in to see non-public pages.
		$user = Users::GetCurrentUser();
		$logged_in = false;
		if($user) { $logged_in = true; }
		
		// If not logged in, make sure the first page returned is not a non-public page. Could throw an error. 
		if(!$logged_in)
		{
			while ($page && $page->public == false) { $page = array_shift($found_pages); }
		}
		
		return $page;
	}
	
	function FindByArea($area, $public=false)
	{
		$public_condition = ( $public ) ? ' AND p.public = 1' : ''; 
		
		return MyActiveRecord::FindBySql('Pages', "SELECT p.* FROM pages p INNER JOIN areas_pages ap ON ap.pages_id = p.id INNER JOIN areas a ON a.id = ap.areas_id WHERE a.name like '" . $area->name . "' AND p.name NOT LIKE '%-draft'".$public_condition." ORDER BY ap.display_order, id ASC");
	}
	
	function delete($deleteChildren = true)
	{
		// unattach all the areas
		$areas = $this->getAreas();
		
		foreach($areas as $area)
		{
			$this->detach($area);
		}
		
		$this->destroy();
	}
	
	// ! - - - - - Get functions on the page object - - - - - 
	
	function getContent()
	{
        /* 
         * Trying to depreciate this function in favor of a more global get_content() but if a site has a blog or other static content, we get an error:
         * Fatal error: Call to undefined method StaticPage::the_content() in /htdocs/www/newsite/content/layouts/default.php on line 8
         * So, we still need to use this function from time to time, esp on page objects
         */
        if( $this->hasContentFile() )
        {
            // We Need This! Used for static paths defined in the Routes
            require_once("../content/".$this->content_file.".php");
            display_page_content();
        
        } else {
         
            $this->the_content(); 
        }
	}
	
	function displayPage( $pageid, $filtered=true )
	{
		$page = Pages::FindById( $pageid ); 
		if ( $filtered && $page->public )
		{
			return $page->getContent(); 
		} elseif ( $page->public ) {
			return $page->content; 
		}
	}
	
	function getAreas() // returns more than one area (multi-dimensional array) even if page is only in one area
	{
	    $parent = $this->get_parent();
	    if($parent)
	    {
	        return $parent->getAreas();
	    }
		return $this->find_linked('areas');
	}
	
	function getOrderInArea($area)
	{
		if($area)
		{
			$query = "SELECT display_order FROM areas_pages WHERE pages_id =  " . $this->id . " AND areas_id = " . $area->id;
			$result = mysql_Query($query, MyActiveRecord::Connection());	
			
			$data = @mysql_fetch_array($result);
			return $data["display_order"];
		}
	}
	
	function updateSelectedAreas($changed_areas) // Used when edit_pages changes the areas a page is in
	{
		 $selected_areas = $this->getAreas();
		
		// look for added areas
		foreach($changed_areas as $changed_area)
		{
			$found = false;
			foreach($selected_areas as $selected_area)
			{
				if($selected_area->id == $changed_area)
				{
					$found = true;
				}
			}
			if(!$found)
			{
				$tmp_area = Areas::FindById($changed_area);
				$this->attach($tmp_area);
			}
		}
		
		// look for deleted areas
		foreach($selected_areas as $selected_area)
		{
			if(!in_array($selected_area->id, $changed_areas))
			{
				//the user has removed this area
				$tmp_area = Areas::FindById($selected_area->id);
				$this->detach($tmp_area);
			}
		}
	}
	
	function get_parent()
	{
		return MyActiveRecord::FindById('Pages', $this->parent_page_id);
	}
	
	function get_children()
	{
		return MyActiveRecord::FindAll('Pages', "parent_page_id={$this->id}", 'display_order ASC');
	}
	
	function get_all_children()
	{
	    $children = $this->get_children();
	    
	    foreach($children as $child)
	    {
	        array_merge($children, $child->get_children());
	    }
	    return $children;
	}
	
	// ! - - - - - Sorting/Display functions - - - - - 
	function updateOrderInArea($area, $order = 1)
	{
		if($area != null)
		{
			$updateQuery = "UPDATE areas_pages SET display_order = " . $order . " WHERE pages_id =  " . $this->id . " AND areas_id = " . $area->id;
			$result = mysql_Query($updateQuery, MyActiveRecord::Connection()) or die(print_r($area)); 
		}
	}
	
	function setDisplayOrderInArea()
	{
		$areas = $this->getAreas();
		foreach ($areas as $area)
		{
			$query = "UPDATE areas_pages SET display_order = 1 WHERE pages_id = {$this->id} AND areas_id = {$area->id};";
			mysql_query($query, MyActiveRecord::Connection());
			
			// only update pages in the area that the page is in
			$display_order = 2;
			$pages = $area->findPages(true);
			
			foreach ($pages as $page)
			{
				if ($page->id != $this->id)
				{
					$query = "UPDATE areas_pages SET display_order = $display_order WHERE pages_id = {$page->id} AND areas_id = {$area->id};";
					mysql_query($query, MyActiveRecord::Connection());
					$display_order++;
				}
			}
		}
	}

	
	// ! - - - - - Templating functions - - - - - 
	function hasContentFile()
	{
		if($this->content_file != "")
		{
			//return true;
		}
		return false;
	}
	
	function initialize()
	{
		if($this->hasContentFile())
		{
			require_once("../content/$this->content_file.php");
			initialize_page();
		}
	}

	function getTemplateForArea($area)
	{
	    // CYA
	    if(!isset($area))
	    {
	        return "default";
	    }
	    
	    // this page has an explicit template selected
	    if($this->template != '')
	    {
	        return $this->template;
	    }
	    
	    // this page inherits from either an area or a parent page
	    if($this->parent_page_id > 0)
	    {
	        
	        $parent = $this->get_parent();
	        return $parent->getTemplateForArea($area);
	    }
	    else
	    {
	        return $area->template;
	    }
	}	
	
	function getRelativeLinkPath() // Used when?
	{
        $parent = $this->get_parent();
        if($parent)
        {
            return $parent->getRelativeLinkPath() . "/{$this->name}";
        }
		if($this->name == "index")
		{
			return "";
		}
		else
		{
			return "" . $this->name;
		}
	}
	
	function getDirectLinkPath( $withURL=false )
	{	
		// Return any area... we can't return a specific area with this function
		$area = $this->getAreas(); 
		if ($withURL):
			return "http://".SITE_URL."/".$area->name."/".$this->name; 
		else:
			return $area->name."/".$this->name; 
		endif; 
	}
	
	function isInPrivateArea()
	{
		$page_areas = $this->getAreas();
		
		foreach($page_areas as $area)
		{
			if($area->public == 0)
			{
				return true;
			}
		}
		
		return false;
	}
	
	// ! - - - - - Draft Functions - - - - - 
	function hasDraft() 
	{
		$query = "SELECT * FROM pages WHERE content_file = 'draft' AND name = '{$this->name}' LIMIT 1;";
		return MyActiveRecord::FindBySql('Pages', $query);
	}
	
	function killDraft() 
	{
		$page_query = "SELECT * FROM pages WHERE content_file = '' AND name = '{$this->name}' LIMIT 1;";
		$page = MyActiveRecord::FindBySql('Pages', $page_query);
		$query = "DELETE FROM pages WHERE id = {$this->id}";
		mysql_query($query ,MyActiveRecord::Connection()) or die($query);
		return array_shift($page);
	}
	
	// ! - - - - - Clean Ups when the names of Insertables change - - - - -	
	function UpdateImageReferences($oldName, $newName)
	{
		$pages = Pages::FindAll();
		
		$anyImagePattern = "/([>{2}<{2}])$oldName([>{2}<{2}])/";
		
		// The updateContent function is found in content_filter.php
		
		foreach($pages as $page)
		{
			$replacement = '$1' . $newName . '$2';
			$page->content = updateContent($page->content, $anyImagePattern, $replacement);
			$page->save();
		}
	}
	
	function UpdateDocumentReferences($oldName, $newName)
	{
		$pages = Pages::FindAll();
		
		$anyDocumentPattern = "/{{2}(document:$oldName+){{2}/";
		
		foreach($pages as $page)
		{
			$replacement = '{{document:' . $newName . '{{';
			$page->content = updateContent($page->content, $anyDocumentPattern, $replacement);
			$page->save();
		}
	}
	
	function UpdateProductReferences($oldName, $newName)
	{
		$pages = Pages::FindAll();
		
		$anyProductPattern = "/{{2}(product:$oldName+){{2}/";
		
		foreach($pages as $page)
		{
			$replacement = '{{product:' . $newName . '{{';
			$page->content = updateContent($page->content, $anyProductPattern, $replacement);
			$page->save();
		}
	}
	
	// Added Mar 18 2011 - WordPress-like edit button function
	function getPageEditLinks($area, $additional_button="")
	{
		if ( IsUserLoggedIn() ) {
			echo "<div id=\"admin-pane-controls\">\n"; 
			echo "\t<h3>Admin Controls:</h3>\n"; 
			echo "\t<a href=\"".get_link("admin/")."\">Dashboard</a>\n"; 
			echo "\t<a href=\"".get_link("admin/edit_page/".$this->id)."\">Edit this Page</a>\n"; 
			if ( $area->id != "1" ) {
				echo "\t<a href=\"".get_link("admin/edit_area/".$area->id)."\">Edit this Area</a>\n"; 
			}
			echo "\t<a href=\"".get_link("admin/list_pages/")."\">List Pages</a>\n"; 
			echo "\t<a href=\"".get_link("admin/add_page")."\">Add a Page</a>\n"; 
			echo "\t<a href=\"".get_link("admin/edit_area/add")."\">Add an Area</a>\n"; 
			
			if ( $additional_button != "" ) {
				echo $additional_button; 
			}
			echo "\t<a href=\"".get_link( "admin/logout")."\">Logout</a>\n"; 
			echo "</div>\n"; 
		}
	}
	
	// Added January 19, 2012 - Make it easier to escape names all the time. Use the WordPress standard of "get" and "the" for return and echo, respectively. 
	// Depreciated. Use the functions in common/model_base.php instead. 
	function get_displayname() {
		return $this->get_title(); 
	}
	function the_displayname() {
		echo $this->get_title(); 
	}
	
	// ! - - - - - Alias function - - - - - Needed by the display_template function to check and see if we should redirect or not
	function checkAlias($selected_areas, $name)
	{
		foreach ($selected_areas as $areaid)
		{
			$area = Areas::FindById($areaid);
			$aliases = Alias::FindBySql('alias', "SELECT * FROM alias WHERE path LIKE '/{$area->name}/{$name}%' OR path LIKE '{$area->name}/{$name}%'");
			if (count($aliases))
			{
				foreach ($aliases as $alias)
				{
					$alias->path = "/{$area->name}/".$this->name;
					$alias->save();
				}
			}
		}
		return false;
	}
}
?>
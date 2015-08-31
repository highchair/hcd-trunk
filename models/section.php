<?php

class Sections extends ModelBase
{
	function FindById($id)
	{          
		return MyActiveRecord::FindById('Sections', $id);
	}
	
	function FindByName( $name )
	{
		//return MyActiveRecord::FindFirst('Areas', "name like '" . $name . "'");
		return array_shift( MyActiveRecord::FindBySql('Sections', "SELECT a.* FROM sections a WHERE a.name like '" . $name . "'") );
	}
	
	function FindAll()
	{
		return MyActiveRecord::FindAll('Sections', null, "display_order, id ASC");
	}
	
	function FindPublicSections( $orderby = "display_order, id ASC" )
	{
		return MyActiveRecord::FindAll('Sections', 'public = 1', $orderby );
	}
	
	// Added April 25 for Lumetta
	function FindByAreaAndName( $area, $name, $include_private = false )
	{
		// $public_condition = Sections::getPublicCondition( $include_private, 'i' );
		// return array_shift( MyActiveRecord::FindBySql( 'Sections', "SELECT i.* FROM sections i INNER JOIN areas_sections its ON its.sections_id = i.id INNER JOIN areas s ON s.id = its.areas_id WHERE {$public_condition} AND i.name like '" . $name . "' AND s.name like '" . $area->name . "' ORDER BY id ASC" ) );
		return array_shift( MyActiveRecord::FindBySql( 'Sections', "SELECT i.* FROM sections i INNER JOIN areas_sections its ON its.sections_id = i.id INNER JOIN areas s ON s.id = its.areas_id WHERE i.name like '" . $name . "' AND s.name like '" . $area->name . "' ORDER BY id ASC" ) );
	}
	
	// Added September 4, 2012
	function findNext( $area, $public = "s.public AND" )
	{
		$order = $this->getOrderInArea($area);
		return array_shift(MyActiveRecord::FindBySql('Sections', "SELECT s.* FROM sections s 
		INNER JOIN areas_sections as ON as.sections_id = s.id 
		INNER JOIN areas a ON a.id = as.areas_id 
		WHERE $public a.id like '" . $area->id . "' 
		AND s.display_order < $order 
		ORDER BY s.display_order DESC LIMIT 1") );   
	}
	
	function findPrev( $area, $public = "i.public AND" )
	{
		$order = $this->getOrderInArea($area);
		return array_shift(MyActiveRecord::FindBySql('Items', "SELECT i.* FROM items i INNER JOIN items_sections its ON its.items_id = i.id INNER JOIN sections s ON s.id = its.sections_id WHERE $public s.id like '" . $section->id . "' AND its.display_order < $order ORDER BY its.display_order DESC LIMIT 1"));

	}
	
	function findItems($include_private = false, $orderby = "id", $order = "ASC")
	{
	    $public_sql= "public = 1";
	    if($include_private)
	    {
	        $public_sql = "";
	    }
	    // Can this query exclude the thumbnail BLOB field? Too much memory is needed. 
		return $this->find_linked('Items', $public_sql, "items_sections.display_order, $orderby $order");
	}
	
	function getPortfolioAreas() {
		return $this->find_linked('areas');
	}
	
	// Added Feb 21, 2012
	function thePortfolioArea() {
		return array_shift( $this->find_linked('areas') );
	}
	
	function getTemplateForArea($area)
	{
	    if(!isset($area->template))
	    {
	        return "portfolio";
	    }
	    else
	    {
	        return $area->template;
	    }
	}
	
	function initialize()
	{

	}
	
	
	function updateSelectedAreas($changed_areas)
	{
		$selected_areas = $this->getPortfolioAreas();
		
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
	
	// Added Sept 4 2012
	function getOrderInArea( $area )
	{
		if( isset($area) ) {
			$query = "SELECT display_order FROM areas_sections WHERE sections_id =  " . $this->id . " AND areas_id = " . $area->id;
			$result = mysql_query( $query, MyActiveRecord::Connection() );	
			
			$data = @mysql_fetch_array( $result );

			return $data["display_order"];
		}
	}
	
	// Added Mar 18 2011 - WordPress-like edit button function
	// Don't forget to use the required CSS if you use this function! 
	function getPageEditLinks( $area="", $additional_button="", $item="" )
	{
		if ( IsUserLoggedIn() ) {
			
			echo "<div id=\"admin-pane-controls\">\n"; 
			echo "\t<h3>Admin Controls:</h3>\n"; 
			echo "\t<a href=\"".get_link("admin/")."\">Dashboard</a>\n"; 
			if ( $item ) {
				echo "\t<a href=\"".get_link("admin/portfolio_edit/".$this->name."/".$item->id)."\">Edit this Item</a>\n"; 
			}
			echo "\t<a href=\"".get_link("admin/portfolio_edit_section/".$this->id)."\">Edit this Section</a>\n"; 
			echo "\t<a href=\"".get_link("admin/portfolio_edit_area/".$area->id)."\">Edit this Area</a>\n"; 
			echo "\t<a href=\"".get_link("admin/portfolio_list")."\">List Portfolio</a>\n"; 
			echo "\t<a href=\"".get_link("admin/portfolio_add_item")."\">Add an Item</a>\n"; 
			echo "\t<a href=\"".get_link("admin/portfolio_add_section")."\">Add a Section</a>\n"; 
			echo "\t<a href=\"".get_link("admin/portfolio_add_area/")."\">Add an Area</a>\n"; 
			if ( $additional_button != "" ) {
				echo $additional_button; 
			}
			echo "\t<a href=\"".get_link( "admin/logout")."\">Logout</a>\n"; 
			echo "</div>\n"; 
		}
	}
	
	// ! Depreciated !
	
	// Replaced by the the_URL() / get_URL() functions, which work on any object type and take more parameters. 
	function getSectionLink( $area=false ) {
		if ( $area ) {
			return get_link( $area->get_slug()."/".$this->get_slug() ); 
		} else {
    		return $this->get_URL();
		}
	}
	function theSectionLink( $area=false ) { echo $this->getSectionLink( $area ); }
	
	
	function getContent()
	{
		$this->the_content();
	}
}
?>
<?php
class Items extends ModelBase
{
	function FindAll( $orderby = "id ASC", $exclude=true )
	{
		//return MyActiveRecord::FindAll('Items', NULL, " $orderby");
		// Let's speed up this query and exclude the blob fields... too much memory for nothing. 
		if ( $exclude ) {
			return MyActiveRecord::FindBySql('Items', "SELECT id, name, display_name, content, template, display_order, sku, price, taxonomy, public, public2, date_created, date_revised FROM items ORDER BY $orderby");
		} else {
			return MyActiveRecord::FindAll('Items'); 
		}
	}
	
	function FindPublic( $orderby = "display_name ASC" )
	{
		return MyActiveRecord::FindBySql('Items', "SELECT id, name, display_name, content, template, display_order, sku, price, taxonomy, public, public2, date_created, date_revised FROM items WHERE public = 1 ORDER BY $orderby");

	}
	
	// Added Feb 21, 2012. Excludes blob fields and returns a specific number of items. Defaults to five. 
	function FindThisMany( $num = 5, $orderby = "date_revised DESC" ) {
		return MyActiveRecord::FindBySQL( 'Items', "SELECT id, name, display_name, content, template, display_order, sku, price, taxonomy, public, public2, date_created, date_revised FROM items ORDER BY $orderby LIMIT $num" ); 
	}
	
	function FindById( $id )
	{
		$itemid = mysql_real_escape_string( $id, MyActiveRecord::Connection() );
		return MyActiveRecord::FindById( 'Items', $itemid) ;
	}
	
	function FindByName( $name )
	{
		$itemname = mysql_real_escape_string( $name, MyActiveRecord::Connection() );
		return array_shift(MyActiveRecord::FindBySql('Items', "SELECT i.id, i.name, i.display_name, i.content, i.template, i.display_order, i.sku, i.price, i.taxonomy, i.public, i.public2, i.date_created, i.date_revised FROM items i WHERE i.name like '" . $itemname . "'"));
	}
	
	// Added April 11, 2012 for Lumetta and the extra taxonomy availble to items
	function FindAllByTaxonomy( $taxonomy, $area=false, $include_private=false, $orderby = "id ASC" )
	{
        $itemtax = mysql_real_escape_string( $taxonomy, MyActiveRecord::Connection() );
        $itemorder = mysql_real_escape_string( $orderby, MyActiveRecord::Connection() );
        
        $public_sql = ( $include_private ) ? "" : "public = 1";
	    
	    if ( is_object($area) ) {
            // Get Items only from a particular area
            return $area->find_linked('Items', $public_sql, "WHERE taxonomy like '".$itemtax."' items_sections.display_order, $itemorder");
        } else {
            // Get items from ANY area
            return MyActiveRecord::FindBySql('Items', "SELECT id, name, display_name, content, template, display_order, sku, price, taxonomy, public, public2, date_created, date_revised FROM items WHERE taxonomy like '".$itemtax."' ORDER BY $itemorder");
        }
	}
	
	function FindOrphans()
	{
		return MyActiveRecord::FindBySql('Items', "SELECT * FROM items i LEFT OUTER JOIN items_sections ON items_sections.items_id = i.id WHERE items_id is null AND sections_id is null ORDER BY id ASC");
	}
	
	function FindBySectionAndName( $section, $name, $include_private = false )
	{
		$itemname = mysql_real_escape_string( $name, MyActiveRecord::Connection() );
		
		$public_condition = Items::getPublicCondition($include_private,'i');
		
		return array_shift(MyActiveRecord::FindBySql('Items', "SELECT i.* FROM items i INNER JOIN items_sections its ON its.items_id = i.id INNER JOIN sections s ON s.id = its.sections_id WHERE {$public_condition} AND i.name like '" . $itemname . "' AND s.name like '" . $section->name . "' ORDER BY id ASC"));
	}
	
	function FindBySection($section, $public_only = false)
	{
		$public_condition = Items::getPublicCondition($public_only,'i');
		return MyActiveRecord::FindBySql('Items', "SELECT i.* FROM items i INNER JOIN items_sections its ON its.items_id = i.id INNER JOIN sections s ON s.id = its.sections_id WHERE {$public_condition} AND s.name like '" . $section->name . "' ORDER BY id ASC");
	}
	
	function find_linked($strClass, $mxdCondition=null, $strOrder=null)
	{
		if($this->id)
		{
			// only attempt to find links if this object has an id
			$table = MyActiveRecord::Class2Table($strClass);
			$thistable = MyActiveRecord::Class2Table($this);
			$linktable=MyActiveRecord::GetLinkTable($table, $thistable);
			$strOrder = $strOrder ? $strOrder: "{$strClass}.id";
			$sql= "SELECT {$table}.* FROM {$table} INNER JOIN {$linktable} ON {$table}_id = {$table}.id WHERE $linktable.{$thistable}_id = {$this->id} ";
			if( is_array($mxdCondition) )
			{
				foreach($mxdCondition as $key=>$val)
				{
					$val = addslashes($val);
					$sql.=" AND $key = '$val' ";
				}
			}
			else
			{
				if($mxdCondition) $sql.=" AND $mxdCondition";
			}
			
			$sql .= " ORDER BY $strOrder";
			return MyActiveRecord::FindBySql($strClass, $sql);
		}
		else
		{
			return array();
		}
	}
	
	function getPublicCondition($include_private = false, $prefix = "")
	{
		if($prefix != "")
		{
			$prefix = $prefix.".";
		}
		if($include_private)
		{
			return "({$prefix}public = 1 or {$prefix}public = 0) ";
		}
		return "{$prefix}public = 1 ";
	}
	
	function isInPrivateSection()
	{
		$item_sections = $this->getSections();
		
		foreach($item_sections as $section)
		{
			if($section->public == 0)
			{
				return true;
			}
		}
		return false;
	}
	
	function updateOrderInSection($section, $order = 1)
	{
		if($section != null)
		{
			$updateQuery = "UPDATE items_sections SET display_order = " . $order . " WHERE items_id =  " . $this->id . " AND sections_id = " . $section->id;
			$result = mysql_Query($updateQuery, MyActiveRecord::Connection()) or die("Error updating item order in section: " . $updateQuery); 
		}
	}
	
	function getOrderInSection($section)
	{
		if($section)
		{
			$query = "SELECT display_order FROM items_sections WHERE items_id =  " . $this->id . " AND sections_id = " . $section->id;
			$result = mysql_query($query, MyActiveRecord::Connection());	
			
			$data = @mysql_fetch_array($result);

			return $data["display_order"];
		}
	}
	
	function findNext($section, $public = "i.public AND")
	{
		$order = $this->getOrderInSection($section);
		return array_shift(MyActiveRecord::FindBySql('Items', "SELECT i.* FROM items i INNER JOIN items_sections its ON its.items_id = i.id INNER JOIN sections s ON s.id = its.sections_id WHERE $public s.id like '" . $section->id . "' AND its.display_order > $order ORDER BY its.display_order ASC LIMIT 1"));
	}
	
	function findPrev($section, $public = "i.public AND")
	{
		$order = $this->getOrderInSection($section);
		return array_shift(MyActiveRecord::FindBySql('Items', "SELECT i.* FROM items i INNER JOIN items_sections its ON its.items_id = i.id INNER JOIN sections s ON s.id = its.sections_id WHERE $public s.id like '" . $section->id . "' AND its.display_order < $order ORDER BY its.display_order DESC LIMIT 1"));

	}
	
	function delete($deleteChildren = true)
	{
		// unattach all the areas
		$sections = $this->getSections();
		
		foreach($sections as $section)
		{
			$this->detach($section);
		}
		
		$this->destroy();
	}
	
	function getSections()
	{
		return $this->find_linked('sections','','display_order ASC');
	}
	
	// Added Feb 21, 2012. Should add a way to pass the Area here to possibly narrow down which Section we are in when there is more than one. 
	function theSection()
	{
		return array_shift( $this->getSections() );
	}
	
	function getGallery()
	{
	    return Galleries::FindBySlug("portfolioGal_".$this->id."_".$this->name);
	}
	
	function getPhotos()
	{
        $gallery = $this->getGallery(); 
        return $gallery->get_photos(); 
	}
	
	function getPhotosAndVideos() 
	{
    	$gallery = $this->getGallery(); 
    	return $gallery->get_photos_and_videos(); 
	}
	
	// This could return a video object if it is the first object by display order
	function getFirstPhoto()
	{
		return array_shift( $this->getPhotos() ); 
	}
	
	function setDisplayOrder()
	{
		$sections = $this->getSections();
		foreach ($sections as $section)
		{
			$display_order = count(Items::FindBySection($section));
			$query = "UPDATE items_sections SET display_order = $display_order WHERE items_id = {$this->id} AND sections_id = {$section->id};";
			mysql_query($query, MyActiveRecord::Connection());
		}
	}
	
	function updateSelectedSections($changed_sections)
	{
		$selected_sections = $this->getSections();
		// look for added sections
		foreach($changed_sections as $changed_section)
		{
			$found = false;
			foreach($selected_sections as $selected_section)
			{
				if($selected_section->id == $changed_section)
				{
					$found = true;
				}
			}
			
			if(!$found)
			{
				$tmp_section = Sections::FindById($changed_section);
				$this->attach($tmp_section);
			}
		}
		
		// look for deleted areas
		foreach($selected_sections as $selected_section)
		{
			if(!in_array($selected_section->id, $changed_sections))
			{
				//the user has removed this area
				$tmp_section = Sections::FindById($selected_section->id);
				$this->detach($tmp_section);
			}
		}
	}
	
	function updateSectionRevisionDates()
	{
		$sections = $this->getSections();
		foreach($sections as $section) {
			$section->date_revised = date('Y-m-d H:i:s'); 
			$section->save(); 
			
			//$updateQuery = "UPDATE section SET date_revised = \"".date('Y-m-d H:i:s')."\" WHERE id = {$section->id}";
			//mysql_Query( $updateQuery, MyActiveRecord::Connection() );
		}
	}
	
	function getRelativeLinkPath()
	{
		if($this->name == "index")
		{
			return "";
		}
		else
		{
			return "" . $this->name;
		}
	}
	
	// Added March 11 2013 for Lumetta... a way to attach documents to Portfolio Items
	function findDocuments( $orderby='name DESC' )
	{
		//return MyActiveRecord::FindAll( 'Documents', 'item_id = '.$this->id, $orderby );
		return MyActiveRecord::FindBySql('Documents', "SELECT * FROM documents WHERE item_id = ".$this->id." ORDER BY ".$orderby."");
	}
	
	// Added Sept 2 2013 for Jeff Carpenter... a way to attach videos to Portfolio Items
	function findVideos( $gallery, $orderby='name DESC' )
	{
		if ( is_object($gallery) ) {
    		return MyActiveRecord::FindBySql('Videos', "SELECT * FROM videos WHERE gallery_id = ".$gallery->id." ORDER BY ".$orderby."");
        } else {
            return "Error: A gallery object is required to fetch Videos for an Item"; 
        }
	}
	
	// ! Depreciated !
	
	// Use the get_url() function in common/model_base.php instead. 
	function getPublicUrl( $section="", $rooturl=false )
	{
		return $this->get_url( $section, $rooturl ); 
	}
	
	
	function getContent($htmlize = false)
	{
		$this->the_content();
	}
}
?>
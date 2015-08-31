<?php
class Images extends ModelBase
{
	function FindAll( $exclude=true )
	{
		// Let's speed up this query and exclude the blob fields... too much memory for nothing. 
		if ( $exclude ) {
			return MyActiveRecord::FindBySql('Images', "SELECT id, name, title, description FROM images ORDER BY id DESC");
		} else {
			return MyActiveRecord::FindAll('Images'); 
		}
	}
	
	function FindById($id)
	{
		return array_shift(MyActiveRecord::FindBySql('Images', "SELECT id, name, title, description FROM images WHERE id = '".$id."'")); 
	}
	
	function FindByName($name)
	{
		// Oct 8, 2014: Add a sort order to get the newest ID first, not last like it was doing. 
		return array_shift(MyActiveRecord::FindBySql('Images', "SELECT id, name, title, description FROM images WHERE name = '".$name."' ORDER BY id DESC"));
	}
	
	function FindAllNames()
	{
		return MyActiveRecord::FindBySql('Images', "SELECT name FROM images ORDER BY name ASC");
	}
	
	function FindRandom()
	{
		return array_shift(MyActiveRecord::FindBySql('Images', "SELECT i.* FROM images i ORDER BY rand() LIMIT 1"));
	}
	
	function FindRandomCover()
	{
		return array_shift(MyActiveRecord::FindBySql('Images', "SELECT i.* FROM images i WHERE name LIKE '%_cover' ORDER BY rand() LIMIT 1;"));
	}
	
	// New functions. Added March 2012
	/* 
	 * Returns a number of total pages based on the number of items in the DB and the number of items to display on each page
	 * @accepts a number of items expected for each page
	 */
	function FindLastPage( $perpage )
	{
		// Get the total number of posts 
		$allimages = Images::FindAll(); 
		$rows = count( $allimages ); 
		
		// Find the page number of the last page 
		return ceil( $rows/$perpage ); 
	}
	
	/* 
	 * Gets a paginated range of items from the DB.
	 * @accepts a number of items expected for each page and the page number of the current request
	 * @uses FindLastPage()
	 */
	function FindByPagination( $perpage, $pagenum )
	{
		// Check to see if there is a page number. If not, set it to page 1 
		if ( !(isset($pagenum)) ) $pagenum = 1; 
	
		$last = Images::FindLastPage( $perpage ); 
		
		// Ensure the page number isn't below one, or more than the maximum pages 
		if ( $pagenum < 1 ): 
			$pagenum = 1; 
		elseif ( $pagenum > $last ): 
			$pagenum = $last; 
		endif;  
		
		// Sets the limit value for the query 
		$max = 'LIMIT ' . ( $pagenum - 1 ) * $perpage .', ' .$perpage;
		
		return MyActiveRecord::FindBySQL( 'Images', "SELECT * FROM images ORDER BY id DESC $max" ); 
	}
	
	function howManyReferencing() 
	{
		//$anyImagePattern = "/([>{2}<{2}])$this->name([>{2}<{2}])/";
		
		$pagecount = $eventcount = $entrycount = 0;
		
		$pages = Pages::FindAll(); 
		if ( CALENDAR_INSTALL ) 
			$events = Events::FindAll();
		if ( BLOG_INSTALL )
			$entries = Blog_Entries::FindAll();
		
		$pattern_recog = array("left" => array("{","{"), "right" => array("}","}"), "reg" => array("{","}"));
		
		foreach ($pattern_recog as $float => $direction) {
			$imagePattern = "*".$direction[0]."{2}([A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
		
			foreach( $pages as $page ) {
				$imageIds = getFilterIds( $page->content, $imagePattern);
				
				print_r($imageIds); 
				
				if ( count($imageIds) >= 1 )
					$pagecount++; 
			}
			if ( CALENDAR_INSTALL ) {
				foreach( $events as $page ) {
					$imageIds = getFilterIds( $page->description, $imagePattern);
					if ( is_array($imageIds) )
						$eventcount++; 
				}
			}
			if ( BLOG_INSTALL ) {
				foreach( $entries as $page ) {
					$imageIds = getFilterIds( $page->content, $imagePattern);
					if ( is_array($imageIds) )
						$entrycount++; 
				}
			}
		}
		$message = $pagecount." Pages";
		if ( CALENDAR_INSTALL ) $message .= ", ".$eventcount." Events";  
		if ( BLOG_INSTALL ) $message .= ", ".$entrycount." Blog Entries"; 
		
		return $message; 
	}
	
	function numberOfPagesReferencing()
	{
		$pages = Pages::FindAll();
		
		// this should work, but it doesn't ensure that the >>s are balanced (ie, it will also match <<image>>)
		$anyImagePattern = "/([>{2}<{2}])$oldName([>{2}<{2}])/";
		
		foreach($pages as $page)
		{
			$replacement = '$1' . $newName . '$2';
			$page->content = updateContent($page->content, $anyImagePattern, $replacement);
			$page->save();
		}
		
		return 0;
	}
	
	function displayImage() 
	{
		echo "<img src=\"".get_link("/images/view/".$this->id)."\" /><br />\n\t\t\t";
	}
	
	function displayThumbnail() 
	{
		echo "<img src=\"".get_link("/images/thumbnail/".$this->id)."\" />";
	}
	
}
?>
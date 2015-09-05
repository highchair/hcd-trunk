<?php
class Blog_Entries extends ModelBase
{
	/*
	 * A pretty useful FindAll function if I say so myself
	 * @accepts two values which are allowed to be empty and one boolean
	 * @uses MyActiveRecord FindBySQL
	 * @returns an object
	 *
	 * $excludeddate can be used as other types of queries, and a few other
	 * functions take advantage of this, like FindByYear()
	 */
	function FindAll( $excludedate='', $orderby='', $public=false ) {

		$query = 'SELECT * FROM blog_entries '; 
		if ( $public ) { $query .= 'WHERE public = 1 AND '; } else { $query .= 'WHERE '; }
		if ( empty($excludedate) ) { $query .= "date('".date("Y-m-d H:i:s")."') >= date(date) "; } else { $query .= $excludedate.' '; }
		if ( empty($orderby) ) { $query .= 'ORDER BY date DESC'; } else { $query .= 'ORDER BY '.$orderby; }
		
		return MyActiveRecord::FindBySQL( 'Blog_Entries', $query );
	}

	function FindById( $id ) {
		return MyActiveRecord::FindById('Blog_Entries', $id);
	}


	/* 
	 * Shortcut for finding only public entries
	 * @accepts an optional order clause
	 * @uses Blog_Entries::FindAll()
	 * @returns an array of objects
	 */
	function FindPublic( $orderby="date DESC" ) {
		return Blog_Entries::FindAll( '', $orderby, true ); 
	}


	/* 
	 * Shortcut for finding only the most recent entry
	 * @accepts an optional public declaration
	 * @uses Blog_Entries::FindAll()
	 * @returns one object
	 */
	function FindFirst( $public=true ) {
		return array_shift( Blog_Entries::FindAll( '', "date DESC LIMIT 1", $public ) ); 
	}


	/* 
	 * Shortcut for finding a certain number of entries
	 * @accepts a number of entries, optional order and public declarations
	 * @uses Blog_Entries::FindAll()
	 * @returns an array of objects
	 */
	function FindThisMany( $num = 5, $orderby = "date DESC", $public=true ) {
		$order_clause = $orderby." LIMIT ".$num; 
		return Blog_Entries::FindAll( '', $order_clause, $public ); 
	}


	/* 
	 * Finds all entries from a given year
	 * @accepts a year(numeric) and optional public declaration
	 * @uses Blog_Entries::FindAll()
	 * @returns an array of objects
	 */
	function FindByYear( $year, $public=true ) {

		if ( is_numeric($year) ) {
    		return Blog_Entries::FindAll( "'".$year."' = year(date) ", '', $public );
		} else { return false; }
	}


	/* 
	 * Returns an array of unique years for entries
	 * @accepts an optional public declaration
	 * @uses MyActiveRecord FindBySQL()
	 * @returns an array of (small) objects containing a new column called ->year
	 */
	function FindUniqueYears( $public=true ) 
	{
    	$public_condition = ( $public ) ? ' WHERE public = 1' : ''; 
    	return MyActiveRecord::FindBySQL( 'Blog_Entries', "SELECT id, year(date) as year FROM blog_entries{$public_condition} GROUP BY year DESC" ); 
	}


	/* 
	 * Returns a number of total pages based on the number of items in the DB and the number of items to display on each page
	 * Assumes public entries only (front-end facing function)
	 * @accepts a number of items expected for each page
	 * @uses Blog_Entries::FindAll()
	 */
	function FindLastPage( $perpage, $public=true )
	{
		// Get the total number of posts 
		$allentries = Blog_Entries::FindAll( '', '', $public ); 
		$rows = count( $allentries ); 

		// Find the page number of the last page 
		return ceil( $rows/$perpage ); 
	}


	/* 
	 * Gets a paginated range of items from the DB.
	 * @accepts a number of items expected for each page and the page number of the current request
	 * @uses FindLastPage() and MyActiveRecord::FindBySQL()
	 * There is no other exclusion date other than "today"
	 */
	function FindByPagination( $perpage, $pagenum, $public=true )
	{
		$last = Blog_Entries::FindLastPage( $perpage, $public ); 
		
		// Check to see if there is a page number. If not, set it to page 1 
		if ( !(isset($pagenum)) ) $pagenum = 1; 

		// Ensure the page number isn't below one, or more than the maximum pages 
		if ( $pagenum < 1 ) {
			$pagenum = 1; 
		} elseif ( $pagenum > $last ) { 
			$pagenum = $last; 
		} 

		// Sets the limit value for the query 
		$max = 'LIMIT ' . ( $pagenum - 1 ) * $perpage .', ' .$perpage;

		$query = 'SELECT * FROM blog_entries'; 
		$query .= ( $public ) ? " WHERE date('".date("Y-m-d H:i:s")."') >= date(date) AND public = 1" : ''; 
		$query .= ' ORDER BY date DESC '.$max; 

		return MyActiveRecord::FindBySQL( 'Blog_Entries', $query ); 
	}


	// Admin functions for date manipulation
	function setEntryDate($date_start = "", $time_start = "")
	{
		$this->date = parseDate($date_start . " " . $time_start);
	}

	function setEntryDateAndTime( $datetime = "" )
	{
		$this->date = parseDate( $datetime );
	}

	function getDateStart($part = "all")
	{
		switch ($part) {
		case "all":
			return formatDateTimeView( $this->date, "m/d/Y H:i:s" );
			break;
		case "date":
			return formatDateView( $this->date, "m/d/Y" );
			break;
		case "time":
			return formatTimeView( $this->date, "H:i:s" );
			break;
		}
		return formatDateTimeView( $this->date, "m/d/Y H:i:s" );
	}


	// Depreciate this
	function getContent() { $this->the_content(); }

	function rss_getContent() 
	{
		$content_to_display = $this->content;

		$content_to_display = document_display( $content_to_display );
		$content_to_display = image_display( $content_to_display ); 
		// Remove the rest of the tags
		$content_to_display = scrub_HCd_Tags( $content_to_display );
		
		return $content_to_display;
	}


	// Getting and attaching categories
	function getCategories()
	{
		return $this->find_linked('categories','','display_name ASC');
	}

	function getCategory()
	{
		// Can only grab the first one off the array of returned objects
		return array_shift( $this->getCategories() ); 
	}

	function updateSelectedCategories( $changed_cats )
	{
		$selected_cats = $this->getCategories();
		// look for newly checked categories
		foreach( $changed_cats as $changed_cat ) {
			$found = false;
			if ( is_array($selected_cats) ) {
				foreach( $selected_cats as $selected_cat ) {
					if( $selected_cat->id == $changed_cat )
					{
						$found = true;
					}
				}
			}
			if( !$found ) {
				$tmp_cat = Categories::FindById( $changed_cat );
				$this->attach( $tmp_cat );
			}
		}

		// look for deleted/unchecked categories
		foreach( $selected_cats as $selected_cat ) {
			if( !in_array( $selected_cat->id, $changed_cats ) ) {
				//the user has removed this area
				$tmp_cat = Categories::FindById( $selected_cat->id );
				$this->detach( $tmp_cat );
			}
		}
	}


	function getAuthor() {
		$author = Users::FindById( $this->author_id ); 
		if ( $author->display_name != "" ) : 
			return $author->get_title(); 
		else :
			$author->email; 
		endif; 
	}


	/* ! ===== Depreciate these in favor of display functions in model_base.php? ===== */

	/* ! - - - Functions for front-end display - - - */

	function chopForBlogDigest( $length, $buttonlanguage = "Read the Entire Post &rarr;" )
	{
		// Preserves HTML and HCd inserted photos and galleries, and breaks on the paragraph

		$content_to_chop = $this->get_content(); 

		if ( strlen($content_to_chop) > $length ) {
			$content_to_chop = substr( $content_to_chop, 0, strpos( $content_to_chop, "</p>", $length ) );
			$content_to_chop .= "\n<p><a class=\"readmore\" href=\"".get_link(BLOG_STATIC_AREA."/view/$this->id/$this->slug")."\">$buttonlanguage</a></p>\n"; 
		} 
		return $content_to_chop;
	}

	/* Use this on the object itself ( $this->chopShort() ). There is another function with a similar name in common/utility.php called chopText() */
	function chopShort( $length, $buttonlanguage = "Read More &rarr;" )
	{
		// Removes HTML and HCd inserted photos and galleries, and breaks on a space. It adds a link at the end of the string as well for a “Read More”.

		$content_to_chop = $this->get_content(); 

		if (strlen($content_to_chop) > $length) {
			$content_to_chop = strip_tags( $content_to_chop ); 
			$content_to_chop = scrub_HCd_Tags( $content_to_chop ); 
			$content_to_chop = substr( $content_to_chop, 0, strpos( $content_to_chop, " ", $length ) );
			$content_to_chop .= "... <a class=\"readmore\" href=\"".get_link(BLOG_STATIC_AREA."/view/$this->id/$this->slug")."\">$buttonlanguage</a>\n"; 
		} 
		return $content_to_chop;
	}
}
?>
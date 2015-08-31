<?php
class Galleries extends ModelBase
{
	function FindAll( $order = "ASC", $exclude_port = true )
	{
		if ( $exclude_port ) {
    		// Do not return Portfolio Galleries
    		return MyActiveRecord::FindAll('Galleries', " slug NOT LIKE 'portfolioGal_%' ", " id $order");
		} elseif ( $exclude_port == "all" ) {
    		// Return all Galleries
    		return MyActiveRecord::FindAll('Galleries', "", " id $order");
		} else {
			// Return only Portfolio Galleries
			return MyActiveRecord::FindAll('Galleries', " slug LIKE 'portfolioGal_%' ", " id $order");
		}
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Galleries', $id);
	}

	function FindBySlug($slug = "")
	{
		return MyActiveRecord::FindFirst('Galleries', "slug = '{$slug}'");
	}

	function FindByName($name = "")
	{
	    $name = mysql_real_escape_string($name, MyActiveRecord::Connection());
		return MyActiveRecord::FindFirst('Galleries', "name = '{$name}'");
	}
		
	function get_photos()
	{
		return MyActiveRecord::FindAll( 'Photos',"gallery_id = {$this->id}","display_order, id ASC" );
	}
	
	// Added Sept 2013 to manipulate attached videos to galleries
	function get_photos_and_videos()
	{
		/* Thanks again, Peter! Complimacated... 
		 * Near as I understand, we select from both tables and then use UNION to join the results. 
		 * The parens around the UNION let us sort on display_order. 
		 * For each col retrieved from one table that does not have an equivalent value in the other, 
		 * null needs to be set as the value or the query fails. 
		 *
		 * We use 'Videos' as the query base here, because that is where are the functions are for these objects
		 */
		return MyActiveRecord::FindBySql('Videos', 
"SELECT * from (
    SELECT g.id AS gallery_id,  'photo' AS type, p.id AS id, p.filename, p.caption, null AS display_name, null AS service, null AS embed, null AS width, null AS height, p.display_order FROM galleries g 
    	JOIN photos AS p ON p.gallery_id = g.id
    	WHERE g.id = {$this->id}
    UNION
    SELECT g.id AS gallery_id, 'video' AS type, v.id AS id, null AS filename, null AS caption, v.display_name, v.service, v.embed, v.width, v.height, v.display_order FROM galleries g 
    	JOIN videos AS v ON v.gallery_id = g.id 
    	WHERE g.id = {$this->id}
) AS u ORDER BY display_order" );
        /* Resulting table looks like this: 
         * gallery_id | type (photo|video) | id | filename | caption | title | service | embed, | width | height | display_order
         * Any results need to be looped on and check the 'type' to figure out how to render the results
         */
	}
	
	/* Added August 2013 for Jeff Carpenter and the larger ability to add Videos like Docs and Images to Galleries
	 * @usage: $gallery_object->get_videos()
	 */
	function get_videos( $orderby='display_order ASC' )
	{
		return MyActiveRecord::FindBySql('Videos', "SELECT * FROM videos WHERE gallery_id = ".$this->id." ORDER BY ".$orderby."");
	}
	
	function has_video()
	{
    	// If we find a video attached to this gallery, return true
    	$possvids = $this->get_videos(); 
    	return ( count($possvids) > 0 ) ? true : false; 
	}
	
	function get_first_photo()
	{
		// This *could* return a Video object if it was first
		return MyActiveRecord::FindFirst( 'Photos',"gallery_id = {$this->id}","display_order, id ASC" );
	}
	
	function get_thumb()
	{
		return MyActiveRecord::FindFirst('Photos', "gallery_id = {$this->id}","id ASC");
	}
	
	function delete()
	{
		foreach($this->get_photos() as $photo)
		{
			$photo->delete(true);
		}
		return MyActiveRecord::Query("DELETE FROM galleries WHERE id = {$this->id}");
	}
	
	function number_of_photos()
	{
		// Also counts videos if they are present
		return count( $this->get_photos() );
	}
	
	/* Added July 13, 2013
	 * Expected to find an item object by exploding the slug of the gallery
	 * Returns false if the gallery slug indicates that this gallery is not attached to an Item
	 */
	function get_item() {
    	// The gallery slug pattern is "portfolioGal_".$item->id."_".$item->name; 
    	$sploded = explode( '_', $this->slug ); 
    	if ( $sploded[0] == 'portfolioGal' ) {
        	$item = Items::FindById( $sploded[1] ); 
        	if ( is_object($item) )
        	    return $item; 
    	} else {
        	return false; 
    	}
	}
	
	// Depreciated. Templates need to use their own functions to do this work. 
	function display_gal($galType="gallery", $tabs="", $prevnext=true, $shuffle=false, $link=false)
	{
		$photosFromGal = $this->get_photos(); 
		
		if ($shuffle) 
		{
			shuffle($photosFromGal);
		}
		$gal = $tabs."<div class=\"$galType\">\n$tabs\t<ul>\n"; 
		foreach($photosFromGal as $photo)
		{
			if ($link)
			{
				$gal .= $tabs."\t\t<li><a class=\"image\" href=\"{$photo->getPublicUrl()}\" title='".cleanupSpecialChars($photo->caption)."' ><img src=\"{$photo->getPublicUrl()}\" alt=\"slideshow\" /></a></li>\n";
			} else {
				$gal .= $tabs."\t\t<li><img src=\"{$photo->getPublicUrl()}\" alt=\"slideshow\" /></li>\n";
			}
		}
		$gal .= $tabs."\t</ul>\n";
		
		if ($galType == "carousel" && $prevnext)
		{
			$gal .= $tabs."\t<a href=\"javascipt:;\" class=\"next\">&gt;</a>\n$tabs\t<a href=\"javascipt:;\" class=\"previous\">&lt;</a>\n"; 
		}
		$gal .= $tabs."</div>\n";
		
		return $gal; 
	}
	
	/* 
	 * Returns a number of total pages based on the number of items in the DB and the number of items to display on each page
	 * @accepts a number of items expected for each page
	 */
	function FindLastPage( $perpage )
	{
		// Get the total number of posts 
		$allgalleries = Galleries::FindAll(); 
		$rows = count( $allgalleries ); 
		
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
	
		$last = Galleries::FindLastPage( $perpage ); 
		
		// Ensure the page number isn't below one, or more than the maximum pages 
		if ( $pagenum < 1 ): 
			$pagenum = 1; 
		elseif ( $pagenum > $last ): 
			$pagenum = $last; 
		endif;  
		
		// Sets the limit value for the query 
		$max = 'LIMIT ' . ( $pagenum - 1 ) * $perpage .', ' .$perpage;
		
		return MyActiveRecord::FindBySQL( 'Galleries', "SELECT * FROM galleries ORDER BY id DESC $max" ); 
	}
}
?>
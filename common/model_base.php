<?php
class ModelBase extends MyActiveRecord {

	// Added February 1, 2012 - Make it easier to escape names all the time. Use the WordPress standard of "get" and "the" for return and echo, respectively. 
	
	// Some objects have a slug in the DB, some don't. Check them as we go through. 
	function get_slug() {
		if ( array_key_exists('name', $this) ) {
			// Areas, Pages, Items, Sections, Categories, Products, Videos, Event Types
			return esc_html( $this->name ); 
        } else if ( array_key_exists('title', $this) ) {
            // Events
            return slug( $this->title ); 
        } else if ( array_key_exists('slug', $this) ) {
            // Blog entries, Galleries
            return $this->slug; 
        } else {
            return null; 
        }
	}
	function the_slug() { echo $this->get_slug(); }
	
	
	/* 
	 * Return the title for the object... works on all object types 
	 */
	function get_title( $before="", $after="" ) {
		if ( array_key_exists('display_name', $this) ) {
			//  Areas, Pages, Items, Sections, Categories, Products
			return $before . strip_tags( $this->display_name ). $after; 
		} else if ( array_key_exists('title', $this) ) {
			// Blog Entries, Videos, Events
			return $before . strip_tags( $this->title ). $after; 
		} else if ( array_key_exists('name', $this) ) {
			// Blog, Calendar, Documents
			return $before . strip_tags( $this->name ). $after; 
		} else {
            return ""; 
        }
	}
	function the_title( $before="", $after="" ) { echo $this->get_title( $before, $after ); }
	
	
	/*
	 * Return a title for an Area or other object, and truncate to something SEO friendly
	 * You have 55 characters (including spaces) for title tags and 115 characters (including spaces) for meta descriptions.
	 */
    function get_seo_title( $append='' ) {
        
        if ( empty($append) ) { 
            $charlimit = 54; // account for the added '...' character
        } else {
            $charlimit = 54 - strlen( $append ); 
        }
        $seotitle = ''; 
        
        if ( get_class($this) == "Areas" ) {
            // Areas
			if ( strlen($this->seo_title) > 1 ) {
    			$seotitle = strip_tags( $this->seo_title ); 
			} else {
    			$seotitle = strip_tags( $this->display_name ); 
			}
        } else {
            // For every other object, use the standard function
            $seotitle = $this->get_title(); 
        }
        
        if ( strlen($seotitle) > $charlimit ) {
    		return substr( $seotitle, 0, $charlimit ).'&hellip;' . $append; 
        } else {
            return $seotitle . $append; 
        }
    }
    function the_seo_title() { echo $this->get_seo_title(); }
    
	
	/* 
	 * Return a thumbnail for the object... only works on those with thumbnail support 
	 */
	function get_thumbnail( $imgtag=true, $fullhttp=false ) {
    	if ( get_class($this) == "Items" ) {
        	if ( $imgtag ) {
            	return "<img src=\"".get_link("/portfolio/thumbnail/".$this->id)."\" alt=\"".$this->get_title()."\">"; 
        	} else {
            	return get_link("/portfolio/thumbnail/".$this->id); 
        	}
    	} elseif ( get_class($this) == "Blog_Entries" ) {
        	// $image should be a Photos object
        	$image = $this->getImage(); 
        	if ( is_object($image) ) {
            	if ( $imgtag ) {
                	return "<img src=\"".$image->getPublicUrl( $fullhttp )."\" alt=\"".$image->caption."\">"; 
            	} else {
                	return $image->getPublicUrl( $fullhttp ); 
            	}
        	} else { return false; }
    	}
	}
	function the_thumbnail( $imgtag=true ) { echo $this->get_thumbnail( $imgtag ); }
	
	
	/* 
	 * Return a URL for the object... Have to do this on an object type by object type basis 
	 * Should only be run on objects that we KNOW are public
	 */
	function get_url( $thisarea="", $thissection="", $rooturl=false )
	{
		$link = ( $rooturl ) ? "http://".SITE_URL.BASEHREF : BASEHREF; 
		$link .= ( REWRITE_URLS ) ? "" : "?id="; 
		
		// Areas
		if ( get_class($this) == "Areas" )
		{
			$link .= $this->name; 
		}
		// Pages
		elseif ( get_class($this) == "Pages" )
		{
			if ( empty($thisarea) ) {
				$area = array_shift( $this->getAreas() ); 
			} else {
    			$area = $thisarea; 
            }
			if ( $area->name == "" ) {
				$link .= $this->name; 
			} else {
				$link .= $area->name."/".$this->name; 
			}
		}
		// Sections
		elseif ( get_class($this) == "Sections" )
		{
			if ( empty($thisarea) )
			{
				$thisarea = $this->thePortfolioArea(); 
			}
			$link .= $thisarea->name."/".$this->name; 
		}
		// Items
		elseif ( get_class($this) == "Items" )
		{
			if ( empty($thissection) )
			{
				$thissection = $this->theSection(); 
			}
			if ( empty($thisarea) )
			{
				$thisarea = $thissection->thePortfolioArea(); 
			}
			if ( ITEM_ID_IN_URL ) {
				$link .= $thisarea->name."/".$thissection->name."/".$this->id."/".$this->name; 
			} else {
				$link .= $thisarea->name."/".$thissection->name."/".$this->name; 
			}
		} 
		// Blog Entries
		elseif ( get_class($this) == "Blog_Entries" )
		{
			$link .= BLOG_STATIC_AREA."/view/".$this->id."/".$this->slug; 
		}
		// Blog Categories
		elseif ( get_class($this) == "Categories" )
		{
			$link .= BLOG_STATIC_AREA."/category/".$this->name; 
		}
		// Events
		elseif ( get_class($this) == "Events" )
		{
    		list($month,$day,$year) = explode( "/", $this->getDateStart("date") );
    		$link .= CALENDAR_STATIC_AREA."/".CALENDAR_STATIC_PAGE."/$year/$month/$day/$this->id/".$this->get_slug(); 
		}
		// Images 
		elseif ( get_class($this) == "Images" )
		{
    		$link .= "images/view/".$this->id; 
        }
        // Photos
        elseif ( get_class($this) == "Photos" )
		{
    		$link .= PUBLIC_DOCUMENTS_ROOT . 'gallery_photos/' . $this->filename;  
        }
		else { return null; }
		
		return $link; 
	}
	function the_url( $thisarea="", $thissection="", $rooturl=false ) { echo $this->get_url( $thisarea, $thissection, $rooturl ); }
	
	
	/* 
	 * Returns content for the object... Have to do this on an object type by object type basis 
	 * Converts all the possible short codes as well. 
	 */
	function get_content()
	{
		if ( array_key_exists('content', $this) ) {
			$content = $this->content;
		} elseif ( array_key_exists('description', $this) ) {
			$content = $this->description; 
		} else {
			return null; 
		}
		
		if ( isset( $content ) ) {
			// The order in which these run is important. 
			$content = document_display($content); // searching for {{document:name_of_doc{{ pattern
			$content = video_display($content); // searching for {{video:name_of_vid{{ pattern
			$content = email_display($content);	/// Need to run email before we insert them from a product
			$content = product_display($content);	// searching for {{product:name_of_product{{ pattern
			$content = image_display($content); // searching for {{name_of_image{{ pattern
			$content = gallery_display($content);  // searching for {{galery:name_of_gal{{ pattern
			$content = carousel_display($content); // searching for {{carousel:name_of_gal{{ pattern
			$content = random_from_gallery_display($content);	 // searching for {{random-from-gallery:name_of_gal{{ pattern
			$content = testimonial_display($content); // searching for {{testimonial:name_of_test{{ pattern
			
			return $content; 
		}
	}
	function the_content() { echo $this->get_content(); }
	
	
	/* 
	 * Return an excerpt for the content... 
	 * Removes HTML markup and shortcodes before parsing for length
	 * Use "</p>" for the stop character to keep paragraphs intact
	 */
	function get_excerpt( $length, $stopchar=" ", $trailing_char='&hellip;' ) 
	{
		// We want it RAW and unprocessed, so don't use get_content(); 
		if ( array_key_exists('excerpt', $this ) ) {
    		// Excerpt might be empty, so fall back to content
    		$content = ( ! empty($this->excerpt) ) ? $this->excerpt : $this->content; 
		} elseif ( array_key_exists('content', $this) ) {
			$content = $this->content;
		} elseif ( array_key_exists('description', $this) ) {
			$content = $this->description; 
		} else {
			return null; 
		}
		
		if ( ! empty( $content ) ) {
    		// ChopText strips HCd tags and all HTML
    		$chopped = chopText( $content, $length, $stopchar ); 
    		if ( strlen($chopped) > $length ) { $chopped = $chopped.$trailing_char; } 
    		return $chopped; 
		} else { return null; }
	}
	function the_excerpt( $length, $stopchar=" ", $trailing_char='&hellip;' ) { echo $this->get_excerpt( $length, $stopchar, $trailing_char ); } 
	
	
	/* 
	 * Return a SKU for the item... 
	 * Should only be run on items that we KNOW are public - does not check it internally.
	 */
	function get_SKU()
	{
		if ( ! empty($this->sku) ) {
			return esc_html( $this->sku ); 
		}
	}
	function the_SKU() { echo $this->get_SKU(); }
	
	
	/*
	 * Returns the current page's URL in a safe way
	 */
    function get_this_URL( $rooturl=true ) {
        $current_url = ( $rooturl ) ? 'http://' . SITE_URL . BASEHREF : BASEHREF; 
        $current_url .= ( REWRITE_URLS ) ? "" : "?id="; 
        
        $counter = 0; 
        
        // Longest possible URL pattern is an event, 7 parts: /events/calendar/2013/20/15/$id/$slug/
        while ( $counter < 7 ) {
            if ( getRequestVarAtIndex( $counter ) != "" ) {
                $current_url .= getRequestVarAtIndex( $counter ) . '/'; 
            }
            $counter++; 
        }
        return $current_url; 
    }
    function this_URL( $rooturl=true ) { echo $this->get_this_URL( $rooturl ); }
    
    
    /* 
	 * Returns an Author's display_name for a blog entry object.
	 */
	function get_author()
	{
		// Make sure it is a blog entry object with the author_id column
		if ( array_key_exists('author_id', $this) ) {
			$author_id = $this->author_id;
    		if ( ! empty( $author_id) ) {
    			$author = Users::FindById( $author_id ); 
    			return $author->get_username(); 
    		}
		} else { return null; }
	}
	function the_author() { echo $this->get_author(); }
	
	
	/* 
	 * Returns a username for a user object.
	 */
	function get_username()
	{
	    $username = strip_tags( $this->display_name ); 
	    if ( ! empty($username) ) {
    	    return $username; 
	    } else {
    	    list( $username, $domain ) = explode( '@', $this->email );
    	    return $username; 
	    }
	}
	function the_username() { echo $this->get_username(); }
	
	
	/* 
	 * Returns a Proper Name or an Email address for a user object.
	 */
	function get_name_or_email()
	{
	    $username = strip_tags( $this->display_name ); 
	    if ( ! empty($username) ) {
    	    return $username; 
	    } else {
    	    return $this->email; 
	    }
	}
	function the_name_or_email() { echo $this->get_name_or_email(); }
	
	
	/* 
	 * Returns a formatted Date for a blog entry object. 
	 * Can be modified to work with Events, but events have more parameters to worry about
	 */
	function get_pubdate( $format="g:ia, F j, Y" )
	{
		if ( get_class($this) == "Blog_Entries" ) {
			return formatDateView( $this->date, $format ); 
		} else { return null; }
	}
	function the_pubdate( $format="g:ia, F j, Y" ) { echo $this->get_pubdate( $format ); }
	
	
	/* 
	 * Returns a list of categories or a single category for a blog entry object. 
	 * Option for the seperator character
	 */
	function get_categories( $separator=', ', $hide_uncategorized=true, $as_string=false ) 
	{
	    if ( get_class($this) == "Blog_Entries" ) {
    	    $striplength = 0 - strlen($separator); 
    	    $categories = $this->getCategories(); 
    	    $categorylist = ''; 
        	
        	foreach ( $categories as $thecat ) {
            	$url_start = ( $as_string ) ? '' : '<a href="'.get_link( BLOG_STATIC_AREA."/category/".$thecat->name ).'">';
            	$url_end = ( $as_string ) ? '' : '</a>'; 
            	
            	if ( $hide_uncategorized ) {
                	if ( $thecat->name != 'uncategorized' )
                    	$categorylist .= $url_start.$thecat->display_name.$url_end.$separator; 
                } else {
                    $categorylist .= $url_start.$thecat->display_name.$url_end.$separator; 
                }
        	}
        	// Remove the last seperator that was added on the last time through the loop
        	return substr( $categorylist, 0, $striplength );
        	
        } else { return null; }
	}
	function the_categories( $separator=', ', $hide_uncategorized=true ) { echo $this->get_categories( $separator, $hide_uncategorized ); }
	
	
	/* 
	 * Functions that help figure out what kind of page/object we are dealing with 
	 * Most of these look directly at the current URL, not the area or page objects present (because they are not always present)
	 
	 * These won't work yet... not doing something right. Failures right away. 
	 
	 */
	function is_blog() 
	{
        return ( getRequestVarAtIndex() == "blog" && ( getRequestVarAtIndex(1) == "view" || getRequestVarAtIndex(1) == "category") ) ? true : false; 
	}
	
	function is_category() 
	{
        return ( getRequestVarAtIndex() == "blog" && getRequestVarAtIndex(1) == "category" ) ? true : false; 
	}
	
	function is_paged() 
	{
        return ( getRequestVarAtIndex() == "blog" && getRequestVarAtIndex(1) == "view" && getRequestVarAtIndex(2) == "page" ) ? true : false; 
	}
}

?>
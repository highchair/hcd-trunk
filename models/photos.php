<?php
class Photos extends ModelBase
{
	/*
     * Columns: id, gallery_id, filename, caption, display_order, video_is, entry_id
     */
	function FindAll()
	{
		return MyActiveRecord::FindAll('Photos', NULL, " id ASC");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Photos', $id);
	}

	function FindByGallerySlug($slug)
	{
		return MyActiveRecord::FindBySql('Photos', "SELECT p.* FROM photos p INNER JOIN galleries g on g.id = p.gallery_id WHERE g.slug like '{$slug}'");
	}
	
	function FindByGalleryId($gallery_id)
	{
		return MyActiveRecord::FindBySql('Photos', "SELECT p.* FROM photos p INNER JOIN galleries g on g.id = p.gallery_id WHERE g.id = {$gallery_id} ORDER BY id DESC");
	}
	
	// Added Nov 2013 for Lumetta and the Blog Entries model
	function FindEntryImage( $entry_id ) 
	{
    	// Should only ever be one image per entry
    	$photo = array_shift( MyActiveRecord::FindBySql('Photos', "SELECT * FROM photos WHERE entry_id = {$entry_id}") ); 
    	if ( ! empty($photo) )
        	return $photo;
	}
	
	// Added Sept 2013 for Jeff Carpenter and the videos model
	function FindVideoPoster( $video_id ) 
	{
    	// Should only ever be one Poster per video
    	return array_shift( MyActiveRecord::FindBySql('Photos', "SELECT * FROM photos WHERE video_id = {$video_id}") );
	}

	function getPublicUrl( $withroot=false )
	{
		$link = ""; 
		if ( $withroot ) {
            $link .= "http://".SITE_URL; 
        }
        $link .= BASEHREF . PUBLIC_DOCUMENTS_ROOT . 'gallery_photos/' . $this->filename;
        return $link; 
	}
	
	function delete()
	{
		$target_path = $this->get_local_image_path($this->filename);
		if(file_exists($target_path))
		{
			unlink($target_path);
		}	  
		return MyActiveRecord::Query( "DELETE FROM photos WHERE id ={$this->id}" );
	}
	
	// This is silly... reconstruct the function to allow the pass through of max dimensions
	// Actually, we should also refactor IMAGES anyway and remove any saving to the DB of them. 
	// This function could become more multi-purpose and move into Common or maybe it is best here. ??
	function save_uploaded_file($tmp_name, $file_name, $isportimg = false, $isentryimg = false, $maxwidth=0, $maxheight=0)
	{		
		$filetype = getFileExtension($file_name); 
		$file_name = slug( basename($file_name, $filetype) ); 
		$new_file_name = $this->id . "-" . $file_name.'.'.$filetype;
		
		move_uploaded_file( $tmp_name, $this->get_local_image_path($new_file_name) );
		
		chmod( $this->get_local_image_path($new_file_name), 0644 );
        
		$max_width = 0;
		$max_height = 0;
		
		if ( $maxwidth != 0 ) {
    		$max_width = $maxwidth; 
		} elseif ( $maxheight != 0 ) {
    		$max_height = $maxheight; 
		} elseif ( $isportimg ) {
			
			if(defined("MAX_PORTFOLIO_IMAGE_HEIGHT"))
			{
			    $max_height = MAX_PORTFOLIO_IMAGE_HEIGHT;
			}
			if(defined("MAX_PORTFOLIO_IMAGE_WIDTH"))
			{
			    $max_width = MAX_PORTFOLIO_IMAGE_WIDTH;
			}		
		} elseif ( $isentryimg ) {
			
			if(defined("MAX_ENTRY_IMAGE_HEIGHT"))
			{
			    $max_height = MAX_ENTRY_IMAGE_HEIGHT;
			}
			if(defined("MAX_ENTRY_IMAGE_WIDTH"))
			{
			    $max_width = MAX_ENTRY_IMAGE_WIDTH;
			}		
		} else {
			if(defined("MAX_GALLERY_IMAGE_HEIGHT"))
			{
			    $max_height = MAX_GALLERY_IMAGE_HEIGHT;
			}
			if(defined("MAX_GALLERY_IMAGE_WIDTH"))
			{
			    $max_width = MAX_GALLERY_IMAGE_WIDTH;
			}
		}
		
		$this->filename = $new_file_name;
		$this->save(); 
		
		resizeToMultipleMaxDimensions( $this->get_local_image_path($new_file_name), $max_width, $max_height, $filetype );
		
		//$query = "UPDATE photos SET filename = $file_name WHERE id = {$this->id};";
		//return mysql_query( $query, MyActiveRecord::Connection() ) or die( $query ); 
	}
	
	
	function get_gallery()
	{
		return Galleries::FindById($this->gallery_id);
	}
	
	function get_local_image_path($file_name)
	{
		if( is_dir(SERVER_DOCUMENTS_ROOT . "gallery_photos/") ) {
    		return SERVER_DOCUMENTS_ROOT . "gallery_photos/" . $file_name;
		} else {
    		die ( throwError("&ldquo;gallery_photos&rdquo; is not a folder, does not have the right permissions, or is not in the right place") ); 
		}
	}
	
	function get_next_photo()
	{
		$order = $this->display_order;
		$next_photo = array_shift(MyActiveRecord::FindBySql('Photos',"SELECT * FROM photos WHERE gallery_id = {$this->gallery_id} AND display_order > {$this->display_order} ORDER BY display_order ASC LIMIT 1"));
		
		if(empty($next_photo))
		{
			// get the first photo
			$next_photo = array_shift(MyActiveRecord::FindBySql('Photos',"SELECT * FROM photos WHERE gallery_id = {$this->gallery_id} ORDER BY display_order ASC LIMIT 1"));
		}
		
		return $next_photo;
	}
	
	function get_previous_photo()
	{
		$order = $this->display_order;
		$prev_photo = array_shift(MyActiveRecord::FindBySql('Photos',"SELECT * FROM photos WHERE gallery_id = {$this->gallery_id} AND display_order < {$this->display_order} ORDER BY display_order DESC LIMIT 1"));
		
		if(empty($prev_photo))
		{
			// get the last photo
			$prev_photo = array_shift(MyActiveRecord::FindBySql('Photos',"SELECT * FROM photos WHERE gallery_id = {$this->gallery_id} ORDER BY display_order DESC LIMIT 1"));
		}
		
		return $prev_photo;
	}
	
	function setDisplayOrder()
	{
		$gallery = $this->get_gallery();
		$display_order = count(Photos::FindByGalleryId($gallery->id));
		$query = "UPDATE photos SET display_order = $display_order WHERE id = {$this->id} AND gallery_id = {$gallery->id};";
		mysql_query($query, MyActiveRecord::Connection());
		
	}
	
	function get_index_in_gallery()
	{
		$all_photos = Photos::FindByGalleryId($this->gallery_id);
		$count = 0;
		foreach($all_photos as $photo)
		{
			$count += 1;
			if($photo->id == $this->id)
			{
				return $count;
			}
		}
		return 0;
	}
	
	// New, added June 2015
	function is_portrait() {
    	$file = SERVER_DOCUMENTS_ROOT . 'gallery_photos/' . $this->filename;
    	list( $width, $height ) = getimagesize( $file ); 
    	
    	return ( $width < $height ) ? true : false; 
	}
	
	function is_landscape() {
    	$file = SERVER_DOCUMENTS_ROOT . 'gallery_photos/' . $this->filename;
    	list( $width, $height ) = getimagesize( $file ); 
    	
    	return ( $width > $height ) ? true : false; 
	}
}
?>

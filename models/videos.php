<?php
class Videos extends ModelBase
{
	
	/* Videos: A few things to remember...
	 * 
     * A video can only be attached to ONE gallery or Item with gallery
     *
	 * Also, when using a gallery to group photos and videos together, a few odd things happen. 
	 * The function Galleries::$this->get_photos_and_videos() returns one array of mixed results from the 
	 * Photos table and the Videos table. BUT, all are treated as a Video object.,
	 * Therefore, some functions here are duplicates of Photos functionality, because they get treated 
	 * as if they were Videos objects. See here: 
	 
	 [104] => Videos Object
        (
            [id] => 104
            [slug] => 
            [display_name] => 
            [service] => 
            [embed] => 
            [width] => 
            [height] => 
            [gallery_id] => 92
            [display_order] => 1
            [type] => photo ** See? This is actually a PHOTO! **
            [filename] => 104-studio-toseeif.jpg
            [caption] => 
        )

    [2] => Videos Object
        (
            [id] => 2
            [slug] => 
            [display_name] => Sweatpants Incident
            [service] => youtube
            [embed] => TgAQApJdUOE
            [width] => 0
            [height] => 360
            [gallery_id] => 92
            [display_order] => 1
            [type] => video ** See? This is actually a VIDEO! **
            [filename] => 
            [caption] => 
        )
	 
	 * Confusing, I know. 
	 */
	
	
	function FindAll( $orderby="display_name ASC" )
	{
		/*
		 * Columns: id, slug, display_name, service (youtube, vimeo), embed (shortcode or unique ID), width, height, gallery_id, display_order
		 */
		return MyActiveRecord::FindAll( 'Videos', NULL, $orderby ); 
	}
	
	function FindById( $id="" )
	{
		// Double check the id to see if it is numeric? Does MyActiveRecord already do that? 
		return MyActiveRecord::FindById( 'Videos', $id );
	}
	
	function FindByName( $name="" )
	{
	    $name = mysql_real_escape_string( $name, MyActiveRecord::Connection() );
		return MyActiveRecord::FindFirst( 'Videos', "slug = '{$name}'" );
	}
	
	function FindByService( $service="" )
	{
	    $service = mysql_real_escape_string( $service, MyActiveRecord::Connection() ); 
		return MyActiveRecord::FindBySql( 'Videos', "SELECT v.* FROM videos v WHERE v.service like '" . $service . "'" );
	}
    
    function FindByGalleryId( $galid="" )
	{
		$id = mysql_real_escape_string( $galid, MyActiveRecord::Connection() );
		return MyActiveRecord::FindBySql( 'Videos', "SELECT v.* FROM videos v WHERE v.gallery_id like '" . $galid . "'" );
	}
    
    function getGallery() {
    	$galid = $this->gallery_id; 
    	return Galleries::FindById( $galid ); 
	}
	
    
    /* ! ===== Display functions ===== */
    
	function embed_video( $autoplay=0 ) 
	{
    	/*
    	    http://youtu.be/tVUCsnMK18E
    	    
    	    Disable suggested videos (rel=0) and set autoplay to true
    	    <iframe width="560" height="315" src="//www.youtube.com/embed/tVUCsnMK18E?rel=0&autoplay=1" frameborder="0" allowfullscreen></iframe>
    	    
    	    http://developer.vimeo.com/player/embedding
    	    <iframe src="http://player.vimeo.com/video/VIDEO_ID?portrait=0&color=333" width="WIDTH" height="HEIGHT" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        */
        
        $width = ( $this->width != "" ) ? $this->width : '560'; 
        $height = ( $this->height != "" ) ? $this->height : '315'; 
        
    	switch ( $this->service ) {
            
            case "vimeo":
                //return '<iframe src="http://player.vimeo.com/video/'.$this->embed.'?portrait=0" width="100%" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                return '<div class="hcd-video embed-container"><iframe src="http://player.vimeo.com/video/'.$this->embed.'?portrait=0" width="100%" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>'; 
                break;
            
            default:
                // YouTube iFrame code for Flash and HTML5
                //return '<iframe width="100%" height="'.$height.'" src="//www.youtube.com/embed/'.$this->embed.'?rel=0&autoplay='.$autoplay.'" frameborder="0" allowfullscreen></iframe>'; 
                return '<div class="hcd-video embed-container"><iframe src="http://www.youtube.com/embed/'.$this->embed.'?rel=0&autoplay='.$autoplay.'" width="100%" height="'.$height.'" frameborder="0" allowfullscreen></iframe></div>';
                break;
        }
	}
	
	function getPublicUrl( $autoplay=0 ) 
	{
    	/* Clean way to control links for viewing the original source video on site
    	 * Does double duty... if this gallery contains Photos and Videos, we check for that here, too
    	 */
    	
    	if ( isset($this->type) && $this->type == 'photo' && ! empty($this->filename) ) {
        	// Can't use a Photo model function here, it is not a Photos object
        	return BASEHREF . PUBLIC_DOCUMENTS_ROOT . 'gallery_photos/' . $this->filename;
        	break; 
        	
    	} else {
        	
        	switch ( $this->service ) {
                
                case "vimeo":
                    return 'http://vimeo.com/'.$this->embed;
                    break;
                
                default:
                    return 'http://youtube.com/watch?v='.$this->embed.'?rel=0&autoplay='.$autoplay;
                    break;
            }
        }
	}
	
	function remove_from_content() 
	{
    	// Not Done or Tested yet
    	
    	// A function to cycle through content types and remove references when deleting a video
    	$videoPattern = "/{{2}(video:[A-Za-z0-9\-\_ \.\(\)'\"]+){{2}/";
    	$videoIds = getFilterIds($content_to_display, $videoPattern);
    	$videos = Array();
    	
    	foreach( $videoIds as $videoId )
    	{
    		$videos[] = Videos::FindByName( end(explode(":", $videoId)) );
    	}
    	
    	foreach( $videos as $thevid )
    	{
    		if( is_object($thevid) )
    		{
    			$replacement = $thevid->get_embed();
    			$content_to_display = updateContent( $content_to_display, "/{{2}video:".str_replace(")","\)",str_replace("(","\(",$thevid->name))."{{2}/", $replacement );
    		} else {
    			$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Video not found!</span> ".$content_to_display; 
    		}
    	}
    	return( $content_to_display );
	}
}
?>
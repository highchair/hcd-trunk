<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
	    $connection = MyActiveRecord::Connection();
	    
		$imageId = mysql_real_escape_string(requestIdParam());
	
		// TODO: use a parameterized query instead of an escaped string
		$query = "SELECT * FROM images WHERE id = $imageId";
		
		$result = mysql_Query($query, $connection);
		
		$data = @ mysql_fetch_array($result);
		
		if ( ! empty($data["original"]) )
		{
			// Output the MIME header
			header("Content-Type: {$data['mime_type']}");
			set_image_cache_headers( "img_".$imageId );
			
			// Output the image
			echo $data["original"];
		}
	}

?>
<?php
function initialize_page()
{
	
}

function display_page_content()
{
	$imageId = requestIdParam();
	
	$query = "SELECT * FROM portfolioimages WHERE id = $imageId";
	
	$result = mysql_Query($query, MyActiveRecord::Connection());
	
	$data = @ mysql_fetch_array($result);
	
	if ( ! empty($data["image"]) )
	{
		// Output the MIME header
		header("Content-Type: {$data["mime_type"]}");
		set_image_cache_headers( "portimg_".$imageId );
		
		// Output the image
		echo $data["image"];
	}
}
?>
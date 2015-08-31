<?php
function initialize_page()
{
	
}

function display_page_content()
{
	$itemId = requestIdParam();
	
	$query = "SELECT * FROM items WHERE id = $itemId";
	
	$result = mysql_Query($query, MyActiveRecord::Connection());
	
	$data = @ mysql_fetch_array($result);
	
	if ( ! empty($data["thumbnail"]) )
	{
		// Output the MIME header
		header("Content-Type: {$data['mime_type']}");
		set_image_cache_headers("portTimg_".$itemId");
		
		// Output the image
		echo $data["thumbnail"];
	}
}
?>
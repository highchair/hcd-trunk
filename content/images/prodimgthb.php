<?php
function initialize_page()
{
	
}

function display_page_content()
{
    $imageId = requestIdParam();
	
	$query = "SELECT thumbnail, mime_type FROM product WHERE id = $imageId";

    $result = mysql_Query($query, MyActiveRecord::Connection());

    $data = @ mysql_fetch_array($result);
    
    if ( ! empty($data["thumbnail"]) )
    {
    	// Output the MIME header
    	header("Content-Type: {$data['mime_type']}");
    	set_image_cache_headers( "prodTimg_".$imageId );
    	
    	// Output the image
    	echo $data["thumbnail"];
    }
}
?>
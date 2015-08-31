<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
	  $item_id = requestIdParam();

	  $query = "SELECT thumbnail FROM items 
	            WHERE id = $item_id";

	  $result = mysql_Query($query, MyActiveRecord::Connection());

	  $data = @ mysql_fetch_array($result);

	  if (!empty($data["thumbnail"]))
	  {
	    // Output the MIME header
	     header("Content-Type: image/jpeg");
	    // Output the image
	     echo $data["thumbnail"];
	   }
	}

?>
<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$imageId = requestIdParam();
		
		echo "<img src=\"".get_link( "images/thumbnail/".$imageId )."\" width=\"180\" alt=\"Preview thumbnail\" />"; 
	}

?>
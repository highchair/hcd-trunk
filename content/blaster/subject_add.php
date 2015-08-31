<?php
	function initialize_page()
	{
		if(IsUserLoggedIn()) { } else { redirect('/'); }
	}
	
	function display_page_content()
	{
		$array_name = requestIdParam();
		$value = getRequestVarAtIndex(3);
		
		$_SESSION['blaster'][$array_name] = $value;
		
		print_r($_SESSION['blaster']);
	}
?>
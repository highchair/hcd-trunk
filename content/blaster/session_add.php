<?php
	function initialize_page()
	{
		if(IsUserLoggedIn()) { } else { redirect('/'); }
	}
	
	function display_page_content()
	{
		$array_name = requestIdParam();
		if (strstr(getRequestVarAtIndex(3), ","))
		{
			$value = array_slice(explode(",", getRequestVarAtIndex(3)), 0, -1);
		} else {
			$value = getRequestVarAtIndex(3);
		}
		
		$_SESSION['blaster'][$array_name] = $value;
		
		//print_r($_SESSION['blaster']);	
	}
?>
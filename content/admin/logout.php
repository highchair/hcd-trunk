<?php
	function initialize_page()
	{
		$_SESSION[LOGIN_TICKET_NAME] = NULL;
		
		setFlash("<h3>You have successfully logged out</h3>");
		redirect("/admin/login");
	}
	
	function display_page_content()
	{
	} 
?>
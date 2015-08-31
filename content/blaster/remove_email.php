<?php
	function initialize_page()
	{
	}
	function display_page_content()
	{
		$the_email = requestIdParam();
		$the_list = getRequestVarAtIndex(3);
		$list = NLLists::FindById($the_list); 
		$email = NLEmails::FindByEmail($the_email);
		
		$email->detach($list);
	}
?>
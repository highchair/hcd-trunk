<?php
	function initialize_page() {}
	
	function display_page_content()
	{
		$hash = requestIdParam();
		$email = getRequestVarAtIndex(3);
		
		$query = "SELECT * FROM mailblast WHERE hash = '$hash';";
		
		if ($result = mysql_query($query, MyActiveRecord::Connection())) {
			echo str_replace("{{-email-}}", $email, mysql_result($result, 0, 'content'));
		} else {
			redirect("/");
		}
	}
?>
<?php
	function initialize_page()
	{
	}
	function display_page_content()
	{
		$removed_id = requestIdParam();
		$upcoming_events = $_SESSION['blaster']['upcoming_events'];	
		$new_array = array();
		foreach ($upcoming_events as $event)
		{
			if ($event != $removed_id) {
				$new_array[] = $event;
			}
		}
		if (count($new_array))
		{
			$_SESSION['blaster']['upcoming_events'] = $new_array;
		} else {
			$_SESSION['blaster']['upcoming_events'] = "";
		}
		//print_r($_SESSION['blaster']);
	}
?>
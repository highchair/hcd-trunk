<?php
	function initialize_page()
	{
	}
	function display_page_content()
	{
		$removed_id = requestIdParam();
		$ongoing_events = $_SESSION['blaster']['ongoing_events'];	
		$new_array = array();
		foreach ($ongoing_events as $event)
		{
			if ($event != $removed_id) {
				$new_array[] = $event;
			}
		}
		if (count($new_array))
		{
			$_SESSION['blaster']['ongoing_events'] = $new_array;
		} else {
			$_SESSION['blaster']['ongoing_events'] = "";
		}
		print_r($_SESSION['blaster']);
	}
?>
<?php
	function initialize_page()
	{
	}
	function display_page_content()
	{
		$listname = getRequestVarAtIndex(2);
		switch($listname)
		{
		case "portfolio":
			foreach ($_POST as $ordered_objects => $order_value)
			{
				// splits up the key to see if we are ordering a section, item or ignoring a portfolio area
				$ordered_parts = explode("_", $ordered_objects);
				
				// NOTICE: I have learned that when there are portfoli orphans, this reordering script breaks. I removed the hidden fields in the Orphans section, but check in on that if you notice reordering breaking again. 
				
				//$debug = "";
				
				if ($ordered_parts[0] != "PortFolioAreas")
				{
					if ($ordered_parts[0] == "SectionOrder")
					{
						$section = Sections::FindById($ordered_parts[1]);
						$section->display_order = $order_value;
						$section->save();
						//$debug .= $section->display_name." updated"; 
					} else {
						$section = Sections::FindById($ordered_parts[0]);
						$item = Items::FindById($ordered_parts[1]);
						$item->updateOrderInSection($section, $order_value);
						//$debug .= $item->display_name." updated"; 
					}
				}
				//setFlash( "<h3>".$debug."</h3>" ); 
    			//setFlash( "<h3>".var_export( $_POST, true )."</h3>" ); 
			}
			break;
		case "areaspages":
			foreach ($_POST as $ordered_objects => $order_value)
			{
				// splits up the key to see if we are ordering a section, item or ignoring a portfolio area
				$ordered_parts = explode("_", $ordered_objects);
				//$debug = ""; 
				
				if ($ordered_parts[0] == "AreaOrder")
				{
					$area = Areas::FindById($ordered_parts[1]);
					$area->display_order = $order_value;
					$area->save();
					//$debug .= "$area->display_name updated"; 
				} else if ($ordered_parts[0] == "SubPage") {
					$page = Pages::FindById($ordered_parts[1]);
					$page->display_order = $order_value;
					$page->save();
					//$debug .= "$page->display_name sub page updated"; 
				} else {
					$area = Areas::FindById($ordered_parts[0]);
					$page = Pages::FindById($ordered_parts[1]);
					$page->updateOrderInArea($area, $order_value);
					//$debug .= "$page->display_name updated in $area->display_name"; 
				}
			}
			//setFlash( "<h3>".$debug."</h3>" ); 
			//setFlash( "<h3>".var_export( $_POST, true )."</h3>" ); 
			
			break;
		}
	}
?>
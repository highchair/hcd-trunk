<?php
	$GLOBALS["ROUTES"] = array();
	
	
	function addRoute($area_name = "", $page_name = "", $template_name = "", $content_file = "")
	{
		$routes = $GLOBALS["ROUTES"];
		
		if($area_name == "" && $page_name == "" && $template_name == "" && $content_file == "")
		{
			return;
		}
		
		$path = "/" . $area_name . "/" . $page_name;
		
		$routes[$path] = (array("template" => $template_name, "file" => $content_file));
		
		$GLOBALS["ROUTES"] = $routes;
		
		return;
	}
?>
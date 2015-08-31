<?php
function override_template()
	{
		$section_name = requestIdParam();
		$section = Sections::FindByName($section_name);
		return $section->template;
	}
	
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$section_name = requestIdParam();
		$section = Sections::FindByName($section_name);
		$item_name = getRequestVarAtIndex(3);
		$item = Items::FindByName($item_name);
		$gallery = $item->getGallery();
		
?>

		<h1><?php echo $item->display_name; ?></h1>
<?php
		$next_item = $item->findNext($section);
		$prev_item = $item->findPrev($section); 
		 
		if ($prev_item)
		{
			echo "\t\t\t\t<a href=\"".get_link("portfolio/item/$section_name/".$prev_item->name)."\">previous</a>\n"; 
		}
		if ($next_item)
		{
			echo "\t\t\t\t<a href=\"".get_link("portfolio/item/$section_name/".$next_item->name)."\">next</a>\n"; 
		}

		echo $item->content;

		foreach ($gallery->get_photos() as $photo) {
			echo "<img src=\"/".$photo->getPublicUrl()."\" /><br />";
		}
	}
?>
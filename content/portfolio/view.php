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
		$items = $section->findItems();
		
		?>
		
		<div id="right">
			<h3>Item Thumbnail</h3>
		</div>
		<div id="center">
			<h3>All the <?php echo $section->display_name; ?></h3>
			<?php echo $section->content; ?>
		<?
		echo "<ul>\r\n";
		foreach($items as $item)
		{
		   echo "\t\t\t<li><a href=\"".get_link("portfolio/item/$section_name/".$item->name)."\">{$item->display_name}</a></li>\r\n";
		}
		echo "\t\t</ul>\r\n";
		?>
		</div>
		<?php
	}
?>
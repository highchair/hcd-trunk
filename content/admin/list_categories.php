<?php	
	function initialize_page()
	{
	
	}
	
	function display_page_content()
	{
		$categories = Categories::FindAll();
?>
	
	<div id="edit-header" class="entrynav">		
		<div class="nav-left column">
			<h1>Edit Blog Categories</h1>
		</div>
		<div class="nav-right column">
			<a class="hcd_button" href="<?php echo get_link("/admin/add_category/") ?>">Add a Category</a>
		</div>
		<div class="clearleft"></div>
	</div>

	<div id="table-header" class="entries">
		<strong class="item-link">Category Name</strong>
		<span clsass="public">Entry Count</span>
	</div>
	
	<ul id="listitems" class="managelist">
	<?php
		foreach($categories as $cat)
		{
			$entries = $cat->getEntries(); 
			$count = count($entries); 
			$count_display = ( $count == 0 ) ? "&ndash;" : $count; 
			echo "\t\t<li>
				<a class=\"item-link\" href=\"".get_link("/admin/edit_category/$cat->id")."\">$cat->display_name</a>
				<span class=\"public\">" . $count_display . "</span>
			</li>\n"; 
		}
?>

	</ul>
<?php 
	}
?>
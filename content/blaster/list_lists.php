<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$lists = NLLists::FindAll();
?>

	<div id="edit-header" class="blaster">
		<div class="nav-left column">
    		<h1>Edit Mailing Lists</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_list") ?>" class="hcd_button">Add a New Mailing List</a> 
		</div>
		<div class="clearleft"></div>
	</div>
					
	<p>Click on the list name to edit it.</p>
	<ul id="list_items" class="managelist">
<?php
	if ( count($lists) > 0 ) {
    	foreach($lists as $list) { 
    		echo "\t\t<li><a class=\"item-link\" href=\"" . get_link("/admin/edit_list/$list->id") . "\">$list->display_name</a></li>\n";
    	}
	} else {
    	echo "<h3 class=\"empty-list\">There are no lists to edit. <a class=\"short\" href=\"".get_link("admin/add_list")."\">Add one if you like</a>.</h3>"; 
	}
?>

	</ul>
<?php } ?>
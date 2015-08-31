<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
?>

    <div id="edit-header" class="eventtypelist">		
		<div class="nav-left column">
			<h1>Edit Existing Event Types</h1>
		</div>
		<div class="nav-right column">
			<a class="hcd_button" href="<?php echo get_link( "admin/add_type" ) ?>">Add an Event Type</a>
		</div>
		<div class="clearleft"></div>
	</div>
	<p class="announce"><b>Event Colors:</b> Each Event Type is associated with a color that you may customize. We will setup colors that work with the color palette of your website and they will be displayed on the calendar where the event is listed.</p>
	
	<div id="table-header" class="eventlist">
		<strong class="item-link">Click Name to Edit</strong>
		<span class="item-filename">Color Preview</span>
	</div>
	<ul id="listitems" class="managelist">
<?php
	$types = EventTypes::FindAll();
	
	foreach($types as $type)
	{ 
		echo "\t\t\t<li><a class=\"item-link\" href=\"" . get_link("/admin/edit_type/$type->id") . "\">$type->name</a> <span class=\"colorpreview\" style=\"background-color: $type->color; color: $type->text_color;\">$type->name</span></li>\n"; 
	}
?>
	
	</ul>

<?php } ?>
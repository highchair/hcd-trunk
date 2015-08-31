<?php
	
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Type" || $post_action == "Add and Return to List" )
		{
			$type = MyActiveRecord::Create('EventTypes');
			$type->name = $_POST['name'];
			$type->color = $_POST['color'];
			$type->text_color = EventTypes::$color_array[$type->color];
			$type->calendar_id = 1;
			
			$type->save();
		
			setFlash("<h3>Event type created</h3>"); 
			if ( $post_action == "Add and Return to List" )
                redirect( "admin/list_event_types" ); 
		}
	}
	
	function display_page_content()
	{
		$types = EventTypes::FindAll();
?>

<script type="text/javascript">
	$().ready(function() {
		$("#add_color").validate({
			rules : {
				name: "required",
				color: "required"
			},
			messages: {
					name: "Please enter an name for this event type.",
					color: "Please select a color."
				}
		});
		$("#color_picker table td").click(function() {
			var thecolor = $(this).attr('bgcolor');
			$('.colorselected').css("background-color",thecolor);
			$('input.thecolor').val(thecolor);
		});
	});
</script>

<div id="edit-header" class="eventtype">
	<h1>Add Event Type</h1>
</div>
	
<form method="POST" id="add_color">
	
	<div class="column half">
		<p class="display_name">
            <label for="name">Name: </label>
    		<?php textField("name", "", "required: true"); ?>
		</p>
    </div>
    <div class="column half last">
		<p>
    		<label for="colorselected">Color: </label>
    		<span class="colorselected">&nbsp;</span>
		</p>
	</div>
	<div class="clearleft"></div>
	
	<p><label for="color">Select New Color from Array: </label>
		<input class="thecolor" type="hidden" name="color" value="white" />
		<?php require_once(snippetPath("color-picker")); ?>
	</p>
	
	<p>&nbsp;</p>
	
	<h2>Other Event Types for comparison</h2>
	<div id="table-header" class="eventlist">
		<strong class="item-link">Click Name to Edit</strong>
		<span class="item-filename">Color Preview</span>
	</div>
	<ul id="listitems" class="managelist">
<?php
	foreach($types as $thetype)
	{ 
		echo "\t\t\t\t\t\t<li><a class=\"item-link\" href=\"" . get_link("/admin/edit_type/$thetype->id") . "\">$thetype->name</a> <span class=\"colorpreview\" style=\"background-color: $thetype->color; color: $thetype->text_color;\">$thetype->name</span></li>\n"; 
	}
?>
	
	</ul>
	
	<div id="edit-footer" class="eventtypenav clearfix">
		<div class="column half">
	
			<p>
				<input type="submit" class="submitbutton" name="submit" value="Add Type" /> <br />
				<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
			</p>
			
		</div>
		<div class="column half last"></div>
	</div>
	
</form>
<?php } ?>
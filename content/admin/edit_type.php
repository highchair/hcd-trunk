<?php
	function initialize_page()
	{
		$type_id = requestIdParam();
		$type = EventTypes::FindById($type_id);
	
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Save Type" || $post_action == "Edit and Return to List" )
		{
			if(isset($_POST['delete']))
			{
				$type->updateEventTypes();
				$type->delete(true);
				setFlash("<h3>Event type deleted</h3>");
				redirect("/admin/list_event_types");
			}
			else
			{
				$type->name = $_POST['name'];
				$type->color = $_POST['color'];
				$type->text_color = EventTypes::$color_array[$type->color];
		
				$type->save();
		
				setFlash("<h3>Event type changes saved</h3>");
				
				if ( $post_action == "Edit and Return to List" )
                    redirect( "admin/list_event_types" ); 
			}
		}
	}
	
	function display_page_content()
	{
		$types = EventTypes::FindAll();
		
		$type_id = requestIdParam();
		$type = EventTypes::FindById($type_id); 
		
		$user = Users::GetCurrentUser(); 
?>

<script type="text/javascript">
	$().ready(function() {
		$("#edit_type").validate({
			rules : {
				name: "required"
			},
			messages: {
					name: "Please enter an name for this event type.<br/>"
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
	<h1>Edit Event Type</h1>
</div>

<form method="POST" id="edit_type">
	
	<div class="column half">
		<p class="display_name">
            <label for="name">Name: </label>
    		<?php textField("name", $type->name, "required: true"); ?>
		</p>
    </div>
    <div class="column half last">
		<p>
    		<label for="colorselected">Color: </label>
    		<span class="colorselected" style="background-color:<?php echo $type->color; ?>">&nbsp;</span>
		</p>
	</div>
	<div class="clearleft"></div>
	
	<p><label for="new_color">Select New Color:</label> <span class="hint">Click any color below to select it, then click the Save button below.</span>
		<input class="thecolor" type="hidden" name="color" value="<?php echo $type->color; ?>" />
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
				<input type="submit" class="submitbutton" name="submit" value="Save Type" /> <br />
				<input type="submit" class="submitbuttonsmall" name="submit" value="Edit and Return to List" />
			</p>
			
		</div>
		<div class="column half last">
			
		<?php if( $user->has_role() && $type->id != 1 ) { ?>
			
			<p><label for="delete">Delete this Event Type?</label>
    		<input name="delete" class="boxes" type="checkbox" value="<?php echo $type->id ?>" />
    		<span class="hint">Check the box and click &ldquo;Save&rdquo; to delete this type from the database</span></p>
		<?php } ?>
		
		</div>
	</div>
	
</form>
<?php } ?>
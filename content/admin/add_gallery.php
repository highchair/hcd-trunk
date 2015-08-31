<?php
	function initialize_page()
	{
		$post_action = "";
		if( isset($_POST['submit']) )
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Gallery" )
		{
			$gallery = MyActiveRecord::Create('Galleries');
			
			$gallery->name = $_POST['name'];
			$gallery->slug = slug($_POST['name']);
			$gallery->save();
				
			setFlash( "<h3>Gallery Added</h3>" );
			redirect( "/admin/edit_gallery/".$gallery->id );
		}
	}
	
	function display_page_content()
	{
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#add_gallery").validate({
				rules: {
					name: "required"
				},
				messages: {
					name: "Please enter a name for this gallery<br/>"
				}
			});
		});
	</script>
	
	<div id="edit-header" class="documentnav">
		<h1>Add Gallery</h1>
	</div>
	
	<form method="POST" id="add_gallery">
		
		<p class="announce"><strong>Instructions:</strong> Name the Gallery first, then use the <a href="<?php echo get_link( "admin/list_galleries" ) ?>">List Galleries</a> page to edit and add photos to the gallery.</p>
		<p>&nbsp;</p>
		
		<p class="display_name">
			<label for="name">Display Name:</label>
			<span class="hint">This is the Proper Name of the gallery.</span><br />
			<?php textField("name", "", "required: true"); ?>
		</p>
		
		<div id="edit-footer" class="gallerynav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Gallery" /> <br />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
		
	</form>
<?php } ?>
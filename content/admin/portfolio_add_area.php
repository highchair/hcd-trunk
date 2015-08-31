
<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Add New Area" || $post_action == "Add and Return to List")
		{
			$area_name = slug($_POST['display_name'])."-portfolio";
			$area_display_name = $_POST['display_name'];
			$area_seo_title = $_POST['seo_title'];
			$area_content = $_POST['area_content'];
			$area_template = 'portfolio';
			$area_public = 0; 
		
			$new_area = MyActiveRecord::Create('Areas', array( 'name'=>$area_name, 'display_name'=>$area_display_name, 'seo_title'=>$area_seo_title, 'content'=>$area_content, 'template'=>$area_template, 'public'=>$area_public));
			$new_area->save();
						
			setFlash("<h3>New portfolio area added</h3>");
			if ($post_action == "Add and Return to List") {
				$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
				redirect( $main_portlink ); 
			}
		}
	}
	
	function display_page_content()
	{
		// get all the sections
		$sections = Sections::FindPublicSections();
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#add_area").validate({
				rules : {
					display_name: "required"
				},
				messages: {
					display_name: "Please enter a name you would like to be displayed for this area"
				}
			});
		});
	
	</script>
	
	<div id="edit-header" class="portareanav">
		<h1>Add Portfolio Area</h1>
	</div>
	
	<form method="POST" id="add_area">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<?php textField( "display_name", "", "required: true" ); ?>
			<span class="hint">This is the Proper Name of the area; how it will display in the navigation. Keep it simple, but use capitals and spaces, please. </span>
		</p>
        
        <p>
			<label for="seo_title">Title:</label>
			<?php textField("seo_title"); ?><br />
			<span class="hint">This title is used in title meta tags (good for SEO). Might also show when a user hovers their mouse over a link. </span>
		</p>
        
		<p><label for="area_content">Portfolio Area Description (optional):</label>
			<?php textArea("area_content", "", 98, EDIT_WINDOW_HEIGHT); ?>
		
		</p>
<?php require_once( snippetPath("admin-insert_configs") ); ?>
		
		<div id="edit-footer" class="portareanav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add New Area" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
				<p>To ensure that a new Portfolio Area does not become public without any Sections inside of it, a new Area will automatically be set to <strong>Not Public</strong>. Add some Sections and edit this Portfolio Area later to make it public and visible. </p>
			</div>
		</div>
		
	</form>
<?php } ?>
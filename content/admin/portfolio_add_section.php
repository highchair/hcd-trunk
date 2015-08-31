<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Add New Section" || $post_action == "Add and Return to List")
		{
			$section_display_name = $_POST['display_name'];
			if (ALLOW_SHORT_PAGE_NAMES) {
				if ($_POST['name'] == "") {
					$section_name = slug($_POST['display_name']);
				} else {
					$section_name = slug($_POST['name']);
				}
			} else {
				$section_name = slug($_POST['display_name']); 
			}
			$section_content = $_POST['section_content'];
			$section_template = $_POST['template'];
			$section_public = isset($_POST['public']) ? 1 : 0;
		
			$new_section = MyActiveRecord::Create('Sections', array( 'name'=>$section_name, 'display_name'=>$section_display_name, 'template'=>$section_template, 'public'=>$section_public, "content"=>$section_content));
			$new_section->save();
			
			// synchronize the users area selections
			$selected_areas = array();
			if(isset($_POST['selected_areas']))
			{
				$selected_areas = $_POST['selected_areas'];
			} else {
				$selected_areas = array('2');
			}
			
			$new_section->updateSelectedAreas($selected_areas);
			
			setFlash("<h3>New section added</h3>");
			
			if ($post_action == "Add and Return to List") 
			{
				$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
				redirect( $main_portlink );
			} else {
				redirect("admin/portfolio_add_section/".$section->id);
			}
		}
	}
	
	function display_page_content()
	{
		$areas = Areas::FindPortAreas(); // get all Portfolio Areas, public or not
		$user = Users::GetCurrentUser();
?>

	<div id="edit-header" class="sectionnav">
		<h1>Add Portfolio Section</h1>
	</div>
	
	<form method="POST" id="add_section">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<?php textField("display_name", "", "required: true"); ?><br />
			<span class="hint">This is the Proper Name of the section; how it will display in the navigation. Keep it simple, but use capitals and spaces, please.</span>
		</p>

		<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>					
		<p>
			<label for="name">Name</label>
			<?php textField("name", "", "required: true"); ?><br />
			<span class="hint">This is the short name of the section for the link and the database. No spaces, commas, or quotes, please.</span>
		</p>
		<?php } ?>
		
	<?php if ($user->email == "admin@highchairdesign.com") { ?>
		<p><label for="template">Template:</label>
			<select id="template" name="template">
			<?php
				$templates = list_available_templates();
				foreach($templates as $template)
				{
					$text = $template;
					echo "<option value=\"$template\">$text</option>";
				}
			?>
			
			</select></p>
	<?php } else {
			hiddenField("template", "portfolio");
		}
	?>
		
		<p>
			<label for="public">Public?</label><input type="checkbox" name="public" class="boxes"/><br />
			<span class="hint">This determines whether or not the Section will appear in the navigation as a &ldquo;Public&rdquo; link. If this Section is not public, then no items within it &ndash; Public or not &ndash; will be visible. A nice way to create a new Section and make sure it works well is to make the Section NOT Public, and all the items within it Public, so one click can turn it all on when it is ready. </span>
		</p>
		
		<p>
			<label for="content">Section Description:</label>
			<?php textArea("section_content", "", 98, EDIT_WINDOW_HEIGHT); ?>
		
		</p>
<?php require_once(snippetPath("admin-insert_configs")); ?>		
		
<?php require_once(snippetPath("admin-portfolio-area")); ?>	

	
		<div id="edit-footer" class="sectionnav clearfix">
			<div id="error_container"></div>
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add New Section" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
				<p>To ensure that a new Section does not become public without any Items inside of it, a new section will automatically be set to <strong>Not Public</strong>. Add some Items and edit this Section later to make it public and visible. </p>
			</div>
		</div>
	</form>
	
	<script type="text/javascript">
		$().ready(function() {
			errorLabelContainer: $("#error_container"),
<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>					
			
			$("#add_section").validate({
					rules : {
						name: "required",
						display_name: "required",
						"selected_areas[]": "required"
					},
					messages: {
							name: "Please enter a name for this Section",
							display_name: "Please enter a display name for this Section",
							"selected_areas[]": "Almost forgot! Select at least one area to include the page in" 
						}
				});
		});
<?php } else { ?>					
		
			$("#add_section").validate({
					rules : {
						display_name: "required",
						"selected_areas[]": "required"
					},
					messages: {
							display_name: "Please enter a display name for this Section",
							"selected_areas[]": "Almost forgot! Select at least one area to include the page in" 
						}
				});
<?php } ?>

		});					
	</script>
	
<?php } ?>
<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Add Page" || $post_action == "Add and Return to List")
		{
			$page = MyActiveRecord::Create('Pages');
			
			$page->display_name = $_POST['display_name'];
			if (ALLOW_SHORT_PAGE_NAMES) {
				if ($_POST['name'] == "") {
					$page->name = slug($_POST['display_name']);
				} else {
					$page->name = slug($_POST['name']);
				}
			} else {
				$page->name = slug($_POST['display_name']); 
			}
			$page->content = $_POST['page_content'];
			$page->content_file = '';
			$page->template = $_POST['template'];
			$page->public = checkboxValue($_POST,'public');
		
			// synchronize the users area selections
			$selected_areas = array();
				if(isset($_POST['selected_areas']))
				{
					$selected_areas = $_POST['selected_areas'];
				}
				if(count($selected_areas) > 0) 
				{
				    $page->parent_page_id = null;
				}
				else 
				{
				    if($_POST['parent_page'] != "")
    				{
    					$page->parent_page_id = $_POST['parent_page'];
    				}
    				else
    				{
    					$page->parent_page_id = null;
    				}
				}
	
			if ( $page->save() && $page->updateSelectedAreas($selected_areas) && $page->setDisplayOrderInArea() )
				setFlash("<h3>Page Added</h3>");
			
			if($post_action == "Add and Return to List")
			{
				redirect("admin/list_pages"); 
			}
		}
	}
	
	function display_page_content()
	{
		// get all the areas
		$areas = Areas::FindAll();
		
		// I know MOST pages dont use the error_container anymore, but this one should! If the user uses any of the drop downs before they pick an Area, the page will not submit and the user will not be able to see the error.
?>

	<script type="text/javascript">
	//<![CDATA[
	    $().ready(function() {
			$("#add_page").validate({
				errorLabelContainer: $("#error_container"),
<?php if (SUB_PAGES) { ?>
	
				rules: {
					display_name: "required"
				},
				messages: {
					display_name: "Please enter a display name for this page"
				}
<?php } else { ?>

				rules: {
					display_name: "required",
					"selected_areas[]": "required"
				},
				messages: {
					display_name: "Please enter a display name for this page",
					"selected_areas[]": "Almost forgot! Select at least one area to include the page in" 
				}
<?php } ?>
			});
		});
	//]]>
	</script>

	<div id="edit-header" class="pagenav">
		<div class="nav-left column">
			<h1>Add Page</h1>
		</div>
		<div class="nav-right column">
			<?php quick_link(); ?>
			
		</div>
		<div class="clearleft"></div>
	</div>

	<form method="POST" id="add_page">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<span class="hint">This is the Proper Name of the page; how it will display in the navigation.</span><br />
			<?php textField("display_name", "", "required: true"); ?>
		</p>
	
		<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>
		<p>
			<label for="name">Short Name:</label>
			<span class="hint">This is the short name of the page, which gets used in the link. No spaces, commas, or quotes please.</span><br />
			<?php textField("name", ""); ?>
		</p>
		<?php } ?>
		
		<p>
			<label for="name">Public:</label>&nbsp; <?php checkBoxField("public"); ?>
			<span class="hint">This determines whether or not the page will be visible to the public.</span>
		</p>
		
		<p>
			<label for="page_content">Content:</label><br />
			<?php textArea("page_content", "", 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
	
<?php 
	require_once(snippetPath("admin-insert_configs")); 

	// We decided to hide templates from everyone except ourselves
	$thisuser = Users::GetCurrentUser(); 
	if ($thisuser->id == "1") { 
?>

		<p><label for="template">Template:</label>
			<select id="template" name="template">
				<?php
					$templates = list_available_templates();
					$templates[] = "";
					foreach($templates as $template)
					{
						$text = $template;
						if($text == "")
						{
							$text = "(inherit)";
						}
						
						echo "<option value=\"$template\">$text</option>\r\n";
					}
				?>
				
			</select>
		</p>
<?php } ?>
	
		<div id="edit-footer" class="pagenav clearfix">
			<div id="error_container"></div>
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Page" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
		
	</form>
<?php } ?>
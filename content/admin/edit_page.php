<?php
	function initialize_page()
	{
		$page_id = requestIdParam();
		$page = Pages::FindById($page_id);
	
		// get all the areas
		$areas = Areas::FindPublicAreas();
		
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Save Page" || $post_action == "Save and Return to List")
		{
			if(isset($_POST['delete']))
			{
				$page->delete(true);
				setFlash("<h3>Page deleted</h3>");
				redirect("/admin/list_pages");
			}
			else
			{
				$page->display_name = $_POST['display_name'];
				$oldname = $page->name;
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
				$page->template = $_POST['template'];
				$page->public = checkboxValue($_POST,'public');
				
				// Pages can either be directly assigned to areas, or assigned as a sub-page.
				// It's an either-or thing. For now, default to areas if they're selected (ie, if both selected, ignore the sub-page)
				// synchronize the users area selections
				$selected_areas = array();
				if(isset($_POST['selected_areas']))
				{
					$selected_areas = $_POST['selected_areas'];
				}
				if(count($selected_areas) > 0) 
				{
					$page->parent_page_id = null;
					$page->updateSelectedAreas($selected_areas);
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
				
				$page->save();
				
				$page->checkAlias($selected_areas, $oldname);
				
				setFlash("<h3>Success. Database Updated</h3>");
				
				if($post_action == "Save and Return to List")
				{
					redirect("admin/list_pages"); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		$page_id = requestIdParam();
		$page = Pages::FindById($page_id);

		// get all the areas
		$areas = Areas::FindAll();
		$page_areas = $page->getAreas();
		
		// I know MOST pages dont use the error_container anymore, but this one should! If the user uses any of the drop downs before they pick an Area, the page will not submit and the user will not be able to see the error.
?>

	<script type="text/javascript">
	//<![CDATA[
		$().ready(function() {
			$("#edit_page").validate({
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
					"selected_areas[]": "You unchecked an area and forgot to choose a new one! Select at least one area to include the page in. If you need to hide it, make it not public." 
				}
<?php } ?>
	
			});
		});
	//]]>
	</script>
	
	<div id="edit-header" class="areanav">
		<div class="nav-left column">
			<h1>Edit Page : <a href="<?php $page->the_url() ?>" title="View <?php $page->the_url() ?>">View Page</a></h1>
		</div>
		<div class="nav-right column">
			<?php quick_link(); ?>
			
		</div>
		<div class="clearleft"></div>
	</div>
	
	<form method="POST" id="edit_page">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label><span class="hint">This is the Proper Name of the page; how it will display in the navigation.</span><br />
			<?php textField("display_name", $page->display_name, "required: true"); ?>
		</p>
	<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>
		
		<p>
			<label for="name">Short Name:</label><span class="hint">This is the short name of the page, which gets used in the link. No spaces, commas, or quotes please.</span><br />
			<?php textField("name", $page->name); ?>
		</p>
	<?php } else { hiddenField("name", $page->name); ?>
	    <p class="page-url">Page URL: <span class="page-url"><?php echo 'http://' . SITE_URL . BASEHREF . "<mark>" . ltrim( $page->get_url(), "/") . "</mark>" ?></span></p>
	<?php } ?>
		
		<p>
			<label for="name">Public:</label>&nbsp; <?php checkBoxField("public", $page->public); ?>
			<span class="hint">This determines whether or not this page will be visible to the public.</span>
		</p>
		
		<p>
			<label for="page_content">Content:</label><br />
			<?php textArea("page_content", $page->content, 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
		
<?php 
	require_once(snippetPath("admin-insert_configs")); 
	
	// We decided to hide templates from everyone except ourselves
	$thisuser = Users::GetCurrentUser(); 
	if ($thisuser->id == "1") { 
?>
	
		<p>
			<label for="template">Template:</label>
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
						
						echo "<option value=\"$template\"";
						if($template == $page->template)
						{
							echo " selected=\"selected\"";
						}
						echo ">$text</option>\r\n";
					}
				?>
				
			</select>
		</p>
<?php 
	} else {	
		hiddenField("template", $page->template); 
	} 
?>
				
		<div id="edit-footer" class="pagenav clearfix">
			<div id="error_container"></div>
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Page" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
<?php 
	$user = Users::GetCurrentUser();
	if($user->has_role() && !in_array($page->id, explode(",",PROTECTED_ADMIN_PAGES)) ) { 
?>
	
				<p>
					<label for="delete">Delete this page?</label>
					<input name="delete" class="boxes" type="checkbox" value="<?php echo $page->id ?>" />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this page from the database</span>
				</p>
	<?php } else { ?>
		
				<p class="red">This page is being protected, it can not be deleted.</p>
	<?php } ?>
	
			</div>
		</div>
		
	</form>
<?php } ?>
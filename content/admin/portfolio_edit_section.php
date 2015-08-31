<?php
	function initialize_page()
	{
		$section_id = requestIdParam();
		$section = Sections::FindById($section_id);
	
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Edit Section" || $post_action == "Edit and Return to List")
		{
			if(isset($_POST['delete']))
			{
				$items = $section->findItems();
				$selected_sections = array('1');
				foreach ($items as $item)
				{
					$item->updateSelectedSections($selected_sections);
				}
				$section->delete(true);
				setFlash("<h3>Section deleted</h3>");
				//$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
				//redirect( $main_portlink ); 
				redirect( "admin/portfolio_list" );
			}
			else 
			{
				if (ALLOW_SHORT_PAGE_NAMES) {
					if ($_POST['name'] == "") {
						$section->name = slug($_POST['display_name']);
					} else {
						$section->name = slug($_POST['name']);
					}
				} else {
					$section->name = slug($_POST['display_name']); 
				}
				$section->display_name = $_POST['display_name'];
				$section->template = $_POST['template'];
				$section->content = $_POST['section_content'];
				$section->public = isset($_POST['public']) ? 1 : 0;
							
				$selected_areas = array();
				if(isset($_POST['selected_areas']))
				{
					$selected_areas = $_POST['selected_areas'];
				} else {
					$selected_areas = array('2');
				}
				
				$section->updateSelectedAreas($selected_areas);
				$section->save();
				
				setFlash("<h3>Section changes saved</h3>");
				
				if ($post_action == "Edit and Return to List") {
					//$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
    				//redirect( $main_portlink );
    				redirect( "admin/portfolio_list" );
				} 
			}
		}
	}
	
	function display_page_content()
	{
		$section_id = requestIdParam();
		$section = Sections::FindById($section_id);
		
		$user = Users::GetCurrentUser();
		
		// get all the areas
		$areas = Areas::FindPortAreas();
		//$page_areas = array();
		if ( is_object($section) )
    		$page_areas = $section->getPortfolioAreas();
?>

	<script type="text/javascript">
		loadTinyMce('section_content');
		$().ready(function() {
			errorLabelContainer: $("#error_container"),
			
<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>					
			$("#edit_section").validate({
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
<?php } else { ?>					
			$("#edit_section").validate({
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
	
	<div id="edit-header" class="sectionnav">
		<h1>Edit Portfolio Section : <a href="<?php $section->the_url() ?>" title="View <?php $section->the_url() ?>">View Section</a></h1>
	</div>
	
	<form method="POST" id="edit_section">
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<?php textField("display_name", $section->display_name, "required: true"); ?><br />
			<span class="hint">This is the Proper Name of the section; how it will display in the navigation.</span>
		</p>
		
		<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>					
		<p>
			<label for="name">Name</label>
			<?php textField("name", $section->name, "required: true"); ?><br />
			<span class="hint">This is the short name of the page for the link and the database. No spaces, commas, or quotes, please.</span>
		</p>
		<?php } ?>
		
	<?php
		if ($user->email == "admin@highchairdesign.com") { ?>
		
		<p><label for="template">Template:</label>
			<select id="template" name="template">
			<?php
				$templates = list_available_templates();
				foreach($templates as $template)
				{
					$text = $template;
					echo "<option value=\"$template\"";
					if($template == $section->template)
					{
						echo " selected=\"selected\"";
					}
					echo ">$text</option>";
				}
			?>
			
			</select></p>
	<?php } else {
			hiddenField("template", $section->template);
		}
	?>
		
		<p>
			<label for="public">Public:</label> <?php checkBoxField("public", $section->public); ?>
		</p>
		
		<p>
			<label for="content">Section Description:</label>
			<?php textArea("section_content", $section->content, 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
<?php require_once(snippetPath("admin-insert_configs")); ?>		
		
<?php require_once(snippetPath("admin-portfolio-area")); ?>						
		
		<div id="edit-footer" class="sectionnav clearfix">
			<div id="error_container"></div>
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Edit Section" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Edit and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
				
			<?php if($user->has_role()) { ?>
				
				<p><label for="delete">Delete this section? <input name="delete" id="de;ete" class="boxes" type="checkbox" value="<?php echo $section->id ?>"></label>
				<span class="hint">Check the box and click &ldquo;Edit&rdquo; to delete from the database</span></p>
			<?php } ?>
			
			</div>
		</div>
		
	</form>
<?php } ?>
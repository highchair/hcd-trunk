
<?php
	function initialize_page()
	{
		$area_id = requestIdParam();
		$area = Areas::FindById($area_id);
		
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Edit Area" || $post_action == "Edit and Return to List")
		{
			if(isset($_POST['delete']))
			{
				$sections = $area->find_linked('sections');
				$selected_sections = array('2');
				foreach ($sections as $section)
				{
					$section->updateSelectedSections($selected_sections);
				}
				$area->delete(true);
				setFlash("<h3>Portfolio Area Deleted</h3>");
				$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
				redirect( $main_portlink );
			}
			else 
			{
				if ($area->id != 2) { $area->name = slug($_POST['display_name']."-portfolio"); }
				$area->display_name = $_POST['display_name'];
				$area->seo_title = $_POST['seo_title'];
				$area->content = $_POST['area_content'];
				$area->public = isset($_POST['public']) ? 1 : 0;
				$area->template = $_POST['template'];
				
				$area->save();
			
				setFlash("<h3>Portfolio Area changes saved</h3>");
				if ($post_action == "Edit and Return to List") {
					$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
					redirect( $main_portlink ); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		$area_id = requestIdParam();
		$area = Areas::FindById($area_id);
		
		$user = Users::GetCurrentUser();
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
		<h1>Edit Portfolio Area : <a href="<?php $area->the_url() ?>" title="View <?php $area->the_url() ?>">View Area</a></h1>
	</div>
	
	<form method="POST" id="add_area">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<input type="text" name="display_name" value="<?php echo $area->display_name; ?>" class="required: true" /><br />
			<span class="hint">This is the Proper Name of the area; how it will display in the navigation. Keep it simple, but use capitals and spaces, please. </span>
		</p>
		
		<p>
			<label for="seo_title">Title (for SEO):</label>
			<?php textField( "seo_title", $area->seo_title ); ?><br />
			<span class="hint">This title is used in title meta tags (good for SEO). Might also show when a user hovers their mouse over a link. </span>
		</p>
		
		<div id="public" class="column half">
			<p>
				<label for="public">Public:</label>&nbsp; <?php checkBoxField("public", $area->public); ?><br />
				<span class="hint">This determines whether or not the Portfolio Area will appear in the navigation as a &ldquo;Public&rdquo; link. If this Portfolio Area is not public, then no sections within it &ndash; Public or not &ndash; will be visible. A nice way to create a new Portfolio Area is to make the Portfolio Area NOT Public, and all the sections within it Public, so one click can turn it all on when it is ready. </span>
			</p>
		
		</div>
		<div id="template" class="column half">
			<p><label for="template">Template:</label>
				<select id="template" name="template">
				<?php
					$templates = list_available_templates();
					foreach($templates as $template) {
						$text = $template;
				
						echo "<option value=\"$template\"";
						if($template == $area->template)
						{
							echo " selected=\"selected\"";
						}
						echo ">$text</option>";
					}
				?>
				
				</select><br />
				<span class="hint">When a Page inside this Area uses the template &ldquo;inherit&rdquo;, the Page will inherit this Area&rsquo;s template selection. So, changing this Template may change the display of all Pages within this Area. </span>
			</p>
		</div>
		<div class="clearleft"></div>
		
		<p><label for="area_content">Portfolio Area Description (optional):</label>
			<?php textArea("area_content", $area->content, 98, EDIT_WINDOW_HEIGHT); ?>
		
		</p>
<?php require_once(snippetPath("admin-insert_configs")); ?>
		
		
		<div id="edit-footer" class="portareanav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Edit Area" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Edit and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
				
			<?php if( $user->has_role() && !in_array( $area->id, explode(",", PROTECTED_ADMIN_AREAS) ) ) { ?>
				
				<p><label for="delete">Delete this Area? <input name="delete" id="delete" class="boxes" type="checkbox" value="<?php echo $area->id ?>"></label>
				<span class="hint">Check the box and click &ldquo;Edit&rdquo; to delete from the database. Any sections or items contained within will become orphans and will be shown in the list for reassignment.</span></p>
			<?php } else { ?>
		
				<p>This area is being protected, it can not be deleted.</p>
			<?php } ?>
			
			</div>
		</div>
		
	</form>
<?php } ?>
<?php
	function initialize_page()
	{
		$category_id = requestIdParam(); 
		$category = Categories::FindById( $category_id ); 
		
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
		
		if( isset($_POST['delete']) )
		{
			$category->delete(true);
			setFlash("<h3>Category Deleted</h3>");
			redirect("/admin/list_categories/");
		
		} else {
		
			if( $post_action == "Edit Category" || $post_action == "Edit and Return to List" )
			{
				$category->display_name = getPostValue('display_name');
				$category->name = slug( getPostValue('display_name') );
				$category->content = getPostValue( 'category_content' );
				
				$category->save();
					
				setFlash("<h3>Category Edited</h3>");
				if ( $post_action == "Edit and Return to List" )
					redirect("admin/list_categories/"); 
			}
		}
	}
	
	function display_page_content()
	{
		$category_id = requestIdParam(); 
		$category = Categories::FindById( $category_id ); 
?>

	<script type="text/javascript">
		//<![CDATA[
	    loadTinyMce("category_content");
		
		$().ready(function() {
			
			$("#category").validate({
				rules: {
						display_name: "required",
					},
				messages: {
						display_name: "Please enter a display name for this category",
					}
			});
		});
		//]]>
	</script>
	
	<div id="edit-header" class="categorynav">
		<h1>Add a Category</h1>
	</div>
	
	<form method="POST" id="add_blog">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<span class="hint">This is the display name of the category; how it will display in navigation.</span><br />
			<?php textField("display_name", $category->display_name, "required: true"); ?>
		</p>
		
		<p>
			<label for="category_content">Content (optional &ndash; not all templates may display this content):</label><br />
			<?php textArea("category_content", $category->content, 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
		
<?php require_once(snippetPath("admin-insert_configs")); ?>
					
		
		<div id="edit-footer" class="categorynav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Edit Category" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Edit and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
			<?php if ( $category->id == 1 ) { ?>
			
				<p class="red">Sorry, the default category can not be deleted.</p>
			<?php } else { ?>
				
				<p>
					<label for="delete">Delete this category?</label>
					<input name="delete" class="boxes" type="checkbox" value='<?php echo $category->id ?>' />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this category from the database</span>
				</p>
			<?php } ?>
			
			</div>
		</div>
		
	</form>
<?php } ?>
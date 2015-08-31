<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Category" || $post_action == "Add and Return to List" )
		{
			$category = MyActiveRecord::Create( 'Categories' );
			
			$category->display_name = getPostValue('display_name');
			$category->name = slug( getPostValue('display_name') );
			$category->content = getPostValue( 'category_content' );
			
			$category->save();
				
			setFlash("<h3>Category Added</h3>");
			if ( $post_action == "Add and Return to List" )
				redirect("admin/list_categories/"); 
		}
	}
	
	function display_page_content()
	{
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
			<?php textField("display_name", "", "required: true"); ?>
		</p>
		
		<p>
			<label for="category_content">Content (optional &ndash; not all templates may display this content):</label><br />
			<?php textArea("category_content", "", 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
		
<?php require_once(snippetPath("admin-insert_configs")); ?>
					
		
		<div id="edit-footer" class="categorynav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Category" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
		
	</form>
<?php } ?>
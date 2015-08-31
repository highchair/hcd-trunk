<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Add")
		{
			$blog = MyActiveRecord::Create('Blogs');
			
			$blog->name = $_POST['name'];
			$blog->slug = slug($_POST['name']);
			$blog->user_id = $_POST['user_id'];
			$blog->save();
				
			setFlash("<h3>Blog Added</h3>");
		}
	}
	
	function display_page_content()
	{
		$users = Users::FindAll();
?>

					<script type="text/javascript">
						loadTinyMce("blog_description");
						
						$().ready(function() {
							$("#add_blog").validate({
									rules: {
											name: "required",
											user: "required"
										},
									messages: {
											name: "Please enter a name for this blog<br/>",
											user: "Please select a user for this blog<br/>"
										}
								});
						});
						
						$().ready(function() {
							
						});
					</script>
					<form method="POST" id="add_blog">
						<h1>Add Blog</h1>
						<p>Instructions: <span class="hint">Name the Blog first, then use the <b>Add Entry</b> page to edit and add photos to the blog.</span></p>
						
						<p><label for="name">Display Name:</label>
						<span class="hint">This is the Proper Name of the blog.</span><br />
						<?php textField("name", "", "required: true"); ?></p>
						
						<p><label for="name">Select User:</label>
						<span class="hint">This is the User associated with the blog.</span><br />
						<?php 
							echo "\t\t\t\t\t\t<select name='user_id' id='user_id'>\n\t\t\t<option>Select User</option>\n";
							foreach ($users as $user)
							{
								echo "\t\t\t\t\t\t\t<option value='{$user->id}'>{$user->email}</option>\n";
							}
							echo "\t\t\t\t\t\t</select>"
						?>
						</p>
						
						<p><input type="submit" class="submitbutton" name="submit" value="Add" /><br />
						
						<div id="error_container"></div>
					</form>
<?php } ?>
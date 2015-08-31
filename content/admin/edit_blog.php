<?php
	// NEED to change and update this file.
	
	/* 
	 * Blogs are no longer associated with a particular user. There should ever only be one Blog (id = 1) and 
	 * we store the author ID along with the entries. 
	 * This doc needs to change to reflect, or get depreciated all together
	 */
	
	function initialize_page()
	{
		$cur_user = Users::GetCurrentUser();
		$blog = Blogs::FindByUser($cur_user->id);
		
		$post_action = "";
		if(isset($_POST['delete']))
		{
			$blog->delete(true);
			setFlash("<h3>Blog Deleted</h3>");
			redirect("/admin/list_pages");
		}
		
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Edit")
		{			
			$blog->name = $_POST['name'];
			$blog->slug = slug($_POST['name']);
			if (isset($_POST['user_id'])) { $blog->user_id = $_POST['user_id']; }
			$blog->save();
				
			setFlash("<h3>Blog Edited</h3>");
		}
	}
	
	function display_page_content()
	{
		$users = Users::FindAll();
		$cur_user = Users::GetCurrentUser();
		
		$blog = Blogs::FindByUser($cur_user->id);
?>

					<script type="text/javascript">
						loadTinyMce("blog_description");
						
						$().ready(function() {
							$("#add_blog").validate({
									rules: {
											name: "required"
										},
									messages: {
											name: "Please enter a name for this blog"
										}
								});
						});
					</script>
					
					<form method="POST" id="add_blog">
						<h1>Edit Blog User</h1>
						<p>Instructions: <span class="hint">Name the Blog first, then use the <b>Add Entry</b> page to add info to it.</span></p>
						
						<p><label for="name">Display Name:</label>
						<span class="hint">This is the Proper Name of the blog.</span><br />
						<?php textField("name", $blog->name, "required: true"); ?></p>
						
					<?php 
					if ($cur_user->has_role())
					{	?>
						<p><label for="name">User:</label>
						<span class="hint">This is the User associated with the blog.</span><br />
						<?php 
							echo "\t\t\t\t\t\t<select name='user_id' id='user_id'>";
							foreach ($users as $user)
							{
								if ($user->id == $blog->user_id) { $selected = "selected"; } else { $selected = ""; }
								echo "\t\t\t\t\t\t\t<option $selected value='{$user->id}'>{$user->email}</option>\n";
							}
							echo "\t\t\t\t\t\t</select>"
						?>
						</p>
					<?php
					} else {	?>
						<p><label for="name">User:</label> <?php 
							foreach ($users as $user)
							{
								if ($user->id == $blog->user_id) { echo $user->email; }
							}	?></p>
					<?php
					}	?>	
						<p><input type="submit" class="submitbutton" name="submit" value="Edit" /><br />
						
						<p id="delete"><label for="delete">Delete this blog?</label>
						<input name='delete' class='boxes' type='checkbox' />
						<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this blog from the database</span></p>
					</form>
<?php } ?>
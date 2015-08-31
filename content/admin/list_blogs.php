<?php	
	function initialize_page()
	{
	}
	
	function display_page_content()
	{
?>					
					<h1>Choose Blog to Edit</h1>
						
					<div class="listhead"><label>Click Name to Edit</label>Associated User</div>
					<ul id="list_items">							
<?php
					$blogs = Blogs::FindAll();
					
					foreach($blogs as $blog)
					{
						$user = Users::FindById($blog->user_id);
?>
						<li>
							<a href="<?php echo get_link("/admin/edit_blog/" . $blog->user_id); ?>"><?php echo $blog->name; ?></a>
							<?php echo $user->email; ?>
						</li>
<?php				}	?>
					</ul>
						
<?php } ?>
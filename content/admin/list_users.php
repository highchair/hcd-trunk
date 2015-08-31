<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
?>

    <div id="edit-header" class="usernav">
		<div class="nav-left column">
    		<h1>Choose a User to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_user") ?>" class="hcd_button">Add a New User</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<p class="announce">
	   <strong>User Roles:</strong> Different levels of user access can be defined here. This is a Default installation of the HCd Back-end system, so, there are two roles. Users are <strong>Admins</strong> or <strong>Non-admins</strong>. Default permissions are thus: Non-admins can not delete pages, events, documents, images, galleries, or products. Non-admins can not create or delete users, or edit the PayPal account information (if installed). Admins can do everything.
    </p>
					
	<div id="table-header">
		<strong class="item-link">User Email</strong>
		<span class="item-public">Name</span>
		<span class="item-path">Privileges</span>
	</div>
	<ul id="listusers" class="managelist">
<?php
	$users = Users::FindAll();
	
	foreach ($users as $user)
	{ 
		echo "\t\t<li><a class=\"item-link\" href=\"" . get_link("/admin/edit_user/$user->id") . "\">{$user->email} <small>EDIT</small></a>"; 
        $displayname = ( $user->display_name != "" ) ? $user->display_name : "(no name)"; 
        echo "<span class=\"item-public\">{$displayname}</span><span class=\"item-path\">";
		if ($user->is_admin)
		{
			echo "Admin"; 
		} else {
			echo "Non-admin"; 
		}
		echo "</span></li>\n"; 
	}
?>
	
	</ul>
<?php } ?>
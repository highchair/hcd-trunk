<?php 
	$adminpages = array(
		"list_paypal", "edit_paypal", 
		"add_user", "edit_user", "list_users",
		"options"
	);  
	$thispage = get_content_page();
	
	if ( in_array($thispage->name, $adminpages) ) { 
        $displayadmin = " opened";
        $openorclosed = " menu-close"; 
    } else { 
        $displayadmin = $openorclosed = ""; 
    }

	$thisuser = Users::GetCurrentUser();
	if( $thisuser->has_role() ) { ?>
					
					<h4><a id="adminbutton" class="openmenu<?php echo $openorclosed; ?>" href="#admincontrols">Admin Controls</a></h4>
					<div id="admincontrols" class="menudrop<?php echo $displayadmin; ?>">
					<?php if (PRODUCT_INSTALL) { ?>	
						<h4>Paypal</h4>
						<a href="<?php echo get_link("admin/list_paypal"); ?>">Edit Accounts</a>
					<?php } ?>
						
						<h4>Users</h4>	
						<a href="<?php echo get_link("admin/list_users"); ?>">Edit Users</a>
						<a href="<?php echo get_link("admin/add_user"); ?>">Add a New User</a>
						
						<h4>HCd&gt;CMS</h4>
						<a href="<?php echo get_link("admin/options"); ?>">Installed &amp; Available Options</a>
					</div>
<?php } // end if user is has admin role ?>

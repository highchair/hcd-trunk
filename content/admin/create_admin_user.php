<?php
	function initialize_page()
	{
		// if there's more than one user, don't do anything.
		$count = MyActiveRecord::Count('Users');
		
		if($count == 0)
		{
			$admin_user = MyActiveRecord::Create('Users', array( 'email'=>'admin@highchairdesign.com', 'password'=>sha1(SHA_SALT . 'hcd_admin'), 'is_admin'=>1));
			$admin_user->save();
		}
		
		redirect("/admin/");
	}
	
	function display_page_content()
	{ } ?>
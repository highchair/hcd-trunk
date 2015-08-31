<?php
	function initialize_page()
	{
		LoginRequired( "/admin/login/", array("admin") );
		
		$post_action = "";
		if( isset($_POST['submit']) )
		{
			$post_action = $_POST['submit'];
		}
        
		if( $post_action == "Add User" || $post_action == "Add and Send New User Email" )
		{
			$email = $_POST['email'];
			$password = $_POST['password'];
			$possible_space = strrpos( $password, " " );
			
			if( empty($email) || empty($password) )
			{
				setFlash("<h3>Please enter a username and/or password of at least 6 characters and no spaces</h3>");
			}
			else if ( $possible_space == true ) 
			{ 
                setFlash("<h3>No spaces are allowed in a password</h3>");
            }
			else if ( strlen( utf8_decode($password) ) < 6 ) 
			{
    			setFlash("<h3>A password should contain at least 6 characters and no spaces</h3>");
			}
			else
			{
				$count = MyActiveRecord::Count('Users',"email = '${email}'");
				
				if( $count > 0 )
				{
					$duplicate = Users::FindByEmail($email); 
					setFlash( "<h3>User already exists (see below)</h3>" );
					redirect( "/admin/edit_user".$duplicate->id );
				}
				else
				{
					$new_user = MyActiveRecord::Create('Users', $_POST);
					$new_user->hash_password();
					$new_user->is_admin = checkboxValue($_POST, 'is_admin');
					$new_user->is_staff = ( $new_user->is_admin ) ? 0 : 1;
					$new_user->save();
					$success = "User added";
					
					if( $post_action == "Add User and Send New User Email" )
					{
						$new_user->send_newuser_email( $_POST['password'] ); 
						$success .= " / Email Notification Sent";
					}
					setFlash( "<h3>".$success."</h3>" );
					redirect( "/admin/list_users" );
				}
				
			}
		}
	}
	
	function display_page_content()
	{
        $siteurl = explode('.', SITE_URL); 
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#add_user").validate({
				rules : {
					display_name: "required",
					email: "required",
					password: "required"
				},
				messages: {
					display_name : "Please enter a display name for this user",
					email: "Please enter an email for this user",
					password: "Please enter a password for this user"
				}
			});
		});
	</script>
	
	<div id="edit-header" class="usernav">
		<h1>Add User</h1>
	</div>
	
	<form method="POST" id="add_user">
		<p class="display_name">
			<label for="title">Display Name:</label>
			<?php textField("display_name", '', "required: true"); ?><br />
			<span class="hint">This is a proper name or nickname for the user &mdash; how they will display on the site.</span>
		</p>
		
		<p><label for="email">Email:</label>
		    <input type="email" id="email" name="email" value="" placeholder="you@<?php echo $siteurl[1].'.'.$siteurl[2] ?> or similar" autocorrect="off" autocapitalize="off" class="required: true" /><br />
			<span class="hint">The user will use this email address as their login information.</span>
        </p>
		
		<p><label for="password">Password:</label>
		    <input type="password" id="password" name="password" value="" placeholder="At least 6 characters, please" autocomplete="off" autocorrect="off" autocapitalize="off" class="required: true" /><br />
			<span class="hint">If you wish to reset a user&rsquo;s password, enter the new password here. If you leave this field blank, no changes will be made.</span><br /> Passwords do not have to contain fancy characters or numbers, but should not contain spaces &mdash; instead, we recommend a long phrase that you will remember easily. (Example: "elvislivesinmemphis". The longer the better)
		</p>
		
		<p class="announce">An &ldquo;Admin&rdquo; has Master Access &ndash; they can edit all features of the site. <b>If this box is not checked, the user is considered a Non-admin. Non-admins may NOT delete content, edit PayPal accounts or create new users.</b> They will be able to create or edit content, upload new images and documents, and create new galleries.</p>
    	<p>&nbsp;</p>
    	<h2>Privileges:</h2>
    	<hr noshade />
    	<p><label for="is_admin">Admin?</label>
    	<?php checkBoxField("is_admin", 0); ?></p>
				
		<div id="edit-footer" class="usernav clearfix">
    		<div class="column half">
    	
    			<p>
    				<input type="submit" class="submitbutton" name="submit" value="Add User" /> <br />
    				<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Send New User Email" />
    			</p>
    			
    		</div>
    		<div class="column half last"></div>
    	</div>
		
	</form>

<?php } ?>
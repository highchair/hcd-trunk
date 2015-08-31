<?php
	function initialize_page()
	{
		$user_id = requestIdParam();
		$user = Users::FindById($user_id);
	
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Edit User" || $post_action == "Edit and Return to List" )
		{
			if( isset($_POST['delete']) )
			{
				$user->delete(true);
				setFlash("<h3>User deleted</h3>");
				redirect("/admin/list_users");
			}
			else
			{
				$user->email = $_POST['email'];
				$user->display_name = $_POST['display_name'];
				$user->is_admin = isset($_POST['is_admin']) ? 1 : 0;
				$user->is_staff = ( $user->is_admin ) ? 0 : 1;
				
				$badpass = false; 
				
				if( isset($_POST['password']) && ! empty( $_POST['password'] ) )
				{
					$possible_space = strrpos( $_POST['password'], " " );
                    if ( $possible_space == true ) 
        			{ 
                        setFlash("<h3>No spaces are allowed in a password. Changes not saved</h3>");
                        $badpass = true; 
                    }
        			else if ( strlen( utf8_decode($_POST['password']) ) < 6 ) 
        			{
            			setFlash("<h3>A password should contain at least 6 characters and no spaces. Changes not saved</h3>");
            			$badpass = true; 
        			}
        			else 
        			{
            			$user->password = $_POST['password'];
    					$user->hash_password();
        			}
				}
				
				if ( ! $badpass ) {
    				$user->save();
    		
    				setFlash("<h3>User changes saved</h3>");
    				if ( $post_action == "Edit and Return to List" )
    				    redirect("admin/list_users"); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		$user_id = requestIdParam();
		$user = Users::FindById($user_id); 
		
		$thisuser = Users::GetCurrentUser(); 
		
    /*<style>
	.passmask { position: relative; }
	.hideshow { text-transform: uppercase; font-size: .75em; color: #0c2347; padding-right: .5em; position: absolute; top: .2em; right: 7%;padding: .9em; }
	</style>
	
    	<p><label for="password">Password:</label>
    	    <fieldset class="passmask">
        	    <input type="password" class="js-hide-show-password" id="password" name="password" value="" placeholder="To reset a password, enter at least 6 characters" autocomplete="off" autocorrect="off" autocapitalize="off" />
            </fieldset><br />
			Passwords do not have to contain fancy characters or numbers, but should not contain spaces &mdash; instead, we recommend a long phrase that you will remember easily. (Example: "elvislivesinmemphis". The longer the better)
		</p>*/
?>

	<div id="edit-header" class="usernav">
		<h1>Edit User</h1>
	</div>
	
	<form method="POST" id="edit_user">
		
		<p class="display_name">
			<label for="title">Display Name:</label>
			<?php textField("display_name", $user->display_name, "required: true"); ?><br />
			<span class="hint">This is a proper name or nickname for the user &mdash; how they will display on the site.</span>
		</p>
	
    	<p><label for="email">Email:</label>
    	    <input type="email" id="email" name="email" value="<?php echo $user->email ?>" autocorrect="off" autocapitalize="off" class="required: true" /><br />
        	<span class="hint">A user will use this email address as their login information.</span>
    	</p>
	
    	<p><label for="password">Password:</label>
    	    <input type="password" id="password" name="password" value="" placeholder="To reset a password, enter at least 6 characters" autocomplete="off" autocorrect="off" autocapitalize="off" /><br />
			Passwords do not have to contain fancy characters or numbers, but should not contain spaces &mdash; instead, we recommend a long phrase that you will remember easily. (Example: "elvislivesinmemphis". The longer the better)
		</p>

<?php 
	$thisuser = Users::GetCurrentUser();
	if( $thisuser->has_role() ) { ?>	
	
    	<p class="announce">An &ldquo;Admin&rdquo; has Master Access &ndash; they can edit all features of the site. <b>If this box is not checked, the user is considered a Non-admin. Non-admins may NOT delete content, edit PayPal accounts or create new users.</b> They will be able to create or edit content, upload new images and documents, and create new galleries.</p>
    	<p>&nbsp;</p>
    	<h2>Privileges:</h2>
    	<hr noshade />
    	<p><label for="is_admin">Admin?</label>
    	<?php checkBoxField("is_admin", $user->is_admin); ?></p>
	
<?php } ?>

        <div id="edit-footer" class="usernav clearfix">
    		<div class="column half">
    			<p>
    				<input type="submit" class="submitbutton" name="submit" value="Edit User" /> <br />
    				<input type="submit" class="submitbuttonsmall" name="submit" value="Edit and Return to List" />
    			</p>
    		</div>
    		<div class="column half last">
    			
    		<?php if ( $thisuser->has_role() ) { ?>
    			
    			<p><label for="delete">Delete this User?</label>
        		<input name="delete" class="boxes" type="checkbox" value="<?php echo $user->id ?>" />
        		<span class="hint">Check the box and click &ldquo;Edit&rdquo; to delete this user from the database</span></p>
    		<?php } ?>
    		
    		</div>
    	</div>
    		
    </form>
    
    <script type="text/javascript">
		$().ready(function() {
			$("#edit_user").validate({
					rules : {
						display_name: "required",
						email: "required"
					},
					messages: {
							display_name: "Please enter an email for this user",
							email: "Please enter an email for this user"
						}
				});
		});
		
		/* From PolarB.com and the great Luke Wroblewski
		 * Throws an error in browsers because prior to jQuery 1.9, changing the type of an input is not allowed (IE6/7/8 would error).
		 * Need to use jQuery 2.0 or 1.9 Migrate to get this to work 
		!function(){$(document).ready(function(){$(".js-hide-show-password").each(function(){var $input=$(this),$hideShowLink=$('<span class="hideshow js-hide-show-link">Show</span>');$hideShowLink.click(function(e){e.preventDefault();var inputType=$input.attr("type");"text"==inputType?($input.attr("type","password"),$hideShowLink.text("Show")):($input.attr("type","text"),$hideShowLink.text("Hide")),$input.focus()}),$input.parent().append($hideShowLink)})})}(); */
	</script>
	
<?php } ?>
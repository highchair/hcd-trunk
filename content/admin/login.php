<?php
	function initialize_page()
	{
		// If the user requested the login page but has a bookmarked place to go (or they were logged out while working)
		$optionalredirect = "";
    	if ( getRequestVarAtIndex(1) != "" && getRequestVarAtIndex(1) != " " && getRequestVarAtIndex(1) != "login" )
    		$optionalredirect .= getRequestVarAtIndex(1); 
    	if ( getRequestVarAtIndex(2) != "" && getRequestVarAtIndex(2) != " " )
    		$optionalredirect .= "/".getRequestVarAtIndex(2); 
    	if ( getRequestVarAtIndex(3) != "" )
    		$optionalredirect .= "/".getRequestVarAtIndex(3); 
    	if ( getRequestVarAtIndex(4) != "" )
    		$optionalredirect .= "/".getRequestVarAtIndex(4); 
    	if ( getRequestVarAtIndex(5) != "" )
    		$optionalredirect .= "/".getRequestVarAtIndex(5); 
    	if ( getRequestVarAtIndex(6) != "" )
    		$optionalredirect .= "/".getRequestVarAtIndex(6); 
		
		if( Users::GetCurrentUser() ) {
			// If the user is still logged in and they hit this page, redirect them to the homepage or somewhere else
			redirect( "/admin".$optionalredirect );
		}
		$post_action = "";
		if( isset($_POST['submit']) ) {
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Login" ) {
			$email = $_POST['email'];
			$password = $_POST['password'];
			
			if( $email == "" || $password == "" ) {
				setFlash("<h3>Please enter a username and password</h3>");
			
			} else {
				$user = Users::FindByEmail($email);
				
				if( is_object($user) ) {
				
    				/* Check to see if we are in maintenance mode */
    				if ( MAINTENANCE_MODE ) { 
        				if( $user->email != "admin@highchairdesign.com" ) {
        				    setFlash("<h3>We are sorry, the site is in maintenance mode right now. Please try to log-in later.</h3>");
        				} else {
            				if( $user->authenticate($password) ) {
        						$user->set_ticket();
        						
        						setFlash("<h3>Thank you for logging in during Maintenance Mode, Admin</h3>");
        						redirect( Users::GetRedirect( "/admin/".$optionalredirect ) );
    						}
        				}
        				
    				} else {
					
    					if( $user->authenticate($password) ) {
    						$user->set_ticket();
    				
                            // Backup database on successful Log in
                            $params = @parse_url( MYACTIVERECORD_CONNECTION_STR ) 
                            	or trigger_error( "MyActiveRecord::Connection() - could not parse connection string: ".MYACTIVERECORD_CONNECTION_STR, E_USER_ERROR );
                            
                            $backupDatabase = new Backup_Database( $params['host'], $params['user'], $params['pass'], trim( $params['path'], ' /' ) );
                            $success = $backupDatabase->backupDatabase( SERVER_DBBACKUP_ROOT, 5 ) ? 'The database has been archived.' : 'The database archive FAILED.';
                            setFlash("<h3>Thank you for logging in. $success</h3>");
    
    						redirect( Users::GetRedirect( "/admin/".$optionalredirect ) );
    											
    					} else {
    						$_SESSION[LOGIN_TICKET_NAME] = NULL;
    						setFlash("<h3>User not found, or password incorrect</h3>");
    					}
    				}
				}
			}
		}
	}
	
	function display_page_content()
	{
	    $welcome_message = ( MAINTENANCE_MODE ) ? 'The site is in Maintenance Mode. Only the Admin may log in.' : 'Login to the HCd&gt;CMS Admin'; 
?>
    
    <div id="edit-header" class="login">
		<h1>Welcome</h1>
	</div>
    			
	<form id="login" method="post">
		
		<!--[if lt IE 8]>
			<p><strong>For reasons beyond our capabilities of understanding, there are certain styles that simply do not work in old versions of Internet Explorer which we deem as very important for general usability and organization.</strong> Therefore, we suggest that you use a (better) browser like <a href="http://www.mozilla.com/en-US/">Mozilla&rsquo;s Firefox browser</a> to view and use the HCd Content Management system. <strong>If you choose to venture forth with an old version of Internet Explorer (5, 5.5, 6, or 7) as your browser, it is at your own risk to your mental health.</strong></p>
		<![endif]-->
		
		<p>
			<label for="email">Email:</label>
			<input type="text" name="email" id="email" value="" autocapitalize="off" />
		</p>
		
		<p>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" value="" autocorrect="off" autocapitalize="off" />
		</p>
		
		<p><input type="submit" class="submitbutton" name="submit" value="Login" /></p>
		
	</form>
<?php } ?>
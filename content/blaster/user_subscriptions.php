<?php
function initialize_page()
{
	$post_action = "";
	if(isset($_POST['submit'])) { $post_action = $_POST['submit']; }
	
	if($post_action == "Manage This Email") {
		redirect("users/manage/".$_POST["email"]);  
	}
	
	if($post_action == "Save Subscription Settings") {
		
		$currentemail = $_POST["email"]; 
		$oldemail = $_POST["oldemail"];
		if ( $currentemail != $oldemail ) {
    		$thisemail = NLEmails::FindByEmail($oldemail);
        } else {
            $thisemail = NLEmails::FindByEmail($currentemail);
        }
		
		$lists = NLLists::FindAll();
		
		// Remove all links first...
		$query = "DELETE FROM nlemails_nllists WHERE nlemails_id = $thisemail->id";
		mysql_query($query, MyActiveRecord::Connection());
		
		if (isset($_POST['delete'])) {
			$thisemail->delete(true);
			redirect("/mail/subscribe/deleted"); 
		}
		
		// Then add the ones selected back in...
		foreach ($lists as $list) {
			if (array_key_exists($list->name, $_POST)) {
				$list->attach($thisemail);
			}
		}
		
		// Set the optional info fields and allow them to change the email they subscribed with... 
		$thisemail->email = $_POST["email"]; 
		$thisemail->first_name = $_POST["first_name"]; 
		$thisemail->last_name = $_POST["last_name"]; 
		$thisemail->address1 = $_POST["address1"]; 
		$thisemail->address2 = $_POST["address2"]; 
		$thisemail->city = $_POST["city"]; 
		$thisemail->state = $_POST["state"]; 
		$thisemail->zip = $_POST["zip"]; 
		$thisemail->phone = $_POST["phone"]; 
		$thisemail->save(); 
		
		setFlash("<h3>Subscription Settings Saved</h3>");
		// If they changed their email, redirect them to that page
		redirect("users/manage/".$thisemail->email); 
	}
}

function display_page_content()
{
	$useremail = requestIDparam(); 
	// Check email by getting it from the database. Find the subscriptions associated with it. If it is not a valid email in our database, then do not display the interface below. Instead, maybe we redirect them to the home page. 
	
	$email = NLEmails::FindByEmail($useremail);
	$lists = NLLists::FindAll();
	
	echo '<div class="subscriber"><div id"status_message"></div>'; 
	
	displayFlash(); 
	
	echo '<h1 class="subscriber--title">Manage your Newsletter Subscriptions</h1>'; 
	
	// There is no email that we can take out of the URL string
	if ($useremail == "") {
?>

			<h3 class="subscriber--subtitle subscriber--error">An email was not detected&hellip; please enter one below:</h3>
			
			<form class="form form__subscriber-profile" method="POST">
				
				<span class="form--input-group"><label for="email" class="screen-reader-text">Email</label><input type="email" id="email" name="email" class="form--input" placeholder="you@example.com" maxlength="128" required="required" tabindex="1" value=""></span>
                
				<p><input type="submit" class="button primary-action" name="submit" value="Manage This Email" tabindex="2"></p>
			</form>
<?php	
	// There is an email, but we didn't find a user associated with it
	} else if (!is_object($email)) {
?>

			<h3 class="subscriber--subtitle subscriber--error">Whoops&hellip; that email is not subscribed to any lists</h3>
			<h3><a href="<?php echo get_link("mail/subscribe/".$useremail); ?>" title="Click here to choose an email list to subscribe this email to">Subscribe to a list.</a></h3>
			
<?php	
	// Found the email and the user. 
	} else {
?>
			
			<form class="form form__subscriber-profile form__wide" method="POST">
				
				<span class="form--input-group"><label for="email">Your Email</label>
                <input type="email" id="email" name="email" class="form--input" maxlength="128" required="required" value="<?php echo $email->email ?>"></span>
				
				<h2 class="subscriber--subtitle">Subscriptions</h2>
				<p><em>You may uncheck a box to be removed from that list</em></p>
				
				<div class="subscriber--lists">
<?php 
	hiddenField("oldemail", $email->email);
	
	foreach ($lists as $list) {
		if ($list->public) {
			if ($email->is_linked($list)) { $checked = "checked"; } else { $checked = ""; }
			echo '<div class="subscriber--list-wrapper"><p class="subscriber--list"><label for="'.$list->name.'" class="check"><input type="checkbox" name="'.$list->name.'" id="'.$list->name.'" value="'.$list->id.'" '.$checked.'> '.$list->display_name.'</label></p>';
			echo '<div class="subscriber--list--description">'.$list->description.'</div></div>';
		}
	}
?>
                
                </div>
                <p><input type="submit" class="button primary-action" name="submit" value="Save Subscription Settings"></p>
                
                <div class="subscriber--details">
    				<h3>Optional Information:</h3>
    				<span class="form--input-group"><label for="first_name">First Name</label> <input type="text" id="first_name" name="first_name" class="form--input" value="<?php echo $email->first_name ?>"></span>
    				<span class="form--input-group"><label for="last_name">Last Name</label> <input type="text" id="last_name" name="last_name" class="form--input" value="<?php echo $email->last_name ?>"></span>
    				<span class="form--input-group"><label for="address1">Address 1</label> <input type="text" id="address1" name="address1" class="form--input" value="<?php echo $email->address1 ?>"></span>
    				<span class="form--input-group"><label for="address2">Address 2</label> <input type="text" id="address2" name="address2" class="form--input" value="<?php echo $email->address2 ?>"></span>
    				<span class="form--input-group"><label for="city">City</label> <input type="text" id="city" name="city" class="form--input" value="<?php echo $email->city ?>"></span>
    				<span class="form--input-group"><label for="state">State</label> <?php echo StateSelectList("state", $email->state); ?></span>
    				<span class="form--input-group"><label for="zip">Zip Code</label> <input type="text" id="zip" name="zip" class="form--input" value="<?php echo $email->zip ?>"></span>
    				<span class="form--input-group"><label for="phone">Phone</label> <input type="tel" id="phone" name="phone" class="form--input" value="<?php echo $email->phone ?>"></span>
				</div>
				
				<p><label for="delete" class="check"><input type="checkbox" name="delete" id="delete" value="<?php echo $email->email ?>">&nbsp; Remove my email from the site completely.</label></p>
				
				<p><input type="submit" class="button primary-action" name="submit" value="Save Subscription Settings"></p>
			</form>
<?php
	}
	echo '</div><!-- end .subscriber -->'; 
} 
?>
			
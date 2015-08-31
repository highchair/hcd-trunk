<?php
	function initialize_page()
	{
		if ($_POST) {
			$post_value = $_POST['submit'];
			if ($post_value == "Save Subscription Settings") {
				$useremail = $_POST['email'];
				$email = NLEmails::FindByEmail($useremail);
				if (!$email) {
					$email = MyActiveRecord::Create('NLEmails');
					$email->email = $useremail;
					$email->save();
				}
				foreach ($_POST['selected_list'] as $key => $value)
				{
					$query = "INSERT INTO nlemails_nllists VALUES ({$email->id}, $value);";
					if( !mysql_query($query, MyActiveRecord::Connection()) ) {
						die($query);
					}
				}
			}
		} 
	}
	
	function display_page_content()
	{
		$useremail = requestIDparam(); 
		if ($useremail == "deleted") { $useremail = ""; }
		
		if (!$_POST)
		{
			$lists = NLLists::FindPublic();
			$list_count = count($lists); 
			
			$welcome_message = 'Subscribe to our mailing list'; 
			if ($list_count > 1) { $welcome_message .= "s"; } 
			
			if (requestIdParam() == "deleted") {
				echo '<div class="feedback feedback__alert"><p class="">That email has been removed</p></div>'; 
			}
?>
		
		<div class="subscriber">
			<script type="text/javascript">
				//<![CDATA[										
					$().ready(function() {
						$("#lists_form").validate({
							rules: {
								email: { required: true, email: true },
								"list[]": "required"
							},
							messages: {
								email: "Please enter a valid email address",
								"list[]": "Almost forgot! Select at least one list to subscribe to." 
							}
						});
					});
				//]]>
			</script>
			
			<h1 class="subscriber--title"><?php echo $welcome_message ?></h1>
			
			<form class="form form__subscribe-new" method="POST">
				
				<span class="form--input-group"><label for="email">Your Email</label>
                <input type="email" id="email" name="email" class="form--input" maxlength="128" required="required" value="<?php echo $useremail ?>"></span>
				
				<div class="subscriber--lists">
<?php 
        	foreach ($lists as $list) {
        		if ($list->public) {
        			echo '<div class="subscriber--list-wrapper"><p class="subscriber--list"><label for="'.$list->name.'" class="check"><input type="checkbox" name="selected_list[]" id="'.$list->name.'" value="'.$list->id.'"> '.$list->display_name.'</label></p>';
        			echo '<div class="subscriber--list--description">'.$list->description.'</div></div>';
        		}
        	}
?>
                
                </div>
                <p><input type="submit" class="button primary-action" name="submit" value="Save Subscription Settings"></p>

			</form>
		</div>

<?php	} else {
			
			// There is a POST. Display a Success page
			$useremail = $_POST['email'];
			$nlemail = NLEmails::FindByEmail($useremail); 
			$thislists = $nlemail->getNLlists();  
?>
		
		<div class="subscriber subscriber--feedback">
			<h1 class="subscriber--title">Thanks!</h1>
			<h3 class="subscriber--subtitle"><?php echo $useremail; ?> is now subscribed to:</h3>
<?php
			foreach ( $thislists as $list ) {
				echo '<h4 class="successfully_subscribed">'.$list->display_name.'</h4>';
			}
?>

			<p>&nbsp;</p>
			<p>Go to the <a href="<?php echo get_link("users/manage/".$useremail); ?>">Manage Subscriptions</a> page to manage your subscriptions or provide additional information.</p>
		</div>
<?php		
		}
	}
?>
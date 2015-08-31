<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
			
			if ($post_action == "Submit All Options and Preview")
			{
				$blast_config = $_SESSION['blaster'];
				if (!is_array($blast_config['lists'])) 
				{
					setFlash('<h3>You must Select a list to send to.</h3>');
					redirect(BASEHREF."admin/mail_blast");
				}
			}
		}
	}
	
	function display_page_content()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
?>

	<div id="mail_blaster">
		<?php if (!$post_action) { 
			$_SESSION['blaster'] = Array();
			$lists = NLLists::FindAll();
		?>
		
		<div id="edit-header" class="blaster">
    		<div class="nav-left column">
        		<h1>Email Blast Setup: Follow these Steps</h1>
    		</div>
    		<div class="nav-right column">
                <a href="<?php echo get_link("admin/list_lists") ?>" class="hcd_button">Manage Mailing Lists</a> 
    		</div>
    		<div class="clearleft"></div>
    	</div>
		
		<script type="text/javascript">
			//<![CDATA[
			$().ready(function() {
				$('.selectList').click(function() {
					$('a.blast_options').fadeIn();
				});
				
				$('.blast_options').click(function() {
					var value = "";
					$('#select_list .selectList').each(function() {
						if ($(this).attr('checked'))
						{
							value += $(this).val()+",";
						}
					});
					$('#session_add').load('<?php echo BASEHREF ?>blaster/session_add/lists/'+value, function() { 
						$("#blast_options").load('<?php echo BASEHREF ?>blaster/blast_options/', function() {
							$("#blast_options").slideDown();
						});
					});
					return false;
				});
				
				$('#step-one').click(function() {
    				$(this).fadeOut('slow');
				}); 
			});
			//]]>
		</script>
		
		<form id="select_list_form" method="POST">
		    <div id="select_list">
		        <h2><big>Step 1:</big> Choose a list to send an email to</h2>
<?php
		foreach ($lists as $list) {
			echo '<p><label for="'.$list->name.'"><input class="selectList" name="list[]" type="checkbox" value="'.$list->name.'" id="'.$list->name.'"/> &nbsp; '.$list->display_name.'</p>';
		}
?>
                    
                <a href="#" id="step-one" class="blast_options submitbutton" style="display:none;">Send a newsletter to selected list(s)</a>
			</div>
			
			<div id="blast_options" style="display: none;"></div>
		</form>
		
		<div id="session_add"></div>
<?php 
		} else if ($post_action == "Submit All Options and Preview") { 
			
			include_once(mailPath('snippets/mail_config_parse'));
    ?>
    
        <div id="edit-header" class="blaster">
    		<h1>Preview Your E-Newsletter Blast</h1>
    		<p><span class="hint">If you use your browser&rsquo;s &ldquo;Back&rdquo; button, you may lose any text or options you have configured here.</span></p>
    	</div>
    <?php 	
			// ! Creates previews in Templates
			$templates_generated = Array();
			$lists = "";
			foreach ($list_names as $slug) 
			{
 				$list = NLLists::FindBySlug($slug);
 				$lists .= "<strong>{$list->display_name}</strong>";
 				if (!in_array($list->template, $templates_generated))
 				{
	 				
	 				echo "<p><strong>Template: {$list->template}</strong> (Please note: Things may look a little funky, as this is meant to be viewed in a mail browser)</p>\n";
 				}
 				$templates_generated[] = $list->template;
 			}
 			echo "<p>Your selected lists are: $lists </p>\n";
 			echo "<p>Your email subject is: ";
 			
 			if ($_POST['subject_line']) 
 			{
 				$subject = $_POST['subject_line'];
 			} else {
 				$subject = $list->display_name." News: ".date("F j\, Y");
 			}
 			echo "<strong>{$subject}</strong></p>"; 
 			
 			echo "<div class=\"mail_template\">\n";
			include_once( mailPath("mail_layouts/".$list->template."_preview") );
			echo "</div>\n";
?>

			<form id="send_list_form" method="POST">
				<?php hiddenField("description", $_POST['description']); ?>
				<?php hiddenField("subject_line", $subject); ?>
				
				<div id="edit-footer" class="blaster clearfix">
            		<div class="column half">
            			<p><input type="submit" class="submitbutton" name="submit" value="Send To Your Lists" /></p>
            		</div>
            		<div class="column half last">
            			<p>Be patient... depending on the number of addresses, this may take awhile.</p>
            		</div>
            	</div>
			</form>
<?php
		} else { 
			
			// Send the Mail
			include_once( mailPath('snippets/mail_config_parse') );
			
			// ! Creates previews in Templates
			$success = "";
			$failure = "";
			
			$subject = $_POST['subject_line'];
 			
 			foreach ($list_names as $slug) 
			{
 				$list = NLLists::FindBySlug($slug);
 				 
 				// Include Template
				include_once( mailPath("mail_layouts/".$list->template) );
				// stupid bug fix
				$mailed_content = str_replace(array("\\\'",'\\\"'),array("'",'"'),$mailed_content);
 				
 				$blast = MyActiveRecord::Create("MailBlast");			
 				$blast->email_subject = $subject; 
 				$blast->date_sent = date("Y-m-d");
 				$blast->hash = md5(date('r', time()));
 				$blast->content = $mailed_content;
 				$blast->list_id = $list->id;
 				$blast->save();
 				
 				$failure_num = 0; 
				$success_num = 0; 
 				
 				foreach ($list->findEmails() as $email)
 				{
 					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
					// Additional headers
					$headers .= 'From: '.$list->display_name.' <'.SENDMAIL_FROM.'>'."\r\n";
					
					// Mail it
					if ( ! mail($email->email, $subject, str_replace("{{-email-}}", $email->email, $blast->content), $headers) ) 
					{
						$failure .= $list->display_name.": ".$email->email."<br />\n";
						$failure_num++; 
					} else {
						$success .= $list->display_name.": ".$email->email."<br />\n";
						$success_num++; 
					}
 				}
 			}
 			echo '<div id="edit-header" class="blaster"><h1>Success!</h1></div>'; 
 			if ($failure_num != 0 ) { echo "<h2>$failure_num Email(s) failed:</h2>\n<p>".$failure."</p>\n<p>&nbsp;</p>\n"; }
 			echo "<h2>$success_num Emails got sent</h2>\n<p>".$success."</p>\n";
 			
		}
?>

	</div>
<?php		
	}
?>
<?php
	function initialize_page()
	{
		 
		$post_action = "";
		if(isset($_POST['submit'])) { $post_action = $_POST['submit']; }
	
		if($post_action == "Edit List") {
			$success = ''; 
			
			$list = NLLists::FindById(requestIdParam());
			$list->description = $_POST['description'];
			$list->public = ( isset($_POST['public']) ) ? $_POST['public'] : 0;
			
			$list->save();
			$success .= "Mailing List Updated";
			$emails = explode(",", str_replace(" ", "", $_POST['emails']));
			
			if (is_array($emails)) {
				
				$count = 0;
				foreach ($emails as $email) {
					
					if ( ! $list->emailLinked($email) && is_validemail($email) ) {
						
						// Check for an existing match in the system
						$newAddy = NLEmails::FindByEmail($email);
						if ( ! isset($newAddy) and ! is_object($newAddy) ) {
							$newAddy = MyActiveRecord::Create('NLEmails');
							$newAddy->email = $email;
							$newAddy->save(); 
							$count++;
						}
						
						// Existing or not, attach that email to this List
						$query = "INSERT INTO nlemails_nllists VALUES ({$newAddy->id}, {$list->id});";
						if(!mysql_query($query, MyActiveRecord::Connection())) { die($query); }
					}
				}
				if ($count > 0) {
					$success .= " / Emails Added to {$list->display_name}";
				} 
			}
			setFlash( "<h3>".$success."</h3>" );
		}
	}
	
	function display_page_content()
	{
		$list = NLLists::FindById(requestIdParam());
		$emails = $list->findEmails(); 
		$subscribers = count($emails); 
?>

	<script type="text/javascript">
		$().ready(function() {
			$("a.email_del").click(function() {
				var email = $(this).attr('title');
				var list = $(this).attr('name');
				var answer = confirm("Do you want to delete "+email+" from the list?");
				if (answer) {
					$("#loadme").load('<?php echo BASEHREF; ?>blaster/remove_email/'+email+'/'+list);
					$(this).parent().fadeOut();
					return false;
				} else {
					return false;
				}
			});
		});
	</script>
	
	<div id="loadme" style="display:none"></div>
	
	<div id="edit-header" class="maillistnav">
		<div class="nav-left column">
    		<h1>Edit a List: <?php echo $list->display_name; ?></h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/list_lists") ?>" class="hcd_button">Back to Mailing Lists</a> 
		</div>
		<div class="clearleft"></div>
	</div>
	
	<form method="POST" id="edit_maillist">
																
		<p><label for="description">Description of List (displayed, if the list is public, when a user manages their subscription preferences):</label><br />
		<?php textArea("description", $list->description, 98, 10); ?>
		
		</p>
		
		<p>&nbsp;</p>
		
		<p><label for="emails">New Emails:</label>
    		<span class="hint">This field requires a single email OR a comma-delimited list of emails (i.e. &ldquo;blah@blah.org, blag@blag.net&rdquo; etc...)</span></p><p>
    		<textarea class="mceNoEditor" name="emails" id="emails" rows="8" style="width: 98%;"></textarea>
		
		</p>
		
		<p><label for="public">Public List:</label>
		<?php checkBoxField("public", $list->public, "1"); ?>
		
		</p>
		
		<p><input type="submit" class="submitbutton" name="submit" value="Edit List" /></p>
		
		<div id="edit-footer" class="maillistnav">
    		
    		<p><label for="subscribers">Current Subscribers (<?php echo $subscribers ?> subscribers):</label>
    		<span class="hint">Click the X to remove email from list.</span>
    		</p>
    		
    		<table width="100%" cellpadding="3" cellspacing="0" border="0">
    			<tbody>
    				<tr>
<?php 
	$count = 0;
	$tabs = "\t\t\t\t\t\t\t\t\t";
	
	foreach ($emails as $email) { 
		echo $tabs."\t<td><div>{$email->email}&nbsp;[<a class=\"email_del\" title=\"{$email->email}\" name=\"$list->id\" href=\"javascript:;\">X</a>]</div></td>\n";
		$count++;
		if ($count == 3){
			echo $tabs."</tr><tr>\n";
			$count = 0;
		}
	}
?>

    				</tr>
    			</tbody>
    		</table>
        </div>
        
	</form>
<?php } ?>
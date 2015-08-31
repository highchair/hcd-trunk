<?php
	function initialize_page()
	{
		 
		$post_action = "";
		if(isset($_POST['submit'])) { $post_action = $_POST['submit']; }
	
		if($post_action == "Add New List") {
			
			$success = ''; 
			$list = MyActiveRecord::Create('NLLists');
			
			$list->display_name = $_POST['name'];
			$list->name = slug($_POST['name']);
			$list->template = $_POST['template'];
			$list->description = $_POST['description'];
			$list->public = $_POST['public'];
			
			$list->save();
			$success .= "Mailing List Created";
			$emails = explode(",", str_replace(" ", "", $_POST['emails']));
			
			if ( is_array($emails) ) {
				
				$count = 0;
				foreach ($emails as $email)  {
					
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
				if ( $count > 0 ) {
					$success .= " / Emails Added to {$list->display_name}";
				} else {
					$success .= " / All Emails Added or Invalid";
				}
			}
			setFlash( "<h3>".$success."</h3>" );
		}
	}
	
	function display_page_content()
	{	
?>

						<script type="text/javascript">
							$().ready(function() {
								$("#add_venue").validate({
										rules : {
											name: "required",
											description: "required"
										},
										messages: {
											name: "Please a name for this list",
											description: "Please enter a description for this list"
										}
									});
							});
						</script>
						
						<form method="POST" enctype="multipart/form-data" id="add_venue">
							<h1>Add a New List</h1>
							
							<p><label for="name">Name:</label><br />
								<?php textField("name", "", "required: true"); ?>
							
							</p>				
							
							<p>
								<label for="template">Template:</label>
								<select id="template" name="template">
									<?php
										$templates = list_mail_templates();
						
										foreach($templates as $template)
										{
											$text = $template;
											$selected_string = "";
											if($text == "default")
											{
												$selected_string = " selected";
											}
											echo "<option value=\"$template\"$selected_string>$text</option>";
										}
									?>
									
								</select>
							</p>
							
							<p><label for="public">Public List:</label>
							<?php checkBoxField("public", 0, "1"); ?>
							
							</p>
														
							<p><label for="description">Description of List:</label><br />
								<?php textArea("description", "", 98, 10); ?>
							
							</p>
							
							<p><label for="emails">Emails:</label>
								<span class="hint">This field requires a comma delimited list of emails ie blah@blah.org, blag@blag.net, etc.</span></p>
								<textarea class="mceNoEditor" name="emails" id="emails" rows="8" style="width: 98%;"></textarea>
							
							</p>
														
							<input type="submit" class="submitbutton" name="submit" value="Add New List" />
						</form>
<?php } ?>
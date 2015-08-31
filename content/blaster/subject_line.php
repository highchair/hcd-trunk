<?php
	function initialize_page()
	{
		if(IsUserLoggedIn()) { } else { redirect('/'); }
	}
	
	function display_page_content()
	{
?>

	<script type="text/javascript">
		//<![CDATA[
		$().ready(function() 
			{
			$('#subject_add').click(function() {
				$('#session_add').load('<?php echo BASEHREF; ?>blaster/subject_add/subject_line/true', function() { 
					$("#subject_added").slideDown();
					$("#subject_removed").slideUp();
				});
				return false;
			});
			$('#subject_remove').click(function() {
				$('#session_add').load('<?php echo BASEHREF; ?>blaster/subject_add/subject_line/false', function() { 
					$("#subject_removed").slideDown();
					$("#subject_added").slideUp();
				});
				return false;
			});
		});
	</script>
			
	<p><label for="subject_line">Subject line of the Email Blast:</label>
    	<?php textField("subject_line", "", ""); ?>
	</p>
	
	<p>
    	<a href="#" id="subject_add" class="submitbutton smaller">Add Subject</a>
    	<a href="#" id="subject_remove" class="submitbutton smaller">Remove Subject</a>
	</p>
	
	<div id="subject_added" class="blast_notice" style="display:none">Subject Line Added</div>	
	<div id="subject_removed" class="blast_notice" style="display:none">Subject Line Removed</div>	
	
	<?php
	}
?>
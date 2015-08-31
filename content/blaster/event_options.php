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
			$('#events_add').click(function() {
				$('#event_include').load('<?php echo BASEHREF ?>blaster/event_include/'+$('#up_event_number').val()+'/'+$('#on_event_number').val(), function() { 
					$("#event_include").slideDown();
				});
				return false;
			});
		});
	</script>
			
	<p><label class="inline" for="up_event_number">Number of Upcoming Events you wish to attach:</label>
	<?php textField("up_event_number", "", ""); ?>
	</p>
	
	<p><label class="inline" for="on_event_number">Number of Ongoing Events you wish to attach:</label>
	<?php textField("on_event_number", "", ""); ?>
	</p>
	<p>
	<a href="#" id="events_add" class="submitbutton smaller">Add Events</a>&nbsp; &nbsp;<span class="hint">Change the numbers above and click &ldquo;Preview Events&rdquo; again to refresh the list below.</span>
	</p>
	
	<div id="event_include"></div>	
	
	<?php
	}
?>
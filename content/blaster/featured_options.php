<?php
	function initialize_page()
	{
		if(IsUserLoggedIn()) { ?> 
		<script type="text/javascript">
			//<![CDATA[
			$().ready(function() { console.log('Logged In:Featured Options'); });
		</script>
		<?php } else { redirect('/'); }
	}
	
	function display_page_content()
	{
?>
	
	<script type="text/javascript">
		//<![CDATA[
		$().ready(function() {
			$('select#featured-event-select').change(function() {
				$('#session_add').load('<?php echo BASEHREF ?>blaster/session_add/featured_event/'+$(this).val(), function() { 
					if ($('select#featured-event-select').val()) {
						$("#featured_event_removed").slideUp(0);
						$("#featured_event_added").slideDown();
					} else {
						$("#featured_event_added").slideUp(0);
						$("#featured_event_removed").slideDown();
					}
				});
			});
		});
		//]]>
	</script>
	
	<b>Select your featured event.</b><br />
	<select id="featured-event-select">
		<option value="">Select an Event</option>
	<?php
		print_r($_SESSION["blaster"]);
		
		$pot_events = Events::FindAll();
		foreach ($pot_events as $event)
		{
			if ($event->hasImage() && !$event->isPast())
			{
				echo "\t\t<option value=\"{$event->id}\">".date("M d",strtotime($event->date_start))." :: {$event->title}</option>\r\n";
			}
		}
	?>
	
	</select>
	<div id="featured_event_added" class="blast_notice" style="display: none;">Featured Event Added <small>(to remove, reset the drop-down menu to the first option, &ldquo;Select an Event&rdquo;)</small></div>
	<div id="featured_event_removed" class="blast_notice" style="display: none;">Featured Event Removed</div>
	<?php
	}
?>
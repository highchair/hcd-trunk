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
		$().ready(function() {
			$("#subject_line").load('<?php echo BASEHREF; ?>blaster/subject_line/');
			$("#custom_text_option").load('<?php echo BASEHREF; ?>blaster/text_options/');
			$("#featured_event_option").load('<?php echo BASEHREF; ?>blaster/featured_options/');
			/*$("#event_list_option").load('<?php echo BASEHREF; ?>blaster/event_options/');*/
			
			$('a.subject').click(function() {
				$('#subject_line').slideToggle();
				return false;
			});
			$('a.text').click(function() {
				$('#custom_text_option').slideToggle();
				return false;
			});
			$('a.featured').click(function() {
				$('#featured_event_option').slideToggle();
				return false;
			});
			/*$('a.event').click(function() {
				$('#event_list_option').slideToggle();
				return false;
			});*/
		});
		//]]>
	</script>
		
	<h2><big>Step 2:</big> Choose some content options</h2>
	<p>A: <a href="#" class="subject clickme">Enter a Email Subject &rarr;</a>
		<span class="hint">Enter what you want the subscriber to read as the subject.</span>
		<div id="subject_line" class="mail_option" style="display: none;"></div>
	</p>
	<p>B: <a href="#" class="text clickme">Custom Text Area Option &rarr;</a>
		<span class="hint">Enter some text for the Newsletter</span>
		<div id="custom_text_option" class="mail_option" style="display: none;"></div>
	</p>
	<?php if (CALENDAR_INSTALL) { ?>
	<!--<p>C: <a href="#" class="featured clickme">Featured Event Option &rarr;</a>
		<span class="hint">Choose a Featured Event to display in the Newsletter</span>
		<div id="featured_event_option" class="mail_option" style="display: none;"></div>
	</p>-->
	<p>C: <a href="#" class="event clickme">Event List Option &rarr;</a>
		<span class="hint">Choose a number of events from the Calendar to display in the Newsletter</span>
		<div id="event_list_option" class="mail_option" style="display: none;"></div>
	</p>
	<?php } ?>
	
	<input type="submit" class="submitbutton" name="submit" value="Submit All Options and Preview" />
    
<?php
	}
?>
<?php
	function initialize_page()
	{
		if(IsUserLoggedIn()) { } else { redirect('/'); }
	}
	
	function display_page_content()
	{
		echo "<hr />\r\n";
		$upcoming_event_num = requestIdParam();
		
		if ($upcoming_event_num) 
		{
			$upcoming = Events::FindUpcoming($upcoming_event_num);
			$upcoming_csv = "";
	?>
		
			<p><span class="hint">Click the [<u>X</u>] to remove an event from list</span></p>
			<h2>Upcoming Events that will be included in this email:</h2>
			<table cellpadding="3" cellspacing="0" border="0">
				<tbody>

	<?php
			foreach ($upcoming as $event) 
			{ 
				
				echo "<tr>
					<td>[<a href=\"javascript;:\" class=\"remove_uevent\" title=\"{$event->id}\">X</a>]&nbsp;</td>
					<td>".$event->getDateStart("date")."&nbsp;</td>
					<td><b>$event->title</b>&nbsp;</td>
				</tr>";
				$upcoming_csv .= $event->id.",";
			}
		}
	?>
		
				</tbody>
			</table>
	<?php
		
		$ongoing_event_num = getRequestVarAtIndex(3);
		if ($ongoing_event_num) {
			$ongoing = Events::FindOngoing($ongoing_event_num);
			$ongoing_csv = "";
	?>
	
			<h2 style="padding-top: 1em;">Ongoing Events that will be included in this email:</h2>
			<table cellpadding="3" cellspacing="0" border="0">
				<tbody>
	<?php
			foreach ($ongoing as $event) 
			{ 
				echo "<tr>
					<td>[<a href=\"javascript;:\" class=\"remove_oevent\" title=\"{$event->id}\">X</a>]&nbsp;</td>
					<td>".$event->getDateStart("date")." &ndash; ".$event->getDateEnd("date")."&nbsp;</td>
					<td><b>$event->title</b>&nbsp;</td>
				</tr>";
				$ongoing_csv .= $event->id.",";
			}
		}
	?>
				
				</tbody>
			</table>

		
	<script type="text/javascript">
		$().ready(function() {
			$('#session_add').load('<?php echo BASEHREF ?>blaster/session_add/upcoming_events/<?php echo $upcoming_csv; ?>', function() {
				$('#session_add').load('<?php echo BASEHREF ?>blaster/session_add/ongoing_events/<?php echo $ongoing_csv; ?>');
			});
			
			$("a.remove_uevent").click(function() {
				var event_id = $(this).attr('title');
				$("#session_add").load('<?php echo BASEHREF ?>blaster/remove_uevent/'+event_id);
				$(this).parent().parent().css({ "background-color" : "#999"});
				return false;
			});
			
			
			$("a.remove_oevent").click(function() {
				var event_id = $(this).attr('title');
				$("#session_add").load('<?php echo BASEHREF ?>blaster/remove_oevent/'+event_id);
				$(this).parent().parent().css({ "background-color" : "#999"});
				return false;
			});

		});
	</script>
<?php
	}
?>
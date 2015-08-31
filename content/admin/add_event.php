<?php
	// TODO: these should be refactored out
	function add_eventRecurrenceExists($recurrences = array(), $day, $modifier)
	{
		foreach($recurrences as $recurrence)
		{
			if($recurrence->day == $day && $recurrence->modifier == $modifier)
			{
				return 1;
			}
		}
		return 0;
	}
	function get_recurrence_tag($recurrences = array(), $day, $modifier)
	{              
		checkBoxField("recurrence_{$day}[]", 0, $modifier);
	}
	function add_eventUpdateRecurrences($event)
	{
		$event_id = $event->id;
		$sunday = getPostValueAsArray("recurrence_Sunday");
		$monday = getPostValueAsArray("recurrence_Monday");
		$tuesday = getPostValueAsArray("recurrence_Tuesday");
		$wednesday = getPostValueAsArray("recurrence_Wednesday");
		$thursday = getPostValueAsArray("recurrence_Thursday");
		$friday = getPostValueAsArray("recurrence_Friday");
		$saturday = getPostValueAsArray("recurrence_Saturday");
		
		Recurrence::ClearForEvent($event_id);
		
		foreach($sunday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Sunday",'modifier'=>"$modifier"));
			$recurrence->save();
		}
		foreach($monday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Monday",'modifier'=>"$modifier"));
			$recurrence->save(); 
		}
		foreach($tuesday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Tuesday",'modifier'=>"$modifier"));
			$recurrence->save(); 
		}
		foreach($wednesday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Wednesday",'modifier'=>"$modifier"));
			$recurrence->save(); 
		}
		foreach($thursday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Thursday",'modifier'=>"$modifier"));
			$recurrence->save(); 
		}
		foreach($friday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Friday",'modifier'=>"$modifier"));
			$recurrence->save(); 
		}
		foreach($saturday as $modifier)
		{
			$recurrence = Recurrence::Create('Recurrence', array('event_id'=>"$event_id", 'day'=>"Saturday",'modifier'=>"$modifier"));
			$recurrence->save(); 
		}
	}
	
	function initialize_page()
	{
		$event_types = EventTypes::FindAll();
		$event_periods = EventPeriods::FindAll();
	
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		
			if( $post_action == "Add Event and add another" || $post_action == "Add and Return to List" )
			{
				$event = MyActiveRecord::Create('Events', $_POST);
				if (!getPostValue('time_start'))
				{
					$event->setDateStart(getPostValue('date_start'), "04:00:00");
				} else {
					$event->time_start = date("H:i:s",strtotime(getPostValue('time_start')));
				}
				if (!getPostValue('date_end') && !getPostValue('time_end'))
				{
					$event->setDateEnd(getPostValue('date_start'), "04:00:00");
				} else if (!getPostValue('date_end') && getPostValue('time_end')) {
					$event->setDateEnd(getPostValue('date_start'), date("H:i:s",strtotime(getPostValue('time_end'))));
				} else {
					$event->setDateEnd(getPostValue('date_end'), date("H:i:s",strtotime(getPostValue('time_end'))));
				}
				$event->eventtype_id = ( isset($_POST['eventtype_id']) ) ? $_POST['eventtype_id'] : 1;
				$event->eventperiod_id = $_POST['eventperiod_id'];
				
				$event->save();
				
				$notdates = getPostValue('notdates');
				if (is_array($notdates)) {
					foreach ($notdates as $date) {
						if (strlen($date)>4) {
							$query = "INSERT INTO events_notdate VALUES('$event->id','".formatDateView($date,"Y-m-d")."')";
							mysql_query($query, MyActiveRecord::Connection()) or die($query);
						}
					}
				}
				add_eventUpdateRecurrences($event);
				
				$thisnewevent = Events::FindById($event->id); 
				
				if ($thisnewevent->date_end < $thisnewevent->date_start)
				{
					setFlash("<h3>Whoops! Event Starts after it Ends! Please correct dates...</h3>");
					$eventyear = parseDate($thisnewevent->date_start, "Y");
					$eventmonth = parseDate($thisnewevent->date_start,"n"); 
					redirect("/admin/edit_event/$eventyear/$eventmonth/$thisnewevent->id");
				} else {
					
					setFlash("<h3>Event added</h3>");
					
					if ($post_action == "Add and Return to List") {
						// Redirect user to the Main Event List
						$datestart = explode("/", getPostValue('date_start')); 
						setFlash("<h3>Event added</h3>");
						redirect("/admin/list_events/$datestart[2]/$datestart[0]");
					}
				}
			}
		}
	}

	function display_page_content()
	{
		$event_types = EventTypes::FindAll();
		$event_periods = EventPeriods::FindAll();
		$days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"); 
?>

<script type="text/javascript">
	//<![CDATA[	
	$().ready(function() {
		setupDateFields("1");
		
		$.datepicker.setDefaults({
            showButtonPanel: true,
			showOtherMonths: true,
			selectOtherMonths: true
        });
        
        $( "#date_start" ).datepicker();
		$( "#time_start" ).timepicker({timeFormat: 'hh:mm:ss tt',stepMinute: 5});
		$( "#date_end" ).datepicker();
		$( "#time_end" ).timepicker({timeFormat: 'hh:mm:ss tt',stepMinute: 5});
		$( "#not_date" ).datepicker();
		
		$("a#notdate_add").click(function() {
			var date = $("input[name='not_date']").val();
			if (date != "") {
				$("input[name='not_date']").val('');
				var all_dates_vis = $("span#notdates").html();
				$("span#notdates").html("<label for=\"notdates[]\">"+date+"&nbsp;<a href=\"javascript:;\" onClick=\"$(this).parent().remove();\">X</a><input type=\"hidden\" name=\"notdates[]\" value=\""+date+"\" /></label>"+all_dates_vis);
			}
		});
		
		$("#eventperiod_id").change(function() {
			var selected = $(this).val();
			setupDateFields(selected);
		});
		
		$("#add_event").validate({
			rules: {
					title: "required",
					date_start: "required",
				},
			messages: {
					title: "Please enter a title for this event",
					date_start: "Please enter at least a start date for this event",
				}
		});	
	});
	//]]>
</script>

<div id="edit-header" class="event">
	<h1>Add Event</h1>
</div>

<div id="calendar_div"></div>

<form method="POST" id="add_event">
	
	<input type="hidden" name="calendar_id" value="1" />
	
	<p class="display_name">
        <label for="title">Title</label>
    	<?php textField('title', '', 'required: true'); ?>
	</p>
	
	<?php if ( ALLOW_EVENT_TYPES && count($event_types) > 1 ) { ?>
	<p>
	    <label for="eventtype_id">Event Type</label>
		<select name="eventtype_id" id="eventtype_id">
		<?php
			foreach($event_types as $event_type)
			{
				echo "<option value=\"$event_type->id\">$event_type->name</option>\r\n";
			}
		?>
			
		</select>
	</p>
	<?php } ?>
	
	<div id="eventdateselects" class="dropslide">
		<p><label for="eventperiod_id">Event Period:</label>
    		<select name="eventperiod_id" id="eventperiod_id">
			<?php
				foreach($event_periods as $event_period)
				{
					echo "<option value=\"$event_period->id\">$event_period->name</option>\r\n";
				}
			?>
    		</select>
		</p>
		
		<p>
		    <label for="date_start">Start Date / Time</label>
    		<input type="text" name="date_start" id="date_start" style="width: 6.5em;" value="" class="required: true" />&nbsp;
    		<input type="text" name="time_start" id="time_start" style="width: 6.5em;" value="" />&nbsp;&nbsp; 
    		
    		<label for="date_start">End Date / Time</label>
    		<input type="text" name="date_end" id="date_end" style="width: 6.5em;" value="" />&nbsp;
    		<input type="text" name="time_end" id="time_end" style="width: 6.5em;" value="" />
		</p>
		
		<div id="recurrence_rules" style="display:none;">
			<p><label for="date_start">Exclusion Date(s)</label>
				<input type="text" name="not_date" id="not_date" style="width: 6.5em;"/>&nbsp;<a href="javascript:;" id="notdate_add">Add to list&rarr;</a> 
				<span id="notdates"></span>
			</p>
			
			<label>Recurrence Rules</label>
			<table>
				<tbody>
					<tr>
						<th>&nbsp;</th>
						<th>Sunday</th>
						<th>Monday</th>
						<th>Tuesday</th>
						<th>Wednesday</th>
						<th>Thursday</th>
						<th>Friday</th>
						<th>Saturday</th>
					</tr>
					<tr>
						<td>Every</td>
						<?php
						foreach($days as $day) {
						    echo "<td>";
							get_recurrence_tag(null, $day, 0);
							echo "</td>";
						}
						?>
					</tr>
					<tr>
						<td>First</td>
						<?php
						foreach($days as $day) {
						    echo "<td>";
							get_recurrence_tag(null, $day, 1);
							echo "</td>";
						}
						?>
					</tr>
					<tr>
						<td>Second</td>
						<?php
						foreach($days as $day) {
						    echo "<td>";
							get_recurrence_tag(null, $day, 2);
							echo "</td>";
						}
						?>
					</tr>
					<tr>
						<td>Third</td>
						<?php
						foreach($days as $day) {
						    echo "<td>";
							get_recurrence_tag(null, $day, 3);
							echo "</td>";
						}
						?>
					</tr>
					<tr>
						<td>Fourth</td>
						<?php
						foreach($days as $day) {
						    echo "<td>";
							get_recurrence_tag(null, $day, 4);
							echo "</td>";
						}
						?>
					</tr>
					<tr>
						<td>Last</td>
						<?php
						foreach($days as $day) {
						    echo "<td>";
							get_recurrence_tag(null, $day, 5);
							echo "</td>";
						}
						?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<p><label for="name">Event Description</label><br />
	<?php textArea("description", "", 98, EDIT_WINDOW_HEIGHT); ?>
	</p>
	
	<?php require_once(snippetPath("admin-insert_configs")); ?>
	
	<div id="edit-footer" class="eventtypenav clearfix">
		<div class="column half">
			<p>
				<input type="submit" class="submitbutton" name="submit" value="Add Event and add another" /> <br />
				<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
			</p>
		</div>
		<div class="column half last"></div>
	</div>
	
</form>
<?php } ?>
<?php
	function initialize_page()
	{ 
		$year = getRequestVarAtIndex(2);
		$month = getRequestVarAtIndex(3);
		$_SESSION["admin_viewing_year"] = $year;
		$_SESSION["admin_viewing_month"] = $month;

		// redirect the user to the current month/year if either is blank
		if($year == "" || $month == "")
		{
			$now = getdate();

			$month = $now['mon'];
			$year = $now['year'];
			redirect("/admin/list_events/$year/$month");
		}
	}
	
	function display_page_content()
	{
		//$events = Events::FindAll("DESC");
		$year = getRequestVarAtIndex(2);
		$month = getRequestVarAtIndex(3);
?>

    <div id="edit-header" class="eventnav">
		<div class="nav-left column">
    		<h1>Choose an Event to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_event") ?>" class="hcd_button">Add a New Event</a> 
            <?php if ( ALLOW_EVENT_TYPES ) { ?>
            <a href="<?php echo get_link("admin/list_event_types") ?>" class="hcd_button">List Event Types</a>
            <?php } ?>
		</div>
		<div class="clearleft"></div>
	</div>
	
<?php
		// Check conf file to see if we need to display events as a list instead...
		if (DISPLAY_EVENTS_AS_LIST)
		{
?>

	<div id="table-header" class="eventlist">
		<strong class="item-link">Click Name to Edit</strong>
		<span class="item-filename">Event Type and Date Range</span>
	</div>
	<ul id="listitems" class="managelist">
<?php			
			$events = Events::FindAll("DESC"); 
			$month = "";
			$year = "";  
			
			foreach($events as $event)
			{ 
				$title = "";
				if ($event->title != "") { $title = $event->title; } else { $title = "(no title given)"; }
				
				$eventyear = parseDate($event->date_start, "Y");
				$eventmonth = parseDate($event->date_start,"m");
				$eventmonthname = parseDate($event->date_start,"F"); 
				
				if ($eventmonthname == $month)
				{
					$year = $eventyear;
					$month = $eventmonthname; 
				} else {
					echo "\t\t\t<li class=\"monthname\">$eventmonthname $eventyear</li>";
					$year = $eventyear;
					$month = $eventmonthname;  
				}
				
				echo "\t\t\t<li><a class=\"item-link\" href=\"". get_link("/admin/edit_event/$eventyear/$eventmonth/$event->id") ."\">$title</a> <span class=\"item-filename\">";
				
				$eventtype = $event->getEventType(); 
				
				if($event->date_start == $event->date_end)
				{
					// this is a one day event
					echo $eventtype->name." ".formatDateView($event->date_start);
				}
				else
				{
					// this event spans a range of dates
					echo $eventtype->name." ".formatDateView($event->date_start)." to ".formatDateView($event->date_end);
				}
				echo "</span></li>\n";
			}
			echo "</ul>"; 
		} else {
			
			// Display as a calendar
			$cal = new AdminCalendar(); 
			if($month != "" && $year != "")
			{
				echo $cal->getMonthView($month, $year); 
			}
			else
			{
				echo $cal->getCurrentMonthView(); 
			}
			
		}

	} 
?>
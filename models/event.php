<?php
class Events extends ModelBase
{
	function __construct() {
		// checks for the notdate table
		$result = mysql_query("SHOW TABLES LIKE 'events_notdate'", MyActiveRecord::Connection());
		if (mysql_num_rows($result) < 1) {
			$query = "CREATE TABLE `events_notdate` (`event_id` int(11) NOT NULL,`date` date NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			mysql_query($query, MyActiveRecord::Connection());
		}
	}
	
	/* ! General Find functions - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	function FindAll($order = "ASC", $calendar_id = 1)
	{ 
		return Events::FindBySql('Events', "SELECT events.* FROM events WHERE calendar_id = $calendar_id ORDER BY events.date_start $order");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Events', $id);
	}
	
	function FindAllForDate($day, $month, $year, $calendar_id = 1)
	{
	    $dateString = Events::formatDateString($day, $month, $year);
$query = <<< QUERY_END_MARKER
SELECT events.* FROM events 
	INNER JOIN eventtypes ON events.eventtype_id = eventtypes.id
	LEFT OUTER JOIN recurrence ON recurrence.event_id = events.id 
WHERE 
	events.calendar_id = $calendar_id AND 
	( date($dateString) = date(date_start) AND date_end = date_start) OR (date($dateString) >= date(date_start) AND date($dateString) <= date_end)
	AND (recurrence.id is null OR 
			( (ceiling(dayofmonth(date($dateString)) / 7) = recurrence.modifier 
				or recurrence.modifier = 0 
				or ( (dayofmonth(last_day($dateString)) - dayofmonth(date($dateString))) = 0 and recurrence.modifier = 5)
			  )  	 
			  AND recurrence.day = dayname(date($dateString))
			) 
		)  
ORDER BY events.date_start, events.time_start, events.id ASC
QUERY_END_MARKER;
        //echo "<!-- $query -->";
        return Events::FindBySql('Events', $query);
	}
	
	function FindOneTimeForDate($day, $month, $year, $calendar_id = 1)
	{
		// Find single day events that occur on a given date
		$dateString = Events::formatDateString($day, $month, $year); 
$query = <<< QUERY_END_MARKER
SELECT events.* FROM events 
WHERE calendar_id = $calendar_id AND ($dateString = date(date_start) AND date_end = date_start) 
ORDER BY events.date_start, events.time_start, events.id DESC		
QUERY_END_MARKER;

		return Events::FindBySql('Events', $query);
	}
	
	function FindMultiDayForDate($day, $month, $year, $calendar_id = 1)
	{
		// Find multi day events that have started or have not yet ended compared to a given date
		$dateString = Events::formatDateString($day, $month, $year); 
$query = <<< QUERY_END_MARKER
SELECT events.* FROM events 
	LEFT OUTER JOIN recurrence ON recurrence.event_id = events.id 
	WHERE calendar_id = $calendar_id AND $dateString >= date(date_start) 
		AND $dateString <= date(COALESCE(date_end, $dateString)) 
		AND eventperiod_id <> 1
		AND (recurrence.id is null OR 
				  (ceiling(dayofmonth(date($dateString)) / 7) = recurrence.modifier 
					or recurrence.modifier = 0 
					or ( (dayofmonth(last_day($dateString)) - dayofmonth(date($dateString))) < 7 and recurrence.modifier = 5)
				  )  	 
				  AND recurrence.day = dayname(date($dateString))
			) 
ORDER BY events.date_start, events.time_start, events.id DESC
QUERY_END_MARKER;
		return Events::FindBySql('Events', $query);
	}
	
	function FindUpcoming($number = 5, $calendar_id = 1, $order_by = "ASC")
	{
		// Find Single- or Multi-day events that have not yet begun
		$dateString = date("Y-m-d");
		return Events::FindBySql('Events', "SELECT * FROM events WHERE 
			date('$dateString') <= date(date_start) 
			ORDER BY date_start $order_by LIMIT $number");
	}
	
	function FindOngoing($number = 5, $calendar_id = 1, $order_by = "ASC")
	{
		// Find Multi-day events that have not yet ended
		$dateString = date("Y-m-d");
		return Events::FindBySql('Events', "SELECT * FROM events WHERE 
		date('$dateString') >= date(date_start) AND date('$dateString') <= date(date_end)
		AND date_end IS NOT null 
		ORDER BY date_end $order_by LIMIT $number;");
	}
	
	function FindUpcomingAndOngoing($number = 5, $calendar_id = 1, $order_by = "ASC")
	{
		// Find Single-day events that have not yet begun and Multi-day events that have not yet ended
		$dateString = date("Y-m-d");
		return Events::FindBySql('Events', "SELECT * FROM events WHERE 
		date('$dateString') <= date(date_start) 
		OR date('$dateString') <= date(date_end)   
		ORDER BY date_start $order_by LIMIT $number;");
	}
	
	function FindUpcomingWithinDateRange($number = 10, $order_by = "ASC", $days = 7)
	{
		$RangeStart = date("Y-m-d");
		$RangeEnd = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$days, date("Y")));
		
		return Events::FindBySql('Events', "SELECT * FROM events WHERE 
			date('$RangeEnd') >= date(date_start) AND date('$RangeStart') <= date(date_end) OR 
			date('$RangeStart') >= date(date_start) AND date('$RangeEnd') <= date(date_end) 			
			ORDER BY events.date_start $order_by LIMIT $number");
	}
	
	function FindPast($calendar_id = 1, $limit = 5, $order_by = "ASC")
	{
		$date = date("Y-m-d");
		return Events::FindBySql('Events', "SELECT events.* FROM events WHERE calendar_id = $calendar_id AND date(date_end) < date('$date') ORDER BY events.date_start $order_by LIMIT $limit");
	}
	
	function FindUpcomingForICal($days = 7, $number = 10, $order_by = "ASC")
	{
		$RangeStart = date("Y-m-d");
		$RangeEnd = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$days, date("Y")));
		
		return Events::FindBySql('Events', "SELECT * FROM events WHERE 
			date('$RangeStart') <= date(date_start) AND
			date('$RangeEnd') >= date(date_start)
			ORDER BY events.date_start $order_by LIMIT $number");
	}
	
	// Expects an event object, Finds events with a date greater than this one, in ASC order, Limited to one
	function FindPrev( $event ) 
	{
    	$thisdate = $event->date_start;
    	//$thistime = $event->time_start; 
    	
    	// Problem: events with the default time start of 4:00am mess this all up, but, 
    	// ideally, we use time_start as well to find a date on the same day that starts later in the day
    	// AND date(time_start) > date('$thistime')
    	return Events::FindBySql('Events', "SELECT * FROM events WHERE 
			date(date_start) <= date('$thisdate') 
			ORDER BY date_start, time_start ASC LIMIT 1");
	}
	
	function FindNext( $event ) 
	{
    	$thisdate = $event->date_start;
    	//$thistime = $event->time_start; 
    	
    	// Problem: events with the default time start of 4:00am mess this all up, but, 
    	// ideally, we use time_start as well to find a date on the same day that starts later in the day
    	// AND date(time_start) > date('$thistime')
    	return Events::FindBySql('Events', "SELECT * FROM events WHERE 
			date(date_start) >= date('$thisdate') 
			ORDER BY date_start, time_start ASC LIMIT 1");
	}
	
	
	/* ! General ->get functions - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	function getDescription()
	{
		$this->the_content();
	}
	
	function getEventType()
	{
		return EventTypes::FindById($this->eventtype_id);
	}
	
	function getEventPeriod()
	{
		return EventPeriods::FindById($this->eventperiod_id);
	}
	
	function getEventLink( $area="events", $page="calendar" ) {
		list($month,$day,$year) = explode("/", $this->getDateStart("date"));
		return get_link("/$area/$page/$year/$month/$day/$this->id");
	}
	
	function getEventEditLink() {
		list($month,$day,$year) = explode("/", $this->getDateStart("date"));
		return get_link("/admin/edit_event/$year/$month/$this->id");
	}
	
	function getDateStart($part = "all")
	{
		switch ($part) {
		case "all":
		    return formatDateTimeView(substr($this->date_start, 0, 10)." ".$this->time_start);
		    break;
		case "date":
		    return formatDateView($this->date_start);
		    break;
		case "time":
			if ($this->time_start == "04:00:00")
			{
			 return ""; 
			} else {
				return formatTimeView($this->time_start);
			}
			break;
		}
	}
	
	function getDateEnd($part = "all")
	{
		if($this->date_end)
		{
			switch ($part) {
			case "all":
			    return formatDateTimeView(substr($this->date_start, 0, 10)." ".$this->time_end);
			    break;
			case "date":
			    return formatDateView($this->date_end);
			    break;
			case "time":
			    if ($this->time_end == "04:00:00")
				{
					return ""; 
				} else {
					return formatTimeView($this->time_end);
				}
			    break;
			}
		}
		else
		{
			return "";
		}
	}
	
	function getDateRangeString()
	{
		$range_string = formatDateView($this->date_start);
		if($this->date_end && $this->date_start != $this->date_end)
		{
			$range_string .= " to " . formatDateView($this->date_end);
		}
		return $range_string;
	}
	
	function getDateRangeStringWithRecurrence($part = "all")
	{
		switch ($part) {
		case "all":
			// It is possible that this is a single day event, so check that first
			if ( $this->is_multiday() ) 
			{
				// Only pass multi day events to this function
				$range_string = $this->formatSpecialRecurrence(); 
				if ($range_string != "All Year")
				{
					$range_string .= " from ".$this->getDateStart("date")." to ".$this->getDateEnd("date"); 
				}
			} else {
				$range_string = formatDateView($this->date_start, "l\, m/d/Y");
			}
		    
		    if ($this->getDateStart("time") && $this->getDateStart("time") != "4:00 AM" && $this->getDateStart("time") != "12:00 AM") 
			{ 
				$range_string .= " &ndash; ".$this->getDateStart("time")." "; 
				if ($this->getDateEnd("time") && $this->getDateEnd("time") != "4:00 AM" && $this->getDateEnd("time") != "12:00 AM") 
				{ 
					$range_string .= " to ".$this->getDateEnd("time"); 
				}
			}
			
			if($this->getNotDates())
			{
				$range_string .= " <i>except these dates:</i> "; 
				foreach (explode(",", $this->getNotDates()) as $date) 
				{
					if ($date != "") 
					{
						$range_string .= "$date, ";
					}
				}
				// Chop the space and comma off the last one
				$range_string = substr($range_string, 0, -2); 
			}
			return $range_string;
		    break;
		
		case "date":
			// It is possible that this is a single day event, so check that first
			if ( $this->is_multiday() ) 
			{
				// Only pass multi day events to this function
				$range_string = $this->formatSpecialRecurrence(); 
				$range_string .= " from ".$this->getDateStart("date")." to ".$this->getDateEnd("date"); 
			} else {
				$range_string = formatDateView($this->date_start);
			}
		    
			if($this->getNotDates())
			{
				$range_string .= " <i>except these dates:</i> "; 
				foreach (explode(",", $this->getNotDates()) as $date) 
				{
					if ($date != "") 
					{
						$range_string .= "$date, ";
					}
				}
				$range_string = substr($range_string, 0, -2); 
			}
			return $range_string;
		    break;
		
		case "time":
			if ($this->getDateStart("time") && $this->getDateStart("time") != "4:00 AM" && $this->getDateStart("time") != "12:00 AM") 
			{ 
				$range_string .= " &ndash; ".$this->getDateStart("time")." "; 
				if ($this->getDateEnd("time") && $this->getDateEnd("time") != "4:00 AM" && $this->getDateEnd("time") != "12:00 AM") 
				{ 
					$range_string .= " to ".$this->getDateEnd("time"); 
				}
			}
			return $range_string;
			
			break;
		}
	}
	
	
	/* ! Formatting and Setting functions - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	function formatDateString($day, $month, $year)
	{
	    return "'" . $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT) . "'";
	}
	
	function formatRecurrence()
	{
		// DO NOT USE THIS FUNCTION ON ITS OWN... it is used in ->getDateRangeStringWithRecurrence(). Use that function instead. 
		
		// This function deals with NON-special recurrences, and is called from the formatSpecialRecurrence() function when a special parameter is not met
		$event_recurrence = Recurrence::FindForEvent($this->id);
		$previousrecurrence_def = ""; 
		$recurrence_string = ""; 
		$counter = 1;
		$countupto = count($event_recurrence); 
		$recurrence_def = array("Every","First","Second","Third","Fourth","Last");	
		
		foreach ($event_recurrence as $thisrecurrence)
		{
			// In general, we will not be going through this loop only twice... helps to remember that for formatting the iterations with commas
			if ($countupto > 1)
			{
				if ($counter == 1)
				{
					// First time through the loop... 
					if ($thisrecurrence->modifier == 0)
					{
						// We have an "Every" event...
						$recurrence_string .= "Every ".$thisrecurrence->day;
					} else {
						$recurrence_string .= $recurrence_def[$thisrecurrence->modifier]." ".$thisrecurrence->day."s";
					}
					$previousrecurrence_def = $thisrecurrence->modifier;
					$counter++; 
				} 
				else if ($counter == $countupto) 
				{
					// We ARE on the last one. Add ampersands...
					if ($previousrecurrence_def == $thisrecurrence->modifier)
					{
						// The modifier is the same, so we only add the day
						if ($thisrecurrence->modifier == 0)
						{
							$recurrence_string .= " &amp; ".$thisrecurrence->day;
						} else {
							$recurrence_string .= " &amp; ".$thisrecurrence->day."s";
						}
						$previousrecurrence_def = $thisrecurrence->modifier; 
						$counter++; 
					} else {
						// Modifier is not the same, so we need to add it to the string
						if ($thisrecurrence->modifier == 0)
						{
							$recurrence_string .= " &amp; Every ".$thisrecurrence->day;
						} else {
							$recurrence_string .= " &amp; ".$recurrence_def[$thisrecurrence->modifier]." ".$thisrecurrence->day."s";
						}
						$previousrecurrence_def = $thisrecurrence->modifier; 
						$counter++;
					}
				} 
				else  
				{
					// We are in the loop. Check and see if the modifier has changed
					if ($previousrecurrence_def == $thisrecurrence->modifier)
					{
						// The modifier is the same, so we only add the day
						if ($thisrecurrence->modifier == 0)
						{
							$recurrence_string .= ", ".$thisrecurrence->day;
						} else {
							$recurrence_string .= ", ".$thisrecurrence->day."s";
						}
						$previousrecurrence_def = $thisrecurrence->modifier; 
						$counter++; 
					} else {
						// Modifier is not the same, so we need to add it to the string
						if ($thisrecurrence->modifier == 0)
						{
							$recurrence_string .= ", Every ".$thisrecurrence->day;
						} else {
							$recurrence_string .= ", ".$recurrence_def[$thisrecurrence->modifier]." ".$thisrecurrence->day."s";
						}
						$previousrecurrence_def = $thisrecurrence->modifier; 
						$counter++;
					}
				} 
			} else {
				// Not greater than one, therefore, there is only one recurrence definition
				if ($thisrecurrence->modifier == 0)
				{
					// We have an "Every" event... so, it does not need to be plural
					$recurrence_string .= "Every ".$thisrecurrence->day;
				} else {
					$recurrence_string .= $recurrence_def[$thisrecurrence->modifier]." ".$thisrecurrence->day."s";
				}
			}
		} 
		
		// Check the string and see if we can reword a few instances that may come up
		if ($recurrence_string == "Every Monday, Tuesday, Wednesday, Thursday &amp; Friday") {
			$recurrence_string = "Weekdays"; 
		} else if ($recurrence_string == "Every Sunday, Thursday, Friday &amp; Saturday") {
			$recurrence_string = "Every Thursday through Sunday"; 
		} else if ($recurrence_string == "Every Sunday, Friday &amp; Saturday") {
			$recurrence_string = "Every Friday through Sunday"; 
		} else if ($recurrence_string == "Every Wednesday, Thursday, Friday &amp; Saturday") {
			$recurrence_string = "Every Wednesday through Saturday";
		} else if ($recurrence_string == "Every Thursday, Friday &amp; Saturday") {
			$recurrence_string = "Every Thursday through Saturday";
		} 
		return $recurrence_string; 
	}
	
	function formatSpecialRecurrence()
	{
		// DO NOT USE THIS FUNCTION ON ITS OWN... it is called from ->getDateRangeStringWithRecurrence()
		
		// This function tries to deal with a few special instances... it calls ->formatRecurrence() when needed
		$event_recurrence = Recurrence::FindForEvent($this->id);
		$firstrecurrence_mod = "";
		$firstrecurrence_day = ""; 
		$recurrence_string = ""; 
		$counter = 1;
		$countupto = count($event_recurrence); 
		$daysofweek = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"); 
		$recurrence_def = array("Every","First","Second","Third","Fourth","Last");	
		
		if (substr($this->getDateStart("date"),0,5) == "01/01" && substr($this->getDateEnd("date"),0,5) == "12/31")
		{
			// This date range encompasses the entire year
			return $recurrence_string =  "All Year"; 
		} else {
			
			// Ok, start the recurrence checking
			if ($countupto == "7") 
			{
				return $recurrence_string =  "All Week";
			}
			else if ($countupto == "6") 
			{
				$first_event_recurrence = array_shift($event_recurrence); 
				if ($first_event_recurrence->modifier == 0)
				{
					// Try to figure out which day this event is NOT on
					
					$recur_array = array(); 
					// Turn the recurrence object into an array...
					foreach ($event_recurrence as $recurrence) 
					{
						array_push($recur_array, $recurrence->day); 
					}
					// Get the difference between the two
					$difference = array_difference($recur_array, $daysofweek);
					
					// What we have is in an array, so a foreach gets it out
					foreach ($difference as $theday)
					{
						$recurrence_string = "Every day but $theday"; 
					}
				} else {
					// Not an "Every" instance, so use the normal recurrence function
					return $recurrence_string = $this->formatRecurrence();  
				}
			}
			else if ($countupto == "2") 
			{
				foreach ($event_recurrence as $recurrence)
				{
					if ($counter == 1)
					{
						// This is the first loop...
						// Just set some variables that will be checked on the second loop
						$firstrecurrence_mod = $recurrence->modifier; 
						$firstrecurrence_day = $recurrence->day; 
						$counter++;
					} else {
						// Now, check the variables before adding to the string
						if ($firstrecurrence_mod == $recurrence->modifier)
						{
							// The modifier is the same
							if ($firstrecurrence_day == $recurrence->day)
							{
								// The days are the same, like "First & Third Tuesdays"
								$recurrence_string .= $recurrence_def[$firstrecurrence_mod]." &amp; ".$recurrence_def[$recurrence->modifier]." ".$recurrence->day."s";
							} else {
								// The days are not the same
								if ($recurrence->modifier == 0)
								{
									// Like "Every Monday & Wednesday"
									$recurrence_string .= "Every $firstrecurrence_day &amp; $recurrence->day";
								} else {
									// Make the days plural, like "Second Mondays & Wednesdays"
									$recurrence_string .= $recurrence_def[$recurrence->modifier]." ".$firstrecurrence_day."s &amp; ".$recurrence->day."s";
								}
							}
						} else {
							// The modifer is different
							if ($firstrecurrence_day == $recurrence->day)
							{
								// The days are the same, like "First & Third Tuesdays"
								$recurrence_string .= $recurrence_def[$firstrecurrence_mod]." &amp; ".$recurrence_def[$recurrence->modifier]." ".$recurrence->day."s";
							} else {
								// The days are not the same, like "First Tuesdays & Third Thursdays"
								$recurrence_string .= $recurrence_def[$firstrecurrence_mod]." ".$firstrecurrence_day."s &amp; ".$recurrence_def[$recurrence->modifier]." ".$recurrence->day."s";
							}
						}
						$counter++;
					}
				}
				// Clean up a few special instances this function may create
				if ($recurrence_string == "Every Sunday &amp; Saturday")
				{
					return $recurrence_string = "Every Weekend"; 
				}
			} else {
				return $recurrence_string = $this->formatRecurrence(); 
			}
		}
		return $recurrence_string; 
	}
	
	function setDateStart($date_start = "", $time_start = "")
	{
		$datestring = parseDate($date_start . " " . $time_start);
		$this->date_start = substr($datestring, 0, 10); 
		$this->time_start = substr($datestring, 11, 8); 
	}
	
	function setDateEnd($date_end = "", $time_end = "")
	{
		$datestring = parseDate($date_end . " " . $time_end);
		$this->date_end = substr($datestring, 0, 10); 
		$this->time_end = substr($datestring, 11, 8); 
	}
	
	function is_multiday() 
	{
        // If the eventperiod_id is 1, we have a single day event. If it is three, we have a multi-day event. 
    	if ( $this->eventperiod_id == 3 ) {
        	
        	// lets be sure
        	if( $this->date_start != $this->date_end ) {
            	return true; 
        	} else {
            	return false; 
        	}
    	} else {
        	return false; 
    	}
	}
	
	/* ! Not Date Functions - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	function notDate($date) {
		$query = "SELECT * FROM events_notdate WHERE date = '$date' AND event_id = $this->id;";
		$result = mysql_query($query, MyActiveRecord::Connection());
		if (mysql_num_rows($result)>0) { return true; } 
		return false;
	}
	
	function getNotDates() {
		$query = "SELECT * FROM events_notdate WHERE event_id = $this->id;";
		$result = mysql_query($query, MyActiveRecord::Connection());
		if (mysql_num_rows($result)) {
			$count = mysql_num_rows($result);
			$notdates = " ";
			for ($i=0;$i<$count;$i++) {
				$notdates .= formatDateView(mysql_result($result, $i, 'date')).",";
			}
			return substr($notdates, 0, -1);
		}
		return false;
	}
	
	
	/* ! WordPress-like edit box feature - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	function getCalendarEditLinks($event="", $additional_button="")
	{
		if ( IsUserLoggedIn() ) {
			
			echo "<div id=\"admin-pane-controls\">\n"; 
			echo "\t<h3>Admin Controls:</h3>\n"; 
			echo "\t<a href=\"".get_link("admin/")."\">Dashboard</a>\n"; 
			echo "\t<a href=\"".get_link("admin/list_events")."\">List Events</a>\n"; 
			
			if ( $event )
			{
				list($month,$day,$year) = explode("/", $event->getDateStart("date"));
				echo "\t<a href=\"".get_link("admin/edit_event/$year/$month/".$event->id)."\">Edit this Event</a>\n"; 
			}
			echo "\t<a href=\"".get_link("admin/add_event")."\">Add an Event</a>\n"; 
			
			echo "\t<a href=\"".get_link("admin/list_event_types")."\">List Event Types</a>\n"; 
			echo "\t<a href=\"".get_link("admin/add_type")."\">Add Events Type</a>\n"; 
			
			if ( $additional_button != "" ) {
				echo $additional_button; 
			}
			echo "\t<a href=\"".get_link( "admin/logout")."\">Logout</a>\n"; 
			echo "</div>\n"; 
		}
	}
}
?>
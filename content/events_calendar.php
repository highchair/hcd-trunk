<?php
function initialize_page()
{
	$year = getRequestVarAtIndex(2);
	$month = getRequestVarAtIndex(3);
	
	$_SESSION["viewing_year"] = $year;
	$_SESSION["viewing_month"] = $month;
	
	// redirect the user to the calendar view of cuurent month/year if either is blank
	if($year == "" || $month == "")
	{
		$now = getdate();
	
		$month = $now['mon'];
		$year = $now['year'];
		redirect("/events/calendar/$year/$month");
	}
}

function display_page_content()
{
	$event_types = EventTypes::FindAll();
		
	$year = getRequestVarAtIndex(2);
	$month = getRequestVarAtIndex(3);
	$day = getRequestVarAtIndex(4);
	$event_id = getRequestVarAtIndex(5);
?>

			<script language="javascript" type="text/javascript">
				//<![CDATA[
				$().ready(function() {
					$("#eventtype").change(function() {
						var selected = $("#eventtype").val();
						if(selected == "All")
						{
							$("table.calendarTable td a").show();
						}
						else
						{
							$("table.calendarTable td a:not(." + selected + ")").hide();
							$("." + selected).show();
						}
					});
				});
				//]]>
			</script>
			
		<?php
			if ($event_id != "")
			// ! Display a particular event's details
			{
				$event = Events::FindById($event_id);
				
				$cal = new Calendar(); 
				echo $cal->getMiniMonthView("events", "calendar", $month, $year, $day, $event_id);  
		?>
			
			<div class="event_details">
				<h1><?php echo $event->title ?></h1>
				<h3><?php echo $event->getDateRangeString() ?></h3>

				<div class="event_description">
					<?php echo $event->getDescription(); ?>
					
				</div>
			</div>
			
		<?php	
			}
				else if ($day != "")
				// ! Display an events of Day page
			{
				$event = Events::FindAllForDate($day, $month, $year);
				
				$cal = new Calendar(); 
				echo $cal->getMiniMonthView("events", "calendar", $month, $year, $day, $event_id); 
				
				if (substr($day, 0, 1) == "0")
				{
					$properday = substr($day, 1, 1); 
				} else {
					$properday = $day; 
				}
				echo "\t\t\t<h2>Events for ".getFullMonthName($month)." ".$properday.", ".$year."</h2>\n";
				
				foreach($event as $theevent)
				{
		?>
		
			<div class="event_details">
				<h1><?php echo $theevent->title ?></h1>
				<h3><?php echo $theevent->getDateRangeString() ?></h3>
				
				<div class="event_description">
					<?php echo chopText($theevent->getDescription(true),100); ?>
				</div>
				<a href="<?php echo get_link("/events/calendar/$year/$month/$day/$theevent->id") ?>">Read More</a>
			</div>
		<?php	
				}
			}
				else
				// ! Display a Calendar page
			{
		?>
			
			<p>Below is our Event Calendar engine, which displays all the past and future events for your website. Use the double arrows to go back or forward in time and view previous or upcoming months. Click on any event to find out more about it. Notice how we can handle recurring events &ndash; repetitive events every week, every first day, second, third, or last. </p>
			
			<select name="eventtype" id="eventtype">
				<?php
					echo "<option value='All' selected>All Events</option>";
					
					foreach($event_types as $event_type)
					{
						echo "<option value='{$event_type->slug()}' ";
					
						echo ">$event_type->name</option>\r\n";
					}
				?>
			</select>
			<p>&nbsp;</p>

<?php
		$cal = new Calendar(); 
		if($month != "" && $year != "")
		{
			echo $cal->getMonthView($month, $year); 
		}
		else
		{
			echo $cal->getCurrentMonthView(); 
		}
	} // end the if statement 
} // end the display_page_content function
?>
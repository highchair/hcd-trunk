<?php 
//print_r($_SESSION['blaster']);
$blast_config = $_SESSION['blaster'];
$list_names = $blast_config['lists'];

//$featured_html = "";
$custom_html = "";
$upcoming_html = "";
$ongoing_html = "";

$subject = $_POST['subject_line']; 

/*if ($blast_config['featured_event'])
{
	// ! Feature Event
	$featured = Events::FindById($blast_config['featured_event']);
	$featured_type = $featured->getEventType();
	$featured_venue = $featured->getVenue($featured->id);
	$featured_dateLink = explode("/", $featured->getDateStart("date"));
	$featured_type = $featured->getEventType();
	
	ob_start();
?>

		<div id="featured">
			<?php $featured->displayImageMail(); ?>
			
			<h1><?php echo "<a href=\"".get_link("events/calendar/".$featured_dateLink[2]."/".$featured_dateLink[0]."/".$featured_dateLink[1]."/".$featured->id)."\">{$featured->title}</a>"; ?></h1>
			<?php if ($featured_venue->name != "" && $featured_venue->name != "None Selected") { ?>
			<h3>at the <?php echo $featured_venue->name; ?></h3>
			<?php } ?>
			<p><b><?php echo $featured->getDateRangeStringWithRecurrence(); ?></b></p>
			<?php echo chopText($featured->description, 200); ?>
			
			<div class="clearit"></div>
		</div>
<?php
	$featured_html = ob_get_contents();
	ob_clean();
}*/

/* ! Custom HTML - - - */
if ( isset($blast_config['custom_html']) )
{
	/* Simple Tiny MCE inoput field needs a custom Image parser */
	$custom_html = str_replace(array("\'",'\"'),array("'",'"'),$_POST['description']); 
	$custom_html = mail_image_display($custom_html);
}


/* ! Upcoming Events - - - 
 * The biggest deployment of this feature is Arts United or LivePawtucket. 
 * They included venues and Event IMages, which we removed here
 */
if ( is_array($blast_config['upcoming_events']) )
{
	ob_start();
	echo "<h2 class=\"blast-title\">Upcoming Events</h2>\n";
	foreach ($blast_config['upcoming_events'] as $event_id)
	{
		$thisevent = Events::FindById($event_id);
		//$type = $thisevent->getEventType();
		//$venue = $thisevent->getVenue($thisevent->id);
?>

		<div class="event">	
			<?php 
				/*echo '<div class="'.slug($type->name).'" title="'.$type->name.'"></div>'; 
				if ($thisevent->hasImage()) { 
					$thisevent->displayImageMail(); 
				}*/
			?>
			
			<h3 class="event-title"><a href="<?php $thisevent->the_url( "members", "calendar", true ) ?>"><?php $thisevent->the_title();?></a></h3>
			<?php //if ($venue->name != "" && $venue->name != "None Selected") { ?>
			<!--<h3>at the <?php //echo $venue->name; ?></h3>-->
			<?php //} ?>
			<p class="event-dates"><strong><?php echo $thisevent->getDateRangeStringWithRecurrence(); ?></strong></p>
			<div class="description">
			    <p><?php $thisevent->the_excerpt( '75' ) ?> <a href="<?php $thisevent->the_URL( "members", "calendar", true ) ?>">Read More</a></p>
            </div>
		</div>
<?php
	}
	$upcoming_html = ob_get_contents();
	ob_clean();
}

if ( is_array($blast_config['ongoing_events']) )
{
	// ! Ongoing Events
	ob_start();
	echo "<h2 class=\"blast-title\">Ongoing Events</h2>\n";
	
	foreach ($blast_config['ongoing_events'] as $event_id)
	{
		$thisevent = Events::FindById($event_id);
		//$type = $thisevent->getEventType();
		//$venue = $thisevent->getVenue($thisevent->id);
?>

		<div class="event">	
			<h3 class="event-title"><a href="<?php $thisevent->the_url( "members", "calendar", true ) ?>"><?php $thisevent->the_title();?></a></h3>
			<?php //if ($venue->name != "" && $venue->name != "None Selected") { ?>
			<!--<h3>at the <?php //echo $venue->name; ?></h3>-->
			<?php //} ?>
			<p class="event-dates"><strong><?php echo $thisevent->getDateRangeStringWithRecurrence(); ?></strong></p>
			<div class="description">
			    <p><?php $thisevent->the_excerpt( '75' ) ?> <a href="<?php $thisevent->the_URL( "members", "calendar", true ) ?>">Read More</a></p>
            </div>
		</div>
<?php
	}
	$ongoing_html = ob_get_contents();
	ob_clean();
}


// ! Mail Image Replacement Function
function mail_image_display($content_to_display)
{
	$pattern_recog = array("left" => array("{","{"), "right" => array("}","}"), "reg" => array("{","}"));
	
	foreach ($pattern_recog as $float => $direction)
	{
		$imagePattern = "*".$direction[0]."{2}([A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
		$imageIds = getFilterIds($content_to_display, $imagePattern);
		$images = Array();
		
		foreach($imageIds as $imageId)
		{
			if($imageId == "random")
			{
				$random_image = Images::FindRandom();
				$random_image->name = "random";
				$images[] = $random_image;
			}
			else if($imageId == "random_cover")
			{
				$random_image = Images::FindRandomCover();
				$random_image->name = "random_cover";
				$images[] = $random_image;
			}
			else
			{
				$images[] = Images::FindByName($imageId);
			}
		}
		
		foreach($images as $key => $image)
		{
			if(isset($image))
			{
				if (substr($image->description, 0, 7) == "http://")  
				{
					$replacement = "<span class=\"photo{$float}\"><a href=\"{$image->description}\"><img src=\"http://".SITE_URL.get_link("/images/view/".$image->id)."\" alt=\"".$image->title."\" /></a>";
					$replacement .= "<br /><a href=\"{$image->description}\">".$image->title."</a></span>";
				} else {
					$replacement = "<span class=\"photo{$float}\"><img src=\"http://".SITE_URL.get_link("/images/view/".$image->id)."\" alt=\"".$image->title."\" />";
					if($image->description != "")
					{
						$replacement .= "<br />".$image->description;
					}
					$replacement .= "</span>"; 
				}
				$content_to_display = updateContent($content_to_display, "*".$direction[0]."{2}{$image->name}".$direction[1]."{2}*", $replacement);
			}
		}
	}
	return($content_to_display);
}
?>
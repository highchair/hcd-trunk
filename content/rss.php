<?php 
function initialize_page()
{
	if ( getRequestVarAtIndex(0) == "rss" )
	{
		redirect("/feed/rss/"); 
	}
}

function display_page_content()
{
	error_reporting(E_ALL);
	
	// Set the Header! Important for compliance.
	header('Content-type: application/rss+xml');
	
	// Create additional parameters here, and edit the Channel info below for each site
	echo "<?xml version=\"1.0\"?>\n"; 

?><rss xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">
	<channel>
		<title><?php echo SITE_NAME ?> RSS Feed</title>
		<link>http://<?php echo SITE_URL ?></link>
		<atom:link rel="self" type="application/rss+xml" href="http://<?php echo SITE_URL.BASEHREF.getRequestVarAtIndex(0)."/".getRequestVarAtIndex(1) ?>" />
		<description><?php echo SITE_NAME ?> RSS Feed</description>
		<language>en-us</language>	
<?php 
	/* We can feed this RSS loop any number of parameters, but for now we feed it a path and then we iterate on what is supplied. Customize and add to this as needed:
	For example:
		/feed/rss/
		/events/rss/
	*/
	
	if ( getRequestVarAtIndex(0) == "feed" || getRequestVarAtIndex(0) == "blog" )
	{
		// Default action = Blog Posts
		$the_blog = Blogs::FindByID( 1 ); 
		$entries = $the_blog->getEntries(); 
		//print_r($entries); 
		
		$firstentry = $entries; 
		$firstentry = array_shift($firstentry); 
		$buildDate = date('r'); 
		
		$rss = "\t\t<pubDate>$buildDate</pubDate>\n"; 
		$rss .= "\t\t<lastBuildDate>$buildDate</lastBuildDate>\n\n"; 
			
		foreach ($entries as $entry)
		{
			$bloglink = "http://".SITE_URL.get_link( "blog/view/1/".$entry->id ); 
			
			$rss .= "\t\t<item>\n"; 
			$rss .= "\t\t\t<title>".cleanupSpecialChars($entry->title)."</title>\n"; 
			$rss .= "\t\t\t<pubDate>".date('r', strtotime($entry->date) )."</pubDate>\n";
			$rss .= "\t\t\t<link>$bloglink</link>\n"; 
			$rss .= "\t\t\t<guid isPermaLink=\"true\">$bloglink</guid>\n"; 
			$rss .= "\t\t\t<description><![CDATA[";
			
			//$blogcontent = scrub_HCd_Tags( $entry->content );
			//$blogcontent = strip_tags( $blogcontent ); 
			$blogcontent = $entry->rss_getContent(); 
			$rss .= cleanupSpecialChars( $blogcontent );
			
			$rss .= "]]></description>\n"; 
			
			if (RSS_AUTHOR) 
			{
				$rss .= "\t\t\t<dc:creator>$entry->user</dc:creator>\n"; 
			}
			$rss .= "\t\t</item>\n"; 
		}
		
	} else if ( getRequestVarAtIndex(0) == "events" ) {
		
		$entries = Events::FindUpcomingWithinDateRange(12, "ASC", 90);
	
		$firstentry = $entries; 
		$firstentry = array_shift($firstentry); 
		$buildDate = date('r'); 
		
		$rss = "\t\t<pubDate>$buildDate</pubDate>\n"; 
		$rss .= "\t\t<lastBuildDate>$buildDate</lastBuildDate>\n\n"; 
			
		foreach ($entries as $entry)
		{
			$type = $entry->getEventType();
			
			if ($entry->time_start != "04:00:00")
			{
				$entrydate = substr($entry->date_start, 0, 10)." ".$entry->time_start; 
			} else {
				$entrydate = substr($entry->date_start, 0, 10)." 12:00:00"; 
			}
	
			$dateLink = explode("/", $entry->getDateStart("date"));
			$eventlink = "http://".SITE_URL.get_link("events/calendar/".$dateLink[2]."/".$dateLink[0]."/".$dateLink[1]."/".$entry->id); 
			
			$rss .= "\t\t<item>\n"; 
			$rss .= "\t\t\t<title>".htmlentities($entry->title, ENT_QUOTES)." (".htmlentities($type->name, ENT_QUOTES).")</title>\n"; 
			date_default_timezone_set('EST');
			$rss .= "\t\t\t<pubDate>".date('r', strtotime($entrydate) )."</pubDate>\n";
			$rss .= "\t\t\t<link>$eventlink</link>\n"; 
			$rss .= "\t\t\t<guid isPermaLink=\"true\">$eventlink</guid>\n"; 
			$rss .= "\t\t\t<description>";
			
			if (RSS_IMAGE) 
			{
				if ($entry->hasImage()) 
				{
					$rss .= "&lt;img src=&quot;http://".SITE_URL.get_link("/images/eventsimage/".$entry->id)."&quot; alt=&quot;".htmlentities($entry->title, ENT_QUOTES)."&quot; &gt;\n"; 
				}
			}
			
			if (substr($entry->description, 0, 1) == "<")
			{
				$rss .= htmlentities($entry->description, ENT_QUOTES)."</description>\n"; 
			} else {
				$rss .= $entry->description."</description>\n"; 
			}
			
			if (RSS_AUTHOR) 
			{
				$rss .= "\t\t\t<dc:creator>$entry->user</dc:creator>\n"; 
			}
			$rss .= "\t\t</item>\n"; 
		}
	}
	
	echo $rss;
?>

	</channel>
</rss>
<?php
}
?>
<?php

// After an upgrade, convert a set of events to blog entries. 

// Point these to the right place manually
require_once( "../conf.php" );
require_once( "common/activerecord.php" ); 
require_once( "common/model_base.php" ); 
require_once( "common/utility.php" ); 
require_once( "models/event.php" ); 
require_once( "models/eventtype.php" ); 
require_once( "models/blog_entries.php" ); 
require_once( "models/categories.php" ); 

// Get the names of columns so we know what we need to match to: 

/*
$event_column_query = MyActiveRecord::FindBySQL( "Events", "SHOW COLUMNS FROM events" );
print_r( $event_column_query ); 

	[] => Events Object
	(
	    [id] => 
	    [calendar_id] => 
	    [eventtype_id] => 
	    [date_start] => 
	    [time_start] => 04:00:00
	    [date_end] => 0000-00-00 00:00:00
	    [time_end] => 04:00:00
	    [title] => 
	    [description] => 
	    [eventperiod_id] => 
	)

$entry_column_query = MyActiveRecord::FindBySQL( "Blog_Entries", "SHOW COLUMNS FROM blog_entries" );
print_r( $entry_column_query ); 

	[] => Blog_Entries Object
	(
	    [id] => 
	    [title] => 
	    [slug] => 
	    [date] => CURRENT_TIMESTAMP
	    [blog_id] => 
	    [author_id] => 
	    [content] => 
	    [public] => 0
	)
        
*/

$all_events = Events::FindAll(); 

$counter = 0; 

foreach ( $all_events as $event ) {
	
	//if ( $counter == 1 ) break; // For testing
	
	$newentry = MyActiveRecord::Create( 'Blog_Entries' );
	
	$newentry->title = esc_html( $event->title );
	$newentry->slug = slug( $event->title );
	$newentry->date = $event->date_start." ".$event->time_start; // format = 2012-03-14 08:03:17
	$newentry->blog_id = 1;
	$newentry->author_id = 2; // manually set to the owner of the site
	$newentry->content = $event->description;
	$newentry->public = 1;
	
	if ( $newentry->save() ) echo "&ldquo;".esc_html($newentry->title)."&rdquo; saved<br />"; 
	
	
	// Now take the venue and convert it to a Category. Categories need to be created first and match names exactly. 
	
	$event_type = $event->getEventType(); 
	$category_match = Categories::FindByName( slug($event_type->name) ); 
	
	$newentry->updateSelectedCategories( array($category_match->id) ); 
	// updateSelectedCategories does not return true on success, so, we can't echo a nice statement if this works. 
	
	$counter++; 
	
}

echo "$counter events converted to blog entries<br />"; 
<?php
class Blogs extends MyActiveRecord
{
	function FindAll() 
	{
		return MyActiveRecord::FindAll('Blogs');
	}
	
	function FindById( $id=1 ) 
	{
		return MyActiveRecord::FindById('Blogs', $id);
	}
	
	function FindByUser($user_id) 
	{
		return array_shift(MyActiveRecord::FindBySql('Blogs', "SELECT i.* FROM blogs i WHERE i.user_id='" . $user_id . "'"));
	}
	
	function FindBySlug($slug) 
	{
		return array_shift(MyActiveRecord::FindBySql('Blogs', "SELECT i.* FROM blogs i WHERE i.slug='" . $slug . "'"));
	}
	
	function isPublic()
	{
		// Check the Blog area to see if this thing is public... do not draw pages if it is not
		// Only works on Installs with a SINGLE blog of the default id of 3
		$blogarea = Areas::FindById( 3 ); 
		return $blogarea->public; 
	}
	
	// Modified Sept 18 2012 to try and return entries that have a date less than today's date AND/OR just public ones
	// For most uses, Blog_Entries::FindAll is better. 
	// Depreciate this for most cases. 
	// ONE good use case is the admin/list_entries template
	function getEntries( $onlygetfromnow=true, $onlypublic=true, $orderby="date DESC"  ) 
	{
		$dateString = date("Y-m-d H:i:s"); 
		
		$publicclause = ( $onlypublic ) ? "public = 1" : ""; 
		$dateclause = ( $onlygetfromnow ) ? "date('".$dateString."') >= date(date)" : "";
		
		$publicclause = ( $onlypublic && $onlygetfromnow ) ? $publicclause." AND " : $publicclause; 
		$where = ( $onlypublic || $onlygetfromnow ) ? "WHERE" : ""; 
		
		return MyActiveRecord::FindBySQL( 'Blog_Entries', "SELECT * FROM blog_entries $where $publicclause"."$dateclause ORDER BY $orderby" );
	}
	
	function list_entries_and_group_by_date( $blogpost_id="", $hvalue_years="h2", $hvalue_months="h3" )
	{
		/* This function draws a sub nav broken down by year and month. Also draws the parts needed to make months collapse. We pass the function an event id in case we want to keep a month open. 
		Items needed in the CSS:
			#wrapper h2
			#wrapper h3
			#wrapper .month_wrapper
			#wrapper .month_wrapper a
		*/
		
		$blogentries = $this->getEntries( true, true ); 
		$year = ""; 
		$month = ""; 
		$a_month_has_been_opened = false; 
		
		if ( $blogpost_id != "" )
		{
			$thisblogpost = Blog_Entries::FindById($blogpost_id); 
			$thisblogpost_month = date("Y-m", strtotime($thisblogpost->date)); 
		}
		
		if ( isset($thisblogpost_month) )
		{
			//$whereat = $thisblogpost_month; 
			$year = date("Y", strtotime($thisblogpost->date));
		} else {
			// Find the first month, and then reset the variables... 
			$firstentry = array_shift($blogentries); 
			$year = date("Y", strtotime($firstentry->date)); 
			$month = date("m", strtotime($firstentry->date)); 
			//$whereat = $year."-".$month; 
			array_unshift($blogentries, $firstentry);
		}
		// be sure to reset this variable back to null, as we need it to be null the first time through the foreach
		$month = ""; 
		
		/*$string_of_entries = <<<EOT
<script type="text/javascript">		
	//<![CDATA[
	function BlogAlreadyOpen(ul_id)
	{
		$('#' + ul_id).show();
		$('div.month_wrapper').each(function() {
			if ( $(this).attr('href') == '#' + ul_id ) { $(this).slideDown(); }
		});
		return true;
	}
	
	$().ready(function() {
		BlogAlreadyOpen('{$whereat}');
		var loc = "";
		
		$("a.month_trigger").click(function() {
			$('.month_wrapper').slideUp();
			
			if (loc != $(this).attr('href')) {
				$($(this).attr('href')).slideDown();
				loc = $(this).attr('href');
			} else {
				loc = "";			
			}
			return false;
		});
	});
	//]]>
</script>	
EOT;*/
		// First: Output the first year header
		$string_of_entries = "<$hvalue_years>$year</$hvalue_years>\n";
		
		foreach ($blogentries as $theentry)
		{
			// Now output the month and save it
			$thisentrymonth = date("Y-m", strtotime($theentry->date));
			$ProperMonth = date("F", strtotime($theentry->date));
			$thisblogpost_month = $showthediv = ""; 
			
			if ( $month == "" )
			{
				// This is the first time through the loop, as the month is not set yet
				$string_of_entries .= "<$hvalue_months><a href=\"#{$thisentrymonth}\" class=\"month_trigger\">$ProperMonth</a></$hvalue_months>\n";
				$month = $thisentrymonth; 
				// First time through, check if there a particular entry to show
				//if ( isset($thisblogpost_month) && $thisblogpost_month != "" ) { $showthediv = " hideme"; }
				// Open the entry wrapper
				$string_of_entries .= "<div id=\"{$month}\" class=\"month_wrapper hideme\">\n"; 
				
			} 
			elseif ( $month != $thisentrymonth ) 
			{
				// Month is set, and it is not the same as this entry's month, so we need a new header
				$string_of_entries .= "</div>\n";
				
				// Next, check if the year has changed. Output it between the divs
				$thisentryyear = date("Y", strtotime($theentry->date));
				if ( $year != $thisentryyear) 
				{
					$string_of_entries .= "<$hvalue_years>$thisentryyear</$hvalue_years>\n";
					$year = $thisentryyear; 
				}
				
				$string_of_entries .= "<$hvalue_months><a href=\"#{$thisentrymonth}\" class=\"month_trigger\">$ProperMonth</a></$hvalue_months>\n";
				$month = $thisentrymonth;
				// Second or third time through... check if there is a particular post to show, and whether or not it matches this month we are trying to draw
				//if ( $thisblogpost_month != $thisentrymonth ) { $showthediv = " hideme"; }
				// Open the entry wrapper
				$string_of_entries .= "<div id=\"{$month}\" class=\"month_wrapper hideme\">\n"; 
			}
			
			// Finally output the post name and a link
			$string_of_entries .= "<a href=\"".$theentry->get_URL()."\">".$theentry->get_title()."</a>\n";
		}	
		// Close the last month that got opened 
		$string_of_entries .= "</div>\n";
		return $string_of_entries; 
	}
	
	// Updated June 9, 2012 to check the public field and remove the reliance on the_blog ID
	function getNextEntry( $date, $public=true )
	{
		$publiccondition = ( $public ) ? 'public = 1 AND ' : ''; 
		return array_shift( MyActiveRecord::FindBySql( 'Blog_Entries', "SELECT * FROM blog_entries WHERE {$publiccondition}date(date) > date('{$date}') AND date != '{$date}' ORDER BY date ASC LIMIT 1" ) ); 
	}
	
	// Updated June 9, 2012 to check the public field and remove the reliance on the_blog ID
	function getPrevEntry( $date, $public=true )
	{
		$publiccondition = ( $public ) ? 'public = 1 AND ' : ''; 
		return array_shift( MyActiveRecord::FindBySql( 'Blog_Entries', "SELECT * FROM blog_entries WHERE {$publiccondition}date(date) < date('{$date}') AND date != '{$date}' ORDER BY date DESC LIMIT 1" ) ); 
	}
	
	// Created Sept 14, 2012 to list out the categories and create public links for them.
	function list_categories( $hide_empty_cats=true, $show_counts=false, $hide_uncategorized=true ) 
	{
    	$categories = Categories::FindAll();
    	
    	$catlist = '<ul class="category-list menu">'; 
    	$output = false; 
    	
    	foreach( $categories as $cat )
		{
			// This is a public-facing function, so we only get Public posts and ones that are not future dated
			$entries = $cat->getEntries( true, true ); 
			
			$count = count( $entries ); 
			$counter = ( $show_counts ) ? ' ('.$count.')' : ""; 
			
			if ( $hide_uncategorized && $cat->name == 'uncategorized' ) continue; 
			
			if ( $hide_empty_cats ) {
    			
    			if ( $count > 0 ) 
        			$catlist .= '<li><a class="category-link" href="'.$cat->get_public_url().'">'.$cat->get_title().' '.$counter.'</a></li>';
        			$output = true;
			} else {
    			$catlist .= '<li><a class="category-link" href="'.$cat->get_public_url().'">'.$cat->get_title().' '.$counter.'</a></li>';
    			$output = true;
			}
		}
		$catlist .= '</ul>';
		
		if ( $output ) return $catlist; 
	}
	
	/* ! WordPress-like edit box feature - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	function DisplayBlogEditLinks($the_blog="", $entry="", $additional_button="")
	{
		if ( !$the_blog )
		{
			$the_blog = Blogs::FindById("1"); 
		}
		
		if ( IsUserLoggedIn() ) {
			
			echo "<div id=\"admin-pane-controls\">\n"; 
			echo "\t<h3>Admin Controls:</h3>\n"; 
			echo "\t<a href=\"".get_link( "admin/list_entries/" )."\">List Blog Entries</a>\n"; 
			
			if ( $entry )
			{
				echo "\t<a href=\"".get_link( "admin/edit_entry/".$entry->id )."\">Edit this Entry</a>\n"; 
			}
			echo "\t<a href=\"".get_link( "admin/edit_entry/add" )."\">Add an Entry</a>\n"; 
						
			if ( $additional_button != "" ) {
				echo $additional_button; 
			}
			echo "\t<a href=\"".get_link( "admin/logout")."\">Logout</a>\n"; 
			echo "</div>\n"; 
		}
	}
}
?>
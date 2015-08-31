<?php	
	function initialize_page() {}
	
	function display_page_content()
	{
		// We use the old way to get all entries, because the FindAll function forces an exclusion date
		$the_blog = Blogs::FindById( BLOG_DEFAULT_ID ); 
		$entries = $the_blog->getEntries( false, false );
		
		$categories = Categories::FindAll(); 
		
		$year_month = $year = $month = $extraheader = "";  
		
		$default_open = "opened"; 
		
		$thiscategory_id = requestIdParam(); 
		$thiscategory = Categories::FindById( $thiscategory_id ); 
		
		if ( is_object($thiscategory) ) {
			$default_open = ""; 
			$extraheader = "from &ldquo;".$thiscategory->display_name."&rdquo;";
			$entries = $thiscategory->getEntries( false, false ); 
		} 
?>
	
	<div id="edit-header" class="entrynav">		
		<div class="nav-left column">
			<h1>Edit <?php echo ucwords( BLOG_STATIC_AREA ) ?> Entries <?php echo $extraheader.' :: <a href="'.get_link(BLOG_STATIC_AREA).'" title="Click to View All Entries">View Entries</a>' ?></h1>
		</div>
		<div class="nav-right column">
			<a class="hcd_button" href="<?php echo get_link("/admin/edit_entry/add") ?>">Add an Entry</a>
			<a class="hcd_button" href="<?php echo get_link("/admin/list_categories/") ?>">Edit Categories</a>
			<a class="hcd_button" href="<?php echo get_link("/admin/add_category/") ?>">Add a Category</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<ul id="sort-list" class="menu tabs">
		<li><a class="<?php echo $default_open ?>" href="<?php echo get_link("admin/list_entries/") ?>">All Categories</a></li><?php
		
		foreach ( $categories as $category ) {
			
			$posts = $category->getEntries( false, false ); 
			
			if ( count( $posts ) > 0 ) {			
				$openclass = ( $category->id == $thiscategory_id ) ? "opened" : ""; 
				echo "<li><a class=\"$openclass\" href=\"".get_link("admin/list_entries/".$category->id)."\">$category->display_name</a></li>"; 
			}
		} 
	?>
	
	</ul>
	<div class="clearleft"></div>
	
<?php if ( count( $entries ) > 0 ) { ?>
	<div id="table-header" class="entries">
		<strong class="item-link">Entry Name</strong>
		<span class="item-public">Public</span>
		<span class="item-created">Publication Date</span>
		<span class="item-revised">Author</span>
	</div>
	
	<ul id="listitems" class="managelist">
	<?php
		foreach( $entries as $entry ) {
			
			$blogyear_month = parseDate($entry->date, "Y-m");
			$blogyear = parseDate($entry->date, "Y");
			$blogmonth = parseDate($entry->date,"F");
			
			if ( $blogyear_month != $year_month ) {
				echo "\t\t<li class=\"monthname\">$blogmonth $blogyear</li>";
				$year_month = $blogyear_month; 
				$year = $blogyear;
				$month = $blogmonth;  
			}
			$public = ( $entry->public ) ? "" : "<span class=\"red\">(not public)</span>"; 

			echo "\t\t<li>
				<a class=\"item-link\" href=\"".get_link("/admin/edit_entry/$entry->id")."\">$entry->title</a>
				<span class=\"item-public\">$public</span>
				<span class=\"item-created\">".formatDateTimeView( $entry->date )."</span>
				<span class=\"item-revised\">".$entry->get_author()."</span>
			</li>\n"; 
		}
?>

	</ul>
<?php 
		} else {
    		echo "\t\t<h3 class=\"empty-list\">There are no ".BLOG_STATIC_AREA." entries to edit. <a class=\"short\" href=\"".get_link("admin/add_entry")."\">Add one if you like</a>.</h3>"; 
		} 
	}
?>
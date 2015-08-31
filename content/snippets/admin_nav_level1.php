<?php 
	$contentpages = array(
		"edit_area", "add_page", "edit_page", "list_pages", 
		"edit_chunk", 
		"portfolio_add_area", "portfolio_edit_area", "portfolio_add_section", "portfolio_edit_section", 
		"portfolio_add_item", "portfolio_edit", "portfolio_list", 
		"add_event", "edit_event", "list_events", 
		"add_type", "edit_type", "list_event_types", 
		"edit_blog", "edit_entry", "list_entries", "add_category", "edit_category", "list_categories",
		"mail_blast", "add_list", "edit_list", "list_lists", "view_old", "list_subscribers"
	); 
	$thispage = get_content_page();
	
	if ( in_array($thispage->name, $contentpages) ) { 
        $displaycontent = " opened";
        $openorclosed = " menu-close"; 
    } else { 
        $displaycontent = $openorclosed = ""; 
    } 
	
	$user = Users::GetCurrentUser();
?>
					<h4><a id="contentbutton" class="openmenu<?php echo $openorclosed; ?>" href="#managetext">Manage Content</a></h4>
					<div id="managetext" class="menudrop<?php echo $displaycontent; ?>">
						<h4>Pages</h4>
						<a href="<?php echo get_link("admin/list_pages"); ?>">Edit Areas/Pages</a>
						<a href="<?php echo get_link("admin/add_page"); ?>">Add a new Page</a>  
						<a href="<?php echo get_link("admin/edit_area/add"); ?>">Add a new Area</a>  
					<?php if (BLOG_INSTALL) { 
							$blog = Blogs::FindById( 1 ); 
							if ( is_object($blog) ) { ?>
					
						<h4><?php echo ucwords( BLOG_STATIC_AREA ) ?></h4>
						<a href="<?php echo get_link("/admin/list_entries/") ?>">Edit <?php echo ucwords( BLOG_STATIC_AREA ) ?> Entries</a>
						<a href="<?php echo get_link("/admin/edit_entry/add") ?>">Add <?php echo ucwords( BLOG_STATIC_AREA ) ?> Entry</a>
						<a href="<?php echo get_link("/admin/list_categories/") ?>">Add/Edit Categories</a>
					<?php }
						}
						if (PORTFOLIO_INSTALL) { 
    						$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
						?>	
						
						<h4>Portfolio</h4>
						<a href="<?php echo get_link($main_portlink); ?>">Edit Portfolio Sections/Items</a>
						<a href="<?php echo get_link("admin/portfolio_add_item"); ?>">Add a New Item</a>  
						<a href="<?php echo get_link("admin/portfolio_add_section"); ?>">Add a New Section</a>
						<a href="<?php echo get_link("admin/portfolio_add_area"); ?>">Add a New Portfolio Area</a>
					<?php } 
						if (CALENDAR_INSTALL) { ?>
						
						<h4>Calendar</h4>
						<a href="<?php echo get_link("admin/list_events") ?>">Edit Events</a>
						<a href="<?php echo get_link("admin/add_event"); ?>">Add Event</a>
						<?php if ( ALLOW_EVENT_TYPES ) { ?>
						<a href="<?php echo get_link("admin/list_event_types") ?>">Edit Event Types</a>
						<a href="<?php echo get_link("admin/add_type"); ?>">Add a New Event Type</a>
					<?php 
					        }
					    } 
						if(BLAST_INSTALL) { ?>
						
						<h4>E-Newsletter</h4>	
						<a href="<?php echo get_link("/admin/mail_blast"); ?>">Send a Email Blast</a>
						<a href="<?php echo get_link("/admin/view_old"); ?>">View Old Blasts</a>
						<a href="<?php echo get_link("/admin/list_lists"); ?>">Edit Mailing Lists</a>
						<a href="<?php echo get_link("admin/add_list"); ?>">Add a New List</a>
						<a href="<?php echo get_link("admin/list_subscribers"); ?>">List Subscribers</a>
					<?php } ?> 
					
					</div>
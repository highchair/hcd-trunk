<?php
	// THIS ONE DOES IT ALL!! modify, simplify as needed. Format is for sliding menus, with counter for a li.first
	
	$area = get_content_area();
	$areas = Areas::FindPublicAreas();
	$open_page = $var1; 
	
	if ( PORTFOLIO_INSTALL && $area->is_portfolioarea() ) {
		$thissection = $page; 
		$open_section = $var1; 
	}
	if ( BLOG_INSTALL ) {
		$blog_id = "1"; 
		$the_blog = Blogs::FindById( $blog_id );
	}
	
	// We use this if statement to try and feed the javascript which menu to leave open
	if ( getRequestVarAtIndex() == "events" ) {
		$whereat = "";
	} elseif ( is_object($area) ) {
		$whereat = "area_".$area->name;
	} else {
		$whereat = "area_".$var0; 
	}
?>

<script type="text/javascript">
	//<![CDATA[
	function alreadyOpen(ul_id)
	{
		$('ul#' + ul_id).show();
		return true;
	}
	$(document).ready(function() {
		alreadyOpen('<?php echo $whereat; ?>');
		
		var loc = "";
		
		$("a.reveal").click(function() {
			$('ul.sub_menu').slideUp();
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
	
	<ul class="navigation menu">
<?php 
	$area_counter = 1; 
	
	foreach( $areas as $index => $menu_area )
	{
		if ( $menu_area->is_blogarea() ) 
		{
			if ( BLOG_INSTALL ) {
				// Blog button
				echo "\t\t<li class=\"area-{$area_counter} area_id-{$menu_area->id}\"><a href=\"".get_link( BLOG_STATIC_AREA )."\">".$menu_area->get_title()."</a></li>\n";
				$area_counter++; 
			}
		} 
		else if( $menu_area->is_portfolioarea() && PORTFOLIO_INSTALL )
		{
			// Portfolio Area
			$portselected = ( $area && $menu_area->name == $area->name ) ? "class=\"selected\" " : ""; 
			
			echo "\t\t<li class=\"area-{$area_counter} area_id-{$menu_area->id}\"><a {$portselected}href=\"".$menu_area->get_url()."\">".$menu_area->get_title()."</a>\n";
			echo "\t\t\t<ul id=\"area_{$menu_area->name}_section\" class=\"sub_menu\">\n";
			
			// Portfolio Sections
			$sections = $menu_area->getSections();
			$sect_counter = 1; 
			foreach( $sections as $index => $menu_section )
			{
				if( $menu_section->name != "" )
				{
					$sectselected = ( $open_section && $menu_section->name == $open_section ) ? "selected" : ""; 
					
					echo "\t\t\t\t<li class=\"section-{$sect_counter} section_id-{$menu_section->id}\"><a class=\"$sectselected \" href=\"".get_link( $menu_area->name."/".$menu_section->name )."\">".$menu_section->get_title()."</a></li>\n";
					$sect_counter++; 
				}
			}
			echo "\t\t\t</ul>\n\t\t</li>\n";
			$area_counter++;
		}
		else if( $menu_area->name != "" ) 
		{
			// gets pages
			$menu_pages = $menu_area->findPages();
			
			// Area Names
			$areaselected = ( $area && $menu_area->name == $area->name ) ? " selected" : ""; 
			
			// IF: Counts the number of sub pages to make sure it should build a sub nav
			if ( count($menu_pages) > 1 ) 
			{
				echo "\t\t<li class=\"area-{$area_counter} area_id-{$menu_area->id}\"><a class=\"reveal{$areaselected}\" href=\"#area_{$menu_area->name}\">".$menu_area->get_title()."</a>\n";
				echo "\t\t\t<ul id=\"area_{$menu_area->name}\" class=\"sub_menu\">\n";
				
				// Page Names
				$page_counter = 1; 
				foreach( $menu_area->findPages() as $menu_page )
				{
					$pageselected = ( $open_page && $menu_page->name == $open_page ) ? "selected" : ""; 
					
					echo "\t\t\t\t<li class=\"page-{$page_counter} page_id-{$menu_page->id}\"><a class=\"$pageselected\" href=\"".get_link( $menu_area->name."/".$menu_page->name )."\">".$menu_page->get_title()."</a></li>\n";
					$page_counter++;
				}					
				echo "\t\t\t</ul>\n\t\t</li>\n";
			} else {
				// Otherwise, we make the Area Name the link
				echo "\t\t<li class=\"area-{$area_counter} area_id-{$menu_area->id}\"><a class=\"$areaselected\" href=\"".$menu_area->get_url()."\">".$menu_area->get_title()."</a></li>\n";
			}
			$area_counter++; 
		}
	}
?>
	</ul>

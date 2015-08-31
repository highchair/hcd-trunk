<?php
	// THIS ONE DOES IT ALL!! modify, simplify as needed. Format is for sliding menus, with counter for a li.first
	$areas = Areas::FindPublicAreas();	
	$total_areas = count( $areas ); 
	$area_counter = 1; 
	$open_page = $open_section = $var1; 
	
	// Subtract the global area
	$total_areas = $total_areas - 1; 
	
	// Hide some areas... these are the IDs 
	$hiddenareas = array( '1','' ); 
echo '<ul class="mainnav--list">'; 

	foreach( $areas as $index => $menu_area ) {
		
		// Pass over the hidden areas
		if ( ! in_array( $menu_area->id, $hiddenareas ) ) {
		    
		    $areaselected = ( isset($var0) && $menu_area->name == $var0 ) ? " mainnav--link__disabled" : ""; 
		    
    		if ( $menu_area->is_blogarea() and BLOG_INSTALL ) {
				echo '<li class="area-'.$area_counter.' area_id-'.$menu_area->id.'"><a class="mainnav--link'.$areaselected.'"  href="'.get_link( BLOG_STATIC_AREA ).'">'.$menu_area->get_title().'</a></li>';
				$area_counter++; 
    		
    		} elseif( $menu_area->is_portfolioarea() && PORTFOLIO_INSTALL ) {
			
    			// Portfolio Area
    			$portselected = ( is_object($area) && $menu_area->name == $area->name ) ? " mainnav--link__disabled" : ""; 
    			
    			echo '<li class="area-'.$area_counter.' area_id-'.$menu_area->id.'"><a class="mainnav--link'.$portselected.'" href="'.$menu_area->get_url().'">'.$menu_area->get_title().'</a>';
    			
    			// Portfolio Sections
    			$sections = $menu_area->getSections();
    			
    			if ( count($sections) > 1 ) {
        			$sect_counter = 1; 
        			
        			echo '<ul class="mainnav--subnav--list">'; 
        			foreach( $sections as $index => $menu_section ) {
        				
        				if( $menu_section->name != "" ) {
        					$sectselected = ( $open_section && $menu_section->name == $open_section ) ? " mainnav--subnav--link__disabled" : ""; 
        					
        					echo '<li class="section-'.$sect_counter.' section_id-'.$menu_section->id.'"><a class="mainnav--subnav--link'.$sectselected.'" href="'.get_link( $menu_area->name."/".$menu_section->name ).'">'.$menu_section->get_title().'</a></li>';
        					$sect_counter++; 
        				}
        			}
        			echo "</ul>";
    			}
    			echo "</li>";
    			$area_counter++;
    		
    		} else {
    			
    			// get pages
    			$menu_pages = $menu_area->findPages();
    			
    			// IF: Counts the number of sub pages to make sure it should build a sub nav
    			if ( count($menu_pages) > 1 ) {
    				
    				echo '<li class="area-'.$area_counter.' area_id-'.$menu_area->id.' has-subnav"><a class="mainnav--link'.$areaselected.'" href="'.$menu_area->get_url().'">'.$menu_area->get_title().'</a><ul class="mainnav--subnav--list">';
    				
    				// Page Names
    				$page_counter = 1; 
    				foreach( $menu_area->findPages() as $menu_page ) {
    					$pageselected = ( $open_page && $menu_page->name == $open_page ) ? " mainnav--subnav--link__disabled" : ""; 
    					
    					echo '<li class="page-'.$page_counter.' page_id-'.$menu_page->id.'"><a class="mainnav--subnav--link'.$pageselected.'" href="'.$menu_page->get_url().'">'.$menu_page->get_title().'</a></li>';
    					$page_counter++;
    				}					
    				echo '</ul></li>';
    			} else {
    				
    				// Otherwise, we make the Area Name the link
    				echo '<li class="area-'.$area_counter.' area_id-'.$menu_area->id.'"><a class="mainnav--link'.$areaselected.'" href="'.$menu_area->get_url().'">'.$menu_area->get_title().'</a></li>';
    			}
    			
    			$area_counter++; 
    		}
		}
	}

	echo '</ul><!-- /.mainnav--list -->'; 
?>
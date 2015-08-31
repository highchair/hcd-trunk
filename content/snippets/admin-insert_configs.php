<!-- Start Insertables -->
<?php
	// These are the pages that this include gets used on... 
	$pages = array( "add_page", "edit_page" ); 
	$portfolio = array( "portfolio_add_area", "portfolio_add_item", "portfolio_add_section", "portfolio_edit_area", "portfolio_edit" ); 
	$events = array( "add_event", "edit_event" ); 
	$blog = array( "add_entry", "edit_entry" ); 
	
	/* Here's the what elements should be allowed where...
	 * Pages - can have anything...
	 * Blog Entries - can have anything
	 * Event descriptions - can have anything
	 * Portfolio - can NOT have galleries
	 */
?>

<div id="opendropslides">
<?php 
	if ( in_array(getRequestVarAtIndex(1), $pages ) ) { 
		if (SUB_PAGES) { ?>
		
	<p><a href="#areachild" class="openclose droplink">Edit Areas/Sub Pages</a>
<?php } else { ?>
			
	<p><a href="#areachild" class="openclose droplink">Edit Areas</a>
<?php 
		}
	} 
?>

		<a href="#doc_insert" class="openclose droplink">Insert Documents</a>
		<a href="#img_insert" class="openclose droplink">Insert Image</a>
		<?php if ( GALLERY_INSTALL && !in_array(getRequestVarAtIndex(1), $portfolio) ) { ?><a href="#gal_insert" class="openclose droplink">Insert Gallery</a><?php } ?>
		<?php if ( PORTFOLIO_INSTALL && ( ITEM_GALLERY_INSERT && !in_array(getRequestVarAtIndex(1), $portfolio) ) ) { ?><a href="#itemgal_insert" class="openclose droplink">Insert Gallery from Item</a><?php } ?>
		<?php if ( VIDEO_INSTALL ) { ?><a href="#video_insert" class="openclose droplink">Insert Video</a><?php } ?>
		<?php if ( TESTIMONIAL_INSTALL ) { ?><a href="#test_insert" class="openclose droplink">Insert Testimonial</a><?php } ?>
		<?php if ( PRODUCT_INSTALL ) { ?><a href="#prod_insert" class="openclose droplink">Insert Product</a><?php } ?>
		<a href="#hide_all" class="openclose droplink">Close</a>
	</p>
<?php 
	if ( in_array(getRequestVarAtIndex(1), $pages ) )
	{
		echo "\t<div id=\"areachild\" class=\"dropslide\"";
		if (getRequestVarAtIndex(1) == "add_page")
		{
			echo ">\n"; 
		} else {
			echo " style=\"display: none;\">\n"; 
		}
		( SUB_PAGES ) ? require_once( snippetPath("admin-area_child") ) : require_once( snippetPath("admin-area") ); 
		echo "\t</div>\n"; 
	}
	$documents = Documents::FindAll();
	require_once( snippetPath("admin-insertDoc") ); 
	
	$images = Images::FindAll();
	require_once( snippetPath("admin-insertImage") ); 
	
	if ( GALLERY_INSTALL && !in_array(getRequestVarAtIndex(1), $portfolio) ) { 
		$galleries = Galleries::FindAll( "DESC" );
		require_once( snippetPath("admin-insertGallery") ); 
	}
	if ( PORTFOLIO_INSTALL && ( ITEM_GALLERY_INSERT && !in_array(getRequestVarAtIndex(1), $portfolio) ) ) { 
		//$galleries = Galleries::FindAll();
		require_once( snippetPath("admin-insertItemGallery") ); 
	}
	if ( VIDEO_INSTALL ) { 
		require_once( snippetPath("admin-insertVideo") ); 
	}
	if ( TESTIMONIAL_INSTALL ) { 
		require_once( snippetPath("admin-insertTestimonial") ); 
	}
	if ( PRODUCT_INSTALL ) { 
		$products = Product::FindAll();
		require_once( snippetPath("admin-insertProduct") ); 
	}
?>
								
	<div class="clearleft"></div>
</div>
<!-- End Insertables -->
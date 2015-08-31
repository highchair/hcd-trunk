<?php 
	$extrapages = array(
		"add_image", "edit_image", "list_images", 
		"add_gallery", "edit_gallery", "list_galleries", 
		"add_document", "edit_document", "list_documents", 
		"edit_video", "list_videos", 
		"edit_testimonial", "list_testimonials", 
		"add_product", "edit_product", "list_products", 
		"alias_add", "alias_edit", "alias_list"
	); 
	$thispage = get_content_page();
	
	if ( in_array($thispage->name, $extrapages) ) { 
        $displayextras = " opened";
        $openorclosed = " menu-close"; 
    } else { 
        $displayextras = $openorclosed = ""; 
    }
?>
					<h4><a id="extrasbutton" class="openmenu<?php echo $openorclosed; ?>" href="#manageextras">Manage Extras</a></h4>
					<div id="manageextras" class="menudrop<?php echo $displayextras; ?>">
						<h4>Documents</h4>	
						<a href="<?php echo get_link("admin/list_documents"); ?>">Edit Documents</a>
						<a href="<?php echo get_link("admin/add_document"); ?>">Upload a new Document</a>
						
						<h4>Images</h4>	
						<a href="<?php echo get_link("admin/list_images"); ?>">Edit Images</a>
						<a href="<?php echo get_link("admin/add_image"); ?>">Upload a new Image</a>
					<?php if (GALLERY_INSTALL) { ?>
					
						<h4>Galleries</h4>	
						<a href="<?php echo get_link("admin/list_galleries"); ?>">Edit Galleries</a>
						<a href="<?php echo get_link("admin/add_gallery"); ?>">Add a new Gallery</a>
					<?php } 
					    if (VIDEO_INSTALL) { ?>
        				
        				<h4>Videos</h4>	
						<a href="<?php echo get_link("admin/list_videos"); ?>">Edit Videos</a>
						<a href="<?php echo get_link("admin/edit_video/add"); ?>">Add a new Video</a>
					<?php }
    					if (TESTIMONIAL_INSTALL) { ?>
        				
        				<h4>Testimonials</h4>	
						<a href="<?php echo get_link("admin/list_testimonials"); ?>">Edit Testimonials</a>
						<a href="<?php echo get_link("admin/edit_testimonial/add"); ?>">Add a new Testimonial</a>
					<?php }
						if (PRODUCT_INSTALL) { ?>
					
						<h4>Products</h4>	
						<a href="<?php echo get_link("admin/list_products"); ?>">Edit Products</a>
						<a href="<?php echo get_link("admin/add_product"); ?>">Add a new Product</a>
					<?php } 
						if (ALIAS_INSTALL) { ?>
					
						<h4>Alias Management</h4>	
						<a href="<?php echo get_link("admin/alias_list"); ?>">Edit Alias&rsquo;</a>
						<a href="<?php echo get_link("admin/alias_add"); ?>">Add an Alias</a>
					<?php } ?>
					
					</div>

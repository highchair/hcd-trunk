<?php
	// addRoute(area_name, page_name, layout_name, content_file)
	
// Front end  -> Custom Routes

	
// Admin Section -> Custom Routes


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - //	
	// Core frontend
	addRoute("images", "view", "blank", "images/view");
	addRoute("images", "preview", "blank", "images/preview");
	addRoute("images", "thumbnail", "blank", "images/thumbnail");
	addRoute("google", "sitemap", "blank", "sitemap-xml");

// ADMIN SECTION -> HCdCMS core... 
	// Core Backend routes
	addRoute("admin", "login", "admin", "admin/login");
	addRoute("admin", "logout", "admin", "admin/logout");
	addRoute("admin", "index", "admin", "admin/index");
	addRoute("admin", "order_parse", "blank", "admin/order_parse");
	addRoute("admin", "edit_chunk", "admin", "admin/edit_chunk");
	addRoute("admin", "options", "admin", "admin/options");
	
	addRoute("admin", "alias_add", "admin", "admin/alias_add");
	addRoute("admin", "alias_edit", "admin", "admin/alias_edit");
	addRoute("admin", "alias_remove", "blank", "admin/alias_remove");
	addRoute("admin", "alias_list", "admin", "admin/alias_list");
	
	// Areas, Pages
	addRoute("admin", "edit_area", "admin", "admin/edit_area");
	addRoute("admin", "add_page", "admin", "admin/add_page");
	addRoute("admin", "edit_page", "admin", "admin/edit_page");
	addRoute("admin", "list_pages", "admin", "admin/list_pages");
	
	// Insertables
	addRoute("admin", "add_image", "admin", "admin/add_image");
	addRoute("admin", "edit_image", "admin", "admin/edit_image");
	addRoute("admin", "list_images", "admin", "admin/list_images");
	
	addRoute("admin", "add_document", "admin", "admin/add_document");
	addRoute("admin", "edit_document", "admin", "admin/edit_document");
	addRoute("admin", "list_documents", "admin", "admin/list_documents");
	
	addRoute("admin", "edit_video", "admin", "admin/edit_video");
	addRoute("admin", "list_videos", "admin", "admin/list_videos");
	
	addRoute("admin", "add_user", "admin", "admin/add_user");
	addRoute("admin", "edit_user", "admin", "admin/edit_user");
	addRoute("admin", "list_users", "admin", "admin/list_users");
	
	// Admin CSS configured and included
	addRoute("css", "admincss", "blank", "admincss/admincss");
	
	// Start checking the conf.php file
	if (BLOG_INSTALL) {
		addRoute("admin", "add_blog", "admin", "admin/add_blog");
		addRoute("admin", "edit_blog", "admin", "admin/edit_blog");
		addRoute("admin", "list_blogs", "admin", "admin/list_blogs");
		
		addRoute("admin", "edit_entry", "admin", "admin/edit_entry");
		addRoute("admin", "list_entries", "admin", "admin/list_entries");
		
		addRoute("admin", "add_category", "admin", "admin/add_category");
		addRoute("admin", "edit_category", "admin", "admin/edit_category");
		addRoute("admin", "list_categories", "admin", "admin/list_categories");
		
		// front-end
		if ( ! defined(BLOG_STATIC_AREA) ) { setConfValue( 'BLOG_STATIC_AREA', "blog" ); }
		addRoute(BLOG_STATIC_AREA, "index", "blank", "blog/archive"); // landing page
		addRoute(BLOG_STATIC_AREA, "browse", "blank", "blog/archive"); // landing page
		addRoute(BLOG_STATIC_AREA, "category", "blank", "blog/archive");
		addRoute(BLOG_STATIC_AREA, "year", "blank", "blog/archive");
		addRoute(BLOG_STATIC_AREA, "page", "blank", "blog/archive");
		addRoute(BLOG_STATIC_AREA, "view", "blank", "blog/archive"); // single post
	}
	
	if (BLAST_INSTALL) {
		addRoute("admin", "view_old", "admin", "blaster/view_old");
		addRoute("admin", "add_list", "admin", "blaster/add_list");
		addRoute("admin", "edit_list", "admin", "blaster/edit_list");
		addRoute("admin", "list_lists", "admin", "blaster/list_lists");
		addRoute("admin", "list_subscribers", "admin", "blaster/list_subscribers");
		addRoute("admin", "mail_blast", "admin", "blaster/mail_blast");
		addRoute("blaster", "blast_options", "blank", "blaster/blast_options");
		addRoute("blaster", "subject_line", "blank", "blaster/subject_line");
		addRoute("blaster", "featured_options", "blank", "blaster/featured_options");
		addRoute("blaster", "text_options", "blank", "blaster/text_options");
		addRoute("blaster", "event_options", "blank", "blaster/event_options");
		addRoute("blaster", "event_include", "blank", "blaster/event_include");
		addRoute("blaster", "session_add", "blank", "blaster/session_add");
		addRoute("blaster", "remove_email", "blank", "blaster/remove_email");
		addRoute("blaster", "remove_oevent", "blank", "blaster/remove_oevent");
		addRoute("blaster", "remove_uevent", "blank", "blaster/remove_uevent");
		
		// front-end
		addRoute("users", "manage", "default", "blaster/user_subscriptions");
		addRoute("mail", "blast", "blank", "blaster/view_email");
		addRoute("mail", "subscribe", "default", "blaster/subscribe");
	}

	if (CALENDAR_INSTALL) {
		addRoute("admin", "add_event", "admin", "admin/add_event");
		addRoute("admin", "edit_event", "admin", "admin/edit_event");
		addRoute("admin", "list_events", "admin", "admin/list_events");
		
		addRoute("admin", "add_type", "admin", "admin/add_type");
		addRoute("admin", "edit_type", "admin", "admin/edit_type");
		addRoute("admin", "list_event_types", "admin", "admin/list_event_types");

		// front-end
		addRoute(CALENDAR_STATIC_AREA, CALENDAR_STATIC_PAGE, "default", "calendar");
	}
	
	if (GALLERY_INSTALL) {
		addRoute("admin", "add_gallery", "admin", "admin/add_gallery");
		addRoute("admin", "edit_gallery", "admin", "admin/edit_gallery");
		addRoute("admin", "list_galleries", "admin", "admin/list_galleries");
		
		// front-end
		addRoute("galleries", "view", "blank", "gallery/view");
		addRoute("galleries", "thumb", "blank", "gallery/thumb");
		addRoute("galleries", "photo", "blank", "gallery/photo");
	}
	
	if (PORTFOLIO_INSTALL) {
		addRoute("admin", "portfolio_add_area", "admin", "admin/portfolio_add_area");
		addRoute("admin", "portfolio_edit_area", "admin", "admin/portfolio_edit_area");
		addRoute("admin", "portfolio_add_section", "admin", "admin/portfolio_add_section");
		addRoute("admin", "portfolio_edit_section", "admin", "admin/portfolio_edit_section");
		addRoute("admin", "portfolio_add_item", "admin", "admin/portfolio_add_item");
		addRoute("admin", "portfolio_edit", "admin", "admin/portfolio_edit");
		addRoute("admin", "portfolio_list", "admin", "admin/portfolio_list");
		
		// front-end
		addRoute("portfolio", "thumbnail", "blank", "portfolio/thumbnail");
	}
	
	if (PRODUCT_INSTALL) {
		addRoute("admin", "add_product", "admin", "admin/add_product");
		addRoute("admin", "edit_product", "admin", "admin/edit_product");
		addRoute("admin", "list_products", "admin", "admin/list_products");
		
		addRoute("admin", "edit_paypal", "admin", "admin/edit_paypal");
		addRoute("admin", "list_paypal", "admin", "admin/list_paypal");
		
		// front-end
		addRoute("images", "prodimg", "blank", "images/prodimg");
		addRoute("images", "prodimgthb", "blank", "images/prodimgthb");
	}
	
	if (RSS_INSTALL) {
		// front-end
		addRoute("rss", "index", "blank", "rss");
		addRoute(BLOG_STATIC_AREA, "rss", "blank", "rss");
		addRoute(CALENDAR_STATIC_AREA, "rss", "blank", "rss");
	}

    if ( ! defined(TESTIMONIAL_INSTALL) ) { setConfValue( 'TESTIMONIAL_INSTALL', false ); }
    if (TESTIMONIAL_INSTALL) {
		addRoute("admin", "edit_testimonial", "admin", "admin/edit_testimonial");
		addRoute("admin", "list_testimonials", "admin", "admin/list_testimonials");
	}
?>
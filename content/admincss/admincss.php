<?php 
function initialize_page() {}
function display_page_content() {
	header('Content-type: text/css'); 
	
	require_once(adminCssPath("admin_core_css"));
	
	// OPTIONS and CONFIGURABLES:
	if (SUB_PAGES) {
		require_once(adminCssPath("admin_subpage_css"));
	}
	
	if (BLAST_INSTALL) {
		require_once(adminCssPath("admin_blast_css"));
	}
	
	if (CALENDAR_INSTALL && BLOG_INSTALL) {
		require_once(adminCssPath("admin_calendar_css"));
		require_once(adminCssPath("admin_datepicker_css"));
	} else if (CALENDAR_INSTALL) {
    	require_once(adminCssPath("admin_calendar_css"));
		require_once(adminCssPath("admin_datepicker_css"));
	} else if (BLOG_INSTALL) {
		require_once(adminCssPath("admin_datepicker_css"));
	}
	
	if (GALLERY_INSTALL) {
		require_once(adminCssPath("admin_gallery_css"));
	}
	
	if (PORTFOLIO_INSTALL) {
		require_once(adminCssPath("admin_portfolio_css"));
	}
}
?>
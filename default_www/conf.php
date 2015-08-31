<?php 

if(!function_exists("setConfValue"))
{
	function setConfValue($name, $value)
	{
		if(!defined($name))
		{
			define($name, $value);
		}
	}
}
date_default_timezone_set("America/New_York");

/*
 * IF IMAGES STOP LOADING LOOK FOR LINE RETURNS AND SPACES AT THE END OF PHP FILES
 * IF Gallery & Doc uploads do not work, check the permissions of the destination folder. Some servers are ok with 775, others need 777
 *
 * 
 * BASEHREF allows the app to be served from inside another directory.
 * Example:
 *    BASEHREF = "/test/site/": http://www.example.com/test/site/admin/login
 *    BASEHREF = "/": http://www.example.com/admin/login
 * "/" is the default. If it is used, it should contain a leading / and trailing /
 */
setConfValue('BASEHREF', '/');
/* REWRITE_URLS allows the site to function properly when mod_rewrite is unavailable (looking at you yahoo...)
 * REWRITE_URLS = true: http://www.example.com/admin/login
 * REWRITE_URLS = false: http://www.example.com/?id=/admin/login
 */
setConfValue( 'REWRITE_URLS', TRUE );
setConfValue( 'MYACTIVERECORD_CONNECTION_STR', 'mysql://username:password@db2.modwest.com/dbname' );
setConfValue( 'LOGIN_TICKET_NAME', 'userticket' );
setConfValue( 'SHA_SALT', 'FYDS*FY&*(987fs98(*F&SD))' );
setConfValue( 'SITE_NAME', 'New Website' );
setConfValue( 'SITE_URL', 'www.example.com' ); // Do not start this with a http://
setConfValue( 'SITE_NON_WWW', 'example.com' ); // Do not start this with a http://
setConfValue( 'SERVER_INCLUDES_ROOT', '/htdocs/' ); // should be the root path to the content/snippets folder... used when files are included
setConfValue( 'SERVER_DBBACKUP_ROOT', SERVER_INCLUDES_ROOT.'www/db_backup/' ); 
setConfValue( 'SERVER_DOCUMENTS_ROOT', SERVER_INCLUDES_ROOT.'www/documents/' );
setConfValue( 'SERVER_CACHE_ROOT', SERVER_INCLUDES_ROOT.'www/cache/' );
// this should be relative
setConfValue( 'PUBLIC_DOCUMENTS_ROOT', 'documents/' );

// July 22 2011, we started using Unfuddle at revision 1 with revision 263. So, for an accurate account of revisions (not like it really matters) add 263 to the Unfuddle number
setConfValue( 'SVN_VERSION', "102" ); // Modified Aug 12, 2015

setConfValue( 'HCD_DEBUG', TRUE );
setConfValue( 'MAINTENANCE_MODE', TRUE); 

setConfValue( 'ALLOW_SHORT_PAGE_NAMES', false );
setConfValue( 'MAX_IMAGE_WIDTH', 800 );

setConfValue( 'PROTECTED_ADMIN_AREAS', "1,2,3," ); // we are exploding on the comma, so there needs to be at least one
setConfValue( 'PROTECTED_ADMIN_PAGES', "1," );


// OPTIONS and CONFIGURABLES:
setConfValue( 'EDIT_WINDOW_HEIGHT', 32 );
setConfValue( 'SUB_PAGES', false );
setConfValue( 'ALIAS_INSTALL', false );
setConfValue( 'VIDEO_INSTALL', TRUE );
setConfValue( 'TESTIMONIAL_INSTALL', TRUE );

setConfValue( 'CALENDAR_INSTALL', false );
if ( CALENDAR_INSTALL ) {
	setConfValue( 'CALENDAR_STATIC_AREA', 'events' );
	setConfValue( 'CALENDAR_STATIC_PAGE', 'calendar' );
	setConfValue( 'DARK_TEXT_COLOR', 'black' );
	setConfValue( 'LIGHT_TEXT_COLOR', 'white' );
	setConfValue( 'DISPLAY_EVENTS_AS_LIST', TRUE );
	setConfValue( 'ALLOW_EVENT_TYPES', false );
}

setConfValue( 'GALLERY_INSTALL', TRUE );
if (GALLERY_INSTALL) {
    setConfValue( 'PROTECTED_ADMIN_GALLERIES', "0," ); //Zero will match none unless we want to match one
	setConfValue( 'MAX_GALLERY_IMAGE_WIDTH', 960 );
	setConfValue( 'MAX_GALLERY_IMAGE_HEIGHT', 540 );
}

setConfValue( 'PORTFOLIO_INSTALL', false );
if ( PORTFOLIO_INSTALL ) {
	setConfValue( 'DISPLAY_ITEMS_AS_LIST', false );
	setConfValue( 'MAX_PORTFOLIO_IMAGE_WIDTH', MAX_GALLERY_IMAGE_WIDTH );
	setConfValue( 'MAX_PORTFOLIO_IMAGE_HEIGHT', MAX_GALLERY_IMAGE_HEIGHT );
	setConfValue( 'PORTFOLIOTHUMB_IMAGE', false );
	setConfValue( 'PORTFOLIOTHUMB_IMAGE_MAXWIDTH', 300 );
	setConfValue( 'PORTFOLIOTHUMB_IMAGE_MAXHEIGHT', 240 );
	setConfValue( 'ITEM_ID_IN_URL', false ); // do we add IDs to the URL string to ensure that items are unique?
	setConfValue( 'ITEM_SKU', false ); // extra feature
	setConfValue( 'ITEM_PRICE', false ); // extra feature
	setConfValue( 'ITEM_TAXONOMY', false ); // extra feature... edit the snippet item-taxonomy.php to customize
	setConfValue( 'ITEM_DOCUMENTS', false ); // extra feature... Documents attached to Items
	setConfValue( 'ITEM_VIDEOS', false ); // extra feature... Videos attached to Items
	setConfValue( 'ITEM_GALLERY_INSERT', false ); // extra feature... Allow galleries attached ot Items to be inserted into content
}

setConfValue( 'BLOG_INSTALL', false );
if ( BLOG_INSTALL ) {
	setConfValue( 'MULTI_BLOG', false ); // Depreciate? No one but The Steelyard uses this. 
	setConfValue( 'BLOG_DEFAULT_ID', 1 ); // this is the default ID of the BLOG, not the blog area
	setConfValue( 'BLOG_STATIC_AREA', 'blog' ); 
	setConfValue( 'BLOG_ENTRY_TEMPLATES', TRUE ); 
	setConfValue( 'BLOG_ENTRY_IMAGES', TRUE ); 
	setConfValue( 'MAX_ENTRY_IMAGE_WIDTH', MAX_GALLERY_IMAGE_WIDTH ); 
	setConfValue( 'MAX_ENTRY_IMAGE_HEIGHT', MAX_GALLERY_IMAGE_HEIGHT ); 
}

setConfValue('RSS_INSTALL', false );
if ( RSS_INSTALL ) {
	setConfValue( 'RSS_IMAGE', false );
	setConfValue( 'RSS_AUTHOR', false );
}

setConfValue( 'PRODUCT_INSTALL', false );
if ( PRODUCT_INSTALL ) {
	setConfValue( 'PRODUCT_IMAGE_MAXWIDTH', 800 );
	setConfValue( 'PRODUCTTHUMB_IMAGE_MAXWIDTH', 360 );
}

setConfValue( 'BLAST_INSTALL', false );
if ( BLAST_INSTALL ) {
    // Modwest Bulk Mail policy: If using PHP to send mail enmasse, specify the bulkmail server as the SMTP server. 
    setConfValue( 'SENDMAIL_FROM', 'noreply@'.SITE_NON_WWW );
    ini_set( 'SMTP', 'listmail.modwest.com' );
    ini_set( 'sendmail_from', SENDMAIL_FROM );
}
setConfValue( 'CONTACT_EMAIL', 'info@'.SITE_NON_WWW );

setConfValue( 'CUSTOM_LEVEL1_ADMIN', false );

setConfValue( 'CUSTOM_LEVEL2_ADMIN', false );

setConfValue( 'CUSTOM_LEVEL3_ADMIN', false );

setConfValue( 'USER_ROLES', 'admin,staff' ); // we are exploding on the comma, so there needs to be at least one

?>
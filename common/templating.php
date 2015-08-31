<?php
function display_page($params = Array())
{
	// canonicalize the input
	$area_name = $page_name = "";
	
	// GET /
	if (count($params) == 0) {
		$area_name = "index";
		$page_name = "index";
	}
	
	// GET /area_name
	if (count($params) == 1) {
		$area_name = $params[0];
		$page_name = "index";
	}
	
	// GET /area_name/page_name
	if (count($params) > 1) {
		$area_name = $params[0];
		$page_name = $params[1];
	}
	
	if (ALIAS_INSTALL) {
    	// Test the MyActiveRecord connection
    	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'alias'") ) == 1 ) { 
        	
        	// check the alias' in the database and see if there is a match
        	$alias = Alias::FindAll();
            /* die(print_r($alias)); */
        	foreach ($alias as $entry) {
        		if ($entry->alias == $area_name || $entry->alias == "/".$area_name) {
        			redirect($entry->path);
        		}
        	}
    	}
	}
	
	// check the routes to see if the requested page has an explicit route setup
	$page_options = $GLOBALS['ROUTES'];
	if (isset($page_options["/".$area_name."/".$page_name])) {
		$page = StaticPage::FindByAreaAndName($area_name, $page_name);
		if (isset($page)) {
			display_admin_with_template($page);
			return true;
		}
	}
	
	// check for pages that are in the "global" area and have params (ie /calendar/2008/1)
	$global_area = Areas::FindByName('index');
	$possible_page = ( Pages::FindByAreaAndName($global_area, $page_name) ) ? Pages::FindByAreaAndName($global_area, $area_name) : ''; 
	
	if (!empty($possible_page)) {
		$area = $global_area;
		$page = $possible_page;
	} else {
		// for now, just include the first page that comes back. later we can handle multiple pages
		$area = Areas::FindByName($area_name);
		if (!isset($area)) { return false; }
		if (strstr($area_name, "-portfolio")) {
			if ($page_name != "index") {
				$page = Sections::FindByName($page_name);
			} else {
				$pages = $area->getSections();
				$page = array_shift($pages); 
			}
		} else {
			$page = Pages::FindPageOrIndex($area, $page_name);
		}
	}
	
	if (!isset($page)) {
		return false;
	}
	
	// check if the page is public or not
	$is_admin = false;
	$user = Users::GetCurrentUser();
	if ($user) { $logged_in = true; }
	
	if( $page->public || ( !$page->public && $logged_in ) ) {
		display_with_template($area, $page);
	} else {
		return false;
	}
	return true;
}


function include_page_content()
{
	echo $GLOBALS['content_page']->content;
}

function get_content_page()
{
	return $GLOBALS['content_page'];
}

function get_content_area()
{
	return $GLOBALS['content_area'];
}

function include_snippet($snippet)
{
	require_once(snippetPath($snippet));
}

function display_admin_with_template($page)
{
    $GLOBALS['content_page'] = $page;
	$GLOBALS['content_area'] = null;
	
	// call initialize for the selected page
	$page->initialize();
	
	$template = $page->template;
	if(function_exists("override_template"))
	{
		$template = override_template();
	}
	require_once(layoutPath($template));
}

function display_with_template($area, $page)
{
	$template = $page->getTemplateForArea($area);
	
	$GLOBALS['content_page'] = $page;
	$GLOBALS['content_area'] = $area;
	
	// call initialize for the selected page
	$page->initialize();
	
	$overridden_template = "";
	if(function_exists("override_template"))
	{
		$overridden_template = override_template();
	}	
	if($overridden_template != "")
	{
		$template = $overridden_template;
	}

	require_once(layoutPath($template));
}

function list_available_templates()
{
	$templates = array();
	$layout_paths = array();
	
	$layout_paths[] = LOCAL_PATH . "framework/content/layouts/*.php";
	$layout_paths[] = LOCAL_PATH . "content/layouts/*.php";
	
	foreach($layout_paths as $layout_path)
	{
	    foreach (glob($layout_path) as $required_file)
    	{
    		if(substr_count($required_file, 'admin') == 0)
    		{
    			$template_name = explode(".", end(explode("/", $required_file)));
    			if(!in_array($template_name[0], $templates))
    			{
    			    $templates[] = $template_name[0];
			    }
    		}
    	}
	}
	
	return $templates;
}


/* ! ===== Global Functions. They were not working in model_base.php, so they are here ===== */
// Prolly something about the way that the classes extend and inherit each other that I don't understand fully. 

/* 
 * Returns a small chunk of content rather than saving these chunks in a page in the Global area. 
 * Also, prevents pages (that should be chunks) in the Global area from being accesible via a URL. 
 * No options. 
 * Works much like the functions in model_base, but needs to be globally available
 */
function get_chunk( $chunkslug='' ) 
{
    if ( $chunkslug != '' ) {
	    // Will return a cached version or a new Query
	    $chunk = Chunks::RenderChunk( $chunkslug ); 
	    
	    /* Careful here. 
	     * Chunks have a 'description' field which we use just to describe to the user where this info is presented in a template. 
	     * So if we run $chunk->get_content() it could return the contents of the description field, 
	     * because Events use a field called 'description' instead of 'content'
	     */
	    return $chunk;
    }
}
function the_chunk( $chunkslug='' ) { echo get_chunk( $chunkslug ); }


/*
 * Returns the URL for a page when we don't have the page object yet. 
 * Used in templates when we need to hardcode a link somewhere
 * Explicitly checks to see if the page is public first, does not check the Area
 */
function GetPageURL( $pageid, $thisarea="", $rooturl=false )
{
	$link = ( $rooturl ) ? "http://".SITE_URL.BASEHREF : BASEHREF; 
	$link .= ( REWRITE_URLS ) ? "" : "?id="; 
	
	if ( ! empty($pageid) ) {
		
		$page = Pages::FindById( $pageid ); 
		if ( $page->public ) {
		    return $page->get_url( $thisarea, "", $rooturl ); 
		} else { return null; }
		
	} else { return null; }
	
}
function ThePageURL( $pageid, $thisarea="", $rooturl=false ) { echo GetPageURL( $pageid, $thisarea, $rooturl ); }
    
?>
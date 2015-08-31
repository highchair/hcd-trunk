<?php
	$GLOBALS["APP_INCLUDE_DIRECTORIES"] = Array();
	//$GLOBALS["APP_INCLUDE_DIRECTORIES"][] = "{FRAMEWORK_PATH}common/";
	$GLOBALS["APP_INCLUDE_DIRECTORIES"][] = LOCAL_PATH . "models/";
	require_once(LOCAL_PATH . "framework/common/activerecord.php");
	require_once(LOCAL_PATH . "framework/common/model_base.php");
	require_once(LOCAL_PATH . "framework/common/backup_database.php");
	require_once(LOCAL_PATH . "framework/common/error_handling.php");
	require_once(LOCAL_PATH . "framework/common/form_helper.php");
	require_once(LOCAL_PATH . "framework/common/routes.php");
	require_once(LOCAL_PATH . "framework/common/templating.php");
	require_once(LOCAL_PATH . "framework/common/replacement.php");
	require_once(LOCAL_PATH . "framework/common/utility.php");
	require_once(LOCAL_PATH . "framework/common/content_filter.php");
	require_once(LOCAL_PATH . "framework/common/content_loader.php");
	require_once(LOCAL_PATH . "framework/common/model_loader.php");
	require_once(LOCAL_PATH . "framework/common/image.php");
	require_once(LOCAL_PATH . "framework/common/csv_importer.php");
	
	// Core Models
	require_once(modelPath("admin_page"));
	require_once(modelPath("area"));
	require_once(modelPath("page"));
	require_once(modelPath("chunks"));
	require_once(modelPath("image"));
	require_once(modelPath("user"));
	require_once(modelPath("alias"));
	// insertables
	require_once(modelPath("document"));
	require_once(modelPath("photos"));
	require_once(modelPath("galleries"));
	require_once(modelPath("videos"));
	require_once(modelPath("testimonials"));
	
	// calendar classes
	if (CALENDAR_INSTALL) {
		require_once(modelPath("admin_calendar"));
		require_once(modelPath("calendar"));
		require_once(modelPath("event"));
		require_once(modelPath("eventperiod"));
		require_once(modelPath("eventtype"));
		require_once(modelPath("recurrence"));
	}
	
	// product classes
	if (PRODUCT_INSTALL) {
		require_once(modelPath("product"));
		require_once(modelPath("paypal_config"));
	}
	
	// blog stuff
	if (BLOG_INSTALL) { 
		require_once(modelPath("blogs"));
		require_once(modelPath("blog_entries"));
		require_once(modelPath("categories"));
	}
	
	// mail blast stuff
	if (BLAST_INSTALL) { 
		require_once(modelPath("nllists"));
		require_once(modelPath("nlemails"));
		require_once(modelPath("mailblast"));
	}
	
	// portfolio classes
	if (PORTFOLIO_INSTALL) { 
		require_once(modelPath("section"));
		require_once(modelPath("portfolioimages"));
		require_once(modelPath("item"));
		require_once(modelPath("keywords"));
	}
	
	require_once("routes.php");
	
	
	// Dec 2012: Updated to avoid problems when there is no models folder or no custom models inside of it. 
	foreach($GLOBALS["APP_INCLUDE_DIRECTORIES"] as $include_dir)
	{
		if ( is_dir($include_dir) ) {
    		$add_models = glob($include_dir . "*.php");
    		
    		if ( count($add_models) > 0 ) {
        		foreach ( $add_models as $required_file ) {
        			require_once( $required_file );
        		}
    		}
		}
	}
	
	// Now that includes are loaded, run this function to see if we need to report errors
	// Uses HCd_debug in utility.php
	if ( HCd_debug() ) {

		// Report simple running errors
		error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
		
		// Report all errors except E_NOTICE
		//error_reporting(E_ALL ^ E_NOTICE);
	
	} else {
		error_reporting(0);
	}
?>
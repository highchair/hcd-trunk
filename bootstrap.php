<?php
if(!function_exists("setConfValue")) {
	function setConfValue($name, $value) {
		if(!defined($name)) {
			define($name, $value);
		}
	}
}

function go() {
	
	ob_start();
	require_once(LOCAL_PATH . "conf.php");
	require_once(LOCAL_PATH . "framework/common/includes.php");
	require_once(LOCAL_PATH . "routes.php");

	// parse the requested page, and make it available globally
	if ( $_GET["id"] != '' ) {
		$GLOBALS["REQUEST_PARAMS"] = explode("/", $_GET["id"]);
	} else {
		$GLOBALS["REQUEST_PARAMS"] = array('',''); 
	}

	// "/admin/login" gets exploded as { "", "admin", "login" } but it looks better when not rewriting 
	// -- so shift off the empty element of the array
	if ( $GLOBALS["REQUEST_PARAMS"][0] == "" ) {
		array_shift($GLOBALS["REQUEST_PARAMS"]);
	}
	
	if ( end($GLOBALS["REQUEST_PARAMS"]) == "" ) {
		array_pop($GLOBALS["REQUEST_PARAMS"]);
	}

	// take out a prefix, if we're not running from /
	// TODO: remove in order
	function remove_prefix($val) {
		return in_array($val, explode("/", ltrim(rtrim(BASEHREF,'/'),'/')));
	}
	if ( REWRITE_URLS ) {
		array_filter($GLOBALS["REQUEST_PARAMS"], "remove_prefix");
	}
	
	// display the requested page
	if ( !display_page($GLOBALS["REQUEST_PARAMS"]) ) {
		display_404();
	}
	ob_end_flush();
}
?>
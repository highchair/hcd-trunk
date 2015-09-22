<?php

// ! UI things... backend feedback on submit
function setFlash($newValue)
{
	$value = $newValue;
	if(isset($_SESSION['flash_msg']))
	{
		$value = $_SESSION['flash_msg'];
		$value = $value . $newValue;
	}
	$_SESSION['flash_msg'] = $value;
}

function displayFlash()
{
	$value = "";
	if(isset($_SESSION['flash_msg']))
	{
		$value = $_SESSION['flash_msg'];
		$_SESSION['flash_msg'] = null;
		
		echo $value;
	}
}


/*
 * Supported levels: error, warning, success, notice
 */
function user_feedback( $level='error', $message ) {

	if ( 
		$level == 'error' or 
		$level == 'warning' or 
		$level == 'success' or 
		$level == 'notice' ) {
		
		return '<div class="feedback feedback__'.$level.'"><p><strong class="feedback--snipe">'.ucwords($level).'</strong> <span class="feedback--message">'.$message.'</span></p></div>'; 

	} else { 
		return false; 
	}
}


/* Context? Example? */
function throwError( $message )
{
	return print_r(error_get_last()).'<br /><strong>'.$message.'</strong>'; 
}

function sql_debug( $query ) 
{
	return "A MySQL error has occurred.<br />Query: " . $query . "<br /> Error: (" . mysql_errno() . ") " . mysql_error(); 
}

function find_db_column( $table, $column )
{
	// Better return values than SHOW COLUMNS but mysql_ commands are being depreciated. Migrate to mysqli_ commands instead. 
	$connectstr = @parse_url(MYACTIVERECORD_CONNECTION_STR) 
		or trigger_error("MyActiveRecord::Connection() - could not parse connection string: ".MYACTIVERECORD_CONNECTION_STR, E_USER_ERROR);
	$dbname = trim($connectstr['path'],' /');  

	$fields = mysql_list_fields( $dbname, $table );
	$columns = mysql_num_fields( $fields );
	for ( $i = 0; $i < $columns; $i++ ) {
		$field_array[] = mysql_field_name( $fields, $i );
	}
	if ( in_array( $column, $field_array ) ) {
		return true;
	} else { return false; }
}

function getPostValue($key)
{
	if( isset($_POST[$key]) ) {
		// Can't use mysql_real_escape_string here, as it will add slashes to content with single quotes inside 
		return $_POST[$key];
	} else {
		return "";
	}
}

// ! Quick Link drop down list of pages and areas in the admin template
function quick_link( $strlimit=36 )
{
	if (IsUserLoggedIn())
	{
		$quick_link = "\n<form id=\"quick_link\" method=\"POST\">\n\t<select name=\"quick_select\" id=\"select_quick\">\n\t\t<option value=\"\">Quick Link- choose&hellip;</option>\n";
		
		$areas = Areas::FindAll();
		//$num_areas = count($areas);
		foreach($areas as $area)
		{
			$thisAreaName = $area->display_name;
			//$thisShortName = $area->name;
			
			$quick_link .= "\t\t<option value=\"" .get_link("/admin/edit_area/{$area->id}") ."\">$thisAreaName</option>\n";
			
			$pages = $area->findPages(true, true);
			//$num_pages = count($pages);
			foreach($pages as $page)
			{ 
				$thisPublic = $page->public; 
				$thisDisplayName = "&nbsp;&nbsp;&nbsp;"; 
				if ($page->parent_page_id) { $thisDisplayName .= " &ndash; "; }
				
				$thisDisplayName .= ( strlen( $page->display_name ) > $strlimit ) ? substr( $page->display_name, 0, $strlimit ).'&hellip;' : $page->display_name;
				 
				if (!$thisPublic) { $thisDisplayName .= " (not public)"; }
				
				$quick_link .= "\t\t<option value=\"" .get_link("/admin/edit_page/{$page->id}") ."\">$thisDisplayName</option>\n";
			}
		}	
		$quick_link .= "\t</select>\n<input type=\"submit\" class=\"submitbutton\" name=\"submit\" value=\"GO\" />\n</form>\n";
		echo $quick_link;
	}
}


// ! URL string manipulation
// TODO: This should be handled more cleanly -- any parts of the url not needed to display a page should be shuffled off into the request_params
function requestIdParam()
{
	if(isset($GLOBALS["REQUEST_PARAMS"][2]))
	{
		return $GLOBALS["REQUEST_PARAMS"][2];
	}
	else
	{
		return false;
	}
}

function getRequestVarAtIndex($index = 0)
{
	if(isset($GLOBALS["REQUEST_PARAMS"][$index]))
	{
		return urldecode($GLOBALS["REQUEST_PARAMS"][$index]);
	}
	else
	{
		return "";
	}
}

function redirect( $url )
{
	$redirect_string = get_link( $url );
	header("Location: {$redirect_string}");
	exit;
}

function redirectToUrl($url = "/")
{	
	header("Location: {$url}");
	exit;
}

function reload()
{
	redirectToUrl($_SERVER['PHP_SELF']);
}

function get_link( $url )
{
	$link_url = $url;

	// if the url is absolute (begins with http), leave it alone
	if( substr($link_url,0,4) == "http" )
	{
		return $link_url;
	}
	if( !REWRITE_URLS )
	{
		$link_url = BASEHREF . "?id={$link_url}";
	
	} else {
		$link_url = ltrim( $link_url,"/" );
		
		if( BASEHREF != "" )
		{
			$link_url = BASEHREF . $link_url;
		}
	}
	return $link_url;
}

function get_link_to_page( $pageid )
{
	// Still useful in some cases when we don't want to get the object beforehand
	$page = Pages::FindById( $pageid );
	return $page->get_url(); 
}

function area_page( $id, $num ) 
{
	$id_name = explode("/", $id);
	return $id_name[$num];	
}


// ! Cleaning up input and output strings, dates and the like
function slug( $str )
{
	$str = strtolower(trim($str));
	$str = preg_replace('/[^a-z0-9-]/', '-', $str); // replace anything that is not alphanumeric with a hyphen
	$str = preg_replace('/-+/', "-", $str); // reduce two hyphens in a row to one
	$str = preg_replace('/^-/', "", $str);
	$str = preg_replace('/-$/', "", $str);
	return $str;
}

function unslug( $str )
{
	// not perfect, but it'll do
	$str = trim($str);
	$str = preg_replace( '/-+/', " ", $str );
	$str = ucwords($str); 
	return $str;
}

function htmlize( $input )
{
	// used when we don't need the complexity of the ->getContent() function
	$order   = array("\r\n", "\n", "\r");
	$replace = '<br />';
		// Processes \r\n's first so they aren't converted twice.
	$newstr = str_replace( $order, $replace, $input );
	return $newstr;
}

function esc_html( $string=false )
{
	// converts & to &amp; and quotes to &ldquo;, etc...
	if ( isset($string) )
		return htmlentities( $string, ENT_QUOTES, "UTF-8" ); 
}
// Alias for the old function
function cleanupSpecialChars( $string=false ) { return esc_html($string); }

function unesc_html( $string=false )
{
	if ( isset($string) )
		return html_entity_decode( $string, ENT_QUOTES, "UTF-8" ); 
}
// Alias for the old function
function displaySpecialChars( $string=false ) { return unesc_html($string); }

function formatDateView($date, $format = "m/d/Y")
{
	return date($format,strtotime($date));
}

function formatDateTimeView($date, $format = "m/d/Y g:i A")
{
	return date($format,strtotime($date));
}

function formatTimeView($date, $format = "g:i A")
{
	return date($format ,strtotime($date));
}

function parseDate($date, $format = "Y-m-d H:i:s")
{
	return date($format,strtotime($date));
}

function is_validemail( $emailtocheck ) {
	/* 
	 * "Simple" regex allows something like hi+sub@whim.gallery or hi@whim.co.uk
	 * Username: allow chars a-z 0-9 _-+ 
	 * Hostname: allow chars a-z 0-9 _-  
	 * TLD: minimum of 2 and then up to 24 chars. Does not allow non alpha chars (and may not need numbers, but we allow it)
	 * This isn't about making sure emails are valid, but simply adds a link to an email that might be
	 * The same regex is used in replacement for the convert emails in content function
	 */
	return ( preg_match("([A-Za-z0-9_\-\+.]+[@]+[A-Za-z0-9_\-\.]+[.]+[A-Za-z0-9]{2,24})", $emailtocheck) ) ? true : false; 
}

function getFullMonthName($month)
{
	if (substr($month, 0, 1) == "0") { $month = substr($month, 1, 1); }
	$monthName = array("there is no zero month","January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December");
	return $monthName[$month];
}

function chopText( $content, $charcount, $stopchar = " " ) 
{
	// This function is needed to scrub content of the HCd backend tags as well as HTML tags. 
	// Can be used on events, pages, whatever... 
	// The Blog model also has one called "chopForBlogDigest" and "chopShort" which works well for Blog entries
	
	// Remove HCd tags
	$content = scrub_HCd_Tags( $content ); 
	
	// Scrub HTML (to remove long <a href> urls) and THEN truncate the text.
	$content = strip_tags( $content );
	
	if ( strlen($content) > $charcount ) {
		
		// Use the $stopchar to find out where to truncate the text. Use "</p>" if you want to go to the end of the next paragraph
		$content = substr( $content, 0, strpos( $content, $stopchar, $charcount ) );
	} 
	return $content;
}

function scrub_HCd_Tags($content_to_display)
{
	// Looks for all of our patterns and removes the tags... Does not return any one particular tag. 
	// If you want to scrub the content but save an image, the Steel Yard has a good function in blogs.php called "cleanForUpdates"
	
	$pattern_recog = array("left" => array("{","{"), "right" => array("}","}"), "reg" => array("{","}"));
	$types = array("image" => "", "gallery"=>"gallery:", "carousel"=>"carousel:", "product"=>"product:", "documents"=>"documents:");
	
	foreach ($pattern_recog as $float => $direction) 
	{
		foreach ($types as $name => $type) 
		{
			$pattern = "*".$direction[0]."{2}(".$type."[A-Za-z0-9_, \-]+)".$direction[1]."{2}*";
			$items = getFilterIds($content_to_display, $pattern);
			
			foreach ($items as $item)
			{
				$search = $direction[0].$direction[0].$item.$direction[1].$direction[1];
				$replacement = "";
				$content_to_display = str_replace($search, $replacement, $content_to_display);
			}
		}
	}
	return $content_to_display;
}

function scrub_url_protocol( $url ) 
{
	// Works on any URL with '//' in it: http://, https://, ftp://, etc…
	$position = strpos($url, '//'); 
	if ( $position ) {
		$newurl = substr( $url, $position+2 );
		return rtrim( $newurl, '/' ); 
	} else {
		return $url; 
	}
}

// ! IMAGE FUNCTIONS
function getPostValueAsArray($key)
{
	if(isset($_POST[$key]))
	{
		return $_POST[$key];
	}
	else
	{
		return array();
	}
}

function getFileExtension($fileName)
{
	//return end(explode(".", $fileName));
	return pathinfo($fileName, PATHINFO_EXTENSION);
}

function getFileName($fileName)
{
	return pathinfo($fileName, PATHINFO_FILENAME); 
}


// ! Log-in / users functions
function IsUserLoggedIn()
{
	return isset( $_SESSION[LOGIN_TICKET_NAME] );
}

function LoginRequired( $login_page="/admin/login/", $required_roles=array() )
{
	if( !IsUserLoggedIn() )
	{
		setFlash("<h3>You must have a valid login to access these pages</h3>");
		redirect( $login_page );
	}
	
	/*
	IF this install needs more than two users (Admin and Staff) then the User table needs more roles added and this code below needs to check the user and the roles allowed. Add the additional roles to the array in the CONF file as well. 
	*/
	
	$user = Users::GetCurrentUser();
	// the user is logged in, make sure they have the correct roles. Admins always have any role
	$has_role = false;
	if ( count($required_roles) > 1 ) 
	{
		foreach( $required_roles as $required_role ) {
			if( $user->has_role($required_role) ) {
				$has_role = true;
			}
		}
	} else {
		$has_role = true;
	}
	if ( !$has_role ) 
	{ 
		$_SESSION[LOGIN_TICKET_NAME] = NULL; 
		setFlash("<h3>Your User Role does not have access to these pages</h3>");
		redirect( $login_page, $optionalredirect ); 
	}
}

// Debug methods
function HCd_debug() {
	if ( HCD_DEBUG ) {
		$user = Users::GetCurrentUser(); 
		if ( is_object( $user ) && strpos( $user->email, 'highchairdesign.com' ) !== false ) {
			return true; 
		} else {
			return false; 
		}
	} else {
		return false; 
	}
}

function debug_block( $message ) {

	$debug = ""; 

	if ( HCd_debug() ) {
		$debug .= '<div class="debug-block">'; 
		$debug .=  '<span class="debug-feedback failed">'.$message.'</span>'; 
		$debug .=  '</div>'; 
	}
	echo $debug; 
}

// Not sure what these are
function request_method()
{
	return $_SERVER['REQUEST_METHOD'];
}

function request_is($method="")
{
	return strcasecmp($method, request_method()) == 0;
}

// ! Number manipulation
function is_odd($number)
{
	// this is a bitwise operator. Check to see if the 1-bit is on or off – on, we have an odd number.
	if ( $number & 1 )
	{
		return true;
	} else {
		return false;
	}
}

// ! Array manipulation
// array_diff is a PHP function that doesn't quite work the way you think it might. Here is one that does. 
function array_difference($array_a, $array_b) 
{
	$union_array = array_merge($array_a, $array_b);
	$intersect_array = array_intersect($array_a, $array_b);
	return array_diff($union_array, $intersect_array); 
}

/*
 * Sort a multi dimensional array on a column
 *
 * @param array $array array with hash array. Must already have been merged with array_merge($arr1, $arr2)
 * @param mixed $column key that you want to sort on
 * @param enum $order asc or desc
 */
function array_sortoncol ( &$array, $column=0, $order="ASC" ) {
	$oper = ($order == "ASC")?">":"<";
	if( !is_array($array) ) return;
	usort( $array, create_function('$a,$b',"return (\$a['$column'] $oper \$b['$column']);") ); 
	reset( $array );
}

// ! Mail functions
function hcd_sendmail( $to, $subject, $message, $from, $html=true )
{
	// Check the email address first
	if ( preg_match( "([A-Za-z0-9]+[@]+[A-Za-z0-9]+[.]+[A-Za-z0-9]{2,4})", $to ) )
	{
		$headers = ""; 
		if ( $html )
		{
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}
		$headers .= 'From: '. $from ."\r\n" .
					'Reply-To: '. $from ."\r\n" .
					'X-Mailer: PHP/' . phpversion();
		//mail ( string $to , string $subject , string $message [, string $additional_headers [, string $additional_parameters ]] )
		mail ( $to, $subject, $message, $headers ); 
		
		return true; 
	} else {
		return false; 
	}
}
?>
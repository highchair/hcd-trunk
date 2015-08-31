<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7">  <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8 ie7">     <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9 ie8">            <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en">             <!--<![endif]-->

<?php
	
	// Sometimes cache-busting seems to be needed on CSS and JS. 
	// Use ?ver=<?php echo date("njHis") 
	if ( ! isset($page) ) { $page = null; }
	
	// Set the title of the page: Check the strings to do some fancy stuff...
	$page_title = $description = $bodyclass = $template = ""; 
	$blogitem = $category = null; 
	$template = ( isset($area) && isset($page) ) ? $page->getTemplateForArea($area) : "no-template-specified"; 
	$current_user = Users::GetCurrentUser(); 
	$admin_class = ( is_object( $current_user ) ) ? ' loggedin' : ' no-loggedin'; 
	$admin_class = ( is_object( Users::GetCurrentUser() ) ) ? ' loggedin' : ''; 
	
	// CUSTOM
	$var0 = getRequestVarAtIndex(0); 
	$var1 = getRequestVarAtIndex(1); 
	$var2 = getRequestVarAtIndex(2); 
	$var3 = getRequestVarAtIndex(3); 
	
	if ( BLOG_INSTALL && $var0 == BLOG_STATIC_AREA ) {
		$blogarea = Areas::FindById( 3 ); // Might need to change this on a per project basis
		$page_title = $blogarea->get_title()." | ".SITE_NAME; 
		$bodyclass = "blog archive";
		if ( $var1 == "category" ) {
    		$category = Categories::FindByName( $var2 ); 
			$description = $category->get_excerpt( 160 );
			$page_title = $category->get_title()." | ".$blogarea->get_title()." | ".SITE_NAME; 
			$bodyclass = "blog category-archive";
		} elseif ( $var2 == "page" ) {
    		$description = '';
			$page_title = "Page $var3 | ".$blogarea->get_title()." | ".SITE_NAME; 
			$bodyclass = "blog page-archive";
		} elseif ( $var2 != "" ) {
			$blogitem = Blog_Entries::FindById( $var2 ); 
			$description = $blogitem->get_excerpt( 160 );
			$page_title = $blogitem->get_title()." | ".$blogarea->get_title()." | ".SITE_NAME; 
			$bodyclass = "blog single-entry";
		}
	} elseif ( CALENDAR_INSTALL && $var1 == "events" ) {
		$page_title = "Event Calendar | ".SITE_NAME;
		$bodyclass = "calendar"; 
		if ( getRequestVarAtIndex(5) ) {
			$event = Events::FindById( getRequestVarAtIndex(5) ); 
			$description = $event->get_excerpt( 160 );
			$page_title = $event->get_title()." | ".SITE_NAME; 
			$bodyclass = "calendar single-event";
		} 
	} elseif ( PORTFOLIO_INSTALL && $area->is_portfolioarea() ) {
		// With Item
		if ( ! empty( $var3 ) ) {
            $item = ( ITEM_ID_IN_URL ) ? Items::FindById( $var3 ) : Items::FindByName( $var3 ); 
            $description = $item->get_excerpt( 160 );
            $page_title = $item->get_title()." | ".SITE_NAME; 
            $bodyclass = "$template portarea-{$area->id} section-{$page->id} item-{$item->id}";
        
        // No item but a port area and section
		} elseif ( isset($page) ) {
            $description = ( $page->content != "" ) ? $page->get_excerpt( 160 ) : $area->get_excerpt( 160 );
            $page_title = $page->get_title()." | ".$area->get_title()." | ".SITE_NAME; 
            $bodyclass = "$template portarea-{$area->id} section-{$page->id}";
		} else {
            $description = $area->get_excerpt( 160 );
            $page_title = $page->get_title()." | ".$area->get_title()." | ".SITE_NAME; 
            $bodyclass = "$template portarea-{$area->id} section-{$page->id}";
		}
    
    } else {
		
		// There is always a page and always an area! 
		if ( $area->id == '1' && $page->id == '1' ) {
			// We are on the homepage
			$page_title = "Welcome to ".SITE_NAME;
		} elseif ( isset($page) && $page->name != "" ) {
			$page_title = $page->get_title()." | ".SITE_NAME;
		} 
		$description = chopText( $page->content, 140 ); 
		$bodyclass = "{$template}-template area-{$area->id} page-{$page->id}";
	} 
	$bodyclass .= $admin_class; 
	
	$current_permalink = "http://".SITE_URL.BASEHREF.getRequestVarAtIndex(0);
	if ( getRequestVarAtIndex(1) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(1); 
	if ( getRequestVarAtIndex(2) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(2); 
	if ( getRequestVarAtIndex(3) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(3); 
	if ( getRequestVarAtIndex(4) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(4); 
	if ( getRequestVarAtIndex(5) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(5); 
	if ( getRequestVarAtIndex(6) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(6);
?>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <link rel="prefetch" href="//fonts.googleapis.com/css?family=Merriweather+Sans:300,700">
    <link rel="prefetch" href="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js">
    
    <meta name="apple-mobile-web-app-title" content="<?php echo SITE_NAME ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="HandheldFriendly" content="True">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=yes">
    
    <link rel="stylesheet" href="<?php echo BASEHREF ?>lib/css/style.css">
    <link rel="stylesheet" href="<?php echo BASEHREF ?>lib/css/mq.css" media="not print, braille, embossed, speech, tty">
    
    <!--[if !(lte IE 8)]><!-->
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
    <!--<![endif]-->
    
    <!--[if (lte IE 8)&(!IEMobile)]>
    <link rel="stylesheet" href="<?php echo BASEHREF ?>lib/css/no-mq.css" media="screen">
    <![endif]-->
    
    <!--<link rel="icon" type="image/x-icon" href="<?php echo BASEHREF ?>favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo BASEHREF ?>apple-touch-icon-144x144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo BASEHREF ?>apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo BASEHREF ?>apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo BASEHREF ?>apple-touch-icon-precomposed.png">
    <link rel="apple-touch-icon" href="<?php echo BASEHREF ?>apple-touch-icon-precomposed.png">-->
    
    <!-- Tile icon for Win8 (144x144 + tile color)
    <meta name="msapplication-TileImage" content="img/touch/apple-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#222222"> -->
    
    <!-- SEO and OpenGraph data - Needs to be fed dynamically according to the content of the page -->
    <meta property="og:site_name" content="<?php echo SITE_NAME ?>">
    <meta property="og:type" content="website">
    
    <title><?php echo $page_title ?></title>
    <meta property="og:title" content="<?php echo $page_title ?>">
    
    <meta name="keywords" content="">
    <?php if ( $description != "" ) { ?><meta name="description" content="<?php echo $description ?>">
    <meta name="og:description" content="<?php echo $description ?>"><?php } ?>
    <?php if ( isset( $blogitem ) ) { 
        echo '<meta property="og:article:published_time" content="'.$blogitem->get_pubdate("c").'">'; 
        //<meta property="og:article:author" content=""> Link to the Author Archive 
        //<meta property="og:article:section" content=""> A high-level section name. E.G. Technology -->
        //<meta property="og:article:tag" content=""> Tag words associated with this article -->
        } 
        if ( isset( $item ) ) { 
            $firstimage = $item->getFirstPhoto(); 
            echo '<meta property=\"og:image\" content=\"'.$firstimage->getPublicUrl().'">'; 
        } 
    ?>
    
    <link rel="canonical" href="<?php echo $current_permalink ?>">
    <meta property="og:url" content="<?php echo $current_permalink ?>"> 
    
    <link rev="highchairdesign" href="http://www.highchairdesign.com/" title="CMS Programming, layout and design by J Hogue and James Re at HCd. Master PHP programming by Peter Landry at Ubercore.org">
    <link type="text/plain" rel="author" href="<?php echo BASEHREF ?>humans.txt">
    
    <script src="<?php echo BASEHREF ?>lib/js/libs/modernizr-dev.js"></script>
    
</head>

<body class="<?php echo $bodyclass ?>">
    
    <div id="nojs">
        <p>While Javascript is not essential for this website, your interaction with the content will be limited. Please turn Javascript on for the full experience. </p>
    </div>
    
    
    <!-- Header elements -->
    <header class="main-header" role="banner">
        <div class="container">
        
            <!-- Allow Screen readers (hidden for all others) to skip the navigation and get to the main content -->
            <div class="skip-link screen-reader-text">
                <a href="#content" title="Skip to content">Skip to content</a>
            </div>
            
            <div class="logo-container">
                <h1 class="site-title">
                    <a href="<?php echo BASEHREF ?>" title="Click to return to home page">
                        <span class="providence">Providence</span> <span class="symposium">Symposium</span>
                    </a>
                </h1>
            </div>
            
            <nav class="main-navigation" role="navigation">
            
<?php include( snippetPath( "main_menu_horizontal" ) ); ?>
            
            </nav>
        
        </div><!-- end .container -->
    </header>

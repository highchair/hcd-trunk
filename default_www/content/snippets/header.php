<!doctype html>
<!--[if lte IE 7]>  <html class="no-js lte-ie9 lte-ie8 lte-ie7 ie7" lang="en">  <![endif]-->
<!--[if IE 8]>      <html class="no-js lte-ie9 lte-ie8 ie8" lang="en">          <![endif]-->
<!--[if IE 9]>      <html class="no-js lte-ie9 ie9" lang="en">                  <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="en">                        <!--<![endif]-->

<?php
	
	// Sometimes cache-busting seems to be needed on CSS and JS. 
	// Use ?ver=<?php echo date("njHis") 
	
	if ( ! isset($page) ) { $page = null; }
	
	// Set the title of the page: Check the strings to do some fancy stuff...
	if ( ! isset($page_title) ) { $page_title = ''; }
	if ( ! isset($description) ) { $description = ''; }
	if ( ! isset($bodyclass) ) { $bodyclass = ''; }
	if ( ! isset($template) ) {
    	$template = ( isset($area) && isset($page) ) ? $page->getTemplateForArea($area) : "static-template"; 
    }
	$current_user = Users::GetCurrentUser(); 
	$admin_class = ( is_object( $current_user ) ) ? 'loggedin' : 'no-loggedin'; 
	
	// CUSTOM
	$var0 = getRequestVarAtIndex(0); 
	$var1 = getRequestVarAtIndex(1); 
	$var2 = getRequestVarAtIndex(2); 
	$var3 = getRequestVarAtIndex(3); 
	
	if ( BLOG_INSTALL && $var0 == BLOG_STATIC_AREA ) {
		$blogarea = Areas::FindById( 3 ); // Might need to change this on a per project basis
		$page_title = $blogarea->get_seo_title( " | ".SITE_NAME ); 
		$bodyclass = "blog archive";
		if ( $var1 == "category" ) {
    		$category = Categories::FindByName( $var2 ); 
			$description = $category->get_excerpt( 160 );
			$page_title = $category->get_seo_title( " | ".$blogarea->get_seo_title()." | ".SITE_NAME ); 
			$bodyclass = "blog category-archive";
		}
		if ( $var1 == "view" && $var2 != "" ) {
			$blogitem = Blog_Entries::FindById( $var2 ); 
			$description = $blogitem->get_excerpt( 160 );
			$page_title = $blogitem->get_seo_title( " | ".$blogarea->get_seo_title()." | ".SITE_NAME ); 
			$bodyclass = "blog single-entry";
		}
	} elseif ( CALENDAR_INSTALL && $var0 == CALENDAR_STATIC_AREA ) {
		$page_title = "Event Calendar | ".SITE_NAME;
		$bodyclass = "calendar"; 
		if ( getRequestVarAtIndex(5) ) {
			$event = Events::FindById( getRequestVarAtIndex(5) ); 
			$description = $event->get_excerpt( 160 );
			$page_title = $event->get_seo_title( " | Event Calendar | ".SITE_NAME ); 
			$bodyclass = "calendar single-event";
		} elseif ( getRequestVarAtIndex(4) ) {
    		$page_title = "Event Calendar for ".date( 'F j, Y', mktime( 0, 0, 0, $var3, getRequestVarAtIndex(4), $var2 ) )." | ".SITE_NAME; 
    		$bodyclass = "calendar events-for-day";
		}
	} elseif ( PORTFOLIO_INSTALL && isset($area) && $area->is_portfolioarea() ) {
		// With Item
		if ( ! empty( $var2 ) ) {
            $item = ( ITEM_ID_IN_URL ) ? Items::FindById( $var2 ) : Items::FindByName( $var2 ); 
            $description = $item->get_excerpt( 160 );
            $page_title = $item->get_seo_title( " | ".$page->get_title()." | ".$area->get_title()." | ".SITE_NAME ); 
            $bodyclass = "portarea-{$area->id} section-{$page->id} single-item item-{$item->id}";
        
        // No item but a port area and section
		} elseif ( isset($page) ) {
            $description = ( $page->content != "" ) ? $page->get_excerpt( 160 ) : $area->get_excerpt( 160 );
            $page_title = $page->get_seo_title( " | ".$area->get_title()." | ".SITE_NAME ); 
            $bodyclass = "portarea-{$area->id} section-landing section-{$page->id}";
		} else {
            $description = $area->get_excerpt( 160 );
            $page_title = $area->get_seo_title( " | ".SITE_NAME ); 
            $bodyclass = "portarea-{$area->id}";
		}
    } else {
		$description = $page->get_excerpt( 160 );
		$bodyclass = "area-{$area->id} page-{$page->id}";
		
		if ( isset($area) && $area->name == "" ) {
			// The homepage
			$page_title = $page->get_seo_title();
		} elseif ( isset($page) ) {
			$page_title = $page->get_seo_title( " | ".$area->get_seo_title()." | ".SITE_NAME ); 
		} 
	} 
	$bodyclass = $template.' '.$bodyclass.' '.$admin_class; 
    
    if ( empty($description) ) {
        $globalarea = Areas::FindById(1); // Store the default meta description in the Global Area seo_title
        $description = $globalarea->get_seo_title(); 
    }
    $description = strip_tags($description); 
    
    
	$current_permalink = "http://".SITE_URL.BASEHREF.$var0;
	if ( $var1 != "" )
		$current_permalink .= "/".$var1; 
	if ( $var2 != "" )
		$current_permalink .= "/".$var2; 
	if ( $var3 != "" )
		$current_permalink .= "/".$var3; 
	if ( getRequestVarAtIndex(4) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(4); 
	if ( getRequestVarAtIndex(5) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(5); 
	if ( getRequestVarAtIndex(6) != "" )
		$current_permalink .= "/".getRequestVarAtIndex(6); 

?><head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website# article: http://ogp.me/ns/article#">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <!--<link rel="prefetch" href="//fonts.googleapis.com/css?family=Domine:700,400">
    <link rel="prefetch" href="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js">-->
    
    <meta name="apple-mobile-web-app-title" content="<?php echo SITE_NAME ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="HandheldFriendly" content="True">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=yes">
    
    <link rel="stylesheet" href="<?php echo BASEHREF ?>lib/css/style.css">
    <link rel="stylesheet" href="<?php echo BASEHREF ?>lib/css/mq.css" media="not print, braille, embossed, speech, tty">
    <!-- <link href='//fonts.googleapis.com/css?family=Domine:700,400' rel="stylesheet" type="text/css"> -->
    
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
    
    <meta name="description" content="<?php echo $description ?>">
    <?php if ( $description != "" ) { ?><meta name="og:description" content="<?php echo $description ?>"><?php } ?>
    <?php if ( isset( $blogitem ) ) { 
        echo '<meta property="og:article:published_time" content="'.$blogitem->the_pubdate("c").'">'; 
        //<meta property="og:article:author" content=""> Link to the Author Archive 
        //<meta property="og:article:section" content=""> A high-level section name. E.G. Technology -->
        //<meta property="og:article:tag" content=""> Tag words associated with this article -->
    } ?>
    
    <link rel="canonical" href="<?php echo $current_permalink ?>">
    <meta property="og:url" content="<?php echo $current_permalink ?>"> 
    <!-- <meta property="og:image" content="" /> If no specific image is part of the content, we should specify a default
    <meta property="og:video" content="" /> -->
    
    <link rev="highchairdesign" href="http://www.highchairdesign.com/" title="CMS Programming, layout and design by J Hogue and James Re at HCd. Master PHP programming by Peter Landry at Ubercore.org">
    <link type="text/plain" rel="author" href="<?php echo BASEHREF ?>humans.txt">
    
    <script src="<?php echo BASEHREF ?>lib/js/libs/modernizr-dev.js"></script>
    
</head>

<body class="<?php echo $bodyclass ?>">
    
    <a class="anchor-top" name="top"></a>
        
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
                        <?php echo SITE_NAME ?>
                    </a>
                </h1>
            </div>
            
            <nav class="main-navigation" role="navigation">
                
                <a class="toggle-menu" href="#secondarymenu"><span class="menu-icon">&equiv;</span> Navigate</a>
                
<?php include( snippetPath("main_menu_horizontal") ); ?>
            
            </nav>
        
        </div>
    </header>

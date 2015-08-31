<?php
	if ( isset($_POST['quick_select']) ) { redirectToUrl( $_POST["quick_select"] ); }
	
	// If the user requested the login page but has a bookmarked place to go (or they were logged out while working)
	$optionalredirect = "";
	if ( getRequestVarAtIndex(1) != "" && getRequestVarAtIndex(1) != " " )
		$optionalredirect .= getRequestVarAtIndex(1); 
	if ( getRequestVarAtIndex(2) != "" && getRequestVarAtIndex(2) != " " )
		$optionalredirect .= "/".getRequestVarAtIndex(2); 
	if ( getRequestVarAtIndex(3) != "" )
		$optionalredirect .= "/".getRequestVarAtIndex(3); 
	if ( getRequestVarAtIndex(4) != "" )
		$optionalredirect .= "/".getRequestVarAtIndex(4); 
	if ( getRequestVarAtIndex(5) != "" )
		$optionalredirect .= "/".getRequestVarAtIndex(5); 
	if ( getRequestVarAtIndex(6) != "" )
		$optionalredirect .= "/".getRequestVarAtIndex(6); 
	
	$userroles = explode( ',', USER_ROLES );
	//$userroles = array( "admin", "staff" ); 
	
	if( ! ( getRequestVarAtIndex(0) == "admin" && getRequestVarAtIndex(1) == "login" ) )
	{
		LoginRequired( "/admin/login/".$optionalredirect, $userroles ); 
	}
	
	$page = get_content_page();
	$area = get_content_area();
	$user = Users::GetCurrentUser();
	
	$pagename = getRequestVarAtIndex(1); 
	
	$pagetitle = ( $pagename != "" ) ? ucwords( unslug( $pagename ) ) : "Backend GUI"; 
	
	$bodyclass = ( $pagename != "" ) ? $pagename : "home"; 
	
	$maintenancemode = ( MAINTENANCE_MODE ) ? ' {Maintenance Mode}' : ''; 
	
?><!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" class="no-js">
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo $pagetitle." | ".SITE_NAME ?></title>
		
		<meta name="ROBOTS" content="NOARCHIVE" /><meta name="ROBOTS" content="NOINDEX,NOFOLLOW" /><meta name="Googlebot" content="NOINDEX,NOFOLLOW" />
		<meta http-equiv="imagetoolbar" content="no" /><meta http-equiv="imagetoolbar" content="false" />
		<meta http-equiv="galleryimg" content="no" /><meta http-equiv="galleryimg" content="false" />
		
		<link rel="shortcut icon" href="<?php echo BASEHREF ?>lib/admin_images/hcd-favicon.ico" />
		<link rev="highchair designhaus" href="http://www.highchairdesign.com/" title="CSS, PHP and HTML design by J Hogue and James Re at Highchair designhaus, Providence RI. Additional master PHP programming by Peter Landry at Ubercore.org" />
		
		<script type="text/javascript">
            (function(d,c){d[c]=d[c].replace(/\bno-js\b/, "js");})(document.documentElement,"className");
        </script>
		
		<script type="text/javascript" src="<?php echo BASEHREF ?>lib/js/libs/jquery.js"></script><!-- 1.8.3 -->
		<script type="text/javascript" src="<?php echo BASEHREF ?>lib/js/libs/jquery-ui.js"></script><!-- 1.9.2 -->
		<script type="text/javascript" src="<?php echo BASEHREF ?>lib/js/admin-plugins.js"></script>
		
		<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
        <script>
            /*
             * Load TinyMCE options. 
             * We are using the CDN version now, loaded in the layouts/admin.php template. 
             * See more http://www.tinymce.com/wiki.php/Configuration:style_formats
             */
            tinymce.init({
                selector:'textarea:not(.mceNoEditor)',
                browser_spellcheck : true,
                block_formats: "Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Blockquote=blockquote;Address=address", 
                plugins: "textcolor link hr fullscreen code importcss visualblocks", // http://www.tinymce.com/wiki.php/Plugin:importcss
                toolbar: [ 
                    "bold | italic | formatselect | bullist | numlist | alignleft | aligncenter | alignright | alignjustify | outdent | indent | styleselect", 
                    "fontsizeselect | forecolor | backcolor | link | unlink | hr | removeformat | code | fullscreen"
                ], 
                menubar: false,
                convert_urls: false,
                document_base_url: "http://<?php echo SITE_URL ?>",
                content_css: "<?php echo BASEHREF ?>lib/stylesheets/mce.css",
                importcss_append: true, 
                style_formats_merge: false,
                style_formats: [
                    {title: "Headers", items: [
                        {title: "Header 1", format: "h1"},
                        {title: "Header 2", format: "h2"},
                        {title: "Header 3", format: "h3"},
                        {title: "Header 4", format: "h4"},
                        {title: "Header 5", format: "h5"},
                        {title: "Header 6", format: "h6"}
                    ]},
                    {title: "Blocks & Lists", items: [
                        {title: "Paragraph", format: "p"},
                        {title: "Blockquote", format: "blockquote"},
                        {title: "Pre", format: "pre"}
                    ]},
                    {title: "Alignment", items: [
                        {title: "Left", icon: "alignleft", format: "alignleft"},
                        {title: "Center", icon: "aligncenter", format: "aligncenter"},
                        {title: "Right", icon: "alignright", format: "alignright"},
                        {title: "Justify", icon: "alignjustify", format: "alignjustify"}
                    ]},
                    {title: "Typography", items: [
                        {title: "Quote", icon: "blockquote", inline: "q"},
                        {title: "Citation", inline: "cite"},
                        {title: "Bold", icon: "bold", inline: "strong"},
                        {title: "Italic", icon: "italic", inline: "em"},
                        {title: "Underline", icon: "underline", inline: "u"},
                        {title: "Strikethrough", icon: "strikethrough", inline: "strike"},
                        {title: "Superscript", icon: "superscript", inline: "sup"},
                        {title: "Subscript", icon: "subscript", inline: "sub"}
                    ]},
                    {title: "Editing Extras", items: [
                        {title: "Abbreviation", inline: "abbr"},
                        {title: "Definition", inline: "dfn"},
                        {title: "Highlight", inline: "mark"},
                        {title: "Deletion", inline: "del"},
                        {title: "Insertion", inline: "ins"}
                    ]},
                    {title: "Other Elements", items: [
                        {title: "Code", inline: "code"},
                        {title: "Variable", inline: "var"},
                        {title: "Keyboard keys", inline: "kbd"},
                        {title: "Sample output", inline: "samp"}
                    ]}
                ]
            });
            function insertDocument(name, leftMarker, rightMarker) {
            	tinyMCE.execCommand("mceInsertContent", false, leftMarker + name + rightMarker);
            }
        </script>
<?php 
        include_snippet("admin_custom_js"); 
		
		$needcalendar = array("add_event", "edit_event", "add_entry", "edit_entry","edit_member"); 
		if (in_array($pagename, $needcalendar)):		
			echo "\t\t<script type=\"text/javascript\" src=\"".BASEHREF."lib/calendar/calendar_util.js\" language=\"javascript\"></script>\n"; 
		endif; 
?>

		<link rel="stylesheet" type="text/css" href="<?php echo get_link("/css/admincss") ?>" media="screen" />
	</head>
	
	<!--[if lt IE 7]><body class="oldie ie6 <?php echo $bodyclass ?>"><![endif]-->
	<!--[if IE 7]>   <body class="oldie ie7 <?php echo $bodyclass ?>"><![endif]-->
	<!--[if IE 8]>   <body class="oldie ie8 <?php echo $bodyclass ?>"><![endif]-->
	<!--[if gt IE 8]><!--><body class="<?php echo $bodyclass ?>"><!--<![endif]-->
        
        <div id="header" class="clearfix">
			<a name="top"></a>
			<div id="global"><a href="<?php echo get_link("/"); ?>">View <?php echo SITE_URL ?></a></div>
			<h1><?php echo SITE_NAME . $maintenancemode ?> <small>Content Management System</small></h1>
			<h4>Developed by Highchair designhaus and Peter Landry, Providence RI</h4>
		</div>
		
    	<div id="document">
			<div id="navigation"> 
<?php if(IsUserLoggedIn()) { ?>
			
				<a class="droplink" href="<?php echo get_link("admin/"); ?>">Dashboard</a>
				<div id="buttons">
<?php 
	include_snippet("admin_nav_level1"); 
	echo "\n"; 
	include_snippet("admin_nav_level2"); 
	echo "\n"; 
	include_snippet("admin_nav_level3"); 
?>

					<h4><a id="admin_logout" href="<?php echo get_link("admin/logout"); ?>">Logout</a></h4>
				</div>
				<p>Logged in as: <strong title="<?php echo $user->email ?>"><?php echo $user->get_name_or_email() ?></strong>
				<a href="<?php echo get_link("admin/edit_user/".$user->id); ?>">Edit your Password</a></p>
				
				<p><b>Need to report a Bug?</b><br /> <a href="mailto:jh@highchairdesign.com">Email jh@highchairdesign.com</a> and be sure to describe the problem that you are having with your browser version and page URL where the error happened. </p>
<?php } else { ?>

				<img src="<?php echo BASEHREF ?>lib/admin_images/hcdlogo_24.png" title="" />
<?php } // end if user is logged in ?>
				
			</div>
			
			<div id="left">
				<div id="content">
					<div id="status_message"><?php displayFlash(); ?></div>
					<?php
                        // Check a few things and throw a warning
                        $warnings = ''; 
                        
                        // See if the cache folder exists where it should
                        if ( ! is_dir( SERVER_CACHE_ROOT ) ) {
                            $warnings .= 'Cache <span>folder is missing!</span> '; 
                        }
                        if ( ! is_dir( SERVER_DBBACKUP_ROOT ) ) {
                            $warnings .= 'DB_backup <span>folder is missing!</span> '; 
                        }
                        if ( ! is_dir( SERVER_DOCUMENTS_ROOT ) ) {
                            $warnings .= 'Documents <span>folder is missing!</span> '; 
                        }
                        if ( ! is_dir( SERVER_DOCUMENTS_ROOT.'gallery_photos' ) ) {
                            $warnings .= 'Gallery_photos <span>folder is missing!</span> '; 
                        }
                        
                        if ( $warnings != '' )  
                            echo '<h2 class="system-warning">'.$warnings.'</h2>'; 
						
						// main content requested goes here
						echo $page->getContent();
					?>
					
				</div>
			</div>
			<p class="lighter">
				Version 2.7 &ndash; build <?php echo SVN_VERSION ?><br />
				This System is designed with a minimum screen width of 1024px. If things don&rsquo;t look right, consider checking your display settings.
			</p>
		</div>
        
        <script type="text/javascript">
			$().ready(function() {
				
				/* A general function that opens and closes a div... takes the href (starting with a #) and looks for a div of the same id (without the #) to open/slide toggle */
				$("a.openmenu").click(function() {
				    var iden = jQuery(this).attr('href');
				    //$(iden).toggleClass("opened"); Bah. Why did this fail all the sudden?
				    $(iden).slideToggle();
				    $(this).toggleClass("menu-close"); 
				    return false;
				}); 
				
				/* This one is for the page insertables. Needs to be separate so we can also close them when opened. */
				$("a.openclose").click(function() {
					
					$("div.dropslide").each(function() {
						$(this).slideUp(); 
					});
					$("a.openclose").each(function() {
						$(this).removeClass("opened")
					}); 
					
					var iden = $(this).attr('href');
					$(this).toggleClass("opened");
					$(iden).slideToggle();
					return false;
				});
				
				/* A version with no return false on it for a href's that also need a click action (anchor more than likely) */
				$("a.openclose-return").click(function() {
					var iden = jQuery(this).attr('href');
					$(this).toggleClass("opened");
					$(iden).slideToggle();
				});
			});
			
		</script>

	</body>
</html>
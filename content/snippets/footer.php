    
    <!-- Footer and social media list -->
    <footer class="main-footer" role="navigation">
        
        <div class="container">
            
            <nav class="secondary-navigation" role="navigation">
            
<?php include("framework/content/snippets/main_menu_horizontal.php"); ?>
            
            </nav>
            
            <div class="socialmedia">
                <h4>Connect With Us</h4>
                <ul class="contact-links menu">
                    <li><a class="icon-facebook" href="http://www.facebook.com/pages/Providence-Preservation-Society/43270439380" title="Friend us!" target="_blank">Facebook</a></li>
                    <li><a class="icon-twitter" href="http://twitter.com/PPSRI_1956" title="Follow us!" target="_blank">Twitter</a></li>
                    <li><a class="icon-mail" href="mailto:info@ppsri.org" title="Contact us!" target="_blank">info@ppsri.org</a></li>
                    <li><a class="icon-phone" href="tel:401.831.7440" title="Call us!" target="_blank">401.831.7440</a></li>
                    <li><a class="icon-PPS-logo" href="http://www.ppsri.org" title="Visit us online!" target="_blank">www.ppsri.org</a></li>
                </ul>
            </div>
            
            <div class="copyright">
                <p>&copy;<?php echo date("Y") ?> <?php echo SITE_NAME ?> and the <span class="pre">Providence Preservation Society.</span> <br>
                Site design and CMS donated in part by <a href="http://www.highchairdesign.com" title="Web design and development in Providence RI" class="pre">Highchair designhaus</a>. </p>
            </div>
            
        </div><!-- end .container -->
        
    </footer>

<?php 
	// HCd Edit Window
	
	//$additional = "<a href=\"".get_link("admin/edit_gallery/7")."\">Edit Home Gallery</a>"; 
	$additional = ( isset( $additional ) ) ? $additional : ""; 
	
	if ( getRequestVarAtIndex() == BLOG_STATIC_AREA || get_class($page) != "StaticPage" ):
		if ( getRequestVarAtIndex() == BLOG_STATIC_AREA ) 
			echo Blogs::DisplayBlogEditLinks(); 
		elseif ( getRequestVarAtIndex() == "events" ) 
			echo Events::DisplayCalendarEditLinks( $event );
		else 
			if ( $page && isset($item) ) 
				echo $page->getPageEditLinks( $area, $additional, $item ); 
			else
				echo $page->getPageEditLinks( $area, $additional );  
	endif; 
?>  
    
    <!--[if lt IE 8]>
    <div id="oldie-warning">
        <p class=chromeframe>Your browser is <em>ancient</em> and <a href="http://www.ie6countdown.com/">Microsoft agrees</a>. <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience a better web.</p>
    </div>
    <![endif]-->
    
    
    <!-- <script src="<?php echo BASEHREF ?>lib/js/jquery-1.7.1.min.js"></script> 
    <script src="<?php echo BASEHREF ?>lib/js/plugins.js"></script>
    <script src="<?php echo BASEHREF ?>lib/js/scripts.js"></script>
    end scripts -->
    
    <!-- Asynchronous Analytics snippet
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'XX-XXXXXXX-X']);
    _gaq.push(['_trackPageview']);
    
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>-->

</body>
</html>
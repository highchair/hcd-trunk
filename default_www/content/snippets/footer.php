    <!-- Footer and social media list -->
    <footer class="main-footer" role="contentinfo">
        <div class="container">
        
            <nav id="secondarymenu" class="secondary-navigation navigation" role="navigation">
            
<?php include( snippetPath("main_menu_horizontal") ); ?>
            
            </nav>
            
            <div class="copyright">
                <p><?php echo SITE_NAME ?>. All content &copy;<?php echo date("Y") ?> the respective authors.</p>
                <p class="hidden">Site design and CMS by <a href="http://www.highchairdesign.com" title="Web design and development in Providence RI">Highchair designhaus</a>. </p>
            </div>
        
        </div>
    </footer>
    
    <a class="anchor-bottom" name="bottom"></a>
    
    <div id="nojs">
        <div class="container">
            <p>While Javascript is not essential for this website, your interaction with the content will be limited. Please turn Javascript on for the full experience. </p>
        </div>
    </div>

    <!--[if lt IE 8]>
    <div id="oldie-warning">
        <div class="container">
            <p class=chromeframe>Your browser is <em>ancient</em> and <a href="http://www.ie6countdown.com/">Microsoft agrees</a>. <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience a better web.</p>
        </div>
    </div>
    <![endif]-->
    

<?php 
	/* HCd Edit Window
	 * The functions that draw the edit window options check to see if a user is logged in. 
	 * No need to pass Users::GetCurrentUser() here
	 */
	 
	//$additional = "<a href=\"".get_link("admin/edit_gallery/7")."\">Edit Home Gallery</a>"; 
	$additional = ( isset( $additional ) ) ? $additional : ""; 
	
	if ( ! is_null($page) ):
		if ( $page && isset($item) ) 
			echo $page->getPageEditLinks( $area, $additional, $item ); 
		else
			echo $page->getPageEditLinks( $area, $additional );  
	elseif ( getRequestVarAtIndex() == "blog" ): 
		echo Blogs::DisplayBlogEditLinks(); 
	elseif ( getRequestVarAtIndex() == "events" ): 
		echo Events::DisplayCalendarEditLinks( $event );
	endif; 
?>  
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ STATIC_URL }}js/libs/jquery.js"><\/script>')</script>
    <script src="<?php echo BASEHREF ?>lib/js/site.js"></script>
    
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
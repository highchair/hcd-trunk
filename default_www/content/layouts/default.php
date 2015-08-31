<?php 
	/* Lots of variables get set in this file */
	
	include_once( snippetPath("header") ); 
	
	/* Available as = 
	 * $page, $area or $area->is_portfolioarea(), is_object( $current_user )
	 * $page_title, $description, $bodyclass, $template
	 * getRequestVarAtIndex()s = $var0, $var1, $var2, $var3
	 * isset( $blogarea ), isset( $category )
	 * isset( $event )
	 * isset( $item )
     * If we use "is_object" for some of the above, we get an "undefined" error is they are not set
	 */
?>
    
        <section id="content" class="content-wrapper">
            <div class="container">
                <div class="row default-columns">
            
                    <article class="main-content column text" role="main">
                        <?php
                            $page->the_content(); 
                        ?>
                        
            		</article>
                    
                    <aside class="main-sidebar column" role="complementary">
                    
                    </aside>
    		
    		    </div>
    		</div>
        </section><!-- end #content .content-wrapper -->

<?php 
	include_once( snippetPath("footer") );
?>
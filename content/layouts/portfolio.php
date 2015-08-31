<?php 
	/* Lots of variables get set in this file */
	
	include_once( snippetPath("globals") ); 
    include_once( snippetPath("header") ); 
	
	/* Available as = 
	 * $page, $area or $area->is_portfolioarea(), is_object( $current_user )
	 * $page_title, $description, $bodyclass, $template
	 * getRequestVarAtIndex()s = $var0, $var1, $var2, $var3
	 * isset( $blogarea ), isset( $category )
	 * isset( $event )
	 * isset( $item )
     * If we use "is_object" for some of the above, we get an "undefined" error if they are not set
     * I prefer empty(), as it returns false if not set at all and true if not false or an array or just not null. 
	 */
	 
	 // Remember, $page in this context is the first (most recent, we hope) section in the area
    
    
    // Render a single Item
    if ( isset($item) ) {
        
        // Render gallery and thumbs
        $photos = $item->getPhotos(); 
        $images = $thumbs = ''; 
        $counter = 1; 
        
        foreach ( $photos as $photo ) {
            
            $url = $photo->getPublicUrl(); 
            /*$images .= '<li class="gallery--item">
                            <figure class="gallery--itemwrap">
                                <a class="gallery--link js-popup" href="'.$url.'">
                                    <img class="gallery--img" src="'.$url.'" alt="'.htmlspecialchars($photo->caption).'">
                                </a>
                                <figcaption class="gallery--caption"><span>'.htmlspecialchars($photo->caption).'</span></figcaption>
                            </figure>
                        </li>';*/
            $images .= '<li class="gallery--item">
                            <figure class="gallery--itemwrap">
                                <img class="gallery--img" src="'.$url.'" alt="'.htmlspecialchars($photo->caption).'">
                                <figcaption class="gallery--caption"><span>'.htmlspecialchars($photo->caption).'</span></figcaption>
                            </figure>
                        </li>';
            
            $counter++; 
        }
        
        $previous = $item->findPrev( $page ); 
        $next = $item->findNext( $page ); 
        
        $prevnext = '<nav class="prevnext prevnext__portfolio" role="navigation"><ul class="prevnext--list menu"><li class="prevnext--previous">'; 
        if ( ! empty($previous) ) {
            $prevnext .= '<a class="prevnext--link" href="'.$previous->get_url( $area, $page ).'"><span class="icon icon-arrow-left" aria-hidden="true"></span><span class="prevnext--label">'.$previous->get_title().'</span></a>'; 
        } else { $prevnext .= '&nbsp;'; }
        
        $prevnext .= '</li><li class="prevnext--next">'; 
        if ( ! empty($next) ) {
            $prevnext .= '<a class="prevnext--link" href="'.$next->get_url( $area, $page ).'"><span class="prevnext--label">'.$next->get_title().'</span><span class="icon icon-arrow-right" aria-hidden="true"></span></a>'; 
        } else { $prevnext .= '&nbsp;'; }
        $prevnext .= '</li></ul></nav>'; 
        
        
        // If this is Projects, allow the first to look like a feature
        // Reminder, $page in this instance is actually the section for Projects
        $return_message = ( $page->id == 5 ) ? 'Project List' : 'Galleries'; 
?>
                            
                        <div class="portfolio gallery" role="main">  
                            <div class="gallery--container"> 
                                <div class="flexslider js-slideshow"><?php // add .js-gallery to initiate MagnificPopup ?>
                                    <ul class="slides gallery--list menu">
                                        <?php echo $images ?>
                                        
                                    </ul>
                                </div>
                                <div class="gallery--total"><?php echo $counter ?> images</div>
                            </div><!-- end .project -->
                            
                            <section class="portfolio--content" role="complementary">
                                <header class="portfolio--header">
                                    <h1 class="portfolio--title"><?php $item->the_title() ?></h1>
                                    <div class="return">
                                        <div class="portfolio--sharing addthis_sharing_toolbox"></div>
                                        <p class="return--link"><a href="<?php $page->the_url( $area ) ?>">&laquo; Back to <?php echo $return_message ?></a></p>
                                    </div>
                                </header>
                                
                                <div class="col-row__default">
                                    <div class="portfolio--description column column--main typography">
                                        <?php $item->the_content() ?>
                                        
                                        <footer class="portfolio--prevnext">
                                            <?php echo $prevnext ?>
                                        </footer>
                                    </div>
                                
                                    <aside class="column column--aside">&nbsp;
                                        <?php //<div class="widget">
                                            //<h3 class="widget--title">Related News</h3> 
                                        //</div>
                                        ?>
                                    </aside>
                                    
                                </div>
                            </section>
                            
                        </div><!-- end  -->
<?php 
    } else { 
        
        // Render the landing page of a Section
        $items = $page->findItems( false, "display_order", "ASC"); 
        $featured = ''; 
        
        // Find a feature.
        // If none, order by Display Order (as query does above)
        // If one exists, show it first and take it out of the render later so it does not appear twice. 
        // But only if this is the Projects section right now
        if ( $page->id == 5 ) {
            $feature = Items::FindFeaturedItem();  
            if ( is_object($feature) ) {
                $gal_image = $feature->getFirstPhoto(); 
                $featured .= '<div class="column portfolio-grid--item portfolio-grid__first"><a class="portfolio-grid--link" href="'.$feature->get_url( $area, $page ).'"><figure class="portfolio-grid--photo"><img src="'.$gal_image->getPublicUrl().'" alt="'.$feature->get_title().'"><figcaption class="portfolio-grid--caption"><span class="portfolio-grid--title">'.$feature->get_title().'</span></figcaption></figure></a></div>'; ; 
            } 
        }
?>

                        <div class="portfolio-grid slide--container">
                        <?php
                            // If it exists or not, echo it. If not, nothing gets treated like a feature
                            echo $featured; 
                            
                            foreach ( $items as $theitem ) {
                                // Now just be sure to exclude what was featured
                                if ( empty($theitem->is_featured) ) {
                                    $gal_image = $theitem->getFirstPhoto(); 
                                    echo '<div class="column portfolio-grid--item"><a class="portfolio-grid--link" href="'.$theitem->get_url( $area, $page ).'"><figure class="portfolio-grid--photo"><img src="'.$gal_image->getPublicUrl().'" alt="'.$theitem->get_title().'"><figcaption class="portfolio-grid--caption"><span class="portfolio-grid--title">'.$theitem->get_title().'</span></figcaption></figure></a></div>'; 
                                }
                            }
                        ?>
                            
                        </div><!-- end .project-grid -->

<?php 
    } // end if( $item ) 
    
    //echo $subnavigation; 
    
    $javascripts = '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5573b6cd1685fe97" async="async"></script>'; 
    
	include_once( snippetPath("footer") );
?>
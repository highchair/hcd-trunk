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
     * If we use "is_object" for some of the above, we get an "undefined" error is they are not set
     * I prefer empty(), as it returns false if not set at all and true if not false or an array or just not null. 
	 */
?>
    
                        <div class="col-row__page">
                        
                            <article class="column--main column pull-right" role="main">  
                                <div class="content--main typography">
                                    <?php $page->getContent(); ?>
   
                                </div>
                            </article>
                            
                            <?php if ( ! empty($area) ) { ?>
                            <aside class="column--aside column" role="complementary">
                                <div class="content--sidebar js-sticky">
                                    <?php 
                                        $subpages = $area->findPages(); 
                                        
                                        if ( count($subpages) > 1 ) {
                                            
                                            echo '<div class="widget widget__subnavigation">';
                                            $area->the_title( '<h3 class="widget--title">','</h3>' ); 
                                            echo '<ul class="widget--list menu">';
                                            
                                            foreach ( $subpages as $thepage ) {
                                                $selected = ( $thepage->id == $page->id ) ? ' widget--link__selected' : ''; 
                                                echo '<li><a class="widget--link'.$selected.'" href="'.$thepage->get_url().'">'.$thepage->get_title().'</a></li>'; 
                                            } 
                                            echo '</ul></div>'; 
                                        }
                                    ?>
                                
                                </div>
                            </aside>
                            <?php } ?>
                            
                        </div>

<?php 
	include_once( snippetPath("footer") );
?>
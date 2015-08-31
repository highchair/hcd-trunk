<?php
function initialize_page() { }

function display_page_content()
{
	
	// Define a function to draw a post... easiest way to manage the output in one place
	function get_entry( $thisentry, $link=true, $excerpt=false, $current_user='' ) 
	{
	   $categories = ( $thisentry->get_categories() != '' ) ? ' in '.$thisentry->get_categories() : ''; 
	   
	   $entry = '<article class="entry typography"><header>'; 
	   
	   // If there is an image
	   if ( $thisentry->template =='with-image' ) {
    	   echo '<p>image</p>'; 
	   }
	   
	   if ( $link ) {
    	   $entry .=  '<h1 class="entry--title"><a href="'.$thisentry->get_url().'">'.$thisentry->get_title().'</a></h1>'; 
	   } else {
    	   $entry .=  '<h1 class="entry--title">'.$thisentry->get_title().'</h1>'; 
	   }
	   
	   // If an admin is logged in, set these flags so they can see new posts in the stream. 
	   if ( empty($current_user) ) {
    	   $public = $future = ''; 
	   } else {
    	   $public = ( $thisentry->public ) ? '' : '<span class="entry--notpublic">Not Public</span> '; 
    	   $future = ( $thisentry->date > date('Y-m-d h:i:s') ) ? '<span class="entry--future">Future Dated</span> ' : ''; 
	   }
	   
	   $entry .=  '<p class="entry--date-posted">'.$future . $public.' <time datetime="'.$thisentry->get_pubdate("c").'">'.$thisentry->get_pubdate( 'F j, Y' ).'</time>'.$categories.'</p>'; 
	   $entry .=  '</header>'; 
	   
	   $entry .=  '<div class="entry--content">'; 
	   if ( $excerpt ) {
           // This preserves HTML and HCD images, but chops on a paragraph and adds a read more. 
    	   $entry .=  $thisentry->chopForBlogDigest( 480, 'Read the Entire Post' );
	   } else {
    	   $entry .= $thisentry->get_content();
	   }
	   $entry .= '</div></article>'."\n"; 
	   
	   return $entry; 
	}
	function the_entry( $thisentry, $link=true, $excerpt=false, $current_user='' ) { echo get_entry( $thisentry, $link, $excerpt, $current_user ); }
	
	
	// Start the template
	
	/* This include sets the following: 
	 * $page, $area or $area->is_portfolioarea(), is_object( $current_user )
	 * $page_title, $description, $bodyclass, $template
	 * getRequestVarAtIndex()s = $var0, $var1, $var2, $var3
	 * isset( $blogarea ), isset( $category ), isset( $blogitem )
	 */
    $area = Areas::FindById( 3 ); 
    include_once( snippetPath("header") );  
	
	
	// Still need this to build the navigation and for previous and next posts
	$the_blog = Blogs::FindById( 1 ); 
	$entries_per_page = 8; 
	
	
	$breadcrumbs = '<div class="breadcrumbs"><a class="breadcrumbs--link" href="'.BASEHREF.'">Home</a> <span class="breadcrumbs--sep">&raquo;</span> <a class="breadcrumbs--link" href="'.get_link( BLOG_STATIC_AREA ).'">'.$area->get_title().'</a>'; 
    
    if ( ! empty($blogitem) ) {
        $title = $blogitem->get_title(); 
        $breadcrumbs .= ' <span class="breadcrumbs--sep">&raquo;</span> <span class="breadcrumbs--link__active">'.chopText( $title, 36, ' ' ).'&hellip;</span>'; 
    }
    $breadcrumbs .= '</div>'; 
?>
                        
                        <?php echo $breadcrumbs ?>
                        
                        <div class="default-col">
                        
                            <section class="column" role="main">  
                                <div class="content--main">
<?php 
    // Single post
	if ( ! empty($blogitem) ) {   
	    
	    $singleentry = get_entry( $blogitem, false, false, $current_user );
        $singleentry .= '<footer class="prevnext prevnext__entry" role="navigation"><ul class="prevnext--list menu">'; 
        
        // Previous entry link
        $prev = $the_blog->getPrevEntry( $blogitem->date ); 
        $singleentry .= '<li class="prevnext--previous">'; 
        if ( !empty($prev) ) {
            $singleentry .= '<a class="prevnext--link prevnext--link__prev" href="'.$prev->get_URL().'"><h4 class="prevnext--label">Previous:</h4><h2 class="prevnext--title">'.$prev->get_title().'</h2></a>'; 
        } else { $singleentry .= '&nbsp;'; }
        $singleentry .= '</li>'; 
        
        // Next entry link
        $next = $the_blog->getNextEntry( $blogitem->date ); 
        $singleentry .= '<li class="prevnext--next">'; 
        if ( !empty($next) ) {
            $singleentry .= '<a class="prevnext--link prevnext--link__next" href="'.$next->get_URL().'"><h4 class="prevnext--label">Next</h4><h2 class="prevnext--title">'.$next->get_title().'</h2></a></li>'; 
        } else { $singleentry .= '&nbsp;'; }
        $singleentry .= '</li></ul></footer>'; 
        
        echo $singleentry; 
    
    } else { 
        
    // Landing page or Filtered page
        echo '<div class="entry--wrapper">'; 
        
        $public = ( is_object( $current_user ) ) ? false : true; 
        
        
    // Check for category
        if ( isset($category) ) {
            
            $pagenum = ''; 
            $entries = $category->getEntries(); 
            
            echo '<header class="category--header">'; 
    		$category->the_title( '<h1 class="category--title">Posts in <b>&ldquo;', '&rdquo;</b></h1>' ); 
    		echo '</header>'."\n"; 
     
    // Check for Year archive   
        } elseif ( $var1 == 'year' and is_numeric($var2) ) {
            
            $pagenum = ''; 
            $entries = Blog_Entries::FindByYear( $var2, $public ); 
            
            echo '<header class="category--header">'; 
    		echo '<h1 class="category--title">Posts from <b>'.$var2.'</b></h1>'; 
    		echo '</header>'."\n"; 
    
    // Not a category landing page, not a year archive, and not a single item, so, this is the blog landing page        
        } else {
    
            $pagenum = ( $var1 == "page" ) ? $var2 : 1; 
            $entries = Blog_Entries::FindByPagination( $entries_per_page, $pagenum, $public ); 
        }


    // The list of articles if there is not a single article present
		foreach ( $entries as $entry ) { 
		
            the_entry( $entry, true, true, $current_user ); 
        } 
        
    // Add some pagination
        if ( ! empty($pagenum) ) { 
?>

                                    <footer class="pagination">
                                        <ul class="pagination--list menu">
                                        <?php 
                                            $lastpage = Blog_Entries::FindLastPage( $entries_per_page, $public );
                                            $numbernav = ""; 
                                            if ( $pagenum > 1 )  
                                    			$numbernav .= '<li class="pagination--listitem"><a class="pagination--link pagination--link__prev" href="'.get_link( BLOG_STATIC_AREA."/page/".($pagenum-1) ).'" title="View Newer Posts">&#9664;</a></li>';  
                                    		
                                    		$counter = 1; 
                                    		while ( $counter <= $lastpage ):
                                    			$thispage = ( $counter == $pagenum ) ? ' pagination--link__disabled' : ''; 
                                    			$numbernav .= '<li class="pagination--listitem"><a class="pagination--link'.$thispage.'" href="'.get_link( BLOG_STATIC_AREA."/page/".$counter ).'">'.$counter.'</a></li>'; 
                                    			$counter++;
                                    		endwhile; 
                                    		
                                    		if ( $pagenum != $lastpage ) 
                                    			$numbernav .=  '<li class="pagination--listitem"><a class="pagination--link pagination--link__next" href="'.get_link( BLOG_STATIC_AREA."/page/".($pagenum+1) ).'" title="View Older Posts">&#9654;</a></li>'; 
                                    		
                                    		echo $numbernav; 
                                        ?>
                                        
                                        </ul>
                                    </footer> 
<?php
        }
        echo '</div><!-- end .entry--wrapper -->'; 
        
    } // end if
?> 

                                </div>
                            </section>
                            
                            <aside class="content--sidebar column" role="complementary">
                            <?php
                            	// List All the Categories
                            	// Show/Hide empty ones, show/hide the number of posts, show/hide the 'Uncategorized' category.
                            	$posts_by_category = $the_blog->list_categories( true, false, true ); 
                            	
                            	if ( ! empty( $posts_by_category ) ) {
                                	echo '<div class="widget widget__categories"><h2 class="widget--title">Categories</h2>'.$posts_by_category.'</div>'; 
                                }
                                
                                // List the unique years
                                $archive = '<div class="widget widget__archive"><h2 class="widget--title">Archive</h2><ul class="widget--list menu">'; 
                                $allyears = Blog_Entries::FindUniqueYears(); 
                                foreach ( $allyears as $year ) {
                                    $archive .= '<li><a href="'.get_link( BLOG_STATIC_AREA.'/year/'.$year->year ).'">'.$year->year.'</a></li>'; 
                                }
                                $archive .= '</ul></div>'; 
                                echo $archive; 
                            ?>
                                
                            </aside>
                        </div><!-- end .default-col -->


<?php 
    include_once( snippetPath("footer") ); 
    
    echo Blogs::DisplayBlogEditLinks( 1, $blogitem ); 
}
?>
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
    			
    		<div class="three-col--center">
                <div class="column image design">
                    <div class="image--crop image--vertical">
                		<img src="<?php echo BASEHREF ?>img/home-left-vert.jpg" alt="Magma landscape design" />
            		</div>
                	<div class="design--content">
                		<h3 class="image--title">We <strong>design</strong> it.</h3>
                	</div>
                </div>
                <div class="column image build">
            		<div class="image--crop image--horizontal">
                		<img src="<?php echo BASEHREF ?>img/home-center-horiz.jpg" alt="architecture and general contracting" />
                    </div>
                	<div class="build--content">
                		<h3 class="image--title">We <strong>build</strong> it.</h3>
                	</div>
                </div>
                <div class="column image love">
                    <div class="image--crop image--horizontal">
                		<img src="<?php echo BASEHREF ?>img/home-right-horiz.jpg" alt="for custom home projects" />
            		</div>
                	<div class="love--content">
                		<h3 class="image--title">You <strong>love</strong> it.</h3>
                	</div>
                </div>
            </div>
    
            <div class="three-col--message">
                <aside class="column logo">
                    <img src="<?php echo BASEHREF ?>img/plants-stone-water.png" alt="Plants Stone Water" />
                </aside>
                <article class="column message home--message">
            		<p><span class="brand">Magma</span> is the fundamental building block of the earth. Its movement is fluid, deliberate, and often energetic. What it leaves in its wake are creations that inspire awe. </p> 
                </article>
            </div>

<?php 
	include_once( snippetPath("footer") );
?>
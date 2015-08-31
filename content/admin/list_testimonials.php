<?php
	function initialize_page() {}
	
	function display_page_content()
	{
		$testimonials = Testimonials::FindAll();
?>
	
	<div id="edit-header" class="testimonialnav">
		<div class="nav-left column">
    		<h1>Choose a Testimonial to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/edit_testimonial/add") ?>" class="hcd_button">Add a new Testimonial</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<p class="announce">Testimonials are stored for insertion into content throughout the site. Depending on your site, there might be a template or two that displays one at random. All testimonials are displayed using a &ldquo;blockquote&rdquo; style. </p>
	
<?php 
		if ( count($testimonials) > 0 ) { 
?>			
	<div id="table-header" class="testimonials">
		<strong class="item-link">Testimonial Name</strong>
		<span class="item-path">Content snippet</span>
		<span class="item-revised">Attribution</span>
		<span>Featured?</span>
	</div>
	
	<ul id="listitems" class="managelist testimoniallist">
		<?php
			foreach( $testimonials as $thetest )
			{ 
				$this_feature = ($thetest->is_featured) ? 'Yes' : ''; 
				
				echo "\t\t<li>
			<span class=\"item-link\"><a href=\"" . get_link("/admin/edit_testimonial/$thetest->id") . "\">".$thetest->get_title()." <small>EDIT</small></a></span>
			<span class=\"item-path\">" . $thetest->get_excerpt( 48, " " )  . "</span>
			<span class=\"item-revised\">" . $thetest->attribution . "</span>
			<span>" . $this_feature . "</span>
		</li>\n";
			}
		?>
	
	</ul>
<?php 
		} else {
			
			echo "\t\t<h3 class=\"empty-list\">There are no testimonials to edit. <a class=\"short\" href=\"".get_link("admin/edit_testimonial/add")."\">Add one if you like</a>.</h3>"; 
		}
	} 
?>
<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$galleries = Galleries::FindAll("DESC");
?>

	<div id="edit-header" class="gallerynav">
		<div class="nav-left column">
    		<h1>Choose a Gallery to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_gallery") ?>" class="hcd_button">Add a New Gallery</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
<?php if ( isset($galleries) && count($galleries) > 0 ) { ?>
	
	<div id="table-header" class="documents">
		<strong class="item-link">Gallery Name &amp; Thumbnail</strong>
		<span class="item-public">Image Count</span>
	</div>
	
	<ul id="listgalleries" class="managelist">
<?php
		foreach( $galleries as $gallery )
		{ 
			$count = $gallery->number_of_photos(); 
			$thumb = $gallery->get_thumb();
			
			echo "\t\t<li>
			<span class=\"item-link\"><a href=\"" . get_link("/admin/edit_gallery/$gallery->id") . "\">";
			if ($thumb) { echo "<img src=\"{$thumb->getPublicUrl()}\" alt=\"$gallery->name\" />"; }		
			echo "$gallery->name</a></span>
			<span class=\"item-public\">$count images</span>";
			
			if ( in_array( $gallery->id, explode( ",", PROTECTED_ADMIN_GALLERIES ) ) ) { 
    			echo "<span class=\"item-public red\">This gallery is used by a template. It can not be deleted.</span>"; 
			}
			echo "</li>\n"; 
		}
?>
	
	</ul>
<?php 
		} else {
			echo "<h3 class=\"empty-list\">There are no galleries yet! <a href=\"".get_link("admin/add_gallery")."\">Add one if you like</a>.</h3>\n"; 
		} 
?>
	
	<p>&nbsp;</p>
	<h3>More about Galleries</h3>
	<p>Galleries can be inserted into content for pages and other things with a shortcode that looks like this:</p>
	<p><span style="color: #137;">}}gallery:name-of-galley}}</span></p>
	<p>The direction of the curly braces in the shortcode define how the gallery will lay out. For example: </p>
	<ul>
		<li><span style="color: #137; display: inline-block; width: 280px;">{{gallery:name-of-galley}}</span> Gallery will center in the content and be as large as the column will allow</li>
		<li><span style="color: #137; display: inline-block; width: 280px;">}}gallery:name-of-galley}}</span> Gallery will float left (the braces point left) and display as a thumbnail</li>
		<li><span style="color: #137; display: inline-block; width: 280px;">{{gallery:name-of-galley{{</span> Gallery will float right (the braces point right) and display as a thumbnail</li>
	</ul>
	<p>Other patterns display galleries as different types of collections of images. Here are the other patterns:</p>
	<ul>
		<li><span style="color: #137; display: inline-block; width: 280px;">{{carousel:name-of-gallery{{</span> Inserts an auto-scrolling carousel and displays it much like the gallery will display. Images can also be opened in a lightbox. </li>
		<li><span style="color: #137; display: inline-block; width: 280px;">{{random-from-gallery:name-of-gallery{{</span> Inserts one image randomly from the gallery, and floats it right or left or centers the image accordingly.</li>
	</ul>
<?php
	}
?>
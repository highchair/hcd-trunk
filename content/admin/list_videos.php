<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$videos = Videos::FindAll();
?>
	
	<div id="edit-header" class="videonav">
		<div class="nav-left column">
    		<h1>Choose a Video to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/edit_video/add") ?>" class="hcd_button">Add a New Video</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<p class="announce">Videos require the use of an outside video hosting and encoding service. The HCd&gt;CMS supports two service providers, YouTube and Vimeo. By defining videos by shortcode here, you can insert videos into text content just like images or documents. If a shortcode needs to change, you may edit it here and all references will be updated. </p>
	
<?php 
		if ( count($videos) > 0 ) { 
?>			
	<div id="table-header" class="videos">
		<strong class="item-link">Video Name</strong>
		<span class="item-filename">Service</span>
		<span class="item-viewlink">Link to Play</span>
	</div>
	
	<ul id="listitems" class="managelist videolist">
		<?php
			foreach( $videos as $thevid )
			{ 
				echo "\t\t<li>
			<span class=\"item-link\"><a href=\"" . get_link("/admin/edit_video/$thevid->id") . "\">".$thevid->get_title()." <small>EDIT</small></a></span>
			<span class=\"item-filename\">" . ucwords($thevid->service) . "</span>
			<span class=\"item-viewlink\"><a href=\"" . $thevid->getPublicUrl() . "\" target=\"_blank\">View video</a></span>
		</li>\n";
			}
		?>
	
	</ul>
<?php 
		} else {
			
			echo "\t\t<h3 class=\"empty-list\">There are no videos to edit. <a class=\"short\" href=\"".get_link("admin/edit_video/add")."\">Add one if you like</a>.</h3>"; 
		}
	} 
?>
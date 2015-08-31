<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		//$images = Images::FindAll();
		//$images = array_reverse($images); 
		
		$pagenum = getRequestVarAtIndex( 2 ); 
		$imagesperpage = 12; 
		$lastpage = Images::FindLastPage( $imagesperpage );
		$images = Images::FindByPagination( $imagesperpage, $pagenum ); 
		
		$numbernav = ""; 
		
		if ( count($images) > 0 ) {
    		$pagenum = ( $pagenum == "" ) ? 1 : $pagenum;
    		if ( $pagenum > 1 ) { 
    			$numbernav .= "<a class=\"pageprev\" href=\"".get_link("admin/list_images/".($pagenum-1))."\">Newer</a>"; 
    		} 
    		$counter = 1; 
    		while ( $counter <= $lastpage ):
    			$thispage = ( $counter == $pagenum ) ? " class=\"thispage\"" : ""; 
    			$numbernav .= "<a{$thispage} href=\"".get_link("admin/list_images/".$counter)."\">$counter</a>"; 
    			$counter++;
    		endwhile; 
    		if ( $pagenum != $lastpage ) {
    			$numbernav .=  "<a class=\"pagenext\" href=\"".get_link("admin/list_images/".($pagenum+1))."\">Older</a>"; 
    		}
        } 
?>

	<div id="edit-header" class="imagenav">
		<div class="nav-left column">
    		<h1>Choose an Image to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_image") ?>" class="hcd_button">Add a New Image</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<div class="page-numbers">
		<?php echo $numbernav ?>

	</div>
<?php if ( count($images) > 0 ) { ?>

	<div id="imageDisplay">
<?php
    	foreach($images as $image)
    	{ 
    		echo "\t\t<div class=\"image\"><a href=\"" . get_link("/admin/edit_image/$image->id") . "\">";
    		//$image->displayThumbnail();
    		echo '<img src="'.get_link( 'images/view/'.$image->id ).'" alt="'.$image->name.'">'; 
    		echo "</a>{$image->name}</div>\n";
    	}
?>
	
		<div class="clearleft"></div>
	</div>
<?php 
        } else {
            echo '<h3>There are no images yet! <a href="'.get_link("admin/add_image").'">Add one if you like</a>.</h3>'; 
        } 
?>

	<div class="page-numbers">
		<?php echo $numbernav ?>

	</div>

<?php } ?>
<?php	
	function initialize_page()
	{
	}
	
	function display_page_content()
	{
    	$message = (PORTFOLIO_INSTALL) ? 'Edit or Re-order a Portfolio Area (red), an Area (blue), or Page (white)' : 'Edit or Re-order an Area (blue) or Page (white)'; 

?>

<script type="text/javascript">
	$().ready(function() {
		$(".drop_item_link").click(function() {
			$(this).next().slideToggle();
			if ($(this).text() == "[view pages]") {
				$(this).text("[hide pages]");
			} else {
				$(this).text("[view pages]");
			}
		});
		$("ul.cat_drag, div.item_drag, div.sub_drag").sortable({
			stop: function() {
				var count = 1;
				$("ul#listpages ul.row li input.area_order").each(function() {
					$(this).val(count);
					count++;
				});
				var count = 1;
				$("ul#listpages ul.row li input.page_order").each(function() {
					$(this).val(count);
					count++;
				});
				<?php if (SUB_PAGES) { ?>
				var count = 1;
				$("ul#listpages ul.row li input.sub_page_order").each(function() {
					$(this).val(count);
					count++;
				});
				<?php } ?>
				$.post("<?php echo get_link('admin/order_parse/areaspages'); ?>", $("form#order_item").serialize());
			}
		});
	});
</script>

	<div id="edit-header" class="areanav">
    	<div class="nav-left column">
			<h1><?php echo $message ?></h1>
		</div>
		<div class="nav-right column">
			<a class="hcd_button" href="<?php echo get_link("admin/add_page"); ?>">Add a Page</a>  
    		<a class="hcd_button" href="<?php echo get_link("admin/edit_area/add"); ?>">Add an Area</a> 
		</div>
		<div class="clearleft"></div>
	</div>
	
	<form id="order_item" method="POST">
		<p>1) Click name to edit. &nbsp;2) &ldquo;Grab&rdquo; a page or area and drag it to change order. (Pages can not be dragged from one area to another. Reassign the page to another area by editing the Page and using the &ldquo;Areas&rdquo; tab.)</p>
		
		<div id="table-header">
			<strong class="item-link">Area / Page Name</strong>
			<span class="item-public">Public</span>
			<span class="item-path">Item Path</span>
		</div>
<?php
	echo "\t<ul id=\"listpages\" class=\"cat_drag clearfix\">\n"; 
	
	// ! Get Portfolio Areas
	$areas = Areas::FindAdminListAreas();
	//$area_count = 0;
	//$num_areas = count($areas);
	
	foreach ( $areas as $area ) {
		$thisAreaName = $area->display_name;
		$thisShortName = $area->name;
	    
		if( $thisShortName == "" ) {
			$thisAreaName = "Global";
			$thisShortName = "";
		}
		$arealink = get_link( "/admin/edit_area/".$area->id ); 
		$areapublic = ( $area->public ) ? "<span class=\"light\">public</span>" : " <span class=\"red\">not public &rarr; <a href=\"".get_link( $area->name )."\" target=\"_blank\">Preview</a></span>"; 
		$isportclass = "c_group";
		if ( $area->is_portfolioarea() ) { 
			$arealink = get_link( "/admin/portfolio_edit_area/".$area->id ); 
			$isportclass = " c_cat";	
		}		
echo <<<EOT

		<ul class="row">
			<li class="$isportclass">
				<!-- Don't reorder these elements -->
				<input class="area_order" type="hidden" title="AreaOrder_$area->id" name="AreaOrder_$area->id" value="$area->display_order" />
				<span class="item-link"><a name=\"jump_{$thisShortName}\"></a><a href="$arealink">$thisAreaName<small>Edit</small></a></span>
				<span class="item-public">$areapublic</span>
				<span class="item-path">/$thisShortName</span>
EOT;
				
		$pages = $area->findPages( true );
		//$pagenum = count( $pages );
		//if ( $pagenum > 8 ) {
		//	echo "\t\t\t\t<a href=\"#jump_{$thisShortName}\" class=\"drop_item_link\">[view pages]</a>\n";
		//	echo "\t\t\t\t<div class=\"item_drag\" style=\"display:none\">\n";	
		//} else {
			echo "\t\t\t\t<div class=\"item_drag\">\n";
		//}
		
		foreach ( $pages as $page ) {
			
			$pagelink = get_link("/admin/edit_page/".$page->id); 
			$pagepublic = ( $page->public ) ? "<span class=\"light\">public</span>" : " <span class=\"red\">not public &rarr; <a href=\"".get_link($area->name."/".$page->name)."\" target=\"_blank\">Preview</a></span>"; 
			$inputname = $area->id."_".$page->id; 
			$pageorder = $page->getOrderInArea($area); 
					
echo <<<EOT

					<ul class="row">
						<li class="item_line">
							<input class="page_order" type="hidden" title="PageOrder_$page->id" name="$inputname" value="$pageorder" />
							<span class="item-link"><a href="$pagelink">$page->display_name<small>Edit</small></a></span>
							<span class="item-public">$pagepublic</span>
							<span class="item-path">/$thisShortName/$page->name</span>
EOT;
			if ( SUB_PAGES ) {
				$children = $page->get_children();
				if ( count( $children > 0 ) ) {
					echo "\t\t\t\t\t<div class=\"sub_drag\">\n";
					foreach ( $children as $page ) {
						$pagelink = get_link("/admin/edit_page/".$page->id); 
						$pagepublic = ( $page->public ) ? "" : " <span class=\"red\">not public &rarr; <a href=\"".get_link($area->name."/".$page->name)."\" target=\"_blank\">Preview</a></span>"; 
						$inputname = "SubPage_".$page->id; 
						$pageorder = $page->getOrderInArea($area); 
					
echo <<<EOT
						<ul class="row">
							<li class="item_line sub_page">
								<span class="item-link"><a href="$pagelink">$page->display_name<small>Edit</small></a></span>
								<span class="item-public">$pagepublic</span>
								<span class="item-path">/$thisShortName/$page->name</span>
								<input class="sub_page_order" type="hidden" title="PageOrder_$page->id" name="$inputname" value="$pageorder" />
							</li>
						</ul>
EOT;
					}
					echo "\t\t\t\t\t</div>\n";
				}
			}
echo <<<EOT
						</li>
					</ul>
EOT;

		} 
		// End item_drag div
		echo "\t\t\t\t\t\t\t</div>\n"; 
		// End li.c_group
		echo "\t\t\t\t\t\t</li>\n"; 
		// End ul.row
		echo "\t\t\t\t\t</ul>\n"; 
	} // End foreach section
	
	// End ul.row cat_drag
	echo "\t\t\t\t</ul>\n";
	// End ul.row
	echo "\t\t\t</ul>\n";
	// end first li
	echo "\t</ul>\n";
	
	
	// Find Orphans
	$orphanlist = ""; 
	
	$allpages = Pages::FindAll(); 
	foreach ( $allpages as $page )
	{
		$possiblearea = $page->getAreas(); 
		if ( !$possiblearea ):
			$orphanlist .= "\t<a href=\"".get_link("/admin/edit_page/".$page->id)."\">$page->display_name</a><br />\n"; 
		endif; 
	}
	if ( $orphanlist != "" ):
		echo "\t<p>&nbsp;</p>\n\t<h3>Orphaned Pages</h3>\n"; 
		echo $orphanlist; 
	endif; 
?>

	<p>&nbsp;</p>
	<p><strong>Extra:</strong> If a page is not Public, you can still view it by clicking the red &ldquo;Preview&rdquo; link. This will open a new window with the page as long as you are logged in (the content will be as recent as the most recent Save). As long as you remain logged in, you may edit the page in one window or tab and preview the content in another window or tab (click refresh after each page save to see the latest changes). </p>
</form>

<?php
    // Content Chunks
    $chunks = Chunks::FindAll(); 
    
    if ( count($chunks) > 0 ) {
?>
    
    <h1 id="chunks-list">Content Chunks</h1>
	
	<p class="announce">Content Chunks are small pieces of editable text that are used by templates. Typically, they do not deserve an entire page, so we put them here. Chunks can not be created or deleted, as they have been set up when templates were set up. </p>
			
	<div id="table-header" class="documents">
		<strong class="item-link">Chunk Name</strong>
		<span class="item-filename">Description</span>
	</div>
	
	<ul id="listitems" class="managelist">
<?php
        foreach ( $chunks as $chunk ) {
            echo "\t\t<li>
			<span class=\"item-link\"><a href=\"" . get_link("/admin/edit_chunk/$chunk->id") . "\">$chunk->slug <small>EDIT</small></a></span>
			<span class=\"item-filename\" style=\"width: 50%\">$chunk->description</span>
		</li>\n";
        }
?>
    </ul>
<?php
    }
    
    $thisuser = Users::GetCurrentUser(); 
	if ($thisuser->id == "1") { 
	    echo '<p><a class="hcd_button" href="'.get_link("admin/edit_chunk/add").'">Create a new Chunk</a></p>'; 
	}
} 
?>
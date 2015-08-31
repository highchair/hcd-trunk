<?php	
	function initialize_page()
	{

	}
	
	function display_page_content()
	{
		$header = "Portfolio List"; 
		$intromessage = "Choose a Portfolio Area (red), Section (blue) or Item (white) to Edit. Drag and drop areas, sections or items to reorder them. (Items can not be dragged from one section to another &ndash; edit the item and the Sections they appear in for that)"; 
		$default_open = "opened"; 
		$alpha_open = $revised_open = $created_open = ""; 
		
		if ( requestIdParam() == "alphabetical" )
		{
			$allitems = Items::FindAll( "display_name ASC" );
			$default_open = ""; 
			$alpha_open = "opened"; 
			$header = "List Portfolio Items Alphabetically"; 
			$intromessage = "Click on any Item name to edit it."; 
		
		} elseif ( requestIdParam() == "recent_edit" ) {
			$allitems = Items::FindAll( "date_revised DESC" ); 
			$default_open = ""; 
			$revised_open = "opened"; 
			$header = "List Portfolio Items by most recently edited"; 
			$intromessage = "Click on any Item name to edit it."; 
		
		} elseif ( requestIdParam() == "recent_add" ) {
			$allitems = Items::FindAll( "id DESC" );
			$default_open = ""; 
			$created_open = "opened"; 
			$header = "List Portfolio Items by most recently added"; 
			$intromessage = "Click on any Item name to edit it."; 
			
		} 
		$itemcount = ( isset($allitems) ) ? count( $allitems ) : "";
?>

	<div id="edit-header" class="portfoliolist">	
		<div class="nav-left column">
			<h1><?php echo $header ?></h1>
		</div>
		<div class="nav-right column">
			<a class="hcd_button" href="<?php echo get_link("/admin/portfolio_add_item/") ?>">Add an Item</a> 
			<a class="hcd_button" href="<?php echo get_link("/admin/portfolio_add_section/") ?>">Add a Section</a> 
			<a class="hcd_button" href="<?php echo get_link("/admin/portfolio_add_area/") ?>">Add an Area</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<p><?php echo $intromessage ?></p>

	<form id="order_item" method="POST">
	
		<ul id="sort-list" class="menu tabs">
			<li><a class="<?php echo $default_open ?>" href="<?php echo get_link("admin/portfolio_list/"); ?>">Show Areas and Sections</a></li>
			<li><a class="<?php echo $alpha_open ?>" href="<?php echo get_link("admin/portfolio_list/alphabetical"); ?>">List <?php echo $itemcount ?> Items Alphabetically</a></li>
			<li><a class="<?php echo $revised_open ?>" href="<?php echo get_link("admin/portfolio_list/recent_edit"); ?>">Items Recently Edited</a></li>
			<li><a class="<?php echo $created_open ?>" href="<?php echo get_link("admin/portfolio_list/recent_add"); ?>">Items Recently Created</a></li>
		</ul>
<?php if ( requestIdParam() == "" ) { ?>
		<div id="table-header">
			<span class="item-link">Area / Section / Item Name</span>
			<span class="item-public">Public</span>
			<span class="item-revised">Date Revised</span>
			<span class="item-created">Date Created</span>
		</div>
<?php } ?>

		<ul id="listitems" class="clearfix">
<?php
	if ( requestIdParam() == "alphabetical" || requestIdParam() == "recent_edit" || requestIdParam() == "recent_add" )
	{
		foreach ( $allitems as $theitem )
		{
			$section = array_shift( $theitem->getSections() ); 
			$itemlink = get_link("/admin/portfolio_edit/".$section->name."/".$theitem->id); 
			if(PORTFOLIOTHUMB_IMAGE) {
				$imagelink = "<img src=\"".get_link("/portfolio/thumbnail/" . $theitem->id)."\" />"; 
			} else {
				$imagelink = " "; 
			}
			$itempublic = ""; 
			if ( !$theitem->public ) { $itempublic = "<span class=\"red\">(not public)</span>"; }
		
			echo "\t\t\t<li class=\"sorted-item\">
				<a href=\"$itemlink\" title=\"ID= $theitem->id\">$imagelink{$theitem->display_name}<small>Edit</small></a> $itempublic
			</li>\n"; 
		}
	} else {
	
		$areas = Areas::FindPortAreas();
	
		foreach ($areas as $area)
		{
			// ! Get Sections
			if($area->id == 2) // Orphan Area
			{
				$orphaned_items = Items::FindOrphans();
				if (count($orphaned_items) > 0) 
				{ 
					// If there are orphans, than list them here
echo <<<EOT
			<li>
				<ul class="row">
					<li class="c_cat">
						<a href="orphans">Portfolio Orphans</a>
						<input class="portfolio_order" type="hidden" title="PortFolioAreas_0" name="PortFolioAreas_0" value="0" />
					</li>
					<ul class="row cat_drag" id="$area->display_name">
					
						<ul class="row">
							<li class="c_group">
								<a href="orphan_section">Orphan Section</a>
								<div class="item_drag">
EOT;
					foreach ( $orphaned_items as $item )
					{
						$itemlink = get_link("/admin/portfolio_edit/orphan_section/".$item->id); 
						if( PORTFOLIOTHUMB_IMAGE ) {
							$imagelink = "<img src=\"".get_link("/portfolio/thumbnail/" . $item->id)."\" />"; 
						} else {
							$imagelink = " "; 
						}
						$itempublic = ( $item->public ) ? "" : "<span class=\"red\">(not public)</span>";
						$inputname = "0_".$item->id; 
					
echo <<<EOT

									<ul class="row">
										<li class="item_line">
											<a href="$itemlink">$imagelink{$item->display_name}<small>Edit</small></a> $itempublic
										</li>
									</ul>
EOT;
					} 
					echo "\t\t\t\t\t\t\t</div>
							</li>
						</ul>
					</ul>
				</ul>
			</li>\n"; 
				}
				
			} else {
				
				$arealink = get_link("/admin/portfolio_edit_area/".$area->id); 
				$areaprivate = ( $area->public ) ? "" : "<span class=\"red\">(not public)</span>"; 
			
echo <<<EOT
			<li>
				<ul class="row">
					<li class="c_cat">
						<a href="$arealink" class="item-link">$area->display_name<small>Edit</small></a>
						<span class="item-public">$areaprivate</span>
						<input class="portfolio_order" type="hidden" title="PortFolioAreas_$area->id" name="PortFolioAreas_$area->id" value="$area->display_order" />
					</li>
					<ul class="row cat_drag" id="$area->display_name">
EOT;

				$sections = $area->find_linked("sections", "(sections.public=1 OR sections.public=0)", "sections.display_order ASC");
				foreach( $sections as $section )
				{
					$sectionlink = get_link("/admin/portfolio_edit_section/".$section->id); 
					$sectionpublic = ( $section->public ) ? "" : " <span class=\"red\">(not public)</span>"; 
				
echo <<<EOT

						<ul class="row">
							<li class="c_group">
								<!-- Don't reorder these elements -->
								<input class="section_order" type="hidden" title="SectionOrder_$section->id" name="SectionOrder_$section->id" value="$section->display_order" />
EOT;
					$items = $section->findItems( true );
					$itemnum = count( $items );
					if ( $itemnum > 6 ) {
						echo "\t\t\t\t<span class=\"item-link\"><a href=\"$sectionlink\">$section->display_name</a> <a href=\"#{$section->name}_{$section->id}\" class=\"drop_item_link\">[view $itemnum items]</a></span>
								<span class=\"item-public\">$sectionpublic</span>\n";
						echo "\t\t\t\t<div id=\"{$section->name}_{$section->id}\" class=\"item_drag\" style=\"display: none;\">\n";	
					} else {
						echo "\t\t\t\t<a class=\"item-link\" href=\"$sectionlink\">$section->display_name<small>Edit</small></a><span class=\"item-public\">$sectionpublic</span>\n";
						echo "\t\t\t\t<div class=\"item_drag\">\n";
					}
					
					foreach ( $items as $item )
					{
						$itemlink = get_link("/admin/portfolio_edit/".$section->name."/".$item->id); 
						if( PORTFOLIOTHUMB_IMAGE ) {
							$imagelink = "<img src=\"".get_link("/portfolio/thumbnail/" . $item->id)."\" /> "; 
						} else {
							$imagelink = " "; 
						}
						$itempublic = ( $item->public ) ? "" : "<span class=\"red\">(not public)</span>";
						$inputname = $section->id."_".$item->id; 
						$item_revised = ( empty( $item->date_revised ) ) ? "" : formatDateTimeView( $item->date_revised ); 
						$item_created = ( empty( $item->date_created ) ) ? "" : formatDateTimeView( $item->date_created ); 
						$itemorder = $item->getOrderInSection($section); 
echo <<<EOT

									<ul class="row">
										<li class="item_line">
											<input class="item_order" type="hidden" title="ItemOrder_$item->id" name="$inputname" value="$itemorder" />
											<a class="item-link" href="$itemlink">$imagelink{$item->display_name}<small>Edit</small></a>
											<span class="item-public">$itempublic</span>
											<span class="item-revised">$item_revised</span>
											<span class="item-created">$item_created</span>
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
				echo "\t\t</li>\n";
			} // end else on orphans
		}
?>

		</ul>
	</form>
	
	<script type="text/javascript">
		$().ready(function() {
			$(".drop_item_link").click(function() {
				
				var div_iden = $(this).attr('href');
				//console.log( 'div identity = ' + div_iden );
				
				$( div_iden ).slideToggle();
				if ( $(this).text() == "[view items]" ) {
					$(this).text("[hide items]");
				} if ( $(this).text() == "[hide items]" ) {
				    $(this).text("[view items]");
				} else {
					$(this).text("[hide items]");
				}
			});
			$("ul.cat_drag, div.item_drag").sortable({
				stop: function() {
					var count = 1;
					$("ul#listitems ul.cat_drag li input.section_order").each(function() {
						$(this).val(count);
						count++;
					});
					var count = 1;
					$("ul#listitems ul.cat_drag li input.item_order").each(function() {
						$(this).val(count);
						count++;
					});
					$.post("<?php echo get_link( "admin/order_parse/portfolio" ); ?>", $("form#order_item").serialize());
				}
			});
		});
	</script>
	
<?php 
		} 
	} // end display_content()
?>
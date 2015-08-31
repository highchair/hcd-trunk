	<!-- Locate this section options -->
	<ul id="portfolio_area" class="tabs menu">
		<li><a href="#portfolio_areas" class="openclose opened">Edit Areas this Section is in</a></li>
	</ul>
	
	<div id="portfolio_areas" class="dropslide">
		<h2><legend>Select a Portfolio Area to include this Section in:</legend></h2>
		<fieldset>
			<p>
			<?php
				foreach( $areas as $area ) {
					$checked = "";
					$area_label = "{$area->display_name}";
					$area_name_to_use = $area->display_name;
					
					if ( isset($page_areas) )
					{
						foreach( $page_areas as $page_area )
						{
							$checked = ( $page_area->id == $area->id ) ? "checked='checked'" : ""; 
						}
					}
					if ( $area->id != 2 ) {
						echo "<label for=\"{$area_name_to_use}\">{$area_label}&nbsp;";
						echo "<input type=\"checkbox\" id=\"selected_areas[]\" name=\"selected_areas[]\" value=\"{$area->id}\" {$checked} /></label>\n"; 
					}
				}
			?>
		
			</p>
		</fieldset>
	</div>
	<!-- End Locate page Options -->

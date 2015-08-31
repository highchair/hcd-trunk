		<script type="text/javascript">
			$().ready(function() {
				$("#edit_page").validate({
					rules: {
							"selected_areas[]": "required"
						},
					messages: {
							"selected_areas[]": "Almost forgot! Select at least one area to include the page in" 
						}
				});
			});
		</script>
		<!-- Locate this page options -->
		<h2>Areas: Location in the Layout</h2>
		<p>Choose an area to assign this page to. More than one area may be selected.</p>
		
		<h3><legend>Select an Area:</legend></h3>
		<fieldset>
			<p><b>Choose an Area(s) for this page to be included in.</b></p>
			<p>
<?php
	foreach( $areas as $area )
	{
		// Check that this is not the Portfolio_Orphans area...
		if( $area->id != "2" )
		{
			$checked = $labelchecked = "";
			if ( $area->id == "1" )
			{
				$area_name_to_use = "Global"; 
				$area_label = "Global (home)";
			} else {
				$area_name_to_use = $area->display_name;
				$area_label = $area->display_name;
			}
			
			if ( isset($page_areas) )
			{
				foreach( $page_areas as $page_area )
				{
					// Removed this for PCF After School... nothing was coming up checked!
					// if ( $page_area->id == $area->id && $page->parent_page_id == null )
					if ( $page_area->id == $area->id )
					{
						$checked = "checked=\"checked\"";
						$labelchecked = " class='selected'"; 
					}
				}
			}
			echo "<label for=\"{$area_name_to_use}\"$labelchecked>{$area_label}&nbsp;<input type=\"checkbox\" id=\"selected_areas[]\" name=\"selected_areas[]\" value=\"{$area->id}\" {$checked} /></label>\n"; 
		}
	}
?>
		
			</p>
		</fieldset>
		
		<p><span class="hint">Any page can be in more than one Area. If a page is inserted into the &ldquo;Global&rdquo; Area by, a link to it may not appear in the navigation &ndash; depending on how your front-end design is set up. </span></p>
		<!-- End Locate page Options -->

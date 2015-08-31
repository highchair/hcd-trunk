<!-- Locate this page options -->
<h2>Areas/Subpages: Location in the Layout</h2>
<p>There are <strong>two options</strong> for locating this page in the database. You can either directly assign this page to one &ndash; or more &ndash; areas, or assign it as a &ldquo;sub page&rdquo; of another existing page. In the latter case, it will appear where ever it&rsquo;s &ldquo;parent&rdquo; appears. <br />
<strong>NOTE:</strong> Pages can either be in a Area or be assigned as a Sub Page, not both. If both of these options are selected the website will default to the selected Area and the selected Sub Page will be ignored. </p>

<table cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td valign="top">
				
				<div id="areas_selection">
					<h2><legend>Select an Area:</legend></h2>
						<!-- We removed the validation for this one, as we also have to consider the sub-pages -->
						<p><b>Choose an Area(s) for this page to be included in.</b></p>
						<p class="lineheight">
						<?php
							foreach($areas as $area)
							{
								// Hide the Portfolio Orphans area
								if ($area->id != 2)
								{
									$checked = "";
									$area_label = "{$area->display_name}";
									$area_name_to_use = $area->display_name;
									
									if (isset($page_areas))
									{
										foreach($page_areas as $page_area)
										{
											if ($page_area->id == $area->id && $page->parent_page_id == null)
											{
												$checked = "checked=\"checked\"";
											}
										}
									}	
									echo "<label for=\"{$area_name_to_use}\">{$area_label}&nbsp;";
									echo "<input type=\"checkbox\" id=\"selected_areas[]\" name=\"selected_areas[]\" value=\"{$area->id}\" {$checked} /></label>\n"; 
								}
							}
						?>
					
						</p>
					
					<p><span class="hint">Any page can be in more than one Area. If no areas are selected, this page will be inserted into the &ldquo;Global&rdquo; Area by default, and a link to it may not appear in the Global navigation &ndash; depending on how your front-end design is set up. </span></p>
				</div>
				
			</td>
			<td valign="middle"><h2>&nbsp;&lt;OR&gt;&nbsp;</h2></td>
			<td valign="top">
				<div id="parent_page_selection"> 
					<h2>Make this Page a Child of Another Page</h2>
					<p>This means that <b>this page</b> that you are editing will become the &ldquo;child&rdquo; of another page. Basically, we are creating a sub-page, and any page can have more than one sub-page. </p>
					<p><label for="parent_page[]">Select a Parent for this page:</label><br />
					
						<select id="id_parent_page" name="parent_page">
							<option value="">(none)</option>
						<?php
							function add_child_pages($page, $prefix = "&nbsp;&nbsp;&nbsp;", $skip_id = 0)
							{
								if($page->id != $skip_id)
								{
									$prefix .= "&nbsp;&nbsp;&nbsp;";
									if(!$page)
									{
										return;
									}
									$children = $page->get_children();
									foreach($children as $child)
									{
										echo "<option value=\"{$child->id}\">{$prefix}{$child->display_name}</option>";
									}
								}
							}
							
							$num_areas = count($areas);
							foreach($areas as $current_area)
							{
								$thisAreaName = $current_area->display_name;
								$thisShortName = $current_area->name;
		
								echo "\n\t\t<option value=\"area\" class=\"disabled\"><b>$thisAreaName</b></option>";
		
								$pages = $current_area->findPages(true, false);
								$num_pages = count($pages);
								
								foreach($pages as $current_page)
								{
									if($page->id != $current_page->id)
									{
										$selected = "";
										if($page->parent_page_id == $current_page->id)
										{
											$selected = "selected='selected' ";
										} 
										echo "\t\t<option {$selected} value=\"{$current_page->id}\">&nbsp;&nbsp;&nbsp;{$current_page->display_name}</option>\n";
										add_child_pages($current_page, "&nbsp;&nbsp;&nbsp;", $current_page->id);
									}
								}
							}
						?>	
						</select></p>
					
					<?php 
						function list_child_pages($parent) 
						{ 
							if(!$parent)
							{
								return;
							}
							$children = $parent->get_children();
							foreach($children as $child)
							{
								echo "<li><a href='".get_link("/admin/edit_page/{$child->id}")."'>{$child->display_name}</a></li>";
							}
						}
						
						if (isset($page))
						{
							$pagechildren = list_child_pages($page);
							if ($pagechildren)
							{
					?>
				
					<p><b>This page has children:</b></p>
					<ul style="list-style-type: none;">
						<?php echo $pagechildren; ?>
					
					</ul>
				<?php
						} 
					}
				?>
					
				</div>
			</td>
		</tr>
	</tbody>
</table>
<!-- End Locate page Options -->
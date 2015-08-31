		<p><label for="taxonomy">Taxonomy (optional):</label><span class="hint">An additional means of sorting items by type.</span><br />
			<select name="taxonomy" id="taxonomy">
			<?php
				$item_id = getRequestVaratIndex(3);
				$item = Items::FindById($item_id);
				
				$taxonomies =  array(); 
				if ( ! empty($taxonomies) ) {
					foreach( $taxonomies as $key => $value )
					{
						$selected = ( $item->taxonomy == $key ) ? ' selected' : ''; 
						echo "<option value=\"$key\"$selected> $value </option>\r\n";
					}
				} else {
					echo "<option value=\"\">No Taxonomy Terms defined!</option>\r\n";
				}
			?>
			
		</select></p>
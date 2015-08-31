<!-- Insert an Item Gallery function -->
<div id="itemgal_insert" class="dropslide" style="display: none; ">
	<p><span class="hint">Click the name of the Item Gallery to insert the link to it in the edit window above. </span> The system will insert the default gallery link: }}gallery:name-of-gallery}}. The direction of the braces &ldquo;design&rdquo; the display &ndash; pointing left, the thumbnail floats left; pointing right, the thumbnail floats right; and centered, the thumbnail displays large in the content column. Other allowed values and patterns are: {{carousel:name-of-gallery{{ or {{random-from-gallery:name-of-gallery{{ </p>
<?php 
		$itemgals = Galleries::FindAll( "DESC", false ); 
		if( count($itemgals) > 0 ) {
?>
	
	<table class="dropdown" cellpadding="0" cellspacing="0" border="0" width="98%">
		<tbody>
			<tr>
<?php 
		$counter_gal = 1; 
		foreach( $itemgals as $gallery ) {

			if ( $counter_gal == 4 ) {
				echo "\t\t\t</tr><tr>\n"; 
				$counter_gal = 1; 
			}
			$thumb = $gallery->get_thumb();
			if( $thumb ) {
?>
			
				<td class="gallerythumb divider" valign="top"><a href="#" onclick="insertDocument('gallery:<?php echo $gallery->slug ?>', '}}', '}}'); return false;"><img src="<?php echo $thumb->getPublicUrl() ?>"> <?php echo $gallery->name ?></a></td>
<?php
			}
			$counter_gal++; 
		} 
?>

			</tr>
		</tbody>
	</table>
<?php
	} else {
		echo "<h3>No items have been created yet!</h3>\n"; 
	}
?>

</div>
<!-- End Insert Gallery function -->
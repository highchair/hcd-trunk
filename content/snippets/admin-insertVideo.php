<!-- Insert a Video function -->
<div id="video_insert" class="dropslide" style="display: none; ">
	<p><span class="hint">Click the name of the Video to insert the embed code for it in the edit window above. The window above might lose the insertion point, so be sure your cursor is where you want it first. </span></p>

<?php 
	$videos = Videos::FindAll();
	
	if( isset($videos) and count($videos) > 0) {
?>

	<table class="dropdown" cellpadding="0" cellspacing="0" border="0" width="98%">
		<thead>
			<th width="25%">Video Name</th>
			<th width="25%">Embed Name</th>
			<th width="25%">Video Name</th>
			<th width="25%">Embed Name</th>
		</thead>
		<tbody>
			<tr>
<?php 
		$counter_doc = 1; 
		foreach($videos as $video) {
			
			if ($counter_doc == 3) {
				echo "\t\t\t</tr><tr>\n"; 
				$counter_doc = 1; 
			}
?>
			
				<td><b><a href="#" onclick="insertDocument('video:<?php echo $video->slug?>', '{{', '{{'); return false;"><?php echo $video->get_title() ?></a></b></td>
				<td class="divider"><?php echo $video->slug ?></td>
<?php
			$counter_doc++; 
		} 
?>

			</tr>
		</tbody>
	</table>
<?php 
	} else {
		echo "<h3>No videos have been created yet!</h3>\n"; 
	}
?>
</div>
<!-- End Insert Video function -->
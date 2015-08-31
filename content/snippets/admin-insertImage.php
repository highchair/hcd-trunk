<!-- Insert an Image functions -->
<div id="img_insert" class="dropslide" style="display: none; ">
	<script type="text/javascript">
		$().ready(function() {
			
			function loadImage(e) {
				var id = $(this).attr('name');
				$('#box_pop').css({
					opacity : 0,
					left : e.pageX+100,
					top : e.pageY-60
				}).load("<?php echo get_link( "images/preview/" ) ?>" + id, function() { 
					$(this).animate(
						{
						opacity : 1
						}, 500
					)
				});
			}
			function clearImage() {
				$('#box_pop').css({
					opacity : 0,
					left: '-9999em'
				});
			}
			$('#images a.preview').hoverIntent(loadImage,clearImage);
		});
	</script>
	
	<p><span class="hint">Roll over the name of the image for a preview :: "Left" will insert the code to display the image and float it left :: "Center" will insert the image without a float, which means that text will not flow around it :: and "Right" will insert the image and float it right.</span></p>
	<div id="box_pop"></div>
<?php 
		if(count($images) > 0)
		{
?>

	<table id="images" class="dropdown" cellpadding="0" cellspacing="0" border="0" width="98%">
		<thead>
			<th width="23%">File Name</th>
			<th colspan="3">Design Options</th>
			<th width="23%">File Name</th>
			<th colspan="3">Design Options</th>
			<th width="23%">File Name</th>
			<th colspan="3">Design Options</th>
		</thead>
		<tbody>
			<tr>
<?php
			$counter_img = 1; 
			foreach($images as $image)
			{ 
				if ($counter_img == 4)
				{
					echo "\t\t\t</tr><tr>\n"; 
					$counter_img = 1; 
				}
?>
		
				<td><a class="preview"  href="#" name="<?php echo $image->id ?>" class="image_reference"><?php echo $image->name ?></a></td>
				<td><a href="#" onclick="insertDocument('<?php echo $image->name ?>', '{{', '{{'); return false;"><img src="<?php echo BASEHREF ?>lib/admin_images/float-left.png" alt="float left" title="float left" /></a></td>
				<td><a href="#" onclick="insertDocument('<?php echo $image->name ?>', '{{', '}}'); return false;"><img src="<?php echo BASEHREF ?>lib/admin_images/float-none.png" alt="float none" title="float none" /></a></td>
				<td style="border-right: 1px solid #666;"><a href="#" onclick="insertDocument('<?php echo $image->name ?>', '}}', '}}'); return false;"><img src="<?php echo BASEHREF ?>lib/admin_images/float-right.png" alt="float right" title="float right" /></a></td>
<?php
				$counter_img++; 
			} 
?>
			</tr>
		</tbody>
	</table>
<?php
		} else {
			echo "<h3>No images have been uploaded yet!</h3>\n"; 
		}
?>

</div>
<!-- End Insert Image function -->
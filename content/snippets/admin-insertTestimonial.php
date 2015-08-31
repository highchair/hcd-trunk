<!-- Insert a Testimonial -->
<div id="test_insert" class="dropslide" style="display: none; ">
	<p><span class="hint">Click the icons to insert the testimonial code for it in the edit window above. Be sure your cursor is where you want it first. </span></p>

<?php 
	$testimonials = Testimonials::FindAll();
	
	if( is_array($testimonials) and count($testimonials) > 0) {
?>

	<table id="images" class="dropdown" cellpadding="0" cellspacing="0" border="0" width="98%">
		<thead>
			<th>Name</th>
			<th>Content Snippet</th>
			<th colspan="3">Design Options</th>
		</thead>
		<tbody>
<?php
			foreach ( $testimonials as $test ) { 
?>
		    <tr>
				<td><strong><a href="#" onclick="insertDocument('testimonial:<?php echo $test->slug ?>', '{{', '}}'); return false;"><?php $test->the_title() ?></strong></a></td>
				<td><?php $test->the_excerpt( 72, " " ); ?>
				<td><a href="#" onclick="insertDocument('testimonial:<?php echo $test->slug ?>', '{{', '{{'); return false;"><img src="<?php echo BASEHREF ?>lib/admin_images/float-left.png" alt="float left" title="float left" /></a></td>
				<td><a href="#" onclick="insertDocument('testimonial:<?php echo $test->slug ?>', '{{', '}}'); return false;"><img src="<?php echo BASEHREF ?>lib/admin_images/float-none.png" alt="float none" title="float none" /></a></td>
				<td><a href="#" onclick="insertDocument('testimonial:<?php echo $test->slug ?>', '}}', '}}'); return false;"><img src="<?php echo BASEHREF ?>lib/admin_images/float-right.png" alt="float right" title="float right" /></a></td>
            </tr>
<?php
			} 
?>
			</tr>
		</tbody>
	</table>
<?php 
	} else {
		echo "<h3>No testimonials have been created yet!</h3>\n"; 
	}
?>
</div>
<!-- End Insert Testimonial function -->
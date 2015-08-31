<?php
	function initialize_page()
	{
		// This file does both, so check the parameters first
		if ( requestIdParam() == "add" ) {
    		$testimonial = MyActiveRecord::Create( 'Testimonials' );
		} else {
    		$testimonial_id = requestIdParam();
    		$testimonial = Testimonials::FindById( $testimonial_id );
		}
		
		$post_action = "";
		if( isset($_POST['submit']) )
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Save Testimonial" || $post_action == "Save and Return to List" )
		{
			if(isset($_POST['delete'])) {
				$testimonial->delete();
				
				setFlash("<h3>Testimonial deleted</h3>");
			
			} else {
			    
			    /*
        		 * Columns: id, display_name, slug, content, attribution
        		 */
			    $postedtitle = $_POST['display_name']; 
			    
			    $testimonial->slug = slug( $postedtitle );
			    $testimonial->display_name = $postedtitle;
			    $testimonial->content = $_POST['content'];
			    $testimonial->attribution = $_POST['attribution'];
			    $testimonial->is_featured = checkboxValue( $_POST,'featured' );
			    
			    $testimonial->save();
                
                $success = 'Testimonial changes saved / '; 
                
                setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
			}
			
			if( isset($_POST['delete']) or $post_action == "Save and Return to List" ) { redirect( "admin/list_testimonials" ); }
		}
	}
	
	function display_page_content()
	{
		// Double check that the proper columns exist
		$add_testimonial = ( requestIdParam() == "add" ) ? true : false; 
		
		if ( $add_testimonial ) {
    		$testimonial = $testimonialtitle = $testimonialcontent = $testimonialatt = $testimonialfeat = null;
		} else {
    		$testimonial_id = requestIdParam();
    		$testimonial = Testimonials::FindById( $testimonial_id );
    		$testimonialtitle = $testimonial->get_title(); 
    		$testimonialcontent = $testimonial->content; 
    		$testimonialatt = $testimonial->attribution; 
    		$testimonialfeat = $testimonial->is_featured; 
		}
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#edit_testimonial").validate({
				rules : {
					title: "required" 
				},
				messages: {
					title: "Please enter a title for this testimonial" 
				}
			});
		});
	</script>
	
	<div id="edit-header" class="testimonialnav">
		<h1><?php if ( $add_testimonial ) { echo 'Add'; } else { echo 'Edit'; } ?> Testimonial</h1>
	</div>
	
	<form method="POST" id="edit_testimonial">
		
		<p class="display_name">
			<label for="title">Testimonial Display Name:</label>
			<?php textField( "display_name", $testimonialtitle, "required: true" ); ?><br />
			<span class="hint">This name will be used in the admin only &mdash; it will not display on the front-end</span>
		</p>
		
		<p>
			<label for="content">Testimonial Content:</label> <span class="hint">Quotes will get added when displayed, so please do not add quotes here.</span><br />
			<?php textArea("content", $testimonialcontent, 98, EDIT_WINDOW_HEIGHT/2 ); ?>
		</p>
		
		<p>
			<label for="attribution">Testimonial attribution:</label>
			<?php textField( "attribution", $testimonialatt ); ?><br />
			<span class="hint">Optional. A &ldquo;credit&rdquo; for the testimonial quote.</span>
		</p>
        
        <p>
            <label for="featured">
                <input type="checkbox" name="featured" id="featured"<?php if ($testimonialfeat) { ?> checked="checked"<?php } ?>>
                Feature this Testimonial
            </label>
            <span class="hint">Optionally show this testimonial in special places as dictated by the site design.</span>
        </p>
        
		<div id="edit-footer" class="testimonialnav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Testimonial" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
<?php 
	$user = Users::GetCurrentUser();
	if( $user->has_role() && requestIdParam() != "add" ) { ?>
	
				<p><label for="delete">Delete this testimonial? <input name="delete" id="delete" class="boxes" type="checkbox" value="<?php echo $testimonial->id ?>" /></label>
					
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; to delete this testimonial from the database. Warning: if this testimonial is removed but still being used in content, an error will be displayed.</span>
				</p>
<?php } ?>
			</div>
		</div>
	
	</form>
<?php } ?>
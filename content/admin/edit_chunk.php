<?php
	function initialize_page()
	{
		// This file does both, so check the parameters first
		if ( requestIdParam() == "add" ) {
    		$chunk = MyActiveRecord::Create( 'Chunks' );
		} else {
    		$chunk_id = requestIdParam();
    		$chunk = Chunks::FindById( $chunk_id );
		}
		
		$post_action = "";
		if( isset($_POST['submit']) )
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Save Chunk" || $post_action == "Save and Return to List" )
		{
			if(isset($_POST['delete']))
			{
				$chunk->delete(true);
				
				setFlash("<h3>Chunk deleted</h3>");
				redirect("/admin/list_pages");
			}
			else
			{
			    /*
        		 * Columns: id, slug, full_html(boolean), content
        		 */
                if ( ! empty( $_POST['slug'] ) )
    			    $chunk->slug = slug( $_POST['slug'] );
			    if ( ! empty( $_POST['description'] ) )
    			    $chunk->description = $_POST['description'];
			    if ( ! empty( $_POST['description'] ) )
    			    $chunk->full_html = checkboxValue($_POST,'full_html');
			    $chunk->content = $_POST['chunk_content']; 
			    
			    $chunk->save();
			
				setFlash("<h3>Chunk changes saved</h3>");
				
				if( $post_action == "Save and Return to List" ) {
					redirect( "admin/list_pages" ); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		if ( requestIdParam() == "add" ) {
    		$chunk = $chunkslug = $chunkdescription = $chunkcontent = null;
    		$chunkhtml = 0; 
		} else {
    		$chunk_id = requestIdParam();
    		$chunk = Chunks::FindById( $chunk_id );
    		$chunkslug = $chunk->slug; 
    		$chunkdescription = $chunk->description; 
    		$chunkhtml = $chunk->full_html; 
    		$chunkcontent = $chunk->content; 
		}
		$user = Users::GetCurrentUser();
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#edit_chunk").validate({
				rules : {
					slug: "required", 
					content: "required"
				},
				messages: {
					slug: "Please enter a title for this video", 
					content: "Please enter some content for this chunk"
				}
			});
			<?php if ( $chunkhtml ) { echo 'loadTinyMce("chunk_content");'; } ?>
			
		});
	</script>
	
	<div id="edit-header" class="chunknav">
		<h1>Edit Chunk</h1>
	</div>
	
	<form method="POST" id="edit_chunk">
		<div class="column half">
    		
<?php if( $user->email == 'admin@highchairdesign.com' ) { ?>
    		
    		<p class="display_name">
    			<label for="slug">Chunk Slug:</label>
    			<?php textField( "slug", $chunkslug, "required: true" ); ?>
    		</p>
    		
    		<p>
    			<label for="full_html">Full Html?:</label>&nbsp; <?php checkBoxField( "full_html", $chunkhtml ); ?> <br>
    			<span class="hint">Do we need to allow full HTML capabilities on the content field? Checked for &ldquo;Yes&rdquo;, not checked for &ldquo;No&rdquo;.</span>
    		</p>
    		
    		<p>
    			<label for="description">Chunk Description:</label>
    			<?php textField( "description", $chunkdescription ); ?>
    		</p>

<?php } else { ?>

            <h2><?php echo $chunkslug ?></h2>
    		
    		<p><strong><?php echo $chunkdescription ?></strong></p>

<?php } ?>

    		<div class="clearit"></div>
		</div>
		<div class="column half last">
    		
    		<p>
    			<label for="chunk_content">Content:</label>
    			<?php textArea("chunk_content", $chunkcontent, 98, 18); ?>
    		</p>
    		
		</div>
		<div class="clearleft"></div>
		
		
		<div id="edit-footer" class="chunknav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Chunk" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
<?php if( $user->email == 'admin@highchairdesign.com' ) { ?>
	
				<p><label for="delete">Delete this chunk?</label>
					<input name="delete" class="boxes" type="checkbox" value="<?php echo $chunk->id ?>" />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this chunk from the database</span>
				</p>
<?php } ?>
			</div>
		</div>
	
	</form>
<?php } ?>
<?php
	function initialize_page()
	{
		// This file does both, so check the parameters first
		if ( requestIdParam() == "add" ) {
    		$video = MyActiveRecord::Create( 'Videos' );
		} else {
    		$video_id = requestIdParam();
    		$video = Videos::FindById( $video_id );
		}
		
		$post_action = "";
		if( isset($_POST['submit']) )
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Save Video" || $post_action == "Save and Return to List" )
		{
			if(isset($_POST['delete']))
			{
				$photo = Photos::FindVideoPoster( $video->id ); 
        		if ( is_object($photo) ) $photo->delete(true); 
				
				$video->delete();
				
				setFlash("<h3>Video deleted</h3>");
				redirect("/admin/list_videos");
			}
			else
			{
			    /*
        		 * Columns: id, name, title, service (youtube, vimeo), embed (shortcode or unique ID), gallery_id, display_order
        		 */
			    $postedtitle = $_POST['title']; 
			    
			    $video->name = slug( $postedtitle );
			    $video->display_name = $postedtitle;
			    $video->service = $_POST['service'];
			    $video->embed = $_POST['embed'];
			    $video->width = $_POST['width'];
			    $video->height = $_POST['height'];
			    
			    // Why does the save() method fail on new objects? Is it because Videos extend Modelbase and not MyActiveRecord?
			    //$video->save();
                if ( requestIdParam() == "add" ) {
                    // id, slug, display_name, service (youtube, vimeo), embed, width, height, gallery_id, display_order
                    $query = "INSERT INTO `videos` VALUES('','$video->name','$video->display_name','$video->service', '$video->embed', '$video->width', '$video->height', '', '')";
        			if ( mysql_query($query, MyActiveRecord::Connection()) ) {
        			    $success = 'New video added / ';
        			} else {
            			die( 'Die:<br>'.print_r($query) );
        			}
                    
                    // This is a safer way to do it (we don't rely on the order of columns not to change: 
                    /*$newvideo = MyActiveRecord::Create( 'Videos', array( 
                        'name' => $video->name,
                        'display_name' => $video->display_name,
                        'service' => $video->service,
                        'embed' => $video->embed,
                        'width' => $video->width,
                        'height' => $video->height,
                    ) );*/
                } else {
                    $video->save(); 
                    $success = 'Video changes saved / '; 
                }
                
                
                if( isset( $_FILES['new_poster'] ) && $_FILES['new_poster']['error'] == 0 ) {
					
					// First, delete an old file if there is one
					$oldphoto = Photos::FindVideoPoster( $video->id ); 
            		if ( is_object($oldphoto) ) $oldphoto->delete(true);   
					
					// New Photo needs to be created as a Photo object
					$newphoto = MyActiveRecord::Create( 'Photos', array( 'caption' => $video->display_name, 'video_id' => $video->id, 'display_order' => 1 ) ); 
					$newphoto->save();
					
					// save_uploaded_file($tmp_name, $file_name, $isportimg = false, $isentryimg = false, $maxwidth=0, $maxheight=0)
					$newphoto->save_uploaded_file( $_FILES['new_poster']['tmp_name'], $_FILES['new_poster']['name'], true ); 
					
					$success .= "New poster image uploaded / ";
				} else {
    				// from http://php.net/manual/en/features.file-upload.errors.php
    				$upload_errors = array( 
                        "0. UPLOAD_ERR_OK: No errors.", 
                        "1. UPLOAD_ERR_INI_SIZE: Larger than upload_max_filesize.", 
                        "2. UPLOAD_ERR_FORM_SIZE: Larger than form MAX_FILE_SIZE.", 
                        "3. UPLOAD_ERR_PARTIAL: Partial upload.", 
                        "4. UPLOAD_ERR_NO_FILE: No file.", 
                        "6. UPLOAD_ERR_NO_TMP_DIR: No temporary directory.", 
                        "7. UPLOAD_ERR_CANT_WRITE: Can't write to disk.", 
                        "8. UPLOAD_ERR_EXTENSION: File upload stopped by extension.", 
                        "UPLOAD_ERR_EMPTY: File is empty." // add this to avoid an offset.
                    ); 

                    $err_num = $_FILES['new_poster']['error']; 
                    if ( $err_num != 4 ) 
                        echo "Upload Error! ".$upload_errors[ $err_num ];
				}
                
				if ( requestIdParam() == "add" ) {
    				setFlash('<h3>' . $success . '<a href="'.get_link('admin/edit_entry/'.$video->id).'">Edit it Now</a></h3>');
                } else {
                    setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
                }
				
				/*if ( requestIdParam() == "add" ) {
    				redirect( "admin/edit_video/".$video->id ); 
                }*/
				
				if( $post_action == "Save and Return to List" ) {
					redirect( "admin/list_videos" ); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		// Double check that the proper columns exist
		$video_id = find_db_column( 'photos', 'video_id' );
		if ( ! $video_id ) {
    		echo '<h2 class="system-warning"><span>HCd&gt;CMS says:</span> The Photos table does not have a column called "video_id"</h2>'; 
        }
		
		$add_video = ( requestIdParam() == "add" ) ? true : false; 
		
		if ( $add_video ) {
    		$video = $videotitle = $videoservice = $videoembed = $videowidth = $videoheight = $videoposter = $attached_item = null;
		} else {
    		$video_id = requestIdParam();
    		$video = Videos::FindById( $video_id );
    		$videotitle = $video->get_title(); 
    		$videoservice = $video->service; 
    		$videoembed = $video->embed; 
    		$videowidth = $video->width; 
    		$videoheight = $video->height; 
    		if ( $video_id ) {
        		$possibleposter = Photos::FindVideoPoster( $video_id ); 
        		$videoposter = ( ! empty($possibleposter) ) ? $possibleposter : null; 
    		}
		}
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#edit_video").validate({
				rules : {
					title: "required", 
					embed: "required"
				},
				messages: {
					title: "Please enter a title for this video", 
					embed: "Please enter an embed code for this video"
				}
			});
		});
	</script>
	
	<div id="edit-header" class="videonav">
		<h1><?php if ( $add_video ) { echo 'Add'; } else { echo 'Edit'; } ?> Video</h1>
	</div>
	
	<form method="POST" id="edit_video" enctype="multipart/form-data">
		
		<p class="display_name">
			<label for="title">Video Display Name:</label>
			<?php textField( "title", $videotitle, "required: true" ); ?><br />
			<span class="hint">This name should match the name you use on the embedding service source (YouTube or Vimeo), but it does not have to.</span>
		</p>
		
		<div class="column half">
    		<p><label for="service">Hosting Service:</label>
    			<select id="service" name="service">
    				<option value="youtube"<?php if ( ! empty($video) ) { if ( $video->service == 'youtube' ) echo ' selected'; } ?>>YouTube</option>
    				<option value="vimeo"<?php if ( ! empty($video) ) { if ( $video->service == 'vimeo' ) echo ' selected'; } ?>>Vimeo</option>
    			</select><br />
    			<span class="hint">Only two are supported at this time &mdash; YouTube is the default service.</span>
    		</p>
    		<p>
    			<label for="embed">Unique ID:</label>
    			<?php textField( "embed", $videoembed, "required: true" ); ?><br />
    			<span class="hint">The unique identifier is a random string of numbers and letters associated with the file. <br />
    			YouTube example: http://www.youtube.com/embed/<mark>tVUCsnMK18E</mark> <br />
    			Vimeo example: http://player.vimeo.com/video/<mark>72632269</mark> <br />
    			In both cases, we are only interested in the text highlighted.</span>
    		</p>
		</div>
		
		<div class="column half last">
    		<div class="column half">
        		<p>
        		    <label for="width">Video Width:</label>
        		    <?php textField( "width", $videowidth ); ?>
        		</p>
    		</div>
    		<div class="column half last">
        		<p>
        		    <label for="height">Video Height:</label>
        		    <?php textField( "height", $videoheight ); ?>
        		</p>
    		</div>
    		<div class="clearit"></div>
    		<p class="hint">With responsive design, the width may be set to 100% by the templates, so that number may not always be used</p>
    		
    		<?php if ( $video_id ) { ?>
    		<!-- Video poster image -->
    		<p><label for="new_poster">Add/Edit a Poster image:</label>
				<input type="file" name="new_poster" id="new_poster" value="" />
			</p>
			<p class="hint">A poster image may be used by your site to display a link to a pop up video player. </p>
			<?php 
			    if ( ! is_null($videoposter) ) {
                    echo '<h3>Existing Poster Image</h3>'; 
                    echo '<p><img src="'.$videoposter->getPublicUrl().'" style="max-width:100%;" alt=""></p>'; 
			    }
			?>
			<?php } ?>
			
		</div>
		<div class="clearleft"></div>
		
<?php
    // Show an attached Item if there is one. 
    if ( is_object($video) ) {
        $attached_gallery = $video->getGallery();
        $attached_item = ( is_object($attached_gallery) ) ? $attached_gallery->get_item() : null; 
    }
    if ( is_object($attached_item) ) {
        $section = array_shift( $attached_item->getSections() ); 
        echo '<h2>This video is attached to this Portfolio Item:</h2>'; 
        echo '<ol id="video_list" class="managelist">'; 
        echo '<li><a href="'.get_link( "admin/portfolio_edit/".$section->name."/".$attached_item->id ).'">'.$attached_item->get_title().' <small>EDIT</small></a></li>'; 
        echo '</ol>'; 
    }
?>
		
		<div id="edit-footer" class="videonav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Video" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
<?php 
	$user = Users::GetCurrentUser();
	if( $user->has_role() && requestIdParam() != "add" ) { ?>
	
				<p><label for="delete">Delete this video?</label>
					<input name="delete" class="boxes" type="checkbox" value="<?php echo $video->id ?>" />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this video from the database</span>
				</p>
<?php } ?>
			</div>
		</div>
	
	</form>
<?php } ?>
<?php
	function initialize_page()
	{
		$post_action = $success = "";
		$gallery = Galleries::FindById( requestIdParam() );
		
		if( isset($_POST['submit']) ) {
			$post_action = $_POST['submit'];
		}
	
        if( 
            $post_action == "Edit Gallery" || 
            $post_action == "Edit and Return to List" || 
            $post_action == "Add Image to Gallery" ) {
			
			if( isset($_POST['delete']) ) {
				
				$photos = $gallery->get_photos(); 
				if ( count($photos) > 0 )
				    $success .= "Photos deleted / "; 
				foreach( $photos as $thephoto ) {			   
					$thephoto->delete(true);
				}
				$gallery->delete(true);
				$success .= "Gallery deleted / ";
				
				setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
				redirect("/admin/list_galleries");
			
			} else {
				
				// Name has changed. 
				if( $gallery->name != $_POST['name'] ) {
    				$gallery->name = $_POST['name'];
    				$gallery->slug = slug($_POST['name']);
    				$gallery->save();
    				$success .= "Gallery name saved / "; 
				}
				
				// Update captions if they are different. 
				if ( isset($_POST['captions']) ) {
					
					$captions = $_POST['captions'];
					foreach( $captions as $key => $thecaption ) {
						
						$photo = Photos::FindById($key);
						if ( $photo->caption != $thecaption ) {
							$photo->caption = $thecaption;
							$photo->save();
						}
					}
					//$success .= "Captions edited / "; 
				}
				
				// Reset the display order if the photos have been moved. 
				if ( isset($_POST['photos_display_order']) ) {
					
					$display_orders = $_POST['photos_display_order'];
					foreach( $display_orders as $key=>$display_order ) {
						
						$photo = Photos::FindById($key);
						if ( $photo->display_order != $display_order ) {
							$photo->display_order = $display_order;
							$photo->save();
						}
					}
					//$success .= "Photo order saved / "; 
				}
				
				// Upload and save a new file. 
				if( isset($_FILES['new_photo']) && $_FILES['new_photo']['error'] == 0 ) {
					
					// Updating the record to include the filename stopped working in photos > save_uploaded_file Jan 2013
					$photo = MyActiveRecord::Create( 'Photos', array( 'caption'=>getPostValue("new_photo_caption"), 'gallery_id'=>$gallery->id, 'display_order'=>1 ) );
					$photo->save();
					
					$photo->save_uploaded_file( $_FILES['new_photo']['tmp_name'], $_FILES['new_photo']['name'] );
					$photo->setDisplayOrder();
					
					$success .= "New photo added / "; 
				
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

                    $err_num = $_FILES['new_photo']['error']; 
                    if ( $err_num != 4 ) 
                        echo "Upload Error! ".$upload_errors[ $err_num ];
				} 
				
				// Delete photos that were checked off to be removed
				if( isset($_POST['deleted_photos']) ) {
					
					$deleted_ids = $_POST['deleted_photos'];
					foreach( $deleted_ids as $status => $photo_id ) {			   
						$photo = Photos::FindById( $photo_id );
						$photo->delete(true);
					}
					$success .= "Photo deleted / "; 
				}
				
				setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
				
				if( $post_action == "Edit and Return to List" ) {
					redirect( "admin/list_galleries" ); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		$gallery = Galleries::FindById(requestIdParam());
		$photos = $gallery->get_photos();
		
		$user = Users::GetCurrentUser();
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#edit_gallery").validate({
				errorLabelContainer: $("#error_container"),
				rules: {
					name: "required"
				},
				messages: {
					name: "Please enter a name for this gallery<br/>"
				}
			});
		});
		
		$().ready(function() {
			$("#photo_list_order").sortable({
				stop: function() {
					$("#photo_list_order li input[type=hidden]").each(function(i) {
						$(this).val(i+1);
					});
				}
			});
		});
	</script>
	
	<div id="edit-header" class="documentnav">
		<h1>Edit Gallery</h1>
	</div>

<?php 
    
    // DEBUG block. Run some tests. NOT PERFECT but should help point in the right direction. 
    if ( HCd_debug() ) {
        
        echo '<div class="debug-block">'; 
        
        // Check the folder path to see if it exists
        if ( is_dir(SERVER_DOCUMENTS_ROOT) ) {
            
            echo '<span class="debug-feedback passed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'&rdquo; exists</span>';
            $permmode = substr(sprintf('%o', fileperms(SERVER_DOCUMENTS_ROOT)), -4); 
            if ( $permmode == '0777' || $permmode == '0775' ) {
                echo '<span class="debug-feedback passed">The folder permission is <strong>'.$permmode.'</strong></span>'; 
            } else {
                echo '<span class="debug-feedback failed">The folder permission is <strong>'.$permmode.'</strong></span>'; 
            }
            
            if ( is_dir(SERVER_DOCUMENTS_ROOT."gallery_photos") ) {
                echo '<span class="debug-feedback passed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'gallery_photos&rdquo; exists</span>';
                $permmode2 = substr(sprintf('%o', fileperms(SERVER_DOCUMENTS_ROOT."gallery_photos")), -4); 
                if ( $permmode2 == '0777' || $permmode2 == '0775' ) {
                    echo '<span class="debug-feedback passed">The folder permission is <strong>'.$permmode2.'</strong></span>'; 
                } else {
                    echo '<span class="debug-feedback failed">The folder permission is <strong>'.$permmode2.'</strong></span>'; 
                } 
            } else {
                echo '<span class="debug-feedback failed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'gallery_photos&rdquo; does NOT exist or can not be seen</span>'; 
            }
        } else {
            echo '<span class="debug-feedback failed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'&rdquo; does NOT exist</span>'; 
        }
        
        $test_file = SERVER_INCLUDES_ROOT . "www/lib/admin_images/hcdlogo_24.png"; 
        if ( is_file($test_file) ) {
            $test_imagecreatefromjpeg = @imagecreatefrompng( $test_file ); 
            
            if ( $test_imagecreatefromjpeg !== false ) {
                echo '<span class="debug-feedback passed">&ldquo;@imagecreatefrompng&rdquo; works</span>'; 
            } else {
                echo '<span class="debug-feedback failed">&ldquo;@imagecreatefrompng&rdquo; returns false</span>'; 
            }
        } else {
            echo '<span class="debug-feedback failed">Test file does not exist. &ldquo;imagecreatefrompng&rdquo; test cannot run. </span>';
            echo '<span class="debug-feedback failed">Test file path: &ldquo;'.$test_file.'&rdquo;</span>'; 
        }
        
        echo '</div>'; 
    }

?>
	
	<form method="POST" id="edit_gallery" enctype="multipart/form-data">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		<a name="top"></a>
		
		<p class="display_name">
			<label for="name">Gallery Display Name:</label> <span class="hint">This is the Proper Name of the gallery.</span><br />
			<?php textField("name", $gallery->name, "required: true"); ?>
		</p>
		
		<p class="announce">
			<strong>Note:</strong> While the server will attempt to upload very large images and resize them, doing so may take a very long time. Please try to resize images to reasonable dimensions (about 800 pixels wide) before uploading. We recommend the Adobe Photoshop &ldquo;Save for Web&rdquo; feature, which makes the image file size very small (In Photoshop, under File > Save for Web. Use JPEG file type and a compression of about 60%. Click Progressive as well).<br />
		<br />
		The server will attempt to resize an image to a maximum dimension of: <strong><?php echo MAX_GALLERY_IMAGE_WIDTH ?> pixels wide by <?php echo MAX_GALLERY_IMAGE_HEIGHT ?> pixels tall</strong>. This is best for viewing images for the web. </p>
		<p>&nbsp;</p>
			
<!-- Start the Show/Hide for editing photos and changing order -->
		<div id="gallery-options" class="clearfix">
			
			<a name="editgal"></a>
			<ul id="gallery-options-nav" class="menu tabs">
				<li><a href="#gallery_sort" class="openclose opened">Change Photo Order</a></li>
				<li><a href="#add_edit_images" class="openclose">Add / Edit Photos and Captions</a></li>
			</ul>
		
			<div id="gallery_sort" class="dropslide">
				<h2>Click and Drag the image to change the order of the display in the Gallery</h2>
				<div id="sortable_container">
<?php
	if ( count($photos) > 0 )
	{
		echo "\t\t\t\t<ol id=\"photo_list_order\">\n";
		foreach($photos as $photo)
		{
			echo "\t\t\t\t\n\t<li><img class=\"changeorderThumb\" src=\"{$photo->getPublicUrl()}\" /><input type=\"hidden\" name=\"photos_display_order[" . $photo->id . "]\" value=\"" . $photo->display_order . "\" /></li>\n";
		}
		echo "\t\t\t\t</ol>\n"; 
	} else {
		echo "\n\n\n\n<h3>There are no photos to put in order. Go ahead and add some.</h3>\n"; 
	}
?>
								
				</div>
				<div class="clearleft"></div>
			</div>
			
			<div id="add_edit_images" class="dropslide" style="display: none;">
				<div id="add_image_wrap">
					<h2>Add an Additional Gallery Image</h2>
					<p><span class="hint">Images for your site would be best displayed at <?php echo MAX_GALLERY_IMAGE_WIDTH ?> pixels wide. If an image is larger, the system will attempt to resize it. Images that are too large might require more memory than the system can handle, and an error may result.</span></p>
					<p><label for="new_photo">Add an additional image:</label>
						<input type="file" name="new_photo" id="new_photo" value="" />
					</p>
					<p><label for="new_photo_caption">Caption for new image:</label>
						<?php textField("new_photo_caption", "", ""); ?>
					</p>
					<p><input type="submit" class="submitbutton" name="submit" value="Add Image to Gallery" /></p>
				</div>
				
				<p>&nbsp;</p>
				<h2>Edit Existing Gallery Photos (delete photos or edit captions. Click Save when done)</label></h2>
				<p><span class="hint"><strong>Image size:</strong> The CSS control for the images designates that they display here (for ease of use) at 50% their actual size. Dont&rsquo;t worry if they appear smaller than on the front-end.<!--[if IE 6]><br /><b>IE 6 Users: The CSS can only force the image to be 240 pixels wide, which for some images, may be larger than normal, resulting in pixellation. </b><![endif]--></span></p>				                                                                                                                                                  
	<?php
		if (count($photos) > 0)
		{
			echo "<ul id=\"add_photo_list\">\n"; 
			foreach($photos as $photo)
			{
				echo "<li><img src=\"{$photo->getPublicUrl()}\" />\n"; 
				echo "<div><input type=\"checkbox\" name=\"deleted_photos[]\" value=\"{$photo->id}\"/>&nbsp;DELETE</div>";
				textField("captions[{$photo->id}]", "{$photo->caption}", "");
				echo "</li>\n"; 
			}
			echo "</ul>\n"; 
		} else {
			echo "<h3>There are no photos to edit. Go ahead and add some.</h3>\n"; 
		}
	?>
								
				<p><a href="#top">Back to the Top of the Page</a></p>
			</div>
		
		</div>
<!-- End the Show/Hide -->
			
		
		<div id="edit-footer" class="gallerynav clearfix">
			<div id="error_container"></div>
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Edit Gallery" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Edit and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
				
			<?php if( $user->has_role() && ! in_array( $gallery->id, explode( ",", PROTECTED_ADMIN_GALLERIES ) ) ) { ?>
				
				<p><label for="delete">Delete this Gallery?</label>
				<input name="delete" class="boxes" type="checkbox" value="<?php echo $gallery->id ?>" />
				<span class="hint">Check the box and click &ldquo;Edit&rdquo; to delete from the database</span></p>
			<?php } else { ?>
			
			    <p>This gallery is being used by a template, so it can not be deleted. Please edit its name or contents only. </p>
			<?php } ?>
			
			</div>
		</div>
	
	</form>
<?php } ?>
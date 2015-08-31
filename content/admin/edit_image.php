<?php
	function initialize_page()
	{
		$image_id = requestIdParam();
		$image = Images::FindById($image_id);
		
		$post_action = ( isset($_POST['submit']) ) ? $_POST['submit'] : "";
	
		if( $post_action == "Save Image" || $post_action == "Save and Return to List" )
		{
			$success = ''; 
			
			if(isset($_POST['delete'])) {
				$image->delete(true);
				setFlash("<h3>Image deleted</h3>");
				redirect("/admin/list_images");
			
			} else {
				
				$old_name = $image->name;
			
				$image->title = cleanupSpecialChars($_POST['title']);
				$image->description = cleanupSpecialChars($_POST['description']);
				if ( ALLOW_SHORT_PAGE_NAMES ) {
    				$image->name = ($_POST['name'] == "") ? slug($_POST['title']) : slug($_POST['name']);
				} else {
					$image->name = slug($_POST['title']); 
				}
				//$image->save();
		
				$updateQuery = "UPDATE images SET title='{$image->title}', name='{$image->name}', description='{$image->description}' WHERE id='{$image->id}';";
                
                if ( mysql_Query($updateQuery, MyActiveRecord::Connection()) ) {
					if( $old_name != $image->name ) {
						Pages::UpdateImageReferences($old_name, $image->name);
					}
					$success .= "Image changes saved / ";
				
				} else {
				
				    die( $updateQuery );
					setFlash("<h3>FAILURE &ndash; Please notify HCd of this error: ".mysql_error()."</h3>");
				}
                
                
                // Replace an existing image with a new one
    			if ( is_uploaded_file($_FILES["new_image"]["tmp_name"]) ) {
    				
    				$mimeType = $_FILES["new_image"]["type"];
    				
    				$filetype = getFileExtension($_FILES["new_image"]["name"]);
    				//list($width) = getimagesize($_FILES["new_image"]["tmp_name"]);
    				$max_width = 0;
            		$max_height = 0;
    
            		if(defined("MAX_IMAGE_WIDTH")) {
            		    $max_width = MAX_IMAGE_WIDTH;
            		}
            		if(defined("MAX_IMAGE_HEIGHT")) {
            		    $max_height = MAX_IMAGE_HEIGHT;
            		}
            		resizeToMultipleMaxDimensions($_FILES["new_image"]["tmp_name"], $max_width, $max_height, $filetype);
    
    				// Open the uploaded file
    				$file = fopen($_FILES["new_image"]["tmp_name"], "r");
    				// Read in the uploaded file
    				$fileContents = fread($file, filesize($_FILES["new_image"]["tmp_name"])); 
    				// Escape special characters in the file
    				$fileContents = AddSlashes($fileContents);            
    				
    				$updateQuery2 = "UPDATE images SET original='{$fileContents}', mime_type='{$mimeType}' WHERE id='{$image->id}';";
                    
                    if ( mysql_Query($updateQuery2, MyActiveRecord::Connection()) ) {
    					$success .= "Image replaced / ";
    				} else {
    				    setFlash("FAILURE &ndash; Please notify HCd of this error: ".mysql_error()."</h3>");
    				    //die( $updateQuery2 );
    				}
    			}
			}
			if ( $post_action == "Save and Return to List" ) {
				redirect("/admin/list_images");
			} 
			setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>"); 
		}
	}
	
	function display_page_content()
	{
		$image_id = getRequestVarAtIndex( 2 ); 
		$image = Images::FindById($image_id);
		
		//$imagenum = $image->howManyReferencing(); 
?>

	<div id="edit-header" class="imagenav">
		<h1>Edit Image</h1>
	</div>
	
	<form method="POST" id="edit_image" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="8400000">
		
		<?php //<p><strong>Number of references: <?php echo $imagenum </strong></p> ?>
		
		<img src="<?php echo get_link( 'images/view/'.$image->id ) ?>" alt="<?php echo $image->get_title() ?>" />
		<p><span class="hint">The CSS will restrict the display width of this image to a max of 360 pixels wide. Don&rsquo;t worry if it appears smaller than normal. <!--[if IE 6]><br /><b>IE 6 Users: The CSS can only force the image to be 240 pixels high, which for some images, may be larger than normal, resulting in pixellation. </b><![endif]--></span></p>
		
		<!--<p>
			<label for="new_image">Select a new image to replace the current one:</label>
			<input type="file" name="new_image" id="new_image" value="" class="required: true" />
		</p>-->
		
		<p class="display_name">
			<label for="title">Title:</label>
			<?php textField("title", $image->get_title(), "required: true"); ?><br />
			<span class="hint">The Proper &ndash; or Pretty &ndash; name. Spaces, commas and quotes are acceptable. </span>
		</p>
		
		<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>
		<p>
			<label for="name">Short Name: </label> 
			<span class="hint">This will be used by the database only and in links. No spaces, commas or quotes please. </span><br />
			<?php textField("name", $image->name, ""); ?>
		</p>
		<?php } ?>
		
		<p>
			<label for="description">Caption:</label>
			<?php textField("description", esc_html($image->description), ""); ?><br />
			<span class="hint">A caption may or may not be used on the front-end display. This field can be left blank.</span>
		</p>
		
		<p><strong>A Trick:</strong> If the caption starts with &ldquo;http://&rdquo;, the system will treat the image as a link (called a &ldquo;hot-link&rdquo;). In this case, the title will become the text for the link, displayed under the image, and the image will be clickable (the cursor will turn into a hand when passed over the image). The image can still be placed to float right or left, or not float at all. </p>
		<p>If you are linking to your own page, use the full URL= <em>http://<?php echo SITE_URL ?>/thearea/thepage</em>. </p>
		
		
		<div id="edit-footer" class="imagenav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Image" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
			<?php 
				$user = Users::GetCurrentUser();
				if($user->has_role()) { 
			?>
				
				<p>
					<label for="delete">Delete this image?</label>
					<input name="delete" class="boxes" type="checkbox" value='<?php echo $image->id ?>' />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this image from the database</span>
				</p>
			<?php } ?>
			
			</div>
		</div>
	
    </form>
    
    <script type="text/javascript">
		$().ready(function() {
			$("#edit_image").validate({
				rules : {
					title: "required",
					image: "required"
				},
				messages: {
						title: "Please enter a title for this image<br/>",
						image: "Please select a file<br/>"
					}
			});
		});
	</script>
    
<?php } ?>

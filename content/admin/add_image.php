<?php

	function initialize_page() {
		
		$post_action = ( isset($_POST['submit']) ) ? $_POST['submit'] : "";
	
		if( $post_action == "Add Image" || $post_action == "Add and Return to List") {
			
			$title = cleanupSpecialChars($_POST['title']);
			$description = cleanupSpecialChars($_POST['description']);
			
			if ( ALLOW_SHORT_PAGE_NAMES ) {
				$name = ($_POST['name'] == "") ? slug($_POST['title']) : slug($_POST['name']);
			} else {
				$name = slug($_POST['title']); 
			}
		
			// Was a file uploaded?
			if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
				
				$mimeType = $_FILES["image"]["type"];
				
				$filetype = getFileExtension($_FILES["image"]["name"]);
				list($width) = getimagesize($_FILES["image"]["tmp_name"]);
				$max_width = 0;
        		$max_height = 0;

        		if(defined("MAX_IMAGE_HEIGHT")) {
        		    $max_height = MAX_IMAGE_HEIGHT;
        		}
        		if(defined("MAX_IMAGE_WIDTH")) {
        		    $max_width = MAX_IMAGE_WIDTH;
        		}
        		resizeToMultipleMaxDimensions($_FILES["image"]["tmp_name"], $max_width, $max_height, $filetype);

				// Open the uploaded file
				$file = fopen($_FILES["image"]["tmp_name"], "r");
				// Read in the uploaded file
				$fileContents = fread($file, filesize($_FILES["image"]["tmp_name"])); 
				// Escape special characters in the file
				$fileContents = AddSlashes($fileContents);            
					
				/*if( copy($_FILES["image"]["tmp_name"], $_FILES["image"]["tmp_name"] . "_thumb") ) {
					
					resizeToMultipleMaxDimensions($_FILES["image"]["tmp_name"] . "_thumb", 200, 0);
	
					$image = open_image($_FILES["image"]["tmp_name"] . "_thumb");
					if ( $image === false ) { die ('Unable to open image for resizing'); }
					$width = imagesx($image);
	
					// Open the thumbnail file
					$thumb_file = fopen($_FILES["image"]["tmp_name"] . "_thumb", "r");
					// Read in the thumbnail file
					$thumb_fileContents = fread($thumb_file, filesize($_FILES["image"]["tmp_name"] . "_thumb")); 
					// Escape special characters in the file
					$thumb_fileContents = AddSlashes($thumb_fileContents);
				}*/
				$thumb_fileContents = NULL; 
			
			} else { 
			    $fileContents = $thumb_fileContents = NULL; 
            }
			
			$insertQuery = "INSERT INTO images VALUES (NULL, \"{$title}\", \"{$description}\", \"{$fileContents}\", \"{$thumb_fileContents}\", \"{$mimeType}\", \"{$name}\")";

			$result = mysql_Query($insertQuery, MyActiveRecord::Connection() );
            
            if ( empty($result) ) {
				//die( $updateQuery );
				setFlash("<h3>FAILURE &ndash; Please notify HCd of this error: ".mysql_error()."</h3>");
			}
            
			setFlash("<h3>Image uploaded</h3>");
			if ( $post_action == "Add and Return to List" ) {
				redirect("/admin/list_images");
			}
		}
	}
	
	function display_page_content()
	{		
?>

	<div id="edit-header" class="imagenav">
		<h1>Add Image</h1>
	</div>
	
	<form method="POST" enctype="multipart/form-data" id="add_image">
		<input type="hidden" name="MAX_FILE_SIZE" value="8400000">

		<p class="announce"><b>A note about images:</b> Image files must be RGB color space, and GIF, JPG, PNG or BMP type. Images should also be cropped and resized before they are uploaded to the website. <strong>The max file size is 8mb</strong>.<br />
		For this website, <strong>we recommend at most <?php echo MAX_IMAGE_WIDTH ?> pixels wide.</strong> A good size for an inline (floating image) is between 140 and 240 pixels wide, depending on the height. Use your best judgement. </p>
		<p>&nbsp;</p>
		
		<p>
			<label for="image">Select an image:</label>
			<input type="file" name="image" id="image" value="" class="required: true" />
		</p>
		
		<p class="display_name">
			<label for="name">Title:</label> 
			<input type="text" name="title" id="title" value="" class="required: true"/>
			<span class="hint">This will be the Proper Name for Display</span>
		</p>
		
		<?php if (ALLOW_SHORT_PAGE_NAMES) { ?>
		<p>
			<label for="name">Short Name: </label> 
			<input type="text" name="name" id="name" value="" />
			<span class="hint">This will be used by the database only and in links. No spaces, commas or quotes please. </span>
		</p>
		<?php } ?>
		
		<p>
			<label for="description">Caption: </label> 
			<input type="text" name="description" id="description" value="" />
			<span class="hint">A caption may or may not be used on the front-end display. This field can be left blank. </span>
		</p>
		
		<p><strong>A Trick:</strong> If the caption starts with &ldquo;http://&rdquo;, the system will treat the image as a link (called a &ldquo;hot-link&rdquo;). In this case, the title will become the text for the link, displayed under the image, and the image will be clickable (the cursor will turn into a hand when passed over the image). The image can still be placed to float right or left, or not float at all. </p>
		<p>If you are linking to your own page, use the full URL= <em>http://<?php echo SITE_URL ?>/thearea/thepage</em>. </p>
		
		
		<div id="edit-footer" class="imagenav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Image" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
		
	</form>
	
	<script type="text/javascript">
		$().ready(function() {
			$("#add_image").validate({
				rules : {
					title: "required",
					image: "required"
				},
				messages: {
					title: "Please enter a title for this image",
					image: "Please select a file"
				}
			});
		});
	</script>
	
<?php } ?>
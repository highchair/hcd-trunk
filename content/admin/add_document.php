<?php

	function initialize_page()
	{
		 
		$post_action = "";
		if( isset($_POST['submit']) ) {
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Document" || $post_action == "Add and Return to List" ) {
			
			$name = $_POST['name'];
			$file_type = getFileExtension($_FILES['file']['name']); 
			$filename = slug( getFileName($_FILES['file']['name']) );
			$filename_string = $filename.".".$file_type; 
			
			// Check to make sure there isn't already a file with that name
			$possibledoc = Documents::FindByFilename( $filename_string ); 
			if ( is_object($possibledoc) ) {
    			setFlash("<h3>Failure: Document filename already exists!</h3>");
    			redirect( "admin/add_document" ); 
			}
			
			
			$target_path = SERVER_DOCUMENTS_ROOT . $filename_string;
			
			if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
				
				$new_doc = MyActiveRecord::Create( 'Documents', array( 'name'=>$name, 'filename'=>$filename_string, 'file_type'=>$file_type ) );
				$new_doc->save();
				if (!chmod($target_path, 0644)) {
					setFlash("<h3>Warning: Document Permissions not set; this file may not display properly</h3>");
				}
				setFlash("<h3>Document uploaded</h3>");
			
			} else {
				
				setFlash("<h3>Failure: Document could not be uploaded</h3>");
			}
			
			if( $post_action == "Add and Return to List" ) {
				redirect( "admin/list_documents" ); 
			}
		}
	}
	
	function display_page_content() {		
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#add_document").validate({
				rules : {
					name: "required",
					file: "required"
				},
				messages: {
					name: "Please a name for this document",
					file: "Please select a file"
				}
			});
		});
	</script>
	
	<div id="edit-header" class="documentnav">
		<h1>Add Document</h1>
	</div>
	
	<form method="POST" enctype="multipart/form-data" id="add_document">
		<input type="hidden" name="MAX_FILE_SIZE" value="15800000">
	
		<p class="announce">Some acceptable document types include PDF, DOC, XLS, PPT, ZIP, and SIT. Images can be uploaded, but the browser will not display them... they will be links instead. <strong>The Max Upload file size is 15mb</strong>.</p>
		
		<p>&nbsp;</p>
		
		<p class="display_name">
			<label for="name">Name: </label>
			<span class="hint">This is a more descriptive name for the file that will be displayed as the link when you insert it into a page. The name can start with the words &ldquo;Click here to download&hellip;&rdquo; in order to be most clear on the front-end display, but that is up to you. </span>
			<input type="text" name="name" id="name" value="" class="required: true" />
		</p>
		
		<p>
			<label for="file">Select a document:</label>
			<input type="file" name="file" id="file" value="" class="required: true" />
		</p>
		
		<div id="edit-footer" class="documentnav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Document" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
				
	</form>
<?php } ?>
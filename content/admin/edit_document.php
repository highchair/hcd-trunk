<?php
	function initialize_page()
	{
		$document_id = requestIdParam();
		$document = Documents::FindById($document_id);
		
		$post_action = "";
		if( isset($_POST['submit']) )
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Save Document" || $post_action == "Save and Return to List" )
		{
			if(isset($_POST['delete']))
			{
				$document->delete(true);
				//Pages::DeleteDocumentReferences($old_filename, $document->filename);
				
				setFlash("<h3>Document deleted</h3>");
				redirect("/admin/list_documents");
			}
			else
			{
			    // did the user upload a new document?
				if($_FILES['file']['name'])
				{
					$extension = substr( $_FILES['file']['name'], strrpos( $_FILES['file']['name'], "." ) );
					$filename = slug( $_FILES['file']['name'] ).$extension;
					$target_path = SERVER_DOCUMENTS_ROOT . $filename;
					if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path))
					{
						if (!chmod($target_path, 0644)) {
							setFlash("<h3>Document Permissions not set</h3>");
						}
					}
					else
					{
						setFlash("<h3>Updated document could not be uploaded</h3>");
					}
					// Grab old name
					$old_filename = $document->filename; 
					// Set the new name
					$document->filename = $filename;
					$document->file_type = substr( $extension, 1); // Chop the dot off
					Pages::UpdateDocumentReferences($old_filename, $document->filename);
			    }
    			
				$document->name = $_POST['name'];
				$document->save();
			
				setFlash("<h3>Document changes saved</h3>");
				
				if( $post_action == "Save and Return to List" ) {
					redirect( "admin/list_documents" ); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		$document_id = requestIdParam();
		$document = Documents::FindById($document_id);
?>

	<script type="text/javascript">
		$().ready(function() {
			$("#edit_document").validate({
				rules : {
					name: "required"
				},
				messages: {
					name: "Please enter a name for this document"
				}
			});
		});
	</script>
	
	<div id="edit-header" class="documentnav">
		<h1>Edit Document</h1>
	</div>
	
	<form method="POST" enctype="multipart/form-data" id="edit_document">
		<input type="hidden" name="MAX_FILE_SIZE" value="15800000">
		
		<p class="display_name">
			<label for="name">Name:</label>
			<?php textField("name", $document->name, "required: true"); ?><br />
			<span class="hint">This is a more descriptive name for the file that will be displayed as the link when you insert it into a page. The name can start with the words &ldquo;Click here to download&hellip;&rdquo; in order t be most clear on the front-end display, but that is up to you.</span>
		</p>
		
		<p>
			<label for="filename">File Name (uneditable):</label>
			<?php echo $document->filename; ?><br />
			<span class="hint">This is the physical name of the file that was uploaded.</span>
		</p>
		
		<p>
			<label for="file">Replace with a new document:</label>
			<?php fileField('file'); ?>
		</p>
		
		
		<div id="edit-footer" class="documentnav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Document" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
<?php 
	$user = Users::GetCurrentUser();
	if($user->has_role()) { ?>
	
				<p><label for="delete">Delete this document?</label>
					<input name="delete" class="boxes" type="checkbox" value="<?php echo $document->id ?>" />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this document from the database</span>
				</p>
<?php } ?>
			</div>
		</div>
	
	</form>
<?php } ?>
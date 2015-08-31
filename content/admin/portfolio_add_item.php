<?php
	function initialize_page()
	{
		$success = $post_action = "";
		
		if( isset($_POST['submit']) ) {
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Item" || $post_action == "Add and Return to List" ) {
			
			// ! create item
			$item = MyActiveRecord::Create('Items');
			$item->content = $_POST['item_content'];
			$item->display_name = $_POST['display_name'];
			$item->name = slug($_POST['display_name']);
			$item->location = $_POST['location'];
			$item->public = checkboxValue($_POST,'public');
			$item->mime_type = 0;
			$item->taxonomy = $_POST['taxonomy'];
			$item->date_created = date('Y-m-d H:i:s');
			
			// optional fields
			$item->sku = ( ITEM_SKU ) ? $_POST['item_sku'] : null;
			$item->taxonomy = ( ITEM_TAXONOMY ) ? $_POST['taxonomy'] : null;
			$item->price = ( ITEM_PRICE ) ? $_POST['item_price'] : null;
			
			// synchronize the users area selections
			$selected_sections = array();
			if( isset($_POST['selected_sections']) ) {
				$selected_sections = $_POST['selected_sections'];
			}
			
			$item->save();
			$item->updateSelectedSections($selected_sections);
			$item->setDisplayOrder();
			$success .= "Item Saved / "; 
			
			// ! create gallery and associate it
			$gallery = MyActiveRecord::Create('Galleries');
			
			$gallery->name = $_POST['display_name']." Gallery";
			$gallery->slug = "portfolioGal_".$item->id."_".slug($_POST['display_name']);
			$gallery->save();
			$success .= "Gallery Created / "; 
			
			if( PORTFOLIOTHUMB_IMAGE ) {
				// now check if a thumbnail was uploaded
				if ( is_uploaded_file($_FILES["thumbnail"]["tmp_name"]) ) {
					
					$mimeType = $_FILES["thumbnail"]["type"];
					$fileType = "";
					switch ($mimeType) {
						case "image/gif";				
								$fileType = "gif";
								break;
						case "image/jpg";
						case "image/jpeg";				
								$fileType = "jpg";
								break;
						case "image/png";				
								$fileType = "png";
								break;
						case "image/x-MS-bmp";			 
								$fileType = "bmp";
								break;
					} 
					resizeToMultipleMaxDimensions( $_FILES["thumbnail"]["tmp_name"], PORTFOLIOTHUMB_IMAGE_MAXWIDTH, PORTFOLIOTHUMB_IMAGE_MAXHEIGHT, $fileType ); 
					// Open the uploaded file
					$file = fopen($_FILES["thumbnail"]["tmp_name"], "r");
					// Read in the uploaded file
					$fileContents = fread($file, filesize($_FILES["thumbnail"]["tmp_name"])); 
					// Escape special characters in the file
					$fileContents = AddSlashes($fileContents);
	
	    			$updateQuery = "UPDATE items SET thumbnail = \"{$fileContents}\", mime_type = \"$mimeType\" WHERE id = {$item->id};";
	    			
	    			if ( mysql_Query( $updateQuery, MyActiveRecord::Connection() ) ) { 
	    			    $success .= "Thumbnail Added / "; 
	    			} else { die( mysql_error() ); }
				}
			}
			setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
			
			// Remember to get a section for the redirect link...
			$itemsection = array_shift( $item->getSections() ); 
			redirect( "/admin/portfolio_edit/".$itemsection->name."/".$item->id );
		}
	}
	
	function display_page_content()
	{
		$portareas = Areas::FindPortAreas( false ); 
		$sections = Sections::FindAll(); 
?>

	<script type="text/javascript">		
		$().ready(function() {
			$("#add_item").validate({
				errorLabelContainer: $("#error_container"),
				rules: {
						thumbnail: "required",
						display_name: "required",
						"selected_sections[]": "required"
					},
				messages: {
						thumbnail: "<br />Please Select a Thumbnail. You may change it later if need be",
						display_name: "Please enter a name that should be displayed for this item",
						"selected_sections[]": "Almost forgot! Select at least one section to include this item in" 
					}
			});
		});
	</script>
	
	<div id="edit-header" class="itemnav">
		<h1>Add Item</h1>
	</div>
	
	<form method="POST" id="add_item" enctype="multipart/form-data">
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
<?php
	if ( count($sections) < 1 ) {
		echo "<h3>There are no sections yet! Please <a class=\"short\" href=\"".get_link("admin/portfolio_add_section")."\">add one</a> first.</h3>";
	} else {
?>
		
<p class="display_name">
			<label for="display_name">Display Name:</label>
			<?php textField( "display_name", '', "required: true" ); ?>
		</p>
		
<?php if(PORTFOLIOTHUMB_IMAGE) { ?>
		<div id="thumbnail" class="column half">
			
			<p><label for="thumbnail">Thumbnail:</label></p>
			<p>
				&nbsp;Select an image to use as a thumbnail:<br />
			    &nbsp;<input type="file" name="thumbnail" id="id_thumbnail" value="" class="" />
			</p>
		</div>
		<div class="column half last">
<?php } else { ?>	
		<div>
<?php } ?>
		
<?php if(ITEM_SKU) { ?>
			<p>
				<label for="item_sku">Item Sku (unique ID):</label>
				<?php textField( "item_sku" ); ?>
			</p>

<?php } ?>
<?php if(ITEM_PRICE) { ?>
			<p>
				<label for="item_price">Item Price:</label>
				<?php textField( "item_price" ); ?>
			</p>

<?php } ?>
<?php if (ITEM_TAXONOMY) { require_once(snippetPath("item-taxonomy")); }  ?>
			
			<p><label for="name">Public:</label><?php checkBoxField( "public" ); ?>&nbsp; <span class="hint">Visible or not visible to the public? If you are working on an item that is not yet ready, leave this off until it is complete.</span></p>
	
		</div>
		<div class="clearleft"></div>

		<p>
			<label for="item_content">Item Description:</label><br />
			<?php textArea("item_content", '', 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
<?php require_once( snippetPath("admin-insert_configs") ); ?>
		
		
		<div id="gallery-options" class="clearfix">
				
			<a name="editgal"></a>
			<ul id="gallery-options-nav" class="menu tabs">
				<li><a href="#section_selector" class="openclose opened">Edit Sections this Item is in</a></li>
			</ul>
			
			<div id="section_selector" class="dropslide">
				<h2><legend>Select a Section to include this Item in:</legend></h2>
				<fieldset>
<?php
foreach( $portareas as $area ) {
	echo "<p><strong>".$area->get_title().":</strong><br />"; 
	
	$sections = $area->getSections( true ); 
	
	foreach ( $sections as $section ) {
		
		echo "\t\t\t\t\t\t<label for='selected_sections[]'>{$section->display_name}&nbsp;";
		echo "<input name='selected_sections[]' class='boxes' type='checkbox' value='{$section->id}' /></label>\n";			
	}
	echo "</p>\n"; 
}
?>
							
					<p><span class="hint">Any item can be in more than one Section. If no sections are selected, this page will not be viewable by the public and it will appear under &ldquo;Orphaned Items&rdquo;.</span></p>
				</fieldset>
			</div><!-- #section_selector -->
		</div>
		
		<p>&nbsp;</p>
		
		<div id="edit-footer" class="itemnav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Item" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last">&nbsp;</div>
		</div>

	</form>
<?php 
		} // end count($sections)
	} // end display_page_content()
?>
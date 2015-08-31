<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Product" or $post_action == "Add and Return to List" )
		{
			$product = MyActiveRecord::Create('Product');
			$product->display_name = $_POST['display_name'];
			$product->name = slug($_POST['display_name']); 
			$product->price = $_POST['price'];
			if (isset($_POST['product_description'])) { $product->description = $_POST['product_description']; }
			$product->id = null;
			$product->save();
			
			$product = Product::FindByName($product->name);
						
			$account = Paypal_Config::FindById($_POST['accountId']);
			
			$account->setLink($product->id, 'product');
			
			// now check if a thumbnail was uploaded
			if (is_uploaded_file($_FILES["image"]["tmp_name"]))
			{
				$mimeType = $_FILES["image"]["type"];
				$fileType = "";
				switch ($mimeType)
				{
					case "image/gif";				
							$mimeName = "GIF Image";
							$fileType = "gif";
							break;
					case "image/jpeg";					
							$mimeName = "JPEG Image";
							$fileType = "jpg";
							break;
					case "image/png";				
							$mimeName = "PNG Image";
							$fileType = "png";
							break;
					case "image/x-MS-bmp";			 
							$mimeName = "Windows Bitmap";
							$fileType = "bmp";
							break;
					default: 
						 $mimeName = "Unknown image type";
				}
				
				// Open the uploaded file
				
				// MAIN IMAGE
				resizeToMaxDimension($_FILES["image"]["tmp_name"], PRODUCT_IMAGE_MAXWIDTH, "jpg");
				// Open the uploaded file
				$file = fopen($_FILES["image"]["tmp_name"], "r");
				$filesize = filesize($_FILES["image"]["tmp_name"]);
				// Read in the uploaded file
				$imageContents = fread($file, $filesize);
				// Escape special characters in the file
				$imageContents = AddSlashes($imageContents);
				
				// THUMBNAIL
				resizeToMaxDimension($_FILES["image"]["tmp_name"], PRODUCTTHUMB_IMAGE_MAXWIDTH, "jpg");
				// Open the uploaded file
				$file = fopen($_FILES["image"]["tmp_name"], "r");
				$filesize = filesize($_FILES["image"]["tmp_name"]);
				// Read in the uploaded file
				$thumbContents = fread($file, $filesize); 
				// Escape special characters in the file
				$thumbContents = AddSlashes($thumbContents);

    			$updateQuery = "UPDATE product SET thumbnail = \"{$thumbContents}\", image = \"{$imageContents}\", mime_type = \"$mimeName\" WHERE id = {$product->id}";
    			$result = mysql_Query($updateQuery, MyActiveRecord::Connection() );
			}	
			
			setFlash("<h3>Product Added</h3>");
			if ( $post_action == "Add and Return to List" ) {
			    redirect( "admin/list_products" ); 
            }
		}
	}
	
	function display_page_content()
	{
		$account = Paypal_Config::GetAccount();
?>
										
	<script type="text/javascript">
		$().ready(function() {
			$("#add_product").validate({
				rules: {
						display_name: "required",
						price: "required"
					},
				messages: {
						display_name: "Please a name that should be displayed for this product",
						price: "Please enter a price for this product"
					}
			});
		});
	</script>
					
	<div id="edit-header" class="productnav">
		<h1>Add Product</h1>
	</div>
	
	<form method="POST" id="add_product" enctype="multipart/form-data">
		<?php hiddenField("accountId", $account->id); ?>
		
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name"><label for="display_name">Display Name:</label><span class="hint">This is the Proper Name of the product.</span><br />
		<?php textField("display_name", "", "required: true"); ?></p>
		
		<p><label for="price">Price:</label><span class="hint">This is the price of the product.</span><br />
		<?php textField("price", "", "required: true"); ?></p>
		
		<p>
		    <label for="id_image">Select an image to use:</label>
		    <input type="file" name="image" id="id_image" value="" />
		</p>
		
		<p><label for="product_description">Description:</label><br />
		<?php textArea("product_description", "", 98, 30); ?>
		</p>
			
		<div id="edit-footer" class="productnav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Product" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
		
	</form>
<?php } ?>
<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
?>
    
    <div id="edit-header" class="productnav">
		<div class="nav-left column">
    		<h1>Choose a Product to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_product") ?>" class="hcd_button">Add a New Product</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<p class="announce">Be sure that you have a <a href="<?php echo get_link("/admin/list_paypal") ?>">PayPal Account Email</a> set up!</p>
					
	<div id="table-header" class="documents">
		<strong class="item-link">Product Name</strong>
		<span class="item-filename">Short Name of Product</span>
	</div>
	
	<ul id="list_items">
<?php
	$products = Product::FindAll();
	
	if (count($products) > 0)
	{
		foreach($products as $product)
		{ 
			echo "\t\t<li><a href=\"" . get_link("/admin/edit_product/$product->id") . "\">";
			if (isset($product->thumbnail)){ $product->displayThumbnail(); }		
			echo " {$product->display_name}</a> {$product->name}</li>\n";
		}
	} else {
		echo "<h3>There are no products to edit. Please <a class=\"short\" href=\"".get_link("admin/add_product")."\">add one</a>.</h3>"; 
	}
?>
	
	</ul>
<?php } ?>
<!-- Insert a Product function -->
<div id="prod_insert" class="dropslide" style="display: none; ">
	<p><span class="hint">Products can be inserted just like images or documents. Create the product first and assign it all the necessary attributes (name, price, etc...). Then, insert it by clicking the name of the product below. The product buttons will display according to the way your site was originally designed. </span></p>
	
<?php 
	if(count($products) > 0)
	{
?>
	<table class="dropdown" cellpadding="0" cellspacing="0" border="0" width="98%">
		<thead>
			<th width="50%">Thumb and Product Name</th>
			<th width="50%">Thumb and Product Name</th>
		</thead>
		<tbody>
			<tr>
<?php			
		$counter_prod = 1; 
		$products = array_reverse($products);
		foreach($products as $product)
		{
			if ($counter_prod == 3)
			{
				echo "\t\t\t</tr><tr>\n"; 
				$counter_prod = 1; 
			}
?>
		
				<td class="gallerythumb divider"><a href="#" onclick="insertDocument('product:<?php echo $product->name; ?>', '}}', '}}'); return false;"><img src="<?php echo get_link("/images/prodimgthb/".$product->id) ?>" alt="product thumbnail"> <?php echo $product->name; ?></a></td>

<?php
			$counter_prod++; 
		} 
?>
			</tr>
		</tbody>
	</table>
<?php 
	} else {
		echo "<h3>No products have been created yet!</h3>\n"; 
	}
?>	
	
</div>
<!-- End Insert Product function -->
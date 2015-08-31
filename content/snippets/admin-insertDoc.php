<!-- Insert a Document function -->
<div id="doc_insert" class="dropslide" style="display: none; ">
	<p><span class="hint">Click the name of the Document to insert the download link to it in the edit window above. The window above might lose the insertion point, so be sure your cursor is where you want it first. </span></p>

<?php 
	if(count($documents) > 0)
	{
?>

	<table class="dropdown" cellpadding="0" cellspacing="0" border="0" width="98%">
		<thead>
			<th width="25%">Document Display Name</th>
			<th width="25%">File Name</th>
			<th width="25%">Document Display Name</th>
			<th width="25%">File Name</th>
		</thead>
		<tbody>
			<tr>
<?php 
		$counter_doc = 1; 
		$documents = array_reverse($documents); 
		foreach($documents as $document)
		{
			if ($counter_doc == 3)
			{
				echo "\t\t\t</tr><tr>\n"; 
				$counter_doc = 1; 
			}
?>
			
				<td><b><a href="#" onclick="insertDocument('document:<?php echo $document->filename?>', '{{', '{{'); return false;"><?php echo $document->name ?></a></b></td>
				<td class="divider"><?php echo $document->filename ?></td>
<?php
			$counter_doc++; 
		} 
?>

			</tr>
		</tbody>
	</table>
<?php 
	} else {
		echo "<h3>No documents have been uploaded yet!</h3>\n"; 
	}
?>
</div>
<!-- End Insert Document function -->
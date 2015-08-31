<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		if ( !requestIdParam() )
		{
			$documents = Documents::FindAll("DESC");
		} else {
			$documents = Documents::FindByFiletype( requestIdParam() ); 
		}
	
?>
	
	<div id="edit-header" class="documentnav">
		<div class="nav-left column">
    		<h1>Choose a Document to Edit</h1>
		</div>
		<div class="nav-right column">
            <a href="<?php echo get_link("admin/add_document") ?>" class="hcd_button">Add a New Document</a>
		</div>
		<div class="clearleft"></div>
	</div>
	
	<p class="announce">Documents are defined as downloadable files or forms. PDFs, DOCs, and the like are acceptable. You may use this feature to make high resolution images downloadable as well. If the image is too large to upload, and you get an error, you may want to compress the image with StuffIt (.sit), WinZip (.zip), or ZipIt (.zip).</p>
	
<?php 
		if ( count($documents) > 0 ) { 
?>			
	<div id="table-header" class="documents">
		<strong class="item-link">Document Name</strong>
		<span class="item-filename">Filename</span>
		<span class="item-viewlink">View file in new window</span>
	</div>
	
		<?php
			//$filetypes = Documents::FindUniqueFiletypes(); 
			
			if ( isset($filetypes) && count($filetypes) > 1 )
			{
				echo "<ul class=\"menu tabs\">
		<li>Show Only: </li>\n"; 
				foreach ( $filetypes as $type )
				{
					echo "\t\t<li><a href=\"".get_link("admin/list_documents/".$type->file_type)."\">$type->file_type</a></li>\n"; 
				}
				echo "\t\t<li><a class=\"hcd_button\" href=\"".get_link("admin/list_documents/")."\">Show All</a></li>\n\t</ul>\n "; 
			}
		?>
	
	<ul id="listitems" class="managelist">
		<?php
			foreach( $documents as $document )
			{ 
				echo "\t\t<li>
			<span class=\"item-link\"><a href=\"" . get_link("/admin/edit_document/$document->id") . "\">$document->name <small>EDIT</small></a></span>
			<span class=\"item-filename\">$document->filename</span>
			<span class=\"item-viewlink\"><a href=\"" . $document->getPublicUrl() . "\" target=\"_blank\">View file</a></span>
		</li>\n";
			}
		?>
	
	</ul>
<?php 
		} else {
			
			echo "\t\t<h3 class=\"empty-list\">There are no documents to edit. <a class=\"short\" href=\"".get_link("admin/add_document")."\">Add one if you like</a>.</h3>"; 
		}
	} 
?>
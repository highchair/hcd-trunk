<?php	
	function initialize_page()
	{
	}
	
	function display_page_content()
	{
?>	
				
	<div id="edit-header" class="aliasnav">
		<h1>Alias List</h1>
	</div>
	
	<p><strong>An Alias</strong> is a meta-redirect &ndash; one address can forward to another. In the first column are your current redirect addresses, and in the second column is the page/event/blog post/etc... that the alias will redirect to. </p>
	
<?php
		$alias = Alias::FindAll();
		
		if ( count($alias) > 0 ) {
?>
			
	<div id="table-header" class="alias">
		<strong class="item-link">Alias Link</strong>
		<span class="item-filename">Alias Destination</span>
		<span class="item-viewlink">Test the Alias</span>
	</div>
	
	<ul class="managelist"> 
<?php		
			foreach($alias as $entry)
			{
				$slash = "/";
				if (substr($entry->alias, 0, 1) == "/") { $slash = ""; }
?>

		<li>
			<a class="item-link" href="<?php echo get_link("/admin/alias_edit/" . $entry->id); ?>"><?php echo SITE_URL.$slash.$entry->alias; ?></a>
			<span class="item-filename"><?php echo $entry->path; ?></span>
			<span class="item-viewlink"><a href="<?php echo "http://".SITE_URL.$slash.$entry->alias; ?>" target="_blank">Open</a></span>
		</li>
<?php
			}	
			echo "</ul>\n"; 
		} else {
			echo "<h3 class=\"empty-list\">There are no Alias' to edit. <a href=\"".get_link("admin/alias_add")."\">Please add one</a>. </h3>"; 
		}
	
	} 
?>
<?php
function initialize_page()
{
	
}

function display_page_content()
{
	$gallery_slug = requestIdParam();
	$gallery = Galleries::FindBySlug($gallery_slug);

	foreach($gallery->get_photos() as $photo)
	{
		echo "<a href='{$photo->getPublicUrl()}' title='{$photo->caption}'>Photo</a>";
	}
}
?>
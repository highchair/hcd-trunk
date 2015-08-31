<?php
function initialize_page()
{
	
}

function display_page_content()
{
	$photo_id = requestIdParam();
	$photo = Photos::FindById($photo_id);
	$next_photo = $photo->get_next_photo();
	$prev_photo = $photo->get_previous_photo();
	if($prev_photo->id != $photo->id)
	{
		echo "<a href='" . get_link("/galleries/photo/$prev_photo->id") . "'>Previous</a>";
	}
	echo "<img src='{$photo->getPublicUrl()}' />";
	if($next_photo->id != $photo->id)
	{
		echo "<a href='" . get_link("/galleries/photo/$next_photo->id") . "'>Next</a>";
	}
	
}
?>
<?php
function display_error($error)
{
	die($error);
}

function display_404()
{
    require_once(pagePath("404"));
}
?>

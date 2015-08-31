<?php
function getFilterIds($content = "", $pattern)
{
	/* preg_match_all will give us both the matched string, and also the matched substrings (in this case, the id) so it's perfect */
	preg_match_all($pattern, $content, $ids, PREG_PATTERN_ORDER);
	
	// $ids[0] is the list of the actually matched strings, $ids[1] is the list of the first parenthized subpattern (image id)
	return $ids[1];
}

function updateContent($content = "", $pattern, $replacement)
{
	$content = preg_replace($pattern, $replacement, $content);
	
	return $content;
}
?>
<?php 
	ob_start();
    
    $slug = slug( SITE_NAME ); 
    
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo SITE_NAME." ".date("F j\, Y"); ?></title>
		<base href="http://<?php echo SITE_URL.BASEHREF; ?>"></base>

<?php require_once(snippetPath("mailblast-css")); ?>

	</head>
	
	<body id="<?php echo $slug ?>_email_body" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		
<?php
	//echo $featured_html;
	if ( isset($custom_html) ) { echo $custom_html; }
	if ( isset($upcoming_html) ) { echo $upcoming_html; }
	if ( isset($ongoing_html) ) { echo $ongoing_html; }
?>

	</body>

</html>
<?php 
	$mailed_content = ob_get_contents();
	ob_clean();
?>
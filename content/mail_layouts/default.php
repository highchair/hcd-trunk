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
		
		<div id="<?php echo $slug ?>_email_template">
			<table cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th>
							<img src="/lib/cssimages/mailblast_header.png" title="<?php echo SITE_NAME; ?>" />
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="datehead">
							<h3>sent <?php echo date("F j\, Y"); ?></h3>
							<p>Can&rsquo;t view in your mail browser? <a href="/mail/blast/<?php echo $random_hash; ?>/{{-email-}}">Click here to view online</a>.</p>
						</td>
					</tr>
					<tr>
						<td id="content">
<?php
	//echo $featured_html;
	if ( isset($custom_html) ) { echo $custom_html; }
	if ( isset($upcoming_html) ) { echo $upcoming_html; }
	if ( isset($ongoing_html) ) { echo $ongoing_html; }
?>
						
						</td>
					</tr>
					<tr>
						<td id="footer">
							<p>&copy;<?php echo date("Y") ?> <?php echo SITE_NAME; ?>. This message was sent to {{-email-}}. Modify/update subscription preferences via the link below.</p>
							<p>To manage your email subscriptions, please visit <a href="/users/manage/{{-email-}}">our website</a>. Opt-out or change lists at any time. </p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>

</html>
<?php 
	$mailed_content = ob_get_contents();
	ob_clean();
?>


<?php require_once(snippetPath("mailblast-css")); ?>

		<div id="downpwt_email_template">
			<table cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th>
							<img src="http://<?php echo SITE_URL.BASEHREF; ?>lib/cssimages/mailblast_header.png" title="<?php echo SITE_NAME; ?>" />
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
							<p>&copy;<?php echo date("Y") ?> <?php echo SITE_NAME; ?>. This message was sent to [ email ]. Modify/update subscription preferences via the link below.</p>
							<p>To manage your email subscriptions, please visit <a href="/users/manage/{{-email-}}">our website</a>. Opt-out or change lists at any time. </p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

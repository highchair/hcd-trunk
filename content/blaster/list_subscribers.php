<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$emails = NLEmails::FindAll();
		$lists = NLLists::FindAll(); 
?>

					<h1>Subscribers listed below</h1>
<?php
	$addresscount = 0; 
	$subscribecount = 0; 
	$subscribers = ""; 
	
	foreach($emails as $email)
	{ 
		$lists_subscribed_to = 0; 
		
		foreach ($lists as $list)
		{
			if ($email->is_linked($list)) 
			{ 
				$lists_subscribed_to++; 
			}
		}
		$address = ""; 
		if ($email->first_name && $email->last_name && $email->address1 && $email->city && $email->state && $email->zip)
		{
			$address = "(Complete address available)"; 
			$addresscount++; 
		}
		$subscribers .= "\t\t\t\t\t\t<li><a href=\"#\"><strong>$email->email</strong></a> Subscribed to: $lists_subscribed_to lists $address</li>\n";
		$subscribecount++; 
	}
?>

					<p>{<?php echo $subscribecount ?>} Subscribers with {<?php echo $addresscount ?>} complete address(es)</p>
					<ul id="list_items">
<?php echo $subscribers ?>
					
					</ul>
					
<?php } ?>
<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
		$accounts = Paypal_Config::FindAll();
	
?>

					<h1>List of Paypal accounts</h1>
					<ul id="paypal">
<?php
	if (count($accounts) > 1)
	{
		foreach($accounts as $account)
		{ 
			echo "\t\t\t\t\t\t<li><a href=\"" . get_link("/admin/edit_paypal/$account->id") . "\">{$account->account_name}</a> $account->email</li>\n";
		}

	} else {
		redirect("admin/edit_paypal/$account->id"); 
	}
?>
	
					</ul>

<?php } ?>
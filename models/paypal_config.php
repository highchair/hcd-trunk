<?php
class Paypal_Config extends ModelBase
{
	function FindAll()
	{
		return MyActiveRecord::FindAll('Paypal_Config', NULL, " id ASC");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Paypal_Config', $id);
	}
	
	// For getting all items from a specific pay pal account
	function GetItems($table)
	{
		return MyActiveRecord::FindBySql("Paypal_Config", "
			SELECT p.id, p.price, p.display_name, pc.account_name, pc.email, pc.return, pc.cancel_return FROM $table p 
			INNER JOIN paypal_items ppi ON ppi.item_id = p.id AND ppi.item_table = '$table' 
			INNER JOIN paypal_config pc ON ppi.paypal_id = pc.id  
			ORDER BY id ASC;");
	}
	
	// For writing custom Paypal buttons
	function GetItem($id, $table)
	{
		return array_shift(MyActiveRecord::FindBySql("Paypal_Config", "
			SELECT p.id, p.price, p.display_name, pc.account_name, pc.email, pc.return, pc.cancel_return FROM $table p 
			INNER JOIN paypal_items ppi ON ppi.item_id = p.id AND ppi.item_table LIKE '$table'
			RIGHT JOIN paypal_config pc ON ppi.paypal_id = pc.id 
			WHERE p.id = $id 
			ORDER BY id ASC;"));
	}
	
	// A function that gets pay pal account information by type. Defaults to the Type uploaded by default_SQL_content in setup.
	function GetAccount($type = "Paypal Account")
	{
		return Paypal_Config::FindFirst("Paypal_Config", "account_name='$type'");
	}
	
	// Sets the links between items and their associated pay pal account
	function setLink($id, $table)
	{
		$paypal_accnt = Paypal_Config::FindFirst("Paypal_Config", "email='{$this->email}'");
		
		if (!$paypal_accnt) { echo "Too bad\n"; return false; }
		
		$query = "INSERT INTO paypal_items (item_table, item_id, paypal_id) VALUES ('$table', '$id', '{$this->id}');";
		mysql_query($query, MyActiveRecord::Connection());
	}
	
	function getViewCartButton() 
	{
		return "\t<form target=\"paypal\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
		<input type=\"hidden\" name=\"cmd\" value=\"_cart\">
		<input type=\"hidden\" name=\"business\" value=\"".$this->email."\">
		<input type=\"hidden\" name=\"display\" value=\"1\">
		<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_viewcart_SM.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">
		<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">
	</form>\n";
	}
}
?>
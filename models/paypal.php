<?php
class Paypal extends ModelBase
{
	function GetItems($table)
	{
		return MyActiveRecord::FindBySql("Paypal_Config", "
			SELECT p.* FROM paypal_config p 
			INNER JOIN paypal_items ppi ON ppi.paypal_id = p.id 
			INNER JOIN $table ppc ON ppc.id = ppi.item_id 
			WHERE ppi.item_table LIKE '$table' 
			ORDER BY id ASC;");
	}
	
	
}
?>
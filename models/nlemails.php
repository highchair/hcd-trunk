<?php
class NLEmails extends ModelBase
{
	function FindAll() 
	{
		return MyActiveRecord::FindAll('NLEmails', '', 'id ASC');
	}
	
	function FindByEmail($email) 
	{
		return array_shift(MyActiveRecord::FindBySql('NLEmails', "SELECT * FROM nlemails WHERE email = '$email'"));
	}
	
	function is_linked($list)
	{
		$result = mysql_query("SELECT * FROM nlemails_nllists WHERE nlemails_id = $this->id AND nllists_id = $list->id", MyActiveRecord::Connection());
		return mysql_num_rows($result);
	}
	
	function getNLlists() {
    	return $this->find_linked( 'nllists', '', 'display_name DESC' ); 
	}
}
?>
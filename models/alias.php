<?php
class Alias extends ModelBase
{
	// This needs to go somewhere else in order to work... not sure where though
	function __construct() {
		// checks for the alias table
		$result = mysql_query("SHOW TABLES LIKE 'alias'", MyActiveRecord::Connection());
		if (mysql_num_rows($result) < 1) {
			$query = "CREATE TABLE `alias` (`id` int(11) NOT NULL auto_increment,`alias` varchar(255) NOT NULL,`path` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
			mysql_query($query, MyActiveRecord::Connection());
		}
	}
	
	function FindByAlias($alias)
	{
		//return MyActiveRecord::FindFirst('Areas', "name like '" . $name . "'");
		return array_shift(MyActiveRecord::FindBySql('alias', "SELECT a.* FROM alias a WHERE a.alias like '" . $name . "'"));
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Alias', $id);
	}
	
	function FindAll()
	{
		return MyActiveRecord::FindAll('Alias');
	}
}
?>
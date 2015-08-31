<?php
class Keywords extends ModelBase
{
	function FindByName($name="")
	{
		if($name == "")
		{
			return;
		}
		return MyActiveRecord::FindFirst('Keywords',"(name like '{$name}')");
		//return array_shift(MyActiveRecord::FindBySql('Keywords', "SELECT );
	}
	
	function FindBySectionName($section_name="")
	{
	    if($section_name == "")
		{
			return array();
		}
		
		$section = Sections::FindByName($section_name);
		$items = $section->findItems();
		
		$id_list = "";
		foreach($items as $item)
		{
		    $id_list .= "{$item->id},";
		}
		$id_list = trim($id_list, ',');
		return MyActiveRecord::FindBySql('Keywords', "SELECT DISTINCT k.* FROM keywords k inner join items_keywords ik ON ik.keywords_id = k.id and ik.items_id in ({$id_list})");
	}
}
?>
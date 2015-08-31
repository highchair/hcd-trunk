<?php
class EventPeriods extends ModelBase
{
	function FindAll()
	{
		return MyActiveRecord::FindAll('EventPeriods');
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('EventPeriods', $id);
	}
	
}
?>
<?php
class Recurrence extends ModelBase
{
	function FindAll()
	{
		return MyActiveRecord::FindAll('Recurrence');
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Recurrence', $id);
	}
	function ClearForEvent($event_id)
	{
		if($event_id)
		{
			$query = "DELETE FROM recurrence WHERE event_id = $event_id";
			MyActiveRecord::Query($query);
		}
	}
	function FindForEvent($event_id)
	{
		if($event_id)
		{
			return MyActiveRecord::FindAll('Recurrence', "event_id=$event_id");
		}
	}
}
?>
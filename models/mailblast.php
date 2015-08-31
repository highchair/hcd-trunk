<?php
class MailBlast extends ModelBase
{
	function FindAll($order = "DESC")
	{
		return MyActiveRecord::FindAll("MailBlast", null, "date_sent $order");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById("MailBlast", $id);
	}
}
<?php
class PortfolioImages extends ModelBase
{
	function FindById($id)
	{
		return MyActiveRecord::FindById('portfolioimages', $id);
	}
	
	function FindForItem($item_id)
	{
		return MyActiveRecord::FindAll('portfolioimages', "item_id = {$item_id}", "id ASC");
	}
	
	function getPublicUrl() 
	{
	    return get_link("/images/portimg/".$this->id);
	}
}
?>
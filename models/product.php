<?php
class Product extends ModelBase
{
	function FindAll()
	{
		return MyActiveRecord::FindAll('Product', NULL, " id ASC");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Product', $id);
	}
	
	function FindByName($name)
	{
		return MyActiveRecord::FindFirst('Product', "name='$name'");
	}
	
	function displayThumbnail() 
	{
		echo "<img src=\"".get_link("/images/prodimgthb/".$this->id)."\" alt=\"{$this->display_name}\"/>";
	}
	
	function displayImage() 
	{
		echo "<img src=\"".get_link("/images/prodimg/".$this->id)."\" alt=\"{$this->display_name}\"/>";
	}
}
<?php
class Categories extends ModelBase
{
	function FindAll()
	{
		return MyActiveRecord::FindAll('categories', "", "display_name ASC");
	}
	
	function FindByName( $name )
	{
		return array_shift(MyActiveRecord::FindBySql('categories', "SELECT c.* FROM categories c WHERE c.name like '" . $name . "'"));
	}
	
	function FindById( $id )
	{
		return MyActiveRecord::FindById('categories', $id);
	}
	
	function getEntries( $onlygetfromnow=true, $onlypublic=true, $orderby="date DESC" )
	{
		$dateString = date("Y-m-d H:i:s"); 
		
		$publicclause = ( $onlypublic ) ? "blog_entries.public = 1" : ""; 
		$dateclause = ( $onlygetfromnow ) ? "date('".$dateString."') >= blog_entries.date" : "";
		
		$publicclause = ( $onlypublic && $onlygetfromnow ) ? $publicclause." AND " : $publicclause; 
		
		return $this->find_linked('blog_entries', "$publicclause"."$dateclause", "blog_entries.$orderby");
	}
	
	function getRelativeLinkPath()
	{
		
	}

	function getContent()
	{
		$this->the_content(); 
	}
	
	function get_public_url() 
	{
    	return get_link( BLOG_STATIC_AREA."/category/".$this->name ); 
	}
	
	function get_admin_url() 
	{
    	return get_link( "admin/edit_category/".$this->id ); 
	}
}
?>
<?php
class Documents extends ModelBase
{
	function FindAll($order = "ASC")
	{
		return MyActiveRecord::FindAll('Documents', NULL, " id $order");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('Documents', $id);
	}

	function FindByName($name)
	{
		return array_shift( MyActiveRecord::FindBySql('Documents', "SELECT d.* FROM documents d WHERE d.name like '" . mysql_real_escape_string($name, MyActiveRecord::Connection()) . "'") );
	}
	
	function FindByFilename($filename)
	{
		return array_shift( MyActiveRecord::FindBySql('Documents', "SELECT d.* FROM documents d WHERE d.filename like '" . mysql_real_escape_string($filename, MyActiveRecord::Connection()) . "'") );
	}
	
	function FindByFiletype($filetype)
	{
		return MyActiveRecord::FindBySql('Documents', "SELECT d.* FROM documents d WHERE d.file_type like '" . mysql_real_escape_string($filetype, MyActiveRecord::Connection()) . "'");
	}
	
	function FindUniqueFiletypes()
	{
		return MyActiveRecord::FindBySql('Documents', "SELECT * FROM documents GROUP BY file_type"); 
	}
	
	function get_filetype()
	{
		$filetype = end( explode( ".", $this->filename) ); 
		return $filetype; 
	}
	
	function getPublicUrl()
	{
		return BASEHREF . PUBLIC_DOCUMENTS_ROOT . escapeshellcmd($this->filename);
	}
	
	function delete()
	{
		$target_path = SERVER_DOCUMENTS_ROOT . $this->filename;
		if(file_exists($target_path))
		{
			unlink($target_path);
		}	  
		return MyActiveRecord::Query( "DELETE FROM documents WHERE id ={$this->id}" );
	}
	
	function getProperName($case = "all")
	{
		$newname = str_replace("-", " ", $this->name); 
		if ($case == "first")
		{
			$newname = ucfirst($newname); 
		} else {
			$newname = ucwords($newname); 
		}
		return $newname; 
	}
	
}
?>
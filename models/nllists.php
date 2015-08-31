<?php
class NLLists extends ModelBase
{
	function FindAll($where = "") 
	{
		return MyActiveRecord::FindAll('NLLists', $where);
	}
	
	function FindPublic() 
	{
		return MyActiveRecord::FindAll('NLLists', 'public = 1');
	}
	
	function FindById($id) 
	{
		return MyActiveRecord::FindById('NLLists', $id);
	}
	
	function FindBySlug($slug)
	{
		$query = "SELECT * FROM nllists WHERE name = '$slug' LIMIT 1;";
		return array_shift(MyActiveRecord::FindBySql("NLLists", $query));	
	}
	
	function findEmails()
	{
		return $this->find_linked('nlemails','','email ASC');
	}
	
	function emailLinked($email)
	{
		// if this email is linked to this list don't add it to the list again
		$query = "SELECT * FROM nlemails_nllists le INNER JOIN nlemails e ON le.nlemails_id = e.id WHERE le.nllists_id = {$this->id} AND e.email = '$email';";
		$result = MyActiveRecord::FindBySql('NLLists', $query);
		if (count($result) > 0)
		{
			return true;
		} else {
			return false;
		}
	}
}

function list_mail_templates()
{
	$templates = array();
	$layout_paths = array();
	
	$layout_paths[] = LOCAL_PATH . "framework/content/mail_layouts/*.php";
	$layout_paths[] = LOCAL_PATH . "content/mail_layouts/*.php";
	
	foreach($layout_paths as $layout_path)
	{
	    foreach (glob($layout_path) as $required_file)
    	{
    		if(substr_count($required_file, '_preview') == 0)
    		{
    			$template_name = explode(".", end(explode("/", $required_file)));
    			if(!in_array($template_name[0], $templates))
    			{
    			    $templates[] = $template_name[0];
			    }
    		}
    	}
	}
	
	return $templates;
}

function mailPath($relative_path)
{
    if(file_exists(LOCAL_PATH . 'content/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'content/' . $relative_path . ".php";
    }
    if(file_exists(LOCAL_PATH . 'framework/content/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'framework/content/' . $relative_path . ".php";
    }
    display_error("Parser '{$relative_path}' could not be found");
}
?>
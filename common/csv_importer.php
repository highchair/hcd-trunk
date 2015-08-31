<?php
class CsvFileImporter
{
    private $file_path;
    private $output_class;
    public $errors;
    private $data;
    private $columns = array();
    private $fp = false;
    private $transformations = array();
    
    function __construct($output_class, $file_path) {
        $this->file_path = $file_path;
        if($this->fp = fopen($this->file_path, 'r'))
        {
            $this->processHeader();
        }
        $this->output_class = $output_class;
        $this->errors = false;
        $data = array();
    }

    private function processHeader()
    {
        $line = fgets($this->fp);
        $fields = split(",", $line);
       
        $index = 0;
        foreach ($fields as $field)
        {
            $field = preg_replace('/[^A-Za-z0-9_\-]/', '', trim($field));
            
            $this->columns[$index] = $field;
            $index++;
        }
    }
    
    function setColumnTransform($columnName, $transformType)
    {
        $this->transformations[$columnName][] = $transformType;
    }
    private function doTransformation($columnName, $value)
    {
        if(array_key_exists($columnName, $this->transformations))
        {
            //$transformed_value = mysql_real_escape_string($value,MyActiveRecord::Connection());
            $transformed_value = $value;
            $transforms = $this->transformations[$columnName];
            foreach($transforms as $transform)
            {
                switch($transform) {
                    case "htmlentities":
                        $transformed_value = htmlentities($transformed_value,ENT_QUOTES,"UTF-8");;
                        //echo htmlentities($transformed_value);
                        
                        break;
                    default:
                        $transformed_value = $transformed_value;
                        break;
                }
            }
            return $transformed_value;
        }
        else
        {
            return $value;
        }
    }
    function csv_read()
    {
        $values = fgetcsv($this->fp);
        while($values !== false)
        {
            $row = array();
            foreach($this->columns as $index=>$name)
            {
                $value = $this->doTransformation($name, $values[$index]);
                $row[$name] = $value;
            }
            $this->data[] = $row;
            $values = fgetcsv($this->fp);
        }
    }
    
    function csv_write()
    {
        foreach($this->data as $row)
        {
            $new_object = MyActiveRecord::Create($this->output_class, $row);
            $new_object->save();
            if(!$success)
            {
                $this->errors = true;
            }
        }
        return $this->errors;
    }
}
?>
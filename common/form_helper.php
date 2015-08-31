<?php
	/** Various output helpers to generate xhtml form elements **/
	function textField($name, $value = "", $validationRules = "")
	{ 
	    $value = htmlspecialchars($value);
		$output = "<input type=\"text\" name=\"$name\" id=\"$name\" value=\"$value\" ";
		if($validationRules != "")
		{
			$output = $output . "class=\"" . $validationRules . "\"";
		}
		$output = $output . " />";
		echo $output;
	}
	
	function passwordField($name, $value = "", $validationRules = "")
	{ 
	    $value = htmlspecialchars($value);
		$output = "<input type=\"password\" name=\"$name\" id=\"$name\" value=\"$value\" ";
		if($validationRules != "")
		{
			$output = $output . "class=\"" . $validationRules . "\"";
		}
		$output = $output . " />";
		echo $output;
	}
	
	function textArea($name, $value = "", $columns = 98, $rows = 10, $validationRules = "")
	{
		$output = "<textarea name=\"$name\" id=\"$name\" rows=\"$rows\" style=\"width: {$columns}%\" ";
		if($validationRules != "")
		{
			$output = $output . "class=\"" . $validationRules . "\"";
		}
		$output = $output . ">$value</textarea>";
		echo $output;
	}
	
	/* Example of a very accesible form. Clicking on the labels will select the checkbox value. 
	<fieldset>
        <legend>Select your pizza toppings:</legend>
        <input id="ham" type="checkbox" name="toppings" value="ham">
        <label for="ham">Ham</label><br>
        <input id="pepperoni" type="checkbox" name="toppings" value="pepperoni">
        <label for="pepperoni">Pepperoni</label><br>
        <input id="mushrooms" type="checkbox" name="toppings" value="mushrooms">
        <label for="mushrooms">Mushrooms</label><br>
    </fieldset>*/
	function checkBoxField($name, $checked = 0, $value = "")
	{
		$output = "<input type=\"checkbox\" name=\"$name\"";
		if($checked == 1 || $checked == true) 
		{ 
			$output = $output . " checked=\"checked\""; 
		}
		if($value != "")
		{
			$output = $output . " id=\"$value\" value=\"$value\"";
		}
		$output = $output . "/>";
		echo $output;
	}
	
	function radioButton($name, $value = "", $label = "", $optionalclass = "", $checked = 0)
	{
		$output = "<input type=\"radio\" name=\"$name\" value=\"$value\" class=\"$optionalclass\""; 
		if ($checked)
		{
			$output .= " checked"; 
		}
		$output .= "> $label<br />"; 
		echo $output; 
	}
	
	function fileField($name)
	{
		echo "<input type=\"file\" name=\"$name\" id=\"$name\" />";
	}
	
	function submitField($value)
	{
	    $value = htmlspecialchars($value);
		echo "<input type=\"submit\" name=\"submit\" value=\"$value\" />\r\n";
	}
	
	function hiddenField($name, $value)
	{
	    $value = htmlspecialchars($value);
		echo "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
	}
	
	function selectList($name, $items = array())
	{
		echo "\n\t\t<select name=\"" . $name . "[]\" id=\"" . $name . "\" style=\"width:98%;\" multiple size=\"10\">\n";
		foreach ($items as $value=>$label)
		{
		    $value = htmlspecialchars($value);
			echo "\t\t\t<option value=\"$value\">$label</option>\n";
		}
		echo "\t\t</select>";
	}
	
	function selectDrop($name, $items = array(), $selected_value = null)
	{
		echo "\n\t\t<select name=\"" . $name . "\" id=\"" . $name . "\">\n\t\t\t<option value=\"0\">Select Item</option>\n";
		
		foreach ($items as $value=>$label)
		{
		    $selected = "";
		    $value = htmlspecialchars($value);
			if ($selected_value == $value) { $selected = "selected"; }
			echo "\t\t\t<option $selected value=\"$value\">$label</option>\n";
		}
		echo "\t\t</select>";
	}
	
	function ulList($name, $list = array(), $style = "none")
	{
		echo "\n\t\t<ul style=\"list-style-type:$style;\" id=\"$name\">\n";
		foreach ($list as $value)
		{
			$cur_state = requestIdParam();
			if($cur_state == $value) 
			{
				echo "\t\t\t<li class=\"active\">$value</li>\n";
			}
			else
			{
				echo "\t\t\t<li><a href=\"".get_link($GLOBALS["REQUEST_PARAMS"][0]."/".$GLOBALS["REQUEST_PARAMS"][1]."/".$value)."\">$value</a></li>\n";
			}
		}
		echo "</ul>\n\n";
	}
	
	function olList($name, $list = array(), $style='decimal')
	{
		echo "\n\t\t<ol style=\"list-style-type:$style;\" id=\"$name\">\n";
		foreach ($list as $value)
		{
			echo "\t\t\t<li>$value</li>\n";
		}
		echo "</ol>\n\n";
	}
	
	function dlList($name, $list = array())
	{
		include("lib/includes/vendorvars.php");
		
		echo "\n\t\t<dl id=\"$name\">\n";
		foreach($list as $cat_name => $the_vendors)
		{
			echo "\t\t<dt>{$category_list[$cat_name]}</dt>\n";
			foreach($the_vendors as $my_vendor)
			{
				echo "\t\t\t<dd><a href=\"".get_link($GLOBALS["REQUEST_PARAMS"][0]."/".$GLOBALS["REQUEST_PARAMS"][1]."/".$my_vendor->state."/".$my_vendor->id)."\">{$my_vendor->name}</a></dd>\n";
			}
		}
		echo "\t\t</dl>\n\n";
	}
	
	/** Functions that assist in working with POST/GET and models **/
	// gets the value of a checkbox field as 0/1 for a tinyint field
	function checkboxValue($post, $index)
	{
		if(!array_key_exists($index, $post))
		{
			return 0;
		}
		if($post[$index] == "on")
		{
			return 1;
		}
		return 0;
	}
	
	function getMonthName($month)
	{
		$monthNames = array("","January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		return $monthNames[$month];
	}
	
	function getProperDayName()
	{
		$dayNames = array("","Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		return $this->dayNames;
	}
	
	function getShortDayName()
	{
		$dayNames = array("","Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
		return $this->dayNames;
	}
	
	/** Big long things belong at the bottom of the page */
	function StateSelectList($name, $selected_value = null, $optionalclass = null)
	{
		echo "<select name=\"".$name."\" id=\"".$name."\" class=\"".$optionalclass."\" title=\"Please select a state\" validate=\"required:true\">\n\t<option value=\"\">Select State or Province</option>\n";
		
		$states = array(
			"AL" => "Alabama",
			"AK" => "Alaska",
			"AZ" => "Arizona",
			"AR" => "Arkansas",
			"CA" => "California",
			"CO" => "Colorado",
			"CT" => "Connecticut",
			"DE" => "Delaware",
			"DC" => "District of Columbia",
			"FL" => "Florida",
			"GA" => "Georgia",
			"HI" => "Hawaii",
			"ID" => "Idaho",
			"IL" => "Illinois",
			"IN" => "Indiana",
			"IA" => "Iowa",
			"KS" => "Kansas",
			"KY" => "Kentucky",
			"LA" => "Louisiana",
			"ME" => "Maine",
			"MD" => "Maryland",
			"MA" => "Massachusetts",
			"MI" => "Michigan",
			"MN" => "Minnesota",
			"MS" => "Mississippi",
			"MO" => "Missouri",
			"MT" => "Montana",
			"NE" => "Nebraska",
			"NV" => "Nevada",
			"NH" => "New Hampshire",
			"NJ" => "New Jersey",
			"NM" => "New Mexico",
			"NY" => "New York",
			"NC" => "North Carolina",
			"ND" => "North Dakota",
			"OH" => "Ohio",
			"OK" => "Oklahoma",
			"OR" => "Oregon",
			"PA" => "Pennsylvania",
			"RI" => "Rhode Island",
			"SC" => "South Carolina",
			"SD" => "South Dakota",
			"TN" => "Tennessee",
			"TX" => "Texas",
			"UT" => "Utah",
			"VT" => "Vermont",
			"VA" => "Virginia",
			"WA" => "Washington",
			"WV" => "West Virginia",
			"WI" => "Wisconsin",
			"WY" => "Wyoming",
			
			"AB" => "Alberta",
            "BC" => "British Columbia",
            "MB" => "Manitoba",
            "NB" => "New Brunswick",
            "NL" => "Newfoundland and Labrador",
            "NS" => "Nova Scotia",
            "NT" => "Northwest Territories",
            "NU" => "Nunavut",
            "ON" => "Ontario",
            "PE" => "Prince Edward Island",
            "QC" => "Quebec",
            "SK" => "Saskatchewan",
            "YT" => "Yukon"
		); 
		
		foreach ($states as $key => $value)
		{
		    if ($key == $selected_value) 
		    { 
				echo "\t<option selected value=\"$key\">$value</option>\n";
			} else {
				echo "\t<option value=\"$key\">$value</option>\n";
			}
		}
		echo "</select>\n";
	}
	
?>
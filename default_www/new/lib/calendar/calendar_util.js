
function setupDateFields(selected_period)
{
	switch(selected_period)
	{
		// Single day
		case "1":
		  showElement("#date_start");
			showElement("#time_start");
			hideElement("#date_end");
			showElement("#time_end");
			$("div#recurrence_rules").hide('fast');
			
		  break;    
		// All day
		case "2":
			showElement("#date_start");
			hideElement("#time_start");
			hideElement("#date_end");
			hideElement("#time_end");
			$("div#recurrence_rules").hide('fast');
		  
		  break;
		// Multi day
		case "3":
			showElement("#date_start");
			showElement("#time_start");
			showElement("#date_end");
			showElement("#time_end");
			$("div#recurrence_rules").show('fast');
	  	
	  	break;
		default:
		break;
	}
}


function hideElement(selector)
{
	var element = $(selector);
	if(!element.attr("disabled"))
	{
		element.attr("origVal", element.val());
		element.val("");
		element.attr("disabled", true);
	}
}

function showElement(selector)
{
	var element = $(selector);
	var origVal = element.attr("origVal");
	
	if(element.attr("disabled"))
	{
		element.val(origVal);
	}
	
	element.attr("disabled", false);
}
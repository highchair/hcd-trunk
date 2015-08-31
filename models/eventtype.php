<?php
class EventTypes extends ModelBase
{		
	function FindAll($calendar_id = 1)
	{
		return MyActiveRecord::FindAll('EventTypes', "calendar_id = $calendar_id", "name ASC");
	}
	
	function FindById($id)
	{
		return MyActiveRecord::FindById('EventTypes', $id);
	}
	
	function FindByName($name)
	{
		return array_shift(MyActiveRecord::FindBySql('EventTypes', "SELECT * FROM eventtypes WHERE name = '$name' LIMIT 1;"));
	}
		
	function slug()
	{
		return slug($this->name);
	}
	
	function getEventsByType($order = "ASC", $calendar_id = 1)
	{ 
		return Events::FindBySql('Events', "SELECT events.* FROM events WHERE eventtype_id = {$this->id} AND calendar_id = $calendar_id ORDER BY events.date_start $order");
	}
	
	function updateEventTypes() 
	{
		$events = $this->getEventsByType();
		foreach ($events as $event) 
		{
			// sets the event type to the generic "Event" type
			$event->eventtype_id = 1;
			$event->save();
		}
	}
	
	static $color_array = array("orange"=>DARK_TEXT_COLOR,
	"darkorange"=>DARK_TEXT_COLOR,
	"tomato"=>DARK_TEXT_COLOR,
	"orangered"=>DARK_TEXT_COLOR,
	"red"=>DARK_TEXT_COLOR,
	"crimson"=>LIGHT_TEXT_COLOR,
	"lightsalmon"=>DARK_TEXT_COLOR,
	"darksalmon"=>DARK_TEXT_COLOR,
	"salmon"=>DARK_TEXT_COLOR,
	"lightcoral"=>DARK_TEXT_COLOR,
	"indianred"=>DARK_TEXT_COLOR,
	"firebrick"=>LIGHT_TEXT_COLOR,
	"sandybrown"=>DARK_TEXT_COLOR,
	"peru"=>DARK_TEXT_COLOR,
	"chocolate"=>DARK_TEXT_COLOR,
	"sienna"=>DARK_TEXT_COLOR,
	"saddlebrown"=>LIGHT_TEXT_COLOR,
	"maroon"=>LIGHT_TEXT_COLOR,
	"yellow"=>DARK_TEXT_COLOR,
	"gold"=>DARK_TEXT_COLOR,
	"goldenrod"=>DARK_TEXT_COLOR,
	"darkgoldenrod"=>LIGHT_TEXT_COLOR,
	"tan"=>DARK_TEXT_COLOR,
	"rosybrown"=>DARK_TEXT_COLOR,
	"pink"=>DARK_TEXT_COLOR,
	"lightpink"=>DARK_TEXT_COLOR,
	"palevioletred"=>DARK_TEXT_COLOR,
	"hotpink"=>DARK_TEXT_COLOR,
	"deeppink"=>LIGHT_TEXT_COLOR,
	"mediumvioletred"=>LIGHT_TEXT_COLOR,
	"thistle"=>DARK_TEXT_COLOR,
	"plum"=>DARK_TEXT_COLOR,
	"violet"=>DARK_TEXT_COLOR,
	"orchid"=>LIGHT_TEXT_COLOR,
	"fuchsia"=>LIGHT_TEXT_COLOR,
	"magenta"=>DARK_TEXT_COLOR,
	"mediumorchid"=>LIGHT_TEXT_COLOR,
	"darkorchid"=>LIGHT_TEXT_COLOR,
	"blueviolet"=>LIGHT_TEXT_COLOR,
	"darkviolet"=>LIGHT_TEXT_COLOR,
	"darkmagenta"=>LIGHT_TEXT_COLOR,
	"purple"=>LIGHT_TEXT_COLOR,
	"mediumpurple"=>DARK_TEXT_COLOR,
	"mediumslateblue"=>DARK_TEXT_COLOR,
	"slateblue"=>LIGHT_TEXT_COLOR,
	"darkslateblue"=>LIGHT_TEXT_COLOR,
	"indigo"=>LIGHT_TEXT_COLOR,
	"skyblue"=>DARK_TEXT_COLOR,
	"lightskyblue"=>DARK_TEXT_COLOR,
	"deepskyblue"=>DARK_TEXT_COLOR,
	"cornflowerblue"=>DARK_TEXT_COLOR,
	"dodgerblue"=>DARK_TEXT_COLOR,
	"steelblue"=>DARK_TEXT_COLOR,
	"royalblue"=>DARK_TEXT_COLOR,
	"blue"=>LIGHT_TEXT_COLOR,
	"mediumblue"=>LIGHT_TEXT_COLOR,
	"darkblue"=>LIGHT_TEXT_COLOR,
	"navy"=>LIGHT_TEXT_COLOR,
	"midnightblue"=>LIGHT_TEXT_COLOR,
	"lightcyan"=>DARK_TEXT_COLOR,
	"paleturquoise"=>DARK_TEXT_COLOR,
	"powderblue"=>DARK_TEXT_COLOR,
	"lightblue"=>DARK_TEXT_COLOR,
	"aqua"=>DARK_TEXT_COLOR,
	"cyan"=>DARK_TEXT_COLOR,
	"aquamarine"=>DARK_TEXT_COLOR,
	"mediumaquamarine"=>DARK_TEXT_COLOR,
	"turquoise"=>DARK_TEXT_COLOR,
	"mediumturquoise"=>DARK_TEXT_COLOR,
	"darkturquoise"=>DARK_TEXT_COLOR,
	"cadetblue"=>DARK_TEXT_COLOR,
	"darkcyan"=>LIGHT_TEXT_COLOR,
	"teal"=>LIGHT_TEXT_COLOR,
	"greenyellow"=>DARK_TEXT_COLOR,
	"chartreuse"=>DARK_TEXT_COLOR,
	"lawngreen"=>DARK_TEXT_COLOR,
	"lime"=>DARK_TEXT_COLOR,
	"yellowgreen"=>DARK_TEXT_COLOR,
	"limegreen"=>DARK_TEXT_COLOR,
	"springgreen"=>DARK_TEXT_COLOR,
	"mediumspringgreen"=>DARK_TEXT_COLOR,
	"lightgreen"=>DARK_TEXT_COLOR,
	"darkseagreen"=>DARK_TEXT_COLOR,
	"mediumseagreen"=>LIGHT_TEXT_COLOR,
	"olive"=>LIGHT_TEXT_COLOR,
	"olivedrab"=>LIGHT_TEXT_COLOR,
	"forestgreen"=>LIGHT_TEXT_COLOR,
	"seagreen"=>LIGHT_TEXT_COLOR,
	"green"=>LIGHT_TEXT_COLOR,
	"darkolivegreen"=>LIGHT_TEXT_COLOR,
	"darkgreen"=>LIGHT_TEXT_COLOR,
	"cornsilk"=>LIGHT_TEXT_COLOR,
	"lightgoldenrodyellow"=>DARK_TEXT_COLOR,
	"lemonchiffon"=>DARK_TEXT_COLOR,
	"palegoldenrod"=>DARK_TEXT_COLOR,
	"khaki"=>DARK_TEXT_COLOR,
	"darkkhaki"=>DARK_TEXT_COLOR,
	"white"=>DARK_TEXT_COLOR,
	"snow"=>DARK_TEXT_COLOR,
	"ghostwhite"=>DARK_TEXT_COLOR,
	"whitesmoke"=>DARK_TEXT_COLOR,
	"mintcream"=>DARK_TEXT_COLOR,
	"azure"=>DARK_TEXT_COLOR,
	"lavenderblush"=>DARK_TEXT_COLOR,
	"honeydew"=>DARK_TEXT_COLOR,
	"ivory"=>DARK_TEXT_COLOR,
	"floralwhite"=>DARK_TEXT_COLOR,
	"seashell"=>DARK_TEXT_COLOR,
	"oldlace"=>DARK_TEXT_COLOR,
	"lightyellow"=>DARK_TEXT_COLOR,
	"beige"=>DARK_TEXT_COLOR,
	"papayawhip"=>DARK_TEXT_COLOR,
	"antiquewhite"=>DARK_TEXT_COLOR,
	"blanchedalmond"=>DARK_TEXT_COLOR,
	"bisque"=>DARK_TEXT_COLOR,
	"peachpuff"=>DARK_TEXT_COLOR,
	"wheat"=>DARK_TEXT_COLOR,
	"gainsboro"=>DARK_TEXT_COLOR,
	"lightgrey"=>DARK_TEXT_COLOR,
	"silver"=>DARK_TEXT_COLOR,
	"darkgray"=>DARK_TEXT_COLOR,
	"gray"=>DARK_TEXT_COLOR,
	"dimgray"=>LIGHT_TEXT_COLOR,
	"lavender"=>DARK_TEXT_COLOR,
	"lightsteelblue"=>DARK_TEXT_COLOR,
	"lightslategray"=>LIGHT_TEXT_COLOR,
	"slategray"=>LIGHT_TEXT_COLOR,
	"darkslategray"=>LIGHT_TEXT_COLOR,
	"black"=>LIGHT_TEXT_COLOR);
}
?>
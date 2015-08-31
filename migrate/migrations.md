Migrate
=======

#Things to look for when upgrading an old site: 


##Events & Blog
If the site uses Events or the Blog, check on the files supporting the calendar picker. We switched from Epoch Calendar to jQuery UI. Styles needs to be in place to support both the Datepicker and the Timepicker. 

Make sure you have a copy of the neccesary jQuery UI images in /lib/cssimages/, mostly for the timepicker

When checking the Events calendar pages with date pickers, make sure the format of the date is m/d/Y H:i:s so that the widget works when showing a date already set by the system

Don't yet delete the /lib/calendar/calendar_util.js. This needs to be refactored. 


##JS Files
I have refactored the JS and combined as much as I could for the Admin into /lib/js/admin-plugins.js. The old files are still in /lib/js/libs/. If the front end needs some of these, and this is an upgrade, be sure to check those paths. Oh, and use CDN jQuery when possible and please, for the love of all that is sacred, use Modernizr. 


##TinyMCE
Moved to the cloud hosted version for new installs.

For older installs, use the latest version whenever you can. Make sure the * admin.js * is now called mce.js. Admin.php is no longer used. 


##Navigation
Animations in the Admin right Sidebar
These open/close animations have been refactored and turned into CSS transitions instead of JS accordions. We detect for "no-js", and keep all the menus open if there is no JS. 

Future upgrades to the system should do the same for drop downs for Areas, Image and Doc insertion, and portfolio pages (though closing them on click has not been handled yet). 


To Do
=====

Switch over to the mysqli() set of functions. Look into more about how they work and how to leverage them to be stronger. 
Convert myActiveRecord to mysqli? 

Harden more queries with mysqli_real_escape_string? Example: 

````
function FindById( $id="" )
{
	$id = mysqli_real_escape_string( $id, MyActiveRecord::Connection() );
	return MyActiveRecord::FindById( 'Videos', $id );
}
````

Which might require MyActiveRecord to run with MySQLi functions instead. 
http://www.php.net/manual/en/mysqli.real-escape-string.php

mysql_real_escape_string is depreciated as of PHP 5.5.0
http://php.net/manual/en/function.mysql-real-escape-string.php


##Videos: 

models/videos.php: get the remove from content function to work and call it when deleting videos. 


##Gallery Photos and/or Images:
----------------------------

Add a way to access (Gallery) Photos and insert them into content descriptions as well as Images.
OR make it so that images are no longer stored in the database, and Photos take over completely. 
Some photos can be added without being part of a Gallery. 
One photo will still only be associated with one Gallery, though. 

Long term: A way to resize and store more than one version of an image?  At least a small (< 640px wide) as well as the large. 
Thumbnail may be nice as well. 

Multi-image drag and drop uploader. Considering https://github.com/blueimp/jQuery-File-Upload

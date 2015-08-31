<?php

// Upgrade a really old version of the site when we dont have shell access

// Point these to the right place manually
require_once( "../conf.php" );
require_once( "common/activerecord.php" ); 
require_once( "common/model_base.php" ); 
require_once( "models/area.php" ); 
require_once( "models/page.php" ); 
require_once( "models/section.php" ); 
require_once( "models/document.php" ); 
require_once( "models/blog_entries.php" ); 
require_once( "models/user.php" ); 
require_once( "models/videos.php" ); 

function __construct() {
    
    // Deconstruct connection string
    $params = @parse_url( MYACTIVERECORD_CONNECTION_STR ); 
    $host = $params['host'];
	if(isset($params['query'])) {
		$host .= ":{$params['query']}";
	}
    
    // Open connection
    $link = mysql_connect( $host, $params['user'], $params['pass'] );
    if (!$link) {
        die('Could not connect: ' . mysql_error());
    } else {
        // select database
		mysql_select_db( str_replace('/', '', $params['path']), $link); 
        echo "<span style='color:blue'>Connected successfully</span><br />";
    }
	
	
	// 1. Create an Alias table if it does not exist
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'alias'") ) == TRUE ) { 
        echo "1) <span style='color:green'>alias table already exists</span><br />";
    } else { 
        $query1 = "CREATE TABLE `alias` (
                `id` int(11) NOT NULL auto_increment,
                `alias` varchar(255) NOT NULL default '',
                `path` varchar(255) NOT NULL default '', 
                PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";
		if ( mysql_query( $query1 ) ) {
			echo "1) <span style='color:blue'>Alias Table Created</span><br />"; 
		} else {
			echo "<span style='color:red'>Error with query 1</span><br />"; 
        }
    }
	
	// 2. Create a Blogs table if it does not exist
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'blogs'") ) == TRUE ) { 
        echo "2) <span style='color:green'>blogs table already exists</span><br />";
    } else { 
        $query2 = "CREATE TABLE `blogs` (
				`id` int(11) NOT NULL auto_increment,
				`name` varchar(512) NOT NULL,
				`slug` varchar(512) NOT NULL,
				`user_id` int(11) NOT NULL,
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;";
		if ( mysql_query( $query2 ) ) {
			mysql_query( "insert into `blogs` values('1','Your Blog','your_blog','1');" );
			echo "2) <span style='color:blue'>Blogs Table Created</span><br />"; 
		} else {
			echo "<span style='color:red'>Error with query 2</span><br />";
		}
    }
	
	// 3. Create a Blog_Entries table if it does not exist
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'blog_entries'") ) == TRUE ) { 
    	echo "3) <span style='color:green'>Blog Entries table already exists</span><br />"; 
    } else {
		$query3 = "CREATE TABLE `blog_entries` (
				`id` int(11) NOT NULL auto_increment,
				`title` varchar(512) NOT NULL,
				`slug` varchar(512) NOT NULL,
				`content` blob NOT NULL,
				`date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				`template` varchar(255) default null,
				`blog_id` int(11) NOT NULL,
				`public` tinyint(11) NOT NULL default '0',
				`author_id` tinyint(11) NOT NULL default '1',
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;";
		if ( mysql_query( $query3 ) )
			echo "3) <span style='color:blue'>Blog Entries Table Created</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 3</span><br />";
	}
	
	// 4. Check the pages table and add in the parent_page_id column, use for the newer drafts feature
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `pages` LIKE 'parent_page_id'") ) == TRUE ) {
    	echo "4) <span style='color:green'>Pages > parent_page_id column already exists</span><br />"; 
	} else {
    	$query4 = "ALTER TABLE pages ADD `parent_page_id` int(11) default NULL;"; 
		if ( mysql_query( $query4 ) )
			echo "4) <span style='color:blue'>Page Parent ID column added to Pages table</span><br />";
		else
			echo "<span style='color:red'>Error with query 4</span><br />"; 
	}
	
	// 5. Check the PayPalconfig table and make sure the fields are the new names:
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `paypal_config` LIKE 'success_url'") ) == TRUE ) {
    	echo "5) <span style='color:green'>Paypal_config > success_url column did not need to be renamed</span><br />"; 
    } else {
		$query5 = "ALTER TABLE `paypal_config` CHANGE `return` `success_url` text(0);"; 
		if ( mysql_query( $query5 ) )
			echo "5) <span style='color:blue'>PayPal column name RETURN changed to SUCCESS_URL</span><br />";
		else
			echo "<span style='color:red'>Error with query 5</span><br />"; 
	} 
	
	// 6. Check the PayPalconfig table and make sure the fields are the new names:
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `paypal_config` LIKE 'cancel_url'") ) == TRUE ) {
    	echo "6) <span style='color:green'>Paypal_config > cancel_url column did not need to be renamed</span><br />"; 
    } else {
		$query6 = "ALTER TABLE `paypal_config` CHANGE `cancel_return` `cancel_url` text(0);"; 
		if ( mysql_query( $query6 ) )
			echo "6) <span style='color:blue'>PayPal column name CANCEL_RETURN changed to CANCEL_URL</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 6</span><br />";
	}
	
	// 7. Add a new column to the Documents table to allow sorting by type
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `documents` LIKE 'file_type'") ) == TRUE ) {
    	echo "7) <span style='color:green'>Documents > file_type column already exists</span><br />"; 
    } else {
		$query7 = "ALTER TABLE documents ADD `file_type` varchar(6) default NULL;"; 
		if ( mysql_query( $query7 ) )
			echo "7) <span style='color:blue'>Added the &ldquo;file_type&rdquo; column to Documents</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 7</span><br />"; 
	
		// Now, loop through the documents and add in data to the new column
		$documents = Documents::FindAll(); 
		$count = 0; 
		foreach ( $documents as $doc ) {
			$doc->file_type = $doc->get_filetype();
			$doc->save();
			$count++; 
		}
		echo $count." documents have had a file_type added in the database<br />"; 
	} 
	
	// 8. Check the Areas table and add the content field if it does not exist:
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `areas` LIKE 'content'") ) == TRUE ) {
    	echo "8) <span style='color:green'>Areas > content column already exists</span><br />"; 
    } else {
		$query8 = "ALTER TABLE areas ADD `content` text;"; 
		if ( mysql_query( $query8 ) )
			echo "8) <span style='color:blue'>Added the &ldquo;content&rdquo; column to Areas</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 8</span><br />"; 
	} 
	
	// 9. Check the Sections table and add the date fields if they do not exist:
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `sections` LIKE 'date_revised'") ) == TRUE ) {
    	echo "9) <span style='color:green'>Sections > date_revised column already exists</span><br />"; 
    } else {
		$query9 = "ALTER TABLE sections ADD `date_revised` datetime default NULL;"; 
		if ( mysql_query( $query9 ) )
			echo "9) <span style='color:blue'>Added the &ldquo;date_revised&rdquo; column to Sections</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 9</span><br />"; 
	} 
	
	// 10. Check the Items table and add the new fields if they do not exist:
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `items` LIKE 'date_created'") ) == TRUE ) {
    	echo "10) <span style='color:green'>Items > date_created column already exists</span><br />";  
    } else {
		$query10 = "ALTER TABLE items ADD `date_created` datetime default NULL;"; 
		if ( mysql_query( $query10 ) )
			echo "10) <span style='color:blue'>Added the &ldquo;date_created&rdquo; column to Items</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 10</span><br />"; 
	} 
	
	
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `items` LIKE 'date_revised'") ) == TRUE ) {
    	echo "11) <span style='color:green'>Items > date_revised column already exists</span><br />"; 
    } else {
		$query11 = "ALTER TABLE items ADD `date_revised` datetime default NULL;"; 
		if ( mysql_query( $query11 ) )
			echo "11) <span style='color:blue'>Added the &ldquo;date_revised&rdquo; column to Items</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 11</span><br />"; 
	} 
	
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `items` LIKE 'sku'") ) == TRUE ) {
    	echo "12) <span style='color:green'>Items > sku column already exists</span><br />"; 
    } else {
		$query12 = "ALTER TABLE items ADD `sku` varchar(255) default NULL;"; 
		if ( mysql_query( $query12 ) )
			echo "12) <span style='color:blue'>Added the &ldquo;sku&rdquo; column to Items</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 12</span><br />"; 
	} 
	
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `items` LIKE 'price'") ) == TRUE ) {
    	echo "13) <span style='color:green'>Items > price column already exists</span><br />";  
    } else {
		$query13 = "ALTER TABLE items ADD `price` varchar(255) default NULL;"; 
		if ( mysql_query( $query13 ) )
			echo "13) <span style='color:blue'>Added the &ldquo;price&rdquo; column to Items</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 13</span><br />"; 
	} 
	
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `items` LIKE 'taxonomy'") ) == TRUE ) {
    	echo "14) <span style='color:green'>Items > taxonomy column already exists</span><br />"; 
    } else {
		$query14 = "ALTER TABLE items ADD `taxonomy` varchar(255) default NULL;"; 
		if ( mysql_query( $query14 ) )
			echo "14) <span style='color:blue'>Added the &ldquo;taxonomy&rdquo; column to Items</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 14</span><br />"; 
	} 
	
	// 15. Add in the Categories table for Blog Entries
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'categories'") ) == 1 ) { 
        echo "15) <span style='color:green'>categories table already exists</span><br />";
    } else { 
        $query15 = "CREATE TABLE `categories` (
			`id` int(11) NOT NULL auto_increment,
			`name` varchar(255) NOT NULL default '',
			`display_name` varchar(255) NOT NULL default '',
			`content` blob NOT NULL default '',
			PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;"; 
		if ( mysql_query( $query15 ) )
			echo "15) <span style='color:blue'>Categories table has been created</span><br />"; 
		else 
			echo "<span style='color:red'>Error with query 15</span><br />"; 

		if ( mysql_query( "insert into `categories` values('1','uncategorized','Uncategorized','The default category for blog entries that do not get added to any other category');" ) ) 
			echo "<span style='color:green'>The &ldquo;Uncategorized&rdquo; category has been added</span><br />"; 
		else 
			echo "<span style='color:red'>Error with adding the Uncategorized category</span><br />";  
    }
	
	// 16. Create the link table needed between blog_entries and categories
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'blog_entries_categories'") ) == TRUE ) { 
        echo "16) <span style='color:green'>blog_entries_categories table already exists</span><br />";
    } else { 
        $query16 = "CREATE TABLE `blog_entries_categories` (
			`blog_entries_id` int(11) NOT NULL default '0',
			`categories_id` int(11) NOT NULL default '0'
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		if ( mysql_query( $query16 ) )
			echo "16) <span style='color:blue'>Blog Entries and Categories link table has been created</span><br />"; 
		else 
			echo "<span style='color:red'>Error with query 16</span><br />"; 
    }
	
	// 17. Modify the Blog_Entries table to include a public option
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `blog_entries` LIKE 'public'") ) == TRUE ) { 
        echo "17) <span style='color:green'>Blog_entries > public column already exists</span><br />"; 
    } else { 
		$query17 = "ALTER TABLE blog_entries ADD `public` tinyint(11) NOT NULL default '0';"; 
		if ( mysql_query( $query17 ) )
			echo "17) <span style='color:blue'>Added the &ldquo;public&rdquo; column to Blog_Entries</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 17</span><br />"; 
		
		// Loop through the blog entries and make them public by default
		$entries = Blog_Entries::FindAll(); 
		foreach ( $entries as $entry ) {
			$entry->public = 1; 
			$entry->save(); 
		}
	} 
	
	// 18. Modify the Blog_Entries table to include an author_id
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `blog_entries` LIKE 'author_id'") ) == TRUE ) { 
        echo "18) <span style='color:green'>Blog_entries > author_id column already exists</span><br />"; 
    } else { 
		$query18 = "ALTER TABLE blog_entries ADD `author_id` tinyint(11) NOT NULL default '1';"; 
		if ( mysql_query( $query18 ) ) {
			echo "18) <span style='color:blue'>Added the &ldquo;author_id&rdquo; column to Blog_Entries</span><br />"; 
		} else {
			echo "<span style='color:red'>Error with query 18</span><br />"; 
		}
		
		// Loop through the blog entries and add a default author
        $entries = Blog_Entries::FindAll(); 
		foreach ( $entries as $entry ) {
			$entry->author_id = 1; 
			$entry->save(); 
		}
	} 
	
	// 19. Modify the Users table to include a display_name
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `users` LIKE 'display_name'") ) == TRUE ) { 
        echo "19) <span style='color:green'>Users > display_name column already exists</span><br />"; 
    } else { 
		$query19 = "ALTER TABLE users ADD `display_name` varchar(255) NOT NULL default '';"; 
		if ( mysql_query( $query19 ) )
			echo "19) <span style='color:blue'>Added the &ldquo;display_name&rdquo; column to Users</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 19</span><br />"; 
		
		$admin = Users::FindByEmail( "admin@highchairdesign.com" ); 
		$admin->display_name = "J. Hogue"; 
		if ( $admin->save() )
			echo "&ldquo;J. Hogue&rdquo; added to the display_name field for admin@highchairdesign.com<br />"; 
	} 
	
	// 20. Modify the Users table to include a is_staff
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `users` LIKE 'is_staff'") ) == TRUE ) { 
        echo "20) <span style='color:green'>Users > is_staff column already exists</span><br />"; 
    } else { 
		$query20 = "ALTER TABLE users ADD `is_staff` tinyint(4) NOT NULL default '0';"; 
		if ( mysql_query( $query20 ) )
			echo "20) <span style='color:blue'>Added the &ldquo;is_staff&rdquo; column to Users</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 20</span><br />"; 
	} 
	
	// 21. Modify the Documents table to include an item_id so documents can be attached to an item
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `documents` LIKE 'item_id'") ) == TRUE ) { 
        echo "21) <span style='color:green'>Documents > item_id column already exists</span><br />"; 
    } else { 
		$query21 = "ALTER TABLE documents ADD `item_id` int(11);"; 
		if ( mysql_query( $query21 ) )
			echo "21) <span style='color:blue'>Added the &ldquo;item_id&rdquo; column to Documents</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 21</span><br />"; 
	} 
	
	// 22. Modify the Documents table to include a display_order so documents can be attached to an item
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `documents` LIKE 'display_order'") ) == TRUE ) { 
        echo "22) <span style='color:green'>Documents > display_order already exists</span><br />"; 
    } else { 
		$query22 = "ALTER TABLE documents ADD `display_order` int(11) default '1';"; 
		if ( mysql_query( $query22 ) )
			echo "22) <span style='color:blue'>Added the &ldquo;display_order&rdquo; column to Documents</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 22</span><br />"; 
	} 
	
	// 23. Ran into a site that did not have the MailBlast table
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'mailblast'") ) == TRUE ) { 
        echo "23) <span style='color:green'>&ldquo;mailblast&rdquo; table already exists</span><br />";
    } else { 
        $query23 = "CREATE TABLE `mailblast` (
                `id` int(11) NOT NULL auto_increment,
                `date_sent` varchar(75) NOT NULL default 'CURRENT_TIMESTAMP',
                `email_subject` varchar(255) NOT NULL default '',
                `content` text NOT NULL,
                `list_id` int(11) NOT NULL,
                `hash` varchar(36) NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		if ( mysql_query( $query23 ) )
			echo "23) <span style='color:blue'>Mailblast Table Created</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 23</span><br />"; 
    }
	
	// 24. Modify the Mailblast table to include a subject field
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM `mailblast` LIKE 'email_subject'") ) == TRUE ) { 
        echo "24) <span style='color:green'>MailBlast > Email_subject already exists</span><br />"; 
    } else { 
		$query24 = "ALTER TABLE mailblast ADD `email_subject` varchar(255) NOT NULL default '';"; 
		if ( mysql_query( $query23 ) )
			echo "23) <span style='color:blue'>Added the &ldquo;email_subject&rdquo; column to MailBlast</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 24</span><br />"; 
	} 
	
	// 25. Add a video table
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'videos'") ) == TRUE ) { 
        echo "25) <span style='color:green'>Videos table already exists</span><br />";
    } else { 
        $query25 = "CREATE TABLE `videos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `slug` varchar(255) NOT NULL,
              `display_name` varchar(255) NOT NULL,
              `service` varchar(255) DEFAULT NULL,
              `embed` varchar(255) DEFAULT NULL,
              `width` decimal(6,0) DEFAULT NULL,
              `height` decimal(6,0) DEFAULT NULL,
              `gallery_id` tinyint(11) DEFAULT NULL,
              `display_order` tinyint(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;"; 
		if ( mysql_query( $query25 ) )
			echo "25) <span style='color:blue'>Added the &ldquo;video&rdquo; table</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 25</span><br />"; 
    }
    
    // 26. Modify the Photos table to include an video_id field
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM photos LIKE 'video_id'") ) == TRUE ) { 
        echo "26) <span style='color:green'>Photos > video_id already exists</span><br />"; 
    } else { 
		$query26 = "ALTER TABLE photos ADD `video_id` int(11) default '0';"; 
		if ( mysql_query( $query26 ) )
			echo "26) <span style='color:blue'>Added the &ldquo;video_id&rdquo; column to Photos</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 26</span><br />"; 
	} 
	
	// 27. Add a db table for chunks
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'chunks'") ) == TRUE ) { 
        echo "27) <span style='color:green'>Chunks table already exists</span><br />";
    } else { 
        $query27 = "CREATE TABLE `chunks` (
              `id` tinyint(11) NOT NULL AUTO_INCREMENT,
              `slug` varchar(256) NOT NULL,
              `description` text DEFAULT NULL,
              `full_html` tinyint(1) NOT NULL DEFAULT '0',
              `content` text NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;"; 
		if ( mysql_query( $query27 ) )
			echo "27) <span style='color:blue'>Added the &ldquo;chunks&rdquo; table</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 27</span><br />"; 
    }
    
    // 28. Modify the Photos table to include an entry_id field
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM photos LIKE 'entry_id'") ) == TRUE ) { 
        echo "28) <span style='color:green'>Photos > entry_id already exists</span><br />"; 
    } else { 
		$query28 = "ALTER TABLE photos ADD `entry_id` int(11) default '0';"; 
		if ( mysql_query( $query28 ) )
			echo "28) <span style='color:blue'>Added the &ldquo;entry_id&rdquo; column to Photos</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 28</span><br />"; 
	} 
	
	// 29. Modify the Blog_entries table to include a template field
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM blog_entries LIKE 'template'") ) == TRUE ) { 
        echo "29) <span style='color:green'>Blog_entries > template already exists</span><br />"; 
    } else { 
		$query29 = "ALTER TABLE blog_entries ADD `template` varchar(255) default 'default';"; 
		if ( mysql_query( $query29 ) ) {
			echo "29) <span style='color:blue'>Added the &ldquo;template&rdquo; column to Blog_entries</span><br />"; 
		} else {
			echo "<span style='color:red'>Error with query 29</span><br />"; 
        }
	} 
	
	// 30. Modify the Areas table to include a seo_title field
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM areas LIKE 'seo_title'") ) == TRUE ) { 
        echo "30) <span style='color:green'>Areas > seo_title already exists</span><br />"; 
    } else { 
		$query30 = "ALTER TABLE areas ADD `seo_title` varchar(255) default null;"; 
		if ( mysql_query( $query30 ) )
			echo "30) <span style='color:blue'>Added the &ldquo;seo_title&rdquo; column to Areas</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 30</span><br />"; 
	} 
	
	// 31. Add a db table for testimonials
	if( mysql_num_rows( mysql_query("SHOW TABLES LIKE 'testimonials'") ) == TRUE ) { 
        echo "31) <span style='color:green'>Testimonials table already exists</span><br />";
    } else { 
        $query31 = "CREATE TABLE `testimonials` (
                `id` tinyint(11) NOT NULL AUTO_INCREMENT,
                `display_name` varchar(256) NOT NULL,
                `slug` varchar(256) NOT NULL,
                `content` text NOT NULL,
                `attribution` varchar(256) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;"; 
		if ( mysql_query( $query31 ) )
			echo "31) <span style='color:blue'>Added the &ldquo;testimonials&rdquo; table</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 31</span><br />"; 
    }
	
	// 32. Modify the Blog_Entries table to include an excerpt
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM blog_entries LIKE 'excerpt'") ) == TRUE ) { 
        echo "32) <span style='color:green'>Blog_entries > &ldquo;excerpt&rdquo; column already exists</span><br />"; 
    } else { 
		$query32 = "ALTER TABLE blog_entries ADD `excerpt` text NOT NULL;"; 
		if ( mysql_query( $query32 ) )
			echo "32) <span style='color:blue'>Added the &ldquo;excerpt&rdquo; column to Blog_Entries</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 32</span><br />"; 
	} 
	
	// 33. Modify the Testimonials table to include an is_featured boolean
	if( mysql_num_rows( mysql_query("SHOW COLUMNS FROM testimonials LIKE 'is_featured'") ) == TRUE ) { 
        echo "33) <span style='color:green'>Testimonial > &ldquo;is_featured&rdquo; column already exists</span><br />"; 
    } else { 
		$query33 = "ALTER TABLE testimonials ADD `is_featured` tinyint(11) NOT NULL DEFAULT '0';"; 
		if ( mysql_query( $query33 ) )
			echo "33) <span style='color:blue'>Added the &ldquo;is_featured&rdquo; column to Testimonials</span><br />"; 
		else
			echo "<span style='color:red'>Error with query 33</span><br />"; 
	} 
	
	
	mysql_close($link);
}

__construct(); 

?>
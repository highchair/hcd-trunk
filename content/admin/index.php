<?php
	function initialize_page() { }
	
	function display_page_content()
	{
?>
    
    <div id="edit-header" class="dashboard">
		<h1>Dashboard: Definitions and Recent Activity</h1>
	</div>
    
	<style type="text/css">
	<!--
	.dropslide { background-color: transparent; }
	blockquote { padding: 10px 10px 0 10px; margin: 0 0 10px 0; border: 1px solid #ccc; }
	ul.list { list-style: disc; padding: 0 0 0 15px; margin: -8px 0 10px 0; }
	blockquote blockquote { 
		border: 1px solid #ccc; 
		padding: 5px 5px 0 5px; 
		background-color: #eee; 
		margin: 0 0 10px 20px; 
	}
	-->
	</style>
	
	<ul id="dashboard" class="tabs menu">
		<li><a href="#welcome" class="openclose opened">Welcome</a></li>
		<li><a href="#general" class="openclose">General Features</a></li>
		<li><a href="#navdescript" class="openclose">The Navigation</a></li>
		<li><a href="#glossary" class="openclose">Glossary</a></li>
		<li><a href="#custom" class="openclose">Custom</a></li>
	</ul>
	
	<div id="welcome" class="dropslide">
		<h1>Welcome</h1>
		<p>Welcome to your content management system, the HCd&gt;CMS. This system allows access to the text and image content of your website. Choose a tab above for a description of each concept, what it does, and how to use it. Or, choose an action below or from the navigation to the right to get started. </p>
	</div>
	
	<div id="general" class="dropslide" style="display:none;">
		<h2>Areas and Pages</h2>
		<p>The core of the HCd&gt;CMS is the management of Areas and Pages. Areas are containers for pages, and pages are independent objects that can be placed inside more than one area. A page has content, or a description, whereas an Area has only a name.</p>
		<p>The order in which Areas and Pages appear when viewing <a href="<?php echo get_link("admin/list_pages") ?>">List Pages</a> is the order in which they will appear on the site, either right to left in a horizontal navigation or top to bottom in a vertical navigation. They may be reordered by dragging a dropping them into place. </p>
		
		<h2>Email addresses</h2>
		<p><strong>Email addresses in Page Content do not need to be coded.</strong> When the database sees an email address (.com, .net, .org, .biz, .info, etc...) it will encode and hide the email from spam robots, but make it an active link for browsers with Javascript turned on. There is no need to manually make email addresses a link. </p>
		
		<h2 class="red">Success or Failure Feedback:</h2>
		<p>When a page has been successfully executed, a red message will appear above the page content. If there is a failure, error, or a required field was not filled out, a message will appear below or above the problem field. </p>
		
		<h2>Public vs. Not Public</h2>
		<p>Areas, pages, sections, items, blog posts and events all have the ability to be Public or non-public. Simply stated, public means that the page or item will be available to users on the front-end, while non-public is hidden from view and available only to those who have access to this admin portion of the website.</p>
		<p>Public-ness can be inherited, though. An Area can be not public, while all of its Pages are public. As long as those pages do not appear in any other Area, those pages will only be public once the Area is public. Same thing follows for Portfolio Areas and Sections &ndash; children of containers inherit the parent&rsquo;s public property. In this way, it is possible to launch a whole new section of content at one time. </p>
	</div>
	
	<div id="navdescript" class="dropslide" style="display:none;">
		<h2>The Navigation (to the Right):</h2>
		<p>The navigation is broken into three Main Areas. Here is what to expect in each:</a></p>
		
		<h2>Manage Content</h2>
		<p>&ldquo;Content&rdquo; is most of your site. In this section, you will be able to manage Pages, Calendar Content, Blog Content, or E-Newsletters. Options may vary slightly from one installation to another, and may contain custom options as well.</p>
		<p><strong class="red">Pages</strong> = <em>Edit Existing Pages</em> displays all the pages from the database in a table, organized by what Area they are contained within. Pages that are not in a specific area will show up in the Global area. From here you can choose a page to edit, or change the order in which they are displayed. The order is top-down &ndash; in other words, the first page listed will also be first in the navigation, wheteher your navigation is right to left or top down. <em>Add a Page, Add an Area</em> = These links to create new pages or areas and add them to the database.</p>
		<?php if (BLOG_INSTALL) { ?>

		<p><strong class="red">Blog</strong> = <em>Edit Blog Entries</em> displays a date-organized list of past blog entries. Click an entry name to edit it. You may also change the date of an older entry. <em>Add Blog Entry</em> creates a new entry in the database, which you may date as the day it was created or post-date. To edit the &ldquo;name&rdquo; of the Blog, you can edit the name of the Area that represents the blog in the navigation.</p>
		<?php } ?>
		<?php if (PORTFOLIO_INSTALL) { ?>

		<p><strong class="red">Portfolio</strong> = <em>Edit Portfolio Sections and Items</em> displays a list of all the Items, organized by Sections and Portfolio Areas. The display order is top-down, and areas, sections and items are all click-and-draggable. Click on any name to edit that entry, whether it be the name of a Portfolio Area, the Name and Description of a Section, or the Name, Description and Image Gallery for an Item. </p>
		<?php } ?>
		<?php if (CALENDAR_INSTALL) { ?>

		<p><strong class="red">Calendar</strong> = <em>Edit Existing Events</em> displays a calendar-formatted list of all the Events in the database. Click an event to edit it, or, use the arrows (to the right and left of the name of the month) to go forward or backward in time. <em>Add a new Event</em> creates an event in the database. <em>Edit Event Types</em> lists the current event types in the database. Click on the name to edit it. An event type is simply a way to organize events for your calendar. You can have as many event types as you like, and customize the colors that they are represented with. Click <em>Add a New Event Type</em> to create a new one.</p>
		<?php } ?>
		
		<h2>Manage Extras</h2>
		<p>&ldquo;Extras&rdquo; are objects that are separate from page content &ndash; by this, we mean things like Images, Documents, Products, Galleries and Videos. They can be inserted into content, but may not be accesible on their own. So we manage them here. </p>
		<p><strong class="red">Images</strong> = <em>Edit Existing Images</em> displays images in a grid by thumbnail and name, listed by the newest image first. Click any thumbnail to edit the image&rsquo;s properties. Click <em>Upload a New Image</em> to add more to the database. </p>
		<blockquote>
			<p><strong>Insert Image tags=</strong> <em>what they look like and what they do:</em></p>
			<ul class="list">
				<li>{{name_of_image{{ :: An image name surrounded by double curly braces. In this case, because they both point left, the image will be floated left. </li>
				<li>}}name_of_image}} :: Both braces point right, therefore the image will be floated right. </li>
				<li>{{name_of_image}} :: Braces point in opposing directions, therefore the image will be centered in the content column and text will <em>not</em> be allowed to flow around it. </li>
			</ul>
		</blockquote>
		
		<p><strong class="red">Documents</strong> = <em>Edit Existing Documents</em> lists documents by name, from newest to oldest. Click any document name to edit the document&rsquo;s properties, or click &ldquo;view&rdquo; to download a copy of that document. Click <em>Upload a New Document</em> to add more to the database. </p>
		<blockquote>
			<p><strong>Insert Document tags=</strong> <em>what they look like and what they do:</em></p>
			<ul class="list">
				<li>{{document:name_of_document{{ :: This is what all tags look like. What they insert ends up looking like this on the front-end= &ldquo;<a href="#">name_of_document</a> (pdf)&ldquo; or whatever type the document may be. </li>
			</ul>
		</blockquote>
		<?php if (GALLERY_INSTALL) { ?>

		<p><strong class="red">Galleries</strong> = <em>Edit Existing Galleries</em> displays a list of Galleries by thumbnail and name. Click any gallery name or image to edit the name, add photos, change the order of photos, edit captions, or delete images. Click <em>Add a New Gallery</em> to add a new name to the database, and then click the name in the <em>Edit Existing</em> list to add photos to it. </p>
		<blockquote>
			<p><strong>Insert Gallery tags=</strong> <em>what they look like and what they do:</em></p>
			<ul class="list">
				<li>{{gallery:name_of_gallery{{, }}gallery:name_of_gallery}}, {{gallery:name_of_gallery}} :: Gallery tags follow the same pattern as image tags. The thumbnail (the first image in the gallery) will be displayed either left, right, or centered above text. If a gallery is centered, it will normally appear larger on the page than if it was floating left or right.  </li>
				<li>{{carousel:name_of_gallery{{ :: In addition, most installs of the HCd&gt;CMS support auto-playing carousels. Try replacing the word &ldquo;gallery&rdquo; in the tag with &ldquo;carousel&rdquo; to see what happens. </li>
				<li>{{random-from-gallery:name_of_gallery{{ :: As the name suggests, this code will randomly select one image from a gallery of images and display it on the page. The curly braces will denote the placement or &ldquo;float&rdquo; of the image.</li>
			</ul>
		</blockquote>
		<?php } ?>
		<?php if (VIDEO_INSTALL) { ?>
		<p><strong class="red">Videos</strong> = <em>Edit Existing Videos</em> displays a list of videos available in the database. Click on any video name to edit the video&rsquo;s properties, including title, unique ID, hosting service, width and height. Click <em>Add a New Video</em> to add more to the database. </p>
		<?php } ?>
		<?php if (PRODUCT_INSTALL) { ?>

		<p><strong class="red">Products</strong> = <em>Edit Existing Products</em> displays a list of Products in the database. Click any product name to edit the product&rsquo;s properties, including price, thumbnail image, and description. Click <em>Add a New Product</em> to add more to the database. </p>
		<?php } ?>
		
		<h2>Admin Controls</h2>
		<p>&ldquo;Admin&rdquo; users have ultimate control over the site, and can manage the level of access that other users have as well. This section holds some of the controls that advanced users may have. </p>
		<?php if (PRODUCT_INSTALL) { ?>

		<p><strong class="red">PayPal</strong> = If your site has Products installed (Manage Extras &gt; Products), then this is where you may edit the information for your PayPal account. Your site may have at least one account, but could have more. <em>Edit Accounts</em> is where these will be listed &ndash; click a name to edit it.</p>
		<?php } ?>
		
		<p><strong class="red">Users</strong> = For any site, users can be Admins (click the box marked &ldquo;Admin?&rdquo;) or they may be just regular users. The difference is that &ldquo;Admins&rdquo; have ultimate control over content &ndash; &ldquo;Admins&rdquo; can delete pages, areas, blog entries, etc... &ldquo;Admins&rdquo; can also create, edit and delete users as well as edit PayPal accounts. Regular users (&ldquo;non-admins&rdquo;) can only edit page, event, or blog content, and upload and edit images, documents or galleries. They can not delete content.</p>
	
	</div>
	
	<div id="glossary" class="dropslide" style="display:none;">
		<h2>Glossary:</h2>
		<dl>
			<dt>Areas</dt>
			<dd>An Area in the HCd&gt;CMS is a grouping of pages &ndash; it is not a page itself, but a way to organize pages into specific groups of content. </dd>
			
			<dt>Global Area</dt>
			<dd>When we say &ldquo;Global Area&rdquo;, or &ldquo;Global Page&rdquo;, that means that it is the first page people see before a choice is made to navigate somewhere else (a home page, or the first page of an &ldquo;area&rdquo;). Sometimes pages that do not have a home of their own will exist in the &ldquo;Global&rdquo; area. </dd>
			
			<dt>Pages</dt>
			<dd>A page exists in the database with specific content. Pages can get added to one or more areas, or be global. </dd>
			
			<dt>Orphans</dt>
			<dd>Any child element without a parent &ndash; A Page without an Area, a Section without an Area, or an Item without a Section.</dd>
		<?php if (PORTFOLIO_INSTALL) { ?>

			<dt>Portfolio Area</dt>
			<dd>A Portfolio Area is similar to an area, but it contains &ldquo;Sections&rdquo; and &ldquo;Items&rdquo; instead of pages. The Portfolio Area will also appear in the list of Areas and Pages, allowing you to organize it into the structure of the rest of your site however you like. The Portfolio Area may simply be called &ldquo;Portfolio&rdquo;, and you may only need one. A Portfolio Area can have a description if your templates need it.</dd>
			
			<dt>Portfolio Section</dt>
			<dd>A Portfolio Section acts just like an Area. You may not need a bunch of these to organize your portfolio, but you need at least one. Unlike an Area, though, a Section can have a description like a page might have.</dd>
			
			<dt>Portfolio Item</dt>
			<dd>An Item is more like a page, with a title and description, and a thumbnail and a Gallery of images attached to it. In this way, an Item is like a Page and a Gallery combined into one object.</dd>
		<?php } ?>
		<?php if (GALLERY_INSTALL) { ?>

			<dt>Galleries</dt>
			<dd>Galleries are simply a collection of images, and the order of images in a gallery can be changed at any time. Images in galleries, however, can not be easily pulled from one and added to another. The source image would need to be uploaded again, so, it is a good idea to keep a version of these images on your computer for later use. </dd>
		<?php } ?>
		<?php if (VIDEO_INSTALL) { ?>
		    
		    <dt>Videos</dt>
			<dd>Videos are managed through the CMS as well. The embed codes are managed in one place, so if they need to change, they can be changed from one location and that change will go out to where ever the video is used. Right now, the HCd&gt;CMS supports embed codes for Vimeo and YouTube. Others can be embedded with the full share/embed code from the host service. </dd>
		<?php } ?>
		</dl>
	</div>
	
	<div id="custom" class="dropslide" style="display:none;">
		<h2>Custom Content</h2>
		<p>Since the HCd&gt;CMS is so customizable, your site may have features not explained here.</p>
	</div>
	
	<p>&nbsp;</p>
	<!-- Now start the dashboard -->
	
<?php
	$count_dashes = 0;

	if ( BLOG_INSTALL ) { 
?>
	<div class="dashboard-widget column">
		<h1><?php echo ucwords( BLOG_STATIC_AREA ) ?> Entries</h1>
		<p>
			<a class="hcdbutton" href="<?php echo get_link("admin/list_entries") ?>">List Entries</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/edit_entry/add") ?>">Add Entry</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/list_categories") ?>">List Categories</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/add_category") ?>">Add Category</a>
		</p>
		<h4>Recently Added Entries:</h4>
		<ul>
		<?php 
			$entries = MyActiveRecord::FindBySQL( 'Blog_Entries', "SELECT * FROM blog_entries ORDER BY date DESC LIMIT 5" ); 
			if ( ! empty($entries) ) {
				foreach ( $entries as $entry ) {
					
					$entrypublic = ( $entry->public ) ? "" : "<span class=\"red\">(not public)</span>";
					$entry_date = parseDate( $entry->date, "m/d g:i A" ); 
					echo "<li>
							<a class=\"item-link\" href=\"".get_link("admin/edit_entry/".$entry->id)."\">".$entry->get_title()."</a>
							<span class=\"item-revised\">$entry_date</span>
							<span class=\"item-public\">$entrypublic</span>
						</li>"; 
					
				}
			}
		?>
		</ul>
	</div>
<?php 
		$count_dashes++;
	}
	
	if ( PORTFOLIO_INSTALL ) { 
?>
	<div class="dashboard-widget column">
		<h1>Portfolio</h1>
		<p>
			<a class="hcdbutton" href="<?php echo get_link("admin/portfolio_list") ?>">List Items</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/portfolio_add_item") ?>">Add Item</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/portfolio_add_section") ?>">Add Section</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/portfolio_add_area") ?>">Add Area</a>
		</p>
		<h4>Recently Added/Edited Items:</h4>
		<ul>
		<?php 
			$items = Items::FindThisMany( 7 ); // BROKEN when the database is missing fields (prolly date_created, date_revised)
			if ( ! empty($items) ) {
				foreach ( $items as $theitem ) {
					$itempublic = ( $theitem->public ) ? "" : "<span class=\"red\">(not public)</span>";
					$section = $theitem->theSection(); 
					$sectionname = ( empty($section->name) ) ? "orphan_section" : $section->name; 
					$item_revised = ( empty($theitem->date_revised) ) ? '' : formatDateTimeView( $theitem->date_revised, "m/d g:i A" ); 
					echo "<li>
							<a class=\"item-link\" href=\"".get_link("admin/portfolio_edit/$sectionname/".$theitem->id)."\">".$theitem->get_title()."</a>
							<span class=\"item-public\">$itempublic</span>
							<span class=\"item-revised\">$item_revised</span>
						</li>"; 
				}
			}
		?>
		</ul>
	</div>
<?php 
		$count_dashes++; 
	} 
	if ( $count_dashes == 2 ) echo "<div class=\"clearleft\"></div>\n"; 
?>
			
	<div class="dashboard-widget column">
		<h1>Pages</h1>
		<p>
			<a class="hcdbutton" href="<?php echo get_link("admin/list_pages") ?>">List Pages</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/add_page") ?>">Add Page</a>
			<a class="hcdbutton" href="<?php echo get_link("admin/edit_page/add") ?>">Add Area</a>
			<?php $chunks = Chunks::FindAll(); if ( count($chunks) > 0 ) { ?>
			<a class="hcdbutton" href="<?php echo get_link("admin/list_pages#chunks-list") ?>">List Chunks</a>
			<?php } ?>
		</p>
		<h4>Recently Added Pages:</h4>
		<ul>
		<?php 
			$pages = Pages::FindAll( "id DESC" ); 
			if ( ! empty($pages) ) {
				$counter = 0; 
				foreach ( $pages as $thepage ) {
					
					if ( $counter < 5 ) {
						$pagepublic = ( $thepage->public ) ? "" : "<span class=\"red\">(not public)</span>";
						//$item_revised = ( empty($theitem->date_revised) ) ? '' : formatDateTimeView( $theitem->date_revised, "m/d g:i A" ); 
						echo "<li>
								<a class=\"item-link\" href=\"".get_link("admin/edit_page/".$thepage->id)."\">".$thepage->get_title()."</a>
								<span class=\"item-public\">$pagepublic</span>
							</li>"; 
						$counter++; 
					}	
				}
			}
		?>
		</ul>
		<h4>Quick select</h4>
		<?php quick_link(); ?>
	</div>				
<?php
	$count_dashes++; 
	
	$classes = "dashboard-widget column";
	if ( ! is_odd($count_dashes) ) {
		$classes = "quick_buttons";
		echo "<div class=\"clearleft\"></div>\n";
	}
?>
			
	<div class="<?php echo $classes ?>">
		<h1>Manage Extras:</h1>
		<p>
			<?php if ( CALENDAR_INSTALL ) { ?>
			<strong>Event Calendar:</strong> 
			<a class="hcd_button" href="<?php echo get_link("admin/list_events"); ?>">Edit Events</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/add_event"); ?>">Add an Event</a><br /><br />
			
			<?php } if ( GALLERY_INSTALL ) { ?>
			<strong>Image Galleries:</strong> 
			<a class="hcd_button" href="<?php echo get_link("admin/list_galleries"); ?>">Edit Galleries</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/add_gallery"); ?>">Add Gallery</a><br /><br />
			
			<?php } if ( PRODUCT_INSTALL ) { ?>
			<strong>Products:</strong> 
			<a class="hcd_button" href="<?php echo get_link("admin/list_products"); ?>">Edit Products</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/add_product"); ?>">Add a Product</a><br /><br />
			
			<?php } if (VIDEO_INSTALL) { ?>
            <strong>Video:</strong> 
            <a class="hcd_button" href="<?php echo get_link("admin/list_videos"); ?>">Edit Videos</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/edit_video/add"); ?>">Add a Video</a><br /><br />
			
    		<?php } if (TESTIMONIAL_INSTALL) { ?>
    		<strong>Testimonials:</strong> 
            <a class="hcd_button" href="<?php echo get_link("admin/list_testimonials"); ?>">Edit Testimonials</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/edit_testimonial/add"); ?>">Add a Testimonial</a><br /><br />
    		
    		<?php } ?>
    		<strong>Images:</strong> 
			<a class="hcd_button" href="<?php echo get_link("admin/list_images"); ?>">Edit Images</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/add_image"); ?>">Upload an Image</a><br /><br />
			
			<strong>Documents:</strong> 
			<a class="hcd_button" href="<?php echo get_link("admin/list_documents"); ?>">Edit Documents</a> 
			<a class="hcd_button" href="<?php echo get_link("admin/add_document"); ?>">Upload a Document</a>
		</p>
	</div>
	
	<div class="clearleft"></div>
					
	<!-- TO DO
		Break out the insert patterns into its own tab for images, documents and galleries. Explain galleries better.
	-->
	
	<?php include_once( snippetPath("admin_tutorial_list") ); ?>
<?php } ?>
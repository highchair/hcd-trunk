<?php
	function initialize_page()
	{
		
	}
	
	function display_page_content()
	{
?>

    <div id="edit-header" class="portareanav">
		<h1>List of HCd&gt;CMS Options</h1>
	</div>
					
	<p>The HCd&gt;CMS is built with growth and scalability in mind. Below is a list of available features for the system, including ones that are not currently installed. If you would like to purchase one of these features, please get in touch to let us know. </p>
	
	<h2>Manage Content</h2>
	<ul id="listitems" class="text-list">
        <li class="turned_on"><strong>Page and Ares Management</strong> &ndash; A base CMS feature &ndash; always on. </li>
        <li class="turned_on"><strong>Image and Document Management</strong> &ndash; A base CMS feature &ndash; always on. </li>
        <?php if ( SUB_PAGES ) { ?>
        <li class="turned_on indent">Optional sub-pages are turned on. </li>
        <?php } else { ?>
        <li class="turned_off indent">Optional sub-pages are available. Sub-pages are an additional tier of navigation. </li>
        <?php } ?>
        
        <?php if ( ALIAS_INSTALL ) { ?>
        <li class="turned_on"><strong>Alias</strong> &ndash; Turned on. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Alias</strong> &ndash; Optional Alias&rsquo; are available. Alias&rsquo;s are quick links from the root of the site to another page, area, event, or anything else. For example, <em>yoursite.com/alias-link</em> can be the quick way to get to <em>yoursite.com/events/calendar/2012/11/28/event</em>. Good for page links that need to be persistent, while the destination may need to change from time to time. </li>
        <?php } ?>
        
        <?php if ( VIDEO_INSTALL ) { ?>
        <li class="turned_on"><strong>Video Management</strong> &ndash; A central way to manage the embed codes for YouTube- and Vimeo-hosted video media. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Video Management</strong> &ndash; Optional Video Management is available. A central way to manage the embed codes for YouTube- and Vimeo-hosted video media. </li>
        <?php } ?>
        
        <?php if ( GALLERY_INSTALL ) { ?>
        <li class="turned_on"><strong>Gallery Management</strong> &ndash; A base CMS feature that manages groups of images as galleries. Can be displayed as slideshows, as a single randomly selected image, or as an auto-playing carousel. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Gallery Management</strong> &ndash; Optional Gallery Management is available. Galleries are simply collections of images with captions and a myriad of display options. </li>
        <?php } ?>
        
        <?php if ( PORTFOLIO_INSTALL ) { ?>
        <li class="turned_on"><strong>Portfolio Management</strong> &ndash; Turned on. 
            This installation is configured with the following options: 
            <?php if ( ITEM_SKU ) { ?>Item SKUs<?php } else { ?>Items SKUs (turned off)<?php } ?>,
            <?php if ( ITEM_PRICE ) { ?>Item Prices<?php } else { ?>Items Prices (turned off)<?php } ?>,
            <?php if ( ITEM_TAXONOMY ) { ?>Item Taxonomy<?php } else { ?>Items Taxonomy (turned off)<?php } ?>,
            <?php if ( ITEM_DOCUMENTS ) { ?>Documents attached to Items<?php } else { ?>Documents attached to Items (turned off)<?php } ?>,
            and <?php if ( ITEM_GALLERY_INSERT ) { ?>Item Galleries inserted into Content<?php } else { ?>Item Galleries inserted into Content (turned off)<?php } ?>.
        </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Portfolio Management</strong> &ndash; Optional Portfolio Management is available. A portfolio has its own set of areas as well as Sections (like a category), Items (like a page with a Gallery) and an additional taxonomy. Items have sortable galleries and documents with optional Prices and SKU numbers. Additional templates for front-end display are required. </li>
        <?php } ?>
        
        <?php if ( CALENDAR_INSTALL ) { ?>
        <li class="turned_on"><strong>Calendar</strong> &ndash; Turned on. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Calendar</strong> &ndash; Optional Calendar content types are available. A calendar can control Events and Event Types, and a custom option for managing Venues is also possible. Single day events and multi-day events are included. Additional templates for front-end display are required. </li>
        <?php } ?>
        
        <?php if ( BLOG_INSTALL ) { ?>
        <li class="turned_on"><strong>Blog</strong> &ndash; Turned on. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Blog</strong> &ndash; Optional Blog is available. Our Blog supports multiple authors (any user with a login can be an author), custom Category taxonomy and the insertion of image, document, gallery content and products. Additional templates for front-end display are required. </li>
        <?php } ?>
        
        <?php if ( PRODUCT_INSTALL ) { ?>
        <li class="turned_on"><strong>Products</strong> &ndash; Turned on. Other shopping carts (other than PayPal) are available, inquire about pricing.</li>
        <?php } else { ?>
        <li class="turned_off"><strong>Products</strong> &ndash; Optional Products are available as an insertable feature, similar to the way that IMages or Documents can be inserted into page content. A PayPal account is needed to manage the shopping experience. Other shopping carts are available, inquire about pricing.</li>
        <?php } ?>
        
        <?php if ( BLAST_INSTALL ) { ?>
        <li class="turned_on"><strong>Email Blast Management</strong> &ndash; Turned on. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>Email Blast Management</strong> &ndash; Optional Email Blast Management is available. Our email blaster supports multiple mailing lists with custom templates, sign up forms and personal user preferences. Additional template design and testing is needed in order to properly implement this feature.  </li>
        <?php } ?>
        
        <?php if ( RSS_INSTALL ) { ?>
        <li class="turned_on"><strong>RSS Feeds</strong> &ndash; Turned on. </li>
        <?php } else { ?>
        <li class="turned_off"><strong>RSS Feeds</strong> &ndash; Optional RSS Feeds are available for content types &ndash; pages, events, blog posts. Additional or custom RSS Feeds are available as custom options. </li>
        <?php } ?>
        
        <li class="turned_on"><strong>Google XML Sitemap</strong> &ndash; A base CMS feature &ndash; always on. </li>
        
        <?php 
            if ( USER_ROLES ) { 
                $userroles = explode( ",", USER_ROLES ); 
                //print_r($userroles); 
                $user_count = count($userroles); 
                $users = ""; 
                foreach( $userroles as $key=>$value ) {
                    $users .= ucwords( $value ).", "; 
                }
        ?>
        
        <li class="turned_on"><strong>User Roles</strong> &ndash; This installation uses <?php echo $user_count ?> user roles: <?php echo substr( $users, 0, -2 ) ?>. Two roles are included with the base installation. </li>
        <?php } ?>
	</ul>
	
	<div id="edit-footer" class="portareanav clearfix">
		<p><strong>Running version HCd&gt;CMS 2.6 v<?php echo SVN_VERSION ?></strong></p>
		<p>Contact J. at Highchairdesign.com for an estimate on an upgrade to your system. </p>
	</div>
<?php } ?>
SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

/* Query 3 */
CREATE TABLE `areas` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `display_name` varchar(255) NOT NULL default '',
  `seo_title` varchar(255) NOT NULL default '',
  `content` text NOT NULL, 
  `display_order` int(11) NOT NULL default '1',
  `template` varchar(100) NOT NULL default 'default',
  `public` tinyint(4) default '1',
  `type` varchar(100) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `areas` values('1','index','(front page)','','','1','default','1','');
insert into `areas` values('2','orphans_portfolio','Portfolio Orphans','','','1','portfolio','0','');
insert into `areas` values('3','site_blog','Blog','','','1','blog','1','');

/* Query 7 */
CREATE TABLE `areas_pages` (
  `areas_id` int(11) NOT NULL default '0',
  `pages_id` int(11) NOT NULL default '0',
  `display_order` int(11) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `areas_pages` values('1','1','1');

/* Query 9 */
CREATE TABLE `blogs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(512) NOT NULL,
  `slug` varchar(512) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `blogs` values('1','Your Blog','your_blog','2');

/* Query 11 */
CREATE TABLE `blog_entries` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(512) NOT NULL,
  `slug` varchar(512) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `content` text NOT NULL,
  `excerpt` text NOT NULL,
  `public` tinyint(11) NOT NULL default '0',
  `template` varchar(255) default null,
  `author_id` int(11) NOT NULL default '1',
  `blog_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `blog_entries` values('','Sample Blog Entry','sample-blog-entry','2014-06-01 14:00:00','<p>Here is your first blog post. Edit it or delete it or make it not public... either way, get blogging!&nbsp;</p>','','1','default','1','1');

/* Query 13 */
CREATE TABLE `categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `display_name` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `categories` values('1','uncategorized','Uncategorized','<p>Categories can have descriptions, but your template may or may not display them.&nbsp;</p>'); 

/* Query 15 */
CREATE TABLE `blog_entries_categories` (
  `blog_entries_id` int(11) NOT NULL default '0',
  `categories_id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 16 */
CREATE TABLE `calendar` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `calendar` values('1','Events');

/* Query 18 */
CREATE TABLE `mailblast` (
  `id` int(11) NOT NULL auto_increment,
  `date_sent` varchar(75) NOT NULL default 'CURRENT_TIMESTAMP',
  `hash` varchar(36) NOT NULL,
  `content` text NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 19 */
CREATE TABLE `nlemails` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(256) NOT NULL,
  `first_name` varchar(256) default NULL,
  `last_name` varchar(256) default NULL,
  `address1` varchar(256) default NULL,
  `address2` varchar(256) default NULL,
  `city` varchar(256) default NULL,
  `state` varchar(100) default NULL,
  `zip` varchar(20) default NULL,
  `phone` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 20 */
insert into `nlemails` values('1','jh@highchairdesign.com','','','','','','','','');

/* Query 21 */
CREATE TABLE `nlemails_nllists` (
  `nlemails_id` int(11) NOT NULL,
  `nllists_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `nlemails_nllists` values('1','1');

/* Query 23 */
CREATE TABLE `nllists` (
  `id` int(11) NOT NULL auto_increment,
  `display_name` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `template` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `public` int(11),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `nllists` values ('1','Highchair','highchair','blank','<p>The Highchair test list... not be used publically.</p>','0');

/* Query 25 */
CREATE TABLE `documents` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_type` varchar(6) default NULL,
  `item_id` int(11) default NULL,
  `display_order` int(11) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 26 */
CREATE TABLE `eventperiods` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `eventperiods` values('1','Single Day'),
 ('3','Multi-Day');

/* Query 28 */
CREATE TABLE `events` (
  `id` int(11) NOT NULL auto_increment,
  `calendar_id` int(11) NOT NULL,
  `eventtype_id` int(11) default NULL,
  `date_start` datetime NOT NULL,
  `time_start` time NOT NULL default '04:00:00',
  `date_end` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_end` time NOT NULL default '04:00:00',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `eventperiod_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 29 */
CREATE TABLE `eventtypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL,
  `text_color` varchar(100) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `eventtypes` values('1','Event','white','black','1');

/* Query 31 */
CREATE TABLE `galleries` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 32 */
CREATE TABLE `images` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `original` longblob NOT NULL,
  `thumbnail` longblob,
  `mime_type` varchar(50) default NULL,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 33 */
CREATE TABLE `items` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `content` text,
  `display_name` varchar(255) default NULL,
  `template` varchar(100) default '',
  `content_file` varchar(255) NOT NULL default '',
  `display_order` int(11) default '1',
  `sku` varchar(255) default NULL, 
  `price` varchar(255) default NULL, 
  `taxonomy` varchar(255) default NULL, 
  `public2` tinyint(11) NOT NULL default '0',
  `public` tinyint(11) NOT NULL default '0',
  `date_created` datetime default NULL,
  `date_revised` datetime default NULL, 
  `thumbnail` longblob,
  `mime_type` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `items_keywords` (
  `id` int(11) NOT NULL auto_increment,
  `items_id` int(11) NOT NULL,
  `keywords_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 35 */
CREATE TABLE `items_sections` (
  `sections_id` int(11) NOT NULL default '0',
  `items_id` int(11) NOT NULL default '0',
  `display_order` int(11) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `areas_sections` (
  `areas_id` int(11) NOT NULL default '0',
  `sections_id` int(11) NOT NULL default '0',
  `display_order` int(11) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 37 */
CREATE TABLE `pages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `content` text,
  `display_name` varchar(255) default NULL,
  `template` varchar(100) default '',
  `content_file` varchar(255) NOT NULL default '',
  `display_order` int(11) default '1',
  `public` tinyint(11) NOT NULL default '0',
  `parent_page_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `pages` values('1','index','<h1>Hello There</h1><p>Welcome to a fresh install of the HCd system. This is your home page content, go ahead and edit it to reflect what you want to do with it. Happy site building! </p>','Welcome to your new site','default','','1','1',null);

/* Query 39 */
CREATE TABLE `paypal_config` (
  `id` int(11) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `success_url` varchar(300) NOT NULL,
  `cancel_url` varchar(300) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `paypal_config` values('0','Paypal Account','default@default.us','','');

CREATE TABLE `paypal_items` (
  `id` int(11) NOT NULL auto_increment,
  `item_table` varchar(150) NOT NULL,
  `item_id` int(5) NOT NULL,
  `paypal_id` int(5) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 42 */
CREATE TABLE `photos` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(255) default NULL,
  `caption` varchar(2000) NOT NULL,
  `display_order` int(11) NOT NULL default '1',
  `gallery_id` int(11) default NULL,
  `entry_id` int(11) default null,
  `video_id` int(11) default null, 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 43 */
CREATE TABLE `product` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(350) NOT NULL,
  `display_name` varchar(500) NOT NULL,
  `price` varchar(10) NOT NULL,
  `description` text,
  `thumbnail` longblob,
  `image` longblob,
  `mime_type` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 44 */
CREATE TABLE `recurrence` (
  `id` int(11) NOT NULL auto_increment,
  `event_id` int(11) NOT NULL,
  `day` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `modifier` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 45 */
CREATE TABLE `sections` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `display_name` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `display_order` int(11) NOT NULL default '1',
  `template` varchar(100) NOT NULL default 'default',
  `public` tinyint(4) default '1',
  `type` varchar(100) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 46 */
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `display_name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `is_admin` tinyint(4) NOT NULL default '0',
  `is_staff` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `users` values('1','J. Hogue','admin@highchairdesign.com','b0dfba6f2a55ae6d01f7e94a02c0ab79ab3edeec','1','0');

/* Query 48 */
CREATE TABLE `alias` (
  `id` int(11) NOT NULL auto_increment,
  `alias` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 49 */
CREATE TABLE `videos` (
  `id` int(11) NOT NULL auto_increment,
  `slug` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `service` varchar(255) DEFAULT NULL,
  `embed` varchar(255) DEFAULT NULL,
  `width` decimal(6,0) DEFAULT NULL,
  `height` decimal(6,0) DEFAULT NULL,
  `gallery_id` tinyint(11) DEFAULT NULL,
  `display_order` tinyint(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 50 */
CREATE TABLE `chunks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(256) NOT NULL,
  `description` text DEFAULT NULL,
  `full_html` tinyint(1) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Query 51 */
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(256) NOT NULL,
  `slug` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `attribution` varchar(256) DEFAULT NULL,
  `is_featured` tinyint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
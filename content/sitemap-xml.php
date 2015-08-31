<?php
/*
COPYRIGHT (C) 2005 BY SMART-IT-CONSULTING.COM
* Do not remove this header
* This program is provided AS IS
* Use this program at your own risk
* Don't publish this code, link to http://www.smart-it-consulting.com/ instead
*/

function initialize_page() { }

function display_page_content()
{
	$isoLastModifiedSite = "";
	$newLine = "\n";
	$indent = " ";
	$rootUrl = "http://".SITE_URL;
	
	$xmlHeader = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>$newLine";
	
	$urlsetOpen = "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\"
	xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
	xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84
	http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">$newLine";
	$urlsetValue = "";
	$urlsetClose = "</urlset>";
	
	function makeUrlString ($urlString) {
	    return htmlentities($urlString, ENT_QUOTES, 'UTF-8');
	}
	
	function makeIso8601TimeStamp ($dateTime) {
	    if (!$dateTime) {
	        $dateTime = date('Y-m-d H:i:s');
	    }
	    if (is_numeric(substr($dateTime, 11, 1))) {
	        $isoTS = substr($dateTime, 0, 10) ."T"
	                 .substr($dateTime, 11, 8) ."+00:00";
	    }
	    else {
	        $isoTS = substr($dateTime, 0, 10);
	    }
	    return $isoTS;
	}
	
	function makeUrlTag ($url, $modifiedDateTime, $changeFrequency, $priority) {
	    GLOBAL $newLine;
	    GLOBAL $indent;
	    GLOBAL $isoLastModifiedSite;
	    $urlOpen = "$indent<url>$newLine";
	    $urlValue = "";
	    $urlClose = "$indent</url>$newLine";
	    $locOpen = "$indent$indent<loc>";
	    $locValue = "";
	    $locClose = "</loc>$newLine";
	    $lastmodOpen = "$indent$indent<lastmod>";
	    $lastmodValue = "";
	    $lastmodClose = "</lastmod>$newLine";
	    $changefreqOpen = "$indent$indent<changefreq>";
	    $changefreqValue = "";
	    $changefreqClose = "</changefreq>$newLine";
	    $priorityOpen = "$indent$indent<priority>";
	    $priorityValue = "";
	    $priorityClose = "</priority>$newLine";
	
	    $urlTag = $urlOpen;
	    $urlValue     = $locOpen .makeUrlString("$url") .$locClose;
	    if ($modifiedDateTime) {
	     $urlValue .= $lastmodOpen .makeIso8601TimeStamp($modifiedDateTime) .$lastmodClose;
	     if (!$isoLastModifiedSite) { // last modification of web site
	         $isoLastModifiedSite = makeIso8601TimeStamp($modifiedDateTime);
	     }
	    }
	    if ($changeFrequency) {
	     $urlValue .= $changefreqOpen .$changeFrequency .$changefreqClose;
	    }
	    if ($priority) {
	     $urlValue .= $priorityOpen .$priority .$priorityClose;
	    }
	    $urlTag .= $urlValue;
	    $urlTag .= $urlClose;
	    return $urlTag;
	}
	
	if (BLOG_INSTALL) {
		$blog_id = "1"; 
		$the_blog = Blogs::FindById($blog_id);
	}
	
	if (PORTFOLIO_INSTALL) {
		$sections = Sections::FindPublicSections();
	}
	
	$areas = Areas::FindPublicAreas();
	//$pageLastModified = date('Y-m-d H:i:s');
	// Today last month instead of today's date
	$pageLastModified = date( "Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")-1, date("d"), date("Y")) ); 
	
	$pageChangeFrequency = "monthly";
	$pagePriority = .6;
	$urlsetValue .= makeUrlTag( $rootUrl, $pageLastModified, $pageChangeFrequency, 1 );
	
	foreach ($areas as $area) 
	{	
		if ($area->name == "site_blog" && BLOG_INSTALL) 
		{
			$urlsetValue .= makeUrlTag( $rootUrl.get_link(BLOG_STATIC_AREA."/view/"), $pageLastModified, $pageChangeFrequency, 1 );
			$entries = $the_blog->getEntries();
			foreach ($entries as $entry)
			{
				$urlsetValue .= makeUrlTag( $rootUrl.$entry->get_url(), $entry->get_pubdate( "Y-m-d H:i:s" ), $pageChangeFrequency, $pagePriority );
			}
		} 
		else if(strstr($area->name, "-portfolio") && PORTFOLIO_INSTALL)
		{
			$first = true;
			foreach($sections as $index => $menu_section)
			{
				if ( $first ) { $pagePriority = 1; $first = false; } else { $pagePriority = .6; }
				if ( $menu_section->name != "" )
				{
					$changedate = ( ! is_null($menu_section->date_revised) ) ?  formatDateTimeView( $menu_section->date_revised, "Y-m-d H:i:s" ) : $pageLastModified; 
					$urlsetValue .= makeUrlTag( $rootUrl.$menu_section->get_url( $area ), $changedate, $pageChangeFrequency, $pagePriority );
					$items = $menu_section->findItems();
					foreach ( $items as $item )
					{
						$changedate = ( ! is_null($item->date_revised) ) ?  formatDateTimeView( $item->date_revised, "Y-m-d H:i:s" ) : $pageLastModified; 
						$urlsetValue .= makeUrlTag( $rootUrl.$item->get_url( $area, $menu_section ), $changedate, $pageChangeFrequency, $pagePriority );
					}
				}
			}
		} 
		else if($area->name != "") 
		{
			$pages = $area->findPages();
			$first = true;
			foreach ($pages as $page)
			{
				if ($first) { $pagePriority = 1; $first = false; } else { $pagePriority = .6; }
				$urlsetValue .= makeUrlTag( $rootUrl.$page->get_url( $area ), $pageLastModified, $pageChangeFrequency, $pagePriority );
			}
		}
	}
	
	header('Content-type: application/xml; charset="utf-8"',true);
	print "$xmlHeader
	$urlsetOpen
	$urlsetValue
	$urlsetClose";
}
?>
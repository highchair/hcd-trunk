<?php
    /* Control the markup pattern
     * @accepts: an image object, direction for float and a special class if needed
     * @returns: full figure with img tag an optional anchor or caption
     */
    function render_hcd_img_wrapper( $image, $floatdirection='none', $special=null ) {
        
        // We only use this function on object we have already checked. This check needed again? 
        if ( is_object($image) ) {
            
            $addclass = ( ! empty($special) ) ? ' '.$special : null; 
            // Too bad... <figure? is block level and when inside a <p>, messes with rendering. 
            // $output = '<figure class="hcd-photo hcd-photo__'.$floatdirection.''.$addclass.'">'; 
            $output = '<span class="hcd-photo hcd-photo__'.$floatdirection.''.$addclass.'">'; 
            $url = $alt = $caption = $urlclose = null; 
            
            if ( get_class($image) == "Images" ) {
                
                // Make a meaningful alt tag
                $alt = ( ! empty($image->description) ) ? $image->description : $image->title; 
                $caption = ( ! empty($image->description) ) ? '<span class="hcd-photo--caption">'.cleanupSpecialChars($image->description).'</span>' : null; 
                
                // Check to see if we need to render a URL
                if ( substr($image->description, 0, 4) == "http" ) {
                    $url = '<a class="hcd-photo--link" href="'.$image->description.'" title="'.$image->title.'">'; 
                    $urlclose = '</a>'; 
                    
                    // Redefine caption because we now know that if it was description, it was a URL
                    $caption = null; 
                } 
            } elseif ( get_class($image) == "Photos" ) {
                
                // Make a meaningful alt tag
                $alt = ( ! empty($image->caption) ) ? $image->caption : $image->filename; 
                $caption = ( ! empty($image->caption) ) ? '<span class="hcd-photo--caption">'.cleanupSpecialChars($image->caption).'</span>' : null; 
            }
            
            $output .= $url . '<img src="'.$image->get_url().'" alt="'.cleanupSpecialChars($alt).'" />' . $urlclose; 
            $output .= $caption . '</span>'; 
        } else {
            return null; 
        }
        return $output; 
    }
    
    
    /*
     * Render one image inserted into content.
     */
    function image_display($content_to_display)
    {
    	$pattern_recog = array(
    	    "left" => array("{","{"), 
    	    "right" => array("}","}"), 
    	    "center" => array("{","}")
        );
    	
    	foreach ( $pattern_recog as $float => $direction ) {
    		$imagePattern = "*".$direction[0]."{2}([A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
    		$imageIds = getFilterIds( $content_to_display, $imagePattern );
    		$images = Array();
    		
    		foreach($imageIds as $imageId) {
    			$images[] = Images::FindByName( $imageId );
    		}
    		
    		foreach( $images as $key => $image ) {
    			if ( is_object($image) ) {
    				
    				$replacement = render_hcd_img_wrapper( $image, $float ); 
    				$content_to_display = updateContent( $content_to_display, "*".$direction[0]."{2}{$image->name}".$direction[1]."{2}*", $replacement );
    			} else {
    				$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Image &ldquo;$imageId&rdquo; not found!</span> ".$content_to_display; 
    			}
    		}
    	}
    	return $content_to_display;
    }
    
    
    /*
     * Grab a gallery slug from content and render one randomly selected image.
     */
    function random_from_gallery_display($content_to_display)
    {
    	$pattern_recog = array(
    	    "left" => array("{","{"), 
    	    "right" => array("}","}"), 
    	    "center" => array("{","}")
        );
    
    	foreach ($pattern_recog as $float => $direction)
    	{
    		$galleryPattern = "*".$direction[0]."{2}(random\-from\-gallery:[A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
    		$gallerySlugs = getFilterIds( $content_to_display, $galleryPattern );
    		$galleries = Array();
    		
    		foreach($gallerySlugs as $slug) {
    			$sploded = explode(":", $slug);
    			$galleries[] = Galleries::FindBySlug($sploded[1]);
    		}
    			
    		foreach($galleries as $key => $gallery) {
    			
    			if ( is_object($gallery) ) {
    				$photos = $gallery->get_photos();
    				shuffle( $photos );
    				$random = array_shift( $photos );
    				
    				$replacement = render_hcd_img_wrapper( $random, $float, 'hcd-photo__random' ); 
    				$content_to_display = updateContent( $content_to_display, "*".$direction[0]."{2}random-from-gallery:{$gallery->slug}".$direction[1]."{2}*", $replacement );
    			} else {
    				$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Gallery &ldquo;{$gallery->slug}&rdquo;not found!</span> ".$content_to_display; 
    			}
    		}
    	}
    	return $content_to_display;
    }

    
    /*
     * Grab a gallery slug from content and render the entire gallery.
     * An external JS call should be made to listen for the .js-gallery trigger.
     * @requires: magnific-popup.js or similar responsive lightbox solution
     */
    function gallery_display($content_to_display)
    {
    	$pattern_recog = array(
    	    "left" => array("{","{"), 
    	    "right" => array("}","}"), 
    	    "center" => array("{","}")
        );
    
    	foreach ($pattern_recog as $float => $direction) {
    		
    		$galleryPattern = "*".$direction[0]."{2}(gallery:[A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
    		$gallerySlugs = getFilterIds($content_to_display, $galleryPattern);
    		$galleries = Array();
    		
    		if ( count($gallerySlugs)>0 ) {
    			$galleriesTagged = Array();
    			
    			foreach ( $gallerySlugs as $slug ) {
    				
    				// check to see if the gallery is followed by a closing 'p' tag
    				$tag_check = substr($content_to_display, strpos($content_to_display, $slug) + strlen($slug), 6);
    				if (substr_count($tag_check, "</p>")) { $galleriesTagged[] = true; } else { $galleriesTagged[] = false; }
    				
    				$sploded = explode(":", $slug);
    				$galleries[] = Galleries::FindBySlug( $sploded[1] );
    			}
    		}
            
            foreach ( $galleries as $key => $gallery ) {
    			
    			if ( is_object($gallery) ) {
    				$replacement = '<figure id="js-gallery_'.$gallery->slug.'" class="hcd-photo hcd-photo__'.$float.' js-gallery" >';
    				$first = true;
    				
    				foreach ( $gallery->get_photos() as $photo ) {
    					
    					if ($first) { $class="hcd-photo--link__show"; $first = false; } else { $class="hcd-photo--link__hide"; }
    					
    					$url = $photo->getPublicUrl(); 
    					$caption = cleanupSpecialChars($photo->caption); 
    					
    					$replacement .= '<a class="hcd-photo--link '.$class.' js-popup" href="'.$url.'" title="'.$caption.'"><img src="'.$url.'" alt="'.$caption.'" /></a>';
    				}
    				
    				$replacement .= '<figcaption class="hcd-photo--caption">'.$gallery->name.' (Click for more)</figcaption></figure>';
    				$content_to_display = updateContent( $content_to_display, "*".$direction[0]."{2}gallery:{$gallery->slug}".$direction[1]."{2}*", $replacement );
    			
    			} else {
    				$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Gallery &ldquo;{$sploded[1]}&rdquo; not found!</span> ".$content_to_display; 
    			}
    		}
    	}
    	return($content_to_display);
    }

    
    /*
     * Grab a gallery slug from content and render a slideshow carousel.
     * An external JS call should be made to listen for the .js-slideshow trigger.
     * @requires: flexslider.js or similar responsive slideshow solution
     */
    function carousel_display($content_to_display)
    {
    	$pattern_recog = array(
    	    "left" => array("{","{"), 
    	    "right" => array("}","}"), 
    	    "center" => array("{","}")
        );
    
    	foreach ($pattern_recog as $float => $direction) {
    		$carouselPattern = "*".$direction[0]."{2}(carousel:[A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
    		$carouselSlugs = getFilterIds($content_to_display, $carouselPattern);
    					
    		foreach ( $carouselSlugs as $slug ) {
    			
    			$galname = end(explode(":", $slug)); 
    			$carousel = Galleries::FindBySlug( $galname );
    			
    			if ( is_object($carousel) ) {
    							
        			// Start the Carousel. Width should be set by _required.css
        			$replacement = '<figure id="carousel_'.$carousel->slug.'" class="hcd-photo hcd-photo__'.$float.'"><div class="slideshow slideshow__inserted"><div class="slideshow--wrapper flexslider js-slideshow js-gallery"><ul class="slides slideshow--list">';
        			
        			// create the LIs in the UL
        			foreach ( $carousel->get_photos() as $photo ) {
        				
        				$url = $photo->getPublicUrl(); 
    					$caption = cleanupSpecialChars($photo->caption);
        				
        				$replacement .= '<li class="slideshow--item"><div class="slideshow--slide"><a class="slideshow--link js-popup" href="'.$url.'" title="'.$caption.'"><img src="'.$url.'" alt="'.$caption.'" /></a></div></li>';
        			}
        			$replacement .= "</ul></div></div></figure>";
        			
        			$content_to_display = updateContent( $content_to_display, "*".$direction[0]."{2}carousel:{$carousel->slug}".$direction[1]."{2}*", $replacement );
        		
        		} else {
    				$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Gallery &ldquo;{$galname}&rdquo; not found!</span> ".$content_to_display; 
    			}
            }
    	}
    	return($content_to_display);
    }

    
    /*
     * Render a single document link inserted into content.
     */
    function document_display($content_to_display)
    {
    	$documentPattern = "/{{2}(document:[A-Za-z0-9\-\_ \.\(\)'\"]+){{2}/";
    	$documentIds = getFilterIds($content_to_display, $documentPattern);
    	$documents = Array();
    	
    	foreach ( $documentIds as $documentId ) {
    		$filename = end(explode(":", $documentId)); 
    		$documents[] = Documents::FindByFilename( $filename );
    	}
    	
    	foreach ( $documents as $document ) {
    		if( is_object($document) ) {
    			
    			$replacement = "<a class=\"hcd-document ".getFileExtension($document->filename)."\" href=\"{$document->getPublicUrl()}\">".cleanupSpecialChars($document->name)."</a> (".getFileExtension($document->filename).")";
    			$content_to_display = updateContent($content_to_display, "/{{2}document:".str_replace(")","\)",str_replace("(","\(",$document->filename))."{{2}/", $replacement);
    		} else {
    			$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Document &ldquo;$filename&rdquo; not found!</span> ".$content_to_display; 
    		}
    	}
    	return $content_to_display;
    }


    /*
     * Render a single video embed code inserted into content.
     */
    function video_display($content_to_display)
    {
    	$videoPattern = "/{{2}(video:[A-Za-z0-9\-\_ \.\(\)'\"]+){{2}/";
    	$videoIds = getFilterIds($content_to_display, $videoPattern);
    	$videos = Array();
    	
    	foreach ( $videoIds as $videoId ) {
    		$videoname = end(explode(":", $videoId));
    		$videos[] = Videos::FindByName( $videoname );
    	}
    	
    	foreach ( $videos as $thevid ) {
    		if ( is_object($thevid) ) {
    			$replacement = $thevid->embed_video();
    			$content_to_display = updateContent( $content_to_display, "/{{2}video:".str_replace(")","\)",str_replace("(","\(",$thevid->slug))."{{2}/", $replacement );
    		} else {
    			$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Video &ldquo;$videoname&rdquo; not found!</span> ".$content_to_display; 
    		}
    	}
    	return( $content_to_display );
    }

    
    /*
     * Render a single product insert it into content.
     */
    function product_display($content_to_display)
    {
    	// This is the pattern: }}product:sample1}}
    	$productPattern = "*}{2}(product:[A-Za-z0-9_ \-]+)}{2}*";
    	$productIds = getFilterIds($content_to_display, $productPattern);
    	$products = Array();
    	
    	foreach($productIds as $productName)
    	{
    		$sploded = explode(":", $productName);
    		$products[] = Product::FindByName($sploded[1]);
    	}
    		
    	foreach($products as $product)
    	{
    		if(is_object($product))
    		{
    			$account = Paypal_Config::GetAccount();
    			//print_r($product);
    			
    			// TO DO: Move the render function into the model, like videos and testimonials
    			$replacement = "
    				<div id =\"product_{$product->id}\" class=\"product\">
    					<h3>$product->display_name</h3>
    					<table cellspacing=\"0\" cellpadding=\"0\">
    						<tbody>
    							<tr>
    								<td valign=\"top\">\n"; 
    			if ($product->thumbnail) 
    				{ 
    					$replacement .= "\t\t\t\t\t\t\t\t\t<div id=\"prodGal_".$product->id."\">
    										<a class=\"product_thumb\" href=\"".get_link("/images/prodimg/".$product->id)."\"><img src=\"".get_link("/images/prodimg/".$product->id)."\" title=\"{$product->display_name}\" /></a>
    									</div>
    									
    									<script type= \"text/javascript\">//<![CDATA[
    									$(function() { $('#prodGal_".$product->id." a').hcdlightBox(); });
    									//]]></script>\n"; 
    			}
    			$replacement .= "
    									<form action=\"https://www.paypal.com/cgi-bin/webscr\" target=\"_blank\" method=\"post\">
    										<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
    										<input type=\"hidden\" name=\"business\" value=\"".$account->email."\">
    										<input type=\"hidden\" name=\"undefined_quantity\" value=\"1\">
    										<input type=\"hidden\" name=\"item_name\" value=\"".$product->display_name."\">
    										<input type=\"hidden\" name=\"item_number\" value=\"".$product->name."\">\n"; 
    			if ($product->price > 0) { $replacement .= "\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"amount\" value=\"".$product->price."\">\n"; }
    			$replacement .= "\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"no_shipping\" value=\"2\">
    										<input type=\"hidden\" name=\"return\" value=\"".$account->return."\">
    										<input type=\"hidden\" name=\"cancel_return\" value=\"".$account->cancel_return."\">
    										<input type=\"hidden\" name=\"no_note\" value=\"0\">
    										<input type=\"hidden\" name=\"currency_code\" value=\"USD\">
    										<input type=\"hidden\" name=\"lc\" value=\"US\">"; 
    			if ($product->price > 0) { $replacement .= "$".$product->price." <br />\n\t\t\t\t\t\t\t\t\t\t<font>(plus shipping if applicable)</font><br />"; }											
    			if ($product->price == 0 || $product->price == 0.00) 
    			{
    				$replacement .= "
    										<input type=\"image\" src=\"https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"Make payments with PayPal - it's fast, free and secure!\">"; 
    			} else {
    										
    				$replacement .= "
    										<input type=\"image\" src=\"https://www.paypal.com/en_US/i/btn/x-click-but23.gif\" border=\"0\" name=\"submit\" alt=\"Make payments with PayPal - it's fast, free and secure!\">";
    			}
    			$replacement .= "
    									</form>
    								</td>
    								<td valign=\"top\">
    									".$product->description."
    								</td>
    							</tr>
    						</tbody>
    					</table>
    				</div>
    			";
    			$search = "}}product:".$product->name."}}";
    			$content_to_display = str_replace($search, $replacement, $content_to_display);
    		} else {
    			$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Product not found!</span> ".$content_to_display; 
    		}
    	}
    	return($content_to_display);
    }

    
    /*
     * Scrub content for email addresses and display them in a safe way
     */
    function email_display($content_to_display)
    {
    	/* 
         * "Simple" regex allows something like hi+sub@whim.gallery or hi@whim.co.uk
    	 * Username: allow chars a-z 0-9 _-+ 
    	 * Hostname: allow chars a-z 0-9 _-  
    	 * TLD: minimum of 2 and then up to 24 chars. Does not allow non alpha chars (and may not need numbers, but we allow it)
    	 * This isn't about making sure emails are valid, but simply adds a link to an email that might be
    	 */
    	if (preg_match_all("([A-Za-z0-9_\-\+.]+[@]+[A-Za-z0-9_\-\.]+[.]+[A-Za-z0-9]{2,24})", $content_to_display, $emailArray)) {
    		$emailArray = $emailArray[0];
    		foreach ($emailArray as $address) {
    			$startPos = strpos($content_to_display, $address);
    			$splitAddress = explode("@", $address);
    			$javaBuilt = "<script type=\"text/javascript\">//<![CDATA[
    				emailE=('".$splitAddress[0]."@' + '".$splitAddress[1]."')
    				document.write('<a href=\"mailto:' + emailE + '\">' + emailE + '</a>')
				//]]></script>
    			";
    			$content_to_display = str_replace($address, $javaBuilt, $content_to_display);
    		}
    	}
    	return($content_to_display);
    }
    
    
    /*
     * Render a single testimonial inserted into content.
     */
    function testimonial_display($content_to_display)
	{
		$pattern_recog = array(
    	    "left" => array("{","{"), 
    	    "right" => array("}","}"), 
    	    "center" => array("{","}")
        );
	
		foreach ($pattern_recog as $float => $direction) {
			$testimonialPattern = "*".$direction[0]."{2}(testimonial:[A-Za-z0-9_ \-]+)".$direction[1]."{2}*";
			$testimonialNames = getFilterIds($content_to_display, $testimonialPattern);
			$testimonials = Array();
			
			if ( count($testimonialNames)>0 ) {
				foreach ( $testimonialNames as $name ) {
					$sploded = explode(":", $name);
					$testimonials[] = Testimonials::FindByName($sploded[1]);
				}
			}
						
			foreach ( $testimonials as $testimonial ) {
				if ( is_object($testimonial) ) {
					$replacement = $testimonial->displayTestimonial( $float );
					$search = "{$direction[0]}{$direction[0]}testimonial:".$testimonial->slug."{$direction[1]}{$direction[1]}";
					$content_to_display = str_replace($search, $replacement, $content_to_display);
				} else {
					$content_to_display = "<span class=\"database_error\">HCd&gt;CMS Warning: Testimonial not found!</span> ".$content_to_display; 
				}
			}
		}
		return($content_to_display);
	}
?>
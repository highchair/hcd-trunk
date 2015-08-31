<?php
	function initialize_page()
	{
        $item = Items::FindById( getRequestVaratIndex(3) );
		
        // get all the sections
        $sections = Sections::FindPublicSections();
		
        /* get this section
         * We do this mostly for the previous and next item functions. If we dont know what section we are currently inside, 
         * the user may get bounced over to a different place than they started. */
        $sectionname = getRequestVaratIndex(2);
        if ( $sectionname != "item_orphan" )
            $section = Sections::FindByName( $sectionname );
		
		// get the associated gallery
		if ( $item ) 
        	$gallery = $item->getGallery();
		
        // finally, get the post action. Harder to hack if we explicitly check the value this way. 
        $post_action = "";
        if( isset($_POST['submit']) )
            $post_action = $_POST['submit'];
	
		if( $post_action == "Save Item" || 
            $post_action == "Add Image" || 
            $post_action == "Add Document" || 
            $post_action == "Add or Edit Video" || 
            $post_action == "Save and Return to List" ) {
            
            /* 
             * Delete this item and its associated components
             */
			if( isset($_POST['delete']) ) {
				
				// delete $photos and $gallery
				if ( is_object($gallery) )  {
    				$gallery->delete(true);
    				$success .= "Gallery and Images Deleted / "; 
				}
				/* Documents ... Why not keep them?
				if ( ITEM_DOCUMENTS ) {
				    $itemdocuments = $item->findDocuments( 'display_order ASC' );
				    foreach ( $itemdocuments as $thedoc ) {
    				    $thedoc->delete(true); 
				    }
				    $success .= "Documents Deleted / ";
				}*/
				$item->delete( true );
				$success .= "Item Deleted / ";
				
				setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
				//$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
				//redirect( $main_portlink );
				redirect( "admin/portfolio_list" );
			
			} else {
				
				$item->content = $_POST['item_content'];
				$item->display_name = $_POST['display_name'];
				
				$previous_name = $item->name; 
				$item->name = slug($_POST['display_name']);
				
				$item->template = 'inherit';
				$item->public = checkboxValue($_POST,'public');
				$item->date_revised = date('Y-m-d H:i:s'); 
				
				// optional fields
				$item->sku = ( ITEM_SKU ) ? $_POST['item_sku'] : null;
				$item->taxonomy = ( ITEM_TAXONOMY ) ? $_POST['taxonomy'] : null;
				$item->price = ( ITEM_PRICE ) ? $_POST['item_price'] : null;
				
				
				// SAVE item... uses a MyActiveRecord method
                $item->save();
				$success = "Item Saved / "; 
				
				
				// synchronize the users section selections only if they are different
				$selected_sections = array();
				$previous_sections = $item->getSections(); 
				
				if( isset($_POST['selected_sections']) ) {
					
					$update_sections = false; 
					$selected_sections = $_POST['selected_sections'];
					
					// Problem: If we loop on only the $previous_sections, we may have fewer or more loops than $selected_sections. 
					// Compare one to the other. 
					if ( count($previous_sections) != count($selected_sections) ) {
    					// The two do not match, so there has been a change
    					$update_sections = true; 
					} else {
    					// In case the two match, let's make sure something is different. 
    					foreach ( $previous_sections as $sect ) {
        					if ( ! in_array( $sect->id, $selected_sections )  ) {
            					$update_sections = true; 
        					}
    					}
					}
					if ( $update_sections ) {
    					$item->updateSelectedSections( $selected_sections );
    					
    					// update the revision dates of sections, too
        				$item->updateSectionRevisionDates(); 
					}
				}
				
				/* 
				 * Rename the gallery if the slug has changed. 
				 * We need the name of the gallery and the name of the slug to be consistent. 
                 * If there isn't a gallery – something broke, so – create a new one. 
                 */
                if ( is_object($gallery) && $previous_name != $item->name ) {
    				$gallery->slug = "portfolioGal_".$item->id."_".$item->name; 
    				$gallery->save(); 
    				$success .= "Gallery name changed / ";
				} 
				if ( ! is_object($gallery) ) {
    				$gallery = MyActiveRecord::Create('Galleries');
        			$gallery->name = $_POST['display_name']." Gallery";
        			$gallery->slug = "portfolioGal_".$item->id."_".slug($_POST['display_name']);
        			$gallery->save();
				}

				/* ! Gallery image functions
                 */
				if( isset( $_FILES['new_photo'] ) && $_FILES['new_photo']['error'] == 0 ) {
					
					// user has added a new file
					$newphoto = MyActiveRecord::Create( 'Photos', array( 'caption' => getPostValue("new_photo_caption" ), 'gallery_id' => $gallery->id, 'display_order' => 1 ) );
					$newphoto->save();
					$newphoto->save_uploaded_file( $_FILES['new_photo']['tmp_name'], $_FILES['new_photo']['name'], true);
					
					$success .= "New photo uploaded / ";
				} 
				
				/* 
                 * Check current captions against previous ones. 
                 */
				if( isset($_POST['captions']) ) {
					
					$captions = $_POST['captions'];
					foreach( $captions as $key=>$thecaption ) {
						$photo = Photos::FindById( $key );
						if ( $photo->caption != $thecaption ) {
							$photo->caption = $thecaption;
							$photo->save();
						}
					}
				}
				
				/* 
                 * Check photo display order against previous ones 
                 */
				if( isset($_POST['photos_display_order']) ) {
					
					$display_orders = $_POST['photos_display_order'];
					foreach($display_orders as $key=>$display_order) {
						$photo = Photos::FindById($key);
						if ( $photo->display_order && $photo->display_order != $display_order ) {
							$photo->display_order = $display_order;
							$photo->save();
						}
					}
					$success .= "Photo order saved / ";
				}
				
				/* 
                 * Delete a photo from the gallery
                 */
				if( isset($_POST['deleted_photos']) ) {
					
					$deleted_ids = $_POST['deleted_photos'];
					foreach($deleted_ids as $status => $photo_id) {			   
						$photo = Photos::FindById($photo_id);
						$photo->delete(true);
					}
					$success .= "A photo was deleted / ";
				}
				
				/* 
                 * Check to see if we allow Portfolio Thumbs
                 */
				if( PORTFOLIOTHUMB_IMAGE ) {
					
					// was a new thumbnail uploaded
					if ( is_uploaded_file( realpath($_FILES["thumbnail"]["tmp_name"]) ) ) {
						
                        if ( 
                        
                            Upload_and_Save_Image( 
    						    $_FILES["thumbnail"],           // $image
    						    'items',                        // $table_name, 
    						    'thumbnail',                    // $file_field_name, 
    						    $item->id,                      // $row_id, 
    						    PORTFOLIOTHUMB_IMAGE_MAXWIDTH,  // $thiswidth=null, 
    						    PORTFOLIOTHUMB_IMAGE_MAXHEIGHT  // $thisheight=null
                            )
                         
                        ) {
                            $success .= "Thumbnail updated / ";
                        }
					}
				}
				
				/* ! Video functions
                 */
				if ( ITEM_VIDEOS ) {
					
					// If this gallery has mixed photos AND videos, check the display order again and set each by object type
    				if( isset($_POST['galitem_display_order']) ) { 
    				    
    				    foreach( $_POST['galitem_display_order'] as $key=>$display_order ) {
    						$type = $_POST['galitem_type'][$key]; 
    						
    						$galitem = ( $type == 'photo' ) ? Photos::FindById($key) : Videos::FindById($key);
    						if ( is_object($galitem) ) {
    						//if ( $galitem->display_order && $galitem->display_order != $display_order ) {
    							$galitem->display_order = $display_order;
    							$galitem->save();
    						}
    					}
    				}
    				
					// Change the name of a video
					if( isset($_POST['vidnames']) ) {
						
						$vidnames = $_POST['vidnames'];
						foreach( $vidnames as $key=>$thename ) {
							$video = Videos::FindById($key);
							if ( $video->display_name != $thename ) {
								$video->name = slug($thename);
								$video->display_name = $thename;
								$video->save();
							}
						}
						//$success .= "Video name updated / "; // False positive
					}
					
					// Change the embed code of a video
					if( isset($_POST['vidcodes']) ) {
						
						$vidnames = $_POST['vidcodes'];
						foreach( $vidnames as $key=>$thecode ) {
							$video = Videos::FindById($key);
							if ( $video->embed != $thecode ) {
								$video->embed = $thecode;
								$video->save();
							}
						}
						//$success .= "Video embed updated / "; // False positive
					}
					
					// Add a new Video
					if( $_POST['newvideo'] != '' ) {
    					$video = MyActiveRecord::Create( 'Videos' ); 
    					
    					/*
                		 * Columns: id, name, title, service, embed, width, height, gallery_id, display_order
                		 */
        			    $vidtitle = $_POST['newvideo']; 
        			    
        			    $video->name = slug( $vidtitle );
        			    $video->display_name = $vidtitle;
        			    $video->service = $_POST['vidservice'];
        			    $video->embed = $_POST['vidembed'];
        			    $video->width = $_POST['vidwidth'];
        			    $video->height = $_POST['vidheight'];
        			    $video->gallery_id = $gallery->id; 
        			    $video->display_order = count( $gallery->get_photos() ) + 1; 
        			    
        			    $video->save();
        			    $success .= "Video added / ";
    				}
    				
    				// Remove video association -- Does not delete the video itself
    				if( isset( $_POST['removevideo'] ) ) {
    				    $video = Videos::FindById( $_POST['removevideo'] ); 
    				    $video->gallery_id = null; 
    				    $video->save();
    				}
				}
				
				/* ! Document functions
                 */
				if ( ITEM_DOCUMENTS ) {
					
					// Change the name of a document
					if( isset($_POST['docname']) ) {
						
						$docnames = $_POST['docname'];
						foreach( $docnames as $key=>$thename ) {
							$document = Documents::FindById($key);
							if ( $document->name != $thename ) {
								$document->name = $thename;
								$document->save();
							}
						}
					}
					
					// Reorder documents
					if( isset($_POST['document_display_order']) ) {
						
						$display_orders = $_POST['document_display_order'];
						foreach( $display_orders as $key=>$display_order ) {
							$doc = Documents::FindById($key);
							if ( $doc->display_order != $display_order ) {
								$doc->display_order = $display_order;
								$doc->save();
							}
						}
					}
					
					// Add a new document
					if( isset($_FILES['new_document']) && $_FILES['new_document']['error'] == 0 ) {
						
						// Set the name equal to the input field or the physical doc name
						$name = ( $_POST['new_document_title'] ) ? $_POST['new_document_title'] : unslug($_FILES['new_document']['name']);
						$name = substr( $name, 0, strrpos( $name, "." ) ); 
						
						// Find the extension. Explode on the period. 
						$extension = substr( $_FILES['new_document']['name'], strrpos( $_FILES['new_document']['name'], "." ) );
						$file_type = substr( $extension, 1); // Chop the dot off
						
						$filename = slug( $name ) . $extension;
						
						$target_path = SERVER_DOCUMENTS_ROOT . $filename;
						
						if( move_uploaded_file($_FILES['new_document']['tmp_name'], $target_path) ) {
							
							$new_doc = MyActiveRecord::Create( 'Documents', array( 'name'=>$name, 'filename'=>$filename, 'file_type'=>$file_type, 'item_id'=>$item->id ) );
							$new_doc->save();
							$success .= "Document uploaded and attached / ";
							
							if ( ! chmod($target_path, 0644) ) {
								$success .= "!Warning: Document Permissions not set; this file may not display properly! / ";
							}
						} else {
							$success .= "!WARNING: Document could not be uploaded! / ";
						}
					} else { 
					   echo $_FILES['new_document']['error']; 
    				}
					
					// Delete Documents
					if( isset($_POST['deleted_documents']) ) {
						
						$deleted_ids = $_POST['deleted_documents'];
						foreach( $deleted_ids as $status => $doc_id ) {			   
							$doc = Documents::FindById($doc_id);
							$doc->delete(true);
						}
						$success .= "A document was deleted / ";
					}
				}
				
				setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
				
				if ( $post_action == "Save and Return to List" ) {
					//$main_portlink = ( DISPLAY_ITEMS_AS_LIST ) ? "admin/portfolio_list/alphabetical" : "admin/portfolio_list"; 
    				//redirect( $main_portlink );
    				redirect( "admin/portfolio_list" ); 
				} else {
				    
				    if ( $update_sections ) {
    				    // Find a new section, the one that has just been assigned...
    				    // Breaks into an infinite loop on Windows servers... can we clear the post somehow? 
    				    $section = Sections::FindById( $_POST['selected_sections'][0] );
				    } 
				    redirect( "/admin/portfolio_edit/".$section->name."/".$item->id );
				}
			}
		}
	}
	
	function display_page_content()
	{
		$portareas = Areas::FindPortAreas( false ); 
		
		$item_id = getRequestVaratIndex(3);
		$item = Items::FindById($item_id);
		$next_item = $prev_item = ""; 
		
		$sectionname = getRequestVaratIndex(2);
		if ( $sectionname != "orphan_section" ) {
    		// Problem is, there could be two sections with the same name in different Areas. So, loop through the Sections attached to this item
			$item_sections = $item->getSections(); 
			foreach ( $item_sections as $thissect ) {
    			if ( $thissect->name == $sectionname ) {
        			$a_section = Sections::FindById( $thissect->id );
        			$thisarea = $a_section->thePortfolioArea(); 
        			
        			$next_item = $item->findNext( $a_section, "" );
        			$prev_item = $item->findPrev( $a_section, "" ); 
        			break; 
    			}
			}
		}
		$user = Users::GetCurrentUser(); 
		
		// If a gallery gets detached or this item doesn't have one, don't create an error. 
		$gallery = $item->getGallery();							
		if ( is_object($gallery) ) {
            $photos = $gallery->get_photos();
            $photocount = ( count( $photos ) == 0 ) ? "None" : count( $photos ); 
            
            // Sometimes the following statement throws errors. Check it out if this page behaves funny. 
            if ( ITEM_VIDEOS ) {
    			$gallery_items = $gallery->get_photos_and_videos(); 
    			$photocount = ( count( $gallery_items ) == 0 ) ? "None" : count( $gallery_items ); 
    			$itemvideos = $item->findVideos( $gallery, 'display_order DESC' ); 
    			$vidcount = ( count( $itemvideos ) == 0 ) ? "None" : count( $itemvideos ); 
    		}
        } else {
            $gallery = false; 
            $photos = $itemvideos = $gallery_items = null; 
            $photocount = 0; 
            $vidcount = "None"; 
        }
		
		if ( ITEM_DOCUMENTS ) {
			$itemdocuments = $item->findDocuments( 'display_order DESC' ); 
			$doccount = ( count( $itemdocuments ) == 0 ) ? "None" : count( $itemdocuments ); 
		}
?>

	<script type="text/javascript">
	//<![CDATA[
		$().ready(function() {
			
			$("#edit_item").validate({
				errorLabelContainer: "#error_container",
				rules: {
						display_name: "required",
						"selected_sections[]": "required",
					},
				messages: {
						display_name: "Please enter a name that should be displayed for this item",
						"selected_sections[]": "Almost forgot! Select at least one section to include this item in", 
					}
			});
			
			$("#photo_list_order").sortable({
				stop: function() {
					$("#photo_list_order li input.displayorder").each(function(i) {
						$(this).val(i+1);
					});
				}
			});
			
			$("#document_list").sortable({
				stop: function() {
					$("#document_list li input[type=hidden]").each(function(i) {
						$(this).val(i+1);
					});
				}
			});
		});
	//]]>
	</script>

	<div id="edit-header" class="itemnav threecolumnnav">
		<div class="nav-left column">
			<h1>Edit Item<?php if ( $sectionname != "orphan_section" ) { ?> : <a href="<?php $item->the_url( $thisarea, $a_section ) ?>" title="View &ldquo;<?php $item->the_title() ?>&rdquo;">View Item</a><?php } ?></h1>
		</div>
		<div class="nav-center column">
			<?php if ( $prev_item != "" ) { ?><a href="<?php echo get_link("/admin/portfolio_edit/".$a_section->name."/".$prev_item->id); ?>" title="<?php $prev_item->the_title() ?>">&larr; Previous Item</a><?php } ?>
		</div>
		<div class="nav-right column">
			<?php if ( $next_item != "" ) { ?><a href="<?php echo get_link("/admin/portfolio_edit/".$a_section->name."/".$next_item->id); ?>" title="<?php $next_item->the_title() ?>">Next Item &rarr;</a><?php } ?>
			
		</div>
		<div class="clearleft"></div>
	</div>


<?php 
    // NEW! Debug messages. 
    
    if ( HCd_debug() ) {
        echo '<div class="debug-block">'; 
        
        if ( ! $gallery ) {
            
            echo '<span class="debug-feedback failed">$gallery is false! Was never created or got detached...</span>';
        }
        
        // Check the folder path to see if it exists
        if ( is_dir(SERVER_DOCUMENTS_ROOT) ) {
            
            echo '<span class="debug-feedback passed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'&rdquo; is a writeable folder</span>';
            echo '<span class="debug-feedback passed">The folder permission is <strong>'.substr(sprintf('%o', fileperms(SERVER_DOCUMENTS_ROOT)), -4).'</strong></span>'; 
            
            if ( is_dir(SERVER_DOCUMENTS_ROOT."gallery_photos") ) {
                echo '<span class="debug-feedback passed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'gallery_photos&rdquo; is a writeable folder</span>';
                echo '<span class="debug-feedback passed">The folder permission is <strong>'.substr(sprintf('%o', fileperms(SERVER_DOCUMENTS_ROOT."gallery_photos")), -4).'</strong></span>';  
            } else {
                echo '<span class="debug-feedback failed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'gallery_photos&rdquo; is NOT a writeable folder</span>'; 
            }
            
        } else {
            echo '<span class="debug-feedback failed">&ldquo;'.SERVER_DOCUMENTS_ROOT.'&rdquo; is NOT a writeable folder</span>'; 
        }
        echo '</div>'; 
    }
?>


	<form method="POST" id="edit_item" enctype="multipart/form-data">
		
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Display Name:</label>
			<?php textField( "display_name", $item->display_name, "required: true" ); ?>
		</p>
		
<?php if( PORTFOLIOTHUMB_IMAGE ) { ?>
		<div id="thumbnail" class="column half">
			
			<p><label for="thumbnail">Thumbnail:</label></p>
			<p>
				<img src="<?php echo get_link("portfolio/thumbnail/$item->id"); ?>" />
			    &nbsp;Select a new image to use as a thumbnail:<br />
			    &nbsp;<input type="file" name="thumbnail" id="id_thumbnail" value="" class="" />
			</p>
		</div>
		<div class="column half last">
<?php } else { ?>	
		<div>
<?php } ?>
		
<?php if( ITEM_SKU ) { ?>
			<p>
				<label for="item_sku">Item Sku (unique ID):</label>
				<?php textField( "item_sku", $item->sku ); ?>
			</p>

<?php } ?>
<?php if( ITEM_PRICE ) { ?>
			<p>
				<label for="item_price">Item Price:</label>
				<?php textField( "item_price", $item->price ); ?>
			</p>

<?php } ?>
<?php if ( ITEM_TAXONOMY ) { require_once(snippetPath("item-taxonomy")); }  ?>
			
			<p><label for="public">Public:</label><?php checkBoxField("public", $item->public); ?>&nbsp; <span class="hint">Visible or not visible to the public? If you are working on an item that is not yet ready, leave this off until it is complete.</span></p>
	
		</div>
		<div class="clearleft"></div>

		<p>
			<label for="item_content">Item Description:</label><br />
			<?php textArea("item_content", $item->content, 98, EDIT_WINDOW_HEIGHT); ?>
		</p>
		
		
<?php require_once( snippetPath("admin-insert_configs") ); ?>
						
						
<!-- Start the Show/Hide for editing photos and changing order -->
		<div id="gallery-options">
			
			<a name="editgal"></a>
			<ul id="gallery-options-nav" class="menu tabs">
				<li><a href="#section_selector" class="openclose">Edit Sections this Item is in</a></li>
				<li><a href="#gallery_sort" class="openclose opened">Change Media Order <span>(<?php echo $photocount ?>)</span></a></li>
				<li><a href="#add_edit_images" class="openclose">Add / Edit Photos and Captions</a></li>
				<?php if ( ITEM_VIDEOS ) {  ?>
				<li><a href="#add_videos" class="openclose">Add / Edit Videos <span>(<?php echo $vidcount ?>)</span></a></li>
				<?php } if ( ITEM_DOCUMENTS ) {  ?>
				<li><a href="#add_edit_documents" class="openclose">Add / Edit Documents <span>(<?php echo $doccount ?>)</span></a></li>
				<?php } ?>
				
			</ul>
			
			
			<!-- Select a Section -->
			<div id="section_selector" class="dropslide" style="display:none;">
				<h2><legend>Select a Section to include this Item in:</legend></h2>
				<fieldset>
	<?php
		foreach( $portareas as $area ) {
			echo "<p><strong>".$area->get_title().":</strong><br />"; 
			
			$sections = $area->getSections( true ); 
			
			foreach ( $sections as $section ) {
				$checked = $labelchecked = "";
				
				if( isset($item_sections) ) {
					foreach( $item_sections as $item_section ) {
						if ( $item_section->id == $section->id ) {
							$checked = "checked='checked'";
							$labelchecked = " class='selected'"; 
						}
					}
				}
				echo "\t\t\t\t\t<label for='selected_sections[]'$labelchecked>{$section->display_name}&nbsp;<input id=\"selected_areas[]\" name='selected_sections[]' class='boxes' {$checked} type='checkbox' value='{$section->id}' /></label>\n"; 
			}
			echo "</p>\n"; 
		}
	?>
									
					<p><span class="hint">Any item can be in more than one Section. If no sections are selected, this item will not be viewable by the public and it will appear under &ldquo;Orphaned Items&rdquo;.</span></p>
				</fieldset>
				<div class="clearleft"></div>
			</div><!-- #section_selector -->
			
			
			<!-- Sort gallery order of the items. Could be mixed images AND videos. -->
			<div id="gallery_sort" class="dropslide">
				<h2>Click and Drag the image to change the order of the display in the Gallery <!--<small>(ID= <?php echo $gallery->id ?>)</small>--></h2>
				<div id="sortable_container">
<?php
    if ( ITEM_VIDEOS && ! is_null($gallery_items) ) {
        
        // There could be videos AND photos in this gallery
        if ( count($gallery_items) > 0 ) {
            echo "\t\t\t\t<ol id=\"photo_list_order\">\n";
            foreach( $gallery_items as $galitem ) {
                if ( $galitem->type == 'photo' ) {
                    // Treat as a photo
                    echo "\t\t\t\t\t<li><img class=\"changeorderThumb\" src=\"{$galitem->getPublicUrl()}\" />
        						<input type=\"hidden\" name=\"galitem_display_order[" . $galitem->id . "]\" class=\"displayorder\" value=\"" . $galitem->display_order . "\" />
        						<input type=\"hidden\" name=\"galitem_type[" . $galitem->id . "]\" value=\"" . $galitem->type . "\" /></li>\n";
                } else {
                    // Treat as a video
                    echo "\t\t\t\t\t<li><div class=\"changeorderThumb videoThumb\">Video: " . $galitem->get_title() . "</div>
        						<input type=\"hidden\" name=\"galitem_display_order[" . $galitem->id . "]\" class=\"displayorder\" value=\"" . $galitem->display_order . "\" />
        						<input type=\"hidden\" name=\"galitem_type[" . $galitem->id . "]\" value=\"" . $galitem->type . "\" /></li>\n";
                }
            }
            echo "\t\t\t\t</ol>\n"; 
        } else {
            echo "<h3 class=\"empty-list\">There are no photos or videos to put in order. Go ahead and add some.</h3>\n"; 
        }
        
    } elseif ( is_object($gallery) ) {
    	if ( count($photos) > 0 ) {
    		
    		echo "\t\t\t\t<ol id=\"photo_list_order\">\n";
    		foreach( $photos as $photo ) {
    			
    			echo "\t\t\t\t\t<li><img class=\"changeorderThumb\" src=\"{$photo->getPublicUrl()}\" />
    						<input type=\"hidden\" name=\"photos_display_order[" . $photo->id . "]\" class=\"displayorder\" value=\"" . $photo->display_order . "\" /></li>\n";
    		}
    		echo "\t\t\t\t</ol>\n"; 
    	} else {
    		echo "<h3 class=\"empty-list\">There are no photos to put in order. Go ahead and add some.</h3>\n"; 
    	}
    } else {
        echo "<h3 class=\"empty-list\">Whoops, there is no Gallery! Save this item to create a new one.</h3>\n"; 
    }
?>
								
				</div>
				<div class="clearleft"></div>
			</div><!-- #gallery_sort -->
			
			
			<!-- Upload a new image or edit captions / delete images -->
			<div id="add_edit_images" class="dropslide" style="display:none;">
				<div id="add_image_wrap">
					<h2>Add an Additional Image</h2>
					<p><span class="hint">Images for your site would be best displayed at <?php echo MAX_PORTFOLIO_IMAGE_WIDTH ?> pixels wide. If an image is larger, the system will attempt to resize it. Images that are too large might require more memory than the system can handle, and an error may result.</span></p>
					<p><label for="new_photo">Add an additional image:</label>
						<input type="file" name="new_photo" id="new_photo" value="" />
					</p>
					<p><label for="new_photo_caption">Caption for new image:</label>
						<?php textField("new_photo_caption", "", ""); ?>
					</p>
					<p><input type="submit" class="submitbutton" name="submit" value="Add Image" /></p>
				</div>
				<p>&nbsp;</p>
				<h2>Edit Existing Photos (delete photos or edit captions. Click Save when done)</label></h2>
				<p><span class="hint"><b>Image size:</b> The CSS control for the images designates that they display here (for ease of use) at 50% their actual size. Dont&rsquo;t worry if they appear smaller than on the front-end.<!--[if IE 6]><br /><b>IE 6 Users: The CSS can only force the image to be 240 pixels wide, which for some images, may be larger than normal, resulting in pixellation. </b><![endif]--></span></p>
				<ul id="add_photo_list">                                                                                                                                                    
<?php
    if ( is_object($gallery) ) {
    	if (count($photos) > 0) {
    		
    		foreach($photos as $photo) {
    		
    			echo "\t\t\t\t<li><img src=\"{$photo->getPublicUrl()}\" />\n"; 
    			echo "\t\t\t\t\t<div><input type=\"checkbox\" name=\"deleted_photos[]\" value=\"{$photo->id}\"/>&nbsp;DELETE</div>";
    			textField("captions[{$photo->id}]", "{$photo->caption}", "");
    			echo "\n\t\t\t\t</li>\n"; 
    		}
    	} else {
    		echo "\t\t\t\t<h3 class=\"empty-list\">There are no photos to edit. Go ahead and add some.</h3>\n"; 
    	}
    }
?>
    				
				</ul>
				<p class="clear:left;"><a href="#top">Back to the Top of the Page</a></p>
			</div>
		</div><!-- #add_edit_images -->
		
		
		<!-- Item Videos -->
		<?php if ( ITEM_VIDEOS ) { ?>
		<div id="add_videos" class="dropslide" style="display:none;">
			<div id="add_video_wrap">
			    <h2>Add a new Video</h2>
			    <p><span class="hint">Video can be &ldquo;attached&rdquo; to an item. They will still be available to pages via the &ldquo;Insert Videos&rdquo; drop down menu. Once uploaded here, they must be further edited or deleted in the <a href="<?php echo get_link("admin/list_videos") ?>">Videos section</a>. </span></p>
			    
			    <!-- Add a new video -->
			    <p>
        			<label for="newvideo">Video Title:</label>
        			<?php textField( "newvideo", '' ); ?><br />
        		</p>
        		
        		<div class="column half">
            		<div class="column half">
                		<p>
                		    <label for="vidwidth">Width:</label>
                		    <?php textField( "vidwidth", '' ); ?>
                		</p>
            		</div>
            		<div class="column half last">
                		<p>
                		    <label for="vidheight">Height:</label>
                		    <?php textField( "vidheight", '' ); ?>
                		</p>
            		</div>
            		<div class="clearit"></div>
            		
            		<p><label for="vidservice">Hosting Service:</label>
            			<select id="vidservice" name="vidservice">
            				<option value="youtube">YouTube</option>
            				<option value="vimeo">Vimeo</option>
            			</select><br />
            		</p>
        		</div>
        		<div class="column half last">
            		<p>
            			<label for="vidembed">Unique ID:</label>
            			<?php textField( "vidembed", '' ); ?><br />
            			<span class="hint">The unique identifier is a random string of numbers and letters associated with the file. <br />
            			YouTube example: http://www.youtube.com/embed/<mark>tVUCsnMK18E</mark> <br />
            			Vimeo example: http://player.vimeo.com/video/<mark>72632269</mark> <br />
            			In both cases, we are only interested in the text highlighted.</span>
            		</p>
        		</div>
        		<div class="clearit"></div>
        		<p><input type="submit" class="submitbutton" name="submit" value="Add or Edit Video" /></p>
            </div>
			<p>&nbsp;</p>
            
            <!-- Edit Videos that are already attached -->
			<h2>Edit Attached Videos</h2>
			<p class="hint">Edit titles or embed codes and click Save Videos. Reorder by dragging and dropping the thumbnails in the gallery. Click Save when done. To delete or do more serious editing, please visit <a href="<?php echo get_link("admin/list_videos") ?>">the Video&rsquo;s edit page</a> instead. </p>
			
			<ol id="video_list" class="managelist"> 
        		<?php
        			foreach ( $itemvideos as $thevid ) {
        				echo '<li><span class="item-link">'; 
            			textField( "vidnames[{$thevid->id}]", $thevid->get_title(), "" ); 
            			echo '</span>
    				<span class="item-public">'; 
        				textField( "vidcodes[{$thevid->id}]", $thevid->embed, "" ); 
        				echo '</span>
    				<span class="item-revised"><label for="removevideo"><input name="removevideo" class="boxes" type="checkbox" value="'.$thevid->id.'"> Remove Video?</label></span>
    				<span class="item-created"><a href="'.get_link( "admin/edit_video/".$thevid->id ).'">Edit Video</a></span>
				</li>'; 
        			}
        		?>
    		
    		</ol>
        </div>
		<?php } // end if ITEM_VIDEOS ?>
		
		
		<?php if ( ITEM_DOCUMENTS ) { ?>
		<div id="add_edit_documents" class="dropslide" style="display:none;">
			<div id="add_document_wrap">
				<h2>Add, Delete or Reorder Documents</h2>
				<p><span class="hint">Documents can be &ldquo;attached&rdquo; to an item. They will still be available to pages via the &ldquo;Insert Document&rdquo; drop down menu. </span></p>
				<p><label for="new_document">Add a document:</label>
					<input type="file" name="new_document" id="new_document" value="" />
				</p>
				<p><label for="new_document_title">Title for document (Optional. If empty, one will be created from the name of the document):</label>
					<?php textField("new_document_title", "", ""); ?>
				
				</p>
				<p><input type="submit" class="submitbutton" name="submit" value="Add Document" /></p>
			</div>
			<p>&nbsp;</p>

			<h2>Edit Attached Documents</h2>
			<p class="hint">Delete documents or edit titles. Reorder by dragging and dropping. Click Save when done. NOTE: The filename may or may not be used in your template when this item is displayed. <strong>Filenames should not contain a period unless it precedes the filetype extension</strong>. </p>
			
			<div id="sortable_container">
				<ol id="document_list" class="managelist">                                                                                                                                                    
<?php
	if ( $doccount > 0 ) {
		foreach( $itemdocuments as $doc ) {
			
			echo "\t\t\t\t\t<li>\n";
			hiddenField( "document_display_order[{$doc->id}]", $doc->display_order ); 
			echo "\n\t\t\t\t\t\t<span class=\"item-link\">"; 
			textField( "docname[{$doc->id}]", $doc->name, "" ); 
			echo "</span>
							<span class=\"item-public\">File type: $doc->file_type</span>
							<span class=\"item-revised\"><a href=\"".$doc->getPublicUrl()."\" title=\"".$doc->get_title()."\">View Document</a></span>
							<span class=\"item-created\"><input type=\"checkbox\" name=\"deleted_documents[]\" value=\"{$doc->id}\"/>&nbsp;DELETE</span>
						</li>\n"; 
		}
	} else {
		echo "\t\t\t\t\t<h3 class=\"empty-list\">There are no documents to edit.</h3>\n"; 
	}
?>
							
				</ol>
			</div>	
			<p class="clear:left;"><a href="#top">Back to the Top of the Page</a></p>
		</div><!-- #add_edit_documents -->
		<?php } // end if ITEM_DOCUMENTS ?>

		<div id="edit-footer" class="itemnav clearfix">
			<div id="error_container"></div>
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Item" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
				</p>
				
			</div>
			<div class="column half last">
				
			<?php if($user->has_role()) { ?>
				
				<p><label for="delete">Delete this item? <input name="delete" id="delete" class="boxes" type="checkbox" value="<?php echo $item->id ?>" /></label>
				<span class="hint">Check this box and then click &ldquo;Save&rdquo; to delete from the database</span></p>
			<?php } ?>
			
			</div>
		</div>
		
	</form>
<?php } ?>
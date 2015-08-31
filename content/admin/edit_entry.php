<?php
	function initialize_page() {
	
		// This file does both, so check the parameters first
		if ( requestIdParam() == "add" ) {
    		$entry = MyActiveRecord::Create( 'Blog_Entries' );
		} else {
    		$entry_id = requestIdParam();
    		$entry = Blog_Entries::FindById( $entry_id );
		}
		
		$post_action = "";
		if( isset($_POST['submit']) ) { $post_action = $_POST['submit']; }
		
		$blog = Blogs::FindById( BLOG_DEFAULT_ID ); 
		
		// Check for the delete action
		if( isset($_POST['delete']) ) {
			// Delete a photo if there is one
			if ( BLOG_ENTRY_IMAGES ) {
        		$photo = array_shift( MyActiveRecord::FindBySql('Photos', "SELECT * FROM photos WHERE entry_id = {$entry->id}") ); 
        		if ( is_object($photo) ) $photo->delete(true); 
			}
			$entry->delete();
			setFlash("<h3>Entry Deleted</h3>");
			redirect("/admin/list_entries/".$user->id);
		
		} else {
			
			if( $post_action == "Save Entry" || $post_action == "Save and Return to List" ) {
				
				/*
    			 * Columns: id, title, slug, date, content, excerpt, public, template, author_id, blog_id
				 */
				$entry->title = getPostValue('title');;
				$entry->slug = slug( getPostValue('title') );
				
				if (getPostValue('date') != "") {
					$entry->setEntryDateAndTime( getPostValue('date') );
				} else {
					$entry->date = date('Y-m-d H:i:s');
				}
				$entry->content = getPostValue( 'entry_content' );
				$entry->excerpt = getPostValue( 'entry_excerpt' );
				$entry->public = checkboxValue( $_POST,'public' );
				if ( BLOG_ENTRY_TEMPLATES ) { $entry->template = $_POST['template']; } 
				$entry->author_id = $_POST['author_id'];
				$entry->blog_id = $blog->id;
				
				$entry->save();
				$success = "Blog Entry Saved / ";
				
				// synchronize the users category selections
				$selected_cats = array();
				if( isset( $_POST['selected_cats'] ) ) {
					$selected_cats = $_POST['selected_cats'];
					$entry->updateSelectedCategories( $selected_cats );
				} else {
					$uncategorized = Categories::FindById( 1 );
					$entry->attach( $uncategorized );
				}
				
				// Upload the photo if one is allowed
				if( isset( $_FILES['entry_image'] ) && $_FILES['entry_image']['error'] == 0 ) {
				
					// delete an old file if there is one
					$oldphoto = array_shift(MyActiveRecord::FindBySql('Photos', "SELECT * FROM photos WHERE entry_id = {$entry->id}")); 
            		if ( is_object($oldphoto) ) $oldphoto->delete(true);  
					
					// user has added a new photo
					$newphoto = MyActiveRecord::Create( 'Photos', array( 'caption' => $entry->title, 'entry_id' => $entry->id ) );
					$newphoto->save();
					$newphoto->save_uploaded_file( $_FILES['entry_image']['tmp_name'], $_FILES['entry_image']['name'], '',$isentryimg = true );
					
					$success .= "New image uploaded / ";
				}
				
				if ( requestIdParam() == "add" ) {
    				setFlash('<h3>' . $success . '<a href="'.get_link('admin/edit_entry/'.$entry->id).'">Edit it Now</a></h3>');
                } else {
                    setFlash("<h3>" . substr( $success, 0, -3 ) . "</h3>");
                }
				
				if ( $post_action == "Save and Return to List" ) { redirect("admin/list_entries/"); }
			}
		}
	}
	
	function display_page_content() {		
		
		// Set all values to null by default
		$entry = $entrytitle = $entrypublic = $entrydate = $entryimage = $entrycontent = $entryexcerpt = $entryauthor = $entrytemplate = $preventry = $nextentry = null; 
		
		// Get values from existing object if this is not the Add page
		if ( requestIdParam() != 'add' ) {
    		$entry_id = requestIdParam();
    		$entry = Blog_Entries::FindById($entry_id);
    		
    		$entrytitle = $entry->title; 
    		$entrypublic = $entry->public; 
    		$entrydate = $entry->getDateStart(); 
    		if ( BLOG_ENTRY_IMAGES ) {
        		$possibleimage = $entry->getImage(); 
        		if ( is_object($possibleimage) ) { $entryimage = $possibleimage; } 
            }
    		$entrycontent = $entry->content; 
    		$entryexcerpt = $entry->excerpt; 
    		$entryauthor = $entry->author_id; 
    		$entrytemplate = $entry->template; 
		}
		
		// Get other needed objects
		$the_blog = Blogs::FindById( BLOG_DEFAULT_ID ); 
		$authors = Users::FindAll(); 
		$categories = Categories::FindAll();
		$thisuser = Users::GetCurrentUser();
		
		
		// Get Previous and Next links
        if ( is_object($entry) ) {
            $preventry = $the_blog->getPrevEntry( $entry->date, false ); 
            $nextentry = $the_blog->getNextEntry( $entry->date, false ); 
        }
		
		
		// Warning thrown
		// Double check that the proper columns exist
		$photo_entry_id = find_db_column( 'photos', 'entry_id' );
		if ( ! $photo_entry_id ) {
            echo '<h2 class="system-warning"><span>HCd&gt;CMS says:</span> The Photos table does not have a column called "entry_id"</h2>'; 
        }
        
        
        // Language for the header
        if ( is_object($entry) ) { 
            $header = 'Edit '.ucwords( BLOG_STATIC_AREA ).' Entry :: <a href="'.get_link( BLOG_STATIC_AREA."/view/".$entry->id."/".slug($entry->title) ).'" title="Click to View this Entry (save it first!)">View Entry</a>'; 
        } else {
            $header = 'Create new '.ucwords( BLOG_STATIC_AREA ).' Entry'; 
        }
?>

	<div id="edit-header" class="entrynav threecolumnnav">
		<div class="nav-left column">
			<h1><?php echo $header ?></h1>
		</div>
		<div class="nav-center column">
			<?php if ( ! empty($preventry) ) { ?><a href="<?php echo get_link("/admin/edit_entry/".$preventry->id); ?>" title="<?php $preventry->the_title() ?>">&larr; Previous Entry</a><?php } ?>
		</div>
		<div class="nav-right column">
			<?php if ( ! empty($nextentry) ) { ?><a href="<?php echo get_link("/admin/edit_entry/".$nextentry->id); ?>" title="<?php $nextentry->the_title() ?>">Next Entry &rarr;</a><?php } ?>
			
		</div>
		<div class="clearleft"></div>
	</div>
	
	<form method="POST" id="edit_entry" enctype="multipart/form-data">
		
		<p><span class="hint">If a text box is underlined in red, it is a required field</span></p>
		
		<p class="display_name">
			<label for="display_name">Title:</label>
			<span class="hint">This is the Title of the entry; how it will display in the navigation.</span><br />
			<?php textField("title", $entrytitle, "required: true"); ?>
		</p>
		
		<div id="entry_date" class="column half">
			
			<p><label for="public">Public: <input type="checkbox" name="public" id="public" <?php if ($entrypublic) { ?>checked="checked"<?php } ?>></label> &nbsp; <span class="hint">Visible or not visible to the public? If you are working on an entry that is not yet ready, leave this off until it is complete. </span></p>
			
			<p>
				<label for="date">Entry Date: </label>
				<input type="text" name="date" id="date" value="<?php echo $entrydate; ?>" />
			</p>
        
        <?php if ( ! BLOG_ENTRY_IMAGES ) { ?>
		</div>
		<div class="column half last">
		<?php } ?>
        
			<p>
				<label for="author_id">Author:</label> &nbsp; 
				<select name="author_id" id="author_id">
				<?php
					foreach ( $authors as $theauthor ) {
					
						$selected = ( $theauthor->id == $entryauthor ) ? ' selected="selected"' : '';
						echo "<option value=\"$theauthor->id\"$selected> ".$theauthor->get_username()." </option>\r\n";
					}
				?>
				
				</select>
			</p>
			
			<?php if ( BLOG_ENTRY_TEMPLATES ) { ?>
			<p>
    			<label for="template">Template:</label>
    			<select id="template" name="template">
    				<?php
    					require_once( snippetPath("blog_templates_array") );
    					
    					foreach( $blog_templates as $template ) {
    						echo '<option value="'.$template.'"';
    						if ( $template == $entrytemplate ) { echo ' selected="selected"'; }
    						echo '>'.$template.'</option>';
    					}
    				?>
    				
    			</select>
    		</p>
    		<?php } ?>

        <?php if ( BLOG_ENTRY_IMAGES ) { ?>
		</div>
		<div id="entry-thumb" class="column half last">
			
			<!-- Entry image -->
            <div style="padding-bottom: 1em;">
                <p><label for="entry_image"><?php echo ( empty($entryimage) ) ? 'Add' : 'Edit'; ?> an Entry Image:</label>
    				<input type="file" name="entry_image" id="entry_image" value="" />
    			</p>
    			<p class="hint">An image may be used by your site design on landing pages or menus. </p>
			<?php 
    			    if ( ! empty($entryimage) ) {
                        echo '<h3>Existing Entry Image (reduced in size)</h3>'; 
                        echo '<p><img src="'.$entryimage->getPublicUrl().'" style="max-width:100%;" alt=""></p>'; 
    			    }
    			    echo '</div>'; 
			    } // end if BLOG_ENTRY_IMAGES
			?>

		</div>
		
		<p class="clearleft">
			<label for="entry_content">Content:</label><br />
			<?php textArea("entry_content", $entrycontent, 98, EDIT_WINDOW_HEIGHT/2); ?>
		</p>
		
<?php require_once(snippetPath("admin-insert_configs")); ?>
		
		<ul id="gallery-options-nav" class="menu tabs">
			<li><a href="#section_selector" class="openclose opened">Edit Categories for this Entry</a></li>
		</ul>
		<div id="section_selector" class="dropslide">
			<h2><legend>Select a Category to include this Entry in:</legend></h2>
			<fieldset>
				<p>
			<?php
				$entrycats = ( is_object($entry) ) ? $entry->getCategories() : false; 
				
				foreach ( $categories as $thecategory ) {
					$checked = "";
					 
					if( $entrycats ) {
						foreach( $entrycats as $entry_cat ) {
							if ( $thecategory->id == $entry_cat->id ) {
								$checked = ' checked="checked"';
							}
						}
					}
					echo '<label for="'.slug($thecategory->display_name).'">'.$thecategory->display_name.'&nbsp;';
					echo '<input name="selected_cats[]" id="'.slug($thecategory->display_name).'" class="boxes"'.$checked.' type="checkbox" value="'.$thecategory->id.'" /></label>';			
				}
			?>
				
				</p>					
				<p><span class="hint">Any entry can be in more than one Category. If no categories are selected, this entry will be categorized in the default &ldquo;Uncategorized&rdquo; category.</span></p>
			</fieldset>
		</div><!-- #section_selector -->
        
        
        <p class="clearleft">
			<label for="entry_excerpt">Excerpt:</label><br />
			<?php textArea("entry_excerpt", $entryexcerpt, 98, EDIT_WINDOW_HEIGHT/3); ?>
			<p><span class="hint"><i>Optional:</i> An excerpt is commonly used on landing pages or in special areas, like the meta (SEO) description. Keep it short and limit the use of HTML.</span></p>
		</p>
		
		
		<div id="edit-footer" class="entrynav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Save Entry"> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List">
				</p>
				
			</div>
			<div class="column half last">
			
				<p>
				<?php if ( is_object($entry) ) { ?>
					<label for="delete">Delete this entry?</label>
					<input name="delete" class="boxes" type="checkbox" value='<?php echo $entry->id ?>' />
					<span class="hint">Check the box and then click &ldquo;Save&rdquo; above to delete this entry from the database</span>
				<?php } else { echo '&nbsp;'; } ?>
				</p>
			
			</div>
		</div>
		
	</form>
	
	<script type="text/javascript">
	    var entrydate;
		
		$().ready(function() {
			
			$( "#date" ).datetimepicker({
    			showButtonPanel: true,
    			showOtherMonths: true,
    			selectOtherMonths: true,
    			timeFormat: 'hh:mm:ss tt',
    			stepMinute: 5
    		});

			$("#edit_entry").validate({
				rules: {
					title: "required",
				},
				messages: {
					title: "Please enter a title for this <?php echo BLOG_STATIC_AREA ?> entry",
				}
			});
		});
	</script>
	
<?php } ?>
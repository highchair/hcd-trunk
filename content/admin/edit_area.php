<?php
	function initialize_page()
	{
		// This file does both, so check the parameters first
		if ( requestIdParam() == "add" ) {
    		$area = MyActiveRecord::Create( 'Areas' );
		} else {
    		$area_id = requestIdParam();
    		$area = Areas::FindById( $area_id );
		}
		
		// Only allow specific post actions
		$post_action = ( isset($_POST['submit']) ) ? $_POST['submit'] : null; 
		
		if ( $post_action == "Save Area" || $post_action == "Save and Return to List" ) {
			
			if ( isset($_POST['delete']) ) {
				
				$pages = $area->findPages();
				$selected_sections = array('1');
				foreach ($pages as $page) {
					$page->updateSelectedAreas($selected_sections);
				}
				$area->delete(true);
				setFlash("<h3>Area Deleted</h3>");
				redirect("/admin/list_pages");
			
			} else  {
				
				$area->display_name = $_POST['display_name'];
				$area->seo_title = $_POST['seo_title'];
				$area->template = $_POST['template'];
				
				if ( ! empty( $_POST['name'] ) ) {
    				$oldname = $_POST['display_name'];
    				// Protect the Global Area, the Default Portfolio Area and any placeholders from getting their names changed
    				if ( $area->id != 1 && 
    				    $area->id != 3 && 
    				    $area->name != "site_blog" && 
    				    $area->name != "placeholder" ) {
    					
    					if ( ALLOW_SHORT_PAGE_NAMES ) {
    						$area->name = ($_POST['name'] == "") ? slug($_POST['display_name']) : slug($_POST['name']);
    					} else {
    						$area->name = slug($_POST['display_name']); 
    					}
    				}
				} else { $area->name = slug($_POST['display_name']); }
				
				
				// Allow the possibility to use the word "portfolio" as the last word in the name
				if ( substr( $area->name, -10 ) == "-portfolio" ) {
					// Chop it off the slug so it doesn't turn into a Portfolio Area
					$area->name = substr( $area->name, 0, -10 ); 
				}
				
				
				// Set the public boolean
				if ( requestIdParam() == "add" ) {
    				$area->public = ( MAINTENANCE_MODE ) ? 1 : 0;
				} else {
    				if ( $area->id != 1 ) { $area->public = isset($_POST['public']) ? 1 : 0; }
				}
				
				
				// Save it or create it
				if ( requestIdParam() == "add" ) {
    				// Don't leave off any columns that we dont want to pass values to. And include an empty value for the ID
        			$query = "INSERT INTO `areas` VALUES('','$area->name','$area->display_name','$area->seo_title', '', '1', '$area->template', '$area->public','')";
        			mysql_query($query, MyActiveRecord::Connection()) or die( 'Die: '.$query );
        			
        			setFlash("<h3>New area &ldquo;".$area->display_name."&rdquo; added</h3>");
				} else {
    				
    				$area->save(); 
    				setFlash("<h3>Area changes saved</h3>");
				}
				
				if ( ALIAS_INSTALL ) {
    				if ( ! empty($oldname) ) { $area->checkAlias($oldname); }
				}
				
				if ( $post_action == "Save and Return to List" ) {
					redirect("admin/list_pages"); 
				}
			}
		}
	}
	
	function display_page_content()
	{
		$add_area = ( requestIdParam() == "add" ) ? true : false; 
		
		if ( $add_area ) {
    		$area = $is_global = $area_id = $areadisplayname = $areaseotitle = $areaname = $areapublic = $areatemplate = null;
		} else {
    		$area_id = requestIdParam();
    		$area = Areas::FindById($area_id); 
    		$is_global = ($area->id == 1) ? true : false;
    		$areaid = $area->id; 
    		$areadisplayname = $area->display_name; 
    		$areaseotitle = $area->seo_title; 
    		$areaname = $area->name; 
    		$areapublic = $area->public; 
    		$areatemplate = $area->template; 
		}
?>

<div id="edit-header" class="areanav">
	<div class="nav-left column">
		<h1>
    		<?php 
        		if ( $add_area ) { 
            		echo 'Add Area'; 
        		} else { 
            		echo 'Edit Area : '; 
            		echo '<a href="'.$area->get_url().'" title="View '.$area->get_url().'">View Area</a>'; 
                } 
            ?>
        </h1>
	</div>
	<div class="nav-right column"><?php quick_link(); ?></div>
	<div class="clearleft"></div>
</div>

<?php // Start the form ?>
<form method="POST" id="js-validate">

	<p class="display_name">
		<label for="display_name">Display Name:</label> 
		<?php textField( "display_name", $areadisplayname, "required: true" ); ?><br />
		<span class="hint">This is the Proper Name of the area; how it will display in the navigation.</span>
	</p>
		
<?php 
	// If not Index, not a Portfolio Area, and not the Blog area
	if ( $is_global != 1 && !strstr($areaname, "_portfolio") && $area_id != 3 ) { 
		if ( ALLOW_SHORT_PAGE_NAMES ) { 
?>
	<p>
		<label for="name">Short Name</label> 
		<?php textField("name", $areaname); ?><br />
		<span class="hint">This is the short name of the page, which gets used in the URL. No spaces, commas, or quotes please.</span>
	</p>
<?php 
		}
	} else { hiddenField("name", $areaname); }
    
    if ( ! $add_area ) { 
        echo '<p class="page-url">Area URL: <span class="page-url">http://'.SITE_URL.'/<mark>'.ltrim( $area->get_url(), "/").'</mark></span></p>'; 
    } 
?>
    
	<div class="column half">
    <?php if ( ! $is_global ) { ?>
		<p>
			<label for="public">Public:</label>&nbsp; <?php checkBoxField("public", $areapublic); ?><br />
			<span class="hint">This determines whether or not the Area will appear in the navigation as a &ldquo;Public&rdquo; link. You may place new pages inside this Area and make them &ldquo;public&rdquo;, but they will still not be visible until the Area is also Public. </span>
		</p>
	<?php } else { echo '<p><input type="hidden" name="public" value="1"></p>'; } ?>
	
    </div>
	<div class="column half last">
		<p>
			<label for="seo_title">SEO Title:</label>
			<?php textField( "seo_title", $areaseotitle ); ?><br />
			<span class="hint">This title is used in title meta tags (good for SEO). Might also show when a user hovers their mouse over a link. Best to be as short as possible.</span>
		</p>
	</div>
	<div class="clearleft"></div>

    <?php // Template ?>
	<p><label for="template">Template:</label>
		<select id="template" name="template">
		<?php
			$templates = list_available_templates();
			foreach ( $templates as $template ) {
                $thistemplate = ( $template == $areatemplate ) ? ' selected="selected"': '';
                
				echo '<option value="'.$template.'"'.$thistemplate.'>'.ucwords($template).'</option>';
			}
		?>
		</select><br />
		<span class="hint">When a Page inside this Area uses the template &ldquo;inherit&rdquo;, the Page will inherit this Area&rsquo;s template selection. So, changing this Template may change the display of all Pages within this Area. </span>
	</p>
		
	
	<div id="edit-footer" class="areanav clearfix">
		<div class="column half">
			<p>
				<input type="submit" class="submitbutton" name="submit" value="Save Area" /> <br />
				<input type="submit" class="submitbuttonsmall" name="submit" value="Save and Return to List" />
			</p>
		</div>
		<div class="column half last">
		<?php 
			$user = Users::GetCurrentUser();
			if ( 
			    $user->has_role() && 
			    ! in_array($area_id, explode(",",PROTECTED_ADMIN_AREAS)) && 
			    requestIdParam() != "add" ) { 
		?>
		
			<p>
				<label for="delete">Delete this Area?</label>
				<input name="delete" class="boxes" type="checkbox" value="<?php echo $area_id ?>" />
				<span class="hint">Check the box and then click &ldquo;Edit&rdquo; above to delete from the database. This will move any pages contained within this area to the Global Area, so they do not become Orphans.</span></p>
		<?php } elseif ( requestIdParam() != "add" ) { ?>
			
			<p class="red">This area is being protected, it can not be deleted.</p>
		<?php } ?>
		</div>
	</div>
		
</form>
	
<script type="text/javascript">
	$().ready(function() {
		$("#js-validate").validate({
			rules : {
				display_name: "required"
			},
			messages: {
				display_name: "Please enter a name you would like to be displayed for this area"
			}
		});
	});

</script>
<?php } ?>
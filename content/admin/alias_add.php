<?php
	function initialize_page()
	{
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if( $post_action == "Add Alias" || $post_action == "Add and Return to List" )
		{

			$alias = MyActiveRecord::Create('Alias');
			$alias->alias = $_POST['alias'];
			$alias->path = $_POST['path'];
			$alias->save();
	
			setFlash("<h3>Alias created</h3>");
			if ( $post_action == "Add and Return to List" )
				redirect("/admin/alias_list");
		}
	}
	
	function display_page_content()
	{		
?>

	<script type="text/javascript">
		function in_array (needle, haystack, argStrict) {
		  	var key = '', strict = !!argStrict; 
		    if (strict) {
		        for (key in haystack) {
		            if (haystack[key] === needle) {
		                return true;            
		            }
		        }
		    } else {
		        for (key in haystack) {
		            if (haystack[key] == needle) {
		            	return true;
		            }
		        }
		    }
		    return false;
		}
		$().ready(function() {
			var area = new Array(<?php 
				$array = "";
				foreach ($areas as $area)
				{
					$array.= "\"{$area->name}\", ";
				}
				echo substr($array,0, -6);
			?>);
			jQuery.validator.addMethod("is_area", function(value, element) {
				console.log(in_array(value, area, false));
				return this.optional(element) || !in_array(value, area, false); 
			});
			
			$("#edit_alias").validate({
				rules : {
					alias: {
						required: true,
						is_area: true
					},
					path: "required"
				},
				messages: {
					alias: "Please enter an alias, or make sure this is not also the name of an existing Area",
					path: "Please enter an path for this alias"
				}
			});
		});
	</script>
	
	<div id="edit-header" class="aliasnav">
		<h1>Add Alias</h1>
	</div>
	
	<form method="POST" id="edit_alias">
		<p>When a user uses the Alias Path below in their browser, the HCd system will redirect them to the specified path below. Please make sure the page/event/blog entry exists or the system will not let you create the Alias. Also, the alias should not have the same name as an existing Area in the system (the system will warn you if it does). </p>
		
		<p class="display_name">
			<label for="alias">Alias:</label>
			<?php textField("alias", '', "required: true"); ?>
			<span class="hint">Enter only the address that will go after your site root, <em><?php echo SITE_URL ?></em>. If this matches an existing Area in the system, a warning will appear.</span>
		</p>
		
		<p>
			<label for="path">Path: </label>
			<?php textField("path", '', "required: true"); ?>
		</p>
			
		<div id="edit-footer" class="aliasnav clearfix">
			<div class="column half">
		
				<p>
					<input type="submit" class="submitbutton" name="submit" value="Add Alias" /> <br />
					<input type="submit" class="submitbuttonsmall" name="submit" value="Add and Return to List" />
				</p>
				
			</div>
			<div class="column half last"></div>
		</div>
	
	</form>
<?php } ?>
<?php
	function initialize_page()
	{
		if(IsUserLoggedIn()) { } else { redirect('/'); }
	}
	
	function display_page_content()
	{
		//$images = Images::FindAll();
	?>
	
	<script type="text/javascript">
		$().ready(function() {
			
			// This is in the admin template, but not in THIS dom...
			$(".droplink").click(function() {
				$("#opendropslides .dropslide").each(function() {
					$(this).slideUp('fast');
				});
				var link = $(this).attr('title');
				$('#'+link).slideDown('fast');
			});
			
			$('#description_add').click(function() {
				$('#session_add').load('<?php echo BASEHREF; ?>blaster/session_add/custom_html/true', function() { 
					$("#custom_text_removed").slideUp(0);
					$("#custom_text_added").slideDown();
				});
				return false;
			});
			
			$('#description_remove').click(function() {
				$('#session_add').load('<?php echo BASEHREF; ?>blaster/session_add/custom_html/false', function() { 
					$("#custom_text_added").slideUp(0);
					$("#custom_text_removed").slideDown();
				});
				return false;
			});
		});
	</script>
	
	<p><label for="name">Custom Text:</label> <span class="red">Be sure to click &ldquo;Add Text&rdquo; below to make this option active. Your text will not be added unless the button has been clicked. </span><br />
    	<textarea name="description" id="description" rows="24" style="width: 98%"></textarea>
	</p>
	
	<p><a href="#insert" class="droplink" title="img_insert">Insert Image</a>
	<a href="#insert" class="droplink" title="doc_insert">Insert Documents</a>
	<a href="#insert" class="droplink">(hide)</a></p>
	<div id="opendropslides">
	<?php
		require_once(snippetPath("admin-insertImage")); 
		require_once(snippetPath("admin-insertDoc"));
	?>
	</div>
	
	<p><a href="#" id="description_add" class="submitbutton smaller">Add Custom HTML</a>&nbsp; &nbsp;
	<a href="#" id="description_remove" class="submitbutton smaller">Remove Custom HTML</a></p>
	
	<div id="custom_text_added" class="blast_notice" style="display: none;">Custom Text Added <small>(to remove, use the button above)</small></div>
	<div id="custom_text_removed" class="blast_notice" style="display: none;">Custom Text Removed</div>
	
	<?php
	}
?>
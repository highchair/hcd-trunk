<?php
	function initialize_page()
	{
		$post_action = "";
		if ( isset($_POST['submit']) ) {
			$post_action = $_POST['submit'];
		}
		
		if ( $post_action == "Delete Selected" )
		{
			foreach ($_POST['delete'] as $blast_id)
			{
				$blast = MailBlast::FindById($blast_id);
				$blast->delete();
			}
			setFlash("<h3>Mail Blasts Updated</h3>");
		}
	}
	
	function display_page_content()
	{
		$blasts = MailBlast::FindAll();
?>
		
<div id="edit-header" class="sentblasts">
	<h1>View or Delete Old Email Blasts</h1>
</div>
		
<form id="blast_list_form" method="POST">		
	<p>Click on the blast name to view it. Check the box and then click &ldquo;Save&rdquo; below to delete that blast from the database. </p>

	<div id="table-header">
		<span class="item-link">Click Name to View</span>
		<span class="item-public">Date Sent</span>
		<span class="item-revised">To List</span>
		<span class="item-created">Delete Option</span>
	</div>
	<ul id="listitems" class="managelist">
<?php
	foreach( $blasts as $blast )
	{
		$list = NLLists::FindById( $blast->list_id );
		$subject = ( $blast->email_subject != '' ) ? $blast->email_subject : 'Sent on '.$blast->date_sent; 
		
		echo "\t\t<li>
		    <a target=\"_blank\" class=\"item-link\" href=\"" . get_link("/mail/blast/$blast->hash") . "\">
		        $subject
            </a> &nbsp; 
            <span class=\"item-public\">".formatDateView( $blast->date_sent )."</span>
			<span class=\"item-revised\">".$list->display_name."</span>
			<span class=\"item-created\"><input name=\"delete[]\" type=\"checkbox\" value=\"$blast->id\" /> Delete?</span>
        </li>\r\n";
	}
?>

	</ul>
	
	<div id="edit-footer">
    	<p><input type="submit" class="submitbutton" name="submit" value="Delete Selected" /></p>
    </div>
		
</form>
<?php
	}
?>
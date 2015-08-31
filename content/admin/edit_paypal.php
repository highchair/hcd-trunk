<?php
	function initialize_page()
	{
		LoginRequired("/admin/login/", array("admin"));
		
		$accnt_id = requestIdParam();
		$account = Paypal_Config::FindById($accnt_id);
		
		$post_action = "";
		if(isset($_POST['submit']))
		{
			$post_action = $_POST['submit'];
		}
	
		if($post_action == "Save")
		{
			
			$account->email = $_POST['email'];
			$account->success_url = $_POST['success_url'];
			$account->cancel_url = $_POST['cancel_url'];
			
			$account->save();
	
			setFlash("<h3>Paypal account changes saved</h3>");
		}
	}
	
	function display_page_content()
	{
		$accnt_id = requestIdParam();
		$account = Paypal_Config::FindById($accnt_id);
?>

<script type="text/javascript">
	$().ready(function() {
		$("#edit_paypal").validate({
			rules: {
					email: { required: true, email: true }
				},
			messages: {
					email: "Please enter a valid email address"
				}
		});
	});
</script>

<form id="edit_paypal" method="POST">

	<h1>Edit Account "<?php echo $account->account_name ?>"</h1>
	
	<p class="announce">There is one PayPal account associated with <a href="<?php echo BASEHREF ?>admin/list_products">products</a> on your site. To change the email associated with products and your PayPal account, edit it here. If you need more than one account &ndash; say, for different types of products &ndash; then let us know, and we can add that functionality.</p>
	<p>&nbsp;</p>
	
	<p><label for="email">Email:</label>
	<?php textField("email", $account->email, "required: true"); ?></p>
	
	<p><label for="email">Success URL:</label>
	<?php textField("success_url", $account->success_url); ?><br />
	<span class="hint">Optional address to send people to when they successfully complete a checkout</span></p>
	
	<p><label for="email">Cancel URL:</label>
	<?php textField("cancel_url", $account->cancel_url); ?><br />
	<span class="hint">Optional address to send people to when they decline or cancel a checkout</span></p>
		
	<input type="submit" class="submitbutton" name="submit" value="Save" />
</form>
<?php } ?>
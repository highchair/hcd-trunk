<?php
class Users extends ModelBase
{
	function FindAll()
	{
		/*
		 * Columns: id, display_name, email, password, is_admin, is_staff
		 */
		return MyActiveRecord::FindAll('Users', NULL, " id ASC");
	}

	function FindByEmail($email)
	{
		//die("SELECT p.* FROM pages p INNER JOIN areas_pages ap ON ap.pages_id = p.id INNER JOIN areas a ON a.id = ap.areas_id WHERE p.name like '" . $name . "' AND a.name like '" . $area->name . "' ORDER BY display_order, id ASC");
		return array_shift(MyActiveRecord::FindBySql('Users', "SELECT * FROM users WHERE email = '${email}'"));
	}

	function FindById($id)
	{
		return MyActiveRecord::FindById('Users', $id);
	}

	function FindNonAdmins()
	{
		return MyActiveRecord::FindBySql('Users', "SELECT * FROM users WHERE is_admin = 0");
	}

	function FindWithRole($role = "")
	{
		return MyActiveRecord::FindBySql('Users', "SELECT * FROM users WHERE is_$role = 1");
	}

	function GetCurrentUser()
	{
		if( !isset($_SESSION[LOGIN_TICKET_NAME]) ) {
			return null;
		}
		$user = null;
		$user_id = $_SESSION[LOGIN_TICKET_NAME];
		if( $user_id ) {
			$user = Users::FindById($user_id);
		}
		return $user;
	}

	function hash_with_salt($password)
	{
		return sha1(SHA_SALT . $password);
	}

	function authenticate($password)
	{
		return $this->password == $this->hash_with_salt($password);
	}

	function set_ticket()
	{
		unset($_SESSION[LOGIN_TICKET_NAME]);
		$_SESSION[LOGIN_TICKET_NAME] = $this->id;	
	}

	function ClearCurrentUser()
	{
		unset($_SESSION[LOGIN_TICKET_NAME]);	
	}

	function hash_password()
	{
		$this->password = $this->hash_with_salt($this->password);
	}

	function SetRedirect()
	{
		$redirect_key = "REDIRECT_ON_LOGIN";
		if(isset($_SESSION[$redirect_key]))
		{
			unset($_SESSION[$redirect_key]);
		}
		
		$_SESSION[$redirect_key] = "/" . implode("/", $GLOBALS["REQUEST_PARAMS"]);
	}

	function GetRedirect($default = "/")
	{
		$redirect_key = "REDIRECT_ON_LOGIN";
		if(!isset($_SESSION[$redirect_key]))
		{
			unset($_SESSION[$redirect_key]);
			return $default;
		}
		
		$redirect = $_SESSION[$redirect_key];
		if($redirect == "/admin/logout")
		{
			$redirect = "/admin";
		}
		unset($_SESSION[$redirect_key]);
		return($redirect);
	}

	function has_role($role = "")
	{
		if($this->is_admin) {
			return true;
		}
		$role_to_check = strtolower($role);
		$role_key_name = "is_$role_to_check";
		if ( $this->$role_key_name ) {
			return true;
		} else { 
			return false;
		} 
	}

	function send_newuser_email( $unsaltedpass )
	{
		$email_message = "Hello new user, \r\n
A new account has been created for you at ".SITE_URL.BASEHREF."admin/ \r\n
\r\n
Please log in with this email address and the password ".$unsaltedpass.". \r\n
\r\n
Once logged in, please look to the right for \"Logged in as...\" under the \"Logout\" link. Click the link \"Edit Your Password\" to change your password to something more memorable. \r\n
\r\n
Thanks \r\n
- the HCd Content Management System"; 
		
		$from = "admin@".substr(SITE_URL,4); // This only works if the SITE_URL starts with "www."
			
		// Send an email message. Set headers...
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// Additional headers
		$headers .= "From: HCd Content Management System <".$from.">\r\n";
		
		$subject = "New User Account at ".SITE_URL;
		$message_to = $this->email; 
		
		// Mail it. 
		hcd_sendmail( $message_to, $subject, $email_message, $from, $html=true ); 
	}
}
?>
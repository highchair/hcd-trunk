<?php 
	/* Lots of variables get set in this file */
	
	include_once( snippetPath("globals") ); 
	include_once( snippetPath("header") ); 
	
	/* Available as = 
	 * $page, $area or $area->is_portfolioarea(), is_object( $current_user )
	 * $page_title, $description, $bodyclass, $template
	 * getRequestVarAtIndex()s = $var0, $var1, $var2, $var3
	 * isset( $blogarea ), isset( $category )
	 * isset( $event )
	 * isset( $item )
     * If we use "is_object" for some of the above, we get an "undefined" error is they are not set
     * I prefer empty(), as it returns false if not set at all and true if not false or an array or just not null. 
	 */
?>
    
                        <div class="col-row__default">
                            
                            <div class="not-column typography">
                                <?php $page->the_title( '<h1 class="content--title">', '</h1>' ) ?>
                            </div>
                            
                            <article class="column column--main" role="main">  
                                <div id="contact" class="contact--form">
                                    <h4 class="widget--title">Send us a Message</h4>
                                    <?php
                                    	// Collect the mail data and post it
                                    	if ( count($_POST) ) {
                                    		
                                    		$valid = true; 
                                    		
                                    		$antispam = $_POST['address'];
                                    		
                                    		$theirname = esc_html($_POST['name']);
                                    		$email = esc_html($_POST['email']);
                                    		if ( ! preg_match("([A-Za-z0-9_\-\+.]+[@]+[A-Za-z0-9_\-\.]+[.]+[A-Za-z0-9]{2,24})", $email) ) {
                                        		// The email address does not have a valid format
                                        		echo '<h2 class="failure">The email address was invalid. Please check and try again.</h2>'; 
                                        		$valid = false; 
                                    		}
                                    		$phone = esc_html($_POST['phone']);
                                    		$message = esc_html($_POST['message']);
                                    
                                    		if ( $antispam == '' and $valid ) {
                                    			//$to      = 'highchairdesignhaus@gmail.com';
                                    			$to      = CONTACT_EMAIL;
                                    			$subject = "Email from MagmaDesignGroup.com contact page";
                                    			
                                    			$bodyofmessage = "Name: ".$theirname."\n";
                                    			$bodyofmessage .= "From: ".$email."\n"; 
                                    			$bodyofmessage .= "Phone: ".$phone."\n\n";
                                    			$bodyofmessage .= "\n".$message."\n";
                                    			
                                    			$headers = "From: $email" . "\r\n" . "Reply-To: $email" . "\r\n" . "X-Mailer: PHP/" . phpversion();
                                    			
                                    			mail( $to, $subject, $bodyofmessage, $headers );
                                    			
                                    			echo '<h2 class="success">Thanks for your message &mdash; it was sent successfully.</h2>'; 
                                    			$theirname = $email = $phone = $message = ''; 
                                    		} 
                                    	} else {
                                        	$theirname = $email = $phone = $message = ''; 
                                    	}
                                    ?>
                                    
                                    <form class="form sitefoot--form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>#contact">
                                        <legend class="screen-reader-text">Send us an email</legend>
                                        <label for="name" class="screen-reader-text">Name</label>
                                        <input type="text" id="name" name="name" class="form--input" placeholder="Name" maxlength="128" required="required" tabindex="1" value="<?php echo $theirname ?>">
                                        <label for="email" class="screen-reader-text">Email</label>
                                        <input type="email" id="email" name="email" class="form--input" placeholder="Email" maxlength="128" required="required" tabindex="2" value="<?php echo $email ?>">
                                        <label for="phone" class="screen-reader-text">Phone</label>
                                        <input type="tel" id="phone" name="phone" class="form--input" placeholder="Phone (optional)" maxlength="14" tabindex="3" value="<?php echo $phone ?>">
                                        <label for="address" class="off-screen">Address</label>
                                        <input type="text" id="address" name="address" class="form--input off-screen" placeholder="Address" maxlength="256">
                                        <label for="message" class="screen-reader-text">Message</label>
                                        <textarea id="message" name="message" class="form--input form--textarea" placeholder="A short message&hellip;" maxlength="1024" rows="6" required="required" tabindex="4"><?php echo $message ?></textarea>
                                        <button type="submit" class="button secondary-action" tabindex="5" onClick="_gaq.push(['_trackEvent', 'Submit', 'Send', 'User submitted via the Contact page email form']);">Send</button>
                                    </form>
                                </div>
                                
                            </article>
                            
                            <aside class="content--sidebar column column--aside" role="complementary">
                                <div class="widget contact--content content--main typography">
                                    <?php $page->the_content() ?>
   
                                </div>
                            </aside>
                            
                        </div>

<?php 
	include_once( snippetPath("footer") );
?>
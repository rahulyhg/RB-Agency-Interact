<?php
// *************************************************************************************************** //
// Respond to Login Request

	if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {
	
		global $error;
		$login = wp_login( $_POST['user-name'], $_POST['password'] );
		$login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => $_POST['remember-me'] ), false );


			global $user_ID;
			if( $user_ID ) {
				$user_info = get_userdata( $user_ID ); 
				// If user_registered date/time is less than 48hrs from now
				// Message will show for 48hrs after registration
				if ( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/account/");
				} elseif( current_user_can( 'manage_options' )) {
					header("Location: ". get_bloginfo("wpurl"). "/wp-admin/");
				} else {
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
				}
			}
	}


// ****************************************************************************************** //
// Already logged in 
	if (is_user_logged_in()) { 
	
	
		global $user_ID; 
		$login = get_userdata( $user_ID );
			
			/*
			echo "    <p class=\"alert\">\n";
						printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', rb_agencyinteract_TEXTDOMAIN), "/profile-member/", $login->display_name );
			echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', rb_agencyinteract_TEXTDOMAIN) ."\">". __('Log out &raquo;', rb_agencyinteract_TEXTDOMAIN) ."</a>\n";
			echo "    </p><!-- .alert -->\n";
			*/
	
// ****************************************************************************************** //
// Not logged in
	} else { 

		// *************************************************************************************************** //
		// Prepare Page
		get_header();

		echo "<div id=\"container\" class=\"one-column rb-agency-interact-account\">\n";
		echo "  <div id=\"content\">\n";
		
			// Show Login Form
			$hideregister = true;
			include("include-login.php"); 	

		echo "  </div><!-- #content -->\n";
		echo "</div><!-- #container -->\n";
		
		get_sidebar();
		get_footer();
	
	} // Done
?>
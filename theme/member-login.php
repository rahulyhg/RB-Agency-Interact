<?php
session_start();
// *************************************************************************************************** //
// Respond to Login Request
$error = "";
$have_error = false;
	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
	$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
	$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
	$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
	$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {
	global $error;
    $login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => isset($_POST['remember-me'])?$_POST['remember-me']:false ), false );
    get_currentuserinfo();
	if(!is_wp_error($login)) {
		wp_set_current_user($login->ID,$login->user_id);// populate
        wp_set_auth_cookie( $login->ID );
        do_action( 'wp_login', $login->user_id );
		get_user_login_info();
	} else {
			$error .= __( $login->get_error_message(), RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
	}
}
//fixed for this site only (password reset form)
if($_GET['action'] == 'rp'){
	$_SESSION['login_username'] = $_GET['login'];
	wp_redirect(site_url()."/wp-login.php?action=".$_GET['action']."&key=".$_GET['key']."&login=".$_GET['login']);
	exit();
}
if($_GET['action'] == 'resetpass'){
	global $wpdb;
	$uid = '';
	$user_table = $wpdb->prefix.'users';
	$users = $wpdb->get_results("SELECT ID FROM $user_table WHERE user_login =  '".$_SESSION['login_username']."'");
	foreach($users as $user){
		$uid = $user->ID;
	}
	//echo $uid;
	wp_set_password( $_POST['pass1-text'], $uid ) ;
}
function get_user_login_info(){
	// get options
    global $user_ID, $wpdb;
	$redirect = isset($_POST["lastviewed"])?$_POST["lastviewed"]:"";
	get_currentuserinfo();
	$user_info = get_userdata( $user_ID );
    // Check if user is registered as Model/Talent
    $profile_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_profile." WHERE ProfileUserLinked = %d  ",$user_ID));
    $is_model_or_talent  = $wpdb->num_rows;
   $rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
	$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
	$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
	$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
	$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
    if(isset($user_ID) && ($is_model_or_talent > 0) || current_user_can("edit_posts")){
			// If user_registered date/time is less than 48hrs from now
			if(!empty($redirect)){
				wp_redirect($redirect);
			} else {
				// If Admin, redirect to plugin
				if(current_user_can("edit_posts")) {
					wp_redirect(admin_url());
					exit();
				}
				// Message will show for 48hrs after registration
				/*elseif( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {
					if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
							wp_redirect(get_bloginfo("url"). "/casting-dashboard/");
					} else {
							wp_redirect(get_bloginfo("url"). "/profile-member/");
					}
				}*/
				else {
						$rb_agency_new_registeredUser = get_user_meta($user_ID,'rb_agency_new_registeredUser',true);
						if(!empty($rb_agency_new_registeredUser)){
							/* delete_user_meta( $user_ID, '_lastlogin' );
							echo 'data reset .. ';
							exit; */
					        $meta = get_user_meta( $user_ID,'_lastlogin', true );
							update_user_meta( $user_ID, '_lastlogin', 'true');
							$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
							if( empty($meta)){
								//echo 'welcome first time.';
								$url = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'];
								if( !empty($url)){
									$customUrl = '/profile-member/account/';
								}else{
									$customUrl = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'];
								}
							}
							else{
								//echo 'Welcome back.';
								$url = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'];
								if( !empty($url)){
									$customUrl = '/profile-member/account/';
								}else{
									$customUrl = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'];
								}
							}
								if($rb_agencyinteract_option_redirect_first_time == 1){
										wp_redirect(get_bloginfo("url"). "/profile-member/account/");
								} else {
									wp_redirect($rb_agencyinteract_option_redirect_first_time_url);
								}
										//echo $user_ID. 'xx'.$customUrl;
										//exit;
										/* echo get_bloginfo("url") . $customUrl;
										exit; */
								//wp_redirect(get_bloginfo("url") . $customUrl);
						} else {
							if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
									wp_redirect(get_bloginfo("url"). "/casting-dashboard/");
							} else {
								if($rb_agencyinteract_option_redirect_afterlogin == 1){
									if(isset($_GET["h"])){
										wp_redirect(get_bloginfo("url").$_GET["h"]);
										exit();
									}else{
										wp_redirect(get_bloginfo("url"). "/profile-member/");
									}
								} else {
									if(isset($_GET["h"])){
										wp_redirect(get_bloginfo("url").$_GET["h"]);
										exit();
									}else{
										wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
									}
								}
							}
						}
				}
				}
	} elseif($profile_is_active->ProfileIsActive == 3){
					wp_redirect(get_bloginfo("url"). "/profile-member/pending/");
	} else {
			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
					$is_casting  = $wpdb->num_rows;
					if( $is_casting > 0){
					wp_logout();
					wp_redirect(get_bloginfo("url"). "/profile-login/?ref=casting");
				} else { // user is a model/talent but wp user_id is not linked to any rb profile.
					if($rb_agencyinteract_option_redirect_afterlogin == 1){
						if(isset($_GET["h"])){
							wp_redirect(get_bloginfo("url").$_GET["h"]);
							exit();
						}else{
							wp_redirect(get_bloginfo("url"). "/profile-member/");
						}
					} else {
						if(isset($_GET["h"])){
							wp_redirect(get_bloginfo("url").$_GET["h"]);
							exit();
						}else{
							wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
						}
					}
				}
	}
}
add_filter('login_redirect', 'rb_agency_interact_login_redirect', 10, 3);
// ****************************************************************************************** //
// Already logged in 
	if (is_user_logged_in()) {
		global $user_ID;
		if(current_user_can("edit_posts")) {
			wp_redirect(admin_url("admin.php?page=rb_agency_menu"));
			exit();
		}
		//redirect to job
		if(isset($_GET["h"])){
			wp_redirect(get_bloginfo("url").$_GET["h"]);
			exit();
		}
		$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;
		if( $is_casting > 0){
			wp_redirect(get_bloginfo("url"). "/casting-dashboard/");
		} else { // user is a model/talent but wp user_id is not linked to any rb profile.
			if($rb_agencyinteract_option_redirect_afterlogin == 1){
				wp_redirect(get_bloginfo("url"). "/profile-member/");
			} else {
				wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
			}
		}
		get_user_login_info();
		// Call Header
		echo $rb_header = RBAgency_Common::rb_header();
		echo "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";
		global $user_ID; 
		$login = get_userdata( $user_ID );
			echo "    <p class=\"alert\">\n";
						printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', RBAGENCY_interact_TEXTDOMAIN), "/profile-member/", $login->display_name );
			echo "		<a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', RBAGENCY_interact_TEXTDOMAIN) ."\">". __('Log out &raquo;', RBAGENCY_interact_TEXTDOMAIN) ."</a>\n";
			echo "    </p><!-- .alert -->\n";
		echo "</div><!-- #rbcontent -->\n";
		// Call Footer
		echo $rb_footer = RBAgency_Common::rb_footer();
// ****************************************************************************************** //
// Not logged in
	} else {
// *************************************************************************************************** //
		// Prepare Page
		// Call Header
		echo $rb_header = RBAgency_Common::rb_header();
			echo "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";
				// Show Login Form
				$hideregister = true;
				include("include-login.php");
			echo "</div><!-- #rbcontent -->\n";
		// Call Footer
		echo $rb_footer = RBAgency_Common::rb_footer();
	}// Done
?>
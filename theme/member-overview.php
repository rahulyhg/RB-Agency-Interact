<?php
/*
Template Name: 	Member Details
 * @name		Member Details
 * @type		PHP page
 * @desc		Member Details
*/

if (!headers_sent()) {
header("Cache-control: private"); //IE 6 Fix
}
global $wpdb;

/* Get User Info ******************************************/ 
global $current_user;
get_currentuserinfo();

// Get Settings
$rb_agency_options_arr 							= get_option('rb_agency_options');
$rb_agency_option_profilenaming 				= isset($rb_agency_options_arr['rb_agency_option_profilenaming']) ?(int)$rb_agency_options_arr['rb_agency_option_profilenaming']:"";
$rb_agency_interact_options_arr 					= get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_registerallow 		= isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow']) ?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow']:"";
$rb_agencyinteract_option_overviewpagedetails 	= isset($rb_agency_interact_options_arr['rb_agencyinteract_option_overviewpagedetails']) ? (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_overviewpagedetails']:"";

// Check Sidebar
$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_profilemanage_sidebar = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']) ?$rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']:"";
$rb_subscription = isset($rb_agency_options_arr['rb_agencyinteract_option_profilelist_subscription']) ?$rb_agency_options_arr['rb_agencyinteract_option_profilelist_subscription']:"";

// Were they users or agents?
$profiletype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)){ $profiletypetext = __("Agent/Producer", rb_agency_interact_TEXTDOMAIN); } else { $profiletypetext = __("Model/Talent", rb_agency_interact_TEXTDOMAIN); }



// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Member Overview";
	}

/* Display Page ******************************************/ 


// Call Header
echo $rb_header = RBAgency_Common::rb_header();
	
	echo "	<div id=\"primary\" class=\"col_12 column rb-agency-interact rb-agency-interact-overview\">\n";
	echo "  	<div id=\"content\">\n";

		// get profile Custom fields value
		$rb_agency_new_registeredUser = get_user_meta($current_user->ID,'rb_agency_new_registeredUser',true);
		
	
		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) { 

                    			/*
			 * Set Media to not show to
			 * client/s, agents, producers,
			 */
			$ptype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
			$ptype = retrieve_title($ptype);
			$restrict = array('client','clients','agents','agent','producer','producers');
	if(empty($rb_agency_new_registeredUser) && rb_get_user_profilstatus() == 3){
		echo "Thank you for joining ".get_bloginfo("name")."! Your account is pending for approval. We will send you an email once your account is approved.";
		$profile_gallery = $wpdb->get_row($wpdb->prepare("SELECT ProfileGallery FROM ".table_agency_profile." WHERE ProfileUserLinked = %d",$current_user->ID));
		echo "<a href=\"". get_bloginfo("wpurl") ."/profile/".$profile_gallery->ProfileGallery."\">View My Profile</a> |";
		echo "<a href=\"". get_bloginfo("wpurl") ."/profile-member/account/\">Manage Account</a>";
		
	}else{
		    if(!empty($rb_agency_new_registeredUser)){
				if(in_array(strtolower($ptype),$restrict)){
					echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 2</div>\n";
				} else {
					echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 3</div>\n";
				}
			}
			

			echo "	<div id=\"profile-manage\" class=\"profile-overview\">\n";

			/* Check if the user is regsitered *****************************************/ 
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = mysql_query($sql);
			$count = mysql_num_rows($results);
			if ($count > 0) {

			// Menu
			include("include-menu.php");
			echo " <div class=\"manage-overview manage-content\">\n";
			  
			$data = mysql_fetch_array($results);  // is there record?
				  
				echo "	 <div class=\"manage-section welcome\">\n";
				echo "	 <h1>". __("Welcome Back", rb_agency_interact_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";
				// Record Exists
			
				/* Show account information here *****************************************/
				 
				echo " <div class=\"section-content section-account\">\n"; // .account
				echo " 	<ul>\n";
				echo "      <li><a href=\"account/\">Edit Your Account Details</a></li>\n";
				echo "      <li><a href=\"manage/\">Manage Your Profile Information</a></li>\n";
				echo "      <li><a href=\"media/\">Manage Photos and Media</a></li>\n";
				if($rb_subscription){
				echo "      <li><a href=\"subscription/\">Manage your Subscription</a></li>\n";
				}
				if(function_exists('rb_agency_casting_menu')){
					if(rb_get_user_profilstatus() != 3){
							echo "      <li><a href=\"".get_bloginfo('wpurl')."/browse-jobs/\">Browse and Apply for a Job</a></li>\n";
					}
				}
				echo "      <li><a href=\"".get_bloginfo('wpurl')."/logout/\">Log out</a></li>\n";
				
				echo "	</ul>\n";
				if(function_exists('rb_agency_casting_menu')){
					echo "</hr>\n";
					echo "<h3>Jobs and Auditions</h3>";

				}
				echo " </div>\n";
			  	echo " </div>\n"; // .welcome
			  	echo " </div>\n"; // .profile-manage-inner
			  
			// No Record Exists, register them
			} else {
					
				echo "<h1>". __("Welcome", rb_agency_interact_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";

				if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)){
					echo "<p>". __("We have you registered as", rb_agency_interact_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong></p>";
					echo "<h2><a href=\"". $rb_agency_interact_WPURL ."/profile-search/\">". __("Begin Your Search", rb_agency_interact_TEXTDOMAIN) ."</a></h2>";
					
					echo " <div id=\"subscription-customtext\">\n";
					$Page = get_page($rb_agencyinteract_option_subscribepagedetails);
					echo apply_filters('the_content', $Page->post_content);
					echo " </div>";

				} else {
					if ($rb_agencyinteract_option_registerallow == 1) {

						// Users CAN register themselves
						echo "". __("We have you registered as", rb_agency_interact_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong>";
						echo "<h2>". __("Setup Your Profile", rb_agency_interact_TEXTDOMAIN) ."</h2>";
					
						// Register Profile
						include("include-profileregister.php");
					} else {
					
						// Cant register
						echo "<strong>". __("Self registration is not permitted.", rb_agency_interact_TEXTDOMAIN) ."</strong>";
					}
				}
			}
		} // if pending for approval
			echo "</div><!-- #profile-manage -->\n";

		} else {

			// Show Login Form
			include("include-login.php");
		}
		
	echo "  </div><!-- #content -->\n";
	echo "</div><!-- #primary -->\n";

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>
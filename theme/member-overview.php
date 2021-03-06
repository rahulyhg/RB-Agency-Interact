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
if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)){$profiletypetext = __("Agent/Producer", RBAGENCY_interact_TEXTDOMAIN); } else {$profiletypetext = __("Model/Talent", RBAGENCY_interact_TEXTDOMAIN); }



// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return __("Member Overview",RBAGENCY_interact_TEXTDOMAIN);
	}

/* Display Page ******************************************/ 


// Call Header
echo $rb_header = RBAgency_Common::rb_header();

	echo "	<div id=\"primary\" class=\"rb-agency-interact member-overview\">\n";
	echo "  	<div id=\"rbcontent\">\n";

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
			echo "<p class=\"rbalert success\">";
			echo "	Thank you for joining ".get_bloginfo("name")."! Your account is pending for approval. We will send you an email once your account is approved.";
			$profile_gallery = $wpdb->get_row($wpdb->prepare("SELECT ProfileGallery FROM ".table_agency_profile." WHERE ProfileUserLinked = %d",$current_user->ID));
			echo "<a href=\"". get_bloginfo("wpurl") ."/profile/".$profile_gallery->ProfileGallery."\">View My Profile</a>";
			echo "<a href=\"". get_bloginfo("wpurl") ."/profile-member/account/\">Manage Account</a>";
			echo "</p>";

		} else {
				
			if(!empty($rb_agency_new_registeredUser)){
				if(in_array(strtolower($ptype),$restrict)){
					echo "<div id=\"profile-steps\">".__("Profile Setup: Step 1 of 2",RBAGENCY_interact_TEXTDOMAIN)."</div>\n";
				} else {
					echo "<div id=\"profile-steps\">".__("Profile Setup: Step 1 of 3",RBAGENCY_interact_TEXTDOMAIN)."</div>\n";
				}
			}	

			echo "	<div id=\"profile-manage\" class=\"profile-overview\">\n";

			// Menu
			include("include-menu.php");

			echo " <div class=\"manage-overview manage-content\">\n";

			/* Check if the user is regsitered *****************************************/ 
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = $wpdb->get_row($sql,ARRAY_A);
			$count = $wpdb->num_rows;
			if ($count > 0) {

				$data = $results;// is there record?

				echo "	<div class=\"manage-section welcome\">\n";
				echo "		<h1>". __("Welcome Back", RBAGENCY_interact_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";
				// Record Exists

				/* Show account information here *****************************************/

				echo " <div class=\"section-content section-account\">\n"; // .account
				echo " 	<ul>\n";
				echo "      <li><a href=\"account/\">". __("Edit Your Account Details", RBAGENCY_interact_TEXTDOMAIN) ."</a></li>\n";
				echo "      <li><a href=\"manage/\">". __("Manage Your Profile Information", RBAGENCY_interact_TEXTDOMAIN) ."</a></li>\n";
				echo "      <li><a href=\"media/\">". __("Manage Photos and Media", RBAGENCY_interact_TEXTDOMAIN) ."</a></li>\n";

				if($rb_subscription){
					echo "      <li><a href=\"subscription/\">".__("Manage your Subscription",RBAGENCY_interact_TEXTDOMAIN)."</a></li>\n";
				}

				if(function_exists('rb_agency_casting_menu')){
					if(rb_get_user_profilstatus() == 1){ //means only visible if account is active
						echo "      <li><a href=\"".get_bloginfo('wpurl')."/browse-jobs/\">".__("Browse and Apply for a Job",RBAGENCY_interact_TEXTDOMAIN)."</a></li>\n";
					}
				}
				echo "      <li><a href=\"".get_bloginfo('wpurl')."/logout/\">".__("Logout",RBAGENCY_interact_TEXTDOMAIN)."</a></li>\n";

				echo "	</ul>\n";
				delete_script();
				Profile_Account();
				/*if(function_exists('rb_agency_casting_menu')){
					echo "</hr>\n";
					echo "<h3>Jobs and Auditions</h3>";

				}*/				
				echo " </div>\n"; // .section-account
				echo " </div>\n"; // .welcome

			// No Record Exists, register them
			} else {

				echo "<h1>". __("Welcome", RBAGENCY_interact_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";

				if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)){
					echo "<p>". __("We have you registered as", RBAGENCY_interact_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong></p>";
					echo "<h2><a href=\"". $rb_agency_interact_WPURL ."/profile-search/\">". __("Begin Your Search", RBAGENCY_interact_TEXTDOMAIN) ."</a></h2>";

					echo " <div id=\"subscription-customtext\">\n";
					$Page = get_page($rb_agencyinteract_option_subscribepagedetails);
					echo apply_filters('the_content', $Page->post_content);
					echo " </div>";

				} else {
					if ($rb_agencyinteract_option_registerallow == 1) {

						// Users CAN register themselves
						echo "". __("We have you registered as", RBAGENCY_interact_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong>";
						echo "<h2>". __("Setup Your Profile", RBAGENCY_interact_TEXTDOMAIN) ."</h2>";

						// Register Profile
						include("include-profileregister.php");
					} else {

						// Cant register
						echo "<strong>". __("Self registration is not permitted.", RBAGENCY_interact_TEXTDOMAIN) ."</strong>";
					}
				}
			}
			// if pending for approval
			echo "</div><!-- .manage-content -->\n";
			echo "</div><!-- .profile-overview -->\n";
		}

	} else {

		// Show Login Form
		include("include-login.php");
	}

	echo "  </div><!-- #rbcontent -->\n";
	echo "</div><!-- #primary -->\n";

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>
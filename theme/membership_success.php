<?php 

echo $rb_header = RBAgency_Common::rb_header();
session_start();
?>


<?php

if(!isset($_GET['status'])){
	$rbagency_use_s2member_option = get_option('rbagency_use_s2member');
	$rbagency_message_after_payment = get_option('rbagency_message_after_payment');

	if($rbagency_use_s2member_option == true){
		if(!empty($rbagency_message_after_payment)){
			echo nl2br($rbagency_message_after_payment);
		}else{
				echo "<h2>".__("Registration Complete!",RBAGENCY_interact_TEXTDOMAIN)."</h2>";
				echo "".__("Your account is currently pending for approval.",RBAGENCY_interact_TEXTDOMAIN)."";
				echo "".__("We will email you once your account is approved.",RBAGENCY_interact_TEXTDOMAIN)."";
		}
	}
	
	          			
	echo "<br>";
	echo "<a href='".get_bloginfo("url")."/registration-success/?status=pending' style='background-color: rgb(0, 0, 0); color: rgb(255, 255, 255); padding: 10px;'>View My Profile</a>";
	echo "<a href='".get_bloginfo("url")."/registration-success/?status=pending' style='margin-left:10px;background-color: rgb(0, 0, 0); color: rgb(255, 255, 255); padding: 10px;'>Manage Account</a>";
	echo "<a href='".get_bloginfo("url")."' style='margin-left:10px;background-color: rgb(0, 0, 0); color: rgb(255, 255, 255); padding: 10px;'>Logout</a>";
}else{
	echo '<h2>'.__('Your account is pending for approval. We will send you an email once your account is approved.',RBAGENCY_interact_TEXTDOMAIN).'</h2><br/>';
}


?>

<?php

echo $rb_footer = RBAgency_Common::rb_footer();

?>
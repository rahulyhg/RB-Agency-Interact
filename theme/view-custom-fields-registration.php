<?php

function rb_model_registration_form($visibility = 0,$result){

	global $wpdb;

	$ProfileCustomValue = "";
	$ProfileCustomTitle = $result['ProfileCustomTitle'];
	$ProfileCustomType = $result['ProfileCustomType'];
	// SET Label for Measurements
	// Imperial(in/lb), Metrics(ft/kg)
	$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_unittype  = $rb_agency_options_arr['rb_agency_option_unittype'];
	$measurements_label = "";
	if ($ProfileCustomType == 7) { //measurements field type
	    if($rb_agency_option_unittype ==0){ // 0 = Metrics(ft/kg)
			if($result['ProfileCustomOptions'] == 1){
				$measurements_label  ="<em>(".__('cm',RBAGENCY_interact_TEXTDOMAIN).")</em>";
			} elseif($result['ProfileCustomOptions'] == 2){
				$measurements_label  ="<em>(".__('kg',RBAGENCY_interact_TEXTDOMAIN).")</em>";
			} elseif($result['ProfileCustomOptions'] == 3){
				$measurements_label  ="<em>(".__('Inches/Feet',RBAGENCY_interact_TEXTDOMAIN).")</em>";
			}
		} elseif($rb_agency_option_unittype ==1){ //1 = Imperial(in/lb)
			if($result['ProfileCustomOptions'] == 1){
				$measurements_label  ="<em>(".__('In Inches',RBAGENCY_interact_TEXTDOMAIN).")</em>";
			} elseif($result['ProfileCustomOptions'] == 2){
					$measurements_label  ="<em>(".__('In Pounds',RBAGENCY_interact_TEXTDOMAIN).")</em>";
			} elseif($result['ProfileCustomOptions'] == 3){
					$measurements_label  ="<em>(".__('In Inches/Feet',RBAGENCY_interact_TEXTDOMAIN).")</em>";
			}
		}
	}

	if ($ProfileCustomType == 1) { // TEXT

		echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
		echo "		<div><input type=\"text\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."\" value=\"". $ProfileCustomValue ."\" /></div>\n";
		echo "	</div>\n";

	} elseif ($ProfileCustomType == 2) { // Min Max

		echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbtext rbmulti\">\n";
	 	echo "  	<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n"; 

		$ProfileCustomOptions_String = str_replace(",",":",strtok(strtok($result['ProfileCustomOptions'],"}"),"{"));
		list($ProfileCustomOptions_Min_label,$ProfileCustomOptions_Min_value,$ProfileCustomOptions_Max_label,$ProfileCustomOptions_Max_value) = explode(":",$ProfileCustomOptions_String);

		echo "		<div>";
		if(!empty($ProfileCustomOptions_Min_value) && !empty($ProfileCustomOptions_Max_value)){
				echo "<div><label for=\"ProfileCustomLabel_min\">". __("Min", RBAGENCY_TEXTDOMAIN) . ":</label>\n";
				echo "<div><input type=\"text\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."\" value=\"". $ProfileCustomOptions_Min_value ."\" /></div>\n";
				echo "<div><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Max", RBAGENCY_TEXTDOMAIN) . ":</label>\n";
				echo "<div><input type=\"text\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."\" value=\"". $ProfileCustomOptions_Max_value ."\" /></div>\n";

		} else {
				echo "<div><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Min", RBAGENCY_TEXTDOMAIN) . ":</label>\n";
				echo "<div><input type=\"text\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."\" value=\"".$_SESSION["ProfileCustomID". $result['ProfileCustomID']]."\" /></div></div>\n";
				echo "<div><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Max", RBAGENCY_TEXTDOMAIN) . ":</label>\n";
				echo "<div><input type=\"text\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."\" value=\"".$_SESSION["ProfileCustomID". $result['ProfileCustomID']]."\" /></div></div>\n";
		}
		echo "		</div>\n";
		echo "	</div>\n";

	} elseif ($ProfileCustomType == 3 || $ProfileCustomType == 9) { // SELECT
		echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbselect rbsingle\">\n";
		echo "		<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
		echo "	<div>\n";

		list($option1,$option2) = explode(":",$result['ProfileCustomOptions']);
		$data = explode("|",$option1);
		$data2 = explode("|",$option2);
		if(!empty($data[0])){
			echo "<label>".$data[0].":</label>";
		}
	    
		echo "<select name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\">\n";
		echo "<option value=\"\">--</option>";

	    $pos = 0;
		foreach($data as $val1){
			if($val1 != end($data) && $val1 != $data[0]){
				if($val1 == $ProfileCustomValue ){
					$isSelected = "selected=\"selected\"";
					echo "<option value=\"".$val1."\" ".$isSelected .">".$val1."</option>";
				} else {
					echo "<option value=\"".$val1."\" >".$val1."</option>";
				}
			}
		}

	  	echo "</select>\n";

		if(!empty($data2) && !empty($option2)){
			echo "<label>".$data2[0].":</label>";
				$pos2 = 0;
				echo "<select name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\">\n";
				echo "<option value=\"\">--</option>";
					foreach($data2 as $val2){
						if($val2 != end($data2) && $val2 !=  $data2[0]){
							if($val2 == $ProfileCustomValue ){
								$isSelected = "selected=\"selected\"";
								echo "<option value=\"".$val2."\" ".$isSelected .">".$val2."</option>";
							} else {
								echo "<option value=\"".$val2."\" >".$val2."</option>";
							}
						}
					}
				echo "</select>\n";

		}
		echo "		</div>\n";
		echo "	</div>";

	} elseif ($ProfileCustomType == 4) {

		if(is_admin()){
			echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbtextarea rbsingle\">\n";
			echo "		<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
			echo "		<div>";
			echo "			<textarea name=\"ProfileCustomID". $result['ProfileCustomID'] ."\">". $ProfileCustomValue ."</textarea>";
			echo "		</div>";
			echo "	</div>";
		}
	} elseif ($ProfileCustomType == 5) {

		echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbtextarea rbsingle\">\n";
		echo "		<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
		echo "		<div>";

		$array_customOptions_values = explode("|",$result['ProfileCustomOptions']);

		foreach($array_customOptions_values as $val){
			if(in_array($val,explode(",",$ProfileCustomValue))){
				echo "<div style=\"display:inline-block;\"><label><input type=\"checkbox\" checked=\"checked\" value=\"". $val."\"  name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\" />";
				echo "<span>". $val."</span></label></div>";
			} else {
				echo "<div style=\"display:inline-block;\"><label><input type=\"checkbox\" value=\"". $val."\"  name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\" />";
				echo "<span>". $val."</span></label></div>";
			}
		}
		echo "			<input type=\"hidden\" value=\"\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\"/>";
		echo "		</div>";
		echo "	</div>";
	       
	} elseif ($ProfileCustomType == 6) {

		echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbtextarea rbsingle\">\n";
		echo "		<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
		echo "		<div>\n";

		$array_customOptions_values = explode("|",$result['ProfileCustomOptions']);

		foreach($array_customOptions_values as $val){
			if(in_array($val,explode(",",$ProfileCustomValue))){
				echo "<div style=\"display:inline-block;\"><label><input type=\"radio\" checked=\"checked\" value=\"". $val."\"  name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\" />";
				echo "<span>". $val."</span></label></div>";
			} else {
				echo "<div style=\"display:inline-block;\"><label><input type=\"radio\" value=\"". $val."\"  name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\" />";
				echo "<span>". $val."</span></label></div>";
			}
		}
		echo "			<input type=\"hidden\" value=\"\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."[]\"/>";
		echo "		</div>"; 
		echo "	</div>";

	} elseif ($ProfileCustomType == 7) { // Imperial(in/lb), Metrics(ft/kg)
		/*   if($result['ProfileCustomOptions']==1){
						if($rb_agency_option_unittype == 1){
							echo "<select name=\"ProfileCustomID". $result['ProfileCustomID'] ."\">\n";
								if (empty($ProfileCustomValue)) {
							echo " 				<option value=\"\">--</option>\n";
								}

								$i=36;
								$heightraw = 0;
								$heightfeet = 0;
								$heightinch = 0;
								while($i<=90)  {
									$heightraw = $i;
									$heightfeet = floor($heightraw/12);
									$heightinch = $heightraw - floor($heightfeet*12);
							echo " <option value=\"". $i ."\" ". selected($ProfileCustomValue, $i) .">". $heightfeet ." ft ". $heightinch ." in</option>\n";
									$i++;
								}
							echo " </select>\n";
						} else {
							echo "	<input type=\"text\" id=\"ProfileStatHeight\" name=\"ProfileStatHeight\" value=\"". $ProfileCustomValue ."\" />\n";
						}
			} else {
		 */
		echo "	<div id=\"rbfield-". $result['ProfileCustomID'] ."\" class=\"rbfield rbtextarea rbsingle\">\n";
		echo "		<label for=\"ProfileCustomID". $result['ProfileCustomID'] ."\">".__($result['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
		echo "		<div><input type=\"text\" name=\"ProfileCustomID". $result['ProfileCustomID'] ."\" value=\"". $ProfileCustomValue ."\" /></div>\n";
		echo "	</div>";
	}
}



?>
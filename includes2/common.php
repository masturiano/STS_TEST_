<?php

$arrStat = array("A"=>"Active","I"=>"Inactive");

function dropDownList($arrCont,$name,$selected,$attr,$addAllOption){
		
	if(!empty($arrCont)){
		echo "<SELECT name=\"$name\" id=\"$name\" $attr>\n";
			if($addAllOption == true){
				echo "\t<OPTION value=\"All\">--ALL--</OPTION>\n";
			}
			foreach ($arrCont as $arrValIndx => $arrContVal){
				echo "\t<OPTION value=\"$arrValIndx\" ";
					echo (trim($selected)==trim($arrValIndx)) ? "selected" : '';
				echo ">\n";
						echo "\t\t".$arrContVal."\n";
				echo "\t</OPTION>\n";
			}
		echo "</SELECT>\n";
	}
	else{
		echo "<SELECT name=\"$name\" id=\"$name\" $attr>\n";
			echo "<OPTION value=''>";
			echo "<OPTION>";
		echo "</SELECT>\n";
	}
}


function isActiveSession($isLogOut,$file){
	
	if(trim($file) == 'posIndex.php'){
		if($isLogOut == 1){
			echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/APC2011/modules/POS/posIndex.php'</script>";
			setcookie("USER_ID", "", time()-3600);
			setcookie("USER_FULLNAME", "", time()-3600);
			setcookie("REGISTER_NUMBER", "", time()-3600);
		}
		if(!empty($_COOKIE['USER_ID'])){
			echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/APC2011/modules/POS/main.php'</script>";
		}
			
	}
	else{
		if(empty($_COOKIE['USER_ID'])){
			echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/APC2011/modules/POS/posIndex.php'</script>";
		}
	}
}


function isActiveSessionProdMaint($isLogOut,$file){
	
	if(trim($file) == 'productMaintenanceIndex.php'){
		if($isLogOut == 1){
			echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/APC2011/modules/POS/productMaintenanceIndex.php'</script>";
			setcookie("USER_ID", "", time()-3600);
			setcookie("USER_FULLNAME", "", time()-3600);
			setcookie("REGISTER_NUMBER", "", time()-3600);
		}
		if(!empty($_COOKIE['USER_ID']) && $_COOKIE['USER_LEVEL'] == 'admin'){
			echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/APC2011/modules/POS/productMaintenanceMain.php'</script>";
		}
			
	}
	else{
		if(empty($_COOKIE['USER_ID'])){
			echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/APC2011/modules/POS/productMaintenanceIndex.php'</script>";
		}
	}
}
?>
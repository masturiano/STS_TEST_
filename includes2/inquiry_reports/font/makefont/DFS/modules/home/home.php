<?
#Description: Homepage(default page)
#Author: Jhae Torres
#Date Created: March 10, 2008


session_start();
$privilege_id = $_SESSION['privilege_id'];

require_once "includes/config.php";
require_once "functions/db_function.php";
require_once "home.obj.php";
require_once "modules/etc/etc.obj.php";

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);

$db = new DB;
$db->connect();
$homeTrans = new homeObject;
$etcTrans = new etcObject;

$company_name = $etcTrans->getCompanyName($_SESSION['comp_code']);

### GET PARENT MENU (Main Menu Content)
$parent_menu = $homeTrans->getParentMenu();
($privilege_id==1 or $privilege_id==8) ? $left_distance -= 9.8 : $left_distance -= 10.9;
foreach($parent_menu as $parent){
	if($parent['parentModule']=='logout'){
		$menu .= "<td id='".$parent['parentModule']."' title='".$parent['title']."'
					onClick='assignTransaction(\"logout\"); confirmLogout();'
					onMouseOver='this.style.backgroundColor=\"#A9C4AB\";'
					onMouseOut='this.style.backgroundColor=\"transparent\"'>".$parent['menuName']."</td>";
	} elseif(($parent['parentModule']=='admin' and ($privilege_id==1 or $privilege_id==8)) or ($parent['parentModule']!='admin' and ($privilege_id!=1 or $privilege_id!=8))){
	//} else{
		$menu .= "<td id='".$parent['parentModule']."' title='".$parent['title']."' onMouseOver='active(this.id); highlightOn(this, this.id);' onMouseOut='highlightOff(this, this.id);'>".$parent['menuName']."</td>";

		if($privilege_id==1 or $privilege_id==8){
			($left_distance>70) ? $left_distance += -1 : $left_distance += 10;
		} else{
			($left_distance>70) ? $left_distance +=- 1 : $left_distance += 11.1;
		}

		### CHECK IF SUB-MENU EXISTS
		$group = $homeTrans->getMenuGroups($parent['parentOrder'], $privilege_id);
		if(empty($group)){
			$menu .= "<div id='".$parent['parentModule']."_' class='sub_menu' style='left:".$left_distance."%;'
							onMouseOver='recent(\"".$parent['parentModule']."\"); active(\"\"); showSubMenu(\"".$parent['parentModule']."\", this.id);'
							onMouseOut='hideSubMenu(\"".$parent['parentModule']."\", this.id);'><hr><hr><hr>";
		} else{
			$menu .= "<div id='".$parent['parentModule']."_' class='sub_menu' style='left:".$left_distance."%;'
							onMouseOver='recent(\"".$parent['parentModule']."\"); active(\"\"); showSubMenu(\"".$parent['parentModule']."\", this.id);'
							onMouseOut='hideSubMenu(\"".$parent['parentModule']."\", this.id);'><hr>";
	
			### GROUP SUB-MENU (Sub-Menu Content)
			foreach($group as $grp){
				$sub_menu = $homeTrans->getSubMenu($parent['parentOrder'], $grp['groupOrder'], $privilege_id);
							
				foreach($sub_menu as $sub){
					(!IS_NULL($sub['menuModule'])) ? $module_name=$sub['menuModule'] : $module_name=$sub['parentModule'];
					
					if($sub['menuOrder']==0){
						$menu .= "<h4>".$sub['title']."</h4>";
					} else{
						$menu .= "<label onClick='assignFrameContent(\"http://".$config['sys_host']."/".$config['sys_dir']."/modules/".$module_name."/".$sub['fileName'].".php\");
											modifyHeaderContent(\"".$sub['menuCode']."\", \"".$sub['menuDesc']."\");
											hideSubMenu(\"".$parent['parentModule']."\", \"".$parent['parentModule']."_\");'>".$sub['title']."</label><br>";
					}
				}
				$menu.="<hr>";
			}
		}
		$menu .= "</div>";
	}
}

#$db->disconnect();

$html .= "<body onLoad='getDateTime();'>";
$html .= "<form name='home_form' action='modules/etc/etc.trans.php' method='POST'>";
$html .= "<input type='hidden' name='transaction' id='transaction' />";

/* banner */
$html .= "<div id='home_banner'>";
$html .= "<img class='banner' src='attachments/puregoldDF.png'></img>";
$html .= "<div class='store'>".$company_name."</div>";
$html .= "</div>";

/* header */
$html .= "<div id='home_header'>";
$html .= "<table>";
$html .= "<tr>";
$html .= "<th width='7%'>PG ID:</th>";
$html .= "<td width='13%'><input type='text' name='pg_id' id='pg_id' readOnly /></td>";
$html .= "<th width='60%' class='company_name' colspan='2'>Puregold Duty Free</th>";
$html .= "<th width='5%'>Date:</th>";
$html .= "<td width='15%'><input type='text' name='date' id='date' value='".$date."' readOnly /></td>";
$html .= "</tr>";
$html .= "<tr>";
$html .= "<th>User:</th>";
$html .= "<td>
			<input type='hidden' name='user_id' id='user_id' value='".$_SESSION['userid']."' readOnly />
			<input type='text' style='width:200px;' name='full_name' id='full_name' value='".$_SESSION['first_name']." ".$_SESSION['middle_name']." ".$_SESSION['last_name']."' readOnly />
			<input type='hidden' name='session_username' id='session_username' value='".$_SESSION['username']."' readOnly />
		</td>";
$html .= "<td colspan='2'><input type='text' class='program_desc' name='category' id='category' readOnly /></td>";
$html .= "<th>Time:</th>";
$html .= "<td><input type='text' name='time' id='time' readOnly /></td>";
$html .= "</tr>";
$html .= "</table>";
$html .= "</div>";

/* menu */
$html .= "<input type='hidden' name='recent_obj' id='recent_obj' />";
$html .= "<input type='hidden' name='active_obj' id='active_obj' />";
$html .= "<div id='menu'>";
$html .= "<table class='parent_tbl'>";
$html .= "<tr>";
$html .= $menu;
$html .= "</tr>";
$html .= "</table>";
$html .= "</div>";

/* right pane menu content */
$html .= "<div id='home_body'>";
$html .= "<iframe src='' class='frame_content' name='frame_content' id='frame_content' frameborder='0'></iframe>";
$html .= "</div>";

$html .= "</form>";
$html .= "</body>";
?>
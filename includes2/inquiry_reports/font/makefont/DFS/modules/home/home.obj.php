<?
#Description: Homepage Module Object
#Author: Jhae Torres
#Date Created: March 10, 2008


class homeObject
{
	function getParentMenu(){
		global $db;
		$query = "SELECT parentOrder, groupOrder, menuOrder,
					parentModule, menuModule,
					menuName, menuStat,
					menuCode, title, menuDesc, fileName
				FROM tblMainMenu
				WHERE menuStat = 'A'
					AND groupOrder = 0
					AND menuOrder = 0
				ORDER BY parentOrder, groupOrder, menuOrder";
		$db->query($query);
		$parent_menu = $db->getArrResult();
		return $parent_menu;
	}
	
	function getMenuGroups($parent_order, $privilege_id){
		global $db;
		if($privilege_id==1){
			$and.=" AND menuPriv != 8";
		} elseif($privilege_id!=8){
			$and.=" AND menuPriv IN('0', '".$privilege_id."')";
		}
		
		$query = "SELECT DISTINCT groupOrder
				FROM tblMainMenu
				WHERE menuStat = 'A'
					AND parentOrder = ".$parent_order."
					AND groupOrder != 0
					$and";
		$db->query($query);
		$group = $db->getArrResult();
		return $group;
	}
	
	function getSubMenu($parent_order, $group_order, $privilege_id){
		global $db;
		if($privilege_id==1){
			$and.=" AND menuPriv != 8";
		} elseif($privilege_id!=8){
			$and.=" AND menuPriv IN('0', '".$privilege_id."')";
		}
		
		$query = "SELECT parentOrder, groupOrder, menuOrder,
					parentModule, menuModule,
					menuName, menuStat,
					menuCode, title, menuDesc, fileName
				FROM tblMainMenu
				WHERE menuStat = 'A'
					AND parentOrder = ".$parent_order."
					AND groupOrder = ".$group_order."
					AND groupOrder != 0
					$and
				ORDER BY parentOrder, groupOrder, menuOrder";
		$db->query($query);
		$sub_menu = $db->getArrResult();
		return $sub_menu;
	}
}
?>
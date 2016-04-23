<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");

class maintenanceObj extends commonObj {
	
	function checkNewFrOld($oldPass){
		$enOldPass = base64_encode($oldPass);
		$sql = "SELECT userName FROM tblusers WHERE userPass = '$enOldPass' AND userId = '{$_SESSION['sts-userId']}'";
		return $this->getRecCount($this->execQry($sql));
	}
	function changePass($newPass){
		$enNewPass = base64_encode($newPass);
		$sqlAdd = "UPDATE tblusers SET userPass = '$enNewPass' WHERE userId = '{$_SESSION['sts-userId']}'";	
		$trans = $this->beginTran();
		if ($trans) {
			$trans = $this->execQry($sqlAdd);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		} else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function countEnhancer(){
		$sql = "Select count(*) as count From tblEnhancerType WHERE stat = 'A'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function searchEnhancerType($sidx,$sord,$start,$limit,$searchField,$searchString){
		$sql = "SELECT TOP $limit * FROM tblEnhancerType WHERE $searchField =
		'$searchString' AND stat = 'A' AND enhanceType NOT IN (SELECT TOP $start enhanceType FROM tblEnhancerType WHERE $searchField =
		'$searchString' AND stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function getPaginatedEnhancerType($sidx,$sord,$start,$limit){
		$sql = "SELECT TOP $limit * FROM tblEnhancerType WHERE stat = 'A' AND enhanceType NOT IN (SELECT TOP $start enhanceType FROM tblEnhancerType WHERE stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function checkIfEnhancerExists($desc){
		$sql = "SELECT * FROM tblEnhancerType WHERE enhanceDesc like '%$desc%'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfEnhancerExistsWId($desc,$id){
		$sql = "SELECT * FROM tblEnhancerType WHERE enhanceDesc like '%$desc%' AND enhanceType != '$id'";
		return $this->getRecCount($this->execQry($sql));
	}
	function addEnhancer($arr){
		$sqlAdd	="INSERT INTO tblEnhancerType (enhanceDesc, stat)
		VALUES ('{$arr['txtEnhancerDesc']}', 'A');";
		return $this->execQry($sqlAdd);
	}
	function enhancerInfo($id){
		$sql = "SELECT * FROM tblEnhancerType WHERE enhanceType = '$id'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function updateEnhancerInfo($arr){
		$sql = "UPDATE tblEnhancerType SET enhanceDesc  = '{$arr['txtEnhancerDesc']}' WHERE enhanceType = '{$arr['hdnEnhancerId']}'";	
		return $this->execQry($sql);
	}
	function deleteEnhancer($id){
		$sqlUpdateDel = "UPDATE tblEnhancerType SET stat = 'D' WHERE enhanceType = '$id'";
		return $this->execQry($sqlUpdateDel);
	}
	
	function countBrand(){
		$sql = "Select count(*) as count From tblBrand WHERE stat = 'A'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function searchBrandType($sidx,$sord,$start,$limit,$searchField,$searchString){
		$sql = "SELECT TOP $limit * FROM tblBrand WHERE $searchField =
		'$searchString' AND stat = 'A' AND stsBrand NOT IN (SELECT TOP $start stsBrand FROM tblBrand WHERE $searchField =
		'$searchString' AND stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function getPaginatedBrandType($sidx,$sord,$start,$limit){
		$sql = "SELECT TOP $limit * FROM tblBrand WHERE stat = 'A' AND stsBrand NOT IN (SELECT TOP $start stsBrand FROM tblBrand WHERE stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function checkIfBrandExists($desc){
		$sql = "SELECT * FROM tblBrand WHERE stsBrandDesc like '%$desc%'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfBrandExistsWId($desc,$id){
		$sql = "SELECT * FROM tblBrand WHERE stsBrandDesc like '%$desc%' AND stsBrand != '$id'";
		return $this->getRecCount($this->execQry($sql));
	}
	function addBrand($arr){
		$sqlAdd	="INSERT INTO tblBrand (stsBrandDesc, stat)
		VALUES ('{$arr['txtBrand']}', 'A');";
		return $this->execQry($sqlAdd);
	}
	function brandInfo($id){
		$sql = "SELECT * FROM tblBrand WHERE stsBrand = '$id'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function updateBrandInfo($arr){
		$sql = "UPDATE tblBrand SET stsBrandDesc  = '{$arr['txtBrand']}' WHERE stsBrand = '{$arr['hdnBrandId']}'";	
		return $this->execQry($sql);
	}
	function deleteBrand($id){
		$sqlUpdateDel = "UPDATE tblBrand SET stat = 'D' WHERE stsBrand = '$id'";
		return $this->execQry($sqlUpdateDel);
	}
}
?>
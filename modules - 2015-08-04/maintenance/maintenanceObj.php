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
	function countLocation(){
		$sql = "Select count(*) as count From tblLocation WHERE locStatus = 'A'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function countDisplay(){
		$sql = "Select count(*) as count From tblDisplaySpecs WHERE stat = 'A'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function countDisplayDtl($dispId){
		$sql = "Select count(*) as count From tblDisplaySpecsDtl WHERE status = 'A' AND displaySpecsId = '$dispId'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function searchBrandType($sidx,$sord,$start,$limit,$searchField,$searchString){
		$sql = "SELECT TOP $limit * FROM tblBrand WHERE $searchField =
		'$searchString' AND stat = 'A' AND stsBrand NOT IN (SELECT TOP $start stsBrand FROM tblBrand WHERE $searchField =
		'$searchString' AND stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function searchLocationType($sidx,$sord,$start,$limit,$searchField,$searchString){
		$sql = "SELECT TOP $limit * FROM tblLocation WHERE $searchField =
		'$searchString' AND locStatus = 'A' AND locId NOT IN (SELECT TOP $start locId FROM tblLocation WHERE $searchField =
		'$searchString' AND locStatus = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function searchDisplayTypeDtl($sidx,$sord,$start,$limit,$searchField,$searchString,$dispId){
		$sql = "SELECT TOP $limit tblDisplaySpecsDtl.* FROM tblDisplaySpecsDtl  WHERE $searchField =
		'$searchString' AND status = 'A' AND displaySpecsId = '$dispId' AND dispDtlId NOT IN (SELECT TOP $start dispDtlId FROM tblDisplaySpecsDtl WHERE $searchField =
		'$searchString' AND status = 'A' AND displaySpecsId = '$dispId' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function searchDisplayType($sidx,$sord,$start,$limit,$searchField,$searchString){
		$sql = "SELECT TOP $limit * FROM tblDisplaySpecs WHERE $searchField =
		'$searchString' AND stat = 'A' AND displaySpecsId NOT IN (SELECT TOP $start displaySpecsId FROM tblDisplaySpecs WHERE $searchField =
		'$searchString' AND stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function getPaginatedBrandType($sidx,$sord,$start,$limit){
		$sql = "SELECT TOP $limit * FROM tblBrand WHERE stat = 'A' AND stsBrand NOT IN (SELECT TOP $start stsBrand FROM tblBrand WHERE stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function getPaginatedLocationType($sidx,$sord,$start,$limit){
		$sql = "SELECT TOP $limit * FROM tblLocation WHERE locStatus = 'A' AND locId NOT IN (SELECT TOP $start locId FROM tblLocation WHERE locStatus = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function getPaginatedDisplayType($sidx,$sord,$start,$limit){
		$sql = "SELECT TOP $limit * FROM tblDisplaySpecs WHERE stat = 'A' AND displaySpecsId NOT IN (SELECT TOP $start displaySpecsId FROM tblDisplaySpecs WHERE stat = 'A' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function getPaginatedDisplayTypeDtl($sidx,$sord,$start,$limit,$dispId){
		 $sql = "SELECT TOP $limit tblDisplaySpecsDtl.*FROM tblDisplaySpecsDtl  WHERE status = 'A' AND displaySpecsId = '$dispId' AND dispDtlId NOT IN (SELECT TOP $start dispDtlId FROM tblDisplaySpecsDtl WHERE status = 'A' AND displaySpecsId = '$dispId' ORDER BY  $sidx $sord) ORDER BY  $sidx $sord";
		return $this->getArrRes($this->execQry($sql));
	}
	function checkIfBrandExists($desc){
		$sql = "SELECT * FROM tblBrand WHERE stsBrandDesc like '%$desc%'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfLocationExists($desc){
		$sql = "SELECT * FROM tblLocation WHERE locDescription like '$desc' and locStatus = 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfSpecExists($desc){
		$sql = "SELECT * FROM tblDisplaySpecs WHERE displaySpecsDesc like '$desc' AND stat = 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfSpecDtlExists($desc){
		$sql = "SELECT * FROM tblDisplaySpecsDtl WHERE dispDesc like '$desc' AND status = 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfBrandExistsWId($desc,$id){
		$sql = "SELECT * FROM tblBrand WHERE stsBrandDesc like '%$desc%' AND stsBrand != '$id'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfLocationExistsWId($desc,$id){
		$sql = "SELECT * FROM tblLocation WHERE locDescription like '$desc' AND locId != '$id'  and locStatus = 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfSpecsExistsWId($desc,$id){
		$sql = "SELECT * FROM tblDisplaySpecs WHERE displaySpecsDesc like '$desc' AND displaySpecsId != '$id' AND stat= 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function checkIfSpecsDtlExistsWId($desc,$masterId,$dtlId){
		$sql = "SELECT * FROM tblDisplaySpecsDtl WHERE dispDesc like '$desc' AND displaySpecsId != '$masterId' AND dispDtlId = '$dtlId' AND status = 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function addBrand($arr){
		$sqlAdd	="INSERT INTO tblBrand (stsBrandDesc, stat)
		VALUES ('{$arr['txtBrand']}', 'A');";
		return $this->execQry($sqlAdd);
	}
	function addLocation($arr){
		$sqlAdd	="INSERT INTO tblLocation (locDescription, locStatus, dateAdded, addedBy)
		VALUES ('{$arr['txtLocation']}', 'A', '".date('m/d/Y')."', '".$_SESSION['sts-userId']."');";
		return $this->execQry($sqlAdd);
	}
	function addSpecs($arr){
		$sqlAdd	="INSERT INTO tblDisplaySpecs (displaySpecsDesc, stat, createdBy, dateCreated, specsAmount)
		VALUES ('{$arr['txtBrand']}', 'A','".$_SESSION['sts-userId']."', '".date('m/d/Y')."', '{$arr['txtAmount']}');";
		return $this->execQry($sqlAdd);
	}
	function addSpecsDtl($arr){
		
		$trans = $this->beginTran();
		
		$sqlAdd	="INSERT INTO tblDisplaySpecsDtl (dispDesc, status, createdBy, dateCreated, displaySpecsId, sizeSpecs) VALUES ('{$arr['txtBrandDtl']}', 'A','".$_SESSION['sts-userId']."', '".date('m/d/Y')."','{$arr['hdnMasterId']}','{$arr['cmbSize']}');";
		
		if ($trans) {
			$trans = $this->execQry($sqlAdd);
		}
		if($trans){
			$sql1 = "SELECT SCOPE_IDENTITY() as id";
			$arrId = $this->getSqlAssoc($this->execQry($sql1));
			
			if($arrId['id']!=''){
				$sql = "INSERT INTO tblDispDaDtlStr (strCode, displaySpecsId, dispDtlId, usableTag)  
				SELECT     tblBranches.strCode, tblDisplaySpecsDtl.displaySpecsId, tblDisplaySpecsDtl.dispDtlId, 'Y'
				FROM         tblDisplaySpecsDtl CROSS JOIN
									  tblBranches
				WHERE     (tblDisplaySpecsDtl.dispDtlId = '{$arrId['id']}') AND (tblBranches.brnStat = 'A') ";
				if ($trans) {
					$trans = $this->execQry($sql);
				}
			}
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		} else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function brandInfo($id){
		$sql = "SELECT * FROM tblBrand WHERE stsBrand = '$id'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function locationInfo($id){
		$sql = "SELECT * FROM tblLocation WHERE locId = '$id'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function specsInfo($id){
		$sql = "SELECT * FROM tblDisplaySpecs WHERE displaySpecsId = '$id'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function specsDtlInfo($id,$dtlId){
		$sql = "SELECT * FROM tblDisplaySpecsDtl WHERE displaySpecsId = '$id' AND dispDtlId = '$dtlId'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function updateBrandInfo($arr){
		$sql = "UPDATE tblBrand SET stsBrandDesc  = '{$arr['txtBrand']}' WHERE stsBrand = '{$arr['hdnBrandId']}'";	
		return $this->execQry($sql);
	}
	function updateLocationInfo($arr){
		$sql = "UPDATE tblLocation SET locDescription  = '{$arr['txtLocation']}', dateAdded = '".date('m/d/Y')."' WHERE locId = '{$arr['hdnLocationId']}'";	
		return $this->execQry($sql);
	}
	function updateSpecsInfo($arr){
		$sql = "UPDATE tblDisplaySpecs SET displaySpecsDesc  = '{$arr['txtBrand']}', specsAmount ='{$arr['txtAmount']}'  WHERE displaySpecsId = '{$arr['hdnBrandId']}'";	
		return $this->execQry($sql);
	}
	function updateSpecsDtlInfo($arr){
		$sql = "UPDATE tblDisplaySpecsDtl SET dispDesc = '{$arr['txtBrandDtl']}', sizeSpecs ='{$arr['cmbSize']}'  WHERE displaySpecsId = '{$arr['hdnMasterId']}' AND dispDtlId = '{$arr['hdnDtlId']}'";
		return $this->execQry($sql);	
	}
	function deleteBrand($id){
		$sqlUpdateDel = "UPDATE tblBrand SET stat = 'D' WHERE stsBrand = '$id'";
		return $this->execQry($sqlUpdateDel);
	}
	function deleteLocation($id){
		$sqlUpdateDel = "UPDATE tblLocation SET locStatus = 'D' WHERE locId = '$id'";
		return $this->execQry($sqlUpdateDel);
	}
	function deleteSpecsDtl($id,$dtlId){
		$trans = $this->beginTran();
		$sqlUpdateDel = "UPDATE tblDisplaySpecsDtl SET status = 'D'  WHERE displaySpecsId = '$id' AND dispDtlId = '$dtlId'";	
		if ($trans) {
			$trans = $this->execQry($sqlUpdateDel);
		}
		$sqlUpdateDel2 = "UPDATE tblDispDaDtlStr SET usableTag = 'N'  WHERE displaySpecsId = '$id' AND dispDtlId = '$dtlId'";	
		if ($trans) {
			$trans = $this->execQry($sqlUpdateDel2);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function deleteSpecs($id){
		$trans = $this->beginTran();
		$sqlUpdateDel = "UPDATE tblDisplaySpecs SET stat = 'D' WHERE displaySpecsId = '$id'";
		if ($trans) {
			$trans = $this->execQry($sqlUpdateDel);
		}
		$sqlUpdateDel2 = "UPDATE tblDispDaDtlStr SET usableTag = 'N'  WHERE displaySpecsId = '$id'";	
		if ($trans) {
			$trans = $this->execQry($sqlUpdateDel2);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
    
    /*
	function getBranches(){
		$sql = "SELECT strCode, brnDesc, cast(strCode as nvarchar)+' - '+brnDesc as strCodeName FROM pg_pf..tblbranches order by strCode";	
		return $this->getArrRes($this->execQry($sql));
	}
    */
    
    function getBranches($userName){
        $sql = "
            SELECT strCode, brnDesc, cast(strCode as nvarchar)+' - '+brnDesc as strCodeName 
            FROM pg_pf..tblbranches 
            where strCode = (select strCode from tblUsers where userName = '$userName')
            order by strCode
        ";    
        return $this->getSqlAssoc($this->execQry($sql));
    }
    
	function listRentables(){
		$sql = "SELECT * FROM tblDisplaySpecs WHERE stat = 'A'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getRentableStores($id,$strCode,$grpCode){
		
		if((int)$grpCode == 0){
			$grpFilter  = "";
		}else{
			$grpFilter = "AND tblDispDaDtlStr.grpCode = '$grpCode'  ";	
		}
		if((int)$id==0){
			$idFilter = "";
		}else{
			$idFilter = "AND tblDispDaDtlStr.displaySpecsId = '$id'";
		}
		$sql = "SELECT     tblDispDaDtlStr.displaySpecsId, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStr.permanentTag, tblDispDaDtlStr.usableTag, tblDispDaDtlStr.availabilityTag, 
                      tblDisplaySpecsDtl.dispDesc, tblDispDaDtlStr.strCode, tblDispDaDtlStr.grpCode, tblDispDaDtlStr.locId, tblDisplaySpecs.displaySpecsDesc
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId AND tblDispDaDtlStr.displaySpecsId = tblDisplaySpecsDtl.displaySpecsId INNER JOIN
                      tblDisplaySpecs ON tblDispDaDtlStr.displaySpecsId = tblDisplaySpecs.displaySpecsId WHERE tblDispDaDtlStr.strCode = '$strCode' AND usableTag = 'Y' and stsRefNo is null $idFilter $grpFilter
					  order by tblDisplaySpecs.displaySpecsDesc, tblDisplaySpecsDtl.dispDesc";
		return $this->getArrRes($this->execQry($sql));
	}
	function deleteRentablesStr($strCode){
		$trans = $this->beginTran();
		$sql = "UPDATe tblDispDaDtlStr set permanentTag = NULL, availabilityTag = NULL WHERE strCode = '".$strCode."' ";	
		if ($trans) {
			$trans = $this->execQry($sql);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function updateRentables($arr){
		
		if($this->deleteRentablesStr($arr['hdnStore'],$arr['hdnSpecs'])){
			$trans = $this->beginTran();
			//$arrHdr = $this->getDispStartEnd($arr['refNo']);
			$ctr = 0;
			for($i=0;$i<=(int)$arr['hdRentCtr'];$i++){
				if((int)$arr["switcherRent_$i"]==1	){
					if((int)$arr["switcherPerm_$i"]==1	){
						$perma = "'Y'";
					}else{
						$perma = "NULL";
					}
					
					$sql = "UPDATE tblDispDaDtlStr set permanentTag = 'Y', availabilityTag = 'Y' , taggedDate = '".date('m/d/Y')."', taggedBy = '".$_SESSION['sts-userId']."', grpCode = '".$arr["cmbGrp_$i"]."', locId = '".$arr["cmbLoc_$i"]."'  WHERE strCode = '".$arr['hdnStore']."' AND displaySpecsId = '".$arr["hdnSpecsId_$i"]."' AND dispDtlId = '".$arr["ckStr_$i"]."' ";	
					$ctr++;
					if ($trans) {
						$trans = $this->execQry($sql);
					}
				}
			}
			if(!$trans){
				$trans = $this->rollbackTran();
				return 0;
			}else{
				$trans = $this->commitTran();
				return $ctr;
			}
		}else{
			return false;	
		}
	}
	function getGrpList(){
		$sql = "SELECT     minCode, CAST(minCode AS nvarchar) + ' - ' + deptDesc AS description
FROM         tblDepartment
WHERE     (deptStat = 'A') AND mdsgGrpTag = 'Y'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getLocationList(){
		$sql = "SELECT     locId, CAST(locId AS nvarchar) + ' - ' + locDescription AS description
FROM         tblLocation
WHERE     (locStatus = 'A')";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getSizeSpecs(){
		$sql = "SELECT * FROM tblSizeSpecs";	
		return $this->getArrRes($this->execQry($sql));	
	}
	function getHeaderInformation($refNo){
		$sql = "SELECT     tblStsHdr.stsRefno, tblStsHdr.stsAmt, tblStsHdr.stsRemarks, tblUsers.fullName, tblStsHdr.dateEntered, tblStsHdr.dateApproved, tblStsHdr.applyDate, 
                      tblStsHdr.nbrApplication, sql_mmpgtlib.dbo.APSUPP.ASNAME, tblStsHdr.suppCode, 
                      payMode = CASE tblStsHdr.stsPaymentMode WHEN 'C' THEN 'CHECK Payment ' ELSE ' Invoice Deduction ' END
FROM         tblStsHdr INNER JOIN
                      tblUsers ON tblStsHdr.enteredBy = tblUsers.userId INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblStsHdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM WHERE stsRefno = $refNo AND tblStsHdr.stsType = 5";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	
	function getRentableBranches($refNo){
		$sql = "SELECT     tblStsDaDetail.stsRefno, tblStsDaDetail.strCode, tblBranches.brnDesc, tblStsDaDetail.displayType, tblStsDaDetail.dispSpecs, tblStsDaDetail.noUnits
FROM         tblStsDaDetail INNER JOIN
                      tblBranches ON tblStsDaDetail.strCode = tblBranches.strCode
WHERE     (tblStsDaDetail.stsRefno = '$refNo')";	
		return $this->getArrRes($this->execQry($sql));	
	}
	
	function getRentableDtlsTactical($refNo,$dispSpecs,$strCode,$dateFrom,$dateTo){
		/*$sql = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStr.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId
					  where tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND strCode = '$strCode' AND permanentTag = 'Y' AND ((usableTag = 'Y' AND availabilityTag = 'Y') OR (stsRefNo = '".$refNo."'))";*/
					  
		$sqlCount = "SELECT     *
			FROM         tblDispDaDtlStr
			WHERE     (strCode = '$strCode') AND (displaySpecsId = '$dispSpecs') AND  permanentTag = 'Y' AND usableTag = 'Y'";
		$recCountDtlStr = $this->getRecCount($this->execQry($sqlCount));
		
		if((int)$recCountDtlStr > 0){
			$sqla = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStr.usableTag = 'Y' AND tblDispDaDtlStr.availabilityTag = 'Y') OR (tblDispDaDtlStrHist.stsRefNo = '".$refNo."') )";

			$resCountSqla = $this->getRecCount($this->execQry($sqla));
			
			if((int)$resCountSqla > 0){
				return $this->getArrRes($this->execQry($sqla));	
				
			}else{
				$sql = "SELECT   top $recCountDtlStr  tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) order by tblDispDaDtlStrHist.stsRefNo ";
				return $this->getArrRes($this->execQry($sql));	
				
			}

		}else{
			$sqlRefNo = "SELECT tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, 
			tblDispDaDtlStr.stsRefNo FROM tblDispDaDtlStr INNER JOIN tblDisplaySpecsDtl ON 
			tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN tblDisplaySpecs ON 
			tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId 
			where tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND strCode = '$strCode' AND stsRefNo = '".$refNo."'";
			
			$countRefNo = $this->getRecCount($this->execQry($sqlRefNo));
			
			if((int)$countRefNo > 0){
				return $this->getArrRes($this->execQry($sqlRefNo));	
			}else{
				$sql = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) ";
				return $this->getArrRes($this->execQry($sql));	
			}
		}
		
		//return $this->getArrRes($this->execQry($sql));	
	}
	function deleteRentablesStr2($refNo,$strCode,$dispId){
		$trans = $this->beginTran();
		$sql = "UPDATe tblDispDaDtlStr set stsRefNo = NULL, startDate = NULL, endDate = NULL, enteredBy = NULL, entryDate = NULL, availabilityTag = 'Y' WHERE strCode = '".$strCode."' AND displaySpecsId = '".$dispId."' AND stsRefNo = '".$refNo."'";	
		if ($trans) {
			$trans = $this->execQry($sql);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function getNumberOfRentables($refNo,$strCode){
		$sql = "SELECT noUnits FROM tblStsDaDetail where stsRefNo = '$refNo' AND strCode = '$strCode'";	
		$arr = $this->getSqlAssoc($this->execQry($sql));
		return $arr['noUnits'];
	}
	function AddSTSRentablesDtl($arr){
			# err code 99 no error, 
			# err code 1 one store with no rentables 
			# err code 2 exceed the number of rentables
			$trans = $this->beginTran();
			$arrHdr = $this->getDispStartEnd($arr['refNo']);
			$arrRent= $this->getRentableBranches($arr['refNo']);
			$errCode = "99";
			
			$sqlInsertReason = "INSERT into tblDaAdjustments (stsRefNo, suppCode, dateAdjusted, adjustedBy, reason, startDate, endDate) 
				VALUES ('".$arr['refNo']."', '".$arr['suppCode']."', '".date('m/d/Y')."', '".$_SESSION['sts-userId']."', '".$arr['txtReason']."', '".$arr['hdnDateFrom']."', '".$arr['hdnDateTo']."')";
				
			if($trans){
				$trans = $this->execQry($sqlInsertReason);
			}	
			foreach($arrRent as $val){
				$arrDispStr = $this->getRentableDtlsTactical($arr['refNo'],$val['dispSpecs'],$val['strCode'],$arr['hdnDateFrom'],$arr['hdnDateTo']);
				
				if($this->deleteRentablesStr2($arr['refNo'],$val['strCode'],$val['dispSpecs'])){
					
					$noUnits = $this->getNumberOfRentables($arr['refNo'],$val['strCode']);
					
					$countRes  = count($arrDispStr);
					$ctrStr = 0;
					$trigger = 0;
					
					foreach($arrDispStr as $valStr){
						$concatenated = $val['strCode'].$valStr['dispDtlId'];
						$ctrStr++;
						
						if((int)$arr["hdnSwitcherRentables_$concatenated"]==1){
							$trigger++;
						}
						if((int)$countRes == (int)$ctrStr){
							if((int)$trigger == 0){
								$trans = false;	
								echo "alert('One Store Found without rentables');";
							}elseif((int)$trigger <> (int)$noUnits){
								$trans = false;
								echo "alert('Number of units not equal to number of rentables selected');";
							}else{
								$trans = true;
							}
						}
						if((int)$arr["hdnSwitcherRentables_$concatenated"]==1){
							
							/*$sql = "UPDATE tblDispDaDtlStr set stsRefNo = '".$arr["refNo"]."', availabilityTag = 'N', startDate = '".$arrHdr['applyDate']."', endDate = '".$arrHdr['endDate']."', entryDate = '".date('m/d/Y')."', enteredBy = '".$_SESSION['sts-userId']."' WHERE strCode = '".$val['strCode']."' AND displaySpecsId = '".$val['dispSpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";	
						
							if ($trans) {
								$trans = $this->execQry($sql);
							}*/
							$sqlCheck = "SELECT availabilityTag FROM tblDispDaDtlStr WHERE strCode = '".$val['strCode']."' AND displaySpecsId = '".$val['dispSpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";
							$arrCheck = $this->getSqlAssoc($this->execQry($sqlCheck));
							
							
							if($arrCheck['availabilityTag']=='Y'){
								$sql = "UPDATE tblDispDaDtlStr set stsRefNo = '".$arr["refNo"]."', availabilityTag = 'N', startDate = '".$arrHdr['impStartDate']."', endDate = '".$arrHdr['impEndDate']."', entryDate = '".date('m/d/Y')."', enteredBy = '".$_SESSION['sts-userId']."' WHERE strCode = '".$val['strCode']."' AND displaySpecsId = '".$val['dispSpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";
							}else{
								$sql = "UPDATE tblDispDaDtlStr set availabilityTag = 'N', enteredBy = '".$_SESSION['sts-userId']."' WHERE strCode = '".$val['strCode']."' AND displaySpecsId = '".$val['dispSpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";
							}
							
							if ($trans) {
								$trans = $this->execQry($sql);
							}
							
							///create a trigger if this is for held
							if((int)$this->checkIfAlreadyExistInDaHist($val['strCode'],$val['dispSpecs'],$valStr['dispDtlId']) == 0){
								$status= "A";
							}else{
								$status= "H";	
							}
							$sql2 = "Insert into tblDispDaDtlStrHist (strCode, displaySpecsId, dispDtlId, stsRefNo, startDate, endDate, enteredBy, entryDate, status) VALUES ('".$val['strCode']."', '".$val['dispSpecs']."', '".$valStr["dispDtlId"]."', '".$arr["refNo"]."', '".$arrHdr['impStartDate']."', '".$arrHdr['impEndDate']."','".$_SESSION['sts-userId']."','".date('m/d/Y')."', '$status')";
							if ($trans) {
								$trans = $this->execQry($sql2);
							}
						}
					}
				}
			}
			if(!$trans){
				$trans = $this->rollbackTran();
				return false;
			}else{
				$trans = $this->commitTran();
				return true;
			}
	}
	function checkIfAlreadyExistInDaHist($strCode,$dispSpecs,$dispDtlId){
		$sql = "Select * from tblDispDaDtlStrHist WHERE strCode = '$strCode' AND displaySpecsId = '$dispSpecs' AND dispDtlId = '$dispDtlId' AND status = 'A'";
		return $this->getRecCount($this->execQry($sql));
	}
	function getDispStartEnd($refNo){
		$sql = "SELECT applyDate, endDate FROM tblStsHdr WHERE stsRefNo = '$refNo'";
		return $this->getSqlAssoc($this->execQry($sql)); 	
	}
}
?>
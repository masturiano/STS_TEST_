<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class daObj extends commonObj {
	/// type 5 For DA
	function countRegSTS(){
		if ($_SESSION['sts-userLevel']!='1')
			$filter = " AND tblfochdr.grpCode='{$_SESSION['sts-grpCode']}' ";
			
		$sql = "Select count(stsRefno) as count From tblfochdr WHERE stsType = '99' $filter";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	
	function getPaginatedDispSTS($sidx,$sord,$start,$limit){	
		//$sql = "Select * From tblfochdr ORDER BY $sidx $sord LIMIT $start , $limit";	
		//if ($_SESSION['sts-userLevel']!='1')
		if($_SESSION['sts-username']!='mike')
			$filter = "AND tblfochdr.grpCode='{$_SESSION['sts-grpCode']}' AND tblfochdr.origStr='{$_SESSION['sts-strCode']}'";
		
		$sql = "SELECT TOP  $limit
			tblfochdr.stsRefNo,
			tblfochdr.suppCode,
			tblfochdr.stsDept,
			tblfochdr.stsRemarks,
			tblfochdr.dateEntered,
			CASE tblfochdr.stsStat
				WHEN 'O' THEN 'OPEN'
				WHEN 'C' THEN 'CANCELLED'
				WHEN 'R' THEN 'RELEASED'
			END AS stsStat,
			sql_mmpgtlib.dbo.APSUPP.ASNAME as suppName,
			tblfochdr.dateApproved,
			(SELECT DISTINCT hierarchyDesc FROM tblStsHierarchy WHERE stsDept = tblfochdr.stsDept AND 	levelCode = 1) as dept
			FROM
			  tblfochdr INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM
			WHERE stsType = '99' AND stsReFNo not in (
				SELECT TOP $start stsRefNo
				FROM tblfochdr
				WHERE stsType = '99' $filter
				ORDER BY $sidx $sord
			)
			$filter
			ORDER BY $sidx $sord
			";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function getPaginatedDispSTSSearch($sidx,$sord,$start,$limit,$refNo){	
		//$sql = "Select * From tblfochdr ORDER BY $sidx $sord LIMIT $start , $limit";	
		//if ($_SESSION['sts-userLevel']!='1')
		if($_SESSION['sts-username']!='mike')
			$filter = "AND tblfochdr.grpCode='{$_SESSION['sts-grpCode']}' AND tblfochdr.origStr='{$_SESSION['sts-strCode']}'";
		
		$sql = "SELECT TOP  $limit
			tblfochdr.stsRefNo,
			tblfochdr.suppCode,
			tblfochdr.stsDept,
			tblfochdr.stsRemarks,
			tblfochdr.dateEntered,
			CASE tblfochdr.stsStat
				WHEN 'O' THEN 'OPEN'
				WHEN 'C' THEN 'CANCELLED'
				WHEN 'R' THEN 'RELEASED'
			END AS stsStat,
			sql_mmpgtlib.dbo.APSUPP.ASNAME as suppName,
			tblfochdr.dateApproved,
			(SELECT DISTINCT hierarchyDesc FROM tblStsHierarchy WHERE stsDept = tblfochdr.stsDept AND 	levelCode = 1) as dept
			FROM
			  tblfochdr INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM
			WHERE stsType = '99' AND stsRefNo = '$refNo' AND stsReFNo not in (
				SELECT TOP $start stsRefNo
				FROM tblfochdr
				WHERE stsType = '99' AND stsRefNo = '$refNo' $filter
				ORDER BY $sidx $sord
			)
			$filter
			ORDER BY $sidx $sord
			";
		return $this->getArrRes($this->execQry($sql));
	}
	function searchDispSTS($sidx,$sord,$start,$limit,$searchField,$searchString){
		
		//if ($_SESSION['sts-userLevel']!='1')
		if($_SESSION['sts-username']!='mike')
			$filter = "AND tblfochdr.grpCode='{$_SESSION['sts-grpCode']}' AND tblfochdr.origStr='{$_SESSION['sts-strCode']}'";
			
		$sql = "SELECT TOP  $limit
			tblfochdr.stsRefNo,
			tblfochdr.suppCode,
			tblfochdr.stsDept,
			tblfochdr.stsRemarks,
			tblfochdr.dateEntered,
			CASE tblfochdr.stsStat
				WHEN 'O' THEN 'OPEN'
				WHEN 'C' THEN 'CANCELLED'
				WHEN 'R' THEN 'RELEASED'
			END AS stsStat,
			sql_mmpgtlib.dbo.APSUPP.ASNAME as suppName,
			tblfochdr.dateApproved,
			(SELECT DISTINCT hierarchyDesc FROM tblStsHierarchy WHERE stsDept = tblfochdr.stsDept AND 	levelCode = 1) as dept
			FROM
			  tblfochdr INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM
			WHERE $searchField = '$searchString' AND stsType = '99' AND stsRefno not in (
				SELECT TOP $start stsRefNo
				FROM tblfochdr
				WHERE stsType = '99' $filter
				ORDER BY $sidx $sord
			)
			$filter ORDER BY $sidx $sord
			";
		//$sql = "Select * From tblfochdr WHERE $searchField = $searchString ORDER BY $sidx $sord LIMIT $start , $limit";	
		return $this->getArrRes($this->execQry($sql));	
	}
	
	function findSupplier($terms){
		$sql = "SELECT TOP 10 sql_mmpgtlib..APSUPP.ASNUM as suppCode, sql_mmpgtlib..APSUPP.ASNAME as suppName, sql_mmpgtlib..APADDR.AACONT as contactPerson 
		FROM sql_mmpgtlib..APSUPP 
		LEFT  JOIN sql_mmpgtlib..APADDR on sql_mmpgtlib..APSUPP.ASNUM  = sql_mmpgtlib..APADDR.AANUM
		WHERE (sql_mmpgtlib..APSUPP.ASNUM like '%$terms%') or (sql_mmpgtlib..APSUPP.ASNAME like '%$terms%') 	
				AND  sql_mmpgtlib..APSUPP.ASNAME not like '%NTBU%'";
		return $this->getArrRes($this->execQry($sql));
	}
	function getAllDept(){
		$sql = "SELECT * FROM tblststranstype WHERE stsTransTypeLvl = 1";	
		return $this->getArrRes($this->execQry($sql));
	}
	function findClass($dept){
		$sql = "SELECT * FROM tblststranstype WHERE stsTransTypeLvl = 2 AND stsTransTypeDept = $dept";	
		return $this->getArrRes($this->execQry($sql));
	}
	function findSubClass($dept,$class){
		$sql = "SELECT * FROM tblststranstype WHERE stsTransTypeLvl = 3 AND stsTransTypeDept = $dept AND stsTransTypeClass = $class";	
		return $this->getArrRes($this->execQry($sql));
	}
	function saveHeader($arr){
		$strCode = '';
		$now = date('m/d/Y H:i:s');
		$trans = $this->beginTran();
		$sqlCount = "SELECT focNo FROM tblFocNo";
		$refNo = $this->getSqlAssoc($this->execQry($sqlCount));
		$tempRefNo = (int)$refNo['focNo']+1;
		$sqlUpdateRefNo = "Update tblFocNo set focNo = $tempRefNo";
		if ($trans) {
			$trans = $this->execQry($sqlUpdateRefNo);
		}
		$newRef = "FOC".$tempRefNo;
		//$arr['txtTerms']==''? $terms = 'NULL': $terms = $arr['txtTerms'];
		$sqlInsert = "INSERT INTO tblfochdr (stsRefNo, suppCode, stsDept, stsCls, stsSubCls, stsRemarks, 
			 stsTerms, nbrApplication, applyDate, enteredBy, dateEntered, grpCode, stsStat, 
			stsType, contractTag, contactPerson, contactPersonPos, endDate, origStr) 
			VALUES 
			('$newRef', '{$arr['hdnSuppCode']}', '0', '0', '0', 
			'{$arr['txtRemarks']}', NULL,
			'{$arr['txtNoApplications']}', '{$arr['txtApDate']}','".$_SESSION['sts-userId']."', '".$now."', '".$_SESSION['sts-grpCode']."', 'O', '99', 'Y', '{$arr['txtRep']}', '{$arr['txtRepPos']}', '{$arr['txtEndDate']}','".$_SESSION['sts-strCode']."')";
		/*if((int)$arr['cmbCompCode']==1002)
			$strCode = '201';
		else
			$strCode = '202';
		$sqlInsertDetail = "INSERT INTO 
						tblFocDtl (
							stsRefNo, stsComp, stsStrCode, stsStrAmt, dtlStatus
							) 
						VALUES (
							$tempRefNo, '{$arr['cmbCompCode']}', '$strCode', '{$arr['txtSTSAmount']}', 'O'
							)";*/
		
		/*$sqlInsertEnhancer = "INSERT INTO tblStsEnhanceDtl (stsRefNo, brandCode, brandRem, enhanceType, dtlStatus) 
		VALUES ('$tempRefNo', '{$arr['cmbBrand']}', '{$arr['txtBRemarks']}',  '{$arr['cmbEnhancer']}', 'O') ";*/
		
		
		if ($trans) {
			$trans = $this->execQry($sqlInsert);
		}
		/*if($trans){
			$trans = $this->execQry($sqlInsertEnhancer);	
		}*/
		
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		} else{
			$trans = $this->commitTran();
			return true;
		}	
	}
	function getLastSTSInserted(){
		$sql = "SELECT TOP 1 stsRefNo
			FROM tblfochdr
			ORDER BY cast(REPLACE(stsRefno, 'FOC', '') as int) desc";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function getRegSTSInfoAssoc($refNo){
		$sql = "SELECT     tblfochdr.stsRefno, tblfochdr.suppCode, tblfochdr.stsAmt, tblfochdr.stsRemarks, tblfochdr.stsPaymentMode, tblfochdr.nbrApplication, tblfochdr.applyDate, tblfochdr.contactPerson, tblfochdr.contactPersonPos, sql_mmpgtlib.dbo.APSUPP.ASNAME as suppName, tblfochdr.endDate, tblfochdr.vatTag
		FROM         tblfochdr INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM WHERE tblfochdr.stsRefNo = '$refNo'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function getRegSTSDetails($refNo){
		$sql = "SELECT     tblFocDtl.stsRefno, tblFocDtl.compCode, tblFocDtl.strCode, tblFocDtl.stsAmt, tblFocDtl.enhanceType, tblBranches.brnShortDesc, tblBranches.brnDesc
FROM         tblBranches INNER JOIN
                      tblFocDtl ON tblBranches.compCode = tblFocDtl.compCode AND tblBranches.strCode = tblFocDtl.strCode WHERE tblFocDtl.stsRefNo =  '$refNo'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function findStore($compCode){
		$sql = "SELECT * from tblbranch where compCode = '$compCode'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getSTSInfo($refNo){
		$sql = "SELECT     *
				FROM         tblfochdr WHERE tblfochdr.stsRefNo = '$refNo'";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function getAllBranches(){
		if((int)$_SESSION['sts-strCode']!=901){
			$filter2 = "WHERE  strCode = ".(int)$_SESSION['sts-strCode']."";
		}
		$sql = "SELECT strCode+'|'+compCode strCodeComp, strCode +' - '+ brnShortDesc as strCodeBranch FROM tblBranches $filter2";
		return $this->getArrRes($this->execQry($sql));
	}
	function getFilteredBranches($compCode,$refNo){
		if($compCode != 'undefined'){
			$sql = "exec sts_getFOCBranchesComp '".$refNo."','".$_SESSION['sts-strCode']."','".$compCode."'";	
		}else{
			$sql = "exec sts_getFOCBranches '".$refNo."','".$_SESSION['sts-strCode']."'";	
		}
		return $this->getArrRes($this->execQry($sql));
	}
	function getEnhancerBranches($refNo){
		$sql = "SELECT     tblFocDtl.stsRefno, tblBranches.brnShortDesc, tblBranches.brnDesc, tblBranches.strCode
FROM         tblFocDtl INNER JOIN
                      tblBranches ON tblFocDtl.compCode = tblBranches.compCode AND tblFocDtl.strCode = tblBranches.strCode WHERE tblFocDtl.stsRefno = '$refNo'";
		return $this->getArrRes($this->execQry($sql));
	}
	function getEnhancerBranchesWithBrand($refNo,$brandCode){
		$sql = "SELECT     tblFocDtl.strCode,
                          (SELECT     enhanceType
                            FROM          tblStsEnhanceDtl
                            WHERE      (brandCode = $brandCode) AND (stsRefno = $refNo) AND strCode = tblFocDtl.strCode) AS enhanceType, tblBranches.brnDesc, tblFocDtl.stsRefno
FROM         tblFocDtl INNER JOIN
                      tblBranches ON tblFocDtl.compCode = tblBranches.compCode AND tblFocDtl.strCode = tblBranches.strCode
WHERE     (tblFocDtl.stsRefno = $refNo)";
		return $this->getArrRes($this->execQry($sql));
	}
	function getBrandEnhancerBranches($refNo,$brandCode){
		
	}
	function getDispStartEnd($refNo){
		$sql = "SELECT applyDate, endDate FROM tblfochdr WHERE stsRefNo = '$refNo'";
		return $this->getSqlAssoc($this->execQry($sql)); 	
	}
	function deleteRentablesStr($refNo,$strCode,$dispId){
		/*$trans = $this->beginTran();
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
		}*/
		
		$trans = $this->beginTran();
		$sql = "UPDATe tblDispDaDtlStr set stsRefNo = NULL, startDate = NULL, endDate = NULL, enteredBy = NULL, entryDate = NULL, availabilityTag = 'Y' WHERE strCode = '".$strCode."' AND displaySpecsId = '".$dispId."' AND stsRefNo = '".$refNo."'";	
		if ($trans) {
			$trans = $this->execQry($sql);
		}
		$sql2 = "Delete FROM tblDispDaDtlStrHist WHERE stsRefNo = '".$refNo."' and strCode = '$strCode' and displaySpecsId = '$dispId'";
		if ($trans) {
			$trans = $this->execQry($sql2);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function deleteRentablesStrRef($refNo){
		$trans = $this->beginTran();
		$sql = "UPDATe tblDispDaDtlStr set stsRefNo = NULL, startDate = NULL, endDate = NULL, enteredBy = NULL, entryDate = NULL, availabilityTag = 'Y' WHERE stsRefNo = '".$refNo."'";	
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
	function untagRentables($refNo,$strCode){
		/*$trans = $this->beginTran();
		$sql = "UPDATe tblDispDaDtlStr set stsRefNo = NULL, startDate = NULL, endDate = NULL, enteredBy = NULL, entryDate = NULL, availabilityTag = 'Y' WHERE strCode = '".$strCode."'  AND stsRefNo = '".$refNo."'";	
		if ($trans) {
			$trans = $this->execQry($sql);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}*/
		$trans = $this->beginTran();
		$sql = "UPDATe tblDispDaDtlStr set stsRefNo = NULL, startDate = NULL, endDate = NULL, enteredBy = NULL, entryDate = NULL, availabilityTag = 'Y' WHERE strCode = '".$strCode."'  AND stsRefNo = '".$refNo."'";	
		if ($trans) {
			$trans = $this->execQry($sql);
		}
		$sql2 = "Delete FROM tblDispDaDtlStrHist WHERE stsRefNo = '".$refNo."' and strCode = '$strCode' ";
		if ($trans) {
			$trans = $this->execQry($sql2);
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
		$sql = "SELECT noUnits FROM tblFocDaDetail where stsRefNo = '$refNo' AND strCode = '$strCode'";	
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
			foreach($arrRent as $val){
				$arrDispStr = $this->getRentableDtlsTactical($arr['refNo'],$val['dispSpecs'],$val['strCode'],$arr['hdnDateFrom'],$arr['hdnDateTo']);
				if($this->deleteRentablesStr($arr['refNo'],$val['strCode'],$val['dispSpecs'])){
					
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
	function AddSTSDtl($arr){
		
		$this->DeleteSTSDtl($arr['hdDtl_refNo']);
		$this->deleteRentablesStrRef($arr['hdDtl_refNo']);
		$nbr = $this->getNbrApplication($arr['hdDtl_refNo']);
		$trans = $this->beginTran();
			for($i=0;$i<=(int)$arr['hdCtr'];$i++) {
				if($arr["switcher_$i"]=="1"){
					$sqlPar = "Insert Into tblFocDaDetail (stsRefNo, compCode, strCode, displayType, brand, location, daSize, dispSpecs, noUnits, daRemarks) 
					VALUES ('{$arr['hdDtl_refNo']}', '".$arr["comp_$i"]."','".$arr["ch_$i"]."', '".$arr['txtDispTyp']."', '".$arr['txtBrand']."','".$arr["txtLoc"]."','".$arr["txtSizeSpecs_$i"]."','".$arr["txtDispSpecs_$i"]."',".$arr["txtNoUnits_$i"].",'".$arr["txtRem"]."');";
					if ($trans) {
						$trans = $this->execQry($sqlPar);
					}
					$sqlDtl = "Insert Into tblFocDtl (stsRefno, compCode, strCode, dtlStatus) 
					VALUES ('{$arr['hdDtl_refNo']}', '".$arr["comp_$i"]."','".$arr["ch_$i"]."','O');";
					if ($trans) {
						$trans = $this->execQry($sqlDtl);
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
	function getNbrApplication($refNo){
		$sql = "SELECT nbrApplication From tblfochdr WHERE stsRefno = '$refNo'";	
		$nbr =  $this->getSqlAssoc($this->execQry($sql));	
		return $nbr['nbrApplication'];
	}
	function addDaDtl($arr){
		//$this->DeleteSTSEnhancerDtl($arr['hdDtl_refNo2'],$arr['cmbBrand']);
		$trans = $this->beginTran();
		$strComp = explode("|",$arr['cmbStr']);
		
		$nbr = $this->getNbrApplication($arr['hdnRefNo2']);
		
		$sqlDtl = "Insert Into tblFocDtl (stsRefno, compCode, strCode, stsAmt, dtlStatus) 
					VALUES ('{$arr['hdnRefNo2']}', '".$strComp[1]."','".$strComp[0]."','".round($arr["txtMonthly"]*$nbr,2)."','O');";
					
		$sqlDa = "Insert Into tblFocDaDetail (stsRefno, compCode, strCode, displayType, brand, location, daSize, dispSpecs, noUnits, daRemarks, perUnitAmt, stsAmt) 
					VALUES ('{$arr['hdnRefNo2']}', '".$strComp[1]."', '".$strComp[0]."', '".$arr['txtDispTyp']."', '".$arr['txtBrand']."','".$arr["txtLoc"]."','".$arr["txtSizeSpecs"]."','".$arr["txtDispSpecs"]."','".$arr["txtNoUnits"]."','".$arr["txtRem"]."','".$arr["txtUnitAmt"]."','".$arr["txtMonthly"]."');";
					
		$sqlUpdateHdr = "Update tblfochdr set stsAmt = (Select sum(stsAmt) from tblFocDtl WHERE stsRefno = '{$arr['hdnRefNo2']}') WHERE stsRefno = '{$arr['hdnRefNo2']}'";
		
		if ($trans) {
			$trans = $this->execQry($sqlDa);
		}
		if ($trans) {
			$trans = $this->execQry($sqlDtl);
		}
		if ($trans) {
			$trans = $this->execQry($sqlUpdateHdr);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}	
	}
	function updateDaDtl($arr){
		
		$trans = $this->beginTran();	
		$strComp = explode("|",$arr['cmbStr']);
		
		$nbr = $this->getNbrApplication($arr['hdnRefNo2']);
		
		//$sqlDtl = "update tblFocDtl (stsRefno, compCode, strCode, stsAmt, dtlStatus) 
					//VALUES ('{$arr['hdnRefNo']}', '".$strComp[1]."','".$strComp[0]."','".round($arr["txtMonthly"]*$nbr,2)."','O');";
		
		$sqlDa = "Update tblFocDaDetail 
					SET displayType = '".$arr['txtDispTyp']."', brand =  '".$arr['txtBrand']."', location = '".$arr["txtLoc"]."',
					 daSize = '".$arr["txtSizeSpecs"]."', dispSpecs = '".$arr["txtDispSpecs"]."', noUnits = '".$arr["txtNoUnits"]."', daRemarks = '".$arr["txtRem"]."', 
					 perUnitAmt = '".$arr["txtUnitAmt"]."', stsAmt='".$arr["txtMonthly"]."'
					WHERE stsRefno = '{$arr['hdnRefNo2']}' AND strCode = '".$strComp[0]."' AND compCode = '".$strComp[1]."'";
		if ($trans) {
			$trans = $this->execQry($sqlDa);
		}
		$sqlDtl = "Update tblFocDtl set stsAmt = '".round($arr["txtMonthly"]*$nbr,2)."' WHERE stsRefno = '{$arr['hdnRefNo2']}' AND strCode = '".$strComp[0]."' AND compCode = '".$strComp[1]."'";		
		if ($trans) {
			$trans = $this->execQry($sqlDtl);
		}
		$sqlUpdateHdr = "Update tblfochdr set stsAmt = (Select sum(stsAmt) from tblFocDtl WHERE stsRefno = '{$arr['hdnRefNo2']}') WHERE stsRefno = '{$arr['hdnRefNo2']}'";
		
		
		if ($trans) {
			$trans = $this->execQry($sqlUpdateHdr);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function DeleteSTSDtl($refNo){
		$trans = $this->beginTran();
		
		$this->deleteRentablesStrRef($refNo);
		
		$sqlDelPar = "DELETE FROM tblFocDaDetail WHERE stsRefno = '$refNo'";
		if ($trans) {
			$trans = $this->execQry($sqlDelPar);
		}
		$sqlDel = "DELETE FROM tblFocDtl WHERE stsRefNo = '$refNo'";

		if ($trans) {
			$trans = $this->execQry($sqlDel);
		}
		$sqlUpdate = "UPDATE tblfochdr set stsAmt = 0 WHERE stsRefno = '$refNo'";
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function DeleteSTSDaDtl($refNo,$strCode){
		$sqlDelDtl = "DELETE FROM tblFocDtl WHERE stsRefNo = '$refNo' AND strCode = '$strCode'";
		$sqlDel = "DELETE FROM tblFocDaDetail WHERE stsRefno = '$refNo' AND strCode = '$strCode'";
		
		$sqlUpdateHdr = "Update tblfochdr set stsAmt = (Select sum(stsAmt) from tblFocDtl WHERE stsRefno = '$refNo') WHERE stsRefno = '$refNo'";
		$trans = $this->beginTran();
		if ($trans) {
			$trans = $this->execQry($sqlDelDtl);
		}
		if ($trans) {
			$trans = $this->execQry($sqlDel);
		}
		if ($trans) {
			$trans = $this->execQry($sqlUpdateHdr);
		}
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function getStsDaDtlArr($refNo){
		$sql = "SELECT * from tblFocDaDetail where stsRefno = '$refNo'";
		return $this->getArrRes($this->execQry($sql));
	}
	function updateHeader($arr){
		$sqlUpdateHdr = "UPDATE tblfochdr SET 
			suppCode = '{$arr['hdnSuppCode']}', 
			endDate = '{$arr['txtEndDate']}', 
			stsRemarks  = '{$arr['txtRemarks']}', 
			stsDept = '0', 
			stsCls = '0', 
			stsSubCls = '0', 
			stsPaymentMode = '{$arr['cmbPayType']}', 
			nbrApplication = '{$arr['txtNoApplications']}',
			applyDate = '{$arr['txtApDate']}',
			contactPerson = '{$arr['txtRep']}',
			contactPersonPos = '{$arr['txtRepPos']}',
			enteredBy = '".$_SESSION['sts-userId']."'
			WHERE stsRefNo = '{$arr['refNo']}'";
		
		$trans = $this->beginTran();
		$count = $this->hasSTSDetail($arr['refNo']);
		///$nbr = $this->getNbrApplication($arr['refNo']);
		if((int)$count>0){		
			$arrPar = $this->getStsDaDtlArr($arr['refNo']);
				foreach($arrPar as $val) {
					$sqlParSts = "update tblFocDtl set stsAmt = '".$val['stsAmt']*$arr['txtNoApplications']."' where strCode='{$val['strCode']}' and compCode='{$val['compCode']}' and stsRefNo='{$arr['refNo']}'\n";
					if ($trans) {
						$trans = $this->execQry($sqlParSts);
					}
				}
		}
		if ($trans) {
			$trans = $this->execQry($sqlUpdateHdr);
		}
		$sqlUpdateHdr2 = "Update tblfochdr set stsAmt = (Select sum(stsAmt) from tblFocDtl WHERE stsRefno = '{$arr['refNo']}') WHERE stsRefno = '{$arr['refNo']}'";
		if ($trans) {
			$trans = $this->execQry($sqlUpdateHdr2);
		}
		/*if($trans){
			$trans = $this->execQry($sqlUpdateEnhance);	
		}*/
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	
	function DeleteSTS($refNo){
		$delPar = "DELETE FROM tblFocDtl WHERE stsRefno = '$refNo'";
		$delDa = "DELETE FROM tblFocDaDetail WHERE stsRefno = '$refNo'";
		$delSTS = "DELETE FROM tblfochdr WHERE stsRefno = '$refNo'";
		//$delEnhanceDtl = "DELETE FROM tblStsEnhanceDtl WHERE stsRefno = '$refNo'";
		
		$this->deleteRentablesStrRef($refNo);
		
		$trans = $this->beginTran();
		if ($trans) {
			$trans = $this->execQry($delPar);
		}
		if ($trans) {
			$trans = $this->execQry($delDa);
		}
		if ($trans) {
			$trans = $this->execQry($delSTS);
		}
		/*if ($trans) {
			$trans = $this->execQry($delEnhanceDtl);
		}*/
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}
	}
	function hasSTSDetail($refNo){
		$sql = "SELECT * FROM tblFocDtl WHERE stsRefno = '$refNo'";	
		return $this->getRecCount($this->execQry($sql));
	}
	function hasSTSDetailAll($refNo){
		$sql = "SELECT     *
			FROM         tblFocDtl
			WHERE     (stsRefno = $refNo) AND (strCode NOT IN
									  (SELECT     strCode
										FROM          tblFocDaDetail
										WHERE      (stsRefno = $refNo)))";	
		return $this->getRecCount($this->execQry($sql));
	}
	function hasEnhancer($refNo){
		$sql = "SELECT     *
			FROM         tblFocDtl
			WHERE     (stsRefno = '$refNo') AND (strCode NOT IN
									  (SELECT     strCode
										FROM          tblFocDaDetail
										WHERE      (stsRefno = '$refNo')))";	
		return $this->getRecCount($this->execQry($sql));
	}
	function releaseSTS($refNo){
		$now = date('Y-m-d H:i:s');
		//$sqlGetSTSNo = "SELECT stsNo FROM pg_pf..tblStsNo";
		//$stsNo = $this->getSqlAssoc($this->execQry($sqlGetSTSNo));
		$qryGetContractNo = "SELECT lastContractNo FROM pg_pf_test..tblContractNo";
		$contractNo = $this->getSqlAssoc($this->execQry($qryGetContractNo));
		$newContract = (int)$contractNo['lastContractNo']+1;
		//$tempSTSNo = (int)$stsNo['stsNo'];
	//	$startingSTS = (int)$stsNo['stsNo']+1;
		$arrPar = $this->getParticipants($refNo);
		
		$trans = $this->beginTran();
		foreach($arrPar as $val){
			$tempSTSNo++;
			$newContract++;
			$sqlDtl = "UPDATE tblFocDtl SET dtlStatus = 'R' WHERE stsRefno = '{$val['stsRefno']}' AND compCode = '{$val['compCode']}' AND strCode = '{$val['strCode']}';";
			$sqlDaDtl = "UPDATE tblFocDaDetail SET  contractNo = '$newContract' WHERE stsRefno = '{$val['stsRefno']}' AND strCode =  '{$val['strCode']}'";
			if ($trans) {
				$trans = $this->execQry($sqlDtl);
			}
			if ($trans) {
				$trans = $this->execQry($sqlDaDtl);
			}
		}
		//$sqlUpdateSTSNo = "UPDATE pg_pf..tblStsNo SET stsNo = '$tempSTSNo';";
		$sqlUpdateContract = "UPDATE pg_pf_test..tblContractNo SET lastContractNo = '$newContract'";
		$sqlUpdateHeader = "UPDATE tblfochdr SET approvedBy = '".$_SESSION['sts-userId']."', dateApproved = '".date('Y-m-d')."', stsStat = 'R', contractNo = '$newContract' WHERE stsRefNo = '$refNo';";
		//$sqlUpdateEnhncer = "UPDATE tblStsEnhanceDtl SET dtlStatus = 'R' WHERE stsRefno = '$refNo'";
		
		if ($trans){
			$trans = $this->execQry($sqlUpdateHeader);
		}
		/*if ($trans){
			$trans = $this->execQry($sqlUpdateSTSNo);
		}*/
		if ($trans){
			$trans = $this->execQry($sqlUpdateContract);
		}
		/*if ($trans){
			$trans = $this->execQry($sqlUpdateEnhncer);
		}*/
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}	
	}
	function getParticipants($refNo){
		$sql = "SELECT * FROM tblFocDtl WHERE stsRefno = '$refNo'";
		return $this->getArrRes($this->execQry($sql));
	}
	function countParticipants($refNo){
		$sql = "SELECT * FROM tblFocDtl WHERE stsRefno = '$refNo'";
		return $this->getRecCount($this->execQry($sql));
	}
	function distinctSuppCur(){
		$sql = "SELECT DISTINCT suppCurr FROM tblsuppliers";	
		return $this->getArrRes($this->execQry($sql));
	}
	function calculateUploadedAmt($stsNo,$compCode,$strCode,$seqNo){
		$sql = "SELECT SUM(stsApplyAmt) as stsApplyAmt FROM tblstsapply WHERE stsNo = '$stsNo' AND status = 'A' AND compCode = '$compCode' AND strCode='$strCode' AND stsSeq = '$seqNo'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function calculateQueuedAmt($stsNo,$compCode,$strCode,$seqNo){
		$sql = "SELECT SUM(stsApplyAmt) as stsApplyAmt FROM tblstsapply WHERE stsNo = '$stsNo' AND status IS NULL AND compCode = '$compCode' AND strCode = '$strCode' AND stsSeq = '$seqNo'";
		return $this->getSqlAssoc($this->execQry($sql));	
	}
	function calculateUploadedAmtSum($refNo){
		$sql = "SELECT SUM(stsApplyAmt) as stsApplyAmt FROM tblstsapply WHERE stsRefno = '$refNo' AND status = 'A' ";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function calculateQueuedAmtSum($refNo){
		$sql = "SELECT SUM(stsApplyAmt) as stsApplyAmt FROM tblstsapply WHERE stsRefno = '$refNo' AND status IS NULL";
		return $this->getSqlAssoc($this->execQry($sql));	
	}
	function getLastCancelledId(){
		$sql = "SELECT MAX(cancelId) as cancelId FROM tblcanceltype;";	
		$lastId = $this->getSqlAssoc($this->execQry($sql));
		return $lastId['cancelId'];
	}
	function getCancelledSTS($refNo){
		$sql = "SELECT * FROM tblCancelledSts WHERE stsRefno = '$refNo'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function cancelSTS($refNo,$reason,$cancelDate){
		$trans = $this->beginTran();
		
		$sqlInsertReason = "INSERT INTO tblCancelType (cancelDesc, cancelStat, createdBy, dateAdded) 
			VALUES ('$reason', 'A', '".$_SESSION['sts-userId']."', '".date('m/d/Y',strtotime($cancelDate))."');";
		if($trans){
			$trans = $this->execQry($sqlInsertReason);	
		}
		
		if($trans){
			$lastId = $this->getLastCancelledId();
		}
		
		$sqlInsertCancelled = "INSERT INTO tblcancelledsts (stsNo, stsSeq, stsRefNo, compCode, strCode, suppCode, stsType, stsPaymentMode, stsDept, stsCls, stsSubCls, grpCode, stsApplyDate) 
		SELECT stsNo, stsSeq, stsRefNo, compCode, strCode, suppCode, stsType, stsPaymentMode, stsDept, stsCls, stsSubCls, grpCode, stsApplyDate FROM tblstsapply WHERE  stsRefno = '$refNo'  AND stsApplyDate >= '$cancelDate';";
		if($trans){
			$trans = $this->execQry($sqlInsertCancelled);	
		}
		$arrCancelled = $this->getCancelledSTS($refNo);
		
		foreach($arrCancelled as $val){
			
			$uploadAmt = $this->calculateUploadedAmt($val['stsNo'],$val['compCode'],$val['strCode'],$val['stsSeq']);
			$uploadAmt['stsApplyAmt']=='' ? $totUploadAmt = 'NULL' : $totUploadAmt = $uploadAmt['stsApplyAmt'];
			
			$qAmt = $this->calculateQueuedAmt($val['stsNo'],$val['compCode'],$val['strCode'],$val['stsSeq']);
			$qAmt['stsApplyAmt']=='' ? $totQAmt = 'NULL' : $totQAmt = $qAmt['stsApplyAmt'];
			
			$strAmt = $this->getStrAmt($refNo,$val['compCode'],$val['strCode']);
			$sqlUpdateCancelled = "UPDATE tblCancelledSts SET stsStrAmt = ".$strAmt.", uploadedAmt = ".$totUploadAmt.", queueAmt = ".$totQAmt.", cancelledBy = '".$_SESSION['sts-userId']."', cancelDate = '".date('m/d/Y',strtotime($cancelDate))."', cancelCode = '".$lastId."' WHERE stsNo = '{$val['stsNo']}' AND compCode = '".$val['compCode']."' AND strCode = '".$val['strCode']."' AND stsSeq = '{$val['stsSeq']}'\n;";
			
			if($trans){
				$trans = $this->execQry($sqlUpdateCancelled);	
			}
		}
		
		$sqlDelStsApply = "DELETE FROM tblstsapply WHERE stsRefNo = '$refNo' AND stsApplyDate >= '$cancelDate';";
		if($trans){
			$trans = $this->execQry($sqlDelStsApply);	
		}
		
		$sqlUpdateSTSHdr = "UPDATE tblfochdr SET stsStat = 'C', cancelDate = '".date('m/d/Y',strtotime($cancelDate))."', cancelledBy = '".$_SESSION['sts-userId']."', cancelId = '".$lastId."' WHERE stsRefNo = '$refNo'";
		if($trans){
			$trans = $this->execQry($sqlUpdateSTSHdr);	
		}
		$sqlUpdateSTSDtl = "UPDATE tblFocDtl SET dtlStatus = 'C' WHERE stsRefNo = '$refNo'";
		if($trans){
			$trans = $this->execQry($sqlUpdateSTSDtl);	
		}
		
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;
		}else{
			$trans = $this->commitTran();
			return true;
		}	
	}
	function getStrAmt($refNo,$compCode,$strCode){
		$sql = "SELECT stsAmt FROM tblFocDtl WHERE stsRefNo = '$refNo' AND compCode = '$compCode' AND strCode = '$strCode'";	
		$amt = $this->getSqlAssoc($this->execQry($sql));
		return $amt['stsAmt'];
	}
	function getAllBrand(){
		$sql = "SELECT * FROM tblBrand WHERE stat = 'A'";
		return $this->getArrRes($this->execQry($sql));
	}
	function getAllEnhancerType(){
		$sql = "SELECT * FROm tblEnhancerType WHERE stat = 'A'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function PrintContract($refNo) {
		$count = $this->checkifPrinted($refNo);
		if ( (int)$count > 0) {
			$fields = "SET stsDatePrinted='".date('m/d/Y')."',stsPrintedBy='".$_SESSION['sts-userId']."'";
		} else {
			$fields = "SET stsDateReprinted='".date('m/d/Y')."',stsReprintedBy='".$_SESSION['sts-userId']."'";
		}
		$sqlPrintContract = "Update tblfochdr $fields where stsRefNo='$refNo'";
		$Trns = $this->beginTran();
		if ($Trns) {
			$Trns = $this->execQry($sqlPrintContract);
		}
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		} else{
			$Trns = $this->commitTran();
			return true;
		}	
	}
	function checkifPrinted($refNo) {
		$sql = "SELECT * FROM tblfochdr WHERE stsRefNo = '$refNo' AND stsPrintedBy IS NULL";
		return $this->getRecCount($this->execQry($sql));
	}
	function getContractInfo($refNo){
		$sql = "SELECT     tblfochdr.stsRefno, tblfochdr.dateApproved, tblfochdr.contractNo, tblfochdr.applyDate, tblfochdr.nbrApplication, tblfochdr.stsAmt, tblfochdr.stsRemarks, DATEADD(month, 
                      tblfochdr.nbrApplication - 1, tblfochdr.applyDate) AS endDate, DATEADD(month, tblfochdr.nbrApplication, tblfochdr.applyDate) AS endDate2, tblUsers.fullName, 
                      tblfochdr.contactPerson, tblfochdr.contactPersonPos, sql_mmpgtlib.dbo.APSUPP.AANAME AS suppName, sql_mmpgtlib.dbo.APADDR.AAADD1 AS add1, 
                      sql_mmpgtlib.dbo.APADDR.AAADD2 AS add2, sql_mmpgtlib.dbo.APADDR.AAADD3 AS add3, 
                      payMode = CASE tblfochdr.stsPaymentMode WHEN 'C' THEN 'CHECK Payment ' ELSE ' Invoice Deduction ' END
FROM         tblfochdr INNER JOIN
                      tblUsers ON tblfochdr.enteredBy = tblUsers.userId INNER JOIN
                      sql_mmpgtlib.dbo.APADDR ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APADDR.AANUM 
					  INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM 
					  WHERE tblfochdr.stsRefNo = '$refNo'";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function getDistinctCompanies(){
		$sql = "SELECT compCode,compShort FROM tblCompany";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getEnhancerDetails($refNo){
		$sql = "SELECT     tblBranches.brnDesc AS brnDesc, tblFocDaDetail.stsRefno, tblFocDaDetail.compCode, tblFocDaDetail.strCode, tblFocDaDetail.location, 
                      tblFocDaDetail.daSize, tblFocDaDetail.dispSpecs, tblFocDaDetail.noUnits, tblFocDaDetail.daRemarks, tblFocDaDetail.contractNo, tblFocDaDetail.stsNo, 
                      tblFocDaDetail.perUnitAmt, tblFocDaDetail.stsAmt, tblDisplaySpecs.displaySpecsDesc AS displayType, tblFocDaDetail.brand
FROM         tblFocDaDetail INNER JOIN
                      tblBranches ON tblBranches.strCode = tblFocDaDetail.strCode INNER JOIN
                      tblDisplaySpecs ON tblFocDaDetail.dispSpecs  = tblDisplaySpecs.displaySpecsId WHERE tblFocDaDetail.stsRefno = '$refNo' order by tblFocDaDetail.strCode";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getEnhancerDetails2($refNo){
		/*$sql = "SELECT     tblBranches.brnDesc AS brnDesc, tblFocDaDetail.stsRefno, tblFocDaDetail.compCode, tblFocDaDetail.strCode, tblFocDaDetail.location, 
                      tblFocDaDetail.daSize, tblFocDaDetail.dispSpecs, tblFocDaDetail.noUnits, tblFocDaDetail.daRemarks, tblFocDaDetail.contractNo, tblFocDaDetail.stsNo, 
                      tblFocDaDetail.perUnitAmt, tblFocDaDetail.stsAmt, tblDisplaySpecs.displaySpecsDesc AS displayType, tblFocDaDetail.brand, tblFocDtl.dtlStatus
FROM         tblFocDaDetail INNER JOIN
                      tblBranches ON tblBranches.strCode = tblFocDaDetail.strCode INNER JOIN
                      tblDisplaySpecs ON tblFocDaDetail.dispSpecs = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblFocDtl ON tblFocDaDetail.stsRefno = tblFocDtl.stsRefno AND tblFocDaDetail.compCode = tblFocDtl.compCode AND tblFocDaDetail.strCode = tblFocDtl.strCode AND 
                      tblFocDaDetail.stsNo = tblFocDtl.stsNo WHERE tblFocDaDetail.stsRefno = '$refNo' AND (tblFocDtl.dtlStatus <> 'C' OR
                      tblFocDtl.dtlStatus IS NULL) order by tblFocDaDetail.strCode";*/
		$sql = "SELECT     tblBranches.brnDesc AS brnDesc, tblFocDaDetail.stsRefno, tblFocDaDetail.compCode, tblFocDaDetail.strCode, tblFocDaDetail.location, 
                      tblFocDaDetail.daSize, tblFocDaDetail.dispSpecs, tblFocDaDetail.noUnits, tblFocDaDetail.daRemarks, tblFocDaDetail.contractNo, tblFocDaDetail.stsNo, 
                      tblFocDaDetail.perUnitAmt, tblFocDaDetail.stsAmt, tblDisplaySpecs.displaySpecsDesc AS displayType, tblFocDaDetail.brand, tblFocDtl.dtlStatus
FROM         tblFocDaDetail INNER JOIN
                      tblBranches ON tblBranches.strCode = tblFocDaDetail.strCode INNER JOIN
                      tblDisplaySpecs ON tblFocDaDetail.dispSpecs = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblFocDtl ON tblFocDaDetail.stsRefno = tblFocDtl.stsRefno AND tblFocDaDetail.compCode = tblFocDtl.compCode AND tblFocDaDetail.strCode = tblFocDtl.strCode AND 
                      tblFocDaDetail.stsNo = tblFocDtl.stsNo WHERE tblFocDaDetail.stsRefno = '$refNo'
					  order by tblFocDaDetail.strCode";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getStsDaDtl($refNo,$strCode){
		$sql = "SELECT * FROM tblFocDaDetail 
			WHERE stsRefno = '$refNo' AND strCode = '$strCode'";	
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function brandExists($refNo,$strCode){
		$sql = "SELECT * FROM tblFocDtl WHERE stsRefno = '$refNo' AND strCode = '$strCode'";
		return $this->getRecCount($this->execQry($sql));
	}
	function getDistinctCategoryBrand($refNo){
		$sql = "SELECT DISTINCT tblStsEnhanceDtl.category, tblBrand.stsBrandDesc, tblStsEnhanceDtl.brandCode, tblStsEnhanceDtl.stsRefno
FROM         tblStsEnhanceDtl INNER JOIN
                      tblBrand ON tblStsEnhanceDtl.brandCode = tblBrand.stsBrand WHERE tblStsEnhanceDtl.stsRefno='$refNo'";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getBrandParticipants($refNo,$brandCode){
		$sql = "SELECT     tblStsEnhanceDtl.category, tblBrand.stsBrandDesc, tblStsEnhanceDtl.brandCode, tblStsEnhanceDtl.stsRefno, tblEnhancerType.enhanceDesc, 
                      tblBranches.brnDesc
FROM         tblStsEnhanceDtl INNER JOIN
                      tblBrand ON tblStsEnhanceDtl.brandCode = tblBrand.stsBrand INNER JOIN
                      tblEnhancerType ON tblStsEnhanceDtl.enhanceType = tblEnhancerType.enhanceType INNER JOIN
                      tblBranches ON tblStsEnhanceDtl.strCode = tblBranches.strCode WHERE stsRefno = '$refNo' AND brandCode = '$brandCode'";
		return $this->getArrRes($this->execQry($sql));	
	}
	function getDaContract($refNo,$strCode){
			$sql = "SELECT      tblFocDaDetail.contractNo, tblBranches.brnDesc, tblFocDtl.stsAmt, tblFocDtl.stsNo, sql_mmpgtlib.dbo.APADDR.AANUM,
		tblfochdr.contactPersonPos, tblfochdr.contactPerson, sql_mmpgtlib.dbo.APSUPP.ASNAME as suppName, tblfochdr.applyDate, tblfochdr.nbrApplication, 
		tblFocDaDetail.displayType, tblFocDaDetail.brand, tblFocDaDetail.location, tblFocDaDetail.daSize, tblFocDaDetail.dispSpecs, 
		tblFocDaDetail.noUnits, tblFocDaDetail.daRemarks,
                      tblfochdr.endDate AS endDate,   DATEADD(month, tblfochdr.nbrApplication, tblfochdr.applyDate) AS endDate2,
					 sql_mmpgtlib.dbo.APADDR.AAADD1 AS add1, 
                      sql_mmpgtlib.dbo.APADDR.AAADD2 AS add2, sql_mmpgtlib.dbo.APADDR.AAADD3 AS add3, 
                      payMode = CASE tblfochdr.stsPaymentMode WHEN 'C' THEN 'CHECK Payment' ELSE 'Invoice Deduction' END, tblGroup.grpDesc, tblFocDaDetail.perUnitAmt,
					  tblFocDaDetail.stsAmt as stsAmtDa, tblUsers.fullName, tblDisplayType.displayTypeDesc,tblDisplaySpecs.displaySpecsDesc, 
                      tblSizeSpecs.sizeSpecsDesc, tblUsers_1.fullName AS approvedBy,tblFocDtl.dtlStatus AS dtlStatus, tblfochdr.stsPaymentMode,tblFocDaDetail.stsVatAmt,  tblFocDaDetail.stsEwtAmt,tblFocDaDetail.stsAmt,
					   (SELECT     TOP 1 effectivityDate
                            FROM          tblcancelledSts
                            WHERE      stsRefno = tblfochdr.stsrefNo) AS effectivityDate,
						(SELECT     TOP 1 cancelDate
                            FROM          tblcancelledSts
                            WHERE      stsRefno = tblfochdr.stsrefNo) AS cancelDate
		FROM         tblBranches INNER JOIN
                      tblFocDaDetail ON tblBranches.strCode = tblFocDaDetail.strCode INNER JOIN
                      tblFocDtl ON tblFocDaDetail.stsRefno = tblFocDtl.stsRefno AND tblFocDaDetail.strCode = tblFocDtl.strCode INNER JOIN
                      tblfochdr ON tblFocDaDetail.stsRefno = tblfochdr.stsRefno INNER JOIN
                      sql_mmpgtlib.dbo.APADDR ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APADDR.AANUM  
					   INNER JOIN
                      sql_mmpgtlib.dbo.APSUPP ON tblfochdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM INNER JOIN
                      tblGroup ON tblfochdr.grpCode = tblGroup.grpCode INNER JOIN
					  tblUsers ON tblfochdr.enteredBy = tblUsers.userId  INNER JOIN
                      tblDisplayType ON tblFocDaDetail.displayType = tblDisplayType.displayTypeId INNER JOIN
                      tblDisplaySpecs ON tblFocDaDetail.dispSpecs = tblDisplaySpecs.displaySpecsId INNER JOIN
                      tblSizeSpecs ON tblFocDaDetail.daSize = tblSizeSpecs.sizeSpecsId  LEFT JOIN
                      tblUsers tblUsers_1 ON tblfochdr.approvedBy = tblUsers_1.userId
		WHERE tblFocDaDetail.stsRefno = '$refNo' AND tblFocDaDetail.strCode = '$strCode'";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function getSizeSpecs(){
		$sql = "SELECT * FROM tblSizeSpecs";	
		return $this->getArrRes($this->execQry($sql));	
	}
	function getDisplaySpecs(){
		$sql = "SELECT * FROM tblDisplaySpecs WHERE stat = 'A'";	
		return $this->getArrRes($this->execQry($sql));	
	}
	function getDisplayType(){
		$sql = "SELECT * FROM tblDisplayType";	
		return $this->getArrRes($this->execQry($sql));	
	}
	function getCancelDates($refNo){
		$sql = "SELECT Distinct stsApplyDate FROM tblStsApply where stsRefNo = '$refNo'";
		return $this->getArrRes($this->execQry($sql));	
	}
	function daExists($refNo){
		$sql = "SELECT * FROM tblFocDaDetail WHERE stsRefno = '$refNo'";
		return $this->getRecCount($this->execQry($sql));
	}
	function getDaHeader($refNo){
		$sql = "SELECT   TOP 1  displayType, brand, location, daRemarks
FROM         tblFocDaDetail
WHERE     (stsRefno = '$refNo')";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	function getVat(){
		$sql = "SELECT top 1 vatMultiplier FROM tblVat";	
		$arr = $this->getSqlAssoc($this->execQry($sql));
		return $arr['vatMultiplier'];
	}
	function getEwt($dept,$class,$subClass){
		$sql = "SELECT ewtMultiplier FROM tblStsHierarchy WHERE (stsDept = '$dept') AND (stsCls = '$class') AND (stsSubCls = '$subClass')";	
		$arr = $this->getSqlAssoc($this->execQry($sql));
		return $arr['ewtMultiplier'];
	}
	function checkPayment($refNo){
		$sql = "SELECT stsPaymentMode from tblfochdr where stsrefno = '$refNo'";
		$arr = $this->getSqlAssoc($this->execQry($sql));
		return $arr['stsPaymentMode'];
	}
	function getRentableDtls($refNo,$dispSpecs,$strCode){
		$sql = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStr.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId
					  where tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND strCode = '$strCode' AND ((usableTag = 'Y' AND availabilityTag = 'Y') OR (stsRefNo = '".$refNo."'))";
		return $this->getArrRes($this->execQry($sql));	
	}
	
	function getRentableDtlsTactical($refNo,$dispSpecs,$strCode,$dateFrom,$dateTo){
		/*$sql = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStr.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId
					  where tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND strCode = '$strCode' AND permanentTag = 'Y' AND ((usableTag = 'Y' AND availabilityTag = 'Y') OR (stsRefNo = '".$refNo."'))";
		return $this->getArrRes($this->execQry($sql));	*/
		
		$sqlCount = "SELECT     tblDispDaDtlStr.*, tblLocation.locDescription as locDescription
FROM         tblDispDaDtlStr LEFT JOIN
                      tblLocation ON tblDispDaDtlStr.locId = tblLocation.locId
			WHERE     (strCode = '$strCode') AND (displaySpecsId = '$dispSpecs') AND  permanentTag = 'Y' AND usableTag = 'Y'";
		$recCountDtlStr = $this->getRecCount($this->execQry($sqlCount));
		
		if((int)$recCountDtlStr > 0){
			/* $sqla = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStr.usableTag = 'Y' AND tblDispDaDtlStr.availabilityTag = 'Y') AND (tblDispDaDtlStrHist.stsRefNo = '".$refNo."') )";*/

			$sqla = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo, tblLocation.locDescription as locDescription
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
					  LEFT JOIN
                      tblLocation ON tblDispDaDtlStr.locId = tblLocation.locId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ( (tblDispDaDtlStrHist.stsRefNo = '".$refNo."') )";

			$resCountSqla = $this->getRecCount($this->execQry($sqla));
			
			if((int)$resCountSqla > 0){
				return $this->getArrRes($this->execQry($sqla));	
				
			}else{
				/*$sql = "SELECT   top $recCountDtlStr  tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) order by tblDispDaDtlStrHist.stsRefNo ";
				return $this->getArrRes($this->execQry($sql));	*/
				/*echo $sql = "SELECT   top $recCountDtlStr  tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo, tblLocation.locDescription as locDescription
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
					   LEFT JOIN
                      tblLocation ON tblDispDaDtlStr.locId = tblLocation.locId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) order by tblDispDaDtlStrHist.stsRefNo ";*/
			$sql = "SELECT   top $recCountDtlStr  tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblLocation.locDescription as locDescription
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
					   LEFT JOIN
                      tblLocation ON tblDispDaDtlStr.locId = tblLocation.locId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) GROUP BY tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblLocation.locDescription ";
				return $this->getArrRes($this->execQry($sql));	
				
			}

		}else{
			/*$sqlRefNo = "SELECT tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, 
			tblDispDaDtlStr.stsRefNo FROM tblDispDaDtlStr INNER JOIN tblDisplaySpecsDtl ON 
			tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN tblDisplaySpecs ON 
			tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId 
			where tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND strCode = '$strCode' AND stsRefNo = '".$refNo."'";
			
			$countRefNo = $this->getRecCount($this->execQry($sqlRefNo));*/
			 $sqlRefNo = "SELECT tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, 
			tblDispDaDtlStr.stsRefNo, tblLocation.locDescription as locDescription
			FROM tblDispDaDtlStr INNER JOIN tblDisplaySpecsDtl ON 
			tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN tblDisplaySpecs ON 
			tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId 
			 LEFT JOIN
                      tblLocation ON tblDispDaDtlStr.locId = tblLocation.locId
			where tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND strCode = '$strCode' AND stsRefNo = '".$refNo."'";
			
			$countRefNo = $this->getRecCount($this->execQry($sqlRefNo));
			
			if((int)$countRefNo > 0){
				return $this->getArrRes($this->execQry($sqlRefNo));	
			}else{
				/*$sql = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) ";
				return $this->getArrRes($this->execQry($sql));*/
				$sql = "SELECT     tblDisplaySpecsDtl.dispDesc, tblDisplaySpecs.displaySpecsDesc, tblDispDaDtlStr.dispDtlId, tblDispDaDtlStrHist.stsRefNo, tblLocation.locDescription as locDescription
FROM         tblDispDaDtlStr INNER JOIN
                      tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId INNER JOIN
                      tblDisplaySpecs ON tblDisplaySpecsDtl.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
                      tblDispDaDtlStrHist ON tblDispDaDtlStr.strCode = tblDispDaDtlStrHist.strCode AND tblDispDaDtlStr.displaySpecsId = tblDispDaDtlStrHist.displaySpecsId AND 
                      tblDispDaDtlStr.dispDtlId = tblDispDaDtlStrHist.dispDtlId
					  LEFT JOIN
                      tblLocation ON tblDispDaDtlStr.locId = tblLocation.locId
WHERE     tblDisplaySpecsDtl.displaySpecsId = '$dispSpecs' AND tblDispDaDtlStr.strCode = '$strCode' AND tblDispDaDtlStr.permanentTag = 'Y' AND ((tblDispDaDtlStrHist.stsRefNo is null) OR (tblDispDaDtlStrHist.endDate < '".$dateFrom."')) ";
				return $this->getArrRes($this->execQry($sql));		
			}
		}
	}
	
	function getRentableBranches($refNo){
		$sql = "SELECT     tblFocDaDetail.stsRefno, tblFocDaDetail.strCode, tblBranches.brnDesc, tblFocDaDetail.displayType, tblFocDaDetail.dispSpecs, tblFocDaDetail.noUnits
FROM         tblFocDaDetail INNER JOIN
                      tblBranches ON tblFocDaDetail.strCode = tblBranches.strCode
WHERE     (tblFocDaDetail.stsRefno = '$refNo')";	
		return $this->getArrRes($this->execQry($sql));	
	}
	
	function addStsRentableDtlperStr($arr){
		$trans = $this->beginTran();
		$arrHdr = $this->getDispStartEnd($arr['refNo']);
		
		$arrDispStr = $this->getRentableDtlsTactical($arr['refNo'],$arr['displaySpecs'],$arr['strCode'],$arr['hdnDateFrom'],$arr['hdnDateTo']);
		if($this->deleteRentablesStr($arr['refNo'],$arr['strCode'],$arr['displaySpecs'])){
			
			$noUnits = $this->getNumberOfRentables($arr['refNo'],$arr['strCode']);
			
			$countRes  = count($arrDispStr);
			$ctrStr = 0;
			$trigger = 0;
			$ctrSuccess = 0;
			foreach($arrDispStr as $valStr){
				$arr["switcherR_".$ctrStr];
				$concatenated = $arr['strCode'].$valStr['dispDtlId'];
				
				if((int)$arr["switcherR_".$ctrStr]==1){
					
					$sqlCheck = "SELECT availabilityTag FROM tblDispDaDtlStr WHERE strCode = '".$arr['strCode']."' AND displaySpecsId = '".$arr['displaySpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";
					$arrCheck = $this->getSqlAssoc($this->execQry($sqlCheck));
					
					if($arrCheck['availabilityTag']=='Y'){
						$sql = "UPDATE tblDispDaDtlStr set stsRefNo = '".$arr["refNo"]."', availabilityTag = 'N', startDate = '".$arrHdr['impStartDate']."', endDate = '".$arrHdr['impEndDate']."', entryDate = '".date('m/d/Y')."', enteredBy = '".$_SESSION['sts-userId']."' WHERE strCode = '".$arr['strCode']."' AND displaySpecsId = '".$arr['displaySpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";
					}else{
						$sql = "UPDATE tblDispDaDtlStr set availabilityTag = 'N', enteredBy = '".$_SESSION['sts-userId']."' WHERE strCode = '".$arr['strCode']."' AND displaySpecsId = '".$arr['displaySpecs']."' AND dispDtlId = '".$valStr["dispDtlId"]."' ";
					}
					
					if ($trans) {
						$trans = $this->execQry($sql);
					}
					
					///create a trigger if this is for held
					if((int)$this->checkIfAlreadyExistInDaHist($arr['strCode'],$arr['displaySpecs'],$valStr['dispDtlId']) == 0){
						$status= "A";
					}else{
						$status= "H";	
					}
					$sql2 = "Insert into tblDispDaDtlStrHist (strCode, displaySpecsId, dispDtlId, stsRefNo, startDate, endDate, enteredBy, entryDate, status) VALUES ('".$arr['strCode']."', '".$arr['displaySpecs']."', '".$valStr["dispDtlId"]."', '".$arr["refNo"]."', '".$arrHdr['impStartDate']."', '".$arrHdr['impEndDate']."','".$_SESSION['sts-userId']."','".date('m/d/Y')."', '$status')";
					if ($trans) {
						$trans = $this->execQry($sql2);
					}
					$ctrSuccess++;
				}
				$ctrStr++;
			}
		}
		if(!$trans){
			$trans = $this->rollbackTran();
		}else{
			$trans = $this->commitTran();
		}
		return $ctrSuccess++;	
	}
	
}	
?>

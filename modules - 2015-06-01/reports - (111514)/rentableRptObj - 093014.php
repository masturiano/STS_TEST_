<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class rentableRptObj extends commonObj {
	
	function findSupplier(){
		$sql = "SELECT DISTINCT 
                      sql_mmpgtlib.dbo.APSUPP.ASNAME 

			AS suppName, sql_mmpgtlib.dbo.APSUPP.ASNUM AS 
			
			suppCode, CAST(sql_mmpgtlib.dbo.APSUPP.ASNUM AS 
			
			varchar) 
								  + ' - ' + 
			
			sql_mmpgtlib.dbo.APSUPP.ASNAME AS suppCodeName
			FROM         tblStsHdr left  JOIN
								  sql_mmpgtlib.dbo.APSUPP ON 
			
			tblStsHdr.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM
			ORDER BY sql_mmpgtlib.dbo.APSUPP.ASNAME";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function findGroup(){
		$sql = "select * from tblgroup";	
		return $this->getArrRes($this->execQry($sql));
	}
	
	function getBranches(){
		$sql = "SELECT strCode, brnDesc, cast(strCode as nvarchar)+' - '+brnDesc as strCodeName FROM pg_pf..tblbranches order by strCode";	
		return $this->getArrRes($this->execQry($sql));
	}
	
	function findDisplaySpecs(){
		$sql = "
			SELECT     displaySpecsId, displaySpecsDesc, createdBy, dateCreated, stat
FROM         tblDisplaySpecs order by displaySpecsDesc";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function specsDetail($strCode,$dispSpecs,$availTag){
		if($strCode == 0){
			$filterStrCode = "";
		}else{
			$filterStrCode = "AND tblDispDaDtlStr.strCode = '{$strCode}'";
		}
		
		if($dispSpecs == 0){
			$filterDispSpecs = "";
		}else{
			$filterDispSpecs = "AND tblDispDaDtlStr.displaySpecsId = '{$dispSpecs}'";
		}
		
		if($availTag == 'Y'){
			$filterAvailTag = "(tblDispDaDtlStr.availabilityTag = 'Y' OR tblDispDaDtlStr.availabilityTag is null)";
		}else{
			$filterAvailTag = "tblDispDaDtlStr.availabilityTag = 'N'";
		}
		
		$sql = "SELECT     
		tblDispDaDtlStr.strCode, tblBranches.brnDesc, tblDisplaySpecs.displaySpecsDesc, tblDisplaySpecsDtl.dispDesc, 
		tblDispDaDtlStr.startDate, tblDispDaDtlStr.endDate, tblDispDaDtlStr.stsRefNo, tblDispDaDtlStr.availabilityTag,
		CASE WHEN tblDispDaDtlStr.availabilityTag = 'N' THEN 'Not Available' ELSE 'Available' END as availTag,
		tblDispDaDtlStr.displaySpecsId,
		tblDisplaySpecsDtl.createdBy, tblDisplaySpecsDtl.dateCreated, 
        tblDisplaySpecsDtl.status, tblDisplaySpecsDtl.grpCode, tblUsers.fullName
		FROM tblDispDaDtlStr LEFT OUTER JOIN
		tblDisplaySpecsDtl ON tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId LEFT OUTER JOIN
		tblDisplaySpecs ON tblDispDaDtlStr.displaySpecsId = tblDisplaySpecs.displaySpecsId LEFT OUTER JOIN
		tblBranches ON tblBranches.strCode = tblDispDaDtlStr.strCode LEFT OUTER JOIN
		tblUsers ON tblUsers.userId = tblDisplaySpecsDtl.createdBy
		WHERE $filterAvailTag
		$filterStrCode
		$filterDispSpecs
		";
		return $this->getArrRes($this->execQry($sql));
	}
}
?>
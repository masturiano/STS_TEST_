<?
$now = date('Y-m-d H:i:s');
ini_set("date.timezone","Asia/Manila");
class rentableShelftagPrintingObj extends commonObj {
	
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
	
	function specsDetail($dispSpecs,$strCode){
		
		if($dispSpecs == 0){
			$filterDispSpecs = "";
		}else{
			$filterDispSpecs = "AND tblDispDaDtlStr.displaySpecsId = '{$dispSpecs}'";
		}
		
		$sql = "select tblDispDaDtlStr.strCode, tblDispDaDtlStr.stsRefno, 
		H.stsRefno, H.suppCode, sql_mmpgtlib.dbo.APSUPP.ASNAME AS supplier, 
		tblDispDaDtlStr.startDate, tblDispDaDtlStr.endDate, tblDisplaySpecsDtl.dispDesc
		from tblDispDaDtlStr
		left join
		(SELECT     cast(stsRefno as nvarchar) as stsRefno, suppCode, stsType,
		contractTag, stsDept, stsCls, stsSubCls, stsAmt, stsRemarks, stsPaymentMode, 
		stsTerms, nbrApplication, applyDate, enteredBy, dateEntered, grpCode, contactPerson,
		contactPersonPos, approvedBy, dateApproved, contractNo, stsStartNo, stsEndNo, stsPrintedBy, 
		stsDatePrinted, stsDateReprinted, stsReprintedBy, applyTagDate, stsApplyTag, stsStat, 
		cancelDate, cancelledBy, cancelId, endDate, origStr, vatTag, eventNo, autoTag,
		eventType, pcaTag
		FROM         tblFocHdr
		UNION
		SELECT     cast(stsRefno as nvarchar) as stsRefno, suppCode, stsType,
		contractTag, stsDept, stsCls, stsSubCls, stsAmt, stsRemarks, stsPaymentMode, 
		stsTerms, nbrApplication, applyDate, enteredBy, dateEntered, grpCode, contactPerson,
		contactPersonPos, approvedBy, dateApproved, contractNo, stsStartNo, stsEndNo, stsPrintedBy, 
		stsDatePrinted, stsDateReprinted, stsReprintedBy, applyTagDate, stsApplyTag, stsStat, 
		cancelDate, cancelledBy, cancelId, endDate, origStr, vatTag, eventNo, autoTag,
		eventType, pcaTag
		FROM         tblStsHdr) as H
		on tblDispDaDtlStr.stsRefno = H.stsRefno
		left join sql_mmpgtlib.dbo.APSUPP 
		ON H.suppCode = sql_mmpgtlib.dbo.APSUPP.ASNUM
		left join tblDisplaySpecsDtl
		on tblDispDaDtlStr.dispDtlId = tblDisplaySpecsDtl.dispDtlId
		where tblDispDaDtlStr.stsRefno is not null
		and tblDispDaDtlStr.strCode  = '{$strCode}'
		and tblDispDaDtlStr.availabilityTag = 'N'
		and H.stsStat = 'R'
		$filterDispSpecs
		";
		return $this->getArrRes($this->execQry($sql));
	}
}
?>
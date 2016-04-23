<?
	//created by: vincent c de torres
	function CostRelease($cstEventNo = "", $compCode, $uSer = ""){
			$qryGetHdr = "SELECT * FROM tblCostEventHeader WHERE cstEventNumber = '{$cstEventNo}' AND compCode = '{$compCode}' ";		
			$resGetHdr = mssql_query($qryGetHdr);
			$rowGetHdr = mssql_fetch_array($resGetHdr);
			
			$qryGetDtl = "SELECT * FROM tblCostEventDtl WHERE cstEventNo = '{$cstEventNo}' AND compCode = '{$compCode}'";
			$resGetDtl = mssql_query($qryGetDtl);
			
			while($rowGetDtl = mssql_fetch_array($resGetDtl)){
		
			$tmpstrtdt = date("n/d/Y", strtotime($rowGetHdr[7]));
			
			if(empty($rowGetHdr[8])){
				$tmpEnddt = "12/31/9999";
			}else{
				$tmpEnddt = date("n/d/Y", strtotime($rowGetHdr[8]));
			}
		
			$qryToCstPln = "INSERT INTO tblCostPlan(compCode,SuppCode,PrdNumber,cstStartDate,cstPrecedence,suppCurr,umCode,prdConv,cstEndDate,cstEventNo,cstTypeCode,cstNewCost,cstCostTag)
											VALUES('{$rowGetHdr[0]}',
												   '{$rowGetHdr[2]}',
												   '{$rowGetDtl[2]}',
												   '{$tmpstrtdt}',
												   '{$rowGetHdr[6]}',
												   '{$rowGetHdr[5]}',
												   '{$rowGetDtl[3]}',
												   '{$rowGetDtl[4]}',
												   '{$tmpEnddt}',
												   '{$rowGetHdr[1]}',
												   '{$rowGetHdr[4]}',
												   '{$rowGetDtl[6]}',
												   '')";	
				$resToCstPln = mssql_query($qryToCstPln);	
			}
			
			$now = date("n/d/Y");
		    $qryOtoR = "UPDATE tblCostEventHeader SET cstEventStatus = 'R',cstEventRlsdDte = '{$now}',cstEventRlsdBy = '{$uSer}'  WHERE cstEventNumber = '{$cstEventNo}' AND compCode = '{$compCode}'";
			$resOtoR = mssql_query($qryOtoR);
	}
?>
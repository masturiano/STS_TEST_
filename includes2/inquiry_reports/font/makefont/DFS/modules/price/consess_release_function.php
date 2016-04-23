<?
//created by: vincent c de torres

function ConsessRelease($prEventNo = "", $compCode, $uSer = ""){
	$qryGetHdr = "SELECT * FROM tblConsessHdr WHERE prEventNumber = '{$prEventNo}' AND compCode = '{$compCode}' ";		
	$resGetHdr = mssql_query($qryGetHdr);
	$rowGetHdr = mssql_fetch_array($resGetHdr);
	
	$qryGetDtl = "SELECT * FROM tblConsessDtl WHERE prEventNumber = '{$prEventNo}' AND compCode = '{$compCode}'";
	$resGetDtl = mssql_query($qryGetDtl);
	
	while($rowGetDtl = mssql_fetch_array($resGetDtl)){
		
			$tmpstrtdt = date("n/d/Y", strtotime($rowGetHdr[5]));
			if(empty($rowGetHdr[6])){
				 $tmpEnddt = "12/31/9999";
			}else{
				$tmpEnddt = date("n/d/Y", strtotime($rowGetHdr[6]));
			}
		
	 		$qryToPricePlan = "INSERT INTO tblConsessPlan(compCode,prdNumber,prStartDate,prPrecedence,prEndDate,umCode,prEventNo,prTypeCode,prNewPrice)
								VALUES('{$rowGetDtl[0]}',
									   '{$rowGetDtl[2]}',
									   '{$tmpstrtdt}',
									   '{$rowGetHdr[4]}',
									   '{$tmpEnddt}',
									   '{$rowGetDtl[3]}',
									   '{$rowGetDtl[1]}',
									   '{$rowGetHdr[3]}',
									   '{$rowGetDtl[5]}')";
			$resToPricePlan = mssql_query($qryToPricePlan);
	}

		$now = date("n/d/Y");
	    $qryOtoR = "UPDATE tblConsessHdr SET prEventStatus = 'R', prEventRlsdDte = '{$now}',prEventRlsdBy = '{$uSer}'  WHERE prEventNumber = '{$prEventNo}' AND compCode = '{$compCode}'";
		$resOtoR = mssql_query($qryOtoR);
}

?>
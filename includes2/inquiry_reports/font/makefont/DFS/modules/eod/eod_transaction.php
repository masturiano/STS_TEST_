<?php
////////////////////////////////////////////////////////////////////////////////////-----
$do=$_GET['do']; $while=$_GET['while'];
////////////////////////////////////////////////////////////////////////////////////-----
if($do=='processEOD'&&$while=="")
	{ processEOD(); }
	
////////////////////////////////////////////////////////////////////////////////////-----
function processEOD()///////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$opsName=$_GET['opsName']; $dateToProcess=$_GET['dateToProcess']; $dateNextToProcess=$_GET['dateNextToProcess'];
	
	$dateToProcessS = $dateToProcess." 00:00:00 AM";
	$dateToProcessE	= $dateToProcess." 11:59:59 PM";
	
	$dateNextToProcessS = $dateNextToProcess." 00:00:00 AM";
	$dateNextToProcessE = $dateNextToProcess." 11:59:59 PM";
	
	//CONNECT CONNECT
	$compcode = '1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	
	//check EOD first if existing
	$checkEODAudit = mssql_query("SELECT * 
	FROM tblEodAudit
	WHERE compCode='$compcode' 
	AND(procDate BETWEEN '$dateToProcessS' AND '$dateToProcessE')
	AND eodDelTag!='D'");
	
	if(mssql_num_rows($checkEODAudit)==1)
		{	echo "Sorry, you have already processed the end of day";
			mssql_close();
			
			echo "<script type='text/javascript'>
			window.location = \"eod_main.php?do=processEOD&while=error&opsName=$opsName&dateToProcess=$dateToProcess&dateNextToProcess=$dateNextToProcess\";
			</script>";	}
	else
		{	/////END OF DAY FOR PRICE
			//update tblProdPrice--
			$udpateProdPrice = mssql_query("UPDATE tblProdPrice
			SET promoPriceEvent='0', prTypeCode='0', promoUnitprice='0', promoPriceStart='1/1/1900', promoPriceEnd='1/1/1900'
			WHERE compCode='$compcode'
			AND ((promoUnitprice!=NULL OR promoUnitprice!='0')
			OR (promoPriceStart!=NULL AND promoPriceEnd!=NULL)
			OR (promoPriceEnd>'$dateNextToProcessE'))");
			
			//update tblPricePlan--
			$udpatePricePlan = mssql_query("UPDATE tblPricePlan
			SET prEventStatus='C'
			WHERE compCode='$compcode'
			AND (prEventStatus!='C' 
			OR prTypeCode!='1'
			OR prEndDate>'$dateNextToProcessE')");
			
			///////////////////////////////////////////
			//EXTRACT REGULAR PRICE from tblPricePlan--
			$checkPricePlan = mssql_query("SELECT *
			FROM tblPricePlan
			WHERE compcode='$compcode'
			AND prTypeCode='1'
			AND prEndDayTag!='Y'
			AND prEventStatus!='C'
			AND prStartDate<'$dateNextToProcessE'");

			if(mssql_num_rows($checkPricePlan)==0)
				{	
				echo "<script type='text/javascript'>
			window.location = \"eod_main.php?do=processEOD&while=noProcess&opsName=$opsName&dateToProcess=$dateToProcess&dateNextToProcess=$dateNextToProcess\";
			</script>";
				}
			else
				{
				while($checkPricePlanRow = mssql_fetch_assoc($checkPricePlan))
					{	$prdNumber = $checkPricePlanRow['prdNumber'];
						$umCode = $checkPricePlanRow['umCode'];
						$prEventNo = $checkPricePlanRow['prEventNo'];
						$prStartDate = $checkPricePlanRow['prStartDate'];
						$prPlanPrice = $checkPricePlanRow['prPlanPrice'];	
						$prPrecedence = $checkPricePlanRow['prPrecedence'];	
	
						//check prd--
						$checkPrice = mssql_query("SELECT prdNumber
						FROM tblProdPrice
						WHERE compCode='$compcode'
						AND	prdNumber='$prdNumber'");
						
						if($checkPrice=='0')
							{	//insert prd--
								$insertNewPrdPrice = mssql_query("INSERT
								INTO tblProdPrice
								VALUES ('$compcode','$prdNumber','$umCode','0','0','0',
										'0','0','1/1/1900','1/1/1900','0',
										NULL, NULL)");	}
												
						//convert date--
						$findDate = mssql_query("SELECT prStartDate
						,CONVERT(varchar(10), prStartDate, 110) AS convertedDate
						FROM tblPricePlan 
						WHERE compCode='$compcode'
						AND prdNumber='$prdNumber'
						AND prEventNo='$prEventNo'");
						while($findDateRow = mssql_fetch_array($findDate))
							{	$convertedDate = trim($findDateRow['convertedDate']);	}
							
						//update tblProdPrice--
						$udpateProdPrice = mssql_query("UPDATE tblProdPrice
						SET regPriceEvent='$prEventNo', regPriceStart='$convertedDate', 
							regUnitPrice='$prPlanPrice', umCode='$umCode'
						WHERE compCode='$compcode'
						AND prdNumber='$prdNumber'");
						
						//update tblPricePlan
						$udpatePricePlan = mssql_query("UPDATE tblPricePlan
						SET prEndDayTag='Y', prPriceTag='1'
						WHERE compCode='$compcode'
						AND prdNumber='$prdNumber'
						AND prPrecedence='$prPrecedence'
						AND prStartDate='$prStartDate'
						AND prEventNo='$prEventNo'");
						
					}
				}
			
			///////////////////////////////////////////
			//EXTRACT PROMO PRICE from tblPricePlan--
			$checkPROMOPricePlan = mssql_query("SELECT *
			FROM tblPricePlan
			WHERE compcode='$compcode'
			AND prTypeCode!='1'
			AND prEndDayTag!='Y'
			AND prEventStatus!='C'
			AND prStartDate<'$dateNextToProcessE'
			ORDER BY prEventNo DESC");

			while($checkPROMOPricePlanRow = mssql_fetch_assoc($checkPROMOPricePlan))
				{	$prdNumberP = $checkPROMOPricePlanRow['prdNumber'];
					$umCodeP = $checkPROMOPricePlanRow['umCode'];
					$prEventNoP = $checkPROMOPricePlanRow['prEventNo'];
					$prStartDateP = $checkPROMOPricePlanRow['prStartDate'];
					$prEndDateP = $checkPROMOPricePlanRow['prEndDate'];
					$prPlanPriceP = $checkPROMOPricePlanRow['prPlanPrice'];	
					$prPrecedenceP = $checkPROMOPricePlanRow['prPrecedence'];
					
					//UPDATE PRICE PLAN SET TAG to 0
					$updatePricePlan = mssql_query("UPDATE
					SET prPriceTag='0'
					WHERE compcode='$compcode'
					AND prdNumber='$prdNumberP'
					AND prEventNo='$prEventNoP'");
					
					//check prd--
					$checkPriceP = mssql_query("SELECT prdNumber
					FROM tblProdPrice
					WHERE compCode='$compcode'
					AND	prdNumber='$prdNumberP'");
					
					if($checkPriceP!='0')
						{							
						//update tblProdPrice--
						$udpateProdPrice = mssql_query("UPDATE tblProdPrice
						SET promoUnitprice='$prPlanPriceP', promoPriceStart='$prStartDateP', 
							promoPriceEnd='$prEndDateP', promoPriceEvent='$prEventNoP'
						WHERE compCode='$compcode'
						AND prdNumber='$prdNumberP'");	
						
						//UPDATE PRICE PLAN SET TAG-0 and EndDaytag-Y
						$updatePricePlan = mssql_query("UPDATE
						SET prPriceTag='1', prEndDayTag='Y'
						WHERE compcode='$compcode'
						AND prdNumber='$prdNumberP'
						AND prEventNo='$prEventNoP'");	}
					else
						{
						if($prEndDateP<)
							{
							}
						//UPDATE PRICE PLAN SET TAG-0 and EndDaytag-Y
						$updatePricePlan = mssql_query("UPDATE
						SET prPriceTag='1', prEndDayTag='Y'
						WHERE compcode='$compcode'
						AND prdNumber='$prdNumberP'
						AND prEventNo='$prEventNoP'");	}
				}
				
			//insert End of Day Record to tblEodAudit
			$insertEODAudit = mssql_query("INSERT 
			INTO tblEodAudit
			VALUES('$compcode','$dateToProcess','$dateNextToProcess','$opsName', '')"); 
		
			mssql_close();
			
			/* echo "<script type='text/javascript'>
			window.location = \"eod_main.php\";
			</script>"; */	}

	}
////////////////////////////////////////////////////////////////////////////////////-----


////////////////////////////////////////////////////////////////////////////////////
?>
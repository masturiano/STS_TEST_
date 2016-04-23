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
		{	//check tblPricePlan
			$checkPricePlan = mssql_query("SELECT *
			FROM tblPricePlan
			WHERE compcode='$compcode'
			AND prTypeCode='1'
			AND prEndDayTag!='Y'
			AND prEventStatus!='C'
			AND (prStartDate>='$dateToProcessS' AND prStartDate<'$dateNextToProcessE')");
			
			echo "SELECT *
			FROM tblPricePlan
			WHERE compcode='$compcode'
			AND prTypeCode='1'
			AND prEndDayTag!='Y'
			AND prEventStatus!='C'
			AND (prStartDate>='$dateToProcessS' AND prStartDate<'$dateNextToProcessE')";
			
			if(mssql_num_rows($checkPricePlan)!=0)
				{	
				echo "marami";
				}
			else
				{	echo "<script type='text/javascript'>
			window.location = \"eod_main.php?do=processEOD&while=noProcess&opsName=$opsName&dateToProcess=$dateToProcess&dateNextToProcess=$dateNextToProcess\";
			</script>";	}	
			
			//insert End of Day Record to tblEodAudit
			/* $insertEODAudit = mssql_query("INSERT 
			INTO tblEodAudit
			VALUES('$compcode','$dateToProcess','$dateNextToProcess','$opsName', '')");  */
			
			mssql_close();
			
			/* echo "<script type='text/javascript'>
			window.location = \"eod_main.php\";
			</script>"; */	}

	}
////////////////////////////////////////////////////////////////////////////////////-----


////////////////////////////////////////////////////////////////////////////////////
?>
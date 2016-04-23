<?php
////////////////////////////////////////////////////////////////////////////////////
$do=$_GET['do']; $while=$_GET['while'];
////////////////////////////////////////////////////////////////////////////////////
if($do=='newRecord'&&$while=="")
	{ newRecord(); }
else if($do=='newRecord'&&$while=='findCustomer')
	{ findCustomer(); }
else if($do=='newRecord'&&$while=='findLocation')
	{ findLocation(); }
else if($do=='newRecord'&&$while=='findIte')
	{ findIte(); }
	
else if($do=='newRecord'&&$while=='newInvoice')
	{ newInvoice(); }
else if($do=='newRecord'&&$while=='saveRecordNow')
	{ saveRecordNow(); }
else if($do=='newRecord'&&$while=='saveNewRecordNow')
	{ saveNewRecordNow(); }
else if($do=='newRecord'&&$while=='deleteRecordNow')
	{ deleteRecordNow(); }
else if($do=='newRecord'&&$while=='deleteItemNow')
	{ deleteItemNow(); }

else if($do=="release"&&$while=="displayItems")
	{ displayItems();	}
else if($do=="release"&&$while=="saveReleaseNow")
	{ saveReleaseNow();	}

////////////////////////////////////////////////////////////////////////////////////
function newRecord()////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do'];
	$compcode='1';	
	$compCode = '1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$lastCiNumber = mssql_fetch_assoc(mssql_query("SELECT lastInvNo FROM tblInvNumber WHERE CompCode='$compCode'"));

	foreach($lastCiNumber as $lastCiNumberNew)
	$lastCiNumberNew = $lastCiNumberNew + 1;
	$updateCi = mssql_query("UPDATE tblInvNumber SET lastInvNo='$lastCiNumberNew' WHERE CompCode='$compCode'");
	mssql_close();
	echo "<script type='text/javascript'>
		window.location = \"commercial_invoice.php?do=newRecord\";
		</script>";
	}
////////////////////////////////////////////////////////////////////////////////////
function findCustomer()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$compcode='1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$findCust = mssql_query("SELECT custCode
	FROM tblCustMast
	WHERE compCode='$compcode'
	AND custCode='$custCode'
	AND custStat!='D'");
	if(mssql_num_rows($findCust)==0)
		{	echo "<script type='text/javascript'>
			alert(\"Customer Code doesn't Exist.\");
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."\";
			</script>"; }
	else
		{	echo "<script type='text/javascript'>
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."\";
			</script>"; }
	}
////////////////////////////////////////////////////////////////////////////////////
function findLocation()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$compcode='1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$findLocation = mssql_query("SELECT locCode
	FROM tblLocation
	WHERE compCode='$compcode'
	AND locCode='$locationCode'
	AND locStat!='D'");
	if(mssql_num_rows($findLocation)==0)
		{	echo "<script type='text/javascript'>
			alert(\"Location Code doesn't Exist.\");
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."\";
			</script>"; }
	else
		{	echo "<script type='text/javascript'>
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."\";
			</script>"; }
	}
////////////////////////////////////////////////////////////////////////////////////
function findIte()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];
	$compcode='1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$findIte = mssql_query("SELECT prdNumber,prdDelTag
	FROM tblProdMast 
	WHERE prdNumber='$prodIte'");
	while($findIteRow=mssql_fetch_assoc($findIte))
		{	$prdDelTag = $findIteRow['prdDelTag'];}		
	if(mssql_num_rows($findIte)==0)
		{	echo "<script type='text/javascript'>
			alert(\"Product Item doesn't Exist.\");
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>"; }			
	else if(mssql_num_rows($findIte)!=0 && $prdDelTag=='D')
		{	echo "<script type='text/javascript'>
			alert(\"Product Item is deleted. Please Enter another Item Code.\");
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>"; }
	else
		{	echo "<script type='text/javascript'>
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=".$prodIte."\";
			</script>"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function newInvoice()///////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];
	$compcode='1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$findIte = mssql_query("SELECT prdNumber,prdDelTag
	FROM tblProdMast 
	WHERE prdNumber='$prodIte'");
	while($findIteRow=mssql_fetch_assoc($findIte))
		{	$prdDelTag = $findIteRow['prdDelTag'];}		
	if(mssql_num_rows($findIte)==0)
		{	echo "<script type='text/javascript'>
			alert(\"Product Item doesn't Exist.\");
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>"; }			
	else if(mssql_num_rows($findIte)!=0 && $prdDelTag=='D')
		{	echo "<script type='text/javascript'>
			alert(\"Product Item is deleted. Please Enter another Item Code.\");
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>"; }
	else
		{	echo "<script type='text/javascript'>
			window.location = \"commercial_invoice.php?do=newRecord&ciNumber=".$ciNumber."&while=".$while."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=".$prodIte."\";
			</script>"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function saveRecordNow()////////////////////////////////////////////////////////////
	{
	date_default_timezone_set('Asia/Singapore');
	$dateNow = date("n-j-Y");
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];$prodDisc=$_GET['prodDisc'];$prodUniPri=$_GET['prodUniPri'];
	$prodQty1=$_GET['prodQty1'];$prodQty2=$_GET['prodQty2'];$prodQty3=$_GET['prodQty3'];$prodQty4=$_GET['prodQty4'];
	if($prodQty1=="")
		{ $prodQty1=0; }
	if($prodQty2=="")
		{ $prodQty2=0; }
	if($prodQty3=="")
		{ $prodQty3=0; }
	if($prodQty4=="")
		{ $prodQty4=0; }
	if($prodDisc=="")
		{ $prodDisc=0; }
	if($pricingSpecial=="")
		{ $pricingSpecial=0; }
	$compcode='1';
	$username='1234';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	
	$findCustomerName = mssql_query("SELECT custTerms
		FROM tblCustMast 
		WHERE compcode='$compcode' 
		AND custCode='$custCode'");
		while($findCustomerNameRow=mssql_fetch_assoc($findCustomerName))
			{	$custTerms = strtoupper($findCustomerNameRow['custTerms']); }
		
	$insertCIHeader = mssql_query("INSERT 
		INTO tblCiHeader 
		VALUES('$compcode','$ciNumber','$strfNumber','$ciDate','$strfDate','$custCode','$custTerms',
			'$locationCode ','$pricingToUse','$pricingSpecial','$remarks','0','0','0',
			'0','0','$username', NULL,'O')");
		
	$findConv = mssql_query("SELECT prdConv, prdBuyUnit
	FROM tblProdMast 
	WHERE prdNumber='$prodIte'");
	while($findConvRow=mssql_fetch_assoc($findConv))
		{	$prdConv = strtoupper($findConvRow['prdConv']);
			$prdBuyUnit = strtoupper($findConvRow['prdBuyUnit']); }
		
		$insertCI = mssql_query("INSERT 
		INTO tblCiItemDtl 
		VALUES('$compcode','$ciNumber','$prodIte','$prdBuyUnit','$prdConv','$prodQty1',
			'$prodQty2','$prodQty3','$prodQty4','$prodUniPri','$prodDisc','0','0','O')");
		echo "<script type='text/javascript'>
			window.location = \"commercial_invoice.php?do=newRecord&while=newInvoice&ciNumber=".$ciNumber."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function saveNewRecordNow()/////////////////////////////////////////////////////////
	{
	date_default_timezone_set('Asia/Singapore');
	$dateNow = date("n-j-Y");
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];$prodDisc=$_GET['prodDisc'];$prodUniPri=$_GET['prodUniPri'];
	$prodQty1=$_GET['prodQty1'];$prodQty2=$_GET['prodQty2'];$prodQty3=$_GET['prodQty3'];$prodQty4=$_GET['prodQty4'];
	if($prodQty1=="")
		{ $prodQty1=0; }
	if($prodQty2=="")
		{ $prodQty2=0; }
	if($prodQty3=="")
		{ $prodQty3=0; }
	if($prodQty4=="")
		{ $prodQty4=0; }
	if($prodDisc=="")
		{ $prodDisc=0; }
	if($pricingSpecial=="")
		{ $pricingSpecial=0; }
	$compcode='1';
	$username='1234';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	
	$findCustomerName = mssql_query("SELECT custTerms
		FROM tblCustMast 
		WHERE compcode='$compcode' 
		AND custCode='$custCode'");
		while($findCustomerNameRow=mssql_fetch_assoc($findCustomerName))
			{	$custTerms = strtoupper($findCustomerNameRow['custTerms']); }
		
	$findConv = mssql_query("SELECT prdConv, prdBuyUnit
	FROM tblProdMast 
	WHERE prdNumber='$prodIte'");
	while($findConvRow=mssql_fetch_assoc($findConv))
		{	$prdConv = strtoupper($findConvRow['prdConv']);
			$prdBuyUnit = strtoupper($findConvRow['prdBuyUnit']); }
		
		$insertCI = mssql_query("INSERT 
		INTO tblCiItemDtl 
		VALUES('$compcode','$ciNumber','$prodIte','$prdBuyUnit','$prdConv','$prodQty1',
			'$prodQty2','$prodQty3','$prodQty4','$prodUniPri','$prodDisc','0','0','O')");
		echo "<script type='text/javascript'>
			window.location = \"commercial_invoice.php?do=newRecord&while=newInvoice&ciNumber=".$ciNumber."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>";
	mssql_close();
	}
////////////////////////////////////////////////////////////////////////////////////-----
function deleteRecordNow()//////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];$prodDisc=$_GET['prodDisc'];$prodUniPri=$_GET['prodUniPri'];
	$prodQty1=$_GET['prodQty1'];$prodQty2=$_GET['prodQty2'];$prodQty3=$_GET['prodQty3'];$prodQty4=$_GET['prodQty4'];
	$compcode='1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	$deleteCIHeader = mssql_query("DELETE 
		FROM tblCiHeader 
		WHERE compCode='$compcode' 
		AND ciNumber='$ciNumber'");

	$deleteCIItem = mssql_query("DELETE 
		FROM tblCiItemDtl 
		WHERE compCode='$compcode' 
		AND ciNumber='$ciNumber'");
		
	echo "<script type='text/javascript'>
		alert(\"You have successfully deleted a Commercial Invoice Record\");
		window.location = \"commercial_invoice.php\";
		</script>";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function deleteItemNow()////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	$ciNumber=$_GET['ciNumber']; $ciStatus=$_GET['ciStatus']; $ciDate=$_GET['ciDate']; $strfNumber=$_GET['strfNumber'];
	$strfDate=$_GET['strfDate']; $custCode=$_GET['custCode']; $locationCode = $_GET['locationCode'];
	$pricingToUse=$_GET['pricingToUse']; $pricingSpecial=$_GET['pricingSpecial']; $remarks=$_GET['remarks'];
	$prodIte=$_GET['prodIte'];$prodDisc=$_GET['prodDisc'];$prodUniPri=$_GET['prodUniPri'];
	$prodQty1=$_GET['prodQty1'];$prodQty2=$_GET['prodQty2'];$prodQty3=$_GET['prodQty3'];$prodQty4=$_GET['prodQty4'];
	$compcode='1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
	
	$deleteCIItem = mssql_query("DELETE 
		FROM tblCiItemDtl 
		WHERE compCode='$compcode' 
		AND prdNumber='$prodIte'");
			
	echo "<script type='text/javascript'>
			alert(\"You have successfully deleted an Item Product Record\");
			window.location = \"commercial_invoice.php?do=newRecord&while=newInvoice&ciNumber=".$ciNumber."&ciStatus=".$ciStatus."&ciDate=".$ciDate."&strfNumber=".$strfNumber."&strfDate=".$strfDate."&custCode=".$custCode."&locationCode=".$locationCode."&pricingToUse=".$pricingToUse."&pricingSpecial=".$pricingSpecial."&remarks=".$remarks."&prodIte=\";
			</script>";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function displayItems()/////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while']; $ciNo = $_GET['ciNo'];

	$compcode = '1';
	$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
	$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		
	$findCiHeader = mssql_query("SELECT * 
	FROM tblCiHeader
	WHERE ciStat='O' 
	AND compCode='$compcode'
	AND ciNumber='$ciNo'");
	while($findCiHeaderRow = mssql_fetch_assoc($findCiHeader))
		{
		$ciLocation = $findCiHeaderRow['ciLocation'];
		$strfNumber = $findCiHeaderRow['strfNumber'];
			}
			
	$findDate = mssql_query("SELECT ciDate
	,CONVERT(varchar(10), ciDate, 110) AS convertedDate
	FROM tblCiHeader 
	WHERE compCode='$compcode'
	AND ciNumber='$ciNo'
	AND ciStat='O'");
	while($findDateRow = mssql_fetch_array($findDate))
		{	$convertedDate = trim($findDateRow['convertedDate']);	}
	echo "<script type='text/javascript'>
			window.location = \"ci_release.php?do=release&while=displayItems&ciNo=".$ciNo."&location=".$ciLocation."&ciDate=".$convertedDate."&strfNumber=".$strfNumber."&adjReason=".$adjReason."&adjRemarks=".$adjRemarks."&hashIte=".$hashIte."&hashQty=".$hashQty."\";
			</script>"; 
	}
////////////////////////////////////////////////////////////////////////////////////-----
function saveReleaseNow()///////////////////////////////////////////////////////////
	{
	date_default_timezone_set('Asia/Singapore');
	$do=$_GET['do']; $while=$_GET['while']; $ciNo = $_GET['ciNo']; $ciDate = $_GET['ciDate'];
	$location = $_GET['location']; $thisMonth = $_GET['thisMonth']; $thisYear = $_GET['thisYear']; 
	$strfNumber= $_GET['strfNumber'];
	$dateNow = date("n-j-Y H:i:s");	$responsible = "1234";
	
	$prodNumber = $_POST['prodNumber']; 
	$prdConv = $_POST['prdConv']; 
	$qtyRegPk = $_POST['qtyRegPk']; 
	$qtyFreePk = $_POST['qtyFreePk']; 
	$qtyRegPc = $_POST['qtyRegPc']; 
	$qtyFreePc = $_POST['qtyFreePc']; 
	$ciDiscPcent = $_POST['ciDiscPcent']; 
	$ciUnitPrice = $_POST['ciUnitPrice']; 
	
	echo "do = $do <br> while = $while <br> CI = $ciNo ; Location = $location ; Month = $thisMonth ; Year = $thisYear ; ciDate = $ciDate";
	foreach($prodNumber as $key=>$prodvalue) 
		{
		$conserver = mssql_connect("192.168.200.170","sa","sa") or die("Cannot Connect to server");
		$selectdb = mssql_select_db("DFS") or die("Cannot select database");
		$compcode = '1'; $transCode = '051';
		
		//find product record in tblCiHeader
		$findCiHeader = mssql_query("SELECT * 
		FROM tblCiHeader
		WHERE ciStat='O' 
		AND compCode='$compcode'
		AND ciNumber='$ciNo'");
		$ciItemTotalN = 0;
		$qtyRegPkN = 0;
		while($findCiHeaderRow = mssql_fetch_assoc($findCiHeader))
			{
			$cItemTotal = $findCiHeaderRow['cItemTotal'];
			$ciQtyRegular = $findCiHeaderRow['ciQtyRegular'];
			$ciTotQtyFree = $findCiHeaderRow['ciTotQtyFree'];
			$ciTerms = $findCiHeaderRow['ciTerms'];	
			$ciPriceCd = $findCiHeaderRow['ciPriceCd'];	
			$custCode = $findCiHeaderRow['custCode'];
			$ciTotExtAmt = $findCiHeaderRow['ciTotExtAmt'];	
			$ciTotDiscAmt = $findCiHeaderRow['ciTotDiscAmt'];	}
			
			//find product record in tblCiItemDtl
			$findInvoices = mssql_query("SELECT *
			FROM tblCiItemDtl 
			WHERE compcode='$compcode' 
			AND ciNumber='$ciNo'
			AND ciStatus='O'");
		
			while($findInvoicesRow=mssql_fetch_assoc($findInvoices))
				{	$prdNumber = $findInvoicesRow['prdNumber'];	
					//$ciDiscPcent = $findInvoicesRow['ciDiscPcent'];
					}
					
				$ciItemTotalN = $ciItemTotalN + 1;
				$ciItemTotalNN = $ciItemTotalN + $cItemTotal;
				
				$totRegQty = ($qtyRegPk[$key] * $prdConv[$key]) + $qtyRegPc[$key];
				$totRegQtyNew = $ciQtyRegular + $totRegQty;
				$totFreeQty = ($qtyFreePk[$key] * $prdConv[$key]) + $qtyFreePc[$key];
				$ciTotQtyFreeNew = $totFreeQty + $ciTotQtyFree;
				$totQty = $totRegQty + $totFreeQty; 
				$ciExtAmt = $totRegQty * $ciUnitPrice[$key];
				$ciTotExtAmtNew = $ciExtAmt + $ciTotExtAmt;
				$ciDiscAmt = $ciExtAmt * ($ciDiscPcent[$key]/100);
				$ciTotDiscAmtNew = $ciTotDiscAmt + $ciDiscAmt;
				
				echo "<hr color='blue'>Product Number: ".$prodvalue;
				echo "<br>Total Reg Qty: ".$totRegQty." = ($qtyRegPk[$key] * $prdConv[$key]) + $qtyRegPc[$key]";
				echo "<br>Total Free Qty: ".$totFreeQty." = ($qtyFreePk[$key] * $prdConv[$key]) + $qtyFreePc[$key]";
				echo "<br>Total Qty: ".$totQty." = ($totRegQty + $totFreeQty)";
				echo "<br>Ext Amt: ".$ciTotExtAmtNew." = ($totRegQty * $ciUnitPrice[$key])";
				echo "<br>Disc Amt: ".$ciTotDiscAmtNew." = $ciExtAmt * ($ciDiscPcent[$key]/100)";
				echo "<br>Unit Price: ".$ciUnitPrice[$key];
							
				//find product record in tblInvBalM
				$findInvBalM = mssql_query("SELECT *
				FROM tblInvBalM 
				WHERE compCode='$compcode'
				AND locCode='$location'
				AND pdYear='$thisYear'
				AND pdMonth='$thisMonth'
				AND prdNumber='$prodvalue'");
				
				if(mssql_num_rows($findInvBalM)==0)
					{	$mtdCiQ = 0;
						$mtdCiA = 0;
						$endBalGoodM = 0;	}
				else if(mssql_num_rows($findInvBalM)!=0)
					{	while($findInvBalMRow = mssql_fetch_assoc($findInvBalM))
						{	$mtdCiQ = $findInvBalMRow['mtdCiQ'];	
							$mtdCiA = $findInvBalMRow['mtdCiA'];
							$endBalGoodM = $findInvBalMRow['endBalGoodM'];	}
					}
						$mtdCiQN = $mtdCiQ + $totQty;
						$mtdCiAN = $mtdCiA + $ciExtAmt;
						$endBalGoodMN = $endBalGoodM - $totQty;
				
				echo "<br><br>mtdCiQN($mtdCiQN) = mtdCiQ($mtdCiQ) + totQty($totQty)";
				//find Product Class
				$findClass = mssql_query("SELECT prdGrpCode, prdDeptCode, prdClsCode, prdSubClsCode, prdType, prdSetTag
				FROM tblProdMast 
				WHERE prdNumber='$prodvalue'");
				while($findClassRow = mssql_fetch_array($findClass))
					{	$prdGrpCode = trim($findClassRow['prdGrpCode']);	
						$prdDeptCode = trim($findClassRow['prdDeptCode']);	
						$prdClsCode = trim($findClassRow['prdClsCode']);	
						$prdSubClsCode = trim($findClassRow['prdSubClsCode']);	
						$prdType = trim($findClassRow['prdType']);	
						$prdSetTag = trim($findClassRow['prdSetTag']);	}
						
				//find Ave Cost		
				$findIteCost = mssql_query("SELECT aveUnitCost
				FROM tblAveCost 
				WHERE prdNumber='$prodvalue'
				AND compCode='$compcode'");
				while($findIteCostRow=mssql_fetch_assoc($findIteCost))
					{	$aveUnitCost = $findIteCostRow['aveUnitCost'];	}
				
				//find location type
				$findLocType = mssql_query("SELECT locType
				FROM tblLocation 
				WHERE locCode='$location'
				AND compCode='$compcode'");
				while($findLocTypeRow = mssql_fetch_array($findLocType))
					{	$locType = trim($findLocTypeRow['locType']);	}
				//insert if no record found in tblInvBalM		
				if(mssql_num_rows($findInvBalM)==0)
					{	
					$insertInvBalM = mssql_query("INSERT 
					INTO tblInvBalM 
					VALUES('$compcode','$location','$prodvalue','$thisYear','$thisMonth',
					'0','0','0','0','0','0','0',
					'0','0','0','0','0','0','0',
					'0','0','0','0','0','0','0','0','0',
					'0','$responsible', CURRENT_TIMESTAMP)");
				
					$updateHeader = mssql_query("UPDATE tblCiHeader
					SET cItemTotal='$ciItemTotalNN', ciQtyRegular='$totRegQtyNew', ciTotQtyFree='$ciTotQtyFreeNew', 
						ciTotExtAmt='$ciTotExtAmtNew', ciTotDiscAmt='$ciTotDiscAmtNew'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'");
////////////////////				
					echo "<br><br>UPDATE tblCiHeader
					SET cItemTotal='$ciItemTotalNN', ciQtyRegular='$totRegQtyNew', ciTotQtyFree='$ciTotQtyFreeNew', 
						ciTotExtAmt='$ciTotExtAmtNew', ciTotDiscAmt='$ciTotDiscAmtNew'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'";
					
					$updateInvBalM = mssql_query("UPDATE tblInvBalM 
					SET mtdCiQ='$mtdCiQN', mtdCiA='$mtdCiAN', endBalGoodM='$endBalGoodMN', 
						updatedBy='$responsible', dateUpdated=CURRENT_TIMESTAMP
					WHERE compCode='$compcode'
					AND locCode='$location'
					AND prdNumber='$prodvalue'
					AND pdYear='$thisYear'
					AND pdMonth='$thisMonth'");
////////////////////
					echo "<br><br>UPDATE tblInvBalM 
					SET mtdCiQ='$mtdCiQN', mtdCiA='$mtdCiAN', endBalGoodM='$endBalGoodMN', 
						updatedBy='$responsible', dateUpdated=CURRENT_TIMESTAMP
					WHERE compCode='$compcode'
					AND locCode='$location'
					AND prdNumber='$prodvalue'
					AND pdYear='$thisYear'
					AND pdMonth='$thisMonth'";
					
					$insertInvTran= mssql_query("INSERT 
					INTO tblInvTran 
					VALUES('$compcode','$location','$locType','$prodvalue','$prdGrpCode',
					'$prdDeptCode','$prdClsCode','$prdSubClsCode','$prdSetTag','$prdType','$transCode','$ciNo',
					'$ciDate','$thisYear','$thisMonth','$strfNumber','0','$custCode','$totRegQty','0','$totFreeQty',
					'$aveUnitCost','0','0','0','$ciUnitPrice[$key]','$ciPriceCd','0',
					'0','$ciExtAmt','$ciDiscPcent[$key]','$ciDiscAmt','0','0','0',
					'0','0','$ciTerms',CURRENT_TIMESTAMP,'$responsible')");
////////////////////					
					echo "<br><br>INSERT 
					INTO tblInvTran 
					VALUES('$compcode','$location','$locType','$prodvalue','$prdGrpCode',
					'$prdDeptCode','$prdClsCode','$prdSubClsCode','$prdSetTag','$prdType','$transCode','$ciNo',
					'$ciDate','$thisYear','$thisMonth','$strfNumber','0','$custCode','$totRegQty','0','$totFreeQty',
					'$aveUnitCost','0','0','0','$ciUnitPrice[$key]','$ciPriceCd','0',
					'0','$ciExtAmt','$ciDiscPcent[$key]','$ciDiscAmt','0','0','0',
					'0','0','$ciTerms',CURRENT_TIMESTAMP,'$responsible')";
					
					$updateCiTemDtl = mssql_query("UPDATE tblCiItemDtl 
					SET ciExtAmt='$ciExtAmt', ciDiscAmt='$ciDiscAmt'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'
					AND prdNumber='$prodvalue'");
////////////////////
					echo "<br><br>UPDATE tblCiItemDtl 
					SET ciExtAmt='$ciExtAmt', ciDiscAmt='$ciDiscAmt'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'
					AND prdNumber='$prodvalue'";
					}	
				//update if no record found in tblInvBalM
				else if(mssql_num_rows($findInvBalM)!=0)
					{	
					$updateHeader = mssql_query("UPDATE tblCiHeader
					SET cItemTotal='$ciItemTotalNN', ciQtyRegular='$totRegQtyNew', ciTotQtyFree='$ciTotQtyFreeNew', 
						ciTotExtAmt='$ciTotExtAmtNew', ciTotDiscAmt='$ciTotDiscAmtNew'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'");
////////////////////					
					echo "<br><br>UPDATE tblCiHeader
					SET cItemTotal='$ciItemTotalNN', ciQtyRegular='$totRegQtyNew', ciTotQtyFree='$ciTotQtyFreeNew', 
						ciTotExtAmt='$ciTotExtAmtNew', ciTotDiscAmt='$ciTotDiscAmtNew'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'";
					
					$updateInvBalM = mssql_query("UPDATE tblInvBalM 
					SET mtdCiQ='$mtdCiQN', mtdCiA='$mtdCiAN', endBalGoodM='$endBalGoodMN',
						updatedBy='$responsible', dateUpdated=CURRENT_TIMESTAMP
					WHERE compCode='$compcode'
					AND locCode='$location'
					AND prdNumber='$prodvalue'
					AND pdYear='$thisYear'
					AND pdMonth='$thisMonth'");
////////////////////
					echo "<br><br>UPDATE tblInvBalM 
					SET mtdCiQ='$mtdCiQN', mtdCiA='$mtdCiAN', endBalGoodM='$endBalGoodMN', 
						updatedBy='$responsible', dateUpdated=CURRENT_TIMESTAMP
					WHERE compCode='$compcode'
					AND locCode='$location'
					AND prdNumber='$prodvalue'
					AND pdYear='$thisYear'
					AND pdMonth='$thisMonth'";
					
					$insertInvTran= mssql_query("INSERT 
					INTO tblInvTran 
					VALUES('$compcode','$location','$locType','$prodvalue','$prdGrpCode',
					'$prdDeptCode','$prdClsCode','$prdSubClsCode','$prdSetTag','$prdType','$transCode','$ciNo',
					'$ciDate','$thisYear','$thisMonth','$strfNumber','0','$custCode','$totRegQty','0','$totFreeQty',
					'$aveUnitCost','0','0','0','$ciUnitPrice[$key]','$ciPriceCd','0',
					'0','$ciExtAmt','$ciDiscPcent[$key]','$ciDiscAmt','0','0','0',
					'0','0','$ciTerms',CURRENT_TIMESTAMP,'$responsible')");
////////////////////
					echo "<br><br>INSERT 
					INTO tblInvTran 
					VALUES('$compcode','$location','$locType','$prodvalue','$prdGrpCode',
					'$prdDeptCode','$prdClsCode','$prdSubClsCode','$prdSetTag','$prdType','$transCode','$ciNo',
					'$ciDate','$thisYear','$thisMonth','$strfNumber','0','$custCode','$totRegQty','0','$totFreeQty',
					'$aveUnitCost','0','0','0','$ciUnitPrice[$key]','$ciPriceCd','0',
					'0','$ciExtAmt','$ciDiscPcent[$key]','$ciDiscAmt','0','0','0',
					'0','0','$ciTerms',CURRENT_TIMESTAMP,'$responsible')";
					
					
					$updateCiTemDtl = mssql_query("UPDATE tblCiItemDtl 
					SET ciExtAmt='$ciExtAmt', ciDiscAmt='$ciDiscAmt'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'
					AND prdNumber='$prodvalue'");
////////////////////
					echo "<br><br>UPDATE tblCiItemDtl 
					SET ciExtAmt='$ciExtAmt', ciDiscAmt='$ciDiscAmt'
					WHERE compCode='$compcode'
					AND ciNumber='$ciNo'
					AND prdNumber='$prodvalue'";
					}
			
		}
	echo "<hr color='red'>Total Item: ".$ciItemTotalNN;
	
	echo "<br>$etoo";
	//SET tblCiHeader status
	$updateHeader = mssql_query("UPDATE tblCiHeader
	SET ciStat='R', ciRlsdDte=CURRENT_TIMESTAMP
	WHERE compCode='$compcode'
	AND ciNumber='$ciNo'");
	echo "<script type='text/javascript'>
	window.location = \"ci_release.php\";
	</script>";
	}
	
////////////////////////////////////////////////////////////////////////////////////
?>
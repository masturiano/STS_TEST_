<?
//module name: daily sales upload from pos 
//programmer : vincent c de torres
//date created: 1/8/2009
	session_start();
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	
	$db = new DB;
	$db->connect();
	$action   = $_GET['action'];
	$compCode = $_SESSION['comp_code'];
	$user     = $_SESSION['userid'];
	$username = $_SESSION['username'];
	
	if(isset($_POST['txtSumarize'])){
		$slsDate = $_POST['cmbMonth']."/".$_POST['cmbDay']."/".$_POST['txtYearOfSales'];
		SummarizeDailySales($compCode,$_POST['cmbLoc'],$slsDate, $_POST['txtNetSales']);
	}
	
	function SummarizeDailySales($cmpCde,$locCde,$salesDt,$nNetSalesAmnt){
		
		$qryChkLoc = "SELECT * FROM tblLocation WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}'";
		$resChkLoc = mssql_query($qryChkLoc);
		$cntChkLoc = mssql_num_rows($resChkLoc);
		if($cntChkLoc == 0){
			showmess("<< Location is not valid for this company >>");
		}
		else{
			$qryChckDlySlsCntrl = "SELECT * FROM tblDlySalesControl WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate ='{$salesDt}'";
			$resChckDlySlsCntrl = mssql_query($qryChckDlySlsCntrl);
			$cntChckDlySlsCntrl = mssql_num_rows($resChckDlySlsCntrl);
			if($cntChckDlySlsCntrl > 0){
				showmess("<< Sales for this Date/Location already Posted...Job Aborted >>");
			}
			else{
				$qryChkVoidTrns = "SELECT * FROM tblPosVoidTrans WHERE compCode = '{$cmpCde}' AND storeNo = '{$locCde}' AND tranDate = '{$salesDt}'";
				$resChkVoidTrns = mssql_query($qryChkVoidTrns);
					if(mssql_num_rows($resChkVoidTrns) > 0){
					while($rowChkVoidTrns = mssql_fetch_assoc($resChkVoidTrns)){
						$qrygetVoidData = "UPDATE tblPosSalesTrans SET slsRecCode = 'V' WHERE compCode = '{$rowChkVoidTrns['compCode']}' AND storeNo = '{$rowChkVoidTrns['storeNo']}' AND termNo = '{$rowChkVoidTrns['termNo']}' AND slstranDate = '{$rowChkVoidTrns['tranDate']}' AND  tranNo = '{$rowChkVoidTrns['tranNo']}'";
						$resgetVoidData = mssql_query($qrygetVoidData);
					}
				}
				
				$qrygetsls = "SELECT * FROM tblPosSalesTrans WHERE compCode = '{$cmpCde}' AND storeNo = '{$locCde}' AND slsTranDate = '{$salesDt}' ";
				$resgetsls = mssql_query($qrygetsls);
				$cntgetsls = mssql_num_rows($resgetsls);
				if($cntgetsls > 0){
					//summarize returns
					while ($rowgetsls = mssql_fetch_assoc($resgetsls)) {
						 	$qrygetExtrctedDataRet = "SELECT DISTINCT(slsUpcNo), compCode,storeNo,  slsUnitPrice, slsTranDate FROM tblPosSalesTrans WHERE compCode = '{$rowgetsls['compCode']}' AND storeNo = '{$rowgetsls['storeNo']}' AND
													slsUpcNo = (SELECT DISTINCT(slsUpcNo) FROM tblPosSalesTrans WHERE  compCode = '{$rowgetsls['compCode']}' AND storeNo = '{$rowgetsls['storeNo']}' AND slsUpcNo = '{$rowgetsls['slsUpcNo']}' AND slsUnitPrice = '{$rowgetsls['slsUnitPrice']}' AND slsRecCode = 'R' AND  slsTranDate = '{$salesDt}') AND slsUnitPrice = '{$rowgetsls['slsUnitPrice']}' AND slsRecCode = 'R' AND  slsTranDate = '{$salesDt}'";		
							$resgetExtrctedDataRet = mssql_query($qrygetExtrctedDataRet);
							
						while($rowgetExtrctedDataRet = mssql_fetch_assoc($resgetExtrctedDataRet)){
						  	$qryMergeSameUpc_Price = "SELECT SUM(slsQty)*-1 as totSlsQty, SUM(slsExtAmt)*-1 as totSlsExtAmt, SUM(slsDiscAmt)*-1 as totSlsDisc FROM tblPosSalesTrans WHERE compCode = '{$rowgetExtrctedDataRet['compCode']}' AND storeNo = '{$rowgetExtrctedDataRet['storeNo']}' AND slsUpcNo = '{$rowgetExtrctedDataRet['slsUpcNo']}' AND slsUnitPrice = '{$rowgetExtrctedDataRet['slsUnitPrice']}' AND slsRecCode = 'R' AND  slsTranDate = '{$salesDt}'";
						 	$resMergeSameUpc_Price = mssql_query($qryMergeSameUpc_Price);
						 	$rowMergeSameUpc_Price = mssql_fetch_assoc($resMergeSameUpc_Price);
						 	
						 	$qrychkReturns = "SELECT * FROM wReturns WHERE compCode = '{$rowgetExtrctedDataRet['compCode']}' AND locCode = '{$rowgetExtrctedDataRet['storeNo']}' AND slsDate = '{$rowgetExtrctedDataRet['slsTranDate']}' AND unitPrice = '{$rowgetExtrctedDataRet['slsUnitPrice']}' AND slsUpcNo = '{$rowgetExtrctedDataRet['slsUpcNo']}'";	
						 	$chkReturns = mssql_query($qrychkReturns);
						 	if(mssql_num_rows($chkReturns) == 0){
						 		
							 		$qrytOWReturns = "INSERT INTO wReturns(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
						 								VALUES('{$rowgetExtrctedDataRet['compCode']}','{$rowgetExtrctedDataRet['storeNo']}','{$rowgetExtrctedDataRet['slsTranDate']}',
						 									   '{$rowgetExtrctedDataRet['slsUpcNo']}','{$rowgetExtrctedDataRet['slsUnitPrice']}','{$rowMergeSameUpc_Price['totSlsQty']}',
						 									    '{$rowMergeSameUpc_Price['totSlsExtAmt']}','{$rowMergeSameUpc_Price['totSlsDisc']}')";
									$restOWReturns = mssql_query($qrytOWReturns);
						 	}
						}//enf of summarize returns
						
						//summaraize sales
							$qrygetExtctedDataSls = "SELECT DISTINCT(slsUpcNo), compCode,storeNo,  slsUnitPrice, slsTranDate FROM tblPosSalesTrans WHERE compCode = '{$rowgetsls['compCode']}' AND storeNo = '{$rowgetsls['storeNo']}' AND
													slsUpcNo = (SELECT DISTINCT(slsUpcNo) FROM tblPosSalesTrans WHERE  compCode = '{$rowgetsls['compCode']}' AND storeNo = '{$rowgetsls['storeNo']}' AND slsUpcNo = '{$rowgetsls['slsUpcNo']}' AND slsUnitPrice = '{$rowgetsls['slsUnitPrice']}' AND slsRecCode = 'S' AND  slsTranDate = '{$salesDt}') AND slsUnitPrice = '{$rowgetsls['slsUnitPrice']}' AND slsRecCode = 'S' AND  slsTranDate = '{$salesDt}'";
							$resgetExtctedDataSls = mssql_query($qrygetExtctedDataSls);
							
						while($rowgetExtctedDataSls = mssql_fetch_assoc($resgetExtctedDataSls)){
						    $qryMergeSameUpc_Price_Sls = "SELECT SUM(slsQty) as totSlsQty, SUM(slsExtAmt) as totSlsExtAmt, SUM(slsDiscAmt) as totSlsDisc FROM tblPosSalesTrans WHERE compCode = '{$rowgetExtctedDataSls['compCode']}' AND storeNo = '{$rowgetExtctedDataSls['storeNo']}' AND slsUpcNo = '{$rowgetExtctedDataSls['slsUpcNo']}' AND slsUnitPrice = '{$rowgetExtctedDataSls['slsUnitPrice']}' AND slsRecCode = 'S' AND  slsTranDate = '{$salesDt}'";
						 	$resMergeSameUpc_Price_Sls = mssql_query($qryMergeSameUpc_Price_Sls);
						 	$rowMergeSameUpc_Price_Sls = mssql_fetch_assoc($resMergeSameUpc_Price_Sls);	
						 	
						 	$qrychkSls = "SELECT * FROM wSales WHERE compCode = '{$rowgetExtctedDataSls['compCode']}' AND locCode = '{$rowgetExtctedDataSls['storeNo']}' AND slsDate = '{$rowgetExtctedDataSls['slsTranDate']}' AND unitPrice = '{$rowgetExtctedDataSls['slsUnitPrice']}' AND slsUpcNo = '{$rowgetExtctedDataSls['slsUpcNo']}'";						
						 	$reschkSls = mssql_query($qrychkSls);
						 	if(mssql_num_rows($reschkSls) == 0){

							 		$qrytOWSales = "INSERT INTO wSales(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
						 								VALUES('{$rowgetExtctedDataSls['compCode']}','{$rowgetExtctedDataSls['storeNo']}','{$rowgetExtctedDataSls['slsTranDate']}',
						 									   '{$rowgetExtctedDataSls['slsUpcNo']}','{$rowgetExtctedDataSls['slsUnitPrice']}','{$rowMergeSameUpc_Price_Sls['totSlsQty']}',
						 									    '{$rowMergeSameUpc_Price_Sls['totSlsExtAmt']}','{$rowMergeSameUpc_Price_Sls['totSlsDisc']}')";
									$restOWSales = mssql_query($qrytOWSales);						 		
						 	}
						}
					}
					
					$totExtAmt  = 0.00;
					$totDiscAmt = 0.00;
					
					$qrygetdattowRet = "SELECT * FROM wReturns WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate = '{$salesDt}'";
					$resgetdattowRet = mssql_query($qrygetdattowRet);
					while ($rowgetdattowRet = mssql_fetch_assoc($resgetdattowRet)) {
							$qryCmprRetToSls = "SELECT * FROM wSales WHERE compCode = '{$rowgetdattowRet['compCode']}' AND locCode = '{$rowgetdattowRet['locCode']}' AND slsDate = '{$rowgetdattowRet['slsDate']}' 
												AND  slsUpcNo = '{$rowgetdattowRet['slsUpcNo']}' AND unitPrice = '{$rowgetdattowRet['unitPrice']}'";
							$resCmprRetToSls = mssql_query($qryCmprRetToSls);
							$rowCmprRetToSls = mssql_fetch_assoc($resCmprRetToSls);
							
							$totqty     =  $rowgetdattowRet['slsQty']+$rowCmprRetToSls['slsQty'];
							$totExtAmt  = $rowgetdattowRet['slsExtAmt']+$rowCmprRetToSls['slsExtAmt'];
							$totDiscAmt = $rowgetdattowRet['slsDiscAmt']+$rowCmprRetToSls['slsDiscAmt'];
							if(mssql_num_rows($resCmprRetToSls) > 0){
								summaizedToDlySales($rowCmprRetToSls['compCode'],$rowCmprRetToSls['locCode'],$rowCmprRetToSls['slsDate'],$rowCmprRetToSls['slsUpcNo'],$rowCmprRetToSls['unitPrice'] ,$totqty,$totExtAmt, $totDiscAmt);
							}
							else{
								summaizedToDlySales($rowgetdattowRet['compCode'],$rowgetdattowRet['locCode'],$rowgetdattowRet['slsDate'],$rowgetdattowRet['slsUpcNo'],$rowgetdattowRet['unitPrice'] ,$rowgetdattowRet['slsQty'],$rowgetdattowRet['slsExtAmt'], $rowgetdattowRet['slsDiscAmt']);
							}
					}
					
				    $qrygetdatatowsls = "SELECT * FROM wSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate = '{$salesDt}'";
					$resgetdatatowsls = mssql_query($qrygetdatatowsls);
					while($rowgetdatatowsls = mssql_fetch_assoc($resgetdatatowsls)){
						$qryCmprSlsToRet = "SELECT * FROM wReturns WHERE compCode = '{$rowgetdatatowsls['compCode']}' AND locCode = '{$rowgetdatatowsls['locCode']}' AND slsDate = '{$rowgetdatatowsls['slsDate']}' 
												AND  slsUpcNo = '{$rowgetdatatowsls['slsUpcNo']}' AND unitPrice = '{$rowgetdatatowsls['unitPrice']}'";
						$resCmprSlsToRet = mssql_query($qryCmprSlsToRet);
						if(mssql_num_rows($resCmprSlsToRet) == 0){
							summaizedToDlySales($rowgetdatatowsls['compCode'],$rowgetdatatowsls['locCode'],$rowgetdatatowsls['slsDate'],$rowgetdatatowsls['slsUpcNo'],$rowgetdatatowsls['unitPrice'] ,$rowgetdatatowsls['slsQty'],$rowgetdatatowsls['slsExtAmt'], $rowgetdatatowsls['slsDiscAmt']);
						}
					}
				}
				
				$qrygetDlyUpcSales = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate = '{$salesDt}'";
				$resgetDlyUpcSales = mssql_query($qrygetDlyUpcSales);
				if(mssql_num_rows($resgetDlyUpcSales) > 0){
					while ($rowgetDlyUpcSales = mssql_fetch_assoc($resgetDlyUpcSales)) {
						$totExtAmttmp  += $rowgetDlyUpcSales['slsExtAmt'];
						$totDiscAmttmp += $rowgetDlyUpcSales['slsDiscAmt'];
					}
				}

				$totNetAmnt = $totExtAmttmp-$totDiscAmttmp;//total net amount
				if($totNetAmnt != $nNetSalesAmnt){
					showmess("<< Store Net Sales Amount Does Not Tally with Data...Job Aborted >>");
					deleteDlySlsUpc($cmpCde,$locCde,$salesDt);
					deleWSales($cmpCde,$locCde,$salesDt);
					deleWReturns($cmpCde,$locCde,$salesDt);
				}
				else{
				    $qrygetallDlySls = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate = '{$salesDt}'";		
					$resgetallDlySls = mssql_query($qrygetallDlySls);			
					while($rowgetallDlySls = mssql_fetch_assoc($resgetallDlySls)){
						$qrygetallupcslstosumrze = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate = '{$salesDt}' 
													AND slsSkuNo = '{$rowgetallDlySls['slsSkuNo']}' AND unitPrice = '{$rowgetallDlySls['unitPrice']}' ";
						$resgetallupcslstosumrze = mssql_query($qrygetallupcslstosumrze);
						$cntgetallupcslstosumrze = mssql_num_rows($resgetallupcslstosumrze);
						$rowgetallupcslstosumrze = mssql_fetch_assoc($resgetallupcslstosumrze);
						if($cntgetallupcslstosumrze == 1){
							$qryToDlySlsSummry = "INSERT INTO tblDlySalesSummary(compCode,locCode,slsDate,slsSkuNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
													VALUES('{$rowgetallupcslstosumrze['compCode']}','{$rowgetallupcslstosumrze['locCode']}','{$rowgetallupcslstosumrze['slsDate']}',
														    '{$rowgetallupcslstosumrze['slsSkuNo']}','{$rowgetallupcslstosumrze['unitPrice']}','{$rowgetallupcslstosumrze['slsQty']}',
														    '{$rowgetallupcslstosumrze['slsExtAmt']}','{$rowgetallupcslstosumrze['slsDiscAmt']}')";
							$resToDlySlsSummry = mssql_query($qryToDlySlsSummry);
						}
					}
					if($cntgetallupcslstosumrze > 1){
						 	$qryMergeSku = "SELECT SUM(slsQty) as totquantity, SUM(slsExtAmt) as totExtendedAmount, SUM(slsDiscAmt) as totDiscountAmount FROM tblDlyUpcSales
											WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsDate = '{$salesDt}' AND slsSkuNo = '{$rowgetallupcslstosumrze['slsSkuNo']}' AND unitPrice = '{$rowgetallupcslstosumrze['unitPrice']}'";
						 	$resMergeSku = mssql_query($qryMergeSku);
						 	$rowMergeSku = mssql_fetch_assoc($resMergeSku);

						 	$qryToDlySlsSummry2 = "INSERT INTO tblDlySalesSummary(compCode,locCode,slsDate,slsSkuNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
													VALUES('{$rowgetallupcslstosumrze['compCode']}','{$rowgetallupcslstosumrze['locCode']}','{$rowgetallupcslstosumrze['slsDate']}',
														    '{$rowgetallupcslstosumrze['slsSkuNo']}','{$rowgetallupcslstosumrze['unitPrice']}','{$rowMergeSku['totquantity']}',
														    '{$rowMergeSku['totExtendedAmount']}','{$rowMergeSku['totDiscountAmount']}')";
							$resToDlySlsSummry2 = mssql_query($qryToDlySlsSummry2);
					}
					deleWSales($cmpCde,$locCde,$salesDt);
					deleWReturns($cmpCde,$locCde,$salesDt);
					delePosSlsTrans($cmpCde,$locCde,$salesDt);
					delePosVoidTrns($cmpCde,$locCde,$salesDt);
				}
			}
		}
	}	
	
	function summaizedToDlySales($companyCode,$locationCode,$salesDate,$salesUpcNumber,$salesUnitPrice ,$salesQuantity,$salesExtensionAmount, $salesDiscountAmount){
		$qryGetSku = "SELECT * FROM tblUpc WHERE upcCode = '{$salesUpcNumber}'";
		$resGetSku = mssql_query($qryGetSku);
		$rowGetSku = mssql_fetch_assoc($resGetSku);
		$qrychckdlySls = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$companyCode}' AND locCode = '{$locationCode}' AND slsDate = '{$salesDate}' AND slsUpcNo = '{$salesUpcNumber}' AND unitPrice = '{$salesUnitPrice}'" ;
		$reschckdlySls = mssql_query($qrychckdlySls);
		if(mssql_num_rows($reschckdlySls) == 0){
			$qryToDlyUpcSales = "INSERT INTO tblDlyUpcSales(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt,slsSkuNo)
				VALUES('{$companyCode}','{$locationCode}','{$salesDate}','{$salesUpcNumber}','{$salesUnitPrice}','{$salesQuantity}','{$salesExtensionAmount}','{$salesDiscountAmount}','{$rowGetSku['prdNumber']}')";
			$resToDlyUpcSales = mssql_query($qryToDlyUpcSales);
		}
	}
	function deleteDlySlsUpc($cmpcde,$loccde,$salesdt){
		$qrydeleDlySlsUpc = "DELETE  FROM tblDlyUpcSls WHERE compCode = '{$cmpcde}' AND locCode = '{$loccde}' AND slsDate = '{$salesdt}'";
		$resdeleDlySlsUpc = mssql_query($qrydeleDlySlsUpc);
	}
	function deleWSales($cmpcde,$loccde,$salesdt){
		$qrydeleWSales = "DELETE  FROM wSales WHERE compCode = '{$cmpcde}' AND locCode = '{$loccde}' AND slsDate = '{$salesdt}'";
		$resdeleWSales = mssql_query($qrydeleWSales);
	}
	function deleWReturns($cmpcde,$loccde,$salesdt){
		$qrydeleWReturns = "DELETE  FROM wRetunrs WHERE compCode = '{$cmpcde}' AND locCode = '{$loccde}' AND slsDate = '{$salesdt}'";
		$resdeleWReturns = mssql_query($qrydeleWReturns);
	}
	function delePosSlsTrans($cmpcde,$loccde,$salesdt){
		$qrydelePosSlsTrns = "DELETE FROM tblPosSlsTrans WHERE compCode = '{$cmpcde}' AND storeNo = '{$loccde}' AND slsTranDate = '{$salesdt}'";
		$resdelePosSlsTrns = mssql_query($qrydelePosSlsTrns);
	}
	function delePosVoidTrns($cmpcde,$loccde,$salesd){
		$qrydelePosVoidTrns = "DELETE FROM tblPosSlsTrans WHERE compCode = '{$cmpcde}' AND storeNo = '{$loccde}' AND TranDate = '{$salesdt}'";
		$resdelePosVoidTrns = mssql_query($qrydelePosVoidTrns);
	}
?>
<html>
	<head><title>Summarize Daily Sales</title>
	<script src="../../functions/prototype.js" type="text/javascript"></script>
		<style type="text/css">
		<!--
			.style3 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
				font-weight: bold;
			}
			.styleme 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
			}
			.style6 
			{
				font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; 
			}
			.style7 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;
			}
		-->
		</style>
	</head>
	<body>
		<form name='frmSalesFromPos' id="frmSalesFromPos" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<table border="0" bgcolor="#DEEDD1" cellspacing="0" cellpadding="0" width="70%" align="center" height="60%">
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="15" nowrap="nowrap" class="style6" align="center">
					<?php echo "<< SUMMARIZE DAILY POS SALES >>";?>        
			        </th>
			    </tr>
				<tr>
					<td height="10">
						<br>
					</td>
				</tr>
				<tr valign="top" align="center" >	
					<?
					 	$qryGetComp = "SELECT * FROM tblCompany WHERE compCode = '{$compCode}'";
					 	$resGetComp = mssql_query($qryGetComp);
					 	$rowGetComp = mssql_fetch_assoc($resGetComp);
					?>
					<td class="style3" height="10">Company<br><input type="text" class="styleme" name="compDesc" id="compDesc" value="<?=$rowGetComp['compName']?>" style="text-align: center;" readonly></td>
				</tr>
				<tr valign="top">
					<td>
						<br>
						<table align="center" border="0">
							<tr>
							<td nowrap="nowrap" colspan="3" class="style3">SALES DATE</th>
							<td nowrap="nowrap" colspan="3" class="style6">:</td>
					        <td colspan="3">
					        	<?
					        		$cmbMonth = date("m");
					        		$cmbDate = date("d");
					        		$YearOfSales = date("Y");
					        	?>
						        <span class="style3">
									<?php populatelist(mymonths(),$cmbMonth,'cmbMonth',' class="styleme" id="cmbMonth" onchange="fValidateDate()"');?>
						            <?php populatelist(daysofmonths(),$cmbDate,'cmbDay',' class="styleme" id="cmbDate" onchange="fValidateDate()"');?>
						            <input name="txtYearOfSales" id="txtYearOfSales" type="text" class="styleme" size="4" value="<?php echo $YearOfSales; ?>" onchange="fValidateDate()"/>
						        </span>  
					        </td>
							</tr>
							<tr>
								<?
								function fLocation(){
									global $compCode;
									$qryGetLoc = "SELECT * FROM tblLocation WHERE compCode = '{$compCode}' AND locstat = 'A'";
									$resGetLoc = mssql_query($qryGetLoc);
									$ArrLoc = array();
									while ($rowLoc = mssql_fetch_array($resGetLoc))
									{
									$ArrLoc[] = $rowLoc[1] . 'xOx' . $rowLoc[2];
									}
									
									return $ArrLoc;
								}
								$arrLoc = fLocation();
									
								?>
								<td nowrap="nowrap" colspan="3" class="style3">Location</td>
								<td nowrap="nowrap" colspan="3" class="style6">:</td>
								<td class="style3"><? echo populatelist($arrLoc,$cmbLoc,"cmbLoc","onchange='floc()' class='styleme'".$disabled)?></td>
							</tr>
							<tr>
							<th nowrap="nowrap" colspan="3" class="style6">NET SALES AMOUNT</th>
							<td nowrap="nowrap" colspan="3" class="style6">:</td>
					        <td class="style3">
								<input type="text" name="txtNetSales" class="styleme" id="txtNetSales" >
					        </td>
							</tr>
							<tr>
								<td height="10" colspan="3"> 
									<br>
								</td>
							</tr>
							<tr>
								<td class="style3" nowrap="nowrap" colspan="3" >USER</td>
								<td class="style3" nowrap="nowrap" colspan="3">:</td>
								<td nowrap="nowrap" colspan="3" class="style3"><input type="text" class="styleme" name="txtUser" id="txtUser" value="<?=$username?>" readonly ></td>
							</tr>
							<tr>
								<td height="10" colspan="7"> 
									<br>
								</td>
							</tr>
							<tr>
								<td colspan="7" align="center">
									<input type="submit" name="txtSumarize" id="txtSumarize" value="SUMARIZE" onclick="return fSummarize()">
									<input type="button" name="btnCncl" id="btnCncl" value="BACK" onclick="parent.location.href='../../../DFS'">
								</td>
							</tr>
						</table>				
					</td>
				</tr>
			</table>
			<input type="hidden" name="hdnCmpCde" id="hdnCmpCde" value="<?=$rowGetComp['compCode']?>">
			<input type="hidden" name="hdnLocCde" id="hdnLocCde" value="<?=$rowGetLocation['locCode']?>">
		</form>
	</body>
</html>
<script>


	function fSummarize(){
		var numericExpression = /^(-?(\d)+|-?(\d)+\.[\d]{1,4})$/;
		var hdncmpCde = $F('hdnCmpCde');
		var slsMnth   = $('cmbMonth');
		var slsDay   = $('cmbDay');
		var slsYr    = $F('txtYearOfSales');
		var loc      = $F('cmbLoc');
		var netSls   = $F('txtNetSales');
		var user     = $('txtUser');
		
		if(hdncmpCde == ""){
			alert("<< Company Is Misssing >>");
			return false;
		}
		if(slsMnth == ""){
			alert("<< Sales Month is Required >>");
			
			return false;		
		}
		if(slsDay == ""){
			alert("<< Sales Day is Required >>");
			return false;		
		}
		if(slsYr == ""){
			alert("<< Sales Year is Required >>");
			return false;		
		}	
		if(loc == ""){
			alert("<< Location is Required >>");
			$('cmbLoc').focus();
			return false;		
		}	
		if(netSls != ""){
			if(!netSls.match(numericExpression)){
				alert("<< Invalid Net Sales Numbers Only >>");
				$('txtNetSales').focus();
				return false;
			}
		}
		if(netSls == ""){
			alert("<< Net Sales Year is Required >>");
			$('txtNetSales').focus();
			return false;		
		}	
		if(user == ""){
			alert("<< User is Missing >>");
			return false;		
		}
		
		var mnthToCls = parseInt($F('cmbMonth'));
		var dayToCls =  parseInt($F('cmbDay'));
		var yearToCls = parseInt($F('txtYearOfSales'));
		
		var DteToCls = Date.parse(mnthToCls+"/"+dayToCls+"/"+yearToCls);
		var Now = Date.parse(Date());
		
		var d = new Date(yearToCls,mnthToCls);
		var LstdayOfMnth = d.toUTCString();
		var tmpLstdayOfMnth = LstdayOfMnth.split(",");
		var newLstdayOfMnth = parseInt(tmpLstdayOfMnth[1].substring(1,3));
			
		if(DteToCls > Now){
			alert("<< Date to Close must not be greater than to Current Date >>");
			$('cmbMonth').focus();
			return false;
		}
		if(dayToCls > newLstdayOfMnth){
			alert("<< Day to Close must not be greater than to " + newLstdayOfMnth + " >>");
			$('cmbDay').focus();
			return false;			
		}		
	}
</script>
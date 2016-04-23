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
		SummarizeDailySales($compCode,$_POST['hdnLocCde'],$slsDate, $_POST['txtNetSales']);
	}
	
	function SummarizeDailySales($cmpCde,$locCde,$salesDt,$nNetSalesAmnt){
		
		$qryChkDlySlesCntrl = "SELECT * FROM tblDlySalesControl WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsdate = '{$salesDt}'";
		$resChkDlySlesCntrl = mssql_query($qryChkDlySlesCntrl);
		$cntChkDlySlesCntrl = mssql_num_rows($resChkDlySlesCntrl);
		if($cntChkDlySlesCntrl > 0){
			showmess("<< Sales for this Date/Location already Posted....Job Aborted >>");
		}
		else{
			$qryChkVoidTrns = "SELECT * FROM tblPosVoidTrans WHERE compCode = '{$cmpCde}' AND tranDate = '{$salesDt}'";
			$resChkVoidTrns = mssql_query($qryChkVoidTrns);
			$cntChkVoidTrns = mssql_num_rows($resChkVoidTrns);
			if($cntChkVoidTrns > 0){
				while ($rowChkVoidTrns = mssql_fetch_assoc($resChkVoidTrns)) {
				    $qryCompareTrns = "SELECT * FROM tblPosSalesTrans WHERE compCode = '{$rowChkVoidTrns['compCode']}' 
										AND storeNo = '{$rowChkVoidTrns['storeNo']}' AND termNo = '{$rowChkVoidTrns['termNo']}' AND slstranDate = '{$rowChkVoidTrns['tranDate']}'";	
					$resCompareTrns = mssql_query($qryCompareTrns);
					$rowCompareTrns = mssql_num_rows($resCompareTrns);
					if($rowCompareTrns > 0){//tag void trans
						$qryUpdtSlsTrns = "UPDATE tblPosSalesTrans SET slsRecCode = 'V' WHERE compCode = '{$rowChkVoidTrns['compCode']}' 
										AND storeNo = '{$rowChkVoidTrns['storeNo']}' AND termNo = '{$rowChkVoidTrns['termNo']}' AND slstranDate = '{$rowChkVoidTrns['tranDate']}'";
						//$resUpdtSlsTrns = mssql_query($qryUpdtSlsTrns);								
					}//end of tag void trans
				}
			}
			
			//summarize returns
			$qrySlsTrnsSumRet = "SELECT DISTINCT(slsUpcNo) FROM tblPosSalesTrans WHERE slsRecCode = 'R' AND slsTranDate = '{$salesDt}' AND compCode = '{$cmpCde}'";
			$resSlsTrnsSumRet = mssql_query($qrySlsTrnsSumRet);
			
				while($rowSlsTrns = mssql_fetch_assoc($resSlsTrnsSumRet)){
					$qryGetReturns = "SELECT compCode, storeNo, slsUpcNo, slsUnitPrice, slsQty, slsExtAmt , slsDiscAmt FROM tblPosSalesTrans WHERE slsRecCode = 'R' AND slsTranDate = '{$salesDt}' AND 
								slsUpcNo = (SELECT DISTINCT(slsUpcNo) FROM tblPosSalesTrans WHERE slsUpcNo = '{$rowSlsTrns['slsUpcNo']}') AND compCode = '{$cmpCde}'";
					$resGetReturns = mssql_query($qryGetReturns);
					$cntGetReturns = mssql_num_rows($resGetReturns);
					$rowGetReturns = mssql_fetch_assoc($resGetReturns);
					
					$qrySummarizeRet = "SELECT SUM(slsQty)*-1 as totQtyRet, SUM(slsExtAmt)*-1 as totExtAmtRet, SUM(slsDiscAmt)*-1 as totDiscAmtRet  FROM tblPosSalesTrans WHERE slsUpcNo = '{$rowGetReturns['slsUpcNo']}' AND compCode = '{$cmpCde}' AND slsTranDate = '{$salesDt}'";
					$resSummarizeRet = mssql_query($qrySummarizeRet);
					$rowSummarizeRet = mssql_fetch_assoc($resSummarizeRet);
					
				   $qrywReturns = "INSERT INTO wReturns(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
									VALUES('{$cmpCde}', '{$locCde}', '{$salesDt}', '{$rowSlsTrns['slsUpcNo']}','{$rowGetReturns['slsUnitPrice']}', '{$rowSummarizeRet['totQtyRet']}', '{$rowSummarizeRet['totExtAmtRet']}' , '{$rowSummarizeRet['totDiscAmtRet']}')";
				   //$reswReturns = mssql_query($qrywReturns);
				}//end of summarize returns	
				
			//summarize Sales
			$qrySlsTrnsSumSls = "SELECT DISTINCT(slsUpcNo) FROM tblPosSalesTrans WHERE slsRecCode = 'S' AND slsTranDate = '{$salesDt}' AND compCode = '{$cmpCde}'";
			$resSlsTrnsSumSls = mssql_query($qrySlsTrnsSumSls);

					while($rowSlsTrnsSumSls = mssql_fetch_assoc($resSlsTrnsSumSls)){
					$qryGetReturns = "SELECT compCode, storeNo, slsUpcNo, slsUnitPrice, slsQty, slsExtAmt , slsDiscAmt FROM tblPosSalesTrans WHERE slsRecCode = 'S' AND slsTranDate = '{$salesDt}' AND 
								slsUpcNo = (SELECT DISTINCT(slsUpcNo) FROM tblPosSalesTrans WHERE slsUpcNo = '{$rowSlsTrnsSumSls['slsUpcNo']}') AND compCode = '{$cmpCde}'";
					$resGetSls = mssql_query($qryGetReturns);
					$cntGetSls = mssql_num_rows($resGetSls);
					$rowGetSls = mssql_fetch_assoc($resGetSls);
					
					$qrySummarizeSls = "SELECT SUM(slsQty) as totQtyRet, SUM(slsExtAmt) as totExtAmtRet, SUM(slsDiscAmt) as totDiscAmtRet  FROM tblPosSalesTrans WHERE slsUpcNo = '{$rowSlsTrnsSumSls['slsUpcNo']}' AND compCode = '{$cmpCde}' AND slsTranDate = '{$salesDt}'";
					$resSummarizeSls = mssql_query($qrySummarizeSls);
					$rowSummarizeSls = mssql_fetch_assoc($resSummarizeSls);
					
				    $qrywSales = "INSERT INTO wSales(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
									VALUES('{$cmpCde}', '{$locCde}', '{$salesDt}', '{$rowSlsTrnsSumSls['slsUpcNo']}','{$rowGetSls['slsUnitPrice']}' ,'{$rowSummarizeSls['totQtyRet']}', '{$rowSummarizeSls['totExtAmtRet']}' , '{$rowSummarizeSls['totDiscAmtRet']}')";
				    //$reswSales = mssql_query($qrywSales);
				}//end of summarize Sales
				
			$ntotExtAmt = 0.00;
			$ntotDiscAmt = 0.00;
			
			$qrytblWReturn = "SELECT * FROM wReturns WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsdate = '{$salesDt}'";
			$restblWReturn = mssql_query($qrytblWReturn);
			while($rowtblWReturn = mssql_fetch_assoc($restblWReturn)){
				summaizedToDlySales($rowtblWReturn['compCode'],$rowtblWReturn['locCode'],$rowtblWReturn['slsDate'],
									$rowtblWReturn['slsUpcNo'],$rowtblWReturn['unitPrice'] ,$rowtblWReturn['slsQty'],$rowtblWReturn['slsExtAmt'], $rowtblWReturn['slsDiscAmt']);
			}
			
			$qrytblSales = "SELECT * FROM wSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsdate = '{$salesDt}'";
			$restblSales = mssql_query($qrytblSales);
			while($rowtblSales = mssql_fetch_assoc($restblSales)){
				summaizedToDlySales($rowtblSales['compCode'],$rowtblSales['locCode'],$rowtblSales['slsDate'],
									$rowtblSales['slsUpcNo'],$rowtblSales['unitPrice'] ,$rowtblSales['slsQty'],$rowtblSales['slsExtAmt'], $rowtblSales['slsDiscAmt']);			
			}
			
			$qryGetGeneratedSalesByUpc = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsdate = '{$salesDt}'";
			$resGetGeneratedSalesByUpc = mssql_query($qryGetGeneratedSalesByUpc);
			while ($rowGetGeneratedSalesByUpc = mssql_fetch_assoc($resGetGeneratedSalesByUpc)) {
				$ntotExtAmt  += $rowGetGeneratedSalesByUpc['slsExtAmt'];
				$ntotDiscAmt += $rowGetGeneratedSalesByUpc['slsDiscAmt'];
			}
			echo $ntotNetAmt = $ntotExtAmt-$ntotDiscAmt;
			
			if($ntotNetAmt != $nNetSalesAmnt){
				showmess("<< Store Net Sales Amount Does not Tally with Data... Job Aborted >>");
				//$qryDeleDlyUpcSalesData = mssql_query("DELETE * FROM tblDlyUpcSales WHERE compCode = '{$cmpCde}'");
				//$qryDelewSales = mssql_query("DELETE * FROM wSales WHERE compCode = '{$cmpCde}'");
				//$qryDelewReturns = mssql_query("DELETE * FROM wReturns WHERE compCode = '{$cmpCde}'");
			}
			else{
	
				$qryGetAllDataFromUpcSales = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$cmpCde}' AND locCode = '{$locCde}' AND slsdate = '{$salesDt}'";
				$resGetAllDataFromUpcSales = mssql_query($qryGetAllDataFromUpcSales);
				
				while ($rowGetAllDataFromUpcSales = mssql_fetch_assoc($resGetAllDataFromUpcSales)) {
					
				echo	$qryToDlySalesSummary = "INSERT INTO tblDlySalesSummary(compCode,locCode,slsDate,slsSkuNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt)
												VALUES('{$rowGetAllDataFromUpcSales['compCode']}',
													   '{$rowGetAllDataFromUpcSales['locCode']}',
													   '{$rowGetAllDataFromUpcSales['slsDate']}',
													   '{$rowGetAllDataFromUpcSales['slsSkuNo']}',
													   '{$rowGetAllDataFromUpcSales['unitPrice']}',
													   '{$rowGetAllDataFromUpcSales['slsQty']}',
													   '{$rowGetAllDataFromUpcSales['slsExtAmt']}',
													   '{$rowGetAllDataFromUpcSales['slsDiscAmt']}')";					
				}
			}
		}
	}	
	
	function summaizedToDlySales($companyCode,$locationCode,$salesDate,$salesUpcNumber,$salesUnitPrice ,$salesQuantity,$salesExtensionAmount, $salesDiscountAmount){
		$qryGetSku = "SELECT * FROM tblUpc WHERE upcCode = '{$salesUpcNumber}'";
		$resGetSku = mssql_query($qryGetSku);
		$rowGetSku = mssql_fetch_assoc($resGetSku);
		
		$qryToDlyUpcSales = "INSERT INTO tblDlyUpcSales(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt,slsSkuNo)
			VALUES('{$companyCode}','{$locationCode}','{$salesDate}','{$salesUpcNumber}','{$salesUnitPrice}','{$salesQuantity}','{$salesExtensionAmount}','{$salesDiscountAmount}','{$rowGetSku['prdNumber']}')";
		//$resToDlyUpcSales = mssql_query($qryToDlyUpcSales);
	}
		
		
?>
<html>
	<head><title>Daily Sales Upload From Pos</title>
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
			<table border="1" bgcolor="#DEEDD1" cellspacing="0" cellpadding="0" width="70%" align="center" height="60%">
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
					<td class="style3" height="10"> <input type="text" class="styleme" name="compDesc" id="compDesc" value="<?=$rowGetComp['compName']?>" style="text-align: center;" readonly></td>
				</tr>
				<tr valign="top">
					<td>
						<br>
						<table align="center" border="1">
							<tr>
							<th nowrap="nowrap" colspan="3" class="style6">SALES DATE</th>
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
							<th nowrap="nowrap" colspan="3" class="style6">LOCATION</th>
							<td nowrap="nowrap" colspan="3" class="style6">:</td>
					        <td  class="style3">
					        	<?
					        		$qryGetLocation = "SELECT * FROM tblLocation WHERE compCode = '{$compCode}'";
					        		$resGetLocation = mssql_query($qryGetLocation);
					        		$rowGetLocation = mssql_fetch_assoc($resGetLocation);
					        	?>
								<input type="text" name="txtLocation" class="styleme" id="txtLocation" value="<?=$rowGetLocation['locName']?>" readonly>
					        </td>
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
fValidateDate();

	function fSummarize(){
		var hdncmpCde = $F('hdnCmpCde');
		var slsMnth   = $('cmbMonth');
		var slsDay   = $('cmbDay');
		var slsYr    = $F('txtYearOfSales');
		var loc      = $F('txtLocation');
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
			alert("<< LOcation is Required >>");
			return false;		
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
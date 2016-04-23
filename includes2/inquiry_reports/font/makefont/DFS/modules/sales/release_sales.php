<?
	//module name: Release Sales
	//programmer : vincent c de torres
	//date created: 1/12/2009
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
	
	if(isset($_POST['btnRelSls'])){
		$slsDate = $_POST['cmbMonth']."/".$_POST['cmbDay']."/".$_POST['txtYearOfSales'];
		releaseSales($compCode,$_POST['PpMonth'],$_POST['PpYear'],$slsDate,$_POST['cmbLoc'],$user,$_POST['txtRelDt']);
	}
	
	function releaseSales($compCode,$procMonth,$procYear,$slsDate,$location,$user,$dateRelease){//release sales
		
		$tmpProcMnth = explode("-",$procMonth);
		$MnthCde = trim($tmpProcMnth[0]);//month code
		
		$datetime = date("m/d/Y");
		
		$qryChkDlySlsCntrl = "SELECT * FROM tblDlySalesControl WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND slsDate = '{$slsDate}'";
		$resChkDlySlsCntrl = mssql_query($qryChkDlySlsCntrl);
		$cntChkDlySlsCntrl = mssql_num_rows($resChkDlySlsCntrl);
		if($cntChkDlySlsCntrl > 0){
			showmess("<< Daily Sales Processing for this Store is Finished...Job Aborted >>");
		}
		else{
			$qryTmpDatSlsControl = "INSERT INTO tblDlySalesControl(compCode,locCode,slsDate,slsTotExtAmt,slsTotDiscAmt,slsDateUpdated,slsDateUpdatedBy)
									VALUES('{$compCode}','{$location}','{$slsDate}','0','0','{$dateRelease}', '{$user}')";
			//$resTmpDatSlsControl = mssql_query($qryTmpDatSlsControl);
			
			$qryGetDataUpcSls = "SELECT * FROM tblDlyUpcSales WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND slsDate = '{$slsDate}'";
			$resGetDataUpcSls = mssql_query($qryGetDataUpcSls);
			while($rowGetDataUpcSls = mssql_fetch_assoc($resGetDataUpcSls)){
				$qryToSlsReg = "INSERT INTO tblSalesRegister(compCode,locCode,slsDate,slsUpcNo,unitPrice,slsQty,slsExtAmt,slsDiscAmt,slsSkuNo)
									VALUES('{$rowGetDataUpcSls['compCode']}',
										   '{$rowGetDataUpcSls['locCode']}',
										   '{$rowGetDataUpcSls['slsDate']}',
										   '{$rowGetDataUpcSls['slsUpcNo']}',
										   '{$rowGetDataUpcSls['unitPrice']}',
										   '{$rowGetDataUpcSls['slsQty']}',
										   '{$rowGetDataUpcSls['slsExtAmt']}',
										   '{$rowGetDataUpcSls['slsDiscAmt']}',
										   '{$rowGetDataUpcSls['slsSkuNo']}')";
				$resToSlsReg = mssql_query($qryToSlsReg);
			}
			deleteDlySlsUpc($compCode,$location,$slsDate);
						
			$qrychkslssummry = "SELECT * FROM tblDlySalesSummary WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND slsDate = '{$slsDate}'";
			$reschkslssummry = mssql_query($qrychkslssummry);
			
			while ($rowchkslssummry  = mssql_fetch_assoc($reschkslssummry)) {
			    $qrychkInvBalM = "SELECT * FROM tblInvBalM WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND prdNumber = '{$rowchkslssummry['slsSkuNo']}' AND pdYear = '{$procYear}' AND pdMonth = '{$MnthCde}'";	
				$reschkInvBalM = mssql_query($qrychkInvBalM);
				$cntchkInvBalM = mssql_num_rows($reschkInvBalM);
				if($cntchkInvBalM == 0){
					$qryNewInvBalMData = "INSERT INTO tblInvBalM(compCode,locCode,prdNumber,pdYear,pdMonth,begBalGoodM,begBalBoM,begCostM,begPriceM,mtdRecitQ,mtdRecitA,mtdRegSlesQ,mtdRegSlesA,mtdRegSlesC,mtdTransIn,mtdTransOut,mtdTransA,mtdAdjQ,mtdAdjA,mtdCountAdjQ,mtdCountAdjA,mtdCiQ,mtdCiA,mtdSuQ,mtdSuA,endBalGoodM,endBalBoM,endCostM,endPriceM,UPdatedBy,DateUpdated)
											VALUES('{$compCode}','{$location}','{$rowchkslssummry['slsSkuNo']}','{$procYear}','{$MnthCde}','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','{$user}','{$datetime}')";
					$resNewInvBalMData = mssql_query($qryNewInvBalMData);
				}
				
				 $qryChkAveCost = "SELECT * FROM tblAveCost WHERE compCode = '{$compCode}' AND prdNumber = '{$rowchkslssummry['slsSkuNo']}'";					
				 $resChkAveCost = mssql_query($qryChkAveCost);
				 $rowChkAveCost = mssql_fetch_assoc($resChkAveCost);
				 
				 $qrygetDupUpc = "SELECT DISTINCT(slsSkuNo), compCode, locCode,slsDate,unitPrice,slsQty,slsExtAmt,slsDiscAmt FROM tblDlySalesSummary WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND slsDate = '{$slsDate}'
				 						AND slsSkuNo = (SELECT DISTINCT(slsSkuNo) FROM tblDlySalesSummary WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND slsDate = '{$slsDate}' AND slsSkuNo= '{$rowchkslssummry['slsSkuNo']}')";
				 $resgetDupUpc = mssql_query($qrygetDupUpc);
				 $cntgetDupUpc = mssql_num_rows($resgetDupUpc);
				 $rowgetDupUpc = mssql_fetch_assoc($resgetDupUpc);

				$qrySumOfDupUpc = "SELECT SUM(slsQty) as SumSlsQty,SUM(slsExtAmt) as SumSlsExtAmt,SUM(slsDiscAmt) as SumSlsDiscAmt FROM tblDlySalesSummary WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND slsDate = '{$slsDate}' AND slsSkuNo= '{$rowgetDupUpc['slsSkuNo']}'";
				$resSumOfDupUpc = mssql_query($qrySumOfDupUpc);
				$rowSumOfDupUpc = mssql_fetch_assoc($resSumOfDupUpc);

				 $cogSold   = $rowSumOfDupUpc['SumSlsQty']*$rowChkAveCost['aveUnitCost'];//Cost of good Sold
				 $DiscPcent = ($rowchkslssummry['slsDiscAmt']/$rowchkslssummry['slsExtAmt'])*100.00;//Percentage Discount
				 $slsDisAmt    += $rowchkslssummry['slsDiscAmt'];
				 $slsTotExtAmt += $rowchkslssummry['slsExtAmt'];
				
			     $qrychkInvBalM2 = "SELECT * FROM tblInvBalM WHERE compCode = '{$compCode}' AND locCode = '{$location}' AND prdNumber = '{$rowchkslssummry['slsSkuNo']}' AND pdYear = '{$procYear}' AND pdMonth = '{$MnthCde}'";	
				 $reschkInvBalM2 = mssql_query($qrychkInvBalM2);
				 if(mssql_num_rows($reschkInvBalM2) > 0){
				 	while ($rowchkInvBalM2 = mssql_fetch_assoc($reschkInvBalM2)) {
				 	  $NewmtdSalesA =  $rowSumOfDupUpc['SumSlsExtAmt']+$rowchkInvBalM2['mtdRegSalesA'];
				 	  $NewendBalGoodM = $rowSumOfDupUpc['SumSlsQty']-$rowchkInvBalM2['endBalGoodM'];
				 	  $NewmtdRegSlesC = $cogSold+$rowchkInvBalM2['mtdRegSlesC'];
				 	  
			 		  $qryUpdtInvty = "UPDATE tblInvBalM SET mtdRegSlesQ = '{$rowSumOfDupUpc['SumSlsQty']}', mtdRegSlesA = '{$NewmtdSalesA}', endBalGoodM = '{$NewendBalGoodM}',mtdRegSlesC = '{$NewmtdRegSlesC}', 
				 	 				endCostM = '{$rowChkAveCost['aveUnitCost']}', endPriceM = '{$rowchkslssummry['unitPrice']}', updatedBy = '{$user}', dateUpdated = '{$datetime}' 
				 	 				WHERE compCode = '{$compCode}' AND prdNumber = '{$rowchkslssummry['slsSkuNo']}' AND pdYear = '{$procYear}' AND pdMonth = '{$MnthCde}'"."<br>";
				 	  $resUpdtInvty = mssql_query($qryUpdtInvty); 				 		
				 	}
				 }
				 
				 $qrygetLocType = "SELECT * FROM tblLocation Where compCode = '{$compCode}' AND locCode = '{$location}'";
				 $resgetLocType = mssql_query($qrygetLocType);
				 $rowgetLocType = mssql_fetch_assoc($resgetLocType);
				 
				 $qryGetToProdMast = "SELECT * FROM tblProdMast WHERE prdNUmber = '{$rowchkslssummry['slsSkuNo']}' AND prdDelTag <> 'D'";
				 $resGetToProdMast = mssql_query($qryGetToProdMast);
				 $rowGetToProdMast = mssql_fetch_assoc($resGetToProdMast);
				 
				 $qryCmprPrce = "SELECT * FROM tblDlyPosPrice WHERE compCode = '{$compCode}' AND prdNumber = '{$rowchkslssummry['slsSkuNo']}' AND posPrice = '{$rowchkslssummry['unitPrice']}' AND upcTag = '1'";
				 $resCmprPrce = mssql_query($qryCmprPrce);
				 $rowCmprPrce = mssql_fetch_assoc($resCmprPrce);
				 if(mssql_num_rows($resCmprPrce) > 0){
				 	$PriceCde = 0;
				 }
				 else{
				 	$PriceCde = 2;
				 }
				 
				 $qryToInvTrn = "INSERT INTO tblInvTran(compCode,locCode,locType,prdNumber,prdGroup,prdDept,prdClass,prdSubClass,setCode,prdType,transCode,DocNumber,DocDate,
				 				pdYear,pdCode,RefNo,Terms,suppCode,CustCode,trQtyGood,trQtyBO,trQtyFree,AveCost,buyCost,CstTypeCode,refCostEvent,unitPrice,PrTypeCode,refPriceEvent,
				 				posPrice,ExtAmt,itemDiscPcents,itemDiscCogY,itemDiscCogN,poLevelDiscCogY,poLevelDiscCogN,rcrAddCharges,rsnCode,DateUpdated,UpdatedBy)VALUES
				 				('{$compCode}',
				 				 '{$location}',
				 				 '{$rowgetLocType['locType']}',
				 				 '{$rowchkslssummry['slsSkuNo']}',
				 				 '{$rowGetToProdMast['prdGroupCode']}',
				 				 '{$rowGetToProdMast['prdDeptCode']}',
				 				 '{$rowGetToProdMast['prdClassCode']}',
				 				 '{$rowGetToProdMast['prdSubClassCode']}',
				 				 '{$rowGetToProdMast['prdSetTag']}',
				 				 '{$rowGetToProdMast['prdType']}',
				 				 '021',
				 				 '0',
				 				 '{$slsDate}',
				 				 '{$procYear}',
				 				 '{$MnthCde}',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '{$rowchkslssummry['slsQty']}',
				 				 '0',
				 				 '0',
				 				 '{$rowChkAveCost['aveUnitCost']}',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '{$rowchkslssummry['unitPrice']}',
				 				 '{$PriceCde}',
				 				 '',
				 				 '{$rowCmprPrce['posPrice']}',
				 				 '{$rowchkslssummry['slsExtAmt']}',
				 				 '{$DiscPcent}',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '0',
				 				 '{$datetime}',
				 				 '{$user}')"."<br>";
				$resToInvTrn = mssql_query($qryToInvTrn);
			}	
		  $qryToslsCntrl = "INSERT INTO tblDlySalesControl(compCode,locCode,slsDate,slsTotExtAmt,slsTotDiscAmt,slsDateUpdated,slsDateUpdatedby)
		 					VALUES('{$compCode}','{$location}','{$slsDate}','{$slsTotExtAmt}','{$slsDisAmt}','{$datetime}','{$user}')";
		  $resToslsCntrl = mssql_query($qryToslsCntrl);
		}
	}//end of sales release

	function deleteDlySlsUpc($cmpcde,$loccde,$salesdt){
		$qrydeleDlySlsUpc = "DELETE  FROM tblDlyUpcSls WHERE compCode = '{$cmpcde}' AND locCode = '{$loccde}' AND slsDate = '{$salesdt}'";
		$resdeleDlySlsUpc = mssql_query($qrydeleDlySlsUpc);
	}
?>
<html>
	<head><title>Release Sales</title>
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
		<form name='frmReleaseSales' id="frmReleaseSales" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<table border="0" bgcolor="#DEEDD1" cellspacing="0" cellpadding="0" width="70%" align="center" height="60%">
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23"  nowrap="nowrap" class="style6" align="center">
					<?php echo "<< RELEASE DAILY POS SALES >>";?>        
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
						<?
							$qrygetProcPrd = "SELECT * FROM tblPeriod WHERE compCode = '{$compCode}' AND pdStat = 'O'";
							$resgetProcPrd = mssql_query($qrygetProcPrd);
							$rowgetProcPrd = mssql_fetch_assoc($resgetProcPrd);	
							$PpMonth = 	$rowgetProcPrd['pdCode'] . " - " . $rowgetProcPrd['pdDesc'];	
							$PpYear = 	$rowgetProcPrd['pdYear'];	
						?>
							<tr>
								<td nowrap="nowrap" colspan="3" class="style3">PROCESSING PERIOD/YEAR</th>
								<td nowrap="nowrap" colspan="3" class="style6">:</td>
						        <td  class="style3">
									MONTH:&nbsp;<input type="text" name="PpMonth" id="PpMonth" value="<?=$PpMonth?>" class="styleme" readonly size="10">
						        </td>
						        <td  class="style3">
									YEAR:&nbsp;<input type="text" name="PpYear" id="PpYear" value="<?=$PpYear?>" class="styleme" readonly size="3">
						        </td>
							</tr>
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
									<?php populatelist(mymonths(),$cmbMonth,'cmbMonth',' class="styleme" id="cmbMonth" ');?>
						            <?php populatelist(daysofmonths(),$cmbDate,'cmbDay',' class="styleme" id="cmbDate" ');?>
						            <input name="txtYearOfSales" id="txtYearOfSales" type="text" class="styleme" size="4" value="<?php echo $YearOfSales; ?>" />
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
								<td class="style3" nowrap="nowrap" colspan="3" >DATE OF RELEASE</td>
								<td class="style3" nowrap="nowrap" colspan="3">:</td>
								<td nowrap="nowrap" colspan="3" class="style3"><input type="text" class="styleme" name="txtRelDt" id="txtRelDt" value="<?=date("m/d/Y")?>" readonly ></td>
							</tr>
							<tr>
								<td height="10" colspan="8"> 
									<br>
								</td>
							</tr>
							<tr>
								<td  align="center" colspan="8">
									<input type="submit" name="btnRelSls" id="btnRelSls" value="RELEASE" onclick="return fReleaseSls()">
									<input type="button" name="btnCncl" id="btnCncl" value="BACK" onclick="parent.location.href='../../../DFS'">
								</td>
							</tr>
						</table>
					</td>
			</table>
			<input type="hidden" name="hdnCmpCde" id="hdnCmpCde" value="<?=$rowGetComp['compCode']?>">
		</form>
	</body>
</html>
<script>
	function fReleaseSls(){
		var hdncmpCde = $F('hdnCmpCde');
		var slsMnth   = $('cmbMonth');
		var slsDay   = $('cmbDay');
		var slsYr    = $F('txtYearOfSales');
		var loc      = $F('cmbLoc');
		
		if(loc == ""){
			alert("<< Location is a Required Field >>");
			$('cmbLoc').focus();
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
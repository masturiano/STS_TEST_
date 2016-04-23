<?
//created by: vincent c de torres
//date created: jan-06-2009

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
	
	if($action == "ProcData"){

	$pdMnthToCls  = explode("-",$_POST['txtMnthToClose']);
	$pdMnthToOpen = explode("-",$_POST['txtMnthToOpen']);
		
		ProcessEOD($compCode,$user,$pdMnthToCls[0], $_POST['txtYrToClose'],$pdMnthToOpen[0],$_POST['txtYrToOpen'] );
	}
	
	
	function ProcessEOD($cmpCde, $userid, $mnthToCls, $yrToCls, $mnthTopen, $yrToOpen){
		
		$now = date("m/d/Y");
		
		$qryChkEOMAuditTbl = "SELECT * FROM tblEomAudit WHERE compCode = '{$cmpCde}' AND pdYear = '{$yrToCls}' AND pdCode = '". trim($mnthToCls) ."'";
		$resChkEOMAuditTbl = mssql_query($qryChkEOMAuditTbl);
		if(mssql_num_rows($resChkEOMAuditTbl) > 0){
			showmess("<< Specified Month to Close Already Closed... Job Aborted >>");
		}
		else{
			//close period
			$qryPeriodToCls = "UPDATE tblPeriod SET pdStat = 'C' WHERE compCode = '{$cmpCde}' AND pdCode = '".trim($mnthToCls)."' AND pdYear = '".trim($yrToCls)."'";
			$resPeriodToCls = mssql_query($qryPeriodToCls);
			
			//open period
		    $qryperiodToOpen = "UPDATE tblPeriod SET pdStat = 'O' WHERE compCode = '{$cmpCde}' AND pdCode = '".trim($mnthTopen)."' AND pdYear = '".trim($yrToOpen)."'";
		    $resperiodToOpen = mssql_query($qryperiodToOpen);
		    
		    $qryGetProd = "SELECT * FROM tblInvbalM WHERE compCode = '{$cmpCde}' AND pdYear = '{$yrToCls}' AND pdMonth = '".trim($mnthToCls)."' AND (endBalGoodM <> '0' OR endBalBoM <> '0')";
		    $resGetProd = mssql_query($qryGetProd);
		    $cntGetProd = mssql_num_rows($resGetProd);
		    if($cntGetProd > 0){
		    	while ($rowGetProd = mssql_fetch_assoc($resGetProd)) {
		    		$qryNewPrd = "INSERT INTO tblInvbalM(compCode,locCode,prdNumber,pdYear,pdMonth,begBalGoodM,begBalBoM,begCostM,begPriceM,
		    											 mtdRecitQ,mtdRecitA,mtdRegSlesQ,mtdRegSlesA,mtdRegSlesC,mtdTransIn,mtdTransOut,mtdTransA,mtdAdjQ,
		    											 mtdAdjA,mtdCountAdjQ,mtdCountAdjA,mtdCiQ,mtdCiA,mtdSuQ,mtdSuA,endBalGoodM,endBalBoM,endCostM,endPriceM,updatedBy,DateUpdated)VALUES
		    											 ('{$cmpCde}','{$rowGetProd['locCode']}','{$rowGetProd['prdNumber']}','{$yrToOpen}','".trim($mnthTopen)."',
		    											 '{$rowGetProd['begBalGoodM']}','{$rowGetProd['begBalBoM']}','{$rowGetProd['begCostM']}','{$rowGetProd['begPriceM']}',
		    											 '0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0',
		    											 '{$rowGetProd['endBalGoodM']}','{$rowGetProd['endBalBoM']}','{$rowGetProd['endCostM']}','{$rowGetProd['endPriceM']}','{$userid}','{$now}')";
		    		$resNewPrd = mssql_query($qryNewPrd);
		    	}
		    	showmess("<< End Of Month Successfully Closed >>");
		    }
		    else{
		    	showmess("<< No Product To Close >>");
		    }
		}
	}
?>
<html>
	<head><title>End of Month Processing</title>
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
	</heah>
	<body>
		<form name="frmEndOfMnth" id="frmEndOfMnth" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
			<table border="0" bgcolor="#DEEDD1" cellspacing="0" cellpadding="0" width="70%" align="center" height="60%">
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="15" nowrap="nowrap" class="style6" align="center">
					<?php echo "<< END OF MONTH PROCESSING >>";?>        
			        </th>
				</tr>
				<tr>
					<td class="style3" colspan="4" valign="top">
					<br><br>
						<table align="center" border="0">
							<tr>
								<th nowrap="nowrap" colspan="3" class="style6">
									PERIOD TO CLOSE
								</th>
							</tr>
							<tr>
								<td class="style3">MONTH</td>
								<td class="style3">:</td>
								<?
									$qryPeriodToClose = "SELECT * FROM tblPeriod WHERE pdStat = 'O'";
									$resPeriodToClose = mssql_query($qryPeriodToClose);
									$rowPeriodToClose = mssql_fetch_assoc($resPeriodToClose);
								?>
								<td><input type="text" class="styleme" name="txtMnthToClose" id="txtMnthToClose" value="<?=$rowPeriodToClose['pdCode'] . " - " . $rowPeriodToClose['pdDesc']?>" readonly></td>
							</tr>
							<tr>
								<td class="style3">YEAR</td>
								<td class="style3">:</td>
								<td><input type="text" class="styleme" name="txtYrToClose" id="txtYrToClose" value="<?=$rowPeriodToClose['pdYear']?>" readonly></td>
							</tr>
							<tr>
								<td>
									<br>
								</td>
							</tr>
							<tr>
								<th nowrap="nowrap" colspan="3" class="style6">
									PERIOD TO OPEN
								</th>
							</tr>
							<tr>
								<td class="style3">MONTH</td>
								<td class="style3">:</td>
								<?
									if($rowPeriodToClose['pdCode'] == 12){
										$MnthToOpen = 1;
									}
									else{
										$MnthToOpen = $rowPeriodToClose['pdCode']+1;
									}
									
									$MnthDesc = date("F",strtotime($rowPeriodToClose['pdCode']." month"));
									if($rowPeriodToClose['pdCode'] ==12){
										$YrToOpen = $rowPeriodToClose['pdYear']+1;
									}
									else{
										$YrToOpen = $rowPeriodToClose['pdYear'];
									}
								?>
								<td><input type="text" class="styleme" name="txtMnthToOpen" id="txtMnthToOpen" value="<?=$MnthToOpen . " - " . $MnthDesc?>" readonly></td>
							</tr>
							<tr>
								<td class="style3">YEAR</td>
								<td class="style3">:</td>
								<?
									
								?>
								<td><input type="text" class="styleme" name="txtYrToOpen" id="txtYrToOpen" value="<?=$YrToOpen?>" readonly></td>
							</tr>
							<tr>
								<td>
									<br>
								</td>
							</tr>
							<tr>
								<td class="style3">PERIOD CLOSE BY</td>
								<td class="style3">:</td>
								<td><input type="text" class="styleme" name="txtUser" id="txtUser" value="<?=$username?>" readonly></td>
							</tr>
							<tr>
								<td class="style3">DATE</td>
								<td class="style3">:</td>
								<td><input type="text" class="styleme" name="txtUser" id="txtUser" value="<?=date("m/d/Y");?>" readonly></td>
							</tr>
							<tr>
								<td>
									<br>
								</td>
							</tr>
							<tr>
								<td colspan="3" class="style3" align="center">
									<input class="styleme" type="button" name="btnProcess" id="btnProcess" value="CONTINUE" onclick="fProcess()">
									<input class="styleme" type="button" name="btnCancel" id="btnCancel" value="CANCEL" onclick="parent.location.href='../../../DFS'">
								</td>
							</td>
						</table>
					</td>
				</tr>
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="15" nowrap="nowrap" class="style6" align="center">
					
			        </th>
				</tr>
			</table>
		</form>
	</body>
</html>
<script>
	function fProcess(){
		var prdToClsMnth = $F('txtMnthToClose');
		var prdToClsYr = $('txtYrToClose');
		
		if(prdToClsMnth == ""){
			alert("<< Month To Close is Missing >>");
			return false;
		}
		if(prdToClsYr == ""){
			alert("<< Year To Close is Missing >>");
			return false;
		}
		
		var proc = confirm("<< Do You Want To Continue? >>");
		if(proc == true){
			$('frmEndOfMnth').action="<?=$_SERVER['PHP_SELF']?>?action=ProcData";
			$('frmEndOfMnth').submit();
		}
	}
</script>
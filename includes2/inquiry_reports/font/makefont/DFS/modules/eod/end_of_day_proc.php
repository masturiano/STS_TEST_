<?
//created by: vincent c de torres
//date created: jan-07-2009

	session_start();
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../mfiles/func_prod_mast_update.php");
	include("eod_function.php");
	
	$db = new DB;
	$db->connect();
	$action   = $_GET['action'];
	$compCode = $_SESSION['comp_code'];
	$user     = $_SESSION['userid'];
	$username = $_SESSION['username'];
	
	$dateToProc = $_POST['cmbMonth']."/".$_POST['cmbDay']."/".$_POST['txtYearToClose'];
	$_SESSION['endOfDayDateToProcess'] = $dateToProc;
	$_SESSION['endOfDayNextDateToProcess'] = $_POST['txtDateToOpen'];
	if($action == 'ProcData'){
			processEOD($compCode, $user, $dateToProc, $_POST['txtDateToOpen']);
		}
		
	function processEOD($cmpCde, $userid, $dttoproc, $nxtdttoproc){
		
		$now = date("m/d/Y");
		$qryChkEOD = "SELECT * FROM tblEodAudit WHERE compCode = '{$cmpCde}' AND procDate = '{$dttoproc}'";
		$resChkEOD = mssql_query($qryChkEOD);
		$cntChkEOD = mssql_num_rows($resChkEOD);
		if($cntChkEOD > 0){
			showmess("<< Specified Date to Process already Closed...Job Aborted >>");
		}
		else{	
			 $qryNewDateToProc = "INSERT INTO tblEodAudit(compCode,procDate,nextProcDate,eodDateProc,eodOptr)
												   VALUES('{$cmpCde}', '{$dttoproc}','{$nxtdttoproc}','{$now}','{$userid}')";
			 $resNewDateToProc = mssql_query($qryNewDateToProc);
			 
			 $eodFunc = new EODFunc();
			 $eodFunc->CostEOD($_POST['txtDateToOpen'],$compCode, $user);
			 $eodFunc->PriceEOD($_POST['txtDateToOpen'],$compCode, $user);
			 
			 
			 $qryGetAllProdRef = "SELECT * FROM wProdMastHeader WHERE Status = 'R'";
			 $resGetAllProdRef = mssql_query($qryGetAllProdRef);
			 
			 while ($rowGetAllProdRef = mssql_fetch_assoc($resGetAllProdRef)) {
			 	productUpdate($rowGetAllProdRef['RefNo'],$user);
			 }
			 echo "<script>alert('<< Master File Successfully Updated >>');</script>";
		}		
	}
?>
<html>
	<head><title>End of Day Processing</title>
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
		<form name="frmEndOfDay" id="frmEndOfDay" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
			<table border="0" bgcolor="#DEEDD1" cellspacing="0" cellpadding="0" width="70%" align="center" height="60%">
				<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
					<th height="23" colspan="15" nowrap="nowrap" class="style6" align="center">
					<?php echo "<< END OF DAY PROCESSING >>";?>        
			        </th>
				</tr>
				<tr>
					<td class="style3" valign="top">
					<br><br>
						<table align="center" border="0">
							<tr>
								<th nowrap="nowrap" colspan="3" class="style6">
									DATE TO CLOSED/END
								</th>
							</tr>
							<tr>
					        <td colspan="3">
					        	<?
					        		$cmbMonth = date("m");
					        		$cmbDate = date("d");
					        		$yrToClosed = date("Y");
					        	?>
						        <span class="style3">
									<?php populatelist(mymonths(),$cmbMonth,'cmbMonth',' class="styleme" id="cmbMonth" onchange="fgenNextDay()"');?>
						            <?php populatelist(daysofmonths(),$cmbDate,'cmbDay',' class="styleme" id="cmbDate" onchange="fgenNextDay()"');?>
						            <input name="txtYearToClose" id="txtYearToClose" type="text" class="styleme" size="4" value="<?php echo $yrToClosed; ?>" onchange="fgenNextDay()"/>
						        </span>  
					        </td>
							</tr>
							<tr>
								<td colspan="3">
									<br>
								</td>
							</tr>
							<tr>
								<th nowrap="nowrap" colspan="3" class="style6">
									NEXT PROCESSING DATE
								</th>
							</tr>
							<tr>
								<td colspan="3" align="center">
									<input type="text" class="styleme" name="txtDateToOpen" id="txtDateToOpen" readonly style="text-align: center">
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<br>
								</td>
							</tr>
							<tr>
								<td class="style3">USER</td>
								<td class="style3">:</td>
								<td><input type="text" class="styleme" name="txtUser" id="txtUser" value="<?=$username?>" readonly ></td>
							</tr>
							<tr>
								<td class="style3">DATE</td>
								<td class="style3">:</td>
								<td><input type="text" class="styleme" name="txtUser" id="txtUser" value="<?=date("m/d/Y");?>" readonly></td>
							</tr>
							<tr>
								<td colspan="3">
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
		var mnthToCls = $F('cmbMonth');
		var dayToCls =  $F('cmbDay');
		var yearToCls = $F('txtYearToClose');
		
		var datetoopen = $F('txtDateToOpen');
		if(mnthToCls == ""){
			alert("<< Month To Close is a Required Field >>");
			$('cmbMonth').focus();
			return false;
		}
		if(dayToCls == ""){
			alert("<< Day To Close is a Required Field >>");
			$('cmbDay').focus();
			return false;
		}
		if(yearToCls == ""){
			alert("<< Year To Close is a Required Field >>");
			$('txtYearToClose').focus();
			return false;
		}
		if(datetoopen == ""){
			alert("<< Next Date to Process is Missing >>");
			return false;
		}		
		
		var proc = confirm("<< Do You Want To Continue? >>");
		if(proc == true){
			$('frmEndOfDay').action='<?=$_SERVER['PHP_SELF']?>?action=ProcData';
			$('frmEndOfDay').submit();
		}
	}
	
	fgenNextDay();
	function fgenNextDay(){
		var mnthToCls = parseInt($F('cmbMonth'));
		var dayToCls =  parseInt($F('cmbDay'));
		var yearToCls = parseInt($F('txtYearToClose'));
		
		var DteToCls = Date.parse(mnthToCls+"/"+dayToCls+"/"+yearToCls);
		var Now = Date.parse(Date());
		
		var d = new Date(yearToCls,mnthToCls);
		var LstdayOfMnth = d.toUTCString();
		var tmpLstdayOfMnth = LstdayOfMnth.split(",");
		var newLstdayOfMnth = parseInt(tmpLstdayOfMnth[1].substring(1,3));
			
		if(DteToCls > Now){
			alert("<< Date to Close must not be greater than to Current Date >>");
			var CurDate = new Date();
			$('cmbMonth').value=CurDate.getMonth();
			$('cmbDay').value=CurDate.getDate();
			$('txtYearToClose').value=CurDate.getFullYear();
			return false;
		}
		if(dayToCls > newLstdayOfMnth){
			alert("<< Day to Close must not be greater than to " + newLstdayOfMnth + " >>");
			$('cmbDay').value=newLstdayOfMnth;
			return false;			
		}
		
		if(dayToCls == newLstdayOfMnth){
			$('txtDateToOpen').value=eval(mnthToCls+1)+"/1/"+yearToCls;
		}
		else{
			$('txtDateToOpen').value=mnthToCls+"/"+eval(dayToCls+1)+"/"+yearToCls;
		}
		if((mnthToCls == 12) && (dayToCls == newLstdayOfMnth)){
			$('txtDateToOpen').value="1/1/"+eval(yearToCls+1);
		}
	}
</script>
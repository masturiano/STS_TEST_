<?php
	//include("../../functions/inquiry_session.php");
	session_start();
	
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	require_once "../../modules/etc/etc.obj.php";
	$etcTrans = new etcObject;
	$db = new DB;
	$db->connect();
	$company_code = $_SESSION['comp_code'];
	$gTag = $_GET['myTag'];	
	$pdCode = $_GET['pdCode'];
	$pdYear = $_GET['pdYear'];
	
	$ArrType = array();
	$ArrType[] = "BxOxBUY";
	$ArrType[] = "SxOxSELL";

	$ArrStatus = array();
	$ArrStatus[] = "AxOxACTIVE";
	$ArrStatus[] = "DxOxDELETED";
	
	($_GET['maction'] == "view") ? $lLock = ' disabled="disabled" ' : $lLock = ' ';	
	if (isset($btnSubmit))
	{
		if ($_GET['maction'] == "edit" or $_GET['maction'] == "view")
		{
			$UpdateSQL = "UPDATE tblPeriod SET ";
			$UpdateSQL .= "pdDesc = '".strtoupper($txtDesc)."', ";
			$UpdateSQL .= "pdYear = ". $txtYear.", ";
			$UpdateSQL .= "pdStart = '". $txtStart."', ";
			$UpdateSQL .= "pdEnd = '". $txtEnd."', ";
			if ($txtClose>"") {
				$UpdateSQL .= "pdDateClosed = '". $txtClose."', ";
			} 
			$UpdateSQL .= "pdStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE pdCode = " . $pdCode . " AND pdYear = " . $pdYear;
			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Period Masterfile successfully updated >>>')</script>";
		}
		
		else
		{	
			$strSQL = "SELECT * FROM tblPeriod WHERE pdCode = " . $txtpdCode . " AND pdYear = " . $txtYear;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr) {
				echo "<script>alert('<<< Period Masterfile exists >>>')</script>";
			} else {
				if ($txtClose=="") {
					$InsertSQL = "INSERT INTO tblPeriod(compCode, pdCode, pdDesc, pdYear, pdStart, pdEnd, pdStat)";
					$InsertSQL .= "VALUES (". $cmbCompany.", ". $txtpdCode. ", '" . strtoupper($txtDesc) . "', " . $txtYear . ", '" . $txtStart . "', '" . $txtEnd . "', '" . $cmbStatus . "')";
				} else {
					$InsertSQL = "INSERT INTO tblPeriod(compCode, pdCode, pdDesc, pdYear, pdStart, pdEnd, pdDateClosed, pdStat)";
					$InsertSQL .= "VALUES (". $cmbCompany.", ". $txtpdCode. ", '" . strtoupper($txtDesc) . "', " . $txtYear . ", '" . $txtStart . "', '" . $txtEnd . "', '" . $txtClose . "', '" . $cmbStatus . "')";			
				}		
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Period successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strPeriod = "SELECT * FROM tblPeriod WHERE pdCode = $pdCode AND pdYear = $pdYear";
		$qryPeriod = mssql_query($strPeriod);
		
		if (mssql_num_rows($qryPeriod) > 0)
		{
			while ($rstPeriod = mssql_fetch_array($qryPeriod))
			{
				$cmbCompany = $rstPeriod[0];
				$txtYear = strtoupper($rstPeriod[1]);
				$txtpdCode = $rstPeriod[2];
				$txtDesc= strtoupper($rstPeriod[3]);
				
				$txtStart = $rstPeriod[4];
				if (!$txtStart) {
					$txtStart = "";
				} else {
					$txtStart = new DateTime( $rstPeriod[4]);
					$txtStart = $txtStart->format("m/j/Y");
				}
				
				$txtEnd = $rstPeriod[5];
				if (!$txtEnd) {
					$txtEnd = "";
				} else {
					$txtEnd = new DateTime( $rstPeriod[5]);
					$txtEnd = $txtEnd->format("m/j/Y");
				}
				
				$txtClose = $rstPeriod[7];
				if (!$txtClose) {
					$txtClose = "";
				} else {
					$txtClose = new DateTime( $rstPeriod[7]);
					$txtClose = $txtClose->format("m/j/Y");
				}
				
				$cmbStatus = $rstPeriod[6];
			} 
		}
	}
?>

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
<html>
<head>
</head>
<body>
<center>
<form method="post" name="myform">
<table width="600" border="0" bgcolor="#DEEDD1">
	<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="center"><input name="hAddEdit" type="hidden" id="hAddEdit" value="<? echo $_GET['maction']; ?>"> 
          <?php echo ($_GET['maction'] == "edit") ? "<<< EDIT PERIOD MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT PERIOD MASTERFILE >>>" : "<<< ADD PERIOD >>>"; ?> 
        </th>
	</tr>
	<tr>
	    <td><span class="style3">Period Start</span></td>
	    <td><span class="style3">
          <input name='txtStart' type='text' class='styleme' onFocus="autoDate();" id='txtStart' value='<?php echo $txtStart;?>' size='15' maxlength="15" readonly="true"/>
          <font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:void(0)" onClick="showCalendar(myform.txtStart,'mm/dd/yyyy','Choose date'); "><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a></font> 
          </span></td>
    </tr>s
	 <tr>
	    <td><span class="style3">Period End</span></td>
	    <td><span class="style3">
          <input name='txtEnd' type='text' class='styleme' onFocus="autoDate();" id='txtEnd' value='<?php echo $txtEnd;?>' size='15' maxlength="15" readonly="true"/>
          <font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:void(0)" onClick="showCalendar(myform.txtEnd,'mm/dd/yyyy','Choose date'); "><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a></font> 
          </span></td>
    </tr>
    <tr>
	    <td width="167"><span class="style3">Period Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtpdCode' type='text' class='styleme' id='txtpdCode' value='<?php echo $txtpdCode;?>' size='10' maxlength="8" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtpdCode' type='text' class='styleme' id='txtpdCode' value='<?php echo $txtpdCode;?>'   size='10' maxlength="8"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Description</span></td>
	    <td>
            <span class="style3">
				<input name='txtDesc' type='text' class='styleme' id='txtDesc' value='<?php echo $txtDesc;?>' size='40' maxlength="10"/>            
            </span>        </td>
    </tr>
    <tr>
	    <td width="167"><span class="style3">Period Year</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtYear' type='text' class='styleme' id='txtYear' value='<?php echo $txtYear;?>' size='10' maxlength="8" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtYear' type='text' class='styleme' id='txtYear' value='<?php echo $txtYear;?>'   size='10' maxlength="8"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Period Date Closed</span></td>
	    <td><span class="style3">
          <input name='txtClose' type='text' class='styleme' id='txtClose' value='<?php echo $txtClose;?>' size='15' maxlength="15" readonly="true"/>
          <font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:void(0)" onClick="showCalendar(myform.txtClose,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a></font> 
          </span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Status</span></td>
	    <td><span class="style3"> 
          <select name="cmbStatus" id="cmbStatus">
		    <option selected><?
							 	if ($cmbStatus=='') {
									echo "H";
								} else {
									echo $cmbStatus;
								}
							 ?></option>
            <option value="O">O</option>
            <option value="C">C</option>
			<option value="H">H</option>
          </select>
          </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Company</span></td>
	    <td><span class="style3"> 
			<?php 
				if ($_GET['maction'] == "edit") {
					$disabled="disabled";
				} else {
					$disabled="";
				}
			?>
          <select name="cmbCompany" id="cmbCompany" <? echo $disabled; ?>>	
							<?
							 	if ($cmbCompany=="") {
									$cmbCompany=$company_code;
									$cmbCompanyName = $etcTrans->getCompanyName($cmbCompany);
								} else {
									$cmbCompanyName = $etcTrans->getCompanyName($cmbCompany);
								}
								echo "<option selected value='$cmbCompany'>
									  $cmbCompanyName
									  </option>";
								$query_company="SELECT * FROM tblCompany ORDER BY compName ASC";
								$result_company=mssql_query($query_company);
								$num_company = mssql_num_rows($result_company);
								for ($i=0;$i < $num_company;$i++){
									$cmb_compcode=mssql_result($result_company,$i,"compCode");								
									$cmb_compname=mssql_result($result_company,$i,"compName");
									echo "<option value='$cmb_compcode'>$cmb_compname</option>";
								}
							?>
          </select>
          </span></td>
    </tr>
	<td colspan="6" nowrap="nowrap" class="style3" align="center">
        <span class="style3">
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validatePeriod(); ' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="period.php" title="Back to Period" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtpdCode').focus();
	function validatePeriod() {
		var bCode = window.document.getElementById('txtpdCode').value;
		var bName = window.document.getElementById('txtDesc').value;
		var bYear = window.document.getElementById('txtYear').value;
		var bStart = window.document.getElementById('txtStart').value;
		var bEnd = window.document.getElementById('txtEnd').value;
		var bClose = window.document.getElementById('txtClose').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var today_date = new Date();
		var bTodayMm = Date.parse(today_date);
		var bStartMm = Date.parse(bStart);
		var bEndMm = Date.parse(bEnd);
		var bDateDiff = bEndMm - bStartMm;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Period Code : Numbers only");
			window.document.getElementById('txtpdCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Period Code is a required field >>>");
			window.document.getElementById('txtpdCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Period Desc is a required field >>>");
			window.document.getElementById('txtDesc').focus();
			return false;
		}
		if(!bYear.match(numeric_expression) && bYear>"") {
			alert("Period Year : Numbers only");
			window.document.getElementById('txtYear').focus();
			return false;
		} 
		if (bYear == "") {
			window.alert("<<< Period Year is a required field >>>");
			window.document.getElementById('txtYear').focus();
			return false;
		}   
		if (bEnd == "") {
			window.alert("<<< Period End is a required field >>>");
			window.document.getElementById('txtEnd').focus();
			return false;
		}   
		if (bStart == "") {
			window.alert("<<< Period Satrt is a required field >>>");
			window.document.getElementById('txtStart').focus();
			return false;
		}
		if (bStartMm > bEndMm) {
			window.alert("<<< Period Start Date is less than the Period End Date >>>");
			window.document.getElementById('txtStart').focus();
			return false;
		}
		//if (bDateDiff > 2529000000) {
		//	window.alert("<<< Over Date Period Range >>>");
		//	window.document.getElementById('txtStart').focus();
		//	return false;
		//}
	}
	function autoDate() {
		var bStart = window.document.getElementById('txtStart').value;
		var bEnd = window.document.getElementById('txtEnd').value;
		var bH = window.document.getElementById('hAddEdit').value;
		
		bStartSplit = bStart.split("/");
		bEndSplit = bEnd.split("/");
		
		var bStartMm = Date.parse(bStart);
		var bEndMm = Date.parse(bEnd);
		var bDateDiff = bEndMm - bStartMm;
		
		if (bStartSplit[0]=="1") {
			var bMonthName="JANUARY";
		}
		if (bStartSplit[0]=="2") {
			var bMonthName="FEBRUARY";
		}
		if (bStartSplit[0]=="3") {
			var bMonthName="MARCH";
		}
		if (bStartSplit[0]=="4") {
			var bMonthName="APRIL";
		}
		if (bStartSplit[0]=="5") {
			var bMonthName="MAY";
		}
		if (bStartSplit[0]=="6") {
			var bMonthName="JUNE";
		}
		if (bStartSplit[0]=="7") {
			var bMonthName="JULY";
		}
		if (bStartSplit[0]=="8") {
			var bMonthName="AUGUST";
		}
		if (bStartSplit[0]=="9") {
			var bMonthName="SEPTEMBER";
		}
		if (bStartSplit[0]=="10") {
			var bMonthName="OCTOBER";
		}
		if (bStartSplit[0]=="11") {
			var bMonthName="NOVEMBER";
		}
		if (bStartSplit[0]=="12") {
			var bMonthName="DECEMBER";
		}
		
		if (bStart>"" && bEnd>"") {
			if ((bStartSplit[0]==bEndSplit[0]) && (bStartSplit[2]==bEndSplit[2])) {
				if (bH!="edit") {
					window.document.getElementById('txtpdCode').value=bStartSplit[0];
					window.document.getElementById('txtYear').value=bStartSplit[2];
				}
				window.document.getElementById('txtDesc').value=bMonthName;
			} else {
				window.document.getElementById('txtEnd').value = "";
				if (bH!="edit") {
					window.document.getElementById('txtpdCode').value=bStartSplit[0];
					window.document.getElementById('txtYear').value = "";
				}
				window.document.getElementById('txtDesc').value = "";
				alert ("<<< Period Start Date must be in-range to Period End Date >>>");
				//return false;
			}
			if (bStartMm > bEndMm) {
				window.alert("<<< Period Start Date is less than the Period End Date >>>");
				window.document.getElementById('txtEnd').value = "";
				if (bH!="edit") {
					window.document.getElementById('txtpdCode').value=bStartSplit[0];
					window.document.getElementById('txtYear').value = "";
				}
				window.document.getElementById('txtDesc').value = "";
				return false;
			}
		}
		
	}
</script>



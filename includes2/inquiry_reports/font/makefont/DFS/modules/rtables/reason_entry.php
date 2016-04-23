<?php
	//include("../../functions/inquiry_session.php");
	session_start();
	$company_code = $_SESSION['comp_code'];
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	
	$db = new DB;
	$db->connect();

	$gTag = $_GET['myTag'];	
	$rsnCode = $_GET['rsnCode'];
	
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
			$UpdateSQL = "UPDATE tblReasons SET ";
			$UpdateSQL .= "rsnDesc = '".strtoupper($txtDesc)."', ";
			$UpdateSQL .= "rsnInit = '".strtoupper($txtShort)."', ";
			$UpdateSQL .= "rsnGlMajor = '".strtoupper($txtMajor)."', ";
			$UpdateSQL .= "rsnGlMinor = '".strtoupper($txtMinor)."', ";
			$UpdateSQL .= "rsnStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE rsnCode = " . $rsnCode;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Reason for Adjustments Masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblReasons WHERE rsnCode = " . $txtrsnCode;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Reason for Adjustments Masterfile exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblReasons(rsnCode, rsnDesc, rsnInit, rsnGlMajor, rsnGlMinor, rsnStat)";
				$InsertSQL .= "VALUES (". $txtrsnCode. ", '" . strtoupper($txtDesc) . "', '" . strtoupper($txtShort) . "', '" . strtoupper($txtMajor) . "','" . strtoupper($txtMinor) . "','" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Reason for Adjustments successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strReason = "SELECT * FROM tblReasons WHERE rsnCode = $rsnCode";
		$qryReason = mssql_query($strReason);
		
		if (mssql_num_rows($qryReason) > 0)
		{
			while ($rstReason = mssql_fetch_array($qryReason))
			{
				$txtrsnCode = $rstReason[0];
				$txtDesc= strtoupper($rstReason[1]);
				$txtShort = strtoupper($rstReason[2]);
				$txtMajor = $rstReason[3];
				$txtMinor = $rstReason[4];
				$cmbStatus = $rstReason[5];
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
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="center">
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT REASON FOR ADJUSMENTS MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT REASON FOR ADJUSTMNETS MASTERFILE >>>" : "<<< ADD REASON FOR ADJUSTMENTS >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Reason Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtrsnCode' type='text' class='styleme' id='txtrsnCode' value='<?php echo $txtrsnCode;?>' size='10' maxlength="8" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtrsnCode' type='text' class='styleme' id='txtrsnCode' value='<?php echo $txtrsnCode;?>' size='10' maxlength="8"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Description</span></td>
	    <td>
            <span class="style3">
				<input name='txtDesc' type='text' class='styleme' id='txtDesc' value='<?php echo $txtDesc;?>' size='40' maxlength="25"/>            
            </span>        </td>
    </tr>
    <tr>
	    <td><span class="style3">Short Name</span></td>
	    <td><span class="style3"><input name='txtShort' type='text' class='styleme' id='txtShort' value='<?php echo $txtShort;?>' size='40' maxlength="5"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">GL Major</span></td>
	    <td><span class="style3"><input name='txtMajor' type='text' class='styleme' id='txtMajor' value='<?php echo $txtMajor;?>' size='40' maxlength="3"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">GL Minor</span></td>
	    <td><span class="style3"><input name='txtMinor' type='text' class='styleme' id='txtMinor' value='<?php echo $txtMinor;?>' size='40' maxlength="3"/>  </span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Status</span></td>
	    <td><span class="style3"> 
          <select name="cmbStatus" id="cmbStatus">
		  	<option selected><?
							 	if ($cmbStatus=='A' || $cmbStatus=='') {
									echo "Active";
								} else {
									echo "Deleted";
								}
							 ?></option>
            <option value="A">Active</option>
            <option value="D">Deleted</option>
          </select>
          </span></td>
    </tr>
	<td colspan="6" nowrap="nowrap" class="style3" align="center">
        <span class="style3">
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validateReason(); ' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="reason.php" title="Back to Reason for Adjustments" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtrsnCode').focus();
	function validateReason() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txtrsnCode').value;
		var bName = window.document.getElementById('txtDesc').value;
		var bShort = window.document.getElementById('txtShort').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Reason Code : Numbers only");
			window.document.getElementById('txtrsnCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Reason Code is a required field >>>");
			window.document.getElementById('txtrsnCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Reason Desc is a required field >>>");
			window.document.getElementById('txtDesc').focus();
			return false;
		}
		if (bShort == "") {
			window.alert("<<< Reason Short Name is a required field >>>");
			window.document.getElementById('txtShort').focus();
			return false;
		}   
	}
</script>



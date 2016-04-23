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
	$trmCode = $_GET['trmCode'];
	
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
			$UpdateSQL = "UPDATE tblTerms SET ";
			$UpdateSQL .= "trmDesc = '".strtoupper($txtDesc)."', ";
			$UpdateSQL .= "trmShort = '".strtoupper($txtShort)."', ";
			$UpdateSQL .= "trmStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE trmCode = " . $trmCode;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Terms Masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblTerms WHERE trmCode = " . $txttrmCode;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Terms Masterfile exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblTerms(trmCode, trmDesc, trmShort, trmStat)";
				$InsertSQL .= "VALUES (". $txttrmCode. ", '" . strtoupper($txtDesc) . "', '" . strtoupper($txtShort) . "', '" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Terms successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strTrans = "SELECT * FROM tblTerms WHERE trmCode = $trmCode";
		$qryTrans = mssql_query($strTrans);
		
		if (mssql_num_rows($qryTrans) > 0)
		{
			while ($rstTrans = mssql_fetch_array($qryTrans))
			{
				$txttrmCode = $rstTrans[0];
				$txtDesc= strtoupper($rstTrans[1]);
				$txtShort = strtoupper($rstTrans[2]);
				$cmbStatus = $rstTrans[3];
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
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT TERMS MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT TERMS MASTERFILE >>>" : "<<< ADD TERMS >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Terms Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txttrmCode' type='text' class='styleme' id='txttrmCode' value='<?php echo $txttrmCode;?>' size='10' maxlength="4" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txttrmCode' type='text' class='styleme' id='txttrmCode' value='<?php echo $txttrmCode;?>' size='10' maxlength="4"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Terms Description</span></td>
	    <td>
            <span class="style3">
				<input name='txtDesc' type='text' class='styleme' id='txtDesc' value='<?php echo $txtDesc;?>' size='40' maxlength="80"/>            
            </span>        </td>
    </tr>
    <tr>
	    <td><span class="style3">Short Name</span></td>
	    <td><span class="style3"><input name='txtShort' type='text' class='styleme' id='txtShort' value='<?php echo $txtShort;?>' size='40' maxlength="40"/>  </span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Status</span></td>
	    <td><span class="style3"> 
          <select name="cmbStatus" id="cmbStatus">
		  	<option selected><?
							 	if ($cmbStatus=="A" || $cmbStatus=="") {
									echo "A";
								} else {
									echo "D";
								}
							 ?></option>
            <option value="A">A</option>
            <option value="D">D</option>
          </select>
          </span></td>
    </tr>
	<td colspan="6" nowrap="nowrap" class="style3" align="center">
        <span class="style3">
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validateLoc(); ' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="terms.php" title="Back to Terms List" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txttrmCode').focus();
	function validateLoc() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txttrmCode').value;
		var bName = window.document.getElementById('txtDesc').value;
		var bShort = window.document.getElementById('txtShort').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Terms Code : Numbers only");
			window.document.getElementById('txttrmCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Terms Code is a required field >>>");
			window.document.getElementById('txttrmCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Terms Desc is a required field >>>");
			window.document.getElementById('txtDesc').focus();
			return false;
		}
		if (bShort == "") {
			window.alert("<<< Terms Short Name is a required field >>>");
			window.document.getElementById('txtShort').focus();
			return false;
		}   
	}
</script>



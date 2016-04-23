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
	$allwTypeCode = $_GET['allwTypeCode'];
	
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
			$UpdateSQL = "UPDATE tblAllowType SET ";
			$UpdateSQL .= "allwDesc = '".strtoupper($txtDesc)."', ";
			$UpdateSQL .= "allwInit = '".strtoupper($txtShort)."', ";
			$UpdateSQL .= "allwCostTag = '".strtoupper($cmbTag)."', ";
			$UpdateSQL .= "allwStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE allwTypeCode = '" . $allwTypeCode . "'";

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Allowance Type Masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblAllowType WHERE allwTypeCode = '" . $txtallwTypeCode . "'";
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Allowance Type Masterfile exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblAllowType(allwTypeCode, allwDesc, allwInit, allwCostTag, allwStat)";
				$InsertSQL .= "VALUES ('". strtoupper($txtallwTypeCode). "', '" . strtoupper($txtDesc) . "', '" . strtoupper($txtShort) . "', '" . strtoupper($cmbTag) . "','" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Allowance Type successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strTrans = "SELECT * FROM tblAllowType WHERE allwTypeCode = '$allwTypeCode'";
		$qryTrans = mssql_query($strTrans);
		
		if (mssql_num_rows($qryTrans) > 0)
		{
			while ($rstTrans = mssql_fetch_array($qryTrans))
			{
				$txtallwTypeCode = strtoupper($rstTrans[0]);
				$txtDesc= strtoupper($rstTrans[1]);
				$txtShort = strtoupper($rstTrans[2]);
				$cmbTag = $rstTrans[3];
				$cmbStatus = $rstTrans[4];
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
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT ALLOWANCE TYPES MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT ALLOWANCE TYPES MASTERFILE >>>" : "<<< ADD ALLOWANCE TYPES >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Allowance Type Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtallwTypeCode' type='text' class='styleme' id='txtallwTypeCode' value='<?php echo $txtallwTypeCode;?>' size='10' maxlength="2" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtallwTypeCode' type='text' class='styleme' id='txtallwTypeCode' value='<?php echo $txtallwTypeCode;?>' size='10' maxlength="2"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Name</span></td>
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
	    <td><span class="style3">Tag</span></td>
	    <td><span class="style3"> 
          <select name="cmbTag" id="cmbTag">
		  	<option selected><?php 
								if ($cmbTag=="") {
									$cmbTag ="";
								}
								echo $cmbTag;
							 ?></option>
            <option value="N">N</option>
			<option value="Y">Y</option>
            <option value=""></option>
          </select>
          </span></td>
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
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validateLoc(); ' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="allowance.php" title="Back to Allowance Types" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtallwTypeCode').focus();
	function validateLoc() {
		var bCode = window.document.getElementById('txtallwTypeCode').value;
		var bName = window.document.getElementById('txtDesc').value;
		var bShort = window.document.getElementById('txtShort').value; 
		if (bCode == "") {
			window.alert("<<< Allowance Type Code is a required field >>>");
			window.document.getElementById('txtallwTypeCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Allowance Type Desc is a required field >>>");
			window.document.getElementById('txtDesc').focus();
			return false;
		}
		if (bShort == "") {
			window.alert("<<< Allowance Type Short Name is a required field >>>");
			window.document.getElementById('txtShort').focus();
			return false;
		}   
	}
</script>



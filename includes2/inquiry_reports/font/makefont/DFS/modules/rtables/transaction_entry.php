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
	$trnTypeCode = $_GET['trnTypeCode'];
	
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
			$UpdateSQL = "UPDATE tblTransactionType SET ";
			$UpdateSQL .= "trnTypeDesc = '".strtoupper($txtDesc)."', ";
			$UpdateSQL .= "trnTypeInit = '".strtoupper($txtShort)."', ";
			$UpdateSQL .= "trnInvTag = '".strtoupper($cmbTag)."', ";
			$UpdateSQL .= "trnInvOprn = '".strtoupper($cmbOprn)."', ";
			$UpdateSQL .= "trnTypeStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE trnTypeCode = " . $trnTypeCode;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Transaction Type Masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblTransactionType WHERE trnTypeCode = " . $txttrnTypeCode;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Transaction Type Masterfile exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblTransactionType(trnTypeCode, trnTypeDesc, trnTypeInit, trnInvTag, trnInvOprn, trnTypeStat)";
				$InsertSQL .= "VALUES (". $txttrnTypeCode. ", '" . strtoupper($txtDesc) . "', '" . strtoupper($txtShort) . "', '" . strtoupper($txtTag) . "','" . strtoupper($txtOprn) . "','" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Transaction Type successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strTrans = "SELECT * FROM tblTransactionType WHERE trnTypeCode = $trnTypeCode";
		$qryTrans = mssql_query($strTrans);
		
		if (mssql_num_rows($qryTrans) > 0)
		{
			while ($rstTrans = mssql_fetch_array($qryTrans))
			{
				$txttrnTypeCode = $rstTrans[0];
				$txtDesc= strtoupper($rstTrans[1]);
				$txtShort = strtoupper($rstTrans[2]);
				$cmbTag = $rstTrans[3];
				$cmbOprn = $rstTrans[4];
				$cmbStatus = $rstTrans[5];
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
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT TRANSACTION TYPES MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT TRANSACTION TYPES MASTERFILE >>>" : "<<< ADD TRANSACTION TYPES >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Transaction Type Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txttrnTypeCode' type='text' class='styleme' id='txttrnTypeCode' value='<?php echo $txttrnTypeCode;?>' size='10' maxlength="8" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txttrnTypeCode' type='text' class='styleme' id='txttrnTypeCode' value='<?php echo $txttrnTypeCode;?>' size='10' maxlength="8"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Name</span></td>
	    <td>
            <span class="style3">
				<input name='txtDesc' type='text' class='styleme' id='txtDesc' value='<?php echo $txtDesc;?>' size='40' maxlength="50"/>            
            </span>        </td>
    </tr>
    <tr>
	    <td><span class="style3">Short Name</span></td>
	    <td><span class="style3"><input name='txtShort' type='text' class='styleme' id='txtShort' value='<?php echo $txtShort;?>' size='40' maxlength="6"/>  </span></td>
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
            <option value=""></option>
          </select>
          </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Operation</span></td>
	    <td><span class="style3"> 
          <select name="cmbOprn" id="cmbOprn">
		  	<option selected><?php 
								if ($cmbOprn=="") {
									$cmbOprn ="";
								}
								echo $cmbOprn;
							 ?></option>
            <option value="S">S</option>
            <option value="A">A</option>
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
        <a href="transaction.php" title="Back to Transaction Types" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txttrnTypeCode').focus();
	function validateLoc() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txttrnTypeCode').value;
		var bName = window.document.getElementById('txtDesc').value;
		var bShort = window.document.getElementById('txtShort').value;
		var bTag = window.document.getElementById('cmbTag').value;
		var bOprn = window.document.getElementById('cmbOprn').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Transaction Type Code : Numbers only");
			window.document.getElementById('txttrnTypeCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Transaction Type Code is a required field >>>");
			window.document.getElementById('txttrnTypeCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Transaction Type Desc is a required field >>>");
			window.document.getElementById('txtDesc').focus();
			return false;
		}
		if (bShort == "") {
			window.alert("<<< Transaction Type Short Name is a required field >>>");
			window.document.getElementById('txtShort').focus();
			return false;
		}   
	}
</script>



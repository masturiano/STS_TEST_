<?php
	//include("../../functions/inquiry_session.php");
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	
	$db = new DB;
	$db->connect();

	$gTag = $_GET['myTag'];	
	$prdNumber = $_GET['prdNumber'];
	$Vref = $_GET['Vref'];
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
			$UpdateSQL = "UPDATE tblVendorProduct SET ";
			$UpdateSQL .= "suppProdNo = '".$txtInit."' ";
			$UpdateSQL .= "WHERE prdNumber = " . $prdNumber . " AND suppCode = " . $Vref;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Product Reference successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblProdMast WHERE prdNumber = " . $txtprdNumber;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Product Reference exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblProdMast(prdNumber, prdDesc, prdSuppItem, cstPrecedence, cstAcct1, cstAcct2, cstTag, cstStat)";
				$InsertSQL .= "VALUES (". $txtprdNumber. ", '" . strtoupper($txtDesc) . "', '" . strtoupper($txtInit) . "', " . $txtPrecedence . ", " . $txtAcct1 . ", " . $txtAcct2 . ", '" . $cmbTag . "', '" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Product Reference successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strPT = "SELECT * FROM tblProdMast WHERE prdNumber = $prdNumber";
		$qryPT = mssql_query($strPT);
		
		if (mssql_num_rows($qryPT) > 0)
		{
			while ($rstPT = mssql_fetch_array($qryPT))
			{
				$txtprdNumber = $rstPT[0];
				$txtDesc = strtoupper($rstPT[1]);
				$txtDesc = str_replace("\\","",$txtDesc);
				$qryKO = mssql_query("SELECT * FROM tblVendorProduct WHERE suppCode = $Vref AND prdNumber = $prdNumber");
				$txtInit=mssql_result($qryKO,0,"suppProdNo"); 
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
          <?php echo ($_GET['maction'] == "edit") ? "<<< EDIT PRODUCT REFERENCE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT PRODUCT REFERENCE >>>" : "<<< ADD PRODUCT REFERENCE >>>"; ?> 
        </th>
      </tr>
      <tr> 
        <td width="167"><span class="style3">Product Code</span></td>
        <td width="423"> <span class="style3"> 
          <?php 
				if ($_GET['maction'] == "edit")
				{
			?>
          <input name='txtprdNumber' type='text' class='styleme' id='txtprdNumber' value='<?php echo $txtprdNumber;?>' size='10' maxlength="10" readonly='readonly'/>
          <?php 
				}
				else
				{
			?>
          <input name='txtprdNumber' type='text' class='styleme' id='txtprdNumber' value='<?php echo $txtprdNumber;?>' size='10' maxlength="10"/>
          <?php	
				}
			?>
          </span></td>
      </tr>
      <tr> 
        <td><span class="style3">Description</span></td>
        <td> <span class="style3"> 
          <input name='txtDesc' type='text' class='styleme' id='txtDesc' value='<?php echo $txtDesc;?>' size='40' maxlength="25" readonly="true"/>
          </span> </td>
      </tr>
      <tr> 
        <td><span class="style3">Vendor Product No.</span></td>
        <td><span class="style3">
          <input name='txtInit' type='text' class='styleme' id='txtInit' value='<?php echo $txtInit;?>' size='40' maxlength="20"/>
          </span></td>
      </tr>
	
	<td colspan="6" nowrap="nowrap" class="style3" align="center"> <span class="style3"> 
        <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validateCT(); ' <?php echo $lLock; ?> />
        </span> </td>
      <tr bgcolor="#6AB5FF"> 
        <th height="23" colspan="14" nowrap="nowrap" class="style6" align="right"> 
          <a href="vendor_reference_details.php?vNo=<? echo $Vref; ?>" title="Back to Product Reference List" >Back</a></th>
      </tr>
    </table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtprdNumber').focus();
	function validateCT() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txtprdNumber').value;
		var bDesc = window.document.getElementById('txtDesc').value;
		var bInit = window.document.getElementById('txtInit').value;
		var bPre = window.document.getElementById('txtPrecedence').value;
		var bAcct1 = window.document.getElementById('txtAcct1').value;
		var bAcct2 = window.document.getElementById('txtAcct2').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Price Type Code : Numbers only");
			window.document.getElementById('txtprdNumber').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Price Type Code is a required field >>>");
			window.document.getElementById('txtprdNumber').focus();
			return false;
		}
		if (bDesc == "") {
			window.alert("<<< Price Type Description is a required field >>>");
			window.document.getElementById('txtDesc').focus();
			return false;
		}
		if (bInit == "") {
			window.alert("<<< Initial Description is a required field >>>");
			window.document.getElementById('txtInit').focus();
			return false;
		}
		if (bPre == "") {
			window.alert("<<< Precedence is a required field >>>");
			window.document.getElementById('txtPrecedence').focus();
			return false;
		}
		if(!bPre.match(numeric_expression)) {
			alert("Precedence : Numbers only");
			window.document.getElementById('txtPrecedence').focus();
			return false;
		} 
		if(!bAcct1.match(numeric_expression) && bAcct1>"") {
			alert("Major GL Account : Numbers only");
			window.document.getElementById('txtAcct1').focus();
			return false;
		} 
		if(!bAcct2.match(numeric_expression) && bAcct2>"") {
			alert("Minor GL Account : Numbers only");
			window.document.getElementById('txtAcct2').focus();
			return false;
		}
		if (bAcct1=="") {
			window.document.getElementById('txtAcct1').value =0;
		}
		if (bAcct2=="") {
			window.document.getElementById('txtAcct2').value =0;
		} 
		if (bStatus == "") {
			window.alert("<<< Price Type status is a required field >>>");
			window.document.getElementById('cmbStatus').focus();
			return false;
		}
	}
</script>


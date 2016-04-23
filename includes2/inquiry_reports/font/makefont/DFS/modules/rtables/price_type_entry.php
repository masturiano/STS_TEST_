<?php
	//include("../../functions/inquiry_session.php");
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	
	$db = new DB;
	$db->connect();

	$gTag = $_GET['myTag'];	
	$prTypeCode = $_GET['prtypecode'];
	
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
			$UpdateSQL = "UPDATE tblPriceType SET ";
			$UpdateSQL .= "prTypeDesc = '".strtoupper($txtDesc)."', ";
			$UpdateSQL .= "prTypeInit = '".strtoupper($txtInit)."', ";
			$UpdateSQL .= "prPrecedence = ".$txtPrecedence.", ";
			$UpdateSQL .= "prAcct1 = ".$txtAcct1.", ";
			$UpdateSQL .= "prAcct2 = ".$txtAcct2.", ";
			$UpdateSQL .= "prStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE prTypeCode = " . $prTypeCode;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Price Type masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblPriceType WHERE prTypeCode = " . $txtPrTypeCode;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Price Type Masterfile exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblPriceType(prTypeCode, prTypeDesc, prTypeInit, prPrecedence, prAcct1, prAcct2, prStat)";
				$InsertSQL .= "VALUES (". $txtPrTypeCode. ", '" . strtoupper($txtDesc) . "', '" . strtoupper($txtInit) . "', " . $txtPrecedence . ", " . $txtAcct1 . ", " . $txtAcct2 . ", '" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Price Type successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strPT = "SELECT * FROM tblPriceType WHERE prTypeCode = $prTypeCode";
		$qryPT = mssql_query($strPT);
		
		if (mssql_num_rows($qryPT) > 0)
		{
			while ($rstPT = mssql_fetch_array($qryPT))
			{
				$txtPrTypeCode = $rstPT[0];
				$txtDesc = strtoupper($rstPT[1]);
				$txtInit = strtoupper($rstPT[2]);
				$txtPrecedence = $rstPT[3];
				$txtAcct1 = $rstPT[4];
				$txtAcct2 = $rstPT[5];
				$cmbStatus = $rstPT[6];
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
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT PRICE TYPE MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT PRICE TYPE MASTERFILE >>>" : "<<< ADD PRICE TYPE >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Price Type Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtPrTypeCode' type='text' class='styleme' id='txtPrTypeCode' value='<?php echo $txtPrTypeCode;?>' size='10' maxlength="2" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtPrTypeCode' type='text' class='styleme' id='txtPrTypeCode' value='<?php echo $txtPrTypeCode;?>' size='10' maxlength="2"/>
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
	    <td><span class="style3">Initial Desc</span></td>
	    <td><span class="style3"><input name='txtInit' type='text' class='styleme' id='txtInit' value='<?php echo $txtInit;?>' size='40' maxlength="5"/>  </span></td>
    </tr>
    <tr>
	    <td><span class="style3">Precedence</span></td>
	    <td><span class="style3"><input name='txtPrecedence' type='text' class='styleme' id='txtPrecedence' value='<?php echo $txtPrecedence;?>' size='40' maxlength="8"/>  </span></td>
    </tr>
     <tr>
	    <td><span class="style3">Major GL Account</span></td>
	    <td><span class="style3"><input name='txtAcct1' type='text' class='styleme' id='txtAcct1' value='<?php echo $txtAcct1;?>' size='40' maxlength="8"/>  </span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Minor GL Account</span></td>
	    <td><span class="style3"><input name='txtAcct2' type='text' class='styleme' id='txtAcct2' value='<?php echo $txtAcct2;?>' size='40' maxlength="8"/>  </span></td>
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
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validatePT(); ' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="price_type.php" title="Back to Buyers" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtPrTypeCode').focus();
	function validatePT() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txtPrTypeCode').value;
		var bDesc = window.document.getElementById('txtDesc').value;
		var bInit = window.document.getElementById('txtInit').value;
		var bPre = window.document.getElementById('txtPrecedence').value;
		var bAcct1 = window.document.getElementById('txtAcct1').value;
		var bAcct2 = window.document.getElementById('txtAcct2').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Price Type Code : Numbers only");
			window.document.getElementById('txtPrTypeCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Price Type Code is a required field >>>");
			window.document.getElementById('txtPrTypeCode').focus();
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


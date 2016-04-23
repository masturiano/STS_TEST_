<?php
	session_start();
	include ("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	
	$db = new DB;
	$db->connect();

	$gTag = $_GET['myTag'];	
	$buy_No = $_GET['buyNo'];
	
	$ArrStatus = array();
	$ArrStatus[] = "AxOxACTIVE";
	$ArrStatus[] = "DxOxDELETED";
	$art=$_GET['maction'];
	($_GET['maction'] == "view") ? $lLock = ' disabled="disabled" ' : $lLock = ' ';	
	if (isset($btnSubmit))
	{
		if ($_GET['maction'] == "edit" or $_GET['maction'] == "view")
		{
			$UpdateSQL = "UPDATE TBLBUYERS SET ";
			$UpdateSQL .= "BUYERCODE = '".strtoupper($txtBuyerCode)."', ";
			$UpdateSQL .= "BUYERNAME = '".strtoupper($txtBuyerName)."', ";
			$UpdateSQL .= "BUYEREMPNO = '".strtoupper($txtEmpNo)."', ";
			$UpdateSQL .= "BUYERADDR1 = '".strtoupper($txtAddr1)."', ";
			$UpdateSQL .= "BUYERADDR2 = '".strtoupper($txtAddr2)."', ";
			$UpdateSQL .= "BUYERADDR3 = '".strtoupper($txtAddr3)."', ";
			$UpdateSQL .= "BUYERTELNO = '".strtoupper($txtTelNo)."', ";
			$UpdateSQL .= "BUYERSTAT = '$cmbStatus' ";
			$UpdateSQL .= "WHERE BUYERCODE = '" . $buy_No . "'";

			mssql_query($UpdateSQL); 
			showmess("<<< Buyer profile successfully updated >>>");
			
		}
		else
		{
			$strSQL = "SELECT * FROM TBLBUYERS WHERE BUYERCODE = '" . $txtBuyerCode ."'";
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				showmess("<<< Buyer profile exists >>>");
			}
			else
			{			
				$InsertSQL = "INSERT INTO TBLBUYERS(BUYERCODE, BUYERNAME, BUYEREMPNO, BUYERADDR1, BUYERADDR2, BUYERADDR3, BUYERTELNO, BUYERSTAT)";
				$InsertSQL .= "VALUES('" . $txtBuyerCode . "', '" . strtoupper($txtBuyerName) . "', '" . $txtEmpNo . "', '" . strtoupper($txtAddr1) . "', ";
				$InsertSQL .= "'" . strtoupper($txtAddr2) . "', '" . strtoupper($txtAddr3) . "', '" . $txtTelNo . "', '" . $cmbStatus . "')";

				mssql_query($InsertSQL);
				showmess("<<< Buyer profile successfully saved >>>");
			}
		}
	}
	if ($_GET['maction'] == "edit")
	{
		$strBuyer = "SELECT * FROM TBLBUYERS WHERE BUYERCODE = '$buy_No' ORDER BY BUYERCODE";
		$qryBuyer = mssql_query($strBuyer);
		
		if (mssql_num_rows($qryBuyer) > 0)
		{
			while ($rstBuyer = mssql_fetch_array($qryBuyer))
			{
				$txtBuyerCode = $rstBuyer[0];
				$txtBuyerName = strtoupper($rstBuyer[1]);
				$txtEmpNo = $rstBuyer[2];
				$txtAddr1 = strtoupper($rstBuyer[3]);
				$txtAddr2 = strtoupper($rstBuyer[4]);
				$txtAddr3 = strtoupper($rstBuyer[5]);
				$txtTelNo = $rstBuyer[6];
				$cmbStatus = $rstBuyer[7];
			}
		}
	}
	
	if ($_GET['action'] == "new") {
		$resBuyers= mssql_query("SELECT * FROM tblBuyerCode");
		$num = mssql_num_rows($resBuyers);
		if ($num>0) {
			$buyer_no_orig=mssql_result($resBuyers,0,"lstBuyerCode");
			$buyer_no=$buyer_no_orig+1;
		} else {
			$buyer_no="1";
		}
		$txtBuyerCode= $buyer_no;
		$resBuyers= mssql_query("UPDATE tblBuyerCode SET lstBuyerCode = $buyer_no WHERE lstBuyerCode = $buyer_no_orig");
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
<script src="../../functions/prototype.js"></script>
</head>
<body onLoad="window.document.getElementById('txtEmpNo').focus();">
<center>
<form action="" method="post" name="myformko">
<table width="600" border="0" bgcolor="#DEEDD1">
	<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="center">
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT BUYER PROFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT BUYER PROFILE >>>" : "<<< ADD BUYER PROFILE >>>"; ?>        
        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Buyer Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtBuyerCode' type='text' class='styleme' id='txtBuyerCode' value='<?php echo $txtBuyerCode;?>' size='10' readonly='true'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtBuyerCode' type='text' class='styleme' id='txtBuyerCode' value='<?php echo $txtBuyerCode;?>' size='10' readonly="true"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Buyer Name</span></td>
	    <td>
            <span class="style3">
				<input name='txtBuyerName' type='text' class='styleme' id='txtBuyerName' value='<?php echo $txtBuyerName;?>' size='25' readonly="true"/>            
            </span>
        </td>
    </tr>
    <tr>
	    <td><span class="style3">Buyer's Employee No</span></td>
	    <td>
	        <span class="style3">
				<input type='text' class='styleme' onChange="val_emp_no()" name='txtEmpNo' id='txtEmpNo' size='10' value='<?php echo $txtEmpNo;?>'/>            
            </span>
        </td>
    </tr>
    <tr>
	    <td><span class="style3">Address 1</span></td>
	    <td>
        	<span class="style3">
				<input type='text' class='styleme' name='txtAddr1' id='txtAddr1' size='30' value='<?php echo $txtAddr1;?>'/>            
            </span>
        </td>
    </tr>
    <tr>
	    <td><span class="style3">Address 2</span></td>
	    <td>
            <span class="style3">
				<input type='text' class='styleme' name='txtAddr2' id='txtAddr2' size='30' value='<?php echo $txtAddr2;?>'/>            
            </span>
        </td>
    </tr>
    <tr>
	    <td><span class="style3">Address 3</span></td>
	    <td>
            <span class="style3">
				<input type='text' class='styleme' name='txtAddr3' id='txtAddr3' size='30' value='<?php echo $txtAddr3;?>'/>            
            </span>
        </td>
    </tr>
    <tr>
	    <td><span class="style3">Contact No</span></td>
	    <td>
        	<span class="style3">
				<input type='text' class='styleme' name='txtTelNo' id='txtTelNo' size='30' value='<?php echo $txtTelNo;?>'/>            
        	</span>
        </td>
    </tr>
    <tr>
	    <td><span class="style3">Buyer Status</span></td>
	    <td> <span class="style3"> 
          <select name="cmbStatus" id="cmbStatus">
            <option selected>
            <?
							 	if ($cmbStatus=='A' ||$cmbStatus=='') {
									echo "Active";
								} else {
									echo "Deleted";
								}
							 ?>
            </option>
            <option value="A">Active</option>
            <option value="D">Deleted</option>
          </select>
          </span> </td>
    </tr>
	<td colspan="6" nowrap="nowrap" class="style3" align="center">
        <span class="style3">
            <input name='btnSubmit' type='button' class='style3' value='Save Entry' <?php echo $lLock; ?> />
        </span>
    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="buyers.php" title="Back to Buyers" >Back</a>
        </th>
</tr>    
</table>
</form>
</center>
</body>
</html>

<script language="javascript" type="text/javascript">
	
	function val_emp_no() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var txtEmpNo = window.document.getElementById('txtEmpNo').value;
		var bName = window.document.getElementById('txtBuyerName').value;
		
		if(!txtEmpNo.match(numeric_expression) && txtEmpNo>"") {
			alert("Buyer's Employee No : Numbers only");
			window.document.getElementById('txtEmpNo').value="";
			window.document.getElementById('txtBuyerName').value="";
			return false;
		} 
		
		new Ajax.Request(
	      'get_user_id_ajax.php?do=getUser&type='+txtEmpNo,
	      {
	         asynchronous : true,     
	         onComplete   : function (req){
	            eval(req.responseText);
	         }
	      }
		);
		
		if (bName == "" || bName == " " ) {
			window.alert("<<< Buyer's name is a required field >>>");
			return false;
		}
	}
	function validateBuyer() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var txtEmpNo = window.document.getElementById('txtEmpNo').value;
		var bName = window.document.getElementById('txtBuyerName').value;
		
		if (txtEmpNo == "") {
			window.alert("<<< Employee No is a required field >>>");
			return false;
		}
		if(!txtEmpNo.match(numeric_expression) && txtEmpNo>"") {
			alert("Buyer's Employee No : Numbers only");
			window.document.getElementById('txtEmpNo').value="";
			window.document.getElementById('txtBuyerName').value="";
			return false;
		} 
		if (bName == "" || bName == " " ) {
			window.alert("<<< Buyer's name is a required field >>>");
			return false;
		}
		document.a.action="<?php echo $_SESSION['PHP_SELF']?>?action=submit";
		document.a.submit();
	}
</script>
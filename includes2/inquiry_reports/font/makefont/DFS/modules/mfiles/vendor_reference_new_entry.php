<?php
	$v_No = $_GET['vNo'];
	$v_Name = $_GET['vName'];
	
	require("../inventory/lbd_function.php");
	require("vendor_function.php");
		
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();

	$ArrType = array();
	$ArrType[] = "AxOxALTERNATE";

	$ArrAllowType = allowType();

	$QryVnd = "SELECT * FROM VIEWVENDORS WHERE SUPPCODE = '{$_GET['vNo']}'";
	$ResVnd = mssql_query($QryVnd);
	$rowVnd = mssql_fetch_array($ResVnd);
	
	
	($_GET['maction'] == "view") ? $lLock = ' disabled="disabled" ' : $lLock = ' ';	
	if (isset($btnSubmit))
	{
		if ($_GET['maction'] == "edit" or $_GET['maction'] == "view")
		{
		
		}
		else
		{
			$strProd = "SELECT * FROM TBLVENDORPRODUCT WHERE SUPPCODE = '$v_No' AND PRDNUMBER = '$txtSKU'";
			$qryProd = mssql_query($strProd);
			$ctr = mssql_fetch_row($qryProd);
			
			if ($ctr > 0)
			{
				showmess("<<< Product already referenced to this vendor >>>");
			}
			else
			{			
				$strVendor = "SELECT * FROM TBLSUPPLIERS WHERE SUPPCODE = '$v_No' AND SUPPSTAT = 'A'";
				$qryVendor = mssql_query($strVendor);
				$rstVendor = mssql_fetch_array($qryVendor);
				
				$Stat = $rstVendor[12];
				
				$InsertSQL = "INSERT INTO TBLVENDORPRODUCT (SUPPCODE, PRDNUMBER, VENDORTYPE, SUPPSTAT, SUPPPRODNO) ";
				$InsertSQL .= "VALUES('$v_No', '$txtSKU', 'A', '$Stat', '$txtInit')";
				
				mssql_query($InsertSQL);
				showmess("<<< Vendor Product reference profile successfully saved >>>");
			}
		}
	}
	if ($_GET['maction'] == "edit")
	{
		$strSKU = "SELECT * FROM TBLPRODMAST WHERE PRDNUMBER = '$skunum'";
		$qrySKU = mssql_query($strAllow);
		
		if (mssql_num_rows($strSKU) > 0)
		{
			while ($rstSKU = mssql_fetch_array($qrySKU))
			{
				$txtSKU = $rstSKU[0];
				$txtDesc = $rstSKU[1];
			}
		}
		
		
	}
?>

<?php

	
	if ($_GET['action'] == 'dovalidate') 
	{		
		$strSQL = "SELECT * FROM TBLPRODMAST WHERE PRDNUMBER = '$txtSKU'";
		$qrySQL = mssql_query($strSQL);
		$row = mssql_num_rows($qrySQL);
		if ($row > 0)
		{
			while ($rstSQL = mssql_fetch_array($qrySQL))
			{
				$txtDesc = $rstSQL[1];
			}
		}
		else
		{
			showmess("<<< SKU is not existing >>>");
			$txtDesc = "";
		}
	}
?>
<html>
<head>
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
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
</style>
<body>
<div class="style6">
<center>
<form name="frmBuyer" method="post">
<table width="700" border="0" bgcolor="#DEEDD1">
	<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="center">
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT ITEM ALLOWANCE PROFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT ITEM ALLOWANCE PROFILE >>>" : "<<< ADD PRODUCT REFERENCE PROFILE >>>"; 
		$cmbType = "P"; ?>        
        </th>
	</tr>
<tr>
<th align="left" colspan="14" bgcolor="#F2FEFF"><font color="#FF0000" size="+1">Vendor :  <? echo $rowVnd[0]." - ".$rowVnd[1];?></font>
</th>
    
    <tr>
	    <td width="122"><span class="style3">SKU Number</span></td>
	    <td width="368">
        	<span class="style3">
				<input type='text' class='styleme' name='txtSKU' id='txtSKU' size='12' value='<?php echo $txtSKU;?>' onfocus="ValidateSKU('<?=$v_No?>')"/>        
				<img src="../images/search.gif" title="Vendor LookUp" style="cursor:pointer;" onclick="fgetVnum()"/>
				<font size="1"><b><i><a href="#" title="Vendor Look Up" style="cursor:pointer;" onclick="fgetVnum()"/>Look Up</a></i></b></font> 
        	</span>
        </td>
    </tr>
    <tr>
	    <td width="122"><span class="style3">SKU Description</span></td>
	    <td width="368">
		<span class="style3"> 
		<input type='text' class='styleme' name='txtDesc' id='txtDesc' size='25' value="<?php echo htmlspecialchars(stripslashes($txtDesc));?>" readonly='readonly'/>        
		</span>        
        </td>
    </tr>   
	
	<tr>
	    <td width="122"><span class="style3">Vendor Product No.</span></td>
	    <td width="368">
		<span class="style3"> 
		<input name='txtInit' type='text' class='styleme' id='txtInit' value="<? echo $txtInit; ?>" size='25' maxlength="20" />        
		</span>        
        </td>
    </tr>
	 
    <tr>
	<td colspan="6" nowrap="nowrap" class="style3" align="center">
        <span class="style3">
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return ValidateReference(); ' <?php echo $lLock; ?> />
        </span>
    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="vendor_reference_details.php?vNo=<?php echo $_GET['vNo']; ?>&vName=<?php echo stripslashes($_GET['vName']); ?>" title="Back to Buyers" >Back</a>
        </th>
</tr>    
    
</table>
<input type="hidden" name="hdnVnd" id="hdnVnd" value="<?=$_GET['v_num']?>">
</form>
</center>
</div>
</body>
</head>
</html>

<script language="javascript" type="text/javascript">
	
	function ValidateSKU(vno)
	{
		varSKU  = window.document.getElementById('txtSKU').value;
		if (varSKU == "")
		{
			window.alert("<<< SKU is a required field >>>");
			window.document.getElementById('txtSKU').focus();
			return false;
		}
		else{
			document.frmBuyer.action='<?=$_SERVER['PHP_SELF']?>?action=dovalidate&vNo='+vno;
			document.frmBuyer.submit();
		}
	}

	function ValidateReference()
	{
		varSKU  = window.document.getElementById('txtSKU').value;
		if (varSKU == "")
		{
			window.alert("<<< SKU is a required field >>>");
			window.document.getElementById('txtSKU').focus();
			return false;
		}
		varType = window.document.getElementById('cmbType').value;
		if (varType == "")
		{
			window.alert("<<< Vendor type is a required field >>>");
			window.document.getElementById('cmbType').focus();
			return false;
		}
	}
	
	
	function fgetVnum(){
			window.open('product_lookup.php','','scrollbars=yes, width=600, height=400, left=200,top=200');	
	}
</script>

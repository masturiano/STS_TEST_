<?php
	
	//include("../../functions/inquiry_session.php");
	session_start();
	
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../inventory/lbd_function.php");
	require_once "../../modules/etc/etc.obj.php";
	require_once "../../functions/inquiry_function.php";
	$etcTrans = new etcObject;
	
	$db = new DB;
	$db->connect();
	$company_code = $_SESSION['comp_code'];

	$gTag = $_GET['myTag'];	
	$custCode = $_GET['custCode'];
	
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
		
		####################### dept code ###########
			$UpdateSQL = "UPDATE tblCustMast SET ";
			$UpdateSQL .= "custName = '".$txtName."', ";
			$UpdateSQL .= "custAddr1 = '".strtoupper($txtAddr1)."', ";
			$UpdateSQL .= "custAddr2 = '".strtoupper($txtAddr2)."', ";
			$UpdateSQL .= "custAddr3 = '".strtoupper($txtAddr3)."', ";
			$UpdateSQL .= "custTel = '".strtoupper($txtTel)."', ";
			$UpdateSQL .= "custFax = '".strtoupper($txtFax)."', ";
			$UpdateSQL .= "custTerms = ".$txtTerm.", ";
			$UpdateSQL .= "custTaxId = '".strtoupper($txtTaxId)."', ";
			$UpdateSQL .= "custType = '".strtoupper($cmbType)."', ";
			$UpdateSQL .= "custCredit = ".$txtCredit.", ";
			$UpdateSQL .= "lastInvDate = '".strtoupper($txtInvDate)."', ";
			$UpdateSQL .= "custStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE custCode = " . $custCode;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Customer masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblCustMast WHERE custCode = " . $txtcustCode;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Customer Masterfile exists >>>')</script>";
			}
			else
			{			
				if ($txtInvDate=="") {
					$InsertSQL = "INSERT INTO tblCustMast(compCode, custCode, custName, custAddr1, custAddr2, custAddr3, custTel, custFax, custTerms, custTaxId, custType, custCredit, custStat)";
					$InsertSQL .= " VALUES (". $cmbCompany. ",". $txtcustCode. ",'" . strtoupper($txtName) . "','" . strtoupper($txtAddr1) . "','" . strtoupper($txtAddr2) . "','" . strtoupper($txtAddr3) . "','" . strtoupper($txtTel) . "','" . strtoupper($txtFax) . "'," . $txtTerm . ",'" . $txtTaxId . "','" . $cmbType . "'," . $txtCredit . ",'" . $cmbStatus . "')";
				} else {
					$InsertSQL = "INSERT INTO tblCustMast(compCode, custCode, custName, custAddr1, custAddr2, custAddr3, custTel, custFax, custTerms, custTaxId, custType, custCredit, lastInvDate, custStat)";
					$InsertSQL .= " VALUES (". $cmbCompany. ",". $txtcustCode. ",'" . strtoupper($txtName) . "','" . strtoupper($txtAddr1) . "','" . strtoupper($txtAddr2) . "','" . strtoupper($txtAddr3) . "','" . strtoupper($txtTel) . "','" . strtoupper($txtFax) . "'," . $txtTerm . ",'" . $txtTaxId . "','" . $cmbType . "'," . $txtCredit . ",'" . $txtInvDate . "','" . $cmbStatus . "')";	
				}
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Customer successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strLoc = "SELECT * FROM tblCustMast WHERE custCode = $custCode";
		$qryLoc = mssql_query($strLoc);
		if (mssql_num_rows($qryLoc) > 0)
		{
			while ($rstLOc = mssql_fetch_array($qryLoc))
			{
				$txtcustCode = $rstLOc[1];
				$txtName= strtoupper($rstLOc[2]);
				$txtAddr1 = strtoupper($rstLOc[3]);
				$txtAddr2 = strtoupper($rstLOc[4]);
				$txtAddr3 = strtoupper($rstLOc[5]);
				$txtTel = strtoupper($rstLOc[6]);
				$txtFax = strtoupper($rstLOc[7]);
				$txtTerm = $rstLOc[8];
				$txtTaxId = strtoupper($rstLOc[9]);
				$cmbType = $rstLOc[10];
				$txtCredit = $rstLOc[11];
				$txtInvDate = $rstLOc[12];
				if (!$txtInvDate) {
					$txtInvDate = "";
				} else {
					$txtInvDate = new DateTime( $txtInvDate);
					$txtInvDate = $txtInvDate->format("m/j/Y");
				}
				$cmbStatus = $rstLOc[13];
				$cmbCompany = $rstLOc[0];
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
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT CUSTOMER MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT CUSTOMER MASTERFILE >>>" : "<<< ADD CUSTOMER >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Customer Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtcustCode' type='text' class='styleme' id='txtcustCode' value='<?php echo $txtcustCode;?>' size='10' maxlength="8" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtcustCode' type='text' class='styleme' id='txtcustCode' value='<?php echo $txtcustCode;?>' size='10' maxlength="8"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Name</span></td>
	    <td>
            <span class="style3">
			<? //$txtName = "arthur\'\'macadini";  ?>
				<input name='txtName' type='text' class='styleme' id='txtName' value="<?php echo $txtName;?>" size='40' maxlength="50" onChange="val_quotes();"/>            
            </span>        </td>
    </tr>
	<tr>
	    <td><span class="style3">Address 1</span></td>
	    <td><span class="style3"><input name='txtAddr1' type='text' class='styleme' id='txtAddr1' value='<?php echo $txtAddr1;?>' size='40' maxlength="50"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Address 2</span></td>
	    <td><span class="style3"><input name='txtAddr2' type='text' class='styleme' id='txtAddr2' value='<?php echo $txtAddr2;?>' size='40' maxlength="50"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Address 3</span></td>
	    <td><span class="style3"><input name='txtAddr3' type='text' class='styleme' id='txtAddr3' value='<?php echo $txtAddr3;?>' size='40' maxlength="50"/>  </span></td>
    </tr>
 	<tr>
	    <td><span class="style3">Contact No</span></td>
	    <td><span class="style3"><input name='txtTel' type='text' class='styleme' id='txtTel' value='<?php echo $txtTel;?>' size='40' maxlength="50"/>  </span></td>
    </tr>
    <tr>
	    <td><span class="style3">Fax No</span></td>
	    <td><span class="style3"><input name='txtFax' type='text' class='styleme' id='txtFax' value='<?php echo $txtFax;?>' size='40' maxlength="15"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Terms</span></td>
	    <td><span class="style3"><input name='txtTerm' type='text' class='styleme' id='txtTerm' value='<?php echo $txtTerm;?>' size='40' maxlength="8"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Tax ID</span></td>
	    <td><span class="style3"><input name='txtTaxId' type='text' class='styleme' id='txtTaxId' value='<?php echo $txtTaxId;?>' size='40' maxlength="50"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Type</span></td>
	    <td><span class="style3"> 
          <select name="cmbType" id="cmbType">
		  	<option selected><?php 
								if ($cmbType=="") {
									$cmbType ="RG";
								}
								echo $cmbType;
							 ?></option>
            <option value="RG">RG</option>
            <option value="DF">DF</option>
			<option value="KA">KA</option>
          </select>
          </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Credit</span></td>
	    <td><span class="style3"><input name='txtCredit' type='text' class='styleme' id='txtCredit' value='<?php echo $txtCredit;?>' size='40' maxlength="9"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Inventory Date</span></td>
	    <td><span class="style3">
          <input name='txtInvDate' type='text' class='styleme' id='txtInvDate' value='<?php echo $txtInvDate;?>' size='40' maxlength="15" readonly="true"/>
          <font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:void(0)" onClick="showCalendar(myform.txtInvDate,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a></font> 
          </span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Status</span></td>
	    <td><span class="style3"> 
          <select name="cmbStatus" id="cmbStatus">
		  	<option selected><?
							 	if ($cmbStatus=='A' ||$cmbStatus=='') {
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
	<tr>
	    <td><span class="style3">Company</span></td>
	    <td><span class="style3"> 
          <select name="cmbCompany" id="cmbCompany">	
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
            <input name='btnSubmit' type='submit' class='style3' onClick='return validateCust(); ' value='Save Entry' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="customer.php" title="Back to Customer Masterfile" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtcustCode').focus();
	function validateCust() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txtcustCode').value;
		var bName = window.document.getElementById('txtName').value;
		var bAddr1 = window.document.getElementById('txtAddr1').value;
		var bAddr2 = window.document.getElementById('txtAddr2').value;
		var bAddr3 = window.document.getElementById('txtAddr3').value;
		var bTel = window.document.getElementById('txtTel').value;
		var bFax = window.document.getElementById('txtFax').value;
		var bTerm = window.document.getElementById('txtTerm').value;
		var bTaxId = window.document.getElementById('txtTaxId').value;
		var bType = window.document.getElementById('cmbType').value;
		var bCredit = window.document.getElementById('txtCredit').value;
		var bInvDate = window.document.getElementById('txtInvDate').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Customer Code : Numbers only");
			window.document.getElementById('txtcustCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Customer Code is a required field >>>");
			window.document.getElementById('txtcustCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Customer Name is a required field >>>");
			window.document.getElementById('txtName').focus();
			return false;
		}
		if (bAddr1=="") {
			window.document.getElementById('txtAddr1').value =" ";
		}
		if (bAddr2=="") {
			window.document.getElementById('txtAddr2').value =" ";
		} 
		if (bAddr3=="") {
			window.document.getElementById('txtAddr3').value =" ";
		}
		if (bTel=="") {
			window.document.getElement
			window.document.getElementById('txtTel').value =" ";
		}
		if (bFax=="") {
			window.document.getElementById('txtFax').value =" ";
		} 
		if(!bTerm.match(numeric_expression) && bTerm>"") {
			alert("Customer Terms : Numbers only");
			window.document.getElementById('txtTerm').focus();
			return false;
		} 
		if (bTerm=="") {
			window.document.getElementById('txtTerm').value =0;
		} 
		if (bTaxId=="") {
			window.document.getElementById('txtTaxId').value =" ";
		} 
		if(!bCredit.match(numeric_expression) && bCredit>"") {
			alert("Customer Credit : Numbers only");
			window.document.getElementById('txtCredit').focus();
			return false;
		} 
		if (bCredit=="") {
			window.document.getElementById('txtCredit').value =0;
		}  
	}
function val_quotes() {
	var bName = window.document.getElementById('txtName').value;
	var split_bName = bName.split(/\'/);
	var count = split_bName.length
 	//var new_bName = "\'";
	var new_bName = "";
	var ctr = count-1;
	var i=0;
		
	for (i=0; i<count; i++) {
		new_bName = new_bName+split_bName[i];
		if (split_bName[i]!="" && i<ctr) {
			new_bName=new_bName+"\'\'";
		}
		if (i==ctr){
			//new_bName=new_bName+"\'";
		}
	}
	
	window.document.getElementById('txtName').value=new_bName;
}

function output_quotes() {
	alert('$art');
}
</script>



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
	$locCode = $_GET['locCode'];
	
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
			$UpdateSQL = "UPDATE tblLocation SET ";
			$UpdateSQL .= "locName = '".strtoupper($txtName)."', ";
			$UpdateSQL .= "locShortName = '".strtoupper($txtShort)."', ";
			$UpdateSQL .= "locAddr1 = '".strtoupper($txtAddr1)."', ";
			$UpdateSQL .= "locAddr2 = '".strtoupper($txtAddr2)."', ";
			$UpdateSQL .= "locAddr3 = '".strtoupper($txtAddr3)."', ";
			$UpdateSQL .= "locZip = ".$txtZip.", ";
			$UpdateSQL .= "locAcct = '".$txtAcct."', ";
			$UpdateSQL .= "locType = '".strtoupper($cmbType)."', ";
			$UpdateSQL .= "locStat = '".strtoupper($cmbStatus)."' ";
			$UpdateSQL .= "WHERE locCode = " . $locCode;

			mssql_query($UpdateSQL); 
			echo "<script>alert('<<< Location masterfile successfully updated >>>')</script>";
		}
		else
		{
			$strSQL = "SELECT * FROM tblLocation WHERE locCode = " . $txtlocCode;
			$qrySQL = mssql_query($strSQL);
			$ctr = mssql_fetch_row($qrySQL);
			if ($ctr)
			{
				echo "<script>alert('<<< Location Masterfile exists >>>')</script>";
			}
			else
			{			
				$InsertSQL = "INSERT INTO tblLocation(compCode, locCode, locName, locShortName, locAddr1, locAddr2, locAddr3, locZip, locAcct, locType, locStat)";
				$InsertSQL .= "VALUES (". $cmbCompany. ", ". $txtlocCode. ", '" . strtoupper($txtName) . "', '" . strtoupper($txtShort) . "', '" . strtoupper($txtAddr1) . "', '" . strtoupper($txtAddr2) . "', '" . strtoupper($txtAddr3) . "'," . $txtZip . ",'" . strtoupper($txtAcct) . "', '" . $cmbType . "', '" . $cmbStatus . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Location successfully saved >>>')</script>";
			}
		}
	}
	
	if ($_GET['maction'] == "edit")
	{
		$strLoc = "SELECT * FROM tblLocation WHERE locCode = $locCode";
		$qryLoc = mssql_query($strLoc);
		
		if (mssql_num_rows($qryLoc) > 0)
		{
			while ($rstLOc = mssql_fetch_array($qryLoc))
			{
				$txtlocCode = $rstLOc[1];
				$txtName= strtoupper($rstLOc[2]);
				$txtShort = strtoupper($rstLOc[3]);
				$txtAddr1 = strtoupper($rstLOc[4]);
				$txtAddr2 = strtoupper($rstLOc[5]);
				$txtAddr3 = strtoupper($rstLOc[6]);
				$txtZip = $rstLOc[7];
				$txtAcct = strtoupper($rstLOc[8]);
				$cmbType = $rstLOc[9];
				$cmbStatus = $rstLOc[10];
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
		<?php echo ($_GET['maction'] == "edit") ? "<<< EDIT LOCATION MASTERFILE >>>" : ($_GET['maction'] == "view") ? "<<< EDIT LOCATION MASTERFILE >>>" : "<<< ADD LOCATION >>>"; ?>        </th>
	</tr>
    <tr>
	    <td width="167"><span class="style3">Location Code</span></td>
	    <td width="423">
        <span class="style3">
        	<?php 
				if ($_GET['maction'] == "edit")
				{
			?>
					<input name='txtlocCode' type='text' class='styleme' id='txtlocCode' value='<?php echo $txtlocCode;?>' size='10' maxlength="8" readonly='readonly'/>
            <?php 
				}
				else
				{
			?>
					<input name='txtlocCode' type='text' class='styleme' id='txtlocCode' value='<?php echo $txtlocCode;?>' size='10' maxlength="8"/>
			<?php	
				}
			?>
		</span></td>
    </tr>
    <tr>
	    <td><span class="style3">Name</span></td>
	    <td>
            <span class="style3">
				<input name='txtName' type='text' class='styleme' id='txtName' value='<?php echo $txtName;?>' size='40' maxlength="25"/>            
            </span>        </td>
    </tr>
    <tr>
	    <td><span class="style3">Short Name</span></td>
	    <td><span class="style3"><input name='txtShort' type='text' class='styleme' id='txtShort' value='<?php echo $txtShort;?>' size='40' maxlength="10"/>  </span></td>
    </tr>
    <tr>
	    <td><span class="style3">Address 1</span></td>
	    <td><span class="style3"><input name='txtAddr1' type='text' class='styleme' id='txtAddr1' value='<?php echo $txtAddr1;?>' size='40' maxlength="25"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Address 2</span></td>
	    <td><span class="style3"><input name='txtAddr2' type='text' class='styleme' id='txtAddr2' value='<?php echo $txtAddr2;?>' size='40' maxlength="25"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Address 3</span></td>
	    <td><span class="style3"><input name='txtAddr3' type='text' class='styleme' id='txtAddr3' value='<?php echo $txtAddr3;?>' size='40' maxlength="25"/>  </span></td>
    </tr>
 	<tr>
	    <td><span class="style3">Zip Code</span></td>
	    <td><span class="style3"><input name='txtZip' type='text' class='styleme' id='txtZip' value='<?php echo $txtZip;?>' size='40' maxlength="8"/>  </span></td>
    </tr>
     <tr>
	    <td><span class="style3">GL Account</span></td>
	    <td><span class="style3"><input name='txtAcct' type='text' class='styleme' id='txtAcct' value='<?php echo $txtAcct;?>' size='40' maxlength="4"/>  </span></td>
    </tr>
	<tr>
	    <td><span class="style3">Type</span></td>
	    <td><span class="style3"> 
          <select name="cmbType" id="cmbType">
		  	<option selected><?php 
								if ($cmbType=="") {
									$cmbType ="W";
								}
								echo $cmbType;
							 ?></option>
            <option value="W">W</option>
            <option value="S">S</option>
          </select>
          </span></td>
    </tr>
	 <tr>
	    <td><span class="style3">Status</span></td>
	    <td><span class="style3"> 
          <select name="cmbStatus" id="cmbStatus">
		  	<option selected><?
							 	if ($cmbStatus=='A' ||$cmbStatus=='') {
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
            <input name='btnSubmit' type='submit' class='style3' value='Save Entry' onClick='return validateLoc(); ' <?php echo $lLock; ?> />
        </span>    </td>
<tr bgcolor="#6AB5FF">
		<th height="23" colspan="14" nowrap="nowrap" class="style6" align="right">
        <a href="location.php" title="Back to Location" >Back</a></th>
</tr>    
</table>
</form>
</center>
</body>
</html>
<script language="javascript" type="text/javascript">
	window.document.getElementById('txtlocCode').focus();
	function validateLoc() {
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		var bCode = window.document.getElementById('txtlocCode').value;
		var bName = window.document.getElementById('txtName').value;
		var bShort = window.document.getElementById('txtShort').value;
		var bAddr1 = window.document.getElementById('txtAddr1').value;
		var bAddr2 = window.document.getElementById('txtAddr2').value;
		var bAddr3 = window.document.getElementById('txtAddr3').value;
		var bZip = window.document.getElementById('txtZip').value;
		var bAcct = window.document.getElementById('txtAcct').value;
		var bType = window.document.getElementById('cmbType').value;
		var bStatus = window.document.getElementById('cmbStatus').value;
		var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
		if(!bCode.match(numeric_expression)) {
			alert("Location Code : Numbers only");
			window.document.getElementById('txtlocCode').focus();
			return false;
		} 
		if (bCode == "") {
			window.alert("<<< Location Code is a required field >>>");
			window.document.getElementById('txtlocCode').focus();
			return false;
		}
		if (bName == "") {
			window.alert("<<< Location Name is a required field >>>");
			window.document.getElementById('txtName').focus();
			return false;
		}
		if (bShort == "") {
			window.alert("<<< Location Short Name is a required field >>>");
			window.document.getElementById('txtShort').focus();
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
		if(!bZip.match(numeric_expression) && bZip>"") {
			alert("Zip Code : Numbers only");
			window.document.getElementById('txtZip').focus();
			return false;
		} 
		if (bZip=="") {
			window.document.getElementById('txtZip').value =0;
		} 
		if (bAcct=="") {
			window.document.getElementById('txtAcct').value =" ";
		}  
	}
</script>



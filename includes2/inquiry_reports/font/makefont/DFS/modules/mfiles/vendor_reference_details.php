<?php
	session_start();
	
	$v_No = $_GET['vNo'];
	$v_Name = $_GET['vName'];
	$v_Type = $_GET['vType'];
 
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();

	if($_GET['action'] == 'deleVndPrd'){
		$qryDeleVndPrd = "DELETE FROM tblVendorProduct WHERE suppCode = '{$v_No}' AND prdNumber = '{$_GET['prdNum']}'";
		$resDeleVndPrd = mssql_query($qryDeleVndPrd);
		echo "<script>alert('Vendor Product Successfully Deleted');</script>";
		echo "<script>location.href='{$_SERVER['PHP_SELF']}?vNo={$_GET['vNo']}';</script>";
	}
	
	
	$QryVnd = "SELECT * FROM VIEWVENDORS WHERE SUPPCODE = '{$_GET['vNo']}'";
	$ResVnd = mssql_query($QryVnd);
	$rowVnd = mssql_fetch_array($ResVnd);
	
	if($rowVnd[9] == "CO"){
		$vndtype = "CONSESSIONAIRE";
	}
	elseif ($rowVnd[9] == "CG"){
		$vndtype = "CONSIGNMENT";
	}
	elseif ($rowVnd[9] == "RG"){
		$vndtype = "REGULAR";
	}else {
		$vndtype = "---";
	}
	
	 $qryVendorRefList = "SELECT vp.suppCode, vp.prdNumber, vp.vendorType, vp.suppStat, pm.prdNumber, pm.prdDesc FROM 
					 	  tblVendorProduct as vp LEFT JOIN tblProdMast as pm ON vp.prdNumber = pm.prdNumber "; 
	 
		 if(!empty($v_No)){
		 	$qryVendorRefList .= "WHERE vp.suppCode = '$v_No' ";
		 }
		 else{
		 	$qryVendorRefList .= "WHERE vp.suppCode = '$v_No' ";
		 }
		 
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'PRODUCT NUMBER'){	
			    $qryVendorRefList .= " AND vp.prdNumber LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryVendorRefList .= " AND pm.prdDesc LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$resVendorRefList = $db->query($qryVendorRefList);
	$procquery = $db->getArrResult();
	
	$Paper = 'letter';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
</style>
</head>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<body>
<div class="style6" style="overflow: auto; height: 500px;">
<center>
<form name="frmBuyer" method="post" action="<? echo $_SERVER['PHP_SELF']?>">
<table width="626" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
<th height="23" colspan="14" nowrap="nowrap" class="style6">VENDOR PRODUCT DETAILS</th>
</tr>
<tr>
<th align="left" colspan="14" bgcolor="#F2FEFF"><font color="#FF0000" size="+1">Vendor : <?php echo $rowVnd[0]." - ".$rowVnd[1]; ?></font>
</th>
</tr>
<tr>
<th align="left" colspan="14" bgcolor="#F2FEFF"><font color="#FF0000" size="+1">Vendor Type: <?php echo $vndtype; ?></font>
</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
          <th height="23" colspan="14" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
            <a href="vendor_reference_new_entry.php?maction=add&vNo=<?php echo $v_No; ?>&vName=<?php echo stripslashes($rowVnd[1]);?>"> 
            New Entry </a> ||<img src="../images/search.gif" onclick="fSearch('<?=$v_No?>','<?=$v_Name?>','<?=$v_Type?>');" title="Search"/> 
            <a onclick="fSearch('<?=$v_No?>','<?=$v_Name?>','<?=$v_Type?>')" title="Search">Search</a> 
            <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
		<select name="cmbSearch" id="cmbSearch">
			<option>PRODUCT NUMBER</option>
			<option>PRODUCT DESC</option>
		</select>
	<a href='<? echo $_SERVER['PHP_SELF']; ?>?vNo=<?=$v_No?>&vName=<?=$v_Name?>'>Reload</a>
    ||
    <a href="vendor_reference.php">
    Back
    </a>
    </th>
</tr>
<tr bgcolor="#DEEDD1">
    <th width="75">Product No</th>
    <th width="396">Product Description</th>
          <th width="76" align="center">Ref. Type</th>
    <th width="61" align="center">Action</th>
</tr>

<?php 
	$i = 0;
	$mTag = 2;
	foreach($procquery as $ctr)
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> style="cursor:pointer;" title="click to view Vendor List">
        <td onclick="window.open('vendor_product_List.php?prodNum=<?=$ctr['prdNumber'];?>&prdDesc=<?=$ctr['prodDesc'];?>','','scrollbar=yes, width=400, height=400, left=300,top=200')"><a ><?php echo $ctr['prdNumber']; ?></a></td>
		<td onclick="window.open('vendor_product_List.php?prodNum=<?=$ctr['prdNumber'];?>&prdDesc=<?=$ctr['prodDesc'];?>','','scrollbar=yes, width=400, height=400, left=300,top=200')"><a ><?php echo htmlspecialchars(stripslashes($ctr['prdDesc'])); ?></a></td>
		<td align="left" onclick="window.open('vendor_product_List.php?prodNum=<?=$ctr['prdNumber'];?>&prdDesc=<?=$ctr['prodDesc'];?>','','scrollbar=yes, width=400, height=400, left=300,top=200')">
			<?php 
				if (trim($ctr['vendorType']) == 'P')
				{
					$PType = "PRIMARY";
				}
				else
				{
					$PType = "ALTERNATE";
				}
				echo $PType;
			?>
		</td>   
		  <td align="center"><a href="vendor_reference_details_edit.php?maction=edit&prdNumber=<?php echo $ctr['prdNumber']; ?>&Vref=<?=$rowVnd[0]?> "><img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Product Reference"  align="absbottom"/></a> 
            <img name="imgDelete" src="../images/mydelete.gif"  title="Delete Vendor Prodcust" onclick="deleVndPrd('<?=$v_No?>','<?=$ctr['prdNumber']?>')"> 
          </td>
	  </tr>
	<?php 
	}
	$i++;
?>
</table>
<input type="hidden" name="hdnVName" id="hdnVName" value="<?=$v_Name?>">
</form>
</center>
</div>
</body>
</html>
<script>
		function fSearch(v_no,vName,vType){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmBuyer.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch&vNo="+v_no+"&vName="+vName+"&vType="+vType;
		document.frmBuyer.submit();
		}
	}
	
	function deleVndPrd(vNo,prdNo){
		vName = document.getElementById('hdnVName').value;
		deleVndPrd = confirm("Do You Want To Delete this Product");
		if(deleVndPrd == true){
			document.frmBuyer.action="<?php $_SERVER['PHP_SELF']?>?action=deleVndPrd&vNo="+vNo+"&vName="+vName+"&prdNum="+prdNo;
			document.frmBuyer.submit();		
		}
	}
</script>
<?
	session_start();
	require("../inventory/lbd_function.php");
	require("vendor_function.php");
		
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$compCode = $_SESSION['comp_code'];
	$db = new DB;
	$db->connect();
	switch ($_GET['action']){
		case 'DeleParentPrd':
			$qryDeleteSetPrd = "DELETE FROM tblMakeupTrans WHERE compCode = '{$compCode}' AND prdParent = '{$_GET['SKUPrnt']}' AND prdChild = '{$_GET['SKUChld']}'";
			$resDeleteSetPrd = mssql_query($qryDeleteSetPrd);
			
			$qryDeleteSetPrdchld = "DELETE FROM tblMakeupTrans WHERE compCode = '{$compCode}' AND prdParent = '{$_GET['SKUPrnt']}' ";
			$resDeleteSetPrdchld = mssql_query($qryDeleteSetPrdchld);
		break;
	}
	
	$qrySetList = "SELECT mk.compCode, mk.prdParent, mk.prdChild, mk.mkUCost, mk.mkRegPrice, mk.MkQuantity, mk.mkDate, mk.mkStartDate, mk.mkEndDate, pm.prdNumber, pm.prdDesc 
						FROM tblMakeuptrans as mk LEFT JOIN tblProdMast as pm 
					ON mk.prdParent = pm.prdNumber WHERE mk.prdChild = '0' ";
	$qrySetList2 = "SELECT mk.compCode, mk.prdParent, mk.prdChild, mk.mkUCost, mk.mkRegPrice, mk.MkQuantity, mk.mkDate, mk.mkStartDate, mk.mkEndDate, pm.prdNumber, pm.prdDesc, pm.prdSellUnit 
						FROM tblMakeuptrans as mk LEFT JOIN tblProdMast as pm 
					ON mk.prdParent = pm.prdNumber WHERE mk.prdChild > -1 ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'Parent SKU'){	
				$qrySetList .= " AND prdParent LIKE '{$_POST['txtSearch']}%' ";
				$qrySetList2 .= " AND prdParent LIKE '{$_POST['txtSearch']}%' ORDER BY mk.prdParent, mk.prdChild,pm.prdDesc ASC";
				
			}
			if ($_POST[cmbSearch] == 'Parent DESCRIPTION'){
				$qrySetList .= " AND prdDesc LIKE '{$_POST['txtSearch']}%' ";
				$qrySetList2 .= " AND prdParent LIKE '{$_POST['txtSearch']}%' ORDER BY mk.prdParent, mk.prdChild,pm.prdDesc ASC";
			}
		}
	}
	$resSetList = mssql_query($qrySetList);
	$cntSetList = mssql_num_rows($resSetList);
?>
<html>
	<head><title>Makeup / Breakup Translation</title>
	<style type="text/css">
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
	</style>
	</head>
	<body>
	<form name='frmMakeup' id="frmMakeup" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<table border="0" align="center" id="tblSetMstrLst" width="100%">
			<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
			<th height="23" colspan="14" nowrap="nowrap" class="style6">MAKEUP / BREAKUP MAINTENANCE</th>
			</tr>
			<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
				
      <th height="23" colspan="14" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
        <a href="makeup_entry_parent.php?trns=add"> New </a> ||<span class="style6"><img src="../images/s_f_prnt.gif" /> 
        <a href="makeup_pdf.php?search_query=<?php echo $qrySetList2; ?> &search_selection=all_record" target="_blank"> 
        Print</a></span> <img src="../images/search.gif" onclick="fSearch()" title="Search" /> 
        <a onclick="fSearch()" title="Search">Search</a> 
        <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
					<select name="cmbSearch" id="cmbSearch">
						<option>Parent SKU</option>
						<option>Parent DESCRIPTION</option>
					</select>
					<a href='<? echo $_SERVER['PHP_SELF']; ?>'>Reload</a>
				</th>
			</tr>
			<tr bgcolor="#DEEDD1" align="center">
				<td class="style3">
					<font class="hdrVnd">Company Code</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Parent SKU</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Parent Description</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Child Items</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Total Cost</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Total Price</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Date Makeup</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">Start Date</font>
				</td>
				<td align="center" class="style3">
					<font class="hdrVnd">End Date</font>
				</td>
				<td align="center" colspan="3" class="style3">
					<font class="hdrVnd">Action</font>
				</td>
			</tr>
			<?$i=0; 
			while($rowSetList = mssql_fetch_array($resSetList)){ 
		 	$sDdate = date('m/d/Y',strtotime($rowSetList[7]));
		 	$sdate = explode("/",$sDdate);
		 	
		 	$eDate = date('m/d/Y',strtotime($rowSetList[8]));
		 	$edate = explode("/",$eDate);
		 			 	
		 	$qryprdChildsum = "SELECT SUM(MkQuantity)as prd_child_sum, SUM(mkUCost) as prd_child_cost, SUM(mkRegPrice) as prd_child_price FROM ViewMakeupTrans WHERE compCode = '{$compCode}' AND prdParent = '{$rowSetList[1]}' AND prdChild <> '0'";
		 	$resprdChildsum = mssql_query($qryprdChildsum);
		 	$rowprdChildsum = mssql_fetch_assoc($resprdChildsum);
		 	
			$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
			$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
			. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
			?>
			<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
				<td class="style6">
					<font class="hdrVndDtl"><?=$rowSetList[0]?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=$rowSetList[1]?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=stripslashes(strtoupper($rowSetList[10]));?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=number_format($rowprdChildsum['prd_child_sum'],0)?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=number_format($rowprdChildsum['prd_child_cost'],0)?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=number_format($rowprdChildsum['prd_child_price'],0)?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=substr($rowSetList[6],0,11);?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=substr($rowSetList[7],0,11);?></font>
				</td>
				<td class="style6">
					<font class="hdrVndDtl"><?=substr($rowSetList[8],0,11);?></font>
				</td>
				<td align="center" class="style6">
					<input name="imgDelete" type="image" onClick="javascript:DeleteData('<?php echo $rowSetList[0]?>','<?php echo $rowSetList[1]?>','<?php echo $rowSetList[2]?>');" src="../images/mydelete.gif" alt="Delete Product Set" title="Delete Product Set" />
				</td>
				<td align="center" class="style6">
					<a href="makeup_entry_parent.php?trns=edit&compCode=<?=$compCode?>&vNo=<?=$rowSetList[1]?>&sMnth=<?=$sdate[0]?>&sDay=<?=$sdate[1]?>&sYr=<?=$sdate[2]?>&eMnth=<?=$edate[0]?>&eDay=<?=$edate[1]?>&eYr=<?=$edate[2]?>"><img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Set Product"  align="middle"/>
				</td>
				<td align="center" class="style6">
					<a href="makeup_child_product_list.php?trns=view&compCode=<?=$compCode?>&SKUPrnt=<?=$rowSetList[1]?>"><img src="../../Images/mydocument.gif" alt="View" width="20" height="18" border="0" title="View child Poduct"  align="middle"/>
				</td>
			</tr>
			<?
			 $totChld += $rowprdChildsum['prd_child_sum'];
			 $totCost += $rowprdChildsum['prd_child_cost'];
			 $totPrice += $rowprdChildsum['prd_child_price'];
			}
			$i++;
			
			?>
		</table>
		<br>
		<table border="0" align="center">
			<tr bgcolor="#EAEAEA">
				<td class="style3">
					Total Parent
				</td>
				<td class="style3">
					Total Child
				</td>
				<td class="style3">
					Total Cost
				</td>
				<td class="style3">
					Total Price
				</td>
				<td class="style3">
					Negative Margin
				</td>
			</tr>
			<tr bgcolor="#F2FEFF">
				<td align="center" class="style6">
					<?=number_format($cntSetList,0)."pcs"?>
				</td>
				<td align="center" class="style6">
					<?=number_format($totChld,0)."pcs"?>
				</td>
				<td align="center" class="style6">
					<?=number_format($totCost,0)?>
				</td>
				<td align="center" class="style6">
					<?=number_format($totPrice,0)?>
				</td>
				<td align="center" class="style6">
					<? 
						$negMargn = $totCost-$totPrice;
						echo $diff = ($totCost < $totPrice) ? "0" : $negMargn; 
					?>
				</td>
			</tr>
		</table>
	</form>
	</body>
</html>
<script>
	function DeleteData(compCode,SKUPrnt,SKUChld){
		DeleChldPrd = confirm('Do You Want to Delete this Product');
		if(DeleChldPrd == true){
			document.frmMakeup.action='<?=$_SERVER['PHP_SELF']?>?action=DeleParentPrd&compCode='+compCode+"&SKUPrnt="+SKUPrnt+"&SKUChld="+SKUChld;
			document.frmMakeup.submit();
		}
		
	}
	
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmMakeup.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmMakeup.submit();
		}
	}
</script>
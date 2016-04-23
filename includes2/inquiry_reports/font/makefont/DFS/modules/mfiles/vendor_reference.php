<?php
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../../functions/pager_function.php");

	$db = new DB;
	$db->connect();

//pager settings
$intLimit = 16;
$intOffset = $_GET['limit_start'];
$cUrl = "sample_pager.php";
if($intOffset == "" ){
	$intOffset = 0;
}
//end of pager settings		
	
	$qryVprodIntMax = "SELECT * FROM VIEWVENDORS ";
	$resVprodIntMax = mssql_query($qryVprodIntMax);
	$intMaxRec = mssql_num_rows($resVprodIntMax);
	
	$qryVendorProductList = "SELECT top $intLimit * FROM VIEWVENDORS WHERE SUPPCODE 
							 NOT IN (SELECT TOP $intOffset SUPPCODE FROM VIEWVENDORS ORDER BY SUPPCODE) ";
	$qryVendorProductList2 ="SELECT TOP 100 PERCENT dbo.tblVendorProduct.suppCode, dbo.tblSuppliers.suppName, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, 
							 dbo.tblProdMast.prdSellUnit, dbo.tblProdMast.prdBuyUnit, dbo.tblProdMast.prdGrpCode, dbo.tblProdMast.prdDeptCode, 
                      		 dbo.tblProdMast.prdClsCode, dbo.tblProdMast.prdConv, dbo.tblProdMast.prdSubClsCode, dbo.tblProdMast.prdSuppItem
							 FROM dbo.tblVendorProduct INNER JOIN
							 dbo.tblProdMast ON dbo.tblVendorProduct.prdNumber = dbo.tblProdMast.prdNumber INNER JOIN
							 dbo.tblSuppliers ON dbo.tblVendorProduct.suppCode = dbo.tblSuppliers.suppCode
							 ORDER BY dbo.tblSuppliers.suppName, dbo.tblProdMast.prdDesc";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'VENDOR CODE'){	
				$qryVendorProductList .= " AND SUPPCODE LIKE '{$_POST['txtSearch']}%' ";
				$qryVendorProductList2 ="SELECT TOP 100 PERCENT dbo.tblVendorProduct.suppCode, dbo.tblSuppliers.suppName, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, 
										 dbo.tblProdMast.prdSellUnit, dbo.tblProdMast.prdBuyUnit, dbo.tblProdMast.prdGrpCode, dbo.tblProdMast.prdDeptCode, 
                      					 dbo.tblProdMast.prdClsCode, dbo.tblProdMast.prdConv, dbo.tblProdMast.prdSubClsCode, dbo.tblProdMast.prdSuppItem
										 FROM dbo.tblVendorProduct INNER JOIN
										 dbo.tblProdMast ON dbo.tblVendorProduct.prdNumber = dbo.tblProdMast.prdNumber INNER JOIN
										 dbo.tblSuppliers ON dbo.tblVendorProduct.suppCode = dbo.tblSuppliers.suppCode
										 WHERE dbo.tblVendorProduct.suppCode LIKE '{$_POST['txtSearch']}%'
										 ORDER BY dbo.tblSuppliers.suppName, dbo.tblProdMast.prdDesc";
			}else{
				$qryVendorProductList .= " AND SUPPNAME LIKE '{$_POST['txtSearch']}%' ";
				$qryVendorProductList2 ="SELECT TOP 100 PERCENT dbo.tblVendorProduct.suppCode, dbo.tblSuppliers.suppName, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, 
										 dbo.tblProdMast.prdSellUnit, dbo.tblProdMast.prdBuyUnit, dbo.tblProdMast.prdGrpCode, dbo.tblProdMast.prdDeptCode, 
                      					 dbo.tblProdMast.prdClsCode, dbo.tblProdMast.prdConv, dbo.tblProdMast.prdSubClsCode, dbo.tblProdMast.prdSuppItem
										 FROM dbo.tblVendorProduct INNER JOIN
										 dbo.tblProdMast ON dbo.tblVendorProduct.prdNumber = dbo.tblProdMast.prdNumber INNER JOIN
										 dbo.tblSuppliers ON dbo.tblVendorProduct.suppCode = dbo.tblSuppliers.suppCode
										 WHERE dbo.tblVendorProduct.suppName LIKE '{$_POST['txtSearch']}%'
										 ORDER BY dbo.tblSuppliers.suppName, dbo.tblProdMast.prdDesc";
			}
		}
	}
	$qryVendorProductList .= "ORDER BY SUPPCODE ";
	$resVendorProductList = mssql_query($qryVendorProductList);
	
	
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
<div class="style6">
<center>
<form name="frmRCR" method="post">
<table width="500" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
<th height="23" colspan="14" nowrap="nowrap" class="style6">VENDOR PRODUCT REFERENCE</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
	      <th height="23" colspan="14" nowrap="nowrap" class="style6"> <span class="style6"><img src="../images/s_f_prnt.gif" /> 
            <a href="vendor_reference_pdf.php?search_query=<?php echo $qryVendorProductList2; ?> &search_selection=all_record" target="_blank"> 
            Print</a> ||</span><img src="../images/search.gif" onclick="fSearch()" title="Search" /> 
            <a onclick="fSearch()" title="Search">Search </a> 
            <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
		<select name="cmbSearch" id="cmbSearch">
			<option>VENDOR CODE</option>
			<option>VENDOR NAME</option>
		</select>
		<a href='<? echo $_SERVER['PHP_SELF']; ?>'>Reload</a>
	</th>
</tr>
<tr bgcolor="#DEEDD1">
    <th width="106">Vendor Code</th>
    <th width="315" align="center">Vendor Name</th>
    <th width="106">Vendor Type</th>
    <th colspan="1" align="center">Action</th>
</tr>
<?php 
	$i = 0;
	$mTag = 2;
	while($ctr = mssql_fetch_assoc($resVendorProductList))
	{
		
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
        <td><?php echo $ctr['suppCode']; ?></td>
		<td width="38" align="left"><?php $vendor_name = str_replace("\\","",$ctr['suppName']); echo $vendor_name; ?></td>
		<td width="38" align="left">
		<?php 	if ($ctr['suppType'] == 'CG')
				{
					 echo 'CONSIGNMENT';
				}
				elseif($ctr['suppType'] == 'CO')
				{
					 echo  'CONCESSIONAIRE';
				}
				elseif($ctr['suppType'] == 'RG')
				{
					 echo  'REGULAR';
				}
				else{
					echo  '---';
				} 
		;?></td>
		<td width="23" colspan="1" align="center">
            <a href="vendor_reference_details.php?vNo=<?php echo $ctr['suppCode']; ?>&vName=<?php echo $ctr['suppName'];?>&vType=<?php echo $SType;?>">        
            <img src="../images/icon/book_open.png" alt="Edit" width="16" height="16" border="0" title="View Vendor Allowance Record" align="middle"/>
            </a></td>
		</tr>
		
	<?php 
	}
	$i++;
?>
</table>
<?if($intMaxRec > 16){?>
	<table height="30" width="50%" border="0" align="center">
		</tr>
			<td align="center" class="style6">
				<? echo fPageLinks($intOffset, $intMaxRec,$intLimit,'');?>
			</td>
		</tr>
	</table>
<?}?>
</form>
</center>
</div>
</body>
</html>
<script type="text/javascript" language="JavaScript">		
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmRCR.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmRCR.submit();
		}
	}
	
</script>
<?php
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../../functions/pager_function.php");

	$db = new DB;
	$db->connect();
	
//pager settings
$intLimit = 18;
$intOffset = $_GET['limit_start'];
$cUrl = "sample_pager.php";
if($intOffset == "" ){
	$intOffset = 0;
}
//end of pager settings	

	if($_GET['action'] == 'Dodelete'){
	$qryDeleteVendor = "DELETE FROM tblSuppliers WHERE suppCode = '{$_GET['suppCode']}'";
	$resDeleteVendor = mssql_query($qryDeleteVendor);
	}
	
	$qryVndrMaxRed = "SELECT * FROM VIEWVENDORS";
	$resVndrMaxRed = mssql_query($qryVndrMaxRed);
	$intMaxRec = mssql_num_rows($resVndrMaxRed);
	
	$qryVendorList = "SELECT TOP $intLimit SUPPCODE, SUPPNAME, SUPPTYPE FROM VIEWVENDORS WHERE SUPPCODE 
						NOT IN (SELECT TOP $intOffset SUPPCODE FROM VIEWVENDORS ORDER BY SUPPNAME) ";
	$qryVendorList2 = "SELECT * FROM tblSuppliers ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'VENDOR CODE'){
					$qryVendorList .= "AND SUPPCODE LIKE '{$_POST['txtSearch']}%' ";
					$qryVendorList2 .= "WHERE suppCode LIKE '{$_POST['txtSearch']}%' ";
				}
			if($_POST[cmbSearch] == 'VENDOR NAME'){
					$qryVendorList .= "AND SUPPNAME LIKE '{$_POST['txtSearch']}%' ";
					$qryVendorList2 .= "WHERE suppName LIKE '{$_POST['txtSearch']}%' ";
				}
			}
		}
		$qryVendorList .= "ORDER BY SUPPNAME ";
		$qryVendorList2 .= " ORDER BY suppName ASC ";
		$db->query($qryVendorList);
		$procquery = $db->getArrResult();
	
		$Paper = 'letter';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
-->
</style>
</head>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<body>
<div class="style6">
<center>
<form name="frmRCR" method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
<table width="500" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
<th height="23" colspan="14" nowrap="nowrap" class="style6">VENDOR MAINTENANCE</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
          <th height="23" colspan="14" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
            <a href="vendor_entry.php?maction=add"> New </a> || <img src="../images/s_f_prnt.gif" /> 
            <a href="vendor_maintenance_pdf.php?search_query=<?php echo $qryVendorList2; ?>&search_selection=all_record" target="_blank"> 
            Print</a> || <img src="../images/search.gif" onclick="fSearch()"/> 
            <a onclick="fSearch()" title="Search">Search</a> 
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
    <th colspan="2" align="center">Action</th>
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
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
        <td><?php echo $ctr['SUPPCODE']; ?></td>         
		<td width="38" align="left"><?php echo stripslashes($ctr['SUPPNAME']); ?></td>
		<td width="38" align="left">
		<?php 
			if($ctr['SUPPTYPE'] == 'CG'){
				echo "CONSIGNMENT";
			}elseif ($ctr['SUPPTYPE'] == 'RG'){
				echo "REGULAR";
			}elseif ($ctr['SUPPTYPE'] == 'CO'){
				echo "CONSESSIONAIRE";
			}else{
				echo "---";
			}
			;
		?>
		</td>  
		<td width="30" colspan="1" align="center">
        	<a href="vendor_entry.php?maction=edit&venNo=<?php echo $ctr['SUPPCODE']; ?>&Vtype=<?=$ctr['SUPPTYPE']?> ">
            <img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Buyer Record"  align="middle"/></a>
            <a href="vendor_entry.php?venNo=<?php echo $ctr['SUPPCODE']; ?> "></a></td>
		<td width="30" colspan="1" align="center">
            <a onclick="fDelete('<?php echo $ctr['SUPPCODE']; ?>')"><img src="../../Images/mydelete.gif" alt="Print" width="18" height="18" border="0" title="Delete Buyer Record"  /></a>
		</td> 
		</tr>
		
	<?php 
	}
	$i++;
?>
</table>
<?if($intMaxRec > 18){?>
	<table width="50%" border="0" align="center">
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
<script>
	function fDelete(suppCode){
		Delete = confirm('Do You Want To Delete This Record?')
		if(Delete == true){
		document.frmRCR.action='<? $_SERVER[PHP_SELF]?>?action=Dodelete&suppCode='+suppCode;
		document.frmRCR.submit();
		}
	}
	
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
<img src="">
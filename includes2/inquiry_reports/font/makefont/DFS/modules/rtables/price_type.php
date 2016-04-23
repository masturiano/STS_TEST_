<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../inventory/lbd_function.php");

	$db = new DB;
	$db->connect();
	

	
if($_GET['action'] == 'Dodelete'){
				 $UMDelete = "DELETE FROM tblPriceType WHERE prTypeCode = '" . $_GET['prTypeCode'] . "'";
				mssql_query($UMDelete);
	}
	
if (($_GET['action']) == 'DoMultDele')
{
	foreach ((array)($_POST['chkbox']) as $index => $value) 
	{
		$cQryMultDele = "DELETE FROM tblPriceType WHERE prTypeCode = '{$value}'";
		$cResMultDele = mssql_query($cQryMultDele);
	}
}
	
	$qryPrTypeList =  "SELECT * FROM tblPriceType ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'PRICE TYPE CODE'){	
				$qryPrTypeList .= " WHERE prTypeCode LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryPrTypeList .= " WHERE prTypeDesc LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$qryPrTypeList .= " ORDER BY prTypeDesc ASC ";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Price Types Maintenance</title>
<style type="text/css">
<!--
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
-->
</style>
</head>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<body>
<div class="style6">
<center>
<form name="frmPrType" method="post" action="<? echo $_SERVER['../rtables/PHP_SELF'];?>">
<table width="500" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
          <th height="23" colspan="14" nowrap="nowrap" class="style6">PRICE TYPES 
            MAINTENANCE</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
	      <th height="23" colspan="14" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
            <a href="../rtables/price_type_entry.php?maction=add"> New </a> ||<span class="style6"> 
            <a href="price_type_pdf.php?search_query=<?php echo $qryPrTypeList; ?> &search_selection=all_record" target="_blank"> 
            Print</a></span> <img src="../images/search.gif" onclick="fSearch()" title="Search" /> 
            <a onclick="fSearch()" title="Search">Search</a> 
            <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
		<select name="cmbSearch" id="cmbSearch">
			<option>PRICE TYPE CODE</option>
			<option>PRICE TYPE DESC</option>
		</select>
		<a href='<? echo $_server['../rtables/php_self']; ?>'>Reload</a>
	</th>
</tr>
<tr bgcolor="#DEEDD1">
	<td></h>
	<th width="148">Type Code</th>
    <th width="216">Description</th>
    <th width="148" align="center">Precedence</th>
    <th colspan="2" align="center">Action</th>
</tr>

<?php 
	$resPrTypeList = mssql_query($qryPrTypeList);
		
	$i = -1;
	$mTag = 2;
	while($row = mssql_fetch_assoc($resPrTypeList))
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
        <td align="center"><input type='checkbox' name='chkbox[<?=$i?>]' value='<?=$row['prTypeCode']?>' id='btncheck<?=$i?>' onclick=\"fChkRow('btncheck<?=$i?>')\"></td>
        <td><?php echo $row['prTypeCode']; ?></td>
		<td width="216" align="left"><?php echo $row['prTypeDesc']; ?></td>
		<td width="216" align="left"><?php echo $row['prPrecedence']; ?></td>
 			<td width="20" colspan="1" align="center">
        		<a href="../rtables/price_type_entry.php?maction=edit&prtypecode=<?php echo $row['prTypeCode']; ?>%20">
           	    <img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Price Types Record"  align="middle"/></a>
                <a href="../rtables/vendor_entry.php?venno=<?php echo $row['prtypecode']; ?>%20"></a></td>
			<td width="16" colspan="1" align="center">
			<input name="imgDelete" type="image" onClick="javascript:procData('<?php echo $row['prTypeCode']?>');" src="../images/mydelete.gif" alt="Delete Price Types Record" title="Delete Price Types Record" /></td>                   
		</tr>
		
	<?php 
	}
	$i++;
?>
		<tr>
			<td colspan="6" align="center">
				<a onClick="checkAll()"><font color="Blue" style="cursor: pointer;" >Select All</font></a> /
				<a onClick="clearSelection()"><font color="Blue" style="cursor: pointer;" >Uncheck All</font></a>&nbsp;
				<a name='btndele' id='btndele' onclick="multdele()" width="18" height="18"><font color="red" style="cursor: pointer;">Delete</font></a>
			</td>
		</tr>
</table>
<input name="txtCOUNT" type="hidden" id="txtCOUNT" value="<?php echo $i; ?>"> 
</form>
</center>
</div>
</body>
</html>

<script type="text/javascript" language="JavaScript">	
	function procData(mysel)
	{
		var lDelete = confirm("Are you sure you want to delete this Price Type?");
		if (lDelete == true)
		{
			document.frmPrType.action="<?php $_SERVER['PHP_SELF']?>?action=Dodelete&prtypecode="+mysel;
			document.frmPrType.submit();
		}
	}
	
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmPrType.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmPrType.submit();
		}
	}
	
	function checkAll()
	{	
		var frm = document.frmPrType;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=0; i<=cnt; i++)
			eval("frm.btncheck" + i + ".checked=true;");
	}
	
	function clearSelection()
	{
		var frm = document.frmPrType;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=0; i<=cnt; i++)
			eval("frm.btncheck" + i + ".checked=false;");
	}
	
	function multdele()
	{	
		multdelet = confirm('Do You Want To Delete Selected Item?');
		if(multdelet == 1)
		{
			document.frmPrType.action='<?=$_SERVER['PHP_SELF']?>?action=DoMultDele';
			document.frmPrType.submit();
		}
	}
</script>
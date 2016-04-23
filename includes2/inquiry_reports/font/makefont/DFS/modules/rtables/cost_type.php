<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../inventory/lbd_function.php");

	$db = new DB;
	$db->connect();
	

	
if($_GET['action'] == 'Dodelete'){
				 $UMDelete = "DELETE FROM tblCostType WHERE cstTypeCode = '" . $_GET['cstTypeCode'] . "'";
				mssql_query($UMDelete);
	}
	
if (($_GET['action']) == 'DoMultDele')
{
	foreach ((array)($_POST['chkbox']) as $index => $value) 
	{
		$cQryMultDele = "DELETE FROM tblCostType WHERE cstTypeCode = '{$value}'";
		$cResMultDele = mssql_query($cQryMultDele);
	}
}
	
	$cstPrTypeList =  "SELECT * FROM tblCostType ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'COST TYPE CODE'){	
				$cstPrTypeList .= " WHERE cstTypeCode LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$cstPrTypeList .= " WHERE cstTypeDesc LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$cstPrTypeList .= " ORDER BY cstTypeDesc ASC ";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cost Types Maintenance</title>
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
<form name="frmCstType" method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
<table width="500" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
          <th height="23" colspan="14" nowrap="nowrap" class="style6">COST TYPES 
            MAINTENANCE</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
	      <th height="23" colspan="14" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
            <a href="cost_type_entry.php?maction=add"> New </a> ||<span class="style6"> 
            <a href="../rtables/cost_type_pdf.php?search_query=<?php echo $cstPrTypeList; ?>%20&search_selection=all_record" target="_blank"> 
            Print</a></span> <img src="../images/search.gif" onclick="fSearch()" title="Search" /> 
            <a onclick="fSearch()" title="Search">Search</a> 
            <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
		<select name="cmbSearch" id="cmbSearch">
			<option>COST TYPE CODE</option>
			<option>COST TYPE DESC</option>
		</select>
		<a href='<? echo $_server['php_self']; ?>'>Reload</a>
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
	$resCstTypeList = mssql_query($cstPrTypeList);
		
	$i = -1;
	$mTag = 2;
	while($row = mssql_fetch_assoc($resCstTypeList))
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
        <td align="center"><input type='checkbox' name='chkbox[<?=$i?>]' value='<?=$row['cstTypeCode']?>' id='btncheck<?=$i?>' onclick=\"fChkRow('btncheck<?=$i?>')\"></td>
        <td><?php echo $row['cstTypeCode']; ?></td>
		<td width="216" align="left"><?php echo $row['cstTypeDesc']; ?></td>
		<td width="216" align="left"><?php echo $row['cstPrecedence']; ?></td>
 			<td width="20" colspan="1" align="center">
        		<a href="cost_type_entry.php?maction=edit&cstTypeCode=<?php echo $row['cstTypeCode']; ?>%20">
           	    <img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Cost Types Record"  align="middle"/></a>
                <a href="vendor_entry.php?venno=<?php echo $row['cstTypeCode']; ?>%20"></a></td>
			<td width="16" colspan="1" align="center">
			<input name="imgDelete" type="image" onClick="javascript:procData('<?php echo $row['cstTypeCode']?>');" src="../images/mydelete.gif" alt="Delete Cost Types Record" title="Delete Cost Types Record" /></td>                   
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
		var lDelete = confirm("Are you sure you want to delete this Cost Type?");
		if (lDelete == true)
		{
			document.frmCstType.action="<?php $_SERVER['PHP_SELF']?>?action=Dodelete&cstTypeCode="+mysel;
			document.frmCstType.submit();
		}
	}
	
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmCstType.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmCstType.submit();
		}
	}
	
	function checkAll()
	{	
		var frm = document.frmCstType;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=0; i<=cnt; i++)
			eval("frm.btncheck" + i + ".checked=true;");
	}
	
	function clearSelection()
	{
		var frm = document.frmCstType;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=0; i<=cnt; i++)
			eval("frm.btncheck" + i + ".checked=false;");
	}
	
	function multdele()
	{	
		multdelet = confirm('Do You Want To Delete Selected Item?');
		if(multdelet == 1)
		{
			document.frmCstType.action='<?=$_SERVER['PHP_SELF']?>?action=DoMultDele';
			document.frmCstType.submit();
		}
	}
</script>
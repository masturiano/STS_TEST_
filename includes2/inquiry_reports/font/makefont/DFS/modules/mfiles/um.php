<?php
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../inventory/lbd_function.php");

	$db = new DB;
	$db->connect();
	

	
if($_GET['action'] == 'Dodelete'){
				 $UMDelete = "DELETE FROM TBLUM WHERE UMCODE = '" . $_GET['umCode'] . "'";
				mssql_query($UMDelete);
	}
	
if (($_GET['action']) == 'DoMultDele')
{
	foreach ((array)($_POST['chkbox']) as $index => $value) 
	{
		$cQryMultDele = "DELETE FROM TBLUM WHERE UMCODE = '{$value}'";
		$cResMultDele = mssql_query($cQryMultDele);
	}
}

	$qryUmMeasureList =  "SELECT * FROM TBLUM ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'UM CODE'){	
				$qryUmMeasureList .= " WHERE UMCODE LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryUmMeasureList .= " WHERE UMDESC LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	
	$resUmMeasureList = mssql_query($qryUmMeasureList);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
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
<form name="frmUMMaint" method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
<table width="500" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
<th height="23" colspan="14" nowrap="nowrap" class="style6">UNIT OF MEASURE MAINTENANCE</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
	      <th height="23" colspan="14" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
            <a href="um_entry.php?maction=add"> New </a> || <img src="../images/s_f_prnt.gif" /> 
            <a href="vendor_loadreport.php"> <span class="style6"><a href="../mfiles/um_pdf.php?search_query=<?php echo $qryUmMeasureList; ?>&search_selection=all_record" target="_blank">Print</a></span></a> 
            || <img src="../images/search.gif" onclick="fSearch()" title="Search" /> 
            <a onclick="fSearch()" title="Search">Search</a> 
            <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
		<select name="cmbSearch" id="cmbSearch">
			<option>UM CODE</option>
			<option>UM DESC</option>
		</select>
		<a href='<? echo $_SERVER['PHP_SELF']; ?>'>Reload</a>
	</th>
</tr>
<tr bgcolor="#DEEDD1">
	<td></h>
    <th width="148">Unit of Measure</th>
    <th width="216" align="center">Description</th>
    <th width="78">Type</th>  
    <th colspan="2" align="center">Action</th>
</tr>

<?php 
	$i = -1;
	$mTag = 2;
	while($row = mssql_fetch_assoc($resUmMeasureList))
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
        <td align="center"><input type='checkbox' name='chkbox[<?=$i?>]' value='<?=$row['umCode']?>' id='btncheck<?=$i?>' onclick=\"fChkRow('btncheck<?=$i?>')\"></td>
        <td><?php echo $row['umCode']; ?></td>
		
		<td width="216" align="left"><?php echo $row['umDesc']; ?></td>
		<td width="78" align="left">
		<?php 
			if ($row['umType'] == 'S')
			{
				echo $SType = 'SELL UNIT';
			}
			if ($row['umType'] == 'B')
			{
				echo $SType = 'BUY UNIT';
			}
		?></td>
 		  <td width="20" colspan="1" align="center">
        		<a href="um_entry.php?maction=edit&umCode=<?php echo $row['umCode']; ?> ">
           	    <img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit UM Record"  align="middle"/></a>
                <a href="vendor_entry.php?venNo=<?php echo $row['umCode']; ?> "></a></td>
			<td width="16" colspan="1" align="center">
			<input name="imgDelete" type="image" onClick="javascript:procData('<?php echo $row['umCode']?>');" src="../images/mydelete.gif" alt="Delete UM Record" title="Delete UM Record" /></td>                   
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
		var lDelete = confirm("Are you sure you want to delete this Unit of Measure?");
		if (lDelete == true)
		{
			document.frmUMMaint.action="<?php $_SERVER['PHP_SELF']?>?action=Dodelete&umCode="+mysel;
			document.frmUMMaint.submit();
		}
	}
	
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmUMMaint.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmUMMaint.submit();
		}
	}
	
	function checkAll()
	{	
		var frm = document.frmUMMaint;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=0; i<=cnt; i++)
			eval("frm.btncheck" + i + ".checked=true;");
	}
	
	function clearSelection()
	{
		var frm = document.frmUMMaint;
		var cnt = parseInt(frm.txtCOUNT.value);
		for (i=0; i<=cnt; i++)
			eval("frm.btncheck" + i + ".checked=false;");
	}
	
	function multdele()
	{	
		multdelet = confirm('Do You Want To Delete Selected Item?');
		if(multdelet == 1)
		{
			document.frmUMMaint.action='<?=$_SERVER['PHP_SELF']?>?action=DoMultDele';
			document.frmUMMaint.submit();
		}
	}
</script>
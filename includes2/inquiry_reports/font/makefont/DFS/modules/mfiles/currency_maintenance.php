<?php
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();

	$db->query("SELECT CURRCODE, CURRDESC, CURRUSDRATE, CURRRATEDATE FROM VIEWCURRENCY");
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
<form name="frmRCR" method="post">
<table width="500" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
<th height="23" colspan="14" nowrap="nowrap" class="style6">CURRENCY MAINTENANCE</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
<th height="23" colspan="14" nowrap="nowrap" class="style6">
    <img src="../images/s_b_insr.gif" />
	<a href="currency_entry.php?maction=add">
	New
    </a> 
    || 
    <img src="../images/s_f_prnt.gif" />
    <a href="currency_loadreport.php" target="_blank">
    Print All 
    </a>
    || 
    <img src="../images/search.gif" />
    Search </th>
</tr>
<tr bgcolor="#DEEDD1">
    <th width="116">Currency Code</th>
    <th colspan="2" align="center">Action</th>
    <th width="234" align="center">Currency Description</th>
    <th width="78">US $ Rate</th>
    <th width="78">Rate Date</th>
    
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
        <td><?php echo $ctr['CURRCODE']; ?></td>
		<td width="20" colspan="1" align="center">
        	<a href="currency_entry.php?maction=edit&cCode=<?php echo $ctr['CURRCODE']; ?> ">
            <img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Buyer Record"  align="middle"/></a>
            <a href="currency_entry.php?venNo=<?php echo $ctr['SUPPCODE']; ?> "></a></td>
		<td width="30" colspan="1" align="center">
            <img src="../../Images/mydelete.gif" alt="Print" width="18" height="18" border="0" title="Delete Buyer Record" /></a>
		</td>            
		<td width="234" align="left"><?php echo $ctr['CURRDESC']; ?></td>
		<td width="78" align="right">
		<?php 
			echo number_format($ctr['CURRUSDRATE'],4);
		?></td>
		<td width="78" align="center">
		<?php 
			echo $ctr['CURRRATEDATE'];
		?></td>
        
		</tr>
		
	<?php 
	}
	$i++;
?>
</table>
</form>
</center>
</div>
</body>
</html>

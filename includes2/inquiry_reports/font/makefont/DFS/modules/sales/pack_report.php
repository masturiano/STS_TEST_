<?php
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();

	$db->query("SELECT CINUMBER, CIDATE, LOCNAME, CUSTNAME FROM VIEWCISUMMARY");
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
<table width="69%" border="0" >
<tr bgcolor="#DEEDD1">
    <th width="9%">CI Number</th>
    <th colspan="1">Action</th>
    <th width="8%" align="center">CI Date</th>
    <th width="29%">From Location</th>
    <th width="29%">To Customer</th>
</tr>

<?php 
	$i = 0;
	foreach($procquery as $ctr)
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?><tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>><td><?php echo $ctr['CINUMBER']; ?></td>
		<td width="4%" align="center"><a href="pack_loadreport.php?cino=<?php echo $ctr['CINUMBER']; ?> "><img src="../../Images/s_f_prnt.gif" alt="Print" width="17" border="0" title="Print CI Record" /></a><a href="pack_loadreport.php?cino=<?php echo $ctr['CINUMBER']; ?> "></a></td>
		<td align="center"><?php echo $ctr['CIDATE']; ?></td>
		<td><?php echo $ctr['LOCNAME']; ?></td>       
		<td><?php echo $ctr['CUSTNAME']; ?></td>   
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

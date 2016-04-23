<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();

	$db->query("SELECT PONUMBER, PODATE, SUPPNAME FROM VIEWPOCORRECTIONSUMMARY WHERE COMPCODE = $company_code");
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
<form name="frmRCR" method="post">
<center>
<table width="60%" border="0" >
<tr bgcolor="#DEEDD1">
    <th width="19%">PO Number</th>
    <th colspan="1">Action</th>
    <th width="14%" align="center">PO Date</th>
    <th width="60%">Vendor Name</th>
</tr>

<?php 
	$i = 0;
	foreach($procquery as $ctr)
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?><tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>><td><?php echo $ctr['PONUMBER']; ?></td>
		<td width="7%" align="center"><a href="po_disc_pdf.php?pono=<?php echo $ctr['PONUMBER']; ?> " target="_blank"><img src="../../Images/s_f_prnt.gif" alt="Print" width="17" border="0" title="Print PO Correction Record" /></a></td>
		<td align="center"><?php echo $ctr['PODATE']; ?></td>
		<td><?php $supp_name = str_replace("\\","",$ctr['SUPPNAME']); echo $supp_name; ?></td>       
		</tr>
	<?php 
	}
	$i++;
?>
</table>
</center>
</form>
</div>
</body>
</html>

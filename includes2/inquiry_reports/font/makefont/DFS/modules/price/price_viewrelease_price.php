<?php
	session_start();
	$company_code = $_SESSION['comp_code'];
	require("../../includes/config.php");
	require("../../functions/db_function.php");
	require("../inventory/lbd_function.php");
	
	$db = new DB;
	$db->connect();
?>
<html>
<head>
<style type="text/css">
<!--
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
-->
</style>

<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
</head>
<body>
<div class="style6">
<center>
<form name="frmPriceEvent" method="post">
<input name="myselection" type="hidden" />
<table width="80%" border="0" >
<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
<th height="23" colspan="14" nowrap="nowrap" class="style6">RELEASED PRICE EVENTS</th>
</tr>
<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
<tr bgcolor="#DEEDD1" class="style6">
    <th width="13%">Price Event No</th>
    <th width="1%">Action</th>
    <th width="15%" align="center">Event Description</th>
</tr>
<?php 
	$strCost = "SELECT PREVENTNUMBER, PREVENTDESC FROM VIEWRELEASEPRICEEVENTS WHERE COMPCODE = $company_code";
	
	$db->query($strCost);
	$procquery = $db->getArrResult();

	$i = 0;
	foreach($procquery as $ctr)
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> class="style6"><td><?php echo $ctr['PREVENTNUMBER']; ?>
        </td>
		<td align="center">
        	<a  target="_blank" href="price_released_pdf.php?EventNo=<?php echo $ctr['PREVENTNUMBER']; ?>"; window.close();">
            <img src="../../Images/s_f_prnt.gif" alt="Print Released Price Event" title="Print Release Price Event" border="0"/>
            </a>
        </td>
		<td><?php echo $ctr['PREVENTDESC']; ?></td>
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

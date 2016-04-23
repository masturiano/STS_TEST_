<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();

	$db->query("SELECT TOP 100 PERCENT dbo.tblPoAudit.poNumber, CONVERT(varchar, dbo.tblPoHeader.poDate, 101) AS poDate, CONVERT(varchar, 
                      dbo.tblPoHeader.poExpDate, 101) AS poExpDate, dbo.tblPoHeader.poTotExt, dbo.tblPoHeader.poCancelDate, dbo.tblPoHeader.poTerms, 
                      dbo.tblPoHeader.poBuyer, dbo.tblBuyers.buyerName, dbo.tblSuppliers.suppAddr1, dbo.tblSuppliers.suppAddr2, dbo.tblSuppliers.suppAddr3, 
                      CONVERT(varchar, dbo.tblPoHeader.suppCode) + ' - ' + UPPER(dbo.tblSuppliers.suppName) AS suppName, UPPER(dbo.tblSuppliers.suppCurr) 
                      AS suppCurr, dbo.tblSuppliers.suppTel, dbo.tblPoHeader.compCode
					  FROM dbo.tblPoAudit INNER JOIN
                      dbo.tblPoHeader ON dbo.tblPoAudit.poNumber = dbo.tblPoHeader.poNumber AND dbo.tblPoAudit.compCode = dbo.tblPoHeader.compCode INNER JOIN
                      dbo.tblSuppliers ON dbo.tblPoHeader.suppCode = dbo.tblSuppliers.suppCode INNER JOIN
                      dbo.tblBuyers ON dbo.tblPoHeader.poBuyer = dbo.tblBuyers.buyerCode
					  WHERE (dbo.tblPoAudit.poPrntDate <= '1/1/1900' OR
                      dbo.tblPoAudit.poPrntDate IS NULL) AND (dbo.tblPoAudit.poCancelDate IS NULL) AND (dbo.tblPoHeader.compCode = $company_code)
					  ORDER BY dbo.tblPoAudit.poNumber");
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
<form action="po_report.php" method="post" name="frmRCR" target="_self">
    <table width="100%" border="0" >
      <tr bgcolor="#DEEDD1"> 
        <th width="10%">PO Number</th>
        <th colspan="1">Action</th>
        <th width="10%" align="center">PO Date</th>
        <th width="37%">Vendor Name</th>
        <th width="16%">Expected Date of Delivery</th>
        <th width="22%">Total PO Extended Amount</th>
      </tr>
      <?php 
	$i = 0;
	foreach($procquery as $ctr)
	{
		$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
		
		?>
      <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
        <td>
          <?php echo $ctr['poNumber']; ?>
        </td>
        <td width="5%" align="center"><a  onclick="javascript:navigator.plugins.refresh(true);" href="po_pdf.php?level=print&pono=<?php echo $ctr['poNumber']; ?> " target="_blank"><img src="../../Images/s_f_prnt.gif" onclick="javascript:navigator.plugins.refresh(true);" alt="Print" width="17" border="0" title="Print PO Record" /></a></td>
        <td align="center">
          <?php echo $ctr['poDate']; ?>
        </td>
        <td>
          <?php $supp_name = str_replace("\\","",$ctr['suppName']); echo $supp_name; ?>
        </td>
        <td align="center">
          <?php echo $ctr['poExpDate']; ?>
        </td>
        <td align="right">
          <?php echo number_format($ctr['poTotExt'],4); ?>
        </td>
      </tr>
      <?php 
	}
	$i++;
?>
    </table>
</form>
</div>
</body>
</html>

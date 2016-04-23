<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	$db->query("SELECT TOP 100 PERCENT tblPoAudit.poNumber,CONVERT(varchar, tblPoAudit.poPrntDate, 101) as poPrntDate, CONVERT(varchar, tblPoHeader.poDate, 101) AS poDate, CONVERT(varchar, 
                      tblPoHeader.poExpDate, 101) AS poExpDate, tblPoHeader.poTotExt, tblPoHeader.poCancelDate, tblPoHeader.poTerms, 
                      tblPoHeader.poBuyer, tblBuyers.buyerName, tblSuppliers.suppAddr1, tblSuppliers.suppAddr2, tblSuppliers.suppAddr3, 
                      CONVERT(varchar, tblPoHeader.suppCode) + ' - ' + UPPER(tblSuppliers.suppName) AS suppName, UPPER(tblSuppliers.suppCurr) 
                      AS suppCurr, tblSuppliers.suppTel, tblPoHeader.compCode, tblPoHeader.poReopenId
					  FROM tblPoAudit INNER JOIN
                      tblPoHeader ON tblPoAudit.poNumber = tblPoHeader.poNumber AND tblPoAudit.compCode = tblPoHeader.compCode INNER JOIN
                      tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
                      tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
                      WHERE (tblPoHeader.compCode = $company_code) AND (tblPoAudit.poPrntDate > '1/1/1900') AND (tblPoAudit.poCancelDate IS NULL)
                      ORDER BY tblPoAudit.poNumber");
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
<script type='text/javascript' src='../../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../../functions/prototype.js'></script>
<body>
<div class="style6">
<form name="frmRCR" method="post">
    <table width="100%" border="0" >
      <tr bgcolor="#DEEDD1"> 
        <th width="7%">PO Number</th>
        <th colspan="1">Action</th>
        <th width="5%" align="center">PO Date</th>
        <th width="48%">Vendor Name</th>
        <th width="9%">Re-Open Status</th>
        <th width="12%">Original Date Printed</th>
        <th width="13%">Total PO Extended Amount</th>
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
        <td width="6%" align="center"><a  onclick="javascript:navigator.plugins.refresh(true);" href="po_pdf.php?level=reprint&pono=<?php echo $ctr['poNumber']; ?> " target="_blank"><img src="../../Images/s_f_prnt.gif" onclick="javascript:navigator.plugins.refresh(true);" alt="Print" width="17" border="0" title="Print PO Record" /></a></td>
        <td align="center"> 
          <?php echo $ctr['poDate']; ?>
        </td>
        <td> 
          <?php $supp_name = str_replace("\\","",$ctr['suppName']); echo $supp_name; ?>
        </td>
        <td align="center">
          <?php 
		  if ($ctr['poReopenId']>"") {
		  	$reopen_status = "YES";
		  } else {
		  	$reopen_status = "NO";
		  }
		  echo $reopen_status; ?>
        </td>
        <td align="center"> 
          <?php echo $ctr['poPrntDate']; ?>
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

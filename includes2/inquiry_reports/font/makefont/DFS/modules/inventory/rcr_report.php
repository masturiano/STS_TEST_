<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	$print_button=$_POST["print_button"];
	if ($print_button=="") {
		$print_button="print";
	}
	if ($print_button=="print") {
		$print_checked = "checked=\"checked\"";
		$reprint_checked ="";
		$level="print";
		$db->query("SELECT tblRcrItemDtl.rcrNumber, CONVERT(varchar, tblRcrHeader.rcrDate, 101) AS rcrDate, tblRcrHeader.suppCode, 
					UPPER(tblSuppliers.suppName) AS suppName, tblRcrHeader.poNumber, SUM(ISNULL(tblRcrItemDtl.rcrExtAmt, 0)) AS rcrExtAmt, 
					tblRcrHeader.compCode, tblRcrItemDtl.compCode AS Expr1, tblRcrAudit.compCode AS Expr2
					FROM tblRcrHeader INNER JOIN
					tblRcrItemDtl ON tblRcrHeader.rcrNumber = tblRcrItemDtl.rcrNumber AND 
					tblRcrHeader.compCode = tblRcrItemDtl.compCode INNER JOIN
					tblSuppliers ON tblRcrHeader.suppCode = tblSuppliers.suppCode INNER JOIN
					tblRcrAudit ON tblRcrHeader.compCode = tblRcrAudit.compCode AND tblRcrHeader.rcrNumber = tblRcrAudit.rcrNumber
					WHERE (tblRcrAudit.rcrPrntDate IS NULL) AND (tblRcrAudit.rcrCancelDate IS NULL) AND (tblRcrHeader.compCode=$company_code)
					GROUP BY tblRcrItemDtl.rcrNumber, tblRcrHeader.rcrDate, tblRcrHeader.suppCode, tblSuppliers.suppName, tblRcrHeader.poNumber, 
					tblRcrHeader.compCode, tblRcrItemDtl.compCode, tblRcrAudit.compCode");
	} else {
		$reprint_checked = "checked=\"checked\"";
		$print_checked = "";
		$level="reprint";
		$db->query("SELECT tblRcrItemDtl.rcrNumber, CONVERT(varchar, tblRcrHeader.rcrDate, 101) AS rcrDate, tblRcrHeader.suppCode, 
                UPPER(tblSuppliers.suppName) AS suppName, tblRcrHeader.poNumber, SUM(ISNULL(tblRcrItemDtl.rcrExtAmt, 0)) AS rcrExtAmt, 
                tblRcrHeader.compCode, tblRcrItemDtl.compCode AS Expr1, tblRcrAudit.compCode AS Expr2,  CONVERT(varchar, tblRcrAudit.rcrPrntDate, 101) as rcrPrntDate
				FROM tblRcrHeader INNER JOIN
                tblRcrItemDtl ON tblRcrHeader.rcrNumber = tblRcrItemDtl.rcrNumber AND 
                tblRcrHeader.compCode = tblRcrItemDtl.compCode INNER JOIN
                tblSuppliers ON tblRcrHeader.suppCode = tblSuppliers.suppCode INNER JOIN
                tblRcrAudit ON tblRcrHeader.compCode = tblRcrAudit.compCode AND tblRcrHeader.rcrNumber = tblRcrAudit.rcrNumber
				WHERE (tblRcrAudit.rcrPrntDate > '1/1/1900') AND (tblRcrAudit.rcrCancelDate IS NULL) AND (tblRcrHeader.compCode=$company_code)
				GROUP BY tblRcrItemDtl.rcrNumber, tblRcrHeader.rcrDate, tblRcrHeader.suppCode, tblSuppliers.suppName, tblRcrHeader.poNumber, 
                tblRcrHeader.compCode, tblRcrItemDtl.compCode, tblRcrAudit.compCode,tblRcrAudit.rcrPrntDate");
	}
	
		
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
    <table width="100%" border="0" >
      <tr bgcolor="#DEEDD1"> 
        <th colspan="6"><table width="30%" border="0" align="center">
            <tr> 
              <td width="58%"><input name="print_button" type="radio" value="print" <? echo $print_checked; ?> />
                Print 
                <input type="radio" name="print_button" value="reprint" <? echo $reprint_checked; ?> />
                Re-Print</td>
              <td width="31%"><div align="center">
                  <input name="view" type="submit" id="view" value="View" />
                </div></td>
            </tr>
          </table></th>
      </tr>
      <tr bgcolor="#DEEDD1"> 
        <th width="10%">RCR No</th>
        <th colspan="1">Action</th>
        <th width="10%" align="center">RCR Date</th>
        <th width="41%">Vendor Name</th>
        <th width="12%">Ref. PO Number</th>
        <th width="22%">Total RCR Extended Amount</th>
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
          <?php echo $ctr['rcrNumber']; ?>
        </td>
        <td width="5%" align="center"><input name="print" type="button" id="print" value="Print" onclick="window.open('rcr_pdf.php?level=<? echo $level; ?>&rcrno=<?php echo $ctr['rcrNumber']; ?>'); navigator.plugins.refresh(true);"/></td>
        <td align="center"> 
          <?php echo $ctr['rcrDate']; ?>
        </td>
        <td> 
          <?php echo $ctr['suppName']; ?>
        </td>
        <td> 
          <?php echo $ctr['poNumber']; ?>
        </td>
        <td align="right"> 
          <?php echo number_format($ctr['rcrExtAmt'],4); ?>
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

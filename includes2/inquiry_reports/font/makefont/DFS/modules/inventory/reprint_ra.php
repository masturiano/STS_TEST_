<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function2.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$today = date("m/d/Y");
$db = new DB;
$db->connect();

################################ if search button is click
$from_date=$_POST['from_date'];
$to_date=$_POST['to_date'];

################################ if first run the module
if ($from_date =="") {
	$from_date=$today;
	$to_date=$today;
}

################################ if print button is click
if(isset($_POST['print_ci'])) {
	$from_date=$_POST['hide_from_date'];
	$to_date=$_POST['hide_to_date'];
} 
?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script type="text/javascript">

function val_date() {
	var today_date = new Date();
 	var from_date=document.form_search.from_date.value
    var to_date=document.form_search.to_date.value
	var today_date_mm = Date.parse(today_date);
	var from_date_mm = Date.parse(from_date);
	var to_date_mm = Date.parse(to_date);
	if(from_date_mm > today_date_mm) {
		alert("From Date must not be greater than to Current Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	}
	if(to_date_mm > today_date_mm) {
		alert("To Date must not be greater than to Current Date.");
		document.form_search.to_date.value="";
		document.form_search.to_date.focus();
		return false;
	}
	if(from_date_mm > to_date_mm) {
		alert("From Date must not be greater than to To Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	} 
}
function val_all() {
	var today_date = new Date();
 	var from_date=document.form_search.from_date.value
    var to_date=document.form_search.to_date.value
	var today_date_mm = Date.parse(today_date);
	var from_date_mm = Date.parse(from_date);
	var to_date_mm = Date.parse(to_date);
	if(from_date_mm > today_date_mm) {
		alert("From Date must not be greater than to Current Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	}
	if(to_date_mm > today_date_mm) {
		alert("To Date must not be greater than to Current Date.");
		document.form_search.to_date.value="";
		document.form_search.to_date.focus();
		return false;
	}
	if(from_date_mm > to_date_mm) {
		alert("From Date must not be greater than to To Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	} 
	if((from_date=="") && (to_date=="")) { 
		
	} else {
		if(from_date=="" || to_date=="") {
			alert("Key-in From Date or To Date.");
			document.form_search.from_Date.focus();
			return false;
		}
	}
	document.form_search.submit();
}
function reprint_ra() {
	var ra_number=document.form_print.hide_ra.value;
	
	window.open ('ra_pdf.php?ra_number='+ra_number);
}
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body>
<fieldset>
<legend>Search by Date</legend>
<form name="form_search" method="post" action="reprint_ra.php">
  <table width="561" border="0" align="left" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="392"><font size="2" face="Arial, Helvetica, sans-serif">From 
        Date 
        <input name="from_date" type="text" id="from_date5"   onBlur="val_date();" value="<? echo $from_date?>" size="10" readonly="true">
        <a href="javascript:void(0)" onClick="showCalendar(form_search.from_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
        To Date 
        <input name="to_date" type="text" id="to_date5" onBlur="val_date();" value="<? echo $to_date?>" size="10" readonly="true">
        <a href="javascript:void(0)" onClick="showCalendar(form_search.to_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
        <input name='view_ci' type='button' class='queryButton' id='view_transfer5' title='Display the Inventory Transaction' value='Search' onClick="val_all();"/>
        </font></td>
    </tr>
  </table>
</form>
</fieldset>
<form action="ra_pdf.php" method="post" name="form_print" target="_blank" id="form_print">
  <div id="Layer2" style="position:absolute; left:10px; top:88px; width:98%; height:30px; z-index:2"> 
    <table width="539" border="0" align="center">
      <tr bgcolor="#6AB5FF"> 
        <td width="60"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Print</font></strong></div></td>
        <td width="171"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">RA 
            Number</font></strong></div></td>
        <td width="171"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">RA 
            Date</font></strong></div></td>
        <td width="119"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">PO 
            Number </font></strong></div></td>
      </tr>
    </table>
  </div>
  <div id="Layer1" style="position:absolute; left:9px; top:110px; width:98%; height:350px; z-index:1; overflow: auto;"> 
    <strong><font size="2" face="Arial, Helvetica, sans-serif"> </font></strong> 
    <table width="536" border="0" align="center" bgcolor="#DEEDD1">
      <tr> 
        <td colspan="4"><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            <? 
				if ($from_date=="") {
					$query_date = "";
				} else {
					$query_date = " AND (raDate >= '$from_date') AND (raDate <= '$to_date')";
				}
				############################# dont forget to get the company code ##################################
				$query_ci_header="SELECT TOP 100 PERCENT tblRaHeader.raNumber, tblPoHeader.poNumber , tblPoHeader.poTerms, CONVERT(varchar, tblRaHeader.raDate, 101) AS raDate, 
								  CONVERT(varchar, tblPoHeader.suppCode) + ' - ' + UPPER(tblSuppliers.suppName) AS suppName, CONVERT(varchar, 
								  tblPoHeader.poBuyer) + ' - ' + UPPER(tblBuyers.buyerName) AS buyerName, CONVERT(varchar, tblPoHeader.poDate, 101) AS poDate, tblRaHeader.compCode, tblRaHeader.raRemarks
								  FROM tblRaHeader INNER JOIN
								  tblPoHeader ON tblRaHeader.poNumber = tblPoHeader.poNumber INNER JOIN
								  tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
								  tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
								  WHERE (tblRaHeader.compCode = $company_code) $query_date
								  ORDER BY tblRaHeader.raNumber DESC";
				$result_ci_header=mssql_query($query_ci_header);
				$num_ci_header = mssql_num_rows($result_ci_header);
				for ($i=0;$i<$num_ci_header;$i++){ 
					$grid_ra_no=mssql_result($result_ci_header,$i,"raNumber");
					$grid_date=mssql_result($result_ci_header,$i,"raDate");
					$grid_po_number=mssql_result($result_ci_header,$i,"poNumber");
			?>
            </font></strong></div></td>
      </tr>
      <tr bordercolor="#DEEDD1" bgcolor="#DEEDD1"> 
        <td width="71"><div align="center"> <strong><font size="2" face="Arial, Helvetica, sans-serif"><a href="ra_pdf.php?ra_number=<? echo $grid_ra_no; ?>" target="_blank"><img src="../images/s_f_prnt.gif" border="0" /></a> 
            </font></strong> </div></td>
        <td width="169"><font size="2"><? echo $grid_ra_no; ?></font><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
          </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font></td>
        <td width="174" bgcolor="#DEEDD1"><font size="2"> 
          <? $date = new DateTime($grid_date);
						  $grid_date = $date->format("m-d-Y");
						  echo $grid_date; ?>
          </font></td>
        <td width="104"><font size="2"><? echo $grid_po_number; }?></font></td>
      </tr>
    </table>
  </div>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  <input name="hide_from_date" type="hidden" id="hide_from_date3" value="<?php echo $from_date; ?>">
  <input name="hide_to_date" type="hidden" id="hide_to_date4" value="<?php echo $to_date; ?>">
  <input name="hide_num_ci_header" type="hidden" id="hide_to_date3" value="<?php echo $num_ci_header; ?>">
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
</body>
</html>

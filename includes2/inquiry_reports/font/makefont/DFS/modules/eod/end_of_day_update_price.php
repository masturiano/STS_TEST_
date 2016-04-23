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

if ($from_date=="") {
	$query_date = "";
} else {
	$query_date = " AND (dbo.tblProdPrice.dateUpdated >= '$from_date') AND (dbo.tblProdPrice.dateUpdated <= '$to_date')";
}
############################# dont forget to get the company code ##################################
$query_ci_header="SELECT dbo.tblProdPrice.prdNumber, dbo.tblUpc.upcCode, dbo.tblProdPrice.regUnitPrice, dbo.tblUpc.upcDesc, dbo.tblProdPrice.dateUpdated, 
	dbo.tblProdMast.prdDeptCode,dbo.tblProdMast.prdGrpCode, dbo.tblProdPrice.compCode
	FROM dbo.tblProdPrice INNER JOIN
	dbo.tblUpc ON dbo.tblProdPrice.prdNumber = dbo.tblUpc.prdNumber INNER JOIN
	dbo.tblProdMast ON dbo.tblProdPrice.prdNumber = dbo.tblProdMast.prdNumber
	WHERE (dbo.tblProdPrice.compCode = $company_code) $query_date
	ORDER BY dbo.tblProdPrice.prdNumber";
$result_ci_header=mssql_query($query_ci_header);
$num_ci_header = mssql_num_rows($result_ci_header);
if ($num_ci_header>0) {
	$meron="";
} else {
	$meron="disabled=\"true\"";
}
?>

<html>
<head>
<title>End of Day - Update Product Unit Price</title>
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

function checkAll() {	
	var frm = document.form_print;
	var cnt = parseInt(frm.txtCOUNT.value);
	for (i=0; i<=cnt; i++)
		eval("frm.btncheck" + i + ".checked=true;");
}
	
function clearSelection() {
	var frm = document.form_print;
	var cnt = parseInt(frm.txtCOUNT.value);
	for (i=0; i<=cnt; i++)
		eval("frm.btncheck" + i + ".checked=false;");
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
<form form method="POST" enctype="multipart/form-data" name="form_export" action="send.php">
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
  <div id="Layer2" style="position:absolute; left:8px; top:75px; width:98%; height:30px; z-index:2; background-color: #F0F0F0; layer-background-color: #F0F0F0; border: 1px none #000000;"> 
    <table width="754" border="0" align="center" >
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th width="109" height="20" nowrap="nowrap" class="style6"><strong><font size="2" face="Arial, Helvetica, sans-serif">
          <input name='print_ci' <? echo $meron; ?> type='submit' class='queryButton' id='print_ci' title='Display the Inventory Transaction' value='Export to Text File'/>
          </font></strong></th>
        <th width="77" height="20" nowrap="nowrap" class="style6">SKU</th>
        <th width="111" height="20" nowrap="nowrap" class="style6"><strong><font size="2" face="Arial, Helvetica, sans-serif">UPC 
          Code</font></strong></th>
        <th width="73" height="20" nowrap="nowrap" class="style6"><strong><font size="2" face="Arial, Helvetica, sans-serif">Grp/Dept</font></strong></th>
        <th width="114" height="20" nowrap="nowrap" class="style6"><strong><font size="2" face="Arial, Helvetica, sans-serif">Unit 
          Price </font></strong></th>
        <td width="244"><div > 
            <div align="center">&nbsp;<strong><font size="2" face="Arial, Helvetica, sans-serif">Description</font></strong></div>
          </div></td>
      </tr>
    </table>
  </div>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  <input name="hide_from_date2" type="hidden" id="hide_from_date22" value="<?php echo $from_date; ?>">
  <input name="hide_to_date2" type="hidden" id="hide_to_date22" value="<?php echo $to_date; ?>">
  <input name="hide_num_ci_header2" type="hidden" id="hide_num_ci_header22" value="<?php echo $num_ci_header; ?>">
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
<form name="form_search" method="post" action="end_of_day_update_price.php">
  <table width="300" border="0" align="center" >
    <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
      <th width="70" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">From 
          Date </font></div></th>
      <th width="118" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name="from_date" type="text" id="from_date3"   onBlur="val_date();" value="<? echo $from_date?>" size="10" readonly="true">
          <a href="javascript:void(0)" onClick="showCalendar(form_search.from_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
          </font></div></th>
      <th width="98" height="20" rowspan="2" nowrap="nowrap" class="style6"><font size="2" face="Arial, Helvetica, sans-serif"> 
        <input name='view_ci' type='button' class='queryButton' id='view_ci4' title='Display the Inventory Transaction' value='Search' onClick="val_all();"/>
        </font></th>
    </tr>
    <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
      <th width="70" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">To 
          Date </font></div></th>
      <th width="118" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name="to_date" type="text" id="to_date2" onBlur="val_date();" value="<? echo $to_date?>" size="10" readonly="true">
          <a href="javascript:void(0)" onClick="showCalendar(form_search.to_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
          </font></div></th>
    </tr>
  </table>
</form>
<form action="makeup_adjstmnt_slip_pdf.php" method="post" name="form_print" target="_blank" id="form_print">
  <div id="Layer1" style="position:absolute; left:10px; top:103px; width:98%; height:400px; z-index:1; overflow: auto; background-color: #F0F0F0; layer-background-color: #F0F0F0; border: 1px none #000000;"> 
    <strong><font size="2" face="Arial, Helvetica, sans-serif"> </font></strong> 
    <table width="754" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr> 
        <td colspan="5"><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            <? 
				for ($i=0;$i<$num_ci_header;$i++){ 
					$grid_sku=mssql_result($result_ci_header,$i,"prdNumber");
					$grid_upc=mssql_result($result_ci_header,$i,"upcCode");
					$grid_group=mssql_result($result_ci_header,$i,"prdGrpCode");
					$grid_dept=mssql_result($result_ci_header,$i,"prdDeptCode");
					$grid_price=mssql_result($result_ci_header,$i,"regUnitPrice");
					$grid_desc=mssql_result($result_ci_header,$i,"upcDesc");
			?>
            </font></strong></div></td>
      </tr>
      <tr> 
        <td width="136"><div align="center"> 
            <input name="check<? echo $i; ?>" type="checkbox" disabled="true" id='btncheck<?=$i?>' value="<? echo $grid_upc; ?>" checked>
          </div></td>
        <td width="96"><font size="2"><? echo $grid_sku; ?></font></td>
        <td width="106"><font size="2"><? echo $grid_upc; ?></font></td>
        <td width="61"><div align="center"><font size="2"><? echo $grid_group . " / " . $grid_dept; ?></font></div></td>
        <td width="96"><div align="right"><font size="2"><? echo $grid_price; ?></font></div></td>
        <td width="259"><div align="center"><font size="2"><? echo $grid_desc; }?></font></div></td>
      </tr>
    </table>
    <p>&nbsp; </p>
    <p>&nbsp;
       </p>
  </div>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  <input name="hide_from_date" type="hidden" id="hide_from_date3" value="<?php echo $from_date; ?>">
  <input name="hide_to_date" type="hidden" id="hide_to_date4" value="<?php echo $to_date; ?>">
  <input name="hide_num_ci_header" type="hidden" id="hide_to_date3" value="<?php echo $num_ci_header; ?>">
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
<p>&nbsp;</p>
</body>
</html>

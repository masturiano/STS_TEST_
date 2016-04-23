<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function.php";
$db = new DB;
$db->connect();
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$today = date("m/d/Y");

$box_group=$_POST['box_group'];
$from_date=$_POST['from_date'];
$from_location=$_POST['from_location'];
if ($from_location =="") {
	$rstSlsLoc=mssql_query("SELECT * FROM tblLocation WHERE compCode = $company_code AND locType = 'S'");
	$numSlsLoc = mssql_num_rows($rstSlsLoc);
	if ($numSlsLoc>"") {
		$from_location=mssql_result($rstSlsLoc,0,"locCode")."-".mssql_result($rstSlsLoc,0,"locName");
	} else {
		$from_location="";
	}
}

if ($from_date =="") {
	$from_loc=getCodeofString($from_location); 
	$rstSlsCntrl=mssql_query("SELECT * FROM tblDlySalesControl WHERE compCode = $company_code AND locCode = $from_loc ORDER BY slsDate DESC");
	$numSlsCntrl = mssql_num_rows($rstSlsCntrl);
	if ($numSlsCntrl) {
		$from_date=mssql_result($rstSlsCntrl,0,"slsDate");
		if ($from_date>"") {
			$from_date = new DateTime($from_date);
			$from_date = $from_date->format("m/d/Y");		
		} else {
			$from_date="";
		}
	} else {
		$from_date="";
	}
}
?>

<html>
<head>
<title>Sales Inquiry</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script type='text/javascript' src='sales_inquiry_javascript.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
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

<body onLoad="salesbyproduct_load('<? echo $from_location; ?>','<? echo $from_date; ?>');">
<form action="sales_inquiry.php" method="post" name="form_search" id="form_search">
  <table width="100%" border="0">
    <tr bgcolor="#DEEDD1"> 
      <td width="6%"><font size="2" face="Arial, Helvetica, sans-serif">Location<font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></font></td>
      <td width="14%"><font size="2" face="Arial, Helvetica, sans-serif"><font size="2" face="Arial, Helvetica, sans-serif"><font size="2" face="Arial, Helvetica, sans-serif"> 
        <select name="from_location" id="from_location" style="width:100px; height:18px;" onchange="salesbyproduct_load();">
          <option selected><? echo $from_location;  ?></option>
          <?
		  	$query_from_location="SELECT * FROM tblLocation WHERE compCode = $company_code AND locType = 'S' ORDER BY locCode ASC";
			$result_from_location=mssql_query($query_from_location);
			$num_from_location = mssql_num_rows($result_from_location);	
			for ($i=0;$i<$num_from_location;$i++){  
					$from_loc_code=mssql_result($result_from_location,$i,"locCode"); 
					$from_loc_name=mssql_result($result_from_location,$i,"locName"); 
				?>
          <option><? echo $from_loc_code."-".$from_loc_name; ?></option>
          <? } ?>
        </select>
        </font> </font></font></td>
      <td width="66%"><font size="2" face="Arial, Helvetica, sans-serif"><font size="2" face="Arial, Helvetica, sans-serif">Sales 
        Date 
        <input name="from_date" style="height:20px" type="text" id="from_date"   onBlur="salesbyproduct_load();" value="<? echo $from_date?>" size="10" readonly="true">
        <a href="javascript:void(0)" onClick="showCalendar(form_search.from_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
        </font></font></td>
      <td width="14%"><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
        <input name='print' type='button' style="width:80px; height:19px" class='queryButton' id='print4' title='Display Daily Sales Summary Report' value='Print' onClick="printdailysalesbyproduct_pdf();"/>
        </font></strong></font></strong></font></td>
    </tr>
  </table>
  <div id="Layer1" style="position:absolute; left:9px; top:35px; width:98%; height:430px; z-index:1; overflow: scroll;"> 
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#EEEEEE"><div align="center">
            <input name="textfield" type="text"  id="textfield" size="50" readonly="true" style="text-align: center; border:0px; height:16px; background-color:#EEEEEE;">
          </div></td>
      </tr>
    </table>
    <table width='100%' border='0' id='table_sales' style="Font-size:12px;" rules="all">
      <tr bgcolor='#6AB5FF'> 
        <td height="0" colspan="6"></td>
      </tr>
      <tr bgcolor='#DEEDD1'> 
        <td><div align="left"><strong>SKU</strong></div></td>
        <td><div align="left"><strong>Description</strong></div></td>
        <td><div align="right"><strong>Unit Price</strong></div></td>
        <td><div align="right"><strong>Qty Sold</strong></div></td>
        <td><div align="right"><strong>Net Amount</strong></div></td>
        <td><div align="right"><strong>Discount</strong></div></td>
      </tr>
    </table>
  </div>
  </form>
</body>
</html>

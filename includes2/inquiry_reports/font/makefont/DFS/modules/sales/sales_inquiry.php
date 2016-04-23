<cfprocessingdirective pageEncoding="iso-8859-1">
<cfcontent type="text/html; charset=iso-8859-1">
<cfset setEncoding("URL", "iso-8859-1")>
<cfset setEncoding("FORM", "iso-8859-1")>
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

$radio_by=$_POST['radio_by'];
$radio_product=$_POST['radio_product'];
$prod_code1=$_POST['prod_code1'];
$prod_code2=$_POST['prod_code2'];
$box_group=$_POST['box_group'];
$box_dept=$_POST['box_dept'];
$box_cls=$_POST['box_cls'];
$box_subcls=$_POST['box_subcls'];

$from_date=$_POST['from_date'];
$to_date=$_POST['to_date'];
if ($from_date =="") {
	$from_date=$today;
	$to_date=$today;
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

<body onLoad="document.form_search.by_search.value='by_product'; document.form_search.code_desc.value='check_code';">
<form action="sales_inquiry.php" method="post" name="form_search" id="form_search">
  <table width="100%" border="0">
    <tr bgcolor="#6AB5FF"> 
      <td colspan="2"> 
        <table width="100%" border="0">
          <tr bgcolor="#DEEDD1">
            <td width="50%"><font size="2" face="Arial, Helvetica, sans-serif">From Date 
              <input name="from_date" style="height:20px" type="text" id="from_date2"   onBlur="val_date();" value="<? echo $from_date?>" size="10" readonly="true">
              <a href="javascript:void(0)" onClick="showCalendar(form_search.from_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
              To Date 
              <input name="to_date" style="height:20px" type="text" id="to_date2" onBlur="val_date();" value="<? echo $to_date?>" size="10" readonly="true">
              <a href="javascript:void(0)" onClick="showCalendar(form_search.to_date,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
              <input name="hide_rows" type="hidden" id="hide_rows2" value="0">
              <input name="by_search" type="hidden" id="by_search2">
              <input name="code_desc" type="hidden" id="code_desc">
              </font></td>
            <td width="50%"><font size="2" face="Arial, Helvetica, sans-serif">Location<font size="2" face="Arial, Helvetica, sans-serif"> 
              <select name="from_location" id="from_location" style="width:100px; height:18px;" onChange="val_sales_returns_void_amt();">
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
              </font></font></td>
          </tr>
        </table>
        </td>
      <td width="11%" rowspan="3"><p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name='search_button' style="width:80px; height:19px" type='button' class='queryButton' id='search_button' title='Display the Inventory Transaction' value='Search' onClick="get_class(this.id);"/>
          </font><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name='print' <? echo $meron; ?> type='button' style="width:80px; height:19px" class='queryButton' id='print' title='Display the Inventory Transaction' value='Print' onClick="print_pdf();"/>
          </font></strong></font></strong></font></p>
        </td>
    </tr>
    <tr bgcolor="#6AB5FF"> 
      <td height="52"><input name="radio_by" type="radio" id="by_product" value="by_product" checked onClick="document.form_search.by_search.value='by_product'">
        By Product</td>
      <td><table width="100%" border="0" align="left" >
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <th width="71" rowspan="2" nowrap="nowrap" class="style6"> <div align="left"> 
                <input name="radio_product" id="check_code" type="radio" onClick="document.form_search.code_desc.value='check_code'; document.form_search.prod_code1.value ='';  document.form_search.prod_code2.value ='';" value="check_code" checked <? echo $check_code; ?> >
                Code </div>
              <div align="left"> 
                <input type="radio" name="radio_product" id="check_desc" value="check_desc" <? echo $check_desc; ?>  onClick="document.form_search.code_desc.value='check_desc'; document.form_search.prod_code1.value ='';  document.form_search.prod_code2.value ='';" >
                Desc</div></th>
            <th width="46" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">From</font></div></th>
            <th width="558" height="20" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
                <input name="prod_code1" type="text" id="prod_code1" onFocus="if(this.value=='type here')this.value='';" style="height:20px" value="type here" size="30" maxlength="40">
                </font></div></th>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <th width="46" height="22" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">To</font></div></th>
            <th width="558" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
                <input name="prod_code2" type="text" id="prod_code2"  onFocus="if(this.value=='type here')this.value='';" style="height:20px" value="type here" size="30" maxlength="40">
                <input name="check_hide" type="hidden" id="check_hide">
                </font></div></th>
          </tr>
        </table>
        </td>
    </tr>
    <tr bgcolor="#6AB5FF"> 
      <td width="19%" bgcolor="#6AB5FF"><p> 
          <input name="radio_by" id="by_class" type="radio" value="by_class" onClick="document.form_search.by_search.value='by_class'">
          By Grp/Dept/Cls/SubCls</p></td>
      <td width="70%"> <select name='box_group' id='box_group' style='width:150px; height:18px' onclick="get_class(this.id);" >
          <option selected> <? echo $box_group; ?> </option>
          <?
		$query_prod_group="SELECT * FROM tblProdClass WHERE prdClstStat = 'A' AND prdClsLvl = 1 ORDER BY prdGrpCode ASC";
		$result_prod_group=mssql_query($query_prod_group);
		$num_prod_group = mssql_num_rows($result_prod_group);
		for ($i=0;$i<$num_prod_group;$i++){  
				$prod_group_code=mssql_result($result_prod_group,$i,"prdGrpCode"); 
				$prod_group_desc=mssql_result($result_prod_group,$i,"prdClsDesc"); 
			
				echo "<option>$prod_group_code-$prod_group_desc</option>";
		}
		?>
        </select> 
        <select name='box_dept' id='box_dept' style='width:150px; height:18px' onclick="get_class(this.id);">
          <option selected> <? echo $box_dept; ?> </option>
        </select>
        <select name='box_cls' id='box_cls' style='width:150px; height:18px' onclick="get_class(this.id);">
          <option selected> <? echo $box_cls; ?> </option>
        </select>
        <select name='box_subcls' id='box_subcls' style='width:150px; height:18px'>
          <option selected> <? echo $box_subcls; ?> </option>
        </select> </td>
    </tr>
  </table>
  <div id="Layer1" style="position:absolute; left:9px; top:119px; width:98%; height:430px; z-index:1; overflow: scroll;"> 
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#EFEFEF"><div align="center">
            <input name="textfield" type="text"  id="textfield" style="text-align: center; border:0px; height:16px; background-color:#EFEFEF;" size="50" readonly="true">
          </div></td>
      </tr>
    </table>
    <table width='100%' border='0' id='table_sales' style="Font-size:12px;" rules="rows">
      <tr bgcolor='#6AB5FF'> 
        <td height="0" colspan="7"></td>
      </tr>
      <tr bgcolor='#DEEDD1'> 
        <td><div align="left"><strong>SKU</strong></div></td>
        <td><div align="left"><strong>Description</strong></div></td>
        <td><div align="left"><strong>Date</strong></div></td>
        <td><div align="right"><strong>Unit Price</strong></div></td>
        <td><div align="right"><strong>Qty Sold</strong></div></td>
        <td><div align="right"><strong>Net Amount</strong></div></td>
        <td><div align="right"><strong>Discount</strong></div></td>
      </tr>
    </table>
  </div>
  <p>&nbsp;</p>
</form>
</body>
</html>

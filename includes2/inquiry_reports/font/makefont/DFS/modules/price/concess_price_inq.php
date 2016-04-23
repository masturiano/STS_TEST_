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
$prod_code1=$_POST['prod_code1'];
$prod_code2=$_POST['prod_code2'];
$hide_action=$_POST['hide_action'];
$radio_search=$_POST['radio_search'];
if ($radio_search=="") {
	$radio_search="check_code";
}
if ($radio_search=="check_code") {
	$check_code = "checked";
	$prd_code_desc = "prdNumber";
}
if ($radio_search=="check_desc") {
	$check_desc = "checked";
	$prd_code_desc = "prdDesc";
}
$num_product=0;
$message="";
################################ if print button is click


if ($prod_code1>"" && $prod_code2>"") {
	$query_product="SELECT dbo.tblConsessPrice.compCode, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, dbo.tblProdMast.prdConv, dbo.tblConsessPrice.umCode, 
                      dbo.tblConsessPrice.regUnitPrice, dbo.tblConsessPrice.regPriceStart, dbo.tblConsessPrice.promoUnitprice, dbo.tblConsessPrice.promoPriceStart, 
                      dbo.tblConsessPrice.promoPriceEnd, dbo.tblAveCost.aveUnitCost
					  FROM dbo.tblConsessPrice LEFT JOIN
                      dbo.tblProdMast ON dbo.tblConsessPrice.prdNumber = dbo.tblProdMast.prdNumber LEFT JOIN
                      dbo.tblAveCost ON dbo.tblConsessPrice.prdNumber = dbo.tblAveCost.prdNumber
					WHERE (dbo.tblConsessPrice.compCode=$company_code) AND (dbo.tblProdMast.$prd_code_desc BETWEEN '$prod_code1' AND '$prod_code2') 
					ORDER BY dbo.tblProdMast.prdDesc ASC";
	$result_product=mssql_query($query_product);
	$num_product = mssql_num_rows($result_product);
	if ($num_product < 1) {
		$message = "No records found.";
	} else {
		$message = "$num_product record/s found.";
	}
} else { 
	if ($prod_code1>"") {
		$query_product="SELECT dbo.tblConsessPrice.compCode, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, dbo.tblProdMast.prdConv, dbo.tblConsessPrice.umCode, 
                      dbo.tblConsessPrice.regUnitPrice, dbo.tblConsessPrice.regPriceStart, dbo.tblConsessPrice.promoUnitprice, dbo.tblConsessPrice.promoPriceStart, 
                      dbo.tblConsessPrice.promoPriceEnd, dbo.tblAveCost.aveUnitCost
					  FROM dbo.tblConsessPrice LEFT JOIN
                      dbo.tblProdMast ON dbo.tblConsessPrice.prdNumber = dbo.tblProdMast.prdNumber LEFT JOIN
                      dbo.tblAveCost ON dbo.tblConsessPrice.prdNumber = dbo.tblAveCost.prdNumber
					WHERE (dbo.tblConsessPrice.compCode=$company_code) AND (dbo.tblProdMast.$prd_code_desc LIKE '$prod_code1%')  
					ORDER BY dbo.tblProdMast.prdDesc ASC";
		$result_product=mssql_query($query_product);
		$num_product = mssql_num_rows($result_product);
		if ($num_product < 1) {
			$message = "No records found.";
		} else {
			$message = "$num_product record/s found.";
		}
	} 
	if ($prod_code2>"") {
		$query_product="SELECT dbo.tblConsessPrice.compCode, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, dbo.tblProdMast.prdConv, dbo.tblConsessPrice.umCode, 
                      dbo.tblConsessPrice.regUnitPrice, dbo.tblConsessPrice.regPriceStart, dbo.tblConsessPrice.promoUnitprice, dbo.tblConsessPrice.promoPriceStart, 
                      dbo.tblConsessPrice.promoPriceEnd, dbo.tblAveCost.aveUnitCost
					  FROM dbo.tblConsessPrice LEFT JOIN
                      dbo.tblProdMast ON dbo.tblConsessPrice.prdNumber = dbo.tblProdMast.prdNumber LEFT JOIN
                      dbo.tblAveCost ON dbo.tblConsessPrice.prdNumber = dbo.tblAveCost.prdNumber
					WHERE (dbo.tblConsessPrice.compCode=$company_code) AND (dbo.tblProdMast.$prd_code_desc LIKE '$prod_code2%')  
					ORDER BY dbo.tblProdMast.prdDesc ASC";
		$result_product=mssql_query($query_product);
		$num_product = mssql_num_rows($result_product);
		if ($num_product < 1) {
			$message = "No records found.";
		} else {
			$message = "$num_product record/s found.";
		}
	}
	
}



if ($num_product>0) {
	$meron="";
} else {
	$meron="disabled=\"true\"";
}

############################# dont forget to get the company code ##################################

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
 	var prod_code1=document.form_search.prod_code1.value
    var prod_code2=document.form_search.prod_code2.value
	if(prod_code1 == "" && prod_code2 == "") {
		alert("Please key-in product code.");
		document.form_search.prod_code1.value="";
		document.form_search.prod_code1.focus();
		return false;
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
function val_prod_code1() {
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	var prod_code1 = document.form_search.prod_code1.value;
		if(!prod_code1.match(numeric_expression) && prod_code1 !="") {
			alert("Product No: Numbers only");
			document.form_search.prod_code1.value="";
			document.form_search.prod_code1.focus();
			return false;
		} 
}
function val_prod_code2() {
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	var prod_code2 = document.form_search.prod_code2.value;
		if(!prod_code2.match(numeric_expression) && prod_code2 !="") {
			alert("Product No: Numbers only");
			document.form_search.prod_code2.value="";
			document.form_search.prod_code2.focus();
			return false;
		} 
}
function val_prod1() {
	var prod_code1 = document.form_search.prod_code1.value;
	var check_hide = document.form_search.check_hide.value;
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	if (check_hide=="check_code") {
		if(!prod_code1.match(numeric_expression) && prod_code1>"") {
			alert("Product Number : Numbers only");
			document.form_search.prod_code1.value="";
			return false;
		} 
	}
}
function val_prod2() {
	var prod_code2 = document.form_search.prod_code2.value;
	var check_hide = document.form_search.check_hide.value;
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	if (check_hide=="check_code") {
		if(!prod_code2.match(numeric_expression) && prod_code2>"") {
			alert("Product Number : Numbers only");
			document.form_search.prod_code2.value="";
			return false;
		} 
	}
}
function print_pdf() {
	document.form_print_pdf.submit();
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

<body onLoad="document.form_search.check_hide.value='check_code'">
<form name="form_search" method="post" action="concess_price_inq.php">
  <table width="543" border="0" align="center" >
    <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
      <th width="100" nowrap="nowrap" class="style6"> <div align="left">
          <input name="radio_search" type="radio" value="check_code" <? echo $check_code; ?> onClick="document.form_search.check_hide.value='check_code'; document.form_search.prod_code1.value ='';  document.form_search.prod_code2.value ='';" >
          Code </div></th>
      <th width="33" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">From</font></div></th>
      <th width="308" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name="prod_code1" type="text" id="prod_code1" onChange="val_prod1();" onBlur="val_date();" value="<? echo $prod_code1?>" size="40" maxlength="40">
          </font></div></th>
      <th width="84" rowspan="2" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><font size="2" face="Arial, Helvetica, sans-serif"> 
        <input name='search_button' style="width:80px;" type='button' class='queryButton' id='view_ci4' title='Display the Inventory Transaction' value='Search' onClick="val_all();"/>
        </font><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
        <input name='print' <? echo $meron; ?> type='button' style="width:80px;" class='queryButton' id='print4' title='Display the Inventory Transaction' value='Print' onClick="print_pdf();"/>
        </font></strong></font></strong></font></th>
    </tr>
    <tr nowrap="wrap"  bgcolor="#DEEDD1">
      <th width="100" nowrap="nowrap" class="style6"><div align="left">
          <input type="radio" name="radio_search" value="check_desc" <? echo $check_desc; ?>  onClick="document.form_search.check_hide.value='check_desc'; document.form_search.prod_code1.value ='';  document.form_search.prod_code2.value ='';" >
          Desc</div></th>
      <th width="33" height="28" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">To</font></div></th>
      <th width="308" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
          <input name="prod_code2" type="text" id="prod_code2" onChange="val_prod2();" onBlur="val_date();" value="<? echo $prod_code2?>" size="40" maxlength="40">
          <input name="check_hide" type="hidden" id="check_hide">
          </font></div></th>
    </tr>
  </table>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
<form action="concess_price_pdf.php" method="post" name="form_print" target="_blank" id="form_print">
  <div id="Layer1" style="position:absolute; left:10px; top:75px; width:98%; height:440px; z-index:1; overflow: auto; background-color: #F0F0F0; layer-background-color: #F0F0F0; border: 1px none #000000;"> 
    <strong><font size="2" face="Arial, Helvetica, sans-serif"> </font></strong> 
    <table width="100%" border="0" align="center" >
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th height="20" colspan="8" nowrap="nowrap" class="style6"><div align="center"></div>
          <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            </font><font size="2"><? echo $message; ?></font></strong> </font></strong></font></div></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#6AB5FF"> 
        <th width="192" height="20" rowspan="2" nowrap="nowrap" class="style6"><font size="2" face="Arial, Helvetica, sans-serif">Product 
          Code and Description</font></th>
        <th width="96" rowspan="2" nowrap="nowrap" class="style6">Sell/Conv Unit</th>
        <th height="20" colspan="2" nowrap="nowrap" class="style6">Regular<font size="2" face="Arial, Helvetica, sans-serif"></font></th>
        <th height="20" colspan="3" nowrap="nowrap" class="style6">Promo</th>
        <th width="101" height="-2" nowrap="nowrap" bgcolor="#6AB5FF" class="style6"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
          </font></strong></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th width="63" nowrap="nowrap" bgcolor="#6AB5FF" class="style6">Price</th>
        <th width="61" nowrap="nowrap" bgcolor="#6AB5FF" class="style6"> Start</th>
        <th width="64" nowrap="nowrap" bgcolor="#6AB5FF" class="style6">Price</th>
        <th width="63" nowrap="nowrap" bgcolor="#6AB5FF" class="style6">Start</th>
        <th width="65" height="20" nowrap="nowrap" bgcolor="#6AB5FF" class="style6"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">End</font></div></th>
        <th width="101" height="20" nowrap="nowrap" bgcolor="#6AB5FF" class="style6">Ave.Unit 
          Cost <strong><font size="2" face="Arial, Helvetica, sans-serif"> 
          <? 
				for ($i=0;$i<$num_product;$i++){ 
					$grid_code=mssql_result($result_product,$i,"prdNumber");
					$grid_desc=mssql_result($result_product,$i,"prdDesc");
					$grid_desc = str_replace("\\","",$grid_desc);
					$grid_prod=$grid_code." - ".$grid_desc;
					$grid_um=mssql_result($result_product,$i,"umCode");
					$grid_conv=mssql_result($result_product,$i,"prdConv");
					$grid_conv = number_format($grid_conv,0);
					$grid_um_conv = $grid_um."/".$grid_conv;
					$grid_reg_price=mssql_result($result_product,$i,"regUnitPrice");
					if ($grid_reg_price > 0) {
						$grid_reg_price = number_format($grid_reg_price,2);
					} else {
						$grid_reg_price = "";
					}
					$grid_reg_start=mssql_result($result_product,$i,"regPriceStart");
					if ($grid_reg_start=="") {
						$grid_reg_start = "";
					} else {
						$date = new DateTime($grid_reg_start);
						$grid_reg_start = $date->format("m/d/Y");
					}
					$grid_promo_price=mssql_result($result_product,$i,"promoUnitPrice");
					if ($grid_promo_price > 0) {
						$grid_promo_price = number_format($grid_promo_price,2);
					} else {
						$grid_promo_price = "";
					}
					$grid_promo_start=mssql_result($result_product,$i,"promoPriceStart");
					if ($grid_promo_start=="") {
						$grid_promo_start = "";
					} else {
						$date = new DateTime($grid_promo_start);
						$grid_promo_start = $date->format("m/d/Y");
					}
					$grid_promo_end=mssql_result($result_product,$i,"promoPriceEnd");
					if ($grid_promo_end=="") {
						$grid_promo_end = "";
					} else {
						$date = new DateTime($grid_promo_end);
						$grid_promo_end = $date->format("m/d/Y");
					}
					$grid_unit_cost=mssql_result($result_product,$i,"aveUnitCost");
					if ($grid_doc_date=="") {
						$grid_doc_date = "";
					} else {
						$date = new DateTime($grid_doc_date);
						$grid_doc_date = $date->format("m/d/Y");
					}
					$grid_unit_cost=mssql_result($result_product,$i,"aveUnitCost");
					if ($grid_unit_cost > 0) {
						$grid_unit_cost = number_format($grid_unit_cost,4);
					} else {
						$grid_unit_cost = "";
					}
			?>
          </font></strong></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th width="192" height="20" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2"><? echo $grid_prod; ?></font></div></th>
        <th width="96" nowrap="nowrap" class="style6"><font size="2"><? echo $grid_um_conv; ?></font></th>
        <th width="63" nowrap="nowrap" class="style6"><font size="2"><? echo $grid_reg_price ?></font></th>
        <th width="61" nowrap="nowrap" class="style6"><font size="2"><? echo $grid_reg_start; ?></font></th>
        <th width="64" nowrap="nowrap" class="style6"><? echo $grid_promo_price; ?></th>
        <th width="63" nowrap="nowrap" class="style6"><? echo $grid_promo_start; ?></th>
        <th width="65" height="20" nowrap="nowrap" class="style6"><div align="center"><font size="2"><? echo $grid_promo_end; ?></font><font size="2" face="Arial, Helvetica, sans-serif"> 
            </font></div></th>
        <th width="101" height="20" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="right"><font size="2"><? echo $grid_unit_cost; }?></font></div></th>
      </tr>
    </table>
    <p>&nbsp; </p>
    <p>&nbsp;
       </p>
  </div>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
<form action="concess_price_inq_pdf.php" method="post" name="form_print_pdf" target="_blank" id="form_print_pdf">
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b>
  <input name="search_query" type="hidden" id="search_query2" value="<?php echo $query_product; ?>">
  <input name="search_selection" type="hidden" id="search_selection" value="all_record">
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
<p>&nbsp;</p>
</body>
</html>

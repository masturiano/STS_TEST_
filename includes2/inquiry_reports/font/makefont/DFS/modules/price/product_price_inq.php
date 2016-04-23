<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../etc/etc.obj.php";
require_once "../../functions/inquiry_function.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m/d/Y", $gmt);
$db = new DB;
$db->connect();

###########################################################
$productcode=$_POST['productcode'];
$findproduct=$_POST['findproduct'];
$hide_find_product=$_POST['hide_find_product'];
$hide_numeric=$_POST['hide_numeric'];
$prod_code1=$_POST['prod_code1'];
$prod_code2=$_POST['prod_code2'];
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
###########################################################

#################### click inquire button #################
$clickinquirebuttonmessage="";
if(isset($_POST['inquire'])) { 
	if ($prod_code1>"" && $prod_code2>"") {
		$query_pdf="SELECT TOP 100 PERCENT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdSellUnit, tblProdMast.prdConv, 
						tblProdPrice.regUnitPrice, tblProdPrice.regPriceStart, tblProdPrice.regPriceEvent, tblProdPrice.promoUnitprice, 
						tblProdPrice.promoPriceStart, tblProdPrice.promoPriceEnd, tblProdPrice.promoPriceEvent, 
						tblProdPrice.compCode, tblAveCost.aveUnitCost  
						FROM tblProdPrice INNER JOIN 
						tblProdMast ON tblProdPrice.prdNumber = tblProdMast.prdNumber LEFT JOIN 
						tblAveCost ON tblProdPrice.prdNumber = tblAveCost.prdNumber  
						WHERE (tblProdPrice.compCode = $company_code) AND (tblProdMast.$prd_code_desc BETWEEN '$prod_code1' AND '$prod_code2') 
						ORDER BY tblProdMast.prdDesc";
		$result_ave_price=mssql_query($query_pdf);
		$num_ave_price = mssql_num_rows($result_ave_price);
		if ($num_ave_price < 1) {
			$message = "No records found.";
		} else {
			$message = "$num_ave_price record/s found.";
		}
	} else { 
		if ($prod_code1>"") {
			
			$query_pdf="SELECT TOP 100 PERCENT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdSellUnit, tblProdMast.prdConv, 
							tblProdPrice.regUnitPrice, tblProdPrice.regPriceStart, tblProdPrice.regPriceEvent, tblProdPrice.promoUnitprice, 
							tblProdPrice.promoPriceStart, tblProdPrice.promoPriceEnd, tblProdPrice.promoPriceEvent, 
							tblProdPrice.compCode, tblAveCost.aveUnitCost 
							FROM tblProdPrice INNER JOIN 
							tblProdMast ON tblProdPrice.prdNumber = tblProdMast.prdNumber LEFT JOIN 
							tblAveCost ON tblProdPrice.prdNumber = tblAveCost.prdNumber 
							WHERE (tblProdPrice.compCode = $company_code) AND (tblProdMast.$prd_code_desc LIKE '$prod_code1%')  
							ORDER BY tblProdMast.prdDesc";
			$result_ave_price=mssql_query($query_pdf);
			$num_ave_price = mssql_num_rows($result_ave_price);
			if ($num_ave_price < 1) {
				$message = "No records found.";
			} else {
				$message = "$num_ave_price record/s found.";
			}
		} 
		if ($prod_code2>"") {
			$query_pdf="SELECT TOP 100 PERCENT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdSellUnit, tblProdMast.prdConv, 
							tblProdPrice.regUnitPrice, tblProdPrice.regPriceStart, tblProdPrice.regPriceEvent, tblProdPrice.promoUnitprice, 
							tblProdPrice.promoPriceStart, tblProdPrice.promoPriceEnd, tblProdPrice.promoPriceEvent, 
							tblProdPrice.compCode, tblAveCost.aveUnitCost 
							FROM tblProdPrice INNER JOIN 
							tblProdMast ON tblProdPrice.prdNumber = tblProdMast.prdNumber LEFT JOIN 
							tblAveCost ON tblProdPrice.prdNumber = tblAveCost.prdNumber 
							WHERE (tblProdPrice.compCode = $company_code) AND (tblProdMast.$prd_code_desc LIKE '$prod_code2%')  
							ORDER BY tblProdMast.prdDesc";
			$result_ave_price=mssql_query($query_pdf);
			$num_ave_price = mssql_num_rows($result_ave_price);
			if ($num_ave_price < 1) {
				$message = "No records found.";
			} else {
				$message = "$num_ave_price record/s found.";
			}
		}
	}
}
###################### end of click inquire button ########################################################

find_product_function($findproduct); /// inquiry_function.php

####################### display record in product #############################################################
	if (($findproduct=="") || ($findproduct=="Code or Desc")) {
		if ($hide_find_product=="") {
			$queryproduct="SELECT * FROM tblProdMast WHERE prdDelTag = 'Z' ORDER BY prdDesc ASC";
		} else {
			if ($hide_numeric=="YES") {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdNumber LIKE '%$hide_find_product%')
				ORDER BY prdDesc ASC";	
			} else {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdDesc LIKE '%$hide_find_product%')
				ORDER BY prdDesc ASC";
			}
		}
	} else {
		if ($findproduct=="*") {
			$queryproduct="SELECT * FROM tblProdMast WHERE prdDelTag = 'A' OR prdDelTag = ' ' ORDER BY prdDesc ASC";
			$hide_find_product="";
		} else {
			if(is_numeric($findproduct)) {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdNumber LIKE '%$findproduct%')
				ORDER BY prdDesc ASC";
				
				$hide_numeric="YES";
			} else {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdDesc LIKE '%$findproduct%')
				ORDER BY prdDesc ASC";
				$hide_numeric="NO";
			}
			$hide_find_product=$findproduct;
		}
	}	
	$resultproduct=mssql_query($queryproduct);
	$numproduct = mssql_num_rows($resultproduct);
####################### end of display record in product #####################################################

?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_function.js'></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}

function val_prod1() {
	var prod_code1 = document.formissi.prod_code1.value;
	var check_hide = document.formissi.check_hide.value;
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	if (check_hide=="check_code") {
		if(!prod_code1.match(numeric_expression) && prod_code1>"") {
			alert("Product Number : Numbers only");
			document.formissi.prod_code1.value="";
			return false;
		} 
	}
}
function val_prod2() {
	var prod_code2 = document.formissi.prod_code2.value;
	var check_hide = document.formissi.check_hide.value;
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	if (check_hide=="check_code") {
		if(!prod_code2.match(numeric_expression) && prod_code2>"") {
			alert("Product Number : Numbers only");
			document.formissi.prod_code2.value="";
			return false;
		} 
	}
}
MM_reloadPage(true);
//-->
</script>
</head>

<body onLoad="document.formissi.check_hide.value='check_code'" >
<div class='header' style="position:absolute; left:10px; top:8px; width:99%; height:500px; z-index:1; overflow: auto;"> 
  <div class='header'> 
    <div class='details'> 
      <form action="" method="post" name="formissi" target="_self" id="formissi">
        <table width="547" border="0" align="center" >
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <th width="89" nowrap="nowrap" class="style6"><div align="left">
                <input name="radio_search" type="radio" value="check_code" <? echo $check_code; ?> onClick="document.formissi.check_hide.value='check_code'; document.form_search.prod_code1.value ='';  document.form_search.prod_code2.value ='';" >
                Code </div></th>
            <th width="33" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">From</font></div></th>
            <th width="314" height="20" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
                <input name="prod_code1" type="text" id="prod_code1"  onChange="val_prod1();" value="<? echo $prod_code1?>" size="40" maxlength="40">
                </font></div></th>
            <th width="93" rowspan="2" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><font size="2" face="Arial, Helvetica, sans-serif"> 
              </font><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name='inquire' style="width:80px;" type='submit' class='queryButton' id='inquire' title='Display the Product Price' value='View Data'/>
              <input name='continue' style="width:80px;" type='button' class='queryButton' id='continue' title='Search New Records' onClick="javascript:document.form1.submit();" value='Clear All'/>
              <? 
				if ($num_ave_price>0) {
					echo "<span class='style6'><img src='../images/s_f_prnt.gif' /> <a href=\"product_price_inq_pdf.php?search_selection=by_product&search_query=$query_pdf&search_box=By Product\" target='_blank'>Print</a>"; 
				}
			?>
              </font></strong></font></strong></font></th>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1">
            <th width="89" nowrap="nowrap" class="style6"><div align="left">
                <input type="radio" name="radio_search" value="check_desc" <? echo $check_desc; ?>  onClick="document.formissi.check_hide.value='check_desc'; document.form_search.prod_code1.value ='';  document.form_search.prod_code2.value ='';" >
                Desc</div></th>
            <th width="33" height="28" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">To</font></div></th>
            <th width="314" nowrap="nowrap" class="style6"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
                <input name="prod_code2" type="text" id="prod_code2"  onChange="val_prod2();" value="<? echo $prod_code2?>" size="40" maxlength="40">
                <input name="check_hide" type="hidden" id="check_hide">
                </font></div></th>
          </tr>
        </table>
        </form>
      <table width="99%" border="0" align="center">
        <tr bgcolor="#6AB5FF"> 
          <td width="46%" colspan="7" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
              </font></strong><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20">
              <?
		  echo $message;
		  ?>
              </span></font></strong></div></td>
        </tr>
        <tr bgcolor="#6AB5FF"> 
          <td width="46%" rowspan="2" bgcolor="#6AB5FF"><strong><font size="2" face="Arial, Helvetica, sans-serif">Product 
            Code and Description</font></strong></td>
          <td colspan="2"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Regular</span></font></strong></div></td>
          <td colspan="3"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Promo</font></strong><strong></strong></div></td>
          <td width="10%"><div align="center"></div></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="9%" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Price</span></font></strong></div></td>
          <td width="9%" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Start</font></strong></div></td>
          <td width="8%" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Price</font></strong></div></td>
          <td width="9%" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Start</font></strong></div></td>
          <td width="9%" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">End</font></strong></div></td>
          <td bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Ave.Unit 
              Cost 
              <?
				for ($i=0;$i<$num_ave_price;$i++){ 
					$grid_code=mssql_result($result_ave_price,$i,"prdNumber");
					$grid_product=mssql_result($result_ave_price,$i,"prdDesc");
					$proddesc=str_replace("\\","",$proddesc);
					$grid_buy_unit=mssql_result($result_ave_price,$i,"prdSellUnit");
					$grid_conv=mssql_result($result_ave_price,$i,"prdConv");
					$grid_product=$grid_code." - ".$grid_product." / SuM: ".$grid_buy_unit." / Conv: ".$grid_conv;	
					$grid_reg_price=mssql_result($result_ave_price,$i,"regUnitPrice");
					if ($grid_reg_price > 0) {
						$grid_reg_price = number_format($grid_reg_price,2);
					} else {
						$grid_reg_price = "";
					}
					$grid_reg_start=mssql_result($result_ave_price,$i,"regPriceStart");
					$grid_reg_event=mssql_result($result_ave_price,$i,"regPriceEvent");
					$grid_pro_price=mssql_result($result_ave_price,$i,"promoUnitPrice");
					if ($grid_pro_price > 0) {
						$grid_pro_price  = number_format($grid_pro_price,2);
					} else {
						$grid_pro_price = "";
					}
					$grid_pro_start=mssql_result($result_ave_price,$i,"promoPriceStart");
					$grid_pro_end=mssql_result($result_ave_price,$i,"promoPriceEnd");
					$grid_pro_event=mssql_result($result_ave_price,$i,"promoPriceEvent"); 
					$ave_unit_cost=mssql_result($result_ave_price,$i,"aveUnitCost"); 
					if ($ave_unit_cost > 0) {
						$ave_unit_cost=number_format($ave_unit_cost,4);
					} else {
						$ave_unit_cost = "";
					}
			?>
              </font></strong></div></td>
        </tr>
        <tr> 
          <td bgcolor="#DEEDD1"> <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
            <?
		  echo $grid_product;
		  ?>
            </span> </font></td>
          <td bgcolor="#DEEDD1"><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
              <span class="style20"> 
              <?
		  echo $grid_reg_price;
		  ?>
              </span></font></strong></div></td>
          <td bgcolor="#DEEDD1"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
              <?
			  if($grid_reg_start>""){	
				$date = new DateTime($grid_reg_start);
				$grid_reg_start = $date->format("m/d/Y");
			  } else {
			  	$grid_reg_start="";
			  }
			  echo $grid_reg_start;
		  ?>
              </font></strong></div></td>
          <td bgcolor="#DEEDD1"><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              <?
		  echo $grid_pro_price;
		  ?>
              </span></font></strong></div></td>
          <td bgcolor="#DEEDD1"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              <?
			  if($grid_pro_start>""){
			  	$date = new DateTime($grid_pro_start);
				$grid_pro_start = $date->format("m/d/Y");
			  } else {
			  	$grid_pro_start="";
			  }
			  echo $grid_pro_start;
		  ?>
              </span></font></strong></div></td>
          <td bgcolor="#DEEDD1"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              <?
			  if($grid_pro_end>""){		
					$date = new DateTime($grid_pro_end);
					$grid_pro_end = $date->format("m/d/Y");
			  } else {
			  	$grid_pro_end="";
			  }
			  echo $grid_pro_end;
		  ?>
              </span></font></strong></div></td>
          <td bgcolor="#DEEDD1"><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              <?
		  echo $ave_unit_cost;
		  ?>
              </span><span class="style18"><span class="style20"> 
              <?
		  } 
		  ?>
              </span></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></strong></div></td>
        </tr>
      </table>
      <font size="2" face="Arial, Helvetica, sans-serif"> </font> 
      <form action="" method="post" name="form1" target="_self">
        <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
          </font> <font size="2" face="Arial, Helvetica, sans-serif"> </font> 
        </div>
      </form>
      <p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> </font><font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></p>
    </div>
  </div>
</div>
</body>
</html>

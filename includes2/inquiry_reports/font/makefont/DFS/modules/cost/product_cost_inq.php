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
	
	$productcode=$_POST['productcode'];
	$findproduct=$_POST['findproduct'];
	$vendorcode=$_POST['vendorcode'];
	$findvendor=$_POST['findvendor'];
	$radiobutton=$_POST['radiobutton'];
	$hide_find_product=$_POST['hide_find_product'];
	$hide_numeric=$_POST['hide_numeric'];
	$hide_find_vendor=$_POST['hide_find_vendor'];
	$hide_numeric2=$_POST['hide_numeric2'];
		
	 if ($radiobutton=="radioproduct") {
			$product_checked="checked";
			$vendor_checked="";
	 }
	 if ($radiobutton=="radiovendor") {
			$product_checked="";
			$vendor_checked="checked";
	 }

	$clickinquirebuttonmessage="";
	if(isset($_POST['inquire'])) { 
		if ($radiobutton=="radioproduct") {
			if ($productcode=="") {
				$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Product selected : ";
			}
		}
		if ($radiobutton=="radiovendor") {
			if ($vendorcode=="") {
				$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Vendor selected : ";
			}
		}
		if ($clickinquirebuttonmessage>"") {
			echo "<script>alert('$clickinquirebuttonmessage')</script>";
			$flagok = 1;
		} else {
			$flagok = 0;
		}
		if ($flagok<1) {  ///if no error found
			if ($radiobutton=="radioproduct") {
				$cut_prod_code=getCodeofString($productcode); ///pick in inventory_inquiry_function.php
				$cut_prod_code=trim($cut_prod_code);
				if ($cut_prod_code =="All") {
					$search_box="By Product";
					$cut_prod_code_new = "";
				} else {
					$search_box=$productcode;
					$cut_prod_code_new = " AND (dbo.tblProdMast.prdNumber = $cut_prod_code) ";
				}
				############################# dont forget to get the company code ##################################
				//$query_prod_cost="SELECT * FROM tblProdCost WHERE (prdNumber = $cut_prod_code) ORDER BY suppCode ASC";
				$search_selection="by_product";
				
				$query_pdf="SELECT TOP 100 PERCENT dbo.tblSuppliers.suppCode,dbo.tblSuppliers.suppName, dbo.tblProdCost.regUnitCost, dbo.tblProdCost.regCostStart, dbo.tblProdCost.regCostEvent, 
						   dbo.tblProdCost.promoUnitCost, dbo.tblProdCost.promoCostStart, dbo.tblProdCost.promoCostEnd, dbo.tblProdCost.promoCostEvent,
						   dbo.tblProdCost.compCode,dbo.tblProdMast.prdNumber
						   FROM dbo.tblProdCost LEFT JOIN
						   dbo.tblSuppliers ON dbo.tblProdCost.suppCode = dbo.tblSuppliers.suppCode LEFT JOIN
						   dbo.tblProdMast ON dbo.tblProdCost.prdNumber = dbo.tblProdMast.prdNumber
						   WHERE (dbo.tblProdCost.compCode = $company_code) $cut_prod_code_new
						   ORDER BY dbo.tblSuppliers.suppName ASC";
				
				$result_prod_cost=mssql_query($query_pdf);
				$num_ave_cost = mssql_num_rows($result_prod_cost);
				
				if ($num_ave_cost<1) {
					echo "<script>alert('No Product Cost for the requested Product... Please enter another.')</script>";
				} 
			}
			if ($radiobutton=="radiovendor") {
				$cut_vendor_code=getCodeofString($vendorcode); ///pick in inventory_inquiry_function.php
				$cut_vendor_code=trim($cut_vendor_code);
				if ($cut_vendor_code =="All") {
					$cut_vendor_code_new = "";
					$search_box="By Vendor";
				} else {
					$search_box=$vendorcode;
					$cut_vendor_code_new = " AND (dbo.tblSuppliers.suppCode = $cut_vendor_code) ";
				}
				############################# dont forget to get the company code ##################################
				
				############################# dont forget to get the company code ##################################
				$search_selection="by_vendor";
				$query_pdf="SELECT TOP 100 PERCENT dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, dbo.tblProdMast.prdBuyUnit, dbo.tblProdMast.prdConv, 
						   dbo.tblProdCost.regUnitCost, dbo.tblProdCost.regCostStart, dbo.tblProdCost.regCostEvent, dbo.tblProdCost.promoUnitCost, 
						   dbo.tblProdCost.promoCostStart, dbo.tblProdCost.promoCostEnd, dbo.tblProdCost.promoCostEvent,
						   dbo.tblProdCost.compCode,dbo.tblSuppliers.suppCode
						   FROM dbo.tblProdCost LEFT JOIN
						   dbo.tblProdMast ON dbo.tblProdCost.prdNumber = dbo.tblProdMast.prdNumber LEFT JOIN
						   dbo.tblSuppliers ON dbo.tblProdCost.suppCode = dbo.tblSuppliers.suppCode
						   WHERE (dbo.tblProdCost.compCode = $company_code) $cut_vendor_code_new
						   ORDER BY dbo.tblProdMast.prdDesc ASC";
				$result_prod_cost=mssql_query($query_pdf);
				$num_ave_cost = mssql_num_rows($result_prod_cost);
				if ($num_ave_cost<1) {
					echo "<script>alert('No Product Cost for the requested Vendor... Please enter another.')</script>";
				}
			}
			
		}
	}
	###################### end of click inquire button ########################################################
	if ($num_ave_cost>0) {
		$print =  "<span class='style6'><img src='../images/s_f_prnt.gif' /> <a href='product_cost_inq_pdf.php?search_query=$query_pdf&search_selection=$search_selection&search_box=$search_box' target='_blank'>Print</a>"; 
	} else {
		$print ="";
	}
	
	###########################################################
	if(isset($_POST['find2'])) { 
		if ($findvendor=="") {
			echo "<script>alert('Key-in Vendor Code or Name!')</script>";
		} else {
			if ($findvendor<>"*") {
				if(is_numeric($findvendor)) {
					$query_find_vendor="SELECT * FROM tblSuppliers 
									WHERE suppCode LIKE '%$findvendor%'
									ORDER BY suppCode ASC";
					$result_find_vendor=mssql_query($query_find_vendor);
					$num_find_vendor = mssql_num_rows($result_find_vendor);
					if ($num_find_vendor>0) {
						$find_supp_code=mssql_result($result_find_vendor,0,"suppCode");
						$find_supp_name=mssql_result($result_find_vendor,0,"suppName");
						$vendorcode = $find_supp_code."-----".$find_supp_name;
					} else {
						echo "<script>alert('No Vendor records found!')</script>";
					}
				} else {
					$query_find_vendor="SELECT * FROM tblSuppliers 
									WHERE suppName LIKE '%$findvendor%'
									ORDER BY suppName ASC";
					$result_find_vendor=mssql_query($query_find_vendor);
					$num_find_vendor = mssql_num_rows($result_find_vendor);
					if ($num_find_vendor>0) {
						$find_supp_code=mssql_result($result_find_vendor,0,"suppCode");
						$find_supp_name=mssql_result($result_find_vendor,0,"suppName");
						$vendorcode = $find_supp_code."-----".$find_supp_name;
					} else {
						echo "<script>alert('No Vendor records found!')</script>";
					}
				}
			}
		}
	}
	####################### display vendor combo #############################################################
	if (($findvendor=="") || ($findvendor=="Code or Name")) {
		if ($hide_find_vendor=="") {
			$query_vendor="SELECT * FROM tblSuppliers WHERE (suppStat = 'Z') ORDER BY suppName ASC";
		} else {
			if ($hide_numeric2=="YES") {
				$query_vendor="SELECT * FROM tblSuppliers 
				WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppCode LIKE '%$hide_find_vendor%')
				ORDER BY suppCode ASC";	
			} else {
				$query_vendor="SELECT * FROM tblSuppliers 
				WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppName LIKE '%$hide_find_vendor%')
				ORDER BY suppCode ASC";
			}
		}
	} else {
		if ($findvendor=="*") {
			$query_vendor="SELECT * FROM tblSuppliers WHERE (suppStat = 'A' OR suppStat = ' ') ORDER BY suppName ASC";
			$hide_find_vendor="";
		} else {
			if(is_numeric($findvendor)) {
				$query_vendor="SELECT * FROM tblSuppliers 
				WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppCode LIKE '%$findvendor%')
				ORDER BY suppCode ASC";
				
				$hide_numeric2="YES";
			} else {
				$query_vendor="SELECT * FROM tblSuppliers 
				WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppName LIKE '%$findvendor%')
				ORDER BY suppCode ASC";
				$hide_numeric2="NO";
			}
			$hide_find_vendor=$findvendor;
		}
	}	
	$result_vendor=mssql_query($query_vendor);
	$num_vendor = mssql_num_rows($result_vendor);
	####################### end of vendor combo #####################################################

	if(isset($_POST['find'])) { 
		if ($findproduct=="") {
			echo "<script>alert('Key-in Product Code or Description!')</script>";
		} else {
			if ($findproduct<>"*") {
				if(is_numeric($findproduct)) {
					$queryfindproduct="SELECT * FROM tblProdMast 
									WHERE prdNumber LIKE '%$findproduct%'
									ORDER BY prdNumber ASC";
					$resultfindproduct=mssql_query($queryfindproduct);
					$numfindproduct = mssql_num_rows($resultfindproduct);
					if ($numfindproduct>0) {
						$findproductprdNumber=mssql_result($resultfindproduct,0,"prdNumber");
						$findproductprdDesc=mssql_result($resultfindproduct,0,"prdDesc");
						$findproductprdSellUnit=mssql_result($resultfindproduct,0,"prdSellUnit");
						$productcode = $findproductprdNumber." - ".$findproductprdDesc." - ".$findproductprdSellUnit;
					} else {
						echo "<script>alert('No Product records found!')</script>";
					}
				} else {
					$queryfindproduct="SELECT * FROM tblProdMast 
									WHERE prdDesc LIKE '%$findproduct%'
									ORDER BY prdDesc ASC";
					$resultfindproduct=mssql_query($queryfindproduct);
					$numfindproduct = mssql_num_rows($resultfindproduct);
					if ($numfindproduct>0) {
						$findproductprdNumber=mssql_result($resultfindproduct,0,"prdNumber");
						$findproductprdDesc=mssql_result($resultfindproduct,0,"prdDesc");
						$findproductprdSellUnit=mssql_result($resultfindproduct,0,"prdSellUnit");
						$productcode = $findproductprdNumber." - ".$findproductprdDesc." - ".$findproductprdSellUnit;
					} else {
						echo "<script>alert('No Product records found!')</script>";
					}
				}
			 }
		}
	}
	
	if (($findproduct=="") || ($findproduct=="Code or Desc")) {
		if ($hide_find_product=="") {
			$queryproduct="SELECT * FROM tblProdMast WHERE prdDelTag = 'Z' ORDER BY prdNumber ASC";
		} else {
			if ($hide_numeric=="YES") {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdNumber LIKE '%$hide_find_product%')
				ORDER BY prdNumber ASC";	
			} else {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdDesc LIKE '%$hide_find_product%')
				ORDER BY prdDesc ASC";
			}
		}
	} else {
		if ($findproduct=="*") {
			$queryproduct="SELECT * FROM tblProdMast WHERE prdDelTag = 'A' OR prdDelTag = ' ' ORDER BY prdNumber ASC";
			$hide_find_product="";
		} else {
			if(is_numeric($findproduct)) {
				$queryproduct="SELECT * FROM tblProdMast 
				WHERE (prdDelTag = 'A' OR prdDelTag = ' ') AND (prdNumber LIKE '%$findproduct%')
				ORDER BY prdNumber ASC";
				
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
MM_reloadPage(true);
//-->
</script>
</head>

<body >
<div class='header'> 
  <div class='header'> 
    <div class='details'>
<form action="" method="post" name="formissi" target="_self" id="formissi">
        <table width="69%" border="0" align="center">
          <tr bgcolor="#DEEDD1"> 
            <td width="14%"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name='radiobutton' type='radio'  id='radio' onClick='javascript:document.formissi.submit();' value='radioproduct' checked checked<? echo $product_checked; ?>>
              </font></strong><font size='2' face='Arial, Helvetica, sans-serif'>Product<strong><strong> 
              </strong></strong></font></td>
            <td width="86%"><font size='2' face='Arial, Helvetica, sans-serif'> 
              <select name='productcode' id='select3' style='width:200px; height:20px'>
                <option selected><? echo $productcode; ?> </option>
                <? for ($i=0;$i<$numproduct;$i++){  
			$prodcode=mssql_result($resultproduct,$i,"prdNumber"); 
			$proddesc=mssql_result($resultproduct,$i,"prdDesc"); 
			$proddesc=str_replace("\\","",$proddesc);
			$prodsum=mssql_result($resultproduct,$i,"prdBuyUnit");
			$prodconv=mssql_result($resultproduct,$i,"prdConv");
			$prodconv=number_format($prodconv,0);
		?>
                <option><? echo $prodcode . "-----" . $proddesc . "-----" . $prodsum . "-----" . $prodconv; ?></option>
                <? }  ?>
                <option></option>
              </select>
              <input name='findproduct' type='text' id='findproduct' onFocus="if(this.value=='Code or Desc')this.value='';" value='Code or Desc' >
              <input name='find' type='submit' class='queryButton' id='find' title='Search Product' onClick='javascript:document.form1.submit();' value='Find'/>
              <span class='style20'><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class='style18'><b><b><span class='style1'><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name='hide_find_product' type='hidden' id='hide_find_product2' value='<? echo $hide_find_product; ?>'>
              <input name='hide_numeric' type='hidden' id='hide_numeric3' value='<? echo $hide_numeric; ?>'>
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              </font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><font size='2' face='Arial, Helvetica, sans-serif'><strong><strong> 
              <input name='radiobutton' type='radio' id='radio3' onClick='javascript:document.formissi.submit();' value='radiovendor'<? echo $vendor_checked; ?>>
              </strong></strong>Vendor<strong><strong> </strong></strong></font></td>
            <td><font size='2' face='Arial, Helvetica, sans-serif'> 
              <select name='vendorcode' id='select' style='width:200px; height:20px;'>
                <option selected><? echo $vendorcode; ?> </option>
                <? $result_vendor=mssql_query($query_vendor);
					$num_vendor = mssql_num_rows($result_vendor);
					for ($i=0;$i<$num_vendor;$i++){  
							$vendor_code=mssql_result($result_vendor,$i,"suppCode"); 
							$vendor_name=mssql_result($result_vendor,$i,"suppName"); 
							$vendor_name = str_replace("\\","",$vendor_name);
					?>
                <option><? echo $vendor_code . "-----" . $vendor_name; ?></option>
                <? } ?>
                <option> </option>
              </select>
              <input name='findvendor' type='text' id='findvendor2' onFocus="if(this.value=='Code or Name')this.value='';" value='Code or Name'>
              <input name='find2' type='submit' class='queryButton' id='find22' title='Search Vendor' onClick='javascript:document.form1.submit();' value='Find'/>
              <span class='style20'><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class='style18'><b><b><span class='style1'><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name='hide_find_vendor' type='hidden' id='hide_find_vendor2' value='<? echo $hide_find_vendor; ?>'>
              <input name='hide_numeric2' type='hidden' id='hide_numeric2' value='<? echo $hide_numeric2;?>'>
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              </font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td colspan="2"><div align="center"><font size='2' face='Arial, Helvetica, sans-serif'><strong><strong> 
                </strong></strong></font><font size='2' face='Arial, Helvetica, sans-serif'> 
                <input name='inquire' style ="width:80px" type='submit' class='queryButton' id='inquire2' title='Display the Product Cost' value='View Data'/>
                <input name='continue' style ="width:80px" type='button' class='queryButton' id='continue2' title='Search New Records' onClick="javascript:document.form1.submit();" value='Clear All'/>
                <? echo $print; ?> <? echo $numproduct. " total record/s"; ?></font></div></td>
          </tr>
        </table>
        <table width='100%' border='0'>
          <tr bgcolor='#FFFFFF'> 
            <td width='42%' rowspan='2' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>Description 
                (Product/Vendor) </strong></font></div></td>
            <td colspan='2' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong><span class='style8 style20'>Regular</span></strong></font></div></td>
            <td colspan='3' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>Promo</strong></font></div></td>
            <td bgcolor="#6AB5FF">&nbsp;</td>
          </tr>
          <tr bgcolor='#FFFFFF'> 
            <td width='10%' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong><span class='style8 style20'>Cost</span></strong></font></div></td>
            <td width='10%' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>Start</strong></font></div></td>
            <td width='10%' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>Cost</strong></font></div></td>
            <td width='9%' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>Start</strong></font></div></td>
            <td width='9%' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>End</strong></font></div></td>
            <td width='10%' bgcolor="#6AB5FF"><div align='center'><font size="2" face="Arial, Helvetica, sans-serif"><strong>Ave.Cost 
                in Sell Units 
                <?
				  //if ($num_ave_cost > 0) {
				  for ($i=0;$i<$num_ave_cost;$i++){ 
									switch ($radiobutton) {
										case "radioproduct":
											$grid_prod_code=mssql_result($result_prod_cost,$i,"prdNumber");
											$grid_supp_code=mssql_result($result_prod_cost,$i,"suppCode");
											$grid_supp_name=mssql_result($result_prod_cost,$i,"suppName");
											$grid_supp_name = str_replace("\\","",$grid_supp_name);
											$grid_product_vendor=$grid_supp_code." - ".$grid_supp_name;
											$grid_reg_cost=mssql_result($result_prod_cost,$i,"regUnitCost");
											if ($grid_reg_cost>0) {
												$grid_reg_ucost=number_format($grid_reg_ucost,2);
												$grid_reg_start=mssql_result($result_prod_cost,$i,"regCostStart");
												$grid_reg_event=mssql_result($result_prod_cost,$i,"regCostEvent");
												if ($grid_reg_start=="") {
													$grid_reg_start = "";
												} else {
													$date = new DateTime($grid_reg_start);
													$grid_reg_start = $date->format("m/d/Y");
												}
											} else {
												$grid_reg_ucost="";
												$grid_reg_start = "";
												$grid_reg_event="";
											}
											
											$grid_promo_ucost=mssql_result($result_prod_cost,$i,"promoUnitCost");
											if ($grid_promo_ucost>0) {
												$grid_promo_ucost=number_format($grid_promo_ucost,2);
												$grid_promo_event=mssql_result($result_prod_cost,$i,"promoCostEvent");
												$grid_promo_start=mssql_result($result_prod_cost,$i,"promoCostStart");
												if ($grid_promo_start=="") {
													$grid_promo_start = "";
												} else {
													$date = new DateTime($grid_promo_start);
													$grid_promo_start = $date->format("m/d/Y");
												}
												$grid_promo_end=mssql_result($result_prod_cost,$i,"promoCostEnd");
												if ($grid_promo_end=="") {
													$grid_promo_end = "";
												} else {
													$date = new DateTime($grid_promo_end);
													$grid_promo_end = $date->format("m/d/Y");
												}
											} else {
												$grid_promo_ucost="";
												$grid_promo_event="";
												$grid_promo_start = "";
												$grid_promo_end = "";
											}
											
											$result_ave=mssql_query("SELECT * FROM tblAveCost WHERE compCode = $company_code AND prdNumber = $grid_prod_code");
											$num_ave = mssql_num_rows($result_ave);
											if ($num_ave > 0) {
												$grid_ave_ucost=mssql_result($result_ave,0,"aveUnitCost");
												if ($grid_ave_ucost>0) {
													$grid_ave_ucost=number_format($grid_ave_ucost,2);
												} else {
													$grid_ave_ucost="";
												}	
											} else {
												$grid_ave_ucost="";
											}
											break;
										case "radiovendor":
											$grid_prod_code=mssql_result($result_prod_cost,$i,"prdNumber");
											$grid_prod_desc=mssql_result($result_prod_cost,$i,"prdDesc");
											$grid_prod_desc=str_replace("\\","",$grid_prod_desc);
											$grid_buy_unit=mssql_result($result_prod_cost,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_prod_cost,$i,"prdConv");
											$grid_conv=number_format($grid_conv,0);
											$grid_product_vendor=$grid_prod_code." - ".$grid_prod_desc."/".$grid_buy_unit."/".$grid_conv;
											$grid_reg_cost=mssql_result($result_prod_cost,$i,"regUnitCost");
											if ($grid_reg_cost>0) {
												$grid_reg_ucost=number_format($grid_reg_ucost,2);
												$grid_reg_event=mssql_result($result_prod_cost,$i,"regCostEvent");
												$grid_reg_start=mssql_result($result_prod_cost,$i,"regCostStart");
												if ($grid_reg_start=="") {
													$grid_reg_start="";
												} else {
													$date = new DateTime($grid_reg_start);
													$grid_reg_start = $date->format("m/d/Y");
												}
											} else {
												$grid_reg_ucost="";
												$grid_reg_start="";
												$grid_reg_event="";
											}	
											
											$grid_promo_ucost=mssql_result($result_prod_cost,$i,"promoUnitCost");
											if ($grid_promo_ucost>0) {
												$grid_promo_ucost=number_format($grid_promo_ucost,2);
												$grid_promo_event=mssql_result($result_prod_cost,$i,"promoCostEvent");
												$grid_promo_start=mssql_result($result_prod_cost,$i,"promoCostStart");
												if ($grid_promo_start=="") {
													$grid_promo_start="";
												} else  {
													$date = new DateTime($grid_promo_start);
													$grid_promo_start = $date->format("m/d/Y");
												}
												$grid_promo_end=mssql_result($result_prod_cost,$i,"promoCostEnd");
												if ($grid_promo_end == "") {
													$grid_promo_end = "";
												} else {
													$date = new DateTime($grid_promo_end);
													$grid_promo_end = $date->format("m/d/Y");
												}
											} else {
												$grid_promo_ucost="";
												$grid_promo_start="";
												$grid_promo_end = "";
												$grid_promo_event="";
											}
											$result_ave=mssql_query("SELECT * FROM tblAveCost WHERE compCode = $company_code AND prdNumber = $grid_prod_code");
											$num_ave = mssql_num_rows($result_ave);
											if ($num_ave > 0) {
												$grid_ave_ucost=mssql_result($result_ave,0,"aveUnitCost");
												if ($grid_ave_ucost>0) {
													$grid_ave_ucost=number_format($grid_ave_ucost,2);
												} else {
													$grid_ave_ucost="";
												}	
											} else {
												$grid_ave_ucost="";
											}
											break;
									}
					  ?>
                </strong></font></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td bgcolor="#DEEDD1"> <font size='2' face='Arial, Helvetica, sans-serif'><span class='style20'> 
              <? echo $grid_product_vendor; ?> </span> </font></td>
            <td><div align='right'><font size='2' face='Arial, Helvetica, sans-serif'> 
                <span class='style20'> <? echo $grid_reg_cost; ?> </span></font></div></td>
            <td><div align='center'><font size='2' face='Arial, Helvetica, sans-serif'> 
                <? echo $grid_reg_start;   ?> </font></div></td>
            <td><div align='right'><font size='2' face='Arial, Helvetica, sans-serif'><span class='style20'> 
                <? echo $grid_promo_ucost; ?> </span></font></div></td>
            <td><div align='center'><font size='2' face='Arial, Helvetica, sans-serif'><span class='style20'> 
                <? echo $grid_promo_start; ?> </span></font></div></td>
            <td><div align='center'><font size='2' face='Arial, Helvetica, sans-serif'><span class='style20'> 
                <? echo $grid_promo_end; ?> </span></font></div></td>
            <td><div align='right'><font size='2' face='Arial, Helvetica, sans-serif'><span class='style20'> 
                <? echo $grid_ave_ucost; ?> </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class='style18'><b><b><span class='style20'> 
                <? }  ?>
                </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
          </tr>
        </table>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
      </form>
    
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

<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function.php";
require_once "../../modules/etc/etc.obj.php";
require_once "../../modules/home/home.obj.php";

$db = new DB;
$db->connect();

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
###########################################################
$month=$_POST['month'];
$year=$_POST['year'];
$resPd=mssql_query("SELECT * FROM tblPeriod WHERE compCode = $company_code AND pdStat = 'O'");
$numPd = mssql_num_rows($resPd);
if ($numPd>0) {
	$monthPd = mssql_result($resPd,0,"pdCode");
	$yearPd = mssql_result($resPd,0,"pdYear");
} else {
	echo "<script>alert('No Open Period... Please assist to your administrator.')</script>";
}
if ($month=="" && $year=="") {
	$month = $monthPd;
	$month=getMonthNum($month); ///pick in inventory_inquiry_function.php 
	$year = $yearPd; 
} 
$todaymonth=$monthPd;
$todayyear=$yearPd;
$locationcode=$_POST['locationcode'];
$productcode=$_POST['productcode'];
$findproduct=$_POST['findproduct'];
$hide_find_product=$_POST['hide_find_product'];
$hide_numeric=$_POST['hide_numeric'];
$hide_sql=$_POST['hide_sql'];
$begBalGoodM="0.00";
$begCostM="0.00";
$mtdReceiptsQ="0.00";
$mtdSalesQ="0.00";
$mdtTransfers="0.00";
$mtdAdjustments="0.00";
$mtdCiQ="0.00";
$mtdSuQ="0.00";
$endBalGoodM="0.00";
$endCostM="0.00";
$begBalBoM="0.00";
$endBalBoM="0.00";
###########################################################

#################### click inquire button #################
$clickinquirebuttonmessage="";
if(isset($_POST['inquire'])) { 
	if (($month=="") || ($year=="")) {
		$clickinquirebuttonmessage="No Month or Year selected : ";
	}
	if (($month > "") && ($year > "")){
		$monthnum=getMonthName($month); ///pick in inventory_inquiry_function.php 
		if ($year >= $todayyear) {
			if ($year == $todayyear) {
				if ($monthnum > $todaymonth) {
					$clickinquirebuttonmessage=$clickinquirebuttonmessage."Month/Year must not be greater than the current period : ";
				}
			} else {
				$clickinquirebuttonmessage=$clickinquirebuttonmessage."Month/Year must not be greater than the current period : ";
			}
		}
	}
	if ($locationcode=="") {
		$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Location selected : ";
	}
	if ($productcode=="") {
		$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Product selected : ";
	}
	if ($clickinquirebuttonmessage>"") {
		echo "<script>alert('$clickinquirebuttonmessage')</script>";
		$flagok = 1;
	} else {
		$flagok = 0;
	}
	if ($flagok<1) {  ///if no error found
		$new1=getCodeofString($locationcode); ///pick in inventory_inquiry_function.php
		$new1=trim($new1);
		$new2=getCodeofString($productcode); ///pick in inventory_inquiry_function.php
		$new2=trim($new2);
		if ($new1=="All") {
			$all_loc="";
		} else {
			$all_loc=" AND (locCode=$new1) ";
		}
		if ($new2=="All") {
			$all_prod="";
		} else {
			$all_prod=" AND (prdNumber=$new2) ";
		}
		$queryinventorytrans="SELECT sum(begBalGoodM) as begBalGoodM_,sum(begCostM) as begCostM_,
			sum(mtdRecitQ) as mtdRecitQ_,sum(mtdRegSlesQ) as mtdRegSlesQ_,sum(mtdTransIn) as mtdTransIn_,
			sum(mtdTransOut) as mtdTransOut_,sum(mtdAdjQ) as mtdAdjQ_,sum(mtdCountAdjQ) as mtdCountAdjQ_,
			sum(mtdCiQ) as mtdCiQ_,sum(mtdSuQ) as mtdSuQ_,sum(endBalGoodM) as endBalGoodM_,
			sum(endCostM) as endCostM_, sum(endBalBoM) as endBalBoM_,sum(begBalBoM) as begBalBoM_
			FROM tblInvBalM 
			WHERE (compCode = $company_code) AND (pdMonth = '$monthnum') AND (pdYear = '$year') $all_loc $all_prod";
		$resulinventorytrans=mssql_query($queryinventorytrans);
		$numinventorytrans = mssql_num_rows($resulinventorytrans);
		if ($all_prod=="") {
			$query_per_stock="SELECT     MAX(prdNumber) AS product, MAX(locCode) AS location, SUM(begBalGoodM) AS begBalGoodM_, SUM(begCostM) AS begCostM_, SUM(mtdRecitQ) 
                    AS mtdRecitQ_, SUM(mtdRegSlesQ) AS mtdRegSlesQ_, SUM(mtdTransIn) AS mtdTransIn_, SUM(mtdTransOut) AS mtdTransOut_, SUM(mtdAdjQ) 
                    AS mtdAdjQ_, SUM(mtdCountAdjQ) AS mtdCountAdjQ_, SUM(mtdCiQ) AS mtdCiQ_, SUM(mtdSuQ) AS mtdSuQ_, SUM(endBalGoodM) AS endBalGoodM_, 
                    SUM(endCostM) AS endCostM_, SUM(endBalBoM) AS endBalBoM_, SUM(begBalBoM) AS begBalBoM_
					FROM tblInvBalM
					WHERE (compCode = $company_code) AND (pdMonth = '$monthnum') AND (pdYear = '$year') $all_loc
					GROUP BY prdNumber"; 
		} else {
			$query_per_stock="SELECT *
				FROM tblInvBalM 
				WHERE (compCode = $company_code) AND (pdMonth = '$monthnum') AND (pdYear = '$year') $all_loc $all_prod
				ORDER BY prdNumber ASC"; 
		}
		$result_per_stock=mssql_query($query_per_stock);
		$num_per_stock = mssql_num_rows($result_per_stock);
		if ($num_per_stock < 1) {
			echo "<script>alert('No Inventory Records for this Product... Please enter another.')</script>";
		} else {
			$begBalGoodM=mssql_result($resulinventorytrans,0,"begBalGoodM_");
			$begCostM=mssql_result($resulinventorytrans,0,"begCostM_");
			$mtdReceiptsQ=mssql_result($resulinventorytrans,0,"mtdRecitQ_");
			$mtdSalesQ=mssql_result($resulinventorytrans,0,"mtdRegSlesQ_");
			$mtdTransIn=mssql_result($resulinventorytrans,0,"mtdTransIn_");
			$mtdTransOut=mssql_result($resulinventorytrans,0,"mtdTransOut_");
			$mdtTransfers = $mtdTransIn - $mtdTransOut;
			$mtdAdjQ=mssql_result($resulinventorytrans,0,"mtdAdjQ_");
			$mtdCountAdjQ=mssql_result($resulinventorytrans,0,"mtdCountAdjQ_");
			$mtdAdjustments=$mtdAdjQ+$mtdCountAdjQ;
			$mtdCiQ=mssql_result($resulinventorytrans,0,"mtdCiQ_");
			$mtdSuQ=mssql_result($resulinventorytrans,0,"mtdSuQ_");
			$endBalGoodM=mssql_result($resulinventorytrans,0,"endBalGoodM_");
			$endCostM=mssql_result($resulinventorytrans,0,"endCostM_");
			$begBalBoM=mssql_result($resulinventorytrans,0,"begBalBoM_");
			$endBalBoM=mssql_result($resulinventorytrans,0,"endBalBoM_");
			$begBalGoodM=number_format($begBalGoodM,2);
			$begCostM=number_format($begCostM,2);
			$mtdReceiptsQ=number_format($mtdReceiptsQ,2);
			$mtdSalesQ=number_format($mtdSalesQ,2);
			$mdtTransfers=number_format($mdtTransfers,2);
			$mtdAdjustments=number_format($mtdAdjustments,2);
			$mtdCiQ=number_format($mtdCiQ,2);
			$mtdSuQ=number_format($mtdSuQ,2);
			$endBalGoodM=number_format($endBalGoodM,2);
			$endCostM=number_format($endCostM,2);
			$begBalBoM=number_format($begBalBoM,2);
			$endBalBoM=number_format($endBalBoM,2);
		}
	}
}
###################### end of click inquire button ########################################################

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

####################### display location combo ############################################################
$querylocation="SELECT * FROM tblLocation WHERE compCode = $company_code ORDER BY locCode ASC";
$resultlocation=mssql_query($querylocation);
$numlocation = mssql_num_rows($resultlocation);
###################### end of display location combo ######################################################

####################### display record in product #########################################################
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
####################### end of display record in product #####################################################

?> 
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
</script><title>INVENTORY STOCK STATUS</title>
 <div class='header'>
  <div class='header'>
    <div class='details'> 
      <form action="inventory_stock_stat_inq.php" method="post" name="formissi" id="formissi">
        <table width="100%" border="0" align="center">
          <tr bgcolor="#DEEDD1"> 
            <td width="10%"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Month/Year 
                <b><b> </b></b></font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"><b><b> 
              <select name="month" style="height:18px;">
                <option selected> <? echo $month; ?> </option>
                <option >January</option>
                <option >February</option>
                <option >March</option>
                <option >April</option>
                <option >May</option>
                <option >June</option>
                <option >July</option>
                <option >August</option>
                <option >September</option>
                <option >October</option>
                <option >November</option>
                <option >December</option>
              </select>
              <b> 
              <input name="year" style="height:20px;" type="text" id="year12" value="<? echo $year; ?>" size="4" maxlength="4">
              <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b> 
              </b></b></b> <b><b><b> </b> </b></b></font> <div align="right"></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Location</font></div></td>
            <td width="89%"><font size="2" face="Arial, Helvetica, sans-serif"> 
              <select name="locationcode" id="select11" style="width:200px; height:18px;" >
                <option selected><? echo $locationcode;  ?></option>
                <option>All</option>
                <?
			for ($i=0;$i<$numlocation;$i++){  
					$loccode=mssql_result($resultlocation,$i,"locCode"); 
					$locname=mssql_result($resultlocation,$i,"locName"); 
				?>
                <option><? echo $loccode." - ".$locname; ?></option>
                <? } ?>
                <option> </option>
              </select>
              </font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Product</font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <select name="productcode" id="select12" style="width:200px; height:18px;">
                <option selected><? echo $productcode; ?></option>
                <option>All</option>
                <?
			for ($i=0;$i<$numproduct;$i++){  
					$prodcode=mssql_result($resultproduct,$i,"prdNumber"); 
					$proddesc=mssql_result($resultproduct,$i,"prdDesc"); 
					$prodsum=mssql_result($resultproduct,$i,"prdSellUnit");
				?>
                <option><? echo $prodcode." - ".$proddesc." - ".$prodsum; ?></option>
                <? } ?>
                <option> </option>
              </select>
              <b><b><b> </b></b></b> 
              <input name="findproduct" style=" height:20px;" type="text" id="findproduct2" onFocus="if(this.value=='Code or Desc')this.value='';"   onChange="document.getElementById('find').focus();" value="Code or Desc">
              <input name='find' type='submit' style="height:20px;" class='queryButton' id='find' title='Search Products' onClick="javascript:document.form1.submit();" value='Find'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name="hide_find_product" type="hidden" id="cbqtybo" value="<?php echo $hide_find_product; ?>">
              <input name="hide_numeric" type="hidden" id="hide_find_product" value="<?php echo $hide_numeric; ?>">
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              </font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td> <div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
                <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
                </font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name='inquire' style="height:20px;" type='submit' class='queryButton' id='inquire2' title='Display the inventory stock status' value='View Data'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              <input name='continue2' style="height:20px;" type='button' class='queryButton' id='continue23' title='Search New Records' onClick="javascript:document.form1.submit();" value='Clear All'/>
              <? if ($num_per_stock > "") { ?>
              <input name='print' type='button' style="height:20px;" class='queryButton' id='print' title='Export to PDF File' onClick="javascript:document.frm_print.submit();" value='Print'/>
              <? } else { ?>
              <input name='print2' type='submit'  style="height:20px;" disabled='true' class='queryButton' id='continue2' title='Export to PDF File' value='Print'/>
              <? } ?>
              </font></td>
          </tr>
        </table>
      </form>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <table width="100%" border="0" align="center">
        <tr bgcolor="#6AB5FF"> 
          <td colspan="2"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
              Beginning Balance </font></strong></div></td>
          <td colspan="2"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
              Ending Balance</font></strong></div></td>
          <td colspan="4"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
              MTD Quantities</font></strong></div></td>
        </tr>
        <tr bgcolor="#DEEDD1"> 
          <td width="104"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Good 
              :</font></div></td>
          <td width="66"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $begBalGoodM; ?></font></div></td>
          <td width="135"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">End. 
              Bal. Good :</font></div></td>
          <td width="64"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $endBalGoodM; ?></font></div></td>
          <td width="124"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Receipts 
              :</font></div></td>
          <td width="70"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $mtdReceiptsQ; ?></font></div></td>
          <td width="124"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Adjust. 
              :</font></div></td>
          <td width="72"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $mtdAdjustments; ?></font></div></td>
        </tr>
        <tr bgcolor="#DEEDD1"> 
          <td width="104"><div align="right"><font size="2"><font face="Arial, Helvetica, sans-serif">BO 
              :</font></font></div></td>
          <td width="66"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $begBalBoM; ?></font></div></td>
          <td width="135"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">End. 
              Bal&nbsp;BO :</font></div></td>
          <td width="64"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $endBalBoM; ?></font></div></td>
          <td width="124"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Sales 
              : </font></div></td>
          <td width="70"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $mtdSalesQ; ?></font></div></td>
          <td width="124"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">CI 
              :</font></div></td>
          <td width="72"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $mtdCiQ; ?></font></div></td>
        </tr>
        <tr bgcolor="#DEEDD1"> 
          <td width="104" height="21"><div align="right"></div></td>
          <td width="66"><div align="right"></div></td>
          <td width="135"><div align="right"></div></td>
          <td width="64"><div align="right"></div></td>
          <td width="124"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Transfer 
              :</font></div></td>
          <td width="70"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $mdtTransfers; ?></font></div></td>
          <td width="124"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Store 
              Use :</font></div></td>
          <td width="72"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $mtdSuQ; ?></font></div></td>
        </tr>
      </table>
      <p align="center">&nbsp;</p>
      <div id="Layer1" style="position:absolute; width:99%; height:340px; z-index:1; left: 5px; top: 106px; background-color: #F2F2F2; layer-background-color: #F2F2F2; border: 1px none #000000; overflow: scroll;"> 
        <div align="center"></div>
        <div align="center"></div>
        <table width="99%" border="0" align="center">
          <tr bgcolor="#6AB5FF"> 
            <td><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><? echo $num_per_stock. " record/s found."; ?></font></strong></div></td>
            <td width="158" rowspan="2"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Location</span></font></strong></div></td>
            <td colspan="3"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Beginning 
                Balance </font></strong></div></td>
            <td colspan="6"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">MTD 
                Quantities </font></strong></div></td>
            <td colspan="3"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Ending 
                Balance </font></strong></div></td>
          </tr>
          <tr bgcolor="#FFFFFF">
            <td width="541" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Product 
                Code and Description</span></font></strong></div></td>
            <td width="70" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Good</span></font></strong></div></td>
            <td width="67" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">BO</span></font></strong></div></td>
            <td width="65" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Unit 
                Cost </font></strong></div></td>
            <td width="71" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Receipts</font></strong></div></td>
            <td width="64" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Sales</font></strong></div></td>
            <td width="65" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Transfer</font></strong></div></td>
            <td width="63" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Adj</font></strong></div></td>
            <td width="63" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">CI</font></strong></div></td>
            <td width="63" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Store 
                Use </font></strong></div></td>
            <td width="63" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Good</font></strong></div></td>
            <td width="61" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">BO</font></strong></div></td>
            <td width="56" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Unit 
                Cost 
                <? 
				for ($i=0;$i<$num_per_stock;$i++){ 
					if ($all_prod=="") {
						$grid_prod_code=mssql_result($result_per_stock,$i,"product");
						$grid_loc_code=mssql_result($result_per_stock,$i,"location");
						///// get locName from table tblLocation....
						$query_location="SELECT * FROM tblLocation WHERE locCode = $grid_loc_code";
						$result_location=mssql_query($query_location);
						$num_location = mssql_num_rows($result_location);
						if ($num_location >0) {
							$grid_loc=mssql_result($result_location,0,"locName");
						} else {
							$grid_loc="NA";
						}
						///// get prdName from table tblProdMast....
						$query_prod="SELECT * FROM tblProdMast WHERE prdNumber = $grid_prod_code";
						$result_prod=mssql_query($query_prod);
						$num_prod = mssql_num_rows($result_prod);
						if ($num_prod >0) {
							$grid_prod=mssql_result($result_prod,0,"prdDesc");
						} else {
							$grid_prod="NA";
						}
						
						$grid_beg_good=mssql_result($result_per_stock,$i,"begBalGoodM_");
						$grid_beg_cost=mssql_result($result_per_stock,$i,"begCostM_");
						$grid_receipts=mssql_result($result_per_stock,$i,"mtdRecitQ_");
						$grid_sales=mssql_result($result_per_stock,$i,"mtdRegSlesQ_");
						$grid_trans_in=mssql_result($result_per_stock,$i,"mtdTransIn_");
						$grid_trans_out=mssql_result($result_per_stock,$i,"mtdTransOut_");
						$grid_trans = $grid_trans_in - $grid_trans_out;
						$grid_adjust=mssql_result($result_per_stock,$i,"mtdAdjQ_");
						$grid_count=mssql_result($result_per_stock,$i,"mtdCountAdjQ_");
						$grid_adjustments=$grid_adjust+$grid_count;
						$grid_ci=mssql_result($result_per_stock,$i,"mtdCiQ_");
						$grid_store=mssql_result($result_per_stock,$i,"mtdSuQ_");
						$grid_end_good=mssql_result($result_per_stock,$i,"endBalGoodM_");
						$grid_end_cost=mssql_result($result_per_stock,$i,"endCostM_");
						$grid_beg_bo=mssql_result($result_per_stock,$i,"begBalBoM_");
						$grid_end_bo=mssql_result($result_per_stock,$i,"endBalBoM_");
					} else {
						$grid_prod_code=mssql_result($result_per_stock,$i,"prdNumber");
						$grid_loc_code=mssql_result($result_per_stock,$i,"locCode");
						///// get locName from table tblLocation....
						$query_location="SELECT * FROM tblLocation WHERE locCode = $grid_loc_code";
						$result_location=mssql_query($query_location);
						$num_location = mssql_num_rows($result_location);
						if ($num_location >0) {
							$grid_loc=mssql_result($result_location,0,"locName");
						} else {
							$grid_loc="NA";
						}
						///// get prdName from table tblProdMast....
						$query_prod="SELECT * FROM tblProdMast WHERE prdNumber = $grid_prod_code";
						$result_prod=mssql_query($query_prod);
						$num_prod = mssql_num_rows($result_prod);
						if ($num_prod >0) {
							$grid_prod=mssql_result($result_prod,0,"prdDesc");
						} else {
							$grid_prod="NA";
						}
						
						$grid_beg_good=mssql_result($result_per_stock,$i,"begBalGoodM");
						$grid_beg_cost=mssql_result($result_per_stock,$i,"begCostM");
						$grid_receipts=mssql_result($result_per_stock,$i,"mtdRecitQ");
						$grid_sales=mssql_result($result_per_stock,$i,"mtdRegSlesQ");
						$grid_trans_in=mssql_result($result_per_stock,$i,"mtdTransIn");
						$grid_trans_out=mssql_result($result_per_stock,$i,"mtdTransOut");
						$grid_trans = $grid_trans_in - $grid_trans_out;
						$grid_adjust=mssql_result($result_per_stock,$i,"mtdAdjQ");
						$grid_count=mssql_result($result_per_stock,$i,"mtdCountAdjQ");
						$grid_adjustments=$grid_adjust+$grid_count;
						$grid_ci=mssql_result($result_per_stock,$i,"mtdCiQ");
						$grid_store=mssql_result($result_per_stock,$i,"mtdSuQ");
						$grid_end_good=mssql_result($result_per_stock,$i,"endBalGoodM");
						$grid_end_cost=mssql_result($result_per_stock,$i,"endCostM");
						$grid_beg_bo=mssql_result($result_per_stock,$i,"begBalBoM");
						$grid_end_bo=mssql_result($result_per_stock,$i,"endBalBoM");
					}
					$grid_beg_good=number_format($grid_beg_good,2);
					$grid_beg_cost=number_format($grid_beg_cost,2);
					$grid_receipts=number_format($grid_receipts,2);
					$grid_sales=number_format($grid_sales,2);
					$grid_trans=number_format($grid_trans,2);
					$grid_adjustments=number_format($grid_adjustments,2);
					$grid_ci=number_format($grid_ci,2);
					$grid_store=number_format($grid_store,2);
					$grid_end_good=number_format($grid_end_good,2);
					$grid_end_cost=number_format($grid_end_cost,4);
					$grid_beg_bo=number_format($grid_beg_bo,2);
					$grid_end_bo=number_format($grid_end_bo,2);
					
			?>
                </font></strong></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td width="541" bgcolor="#DEEDD1"><font size="2"><? echo $grid_prod_code." - ".$grid_prod; ?></font></td>
            <td width="158"><font size="2"><? echo $grid_loc_code." - ".$grid_loc; ?></font></td>
            <td width="70"> <div align="right"><font size="2"><? echo $grid_beg_good; ?></font><font size="2" face="Arial, Helvetica, sans-serif"> 
                </font></div></td>
            <td width="67"><div align="right"><font size="2"><font face="Arial, Helvetica, sans-serif"><? echo $grid_beg_bo; ?></font></font><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_beg_cost; ?><span class="style20"> 
                </span></font></div></td>
            <td width="71"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_receipts; ?><span class="style20"> 
                </span></font></div></td>
            <td width="64"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_sales; ?><span class="style20"> 
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_trans; ?><span class="style20"> 
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_adjustments; ?><span class="style20"> 
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_ci; ?><span class="style20"> 
                </span></font></div></td>
            <td width="63"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_store; ?></font></div></td>
            <td width="63"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_end_good; ?></font></div></td>
            <td width="61"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><? echo $grid_end_bo; ?><span class="style20"> 
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                </span><? echo $grid_end_cost; ?><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                <?
		  }
		  ?>
                </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
          </tr>
        </table>
      </div>
      <p><font size="2" face="Arial, Helvetica, sans-serif"> </font> </p>
      <form action="" method="post" name="form1" target="_self">
        <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
          </font> <font size="2" face="Arial, Helvetica, sans-serif"> </font> 
        </div>
      </form>
      <form action="inventory_stock_stat_inq_pdf.php" method="post" enctype="multipart/form-data" name="frm_print" target="_blank" id="frm_print">
        <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
        <input name="hide_beg_bal_good_m" type="hidden" id="hide_prod_code2" value="<?php echo $begBalGoodM; ?>">
        <input name="hide_beg_bal_bo_m" type="hidden" id="hide_loc_code2" value="<?php echo $begBalBoM ?>">
        <input name="hide_beg_cost_m" type="hidden" id="hide_prod_code3" value="<?php echo $begCostM; ?>">
        <input name="hide_mtd_receipts_q" type="hidden" id="hide_loc_code3" value="<?php echo $mtdReceiptsQ ?>">
        <input name="hide_mtd_sales_q" type="hidden" id="hide_mtd_sales_q" value="<?php echo $mtdSalesQ; ?>">
        <input name="hide_mdt_transfers" type="hidden" id="hide_mdt_transfers" value="<?php echo $mdtTransfers ?>">
        <input name="hide_mtd_adjustments" type="hidden" id="hide_beg_cost_m" value="<?php echo $mtdAdjustments; ?>">
        <input name="hide_mtd_ci_q" type="hidden" id="hide_mtd_receipts_q" value="<?php echo $mtdCiQ?>">
        <input name="hide_mtd_su_q" type="hidden" id="hide_beg_bal_good_m2" value="<?php echo $mtdSuQ; ?>">
        <input name="hide_end_bal_good_m" type="hidden" id="hide_beg_bal_bo_m2" value="<?php echo $endBalGoodM ?>">
        <input name="hide_end_bal_bo_m" type="hidden" id="hide_beg_cost_m2" value="<?php echo $endBalBoM; ?>">
        <input name="hide_end_cost_m" type="hidden" id="hide_mtd_receipts_q2" value="<?php echo $endCostM ?>">
        <input name="hide_month" type="hidden" id="hide_beg_bal_good_m4" value="<?php echo $month; ?>">
        <input name="hide_year" type="hidden" id="hide_beg_bal_good_m5" value="<?php echo $year; ?>">
        <input name="hide_prod_code" type="hidden" id="hide_beg_bal_good_m6" value="<?php echo $productcode; ?>">
        <input name="hide_loc_code" type="hidden" id="hide_beg_bal_good_m7" value="<?php echo $locationcode; ?>">
        </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
      </form>
      <p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> </font><font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></p>
    </div>
  </div>
</div>

<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../etc/etc.obj.php";
require_once "../../functions/inquiry_function.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$today = date("m/d/Y");
$db = new DB;
$db->connect();

###########################################################
$fromdate=$_POST['fromdate'];
$todate=$_POST['todate'];
$locationcode=$_POST['locationcode'];
$productcode=$_POST['productcode'];
$transactiontype=$_POST['transactiontype'];
$findproduct=$_POST['findproduct'];
$hide_find_product=$_POST['hide_find_product'];
$hide_numeric=$_POST['hide_numeric'];

$cbqtygood="0.00";
$cbqtybo="0.00";
$cbunitcost="0.0000";
###########################################################

#################### click inquire button #################
$clickinquirebuttonmessage="";
if(isset($_POST['inquire'])) { 
	if (($fromdate=="") || ($todate=="")) {
		$clickinquirebuttonmessage="Key-in From Date or To Date : ";
	}
	if ($fromdate>"") {
		$validate_date="";
		$validate_date=check_date_error($fromdate);
		$message=$message.$validate_date;
	}
	if ($todate>"") {
		$validate_date="";
		$validate_date=check_date_error($todate);
		$message=$message.$validate_date;
	}
	if (($fromdate > "") && ($todate > "")){
		$errordate = new date_difference($todate, $fromdate); ///pick in inventory_inquiry_function.php 
		if ($errordate->days<0) {		
				$clickinquirebuttonmessage=$clickinquirebuttonmessage."From Date must not be greater than to To Date : ";
				$fromdate="";
				$todate=""; 
		} else {
			$errordate = new date_difference($today, $todate); ///pick in inventory_inquiry_function.php 
			if ($errordate->days<0) {		
				$clickinquirebuttonmessage=$clickinquirebuttonmessage."To Date must not be greater than Current Date : ";
				$todate=""; 
			}
		}
	}
	if ($locationcode=="") {
		$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Location selected : ";
	}
	if ($productcode=="") {
		$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Product selected : ";
	}
	if ($transactiontype=="") {
		$clickinquirebuttonmessage=$clickinquirebuttonmessage."No Transaction Type selected";
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
		$new3=getCodeofString($transactiontype); ///pick in inventory_inquiry_function.php
		$new3=trim($new3);
		if ($new1=="All") {
			$new_loc_code="";
		} else {
			$new_loc_code=" AND (locCode=$new1) ";
		}
		if ($new3=="All") {
			$new_trans_type="";
		} else {
			$new_trans_type=" AND (transCode = $new3) ";
		}
		if ($new2=="All") {
			$new_prod_code="";
		} else {
			$new_prod_code=" AND (prdNumber = $new2) ";
		}	
		############################# dont forget to get the company code ##################################
		$queryinventorytrans="SELECT * FROM tblInvTran 
							  WHERE (compCode = $company_code) AND (docDate >= '$fromdate') AND (docDate <= '$todate') $new_prod_code $new_loc_code $new_trans_type 
							  ORDER BY prdNumber,docDate,docNumber ASC";
		$resulinventorytrans=mssql_query($queryinventorytrans);
		$numinventorytrans = mssql_num_rows($resulinventorytrans);
		if ($numinventorytrans < 1) {
			echo "<script>alert('No transactions for this product for the requested period... Please enter another.')</script>";
		} else {
			$from_date = new DateTime($_POST['fromdate']);
			$from_date = $from_date->format("m/j/Y");
			$to_date = new DateTime($_POST['todate']);
			$to_date = $to_date->format("m/j/Y");
			#####################################################
			///// get current balance from table tblInvbalM.... use location code, transaction code, from date, to date
			############################## dont forget to get the company code ############################
			if ($new_prod_code=="") {
				$numcurrentbalance = 0;
			} else {
				/*$querycurrentbalance="SELECT endBalGoodM, endBalBoM, endCostM, locCode, prdNumber, pdYear, pdMonth
					FROM tblInvBalM
					WHERE (prdNumber <> 0) $new_prod_code $new_loc_code
					ORDER BY pdYear, pdMonth DESC";*/
				$resPd=mssql_query("SELECT * FROM tblPeriod WHERE compCode = $company_code AND pdStat = 'O'");
				$numPd = mssql_num_rows($resPd);
				if ($numPd>0) {
					$monthPd = mssql_result($resPd,0,"pdCode");
					$yearPd = mssql_result($resPd,0,"pdYear");
				} else {
					echo "<script>alert('No Open Period... Please assist to your administrator.')</script>";
				}
				$querycurrentbalance="SELECT sum(endBalGoodM) as endBalGoodM, sum(endBalBoM) as endBalBoM, max(endCostM) as endCostM
					FROM tblInvBalM
					WHERE (pdYear=$yearPd) AND (pdMonth=$monthPd) AND (compCode = $company_code) AND (prdNumber <> 0) $new_prod_code $new_loc_code";
				$resultcurrentbalance=mssql_query($querycurrentbalance);
				$numcurrentbalance = mssql_num_rows($resultcurrentbalance);
			}
			if ($numcurrentbalance>0) {
				$cbqtygood=mssql_result($resultcurrentbalance,0,"endBalGoodM");
				$cbqtybo=mssql_result($resultcurrentbalance,0,"endBalBoM");
				$cbunitcost=mssql_result($resultcurrentbalance,0,"endCostM");
				$cbqtygood=number_format($cbqtygood,2);
				$cbqtybo=number_format($cbqtybo,2);
				$cbunitcost=number_format($cbunitcost,4);
			}
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
						$productcode = $findproductprdNumber."-".$findproductprdDesc."-".$findproductprdSellUnit;
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
						$productcode = $findproductprdNumber."-".$findproductprdDesc."-".$findproductprdSellUnit;
					} else {
						echo "<script>alert('No Product records found!')</script>";
					}
				}
			 }
		}
	}

if (($findproduct=="") || ($findproduct=="Code or Desc")) { // display record in product #############################################################
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

################### display location combo ################################################################
########################## dont forget to get the company code #######################
$querylocation="SELECT * FROM tblLocation WHERE compCode = $company_code ORDER BY locCode ASC";
$resultlocation=mssql_query($querylocation);
$numlocation = mssql_num_rows($resultlocation);
################### end of display location combo #########################################################

#################### display location combo ###############################################################
$queryTransactionType="SELECT * FROM tblTransactionType ORDER BY trnTypeInit ASC";
$resultTransactionType=mssql_query($queryTransactionType);
$numTransactionType = mssql_num_rows($resultTransactionType);
#################### end of display location combo ########################################################

?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
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
<div class='header'> 
  <div class='header'> 
    <div class='details'> 
      <form action="inventory_trans_inq.php" method="post" name="formissi" id="formissi">
        <table width="100%" border="0">
          <tr bgcolor="#DEEDD1"> 
            <td width="14%"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">From 
                Date<b><b> </b></b></font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"><b><b><b> 
              <input name="fromdate" style="height:20px" type="text" id="fromdate2" value="<? echo $fromdate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b><a href="javascript:void(0)" onClick="showCalendar(formissi.fromdate,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a><b><b><b> 
              </b></b></b>To Date<b><b><b> 
              <input name="todate" type="text" style="height:20px" id="todate2" value="<? echo $todate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b><a href="javascript:void(0)" onClick="showCalendar(formissi.todate,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a><b><b><b> 
              </b></b></b></font></td>
            <td><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><strong>Current 
                Bal.</strong></font></div></td>
            <td>&nbsp;</td>
            <td><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><b><b> 
                <b> <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b> 
                </b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b> 
                </b></b></b> <b><b><b> </b> </b></b></font> <font size="2" face="Arial, Helvetica, sans-serif"><b><b><b> 
                </b></b></b></font></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Location</font></div></td>
            <td width="67%"><font size="2" face="Arial, Helvetica, sans-serif"> 
              <select name="locationcode" id="select" style="width:200px; height:18px;">
                <option selected><? echo $locationcode;  ?></option>
                <option>All</option>
                <?
			for ($i=0;$i<$numlocation;$i++){  
					$loccode=mssql_result($resultlocation,$i,"locCode"); 
					$locname=mssql_result($resultlocation,$i,"locName"); 
				?>
                <option><? echo $loccode."-".$locname; ?></option>
                <? } ?>
                <option> </option>
              </select>
              </font></td>
            <td width="11%"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif">Qty 
                Good :</font></div></td>
            <td width="6%"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $cbqtygood;
		  ?>
                <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                <input name="cbqtygood" type="hidden" id="cbqtygood" value="<?php echo $cbqtygood; ?>">
                </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b> 
                </span></font></div></td>
            <td width="1%"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"> 
                </font></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"> 
                Product</font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <select name="productcode" id="select3" style="width:200px; height:18px;">
                <option selected><? echo $productcode; ?></option>
                <?
				for ($i=0;$i<$numproduct;$i++){  
					$prodcode=mssql_result($resultproduct,$i,"prdNumber"); 
					$proddesc=mssql_result($resultproduct,$i,"prdDesc"); 
					$prodsum=mssql_result($resultproduct,$i,"prdSellUnit");
				?>
                <option><? echo $prodcode."-".$proddesc."-".$prodsum; ?></option>
                <? } ?>
                <option>All</option>
              </select>
              <b><b><b> </b></b></b> 
              <input name="findproduct" type="text" style="height:20px" id="findproduct" onFocus="if(this.value=='Code or Desc')this.value='';" value="Code or Desc">
              <input name='find' type='submit' style="height:20px" class='queryButton' id='continue' title='Search Products' onClick="javascript:document.form1.submit();" value='Find'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name="hide_find_product" type="hidden" id="cbqtybo" value="<?php echo $hide_find_product; ?>">
              <input name="hide_numeric" type="hidden" id="hide_find_product" value="<?php echo $hide_numeric; ?>">
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              <b><b><b> </b></b></b> </font></td>
            <td><div align="left">Qty BO :</div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $cbqtybo;
		  ?>
                <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                <input name="cbqtybo" type="hidden" id="totalusername" value="<?php echo $cbqtybo; ?>">
                </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b> 
                </span></font></div></td>
            <td>&nbsp;</td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">Transaction 
                Type</font></div></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"> 
              <select name="transactiontype" id="select2" style="width:200px; height:18px;">
                <option selected><? echo $transactiontype; ?></option>
                <option>All</option>
                <?	
			for ($i=0;$i<$numTransactionType;$i++){  
					$TransactionTypeCode=mssql_result($resultTransactionType,$i,"trnTypeCode"); 
					$TransactionTypeInit=mssql_result($resultTransactionType,$i,"trnTypeDesc"); 
				?>
                <option><? echo $TransactionTypeCode. " - " .$TransactionTypeInit; ?></option>
                <? } ?>
                <option> </option>
              </select>
              </font></td>
            <td><div align="left">Unit Cost :</div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $cbunitcost;
		  ?>
                <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                <input name="cbunitcost" type="hidden" id="totalusername2" value="<?php echo $cbunitcost; ?>">
                </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b> 
                </span></font></div></td>
            <td><div align="right"></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td colspan="5"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
                <input name='inquire' style="height:20px" type='submit' class='queryButton' id='inquire3' title='Display the Inventory Transaction' value='View Data'/>
                <input name='continue' style="height:20px" type='button' class='queryButton' id='continue3' title='Search New Records' onClick="javascript:document.form1.submit();" value='Clear All'/>
                <? if ($numinventorytrans < 1) { ?>
                <input name='print2' style="height:20px" disabled="true" type='button' class='queryButton' id='print2' title='Print' onClick="javascript:document.frm_print.submit();" value='Print'/>
                <? } else {?>
                <input name='print' type='button' style="height:20px" class='queryButton' id='print' title='Print' onClick="javascript:document.frm_print.submit();" value='Print'/>
                <? } ?>
                </font></div></td>
          </tr>
        </table>
      </form>
      <div id="Layer1" style="position:absolute; left:8px; top:131px; width:99%; height:400px; z-index:1; overflow: scroll;">
        <table width="100%" border="0">
          <tr bgcolor="#FFFFFF"> 
            <td colspan="2" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Document</font></strong></div></td>
            <td width="283" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $numinventorytrans." record/s found.";
		  ?>
                </span></font></strong></div></td>
            <td width="74" rowspan="2" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Location</font></strong></div></td>
            <td width="43" rowspan="2" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Trans 
                Type</font></strong></div></td>
            <td width="114" rowspan="2" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Vendor/Customer 
                Name</font></strong></div></td>
            <td colspan="3" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Quantity</font></strong></div></td>
            <td width="68" rowspan="2" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Extended 
                Amount</font></strong></div></td>
            <td width="79" rowspan="2" bgcolor="#6AB5FF"><div align="center"></div>
              <div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
                Disc Amount </font></strong></div></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td width="29" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Date</font></strong></div></td>
            <td width="61" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Number</span></font></strong></div></td>
            <td width="283" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Product</font></strong></div></td>
            <td width="66" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Reg.</font></strong></div></td>
            <td width="54" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Free</font></strong></div></td>
            <td width="50" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">BO</font></strong></div></td>
          </tr>
          <? 
				for ($i=0;$i<$numinventorytrans;$i++){ 
					$gridbusinesspartner="NA";
					$griddocdate=mssql_result($resulinventorytrans,$i,"docDate");
					$griddocnumber=mssql_result($resulinventorytrans,$i,"docNumber");
					$sku=mssql_result($resulinventorytrans,$i,"prdNumber");
					$rst_prod=mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $sku");
					$grid_product=$sku."-" . mssql_result($rst_prod,0,"prdDesc");
					$grid_loc_code=mssql_result($resulinventorytrans,$i,"locCode");
					$gridtranscode=mssql_result($resulinventorytrans,$i,"transCode");
					$gridtrQtyGood=mssql_result($resulinventorytrans,$i,"trQtyGood");
					$gridtrQtyFree=mssql_result($resulinventorytrans,$i,"trQtyFree");
					$gridtrQtyBo=mssql_result($resulinventorytrans,$i,"trQtyBo");
					$gridextAmt=mssql_result($resulinventorytrans,$i,"extAmt");
					$griditemDiscCogY=mssql_result($resulinventorytrans,$i,"itemDiscCogY");
					$griditemDiscCogN=mssql_result($resulinventorytrans,$i,"itemDiscCogN");
					$gridpoLevelDiscCogY=mssql_result($resulinventorytrans,$i,"poLevelDiscCogY");
					$gridpoLevelDiscCogN=mssql_result($resulinventorytrans,$i,"poLevelDiscCogN");
					$gridcustCode=mssql_result($resulinventorytrans,$i,"custCode");
					$gridsuppCode=mssql_result($resulinventorytrans,$i,"suppCode");
					$grid=mssql_result($resulinventorytrans,$i,"suppCode");
					$gridtrQtyGood=number_format($gridtrQtyGood,2);
					$gridtrQtyFree=number_format($gridtrQtyFree,2);
					$gridtrQtyBo=number_format($gridtrQtyBo,2);
					$gridextAmt=number_format($gridextAmt,4);
					$gridtrQtyGood=number_format($gridtrQtyGood,2);
					///// get total discount amount = the sum of griditemDiscCogY + griditemDiscCogN + gridpoLevelDiscCogY + gridpoLevelDiscCogN
					$gridtotaldiscountamount=$griditemDiscCogY+$griditemDiscCogN+$gridpoLevelDiscCogY+$gridpoLevelDiscCogN;
					$gridtotaldiscountamount=number_format($gridtotaldiscountamount,2);
					///// get locName from table tblLocation....
					$query_location="SELECT * FROM tblLocation WHERE locCode = $grid_loc_code";
					$result_location=mssql_query($query_location);
					$num_location = mssql_num_rows($result_location);
					if ($num_location >0) {
						$grid_location=$grid_loc_code."-" .mssql_result($result_location,0,"locName");
					} else {
						$grid_location="NA";
					}
					
					///// get tranTypeInit from table tblTransactionType.... use transCode of extract file
					$querytranTypeInit="SELECT * FROM tblTransactionType WHERE trnTypeCode = '$gridtranscode'";
					$resulttranTypeInit=mssql_query($querytranTypeInit);
					$num_tran_type_init = mssql_num_rows($resulttranTypeInit);
					if ($num_tran_type_init >0) {
						$gridtranTypeInit=$gridtranscode."-" .mssql_result($resulttranTypeInit,0,"trnTypeInit");
					} else {
						$gridtranTypeInit="NA";
					}
					
					$gridbusinesspartner="NA";
					if ($gridtranscode==21) {
						$gridbusinesspartner="Various";
					}
					if ($gridtranscode==51) {
						///// get custName, if transCode = 51 from table tblCustMast... use custCode of extract file
						$querycustName="SELECT * FROM tblCustMast WHERE custCode = $gridcustCode";
						$resultcustName=mssql_query($querycustName);
						$num_custName = mssql_num_rows($resultcustName);
						if ($num_custName >0) {
							$gridbusinesspartner=$gridcustCode."-".mssql_result($resultcustName,0,"custName");
						} else {
							$gridbusinesspartner="NA";
						}
					} 
					if (($gridtranscode==11)||($gridtranscode==12)||($gridtranscode==13)) {
						///// get suppName, if transCode = 51 from table tblSuppliers... use suppCode of extract file
						$querysuppName="SELECT * FROM tblSuppliers WHERE suppCode = $gridsuppCode";
						$resultsuppName=mssql_query($querysuppName);
						$num_suppName = mssql_num_rows($resultsuppName);
						if ($num_suppName >0) {
							$gridbusinesspartner=$gridsuppCode."-".mssql_result($resultsuppName,0,"suppName");
						} else {
							$gridbusinesspartner="NA";
						}
					} 
			?>
          <tr bgcolor="#DEEDD1"> 
            <td width="29"> <font size="2" face="Arial, Helvetica, sans-serif"> 
              <?
			if ($griddocdate>"") {
				$date = new DateTime($griddocdate);
				$griddocdate = $date->format("m-d-Y");		
			} else {
				$griddocdate="";
			}
			echo $griddocdate;
		  ?>
              </font></td>
            <td width="61"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $griddocnumber;
		  ?>
                </span></font></div></td>
            <td bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              </span> 
              <input name="findproduct2" type="text" id="findproduct2" style="height:20px; background-color:#DEEDD1; border:0px;" onFocus="if(this.value=='Code or Desc')this.value='';" value="<? echo $grid_product; ?>" size="40" readonly="true">
              <span class="style20"> </span></font></td>
            <td><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              <?
		  echo $grid_location;
		  ?>
              </span></font></td>
            <td width="43"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              <?
		  echo $gridtranTypeInit;
		  ?>
              </span></font></td>
            <td width="114"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
              </span> 
              <input name="findproduct22" type="text" id="findproduct22" style="height:20px; background-color:#DEEDD1; border:0px;" onFocus="if(this.value=='Code or Desc')this.value='';" value="<? echo $gridbusinesspartner; ?>" readonly="true">
              <span class="style20"> </span></font></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $gridtrQtyGood;
		  ?>
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $gridtrQtyFree;
		  ?>
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $gridtrQtyBo;
		  ?>
                </span></font></div></td>
            <td width="68"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $gridextAmt;
		  ?>
                </span></font></div></td>
            <td><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                <?
		  echo $gridtotaldiscountamount;
		  ?>
                </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
          </tr>
          <?
		  	}
		  ?>
        </table>
      </div>
      <font size="2" face="Arial, Helvetica, sans-serif"> </font> 
      <form action="" method="post" name="form1" target="_self">
        <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
          </font> <font size="2" face="Arial, Helvetica, sans-serif"> </font> 
        </div>
      </form>
      <form action="inventory_trans_inq_pdf_revise.php" method="post" name="frm_print" target="_blank" id="frm_print">
        <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
        <input name="hide_from_date" type="hidden" id="hide_from_date" value="<?php echo $fromdate; ?>">
        <input name="hide_to_date" type="hidden" id="hide_numeric" value="<?php echo $todate; ?>">
        <input name="hide_loc_code" type="hidden" id="hide_loc_code" value="<?php echo $locationcode; ?>">
        <input name="hide_prod_code" type="hidden" id="hide_prod_code" value="<?php echo $productcode; ?>">
        <input name="hide_trans_type" type="hidden" id="hide_trans_type" value="<?php echo $transactiontype; ?>">
        <input name="hide_qty_good" type="hidden" id="hide_qty_good" value="<?php echo $cbqtygood; ?>">
        <input name="hide_qty_bo" type="hidden" id="hide_qty_bo" value="<?php echo $cbqtybo; ?>">
        <input name="hide_unit_cost" type="hidden" id="hide_unit_cost" value="<?php echo $cbunitcost; ?>">
        </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
      </form>
      <p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> </font><font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></p>
    </div>
  </div>
</div>
</body>
</html>

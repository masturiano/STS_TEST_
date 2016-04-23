<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../etc/etc.obj.php";
require_once "../../functions/inquiry_function.php";
require_once "../../functions/activecalendar.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$db = new DB;
$db->connect();

###########################################################
$status_list=$_POST['status_list'];
$fromdate=$_POST['fromdate'];
$todate=$_POST['todate'];
$vendorcode=$_POST['vendorcode'];
$findvendor=$_POST['findvendor'];
$hide_find_vendor=$_POST['hide_find_vendor'];
$hide_numeric2=$_POST['hide_numeric2'];
$radio_view_po=$_POST['hide_po_number'];
$search_box = "From ".$fromdate." To ".$todate." / Vendor: ".$vendorcode;
###########################################################

#################### click inquire button #################
$message="";
if(isset($_POST['inquire'])) { 
	if (($fromdate=="") || ($todate=="")) {
		$message="Key-in From Date or To Date : ";
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
	if ($vendorcode=="") {
		$message=$message."No Vendor selected : ";
	}
	if (($fromdate > "") && ($todate > "")){
		$errordate = new date_difference($todate, $fromdate); ///pick in inventory_inquiry_function.php 
		if ($errordate->days<0) {		
				$message=$message."From Date must not be greater than to To Date. : ";
				$fromdate="";
				$todate=""; 
		}
	}
	if ($message>"") {
		echo "<script>alert('$message')</script>";
	} else {
		$cut_vendor_code=getCodeofString($vendorcode); ///pick in inventory_inquiry_function.php
		if (trim($cut_vendor_code)=="All") {
			$all_vendor = "";
		} else {
			$all_vendor = " AND (tblPoHeader.suppCode = $cut_vendor_code) ";
		}
		if (trim($status_list)=="All") {
			$all_status_list = " AND (tblPoHeader.poStat != 'X' AND tblPoHeader.poStat != 'O') ";
		} else {
			if ($status_list=="For Delivery") {
				$status_code = "D";
			}
			if ($status_list=="Released") {
				$status_code = "R";
			}
			if ($status_list=="Closed") {
				$status_code = "C";
			}
			if ($status_list=="Partial") {
				$status_code = "P";
			}
			$all_status_list = " AND (tblPoHeader.poStat = '$status_code') ";
		}	
		############################# dont forget to get the company code ##################################
		$query_pdf="SELECT tblPoHeader.poStat, tblPoHeader.poNumber, tblPoHeader.poDate, tblPoHeader.poItemTotal, tblPoHeader.poTotExt, tblPoHeader.poTotDisc, 
                      tblPoHeader.poTotAllow, tblPoHeader.poStat, tblPoItemDtl.prdNumber, tblProdMast.prdDesc, tblPoItemDtl.orderedQty, 
                      tblPoItemDtl.rcrQty, tblPoItemDtl.poUnitCost, tblPoItemDtl.poExtAmt, tblPoItemDtl.itemDiscPcents, tblSuppliers.suppName, 
                      tblSuppliers.suppCode 
					  FROM tblProdMast INNER JOIN 
                      tblPoItemDtl ON tblProdMast.prdNumber = tblPoItemDtl.prdNumber INNER JOIN 
                      tblPoHeader ON tblPoItemDtl.poNumber = tblPoHeader.poNumber INNER JOIN 
                      tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode 
				      WHERE (tblPoHeader.compCode = $company_code) AND (tblPoHeader.poDate >= '$fromdate') AND (tblPoHeader.poDate <= '$todate') $all_vendor $all_status_list
				    ORDER BY tblPoItemDtl.poNumber, tblProdMast.prdDesc";
		$query_po_header="SELECT * FROM tblPOHeader WHERE (compCode = $company_code) AND (poDate >= '$fromdate') AND (poDate <= '$todate') $all_vendor $all_status_list ORDER BY poNumber ASC";
		$result_po_header=mssql_query($query_po_header);
		$num_po_header = mssql_num_rows($result_po_header);
		if ($num_po_header < 1) {
			echo "<script>alert('No Transactions for this Purchase Order for the requested period... Please enter another.')</script>";
		} 
	}
}
###################### end of click inquire button ########################################################

#################### click po inq details back button #################
if(isset($_POST['po_inq_details_back_button'])) { 
	$radio_view_po=$_POST['hide_po_number'];
	$fromdate=$_POST['hide_from_date'];
	$todate=$_POST['hide_to_date'];
	$vendorcode=$_POST['hide_vendor'];
	$hide_find_vendor=$_POST['hide_find_vendor'];
	$hide_numeric2=$_POST['hide_numeric'];
	$hide_sql=$_POST['hide_sql'];
	$cut_vendor_code=getCodeofString($vendorcode); ///pick in inventory_inquiry_function.php
	if (trim($cut_vendor_code)=="All") {
		$all_vendor = "";
	} else {
		$all_vendor = " AND (suppCode = $cut_vendor_code) ";
	}	
	if (trim($status_list)=="All") {
		$all_status_list = " AND (tblPoHeader.poStat != 'X' AND tblPoHeader.poStat != 'O') ";
	} else {
		if ($status_list=="For Delivery") {
			$status_code = "D";
		}
		if ($status_list=="Released") {
			$status_code = "R";
		}
		if ($status_list=="Closed") {
			$status_code = "C";
		}
		if ($status_list=="Partial") {
			$status_code = "P";
		}
		$all_status_list = " AND (tblPoHeader.poStat = '$status_code') ";
	}
	############################# dont forget to get the company code ##################################
	$query_po_header="SELECT * FROM tblPOHeader WHERE (compCode = $company_code) AND (poDate >= '$fromdate') AND (poDate <= '$todate') $all_vendor $all_status_list ORDER BY poNumber ASC";
	$result_po_header=mssql_query($query_po_header);
	$num_po_header = mssql_num_rows($result_po_header);
	if ($num_po_header < 1) {
		echo "<script>alert('No Transactions for this Purchase Order for the requested period... Please enter another.')</script>";
	} 
}
###################### end of click po inq details back button ########################################################

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

function click_detail(idko) {
	document.form_po_product.click_detail_hide.value = idko;
	document.form_po_product.action = "po_short_over_details.php";
	document.form_po_product.target = "_self";
	javascript:document.form_po_product.submit(this.id);
}
function click_print_button() {
	document.form_po_product.action = "po_short_over_pdf.php";
	document.form_po_product.target = "_blank";
	document.form_po_product.submit();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body onLoad="hide('layer_from_date'); hide('layer_to_date');">
<div class='header' > 
  <div class='header'> 
    <div class='details'>
<form action="" method="post" name="formissi" target="_self" id="formissi">
        <table width="99%" border="0" bgcolor="#DEEDD1">
          <tr bgcolor="#DEEDD1"> 
            <td width="74" height="16"><div align="right"> <font size="2" face="Arial, Helvetica, sans-serif">From 
                Date<b><b> </b></b></font></div></td>
            <td width="496" bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif"><b><b><b>
              <input name="fromdate" style="height:20px;" type="text"  id="fromdate2" value="<? echo $fromdate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b><a href="javascript:void(0)" onClick="showCalendar(formissi.fromdate,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
              To Date<b><b><b> 
              <input name="todate" style="height:20px;" type="text" id="todate2" value="<? echo $todate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b><a href="javascript:void(0)" onClick="showCalendar(formissi.todate,'mm/dd/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a><b><b><b> 
              </b></b></b></font></td>
            <td width="177" height="16" bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif"><b><b><b> 
              </b></b></b><b><b><b> </b></b></b></font></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td width="74" height="16"><div align="right"> <font size="2" face="Arial, Helvetica, sans-serif"> 
                </font> <font size="2" face="Arial, Helvetica, sans-serif">Vendor 
                </font></div></td>
            <td width="496" height="16"><font size="2" face="Arial, Helvetica, sans-serif">
              <select name="vendorcode" id="select" style="width:200px; height:18px;">
                <option selected><? echo $vendorcode; ?></option>
                <option>All</option>
                <?
			$result_vendor=mssql_query($query_vendor);
			$num_vendor = mssql_num_rows($result_vendor);
			for ($i=0;$i<$num_vendor;$i++){  
					$vendor_code=mssql_result($result_vendor,$i,"suppCode"); 
					$vendor_name=mssql_result($result_vendor,$i,"suppName"); 
				?>
                <option><? echo $vendor_code."-----".$vendor_name; ?></option>
                <? } ?>
                <option> </option>
              </select>
              <input name="findvendor" style="height:20px;" type="text" id="findvendor2" onFocus="if(this.value=='Code or Name')this.value='';" value="Code or Name">
              <input name='find2' style="height:20px;" type='submit' class='queryButton' id='find22' title='Search Vendor' onClick="javascript:document.form1.submit();" value='Find'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name="hide_find_vendor" type="hidden" id="hide_find_vendor2" value="<?php echo $hide_find_vendor; ?>">
              <input name="hide_numeric2" type="hidden" id="hide_numeric2" value="<?php echo $hide_numeric2; ?>">
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font></td>
            <td width="177" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"> 
                PO Status 
                <select name="status_list" id="status_list" style="width:100px; height:18px;">
                  <?
					if ($status_list=="") {
						$status_list = "All";
					}
				?>
                  <option><? echo $status_list; ?></option>
                  <option>All</option>
                  <option>For Delivery</option>
                  <option>Released</option>
                  <option>Closed</option>
                  <option>Partial</option>
                </select>
                </font></div></td>
          </tr>
          <tr bordercolor="#FFFFFF" bgcolor="#DEEDD1"> 
            <td width="74" height="16">&nbsp;</td>
            <td width="496" height="16" bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif">
              <input name='inquire' style="height:20px;"type='submit' class='queryButton' id='inquire' title='Display the PO Header' value="View PO's"/>
              <input name='continue' style="height:20px;"type='button' class='queryButton' id='continue2' title='Search New Records' onClick="javascript:document.form1.submit();" value='Clear All'/>
              </font></td>
            <td width="177" height="16" bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp; 
              </font></td>
          </tr>
          <tr bordercolor="#FFFFFF" bgcolor="#6AB5FF"> 
            <td height="1" colspan="3"></td>
          </tr>
        </table>
        </form>
      <form action="" method="post" name="form_po_product" id="form_po_product">
        <div align="center" style="position:absolute; width:98.5%; height:65px; z-index:1; left: -2px; top: 87px; border: 1px none #000000; overflow: scroll;"> 
          <table width="98%" border="0">
            <tr bgcolor="#C2DDAA"> 
              <td height="16" colspan="5"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Purchase 
                  Order Header </font><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                  <?
				  if ($num_po_header>"") {
						echo " - ". $num_po_header . " record/s found.";	  
				  } else {
				  		echo  "";
				  }
		  
		  ?>
                  </span></font></div></td>
            </tr>
            <tr bgcolor="#6AB5FF"> 
              <td width="43" height="16"><div align="center"><strong><span class="style6"><font size="2" face="Arial, Helvetica, sans-serif"> 
                  <input name='print' style="height:20px;"type='button' class='queryButton' id='print' title='Search New Records' onClick="click_print_button();" value='Print'/>
                  </font></span></strong></div></td>
              <td width="50" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">PO 
                  No</font></strong></div></td>
              <td width="71" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">PO 
                  Date </span></font></strong></div></td>
              <td width="212" height="16" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Vendor</font></strong></div></td>
              <td width="77" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Status 
                  </font></strong></div></td>
            </tr>
          </table>
        </div>
        <div align="center" style="position:absolute; width:98.5%; height:350px; z-index:1; left: -2px; top: 131px; border: 1px none #000000; overflow: scroll;">
          <table width="98%" border="0" bgcolor="#DEEDD1">
            <? 
				for ($i=0;$i<$num_po_header;$i++){ 
					$grid_number=mssql_result($result_po_header,$i,"poNumber");
					$grid_date=mssql_result($result_po_header,$i,"poDate");
					$grid_item_total=mssql_result($result_po_header,$i,"poItemTotal");
					$grid_total_ext=mssql_result($result_po_header,$i,"poTotExt");
					$grid_total_disk=mssql_result($result_po_header,$i,"poTotDisc");
					$grid_total_allow=mssql_result($result_po_header,$i,"poTotAllow");
					$grid_stat=mssql_result($result_po_header,$i,"poStat");
					$suppCode=mssql_result($result_po_header,$i,"suppCode");
					
					$result_po_vendor=mssql_query("SELECT * FROM tblSuppliers WHERE suppCode = $suppCode");
					$num_po_vendor = mssql_num_rows($result_po_vendor);
					if ($num_po_vendor>0) {
							$grid_vendor_code=mssql_result($result_po_vendor,0,"suppCode");
							$grid_vendor_name=mssql_result($result_po_vendor,0,"suppName"); 
							$grid_vendor = $grid_vendor_code." - ".$grid_vendor_name;
					} else {
							$grid_vendor="NA";
					}
					if ($radio_view_po==$grid_number) {
						$code_checked="checked";
					} else {
						$code_checked="";
					}
			?>
            <tr bgcolor="#DEEDD1"> 
              <td width="45" height="16"> <div align="center"> 
                  <input name="check<? echo $i; ?>" type="checkbox" value="<?php echo $grid_number; ?>">
                </div></td>
              <td width="50" height="16"> <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
                  <font color="#0000FF"> 
                  <label id="<? echo $grid_number; ?>" onClick="click_detail(this.id);" style="cursor: pointer;"><? echo $grid_number; ?></label>
                  </font> <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                  <input name="hide_po_number" type="hidden" id="hide_po_number" value="<?php echo $grid_number; ?>">
                  <input name="hide_from_date" type="hidden" id="hide_from_date" value="<?php echo $fromdate; ?>">
                  <input name="hide_to_date" type="hidden" id="hide_to_date" value="<?php echo $todate; ?>">
                  <input name="hide_vendor" type="hidden" id="hide_vendor" value="<?php echo $vendorcode; ?>">
                  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
                  </font></div></td>
              <td width="72" height="16"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                  <?
			  if ($grid_date>"") {
				$date = new DateTime($grid_date);
				$grid_date = $date->format("m-d-Y");		
				} else {
					$grid_date="";
				}
		  echo $grid_date;
		  ?>
                  <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                  <input name="hide_sql" type="hidden" id="hide_find_vendor6" value="<?php echo $query_po_header; ?>">
                  <input name="hide_find_vendor_details" type="hidden" id="hide_find_vendor7" value="<?php echo $hide_find_vendor; ?>">
                  <input name="hide_numeric_details" type="hidden" id="hide_numeric23" value="<?php echo $hide_numeric2; ?>">
                  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font></div></td>
              <td width="213" height="16" bgcolor="#DEEDD1"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                  <?
		  echo $grid_vendor;
		  ?>
                  <b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
                  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b> 
                  </span></font></div></td>
              <td width="75" height="16"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                  <?
				  if ($grid_stat=="R") {
				  	$grid_stat = "Released";	  
				  }
				  if ($grid_stat=="H") {
				  	$grid_stat = "Held";	  
				  }
				  if ($grid_stat=="P") {
				  	$grid_stat = "Partial";	  
				  }
				   if ($grid_stat=="C") {
				  	$grid_stat = "Closed";	  
				  }
		  echo $grid_stat;
		  ?>
                  </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                  </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
            </tr>
            <?
		  }
		  ?>
          </table>
          
        </div>
		 <input name="click_detail_hide" type="hidden" id="click_detail_hide">
		 <input name="num_detail_hide" type="hidden" id="num_detail_hide" value="<? echo $num_po_header; ?>">
        <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b>
        <input name="hide_status" type="hidden" id="hide_status" value="<?php echo $status_list; ?>">
        </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
      </form>
      <form action="" method="post" name="form1" id="form1">
      </form>
      <form action="po_inq_pdf_revise.php" method="post" name="form2" target="_blank">
        <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
        <input name="search_query" type="hidden" id="hide_find_vendor" value="<?php echo $query_po_header; ?>">
        <input name="search_selection" type="hidden" id="query_pdf" value="all_record">
        <input name="search_box" type="hidden" id="query_pdf2" value="<?php echo $search_box; ?>">
        </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
      </form>
      <p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> </font><font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></p>
    </div>
  </div>
</div>
</body>
</html>

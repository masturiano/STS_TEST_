<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../etc/etc.obj.php";
require_once "../../functions/inquiry_function.php";
$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$db = new DB;
$db->connect();

###########################################################
$fromdate=$_POST['hide_from_date'];
//$po_number=$_POST['radio_view_po'];
$po_number=$_POST['click_detail_hide'];
$vendor=$_POST['hide_vendor'];
$hide_status=$_POST['hide_status'];
$todate=$_POST['hide_to_date'];
$hide_sql=$_POST['hide_sql'];
$hide_find_vendor=$_POST['hide_find_vendor_details'];
$hide_numeric=$_POST['hide_numeric_details'];
###########################################################

#################### click po inq details back button #################
if(isset($_POST['po_inq_received_back_button'])) { 
	$po_number=$_POST['hide_po_number'];
	$fromdate=$_POST['hide_from_date'];
	$todate=$_POST['hide_to_date'];
	$vendor=$_POST['hide_vendor'];
	$hide_find_vendor=$_POST['hide_find_vendor'];
	$hide_numeric=$_POST['hide_numeric'];
	$hide_sql=$_POST['hide_sql'];
	$radio_view_prod=$_POST['hide_prod_number'];
}
###################### end of click po inq details back button ########################################################

###########################################################
$query_po_details="SELECT * FROM tblPOItemDtl WHERE orderedQty > 0 AND poNumber = $po_number ORDER BY prdNumber ASC";
$result_po_details=mssql_query($query_po_details);
$num_po_details = mssql_num_rows($result_po_details);
if ($num_po_details < 1) {
	echo "<script>alert('No Transactions for this Purchase Order Details for the requested period')</script>";
} else {
	$query_add_charges="SELECT * FROM tblPOAddCharges WHERE poNumber = $po_number ORDER BY poAddChargePcent ASC";
	$result_add_charges=mssql_query($query_add_charges);
	$num_add_charges = mssql_num_rows($result_add_charges);
	
	$query_misc_charges="SELECT * FROM tblPOMiscDtl WHERE poNumber = $po_number ORDER BY poMiscDesc ASC";
	$result_misc_charges=mssql_query($query_misc_charges);
	$num_misc_charges = mssql_num_rows($result_misc_charges);
	$query_allow="SELECT * FROM tblPoAllwDtl WHERE poNumber = $po_number ORDER BY allwTypeCode ASC";
	$result_allow=mssql_query($query_allow);
	$num_allow = mssql_num_rows($result_allow);
}


###########################################################

?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
<form action="po_inq.php" method="post" name="formissi" id="formissi">
        <table width="100%" border="0" bgcolor="#DEEDD1">
          <tr bgcolor="#DEEDD1"> 
            <td width="68" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">From 
                Date<b><b> </b></b></font></div></td>
            <td width="431" height="16"><font size="2" face="Arial, Helvetica, sans-serif"><b><b><b> 
              <input name="fromdate" style="height:20px;" type="text" id="fromdate2" value="<? echo $fromdate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b>To Date<b><b><b> 
              <input name="todate" style="height:20px;" type="text" id="todate2" value="<? echo $todate; ?>" size="10" maxlength="10" readonly="true">
              </b></b></b></font></td>
            <td width="256" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">PO 
                Number<b><b><b> </b></b></b> 
                <input name="po_number" style="height:20px;" type="text" id="po_number3" value="<? echo $po_number; ?>" size="15" readonly="true">
                </font></div></td>
          </tr>
          <tr bgcolor="#DEEDD1"> 
            <td width="20" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"> 
                Vendor </font></div></td>
            <td width="431" height="16"><font size="2" face="Arial, Helvetica, sans-serif"> 
              <input name="vendor" style="height:20px;" type="text" id="vendor2" value="<? echo $vendor; ?>" size="35" readonly="true">
              <input name='po_inq_details_back_button' style="height:20px;" type='submit' class='queryButton' id='po_inq_details_back_button' title='Back to PO Header' value='Back'/>
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              <input name="hide_from_date" type="hidden" id="hide_from_date3" value="<?php echo $hide_from_date; ?>">
              <input name="hide_to_date" type="hidden" id="hide_to_date2" value="<?php echo $hide_to_date; ?>">
              <input name="hide_vendor" type="hidden" id="hide_vendor2" value="<?php echo $hide_vendor; ?>">
              <input name="hide_po_number" type="hidden" id="hide_po_number2" value="<?php echo $po_number; ?>">
              <input name="hide_sql" type="hidden" id="hide_sql3" value="<?php echo $hide_sql; ?>">
              <input name="hide_find_vendor" type="hidden" id="hide_find_vendor4" value="<?php echo $hide_find_vendor; ?>">
              <input name="hide_numeric" type="hidden" id="hide_numeric3" value="<?php echo $hide_numeric; ?>">
              <input name="status_list" type="hidden" id="status_list" value="<?php echo $hide_status; ?>">
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              </font><font size="2" face="Arial, Helvetica, sans-serif"> <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
              </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span> 
              <b><b><b> </b></b></b> </font></td>
            <td width="256" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif">PO 
                Status 
				<?
				$result_header=mssql_query("SELECT * FROM tblPoHeader WHERE compCode = $company_code AND poNumber = $po_number");
				$grid_stat = mssql_result($result_header,0,"poStat");
				  if ($grid_stat=="R") {
					$grid_stat = "Released";	  
				  }
				  if ($grid_stat=="O") {
					$grid_stat = "Open";	  
				  }
				  if ($grid_stat=="X") {
					$grid_stat = "Cancelled";	  
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
				?>
                <input name="status_list" style="height:20px;" type="text" id="po_number" value="<? echo $grid_stat; ?>" size="15" readonly="true">
                </font></div></td>
          </tr>
          <tr bgcolor="#6AB5FF"> 
            <td height="0" colspan="3"><font size="2" face="Arial, Helvetica, sans-serif"> 
            </td>
          </tr>
        </table>
        </form>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <div align="right"></div>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <form action="po_inq_received.php" method="post" name="form_po_received" id="form_po_received">
        <div align="center"> 
          <div id="Layer1" style="position:absolute; left:-1px; top:63px; width:99%; height:80px; z-index:1; overflow: scroll;"> 
            <table width="98%" border="0" align="center" >
              <tr bgcolor="#C2DDAA"> 
                <td height="16" colspan="9"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Purchase 
                    Order Details </font><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
				  if ($num_po_details>"") {
						echo " - ". $num_po_details . " record/s found.";	  
				  } else {
				  		echo  "";
				  }
		  
		  ?>
                    </span></font></div></td>
              </tr>
              <tr bgcolor="#6AB5FF"> 
                <td width="59" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"> 
                    Details</font></strong></div></td>
                <td width="73" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Product 
                    Code </font></strong></div></td>
                <td width="212" height="16" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Description</span></font></strong></div></td>
                <td width="70" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">U/M</font></strong></div></td>
                <td width="68" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Qty 
                    Ordered</font></strong></div></td>
                <td width="73" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Qty 
                    Received</font></strong></div></td>
                <td width="66" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Unit 
                    Cost </font></strong></div></td>
                <td width="63" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Ext 
                    Amt </font></strong></div></td>
                <td width="58" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Discount 
                    </font></strong></div></td>
              </tr>
            </table>
          </div>
          <div id="Layer1" style="position:absolute; left:-1px; top:104px; width:99%; height:216px; z-index:1; overflow: scroll;"> 
            <table width="98%" border="0" align="center" >
              <? 
			  	$total_ordered=0;
				$total_received=0;
				$total_extended=0;
				for ($i=0;$i<$num_po_details;$i++){ 
					$grid_number=mssql_result($result_po_details,$i,"prdNumber");
					$grid_um_code=mssql_result($result_po_details,$i,"umCode");
					$grid_conv=mssql_result($result_po_details,$i,"prdConv");
					$grid_conv= $grid_conv * 1;
					$grid_um=$grid_um_code." / ".$grid_conv;
					$grid_unit_cost=mssql_result($result_po_details,$i,"poUnitCost");
					$grid_discounts=mssql_result($result_po_details,$i,"itemDiscPcents");
					$grid_qty_ordered=mssql_result($result_po_details,$i,"orderedQty");
					$grid_qty_received=mssql_result($result_po_details,$i,"rcrQty");
					$grid_ext_amt=mssql_result($result_po_details,$i,"poExtAmt");
					$total_ordered=$total_ordered+$grid_qty_ordered;
					$total_received=$total_received+$grid_qty_received;
					$total_extended=$total_extended+$grid_ext_amt;
					if ($grid_discounts=="") {
						$grid_discounts="0";
					}
					///// get product description
					$query_prod_desc="SELECT * FROM tblProdMast WHERE prdNumber = $grid_number";
					$result_prod_desc=mssql_query($query_prod_desc);
					$num_prod_desc = mssql_num_rows($result_prod_desc);
					if ($num_prod_desc>0) {
						$grid_desc=mssql_result($result_prod_desc,0,"prdDesc");
						$grid_desc=str_replace("\\","",$grid_desc);
					} else {
						$grid_desc="NA";
					}
					
					if ($radio_view_prod==$grid_number) {
						$code_checked="checked";
					} else {
						$code_checked="";
					}
			?>
              <tr bgcolor="#DEEDD1"> 
                <td width="59"><div align="center"> 
                    <input name="radio_view_prod" type="radio" onClick="javascript:document.form_po_received.submit();" value="<? echo $grid_number; ?>" <? echo $code_checked; ?>>
                  </div></td>
                <td width="73"><font size="2" face="Arial, Helvetica, sans-serif"> 
                  <?
			echo $grid_number;
		  ?>
                  </font></td>
                <td width="212" bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                  <?
			
		  echo $grid_desc;
		  ?>
                  </span></font></td>
                <td width="70"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
		  echo $grid_um;
		  ?>
                    </span></font></div></td>
                <td width="68"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
		  echo $grid_qty_ordered;
		  ?>
                    </span></font></div></td>
                <td width="73"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
		  echo $grid_qty_received;
		  ?>
                    </span></font></div></td>
                <td width="66"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
		  echo $grid_unit_cost;
		  ?>
                    </span></font></div></td>
                <td width="63"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    <?
		  echo $grid_ext_amt;
		  ?>
                    </span></font></div></td>
                <td width="58"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                    </span><span class="style20"> 
                    <?
		  $rstSkuDisc=mssql_query("SELECT CONVERT(FLOAT, poItemDiscPcnt) AS AAA,poItemDiscTag  FROM tblPoItemDisc WHERE compCode = $company_code AND poNumber = $po_number AND prdNumber = $grid_number");	
		  $numSkuDisc = mssql_num_rows($rstSkuDisc);
		  $grid_discounts2="";
		  for ($k=0; $k<$numSkuDisc; $k++) {
		  	$grid_discounts2 = $grid_discounts2.mssql_result($rstSkuDisc,$k,"AAA")."% (".mssql_result($rstSkuDisc,$k,"poItemDiscTag")."), ";
		  }
		  echo $grid_discounts2;
		  ?>
                    </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                    </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
              </tr>
              <?
		  }
		  ?>
            </table>
          </div>
          <font size="2" face="Arial, Helvetica, sans-serif"> <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
          </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
          <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
          </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font>
          <div id="Layer2" style="position:absolute; left:7px; top:304px; width:98.5%; height:200; z-index:2; overflow: scroll;"> 
            <table width="100%" border="0">
              <tr> 
                <td width="27%" height="21" colspan="2"><table width="99%" border="0" bgcolor="#CCCCCC">
                    <tr bgcolor="#DEEDD1"> 
                      <td><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
                          Qty Ordered <span class="style20"> : 
                          <?
				  if ($total_ordered>"") {
				  		$total_ordered = number_format($total_ordered,4);
						echo $total_ordered;	  
				  } else {
				  		echo  "0.0000";
				  }
		  
		  ?>
                          </span></font></strong></div></td>
                      <td><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
                          Qty Received<span class="style20"> : 
                          <?
				  if ($total_received>"") {
				  		$total_received = number_format($total_received,4);
						echo $total_received;	  
				  } else {
				  		echo  "0.0000";
				  }
		  
		  ?>
                          </span></font></strong></div></td>
                      <td><div align="right"><strong><font size="2" face="Arial, Helvetica, sans-serif">Total 
                          Extended Amount :<span class="style20"> 
                          <?
				  if ($total_extended>"") {
				  		$total_extended = number_format($total_extended,4);		
						echo $total_extended;	  
				  } else {
				  		echo  "0.0000";
				  }
		  
		  ?>
                          </span><span class="style20"> </span></font></strong></div></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td height="0" colspan="2" bgcolor="#6AB5FF"></td>
              </tr>
              <tr>
                <td height="0">&nbsp;</td>
                <td height="0"><table width="500" border="0" align="right">
                    <tr bgcolor="#C2DDAA"> 
                      <td height="16" colspan="3"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Vendor 
                          Allowances </font></div></td>
                    </tr>
                    <tr bgcolor="#6AB5FF">
                      <td width="257" bgcolor="#6AB5FF"><strong><font size="2" face="Arial, Helvetica, sans-serif">Allowance 
                        Description</font></strong></td>
                      <td width="257" height="16" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">%</font></strong></div></td>
                      <td width="233" bgcolor="#6AB5FF" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Amount</span> 
                          <? 
				for ($i=0;$i<$num_allow;$i++){ 
					$grid_allow_code=mssql_result($result_allow,$i,"allwTypeCode");
					$result_allow_desc=mssql_query("SELECT * FROM tblAllowType WHERE allwTypeCode = $grid_allow_code");
					$grid_allow_code = $grid_allow_code." - ". mssql_result($result_allow_desc,0,"allwDesc");
					$grid_allow_percent=mssql_result($result_allow,$i,"poAllwPcnt");
					$grid_allow_percent=$grid_allow_percent*1;
					$grid_allow_amount=mssql_result($result_allow,$i,"poAllwAmt");
			?>
                          </font></strong></div></td>
                    </tr>
                    <tr bgcolor="#DEEDD1">
                      <td width="257"><font size="2" face="Arial, Helvetica, sans-serif">
                        <?
			echo $grid_allow_code;
		  ?>
                        </font></td>
                      <td width="257" height="16"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
                          <?
			echo $grid_allow_percent. " (".mssql_result($result_allow_desc,0,"allwCostTag") .")";
		  ?>
                          </font></div></td>
                      <td width="233" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                          <?
			
		  echo $grid_allow_amount;
		  ?>
                          </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                          <?
		  }
		  ?>
                          </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b><span class="style20"> 
                          </span></font></div></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td height="0">&nbsp;</td>
                <td width="0" height="0"><table width="500" border="0" align="right">
                    <tr bgcolor="#C2DDAA"> 
                      <td height="16" colspan="3"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Additional 
                          Charges </font></div></td>
                    </tr>
                    <tr bgcolor="#6AB5FF"> 
                      <td width="48" height="16" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">%</font></strong></div></td>
                      <td width="89" bgcolor="#6AB5FF" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Amount</span></font></strong></div></td>
                      <td width="355" height="16" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Remarks 
                          <? 
				for ($i=0;$i<$num_add_charges;$i++){ 
					$grid_add_percent=mssql_result($result_add_charges,$i,"poAddChargePcent");
					$grid_add_percent=$grid_add_percent*1;
					$grid_add_amount=mssql_result($result_add_charges,$i,"poAddChargeAmt");
					$grid_add_remarks=mssql_result($result_add_charges,$i,"poAddChargeRemarks");
			?>
                          </font></strong></div></td>
                    </tr>
                    <tr bgcolor="#DEEDD1"> 
                      <td width="48" height="16"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"> 
                          <?
			echo $grid_add_percent;
		  ?>
                          </font></div></td>
                      <td width="89" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                          <?
			
		  echo $grid_add_amount;
		  ?>
                          </span></font></div></td>
                      <td width="355" height="16" bgcolor="#DEEDD1"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                          </span><span class="style20"> 
                          <?
		  echo $grid_add_remarks;
		  ?>
                          </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                          <?
		  }
		  ?>
                          </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td width="27%" height="0">&nbsp;</td>
                <td width="47%" height="0"><table width="500" border="0" align="right">
                    <tr bgcolor="#C2DDAA"> 
                      <td height="16" colspan="2"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Miscellaneous 
                          Charges </font></div></td>
                    </tr>
                    <tr bgcolor="#6AB5FF"> 
                      <td width="413" height="16"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Description</span></font></strong></td>
                      <td width="81" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Amount 
                          <? 
				for ($i=0;$i<$num_misc_charges;$i++){ 
					$grid_misc_desc=mssql_result($result_misc_charges,$i,"poMiscDesc");
					$grid_misc_amount=mssql_result($result_misc_charges,$i,"poMiscAmt");
			?>
                          </font></strong></div></td>
                    </tr>
                    <tr bgcolor="#DEEDD1"> 
                      <td width="413" height="16"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                        <?
			
		  echo $grid_misc_desc;
		  ?>
                        </span></font></td>
                      <td width="81" height="16"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
                          </span><span class="style20"> 
                          <?
		  echo $grid_misc_amount;
		  ?>
                          </span><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style20"> 
                          <?
		  }
		  ?>
                          </span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></font></div></td>
                    </tr>
                  </table></td>
              </tr>
            </table>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
          </div>
          <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b>
          <input name="hide_from_date2" type="hidden" id="hide_from_date" value="<?php echo $hide_from_date; ?>">
          <input name="hide_to_date2" type="hidden" id="hide_to_date" value="<?php echo $hide_to_date; ?>">
          <input name="hide_vendor2" type="hidden" id="hide_vendor" value="<?php echo $hide_vendor; ?>">
          <input name="hide_po_number2" type="hidden" id="hide_po_number3" value="<?php echo $po_number; ?>">
          <input name="hide_sql2" type="hidden" id="hide_sql2" value="<?php echo $hide_sql; ?>">
          <input name="hide_find_vendor2" type="hidden" id="hide_find_vendor2" value="<?php echo $hide_find_vendor; ?>">
          <input name="hide_numeric2" type="hidden" id="hide_numeric22" value="<?php echo $hide_numeric; ?>">
          </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
          </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
        </div>
      </form>
      <form action="" method="post" name="form1" id="form1">
      </form>
      <p align="center"><font size="2" face="Arial, Helvetica, sans-serif"> </font><font size="2" face="Arial, Helvetica, sans-serif"> 
        </font></p>
    </div>
  </div>
</div>
</body>
</html>

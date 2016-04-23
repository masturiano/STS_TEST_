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
<form action="po_short_over.php" method="post" name="formissi" id="formissi">
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
                <input name="status_list" style="height:20px;" type="text" id="po_number" value="<? echo $grid_stat ?>" size="15" readonly="true">
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
                <td height="16" colspan="7"><div align="center"><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">Purchase 
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
                <td width="107" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Product 
                    Code </font></strong></div></td>
                <td width="308" height="16" bgcolor="#6AB5FF"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><span class="style8 style20">Description</span></font></strong></div></td>
                <td width="101" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">U/M</font></strong></div></td>
                <td width="99" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Qty 
                    Ordered</font></strong></div></td>
                <td width="106" height="16"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Qty 
                    Received</font></strong></div></td>
                <td width="96"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif">Unit 
                    Cost </font></strong></div></td>
                <td width="101" height="16"><div align="center"><strong>Short/Over</strong></div></td>
              </tr>
            </table>
          </div>
          <div id="Layer1" style="position:absolute; left:-1px; top:104px; width:99%; height:400px; z-index:1; overflow: scroll;"> 
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
					$grid_um=$grid_um_code ." / ". $grid_conv;
					$grid_unit_cost=mssql_result($result_po_details,$i,"poUnitCost");
					$grid_discounts=mssql_result($result_po_details,$i,"itemDiscPcents");
					$grid_qty_ordered=mssql_result($result_po_details,$i,"orderedQty");
					$grid_qty_received=mssql_result($result_po_details,$i,"rcrQty");
					$grid_ext_amt=mssql_result($result_po_details,$i,"poExtAmt");
					$total_ordered=$total_ordered+$grid_qty_ordered;
					$total_received=$total_received+$grid_qty_received;
					$total_extended=$total_extended+$grid_ext_amt;
					$short_over = ($grid_qty_ordered * $grid_conv) - $grid_qty_received;
					$short_over = number_format($short_over,2);
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
                <td width="70"><div align="left"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"> 
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
                <td width="66"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"><span class="style20">
                    <?
		  echo $short_over;
		  ?>
                    </span></font></div></td>
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

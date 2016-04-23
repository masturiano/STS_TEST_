<?
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function2.php";
require_once "../../functions/inquiry_function.php";

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$today = date("m/d/Y");
$db = new DB;
$db->connect();

include "transfers_transact.php";

?>

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_reports/calendar.js'></script>
<script type='text/javascript' src='transfers_javascript.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4
 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body onLoad="<? echo $onload; ?>">
<?
	
?>
<form action="" method="post" name="transfers_form" id="transfers_form">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#DEEDD1">
    <tr bgcolor="#C5DFAC"> 
      <td height="16" colspan="9" class="style3"> <div align="center"></div>
        <div align="center"></div>
        <div align="center"></div>
        <table width="100%" border="0" align="center">
          <tr bgcolor="#6AB5FF"> 
            <td width="22%" height="16"><span class="style3">Transfer No 
              <input name="trans_no" type="text" id="trans_no" value="<? echo $trans_no; ?>" size="10" maxlength="8" readonly="true">
              <? echo $transfers_lookup; ?> </span><span class="style3"> </span></td>
            <td width="49%" height="16">Transfer Out : [[ <? echo $new_transfers; ?> 
              <? echo $edit_button; ?> <? echo $delete_button; ?> <? echo $refresh_button; ?> 
              <? echo $out_in_button; ?> ]] </td>
            <td width="29%" height="16"><? echo $edit_button_in; ?> <? echo $release_button; ?>
<input name="hide_button" type="text"  value="<? echo $hide_button; ?>" id="hide_button" style="position: absolute; left: 1300" size="1">
              <font color="#0000FF">
              <input name="hide_transfer_no" type="hidden" id="hide_transfer_no" value="<? echo $trans_no; ?>">
              </font><font color="#0000FF">
              <input name="hide_company" type="hidden" id="hide_company" value="<? echo $company_code; ?>">
              </font>
          </tr>
        </table></td>
    </tr>
    <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
      <th height="2" colspan="20" nowrap="nowrap" class="style6" align="center"> 
      </th>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#DEEDD1"> 
      <td width="87">From Loc.</td>
      <td width="200" bgcolor="#DEEDD1"><font size="2" face="Arial, Helvetica, sans-serif"> 
        <select name="from_location"  id="select" style="width:200px; height:20px;" onChange="val_location();">
          <option selected><? echo $from_location;  ?></option>
          <?
		  	$query_from_location="SELECT * FROM tblLocation WHERE compCode = $company_code ORDER BY locCode ASC";
			$result_from_location=mssql_query($query_from_location);
			$num_from_location = mssql_num_rows($result_from_location);	
			for ($i=0;$i<$num_from_location;$i++){  
					$from_loc_code=mssql_result($result_from_location,$i,"locCode"); 
					$from_loc_name=mssql_result($result_from_location,$i,"locName"); 
				?>
          <option><? echo $from_loc_code."-".$from_loc_name; ?></option>
          <? } ?>
          <option></option>
        </select>
        </font></td>
      <td width="85"><span class="style3">Status</span></td>
      <td width="119"> <input name="transfers_status" type="text" id="transfers_status" value="<? echo $transfers_status; ?>" size="10" maxlength="8" readonly="true"></td>
      <th width="0" rowspan="5" nowrap bgcolor="#FFFFFF"> <div align="left"></div></th>
      <td width="256" colspan="5" rowspan="5"><table width="100" border="0">
          <tr bgcolor="#C5DFAC"> 
            <td height="16" colspan="4"><div align="center"><strong><font size="2">Control 
                Totals</font></strong></div></td>
          </tr>
          <tr bgcolor="#46A3FF"> 
            <td width="18%" height="12"><font size="2">&nbsp;</font></td>
            <td width="19%" height="12"><font size="2">Hash</font></td>
            <td width="31%" height="12"><font size="2">Entered</font></td>
            <td width="32%" height="12"><font size="2">Difference</font></td>
          </tr>
          <tr bgcolor="#C5DFAC"> 
            <td height="16">Items</td>
            <td height="16"><input name="hash_items" style="height:20px;" type="text" onChange="val_hash();" id="hash_items" value="<? 
																				if (!$hash_items) {
																					$hash_items = "0";
																				}
																				echo $hash_items; 
																			?>" size="8" maxlength="8"></td>
            <td height="16"><font class="hdrProdDtl"> 
              <? 
											if (!$entered_items) {
												$entered_items = "0";
											}	
										  ?>
              <input name="entered_items" style="height:20px;" type="text" id="entered_items" value="<? echo $entered_items; ?>" size="8" maxlength="8" readonly="true">
              </font></td>
            <td height="16"><font class="hdrProdDtl"> 
              <? 
											if (!$difference_items) {
												$difference_items = "0";
											}
										 ?>
              <input name="differece_items" style="height:20px;" type="text" id="difference_items" onChange="val_hash();" value="<? echo $difference_items; ?>" size="8" maxlength="8" readonly="true">
              </font></td>
          </tr>
          <tr bgcolor="#C5DFAC"> 
            <td>Qantity</td>
            <td><input name="hash_quantity" style="height:20px;" type="text"  onChange="val_hash();" id="hash_quantity" size="8" maxlength="8" value="<? 
																				if (!$hash_quantity) {
																					$hash_quantity = "0";
																				}
																				echo $hash_quantity; 
																			?>"></td>
            <td><font class="hdrProdDtl"> 
              <? 
										   if (!$entered_quantity) {
												$entered_quantity = "0";
										   }
										 ?>
              <input name="entered_quantity" style="height:20px;" type="text" id="entered_quantity" value="<? echo $entered_quantity; ?>" size="8" maxlength="8" readonly="true">
              </font></td>
            <td><font class="hdrProdDtl"> 
              <? 
										   if (!$difference_quantity) {
												$difference_quantity = "0";
										   }
										 ?>
              <input name="difference_quantity" style="height:20px;" type="text" id="hash_items242" value="<? echo $difference_quantity; ?>" size="8" maxlength="8" readonly="true">
              </font></td>
          </tr>
          <tr bgcolor="#C5DFAC"> 
            <td height="16" colspan="4"><div align="center"><font size="2" class="hdrProdDtl"> 
                </font><font class="hdrProdDtl"> 
                <input name="control_totals"  border="none" style="background-color:#C5DFAC; height:18px; text-align: center; Cancelground:#C5DFAC; border:0px solid;" type="text" id="control_totals" value="<? echo $control_totals; ?>" size="40" maxlength="100" readonly="true">
                </font><font size="2" class="hdrProdDtl"> </font><font class="hdrProdDtl"> 
                </font><font class="hdrProdDtl"> </font></div></td>
          </tr>
        </table></td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#DEEDD1"> 
      <td width="87">To Loc.</td>
      <td width="200"><font size="2" face="Arial, Helvetica, sans-serif"> 
        <select name="to_location" id="select4" style="width:200px; height:20px;" onChange="val_location();">
          <option selected><? echo $to_location;  ?></option>
          <?
		  	$query_from_location="SELECT * FROM tblLocation WHERE compCode = $company_code ORDER BY locCode ASC";
			$result_from_location=mssql_query($query_from_location);
			$num_from_location = mssql_num_rows($result_from_location);	
			for ($i=0;$i<$num_from_location;$i++){  
					$from_loc_code=mssql_result($result_from_location,$i,"locCode"); 
					$from_loc_name=mssql_result($result_from_location,$i,"locName"); 
				?>
          <option><? echo $from_loc_code."-".$from_loc_name; ?></option>
          <? } ?>
          <option></option>
        </select>
        </font></td>
      <td width="85">Responsible</td>
      <td width="119"><font class="hdrProdDtl"> 
        <input name="responsible" type="text" id="responsible2" value="<? echo $responsible; ?>" size="15" maxlength="20" readonly="true">
        </font><font size="2" face="Arial, Helvetica, sans-serif">&nbsp; </font></td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#DEEDD1"> 
      <td width="87">Date</td>
      <td width="200"><font size="2" face="Arial, Helvetica, sans-serif"> 
        <input name="transfers_date" type="text" id="transfers_date"   onBlur="val_date();" value="<? 
																									$gmt = time() + (8 * 60 * 60);
																									$date = date("m-d-Y", $gmt);
																									$today = date("m/d/Y");
																									if (!$transfers_date) {
																										$transfers_date = $today;
																									}
																									echo $transfers_date;
																									?>" size="10" readonly="true">
        <a href="javascript:void(0)" onClick="showCalendar(transfers_form.transfers_date,'MM/DD/yyyy','Choose date')"><img src="../../functions/inquiry_reports/CAL-icon.gif" border="0" width="16" height="16" alt="Click Here to use a calendar"></a> 
        </font></td>
      <td width="85" colspan="2" rowspan="3">&nbsp; </td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#DEEDD1"> 
      <td>Stock Tag</td>
      <td><font size="2" face="Arial, Helvetica, sans-serif"> 
        <select name="stock_tag" id="select9">
          <option selected><? echo $stock_tag;?></option>
          <option>1 - Good</option>
          <option>2 - Bad</option>
        </select>
        </font></td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#DEEDD1"> 
      <td width="87">Remarks</td>
      <td width="200"><input name="remarks" type="text" id="remarks" value="<? 
	  																		if (!$remarks) {
																				$remarks = "None";
																			}																		
																			echo $remarks; 
																			?>" size="33" maxlength="50"></td>
    </tr>
  </table>
  <div id="product_lookup"  style="position:absolute; left:4px; top:330px; width:99%; height:80px; z-index:2; Cancelground-color: #CCCCCC; layer-Cancelground-color: #CCCCCC; overflow: visible;"> 
    <table width="98%" border="0" align="center" id="table_lookup">
      <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
        <th height="16" colspan="17" align="center" nowrap="nowrap" class="style3"><font size="2">UPC 
          LOOKUP 
          <?
				#########################################     details
				$query_details2="SELECT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdSellUnit, tblProdPrice.regUnitPrice, tblAveCost.aveUnitCost, 
                      				tblUpc.upcCode, tblUpc.upcDesc, tblProdPrice.compCode, tblAveCost.compCode AS Expr1
									FROM tblProdMast LEFT JOIN
                      				tblAveCost ON tblProdMast.prdNumber = tblAveCost.prdNumber LEFT JOIN
                      				tblProdPrice ON tblProdMast.prdNumber = tblProdPrice.prdNumber LEFT JOIN
                      				tblUpc ON tblProdMast.prdNumber = tblUpc.prdNumber
									WHERE tblProdMast.prdNumber = 0";		
				if ($combo_search=="UPC Number" && $prod_no_desc>"") {
					$query_details2="SELECT     TOP 100 PERCENT tblUpc.upcCode, tblUpc.upcDesc, tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdFrcTag, 
                      				tblProdMast.prdDelTag, tblProdMast.prdSellUnit
									FROM         tblUpc LEFT OUTER JOIN
                      				tblProdMast ON tblUpc.prdNumber = tblProdMast.prdNumber
									WHERE (tblProdMast.prdDelTag <> 'D' OR tblProdMast.prdDelTag <> 'I') AND tblUpc.upcCode LIKE '$prod_no_desc%'
									ORDER BY tblUpc.upcDesc ASC";		
				} 
				if ($combo_search=="UPC Description" && $prod_no_desc>"") {
					$query_details2="SELECT     TOP 100 PERCENT tblUpc.upcCode, tblUpc.upcDesc, tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdFrcTag, 
                      				tblProdMast.prdDelTag, tblProdMast.prdSellUnit
									FROM         tblUpc LEFT OUTER JOIN
                      				tblProdMast ON tblUpc.prdNumber = tblProdMast.prdNumber
									WHERE (tblProdMast.prdDelTag <> 'D' OR tblProdMast.prdDelTag <> 'I') AND tblUpc.upcDesc LIKE '$prod_no_desc%' 
									ORDER BY tblUpc.upcDesc ASC";
				}
				if ($combo_search=="SKU" && $prod_no_desc>"") {
					$query_details2="SELECT     TOP 100 PERCENT tblUpc.upcCode, tblUpc.upcDesc, tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdFrcTag, 
                      				tblProdMast.prdDelTag, tblProdMast.prdSellUnit
									FROM         tblUpc LEFT OUTER JOIN
                      				tblProdMast ON tblUpc.prdNumber = tblProdMast.prdNumber
									WHERE (tblProdMast.prdDelTag <> 'D' OR tblProdMast.prdDelTag <> 'I') AND tblProdMast.prdNumber LIKE '$prod_no_desc%' 
									ORDER BY tblUpc.upcDesc ASC";
				}
				$result_details2=mssql_query($query_details2);
				$num_details2 = mssql_num_rows($result_details2);
				if ($num_details2 < 1) {
					$message = "O record/s.";
				} else {
					$message = "$num_details2 record/s.";
					
				}
				echo "<input name='hide_num' type='text' id='hide_num' value='$num_details2' style='position: absolute; left: 1300' size='1'>";
				echo " [ ".$message." ] ";
		  ?>
          </font> </th>
      </tr>
      <tr nowrap="wrap" align="left" bgcolor="#DEEDD1"> 
        <th height="16" colspan="17" align="center" nowrap="nowrap" class="style3"><font color="#0000FF"> 
          <input name="hide_insert" type="text" id="hide_insert" value="<? echo $hide_insert; ?>" style="position: absolute; left: 1300" size="1">
          <input name="hide_find" type="hidden" id="hide_find" value="<? echo $hide_find; ?>">
          </font>Search By 
          <?
		  	if (!$combo_search) {
				$combo_search="UPC Number";
			}
		  ?>
          <select name="combo_search" id="select3" style="Height:18px;">
            <option selected><? echo $combo_search; ?></option>
            <option value="UPC Number">UPC Number</option>
            <option value="UPC Description">UPC Description</option>
			<option value="SKU">SKU</option>
          </select> <input name="prod_no_desc" type="text" onchange="find_product(); document.transfers_form.prod_no_desc.focus();" size="30" maxlength="40" style="height:20px;">
         </th>
      </tr>
      <tr bgcolor="#6AB5FF" > 
        <td width="15" align="center" class="style3"> <div align="center"><font size="2">--</font></div></td>
        <td width="94" align="center" class="style3"><font size="2">UPC No</font></td>
        <td width="78" align="center" class="style3"> <div align="center"><font size="2">SKU</font></div></td>
        <td width="306" align="center" class="style3"> <div align="center"><font size="2">UPC 
            Description </font></div></td>
        <td width="79" align="center" class="style3"> <div align="center"><font size="2">UM</font></div></td>
        <td width="78" align="center" class="style3"> <div align="center"><font size="2">Qty 
            Out</font></div></td>
        <td width="77" align="center" class="style3"> <div align="center"><font size="2">Oty 
            In</font></div></td>
        <td width="79" align="center" class="style3"><div align="center"><font size="2">Unit 
            Cost</font></div></td>
        <td width="104" align="center" class="style3"> <div align="left"><font size="2">Unit 
            Price</font></div></td>
      </tr>
      <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
        <th height="1" colspan="17" nowrap="nowrap" class="style6" align="center"><font size="2"></font> 
        </th>
      </tr>
      <?
	#########################################     details
	for ($i=0;$i<$num_details2;$i++){ 
		$prod_no2=mssql_result($result_details2,$i,"upcCode");
		$prod_no_new=mssql_result($result_details2,$i,"prdNumber");
		$prod_desc2=mssql_result($result_details2,$i,"upcDesc");
		$prod_desc2=str_replace("\\","",$prod_desc2);
		$um2=mssql_result($result_details2,$i,"prdSellUnit");
		
		$result_ave_price=mssql_query("SELECT * FROM tblProdPrice WHERE prdNumber = $prod_no_new AND compCode = $company_code");
		$num_ave_price = mssql_num_rows($result_ave_price);
		if ($num_ave_price>0) {
			$unit_price2=mssql_result($result_ave_price,0,"regUnitPrice");
		} else {
			$unit_price2="";
		}
		$result_ave_cost=mssql_query("SELECT * FROM tblAveCost WHERE prdNumber = $prod_no_new AND compCode = $company_code");
		$num_ave_cost = mssql_num_rows($result_ave_cost);
		if ($num_ave_cost>0) {
			$unit_cost2=mssql_result($result_ave_cost,0,"aveUnitCost");
		} else {
			$unit_cost2="";
		}
		
		$fractional_tag2=mssql_result($result_details2,$i,"prdFrcTag");
		$unit_cost2 = number_format($unit_cost2,4);
		$unit_price2 = number_format($unit_price2,2);
		if ($option_button == "new_transfers") {
			/*
			$out2_readonly = "";
			$in2_readonly = "readonly=\"true\"";
			*/
		} else {
			/*if ($option_button == "edit_transfers" || $option_button == "edit_transfers_in") {
				if ($quantity_button == "in") {
					$out2_readonly = "readonly=\"true\"";
					$in2_readonly = "";
				} else {
					$out2_readonly = "";
					$in2_readonly = "readonly=\"true\"";
				}
			}*/
		}
	?>
      <?
	}
	?>
    </table>
    <p align="center"><font color="#0000FF"> </font></p>
  </div>
  <div id="Layer1" style="position:absolute; left:3px; top:159px; width:99%; height:100px; z-index:3; overflow: visible;"> 
    <table width="98%" border="0" align="center" id="table_detail">
      <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
        <th height="16" colspan="17" align="center" nowrap="nowrap" class="style3"><font size="2">TRANSFER 
          DETAILS 
          <? $num_details = $num_details*1; echo " [ " . $num_details." reccord/s. ]"; 
		echo "<input name='hide_num_details' type='text' id='hide_num_details' value='$num_details' style='position: absolute; left: 1300' size='1'>";
        ?>
          </font></th>
      </tr>
      <tr bgcolor="#C5DFAC" > 
        <td height="22" colspan="5" align="center" class="style3"> <div align="left"> 
            <?
		if ($num_details>0 || $option_button == "new_transfers") {
		 	echo "<div align='left'>With Selected : ";
		  	echo "<input type='button' value='Update Quantity' style='height:20px;' onClick='open_lookup2();'>";
			echo " || ";
			echo "<input type='button' value='Delete Details' style='height:20px;' onClick='open_lookup3();'>";
		}
	  ?>
          </div>
          <div align="left"></div></td>
        <td height="22" align="center" class="style3"><font size="2"> 
          <input name="total_out" type="text" id="total_out" style="height:20px; text-align: right;" onChange="val_hash();" value="<? 
																				if (!$entered_quantity) {
																					$entered_quantity = "0";
																				}
																				echo $entered_quantity; 
																			?>" size="9" maxlength="15" readonly="true">
          </font></td>
        <td height="22" align="center" class="style3"><font size="2"> 
          <?
		  		if (!$total_in) {
					$total_in = "0";
					$boldText = "color:RED;";
					$blink_head = "<BLINK>";
					$blink_foot = "</BLINK>";
				} else {
					$boldText = "";
					$blink_head = "";
					$blink_foot = "";
				}
		  ?>
		  <? echo $blink_head; ?>
		  <input name="total_in" type="text" id="total_in7" style="height:20px; text-align: right; <? echo $boldText; ?>" onChange="val_hash();" value="<? echo $total_in; ?>" size="9" maxlength="15" readonly="true">
          <? echo $blink_foot; ?>
		  </font></td>
        <td height="22" colspan="2" align="center" class="style3"><font size="2"><img src="<? echo $pic_qty; ?>" name="check_qty" width="15" height="15" border="0" align="absbottom" id="check_qty" onclick="fDeleDtl('<?=$compCode?>','<?=$_POST['txtEventNo']?>','<?=$SKU?>')" type="button" value="..."> 
          <span class="style3"><? echo $quantity_memo; ?></span> </font></td>
      </tr>
      <tr bgcolor="#6AB5FF" > 
        <td width="17" height="16" align="center" class="style3"> <div align="center"><font size="2">--</font></div></td>
        <td width="103" align="center" class="style3"><font size="2">UPC</font></td>
        <td width="81" height="16" align="center" class="style3"> <div align="center"><font size="2">SKU</font></div></td>
        <td width="321" align="center" class="style3"> <div align="center"><font size="2">UPC 
            Description </font></div></td>
        <td width="83" align="center" class="style3"> <div align="center"><font size="2">UM</font></div></td>
        <td width="59" align="center" class="style3"> <div align="center"><font size="2">Qty 
            Out</font></div></td>
        <td width="61" align="center" class="style3"> <div align="center"><font size="2">Oty 
            In</font></div></td>
        <td width="81" align="center" class="style3"><div align="center"><font size="2">Unit 
            Cost</font></div></td>
        <td width="104" align="center" class="style3"> <div align="left"><font size="2">Unit 
            Price</font></div></td>
      </tr>
      <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
        <th height="2" colspan="17" nowrap="nowrap" class="style6" align="center"> 
        </th>
      </tr>
      <?
	for ($i=0;$i<$num_details;$i++){ 
		$prod_no=mssql_result($result_details,$i,"upcCode");
		$prd_no=mssql_result($result_details,$i,"prdNumber");
		$prod_desc=mssql_result($result_details,$i,"upcDesc");
		$prod_desc=str_replace("\\","",$prod_desc);
		$um=mssql_result($result_details,$i,"umCode");
		$quantity_out=mssql_result($result_details,$i,"trfQtyOut");
		$quantity_in=mssql_result($result_details,$i,"trfQtyIn");
		$unit_cost=mssql_result($result_details,$i,"trfCost");
		$unit_price=mssql_result($result_details,$i,"trfPrice");
		$fractional_tag=mssql_result($result_details,$i,"prdFrcTag");
		
		$quantity_out = number_format($quantity_out,0);
		$quantity_in = number_format($quantity_in,0);
		$unit_cost = number_format($unit_cost,2);
		$unit_price = number_format($unit_price,2);
	?>
      <?
	}
	?>
    </table>
  </div>
  <div id="Layer1" style="position:absolute; left:17px; top:228px; width:96.2%; height:100px; z-index:3; overflow: scroll;"> 
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" id="table_detail">
      <?
	for ($i=0;$i<$num_details;$i++){ 
		$prod_no=mssql_result($result_details,$i,"upcCode");
		$prd_no=mssql_result($result_details,$i,"prdNumber");
		$prod_desc=mssql_result($result_details,$i,"upcDesc");
		$prod_desc=str_replace("\\","",$prod_desc);
		$um=mssql_result($result_details,$i,"umCode");
		$quantity_out=mssql_result($result_details,$i,"trfQtyOut");
		$quantity_in=mssql_result($result_details,$i,"trfQtyIn");
		$unit_cost=mssql_result($result_details,$i,"trfCost");
		$unit_price=mssql_result($result_details,$i,"trfPrice");
		$fractional_tag=mssql_result($result_details,$i,"prdFrcTag");
		
		$quantity_out = number_format($quantity_out,0);
		$quantity_in = number_format($quantity_in,0);
		if ($quantity_out!=$quantity_in) {
			$color = "color:RED;";
		} else {
			$color = "";
		}
		$unit_cost = number_format($unit_cost,2);
		$unit_price = number_format($unit_price,2);
	?>
      <tr bgcolor="#DEEDD1" > 
        <td width="20" align="center" class="style3"><p> 
            <input name="check_detail<? echo $i;?>" type="checkbox" id="check_detail<? echo $i;?>" value="prod_no">
          </p></td>
        <td width="78" align="center" class="style3"><p> 
            <input name="prod_no<? echo $i;?>" style="height:20px;" type="text" id="prod_no<? echo $i;?>" value="<? echo $prod_no; ?>" size="13" maxlength="13" readonly="true">
          </p></td>
        <td width="60" align="center" class="style3"><div align="left">
            <input name="prd_no" type="text" id="unit_price22" value="<? echo $prd_no ?>" style="height:20px; " size="10" maxlength="9" readonly="true">
          </div></td>
        <td width="300" align="center" class="style3"><p align="left"> 
            <input name="prod_desc" type="text" style="height:20px;" id="prod_desc" value="<? echo $prod_desc; ?>" size="50" maxlength="40" readonly="true">
          </p></td>
        <td width="60" align="center" class="style3"><p align="left"> 
            <input name="um" type="text" id="um" style="height:20px;" value="<? echo $um; ?>" size="10" maxlength="8" readonly="true">
          </p></td>
        <td width="60" align="center" class="style3"><p align="left"> 
            <input name="quantity_out<? echo $i;?>" ontype="text" id="quantity_out<? echo $i;?>" style="text-align: right; height:20px; <? echo $color;?>" value="<? echo $quantity_out; ?>" size="10" maxlength="9" <? echo $out_readonly; ?> onChange="val_qty_out(this.id);">
          </p></td>
        <td width="60" align="center" class="style3"><p align="left"> 
            <input name="quantity_in<? echo $i;?>" type="text" id="quantity_in<? echo $i;?>" style="text-align: right; height:20px; <? echo $color;?>" value="<? echo $quantity_in; ?>" size="10" maxlength="9" <? echo $in_readonly; ?> onChange="val_qty_in(this.id);">
          </p></td>
        <td width="60" align="center" class="style3"><p align="left"> 
            <input name="unit_cost" type="text" id="unit_cost" value="<? echo $unit_cost; ?>" style="height:20px; text-align: right;" size="10" maxlength="9" readonly="true">
          </p></td>
        <td width="279" align="center" class="style3"><p align="left"> 
            <input name="unit_price" type="text" id="unit_price" value="<? echo $unit_price ?>" style="height:20px; text-align: right;" size="10" maxlength="9" readonly="true">
            <font color="#0000FF">
            <input name="fractional_tag<? echo $i;?>" type="hidden" id="fractional_tag<? echo $i;?>" value="<? echo $fractional_tag; ?>">
            </font> </p></td>
      </tr>
      <?
	}
	?>
    </table>
  </div>
  <div id="product_lookup"  style="position:absolute; left:10px; top:399px; width:97.2%; height:100px; z-index:2; Cancelground-color: #CCCCCC; layer-Cancelground-color: #CCCCCC; overflow: scroll;"> 
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" id="table_lookup">
      <?
	#########################################     details
	for ($i=0;$i<$num_details2;$i++){ 
		$prod_no2=mssql_result($result_details2,$i,"upcCode");
		$prod_no_new=mssql_result($result_details2,$i,"prdNumber");
		$prod_desc2=mssql_result($result_details2,$i,"upcDesc");
		$prod_desc2=str_replace("\\","",$prod_desc2);
		$um2=mssql_result($result_details2,$i,"prdSellUnit");
		$result_ave_price=mssql_query("SELECT * FROM tblProdPrice WHERE prdNumber = $prod_no_new AND compCode = $company_code");
		$num_ave_price = mssql_num_rows($result_ave_price);
		if ($num_ave_price>0) {
			$unit_price2=mssql_result($result_ave_price,0,"regUnitPrice");
		} else {
			$unit_price2="";
		}
		$result_ave_cost=mssql_query("SELECT * FROM tblAveCost WHERE prdNumber = $prod_no_new AND compCode = $company_code");
		$num_ave_cost = mssql_num_rows($result_ave_cost);
		if ($num_ave_cost>0) {
			$unit_cost2=mssql_result($result_ave_cost,0,"aveUnitCost");
		} else {
			$unit_cost2="";
		}
		$fractional_tag2=mssql_result($result_details2,$i,"prdFrcTag");
		$unit_cost2 = number_format($unit_cost2,4);
		$unit_price2 = number_format($unit_price2,2);
		/*if ($option_button == "new_transfers") {
			$out2_readonly = "";
			$in2_readonly = "readonly=\"true\"";
		} else {
			if ($option_button == "edit_transfers" || $option_button == "edit_transfers_in") {
				if ($quantity_button == "in") {
					$out2_readonly = "readonly=\"true\"";
					$in2_readonly = "";
				} else {
					$out2_readonly = "";
					$in2_readonly = "readonly=\"true\"";
				}
			}
		}*/
	?>
      <tr bgcolor="#DEEDD1" > 
        <td width="21" align="center" class="style3"><p> <img src="b_drop.png" name="check_detailz<? echo $i;?>" width="16" height="16" border="0" align="absbottom" id="check_detailz<? echo $i;?>" onclick="fDeleDtl('<?=$compCode?>','<?=$_POST['txtEventNo']?>','<?=$SKU?>')" type="button" value="..."> 
          </p></td>
        <td width="70" align="center" class="style3"><input name="prod_noz<? echo $i;?>" type="text" id="prod_noz<? echo $i;?>" value="<? echo $prod_no2; ?>" size="13" maxlength="13" readonly="true"></td>
        <td width="70" align="center" class="style3"><p>
            <input name="prd_no2" type="text" id="prd_no" value="<? echo $prod_no_new ?>" style="height:20px; " size="10" maxlength="9" readonly="true">
          </p></td>
        <td width="180" align="center" class="style3"><p> 
            <input name="prod_desc2" type="text" id="prod_desc2" value="<? echo htmlspecialchars(stripslashes($prod_desc2)); ?>" size="50" maxlength="40" readonly="true">
          </p></td>
        <td width="60" align="center" class="style3"><p> 
            <input name="umz<? echo $i;?>" type="text" id="umz<? echo $i;?>" value="<? echo $um2; ?>" size="10" maxlength="8" readonly="true">
          </p></td>
        <td width="60" align="center" bgcolor="#DEEDD1" class="style3"><p> 
            <?
		if ($quantity_outz=="") {
			$quantity_outz=0;
		}
		?>
            <input name="quantity_outz<? echo $i;?>" <? echo $out2_readonly; ?> type="text" onChange="val_qty_out2(this.id);" id="quantity_outz<? echo $i;?>" style="text-align: right;" value="<? echo $quantity_outz; ?>" size="10" maxlength="9">
          </p></td>
        <td width="60" align="center" class="style3"><p> 
            <input name="quantity_inz<? echo $i;?>" type="text" <? echo $in2_readonly; ?> id="quantity_inz<? echo $i;?>" style="text-align: right;" value="<? 
																										if ($quantity_inz=="") {
																											$quantity_inz=0;
																										}
																										echo $quantity_inz; 
																									  ?>" size="10" maxlength="9">
          </p></td>
        <td width="60" align="center" class="style3"><p> 
            <input name="unit_costz<? echo $i;?>" type="text" id="unit_costz<? echo $i;?>" value="<? echo $unit_cost2; ?>" style="text-align: right;" size="10" maxlength="9" readonly="true">
          </p></td>
        <td width="315" align="center" class="style3"><p align="left"> 
            <input name="unit_pricez<? echo $i;?>" type="text" id="unit_pricez<? echo $i;?>" value="<? echo $unit_price2 ?>" style="text-align: right;" size="10" maxlength="9" readonly="true">
            <font color="#0000FF"> 
            <input name="fractional_tagz<? echo $i;?>" type="hidden" id="fractional_tagz<? echo $i;?>" value="<? echo $fractional_tag2; ?>">
            </font> </p></td>
      </tr>
      <?
	}
	?>
    </table>
    <p align="center"><font color="#0000FF"> </font></p>
  </div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p><table width="20%" border="0" align="center">
  <tr> 
    <td height="30"><div align="center"><font color="#0000FF"> 
        <input name="Button" type="button" id="Button5"  style="height:20px;" onClick="open_lookup();" value="Save Transfer">
        </font></div></td>
  </tr>
</table>
<p>&nbsp;</p>
<p align="center"><font color="#0000FF"> </font></p>
</body>
</html>

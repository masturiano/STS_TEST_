<?
$trans_no=0;
$trans_no=$_POST['trans_no'];
$option_button=$_POST['option_button'];
$hide_button=$_POST['hide_button'];
$hide_insert=$_POST['hide_insert'];
$hide_num=$_POST['hide_num'];
$hide_num_details=$_POST['hide_num_details'];
$hide_find=$_POST['hide_find'];
$quantity_button=$_POST['quantity_button'];
if($hide_find=="find_mode"){
	$onload = "document.transfers_form.prod_no_desc.focus();";
} else {
	$onload="";
}
################################## insert / update transfers
switch ($hide_insert) {
	case "insert_mode":
			$hide_insert = "";
			if ($option_button == "new_transfers") {
				$total_details_checked = 0;
				$total_quantity_checked = 0;
				for($y = 0; $y < $hide_num; $y++) {
					$quantity_out = $_POST["quantity_outz$y"];
					if ($quantity_out > 0) {
						$from_location=getCodeofString($from_location); ///pick in inventory_inquiry_function.php
						$to_location=getCodeofString($to_location); ///pick in inventory_inquiry_function.php
						$stock_tag=getCodeofString($stock_tag);
						$stock_tag=trim($stock_tag);
						$total_details_checked++;
						$total_quantity_checked=$total_quantity_checked+$quantity_out;
						$prod_no = $_POST["prod_noz$y"];
						$sql_prdno = "SELECT * FROM tblUpc WHERE upcCode = '$prod_no'";
						$result_new=mssql_query($sql_prdno);
						$prod_no_new=mssql_result($result_new,0,"prdNumber");
						$um = $_POST["umz$y"];
						$unit_cost = $_POST["unit_costz$y"];
						$unit_price = $_POST["unit_pricez$y"];
						$InsertSQL = "INSERT INTO tblTransferDtl(compCode, trfNumber, upcCode, prdNumber, umCode, trfQtyOut, trfCost, trfPrice, trfItemTag)";
						$InsertSQL .= "VALUES (". $company_code. ", " . $trans_no . ", '" . $prod_no . "',".$prod_no_new.", '" . $um . "', " . $quantity_out . ", " . $unit_cost . ", " . $unit_price . ", '" . O . "')";
						mssql_query($InsertSQL);		
					}
				}
				$InsertSQL = "INSERT INTO tblTransferHeader(compCode, trfNumber, fromLocCode, stockTag, trfDate, toLocCode, trfItemTot, trfQtyTot, trfResponsible, trfRemarks, trfStatus)";
				$InsertSQL .= "VALUES (". $company_code. ", " . $trans_no . ", " . $from_location . ", " . $stock_tag . ", '" . $transfers_date . "', " . $to_location . ", " . $hash_items . ", " . $hash_quantity . ", " . $user_id . ", '" . $remarks . "', '" . O . "')";
				mssql_query($InsertSQL);
				echo "<script>alert('<<< Transfer No. $trans_no successfully saved >>>')</script>";
			}
			if ($option_button == "edit_transfers" || $option_button == "edit_transfers_in") {
					$from_location=getCodeofString($from_location); ///pick in inventory_inquiry_function.php
					$to_location=getCodeofString($to_location); ///pick in inventory_inquiry_function.php
					$stock_tag=getCodeofString($stock_tag);
					$stock_tag=trim($stock_tag);		
					$total_details_checked = 0;
					$total_quantity_checked = 0;
					$exist_message="";
					$flag_atleast_one = 0;
					for($y = 0; $y < $hide_num; $y++) {
						$quantity_out = $_POST["quantity_outz$y"];
						if ($quantity_out > 0) {
							$total_details_checked++;
							$total_quantity_checked=$total_quantity_checked+$quantity_out;
							$prod_no = $_POST["prod_noz$y"];
							$sql_prdno = "SELECT * FROM tblUpc WHERE upcCode = '$prod_no'";
							$result_new=mssql_query($sql_prdno);
							$prod_no_new=mssql_result($result_new,0,"prdNumber");
							$upc_desc_new=mssql_result($result_new,0,"upcDesc");
							$um = $_POST["umz$y"];
							$unit_cost = $_POST["unit_costz$y"];
							$unit_price = $_POST["unit_pricez$y"];
							$query_exist="SELECT * FROM tblTransferDtl WHERE compCode = $company_code AND trfNumber = $trans_no AND upcCode = '$prod_no'";
							$result_exist=mssql_query($query_exist);
							$num_exist = mssql_num_rows($result_exist);
							if ($num_exist > 0) {
								$exist_message = $exist_message .$prod_no." - ". $upc_desc_new .", ";
							} else {
								$flag_atleast_one = 1;
								$InsertSQL = "INSERT INTO tblTransferDtl(compCode, trfNumber, upcCode, prdNumber, umCode, trfQtyOut, trfCost, trfPrice, trfItemTag)";
								$InsertSQL .= "VALUES (". $company_code. ", " . $trans_no . ", '" . $prod_no . "',".$prod_no_new.", '" . $um . "', " . $quantity_out . ", " . $unit_cost . ", " . $unit_price . ", '" . O . "')";
								mssql_query($InsertSQL);		
							}
							
							
						}
					}
					$UpdateSQL = "UPDATE tblTransferHeader SET ";
					$UpdateSQL .= "fromLocCode = ".$from_location.", ";
					$UpdateSQL .= "stockTag = '".$stock_tag."', ";
					$UpdateSQL .= "trfDate = '".$transfers_date."', ";
					$UpdateSQL .= "toLocCode = ".$to_location.", ";
					$UpdateSQL .= "trfItemTot = ".$hash_items.", ";
					$UpdateSQL .= "trfQtyTot = ".$hash_quantity.", ";
					$UpdateSQL .= "trfRemarks = '".$remarks."' ";
					$UpdateSQL .= "WHERE trfNumber = " . $trans_no . " AND compCode = " . $company_code;
					mssql_query($UpdateSQL); 
					//if ($flag_atleast_one > 0) {
						echo "<script>alert('<<< Transfer No. $trans_no successfully saved >>>')</script>";
					//}
					if ($exist_message > "") {
						$exist_message = "UPC no/s. $exist_message are already exist...";
						echo "<script>alert('$exist_message')</script>";
					}
			}
			if ($option_button == "edit_transfers" || $option_button == "new_transfers") {
				$option_button = "edit_transfers";
			}
			if ($option_button == "edit_transfers_in") {
				$option_button = "edit_transfers_in";
			}
	break;
	case "edit_mode":
			$hide_insert = "";
			for($y = 0; $y < $hide_num_details; $y++) {
				$check_detail = $_POST["check_detail$y"];
				if ($check_detail == true) {
					$prod_no = $_POST["prod_no$y"];
					$quantity_out = $_POST["quantity_out$y"];
					$quantity_in = $_POST["quantity_in$y"];
					$UpdateSQL = "UPDATE tblTransferDtl SET ";
					$UpdateSQL .= "trfQtyOut = ".$quantity_out.", ";
					$UpdateSQL .= "trfQtyIn = ".$quantity_in." ";
					$UpdateSQL .= "WHERE trfNumber = " . $trans_no . " AND upcCode = '" . $prod_no . "' AND compCode = " . $company_code;
					mssql_query($UpdateSQL); 		
				}
			}
			if (trim($quantity_button)=="in") {
				$sql_header = mssql_query("SELECT * FROM tblTransferHeader WHERE trfNumber = $trans_no AND compCode = $company_code"); 
				if (mssql_result($sql_header,0,"trfRcvdDte")=="") {
					$today = date("m/d/Y");
					mssql_query("UPDATE tblTransferHeader SET trfRcvdDte = '$today' WHERE trfNumber = $trans_no AND compCode = $company_code"); 
				}
			}
			echo "<script>alert('<<< Detail/s of Transfer No. $trans_no successfully saved >>>')</script>";
	break;
	case "delete_mode":
			$hide_insert = "";
			for($y = 0; $y < $hide_num_details; $y++) {
				$check_detail = $_POST["check_detail$y"];
				if ($check_detail == true) {
					$prod_no = $_POST["prod_no$y"];
					$quantity_out = $_POST["quantity_out$y"];
					$quantity_in = $_POST["quantity_in$y"];
					mssql_query("DELETE FROM tblTransferDtl WHERE trfNumber = $trans_no AND upcCode = '$prod_no' AND compCode = $company_code"); 		
				}
			}
			
			echo "<script>alert('<<< Detail/s of Transfer No. $trans_no successfully deleted >>>')</script>";
	break;
	case "delete_all_mode":
			$hide_insert = "";
			mssql_query("DELETE FROM tblTransferHeader WHERE trfNumber = $trans_no AND compCode = $company_code"); 		
			mssql_query("DELETE FROM tblTransferDtl WHERE trfNumber = $trans_no AND compCode = $company_code"); 		
			echo "<script>alert('<<< Transfer No. $trans_no successfully deleted >>>')</script>";
			$option_button = "edit_transfers";
			$trans_no="";
	break;
	case "release_mode":
			$resPd=mssql_query("SELECT * FROM tblPeriod WHERE compCode = $company_code AND pdStat = 'O'");
			$numPd = mssql_num_rows($resPd);
			if ($numPd>0) {
				$monthPd = mssql_result($resPd,0,"pdCode");
				$yearPd = mssql_result($resPd,0,"pdYear");
			} else {
				echo "<script>alert('No Open Period... Please assist to your administrator.')</script>";
			}
			$hide_insert = "";
			mssql_query("UPDATE tblTransferHeader SET trfStatus = 'R' WHERE trfNumber = $trans_no AND compCode = $company_code");
			$sql_header = mssql_query("SELECT * FROM tblTransferHeader WHERE trfNumber = $trans_no AND compCode = $company_code");  
			$stockTag = mssql_result($sql_header,0,"stockTag");
			$trfDate = mssql_result($sql_header,0,"trfDate");
			$fromLocCode = mssql_result($sql_header,0,"fromLocCode");
			$sql_from_loc = mssql_query("SELECT * FROM tblLocation WHERE locCode = $fromLocCode AND compCode = $company_code");  
			$from_loc_type = mssql_result($sql_from_loc,0,"locType");
			
			$toLocCode = mssql_result($sql_header,0,"toLocCode");
			$sql_to_loc = mssql_query("SELECT * FROM tblLocation WHERE locCode = $toLocCode AND compCode = $company_code");  
			$to_loc_type = mssql_result($sql_to_loc,0,"locType");
			
			$today = date("m/d/Y h:i A");
			$trans_date = new DateTime($trfDate);
			$trans_month = $trans_date->format("m");
			$trans_year = $trans_date->format("Y");
			
			$sql_details = mssql_query("SELECT * FROM tblTransferDtl WHERE trfNumber = $trans_no AND compCode = $company_code"); 
			$num_sql_details = mssql_num_rows($sql_details);
			for ($i=0; $i<$num_sql_details; $i++) {
				$prdNumber = mssql_result($sql_details,$i,"prdNumber");
				$sql_prod_mast = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $prdNumber");  
				$prdGrpCode = mssql_result($sql_prod_mast,0,"prdGrpCode");
				$prdDeptCode = mssql_result($sql_prod_mast,0,"prdDeptCode");
				$prdClsCode = mssql_result($sql_prod_mast,0,"prdClsCode");
				$prdSubClsCode = mssql_result($sql_prod_mast,0,"prdSubClsCode");
				$prdType = mssql_result($sql_prod_mast,0,"prdType");
				$prdSetTag = mssql_result($sql_prod_mast,0,"prdSetTag");
				
				$umCode = mssql_result($sql_details,$i,"umCode");
				$trfQtyOut = mssql_result($sql_details,$i,"trfQtyOut");
				$trfQtyIn = mssql_result($sql_details,$i,"trfQtyIn");
				$trfQtyOutTemp = mssql_result($sql_details,$i,"trfQtyOut");
				$trfQtyInTemp = mssql_result($sql_details,$i,"trfQtyIn");
				$trfCost = mssql_result($sql_details,$i,"trfCost");
				$trfExtAmtOut = $trfQtyOutTemp * $trfCost;
				$trfExtAmtIn = $trfQtyInTemp * $trfCost;
				$trfPrice = mssql_result($sql_details,$i,"trfPrice");
				$trfItemTag = mssql_result($sql_details,$i,"trfItemTag");
				$upcCode = mssql_result($sql_details,$i,"upcCode");
				
				############### from location (quantity out)#####################
				$sql_inv_bal = mssql_query("SELECT * FROM tblInvBalM WHERE compCode = $company_code AND locCode = $fromLocCode AND prdNumber = $prdNumber AND pdMonth = $trans_month AND pdYear = $trans_year");
				$num_inv_bal = mssql_num_rows($sql_inv_bal);
				if ($num_inv_bal>0) {
					if ($stockTag<2) {
						$endBalGoodM = mssql_result($sql_inv_bal,0,"endBalGoodM")-$trfQtyOutTemp;
					} else {
						$endBalBoM = mssql_result($sql_inv_bal,0,"endBalBoM")-$trfQtyOutTemp;
					}
					$trfQtyOut = $trfQtyOut+mssql_result($sql_inv_bal,0,"mtdTransOut");
					$trfExtAmtOut = mssql_result($sql_inv_bal,0,"mtdTransA")-$trfExtAmtOut;
					if ($stockTag<2) {
						$UpdateSQL = "UPDATE tblInvBalM SET ";
						$UpdateSQL .= "endCostM = ".$trfCost.", ";
						$UpdateSQL .= "mtdTransOut = ".$trfQtyOut.", ";
						$UpdateSQL .= "mtdTransA = ".$trfExtAmtOut.", ";
						$UpdateSQL .= "endBalGoodM = ".$endBalGoodM." ";
						$UpdateSQL .= "WHERE compCode = $company_code AND locCode = $fromLocCode AND prdNumber = $prdNumber AND pdYear = $yearPd AND pdMonth = $monthPd";
					} else {
						$UpdateSQL = "UPDATE tblInvBalM SET ";
						$UpdateSQL .= "endCostM = ".$trfCost.", ";
						$UpdateSQL .= "mtdTransOut = ".$trfQtyOut.", ";
						$UpdateSQL .= "mtdTransA = ".$trfExtAmtOut.", ";
						$UpdateSQL .= "endBalBoM = ".$endBalBoM." ";
						$UpdateSQL .= "WHERE compCode = $company_code AND locCode = $fromLocCode AND prdNumber = $prdNumber AND pdYear = $yearPd AND pdMonth = $monthPd";
					}
					mssql_query($UpdateSQL); 
				} else {
					$trfExtAmtOut = 0-$trfExtAmtOut;
					if ($stockTag<2) {
						$endBalGoodM = 0-$trfQtyOutTemp;
						$endBalBoM = 0;
					} else {
						$endBalGoodM = 0;
						$endBalBoM = 0-$trfQtyOutTemp;
					}
					$begBalGoodM=0; $begBalBoM=0; $begCostM=0; $begPriceM=0; $mtdRecitQ=0;
					$mtdRecitA=0; $mtdRegSlesQ=0; $mtdRegSlesA=0; $mtdRegSlesC=0; $mtdTransIn=0;
					$mtdTransOut=0; $mtdTransA=0; $mtdAdjQ=0; $mtdAdjA=0; $mtdCountAdjQ=0; 
					$mtdCountAdjA=0; $mtdCiQ=0; $mtdCiA=0; $mtdSuQ=0; $mtdSuA=0;
					$endCostM=0; $endPriceM=0; 
					$InsertSQL = "INSERT INTO tblInvBalM(compCode, locCode, prdNumber, pdYear, pdMonth,
								  begBalGoodM, begBalBoM, begCostM, begPriceM, mtdRecitQ, 
								  mtdRecitA, mtdRegSlesQ, mtdRegSlesA, mtdRegSlesC, mtdTransIn, 
								  mtdTransOut, mtdTransA, mtdAdjQ, mtdAdjA, mtdCountAdjQ, 
								  mtdCountAdjA, mtdCiQ, mtdCiA, mtdSuQ, mtdSuA, 
								  endBalGoodM, endBalBoM, endCostM, endPriceM, updatedBy, dateUpdated)";
					$InsertSQL .= "VALUES (".$company_code.",".$fromLocCode.",".$prdNumber.",".$yearPd.",".$monthPd.
								  ",".$begBalGoodM.",".$begBalBoM.",".$begCostM.",".$begPriceM.",".$mtdRecitQ.
								  ",".$mtdRecitA.",".$mtdRegSlesQ.",".$mtdRegSlesA.",".$mtdRegSlesC.",".$mtdTransIn.
								  ",".$trfQtyOut.",".$trfExtAmtOut.",".$mtdAdjQ.",".$mtdAdjA.",".$mtdCountAdjQ.
								  ",".$mtdCountAdjA.",".$mtdCiQ.",".$mtdCiA.",".$mtdSuQ.",".$mtdSuA.
								  ",".$endBalGoodM.",".$endBalBoM.",".$trfCost.",".$endPriceM.",'".$user_id."','".$today."')";
					mssql_query($InsertSQL);
				}
				if ($stockTag<2) {
					$trQtyGood = $trfQtyOutTemp;
					$trQtyBo = 0;
				} else {
					$trQtyBo = $trfQtyOutTemp;
					$trQtyGood = 0;
				}
				$prdGroup=0; $prdDept=0; $prdClass=0; $prdSubClass=0;  
				$prdType=0; $pdYear=0; $pdCode=0; $suppCode=0; $custCode=0;
				$mtdCountAdjQ=0; $trQtyFree=0; $buyCost=0; $cstTypeCode=0;$refCostEvent=0;
				$UnitPrice=0; $refPriceEvent=0; $posPrice=0; $extAmt=0; $transCode=0; 
				$itemDiscCogY=0; $itemDiscCogN=0; $poLevelDiscCogY=0; $poLevelDiscCogN=0; $rcrAddCharges=0;
				$rsnCode=0; $terms=0;
				//if ($trfExtAmtOut<0) {
				$trfExtAmtOut = $trfQtyOutTemp * $trfCost;
				//} 
				$InsertSQL = "INSERT INTO tblInvTran(compCode, locCode, locType, prdNumber, prdGroup, 
							  prdDept, prdClass, prdSubClass, setCode, prdType, 
							  transCode, docNumber, docDate, pdYear, pdCode, 
							  refNo, suppCode, custCode, trQtyGood, trQtyBo,
							  trQtyFree, aveCost, buyCost, cstTypeCode, refCostEvent,
							  UnitPrice, prTypeCode, refPriceEvent, posPrice, extAmt,
							  itemDiscPcents, itemDiscCogY, itemDiscCogN, poLevelDiscCogY, poLevelDiscCogN,
							  rcrAddCharges, rsnCode, terms, dateUpdated, updatedBy)";
				$InsertSQL .= "VALUES (".$company_code.",".$fromLocCode.",'".$from_loc_type."',".$prdNumber.",".$prdGrpCode.
							  ",".$prdDeptCode.",".$prdClsCode.",".$prdSubClsCode.",'".$prdSetTag."','".$prdType.
							  "','32',".$trans_no.",'".$trfDate."',".$yearPd.",".$monthPd.
							  ",".$trans_no.",".$suppCode.",".$custCode.",".$trQtyGood.",".$trQtyBo.
							  ",".$trQtyFree.",".$trfCost.",".$buyCost.",".$cstTypeCode.",".$refCostEvent.
							  ",".$trfPrice.",'".$prTypeCode."',".$refPriceEvent.",".$posPrice.",".$trfExtAmtOut.
							  ",'".$itemDiscPcents."',".$itemDiscCogY.",".$itemDiscCogN.",".$poLevelDiscCogY.",".$poLevelDiscCogN.
							  ",".$rcrAddCharges.",".$rsnCode.",".$terms.",'".$today."','".$user_id."')";
				mssql_query($InsertSQL);
				
				############### to location (quantity in)########################
				$sql_inv_bal = mssql_query("SELECT * FROM tblInvBalM WHERE compCode = $company_code AND locCode = $toLocCode AND prdNumber = $prdNumber AND pdMonth = $trans_month AND pdYear = $trans_year");
				$num_inv_bal = mssql_num_rows($sql_inv_bal);
				
				if ($num_inv_bal>0) {
					if ($stockTag<2) {
						$endBalGoodM = mssql_result($sql_inv_bal,0,"endBalGoodM")+$trfQtyInTemp;
					} else {
						$endBalBoM = mssql_result($sql_inv_bal,0,"endBalBoM")+$trfQtyInTemp;
					}
					$trfQtyIn = $trfQtyInTemp+mssql_result($sql_inv_bal,0,"mtdTransIn");
					$trfExtAmtIn = mssql_result($sql_inv_bal,0,"mtdTransA")+$trfExtAmtIn;
					if ($stockTag<2) {
						$UpdateSQL = "UPDATE tblInvBalM SET ";
						$UpdateSQL .= "endCostM = ".$trfCost.", ";
						$UpdateSQL .= "mtdTransIn = ".$trfQtyIn.", ";
						$UpdateSQL .= "mtdTransA = ".$trfExtAmtIn.", ";
						$UpdateSQL .= "endBalGoodM = ".$endBalGoodM." ";
						$UpdateSQL .= "WHERE compCode = $company_code AND locCode = $toLocCode AND prdNumber = $prdNumber AND pdYear = $yearPd AND pdMonth = $monthPd";
					} else {
						$UpdateSQL = "UPDATE tblInvBalM SET ";
						$UpdateSQL .= "endCostM = ".$trfCost.", ";
						$UpdateSQL .= "mtdTransIn = ".$trfQtyIn.", ";
						$UpdateSQL .= "mtdTransA = ".$trfExtAmtIn.", ";
						$UpdateSQL .= "endBalBoM = ".$endBalBoM." ";
						$UpdateSQL .= "WHERE compCode = $company_code AND locCode = $toLocCode AND prdNumber = $prdNumber AND pdYear = $yearPd AND pdMonth = $monthPd";
					}
					mssql_query($UpdateSQL); 
				} else {
					$trfExtAmtIn = $trfExtAmtIn;
					if ($stockTag<2) {
						$endBalGoodM = $trfQtyInTemp;
						$endBalBoM = 0;
					} else {
						$endBalGoodM = 0;
						$endBalBoM = $trfQtyInTemp;
					}
					$begBalGoodM=0; $begBalBoM=0; $begCostM=0; $begPriceM=0; $mtdRecitQ=0;
					$mtdRecitA=0; $mtdRegSlesQ=0; $mtdRegSlesA=0; $mtdRegSlesC=0; $mtdTransIn=0;
					$mtdTransOut=0; $mtdTransA=0; $mtdAdjQ=0; $mtdAdjA=0; $mtdCountAdjQ=0; 
					$mtdCountAdjA=0; $mtdCiQ=0; $mtdCiA=0; $mtdSuQ=0; $mtdSuA=0;
					$endCostM=0; $endPriceM=0; 
					$InsertSQL = "INSERT INTO tblInvBalM(compCode, locCode, prdNumber, pdYear, pdMonth, 
								  begBalGoodM, begBalBoM, begCostM, begPriceM, mtdRecitQ, 
								  mtdRecitA, mtdRegSlesQ, mtdRegSlesA, mtdRegSlesC, mtdTransIn, 
								  mtdTransOut, mtdTransA, mtdAdjQ, mtdAdjA, mtdCountAdjQ, 
								  mtdCountAdjA, mtdCiQ, mtdCiA, mtdSuQ, mtdSuA, 
								  endBalGoodM, endBalBoM, endCostM, endPriceM, updatedBy, dateUpdated)";
					$InsertSQL .= "VALUES (".$company_code.",".$toLocCode.",".$prdNumber.",".$yearPd.",".$monthPd.
								  ",".$begBalGoodM.",".$begBalBoM.",".$begCostM.",".$begPriceM.",".$mtdRecitQ.
								  ",".$mtdRecitA.",".$mtdRegSlesQ.",".$mtdRegSlesA.",".$mtdRegSlesC.",".$trfQtyIn.
								  ",".$mtdTransOut.",".$trfExtAmtIn.",".$mtdAdjQ.",".$mtdAdjA.",".$mtdCountAdjQ.
								  ",".$mtdCountAdjA.",".$mtdCiQ.",".$mtdCiA.",".$mtdSuQ.",".$mtdSuA.
								  ",".$endBalGoodM.",".$endBalBoM.",".$trfCost.",".$endPriceM.",'".$user_id."','".$today."')";
					mssql_query($InsertSQL);
				}
				if ($stockTag<2) {
					$trQtyGood = $trfQtyInTemp;
					$trQtyBo = 0;
				} else {
					$trQtyBo = $trfQtyInTemp;
					$trQtyGood = 0;
				}
				$prdGroup=0; $prdDept=0; $prdClass=0; $prdSubClass=0; 
				$prdType=0; $pdYear=0; $pdCode=0; $suppCode=0; $custCode=0;
				$mtdCountAdjQ=0; $trQtyFree=0; $buyCost=0; $cstTypeCode=0;$refCostEvent=0;
				$UnitPrice=0; $refPriceEvent=0; $posPrice=0; $extAmt=0; $transCode=0; 
				$itemDiscCogY=0; $itemDiscCogN=0; $poLevelDiscCogY=0; $poLevelDiscCogN=0; $rcrAddCharges=0;
				$rsnCode=0; $terms=0;
				//if ($trfExtAmtIn<0) {
				$trfExtAmtIn = $trfQtyInTemp * $trfCost;
				//} 
				$InsertSQL = "INSERT INTO tblInvTran(compCode, locCode, locType, prdNumber, prdGroup, 
							  prdDept, prdClass, prdSubClass, setCode, prdType, 
							  transCode, docNumber, docDate, pdYear, pdCode, 
							  refNo, suppCode, custCode, trQtyGood, trQtyBo,
							  trQtyFree, aveCost, buyCost, cstTypeCode, refCostEvent,
							  UnitPrice, prTypeCode, refPriceEvent, posPrice, extAmt,
							  itemDiscPcents, itemDiscCogY, itemDiscCogN, poLevelDiscCogY, poLevelDiscCogN,
							  rcrAddCharges, rsnCode, terms, dateUpdated, updatedBy)";
				$InsertSQL .= "VALUES (".$company_code.",".$toLocCode.",'".$to_loc_type."',".$prdNumber.",".$prdGrpCode.
							  ",".$prdDeptCode.",".$prdClsCode.",".$prdSubClsCode.",'".$prdSetTag."','".$prdType.
							  "','31',".$trans_no.",'".$trfDate."',".$yearPd.",".$monthPd.
							  ",".$trans_no.",".$suppCode.",".$custCode.",".$trQtyGood.",".$trQtyBo.
							  ",".$trQtyFree.",".$trfCost.",".$buyCost.",".$cstTypeCode.",".$refCostEvent.
							  ",".$trfPrice.",'".$prTypeCode."',".$refPriceEvent.",".$posPrice.",".$trfExtAmtIn.
							  ",'".$itemDiscPcents."',".$itemDiscCogY.",".$itemDiscCogN.",".$poLevelDiscCogY.",".$poLevelDiscCogN.
							  ",".$rcrAddCharges.",".$rsnCode.",".$terms.",'".$today."','".$user_id."')";
				mssql_query($InsertSQL);
			}		
			echo "<script>alert('<<< Transfer No. $trans_no successfully released >>>')</script>";
			$option_button = "edit_transfers_in";
			$trans_no="";
	break;
	case "cancel_release_mode":
			$hide_insert = "";
			$option_button = "edit_transfers_in";
	break;
	case "cancel_delete_all_mode":
			$hide_insert = "";
			$option_button = "edit_transfers";
	break;
}
############################################################
$query_trans_no="SELECT * FROM tblTransferNo WHERE compCode = $company_code ORDER BY lastTransferNo DESC";
$result_trans_no=mssql_query($query_trans_no);
$num_trans_no = mssql_num_rows($result_trans_no);
if ($num_trans_no > 0) {
	$last_trans_no=mssql_result($result_trans_no,0,"lastTransferNo");
}
#######################################

if ($option_button=="") {
	$option_button="refresh_transfers";
}
switch ($option_button) {
	case "new_transfers":
				$out2_readonly = "";
				$in2_readonly = "readonly=\"true\"";	
				$new_checked = "checked";
				$transfers_status = "Open";
				$responsible = $user_first_last;
				##refresh button
				$new_transfers = "<input name='option_button' type='radio' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				//$edit_button = "<input type='radio' name='option_button' $edit_checked value='edit_transfers' onClick='javascript:document.transfers_form.submit();'>Update/Delete  ";
				$refresh_button = "<input type='radio' name='option_button' $refresh_checked value='refresh_transfers' onClick='javascript:document.transfers_form.submit();'>Cancel ";
				if ($num_trans_no < 1) {
					echo "<script>alert('Please assign transfer number to your company...')</script>";
				} else {
					if ($hide_find=="find_mode") {
						
					} else {
						$trans_no =  $last_trans_no  + 1;
						$query_update_trans_no="UPDATE tblTransferNo SET lastTransferNo = $trans_no WHERE compCode = $company_code";
						$result_open_header=mssql_query($query_update_trans_no);
					}
				}
				break;
	case "edit_transfers":
				if ($trans_no==($last_trans_no + 1)) {
					$trans_no=0;
				}
				$edit_checked = "checked";
				$transfers_lookup = "<img src='../images/search.gif' name='img_code' align='absbottom' id='img_code' style='cursor:pointer;' title='Open Transfers LookUp' onClick=\"window.open('../../functions/transfers_lookup.php?search_selection=open_transfers','','width=500,height=500,left=250,top=100')\"/>";
				##delete button
				$new_transfers = "<input name='option_button' type='radio' id='new_transfers' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				$out_in_button = "<input name='quantity_button' type='radio' style='position: absolute; left: 1300' size='1' style='position: absolute; left: 1300' size='1' value='out' checked>";
				//$refresh_button = "<input type='radio' name='option_button' $refresh_checked value='refresh_transfers' onClick='option_button_click(this.id);'>Cancel ";
				###################### open header
				if ($trans_no >"") {		
					$out_readonly = "";
					$in_readonly = "readonly=\"true\"";
					$out2_readonly = "";
					$in2_readonly = "readonly=\"true\"";
					$delete_button = "<input type='radio' id='delete_transfers' name='option_button' $delete_checked value='delete_transfers' onClick='open_lookup4();'>Delete  ";
					##release button
					//$release_button = "<input type='radio' id='release_transfers' name='option_button'$release_checked value='release_transfers' onClick='option_button_click(this.id);'>Release Transfer  ";
					$edit_button = "<input type='radio' id='edit_transfers' name='option_button' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update";
					$edit_button_in = "<input type='radio' id='edit_transfers_in' name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
					$out_in_button = "<input name='quantity_button' type='radio' style='position: absolute; left: 1300' size='1' value='out' checked>";
					$query_open_header="SELECT * FROM tblTransferHeader WHERE trfNumber = $trans_no";
					$result_open_header=mssql_query($query_open_header);
					$num_ = mssql_num_rows($result_open_header);
					$trfNumber=mssql_result($result_open_header,0,"trfNumber");
					$from_loc_code=mssql_result($result_open_header,0,"fromLocCode");
					$to_loc_code=mssql_result($result_open_header,0,"toLocCode");
					$remarks=mssql_result($result_open_header,0,"trfRemarks");
					$status=mssql_result($result_open_header,0,"trfStatus");
					$transfers_date=mssql_result($result_open_header,0,"trfDate");
					$tag=mssql_result($result_open_header,0,"stockTag");
					$responsi=mssql_result($result_open_header,0,"trfResponsible");
					$query_responsible=mssql_query("SELECT * FROM tblUsers WHERE userid = $responsi");
					$responsible=mssql_result($query_responsible,0,"firstName") . " " . mssql_result($query_responsible,0,"lastName");
					$hash_items=mssql_result($result_open_header,0,"trfItemTot");
					$hash_quantity=mssql_result($result_open_header,0,"trfQtyTot");
					#################################
					$query_from_loc="SELECT * FROM tblLocation WHERE locCode = $from_loc_code AND compCode = $company_code";
					$result_from_loc=mssql_query($query_from_loc);
					$from_loc_name=mssql_result($result_from_loc,0,"locName");
					$from_location = $from_loc_code."-".$from_loc_name;
					#################################
					$query_to_loc="SELECT * FROM tblLocation WHERE locCode = $to_loc_code AND compCode = $company_code";
					$result_to_loc=mssql_query($query_to_loc);
					$to_loc_name=mssql_result($result_to_loc,0,"locName");
					$to_location = $to_loc_code."-".$to_loc_name;
					#################################
					if ($status == "O") {
						$transfers_status = "Open";
					} 
					if ($status == "R") {
						$transfers_status = "Released";
					} 
					if ($transfers_date=="") {
						$transfers_date = "";
					} else {
						$date = new DateTime($transfers_date);
						$transfers_date = $date->format("m/d/Y");
					}
					if ($tag == "1") {
						$stock_tag = "1 - Good";
					} 
					if ($tag == "2") {
						$stock_tag = "2 - Bad";
					} 
					$hash_items = number_format($hash_items,0);
					$hash_quantity = number_format($hash_quantity,0);
					#########################################
					$query_detail="SELECT COUNT(prdNumber) AS Expr1, SUM(trfQtyOut) AS Expr2, SUM(trfQtyIn) AS Expr3
								   FROM tblTransferDtl WHERE trfNumber = $trans_no";
					$result_detail=mssql_query($query_detail);
					$entered_items=mssql_result($result_detail,0,"Expr1");
					$entered_quantity=mssql_result($result_detail,0,"Expr2");
					$total_in=mssql_result($result_detail,0,"Expr3");
					$entered_items = number_format($entered_items,0);
					$entered_quantity = number_format($entered_quantity,0);
					$total_in = number_format($total_in,0);
					if ($entered_quantity!=$total_in) {
						$pic_qty = "../../Images/b_drop.png";
						$quantity_memo = "Totals Qty are not equal.";
					} else {
						$pic_qty = "../../Images/check.gif";
						$quantity_memo = "Totals Qty are complete.";
					}
					#########################################
					$difference_items = $hash_items - $entered_items;
					$difference_quantity = $hash_quantity - $entered_quantity;
					if ($difference_items!=0 || $difference_quantity!=0) {
						$control_totals = "Entered Totals Don't Tally With Control Totals... Please Check.";
					} else {
						$control_totals = "Data Entered are complete and accurate.";
					}
				} else {
					$edit_button = "<input type='radio' id='edit_transfers'  name='option_button' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update/Delete  ";
					$edit_button_in = "<input type='radio' id='edit_transfers_in'  name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
					$out_in_button = "<input name='quantity_button' type='radio' style='position: absolute; left: 1300' size='1' value='out' checked>";
				}
				break;
	case "release_transfers":
			    $edit_checked_in = "checked";
				$release_checked="";
				##release button
				$release_button = "<input type='radio' id='release_transfers'  name='option_button'$release_checked value='release_transfers' onClick='open_lookup5();'>Release";
				$edit_button = "<input type='radio' id='edit_transfers'  name='option_button' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update/Delete  ";
				$edit_button_in = "<input type='radio' id='edit_transfers_in'  name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
				$new_transfers = "<input id='new_transfers' id='new_transfers' name='option_button' type='radio' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				break;
	case "refresh_transfers":
				$$total_in="0";
				$hide_find = "";
				$hide_insert = "";
				$trans_no="";
				$from_location = "";
				$to_location = "";
				$remarks="None";
				$transfers_date = $today;
				$transfers_status = "Open";
				$stock_tag = "1 - Good";
				$hash_items = "0";
				$hash_quantity = "0";
				$entered_items = "0";
				$entered_quantity = "0";
				$difference_quantity="0";
				$responsible = $user_first_last;
				$refresh_checked = "checked";
				##refresh button
				//$refresh_button = "<input type='radio' name='option_button'$refresh_checked value='refresh_transfers' onClick='option_button_click(this.id);'>Menu ";
				$edit_button = "<input type='radio' name='option_button' id='edit_transfers' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update/Delete  ";
				$new_transfers = "<input name='option_button' id='new_transfers' type='radio' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				$edit_button_in = "<input type='radio' id='edit_transfers_in'  name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
				break;
	case "delete_transfers":
				$trans_no=0;
				$edit_checked = "checked";
				$transfers_lookup = "<img src='../images/search.gif' name='img_code' align='absbottom' id='img_code' style='cursor:pointer;' title='Open Transfers LookUp' onClick=\"window.open('../../functions/transfers_lookup.php?search_selection=open_transfers','','width=500,height=500,left=250,top=100')\"/>";
				##delete button
				$new_transfers = "<input name='option_button' type='radio' id='new_transfers' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				$edit_button = "<input type='radio' id='edit_transfers'  name='option_button' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update/Delete";
				$edit_button_in = "<input type='radio' id='edit_transfers_in' name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
				break;
	case "edit_transfers_in":
				if ($trans_no==($last_trans_no + 1)) {
					$trans_no=0;
				}
				$edit_checked_in = "checked";
				$transfers_lookup = "<img src='../images/search.gif' name='img_code' align='absbottom' id='img_code' style='cursor:pointer;' title='Open Transfers LookUp' onClick=\"window.open('../../functions/transfers_lookup.php?search_selection=open_transfers','','width=500,height=500,left=250,top=100')\"/>";
				##delete button
				$new_transfers = "<input name='option_button' type='radio' id='new_transfers' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				$out_in_button = "<input name='quantity_button' type='radio' style='position: absolute; left: 1300' size='1' style='position: absolute; left: 1300' size='1' value='in' checked>";
				//$refresh_button = "<input type='radio' name='option_button' $refresh_checked value='refresh_transfers' onClick='option_button_click(this.id);'>Cancel ";
				###################### open header
				if ($trans_no >"") {	
					$out_readonly = "readonly=\"true\"";
					$in_readonly = "";
					$out2_readonly = "readonly=\"true\"";
					$in2_readonly = "readonly=\"true\"";
					##release button
					$release_button = "<input type='radio' id='release_transfers' name='option_button'$release_checked value='release_transfers' onClick='open_lookup5();'>Release";
					$edit_button = "<input type='radio' id='edit_transfers' name='option_button' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update";
					$edit_button_in = "<input type='radio' id='edit_transfers_in' name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
					$out_in_button = "<input name='quantity_button' type='radio' style='position: absolute; left: 1300' size='1' style='position: absolute; left: 1300' size='1' value='in' checked>";
					$query_open_header="SELECT * FROM tblTransferHeader WHERE trfNumber = $trans_no";
					$result_open_header=mssql_query($query_open_header);
					$num_ = mssql_num_rows($result_open_header);
					$trfNumber=mssql_result($result_open_header,0,"trfNumber");
					$from_loc_code=mssql_result($result_open_header,0,"fromLocCode");
					$to_loc_code=mssql_result($result_open_header,0,"toLocCode");
					$remarks=mssql_result($result_open_header,0,"trfRemarks");
					$status=mssql_result($result_open_header,0,"trfStatus");
					$transfers_date=mssql_result($result_open_header,0,"trfDate");
					$tag=mssql_result($result_open_header,0,"stockTag");
					$responsi=mssql_result($result_open_header,0,"trfResponsible");
					$query_responsible=mssql_query("SELECT * FROM tblUsers WHERE userid = $responsi");
					$responsible=mssql_result($query_responsible,0,"firstName") . " " . mssql_result($query_responsible,0,"lastName");
					$hash_items=mssql_result($result_open_header,0,"trfItemTot");
					$hash_quantity=mssql_result($result_open_header,0,"trfQtyTot");
					#################################
					$query_from_loc="SELECT * FROM tblLocation WHERE locCode = $from_loc_code AND compCode = $company_code";
					$result_from_loc=mssql_query($query_from_loc);
					$from_loc_name=mssql_result($result_from_loc,0,"locName");
					$from_location = $from_loc_code."-".$from_loc_name;
					#################################
					$query_to_loc="SELECT * FROM tblLocation WHERE locCode = $to_loc_code AND compCode = $company_code";
					$result_to_loc=mssql_query($query_to_loc);
					$to_loc_name=mssql_result($result_to_loc,0,"locName");
					$to_location = $to_loc_code."-".$to_loc_name;
					#################################
					if ($status == "O") {
						$transfers_status = "Open";
					} 
					if ($status == "R") {
						$transfers_status = "Released";
					} 
					if ($transfers_date=="") {
						$transfers_date = "";
					} else {
						$date = new DateTime($transfers_date);
						$transfers_date = $date->format("m/d/Y");
					}
					if ($tag == "1") {
						$stock_tag = "1 - Good";
					} 
					if ($tag == "2") {
						$stock_tag = "2 - Bad";
					} 
					$hash_items = number_format($hash_items,0);
					$hash_quantity = number_format($hash_quantity,0);
					#########################################
					$query_detail="SELECT COUNT(prdNumber) AS Expr1, SUM(trfQtyOut) AS Expr2, SUM(trfQtyIn) AS Expr3
								   FROM tblTransferDtl WHERE trfNumber = $trans_no";
					$result_detail=mssql_query($query_detail);
					$entered_items=mssql_result($result_detail,0,"Expr1");
					$entered_quantity=mssql_result($result_detail,0,"Expr2");
					$total_in=mssql_result($result_detail,0,"Expr3");
					$entered_items = number_format($entered_items,0);
					$entered_quantity = number_format($entered_quantity,0);
					$total_in = number_format($total_in,0);
					if ($entered_quantity!=$total_in) {
						$pic_qty = "../../Images/b_drop.png";
						$quantity_memo = "Totals Qty are not equal.";
					} else {
						$pic_qty = "../../Images/check.gif";
						$quantity_memo = "Totals Qty are complete.";
					}
					#########################################
					$difference_items = $hash_items - $entered_items;
					$difference_quantity = $hash_quantity - $entered_quantity;
					if ($difference_items!=0 || $difference_quantity!=0) {
						$control_totals = "Entered Totals Don't Tally With Control Totals... Please Check.";
					} else {
						$control_totals = "Data Entered are complete and accurate.";
					}
				} else {
					$edit_button = "<input type='radio' id='edit_transfers'  name='option_button' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update/Delete  ";
					$edit_button_in = "<input type='radio' id='edit_transfers_in'  name='option_button' $edit_checked_in value='edit_transfers_in' onClick='option_button_click(this.id);'>Transfer In";
					$out_in_button = "<input name='quantity_button' type='radio' style='position: absolute; left: 1300' size='1' style='position: absolute; left: 1300' size='1' value='in' checked>";
				}
				break;
	default :
				
				$transfers_status = "Open";
				$responsible = $user_first_last;
				##refresh button
				$new_transfers = "<input name='option_button' type='radio' id='new_transfers' value='new_transfers' $new_checked onClick='option_button_click(this.id);'>Create/New  ";
				//$refresh_button = "<input type='radio' name='option_button' $refresh_checked value='refresh_transfers' onClick='option_button_click(this.id);'>Menu ";
				$edit_button = "<input type='radio' name='option_button'  id='edit_transfers' $edit_checked value='edit_transfers' onClick='option_button_click(this.id);'>Update/Delete  ";
				break;
				
}
#########################################     details
		if (($option_button=="edit_transfers" || $option_button=="edit_transfers_in") && $trans_no >"") {
			$query_details="SELECT tblProdMast.prdDesc, tblProdMast.prdNumber,tblProdMast.prdFrcTag, tblTransferDtl.umCode, tblTransferDtl.trfQtyOut, tblTransferDtl.trfQtyIn, 
                      		tblTransferDtl.trfCost, tblTransferDtl.trfPrice, tblTransferDtl.trfNumber, tblUpc.upcCode, tblUpc.upcDesc
							FROM tblTransferDtl INNER JOIN
                     		tblProdMast ON tblTransferDtl.prdNumber = tblProdMast.prdNumber INNER JOIN
                      		tblUpc ON tblTransferDtl.upcCode = tblUpc.upcCode WHERE tblTransferDtl.trfNumber = $trans_no ORDER BY tblUpc.upcDesc ASC";
			$result_details=mssql_query($query_details);
			$num_details = mssql_num_rows($result_details);
		}

?>

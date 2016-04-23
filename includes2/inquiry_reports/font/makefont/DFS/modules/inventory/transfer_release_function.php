<?
	//created by: vincent c de torres
	function TransferRelease($trans_no = "", $company_code, $user_id = ""){
			$resPd=mssql_query("SELECT * FROM tblPeriod WHERE compCode = $company_code AND pdStat = 'O'");
			$numPd = mssql_num_rows($resPd);
			if ($numPd>0) {
				$monthPd = mssql_result($resPd,0,"pdCode");
				$yearPd = mssql_result($resPd,0,"pdYear");
			} else {
				echo "<script>alert('No Open Period... Please assist to your administrator.')</script>";
			}
			
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
			$monthPd = $trans_date->format("m");
			$yearPd = $trans_date->format("Y");
			
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
				$sql_inv_bal = mssql_query("SELECT * FROM tblInvBalM WHERE compCode = $company_code AND locCode = $fromLocCode AND prdNumber = $prdNumber AND pdMonth = $monthPd AND pdYear = $yearPd");
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
				$mtdCountAdjQ=0; $trQtyFree=0; $buyCost=0; $cstTypeCode=0; $refCostEvent=0;
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
				$sql_inv_bal = mssql_query("SELECT * FROM tblInvBalM WHERE compCode = $company_code AND locCode = $toLocCode AND prdNumber = $prdNumber AND pdMonth = $monthPd AND pdYear = $yearPd");
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
	}
?>
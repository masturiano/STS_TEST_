<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y H:i:s", $gmt);
	$newdate="Run Date : ".$newdate;
	$db = new DB;
	$db->connect();
		
	$hide_from_date=$_POST['hide_from_date'];
	$hide_to_date=$_POST['hide_to_date'];
	$from_to = "Report Date from ".$hide_from_date." to ".$hide_to_date;
	$hide_loc_code=$_POST['hide_loc_code'];
	$hide_prod_code=$_POST['hide_prod_code'];
	$hide_trans_type=$_POST['hide_trans_type'];
	$hide_qty_good=$_POST['hide_qty_good'];
	$hide_qty_bo=$_POST['hide_qty_bo'];
	$hide_unit_cost=$_POST['hide_unit_cost'];
	
	#################### click inquire button #################
	$new1=getCodeofString($hide_loc_code); ///pick in inventory_inquiry_function.php
	$new1=trim($new1);	
	$new2=getCodeofString($hide_prod_code); ///pick in inventory_inquiry_function.php
	$new2=trim($new2);
	$new3=getCodeofString($hide_trans_type); ///pick in inventory_inquiry_function.php
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
	$queryinventorytrans="SELECT * FROM tblInvTran WHERE (compCode = $company_code) AND (docDate >= '$hide_from_date') AND (docDate <= '$hide_to_date') $new_prod_code $new_loc_code $new_trans_type ORDER BY docDate,prdNumber,locCode,transCode ASC";
	$resulinventorytrans=mssql_query($queryinventorytrans);
	$numinventorytrans = mssql_num_rows($resulinventorytrans);
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	###################### end of click inquire button #######################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$dtl_ht=4;
	$m_width=310;
	$m_width_3_fields=103;
	$font="Courier";
	$m_line = 35;  ///maximum line
	$m_page=$numinventorytrans / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$numinventorytrans; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
	
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$numinventorytrans." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$numinventorytrans." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : INV009I",0,0);
		$pdf->Cell($m_width_3_fields,5,"INVENTORY TRANSACTIONS REGISTER",0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$entries,0,1,'R');
		$pdf->ln();
		$pdf->Cell(250, $dtl_ht, $from_to, 0, 0);
		/*
		$pdf->Cell(35,$dtl_ht, 'Current Balance ', 0, 0);
		$pdf->ln();
		$pdf->Cell(40, $dtl_ht, 'Location ', 0, 0);
		$pdf->Cell(210, $dtl_ht, $hide_loc_code, 0, 0);
		$pdf->Cell(35,$dtl_ht, ' Qty Good', 0, 0);
		$pdf->Cell(25, $dtl_ht, $hide_qty_good, 0, 0,'R');
		$pdf->ln();
		$pdf->Cell(40, $dtl_ht, 'Product ',0, 0);
		$pdf->Cell(210, $dtl_ht, $hide_prod_code,0, 0);
		$pdf->Cell(35,$dtl_ht, ' Qty BO ', 0, 0);
		$pdf->Cell(25, $dtl_ht, $hide_qty_bo, 0, 0,'R');
		$pdf->ln();
		$pdf->Cell(40,$dtl_ht, 'Transaction Type ', 0, 0);
		$pdf->Cell(210, $dtl_ht, $hide_trans_type, 0, 0);
		$pdf->Cell(35,$dtl_ht, ' Unit Cost ', 0, 0);
		$pdf->Cell(25, $dtl_ht, $hide_unit_cost, 0, 0,'R');
		*/
		$pdf->ln();
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$pdf->Cell(25,$dtl_ht, 'DATE', 0, 0);
		$pdf->Cell(20,$dtl_ht, 'DOC NO', 0, 0);
		$pdf->Cell(20,$dtl_ht, 'REF NO', 0, 0);
		$pdf->Cell(30,$dtl_ht, 'LOCATION', 0, 0);
		$pdf->Cell(20,$dtl_ht, 'TYPE', 0, 0);
		$pdf->Cell(60,$dtl_ht, 'BUSINESS PARTNER', 0, 0);
		$pdf->Cell(25,$dtl_ht, 'PRICE/COST', 0, 0,'R');
		$pdf->Cell(20,$dtl_ht, 'REG', 0, 0,'R');
		$pdf->Cell(20,$dtl_ht, 'FREE', 0, 0,'R');
		$pdf->Cell(20,$dtl_ht, 'BO', 0, 0,'R');
		$pdf->Cell(25,$dtl_ht, 'EXT AMT', 0, 0,'R');
		$pdf->Cell(25,$dtl_ht, 'TOT DISC', 0, 0,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$temp_sku="";
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			for ($i=$tmp_first;$i <= $tmp_last;$i++){
				$i--;
							$gridbusinesspartner="NA";							
							$griddocdate=mssql_result($resulinventorytrans,$i,"docDate");
							if ($griddocdate>"") {
								$date = new DateTime($griddocdate);
								$griddocdate = $date->format("m/d/Y");		
							} else {
								$griddocdate="";
							}
							$sku=mssql_result($resulinventorytrans,$i,"prdNumber");
							$rst_sku=mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $sku");
							$grid_product=mssql_result($rst_sku,0,"prdDesc");
							$griddocnumber=mssql_result($resulinventorytrans,$i,"docNumber");
							$grid_loc_code=mssql_result($resulinventorytrans,$i,"locCode");
							$gridtranscode=mssql_result($resulinventorytrans,$i,"transCode");
							$gridtrQtyGood=mssql_result($resulinventorytrans,$i,"trQtyGood");
							$gridtrQtyFree=mssql_result($resulinventorytrans,$i,"trQtyFree");
							$gridtrQtyBo=mssql_result($resulinventorytrans,$i,"trQtyBo");
							$gridextAmt=mssql_result($resulinventorytrans,$i,"extAmt");
							$gridtrQtyGood = number_format($gridtrQtyGood, 2);   
							$gridtrQtyFree = number_format($gridtrQtyFree, 2);  
							$gridtrQtyBo = number_format($gridtrQtyBo, 2);  
							$gridextAmt = number_format($gridextAmt, 2);  
							$griditemDiscCogY=mssql_result($resulinventorytrans,$i,"itemDiscCogY");
							$griditemDiscCogN=mssql_result($resulinventorytrans,$i,"itemDiscCogN");
							$gridpoLevelDiscCogY=mssql_result($resulinventorytrans,$i,"poLevelDiscCogY");
							$gridpoLevelDiscCogN=mssql_result($resulinventorytrans,$i,"poLevelDiscCogN");
							$gridcustCode=mssql_result($resulinventorytrans,$i,"custCode");
							$gridsuppCode=mssql_result($resulinventorytrans,$i,"suppCode");
							$grid_ref_no=mssql_result($resulinventorytrans,$i,"refNo");
							$grid=mssql_result($resulinventorytrans,$i,"suppCode");
							$grid_prod_number=mssql_result($resulinventorytrans,$i,"prdNumber");
							$grid_ave_cost=mssql_result($resulinventorytrans,$i,"aveCost");
							$grid_unit_price=mssql_result($resulinventorytrans,$i,"unitPrice");
							///// get total discount amount = the sum of griditemDiscCogY + griditemDiscCogN + gridpoLevelDiscCogY + gridpoLevelDiscCogN
							$gridtotaldiscountamount=$griditemDiscCogY+$griditemDiscCogN+$gridpoLevelDiscCogY+$gridpoLevelDiscCogN;
							$gridtotaldiscountamount = number_format($gridtotaldiscountamount, 2);
							///// get locName from table tblLocation....
							$query_location="SELECT * FROM tblLocation WHERE locCode = $grid_loc_code";
							$result_location=mssql_query($query_location);
							$num_location = mssql_num_rows($result_location);
							if ($num_location >0) {
								$grid_location=$grid_loc_code."-".mssql_result($result_location,0,"locName");
							} else {
								$grid_location="NA";
							}
							
							///// get tranTypeInit from table tblTransactionType.... use transCode of extract file
							$querytranTypeInit="SELECT * FROM tblTransactionType WHERE trnTypeCode = $gridtranscode";
							$resulttranTypeInit=mssql_query($querytranTypeInit);
							$num_tran_type_init = mssql_num_rows($resulttranTypeInit);
							if ($num_tran_type_init >0) {
								$gridtranTypeInit=$gridtranscode."-" .mssql_result($resulttranTypeInit,0,"trnTypeInit");
							} else {
								$gridtranTypeInit="NA";
							}
							
							///// get unit cost or unit price from table tblProdCost and tblProdPrice.... 
							if ((trim($gridtranscode)==21) || (trim($gridtranscode)==51)) {
								$grid_price_cost = number_format($grid_unit_price, 2);  
							} else {
								$grid_price_cost = number_format($grid_ave_cost, 2);  
							}
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
							if ($sku != $temp_sku) {
								$pdf->ln();
								$pdf->SetFont($font, 'B', '10');
								$pdf->Cell(25,$dtl_ht, $sku." - ".$grid_product, 0, 0);
								$pdf->SetFont($font, '', '10');
							}
							$pdf->ln();
							$pdf->Cell(25,$dtl_ht, $griddocdate, 0, 0);
							$pdf->Cell(20,$dtl_ht, $griddocnumber, 0, 0);
							$pdf->Cell(20,$dtl_ht, $grid_ref_no, 0, 0);
							$pdf->Cell(30,$dtl_ht, $grid_location, 0, 0);
							$pdf->Cell(20,$dtl_ht, $gridtranTypeInit, 0, 0);
							$pdf->Cell(60,$dtl_ht, $gridbusinesspartner, 0, 0);
							$pdf->Cell(25,$dtl_ht, $grid_price_cost, 0, 0,'R');
							$pdf->Cell(20,$dtl_ht, $gridtrQtyGood, 0, 0,'R');
							$pdf->Cell(20,$dtl_ht, $gridtrQtyFree, 0, 0,'R');
							$pdf->Cell(20,$dtl_ht, $gridtrQtyBo, 0, 0,'R');
							$pdf->Cell(25,$dtl_ht, $gridextAmt, 0, 0,'R');
							$pdf->Cell(25,$dtl_ht, $gridtotaldiscountamount, 0, 0,'R');
						
							
							$temp_sku=$sku;	
				$i++;
			} 
			/*
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0, 0,'C');
			$pdf->ln();
			$printed_by = "Prepared By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);	
			*/
		} else {            /// more than 1 page consume
			$tmp_last_more=$j*$m_line;
			for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
				$i--;
							$gridbusinesspartner="NA";							
							$griddocdate=mssql_result($resulinventorytrans,$i,"docDate");
							if ($griddocdate>"") {
								$date = new DateTime($griddocdate);
								$griddocdate = $date->format("m/d/Y");		
							} else {
								$griddocdate="";
							}
							$sku=mssql_result($resulinventorytrans,$i,"prdNumber");
							$rst_sku=mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $sku");
							$grid_product=mssql_result($rst_sku,0,"prdDesc");
							$griddocnumber=mssql_result($resulinventorytrans,$i,"docNumber");
							$grid_loc_code=mssql_result($resulinventorytrans,$i,"locCode");
							$gridtranscode=mssql_result($resulinventorytrans,$i,"transCode");
							$gridtrQtyGood=mssql_result($resulinventorytrans,$i,"trQtyGood");
							$gridtrQtyFree=mssql_result($resulinventorytrans,$i,"trQtyFree");
							$gridtrQtyBo=mssql_result($resulinventorytrans,$i,"trQtyBo");
							$gridextAmt=mssql_result($resulinventorytrans,$i,"extAmt");
							$gridtrQtyGood = number_format($gridtrQtyGood, 2);   
							$gridtrQtyFree = number_format($gridtrQtyFree, 2);  
							$gridtrQtyBo = number_format($gridtrQtyBo, 2);  
							$gridextAmt = number_format($gridextAmt, 2);  
							$griditemDiscCogY=mssql_result($resulinventorytrans,$i,"itemDiscCogY");
							$griditemDiscCogN=mssql_result($resulinventorytrans,$i,"itemDiscCogN");
							$gridpoLevelDiscCogY=mssql_result($resulinventorytrans,$i,"poLevelDiscCogY");
							$gridpoLevelDiscCogN=mssql_result($resulinventorytrans,$i,"poLevelDiscCogN");
							$gridcustCode=mssql_result($resulinventorytrans,$i,"custCode");
							$gridsuppCode=mssql_result($resulinventorytrans,$i,"suppCode");
							$grid_ref_no=mssql_result($resulinventorytrans,$i,"refNo");
							$grid=mssql_result($resulinventorytrans,$i,"suppCode");
							$grid_prod_number=mssql_result($resulinventorytrans,$i,"prdNumber");
							$grid_ave_cost=mssql_result($resulinventorytrans,$i,"aveCost");
							$grid_unit_price=mssql_result($resulinventorytrans,$i,"unitPrice");
							///// get total discount amount = the sum of griditemDiscCogY + griditemDiscCogN + gridpoLevelDiscCogY + gridpoLevelDiscCogN
							$gridtotaldiscountamount=$griditemDiscCogY+$griditemDiscCogN+$gridpoLevelDiscCogY+$gridpoLevelDiscCogN;
							$gridtotaldiscountamount = number_format($gridtotaldiscountamount, 2);
							///// get locName from table tblLocation....
							$query_location="SELECT * FROM tblLocation WHERE locCode = $grid_loc_code";
							$result_location=mssql_query($query_location);
							$num_location = mssql_num_rows($result_location);
							if ($num_location >0) {
								$grid_location=$grid_loc_code."-".mssql_result($result_location,0,"locName");
							} else {
								$grid_location="NA";
							}
							
							///// get tranTypeInit from table tblTransactionType.... use transCode of extract file
							$querytranTypeInit="SELECT * FROM tblTransactionType WHERE trnTypeCode = $gridtranscode";
							$resulttranTypeInit=mssql_query($querytranTypeInit);
							$num_tran_type_init = mssql_num_rows($resulttranTypeInit);
							if ($num_tran_type_init >0) {
								$gridtranTypeInit=$gridtranscode."-" .mssql_result($resulttranTypeInit,0,"trnTypeInit");
							} else {
								$gridtranTypeInit="NA";
							}
							
							///// get unit cost or unit price from table tblProdCost and tblProdPrice.... 
							if ((trim($gridtranscode)==21) || (trim($gridtranscode)==51)) {
								$grid_price_cost = number_format($grid_unit_price, 2);  
							} else {
								$grid_price_cost = number_format($grid_ave_cost, 2);  
							}
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
							if ($sku != $temp_sku) {
								$pdf->ln();
								$pdf->SetFont($font, 'B', '10');
								$pdf->Cell(25,$dtl_ht, $sku." - ".$grid_product, 0, 0);
								$pdf->SetFont($font, '', '10');
							}
							$pdf->ln();
							$pdf->Cell(25,$dtl_ht, $griddocdate, 0, 0);
							$pdf->Cell(20,$dtl_ht, $griddocnumber, 0, 0);
							$pdf->Cell(20,$dtl_ht, $grid_ref_no, 0, 0);
							$pdf->Cell(30,$dtl_ht, $grid_location, 0, 0);
							$pdf->Cell(20,$dtl_ht, $gridtranTypeInit, 0, 0);
							$pdf->Cell(60,$dtl_ht, $gridbusinesspartner, 0, 0);
							$pdf->Cell(25,$dtl_ht, $grid_price_cost, 0, 0,'R');
							$pdf->Cell(20,$dtl_ht, $gridtrQtyGood, 0, 0,'R');
							$pdf->Cell(20,$dtl_ht, $gridtrQtyFree, 0, 0,'R');
							$pdf->Cell(20,$dtl_ht, $gridtrQtyBo, 0, 0,'R');
							$pdf->Cell(25,$dtl_ht, $gridextAmt, 0, 0,'R');
							$pdf->Cell(25,$dtl_ht, $gridtotaldiscountamount, 0, 0,'R');
						
							
							$temp_sku=$sku;
				$i++;
			}
		}
		###################### P A G E  F O O T E R ##########################
		if ($m_page > 1) {
			//$pdf->ln();
			//$pdf->Cell(30,$dtl_ht, $total_line, 0, 0,'R');
			//$pdf->ln();
		}
		
		###################### R E P O R T  F O O T E R #########################
		if ($tmp_rec <= 0) { /// 1 page consume
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell(30,$dtl_ht, "Total Number of Items : ".$numinventorytrans, 0, 0);
			$pdf->ln();
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0,1,'C');
			$pdf->ln();
			$printed_by = "Prepared By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}

	$pdf->Output();
?>
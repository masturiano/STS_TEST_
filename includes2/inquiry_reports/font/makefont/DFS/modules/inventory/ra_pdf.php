<?php
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$newdate="Run Date : ".$newdate;
	include "../../functions/inquiry_session.php";
	require('../inventory/lbd_function.php');
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";	
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$db = new DB;
	$db->connect();
	$ra_number=$_GET['ra_number'];
	$po_number=$_GET['po_number'];
	if ($po_number>"") {
		$title = "Receiver Authorization Listing";
	} else {
		$title = "Receiver Authorization Listing (Re-print)";
		$query_ra="SELECT * FROM tblRaHeader WHERE (raNumber = $ra_number)";
		$result_ra=mssql_query($query_ra);
		$num_ra = mssql_num_rows($result_ra);
		if ($num_ra >0){
			$po_number=mssql_result($result_ra,0,"poNumber");
		} 
	}
	
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	
	$strCtr = "SELECT TOP 100 PERCENT tblRaHeader.raNumber, tblPoHeader.poNumber , tblPoHeader.poTerms, CONVERT(varchar, tblRaHeader.raDate, 101) AS raDate, 
              CONVERT(varchar, tblPoHeader.suppCode) + ' - ' + UPPER(tblSuppliers.suppName) AS suppName, CONVERT(varchar, 
              tblPoHeader.poBuyer) + ' - ' + UPPER(tblBuyers.buyerName) AS buyerName, CONVERT(varchar, tblPoHeader.poDate, 101) AS poDate, tblRaHeader.compCode, tblRaHeader.raRemarks
			  FROM tblRaHeader INNER JOIN
              tblPoHeader ON tblRaHeader.poNumber = tblPoHeader.poNumber INNER JOIN
              tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
              tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
			  WHERE tblRaHeader.raNumber = $ra_number AND tblRaHeader.compCode = $company_code AND tblPoHeader.poNumber = '$po_number' ORDER BY tblRaHeader.raNumber, tblPoHeader.poNumber";
			  
	$query_event_date = mssql_query($strCtr);
	$num_event_date = mssql_num_rows($query_event_date);
	
	if ($num_event_date>0) {
		$poNumber=mssql_result($query_event_date,0,"poNumber");
		$poTerms=mssql_result($query_event_date,0,"poTerms");
		$result_terms = mssql_query("SELECT * FROM tblTerms WHERE trmCode = $poTerms");
		$num_terms = mssql_num_rows($result_terms);
		if ($num_terms>0) {
			$trmDesc=$poTerms." - ".mssql_result($result_terms,0,"trmDesc");
		} else {
			$trmDesc="NA";
		}
		$raDate=mssql_result($query_event_date,0,"raDate");
		$suppName=mssql_result($query_event_date,0,"suppName");
		$buyerName=mssql_result($query_event_date,0,"buyerName");
		$poDate=mssql_result($query_event_date,0,"poDate");
		$remarks=mssql_result($query_event_date,0,"raRemarks");
	}
	
	$strSQL = "SELECT TOP 100 PERCENT tblRaItemDtl.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, tblRaItemDtl.raOrderedQty, 
               tblRaItemDtl.prdConv, tblRaItemDtl.raOrderedQty / tblRaItemDtl.prdConv AS orderedQty, UPPER(tblProdMast.prdSellUnit) 
               AS prdSellUnit, UPPER(tblProdMast.prdBuyUnit) AS prdBuyUnit, tblRaItemDtl.poNumber, tblRaItemDtl.compCode, tblRaItemDtl.raNumber
			   FROM tblRaItemDtl INNER JOIN
               tblProdMast ON tblRaItemDtl.prdNumber = tblProdMast.prdNumber
			   WHERE tblRaItemDtl.poNumber  = '$po_number' AND tblRaItemDtl.compCode = $company_code AND tblRaItemDtl.raNumber = $ra_number
			   GROUP BY tblRaItemDtl.prdNumber, UPPER(tblProdMast.prdDesc), tblRaItemDtl.raOrderedQty, tblRaItemDtl.prdConv, 
               tblRaItemDtl.raOrderedQty / tblRaItemDtl.prdConv, UPPER(tblProdMast.prdSellUnit), UPPER(tblProdMast.prdBuyUnit), 
               tblRaItemDtl.poNumber , tblRaItemDtl.compCode, tblRaItemDtl.raNumber
			   ORDER BY tblRaItemDtl.poNumber, tblRaItemDtl.prdNumber";
			   
	$result_loc = mssql_query($strSQL);
	$num_loc = mssql_num_rows($result_loc);
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$m_line=25;
	$dtl_ht=5.2;
	$max_tot_line=30;
	$m_width=255;
	$m_width_3_fields=85;
	$m_width_2_fields=127;
	$font="Courier";
	$m_page=$num_loc / $m_line;
	$aaa = split("\.",$m_page);
	$aaaa = ".".$aaa[1];
	$last_excempt = $aaaa * 25;
	$m_page=ceil($m_page); //// maximum page	
	$flag=0; 
	if ($last_excempt>24 || $last_excempt==0){
		$m_page++;
		$flag=1;
	}
	//$m_page=3;
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_loc; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_loc." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_loc." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,"RA No. : " . $ra_number,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : RCR001P",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		if ($j==1) {
			$pdf->ln();
			$pdf->Cell($m_width_2_fields,5,"Location : _______ - ________________",0,0);
			$pdf->Cell($m_width_2_fields,5,"Carrier : ___________________________",0,1);
			$pdf->Cell($m_width_2_fields,5,"Vendor : ".$suppName,0,0);
			$pdf->Cell($m_width_2_fields,5,"Container : _________________________",0,1);
			$pdf->Cell($m_width_2_fields,5,"Ref. PO# and PO Date : ".$poNumber." - ".$poDate,0,0);
			$pdf->Cell($m_width_2_fields,5,"Buyer : ".$buyerName,0,1);
			$pdf->Cell($m_width_2_fields,5,"Terms : ".$trmDesc,0,1);
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
				$pdf->Cell(15,4, '', 0, 0);
				$pdf->Cell(30,4, '', 0, 0);
				$pdf->Cell(90,4, '', 0, 0);
				$pdf->Cell(30,4, 'Order', 0, 0,'R');
				$pdf->Cell(15,4, 'Sell/Buy', 0, 0);
				$pdf->Cell(35,4, 'Qty', 0, 0,'R');
				$pdf->Cell(36,4, '--Qty Received--', 0, 0,'C');
				$pdf->ln();
				$pdf->Cell(15,4, 'SKU', 0, 0);
				$pdf->Cell(30,4, 'UPC', 0, 0);
				$pdf->Cell(90,4, 'Description', 0, 0);
				$pdf->Cell(30,4, 'Qty', 0, 0,'R');
				$pdf->Cell(15,4, 'UM', 0, 0);
				$pdf->Cell(35,4, 'Expected', 0, 0,'R');
				$pdf->Cell(12,4, 'Good', 0, 0);
				$pdf->Cell(12,4, 'BO', 0, 0);
				$pdf->Cell(12,4, 'Free', 0, 0);
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$grand_total_qty_exp = 0;
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=10; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					$pageTotal = 0;
					$total_qty_exp = 0;
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;						
											$prd_number=mssql_result($result_loc,$i,0);
											$qryUpc = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $prd_number");
											$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
											
											$ProdDesc=mssql_result($result_loc,$i,1);
											$ProdDesc = str_replace("\\","",$ProdDesc);
											$qty_order = number_format(mssql_result($result_loc,$i,4),2);
											$conv=number_format(mssql_result($result_loc,$i,3),0);
											$buy_sell_conv = mssql_result($result_loc,$i,6)."/".mssql_result($result_loc,$i,5)."/".$conv;
											$total  = number_format(mssql_result($result_loc,$i,2),2);
											$potot = $potot + mssql_result($result_loc,$i,2);
											$total_qty_exp = $total_qty_exp + $total;
											$grand_total_qty_exp = $grand_total_qty_exp + $total;
											$pageTotal ++;	
											$pdf->Cell(15,$dtl_ht, $prd_number, 0, 0);
											$pdf->Cell(30,$dtl_ht, $upcCode, 0, 0);
											$pdf->Cell(90,$dtl_ht, $ProdDesc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $qty_order, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $buy_sell_conv, 0, 0);
											$pdf->Cell(35,$dtl_ht, $total, 0, 0,'R');
											$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
											$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
											$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
											$pdf->ln();
						$i++;
					} 
					break;
				} 
				$m_line = $m_line-1;
			}	
		} else {            /// more than 1 page consume
			for($g=1; $g<=10; $g++) {
				$temp_supp_code="";
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
											$prd_number=mssql_result($result_loc,$i,0);
											$qryUpc = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $prd_number");
											$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
											
											$ProdDesc=mssql_result($result_loc,$i,1);
											$ProdDesc = str_replace("\\","",$ProdDesc);
											$qty_order = number_format(mssql_result($result_loc,$i,4),2);
											$conv=number_format(mssql_result($result_loc,$i,3),0);
											$buy_sell_conv = mssql_result($result_loc,$i,6)."/".mssql_result($result_loc,$i,5)."/".$conv;
											$total  = number_format(mssql_result($result_loc,$i,2),2);
											$potot = $potot + mssql_result($result_loc,$i,2);
											$total_qty_exp = $total_qty_exp + $total;
											$grand_total_qty_exp = $grand_total_qty_exp + $total;
											$pageTotal ++;	
											$pdf->Cell(15,$dtl_ht, $prd_number, 0, 0);
											$pdf->Cell(30,$dtl_ht, $upcCode, 0, 0);
											$pdf->Cell(90,$dtl_ht, $ProdDesc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $qty_order, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $buy_sell_conv, 0, 0);
											$pdf->Cell(35,$dtl_ht, $total, 0, 0,'R');
											$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
											$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
											$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
											$pdf->ln();
						$i++;
					}
					break;
				} 
				$m_line=$m_line - 1;
			} 
		}
		###################### P A G E  F O O T E R ##########################
		if ($m_page > 1 && $flag != 2) {
			if ($j >= $m_page) {
				$pdf->Cell(23,$dtl_ht, "" , 0, 0);	
			}
			$pdf->ln();
			$total_qty_exp = number_format($total_qty_good,2);
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, "Page Total : ".$pageTotal, 0, 0);
			$pdf->Cell(90,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(35,$dtl_ht, $total_qty_exp, 0, 0,'R');
			$pdf->Cell(12,$dtl_ht, "_____", 0, 0,'R');
			$pdf->Cell(12,$dtl_ht, "_____", 0, 0,'R');
			$pdf->Cell(12,$dtl_ht, "_____", 0, 0,'R');
			$pdf->ln();
		}
		
		###################### R E P O R T  F O O T E R #########################
		if ($tmp_rec <= 0 && $j >= $m_page) { /// 1 page consume $j >=$m_page &&  $last_excempt < 8
			$pdf->ln();
			if ($flag==1) {
				$pdf->Cell($m_width,$dtl_ht, '', 0,1,'C');
			}
			$grand_total_qty_exp = number_format($grand_total_qty_exp,2);
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, "Total Number of Items : ".$num_loc, 0, 0);
			$pdf->Cell(90,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(35,$dtl_ht, $grand_total_qty_exp, 0, 0,'R');
			$pdf->Cell(12,$dtl_ht, "_____", 0, 0,'R');
			$pdf->Cell(12,$dtl_ht, "_____", 0, 0,'R');
			$pdf->Cell(12,$dtl_ht, "_____", 0, 0,'R');
			$pdf->ln();
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0,1,'C');
			$pdf->ln();
			$pdf->Cell(1,$dtl_ht, "Remarks : ".$remarks, 0, 1);
			$pdf->Cell(1,$dtl_ht, "Received By : _____________________", 0, 1);
			$pdf->Cell(1,$dtl_ht, "Date Received : ___________________", 0, 1);
			$pdf->ln();
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	
	$pdf->AddPage();
	$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
	$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
	$pdf->Cell($m_width_3_fields,5,"RA No. : " . $ra_number,0,1,'R');
	$pdf->Cell($m_width_3_fields,5,"Report ID : RCR001P",0,0);
	$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
	$pdf->Cell($m_width_3_fields,5,"",0,1,'R');
	$pdf->ln();
	$pdf->Cell($m_width, 0, '', 1, 0);
	$pdf->ln();
	$pdf->Cell(15,4, '', 0, 0);
	$pdf->Cell(30,4, '', 0, 0);
	$pdf->Cell(90,4, '', 0, 0);
	$pdf->Cell(30,4, 'Order', 0, 0,'R');
	$pdf->Cell(15,4, 'Sell/Buy', 0, 0);
	$pdf->Cell(35,4, 'Qty', 0, 0,'R');
	$pdf->Cell(36,4, '--Qty Received--', 0, 0,'C');
	$pdf->ln();
	$pdf->Cell(15,4, 'SKU', 0, 0);
	$pdf->Cell(30,4, 'UPC', 0, 0);
	$pdf->Cell(90,4, 'Description', 0, 0);
	$pdf->Cell(30,4, 'Qty', 0, 0,'R');
	$pdf->Cell(15,4, 'UM', 0, 0);
	$pdf->Cell(35,4, 'Expected', 0, 0,'R');
	$pdf->Cell(12,4, 'Good', 0, 0);
	$pdf->Cell(12,4, 'BO', 0, 0);
	$pdf->Cell(12,4, 'Free', 0, 0);
	$pdf->ln();
	$pdf->Cell($m_width, 0, '', 1, 0);
	$pdf->ln();
	$pdf->Cell(15,$dtl_ht, "", 0, 1);
	$pdf->Cell(15,$dtl_ht, "For Additional Items (Not in PO)", 0, 0);
	for ($i=1; $i<=20; $i++) {
		$pdf->Cell(15,$dtl_ht, "", 0, 1);
		$pdf->Cell(15,$dtl_ht, "__________", 0, 0);
		$pdf->Cell(30,$dtl_ht, "____________", 0, 0);
		$pdf->Cell(90,$dtl_ht, "____________________________________________", 0, 0);
		$pdf->Cell(30,$dtl_ht, "__________", 0, 0,'R');
		$pdf->Cell(15,$dtl_ht, "__________", 0, 0);
		$pdf->Cell(35,$dtl_ht, "__________", 0, 0,'R');
		$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
		$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
		$pdf->Cell(12,$dtl_ht, "_____", 0, 0);
	}
	$pdf->Output();
?>
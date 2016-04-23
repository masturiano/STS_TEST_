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
	$rcrnum = $_GET['rcrno'];
	$level = $_GET['level'];
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	if ($level=="reprint") {
		$title = "RECEIVER CONFIRMATION REPORT (RE-PRINT COPY)";
	} else {
		$title = "RECEIVER CONFIRMATION REPORT";
	}
	$strCtr = "SELECT tblRcrItemDtl.rcrNumber, CONVERT(varchar, tblRcrHeader.rcrDate, 101) AS rcrDate, tblRcrHeader.suppCode, tblSuppliers.suppName, 
               tblRcrHeader.poNumber, AVG(ISNULL(tblRcrItemDtl.rcrExtAmt, 0)) AS rcrExtAmt, tblRcrItemDtl.prdNumber, 
               UPPER(tblProdMast.prdDesc) AS prdDesc, tblRcrHeader.rcrLocation, tblLocation.locName, tblRcrHeader.raNumber, CONVERT(varchar,
               tblRcrHeader.raDate, 101) AS raDate, tblRcrHeader.carrier, tblRcrHeader.containerNo, tblRcrHeader.poBuyer, 
               ISNULL(tblBuyers.buyerName, 'UNKNOWN BUYER') AS buyerName, tblRcrHeader.poTerms, tblRcrItemDtl.rcrQtyGood, 
               tblRcrItemDtl.rcrQtyFree, tblRcrItemDtl.rcrQtyBad, tblRcrItemDtl.orderedQty, tblRcrItemDtl.poUnitCost, 
               UPPER(tblRcrItemDtl.umCode) AS umCode, tblRcrItemDtl.prdConv, CONVERT(varchar, tblRcrHeader.poDate, 101) AS poDate, 
			   tblRcrHeader.compCode, tblRcrHeader.rcrDiscAmtTotal, tblRcrHeader.rcrAllwAmtTotal, tblRcrHeader.rcrAddChargesTotal, 
			   tblRcrHeader.rcrRemarks,tblRcrHeader.rcrType,tblRcrHeader.suppCurr,tblRcrHeader.currUsdRate
			   FROM tblRcrHeader INNER JOIN
               tblSuppliers ON tblRcrHeader.suppCode = tblSuppliers.suppCode INNER JOIN
               tblRcrAudit ON tblRcrHeader.rcrNumber = tblRcrAudit.rcrNumber INNER JOIN
               tblLocation ON tblRcrHeader.rcrLocation = tblLocation.locCode INNER JOIN
               tblRcrItemDtl ON tblRcrHeader.rcrNumber = tblRcrItemDtl.rcrNumber INNER JOIN
               tblProdMast ON tblRcrItemDtl.prdNumber = tblProdMast.prdNumber LEFT OUTER JOIN
               tblBuyers ON tblRcrHeader.poBuyer = tblBuyers.buyerCode
			   WHERE ((tblRcrAudit.rcrCancelDate IS NULL) OR
               (tblRcrItemDtl.rcrQtyGood <> 0) AND (tblRcrItemDtl.rcrQtyFree <> 0) AND (tblRcrItemDtl.rcrQtyBad <> 0)) AND tblRcrItemDtl.rcrNumber = '$rcrnum' AND tblRcrHeader.compCode = $company_code
			   GROUP BY tblRcrItemDtl.rcrNumber, tblRcrHeader.rcrDate, tblRcrHeader.suppCode, tblSuppliers.suppName, tblRcrHeader.poNumber, 
               tblRcrItemDtl.prdNumber, tblProdMast.prdDesc, tblRcrHeader.rcrLocation, tblLocation.locName, tblRcrHeader.raNumber, 
               tblRcrHeader.raDate, tblRcrHeader.carrier, tblRcrHeader.containerNo, tblRcrHeader.poBuyer, tblBuyers.buyerName, 
               tblRcrHeader.poTerms, tblRcrItemDtl.rcrQtyGood, tblRcrItemDtl.rcrQtyFree, tblRcrItemDtl.rcrQtyBad, tblRcrItemDtl.orderedQty, 
               tblRcrItemDtl.poUnitCost, tblRcrItemDtl.prdConv, tblRcrItemDtl.umCode, tblRcrHeader.poDate, tblRcrHeader.compCode, 
			   tblRcrHeader.rcrDiscAmtTotal, tblRcrHeader.rcrAllwAmtTotal, tblRcrHeader.rcrAddChargesTotal, tblRcrHeader.rcrRemarks,
			   tblRcrHeader.rcrType,tblRcrHeader.suppCurr,tblRcrHeader.currUsdRate";
			  
	$query_event_date = mssql_query($strCtr);
	$num_event_date = mssql_num_rows($query_event_date);
	
	if ($num_event_date>0) {
		$poTerms=mssql_result($query_event_date,0,"poTerms");
		$result_terms = mssql_query("SELECT * FROM tblTerms WHERE trmCode = $poTerms");
		$num_terms = mssql_num_rows($result_terms);
		if ($num_terms>0) {
			$trmDesc=$poTerms." - ".mssql_result($result_terms,0,"trmDesc");
		} else {
			$trmDesc="NA";
		}
		
		$Location = mssql_result($query_event_date,0,8) . " - " . strtoupper(mssql_result($query_event_date,0,9));
		$Vendor = mssql_result($query_event_date,0,2) . " - " . strtoupper(mssql_result($query_event_date,0,3));
		$Carrier = strtoupper(mssql_result($query_event_date,0,12));
		$Container = strtoupper(mssql_result($query_event_date,0,13));
		$Terms = mssql_result($query_event_date,0,16) . " DAYS";
		$PO = mssql_result($query_event_date,0,4) . " - " . mssql_result($query_event_date,0,24); 
		$RA = mssql_result($query_event_date,0,10) . " - " . mssql_result($query_event_date,0,11);
		$Buyer = mssql_result($query_event_date,0,14) . " - " . strtoupper(mssql_result($query_event_date,0,15));
		$sku_discount = mssql_result($query_event_date,0,"rcrDiscAmtTotal");
		$allowances = mssql_result($query_event_date,0,"rcrAllwAmtTotal");
		$charges = mssql_result($query_event_date,0,"rcrAddChargesTotal");
		$remarks = mssql_result($query_event_date,0,"rcrRemarks");
		$Type = mssql_result($query_event_date,0,"rcrType");
		$Currency = mssql_result($query_event_date,0,"suppCurr");
		$Currency_Rate = mssql_result($query_event_date,0,"currUsdRate");
		if ($Type==1) {
			$Type="PO/RA Receipt";
		}
		if ($Type==2) {
			$Type="Direct Store Deliveries";
		}
		if ($Type==3) {
			$Type="Additional PO Items";
		}
	}
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$m_line=20;
	$dtl_ht=5.2;
	$max_tot_line=30;
	$m_width=310;
	$m_width_3_fields=103;
	$m_width_2_fields=155;
	$font="Courier";
	$m_page=$num_event_date / $m_line;
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
	$tmp_rec=$num_event_date; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_event_date." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_event_date." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,"RCR No. : " . $rcrnum,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : RCR004P",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		if ($j==1) {
			$pdf->ln();
			$pdf->Cell($m_width_3_fields,5,"Location : ".$Location,0,0);
			$pdf->Cell($m_width_3_fields,5,"Carrier : ".$Carrier,0,0);
			$pdf->Cell($m_width_3_fields,5,"Type : ".$Type,0,1);
			$pdf->Cell($m_width_3_fields,5,"Vendor : ".$Vendor,0,0);
			$pdf->Cell($m_width_3_fields,5,"Container : ".$Container,0,0);
			$pdf->Cell($m_width_3_fields,5,"Vendor Currency : ".$Currency,0,1);
			$pdf->Cell($m_width_3_fields,5,"Ref. PO# / PO Date : ".$PO,0,0);
			$pdf->Cell($m_width_3_fields,5,"Buyer : ".$Buyer,0,0);
			$pdf->Cell($m_width_3_fields,5,"Currency Rate to USD: ".number_format($Currency_Rate,2),0,1);
			$pdf->Cell($m_width_3_fields,5,"Ref. RA# / RA Date : ".$RA,0,0);
			$pdf->Cell($m_width_3_fields,5,"Terms : ".$trmDesc,0,1);
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
				$pdf->Cell(15,4, '', 0, 0);
				$pdf->Cell(30,4, '', 0, 0);
				$pdf->Cell(90,4, '', 0, 0);
				$pdf->Cell(25,4, 'Qty(pcs)', 0, 0,'R');
				$pdf->Cell(15,4, 'Buy', 0, 0);
				$pdf->Cell(30,4, 'Buy', 0, 0,'R');
				$pdf->Cell(25,4, 'Unit', 0, 0,'R');
				$pdf->Cell(50,4, '---Qty Received---', 0, 0,'R');
				$pdf->Cell(30,4, '', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell(15,4, 'SKU', 0, 0);
				$pdf->Cell(30,4, 'UPC', 0, 0);
				$pdf->Cell(90,4, 'Description', 0, 0);
				$pdf->Cell(25,4, 'Ordered', 0, 0,'R');
				$pdf->Cell(15,4, 'UM', 0, 0);
				$pdf->Cell(30,4, 'Cost', 0, 0,'R');
				$pdf->Cell(25,4, 'BuyCost', 0, 0,'R');
				$pdf->Cell(20,4, 'Good', 0, 0,'R');
				$pdf->Cell(15,4, 'BO', 0, 0,'R');
				$pdf->Cell(15,4, 'Free', 0, 0,'R');
				$pdf->Cell(30,4, 'Amount(USD)', 0, 0,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$grand_total_qty_good = 0;
		$grand_total_qty_bad = 0;
		$grand_total_qty_free = 0;
		$grand_total_ext_amount = 0;
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=10; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					$total_qty_good= 0;
					$total_qty_bad = 0;
					$total_qty_free = 0;
					$total_ext_amount = 0;
					$pageTotal = 0;
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;						
											$prd_number=mssql_result($query_event_date,$i,6);
											$qryUpc = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $prd_number");
											$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
		
											$ProdDesc=mssql_result($query_event_date,$i,7);
											$ProdDesc = str_replace("\\","",$ProdDesc);
											$qty_order = number_format(mssql_result($query_event_date,$i,20),2);
											$unit_cost = number_format(mssql_result($query_event_date,$i,21),4);
											$conv=number_format(mssql_result($query_event_date,$i,23),0);
											$buy_sell_conv = mssql_result($query_event_date,$i,22)."/".$conv;
											$qty_good = number_format(mssql_result($query_event_date,$i,17),0);
											$qty_bad = number_format(mssql_result($query_event_date,$i,19),0);
											$qty_free = number_format(mssql_result($query_event_date,$i,18),0);
											$ext_amount = number_format(mssql_result($query_event_date,$i,5),4);
											$total_qty_good= $total_qty_good + $qty_good;
											$total_qty_bad = $total_qty_bad + $qty_bad;
											$total_qty_free = $total_qty_free + $qty_free;
											$total_ext_amount = $total_ext_amount + $ext_amount;
											$pageTotal ++;
											$grand_total_qty_good= $grand_total_qty_good + $qty_good;
											$grand_total_qty_bad = $grand_total_qty_bad + $qty_bad;
											$grand_total_qty_free = $grand_total_qty_free + $qty_free;
											$grand_total_ext_amount = $grand_total_ext_amount + $ext_amount;
											$unit_buy_cost = $unit_cost / $conv;
											$unit_buy_cost = number_format($unit_buy_cost,4);
											$pdf->Cell(15,$dtl_ht, $prd_number, 0, 0);
											$pdf->Cell(30,$dtl_ht, $upcCode, 0, 0);
											$pdf->Cell(90,$dtl_ht, $ProdDesc, 0, 0);
											$pdf->Cell(25,$dtl_ht, $qty_order, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $buy_sell_conv, 0, 0);
											$pdf->Cell(30,$dtl_ht, $unit_cost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $unit_buy_cost, 0, 0,'R');
											$pdf->Cell(20,$dtl_ht, $qty_good, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $qty_bad, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $qty_free, 0, 0,'R');
											$pdf->Cell(30,$dtl_ht, $ext_amount, 0, 0,'R');
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
					$total_qty_good= 0;
					$total_qty_bad = 0;
					$total_qty_free = 0;
					$total_ext_amount = 0;
					$pageTotal = 0;
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
											$prd_number=mssql_result($query_event_date,$i,6);
											$qryUpc = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $prd_number");
											$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
		
											$ProdDesc=mssql_result($query_event_date,$i,7);
											$ProdDesc = str_replace("\\","",$ProdDesc);
											$qty_order = number_format(mssql_result($query_event_date,$i,20),2);
											$unit_cost = number_format(mssql_result($query_event_date,$i,21),4);
											$conv=number_format(mssql_result($query_event_date,$i,23),0);
											$buy_sell_conv = mssql_result($query_event_date,$i,22)."/".$conv;
											$qty_good = number_format(mssql_result($query_event_date,$i,17),0);
											$qty_bad = number_format(mssql_result($query_event_date,$i,19),0);
											$qty_free = number_format(mssql_result($query_event_date,$i,18),0);
											$ext_amount = number_format(mssql_result($query_event_date,$i,5),4);
											$total_qty_good= $total_qty_good + $qty_good;
											$total_qty_bad = $total_qty_bad + $qty_bad;
											$total_qty_free = $total_qty_free + $qty_free;
											$total_ext_amount = $total_ext_amount + $ext_amount;
											$pageTotal ++;
											$grand_total_qty_good= $grand_total_qty_good + $qty_good;
											$grand_total_qty_bad = $grand_total_qty_bad + $qty_bad;
											$grand_total_qty_free = $grand_total_qty_free + $qty_free;
											$grand_total_ext_amount = $grand_total_ext_amount + $ext_amount;
											$unit_buy_cost = $unit_cost / $conv;
											$unit_buy_cost = number_format($unit_buy_cost,4);
											$pdf->Cell(15,$dtl_ht, $prd_number, 0, 0);
											$pdf->Cell(30,$dtl_ht, $upcCode, 0, 0);
											$pdf->Cell(90,$dtl_ht, $ProdDesc, 0, 0);
											$pdf->Cell(25,$dtl_ht, $qty_order, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $buy_sell_conv, 0, 0);
											$pdf->Cell(30,$dtl_ht, $unit_cost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $unit_buy_cost, 0, 0,'R');
											$pdf->Cell(20,$dtl_ht, $qty_good, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $qty_bad, 0, 0,'R');
											$pdf->Cell(15,$dtl_ht, $qty_free, 0, 0,'R');
											$pdf->Cell(30,$dtl_ht, $ext_amount, 0, 0,'R');
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
			$total_qty_good = number_format($total_qty_good,0);
			$total_qty_bad = number_format($total_qty_bad,0);
			$total_qty_free = number_format($total_qty_free,0);
			$total_ext_amount = number_format($total_ext_amount,4);
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, "Page Total : ".$pageTotal, 0, 0);
			$pdf->Cell(90,$dtl_ht, '', 0, 0);
			$pdf->Cell(25,$dtl_ht, '', 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, 'Page Total', 0, 0,'R');
			$pdf->Cell(20,$dtl_ht, $total_qty_good, 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, $total_qty_bad, 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, $total_qty_free, 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $total_ext_amount, 0, 0,'R');
			
			$pdf->ln();
		}
		
		###################### R E P O R T  F O O T E R #########################
		if ($tmp_rec <= 0 && $j >= $m_page) { /// 1 page consume $j >=$m_page &&  $last_excempt < 8
			$pdf->ln();
			if ($flag==1) {
				$pdf->Cell($m_width,$dtl_ht, '', 0,1,'C');
			}
			
			$grand_total_qty_good = number_format($grand_total_qty_good,0);
			$grand_total_qty_bad = number_format($grand_total_qty_bad,0);
			$grand_total_qty_free = number_format($grand_total_qty_free,0);
			$grand_total_ext_amount = number_format($grand_total_ext_amount,4);
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, "Total Number of Items : ".$num_event_date, 0, 0);
			$pdf->Cell(90,$dtl_ht, '', 0, 0);
			$pdf->Cell(25,$dtl_ht, '', 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, '', 0, 0);
			$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, 'Total', 0, 0,'R');
			$pdf->Cell(20,$dtl_ht, $grand_total_qty_good, 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, $grand_total_qty_bad, 0, 0,'R');
			$pdf->Cell(15,$dtl_ht, $grand_total_qty_free, 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $grand_total_ext_amount, 0, 1,'R');
			$pdf->ln();
			$pdf->Cell(200,$dtl_ht, "", 0, 0);
			$pdf->Cell(50,$dtl_ht, "Less", 0, 1);
			$pdf->Cell(200,$dtl_ht, "", 0, 0);
			$pdf->Cell(50,$dtl_ht, "   PO Level Discounts     : ", 0, 0);
			$pdf->Cell(60,$dtl_ht, $sku_discount, 0, 1,'R');
			$pdf->Cell(200,$dtl_ht, "", 0, 0);
			$pdf->Cell(50,$dtl_ht, "   Vendor Allowances      : ", 0, 0);
			$pdf->Cell(60,$dtl_ht, $allowances, 0, 1,'R');
			$pdf->Cell(200,$dtl_ht, "", 0, 0);
			$pdf->Cell(50,$dtl_ht, "Add:", 0, 1);
			$pdf->Cell(200,$dtl_ht, "", 0, 0);
			$pdf->Cell(50,$dtl_ht, "   Additional Charges     : ", 0, 0);
			$pdf->Cell(60,$dtl_ht, $charges, 0, 1,'R');
			$net_amount = $grand_total_ext_amount - $sku_discount - $allowances + $charges;
			$net_amount = number_format($net_amount,4);
			$pdf->Cell(200,$dtl_ht, "", 0, 0);
			$pdf->Cell(50,$dtl_ht, "Net Amount                : ", 0, 0);
			$pdf->Cell(60,$dtl_ht, $net_amount, 0, 1,'R');
			$pdf->Cell(1,$dtl_ht, "Remarks : ".$remarks, 0, 1);
			$pdf->Cell(1,$dtl_ht, "Received By   : ____________________", 0, 1);
			$pdf->Cell(1,$dtl_ht, "Date Received : ____________________", 0, 1);
			$pdf->ln();
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0,1,'C');
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	$pdf->Output();
	
	$gmt = time() + (8 * 60 * 60);
	$newdate2 = date("m/d/Y", $gmt);
	if ($level == "print") {
		$UpdateSQL = "UPDATE tblRcrAudit SET ";
		$UpdateSQL .= "rcrPrntDate = '".$newdate2."', ";
		$UpdateSQL .= "rcrPrntOptr = '".$user_first_last."' ";
		$UpdateSQL .= "WHERE compCode = $company_code AND rcrNumber = " . $rcrnum ;
		mssql_query($UpdateSQL); 
	}
	if ($level == "reprint") {
		$UpdateSQL = "UPDATE tblRcrAudit SET ";
		$UpdateSQL .= "rcrReprntDate = '".$newdate2."', ";
		$UpdateSQL .= "rcrReprntOptr = '".$user_first_last."' ";
		$UpdateSQL .= "WHERE compCode = $company_code AND rcrNumber = " . $rcrnum ;
		mssql_query($UpdateSQL); 
	}
?>
<?php
	$gmt = time() + (8 * 100 * 100);
	$newdate = date("m/d/Y h:iA", $gmt);
	$newdate="Run Date : ".$newdate;
	$ponum = $_REQUEST['pono'];
	$level = $_GET['level'];
	include "../../functions/inquiry_session.php";
	require('../inventory/lbd_function.php');
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";	
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	include "../../functions/inquiry_session.php";
	require('lbd_number.php');
	$db = new DB;
	$db->connect();
	
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	$company_code = trim($company_code);
	$ponum = trim($ponum);
	$strCtr = "SELECT tblPoHeaderCorr.poNumber, CONVERT(varchar, tblPoHeaderCorr.poDate, 101) AS poDate, CONVERT(varchar, tblPoHeaderCorr.suppCode) 
                      + ' - ' + UPPER(tblSuppliers.suppName) AS suppName, tblPoHeaderCorr.corrPrintTag, tblPoHeaderCorr.poTerms, 
                      tblSuppliers.suppCurr, tblSuppliers.suppAddr1, tblSuppliers.suppAddr2, tblSuppliers.suppAddr3, tblSuppliers.suppTel, 
                      CONVERT(varchar, tblPoAudit.poRlseDate, 101) AS poRlseDate, tblPoAudit.compCode, tblPoHeaderCorr.compCode AS Expr1, 
                      tblPoHeader1.compCode AS Expr2
					  FROM tblPoHeaderCorr INNER JOIN
                      tblSuppliers ON tblPoHeaderCorr.suppCode = tblSuppliers.suppCode INNER JOIN
                      tblPoHeader1 ON tblPoHeaderCorr.compCode = tblPoHeader1.compCode AND 
                      tblPoHeaderCorr.poNumber = tblPoHeader1.poNumber INNER JOIN
                      tblPoAudit ON tblPoHeaderCorr.compCode = tblPoAudit.compCode AND tblPoHeaderCorr.poNumber = tblPoAudit.poNumber
					  WHERE (tblPoHeaderCorr.corrPrintTag <> 'Y') AND (tblPoHeader1.compCode=$company_code) AND (tblPoAudit.compCode=$company_code) AND (tblPoHeaderCorr.compCode=$company_code)  AND (tblPoHeaderCorr.poNumber=$ponum)";
	$result_loc = mssql_query($strCtr);
	$num_loc = mssql_num_rows($result_loc);
	if ($num_loc>0) {
		$suppCurr=mssql_result($result_loc,0,"suppCurr");
		$result_currency = mssql_query("SELECT * FROM tblCurrency WHERE currCode = '$suppCurr'");
		$num_currency = mssql_num_rows($result_currency);
		if ($num_currency>0) {
			$currency = mssql_result($result_currency,0,"currDesc");
			$currency = trim($currency);
		} else {
			$currency = "No Currency";
		}
		$suppName=mssql_result($result_loc,0,"suppName");
		$suppName = str_replace("\\","",$suppName);
		$suppAddr1=mssql_result($result_loc,0,"suppAddr1");
		$suppTel=mssql_result($result_loc,0,"suppTel");
		$address_tel = $suppAddr1." / ".$suppTel;
		$poTerms=mssql_result($result_loc,0,"poTerms");
		$strTerms = "SELECT * FROM tblTerms WHERE trmCode = $poTerms";
		$qryTerms = mssql_query($strTerms);
		$numTerms = mssql_num_rows($qryTerms);
		if ($numTerms >0){
			$terms=mssql_result($qryTerms,0,"trmDesc");
		} else {
			$terms="NA";
		}
		$original_date=mssql_result($result_loc,0,"poDate");
		$final_date=mssql_result($result_loc,0,"poRlseDate");
	} 
	###################################################################
	if ($level=="print") {
		$title = "PURCHASE ORDER";
	} else {
		if ($poReopenId>"") {
			$title = "PURCHASE ORDER (RE-OPENED COPY)";
		} else {
			$title = "PURCHASE ORDER (RE-PRINTED COPY)";
		}
	}
	$title = "PO Discrepancy Report";
	$strCtr = "SELECT  TOP 100 PERCENT tblPoHeaderCorr.poNumber, tblPoHeaderCorr.suppCode, UPPER(tblSuppliers.suppName) AS suppName, 
                      tblPoHeaderCorr.poTerms, CONVERT(varchar, tblPoHeader1.poDate, 101) AS [Original PO Date], CONVERT(varchar, 
                      tblPoAudit.poRlseDate, 101) AS [Final PO Date], tblPoItemCorr.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, 
                      UPPER(tblPoItemCorr.umCode) AS umCode, tblPoItemCorr.orderedQty, tblPoItemCorr.poUnitCost, tblPoItemCorr.prdConv, 
                      tblPoItemCorr.itemDiscPcents, tblPoItemCorr.poExtAmt, tblSuppliers.suppAddr1, tblSuppliers.suppAddr2, tblSuppliers.suppAddr3, 
                      tblSuppliers.suppTel, tblSuppliers.suppCurr, tblPoAudit.compCode, tblPoHeader1.compCode AS Expr1, 
                      tblPoHeaderCorr.compCode AS Expr2, tblPoItemCorr.compCode AS Expr3
					  FROM tblPoHeaderCorr INNER JOIN
                      tblSuppliers ON tblPoHeaderCorr.suppCode = tblSuppliers.suppCode INNER JOIN
                      tblPoHeader1 ON tblPoHeaderCorr.compCode = tblPoHeader1.compCode AND 
                      tblPoHeaderCorr.poNumber = tblPoHeader1.poNumber INNER JOIN
                      tblPoAudit ON tblPoHeaderCorr.compCode = tblPoAudit.compCode AND 
                      tblPoHeaderCorr.poNumber = tblPoAudit.poNumber INNER JOIN
                      tblPoItemCorr ON tblPoHeaderCorr.compCode = tblPoItemCorr.compCode AND 
                      tblPoHeaderCorr.poNumber = tblPoItemCorr.poNumber INNER JOIN
                      tblProdMast ON tblPoItemCorr.prdNumber = tblProdMast.prdNumber INNER JOIN
                      ViewPOCorrectionSummary ON tblPoHeaderCorr.poNumber = ViewPOCorrectionSummary.poNumber
					  WHERE (tblPoHeader1.compCode=$company_code) AND (tblPoAudit.compCode=$company_code) AND (tblPoHeaderCorr.compCode=$company_code)  AND (tblPoHeaderCorr.poNumber=$ponum)
					  GROUP BY tblPoHeaderCorr.poNumber, tblPoHeaderCorr.suppCode, UPPER(tblSuppliers.suppName), tblPoHeaderCorr.poTerms, 
                      CONVERT(varchar, tblPoHeader1.poDate, 101), CONVERT(varchar, tblPoAudit.poRlseDate, 101), tblPoItemCorr.prdNumber, 
                      UPPER(tblProdMast.prdDesc), UPPER(tblPoItemCorr.umCode), tblPoItemCorr.orderedQty, tblPoItemCorr.poUnitCost, 
                      tblPoItemCorr.prdConv, tblPoItemCorr.itemDiscPcents, tblPoItemCorr.poExtAmt, tblSuppliers.suppAddr1, tblSuppliers.suppAddr2, 
                      tblSuppliers.suppAddr3, tblSuppliers.suppTel, tblSuppliers.suppCurr, tblPoAudit.compCode, tblPoHeader1.compCode, tblPoItemCorr.compCode, 
                      tblPoHeaderCorr.compCode, tblPoItemCorr.compCode
					  ORDER BY tblProdMast.prdDesc";
	$result_loc = mssql_query($strCtr);
	$num_loc = mssql_num_rows($result_loc);
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$m_line=25;
	$dtl_ht=5.2;
	$max_tot_line=30;
	$m_width=310;
	$m_width_3_fields=103;
	$m_width_2_fields=155;
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
		$pdf->Cell($m_width_3_fields,5,"PO No. : " . $ponum,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : PO005P",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		if ($j==1) {
			$pdf->ln();
			$pdf->Cell(240,5,"PO Number : ".$ponum,0,0);
			$pdf->Cell(50,5,"Original PO Date : ".$original_date,0,1);
			$pdf->Cell(240,5,"   Vendor : ".$suppName,0,0);
			$pdf->Cell(50,5,"Final PO Date : ".$final_date,0,1);
			$pdf->Cell(240,5,"   Address/Tel.No. : ".$address_tel,0,0);
			$pdf->Cell(50,5,"Currency : ".$currency,0,1);
			$pdf->Cell(240,5,"   PO Terms : ".$terms,0,1);
		}
		//$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
				$pdf->Cell(15,4, '', 0, 0);
				$pdf->Cell(25,4, '', 0, 0);
				$pdf->Cell(90,4, '', 0, 0);
				$pdf->Cell(15,4, 'Buy', 0, 0);
				$pdf->Cell(30,4, 'Qty', 0, 0,'R');
				$pdf->Cell(35,4, 'Buy', 0, 0,'R');
				$pdf->Cell(60,4, '', 0, 0,'R');
				$pdf->Cell(40,4, 'Extended', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell(15,4, 'SKU', 0, 0);
				$pdf->Cell(25,4, 'UPC', 0, 0);
				$pdf->Cell(90,4, 'Description', 0, 0);
				$pdf->Cell(15,4, 'UM', 0, 0);
				$pdf->Cell(30,4, 'Diff.', 0, 0,'R');
				$pdf->Cell(35,4, 'Cost', 0, 0,'R');
				$pdf->Cell(60,4, 'Disc %', 0, 0,'R');
				$pdf->Cell(40,4, 'Amount', 0, 0,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$pageTotal = 0;
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=10; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;								
											$grid_code=mssql_result($result_loc,$i,"prdNumber");
											########################################
											$strUpc = "SELECT * FROM tblProdMast WHERE prdNumber = $grid_code";
											$qryUpc = mssql_query($strUpc);
											$numUpc = mssql_num_rows($qryUpc);
											if ($numUpc >0){
												$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
												$upcCode = $upcCode*1;
											} else {
												$upcCode="NA";
											}
											########################################
											$prd_desc = str_replace("\\","",mssql_result($result_loc,$i,"prdDesc"));
											$conv = number_format(mssql_result($result_loc,$i,"prdConv"),0);
											$bum_conv = trim(mssql_result($result_loc,$i,"umCode"))."/".$conv;
											$grid_ordered_qty=mssql_result($result_loc,$i,"orderedQty");
											$grid_ordered_qty = number_format($grid_ordered_qty,2);
											$grid_buy_cost=mssql_result($result_loc,$i,"poUnitCost");
											$grid_buy_cost = number_format($grid_buy_cost,4);
											$grid_disc=mssql_result($result_loc,$i,"itemDiscPcents");
											$rstSkuDisc=mssql_query("SELECT CONVERT(FLOAT, poItemDiscPcnt) AS AAA,poItemDiscTag  FROM tblPoItemDisc WHERE compCode = $company_code AND poNumber = $ponum AND prdNumber = $grid_code");	
											$numSkuDisc = mssql_num_rows($rstSkuDisc);
											$grid_discounts2="";
											for ($k=0; $k<$numSkuDisc; $k++) {
												$grid_discounts2 = $grid_discounts2.mssql_result($rstSkuDisc,$k,"AAA")."%(".mssql_result($rstSkuDisc,$k,"poItemDiscTag")."), ";
											}
											//$grid_disc = number_format($grid_disc,4);
											$grid_extended=mssql_result($result_loc,$i,"poExtAmt");
											$pageTotal = $pageTotal + $grid_extended;
											$grid_extended = number_format($grid_extended,4);
											$pdf->Cell(15,$dtl_ht, $grid_code, 0, 0);
											$pdf->Cell(25,$dtl_ht, $upcCode, 0, 0);
											$pdf->Cell(90,$dtl_ht, $prd_desc, 0, 0);
											$pdf->Cell(15,$dtl_ht, $bum_conv, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_ordered_qty, 0, 0,'R');
											$pdf->Cell(35,$dtl_ht, $grid_buy_cost, 0, 0,'R');
											$pdf->Cell(60,$dtl_ht, $grid_discounts2, 0, 0,'R');
											$pdf->Cell(40,$dtl_ht, $grid_extended, 0, 0,'R');
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
											$grid_code=mssql_result($result_loc,$i,"prdNumber");
											########################################
											$strUpc = "SELECT * FROM tblProdMast WHERE prdNumber = $grid_code";
											$qryUpc = mssql_query($strUpc);
											$numUpc = mssql_num_rows($qryUpc);
											if ($numUpc >0){
												$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
												$upcCode = $upcCode*1;
											} else {
												$upcCode="NA";
											}
											########################################
											$prd_desc = str_replace("\\","",mssql_result($result_loc,$i,"prdDesc"));
											$conv = number_format(mssql_result($result_loc,$i,"prdConv"),0);
											$bum_conv = trim(mssql_result($result_loc,$i,"umCode"))."/".$conv;
											$grid_ordered_qty=mssql_result($result_loc,$i,"orderedQty");
											$grid_ordered_qty = number_format($grid_ordered_qty,2);
											$grid_buy_cost=mssql_result($result_loc,$i,"poUnitCost");
											$grid_buy_cost = number_format($grid_buy_cost,4);
											$grid_disc=mssql_result($result_loc,$i,"itemDiscPcents");
											$rstSkuDisc=mssql_query("SELECT CONVERT(FLOAT, poItemDiscPcnt) AS AAA,poItemDiscTag  FROM tblPoItemDisc WHERE compCode = $company_code AND poNumber = $ponum AND prdNumber = $grid_code");	
											$numSkuDisc = mssql_num_rows($rstSkuDisc);
											$grid_discounts2="";
											for ($k=0; $k<$numSkuDisc; $k++) {
												$grid_discounts2 = $grid_discounts2.mssql_result($rstSkuDisc,$k,"AAA")."%(".mssql_result($rstSkuDisc,$k,"poItemDiscTag")."), ";
											}
											//$grid_disc = number_format($grid_disc,4);
											$grid_extended=mssql_result($result_loc,$i,"poExtAmt");
											$pageTotal = $pageTotal + $grid_extended;
											$grid_extended = number_format($grid_extended,4);
											$pdf->Cell(15,$dtl_ht, $grid_code, 0, 0);
											$pdf->Cell(25,$dtl_ht, $upcCode, 0, 0);
											$pdf->Cell(90,$dtl_ht, $prd_desc, 0, 0);
											$pdf->Cell(15,$dtl_ht, $bum_conv, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_ordered_qty, 0, 0,'R');
											$pdf->Cell(35,$dtl_ht, $grid_buy_cost, 0, 0,'R');
											$pdf->Cell(60,$dtl_ht, $grid_discounts2, 0, 0,'R');
											$pdf->Cell(40,$dtl_ht, $grid_extended, 0, 0,'R');
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
			$pageTotal = number_format($pageTotal,4);
			$pdf->Cell(230,$dtl_ht, "" , 0, 0);
			$pdf->Cell(40,$dtl_ht, "Page Total : " , 0, 0);
			$pdf->Cell(40,$dtl_ht, $pageTotal, 0, 0,'R');
			$pdf->ln();
		}
		
		###################### R E P O R T  F O O T E R #########################
		if ($tmp_rec <= 0 && $j >= $m_page) { /// 1 page consume $j >=$m_page &&  $last_excempt < 8
			$strTotals = "SELECT SUM(POTOTEXT) AS POTOTEXT, SUM(POTOTDISC) AS POTOTDISC, SUM(POTOTALLOW) AS POTOTALLOW, SUM(POTOTMISC) AS POTOTMISC FROM TBLPOHEADERCORR";
			$strTotals .= " WHERE PONUMBER = '$ponum' AND COMPCODE = $company_code";
			$qryTotals = mssql_query($strTotals);
			$rstTotals = mssql_fetch_array($qryTotals);
			$qryAllow = mssql_query("SELECT * FROM tblPOAllwDtl WHERE poNumber = $ponum AND compcode = $company_code");
			$numAllow = mssql_num_rows($qryAllow);
			$TotDisc = $rstTotals['POTOTDISC'];
			$TotAllow = $rstTotals['POTOTALLOW'];
			$TotMisc= $rstTotals['POTOTMISC'];
			$TotExtended=$rstTotals['POTOTEXT'];
			$TotalNet = $rstTotals['POTOTEXT'] - ($TotDisc + $TotAllow);
			//$total_total = $TotalNet + $TotMisc;
			$TotalNet = number_format($TotalNet,4);
			$pdf->ln();
			$pdf->Cell(230,$dtl_ht, "" , 0, 0);
			$pdf->Cell(40,$dtl_ht, "Gross Amount : " , 0, 0);
			$pdf->Cell(40,$dtl_ht, number_format($TotExtended,4), 0, 0,'R');
			
			$pdf->ln();
			$pdf->SetFont($font, 'B', '10');
			$pdf->Cell(80,$dtl_ht, "Discrepancies: ", 0, 1);
			$pdf->SetFont($font, '', '10');
			$pdf->Cell(80,$dtl_ht, "SKU Discounts : " . $TotDisc , 0, 0);
			$pdf->Cell(80,$dtl_ht, "Vendor Allowances : " . $TotAllow , 0, 0);
			$pdf->Cell(70,$dtl_ht, "", 0, 0);
			$pdf->Cell(40,$dtl_ht, "Net Amount : ", 0, 0);
			$pdf->Cell(40,$dtl_ht, $TotalNet, 0, 0,'R');
			
			$pdf->ln();
			if ($flag==1) {
				$pdf->Cell($m_width,$dtl_ht, '', 0,1,'C');
			}
			$pdf->ln();
			$pdf->Cell($m_width, 0, '', 1, 0);
			$pdf->Cell(183,$dtl_ht, "" , 0, 1);
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT. NOTHING FOLLOWS. * * *', 0,1,'C');
			$pdf->ln();
			$pdf->Cell(1,$dtl_ht, "Total Number of Items : ".$num_loc, 0, 1);
			$pdf->ln();
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	//echo $num_loc;
	$pdf->Output();
?>
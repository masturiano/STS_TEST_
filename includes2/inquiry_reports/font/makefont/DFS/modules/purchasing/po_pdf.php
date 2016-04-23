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
	$qryMisc= mssql_query("SELECT * FROM tblPOMiscDtl WHERE poNumber = $ponum AND compcode = $company_code ORDER BY poMiscSeq ASC");
	$numMisc = mssql_num_rows($qryMisc);
	$qryAllow = mssql_query("SELECT * FROM tblPOAllwDtl WHERE poNumber = $ponum AND compcode = $company_code");
	$numAllow = mssql_num_rows($qryAllow);
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
		$comp_address1=mssql_result($result_company,$i,"compAddr1");
		$comp_address2=mssql_result($result_company,$i,"compAddr2");
		$comp_address3=mssql_result($result_company,$i,"compAddr3");
	} else {
		$comp_name="NA";
		$comp_address1="NA";
		$comp_address2="NA";
		$comp_address3="NA";
	}
	$company_code = trim($company_code);
	$ponum = trim($ponum);
	if ($level == "original") {
		$strCtr = "SELECT TOP 100 PERCENT tblPoItemDtl1.poNumber, tblPoItemDtl1.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, 
						  tblPoItemDtl1.orderedQty, tblPoItemDtl1.poUnitCost, tblPoItemDtl1.prdConv, tblPoItemDtl1.itemDiscPcents, tblPoItemDtl1.poExtAmt, 
						  UPPER(tblPoItemDtl1.umCode) AS umCode, tblPoHeader1.compCode, tblSuppliers.suppCode, tblSuppliers.suppName, tblSuppliers.suppAddr1, tblSuppliers.suppTel,
						  tblPoHeader1.poTerms, tblPoHeader1.poReopenId, tblSuppliers.suppCurr, tblBuyers.buyerName, tblBuyers.buyerCode, tblPoItemDtl1.compCode
						  FROM tblProdMast INNER JOIN
						  tblPoItemDtl1 ON tblProdMast.prdNumber = tblPoItemDtl1.prdNumber INNER JOIN
						  tblPoHeader1 ON tblPoItemDtl1.poNumber = tblPoHeader1.poNumber INNER JOIN
						  tblSuppliers ON tblPoHeader1.suppCode = tblSuppliers.suppCode INNER JOIN
						  tblBuyers ON tblPoHeader1.poBuyer = tblBuyers.buyerCode
						  WHERE (tblPoItemDtl1.orderedQty > 0) AND (tblPoHeader1.poNumber  = $ponum) AND (tblPoHeader1.compCode = $company_code) AND (tblPoItemDtl1.compCode = $company_code)
						  ORDER BY tblProdMast.prdDesc";
	} else {
		$strCtr = "SELECT TOP 100 PERCENT tblPoItemDtl.poNumber, tblPoItemDtl.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, 
						  tblPoItemDtl.orderedQty, tblPoItemDtl.poUnitCost, tblPoItemDtl.prdConv, tblPoItemDtl.itemDiscPcents, tblPoItemDtl.poExtAmt, 
						  UPPER(tblPoItemDtl.umCode) AS umCode, tblPoHeader.compCode, tblSuppliers.suppCode, tblSuppliers.suppName, tblSuppliers.suppAddr1, tblSuppliers.suppTel,
						  tblPoHeader.poTerms, tblPoHeader.poReopenId, tblSuppliers.suppCurr, tblBuyers.buyerName, tblBuyers.buyerCode, tblPoItemDtl.compCode
						  FROM tblProdMast INNER JOIN
						  tblPoItemDtl ON tblProdMast.prdNumber = tblPoItemDtl.prdNumber INNER JOIN
						  tblPoHeader ON tblPoItemDtl.poNumber = tblPoHeader.poNumber INNER JOIN
						  tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
						  tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
						  WHERE (tblPoItemDtl.orderedQty > 0) AND (tblPoHeader.poNumber  = $ponum) AND (tblPoHeader.compCode = $company_code) AND (tblPoItemDtl.compCode = $company_code)
						  ORDER BY tblProdMast.prdDesc";
	}
	$result_loc = mssql_query($strCtr);
	$num_loc = mssql_num_rows($result_loc);
	if ($num_loc>0) {
		$suppCode=mssql_result($result_loc,0,"suppCode");
		$suppName=mssql_result($result_loc,0,"suppName");
		$suppliers = $suppCode." - ". $suppName;
		$suppAddr1=mssql_result($result_loc,0,"suppAddr1");
		$suppTel=mssql_result($result_loc,0,"suppTel");
		$address_tel = $suppAddr1." / ".$suppTel;
		$poTerms=mssql_result($result_loc,0,"poTerms");
		$poReopenId=mssql_result($result_loc,0,"poReopenId");
		$strTerms = "SELECT * FROM tblTerms WHERE trmCode = $poTerms";
		$qryTerms = mssql_query($strTerms);
		$numTerms = mssql_num_rows($qryTerms);
		if ($numTerms >0){
			$terms=mssql_result($qryTerms,0,"trmDesc");
		} else {
			$terms="NA";
		}
		$suppCurr=mssql_result($result_loc,0,"suppCurr");
		$result_currency = mssql_query("SELECT * FROM tblCurrency WHERE currCode = '$suppCurr'");
		$num_currency = mssql_num_rows($result_currency);
		if ($num_currency>0) {
			$currency = mssql_result($result_currency,0,"currDesc");
			$currency = trim($currency);
		} else {
			$currency = "No Currency";
		}
		$buyerCode=mssql_result($result_loc,0,"buyerCode");
		$buyerName=mssql_result($result_loc,0,"buyerName");
		$buyers = $buyerCode." - ".$buyerName;
	} 
	
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,0,"compName");
	} else {
		$comp_name="NA";
	}
	###################################################################
	//include FPDF class
	if ($level=="print") {
		$title = "PURCHASE ORDER";
	} 
	if ($level=="reprint") {
		if ($poReopenId>"") {
			$title = "PURCHASE ORDER (RE-OPENED COPY)";
		} else {
			$title = "PURCHASE ORDER (RE-PRINTED COPY)";
		}
	}
	if ($level=="original") {
		$title = "PURCHASE ORDER (ORIGINAL RE-OPEN)";
	} 
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
	if ($last_excempt>8 || $last_excempt==0){
		$m_page++;
	}
	if ($last_excempt>0 && $last_excempt<8) {
		$flag=1;
	}
	if ($last_excempt>=8 && $last_excempt<19) {
		$flag=2;
	}
	if ($last_excempt>=19 || $last_excempt==0) {
		$flag=3;
	}
	if (($numMisc>2 && ($last_excempt>8 && $last_excempt<19)) || ($numAllow>3 && $last_excempt>8)) {
		$m_page++;
		$flag=3;
	}
	if (($numMisc>2 && $last_excempt<9) || ($numAllow>3 && $last_excempt<9)) {
		$m_page++;
		$flag=2;
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
		$XXX = $pdf->GetX();
		$YYY = $pdf->GetY();
		$XXX = $XXX + 100;
		$pdf->Image("../../Images/pg_head_logo.jpg",$XXX,$YYY,$w=100,$h=10,$type='',$link='');
		
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,'',0,0,'C');
		$pdf->Cell($m_width_3_fields,5,"PO No. : " . $ponum,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : PO01P",0,0);
		$pdf->Cell($m_width_3_fields,5,"",0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"",0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_address,0,1,'C');
		$pdf->Cell($m_width_3_fields,5,"",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		if ($j==1) {
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell($m_width_3_fields,5,"Vendor : ".$suppliers,0,0);
			$pdf->Cell($m_width_3_fields,5,"",0,0,'C');
			$pdf->Cell($m_width_3_fields,5,"Currency : ".$currency,0,1,'R');
			$pdf->Cell($m_width_2_fields,5,"Address/Tel.No. : ".$address_tel,0,0);
			$pdf->Cell($m_width_2_fields,5,"Buyer : ".$buyers,0,1,'R');
			$pdf->Cell($m_width_2_fields,5,"PO Terms : ".$terms,0,1);
		} else {
			$pdf->ln();
			$pdf->ln();
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
				$pdf->Cell(30,4, 'Ordered', 0, 0,'R');
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
			$pdf->Cell(183,$dtl_ht, "" , 0, 0);
			$pdf->Cell(23,$dtl_ht, "Page Total : " , 0, 0);
			$pdf->Cell(103,$dtl_ht, $pageTotal, 0, 0,'R');
			//$pdf->ln();
		}
		
		###################### R E P O R T  F O O T E R #########################
		if ($tmp_rec <= 0) { /// 1 page consume $j >=$m_page &&  $last_excempt < 8
			if ($j >=$m_page) {
				$pdf->Cell(103,$dtl_ht, '', 0, 1,'R');
				$pdf->Cell(1,$dtl_ht, "Total Number of Items : ".$num_loc, 0, 1);
			}
			if ($flag==1) {
				include "po_pdf_total.php";
				include "po_pdf_footer.php";
			}
			if ($flag==2) {
				$pdf->Cell(103,$dtl_ht, '', 0, 0,'R');
				if ($j <$m_page) {
					include "po_pdf_total.php";
				} else {
					include "po_pdf_footer.php";
				}			
			}
			if ($flag==3) {
				if ($j >=$m_page) {
					$pdf->Cell(103,$dtl_ht, '', 0, 0,'R');
					include "po_pdf_total.php";
					include "po_pdf_footer.php";
				}			
			}
		}
	}
	//echo $num_loc;
	$pdf->Output();
	
	$gmt = time() + (8 * 60 * 60);
	$newdate2 = date("m/d/Y", $gmt);
	if ($level == "print") {
		$UpdateSQL = "UPDATE tblPoAudit SET ";
		$UpdateSQL .= "poPrntDate = '".$newdate2."', ";
		$UpdateSQL .= "poPrntOptr = '".$user_first_last."' ";
		$UpdateSQL .= "WHERE compCode = $company_code AND poNumber = " . $ponum ;
		mssql_query($UpdateSQL); 
	}
	if ($level == "reprint") {
		$UpdateSQL = "UPDATE tblPoAudit SET ";
		$UpdateSQL .= "poReprntDate = '".$newdate2."', ";
		$UpdateSQL .= "poReprntOptr = '".$user_first_last."' ";
		$UpdateSQL .= "WHERE compCode = $company_code AND poNumber = " . $ponum ;
		mssql_query($UpdateSQL); 
	}
?>
<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../etc/etc.obj.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$newdate="Run Date : ".$newdate;
	
	$locCode = $_GET['locCode'];
	$docDate = $_GET['docDate'];
	$from_loc=trim(getCodeofString($locCode)); 
	if ($docDate>"") {
		$docDate = new DateTime($docDate);
		$docDate = $docDate->format("m/d/Y");		
	} else {
		$docDate="";
	}
	$j=1;
	
	$qryTran = "SELECT TOP 100 PERCENT tblProdClass.prdGrpCode, tblProdClass.prdClsDesc, SUM(tblDlySalesSummary.slsExtAmt) AS Expr1, 
				SUM(tblDlySalesSummary.slsDiscAmt) AS Expr2, MAX(tblDlySalesSummary.compCode) as Expr3
				FROM tblDlySalesSummary INNER JOIN
				tblProdClass ON tblDlySalesSummary.prdGrpCode = tblProdClass.prdGrpCode
				WHERE (tblDlySalesSummary.locCode = $from_loc) AND (tblDlySalesSummary.slsDate = '$docDate') AND (tblProdClass.prdClsLvl = 1) AND (tblDlySalesSummary.compCode=$company_code)
				GROUP BY tblProdClass.prdGrpCode, tblProdClass.prdClsDesc
				ORDER BY tblProdClass.prdClsDesc";
	$resulinventorytrans = mssql_query($qryTran);
	$numinventorytrans = mssql_num_rows($resulinventorytrans);
	
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$dtl_ht=4;
	$m_width=200;
	$m_line=56;
	$m_width_3_fields=66;
	$font="Courier";
	$pdf->AddPage();
	$pdf->SetFont($font, '', '10');
	$page=0;
	$m_page=$numinventorytrans / $m_line;
	$m_page=ceil($m_page); //// maximum page
	include 'daily_sales_flash_pdf_header.php';
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	for ($i=0;$i < $numinventorytrans;$i++){
		$group=mssql_result($resulinventorytrans,$i,"prdGrpCode")."-".mssql_result($resulinventorytrans,$i,"prdClsDesc");
		$ext_amt=mssql_result($resulinventorytrans,$i,"Expr1");
		$disc_amt=mssql_result($resulinventorytrans,$i,"Expr2");
		$ext_amt = number_format($ext_amt,2);
		$disc_amt = number_format($disc_amt,2);
			$pdf->ln();
			$getX = $pdf->getX();
			$getY = $pdf->getY();
			###################### header
			if ($getY>=258.00125) {
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				include 'daily_sales_flash_pdf_header.php';
			}
			################################
			$pdf->Cell(65,$dtl_ht, $group, 0, 0);
			$pdf->Cell(40,$dtl_ht, $ext_amt, 0, 0,'R');
			$pdf->Cell(40,$dtl_ht, $disc_amt, 0, 0,'R');
	} 
	###################### R E P O R T  F O O T E R #########################
	$qryGrand = "SELECT TOP 100 PERCENT SUM(slsExtAmt) AS Expr1, SUM(slsDiscAmt) AS Expr2, MAX(compCode) AS Expr3
				 FROM tblDlySalesSummary
				 WHERE (locCode = $from_loc) AND (slsDate = '$docDate') AND (compCode = $company_code)";
	$resGrand = mssql_query($qryGrand);
	$ext_grand=mssql_result($resGrand,0,"Expr1");
	$disc_grand=mssql_result($resGrand,0,"Expr2");
	$ext_grand = number_format($ext_grand,2);
	$disc_grand = number_format($disc_grand,2);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(65,$dtl_ht, "GRAND TOTAL", 0, 0,'R');
	$pdf->SetFont($font, 'B', '10');
	$pdf->Cell(40,$dtl_ht, $ext_grand, 1, 0,'R');
	$pdf->Cell(40,$dtl_ht, $disc_grand, 1, 0,'R');
	$pdf->SetFont($font, '', '10');
	$pdf->ln();
	$pdf->ln();
	$printed_by = "Prepared By : ".$user_first_last;
	$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
	#########################################################################
	$pdf->Output();
?>
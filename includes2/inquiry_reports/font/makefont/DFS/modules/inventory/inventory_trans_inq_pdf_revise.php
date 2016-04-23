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
	$queryinventorytrans="SELECT * FROM tblInvTran 
						  WHERE (compCode = $company_code) AND (docDate >= '$hide_from_date') AND (docDate <= '$hide_to_date') $new_prod_code $new_loc_code $new_trans_type 
						  ORDER BY prdNumber,docDate,docNumber ASC";
	$resulinventorytrans=mssql_query($queryinventorytrans);
	$numinventorytrans = mssql_num_rows($resulinventorytrans);
	$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (compCode = $company_code) AND (docDate >= '$hide_from_date') AND (docDate <= '$hide_to_date') $new_prod_code $new_loc_code $new_trans_type 
				GROUP BY prdNumber";
	$rstOtherLine = mssql_query($qryMaxRec);
	$numOtherLine = mssql_num_rows($rstOtherLine);
	
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
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$dtl_ht=4;
	$m_width=310;
	$max_tot_line=38;
	$m_line = 41;  ///maximum line
	$m_width_3_fields=103;
	$font="Courier";
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$numinventorytrans; //// temporary total record
	$m_page=($numOtherLine+$numinventorytrans) / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$pdf->AddPage();
	$pdf->SetFont($font, '', '10');
	include 'inventory_trans_inq_pdf_revise_header.php';
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	$pdf->Cell(25,$dtl_ht, '', 0, 1);
	for ($i=0;$i < $numinventorytrans;$i++){						
		$gridbusinesspartner="* * *";							
		$griddocdate=mssql_result($resulinventorytrans,$i,"docDate");
		if ($griddocdate>"") {
			$date = new DateTime($griddocdate);
			$griddocdate = $date->format("m/d/y");		
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
			$grid_location=mssql_result($result_location,0,"locName");
		} else {
			$grid_location="NA";
		}	
		$resulttranTypeInit=mssql_query("SELECT * FROM tblTransactionType WHERE trnTypeCode = $gridtranscode");
		$gridtranTypeInit=mssql_result($resulttranTypeInit,0,"trnTypeInit");
		if ((trim($gridtranscode)==21) || (trim($gridtranscode)==51)) {
			$grid_price_cost = number_format($grid_unit_price, 2);  
		} else {
			$grid_price_cost = number_format($grid_ave_cost, 2);  
		}
		if ($gridtranscode==21) {
			$gridbusinesspartner="Various";
		}
		if ($gridtranscode==51) {
			$resultcustName=mssql_query("SELECT * FROM tblCustMast WHERE custCode = $gridcustCode");

			$gridbusinesspartner=$gridcustCode."-".mssql_result($resultcustName,0,"custName");
		} 
		if (($gridtranscode==11)||($gridtranscode==12)||($gridtranscode==13)) {
			$resultsuppName=mssql_query("SELECT * FROM tblSuppliers WHERE suppCode = $gridsuppCode");
			$gridbusinesspartner=$gridsuppCode."-".mssql_result($resultsuppName,0,"suppName");
		} 
		if ($sku != $temp_sku) {
			$pdf->ln();
			$getX = $pdf->getX();
			$getY = $pdf->getY();
			if ($getY>=194.00125) {
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				$pdf->Cell(25,$dtl_ht, '', 0, 1);
				include 'inventory_trans_inq_pdf_revise_header.php';
			}
			$pdf->SetFont($font, 'B', '10');
			$pdf->Cell(25,$dtl_ht, $sku." - ".$grid_product, 0, 0);
			$pdf->SetFont($font, '', '10');
		}
		$pdf->ln();
		$getX = $pdf->getX();
		$getY = $pdf->getY();
		if ($getY>=194.00125) {
			$pdf->Cell(25,$dtl_ht, '', 0, 1);
			$pdf->Cell(25,$dtl_ht, '', 0, 1);
			$pdf->Cell(25,$dtl_ht, '', 0, 1);
			$pdf->Cell(25,$dtl_ht, '', 0, 1);
			$pdf->Cell(25,$dtl_ht, '', 0, 1);
			include 'inventory_trans_inq_pdf_revise_header.php';
		}
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
	}
	$pdf->ln();
	$pdf->ln();
	$pdf->Cell(30,$dtl_ht, "Total Number of Items : ".$numinventorytrans, 0, 0);
	$pdf->ln();
	$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0,1,'C');
	$pdf->ln();
	$printed_by = "Prepared By : ".$user_first_last;
	$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);

	$pdf->Output();
?>
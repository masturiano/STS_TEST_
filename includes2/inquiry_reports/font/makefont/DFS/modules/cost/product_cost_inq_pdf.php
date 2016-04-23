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
	$search_selection=$_GET['search_selection'];
	$search_query=$_GET['search_query'];
	$search_box=$_GET['search_box'];
	
	############################# dont forget to get the company code ##################################
	switch ($search_selection) {
		case "by_product":
			$title = "Product Cost Listing (By Product)";
			$m_line = 40;  ///maximum line
			break;
		case "by_vendor":
			$title = "Product Cost Listing (By Vendor)";
			$m_line = 40;  ///maximum line
			break;
	}
	####################company name##################################
	$result_prod_cost=mssql_query($search_query);
	$num_prod_cost = mssql_num_rows($result_prod_cost);
	
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
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$dtl_ht=4;
	$max_tot_line=30;
	$m_width=255;
	$m_width_3_fields=85;
	$font="Courier";
	$m_page=$num_prod_cost / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_prod_cost; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_prod_cost." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_prod_cost." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : EVN005I",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		if (($search_box!="By Product")&&($search_box!="By Vendor")) {
			$search_box = str_replace("-","",$search_box);
			if ($search_selection=="by_vendor") {
				$search_box = "Vendor : " . $search_box;
			} else {
				$search_box = "Product : " . $search_box;
			}
			$pdf->Cell($m_width_3_fields,5,$search_box,0,1);
			$pdf->ln();
		}
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		switch ($search_selection) {
			case "by_product":
				$pdf->Cell(95,$dtl_ht, '', 0, 0);
				$pdf->Cell(75,$dtl_ht, '     -----Regular-----', 0, 0,'C');
				$pdf->Cell(100,$dtl_ht, '     --------------Promo---------------', 0, 0,'C');
				$pdf->Cell(30,$dtl_ht, '', 0, 1);
				
				$pdf->Cell(95,$dtl_ht, 'Vendor', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cost', 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, 'Start', 0, 0,'C');
				//$pdf->Cell(20,$dtl_ht, 'Event', 0, 0,'C');
				$pdf->Cell(30,$dtl_ht, 'Cost', 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, 'Start', 0, 0,'C');
				$pdf->Cell(25,$dtl_ht, 'End', 0, 0,'C');
				//$pdf->Cell(20,$dtl_ht, 'Event', 0, 0,'C');
				$pdf->Cell(30,$dtl_ht, 'Ave Cost', 0, 0,'R');
				break;
			case "by_vendor":
				$pdf->Cell(95,$dtl_ht, '', 0, 0);
				$pdf->Cell(75,$dtl_ht, '-----Regular-----', 0, 0,'C');
				$pdf->Cell(100,$dtl_ht, '-----------Promo-----------', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 1);
				
				$pdf->Cell(95,$dtl_ht, 'Product', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cost', 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, 'Start', 0, 0,'C');
				//$pdf->Cell(20,$dtl_ht, 'Event', 0, 0,'C');
				$pdf->Cell(30,$dtl_ht, 'Cost', 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, 'Start', 0, 0,'C');
				$pdf->Cell(25,$dtl_ht, 'End', 0, 0,'C');
				//$pdf->Cell(20,$dtl_ht, 'Event', 0, 0,'C');
				$pdf->Cell(30,$dtl_ht, 'Ave Cost', 0, 0,'R');
				break;
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=10; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									switch ($search_selection) {
										case "by_product":
											$grid_prod_code=mssql_result($result_prod_cost,$i,"prdNumber");
											$grid_supp_code=mssql_result($result_prod_cost,$i,"suppCode");
											$grid_supp_name=mssql_result($result_prod_cost,$i,"suppName");
											$grid_supp_name = str_replace("\\","",$grid_supp_name);
											$grid_supp_name=$grid_supp_code."-".$grid_supp_name;
											$grid_reg_ucost=mssql_result($result_prod_cost,$i,"regUnitCost");
											if ($grid_reg_ucost>0) {
												$grid_reg_ucost=number_format($grid_reg_ucost,2);
												$grid_reg_start=mssql_result($result_prod_cost,$i,"regCostStart");
												if ($grid_reg_start=="") {
													$grid_reg_start = "";
												} else {
													$date = new DateTime($grid_reg_start);
													$grid_reg_start = $date->format("m/d/Y");
												}
												$grid_reg_event=mssql_result($result_prod_cost,$i,"regCostEvent");
											} else {
												$grid_reg_ucost="";
												$grid_reg_start="";
												$grid_reg_event="";
											}
											
											$grid_promo_ucost=mssql_result($result_prod_cost,$i,"promoUnitCost");
											if ($grid_promo_ucost > 0) {
												$grid_promo_ucost=number_format($grid_promo_ucost,2);
												$grid_promo_start=mssql_result($result_prod_cost,$i,"promoCostStart");
												if ($grid_promo_start=="") {
													$grid_promo_start ="";
												} else {
													$date = new DateTime($grid_promo_start);
													$grid_promo_start = $date->format("m/d/Y");
												}
												$grid_promo_end=mssql_result($result_prod_cost,$i,"promoCostEnd");
												if ($grid_promo_end=="") {
													$grid_promo_end = "";
												} else {
													$date = new DateTime($grid_promo_end);
													$grid_promo_end = $date->format("m/d/Y");
												}
												$grid_promo_event=mssql_result($result_prod_cost,$i,"promoCostEvent");
											} else {
												$grid_promo_ucost="";
												$grid_promo_start="";
												$grid_promo_end = "";
												$grid_promo_event="";
											}
											$result_ave=mssql_query("SELECT * FROM tblAveCost WHERE compCode = $company_code AND prdNumber = $grid_prod_code");
											$num_ave = mssql_num_rows($result_ave);
											if ($num_ave > 0) {
												$grid_ave_ucost=mssql_result($result_ave,0,"aveUnitCost");	
												if ($grid_ave_ucost>0) {
													$grid_ave_ucost=number_format($grid_ave_ucost,2);
												} else {
													$grid_ave_ucost="";
												}
											} else {
												$grid_ave_ucost="";
											}	
											$pdf->Cell(95,$dtl_ht, $grid_supp_name, 0, 0);
											$pdf->Cell(30,$dtl_ht, 	$grid_reg_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_reg_start, 0, 0,'C');
											//$pdf->Cell(20,$dtl_ht, $grid_reg_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_promo_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_promo_start, 0, 0,'C');
											$pdf->Cell(25,$dtl_ht, $grid_promo_end, 0, 0,'C');
											//$pdf->Cell(20,$dtl_ht, $grid_promo_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_ave_ucost, 0, 0,'R');
											$pdf->ln();
											break;
										case "by_vendor":
											case "by_vendor":
											$grid_prod_code=mssql_result($result_prod_cost,$i,"prdNumber");
											$grid_prod_desc=mssql_result($result_prod_cost,$i,"prdDesc");
											$grid_prod_desc=str_replace("\\","",$grid_prod_desc);
											//$grid_buy_unit=mssql_result($result_prod_cost,$i,"prdBuyUnit");
											//$grid_conv=mssql_result($result_prod_cost,$i,"prdConv");
											$grid_prod_desc=$grid_prod_code."-".$grid_prod_desc;
											$grid_reg_ucost=mssql_result($result_prod_cost,$i,"regUnitCost");
											if ($grid_reg_ucost>0) {
												$grid_reg_ucost=number_format($grid_reg_ucost,2);
												$grid_reg_start=mssql_result($result_prod_cost,$i,"regCostStart");
												if ($grid_reg_start=="") {
													$grid_reg_start = "";
												} else {
													$date = new DateTime($grid_reg_start);
													$grid_reg_start = $date->format("m/d/Y");
												}
												$grid_reg_event=mssql_result($result_prod_cost,$i,"regCostEvent");
											} else {
												$grid_reg_start="";
												$grid_reg_ucost="";
											}
											$grid_promo_ucost=mssql_result($result_prod_cost,$i,"promoUnitCost");
											if ($grid_promo_ucost>0) {
												$grid_promo_ucost=number_format($grid_promo_ucost,2);
												$grid_promo_start=mssql_result($result_prod_cost,$i,"promoCostStart");
												$grid_promo_event=mssql_result($result_prod_cost,$i,"promoCostEvent");
												if ($grid_promo_start == "") {
													$grid_promo_start = "";
												} else {
													$date = new DateTime($grid_promo_start);
													$grid_promo_start = $date->format("m/d/Y");
												}
												$grid_promo_end=mssql_result($result_prod_cost,$i,"promoCostEnd");
												if ($grid_promo_end == "") {
													$grid_promo_end = "";
												} else {
													$date = new DateTime($grid_promo_end);
													$grid_promo_end = $date->format("m/d/Y");
												}
											} else {
												$grid_promo_ucost="";
												$grid_promo_end="";
												$grid_promo_start="";
												$grid_promo_event="";
											}
											
											
											$result_ave=mssql_query("SELECT * FROM tblAveCost WHERE compCode = $company_code AND prdNumber = $grid_prod_code");
											$num_ave = mssql_num_rows($result_ave);
											if ($num_ave > 0) {
												$grid_ave_ucost=mssql_result($result_ave,0,"aveUnitCost");
												if ($grid_ave_ucost>0) {
													$grid_ave_ucost=number_format($grid_ave_ucost,2);
												} else {
													$grid_ave_ucost="";
												}	
											} else {
												$grid_ave_ucost="";
											}
											$pdf->Cell(95,$dtl_ht, $grid_prod_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, 	$grid_reg_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_reg_start, 0, 0,'C');
											//$pdf->Cell(20,$dtl_ht, $grid_reg_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_promo_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_promo_start, 0, 0,'C');
											$pdf->Cell(25,$dtl_ht, $grid_promo_end, 0, 0,'C');
											//$pdf->Cell(20,$dtl_ht, $grid_promo_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_ave_ucost, 0, 0,'R');
											$pdf->ln();
											break;
									}
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
									switch ($search_selection) {
										case "by_product":
											$grid_prod_code=mssql_result($result_prod_cost,$i,"prdNumber");
											$grid_supp_code=mssql_result($result_prod_cost,$i,"suppCode");
											$grid_supp_name=mssql_result($result_prod_cost,$i,"suppName");
											$grid_supp_name = str_replace("\\","",$grid_supp_name);
											$grid_supp_name=$grid_supp_code."-".$grid_supp_name;
											$grid_reg_ucost=mssql_result($result_prod_cost,$i,"regUnitCost");
											if ($grid_reg_ucost>0) {
												$grid_reg_ucost=number_format($grid_reg_ucost,2);
												$grid_reg_start=mssql_result($result_prod_cost,$i,"regCostStart");
												if ($grid_reg_start=="") {
													$grid_reg_start = "";
												} else {
													$date = new DateTime($grid_reg_start);
													$grid_reg_start = $date->format("m/d/Y");
												}
												$grid_reg_event=mssql_result($result_prod_cost,$i,"regCostEvent");
											} else {
												$grid_reg_ucost="";
												$grid_reg_start="";
												$grid_reg_event="";
											}
											
											$grid_promo_ucost=mssql_result($result_prod_cost,$i,"promoUnitCost");
											if ($grid_promo_ucost > 0) {
												$grid_promo_ucost=number_format($grid_promo_ucost,2);
												$grid_promo_start=mssql_result($result_prod_cost,$i,"promoCostStart");
												if ($grid_promo_start=="") {
													$grid_promo_start ="";
												} else {
													$date = new DateTime($grid_promo_start);
													$grid_promo_start = $date->format("m/d/Y");
												}
												$grid_promo_end=mssql_result($result_prod_cost,$i,"promoCostEnd");
												if ($grid_promo_end=="") {
													$grid_promo_end = "";
												} else {
													$date = new DateTime($grid_promo_end);
													$grid_promo_end = $date->format("m/d/Y");
												}
											} else {
												$grid_promo_ucost="";
												$grid_promo_start="";
												$grid_promo_end = "";
											}
											
											$grid_promo_event=mssql_result($result_prod_cost,$i,"promoCostEvent");
											$result_ave=mssql_query("SELECT * FROM tblAveCost WHERE compCode = $company_code AND prdNumber = $grid_prod_code");
											$num_ave = mssql_num_rows($result_ave);
											if ($num_ave > 0) {
												$grid_ave_ucost=mssql_result($result_ave,0,"aveUnitCost");	
												if ($grid_ave_ucost>0) {
													$grid_ave_ucost=number_format($grid_ave_ucost,4);
												} else {
													$grid_ave_ucost="";
												}
											} else {
												$grid_ave_ucost="";
											}
											$pdf->Cell(95,$dtl_ht, $grid_supp_name, 0, 0);
											$pdf->Cell(30,$dtl_ht, 	$grid_reg_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_reg_start, 0, 0,'C');
											$pdf->Cell(20,$dtl_ht, $grid_reg_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_promo_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_promo_start, 0, 0,'C');
											$pdf->Cell(25,$dtl_ht, $grid_promo_end, 0, 0,'C');
											$pdf->Cell(20,$dtl_ht, $grid_promo_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_ave_ucost, 0, 0,'R');
											$pdf->ln();
											break;
										case "by_vendor":
											case "by_vendor":
											$grid_prod_code=mssql_result($result_prod_cost,$i,"prdNumber");
											$grid_prod_desc=mssql_result($result_prod_cost,$i,"prdDesc");
											$grid_prod_desc=str_replace("\\","",$grid_prod_desc);
											//$grid_buy_unit=mssql_result($result_prod_cost,$i,"prdBuyUnit");
											//$grid_conv=mssql_result($result_prod_cost,$i,"prdConv");
											$grid_prod_desc=$grid_prod_code."-".$grid_prod_desc;
											$grid_reg_ucost=mssql_result($result_prod_cost,$i,"regUnitCost");
											if ($grid_reg_ucost>0) {
												$grid_reg_ucost=number_format($grid_reg_ucost,2);
												$grid_reg_start=mssql_result($result_prod_cost,$i,"regCostStart");
												if ($grid_reg_start=="") {
													$grid_reg_start = "";
												} else {
													$date = new DateTime($grid_reg_start);
													$grid_reg_start = $date->format("m/d/Y");
												}
												$grid_reg_event=mssql_result($result_prod_cost,$i,"regCostEvent");
											} else  {
												$grid_reg_ucost="";
												$grid_reg_start = "";
												$grid_reg_event="";
											}
											
											$grid_promo_ucost=mssql_result($result_prod_cost,$i,"promoUnitCost");
											if ($grid_promo_ucost>0) {
												$grid_promo_ucost=number_format($grid_promo_ucost,2);
												$grid_promo_start=mssql_result($result_prod_cost,$i,"promoCostStart");
												$grid_promo_event=mssql_result($result_prod_cost,$i,"promoCostEvent");
												if ($grid_promo_start == "") {
													$grid_promo_start = "";
												} else {
													$date = new DateTime($grid_promo_start);
													$grid_promo_start = $date->format("m/d/Y");
												}
												$grid_promo_end=mssql_result($result_prod_cost,$i,"promoCostEnd");
												if ($grid_promo_end == "") {
													$grid_promo_end = "";
												} else {
													$date = new DateTime($grid_promo_end);
													$grid_promo_end = $date->format("m/d/Y");
												}
											} else {
												$grid_promo_ucost="";
												$grid_promo_end="";
												$grid_promo_start="";
												$grid_promo_event="";
											}
											
											
											$result_ave=mssql_query("SELECT * FROM tblAveCost WHERE compCode = $company_code AND prdNumber = $grid_prod_code");
											$num_ave = mssql_num_rows($result_ave);
											if ($num_ave > 0) {
												$grid_ave_ucost=mssql_result($result_ave,0,"aveUnitCost");	
												if ($grid_ave_ucost>0) {
													$grid_ave_ucost=number_format($grid_ave_ucost,4);
												} else {
													$grid_ave_ucost="";
												}
											} else {
												$grid_ave_ucost="";
											}
											
											
											
											$pdf->Cell(95,$dtl_ht, $grid_prod_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, 	$grid_reg_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_reg_start, 0, 0,'C');
											$pdf->Cell(20,$dtl_ht, $grid_reg_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_promo_ucost, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_promo_start, 0, 0,'C');
											$pdf->Cell(25,$dtl_ht, $grid_promo_end, 0, 0,'C');
											$pdf->Cell(20,$dtl_ht, $grid_promo_event, 0, 0,'C');
											$pdf->Cell(30,$dtl_ht, $grid_ave_ucost, 0, 0,'R');
											$pdf->ln();
											break;
									}
						$i++;
					}
					break;
				} 
				$m_line=$m_line - 1;
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
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0,1,'C');
			$pdf->ln();
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	//echo $num_prod_cost;
	$pdf->Output();
?>
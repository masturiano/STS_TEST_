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
	$newdate2 = date("m/d/Y", $gmt);
	$newdate="Run Date : ".$newdate;
	$search_query=$_POST['search_query'];
	$search_selection=$_POST['search_selection'];
	
	############################# dont forget to get the company code ##################################
	switch ($search_selection) {
		case "all_record":
			$title = "Average Cost Listing as of $newdate2";
			$m_line = 55;  ///maximum line
			break;
	}
	####################company name##################################
	$result_trans=mssql_query($search_query);
	$num_trans = mssql_num_rows($result_trans);
	
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
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$dtl_ht=4;
	$max_tot_line=30;
	$m_width=200;
	$m_width_3_fields=66;
	$font="Courier";
	$m_page=$num_trans / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_trans; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_trans." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_trans." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : AVECOSTL",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		switch ($search_selection) {
			case "all_record":
				$pdf->Cell(110,$dtl_ht, '', 0, 0);
				$pdf->Cell(10,$dtl_ht, 'Sell', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Average', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Last', 0, 0,'C');
				$pdf->ln();
				$pdf->Cell(110,$dtl_ht, 'Product Code and Description', 0, 0);
				$pdf->Cell(10,$dtl_ht, 'UM', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Unit Cost', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Upate', 0, 0,'C');
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
										case "all_record":
											$grid_code=mssql_result($result_trans,$i,"prdNumber");
											$grid_name=mssql_result($result_trans,$i,"prdDesc");
											$grid_name = str_replace("\\","",$grid_name);
											$grid_prod = $grid_code." - ".$grid_name;
											$grid_um=mssql_result($result_trans,$i,"UmCode");
											$grid_unit_cost=mssql_result($result_trans,$i,"aveUnitCost");
											if ($grid_unit_cost > 0) {
												$grid_unit_cost = number_format($grid_unit_cost,4);
											} else {
												$grid_unit_cost = "";
											}
											$grid_date=mssql_result($result_trans,$i,"aveDocDate");
											if ($grid_date=="") {
												$grid_date = "";
											} else {
												$date = new DateTime($grid_date);
												$grid_date = $date->format("m/d/Y");
											}
											$pdf->Cell(110,$dtl_ht, $grid_prod, 0, 0);
											$pdf->Cell(10,$dtl_ht, $grid_um, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_unit_cost, 0, 0,'R');
											$pdf->Cell(30,$dtl_ht, $grid_date, 0, 0,'C');
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
										case "all_record":
											$grid_code=mssql_result($result_trans,$i,"prdNumber");
											$grid_name=mssql_result($result_trans,$i,"prdDesc");
											$grid_name = str_replace("\\","",$grid_name);
											$grid_prod = $grid_code." - ".$grid_name;
											$grid_um=mssql_result($result_trans,$i,"UmCode");
											$grid_unit_cost=mssql_result($result_trans,$i,"aveUnitCost");
											if ($grid_unit_cost > 0) {
												$grid_unit_cost = number_format($grid_unit_cost,4);
											} else {
												$grid_unit_cost = "";
											}
											$grid_date=mssql_result($result_trans,$i,"aveDocDate");
											if ($grid_date=="") {
												$grid_date = "";
											} else {
												$date = new DateTime($grid_date);
												$grid_date = $date->format("m/d/Y");
											}
											$pdf->Cell(110,$dtl_ht, $grid_prod, 0, 0);
											$pdf->Cell(10,$dtl_ht, $grid_um, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_unit_cost, 0, 0,'R');
											$pdf->Cell(30,$dtl_ht, $grid_date, 0, 0,'C');
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
	//echo $num_trans;
	$pdf->Output();
?>
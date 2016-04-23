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
	############################# dont forget to get the company code ##################################
	switch ($search_selection) {
		case "all_record":
			$title = "Vendor Allowance Listing";
			$m_line = 45;  ///maximum line
			break;
	}
	####################company name##################################
	$result_allow=mssql_query($search_query);
	$num_allow = mssql_num_rows($result_allow);
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
	$max_tot_line=60;
	$m_width=200;
	$m_width_3_fields=66;
	$font="Courier";
	$m_page=$num_allow / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_allow; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_allow." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_allow." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : VendAllowMaint",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		switch ($search_selection) {
			case "all_record":
				$pdf->Cell(5,$dtl_ht, '', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(85,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(15,$dtl_ht, 'BuyUM', 0, 0);
				$pdf->Cell(20,$dtl_ht, '% Disc', 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, 'Start', 0, 0,'C');
				$pdf->Cell(25,$dtl_ht, 'End', 0, 0,'C');
				break;
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=10; $g++) {
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				$total_line=0;
				for ($i=$tmp_first;$i <= $tmp_last;$i++){
					$i--;							
						switch ($search_selection) {
							case "all_record":
								$grid_supp_code=mssql_result($result_allow,$i,"suppCode");
								if ($grid_supp_code != $temp_supp_code) {
									$total_line=$total_line+1;
									if ($temp_supp_code >"") {
										$total_line=$total_line+1;
										//$pdf->Text(10,40,str_pad("=",95,'='));
									}
									$total_line=$total_line+1;
								} 
								$total_line=$total_line+1;
								$temp_supp_code=$grid_supp_code;
								break;
						}
					$i++;
				} 
				$temp_supp_code="";
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									switch ($search_selection) {
										case "all_record":
											$grid_supp_code=mssql_result($result_allow,$i,"suppCode");
											$grid_supp_name=mssql_result($result_allow,$i,"suppName");
											$grid_prod_no=mssql_result($result_allow,$i,"prdNumber");
											$grid_desc=mssql_result($result_allow,$i,"prdDesc");
											$grid_bum=mssql_result($result_allow,$i,"prdBuyUnit");
											$grid_percent=mssql_result($result_allow,$i,"allwPcent");
											$grid_start=mssql_result($result_allow,$i,"allwStartDate");
											$date = new DateTime($grid_start);
											$grid_start = $date->format("m-d-Y");
											$grid_end=mssql_result($result_allow,$i,"allwEndDate");
											$date = new DateTime($grid_end);
											$grid_end = $date->format("m-d-Y");
											if ($grid_supp_code != $temp_supp_code) {
												if ($temp_supp_code>"") {
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
												} else {
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
												}
												$grid_supp_name="Vendor: ".$grid_supp_code." ".$grid_supp_name;
												$pdf->Cell(20,$dtl_ht, $grid_supp_name, 0, 0);
											} 
											$pdf->Cell(80,$dtl_ht, '', 0, 1);
											$grid_percent=number_format($grid_percent,4);
											$pdf->Cell(5,$dtl_ht, '', 0, 0);
											$pdf->Cell(20,$dtl_ht, $grid_prod_no, 0, 0);
											$pdf->Cell(85,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(15,$dtl_ht, $grid_bum, 0, 0);
											$pdf->Cell(20,$dtl_ht, $grid_percent, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_start, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_end, 0, 0,'R');
											$temp_supp_code=$grid_supp_code;
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
				###################################
				####### READING TOTAL LINE ########
				###################################
				//$tmp_last_more2=$tmp_last_more;
				//$tmp_last_more2=$j*$m_line;
				//echo $m_line." - ";
				$tmp_last_more=$j*$m_line;
				$total_line=0;
				for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
					$i--;
						switch ($search_selection) {
							case "all_record":
								$grid_supp_code=mssql_result($result_allow,$i,"suppCode");
								if ($grid_supp_code != $temp_supp_code) {
									$total_line=$total_line+1;
									if ($temp_supp_code >"") {
										$total_line=$total_line+1;
									}
									$total_line=$total_line+1;
								} 
								$total_line=$total_line+1;
								$temp_supp_code=$grid_supp_code;
								break;
						}
					$i++;
				}
				###################################
				####### READING TOTAL LINE ########
				###################################
				$temp_supp_code="";
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
									switch ($search_selection) {
										case "all_record":
											$grid_supp_code=mssql_result($result_allow,$i,"suppCode");
											$grid_supp_name=mssql_result($result_allow,$i,"suppName");
											$grid_prod_no=mssql_result($result_allow,$i,"prdNumber");
											$grid_desc=mssql_result($result_allow,$i,"prdDesc");
											$grid_bum=mssql_result($result_allow,$i,"prdBuyUnit");
											$grid_percent=mssql_result($result_allow,$i,"allwPcent");
											$grid_start=mssql_result($result_allow,$i,"allwStartDate");
											$grid_end=mssql_result($result_allow,$i,"allwEndDate");
											if ($grid_supp_code != $temp_supp_code) {
												if ($temp_supp_code>"") {
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
												} else {
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
												}	
												$grid_supp_name="Vendor: ".$grid_supp_code." ".$grid_supp_name;
												$pdf->Cell(20,$dtl_ht, $grid_supp_name, 0, 0);
											} 
											$pdf->Cell(80,$dtl_ht, '', 0, 1);
											$grid_percent=number_format($grid_percent,4);
											$pdf->Cell(5,$dtl_ht, '', 0, 0);
											$pdf->Cell(20,$dtl_ht, $grid_prod_no, 0, 0);
											$pdf->Cell(85,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(15,$dtl_ht, $grid_bum, 0, 0);
											$pdf->Cell(20,$dtl_ht, $grid_percent, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_start, 0, 0,'R');
											$pdf->Cell(25,$dtl_ht, $grid_end, 0, 0,'R');
											$temp_supp_code=$grid_supp_code;
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
			$printed_by = "Prepared By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	$pdf->Output();
?>
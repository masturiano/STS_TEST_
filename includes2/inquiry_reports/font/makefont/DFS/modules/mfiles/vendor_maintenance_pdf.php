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
	$search_query=$_GET['search_query'];
	$search_selection=$_GET['search_selection'];
	
	############################# dont forget to get the company code ##################################
	switch ($search_selection) {
		case "all_record":
			$title = "Vendors Listing";
			$m_line = 75;  ///maximum line
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
	$pdf = new FPDF('P', 'mm', 'LEGAL');
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
		$pdf->Cell($m_width_3_fields,5,"Report ID : SUPPL",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		switch ($search_selection) {
			case "all_record":
				$pdf->Cell(78,$dtl_ht, 'Vendor Code and Name', 0, 0);
				$pdf->Cell(58,$dtl_ht, 'Terms', 0, 0);
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
											$grid_code=mssql_result($result_trans,$i,"suppCode");
											$grid_terms=mssql_result($result_trans,$i,"suppTerms");
											$query_terms="SELECT * FROM tblTerms 
															WHERE trmCode = $grid_terms";
											$result_terms=mssql_query($query_terms);
											$num_terms = mssql_num_rows($result_terms);
											$terms_len=0;
											$terms_desc="";
											$terms_desc2="";
											$terms_desc3="";
											if ($num_terms>0) {
												$terms_desc2 = mssql_result($result_terms,0,"trmShort");
												$terms_len = strlen($terms_desc2);
												for ($art=0; $art < 61; $art++) {
													$terms_desc = $terms_desc . $terms_desc2[$art];
												}
											} else {
												$terms_desc = "**********";
											}
											$grid_name=mssql_result($result_trans,$i,"suppName");
											$grid_code_name = $grid_code." - ".$grid_name;
											$grid_name = str_replace("\\","",$grid_name);
											$grid_address=mssql_result($result_trans,$i,"suppAddr1");
											$grid_address = str_replace("\\","",$grid_address);
											$grid_contact=mssql_result($result_trans,$i,"suppTel");
											$pdf->Cell(78,$dtl_ht, $grid_code_name, 0, 0);
											$pdf->Cell(58,$dtl_ht, $terms_desc, 0, 0);
											if ($terms_len>60) {
												for ($art=61; $art <= $terms_len; $art++) {
													$terms_desc3 = $terms_desc3 . $terms_desc2[$art];
												}
												$pdf->ln();
												$pdf->Cell(78,$dtl_ht, '', 0, 0);
												$pdf->Cell(58,$dtl_ht, $terms_desc3, 0, 0);
											}
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
											$grid_code=mssql_result($result_trans,$i,"suppCode");
											$grid_terms=mssql_result($result_trans,$i,"suppTerms");
											$query_terms="SELECT * FROM tblTerms 
															WHERE trmCode = $grid_terms";
											$result_terms=mssql_query($query_terms);
											$num_terms = mssql_num_rows($result_terms);
											$terms_len=0;
											$terms_desc="";
											$terms_desc2="";
											$terms_desc3="";
											if ($num_terms>0) {
												$terms_desc2 = mssql_result($result_terms,0,"trmShort");
												$terms_len = strlen($terms_desc2);
												for ($art=0; $art < 61; $art++) {
													$terms_desc = $terms_desc . $terms_desc2[$art];
												}
											} else {
												$terms_desc = "**********";
											}
											$grid_name=mssql_result($result_trans,$i,"suppName");
											$grid_code_name = $grid_code." - ".$grid_name;
											$grid_name = str_replace("\\","",$grid_name);
											$grid_address=mssql_result($result_trans,$i,"suppAddr1");
											$grid_address = str_replace("\\","",$grid_address);
											$grid_contact=mssql_result($result_trans,$i,"suppTel");
											$pdf->Cell(78,$dtl_ht, $grid_code_name, 0, 0);
											$pdf->Cell(58,$dtl_ht, $terms_desc, 0, 0);
											if ($terms_len>60) {
												for ($art=61; $art <= $terms_len; $art++) {
													$terms_desc3 = $terms_desc3 . $terms_desc2[$art];
												}
												$pdf->ln();
												$pdf->Cell(78,$dtl_ht, '', 0, 0);
												$pdf->Cell(58,$dtl_ht, $terms_desc3, 0, 0);
											}
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

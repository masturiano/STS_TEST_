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
	$cmb_group=$_GET['cmb_group'];
	$cmb_dept=$_GET['cmb_dept'];
	$cmb_class=$_GET['cmb_class'];
	$cmb_sub_class=$_GET['cmb_sub_class'];
	
	############################# dont forget to get the company code ##################################
	switch ($search_selection) {
		case "all_record":
			$title = "Product Hierarchy Listing";
			$m_line = 55;  ///maximum line
			break;
	}
	####################company name##################################
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	###################################################################
	$cmb_group2=getCodeofString($cmb_group); ///pick in inventory_inquiry_function.php
	$cmb_group=trim($cmb_group2);
	$cmb_dept2=getCodeofString($cmb_dept); ///pick in inventory_inquiry_function.php
	$cmb_dept=trim($cmb_dept2);
	$cmb_class2=getCodeofString($cmb_class); ///pick in inventory_inquiry_function.php
	$cmb_class=trim($cmb_class2);
	$cmb_sub_class2=getCodeofString($cmb_sub_class); ///pick in inventory_inquiry_function.php
	$cmb_sub_class=trim($cmb_sub_class2);
	$query_group="SELECT *
				  FROM tblProdClass 
				  WHERE (prdGrpCode = $cmb_group)
				  ORDER BY prdGrpCode, prdDeptCode, prdClsCode, prdSubClsCode, prdClsDesc ASC";
	$result_group=mssql_query($query_group);
	$num_group = mssql_num_rows($result_group);
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$dtl_ht=4;
	$max_tot_line=25;
	$m_width=200;
	$m_width_3_fields=66;
	$font="Courier";
	$m_page=$num_group / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_group; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_group." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_group." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : ClassMaint",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		switch ($search_selection) {
			case "all_record":
				$pdf->Cell(20,$dtl_ht, 'Group', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'Dept', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'Class', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'Sub Class', 0, 0);
				break;
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=14; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
										switch ($search_selection) {
											case "all_record":
												$grid_group_code=mssql_result($result_group,$i,"prdGrpCode");
												$grid_dept_code=mssql_result($result_group,$i,"prdDeptCode");
												$grid_class_code=mssql_result($result_group,$i,"prdClsCode");
												$grid_sub_class_code=mssql_result($result_group,$i,"prdSubClsCode");
												if ($grid_group_code!=$temp_group_code) {
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_group_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, $grid_desc, 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(30,$dtl_ht, '', 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
												if ($grid_dept_code!=$temp_dept_code) {
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_dept_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, $grid_desc, 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(30,$dtl_ht, '', 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
												if ($grid_class_code!=$temp_class_code) {
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_class_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, $grid_desc, 0, 0);
													$pdf->Cell(30,$dtl_ht, '', 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
												if ($grid_sub_class_code!=$temp_sub_class_code) {	
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_sub_class_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(30,$dtl_ht, $grid_desc, 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
										}
						$i++;
					} 
					break;
				} 
				$m_line = $m_line-5;
			}	
		} else {            /// more than 1 page consume
			for($g=1; $g<=14; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
										switch ($search_selection) {
											case "all_record":
												$grid_group_code=mssql_result($result_group,$i,"prdGrpCode");
												$grid_dept_code=mssql_result($result_group,$i,"prdDeptCode");
												$grid_class_code=mssql_result($result_group,$i,"prdClsCode");
												$grid_sub_class_code=mssql_result($result_group,$i,"prdSubClsCode");
												if ($grid_group_code!=$temp_group_code) {
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_group_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, $grid_desc, 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(30,$dtl_ht, '', 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
												if ($grid_dept_code!=$temp_dept_code) {
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_dept_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, $grid_desc, 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(30,$dtl_ht, '', 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
												if ($grid_class_code!=$temp_class_code) {
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_class_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, $grid_desc, 0, 0);
													$pdf->Cell(30,$dtl_ht, '', 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
												if ($grid_sub_class_code!=$temp_sub_class_code) {	
													$grid_desc=mssql_result($result_group,$i,"prdClsDesc");
													$grid_desc=$grid_sub_class_code." - ".$grid_desc;
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(20,$dtl_ht, '', 0, 0);
													$pdf->Cell(30,$dtl_ht, $grid_desc, 0, 1);
													$temp_group_code=$grid_group_code;
													$temp_dept_code=$grid_dept_code;
													$temp_class_code=$grid_class_code;
													break;
												}
										}
						$i++;
					}
					break;
				} 
				$m_line=$m_line - 5;
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
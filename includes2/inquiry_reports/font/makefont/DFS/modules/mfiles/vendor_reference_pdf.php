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
			$title = "Vendor-Products References Listing";
			$m_line = 45;  ///maximum line
			break;
	}
	####################company name##################################
	$result_ref=mssql_query($search_query);
	$num_ref = mssql_num_rows($result_ref);
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
	$m_page=$num_ref / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_ref; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_ref." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_ref." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : RefMaint",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,1,'C');
		//$pdf->Cell($m_width,5,$entries,0,1,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		switch ($search_selection) {
			case "all_record":
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(25,$dtl_ht, 'SellUM/', 0, 0);
				$pdf->Cell(22,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Vendor', 0, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(25,$dtl_ht, 'BuyUM/', 0, 0);
				$pdf->Cell(22,$dtl_ht, 'Vendor', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Product', 0, 0);
				$pdf->ln();
				
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(25,$dtl_ht, 'Conv', 0, 0);
				$pdf->Cell(22,$dtl_ht, 'Type', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Number', 0, 0);
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
								$grid_supp_code=mssql_result($result_ref,$i,"suppCode");
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
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									switch ($search_selection) {
										case "all_record":
											$grid_supp_code=mssql_result($result_ref,$i,"suppCode");
											$grid_supp_name=mssql_result($result_ref,$i,"suppName");
											$grid_supp_name= str_replace("\\","",$grid_supp_name);
											$grid_prod_no=mssql_result($result_ref,$i,"prdNumber");
											$grid_desc=mssql_result($result_ref,$i,"prdDesc");
											$grid_desc = str_replace("\\","",$grid_desc);
											$grid_sum=mssql_result($result_ref,$i,"prdSellUnit");
											$grid_bum=mssql_result($result_ref,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_ref,$i,"prdConv");
											$grid_upc=mssql_result($result_ref,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv,0);
											$grid_sell_buy_conv = $grid_sum."/".$grid_bum."/".$grid_conv;
											$grid_group=mssql_result($result_ref,$i,"prdGrpCode");
											$grid_dept=mssql_result($result_ref,$i,"prdDeptCode");
											$grid_class=mssql_result($result_ref,$i,"prdClsCode");
											$grid_sub_class=mssql_result($result_ref,$i,"prdSubClsCode");
											
											$result_vendor_product=mssql_query("SELECT * FROM tblVendorProduct WHERE suppCode = $grid_supp_code AND prdNumber = $grid_prod_no");
											$num_vendor_product = mssql_num_rows($result_vendor_product);
											if ($num_vendor_product >0){
												$suppProdNo=mssql_result($result_vendor_product,0,"suppProdNo");
												$vendorType=mssql_result($result_vendor_product,0,"vendorType");
												if ($vendorType[0]=="P") {
													$vendorType="Primary";
												} 
												if ($vendorType[0]=="A") {
													$vendorType="Alternate";
												}
											} else {
												$suppProdNo="";
												$vendorType="";
											}
											
											$grid_grp = $grid_group."/".$grid_dept."/".$grid_class."/".$grid_sub_class;
											if ($grid_supp_code != $temp_supp_code) {
												$pdf->Cell(80,$dtl_ht, '', 0, 1);
												if ($temp_supp_code >"") {
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
												}
												$grid_supp_name=$grid_supp_code."-".$grid_supp_name;
												$pdf->SetFont($font, 'B', '10');
												$pdf->Cell(20,$dtl_ht, $grid_supp_name, 0, 0);
												$pdf->SetFont($font, '', '10');
											} 
											$pdf->ln();
											
											$pdf->Cell(20,$dtl_ht, $grid_prod_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(25,$dtl_ht, $grid_sell_buy_conv, 0, 0);
											$pdf->Cell(22,$dtl_ht, $vendorType, 0, 0);
											$pdf->Cell(30,$dtl_ht, $suppProdNo, 0, 0);
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
								$grid_supp_code=mssql_result($result_ref,$i,"suppCode");
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
											$grid_supp_code=mssql_result($result_ref,$i,"suppCode");
											$grid_supp_name=mssql_result($result_ref,$i,"suppName");
											$grid_supp_name= str_replace("\\","",$grid_supp_name);
											$grid_prod_no=mssql_result($result_ref,$i,"prdNumber");
											$grid_desc=mssql_result($result_ref,$i,"prdDesc");
											$grid_desc = str_replace("\\","",$grid_desc);
											$grid_sum=mssql_result($result_ref,$i,"prdSellUnit");
											$grid_bum=mssql_result($result_ref,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_ref,$i,"prdConv");
											$grid_upc=mssql_result($result_ref,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv,0);
											$grid_sell_buy_conv = $grid_sum."/".$grid_bum."/".$grid_conv;
											$grid_group=mssql_result($result_ref,$i,"prdGrpCode");
											$grid_dept=mssql_result($result_ref,$i,"prdDeptCode");
											$grid_class=mssql_result($result_ref,$i,"prdClsCode");
											$grid_sub_class=mssql_result($result_ref,$i,"prdSubClsCode");
											
											$result_vendor_product=mssql_query("SELECT * FROM tblVendorProduct WHERE suppCode = $grid_supp_code AND prdNumber = $grid_prod_no");
											$num_vendor_product = mssql_num_rows($result_vendor_product);
											if ($num_vendor_product >0){
												$suppProdNo=mssql_result($result_vendor_product,0,"suppProdNo");
												$vendorType=mssql_result($result_vendor_product,0,"vendorType");
												if ($vendorType[0]=="P") {
													$vendorType="Primary";
												} 
												if ($vendorType[0]=="A") {
													$vendorType="Alternate";
												}
											} else {
												$suppProdNo="";
												$vendorType="";
											}
											
											$grid_grp = $grid_group."/".$grid_dept."/".$grid_class."/".$grid_sub_class;
											if ($grid_supp_code != $temp_supp_code) {
												$pdf->Cell(80,$dtl_ht, '', 0, 1);
												if ($temp_supp_code >"") {
													$pdf->Cell(80,$dtl_ht, '', 0, 1);
												}
												$grid_supp_name=$grid_supp_code."-".$grid_supp_name;
												$pdf->SetFont($font, 'B', '10');
												$pdf->Cell(20,$dtl_ht, $grid_supp_name, 0, 0);
												$pdf->SetFont($font, '', '10');
											} 
											$pdf->ln();
											
											$pdf->Cell(20,$dtl_ht, $grid_prod_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(25,$dtl_ht, $grid_sell_buy_conv, 0, 0);
											$pdf->Cell(22,$dtl_ht, $vendorType, 0, 0);
											$pdf->Cell(30,$dtl_ht, $suppProdNo, 0, 0);
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
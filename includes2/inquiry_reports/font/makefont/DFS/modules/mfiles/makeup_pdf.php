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
			$title = "Makeup/Breakup Translation Table Listing";
			$m_line = 30;  ///maximum line
			break;
	}
	####################company name##################################
	$result_makeup=mssql_query($search_query);
	$num_makeup = mssql_num_rows($result_makeup);
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
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$dtl_ht=4;
	$max_tot_line=75;
	$m_width=255;
	$m_width_3_fields=85;
	$font="Courier";
	$m_page=$num_makeup / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_makeup; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_makeup." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_makeup." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : SetProdL",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		switch ($search_selection) {
			case "all_record":
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(10,$dtl_ht, '', 0, 0);
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Product', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Product', 0, 0,'R');
				$pdf->Cell(60,$dtl_ht, 'Selling Period', 0, 1,'C');
		
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(10,$dtl_ht, 'U/M', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'Qty', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Cost', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Price', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, '---Start', 0, 0,'C');
				$pdf->Cell(30,$dtl_ht, 'End---', 0, 0,'C');
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
								$grid_child=mssql_result($result_makeup,$i,"prdChild");
								if ($grid_child < 1) {
									$total_line=$total_line+4;
								}
								################# SETUP CHILD
								if ($grid_child > 0) {
									$total_line=$total_line+1;
								}
								$total_line=$total_line+1;
								break;
						}
					$i++;
				} 
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									switch ($search_selection) {
										case "all_record":
											$grid_child=mssql_result($result_makeup,$i,"prdChild");
											$grid_ucost=mssql_result($result_makeup,$i,"mkUCost");
											$grid_reg_price=mssql_result($result_makeup,$i,"mkRegPrice");
											$grid_qty=mssql_result($result_makeup,$i,"MkQuantity");
											if ($grid_child < 1) {
												$grid_parent=mssql_result($result_makeup,$i,"prdParent");
												$grid_code=mssql_result($result_makeup,$i,"prdNumber");
												$grid_desc=mssql_result($result_makeup,$i,"prdDesc");
												$grid_desc = str_replace("\\","",$grid_desc);
												$grid_su=mssql_result($result_makeup,$i,"prdSellUnit");
												$grid_start=mssql_result($result_makeup,$i,"mkStartDate");
												$date = new DateTime($grid_start);
												$grid_start = $date->format("m-d-Y");
												$grid_end=mssql_result($result_makeup,$i,"mkEndDate");
												$date = new DateTime($grid_end);
												$grid_end = $date->format("m-d-Y");
												///// get regUnitPrice from table tblProdPrice....
												$query_price="SELECT * FROM tblProdPrice WHERE prdNumber = $grid_parent";
												$result_price=mssql_query($query_price);
												$num_price = mssql_num_rows($result_price);
												if ($num_price >0) {
													$grid_price=mssql_result($result_price,0,"regUnitPrice");
												} else {
													$grid_price="NA";
												}
												///// get aveUnitCost from table tblAveCost....
												$query_cost="SELECT * FROM tblAveCost WHERE prdNumber = $grid_parent";
												$result_cost=mssql_query($query_cost);
												$num_cost = mssql_num_rows($result_cost);
												if ($num_cost >0) {
													$grid_cost=mssql_result($result_cost,0,"aveUnitCost");
												} else {
													$grid_cost="NA";
												}
												$grid_qty=number_format($grid_qty,0);
												$grid_cost=number_format($grid_cost,2);
												$grid_price=number_format($grid_price,2);
												$pdf->ln();
												$pdf->SetFont($font, 'B', '10');
												$pdf->Cell($m_width,$dtl_ht,'Product Set/Parent Product', 0, 1);
												$pdf->SetFont($font, '', '10');
												$pdf->Cell(20,$dtl_ht, $grid_parent, 0, 0);
												$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
												$pdf->Cell(10,$dtl_ht, $grid_su, 0, 0);
												$pdf->Cell(20,$dtl_ht, '1', 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_cost, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_price, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_start, 0, 0,'C');
												$pdf->Cell(30,$dtl_ht, $grid_end, 0, 1,'C');
												$pdf->SetFont($font, 'B', '10');
												$pdf->Cell($m_width,$dtl_ht,'Component Products:', 0, 0);
												$pdf->SetFont($font, '', '10');
											}
											################# SETUP CHILD
											if ($grid_child > 0) {
												$grid_ucost=mssql_result($result_makeup,$i,"mkUCost");
												$grid_reg_price=mssql_result($result_makeup,$i,"mkRegPrice");
												$grid_qty=mssql_result($result_makeup,$i,"MkQuantity");
												///// get prdDesc from table tblProdMast....
												$query_desc="SELECT * FROM tblProdMast WHERE prdNumber = $grid_child";
												$result_desc=mssql_query($query_desc);
												$num_desc = mssql_num_rows($result_desc);
												if ($num_desc >0) {
													$grid_desc=mssql_result($result_desc,0,"prdDesc");
													$grid_desc = str_replace("\\","",$grid_desc);
												} else {
													$grid_desc="NA";
												}
												$grid_qty=number_format($grid_qty,0);
												$grid_ucost=number_format($grid_ucost,2);
												$grid_reg_price=number_format($grid_reg_price,2);
												$pdf->Cell(20,$dtl_ht, $grid_child, 0, 0);
												$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
												$pdf->Cell(10,$dtl_ht, $grid_su, 0, 0);
												$pdf->Cell(20,$dtl_ht, $grid_qty, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_ucost, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_reg_price, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht,'', 0, 0,'C');
												$pdf->Cell(30,$dtl_ht, '', 0, 0,'C');
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
								$grid_child=mssql_result($result_makeup,$i,"prdChild");
								if ($grid_child < 1) {
									$total_line=$total_line+4;
								}
								################# SETUP CHILD
								if ($grid_child > 0) {
									$total_line=$total_line+1;
								}
								$total_line=$total_line+1;
								break;
						}
					$i++;
				}
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
									switch ($search_selection) {
										case "all_record":
											$grid_child=mssql_result($result_makeup,$i,"prdChild");
											$grid_ucost=mssql_result($result_makeup,$i,"mkUCost");
											$grid_reg_price=mssql_result($result_makeup,$i,"mkRegPrice");
											$grid_qty=mssql_result($result_makeup,$i,"MkQuantity");
											if ($grid_child < 1) {
												$grid_parent=mssql_result($result_makeup,$i,"prdParent");
												$grid_code=mssql_result($result_makeup,$i,"prdNumber");
												$grid_desc=mssql_result($result_makeup,$i,"prdDesc");
												$grid_desc = str_replace("\\","",$grid_desc);
												$grid_su=mssql_result($result_makeup,$i,"prdSellUnit");
												$grid_start=mssql_result($result_makeup,$i,"mkStartDate");
												$date = new DateTime($grid_start);
												$grid_start = $date->format("m-d-Y");
												$grid_end=mssql_result($result_makeup,$i,"mkEndDate");
												$date = new DateTime($grid_end);
												$grid_end = $date->format("m-d-Y");
												///// get regUnitPrice from table tblProdPrice....
												$query_price="SELECT * FROM tblProdPrice WHERE prdNumber = $grid_parent";
												$result_price=mssql_query($query_price);
												$num_price = mssql_num_rows($result_price);
												if ($num_price >0) {
													$grid_price=mssql_result($result_price,0,"regUnitPrice");
												} else {
													$grid_price="NA";
												}
												///// get aveUnitCost from table tblAveCost....
												$query_cost="SELECT * FROM tblAveCost WHERE prdNumber = $grid_parent";
												$result_cost=mssql_query($query_cost);
												$num_cost = mssql_num_rows($result_cost);
												if ($num_cost >0) {
													$grid_cost=mssql_result($result_cost,0,"aveUnitCost");
												} else {
													$grid_cost="NA";
												}
												$grid_qty=number_format($grid_qty,0);
												$grid_cost=number_format($grid_cost,2);
												$grid_price=number_format($grid_price,2);
												$pdf->ln();
												$pdf->SetFont($font, 'B', '10');
												$pdf->Cell($m_width,$dtl_ht,'Product Set/Parent Product', 0, 1);
												$pdf->SetFont($font, '', '10');
												$pdf->Cell(20,$dtl_ht, $grid_parent, 0, 0);
												$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
												$pdf->Cell(10,$dtl_ht, $grid_su, 0, 0);
												$pdf->Cell(20,$dtl_ht, '1', 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_cost, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_price, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_start, 0, 0,'C');
												$pdf->Cell(30,$dtl_ht, $grid_end, 0, 1,'C');
												$pdf->SetFont($font, 'B', '10');
												$pdf->Cell($m_width,$dtl_ht,'Component Products:', 0, 0);
												$pdf->SetFont($font, '', '10');
											}
											################# SETUP CHILD
											if ($grid_child > 0) {
												$grid_ucost=mssql_result($result_makeup,$i,"mkUCost");
												$grid_reg_price=mssql_result($result_makeup,$i,"mkRegPrice");
												$grid_qty=mssql_result($result_makeup,$i,"MkQuantity");
												///// get prdDesc from table tblProdMast....
												$query_desc="SELECT * FROM tblProdMast WHERE prdNumber = $grid_child";
												$result_desc=mssql_query($query_desc);
												$num_desc = mssql_num_rows($result_desc);
												if ($num_desc >0) {
													$grid_desc=mssql_result($result_desc,0,"prdDesc");
													$grid_desc = str_replace("\\","",$grid_desc);
												} else {
													$grid_desc="NA";
												}
												$grid_qty=number_format($grid_qty,0);
												$grid_ucost=number_format($grid_ucost,2);
												$grid_reg_price=number_format($grid_reg_price,2);
												$pdf->Cell(20,$dtl_ht, $grid_child, 0, 0);
												$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
												$pdf->Cell(10,$dtl_ht, $grid_su, 0, 0);
												$pdf->Cell(20,$dtl_ht, $grid_qty, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_ucost, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_reg_price, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht,'', 0, 0,'C');
												$pdf->Cell(30,$dtl_ht, '', 0, 0,'C');
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
			$printed_by = "Prepared By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	$pdf->Output();
?>
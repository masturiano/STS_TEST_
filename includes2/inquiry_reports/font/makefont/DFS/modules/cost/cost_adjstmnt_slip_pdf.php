<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y H:i:s", $gmt);
	$newdate="Run Date : ".$newdate;
	$db = new DB;
	$db->connect();
		
	$t_ci_header=$_POST['hide_num_ci_header'];
	$hide_from_date=$_POST['hide_from_date'];
	$hide_to_date=$_POST['hide_to_date'];
	$from_to = "Report Date from ".$hide_from_date." to ".$hide_to_date;

	
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '10');
	$dtl_ht=4;
	$m_width=260;
	$m_width_3_fields=86;
	$m_width_2_fields=130;
	$m_line = 30;  ///maximum line
	
	for ($a=0;$a<$t_ci_header;$a++){ 
		$new_page=0;
		if(isset($_POST["check$a"])) {
			$check_ci_no=$_POST["check$a"];
			$query_ci_header="SELECT * FROM tblCostAdjHeader WHERE (adjNumber = $check_ci_no)";
			$result_ci_header=mssql_query($query_ci_header);
			$ci_no=mssql_result($result_ci_header,0,"adjNumber");
			$date=mssql_result($result_ci_header,0,"adjDate");
			$date = new DateTime($date);
			$date = $date->format("m-d-Y");
			$date="Date : ".$date;
			$remarks=mssql_result($result_ci_header,0,"adjRemarks");
			$comp_code=mssql_result($result_ci_header,0,"compCode");
			///// get compName from tblCompany....
			$query_company="SELECT * FROM tblCompany WHERE compCode = $comp_code";
			$result_company=mssql_query($query_company);
			$num_company = mssql_num_rows($result_company);
			if ($num_company >0) {
				$company_name=mssql_result($result_company,0,"compName");
			} else {
				$company_name="NA";
			}
			#################################################
			$m_page=0; $tmp_first=0; $tmp_last=0; $tmp_rec=0;
			################################################# 
			$query_ci_detail="SELECT * FROM tblCostAdjDtl WHERE (adjNumber = $check_ci_no)";
			$result_ci_detail=mssql_query($query_ci_detail);
			$num_ci_detail = mssql_num_rows($result_ci_detail);
			$details_total="No.of Items = ".$num_ci_detail;
			$m_page=$num_ci_detail / $m_line;
			$m_page=ceil($m_page); //// maximum page
			$tmp_first=0;          //// temporary first record
			$tmp_last=0;           //// temporary last record
			$tmp_rec=$num_ci_detail; //// temporary total record
			$new_page=0;
			$x_price_total_grand=0;
			$x_cost_total_grand=0;
			for ($b=0;$b<$m_page;$b++){
				$pdf->AddPage();
				$new_page++;
				$tmp_rec=$tmp_rec - $m_line;
				$tmp_first= ($new_page * $m_line) - ($m_line - 1);
				if ($tmp_rec <= 0) { /// 1 page consume
					$tmp_last=($new_page*$m_line) + $tmp_rec;
					$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_ci_detail." record/s";
					$details_per_page=($tmp_last+1) - $tmp_first;
					$details_per_page="No.of Items = ".$details_per_page;
					$page="Page ".$new_page." of ".$m_page;
				} else {
					$tmp_last_more=$new_page*$m_line;
					$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_ci_detail." record/s";
					$details_per_page=($tmp_last_more+1) - $tmp_first;
					$details_per_page="No.of Items = ".$details_per_page;
					$page="Page ".$new_page." of ".$m_page;
				}
				##################### P A G E   H E A D E R ########################
				$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
				$pdf->Cell($m_width_3_fields,5,$company_name,0,0,'C');
				$CIN="Adj No. : ".$ci_no;
				$pdf->Cell($m_width_3_fields,5,$CIN,0,1,'R');
				$pdf->Cell($m_width_3_fields,5,"Program-Id : ADJ01P",0,0);
				$pdf->Cell($m_width_3_fields,5,"Cost Adjustments",0,0,'C');
				$pdf->Cell($m_width_3_fields,5,$date,0,1,'R');
				if ($b == 0) {
					$pdf->ln();
					$pdf->Cell(167,3,"Remarks:",0,1);
					$pdf->ln();
					$get_x=$pdf->GetX();
					$get_y=$pdf->GetY();
					$get_y=$get_y+6;
					$pdf->Text($get_x,$get_y,'____________________________________________');
					$get_x=$pdf->GetX();
					$get_y=$pdf->GetY();
					$get_y=$get_y+9;
					$pdf->Text($get_x,$get_y,'____________________________________________');
					$get_x=$pdf->GetX();
					$get_y=$pdf->GetY();
					$get_y=$get_y+3;
					$pdf->Text($get_x,$get_y,'____________________________________________');
					$get_x=$pdf->GetX();
					$get_y=$pdf->GetY();
					$get_y=$get_y+6;
					$pdf->Text($get_x,$get_y,'____________________________________________');
					$pdf->Cell(167,3,$remarks,0,0);
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
				}
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(70,$dtl_ht, '', 0, 0);
				$pdf->Cell(35,$dtl_ht, '', 0, 0,'R');
				$pdf->Cell(35,$dtl_ht, 'Current', 0, 0,'R');
				$pdf->Cell(55,$dtl_ht, '---For Adjustment---', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(70,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(35,$dtl_ht, 'Unit Price', 0, 0,'R');
				$pdf->Cell(35,$dtl_ht, 'Ave.Cost', 0, 0,'R');
				$pdf->Cell(20,$dtl_ht, 'Qty', 0, 0,'R');
				$pdf->Cell(35,$dtl_ht, 'Ave.Cost', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$x_price_total_per_page=0;
				$x_cost_total_per_page=0;
				$last_page_pos=0;
				if ($tmp_rec <= 0) { /// 1 page consume
					$tmp_last=($new_page*$m_line) + $tmp_rec;
					$last_page_pos=$m_line - ($tmp_last -$tmp_first);
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									$grid_sku=mssql_result($result_ci_detail,$i,"prdNumber");
									$grid_adj_qty=mssql_result($result_ci_detail,$i,"adjQty");
									$grid_adj_price=mssql_result($result_ci_detail,$i,"adjPrice");
									$grid_adj_cost=mssql_result($result_ci_detail,$i,"adjCost");
									$grid_adj_ncost=mssql_result($result_ci_detail,$i,"adjNewCost");
									///// get prdDesc,prdBuyUnit,prdConv from table tblProdMast....
									$query_product="SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku";
									$result_product=mssql_query($query_product);
									$num_product = mssql_num_rows($result_product);
									if ($num_product >0) {
										$grid_desc=mssql_result($result_product,0,"prdDesc");
									} else {
										$grid_desc="NA";
									}
									$grid_adj_qty = number_format($grid_adj_qty, 0);        
									$grid_adj_price = number_format($grid_adj_price, 2);              
									$grid_adj_cost = number_format($grid_adj_cost, 4);            
									$grid_adj_ncost = number_format($grid_adj_ncost,4);                                
									$pdf->Cell(20,$dtl_ht, $grid_sku, 0, 0);
									$pdf->Cell(70,$dtl_ht, $grid_desc, 0, 0);
									$pdf->Cell(35,$dtl_ht, $grid_adj_price, 0, 0,'R');
									$pdf->Cell(35,$dtl_ht, $grid_adj_cost, 0, 0,'R');
									$pdf->Cell(20,$dtl_ht, $grid_adj_qty, 0, 0,'R');
									$pdf->Cell(35,$dtl_ht, $grid_adj_ncost, 0, 0,'R');
									$pdf->ln();	
						$i++;
					} 
					//$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0, 0,'C');	
				} else {            /// more than 1 page consume
					$tmp_last_more=$new_page*$m_line;
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
									$grid_sku=mssql_result($result_ci_detail,$i,"prdNumber");
									$grid_adj_qty=mssql_result($result_ci_detail,$i,"adjQty");
									$grid_adj_price=mssql_result($result_ci_detail,$i,"adjPrice");
									$grid_adj_cost=mssql_result($result_ci_detail,$i,"adjCost");
									$grid_adj_ncost=mssql_result($result_ci_detail,$i,"adjNewCost");
									///// get prdDesc,prdBuyUnit,prdConv from table tblProdMast....
									$query_product="SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku";
									$result_product=mssql_query($query_product);
									$num_product = mssql_num_rows($result_product);
									if ($num_product >0) {
										$grid_desc=mssql_result($result_product,0,"prdDesc");
									} else {
										$grid_desc="NA";
									}
									$grid_adj_qty = number_format($grid_adj_qty, 0);        
									$grid_adj_price = number_format($grid_adj_price, 2);              
									$grid_adj_cost = number_format($grid_adj_cost, 4);            
									$grid_adj_ncost = number_format($grid_adj_ncost, 4);                                
									$pdf->Cell(20,$dtl_ht, $grid_sku, 0, 0);
									$pdf->Cell(70,$dtl_ht, $grid_desc, 0, 0);
									$pdf->Cell(35,$dtl_ht, $grid_adj_price, 0, 0,'R');
									$pdf->Cell(35,$dtl_ht, $grid_adj_cost, 0, 0,'R');
									$pdf->Cell(20,$dtl_ht, $grid_adj_qty, 0, 0,'R');
									$pdf->Cell(35,$dtl_ht, $grid_adj_ncost, 0, 0,'R');
									$pdf->ln();
						$i++;
					}
				}
				###################### P A G E  F O O T E R ##########################
				if ($m_page > 1) {
					$pdf->ln();
					$pdf->Cell(20,$dtl_ht, '', 0, 0);
					$pdf->Cell(70,$dtl_ht, $details_per_page, 0, 0);
					//$pdf->Cell(90,$dtl_ht, 'Page Total', 0, 0,'R');
					$pdf->ln();
					$pdf->ln();
					if ($tmp_rec > 0) { /// 1 page consume
						//$pdf->Cell($m_width,$dtl_ht, $page, 0, 0,'R');
					}
				}
				if ($tmp_rec <= 0) { /// 1 page consume
					###################### R E P O R T  F O O T E R #########################
					$pdf->ln();
					$pdf->Cell(20,$dtl_ht, '', 0, 0);
					$pdf->Cell(70,$dtl_ht, $details_total, 0, 0);
					//$pdf->Cell(90,$dtl_ht, 'Total', 0, 0,'R');
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->Cell($m_width_2_fields,$dtl_ht,"Prepared by : __________________ Date : __________", 0, 0);
					$pdf->Cell($m_width_2_fields,$dtl_ht, "Approved by : __________________ Date : __________", 0, 0,'R');
					$pdf->ln();
					$pdf->Cell($m_width_2_fields,$dtl_ht, "(Sign Over Printed Name)", 0, 0);
					$pdf->Cell($m_width_2_fields,$dtl_ht, "           (Sign Over Printed Name)", 0, 0);
					##########################################################
					//for ($c=1;$c < ($last_page_pos - 8); $c++){
					//	$pdf->ln();
					//}
					$pdf->ln();
					$pdf->ln();
					$printed_by = "Printed By : ".$user_first_last;
					$pdf->Cell($m_width_2_fields,$dtl_ht, $printed_by, 0, 0);         
					##########################################################
				}
			}
		}
	}
	$pdf->Output();
?>
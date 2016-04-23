<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	require_once "../../modules/etc/etc.obj.php";
	require_once "../../modules/home/home.obj.php";
	$gmt = time() + (8 * 60 * 60);
	$newdate = "Run Date : ".date("m/d/Y H:iA", $gmt);
	$db = new DB;
	$db->connect();
	
	$hide_beg_bal_good_m=$_POST['hide_beg_bal_good_m'];
	$hide_beg_bal_bo_m=$_POST['hide_beg_bal_bo_m'];
	$hide_beg_cost_m=$_POST['hide_beg_cost_m'];
	$hide_mtd_receipts_q=$_POST['hide_mtd_receipts_q'];
	$hide_mtd_sales_q=$_POST['hide_mtd_sales_q'];
	$hide_mdt_transfers=$_POST['hide_mdt_transfers'];
	$hide_mtd_adjustments=$_POST['hide_mtd_adjustments'];
	$hide_mtd_ci_q=$_POST['hide_mtd_ci_q'];
	$hide_mtd_su_q=$_POST['hide_mtd_su_q'];
	$hide_end_bal_good_m=$_POST['hide_end_bal_good_m'];
	$hide_end_bal_bo_m=$_POST['hide_end_bal_bo_m'];
	$hide_end_cost_m=$_POST['hide_end_cost_m'];
	$hide_month=$_POST['hide_month'];
	$hide_year=$_POST['hide_year'];
	$month_year=$hide_month." ".$hide_year;
	$hide_loc_code=$_POST['hide_loc_code'];
	$hide_prod_code=$_POST['hide_prod_code'];
	
	#################### click inquire button #################
	$monthnum=getMonthName($hide_month); ///pick in inventory_inquiry_function.php 
	$new1=getCodeofString($hide_loc_code); ///pick in inventory_inquiry_function.php
	$new1=trim($new1);	
	$new2=getCodeofString($hide_prod_code); ///pick in inventory_inquiry_function.php
	$new2=trim($new2);
	if ($new1=="All") {
		$all_loc="";
	} else {
		$all_loc=" AND (locCode=$new1) ";
	}
	if ($new2=="All") {
		$all_prod="";
	} else {
		$all_prod=" AND (prdNumber=$new2) ";
	}
	############################# dont forget to get the company code ##################################
	if ($all_prod=="") { ##all location or all product
		$query_per_stock="SELECT     MAX(prdNumber) AS product, MAX(locCode) AS location, SUM(begBalGoodM) AS begBalGoodM_, SUM(begCostM) AS begCostM_, SUM(mtdRecitQ) 
				AS mtdRecitQ_, SUM(mtdRegSlesQ) AS mtdRegSlesQ_, SUM(mtdTransIn) AS mtdTransIn_, SUM(mtdTransOut) AS mtdTransOut_, SUM(mtdAdjQ) 
				AS mtdAdjQ_, SUM(mtdCountAdjQ) AS mtdCountAdjQ_, SUM(mtdCiQ) AS mtdCiQ_, SUM(mtdSuQ) AS mtdSuQ_, SUM(endBalGoodM) AS endBalGoodM_, 
				SUM(endCostM) AS endCostM_, SUM(endBalBoM) AS endBalBoM_, SUM(begBalBoM) AS begBalBoM_
				FROM tblInvBalM
				WHERE (compCode = $company_code) AND (pdMonth = '$monthnum') AND (pdYear = '$hide_year') $all_loc
				GROUP BY prdNumber"; 
	} else { ##
		$query_per_stock="SELECT *
			FROM tblInvBalM 
			WHERE (compCode = $company_code) AND (pdMonth = '$monthnum') AND (pdYear = '$hide_year') $all_loc $all_prod
			ORDER BY prdNumber ASC"; 
	}
	$result_per_stock=mssql_query($query_per_stock);
	$num_per_stock = mssql_num_rows($result_per_stock);
	
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,0,"compName");
	} else {
		$comp_name="NA";
	}
	####################################################################################################
	
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$dtl_ht=4;
	$font="Courier";
	$pdf->SetFont($font, '', '10');
	$m_width=255;
	$m_width_3_fields=85;
	$m_line = 35;  ///maximum line
	$m_page=$num_per_stock / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_per_stock; //// temporary total record
	$grand_amount=0;
	$grand_details=0;
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
	
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_per_stock." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_per_stock." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : INV007P",0,0);
		$pdf->Cell($m_width_3_fields,5,"INVENTORY VALUATION REPORT",0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$entries,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"",0,0);
		$pdf->Cell($m_width_3_fields,5,"For the month of ".$month_year,0,1,'C');
		$pdf->Cell($m_width_3_fields,5,"",0,1,'R');
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$pdf->Cell(100,$dtl_ht, '', 0, 0);
		$pdf->Cell(30,$dtl_ht, '', 0, 0);
		$pdf->Cell(15,$dtl_ht, '', 0, 0);
		$pdf->Cell(50,$dtl_ht, '      CURRENT bALANCE', 0, 0,'C');
		$pdf->Cell(30,$dtl_ht, 'AVERAGE', 0, 0,'R');
		$pdf->ln();
		$pdf->Cell(100,$dtl_ht, 'PRODUCT CODE AND DESCRIPTION', 0, 0);
		$pdf->Cell(30,$dtl_ht, 'LOCATION', 0, 0);
		$pdf->Cell(15,$dtl_ht, 'U/M', 0, 0);
		$pdf->Cell(25,$dtl_ht, 'GOOD', 0, 0,'R');
		$pdf->Cell(25,$dtl_ht, 'BO', 0, 0,'R');
		$pdf->Cell(30,$dtl_ht, 'UNIT COST', 0, 0,'R');
		$pdf->Cell(30,$dtl_ht, 'AMOUNT', 0, 0,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$total_amount=0;
		$total_details=0;
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$footer_space = $tmp_last - $tmp_first;
			$footer_space = ($m_line - $footer_space) - 10;
			for ($i=$tmp_first;$i <= $tmp_last;$i++){
				$i--;							
							if ($all_prod=="") {
								$grid_prod=mssql_result($result_per_stock,$i,"product");
								$grid_loc=mssql_result($result_per_stock,$i,"location");
								$resPd=mssql_query("SELECT * FROM tblLocation WHERE compCode = $company_code AND locCode = $grid_loc");
								$grid_loc = mssql_result($resPd,0,"locCode")." ".mssql_result($resPd,0,"locName");		
								$grid_beg_good=mssql_result($result_per_stock,$i,"begBalGoodM_");
								$grid_beg_cost=mssql_result($result_per_stock,$i,"begCostM_");
								$grid_receipts=mssql_result($result_per_stock,$i,"mtdRecitQ_");
								$grid_sales=mssql_result($result_per_stock,$i,"mtdRegSlesQ_");
								$grid_trans_in=mssql_result($result_per_stock,$i,"mtdTransIn_");
								$grid_trans_out=mssql_result($result_per_stock,$i,"mtdTransOut_");
								$grid_trans = $grid_trans_in - $grid_trans_out;
								$grid_adjust=mssql_result($result_per_stock,$i,"mtdAdjQ_");
								$grid_count=mssql_result($result_per_stock,$i,"mtdCountAdjQ_");
								$grid_adjustments=$grid_adjust+$grid_count;
								$grid_ci=mssql_result($result_per_stock,$i,"mtdCiQ_");
								$grid_store=mssql_result($result_per_stock,$i,"mtdSuQ_");
								$grid_end_good=mssql_result($result_per_stock,$i,"endBalGoodM_");
								$grid_end_cost=mssql_result($result_per_stock,$i,"endCostM_");
								$grid_beg_bo=mssql_result($result_per_stock,$i,"begBalBoM_");
								$grid_end_bo=mssql_result($result_per_stock,$i,"endBalBoM_");
							} else {
								$grid_prod=mssql_result($result_per_stock,$i,"prdNumber");
								$grid_loc=mssql_result($result_per_stock,$i,"locCode");
								$resPd=mssql_query("SELECT * FROM tblLocation WHERE compCode = $company_code AND locCode = $grid_loc");
								$grid_loc = mssql_result($resPd,0,"locCode")." ".mssql_result($resPd,0,"locName");
								$grid_beg_good=mssql_result($result_per_stock,$i,"begBalGoodM");
								$grid_beg_cost=mssql_result($result_per_stock,$i,"begCostM");
								$grid_receipts=mssql_result($result_per_stock,$i,"mtdRecitQ");
								$grid_sales=mssql_result($result_per_stock,$i,"mtdRegSlesQ");
								$grid_trans_in=mssql_result($result_per_stock,$i,"mtdTransIn");
								$grid_trans_out=mssql_result($result_per_stock,$i,"mtdTransOut");
								$grid_trans = $grid_trans_in - $grid_trans_out;
								$grid_adjust=mssql_result($result_per_stock,$i,"mtdAdjQ");
								$grid_count=mssql_result($result_per_stock,$i,"mtdCountAdjQ");
								$grid_adjustments=$grid_adjust+$grid_count;
								$grid_ci=mssql_result($result_per_stock,$i,"mtdCiQ");
								$grid_store=mssql_result($result_per_stock,$i,"mtdSuQ");
								$grid_end_good=mssql_result($result_per_stock,$i,"endBalGoodM");
								$grid_end_cost=mssql_result($result_per_stock,$i,"endCostM");
								$grid_beg_bo=mssql_result($result_per_stock,$i,"begBalBoM");
								$grid_end_bo=mssql_result($result_per_stock,$i,"endBalBoM");
							}
							///// get prdName from table tblProdMast....
							$query_prod="SELECT * FROM tblProdMast WHERE prdNumber = $grid_prod";
							$result_prod=mssql_query($query_prod);
							$num_prod = mssql_num_rows($result_prod);
							if ($num_prod >0) {
								$grid_prod=$grid_prod." ".mssql_result($result_prod,0,"prdDesc");
								$grid_um=mssql_result($result_prod,0,"prdSellUnit");
							} else {
								$grid_prod="NA";
								$grid_um="NA";
							}
							$grid_amount=($grid_end_good+$grid_end_bo)*$grid_end_cost;
							$grand_amount = $grand_amount + $grid_amount;
							$total_amount = $total_amount + $grid_amount;
							$grand_details++;
							$total_details++;
							$grid_beg_good=number_format($grid_beg_good,0);
							$grid_beg_cost=number_format($grid_beg_cost,2);
							$grid_receipts=number_format($grid_receipts,0);
							$grid_sales=number_format($grid_sales,0);
							$grid_trans=number_format($grid_trans,0);
							$grid_adjustments=number_format($grid_adjustments,0);
							$grid_ci=number_format($grid_ci,0);
							$grid_store=number_format($grid_store,0);
							$grid_end_good=number_format($grid_end_good,0);
							$grid_end_cost=number_format($grid_end_cost,4);
							$grid_beg_bo=number_format($grid_beg_bo,0);
							$grid_end_bo=number_format($grid_end_bo,0);
							$grid_amount=number_format($grid_amount,4);
							
							$pdf->Cell(100,$dtl_ht, $grid_prod, 0, 0);
							$pdf->Cell(30,$dtl_ht, $grid_loc, 0, 0);
							$pdf->Cell(15,$dtl_ht, $grid_um, 0, 0);
							$pdf->Cell(25,$dtl_ht, $grid_end_good, 0, 0,'R');
							$pdf->Cell(25,$dtl_ht, $grid_end_bo, 0, 0,'R');
							$pdf->Cell(30,$dtl_ht, $grid_end_cost, 0, 0,'R');
							$pdf->Cell(30,$dtl_ht, $grid_amount, 0, 0,'R');
							$pdf->ln();			
				$i++;
			} 		
		} else {            /// more than 1 page consume
			$tmp_last_more=$j*$m_line;
			for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
				$i--;
							if ($all_prod=="") {
								$grid_prod=mssql_result($result_per_stock,$i,"product");
								$grid_loc=mssql_result($result_per_stock,$i,"location");
								$resPd=mssql_query("SELECT * FROM tblLocation WHERE compCode = $company_code AND locCode = $grid_loc");
								$grid_loc = mssql_result($resPd,0,"locCode")." ".mssql_result($resPd,0,"locName");		
								$grid_beg_good=mssql_result($result_per_stock,$i,"begBalGoodM_");
								$grid_beg_cost=mssql_result($result_per_stock,$i,"begCostM_");
								$grid_receipts=mssql_result($result_per_stock,$i,"mtdRecitQ_");
								$grid_sales=mssql_result($result_per_stock,$i,"mtdRegSlesQ_");
								$grid_trans_in=mssql_result($result_per_stock,$i,"mtdTransIn_");
								$grid_trans_out=mssql_result($result_per_stock,$i,"mtdTransOut_");
								$grid_trans = $grid_trans_in - $grid_trans_out;
								$grid_adjust=mssql_result($result_per_stock,$i,"mtdAdjQ_");
								$grid_count=mssql_result($result_per_stock,$i,"mtdCountAdjQ_");
								$grid_adjustments=$grid_adjust+$grid_count;
								$grid_ci=mssql_result($result_per_stock,$i,"mtdCiQ_");
								$grid_store=mssql_result($result_per_stock,$i,"mtdSuQ_");
								$grid_end_good=mssql_result($result_per_stock,$i,"endBalGoodM_");
								$grid_end_cost=mssql_result($result_per_stock,$i,"endCostM_");
								$grid_beg_bo=mssql_result($result_per_stock,$i,"begBalBoM_");
								$grid_end_bo=mssql_result($result_per_stock,$i,"endBalBoM_");
							} else {
								$grid_prod=mssql_result($result_per_stock,$i,"prdNumber");
								$grid_loc=mssql_result($result_per_stock,$i,"locCode");
								$resPd=mssql_query("SELECT * FROM tblLocation WHERE compCode = $company_code AND locCode = $grid_loc");
								$grid_loc = mssql_result($resPd,0,"locCode")." ".mssql_result($resPd,0,"locName");
								$grid_beg_good=mssql_result($result_per_stock,$i,"begBalGoodM");
								$grid_beg_cost=mssql_result($result_per_stock,$i,"begCostM");
								$grid_receipts=mssql_result($result_per_stock,$i,"mtdRecitQ");
								$grid_sales=mssql_result($result_per_stock,$i,"mtdRegSlesQ");
								$grid_trans_in=mssql_result($result_per_stock,$i,"mtdTransIn");
								$grid_trans_out=mssql_result($result_per_stock,$i,"mtdTransOut");
								$grid_trans = $grid_trans_in - $grid_trans_out;
								$grid_adjust=mssql_result($result_per_stock,$i,"mtdAdjQ");
								$grid_count=mssql_result($result_per_stock,$i,"mtdCountAdjQ");
								$grid_adjustments=$grid_adjust+$grid_count;
								$grid_ci=mssql_result($result_per_stock,$i,"mtdCiQ");
								$grid_store=mssql_result($result_per_stock,$i,"mtdSuQ");
								$grid_end_good=mssql_result($result_per_stock,$i,"endBalGoodM");
								$grid_end_cost=mssql_result($result_per_stock,$i,"endCostM");
								$grid_beg_bo=mssql_result($result_per_stock,$i,"begBalBoM");
								$grid_end_bo=mssql_result($result_per_stock,$i,"endBalBoM");
							}
							///// get prdName from table tblProdMast....
							$query_prod="SELECT * FROM tblProdMast WHERE prdNumber = $grid_prod";
							$result_prod=mssql_query($query_prod);
							$num_prod = mssql_num_rows($result_prod);
							if ($num_prod >0) {
								$grid_prod=$grid_prod." ".mssql_result($result_prod,0,"prdDesc");
								$grid_um=mssql_result($result_prod,0,"prdSellUnit");
							} else {
								$grid_prod="NA";
								$grid_um="NA";
							}
							$grid_amount=($grid_end_good+$grid_end_bo)*$grid_end_cost;
							$grand_amount = $grand_amount + $grid_amount;
							$total_amount = $total_amount + $grid_amount;
							$grand_details++;
							$total_details++;
							$grid_beg_good=number_format($grid_beg_good,0);
							$grid_beg_cost=number_format($grid_beg_cost,2);
							$grid_receipts=number_format($grid_receipts,0);
							$grid_sales=number_format($grid_sales,0);
							$grid_trans=number_format($grid_trans,0);
							$grid_adjustments=number_format($grid_adjustments,0);
							$grid_ci=number_format($grid_ci,0);
							$grid_store=number_format($grid_store,0);
							$grid_end_good=number_format($grid_end_good,0);
							$grid_end_cost=number_format($grid_end_cost,4);
							$grid_beg_bo=number_format($grid_beg_bo,0);
							$grid_end_bo=number_format($grid_end_bo,0);
							$grid_amount=number_format($grid_amount,4);
							
							$pdf->Cell(100,$dtl_ht, $grid_prod, 0, 0);
							$pdf->Cell(30,$dtl_ht, $grid_loc, 0, 0);
							$pdf->Cell(15,$dtl_ht, $grid_um, 0, 0);
							$pdf->Cell(25,$dtl_ht, $grid_end_good, 0, 0,'R');
							$pdf->Cell(25,$dtl_ht, $grid_end_bo, 0, 0,'R');
							$pdf->Cell(30,$dtl_ht, $grid_end_cost, 0, 0,'R');
							$pdf->Cell(30,$dtl_ht, $grid_amount, 0, 0,'R');
							$pdf->ln();
				$i++;
			}
		}
		###################### P A G E  F O O T E R ##########################
		if ($m_page > 1) {
			$total_amount = number_format($total_amount, 4);  #
			$pdf->ln();
			$pdf->Cell(100,$dtl_ht, "", 0, 0);
			$pdf->Cell(30,$dtl_ht, "", 0, 0);
			$pdf->Cell(15,$dtl_ht, "", 0, 0);
			$pdf->Cell(25,$dtl_ht, "", 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, "Total (".$total_details." item/s)", 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, "", 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $total_amount, 0, 0,'R');
			$pdf->ln();
		}
		if ($tmp_rec <= 0) { /// 1 page consume
			###################### R E P O R T  F O O T E R #########################
			$pdf->ln();
			$grand_amount = number_format($grand_amount, 4);        #
			$pdf->Cell(100,$dtl_ht, "", 0, 0);
			$pdf->Cell(30,$dtl_ht, "", 0, 0);
			$pdf->Cell(15,$dtl_ht, "", 0, 0);
			$pdf->Cell(25,$dtl_ht, "", 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, "Grand Total (".$grand_details." item/s)", 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, "", 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $grand_amount, 0, 0,'R');
			$pdf->ln();
			$pdf->ln();
			$pdf->Cell($m_width,$dtl_ht, "* * * End of Report. Nothing Follows. * * *", 0, 0,'C');
			$pdf->ln();
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell($m_width_2_fields,$dtl_ht, $printed_by, 0, 0);         
			##########################################################
		}
	}
	$pdf->Output();
?>
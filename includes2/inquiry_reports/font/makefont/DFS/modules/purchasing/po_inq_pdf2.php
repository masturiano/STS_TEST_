<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../etc/etc.obj.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y", $gmt);
	$newdate="Run Date : ".$newdate;
	$search_selection=$_POST['search_selection'];
	$search_query=$_POST['search_query'];
	$search_box=$_POST['search_box'];
	switch ($search_selection) {
		case "all_record":
			$title = "Purchased Order Listing";
			$m_line = 8;  ///maximum line
			break;
	}
	############################# dont forget to get the company code ##################################
	####################company name##################################
	$result_po=mssql_query($search_query);
	$num_po = mssql_num_rows($result_po);
	
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
	$max_tot_line=40;
	$m_width=310;
	$m_width_3_fields=103;
	$m_width_3_fields_minus_30=$m_width_3_fields-40;
	$font="Courier";
	$m_page=$num_po / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_po; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_po." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_po." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : PO009L",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->ln();
		
		$pdf->Cell($m_width_3_fields,5,$search_box,0,1);
		
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$pdf->Cell(100,$dtl_ht, '', 0, 0);
		$pdf->Cell(80,$dtl_ht, 'Product', 0, 0);
		$pdf->Cell(10,$dtl_ht, '', 0, 0);
		$pdf->Cell(20,$dtl_ht, 'Ordered', 0, 0,'R');
		$pdf->Cell(20,$dtl_ht, 'Received', 0, 0,'R');
		$pdf->Cell(30,$dtl_ht, 'Unit Cost', 0, 0,'R');
		$pdf->Cell(30,$dtl_ht, 'Ext Amt', 0, 0,'R');
		$pdf->Cell(15,$dtl_ht, 'Disc', 0, 0,'R');
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=50; $g++) {
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				$total_line=0;
				for ($i=$tmp_first;$i <= $tmp_last;$i++){
					$i--;							
						switch ($search_selection) {
							case "all_record":
								$grid_po_number=mssql_result($result_po,$i,"poNumber");
								if ($grid_po_number != $temp_po_number) {
									$total_line=$total_line+11;
								} 
								$total_line=$total_line+1;
								$temp_po_number=$grid_po_number;
								break;
						}
					$i++;
				} 
				$temp_po_number="";
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									switch ($search_selection) {
										case "all_record":
											$grid_po_number=mssql_result($result_po,$i,"poNumber");
											$grid_supp_code=mssql_result($result_po,$i,"suppCode");
											$grid_supp_name=mssql_result($result_po,$i,"suppName");
											$grid_supp_name=$grid_supp_code." - ".$grid_supp_name;
											$grid_po_date=mssql_result($result_po,$i,"poDate");
											$date = new DateTime($grid_po_date);
											$grid_po_date = $date->format("m-d-Y");
											$grid_item_tot=mssql_result($result_po,$i,"poItemTotal");
											$grid_tot_ext=mssql_result($result_po,$i,"poTotExt");
											$grid_tot_disc=mssql_result($result_po,$i,"poTotDisc");
											$grid_tot_allow=mssql_result($result_po,$i,"poTotAllow");
											//$grid_tot_misc=mssql_result($result_po,$i,"poTotMisc");
											$grid_stat=mssql_result($result_po,$i,"poStat");
											$grid_prd_number=mssql_result($result_po,$i,"prdNumber");
											$grid_prd_desc=mssql_result($result_po,$i,"prdDesc");
											$grid_prd_desc=str_replace("\\","",$grid_prd_desc);
											$grid_prd_desc=$grid_prd_number." - ".$grid_prd_desc;
											//$grid_um_desc=mssql_result($result_po,$i,"umDesc");
											//$grid_conv=mssql_result($result_po,$i,"prdConv");
											$grid_ord_qty=mssql_result($result_po,$i,"orderedQty");
											$grid_ucost=mssql_result($result_po,$i,"poUnitCost");
											$grid_ext_amt=mssql_result($result_po,$i,"poExtAmt");
											$grid_disc_pcents=mssql_result($result_po,$i,"itemDiscPcents");
											$grid_rcr_qty=mssql_result($result_po,$i,"rcrQty");
											//$grid_rcr_number=mssql_result($result_po,$i,"rcrNumber");
											//$grid_rcr_date=mssql_result($result_po,$i,"rcrDate");
											//$grid_rcr_good=mssql_result($result_po,$i,"rcrQtyGood");
											//$grid_rcr_bad=mssql_result($result_po,$i,"rcrQtyBad");
											//$grid_rcr_free=mssql_result($result_po,$i,"rcrQtyFree");
											$grid_tot_ext=number_format($grid_tot_ext,2);
											$grid_tot_disc=number_format($grid_tot_disc,2);
											$grid_tot_allow=number_format($grid_tot_allow,2);
											//$grid_tot_misc=number_format($grid_tot_misc,2);
											//$grid_conv=number_format($grid_conv,0);
											$grid_ord_qty=number_format($grid_ord_qty,0);
											$grid_ucost=number_format($grid_ucost,2);
											$grid_ext_amt=number_format($grid_ext_amt,2);
											$grid_disc_pcents=number_format($grid_disc_pcents,0);
											$grid_rcr_qty=number_format($grid_rcr_qty,0);
											//$grid_rcr_good=number_format($grid_rcr_good,0);
											//$grid_rcr_bad=number_format($grid_rcr_bad,0);
											//$grid_rcr_free=number_format($grid_rcr_free,0);
											if ($grid_po_number!=$temp_po_number) {
												$pdf->ln();
												$pdf->Cell(40,$dtl_ht, 'PO Number', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_po_number, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'PO Date', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_po_date, 0, 1);
												$pdf->Cell(40,$dtl_ht, '# of Items', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_item_tot, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Total Ext Amt', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_ext, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Total Disc', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_disc, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Total Allow', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_allow, 0, 1);
												//$pdf->Cell(40,$dtl_ht, 'Total Misc', 0, 0);
												//$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_misc, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Status', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_stat, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Supplier', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_supp_name, 0, 0);	
												$pdf->ln();
											}
											//if ($grid_po_number!=$temp_po_number) {
												$pdf->Cell(100,$dtl_ht, '', 0, 0);
												$pdf->Cell(80,$dtl_ht, $grid_prd_desc, 0, 0);
												//$pdf->Cell(10,$dtl_ht, $grid_um_desc, 0, 0);
												$pdf->Cell(20,$dtl_ht, $grid_ord_qty, 0, 0,'R');
												$pdf->Cell(20,$dtl_ht, $grid_rcr_qty, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_ucost, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_ext_amt, 0, 0,'R');
												$pdf->Cell(15,$dtl_ht, $grid_disc_pcents, 0, 0,'R');
												$pdf->ln();
											//}
											$temp_po_number=$grid_po_number;
											break;
									}
						$i++;
					} 
					break;
				} 
				$m_line = $m_line-1;
			}	
		} else {            /// more than 1 page consume
			for($g=1; $g<=50; $g++) {
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
								$grid_po_number=mssql_result($result_po,$i,"poNumber");
								if ($grid_po_number != $temp_po_number) {
									$total_line=$total_line+11;
								} 
								$total_line=$total_line+1;
								$temp_po_number=$grid_po_number;
								break;
						}
					$i++;
				}
				$temp_po_number="";
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
									switch ($search_selection) {
										case "all_record":
											$grid_po_number=mssql_result($result_po,$i,"poNumber");
											$grid_supp_code=mssql_result($result_po,$i,"suppCode");
											$grid_supp_name=mssql_result($result_po,$i,"suppName");
											$grid_supp_name=$grid_supp_code." - ".$grid_supp_name;
											$grid_po_date=mssql_result($result_po,$i,"poDate");
											$date = new DateTime($grid_po_date);
											$grid_po_date = $date->format("m-d-Y");
											$grid_item_tot=mssql_result($result_po,$i,"poItemTotal");
											$grid_tot_ext=mssql_result($result_po,$i,"poTotExt");
											$grid_tot_disc=mssql_result($result_po,$i,"poTotDisc");
											$grid_tot_allow=mssql_result($result_po,$i,"poTotAllow");
											//$grid_tot_misc=mssql_result($result_po,$i,"poTotMisc");
											$grid_stat=mssql_result($result_po,$i,"poStat");
											$grid_prd_number=mssql_result($result_po,$i,"prdNumber");
											$grid_prd_desc=mssql_result($result_po,$i,"prdDesc");
											$grid_prd_desc=str_replace("\\","",$grid_prd_desc);
											$grid_prd_desc=$grid_prd_number." - ".$grid_prd_desc;
											//$grid_um_desc=mssql_result($result_po,$i,"umDesc");
											//$grid_conv=mssql_result($result_po,$i,"prdConv");
											$grid_ord_qty=mssql_result($result_po,$i,"orderedQty");
											$grid_ucost=mssql_result($result_po,$i,"poUnitCost");
											$grid_ext_amt=mssql_result($result_po,$i,"poExtAmt");
											$grid_disc_pcents=mssql_result($result_po,$i,"itemDiscPcents");
											$grid_rcr_qty=mssql_result($result_po,$i,"rcrQty");
											//$grid_rcr_number=mssql_result($result_po,$i,"rcrNumber");
											//$grid_rcr_date=mssql_result($result_po,$i,"rcrDate");
											//$grid_rcr_good=mssql_result($result_po,$i,"rcrQtyGood");
											//$grid_rcr_bad=mssql_result($result_po,$i,"rcrQtyBad");
											//$grid_rcr_free=mssql_result($result_po,$i,"rcrQtyFree");
											$grid_tot_ext=number_format($grid_tot_ext,2);
											$grid_tot_disc=number_format($grid_tot_disc,2);
											$grid_tot_allow=number_format($grid_tot_allow,2);
											//$grid_tot_misc=number_format($grid_tot_misc,2);
											//$grid_conv=number_format($grid_conv,0);
											$grid_ord_qty=number_format($grid_ord_qty,0);
											$grid_ucost=number_format($grid_ucost,2);
											$grid_ext_amt=number_format($grid_ext_amt,2);
											$grid_disc_pcents=number_format($grid_disc_pcents,0);
											$grid_rcr_qty=number_format($grid_rcr_qty,0);
											//$grid_rcr_good=number_format($grid_rcr_good,0);
											//$grid_rcr_bad=number_format($grid_rcr_bad,0);
											//$grid_rcr_free=number_format($grid_rcr_free,0);
											if ($grid_po_number!=$temp_po_number) {
												$pdf->ln();
												$pdf->Cell(40,$dtl_ht, 'PO Number', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_po_number, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'PO Date', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_po_date, 0, 1);
												$pdf->Cell(40,$dtl_ht, '# of Items', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_item_tot, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Total Ext Amt', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_ext, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Total Disc', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_disc, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Total Allow', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_allow, 0, 1);
												//$pdf->Cell(40,$dtl_ht, 'Total Misc', 0, 0);
												//$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_tot_misc, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Status', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_stat, 0, 1);
												$pdf->Cell(40,$dtl_ht, 'Supplier', 0, 0);
												$pdf->Cell($m_width_3_fields_minus_30,$dtl_ht, $grid_supp_name, 0, 0);
												$pdf->ln();	
											}
											//if ($grid_po_number!=$temp_po_number) {
												$pdf->Cell(100,$dtl_ht, '', 0, 0);
												$pdf->Cell(80,$dtl_ht, $grid_prd_desc, 0, 0);
												$pdf->Cell(10,$dtl_ht, '', 0, 0);
												$pdf->Cell(20,$dtl_ht, $grid_ord_qty, 0, 0,'R');
												$pdf->Cell(20,$dtl_ht, $grid_rcr_qty, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_ucost, 0, 0,'R');
												$pdf->Cell(30,$dtl_ht, $grid_ext_amt, 0, 0,'R');
												$pdf->Cell(15,$dtl_ht, $grid_disc_pcents, 0, 0,'R');
												$pdf->ln();
											//}
											$temp_po_number=$grid_po_number;
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
	//echo $num_po;
	$pdf->Output();
?>
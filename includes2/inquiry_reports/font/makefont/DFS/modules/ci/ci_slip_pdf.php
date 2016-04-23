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
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$pdf->SetFont('Courier', '', '10');
	$dtl_ht=4;
	$m_width=310;
	$m_width_3_fields=103;
	$m_width_2_fields=155;
	$m_line = 30;  ///maximum line
	
	for ($a=0;$a<$t_ci_header;$a++){ 
		$new_page=0;
		if(isset($_POST["check$a"])) {
			$check_ci_no=$_POST["check$a"];
			$query_ci_header="SELECT * FROM tblCiHeader WHERE (ciNumber = $check_ci_no)";
			$result_ci_header=mssql_query($query_ci_header);
			$ci_no=mssql_result($result_ci_header,0,"ciNumber");
			$date=mssql_result($result_ci_header,0,"ciDate");
			$date = new DateTime($date);
			$date = $date->format("m/d/Y");
			$date="Date : ".$date;
			$from_loc=mssql_result($result_ci_header,0,"ciLocation");
			$cust_code=mssql_result($result_ci_header,0,"custCode");
			$terms=mssql_result($result_ci_header,0,"ciTerms");
			$terms="Terms : ".$terms;
			$strf_no=mssql_result($result_ci_header,0,"strfNumber");
			$strf_no="Ref. STRF NO : ".$strf_no;
			$remarks=mssql_result($result_ci_header,0,"ciRemarks");
			$comp_code=mssql_result($result_ci_header,0,"compCode");
			$item_total=mssql_result($result_ci_header,0,"cItemTotal");
			$total_x_amount=mssql_result($result_ci_header,0,"ciTotExtAmt");
			$total_disc_amount=mssql_result($result_ci_header,0,"ciTotDiscAmt");
			///// get compName from tblCompany....
			$query_company="SELECT * FROM tblCompany WHERE compCode = $comp_code";
			$result_company=mssql_query($query_company);
			$num_company = mssql_num_rows($result_company);
			if ($num_company >0) {
				$company_name=mssql_result($result_company,0,"compName");
			} else {
				$company_name="NA";
			}
			///// get from_loc from locName from table tblLocation....
			$query_from_loc="SELECT * FROM tblLocation WHERE locCode = $from_loc";
			$result_from_loc=mssql_query($query_from_loc);
			$num_from_loc = mssql_num_rows($result_from_loc);
			if ($num_from_loc >0) {
				$from_loc_name=mssql_result($result_from_loc,0,"locName");
			} else {
				$from_loc_name="NA";
			}
			$from_loc=$from_loc." - ".$from_loc_name;
			///// get custName from table tblCustMast....
			$query_customer="SELECT * FROM tblCustMast WHERE custCode = $cust_code";
			$result_customer=mssql_query($query_customer);
			$num_customer = mssql_num_rows($result_customer);
			if ($num_customer >0) {
				$cust_name=mssql_result($result_customer,0,"custName");
				$cust_addr1=mssql_result($result_customer,0,"custAddr1");
				$cust_addr2=mssql_result($result_customer,0,"custAddr2");
				$cust_addr3=mssql_result($result_customer,0,"custAddr3");
				$cust_addr12=$cust_addr1.", ".$cust_addr2;
			} else {
				$cust_name="NA";
			}
			#################################################
			$m_page=0; $tmp_first=0; $tmp_last=0; $tmp_rec=0;
			################################################# 
			$query_ci_detail="SELECT * FROM tblciItemDtl WHERE (ciNumber = $check_ci_no)";
			$result_ci_detail=mssql_query($query_ci_detail);
			$num_ci_detail = mssql_num_rows($result_ci_detail);
			$details_total="No.of Items = ".$num_ci_detail;
			$m_page=$num_ci_detail / $m_line;
			$m_page=ceil($m_page); //// maximum page
			$tmp_first=0;          //// temporary first record
			$tmp_last=0;           //// temporary last record
			$tmp_rec=$num_ci_detail; //// temporary total record
			$new_page=0;
			$x_amt_grand_total=0;
			$disc_amt_grand_total=0;
			$net_amt_grand_total=0;
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
				$CIN="Commercial Invoice No. : ".$ci_no;
				$pdf->Cell($m_width_3_fields,5,$CIN,0,1,'R');
				$pdf->Cell($m_width_3_fields,5,"Report ID : CI002P",0,0);
				$pdf->Cell($m_width_3_fields,5,"Commercial Invoice",0,0,'C');
				$pdf->Cell($m_width_3_fields,5,$date,0,1,'R');
				if ($b == 0) {
					$pdf->Cell($m_width_3_fields,5,"",0,0);
					$pdf->Cell($m_width_3_fields,5,"",0,0,'C');
					$pdf->Cell($m_width_3_fields,5,$strf_no,0,1,'R');
					$pdf->Cell($m_width_3_fields,5,'From : '.$from_loc,0,0);
					$pdf->Cell($m_width_3_fields,5,"",0,0);
					$pdf->Cell($m_width_3_fields,5,$terms." day/s",0,0,'R');
					$pdf->ln();
					//$pdf->ln();
					$pdf->Cell($m_width_2_fields,5,'Sold To : '.$cust_code." - ".$cust_name,0,0);
					$pdf->Cell($m_width_2_fields,5,'Remarks : '.$remarks,0,0,'R');
					$pdf->ln();
					$pdf->Cell(20,5,'',0,0);
					$pdf->Cell(200,5," ".$cust_addr12.",",0,0);
					$pdf->ln();
					$pdf->Cell(20,5,'',0,0);
					$pdf->Cell(200,5," ".$cust_addr3,0,0);
					$pdf->ln();
				}
				//$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(10,$dtl_ht, '', 0, 0,'C');
				$pdf->Cell(40,$dtl_ht, 'Quantity', 0, 0,'C');
				$pdf->Cell(60,$dtl_ht, '', 0, 0,'C');
				$pdf->Cell(60,$dtl_ht, '', 0, 0,'C');
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'UPC', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(10,$dtl_ht, 'U/M', 0, 0);
				$pdf->Cell(20,$dtl_ht, '---Reg.', 0, 0,'R');
				$pdf->Cell(20,$dtl_ht, 'Free---', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Unit Price', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Extndd Amt', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Disc Amt', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Net Amt', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$x_amt_total_per_page=0;
				$disc_amt_total_per_page=0;
				$net_amt_total_per_page=0;
				$last_page_pos=0;
				if ($tmp_rec <= 0) { /// 1 page consume
					$tmp_last=($new_page*$m_line) + $tmp_rec;
					$last_page_pos=$m_line - ($tmp_last -$tmp_first);
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									$grid_sku=mssql_result($result_ci_detail,$i,"prdNumber");
									$qryUpc = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku");
									$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
									$grid_unit_price=mssql_result($result_ci_detail,$i,"ciUnitPrice");
									$grid_ext_amt=mssql_result($result_ci_detail,$i,"ciExtAmt");
									$grid_disc_amt=mssql_result($result_ci_detail,$i,"ciDiscAmt");
									$grid_reg_pak=mssql_result($result_ci_detail,$i,"qtyRegPk");
									$grid_free_pak=mssql_result($result_ci_detail,$i,"qtyFreePk");
									$grid_qty_reg_pc=mssql_result($result_ci_detail,$i,"qtyRegPc");
									$grid_qty_free_pc=mssql_result($result_ci_detail,$i,"qtyFreePc");
									$grid_conv=mssql_result($result_ci_detail,$i,"prdConv");
									///// get prdDesc,prdBuyUnit,prdConv from table tblProdMast....
									$query_product="SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku";
									$result_product=mssql_query($query_product);
									$num_product = mssql_num_rows($result_product);
									if ($num_product >0) {
										$grid_desc=mssql_result($result_product,0,"prdDesc");
										$grid_sell=mssql_result($result_product,0,"prdSellUnit");
									} else {
										$grid_desc="NA";
										$grid_buy="NA";
										$grid_sell="NA";
										$grid_conv="NA";
									}
									$grid_qty_reg = ($grid_reg_pak*$grid_conv) + $grid_qty_reg_pc;
									$grid_qty_free = ($grid_free_pak*$grid_conv) + $grid_qty_free_pc;
									$net_amt=$grid_ext_amt - $grid_disc_amt;
									##################################################################
									$x_amt_total_per_page=$x_amt_total_per_page+$grid_ext_amt;       #
									$disc_amt_total_per_page=$disc_amt_total_per_page+$grid_disc_amt;#
									$net_amt_total_per_page=$net_amt_total_per_page+$net_amt;        #
									$x_amt_grand_total=$x_amt_grand_total+$grid_ext_amt;             #
									$disc_amt_grand_total=$disc_amt_grand_total+$grid_disc_amt;      #
									$net_amt_grand_total=$net_amt_grand_total+$net_amt;              #
									##################################################################
									$grid_unit_price = number_format($grid_unit_price, 2);        
									$grid_ext_amt = number_format($grid_ext_amt, 2);              
									$grid_disc_amt = number_format($grid_disc_amt, 2);            
									$net_amt = number_format($net_amt, 2);                        
									$grid_qty_reg = number_format($grid_qty_reg, 0);              
									$grid_qty_free = number_format($grid_qty_free, 0);            
									$grid_conv = number_format($grid_conv, 0);                    
							
									$pdf->Cell(20,$dtl_ht, $grid_sku, 0, 0);
									$pdf->Cell(30,$dtl_ht, $upcCode, 0, 0);
									$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
									$grid_conv=$grid_conv."/".$grid_sell;
									$pdf->Cell(10,$dtl_ht, $grid_sell, 0, 0);
									$pdf->Cell(20,$dtl_ht, $grid_qty_reg, 0, 0,'R');
									$pdf->Cell(20,$dtl_ht, $grid_qty_free, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_unit_price, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_ext_amt, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_disc_amt, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $net_amt, 0, 0,'R');
									$pdf->ln();	
						$i++;
					} 
					//$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0, 0,'C');	
				} else {            /// more than 1 page consume
					$tmp_last_more=$new_page*$m_line;
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
									$grid_sku=mssql_result($result_ci_detail,$i,"prdNumber");
									$qryUpc = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku");
									$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
									$grid_unit_price=mssql_result($result_ci_detail,$i,"ciUnitPrice");
									$grid_ext_amt=mssql_result($result_ci_detail,$i,"ciExtAmt");
									$grid_disc_amt=mssql_result($result_ci_detail,$i,"ciDiscAmt");
									$grid_reg_pak=mssql_result($result_ci_detail,$i,"qtyRegPk");
									$grid_free_pak=mssql_result($result_ci_detail,$i,"qtyFreePk");
									$grid_qty_reg_pc=mssql_result($result_ci_detail,$i,"qtyRegPc");
									$grid_qty_free_pc=mssql_result($result_ci_detail,$i,"qtyFreePc");
									$grid_conv=mssql_result($result_ci_detail,$i,"prdConv");
									///// get prdDesc,prdBuyUnit,prdConv from table tblProdMast....
									$query_product="SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku";
									$result_product=mssql_query($query_product);
									$num_product = mssql_num_rows($result_product);
									if ($num_product >0) {
										$grid_desc=mssql_result($result_product,0,"prdDesc");
										$grid_sell=mssql_result($result_product,0,"prdSellUnit");
									} else {
										$grid_desc="NA";
										$grid_buy="NA";
										$grid_sell="NA";
										$grid_conv="NA";
									}
									$grid_qty_reg = ($grid_reg_pak*$grid_conv) + $grid_qty_reg_pc;
									$grid_qty_free = ($grid_free_pak*$grid_conv) + $grid_qty_free_pc;
									$net_amt=$grid_ext_amt - $grid_disc_amt;
									##################################################################
									$x_amt_total_per_page=$x_amt_total_per_page+$grid_ext_amt;       #
									$disc_amt_total_per_page=$disc_amt_total_per_page+$grid_disc_amt;#
									$net_amt_total_per_page=$net_amt_total_per_page+$net_amt;        #
									$x_amt_grand_total=$x_amt_grand_total+$grid_ext_amt;             #
									$disc_amt_grand_total=$disc_amt_grand_total+$grid_disc_amt;      #
									$net_amt_grand_total=$net_amt_grand_total+$net_amt;              #
									##################################################################
									$grid_unit_price = number_format($grid_unit_price, 2);        
									$grid_ext_amt = number_format($grid_ext_amt, 2);              
									$grid_disc_amt = number_format($grid_disc_amt, 2);            
									$net_amt = number_format($net_amt, 2);                        
									$grid_qty_reg = number_format($grid_qty_reg, 0);              
									$grid_qty_free = number_format($grid_qty_free, 0);            
									$grid_conv = number_format($grid_conv, 0);                    
							
									$pdf->Cell(20,$dtl_ht, $grid_sku, 0, 0);
									$pdf->Cell(30,$dtl_ht, $upcCode, 0, 0);
									$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
									$grid_conv=$grid_conv."/".$grid_sell;
									$pdf->Cell(10,$dtl_ht, $grid_sell, 0, 0);
									$pdf->Cell(20,$dtl_ht, $grid_qty_reg, 0, 0,'R');
									$pdf->Cell(20,$dtl_ht, $grid_qty_free, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_unit_price, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_ext_amt, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_disc_amt, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $net_amt, 0, 0,'R');
									$pdf->ln();
						$i++;
					}
				}
				###################### P A G E  F O O T E R ##########################
				if ($m_page > 1) {
					$x_amt_total_per_page = number_format($x_amt_total_per_page, 2);        #
					$disc_amt_total_per_page = number_format($disc_amt_total_per_page, 2);  #
					$net_amt_total_per_page = number_format($net_amt_total_per_page, 2);
					$pdf->ln();
					$pdf->Cell(20,$dtl_ht, '', 0, 0);
					$pdf->Cell(30,$dtl_ht, '', 0, 0);
					$pdf->Cell(30,$dtl_ht, '', 0, 0);
					$pdf->Cell(60,$dtl_ht, $details_per_page, 0, 0);
					$pdf->Cell(10,$dtl_ht, '', 0, 0,'R');
					$pdf->Cell(20,$dtl_ht, '', 0, 0,'R');
					$pdf->Cell(50,$dtl_ht, 'Page Total', 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $x_amt_total_per_page, 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $disc_amt_total_per_page, 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $net_amt_total_per_page, 0, 0,'R');
					$pdf->ln();
					$pdf->ln();
					if ($tmp_rec > 0) { /// 1 page consume
						$pdf->Cell($m_width,$dtl_ht, $page, 0, 0,'R');
					}
				}
				if ($tmp_rec <= 0) { /// 1 page consume
					###################### R E P O R T  F O O T E R #########################
					$pdf->ln();
					$x_amt_grand_total = number_format($x_amt_grand_total, 2);              #
					$disc_amt_grand_total = number_format($disc_amt_grand_total, 2);        #
					$net_amt_grand_total = number_format($net_amt_grand_total, 2);   
					$pdf->Cell(20,$dtl_ht, '', 0, 0);
					$pdf->Cell(30,$dtl_ht, '', 0, 0);
					$pdf->Cell(30,$dtl_ht, '', 0, 0);
					$pdf->Cell(60,$dtl_ht, $details_total, 0, 0);
					$pdf->Cell(10,$dtl_ht, '', 0, 0,'R');
					$pdf->Cell(20,$dtl_ht, '', 0, 0,'R');
					$pdf->Cell(50,$dtl_ht, 'Invoice Total', 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $x_amt_grand_total, 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $disc_amt_grand_total, 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $net_amt_grand_total, 0, 0,'R');
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$pdf->Cell($m_width_2_fields,$dtl_ht,"Approved for Release : __________________ Date : __________", 0, 0);
					$pdf->Cell($m_width_2_fields,$dtl_ht, "Received By : __________________ Date : __________", 0, 0,'R');
					$pdf->ln();
					$pdf->Cell($m_width_2_fields,$dtl_ht, "(Sign Over Printed Name)", 0, 0);
					$pdf->Cell($m_width_2_fields,$dtl_ht, "                       (Sign Over Printed Name)", 0, 0);
					##########################################################
					//for ($c=1;$c < ($last_page_pos - 8); $c++){
					//	$pdf->ln();
					//}
					$pdf->ln();
					$pdf->ln();
					$printed_by = "Prepared By : ".$user_first_last;
					$pdf->Cell($m_width_2_fields,$dtl_ht, $printed_by, 0, 0);
					$pdf->Cell($m_width_2_fields,$dtl_ht, $page, 0, 0,'R');               
					##########################################################
				}
			}
		}
	}
	$pdf->Output();
?>
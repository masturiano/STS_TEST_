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
		
	$t_trans_header=$_POST['hide_num_trans_header'];
	$hide_from_date=$_POST['hide_from_date'];
	$hide_to_date=$_POST['hide_to_date'];
	$from_to = "Report Date from ".$hide_from_date." to ".$hide_to_date;
	
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '10');
	$dtl_ht=4;
	$m_width=255;
	$m_width_3_fields=85;
	$m_line = 30;  ///maximum line
	
	for ($a=0;$a<$t_trans_header;$a++){ 
		$new_page=0;
		if(isset($_POST["check$a"])) {
			$check_trans_no=$_POST["check$a"];
			$query_trans_header="SELECT * FROM tblTransferHeader WHERE (trfNumber = $check_trans_no)";
			$result_trans_header=mssql_query($query_trans_header);
			$comp_code=mssql_result($result_trans_header,0,"compCode");
			$transfer_no=mssql_result($result_trans_header,0,"trfNumber");
			$status=mssql_result($result_trans_header,0,"trfStatus");
			if($status=="R"){
				$title="Transfer Slip (Released)";
			} else {
				$title="Transfer Slip (Unrelease)";
			}
			
			$date=mssql_result($result_trans_header,0,"trfDate");
			$date = new DateTime($date);
			$date = $date->format("m-d-Y");
			$date="Date Transferred : ".$date;
			$from_loc=mssql_result($result_trans_header,0,"fromLocCode");
			$to_loc=mssql_result($result_trans_header,0,"toLocCode");
			$remarks=mssql_result($result_trans_header,0,"trfRemarks");
			$received_date=mssql_result($result_trans_header,0,"trfRcvdDte");
			$responsible=mssql_result($result_trans_header,0,"trfResponsible");
			$result_person=mssql_query("SELECT * FROM tblUsers WHERE userid = $responsible");
			$num_person = mssql_num_rows($result_person);
			if ($num_person >0) {
				$responsible=mssql_result($result_person,0,"firstName")." ".mssql_result($result_person,0,"lastName");
			} else {
				$company_name="NA";
			}
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
			///// get to_loc from locName from table tblLocation....
			$query_to_loc="SELECT * FROM tblLocation WHERE locCode = $to_loc";
			$result_to_loc=mssql_query($query_to_loc);
			$num_to_loc = mssql_num_rows($result_to_loc);
			if ($num_from_loc >0) {
				$to_loc_name=mssql_result($result_to_loc,0,"locName");
			} else {
				$to_loc_name="NA";
			}
			$to_loc=$to_loc." - ".$to_loc_name;
			$m_page=0; $tmp_first=0; $tmp_last=0; $tmp_rec=0; 
			$query_trans_detail="SELECT * FROM tblTransferDtl WHERE (trfNumber = $check_trans_no) ORDER BY upcCode";
			$result_trans_detail=mssql_query($query_trans_detail);
			$num_trans_detail = mssql_num_rows($result_trans_detail);
			$details_total="No.of Items = ".$num_trans_detail;
			$m_page=$num_trans_detail / $m_line;
			$m_page=ceil($m_page); //// maximum page
			$tmp_first=0;          //// temporary first record
			$tmp_last=0;           //// temporary last record
			$tmp_rec=$num_trans_detail; //// temporary total record
			$new_page=0;
			$x_cost_grand_total=0;
			$x_price_grand_total=0;
			$grand_total_out=0;
			$grand_total_un=0;
			for ($b=0;$b<$m_page;$b++){
				$pdf->AddPage();
				$new_page++;
				$tmp_rec=$tmp_rec - $m_line;
				$tmp_first= ($new_page * $m_line) - ($m_line - 1);
				if ($tmp_rec <= 0) { /// 1 page consume
					$tmp_last=($new_page*$m_line) + $tmp_rec;
					$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_trans_detail." record/s";
					$details_per_page=($tmp_last+1) - $tmp_first;
					$details_per_page="No.of Items = ".$details_per_page;
					$page="Page ".$new_page." of ".$m_page;
				} else {
					$tmp_last_more=$new_page*$m_line;
					$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_trans_detail." record/s";
					$details_per_page=($tmp_last_more+1) - $tmp_first;
					$details_per_page="No.of Items = ".$details_per_page;
					$page="Page ".$new_page." of ".$m_page;
				}
				##################### P A G E   H E A D E R ########################
				$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
				$pdf->Cell($m_width_3_fields,5,$company_name,0,0,'C');
				$TRF="TRF ".$transfer_no;
				$pdf->Cell($m_width_3_fields,5,$TRF,0,1,'R');
				$pdf->Cell($m_width_3_fields,5,"Report ID : TRF001P",0,0);
				$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
				$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
				if ($b==0) {
					$pdf->ln();
					$pdf->Cell(33,5,'From Location : ',0,0);
					$pdf->Cell(50,5,$from_loc,0,0);
					$pdf->Cell($m_width_3_fields,5,"",0,0,'C');
					$pdf->Cell($m_width_3_fields,5,$date,0,1,'R');
					$pdf->Cell(33,5,'To Location : ',0,0);
					$pdf->Cell(50,5,$to_loc,0,0);
					$pdf->ln();
				}
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(60,$dtl_ht, '', 0, 0);
				$pdf->Cell(40,$dtl_ht, '-----U/M-----', 0, 0,'C');
				$pdf->Cell(40,$dtl_ht, '---Total Units---', 0, 0,'C');
				$pdf->Cell(60,$dtl_ht, '-----------Cost-----------', 0, 0,'C');
				//$pdf->Cell(60,$dtl_ht, '----------Price-----------', 0, 0,'C');
				$pdf->ln();
				$pdf->Cell(30,$dtl_ht, 'UPC', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(60,$dtl_ht, 'UPC Description', 0, 0);
				$pdf->Cell(20,$dtl_ht, 'Buy', 0, 0,'C');
				//$pdf->Cell(20,$dtl_ht, 'Sell', 0, 0,'C');
				$pdf->Cell(20,$dtl_ht, 'Conv/Sell', 0, 0,'C');
				$pdf->Cell(20,$dtl_ht, 'Trnsfrd', 0, 0,'R');
				$pdf->Cell(20,$dtl_ht, 'Recvd', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Unit', 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, 'Extended', 0, 0,'R');
				//$pdf->Cell(30,$dtl_ht, 'Unit', 0, 0,'R');
				//$pdf->Cell(30,$dtl_ht, 'Extended', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$x_cost_total_per_page=0;
				$x_price_total_per_page=0;
				$total_out=0;
				$total_un=0;
				$last_page_pos=0;
				if ($tmp_rec <= 0) { /// 1 page consume
					$tmp_last=($new_page*$m_line) + $tmp_rec;
					$last_page_pos=$m_line - ($tmp_last -$tmp_first);
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;
									$grid_upc=mssql_result($result_trans_detail,$i,"upcCode");
									$result_upc=mssql_query("SELECT * FROM tblUpc WHERE upcCode = $grid_upc");
									$grid_upc_desc=mssql_result($result_upc,0,"upcDesc");
									$grid_upc_desc = str_replace("\\","",$grid_upc_desc);
									
									$grid_sku=mssql_result($result_trans_detail,$i,"prdNumber");
									$grid_um_code=mssql_result($result_trans_detail,$i,"umCode");
									$grid_qty_out=mssql_result($result_trans_detail,$i,"trfQtyOut");
									$grid_qty_in=mssql_result($result_trans_detail,$i,"trfQtyIn");
									$grid_cost=mssql_result($result_trans_detail,$i,"trfCost");
									$grid_price=mssql_result($result_trans_detail,$i,"trfPrice");
									$grid_x_cost=$grid_qty_in * $grid_cost;
									$grid_x_price=$grid_qty_in * $grid_price;
									$x_cost_total_per_page=$x_cost_total_per_page+$grid_x_cost;
									$x_price_total_per_page=$x_price_total_per_page+$grid_x_price;
									$x_cost_grand_total=$x_cost_grand_total+$grid_x_cost;
									$x_price_grand_total=$x_price_grand_total+$grid_x_price;
									$total_out=$total_out+$grid_qty_out;
									$total_un=$total_un+$grid_qty_in;
									$grand_total_out=$grand_total_out+$grid_qty_out;
									$grand_total_un=$grand_total_un+$grid_qty_in;
									///// get prdDesc,prdBuyUnit,prdConv from table tblProdMast....
									$query_product="SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku";
									$result_product=mssql_query($query_product);
									$num_product = mssql_num_rows($result_product);
									if ($num_product >0) {
										$grid_desc=mssql_result($result_product,0,"prdDesc");
										$grid_buy=mssql_result($result_product,0,"prdBuyUnit");
										$grid_sell=mssql_result($result_product,0,"prdSellUnit");
										$grid_conv=mssql_result($result_product,0,"prdConv");
									} else {
										$grid_desc="NA";
										$grid_buy="NA";
										$grid_sell="NA";
										$grid_conv="NA";
									}
									$grid_conv = number_format($grid_conv, 0);   
									$grid_qty_out = number_format($grid_qty_out, 0); 
									$grid_qty_in = number_format($grid_qty_in, 0); 
									$grid_cost = number_format($grid_cost, 2); 
									$grid_price = number_format($grid_price, 2); 
									$grid_x_cost = number_format($grid_x_cost, 2); 
									$grid_x_price = number_format($grid_x_price, 2); 
									
									$pdf->Cell(30,$dtl_ht, $grid_upc, 0, 0);
									$pdf->Cell(20,$dtl_ht, $grid_sku, 0, 0);
									$pdf->Cell(60,$dtl_ht, $grid_upc_desc, 0, 0);
									$pdf->Cell(20,$dtl_ht, $grid_buy, 0, 0,'C');
									//$pdf->Cell(20,$dtl_ht, $grid_sell, 0, 0,'C');
									$grid_conv=$grid_conv."/".$grid_sell;
									$pdf->Cell(20,$dtl_ht, $grid_conv, 0, 0,'C');
									$pdf->Cell(20,$dtl_ht, $grid_qty_out, 0, 0,'R');
									$pdf->Cell(20,$dtl_ht, $grid_qty_in, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_cost, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_x_cost, 0, 0,'R');
									//$pdf->Cell(30,$dtl_ht, $grid_price, 0, 0,'R');
									//$pdf->Cell(30,$dtl_ht, $grid_x_price, 0, 0,'R');
									$pdf->ln();	
						$i++;
					} 
					//$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0, 0,'C');	
				} else {            /// more than 1 page consume
					$tmp_last_more=$new_page*$m_line;
					for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
						$i--;
									$grid_upc=mssql_result($result_trans_detail,$i,"upcCode");
									$result_upc=mssql_query("SELECT * FROM tblUpc WHERE upcCode = $grid_upc");
									$grid_upc_desc=mssql_result($result_upc,0,"upcDesc");
									$grid_upc_desc = str_replace("\\","",$grid_upc_desc);
									
									$grid_sku=mssql_result($result_trans_detail,$i,"prdNumber");
									$grid_um_code=mssql_result($result_trans_detail,$i,"umCode");
									$grid_qty_out=mssql_result($result_trans_detail,$i,"trfQtyOut");
									$grid_qty_in=mssql_result($result_trans_detail,$i,"trfQtyIn");
									$grid_cost=mssql_result($result_trans_detail,$i,"trfCost");
									$grid_price=mssql_result($result_trans_detail,$i,"trfPrice");
									$grid_x_cost=$grid_qty_in * $grid_cost;
									$grid_x_price=$grid_qty_in * $grid_price;
									$x_cost_total_per_page=$x_cost_total_per_page+$grid_x_cost;
									$x_price_total_per_page=$x_price_total_per_page+$grid_x_price;
									$x_cost_grand_total=$x_cost_grand_total+$grid_x_cost;
									$x_price_grand_total=$x_price_grand_total+$grid_x_price;
									$total_out=$total_out+$grid_qty_out;
									$total_un=$total_un+$grid_qty_in;
									$grand_total_out=$grand_total_out+$grid_qty_out;
									$grand_total_un=$grand_total_un+$grid_qty_in;
									///// get prdDesc,prdBuyUnit,prdConv from table tblProdMast....
									$query_product="SELECT * FROM tblProdMast WHERE prdNumber = $grid_sku";
									$result_product=mssql_query($query_product);
									$num_product = mssql_num_rows($result_product);
									if ($num_product >0) {
										$grid_desc=mssql_result($result_product,0,"prdDesc");
										$grid_buy=mssql_result($result_product,0,"prdBuyUnit");
										$grid_sell=mssql_result($result_product,0,"prdSellUnit");
										$grid_conv=mssql_result($result_product,0,"prdConv");
									} else {
										$grid_desc="NA";
										$grid_buy="NA";
										$grid_sell="NA";
										$grid_conv="NA";
									}
									$grid_conv = number_format($grid_conv, 0);   
									$grid_qty_out = number_format($grid_qty_out, 0); 
									$grid_qty_in = number_format($grid_qty_in, 0); 
									$grid_cost = number_format($grid_cost, 2); 
									$grid_price = number_format($grid_price, 2); 
									$grid_x_cost = number_format($grid_x_cost, 2); 
									$grid_x_price = number_format($grid_x_price, 2); 
					
									$pdf->Cell(30,$dtl_ht, $grid_upc, 0, 0);
									$pdf->Cell(20,$dtl_ht, $grid_sku, 0, 0);
									$pdf->Cell(60,$dtl_ht, $grid_upc_desc, 0, 0);
									$pdf->Cell(20,$dtl_ht, $grid_buy, 0, 0,'C');
									//$pdf->Cell(20,$dtl_ht, $grid_sell, 0, 0,'C');
									$grid_conv=$grid_conv."/".$grid_sell;
									$pdf->Cell(20,$dtl_ht, $grid_conv, 0, 0,'C');
									$pdf->Cell(20,$dtl_ht, $grid_qty_out, 0, 0,'R');
									$pdf->Cell(20,$dtl_ht, $grid_qty_in, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_cost, 0, 0,'R');
									$pdf->Cell(30,$dtl_ht, $grid_x_cost, 0, 0,'R');
									//$pdf->Cell(30,$dtl_ht, $grid_price, 0, 0,'R');
									//$pdf->Cell(30,$dtl_ht, $grid_x_price, 0, 0,'R');
									$pdf->ln();
						$i++;
					}
				}
				###################### P A G E  F O O T E R ##########################
				if ($m_page > 1) {
					$x_cost_total_per_page = number_format($x_cost_total_per_page, 2);
					$x_price_total_per_page = number_format($x_price_total_per_page, 2);
					$total_out = number_format($total_out); 
					$total_un = number_format($total_un); 
					$pdf->ln();
					$pdf->Cell(20,$dtl_ht, $details_per_page, 0, 0);
					$pdf->Cell(120,$dtl_ht, '', 0, 0);
					$pdf->Cell(10,$dtl_ht, 'Page Total', 0, 0);
					$pdf->Cell(20,$dtl_ht, $total_out, 0, 0,'R');
					$pdf->Cell(20,$dtl_ht, $total_un, 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $x_cost_total_per_page, 0, 0,'R');
					//$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
					//$pdf->Cell(30,$dtl_ht, $x_price_total_per_page, 0, 0,'R');
					$pdf->ln();
				}
				if ($tmp_rec <= 0) { /// 1 page consume
					###################### R E P O R T  F O O T E R #########################
					$x_cost_grand_total = number_format($x_cost_grand_total, 2);
					$x_price_grand_total = number_format($x_price_grand_total, 2);
					$grand_total_out = number_format($grand_total_out); 
					$grand_total_un = number_format($grand_total_un); 
					$pdf->ln();
					$pdf->Cell(20,$dtl_ht, $details_total, 0, 0);
					$pdf->Cell(120,$dtl_ht, '', 0, 0);
					$pdf->Cell(10,$dtl_ht, 'Total', 0, 0);
					$pdf->Cell(20,$dtl_ht, $grand_total_out, 0, 0,'R');
					$pdf->Cell(20,$dtl_ht, $grand_total_un, 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
					$pdf->Cell(30,$dtl_ht, $x_cost_grand_total, 0, 0,'R');
					//$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
					//$pdf->Cell(30,$dtl_ht, $x_price_grand_total, 0, 0,'R');
					$pdf->ln();
					$pdf->ln();
					$pdf->ln();
					$remarks="Remarks : ".$remarks;
					$pdf->Cell(155,$dtl_ht, $remarks, 0, 1);
					$pdf->Cell(100,$dtl_ht, '', 0, 0);
					$responsible="Transferred By : ".$responsible;
					$pdf->Cell(100,$dtl_ht, $responsible, 0, 0);
					$date = new DateTime($received_date);
					$received_date = $date->format("m-d-Y");
					$received_date="Date : ".$received_date;
					$pdf->Cell(100,$dtl_ht, $received_date, 0, 0);
					$pdf->ln();
					$pdf->Cell(100,$dtl_ht, '', 0, 0);
					$received_by="Received By :    ________________";
					$pdf->Cell(100,$dtl_ht, $received_by, 0, 0);
					$date_received="Date : __________";
					$pdf->Cell(100,$dtl_ht, $date_received, 0, 0);
					###########################################################
					//for ($c=1;$c < ($last_page_pos - 8); $c++){
					//	$pdf->ln();
					//}
					$pdf->ln();
					$pdf->ln();
					$printed_by = "Prepared By : ".$user_first_last;
					$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
					###########################################################
				}
			}
		}
	}
	$pdf->Output();
?>
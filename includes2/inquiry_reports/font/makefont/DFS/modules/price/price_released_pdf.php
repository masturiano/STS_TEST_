<?php
	$gmt = time() + (8 * 100 * 100);
	$newdate = date("m/d/Y", $gmt);
	$newdate="Run Date : ".$newdate;
	$E_No = $_GET['EventNo'];
	include "../../functions/inquiry_session.php";
	require('../inventory/lbd_function.php');
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";	
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$db = new DB;
	$db->connect();
	
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	$query_event_date = mssql_query("SELECT * FROM tblPriceEventHeader WHERE prEventNumber = '$E_No'");
	$num_event_date = mssql_num_rows($query_event_date);
	if ($num_event_date>0) {
		$event_date=mssql_result($query_event_date,0,"prDocDte");
		if ($event_date=="") {
			$event_date = "";
		} else {
			$date = new DateTime($event_date);
			$event_date = $date->format("m/d/Y");
		}
		$event_date = "Doc.Date : " . $event_date;
	} else {
		$event_date="";
	}
	
	$strCtr = "SELECT * FROM VIEWRELEASEPRICEEVENTS WHERE PREVENTNUMBER = '$E_No'";
	$result_loc1 = mssql_query($strCtr);
	$num_loc = mssql_num_rows($result_loc1);
	if ($num_loc>0) {
		$Desc=mssql_result($result_loc1,0,3);
		$Start=mssql_result($result_loc1,0,1);
		$End=mssql_result($result_loc1,0,2);
	} 
	$strSQL = "SELECT * FROM VIEWRELEASEPRICEEVENTSDETAILS WHERE PREVENTNUMBER = '$E_No' ORDER BY PRDDESC";
	$result_loc = mssql_query($strSQL);
	$num_loc = mssql_num_rows($result_loc);
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$m_line=25;
	$dtl_ht=5.2;
	$max_tot_line=30;
	$m_width=255;
	$m_width_3_fields=85;
	$m_width_2_fields=127;
	$font="Courier";
	$m_page=$num_loc / $m_line;
	$aaa = split("\.",$m_page);
	$aaaa = ".".$aaa[1];
	$last_excempt = $aaaa * 25;
	echo $aaaa;
	$m_page=ceil($m_page); //// maximum page	
	$flag=0; 
	if ($last_excempt>24 || $last_excempt==0){
		$m_page++;
		$flag=1;
	}
	//$m_page=3;
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_loc; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_loc." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_loc." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,"Event No. : " . $E_No,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : EVN006P",0,0);
		$pdf->Cell($m_width_3_fields,5,"Price Event List",0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		if ($j==1) {
			$pdf->ln();
			$pdf->Cell(200,5,"Event Description : ".$Desc,0,0);
			$pdf->Cell($m_width_2_fields,5,$event_date,0,1);
			$pdf->Cell(200,5,"",0,0);
			$pdf->Cell($m_width_2_fields,5,"Start Date : ".$Start,0,1);
			$pdf->Cell(200,5,"",0,0);
			$pdf->Cell($m_width_2_fields,5,"End Date : ".$End,0,1);
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
				$pdf->Cell(15,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(15,$dtl_ht, 'Sell', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0,'R');
				$pdf->Cell(35,$dtl_ht, '', 0, 0,'R');
				$pdf->Cell(60,$dtl_ht, '', 0, 0,'R');
				$pdf->ln();
				$pdf->Cell(15,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'UPC', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(15,$dtl_ht, 'UM', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Old Price', 0, 0,'R');
				$pdf->Cell(35,$dtl_ht, 'New Price', 0, 0,'R');
				$pdf->Cell(35,$dtl_ht, 'Average Cost', 0, 0,'R');
			
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		$pageTotal = 0;
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=10; $g++) {
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;
						$SKU = $rstSQL[1];
		$UPC = $rstSQL[3];
		$ProdDesc = $rstSQL[2];
		$ProdDesc = str_replace("\\","",$ProdDesc);
		$rstSQL[5]=number_format( $rstSQL[5],0);
		$Um = $rstSQL[4];
		$OldPrice = $rstSQL[6];
		if ($OldPrice>0) {
			$OldPrice=number_format( $OldPrice,2);
		} else {
			$OldPrice="";
		}
		$NewPrice = $rstSQL[7];
		if ($NewPrice>0) {
			$NewPrice=number_format( $NewPrice,2);
		} else {
			$NewPrice="";
		}
		$AveCost = $rstSQL[8];
		if ($AveCost>0) {
			$AveCost=number_format( $AveCost,4);
		} else {
			$AveCost="";
		}						
											$SKU=mssql_result($result_loc,$i,"prdNumber");
											$UPC=mssql_result($result_loc,$i,"prdSuppItem");
											$ProdDesc=mssql_result($result_loc,$i,"prdDesc");
											$ProdDesc = str_replace("\\","",$ProdDesc);
											$conv=number_format(mssql_result($result_loc,$i,"prdConv"),0);
											$Um = mssql_result($result_loc,$i,"umCode"). "/". $conv;
											$OldPrice=mssql_result($result_loc,$i,"prOldPrice");
											if ($OldPrice > 0) {
												$OldPrice=number_format($OldPrice,2);
											} else {
												$OldPrice="";
											}
											$NewPrice=mssql_result($result_loc,$i,"prNewPrice");
											if ($NewPrice>0) {
												$NewPrice=number_format( $NewPrice,2);
											} else {
												$NewPrice="";
											}
											$AveCost=mssql_result($result_loc,$i,"aveUnitCost");
											if ($AveCost>0) {
												$AveCost=number_format( $AveCost,4);
											} else {
												$AveCost="";
											}
													
											$pdf->Cell(15,$dtl_ht, $SKU, 0, 0);
											$pdf->Cell(30,$dtl_ht, $UPC, 0, 0);
											$pdf->Cell(90,$dtl_ht, $ProdDesc, 0, 0);
											$pdf->Cell(15,$dtl_ht, $Um, 0, 0);
											$pdf->Cell(30,$dtl_ht, $OldPrice, 0, 0,'R');
											$pdf->Cell(35,$dtl_ht, $NewPrice, 0, 0,'R');
											$pdf->Cell(35,$dtl_ht, $AveCost, 0, 0,'R');
											$pdf->ln();
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
											$SKU=mssql_result($result_loc,$i,"prdNumber");
											$UPC=mssql_result($result_loc,$i,"prdSuppItem");
											$ProdDesc=mssql_result($result_loc,$i,"prdDesc");
											$ProdDesc = str_replace("\\","",$ProdDesc);
											$conv=number_format(mssql_result($result_loc,$i,"prdConv"),0);
											$Um = mssql_result($result_loc,$i,"umCode"). "/". $conv;
											$OldPrice=mssql_result($result_loc,$i,"prOldPrice");
											if ($OldPrice > 0) {
												$OldPrice=number_format($OldPrice,2);
											} else {
												$OldPrice="";
											}
											$NewPrice=mssql_result($result_loc,$i,"prNewPrice");
											if ($NewPrice>0) {
												$NewPrice=number_format( $NewPrice,2);
											} else {
												$NewPrice="";
											}
											$AveCost=mssql_result($result_loc,$i,"aveUnitCost");
											if ($AveCost>0) {
												$AveCost=number_format( $AveCost,4);
											} else {
												$AveCost="";
											}
													
											$pdf->Cell(15,$dtl_ht, $SKU, 0, 0);
											$pdf->Cell(30,$dtl_ht, $UPC, 0, 0);
											$pdf->Cell(90,$dtl_ht, $ProdDesc, 0, 0);
											$pdf->Cell(15,$dtl_ht, $Um, 0, 0);
											$pdf->Cell(30,$dtl_ht, $OldPrice, 0, 0,'R');
											$pdf->Cell(35,$dtl_ht, $NewPrice, 0, 0,'R');
											$pdf->Cell(35,$dtl_ht, $AveCost, 0, 0,'R');
											$pdf->ln();
						$i++;
					}
					break;
				} 
				$m_line=$m_line - 1;
			} 
		}
		###################### P A G E  F O O T E R ##########################
		if ($m_page > 1 && $flag != 2) {
			/*if ($j >= $m_page) {
				$pdf->Cell(23,$dtl_ht, "" , 0, 0);	
			}
			$pdf->ln();
			$pageTotal = number_format($pageTotal,4);
			$pdf->Cell(183,$dtl_ht, "" , 0, 0);
			$pdf->Cell(23,$dtl_ht, "Page Total : " , 0, 0);
			$pdf->Cell(103,$dtl_ht, $pageTotal, 0, 0,'R');
			$pdf->ln();*/
		}
		
		###################### R E P O R T  F O O T E R #########################
		if ($tmp_rec <= 0 && $j >= $m_page) { /// 1 page consume $j >=$m_page &&  $last_excempt < 8
			/*if ($flag==1) {
				include "po_pdf_total.php";
				include "po_pdf_footer.php";
			}
			if ($flag==2) {
				$pdf->Cell(103,$dtl_ht, '', 0, 0,'R');
				if ($j <$m_page) {
					include "po_pdf_total.php";
				} else {
					include "po_pdf_footer.php";
				}			
			}
			if ($flag==3) {
				if ($j >=$m_page) {
					$pdf->Cell(103,$dtl_ht, '', 0, 0,'R');
					include "po_pdf_total.php";
					include "po_pdf_footer.php";
				}			
			}
			*/
			$pdf->ln();
			if ($flag==1) {
				$pdf->Cell($m_width,$dtl_ht, '', 0,1,'C');
			}
			$pdf->Cell($m_width,$dtl_ht, '* * * END OF REPORT * * *', 0,1,'C');
			$pdf->ln();
			
			$pdf->Cell(1,$dtl_ht, "Total Number of Items : ".$num_loc, 0, 1);
			$pdf->ln();
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	//echo $num_loc;
	$pdf->Output();
?>
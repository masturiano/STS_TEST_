<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../etc/etc.obj.php";
	require_once "../../functions/inquiry_function.php";
	//require_once "prod_inquiry_trans.php";
	$db = new DB;
	$db->connect();
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$newdate="Run Date : ".$newdate;
	$search_selection=$_POST['hide_search_selection'];
	if ($search_selection=="") {
		$search_selection="by_vendor";
	}
	############################# dont forget to get the company code ##################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	
	switch ($search_selection) {
		case "by_vendor":
			$pdf = new FPDF('P', 'mm', 'LETTER');
			$dtl_ht=4;
			$max_tot_line=75;
			$m_line = 52;  ///maximum line
			$m_width=200;
			$m_width_3_fields=66;
			$box_supplier=$_POST['hide_box_supplier'];
			$split=split("-",$box_supplier);
			$box_supplier= $split[0]."-".$split[1];
			$box_supplier_code=getCodeofString($box_supplier); ///pick in inventory_inquiry_function.php
			$box_supplier_code=trim($box_supplier_code);
			if ($box_supplier_code=="All") {
				$supplier="";
				$title = "Product Listing (By Vendor)";
			} else {
				$supplier=" AND (tblProdMast.suppCode=$box_supplier_code) ";
				$title = "Product Listing By Vendor";
				$title2 = "(".$box_supplier.")";
			}
			
			$query_product="SELECT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdSubClsCode, tblProdMast.prdSellUnit, 
                      		tblProdMast.prdBuyUnit, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdSuppItem, tblProdMast.prdSetTag, 
                      		tblSuppliers.suppName
							FROM tblProdMast INNER JOIN
                      		tblSuppliers ON tblProdMast.suppCode = tblSuppliers.suppCode
							WHERE (tblProdMast.prdDelTag = 'A') $supplier
							ORDER BY tblSuppliers.suppName,tblProdMast.prdDesc";
			break;
		case "by_group":
			$pdf = new FPDF('P', 'mm', 'LETTER');
			$dtl_ht=4;
			##############################
			$max_tot_line=1000; //76 55
			$m_line = 1000;  //30///maximum line23
			##############################
			$m_width=200;
			$m_width_3_fields=66;
			$box_group=$_POST['hide_box_group'];
			$box_group_code=getCodeofString($box_group); ///pick in inventory_inquiry_function.php
			$box_group_code=trim($box_group_code);
			if ($box_group_code=="All") {
				$group="";
				$title = "Product Listing (By Group)";
			} else {
				$group=" AND (tblProdMast.prdGrpCode=$box_group_code) ";
				$title = "Product Listing By Group ($box_group)";
			}
			$query_product="SELECT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdSubClsCode,tblProdMast.prdSellUnit, 
                     		tblProdMast.prdBuyUnit, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdSuppItem, tblProdMast.prdSetTag, tblProdClass.prdClsShortdesc
							FROM tblProdMast INNER JOIN
                      		tblProdClass ON tblProdMast.prdGrpCode = tblProdClass.prdGrpCode
							WHERE tblProdMast.prdDelTag = 'A' AND tblProdClass.prdClsLvl = 1 $group
							ORDER BY tblProdClass.prdClsShortdesc, tblProdMast.prdDeptCode,tblProdMast.prdClsCode,tblProdMast.prdDesc";
			break;
		case "by_upc":
			$pdf = new FPDF('P', 'mm', 'LEGAL');
			$dtl_ht=4;
			$max_tot_line=75;
			$m_width=200;
			$m_width_3_fields=66;
			$m_line = 70;  ///maximum line
			$box_upc=$_POST['hide_box_upc'];
			$box_upc2=$_POST['hide_box_upc2'];
		  	$title = "Product Listing (By UPC)";
			if (($box_upc2>"" && $box_upc2!="type here") && ($box_upc>"" && $box_upc!="type here")) { //box_upc1 and box_upc2 searching
				$query_product="SELECT tblUpc.upcCode, tblProdMast.prdDelTag,tblUpc.upcDesc, tblUpc.prdNumber, tblUpc.upcStat, tblUpc.upcParTag, tblProdMast.prdNumber AS Expr1, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdShort, tblProdMast.prdSuppItem, tblProdMast.prdSellUnit, tblProdMast.prdBuyUnit, tblProdMast.prdSetTag, tblProdMast.prdSubClsCode, tblProdMast.buyerCode
								FROM tblUpc INNER JOIN
								tblProdMast ON tblProdMast.prdNumber = tblUpc.prdNumber
								WHERE (tblProdMast.prdDelTag='A' OR tblProdMast.prdDelTag=' ') AND (tblProdMast.prdDesc BETWEEN '$box_upc' AND '$box_upc2')
								ORDER BY tblProdMast.prdDesc, tblUpc.upcDesc";
			} else {  ///box_upc1 or box_upc2 searching
				if ($box_upc2>"" && $box_upc2!="type here") { ///box upc2 searching
					$query_product="SELECT tblUpc.upcCode,tblProdMast.prdDelTag, tblUpc.upcDesc, tblUpc.prdNumber, tblUpc.upcStat, tblUpc.upcParTag, tblProdMast.prdNumber AS Expr1, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdShort, tblProdMast.prdSuppItem, tblProdMast.prdSellUnit, tblProdMast.prdBuyUnit, tblProdMast.prdSetTag, tblProdMast.prdSubClsCode, tblProdMast.buyerCode
									FROM tblUpc INNER JOIN
									tblProdMast ON tblProdMast.prdNumber = tblUpc.prdNumber
									WHERE (tblProdMast.prdDelTag='A' OR tblProdMast.prdDelTag=' ') AND (tblProdMast.prdDesc LIKE '$box_upc2%')
									ORDER BY tblProdMast.prdDesc, tblUpc.upcDesc";
				} else { 
					$query_product="SELECT tblUpc.upcCode,tblProdMast.prdDelTag, tblUpc.upcDesc, tblUpc.prdNumber, tblUpc.upcStat, tblUpc.upcParTag, tblProdMast.prdNumber AS Expr1, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdShort, tblProdMast.prdSuppItem, tblProdMast.prdSellUnit, tblProdMast.prdBuyUnit, tblProdMast.prdSetTag, tblProdMast.prdSubClsCode, tblProdMast.buyerCode
									FROM tblUpc INNER JOIN
									tblProdMast ON tblProdMast.prdNumber = tblUpc.prdNumber
									WHERE (tblProdMast.prdDelTag='A' OR tblProdMast.prdDelTag=' ') AND (tblProdMast.prdDesc LIKE '$box_upc%')
									ORDER BY tblProdMast.prdDesc, tblUpc.upcDesc";
				}
			}
			break;
		case "by_code":
			$pdf = new FPDF('P', 'mm', 'LEGAL');
			$dtl_ht=4;
			$max_tot_line=75;
			$m_width=200;
			$m_width_3_fields=66;
			$title = "Product Listing (By Product Code)";
			$m_line = 70;  ///maximum line
			$box_code=$_POST['hide_box_code'];
			$box_code2=$_POST['hide_box_code2'];
			if (($box_code2>"" && $box_code2!="type here") && ($box_code>"" && $box_code!="type here")) { //box_upc1 and box_upc2 searching
				$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdNumber >= $box_code) AND (prdNumber <= $box_code2) 
							ORDER BY prdNumber ASC";
			} else {  ///box_upc1 or box_upc2 searching
				if ($box_code2>"" && $box_code2!="type here") { ///box upc2 searching
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdNumber LIKE '$box_code2%') 
							ORDER BY prdNumber ASC";
				} else {
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdNumber LIKE '$box_code%') 
							ORDER BY prdNumber ASC";
				}
			}
			break;
		case "by_desc":
			$pdf = new FPDF('P', 'mm', 'LEGAL');
			$dtl_ht=4;
			$max_tot_line=75;
			$m_width=200;
			$m_width_3_fields=66;
			$title = "Product Listing (By Product Description)";
			$m_line = 70;  ///maximum line
			$box_desc=$_POST['hide_box_desc'];
			$box_desc2=$_POST['hide_box_desc2'];
			if (($box_desc2>"" && $box_desc2!="type here") && ($box_desc>"" && $box_desc!="type here")) { //box_upc1 and box_upc2 searching
				$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdDesc BETWEEN '$box_desc' AND '$box_desc2') 
							ORDER BY prdDesc ASC";
			} else {  ///box_upc1 or box_upc2 searching
				if ($box_desc2>""  && $box_desc2!="type here") { ///box upc2 searching
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdDesc LIKE '$box_desc2%') 
							ORDER BY prdDesc ASC";
				} else {
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdDesc LIKE '$box_desc%') 
							ORDER BY prdDesc ASC";
				}
			}
			break;
		case "by_buyer":
			$pdf = new FPDF('P', 'mm', 'LEGAL');
			$dtl_ht=4;
			$max_tot_line=75;
			$m_width=200;
			$m_width_3_fields=66;
			$title = "Product Listing (By Product Description)";
			$m_line = 70;  ///maximum line
			$box_buyer=$_POST['hide_box_buyer'];
		  	$title = "Product Listing (By Buyer)";
			$query_product="SELECT dbo.tblBuyers.buyerCode, dbo.tblBuyers.buyerName, dbo.tblProdMast.prdNumber, dbo.tblProdMast.prdDesc, dbo.tblProdMast.prdBuyUnit, 
                      dbo.tblProdMast.prdSellUnit, dbo.tblProdMast.prdConv, dbo.tblSuppliers.suppCode, dbo.tblSuppliers.suppName, dbo.tblProdMast.prdSuppItem, 
                      dbo.tblProdMast.prdGrpCode, dbo.tblProdMast.prdDeptCode, dbo.tblProdMast.prdClsCode, dbo.tblProdMast.prdSubClsCode, 
                      dbo.tblProdMast.prdSetTag
					  FROM dbo.tblProdMast INNER JOIN
                      dbo.tblBuyers ON dbo.tblProdMast.buyerCode = dbo.tblBuyers.buyerCode INNER JOIN
                      dbo.tblSuppliers ON dbo.tblProdMast.suppCode = dbo.tblSuppliers.suppCode
					  WHERE (dbo.tblBuyers.buyerCode =$box_buyer)
					  ORDER BY dbo.tblBuyers.buyerCode,dbo.tblProdMast.prdNumber";
			$query_buyer="SELECT * FROM tblBuyers WHERE (buyerCode = $box_buyer)";
			$result_buyer=mssql_query($query_buyer);
			$num_buyer = mssql_num_rows($result_buyer);
			if ($num_buyer >0){
				$buyer_name=mssql_result($result_buyer,0,"buyerName");
				$buyer_name = "Buyer : " . $box_buyer . "-" . $buyer_name;
			} else {
				$buyer_name="Buyer : " . $box_buyer . "-" . "N/A";
			}
			break;
		
	}
	####################company name##################################
	$result_product=mssql_query($query_product);
	$num_product = mssql_num_rows($result_product);
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,0,"compName");
	} else {
		$comp_name="NA";
	}
	###################################################################
	
	$font="Courier";
	$m_page=$num_product / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$num_product; //// temporary total record
	for ($j=1;$j<=$m_page;$j++){ 
		$tmp_rec=$tmp_rec - $m_line;
		$tmp_first= ($j * $m_line) - ($m_line - 1);
		$pdf->AddPage();
		$pdf->SetFont($font, '', '10');
		if ($tmp_rec <= 0) { /// 1 page consume
			$tmp_last=($j*$m_line) + $tmp_rec;
			$entries="Entries ".$tmp_first." to ".$tmp_last." of ".$num_product." record/s";
			$page="Page ".$j." of ".$m_page;
		} else {
			$tmp_last_more=$j*$m_line;
			$entries="Entries ".$tmp_first." to ".$tmp_last_more." of ".$num_product." record/s";
			$page="Page ".$j." of ".$m_page;
		}
		$pdf->Cell($m_width_3_fields,5,$newdate,0,0);
		$pdf->Cell($m_width_3_fields,5,$comp_name,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,$page,0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"Report ID : PrdList",0,0);
		$pdf->Cell($m_width_3_fields,5,'',0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->Cell($m_width_3_fields,5,"",0,0);
		$pdf->Cell($m_width_3_fields,5,$title,0,0,'C');
		$pdf->Cell($m_width_3_fields,5,'',0,1,'R');
		$pdf->Cell($m_width,5,$title2,0,1,'C');
		switch ($search_selection) {
			case "by_vendor":
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Grp/Dept/', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'SellUM/BuyUM/', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cls/Sub-Cls', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Conv', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Primary UPC', 0, 0);
				break;
			case "by_code":
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Grp/Dept/', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'SellUM/BuyUM/', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cls/Sub-Cls', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Conv', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Primary UPC', 0, 0);
				break;
			case "by_desc":
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Grp/Dept/', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'SellUM/BuyUM/', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cls/Sub-Cls', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Conv', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Primary UPC', 0, 0);
				break;
			case "by_group":
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, '', 0, 0);
				$pdf->Cell(90,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Grp/Dept/', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Sell/Buy/', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->ln();
				$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cls/Sub-Cls', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Conv', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Primary UPC', 0, 0);
				break;
			case "by_upc":
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
			  	$pdf->Cell(20,$dtl_ht, 'SKU', 0, 0);
				$pdf->Cell(90,$dtl_ht, 'Description', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'UPC Code', 0, 0);
				$pdf->Cell(70,$dtl_ht, 'UPC Desc', 0, 0);
				break;
			case "by_buyer":
				$pdf->Cell($m_width,$dtl_ht, $buyer_name, 0, 0,'C');
				$pdf->ln();
				$pdf->ln();
				$pdf->Cell($m_width, 0, '', 1, 0);
				$pdf->ln();
			
				$pdf->Cell(110,$dtl_ht, '', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Grp/Dept/', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'SellUM/BuyUM/', 0, 0);
				$pdf->Cell(30,$dtl_ht, '', 0, 0);
				$pdf->ln();
			
				$pdf->Cell(110,$dtl_ht, 'SKU / Description', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Cls/Sub-Cls', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Conv', 0, 0);
				$pdf->Cell(30,$dtl_ht, 'Primary UPC', 0, 0);
				break;
		}
		$pdf->ln();
		$pdf->Cell($m_width, 0, '', 1, 0);
		$pdf->ln();
		
		if ($tmp_rec <= 0) { /// 1 page consume
			for($g=1; $g<=14; $g++) {
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last=($j*$m_line) + $tmp_rec;
				$total_line=0;
				for ($i=$tmp_first;$i <= $tmp_last;$i++){
					$i--;							
								switch ($search_selection) {
									case "by_buyer":
										$grid_buyer_code=mssql_result($result_product,$i,"buyerCode");
										if($grid_buyer_code!=$temp_buyer_code) {
											$total_line=$total_line+3;
										}
										$total_line=$total_line+1;
										$temp_buyer_code=$grid_buyer_code;
										break;
									case "by_vendor":
										$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
										if($grid_supp_code!=$temp_supp_code) {
											$total_line=$total_line+3;
										}
										$total_line=$total_line+1;
										$temp_supp_code=$grid_supp_code;
										break;
									case "by_group":
										$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
										$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
										$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
										$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");		
										if($grid_dept_code!=$temp_dept_code) {
											$total_line=$total_line+2;
										} 
										if($grid_cls_code!=$temp_class_code) {
											if($grid_dept_code==$temp_dept_code) {
												$total_line=$total_line+1;
											}
										} else {
											if ($grid_dept_code!=$temp_dept_code) {
												$total_line=$total_line+1;
											}
										}
										if(trim($grid_sub_cls_code)!=trim($temp_sub_class_code)) {
											if($grid_cls_code==$temp_class_code) {
												$total_line=$total_line+1;
											}
										} else {
											if ($grid_cls_code!=$temp_class_code) {											
												$total_line=$total_line+1;
											}
										}
										###################################################
										$total_line=$total_line+1;
										$temp_group_code=$grid_grp_code;
										$temp_dept_code=$grid_dept_code;
										$temp_class_code=$grid_cls_code;
										$temp_sub_class_code=$grid_sub_cls_code;
										break;
								}
					$i++;
				} 
				###################################
				####### READING TOTAL LINE ######## 107 - 55
				###################################
			
				$tmp_last=($j*$m_line) + $tmp_rec;
				if ($total_line <=$max_tot_line) {
					for ($i=$tmp_first;$i <= $tmp_last;$i++){
						$i--;							
									switch ($search_selection) {
										case "by_vendor":
											$grid_supp_name=mssql_result($result_product,$i,"suppName");
											$grid_supp_name = str_replace("\\","",$grid_supp_name);
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
											$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											$temp_supp_code=$grid_supp_code;
											break;
										case "by_group":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
											
											///// get prdGrpInit from table tblProdGrp....
											$query_group="SELECT * FROM tblProdClass WHERE prdGrpCode = $grid_grp_code AND prdClsLvl = 1";
											$result_group=mssql_query($query_group);
											$num_group= mssql_num_rows($result_group);
											if ($num_group >0) {
												$grid_group_desc=mssql_result($result_group,0,"prdClsDesc");
											} else {
												$grid_group_desc="NA";
											}
											///// get prdDeptInit from table tblProdDept....
											$query_dept="SELECT * FROM tblProdClass WHERE prdDeptCode = $grid_dept_code AND prdGrpCode = $grid_grp_code AND prdClsLvl =2";
											$result_dept=mssql_query($query_dept);
											$num_dept = mssql_num_rows($result_dept);
											if ($num_dept >0) {
												$grid_dept_desc=mssql_result($result_dept,0,"prdClsDesc");
											} else {
												$grid_dept_desc="NA";
											}
											///// get prdClsShortdesc from table tblProdClass....
											$query_class="SELECT * FROM tblProdClass WHERE prdClsCode = $grid_cls_code AND prdDeptCode = $grid_dept_code AND prdGrpCode = $grid_grp_code AND prdClsLvl = 3";
											$result_class=mssql_query($query_class);
											$num_class = mssql_num_rows($result_class);
											if ($num_class >0) {
												$grid_class_desc=mssql_result($result_class,0,"prdClsDesc");
											} else {
												$grid_class_desc="NA";
											}
											///// get prdSubClsInit from table tblProdSubCls....
											$query_sub_class="SELECT * FROM tblProdClass WHERE prdSubClsCode = $grid_sub_cls_code AND prdDeptCode = $grid_dept_code AND prdGrpCode = $grid_grp_code AND prdClsLvl = 4";
											$result_sub_class=mssql_query($query_sub_class);
											$num_sub_class = mssql_num_rows($result_sub_class);
											if ($num_sub_class >0) {
												$grid_sub_class_desc=mssql_result($result_sub_class,0,"prdClsDesc");
											} else {
												$grid_sub_class_desc="NA";
											}
											///// get suppName from table tblSuppliers....
											$query_vendor="SELECT * FROM tblSuppliers WHERE suppCode = $grid_supp_code";
											$result_vendor=mssql_query($query_vendor);
											$num_vendor = mssql_num_rows($result_vendor);
											if ($num_vendor >0) {
												$grid_vendor=mssql_result($result_vendor,0,"suppName");
												$grid_vendor=str_replace("\\","",$grid_vendor);
												$grid_vendor=$grid_supp_code."-".$grid_vendor;
											} else {
												$grid_vendor="NA";
											}
											
											if(($grid_grp_code!=$temp_group_code) || ($grid_dept_code!=$temp_dept_code) || ($grid_cls_code!=$temp_class_code) || ($grid_sub_cls_code!=$temp_sub_class_code)) {
												//$pdf->Cell(20,$dtl_ht, '', 0, 1);
											}
											$pdf->SetFont($font, 'B', '10');
											if($grid_grp_code!=$temp_group_code) {
												if ($temp_group_code!="") {
													$pdf->Cell($m_width,$dtl_ht, '', 0, 1);
													//$pdf->Cell($m_width,$dtl_ht, '', 0, 1);
												}
												$grid_group_desc= "Group: ".$grid_group_desc;
												$pdf->Cell($m_width,$dtl_ht, $grid_group_desc, 0, 1);
											}
											if($grid_dept_code!=$temp_dept_code) {
												$pdf->ln();
												$pdf->ln();
												$grid_dept_desc= "Dept: " . $grid_dept_code . "-" .$grid_dept_desc;
												$pdf->Cell(20,$dtl_ht, $grid_dept_desc, 0, 1);
												//$temp_dept_code=$grid_dept_code;
											} 
											if($grid_cls_code!=$temp_class_code) {
												if ($grid_dept_code==$temp_dept_code) {
													$pdf->ln();
												}
												$pdf->Cell(5,$dtl_ht,'', 0, 0);
												$pdf->Cell(3,$dtl_ht,'', 0, 0);
												$grid_class_desc= "Class: ". $grid_cls_code . "-" . $grid_class_desc;
												$pdf->Cell(20,$dtl_ht,$grid_class_desc, 0,1);
												//$temp_class_code=$grid_cls_code;
											} else {
												if ($grid_dept_code!=$temp_dept_code) {
													$pdf->Cell(5,$dtl_ht,'', 0, 0);
													$pdf->Cell(3,$dtl_ht,'', 0, 0);
													$grid_class_desc= "Class: ". $grid_cls_code . "-" . $grid_class_desc;
													$pdf->Cell(20,$dtl_ht,$grid_class_desc, 0,1);
													//$temp_class_code=$grid_cls_code;
												}
											}
											if(trim($grid_sub_cls_code)!=trim($temp_sub_class_code)) {
												if ($grid_cls_code==$temp_class_code) {
													$pdf->ln();
												}
												$pdf->Cell(10,$dtl_ht,'', 0, 0);
												$pdf->Cell(3,$dtl_ht,'', 0, 0);
												$grid_sub_class_desc= "Sub-Cls: " . $grid_sub_cls_code . "-" .$grid_sub_class_desc;
												$pdf->Cell(20,$dtl_ht,$grid_sub_class_desc, 0,1);
												//$temp_sub_class_code=$grid_sub_cls_code;
											} else {
												if ($grid_cls_code!=$temp_class_code) {											
													$pdf->Cell(10,$dtl_ht,'', 0, 0);
													$pdf->Cell(3,$dtl_ht,'', 0, 0);
													$grid_sub_class_desc= "Sub-Cls: " . $grid_sub_cls_code . "-" .$grid_sub_class_desc;
													$pdf->Cell(20,$dtl_ht,$grid_sub_class_desc, 0,1);
													//$temp_sub_class_code=$grid_sub_cls_code;
												}
											}
											$pdf->SetFont($font, '', '10');
											if(($grid_grp_code!=$temp_group_code) || ($grid_dept_code!=$temp_dept_code) || ($grid_cls_code!=$temp_class_code) || ($grid_sub_cls_code!=$temp_sub_class_code)) {
												//$pdf->Cell(20,$dtl_ht, '', 0, 1);
											}
											###################################################
											$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											$temp_group_code=$grid_grp_code;
											$temp_dept_code=$grid_dept_code;
											$temp_class_code=$grid_cls_code;
											$temp_sub_class_code=$grid_sub_cls_code;
											break;
										case "by_upc":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
											
											$grid_upc_code=mssql_result($result_product,$i,"upcCode");
											$grid_upc_desc=mssql_result($result_product,$i,"upcDesc");
											$grid_upc_desc=str_replace("\\","",$grid_upc_desc);
											$grid_upc_stat=mssql_result($result_product,$i,"upcStat");
											if($grid_no!=$temp_no) {
												$pdf->Cell(20,$dtl_ht, '', 0, 1);
												$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
												$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
												$pdf->Cell(30,$dtl_ht, $grid_upc_code, 0, 0);
												$pdf->Cell(70,$dtl_ht, $grid_upc_desc, 0, 0);
												
											} else {
												$pdf->Cell(20,$dtl_ht, '', 0, 0);
												$pdf->Cell(90,$dtl_ht, '', 0, 0);
												$pdf->Cell(30,$dtl_ht, $grid_upc_code, 0, 0);
												$pdf->Cell(70,$dtl_ht, $grid_upc_desc, 0, 0);
												
											}
											
											//$pdf->Cell(15,$dtl_ht, $grid_upc_stat, 0, 0);
											$pdf->ln();
											$temp_no=$grid_no;
											break;
										case "by_code":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
				
											///// get prdGrpInit from table tblProdGrp....
											$query_group="SELECT * FROM tblProdClass WHERE prdGrpCode = $grid_grp_code AND prdClsLvl = 1";
											$result_group=mssql_query($query_group);
											$num_group= mssql_num_rows($result_group);
											if ($num_group >0) {
												$grid_group_desc=mssql_result($result_group,0,"prdClsShortdesc");
											} else {
												$grid_group_desc="NA";
											}
											///// get prdDeptInit from table tblProdDept....
											$query_dept="SELECT * FROM tblProdClass WHERE prdDeptCode = $grid_dept_code AND prdClsLvl = 2";
											$result_dept=mssql_query($query_dept);
											$num_dept = mssql_num_rows($result_dept);
											if ($num_dept >0) {
												$grid_dept_desc=mssql_result($result_dept,0,"prdClsShortdesc");
											} else {
												$grid_dept_desc="NA";
											}
											///// get prdClsShortdesc from table tblProdClass....
											$query_class="SELECT * FROM tblProdClass WHERE prdClsCode = $grid_cls_code AND prdClsLvl = 3";
											$result_class=mssql_query($query_class);
											$num_class = mssql_num_rows($result_class);
											if ($num_class >0) {
												$grid_class_desc=mssql_result($result_class,0,"prdClsShortdesc");
											} else {
												$grid_class_desc="NA";
											}
											///// get prdSubClsInit from table tblProdSubCls....
											$query_sub_class="SELECT * FROM tblProdClass WHERE prdSubClsCode = $grid_sub_cls_code AND prdClsLvl = 4";
											$result_sub_class=mssql_query($query_sub_class);
											$num_sub_class = mssql_num_rows($result_sub_class);
											if ($num_sub_class >0) {
												$grid_sub_class_desc=mssql_result($result_sub_class,0,"prdClsShortdesc");
											} else {
												$grid_sub_class_desc="NA";
											}
										$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											break;
										case "by_desc":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
				
											$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											break;
										case "by_buyer":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_no_desc=$grid_no . "-" . $grid_desc;
											$grid_group=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept=mssql_result($result_product,$i,"prdDeptCode");
											$grid_class=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_class=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_grp = $grid_group . "/" . $grid_dept . "/" . $grid_class . "/" . $grid_sub_class;
											$grid_sell=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_conv=number_format($grid_conv,0);
											$grid_buy_sell_conv=trim($grid_buy) . "/" . trim($grid_sell) . "/" . $grid_conv;
											$grid_supp_code=mssql_result($result_product,$i,"suppCode");
											$grid_supp_name=mssql_result($result_product,$i,"suppName");
											$grid_supp_name=str_replace("\\","",$grid_supp_name);
											$grid_supp=$grid_supp_code . "-" . $grid_supp_name;
											$grid_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_tag=mssql_result($result_product,$i,"prdSetTag");
											$grid_buyer_code=mssql_result($result_product,$i,"buyerCode");
											$grid_buyer_name=mssql_result($result_product,$i,"buyerName");
											$grid_buyer=$grid_buyer_code . "-" . $grid_buyer_name;
											if($grid_buyer_code!=$temp_buyer_code) {
												$pdf->ln();
												$pdf->Cell(100,$dtl_ht, $grid_buyer, 0, 0);
												$pdf->ln();
											} 
											$pdf->Cell(110,$dtl_ht, $grid_no_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
				
											$pdf->Cell(30,$dtl_ht, $grid_buy_sell_conv, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_upc, 0, 0);
											//$pdf->Cell(15,$dtl_ht, $grid_upc_stat, 0, 0);
											$pdf->ln();
											$temp_buyer_code=$grid_buyer_code;
											break;
									}
						$i++;
					} 
					break;
				} 
				$m_line = $m_line-5;
			}	
		} else {            /// more than 1 page consume
			for($g=1; $g<=14; $g++) {
				###################################
				####### READING TOTAL LINE ########
				###################################
				$tmp_last_more=$j*$m_line;
				$total_line=0;
				for ($i=$tmp_first;$i <= $tmp_last_more;$i++){
					$i--;
								switch ($search_selection) {
									case "by_buyer":
										$grid_buyer_code=mssql_result($result_product,$i,"buyerCode");
										if($grid_buyer_code!=$temp_buyer_code) {
											$total_line=$total_line+3;
										}
										$total_line=$total_line+1;
										$temp_buyer_code=$grid_buyer_code;
										break;
									case "by_vendor":
										$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
										if($grid_supp_code!=$temp_supp_code) {
											$total_line=$total_line+3;
										}
										$total_line=$total_line+1;
										$temp_supp_code=$grid_supp_code;
										break;
									case "by_group":
										$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
										$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
										$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
										$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");		
										if($grid_dept_code!=$temp_dept_code) {
											$total_line=$total_line+2;
										} 
										if($grid_cls_code!=$temp_class_code) {
											if($grid_dept_code==$temp_dept_code) {
												$total_line=$total_line+1;
											}
										} else {
											if ($grid_dept_code!=$temp_dept_code) {
												$total_line=$total_line+1;
											}
										}
										if(trim($grid_sub_cls_code)!=trim($temp_sub_class_code)) {
											if($grid_cls_code==$temp_class_code) {
												$total_line=$total_line+1;
											}
										} else {
											if ($grid_cls_code!=$temp_class_code) {											
												$total_line=$total_line+1;
											}
										}
										###################################################
										$total_line=$total_line+1;
										$temp_group_code=$grid_grp_code;
										$temp_dept_code=$grid_dept_code;
										$temp_class_code=$grid_cls_code;
										$temp_sub_class_code=$grid_sub_cls_code;
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
										case "by_vendor":
											$grid_supp_name=mssql_result($result_product,$i,"suppName");
											$grid_supp_name=str_replace("\\","",$grid_supp_name);
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
											$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											$temp_supp_code=$grid_supp_code;
											break;
										case "by_group":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
											
											///// get prdGrpInit from table tblProdGrp....
											$query_group="SELECT * FROM tblProdClass WHERE prdGrpCode = $grid_grp_code AND prdClsLvl = 1";
											$result_group=mssql_query($query_group);
											$num_group= mssql_num_rows($result_group);
											if ($num_group >0) {
												$grid_group_desc=mssql_result($result_group,0,"prdClsDesc");
											} else {
												$grid_group_desc="NA";
											}
											///// get prdDeptInit from table tblProdDept....
											$query_dept="SELECT * FROM tblProdClass WHERE prdDeptCode = $grid_dept_code AND prdGrpCode = $grid_grp_code AND prdClsLvl =2";
											$result_dept=mssql_query($query_dept);
											$num_dept = mssql_num_rows($result_dept);
											if ($num_dept >0) {
												$grid_dept_desc=mssql_result($result_dept,0,"prdClsDesc");
											} else {
												$grid_dept_desc="NA";
											}
											///// get prdClsShortdesc from table tblProdClass....
											$query_class="SELECT * FROM tblProdClass WHERE prdClsCode = $grid_cls_code AND prdDeptCode = $grid_dept_code AND prdGrpCode = $grid_grp_code AND prdClsLvl = 3";
											$result_class=mssql_query($query_class);
											$num_class = mssql_num_rows($result_class);
											if ($num_class >0) {
												$grid_class_desc=mssql_result($result_class,0,"prdClsDesc");
											} else {
												$grid_class_desc="NA";
											}
											///// get prdSubClsInit from table tblProdSubCls....
											$query_sub_class="SELECT * FROM tblProdClass WHERE prdSubClsCode = $grid_sub_cls_code AND prdDeptCode = $grid_dept_code AND prdGrpCode = $grid_grp_code AND prdClsLvl = 4";
											$result_sub_class=mssql_query($query_sub_class);
											$num_sub_class = mssql_num_rows($result_sub_class);
											if ($num_sub_class >0) {
												$grid_sub_class_desc=mssql_result($result_sub_class,0,"prdClsDesc");
											} else {
												$grid_sub_class_desc="NA";
											}
											///// get suppName from table tblSuppliers....
											$query_vendor="SELECT * FROM tblSuppliers WHERE suppCode = $grid_supp_code";
											$result_vendor=mssql_query($query_vendor);
											$num_vendor = mssql_num_rows($result_vendor);
											if ($num_vendor >0) {
												$grid_vendor=mssql_result($result_vendor,0,"suppName");
												$grid_vendor=str_replace("\\","",$grid_vendor);
												$grid_vendor=$grid_supp_code."-".$grid_vendor;
											} else {
												$grid_vendor="NA";
											}
											
											if(($grid_grp_code!=$temp_group_code) || ($grid_dept_code!=$temp_dept_code) || ($grid_cls_code!=$temp_class_code) || ($grid_sub_cls_code!=$temp_sub_class_code)) {
												//$pdf->Cell(20,$dtl_ht, '', 0, 1);
											}
											$pdf->SetFont($font, 'B', '10');
											if($grid_grp_code!=$temp_group_code) {
												if ($temp_group_code!="") {
													$pdf->Cell($m_width,$dtl_ht, '', 0, 1);
													//$pdf->Cell($m_width,$dtl_ht, '', 0, 1);
												}
												$grid_group_desc= "Group: ".$grid_group_desc;
												$pdf->Cell($m_width,$dtl_ht, $grid_group_desc, 0, 1);
											}
											if($grid_dept_code!=$temp_dept_code) {
												$pdf->ln();
												$pdf->ln();
												$grid_dept_desc= "Dept: " . $grid_dept_code . "-" .$grid_dept_desc;
												$pdf->Cell(20,$dtl_ht, $grid_dept_desc, 0, 1);
												//$temp_dept_code=$grid_dept_code;
											} 
											if($grid_cls_code!=$temp_class_code) {
												if ($grid_dept_code==$temp_dept_code) {
													$pdf->ln();
												}
												$pdf->Cell(5,$dtl_ht,'', 0, 0);
												$pdf->Cell(3,$dtl_ht,'', 0, 0);
												$grid_class_desc= "Class: ". $grid_cls_code . "-" . $grid_class_desc;
												$pdf->Cell(20,$dtl_ht,$grid_class_desc, 0,1);
												//$temp_class_code=$grid_cls_code;
											} else {
												if ($grid_dept_code!=$temp_dept_code) {
													$pdf->Cell(5,$dtl_ht,'', 0, 0);
													$pdf->Cell(3,$dtl_ht,'', 0, 0);
													$grid_class_desc= "Class: ". $grid_cls_code . "-" . $grid_class_desc;
													$pdf->Cell(20,$dtl_ht,$grid_class_desc, 0,1);
													//$temp_class_code=$grid_cls_code;
												}
											}
											if(trim($grid_sub_cls_code)!=trim($temp_sub_class_code)) {
												if ($grid_cls_code==$temp_class_code) {
													$pdf->ln();
												}
												$pdf->Cell(10,$dtl_ht,'', 0, 0);
												$pdf->Cell(3,$dtl_ht,'', 0, 0);
												$grid_sub_class_desc= "Sub-Cls: " . $grid_sub_cls_code . "-" .$grid_sub_class_desc;
												$pdf->Cell(20,$dtl_ht,$grid_sub_class_desc, 0,1);
												//$temp_sub_class_code=$grid_sub_cls_code;
											} else {
												if ($grid_cls_code!=$temp_class_code) {											
													$pdf->Cell(10,$dtl_ht,'', 0, 0);
													$pdf->Cell(3,$dtl_ht,'', 0, 0);
													$grid_sub_class_desc= "Sub-Cls: " . $grid_sub_cls_code . "-" .$grid_sub_class_desc;
													$pdf->Cell(20,$dtl_ht,$grid_sub_class_desc, 0,1);
													//$temp_sub_class_code=$grid_sub_cls_code;
												}
											}
											$pdf->SetFont($font, '', '10');
											if(($grid_grp_code!=$temp_group_code) || ($grid_dept_code!=$temp_dept_code) || ($grid_cls_code!=$temp_class_code) || ($grid_sub_cls_code!=$temp_sub_class_code)) {
												//$pdf->Cell(20,$dtl_ht, '', 0, 1);
											}
											###################################################
											$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											$temp_group_code=$grid_grp_code;
											$temp_dept_code=$grid_dept_code;
											$temp_class_code=$grid_cls_code;
											$temp_sub_class_code=$grid_sub_cls_code;
											break;
										case "by_upc":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
											
											$grid_upc_code=mssql_result($result_product,$i,"upcCode");
											$grid_upc_desc=mssql_result($result_product,$i,"upcDesc");
											$grid_upc_desc=str_replace("\\","",$grid_upc_desc);
											$grid_upc_stat=mssql_result($result_product,$i,"upcStat");
											if($grid_no!=$temp_no) {
												$pdf->Cell(20,$dtl_ht, '', 0, 1);
												$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
												$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
												$pdf->Cell(30,$dtl_ht, $grid_upc_code, 0, 0);
												$pdf->Cell(70,$dtl_ht, $grid_upc_desc, 0, 0);
												
											} else {
												$pdf->Cell(20,$dtl_ht, '', 0, 0);
												$pdf->Cell(90,$dtl_ht, '', 0, 0);
												$pdf->Cell(30,$dtl_ht, $grid_upc_code, 0, 0);
												$pdf->Cell(70,$dtl_ht, $grid_upc_desc, 0, 0);
												
											}
											
											//$pdf->Cell(15,$dtl_ht, $grid_upc_stat, 0, 0);
											$pdf->ln();
											$temp_no=$grid_no;
											break;
										case "by_code":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
				
											///// get prdGrpInit from table tblProdGrp....
											$query_group="SELECT * FROM tblProdClass WHERE prdGrpCode = $grid_grp_code AND prdClsLvl = 1";
											$result_group=mssql_query($query_group);
											$num_group= mssql_num_rows($result_group);
											if ($num_group >0) {
												$grid_group_desc=mssql_result($result_group,0,"prdClsShortdesc");
											} else {
												$grid_group_desc="NA";
											}
											///// get prdDeptInit from table tblProdDept....
											$query_dept="SELECT * FROM tblProdClass WHERE prdDeptCode = $grid_dept_code AND prdClsLvl = 2";
											$result_dept=mssql_query($query_dept);
											$num_dept = mssql_num_rows($result_dept);
											if ($num_dept >0) {
												$grid_dept_desc=mssql_result($result_dept,0,"prdClsShortdesc");
											} else {
												$grid_dept_desc="NA";
											}
											///// get prdClsShortdesc from table tblProdClass....
											$query_class="SELECT * FROM tblProdClass WHERE prdClsCode = $grid_cls_code AND prdClsLvl = 3";
											$result_class=mssql_query($query_class);
											$num_class = mssql_num_rows($result_class);
											if ($num_class >0) {
												$grid_class_desc=mssql_result($result_class,0,"prdClsShortdesc");
											} else {
												$grid_class_desc="NA";
											}
											///// get prdSubClsInit from table tblProdSubCls....
											$query_sub_class="SELECT * FROM tblProdClass WHERE prdSubClsCode = $grid_sub_cls_code AND prdClsLvl = 4";
											$result_sub_class=mssql_query($query_sub_class);
											$num_sub_class = mssql_num_rows($result_sub_class);
											if ($num_sub_class >0) {
												$grid_sub_class_desc=mssql_result($result_sub_class,0,"prdClsShortdesc");
											} else {
												$grid_sub_class_desc="NA";
											}
										$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											break;
										case "by_desc":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_grp_code=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept_code=mssql_result($result_product,$i,"prdDeptCode");
											$grid_cls_code=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_cls_code=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_sell_unit=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy_unit=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_supp_code=mssql_result($result_product,$i,"SuppCode");
											$grid_primary_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_conv = number_format($grid_conv, 0);  
											$grid_grp=$grid_grp_code."/".$grid_dept_code."/".$grid_cls_code."/".$grid_sub_cls_code;
											$grid_sell_buy= $grid_sell_unit."/". $grid_buy_unit."/".$grid_conv;
				
											$pdf->Cell(20,$dtl_ht, $grid_no, 0, 0);
											$pdf->Cell(90,$dtl_ht, $grid_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_sell_buy, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_primary_upc, 0, 0);
											$pdf->ln();
											break;
										case "by_buyer":
											$grid_no=mssql_result($result_product,$i,"prdNumber");
											$grid_desc=mssql_result($result_product,$i,"prdDesc");
											$grid_desc=str_replace("\\","",$grid_desc);
											$grid_no_desc=$grid_no . "-" . $grid_desc;
											$grid_group=mssql_result($result_product,$i,"prdGrpCode");
											$grid_dept=mssql_result($result_product,$i,"prdDeptCode");
											$grid_class=mssql_result($result_product,$i,"prdClsCode");
											$grid_sub_class=mssql_result($result_product,$i,"prdSubClsCode");
											$grid_grp = $grid_group . "/" . $grid_dept . "/" . $grid_class . "/" . $grid_sub_class;
											$grid_sell=mssql_result($result_product,$i,"prdSellUnit");
											$grid_buy=mssql_result($result_product,$i,"prdBuyUnit");
											$grid_conv=mssql_result($result_product,$i,"prdConv");
											$grid_conv=number_format($grid_conv,0);
											$grid_buy_sell_conv=trim($grid_buy) . "/" . trim($grid_sell) . "/" . $grid_conv;
											$grid_supp_code=mssql_result($result_product,$i,"suppCode");
											$grid_supp_name=mssql_result($result_product,$i,"suppName");
											$grid_supp_name=str_replace("\\","",$grid_supp_name);
											$grid_supp=$grid_supp_code . "-" . $grid_supp_name;
											$grid_upc=mssql_result($result_product,$i,"prdSuppItem");
											$grid_tag=mssql_result($result_product,$i,"prdSetTag");
											$grid_buyer_code=mssql_result($result_product,$i,"buyerCode");
											$grid_buyer_name=mssql_result($result_product,$i,"buyerName");
											$grid_buyer=$grid_buyer_code . "-" . $grid_buyer_name;
											if($grid_buyer_code!=$temp_buyer_code) {
												$pdf->ln();
												$pdf->Cell(100,$dtl_ht, $grid_buyer, 0, 0);
												$pdf->ln();
											} 
											$pdf->Cell(110,$dtl_ht, $grid_no_desc, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_grp, 0, 0);
				
											$pdf->Cell(30,$dtl_ht, $grid_buy_sell_conv, 0, 0);
											$pdf->Cell(30,$dtl_ht, $grid_upc, 0, 0);
											//$pdf->Cell(15,$dtl_ht, $grid_upc_stat, 0, 0);
											$pdf->ln();
											$temp_buyer_code=$grid_buyer_code;
											break;
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
			$printed_by = "Printed By : ".$user_first_last;
			$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
		}
	}
	$pdf->Output();
?>
<title>Product Listing</title>
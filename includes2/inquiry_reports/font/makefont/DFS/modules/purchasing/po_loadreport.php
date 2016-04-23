<?php
	$ponum = $_REQUEST['pono'];
	$level = $_GET['level'];
	include "../../functions/inquiry_session.php";
	require('../inventory/lbd_function.php');
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";	
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	include "../../functions/inquiry_session.php";
	require('lbd_number.php');
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
	$pdf=new FPDF('l','mm','legal');
	$pdf->SetFont('Courier','');
	$pdf->SetFont('', '');
	$company_code = trim($company_code);
	$ponum = trim($ponum);
	$strCtr = "SELECT TOP 100 PERCENT tblPoItemDtl.poNumber, tblPoItemDtl.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, 
                      tblPoItemDtl.orderedQty, tblPoItemDtl.poUnitCost, tblPoItemDtl.prdConv, tblPoItemDtl.itemDiscPcents, tblPoItemDtl.poExtAmt, 
                      UPPER(tblPoItemDtl.umCode) AS umCode, tblPoHeader.compCode, tblSuppliers.suppCode, tblSuppliers.suppName, tblSuppliers.suppAddr1, tblSuppliers.suppTel,
                      tblPoHeader.poTerms, tblSuppliers.suppCurr, tblBuyers.buyerName, tblBuyers.buyerCode, tblPoItemDtl.compCode
					  FROM tblProdMast INNER JOIN
                      tblPoItemDtl ON tblProdMast.prdNumber = tblPoItemDtl.prdNumber INNER JOIN
                      tblPoHeader ON tblPoItemDtl.poNumber = tblPoHeader.poNumber INNER JOIN
                      tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
                      tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
					  WHERE (tblPoHeader.poNumber  = $ponum) AND (tblPoHeader.compCode = $company_code) AND (tblPoItemDtl.compCode = $company_code)
					  ORDER BY tblProdMast.prdDesc";
	$qryCtr = mssql_query($strCtr);
	$nCtr = mssql_num_rows($qryCtr);
	
	$i = 0;
	$maxline = 25;
	$npagemax = 1;
	$npagemax = $nCtr / $maxline;
	$npagemax = ceil($npagemax);
	if ($npagemax>1) {
		$npagemax2 =  $npagemax+1;
	} else {
		$npagemax2 = $npagemax;
	}
	$npage = 1;
	$nrw=0;
	$nrc=0;	
	$hspace=17;	
	
	showHeader($pdf,5,$npage,$npagemax);

	$strHead = "SELECT TOP 100 PERCENT tblPoItemDtl.poNumber, tblPoItemDtl.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, 
                      tblPoItemDtl.orderedQty, tblPoItemDtl.poUnitCost, tblPoItemDtl.prdConv, tblPoItemDtl.itemDiscPcents, tblPoItemDtl.poExtAmt, 
                      UPPER(tblPoItemDtl.umCode) AS umCode, tblPoHeader.compCode, tblSuppliers.suppCode, tblSuppliers.suppName, tblSuppliers.suppAddr1, tblSuppliers.suppTel,
                      tblPoHeader.poTerms, tblSuppliers.suppCurr, tblBuyers.buyerName, tblBuyers.buyerCode, tblPoItemDtl.compCode
					  FROM tblProdMast INNER JOIN
                      tblPoItemDtl ON tblProdMast.prdNumber = tblPoItemDtl.prdNumber INNER JOIN
                      tblPoHeader ON tblPoItemDtl.poNumber = tblPoHeader.poNumber INNER JOIN
                      tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
                      tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
					  WHERE tblPoHeader.poNumber  = $ponum and tblPoHeader.compCode = $company_code  AND (tblPoItemDtl.compCode = $company_code)
					  ORDER BY tblProdMast.prdDesc";
	$qryHead = mssql_query($strHead);
	$rstHead = mssql_fetch_array($qryHead);
	
	###########################################################
	$nrw = $nrw + 7;
	###########################################################
	$Vendor = str_replace("\\","",$rstHead['suppName']);
	########################################
	$Terms = $rstHead['poTerms'];
	if ($Terms>"") {
		$strTerms = "SELECT * FROM tblTerms WHERE trmCode = $Terms";
		$qryTerms = mssql_query($strTerms);
		$numTerms = mssql_num_rows($qryTerms);
		if ($numTerms >0){
			$TermsDesc=mssql_result($qryTerms,0,"trmDesc");
		} else {
			$TermsDesc="NA";
		}
	} else  {
		$TermsDesc="NA";
	}
	########################################
	
	$Curr = $rstHead['suppCurr'];
	$Buyer = $rstHead['buyerCode'] . " - " . $rstHead['buyerName'];
	$addr1 = $rstHead['suppAddr1'] . " / " . $rstHead['suppTel'];
	
	$pdf->Text(30,$nrw+$hspace,$Vendor);
	$pdf->Text(210,$nrw+$hspace,$Curr);
	
	$nrw = $nrw + 5;
	$pdf->Text(203,$nrw+$hspace,$Buyer);
	$pdf->Text(47,$nrw+$hspace,$addr1);

	$nrw = $nrw + 5;
	$pdf->Text(28,$nrw+$hspace,$Terms . "-" . $TermsDesc);

	//$strSQL = "SELECT * FROM VIEWPOSUMMARYDETAIL WHERE PONUMBER = '$ponum' AND compcode = $company_code ORDER BY PRDDESC ASC";
	$strSQL = "SELECT TOP 100 PERCENT tblPoItemDtl.poNumber, tblPoItemDtl.prdNumber, UPPER(tblProdMast.prdDesc) AS prdDesc, 
                      tblPoItemDtl.orderedQty, tblPoItemDtl.poUnitCost, tblPoItemDtl.prdConv, tblPoItemDtl.itemDiscPcents, tblPoItemDtl.poExtAmt, 
                      UPPER(tblPoItemDtl.umCode) AS umCode, tblPoHeader.compCode, tblSuppliers.suppCode, tblSuppliers.suppName, tblSuppliers.suppAddr1, tblSuppliers.suppTel, 
                      tblPoHeader.poTerms, tblSuppliers.suppCurr, tblBuyers.buyerName, tblBuyers.buyerCode, tblPoItemDtl.compCode
					  FROM tblProdMast INNER JOIN
                      tblPoItemDtl ON tblProdMast.prdNumber = tblPoItemDtl.prdNumber INNER JOIN
                      tblPoHeader ON tblPoItemDtl.poNumber = tblPoHeader.poNumber INNER JOIN
                      tblSuppliers ON tblPoHeader.suppCode = tblSuppliers.suppCode INNER JOIN
                      tblBuyers ON tblPoHeader.poBuyer = tblBuyers.buyerCode
					  WHERE tblPoHeader.poNumber  = $ponum and tblPoHeader.compCode = $company_code  AND (tblPoItemDtl.compCode = $company_code)
					  ORDER BY tblProdMast.prdDesc";
	$qrySQL = mssql_query($strSQL);
	$numqrySQL = mssql_num_rows($qrySQL);
	##################
	$nrw = $nrw + 12;
	##################
	
	for ($j=0; $j<$numqrySQL; $j++) 
	{
		$pdf->SetFont('Courier','',10);		
		$nrw = $nrw + 6;
		$prd_number = mssql_result($qrySQL,$j,"prdNumber");
		########################################
		$strUpc = "SELECT * FROM tblProdMast WHERE prdNumber = $prd_number";
		$qryUpc = mssql_query($strUpc);
		$numUpc = mssql_num_rows($qryUpc);
		if ($numUpc >0){
			$upcCode=mssql_result($qryUpc,0,"prdSuppItem");
			$upcCode = $upcCode*1;
		} else {
			$upcCode="NA";
		}
		########################################
		$pdf->Text(10,$nrw+$hspace,$prd_number." ".$upcCode);
		$prd_desc = str_replace("\\","",mssql_result($qrySQL,$j,"prdDesc"));
		$pdf->Text(53,$nrw+$hspace,strtoupper($prd_desc));
		$conv = number_format(mssql_result($qrySQL,$j,"prdConv"),0);
		$bum_conv = trim(mssql_result($qrySQL,$j,"umCode"))."/".$conv;
		$pdf->Text(140,$nrw+$hspace,$bum_conv);
		$pdf->Text(155,$nrw+$hspace,oa_fmenum(mssql_result($qrySQL,$j,"orderedQty"),12));
		$pdf->Text(185,$nrw+$hspace,oa_fmenum_4(mssql_result($qrySQL,$j,"poUnitCost"),14));
		$pdf->Text(236,$nrw+$hspace,mssql_result($qrySQL,$j,"itemDiscPcents"));
		$pdf->Text(275,$nrw+$hspace,oa_fmenum_4(mssql_result($qrySQL,$j,"poExtAmt"),14));
		$pageTotal = $pageTotal + mssql_result($qrySQL,$j,"poExtAmt");
		
		
		$nrc++;
		if ($nrc >= $maxline)
		{
			$nrw = $nrw + 10;
			$pdf->SetFont('Courier','',10);
			$pdf->SetFont('', '');
			$pdf->Text(200,$nrw+$hspace,"PAGE TOTAL");
			$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($pageTotal,14));
	
			$maxline = 25;
			$npage = $npage + 1;
			$nrw=0;
			$nrc=0;	
			$hspace=17;
			//showHeader($pdf,5,$npage,$npagemax);
			$pdf->AddPage();
						$gmt = time() + (8 * 60 * 60);
						$newdate = date("m/d/Y h:iA", $gmt);
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$newdate="RUN DATE :  ".$newdate;
						###################################
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						
						###################################
						$pdf->Text(10,$nrw,$newdate);
						$pdf->Text(280,$nrw,"PO :");
						$pdf->Text(297,$nrw,$_REQUEST['pono']);
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;		
						$pdf->Text(10,$nrw,"REPORT ID :");
						$pdf->Text(36,$nrw,"PO001P");
						$pdf->Text(280,$nrw,"PAGE :");
						$pdf->Text(297,$nrw,$npage . " of " . $npagemax2);
						$pdf->SetFont('Courier','',10);
						$pdf->SetFont('', '');
						$pdf->Cell(300,$nrw - 15,$comp_name,0,1,'C');
						if ($level=="print") {
							$pdf->Cell(300,$nrw - 7,"PURCHASE ORDER",0,1,'C');
						} else {
							$pdf->SetFont('Courier','B',12);
							$pdf->Cell(300,$nrw - 7,"PURCHASE ORDER (RE-PRINTED COPY)",0,1,'C');
							$pdf->SetFont('Courier','',10);
						}
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$pdf->Text(10,$nrw,str_pad("=",145,'-'));
						$nrw++;
						$nrw++;
						$nrw++;
						$pdf->Text(10,$nrw,"SKU");
						$pdf->Text(25,$nrw,"UPC");
						$pdf->Text(53,$nrw,"Description");		
						$pdf->Text(139,$nrw,"Buy");		
						$pdf->Text(208,$nrw,"Buy");
						$pdf->Text(173,$nrw,"Qty");
						$pdf->Text(290,$nrw,"Extended");
						$nrw++;
						$nrw++;
						$nrw++;		
						$pdf->Text(139,$nrw,"UM");
						$pdf->Text(165,$nrw,"Ordered");
						$pdf->Text(206,$nrwe,"Cost");
						$pdf->Text(236,$nrw,"Disc %");
						$pdf->Text(290,$nrw,"Amount");
						$nrw++;
						$nrw++;		
						$pdf->Text(10,$nrw,str_pad("=",145,'-'));
						$nrw=14;
		}
		
	}
	
	if ($npage >= $npagemax)
	{
		$strTotals = "SELECT SUM(POTOTEXT) AS POTOTEXT, SUM(POTOTDISC) AS POTOTDISC, SUM(POTOTALLOW) AS POTOTALLOW, SUM(POTOTMISC) AS POTOTMISC FROM TBLPOHEADER";
		$strTotals .= " WHERE PONUMBER = '$ponum' AND COMPCODE = $company_code";
		
		$qryTotals = mssql_query($strTotals);
		$rstTotals = mssql_fetch_array($qryTotals);

		$qryAllow = mssql_query("SELECT * FROM tblPOAllwDtl WHERE poNumber = $ponum AND compcode = $company_code");
		$numAllow = mssql_num_rows($qryAllow);
		
		$TotDisc = $rstTotals['POTOTDISC'];
		$TotAllow = $rstTotals['POTOTALLOW'];
		$TotMisc= $rstTotals['POTOTMISC'];
		$TotExtended=$rstTotals['POTOTEXT'];
		$TotalNet = $rstTotals['POTOTEXT'] - ($TotDisc + $TotAllow);
		$total_total = $TotalNet + $TotMisc;
		
		
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');

		$pdf->Text(200,$nrw+$hspace,"Gross Amount");
		$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($TotExtended,14));
		
		if ($TotDisc>0 || $numAllow>0) {
			$nrw = $nrw + 5;
			$pdf->Text(200,$nrw+$hspace,"Less:");
		}
		if ($TotDisc>0) {
			$nrw = $nrw + 5;
			$pdf->Text(206,$nrw+$hspace,"   SKU Discounts");
			$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($TotDisc,14));
		} 
				if ($numAllow >0){
					for ($i=0; $i<$numAllow; $i++) {
						$allow_code=mssql_result($qryAllow,$i,"allwTypeCode");
						$allow_amt=mssql_result($qryAllow,$i,"poAllwAmt");
						$percent = mssql_result($qryAllow,$i,"allwTypeCode");
						$percent = $percent."% ";
						$qryAllowDesc = mssql_query("SELECT * FROM tblAllowType WHERE allwTypeCode = $allow_code");
						$numAllowDesc = mssql_num_rows($qryAllowDesc);
						if ($numAllowDesc>0) {
							$others_desc = mssql_result($qryAllowDesc,0,"allwDesc");
							$others_desc = "   ".$percent . "- ". $others_desc;
						} else {
							$others_desc = "   ".$percent . "- " . "NA";
						}
						$nrw = $nrw + 5;
						$pdf->Text(206,$nrw+$hspace,$others_desc);
						$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($allow_amt,14));
					}
				} 
					
		$nrw = $nrw + 8;
		$pdf->Text(200,$nrw+$hspace,"Net Amount");
		$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($TotalNet,14));
		
		if ($TotMisc>0) {
			$nrw = $nrw + 8;
			$pdf->Text(200,$nrw+$hspace,"Plus: Miscelleneous Charges");
			$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($TotMisc,14));
		}
		
		$nrw = $nrw + 8;
		$pdf->Text(200,$nrw+$hspace,"Total Amount");
		$pdf->Text(275,$nrw+$hspace,oa_fmenum_4($total_total,14));
		##################################### get cents
		$number_to_word = int_to_words(oa_intonly($total_total));
		$total_total = number_format($total_total,4);
		$split_total = split("\.",$total_total);
		$new_total = $split_total[1];
		$new_total2 =$new_total[0].$new_total[1].".".$new_total[2].$new_total[3];
		$new_total2 = number_format($new_total2,0);
		if ($new_total2>0) {
			$number_to_word = $number_to_word. " AND ".$new_total2."/100";
		}	
		############################################
					if ($nrw>150) {
						$pdf->AddPage();
						$maxline = 25;
						$npage = $npage + 1;
						$nrw=0;
						$nrc=0;	
						$hspace=17;
						$gmt = time() + (8 * 60 * 60);
						$newdate = date("m/d/Y h:iA", $gmt);
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$newdate="RUN DATE :  ".$newdate;
						###################################
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						
						###################################
						$pdf->Text(10,$nrw,$newdate);
						$pdf->Text(280,$nrw,"PO :");
						$pdf->Text(297,$nrw,$_REQUEST['pono']);
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;		
						$pdf->Text(10,$nrw,"REPORT ID :");
						$pdf->Text(36,$nrw,"PO001P");
						$pdf->Text(280,$nrw,"PAGE :");
						$pdf->Text(297,$nrw,$npage . " of " . $npagemax2);
						$pdf->PageNo();
						
						$pdf->SetFont('Courier','',10);
						$pdf->SetFont('', '');
						$pdf->Cell(300,$nrw - 15,$comp_name,0,1,'C');
						if ($level=="print") {
							$pdf->Cell(300,$nrw - 7,"PURCHASE ORDER",0,1,'C');
						} else {
							$pdf->SetFont('Courier','B',12);
							$pdf->Cell(300,$nrw - 7,"PURCHASE ORDER (RE-PRINTED COPY)",0,1,'C');
							$pdf->SetFont('Courier','',10);
						}
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$nrw++;
						$pdf->Text(10,$nrw,str_pad("=",145,'-'));
						$nrw++;
						$nrw++;
						$nrw++;
						$pdf->Text(10,$nrw,"SKU");
						$pdf->Text(25,$nrw,"UPC");
						$pdf->Text(53,$nrw,"Description");		
						$pdf->Text(139,$nrw,"Buy");		
						$pdf->Text(208,$nrw,"Buy");
						$pdf->Text(173,$nrw,"Qty");
						$pdf->Text(290,$nrw,"Extended");
						$nrw++;
						$nrw++;
						$nrw++;		
						$pdf->Text(139,$nrw,"UM");
						$pdf->Text(165,$nrw,"Ordered");
						$pdf->Text(206,$nrwe,"Cost");
						$pdf->Text(236,$nrw,"Disc %");
						$pdf->Text(290,$nrw,"Amount");
						$nrw++;
						$nrw++;		
						$pdf->Text(10,$nrw,str_pad("=",145,'-'));
						
									$nrw = $nrw + 8;
						$pdf->Text(10,$nrw+$hspace,strtoupper("Amount in Words : " . $Curr . " " . $number_to_word));
						#################################### remarks
						$qryRemarks = mssql_query("SELECT * FROM tblPoRemarks WHERE poNumber = $ponum AND compCode = $company_code");
						$numRemarks = mssql_num_rows($qryRemarks);
						if ($numRemarks > 0) {
							$remarks1 = "";
							$remarks2 = "";
							$remarks3 = "";
							$remarks = mssql_result($qryRemarks,0,"remark");
							for ($art=0; $art <= 130; $art++) {
								$remarks1 = $remarks1 . $remarks[$art];
							}
							$remarks1 = "REMARKS : " . $remarks1;
							for ($art=131; $art <= 260; $art++) {
								$remarks2 = $remarks2 . $remarks[$art];
							}
							for ($art=261; $art <= 390; $art++) {
								$remarks3 = $remarks3 . $remarks[$art];
							}
						} else {
							$remarks1 = "";
							$remarks2 = "";
							$remarks3 = "";
						}
					//	echo $remarks;
						$nrw = $nrw + 8;
						$pdf->Text(10,$nrw+$hspace,strtoupper($remarks1));
						if ($remarks2>"") {
							$nrw = $nrw + 5;
							$pdf->Text(10,$nrw+$hspace,strtoupper("          " . $remarks2));
						}
						if ($remarks3>"") {
							$nrw = $nrw + 5;
							$pdf->Text(10,$nrw+$hspace,strtoupper("          " . $remarks3));
						}
					} else {
							$nrw = $nrw + 8;
						$pdf->Text(10,$nrw+$hspace,strtoupper("Amount in Words : " . $Curr . " " . $number_to_word));
						#################################### remarks
						$qryRemarks = mssql_query("SELECT * FROM tblPoRemarks WHERE poNumber = $ponum AND compCode = $company_code");
						$numRemarks = mssql_num_rows($qryRemarks);
						if ($numRemarks > 0) {
							$remarks1 = "";
							$remarks2 = "";
							$remarks3 = "";
							$remarks = mssql_result($qryRemarks,0,"remark");
							for ($art=0; $art <= 130; $art++) {
								$remarks1 = $remarks1 . $remarks[$art];
							}
							$remarks1 = "REMARKS : " . $remarks1;
							for ($art=131; $art <= 260; $art++) {
								$remarks2 = $remarks2 . $remarks[$art];
							}
							for ($art=261; $art <= 390; $art++) {
								$remarks3 = $remarks3 . $remarks[$art];
							}
						} else {
							$remarks1 = "";
							$remarks2 = "";
							$remarks3 = "";
						}
					//	echo $remarks;
						$nrw = $nrw + 8;
						$pdf->Text(10,$nrw+$hspace,strtoupper($remarks1));
						if ($remarks2>"") {
							$nrw = $nrw + 5;
							$pdf->Text(10,$nrw+$hspace,strtoupper("          " . $remarks2));
						}
						if ($remarks3>"") {
							$nrw = $nrw + 5;
							$pdf->Text(10,$nrw+$hspace,strtoupper("          " . $remarks3));
						}
					}
					$nrw = 150;
					$pdf->Text(10,$nrw+$hspace,str_pad("=",145,'-'));
					$nrw = $nrw + 5;
					$pdf->Text(10,$nrw+$hspace,"Prepared By / Date");
					$pdf->Text(83,$nrw+$hspace,"Noted By / Date");
					$pdf->Text(154,$nrw+$hspace,"Approved By / Date");
					
					$nrw = $nrw + 5;
					$pdf->Text(10,$nrw+$hspace,"______________________________");		
					$pdf->Text(83,$nrw+$hspace,"______________________________");		
					$pdf->Text(154,$nrw+$hspace,"______________________________");		
			
					$nrw = $nrw + 5;
					$pdf->Text(10,$nrw+$hspace,str_pad("=",145,'-'));
					
					$nrw = $nrw + 5;		
					$pdf->Text(10,$nrw+$hspace,"NOTE TO SUPPLIERS :");
			
					$nrw = $nrw + 5;		
					$pdf->Text(15,$nrw+$hspace,"1. Please submit the original copy of the invoice with the copy of the P.O. to the delivery address stated above for countering.");
					$nrw = $nrw + 5;		
					$pdf->Text(15,$nrw+$hspace,"2. Confirm your delivery at least 2 days before the intended delivery date.");
					$nrw = $nrw + 5;		
					$pdf->Text(15,$nrw+$hspace,"3. Any changes in the P.O. shall require prior approval of Buying Department at least 2 days before delivery.");
					$nrw = $nrw + 5;		
					$pdf->Text(15,$nrw+$hspace,"4. Please check P.O. prices before delivery. If there is any discrepancy it is the policy of KMC to follow the lower price.");
	}	
	
	$pdf->Output();

	function showHeader($pdf,$nrw,$npage,$npagemax)
	{
		$ponum = $_REQUEST['pono'];
		$level = $_GET['level'];
		include "../../functions/inquiry_session.php";
		$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
		$result_company=mssql_query($query_company);
		$num_company = mssql_num_rows($result_company);
		if ($num_company >0){
			$comp_name=mssql_result($result_company,$i,"compName");
		} else {
			$comp_name="NA";
		}
		$pdf->AddPage();
		$gmt = time() + (8 * 60 * 60);
		$newdate = date("m/d/Y h:iA", $gmt);
		$newdate="RUN DATE :  ".$newdate;
		###################################
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		
		###################################
		$pdf->Text(10,$nrw,$newdate);
		$pdf->Text(280,$nrw,"PO :");
		$pdf->Text(297,$nrw,$_REQUEST['pono']);
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw,"REPORT ID :");
		$pdf->Text(36,$nrw,"PO001P");
		$pdf->Text(280,$nrw,"PAGE :");
		if ($npagemax>1) {
			$npagemax2 =  $npagemax+1;
		}	else {
			$npagemax2 =  $npagemax;
		}
		$pdf->Text(297,$nrw,$npage . " of " . $npagemax2);
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		$pdf->Cell(300,$nrw - 15,$comp_name,0,1,'C');
		if ($level=="print") {
			$pdf->Cell(300,$nrw - 7,"PURCHASE ORDER",0,1,'C');
		} else {
			$pdf->SetFont('Courier','B',12);
			$pdf->Cell(300,$nrw - 7,"PURCHASE ORDER (RE-PRINTED COPY)",0,1,'C');
			$pdf->SetFont('Courier','',10);
		}
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;
				

		if ($npage<2) {
			$pdf->Text(10,$nrw+$hspace,"Vendor :");
			$pdf->Text(185,$nrw+$hspace,"Currency :");
			
			$nrw++;
			$nrw++;		
			$nrw++;		
			$nrw++;
			$nrw++;
			$pdf->Text(10,$nrw+$hspace,"Address/Tel.No :");
			$pdf->Text(185,$nrw+$hspace,"Buyer :");
			
			$nrw++;
			$nrw++;		
			$nrw++;
			$nrw++;
			$nrw++;
			$pdf->Text(10,$nrw+$hspace,"Terms :");
			
			$nrw++;		
			$nrw++;
			$nrw++;
			$nrw++;		
			$nrw++;
		}
		$pdf->Text(10,$nrw+$hspace,str_pad("=",145,'-'));
		$nrw++;
		$nrw++;
		$nrw++;
		$pdf->Text(10,$nrw+$hspace,"SKU");
		$pdf->Text(25,$nrw+$hspace,"UPC");
		$pdf->Text(53,$nrw+$hspace,"Description");		
		$pdf->Text(139,$nrw+$hspace,"Buy");		
		$pdf->Text(208,$nrw+$hspace,"Buy");
		$pdf->Text(173,$nrw+$hspace,"Qty");
		$pdf->Text(290,$nrw+$hspace,"Extended");
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(139,$nrw+$hspace,"UM");
		$pdf->Text(165,$nrw+$hspace,"Ordered");
		$pdf->Text(206,$nrw+$hspace,"Cost");
		$pdf->Text(236,$nrw+$hspace,"Disc %");
		$pdf->Text(290,$nrw+$hspace,"Amount");
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw+$hspace,str_pad("=",145,'-'));
		
	}
	
	$gmt = time() + (8 * 60 * 60);
	$newdate2 = date("m/d/Y", $gmt);
	if ($level == "print") {
		$UpdateSQL = "UPDATE tblPoAudit SET ";
		$UpdateSQL .= "poPrntDate = '".$newdate2."', ";
		$UpdateSQL .= "poPrntOptr = '".$user_first_last."' ";
		$UpdateSQL .= "WHERE poNumber = " . $ponum ;
		mssql_query($UpdateSQL); 
	}
	if ($level == "reprint") {
		$UpdateSQL = "UPDATE tblPoAudit SET ";
		$UpdateSQL .= "poReprntDate = '".$newdate2."', ";
		$UpdateSQL .= "poReprntOptr = '".$user_first_last."' ";
		$UpdateSQL .= "WHERE poNumber = " . $ponum ;
		mssql_query($UpdateSQL); 
	}
?>



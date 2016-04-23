<?
if ($level == "original") {
	$TBLPOHEADER = "TBLPOHEADER1";
} else {
	$TBLPOHEADER = "TBLPOHEADER";
}
$strTotals = "SELECT SUM(POTOTEXT) AS POTOTEXT, SUM(POTOTDISC) AS POTOTDISC, SUM(POTOTALLOW) AS POTOTALLOW, SUM(POTOTMISC) AS POTOTMISC FROM $TBLPOHEADER";
$strTotals .= " WHERE PONUMBER = '$ponum' AND COMPCODE = $company_code";
$qryTotals = mssql_query($strTotals);
$rstTotals = mssql_fetch_array($qryTotals);
$TotDisc = $rstTotals['POTOTDISC'];
$TotAllow = $rstTotals['POTOTALLOW'];
$TotMisc= $rstTotals['POTOTMISC'];
$TotExtended=$rstTotals['POTOTEXT'];
$TotalNet = $rstTotals['POTOTEXT'] - ($TotDisc + $TotAllow);
$total_total = $TotalNet + $TotMisc;

$pdf->ln();
$pdf->Cell(183,$dtl_ht, "" , 0, 0); 
$pdf->Cell(23,$dtl_ht, "Gross Amount : " , 0, 0);
$pdf->Cell(103,$dtl_ht, number_format($TotExtended,4), 0, 0,'R');

if ($TotDisc>0 || $numAllow>0) {
	$pdf->ln();
	$pdf->Cell(183,$dtl_ht, "" , 0, 0);
	$pdf->Cell(250,$dtl_ht, "Less : " , 0, 0);
}
if ($TotDisc>0) {
	$pdf->ln();
	$pdf->Cell(183,$dtl_ht, "" , 0, 0);
	$pdf->Cell(23,$dtl_ht, "   SKU Discount : " , 0, 0);
	$pdf->Cell(103,$dtl_ht, number_format($TotDisc,4), 0, 0,'R');
} 
		if ($numAllow >0){
			for ($i=0; $i<$numAllow; $i++) {
				$allow_code=mssql_result($qryAllow,$i,"allwTypeCode");
				$allow_amt=mssql_result($qryAllow,$i,"poAllwAmt");
				$percent = mssql_result($qryAllow,$i,"poAllwPcnt");
				$percent = $percent."% ";
				$qryAllowDesc = mssql_query("SELECT * FROM tblAllowType WHERE allwTypeCode = $allow_code");
				$numAllowDesc = mssql_num_rows($qryAllowDesc);
				if ($numAllowDesc>0) {
					$others_desc = mssql_result($qryAllowDesc,0,"allwDesc");
					$others_desc = "   ".$percent . "- ". $others_desc;
				} else {
					$others_desc = "   ".$percent . "- " . "NA";
				}
				$pdf->ln();
				$pdf->Cell(183,$dtl_ht, "" , 0, 0);
				$pdf->Cell(23,$dtl_ht, $others_desc , 0, 0);
				$pdf->Cell(103,$dtl_ht, number_format($allow_amt,4), 0, 0,'R');
			}
		} 
			
$pdf->ln();
$pdf->Cell(183,$dtl_ht, "" , 0, 0);
$pdf->Cell(23,$dtl_ht, "Net Amount : " , 0, 0);
$pdf->Cell(103,$dtl_ht, number_format($TotalNet,4), 0, 0,'R');

/*if ($TotMisc>0) {
	$pdf->ln();
	$pdf->Cell(183,$dtl_ht, "" , 0, 0);
	$pdf->Cell(23,$dtl_ht, "Plus: Miscelleneous Charges" , 0, 0);
	$pdf->Cell(103,$dtl_ht, number_format($TotMisc,4), 0, 0,'R');
}*/
		if ($numMisc >0){
			$pdf->ln();
			$pdf->Cell(183,$dtl_ht, "" , 0, 0);
			$pdf->Cell(23,$dtl_ht, "Plus: Miscelleneous Charges" , 0, 0);
			for ($i=0; $i<$numMisc; $i++) {
				$misc_desc=mssql_result($qryMisc,$i,"poMiscSeq") . ". " . mssql_result($qryMisc,$i,"poMiscDesc");
				$misc_amt=mssql_result($qryMisc,$i,"poMiscAmt");
				$pdf->ln();
				$pdf->Cell(183,$dtl_ht, "" , 0, 0);
				$pdf->Cell(23,$dtl_ht, "   ".$misc_desc , 0, 0);
				$pdf->Cell(103,$dtl_ht, number_format($misc_amt,4), 0, 0,'R');
			}
		} 

$pdf->ln();
$pdf->Cell(183,$dtl_ht, "" , 0, 0);
$pdf->Cell(23,$dtl_ht, "Total Amount" , 0, 0);
$pdf->Cell(103,$dtl_ht, number_format($total_total,4), 0, 0,'R');

?>

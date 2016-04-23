<? 
		$page=$page+1;
		$ctr=10;
		$getYLock=0;
		$pdf->Text(10,$ctr,$newdate);
		$pdf->Text(100,$ctr,$comp_name);
		$pdf->Text(170,$ctr,"Page ".$page. " of ".$m_page);
		$ctr=$ctr+5;
		$pdf->Text(10,$ctr,"Report ID : DLYSLSFR");
		$pdf->Text(85,$ctr,"Daily Sales Flash Report");
			$ctr=$ctr+3;
			$pdf->Text(10, $ctr, '__________________________________________________________________________________________');
			$ctr=$ctr+4;
			$pdf->Text(10,$ctr, 'PRODUCT GROUP');
			$pdf->Text(95,$ctr, 'GROSS AMT');
			$pdf->Text(137,$ctr, 'DISC AMT');
			$ctr=$ctr+1;
			$pdf->Text(10,$ctr, '__________________________________________________________________________________________');
?>
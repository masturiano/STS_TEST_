<? 
		$page=$page+1;
		$ctr=10;
		$getYLock=0;
		$pdf->Text(10,$ctr,$newdate);
		$pdf->Text(100,$ctr,$comp_name);
		$pdf->Text(170,$ctr,"Page ".$page. " of ".$m_page);
		$ctr=$ctr+5;
		$pdf->Text(10,$ctr,"Report ID : DLYSLSPR");
		$pdf->Text(85,$ctr,"Daily Sales Summary Report (By Product)");
			$ctr=$ctr+3;
			$pdf->Text(10, $ctr, '__________________________________________________________________________________________');
			$ctr=$ctr+4;
			$pdf->Text(10,$ctr, '');
			$pdf->Text(115,$ctr, 'UNIT');
			$pdf->Text(140,$ctr, 'QTY');
			$pdf->Text(165,$ctr, 'NET');
			$ctr=$ctr+3;
			$pdf->Text(10,$ctr, 'PRODUCT CODE AND DESCRIPTION');
			$pdf->Text(114,$ctr, 'PRICE');
			$pdf->Text(139,$ctr, 'SOLD');
			$pdf->Text(165,$ctr, 'AMT');
			$pdf->Text(185,$ctr, 'DISC');
			$ctr=$ctr+1;
			$pdf->Text(10,$ctr, '__________________________________________________________________________________________');
?>
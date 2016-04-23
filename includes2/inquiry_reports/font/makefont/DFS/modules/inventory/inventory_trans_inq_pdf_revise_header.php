<? 
		$page=$page+1;
		$ctr=10;
		$getYLock=0;
		$pdf->Text(10,$ctr,$newdate);
		$pdf->Text(160,$ctr,$comp_name);
		$pdf->Text(285,$ctr,"Page ".$page. " of ".$m_page);
		$ctr=$ctr+5;
		$pdf->Text(10,$ctr,"Report ID : INV009I");
		$pdf->Text(140,$ctr,"INVENTORY TRANSACTIONS REGISTER");
		
		$ctr=$ctr+5;
		$pdf->Text(133,$ctr,$from_to);
		$ctr=$ctr+3;
		$pdf->Text(10,$ctr, '__________________________________________________________________________________________________________________________________________________');
		$ctr=$ctr+4;
		$pdf->Text(10,$ctr, 'DATE');
		$pdf->Text(36,$ctr, 'DOC NO');
		$pdf->Text(56,$ctr, 'REF NO');
		$pdf->Text(76,$ctr, 'LOCATION');
		$pdf->Text(106,$ctr, 'TYPE');
		$pdf->Text(126,$ctr, 'BUSINESS PARTNER');
		$pdf->Text(187,$ctr, 'PRICE/COST');
		$pdf->Text(222,$ctr, 'REG');
		$pdf->Text(240,$ctr, 'FREE');
		$pdf->Text(264,$ctr, 'BO');
		$pdf->Text(279,$ctr, 'EXT AMT');
		$pdf->Text(302,$ctr, 'TOT DISC');
		//$ctr=$ctr+1;
		$pdf->Text(10,$ctr, '__________________________________________________________________________________________________________________________________________________');
?>
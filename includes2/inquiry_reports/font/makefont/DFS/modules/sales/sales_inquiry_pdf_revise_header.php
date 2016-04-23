<? 
		$page=$page+1;
		$ctr=10;
		$getYLock=0;
		$pdf->Text(10,$ctr,$newdate);
		$pdf->Text(100,$ctr,$comp_name);
		$pdf->Text(170,$ctr,"Page ".$page. " of ".$m_page);
		$ctr=$ctr+5;
		$pdf->Text(10,$ctr,"Report ID : SALES001R");
		$pdf->Text(83,$ctr,"Sales Transactions Register");
		if ($by_search=="by_product") { ####### SEARCH BY PRODUCT
			$ctr=$ctr+5;
			$pdf->Text(85,$ctr,$from_to);
			if ($page==1) {
				$ctr=$ctr+5;
				if ($code_desc=="check_code") {
					$pdf->Text(10,$ctr,"Product Code from: " . $prod_code1);
					$ctr=$ctr+3;
					$pdf->Text(10,$ctr,"               to: " . $prod_code2);
				} else {
					$pdf->Text(10,$ctr,"Product Desc from: " . $prod_code1);
					$ctr=$ctr+3;
					$pdf->Text(10,$ctr,"               to: " . $prod_code2);
				}
			}
			$ctr=$ctr+3;
			$pdf->Text(10, $ctr, '__________________________________________________________________________________________');
			$ctr=$ctr+4;
			$pdf->Text(10,$ctr, '');
			$pdf->Text(51,$ctr, 'UNIT');
			$pdf->Text(81,$ctr, 'QTY');
			$pdf->Text(110,$ctr, 'NET');
			$ctr=$ctr+3;
			$pdf->Text(10,$ctr, 'DATE');
			$pdf->Text(50,$ctr, 'PRICE');
			$pdf->Text(80,$ctr, 'SOLD');
			$pdf->Text(110,$ctr, 'AMT');
			$pdf->Text(140,$ctr, 'DISC');
			$ctr=$ctr+1;
			$pdf->Text(10,$ctr, '__________________________________________________________________________________________');
		} else {
			$ctr=$ctr+5;
			$pdf->Text(85,$ctr,$from_to);
			if ($page==1) {
				$ctr=$ctr+5;
				if ($subcls_=="") {
					$subcls_="All";
				}
				if ($cls_=="") {
					$cls_="All";
				}
				if ($dept_=="") {
					$dept_="All";
				}
				$pdf->Text(10,$ctr,"Group: " . $group_);
				$pdf->Text(60,$ctr,"     Dept: " . $dept_);
				$ctr=$ctr+3;
				$pdf->Text(10,$ctr,"Class: " . $cls_);
				$pdf->Text(60,$ctr,"Sub-Class: " . $subcls_);
			}
			$ctr=$ctr+3;
			$pdf->Text(10,$ctr, '__________________________________________________________________________________________');
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
		}
?>
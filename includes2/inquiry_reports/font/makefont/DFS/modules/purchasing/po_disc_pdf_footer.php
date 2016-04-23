<?
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
				$pdf->ln();
				$pdf->ln();
				$pdf->Cell(10,$dtl_ht, strtoupper("Amount in Words : " . $suppCurr . " " . $number_to_word), 0, 0);
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
				$pdf->ln();
				$pdf->ln();
				$pdf->Text(10,$nrw+$hspace,strtoupper($remarks1));
				$pdf->Cell(10,$dtl_ht,strtoupper($remarks1), 0, 0);
				if ($remarks2>"") {
					$pdf->ln();
					$pdf->Cell(10,$dtl_ht,strtoupper($remarks2), 0, 0);
				}
				
				$nrw=150;
				$hspace=17;
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

?>
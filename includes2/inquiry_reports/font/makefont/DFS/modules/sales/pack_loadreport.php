<?php
	$cinum = $_REQUEST['cino'];
	require('../inventory/lbd_function.php');
	
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require('../inventory/mypdf.php');
	define('FPDF_FONTPATH','../inventory/fonts/');
	
	require('../inventory/fpdf_js.php');	
	require('../inventory/fpdf_auto_print.php');	
	
	$db = new DB;
	$db->connect();
	
	
	$pdf=new FPDF('p','mm','letter');
	$pdf->SetFont('Courier','');
	$pdf->SetFont('', '');

	$strCtr = "SELECT * FROM VIEWCISUMMARY WHERE CINUMBER = '$cinum'";
	$qryCtr = mssql_query($strCtr);
	$nCtr = mssql_num_rows($qryCtr);

	$maxline = 50;
	$nn = 0;
	$npagemax = 1;
	$npagemax = $nCtr / $maxline;
	$npagemax = ceil($npagemax);
	$mm = 0;
	$npage = 1;
	$nrw=0;
	$nrc=0;	
	$hspace=17;
	$mspace = 4;

	showHeader($pdf,5,$npage,$npagemax);

	$strHead = "SELECT * FROM VIEWCISUMMARY WHERE CINUMBER = '$cinum'";
	$qryHead = mssql_query($strHead);
	$rstHead = mssql_fetch_array($qryHead);
	
	$nrw = $nrw + 5;
	$CI = $rstHead[0];
	$From =	$rstHead[2];
	$STRF = $rstHead[7];
	$SoldTo = $rstHead[3];
	$CIDate = $rstHead[1];
	$Terms = $rstHead[8] . " DAYS";
	$Remarks = $rstHead[9];
	
	$pdf->Text(30,$nrw+$hspace,$From);
	$pdf->Text(130,$nrw+$hspace,$STRF);	
	
	$nrw = $nrw + 3;
	$pdf->Text(30,$nrw+$hspace,$SoldTo);
	$pdf->Text(130,$nrw+$hspace,$CIDate);	

	$nrw = $nrw + 3;
	$pdf->Text(130,$nrw+$hspace,$Terms);	
	
	$strDetl = "SELECT * FROM VIEWCISUMMARYDETAIL WHERE CINUMBER = '". $CI . "'";
	$qryDetl = mssql_query($strDetl);
	while ($rstDetl = mssql_fetch_array($qryDetl))
	{
		$nrw = $nrw + 20;	
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');
	
		$SKU = $rstDetl[1];
		$Desc = $rstDetl[2];
		$Conv = $rstDetl[3];
		$PckReg = $rstDetl[4];
		$PckFree = $rstDetl[5];
		$UntReg = $rstDetl[6];
		$UntFree = $rstDetl[7];
		$TotUnits = (($PckReg + $PckFree) * $Conv) + $UntReg + $UntFree;
				
		$pdf->Text(10,$nrw+$hspace,$SKU);
		$pdf->Text(25,$nrw+$hspace,$Desc);		
		$pdf->Text(86,$nrw+$hspace,oa_intonly($Conv,0));		
		$pdf->Text(115,$nrw+$hspace,oa_intonly($PckReg,0));		
		$pdf->Text(133,$nrw+$hspace,oa_intonly($PckFree,0));				
		$pdf->Text(151,$nrw+$hspace,oa_intonly($UntReg,0));				
		$pdf->Text(169,$nrw+$hspace,oa_intonly($UntFree,0));				
		$pdf->Text(198,$nrw+$hspace,oa_intonly($TotUnits,0));	
		
		$TotPckReg = $TotPckReg + $PckReg;
		$TotPckFree = $TotPckFree + $PckFree;
		$TotUntReg = $TotUntReg + $UntReg;
		$TotUntFree = $TotUntFree + $UntFree;
		
		$Total = $Total + $TotUnits;
	}

	$pdf->SetFont('Courier','',8);
	$pdf->SetFont('', 'B');
	
	$nrw = $nrw + 5;
	$pdf->Text(75,$nrw+$hspace,"Totals");
	$pdf->Text(115,$nrw+$hspace,oa_intonly($TotPckReg,0));		
	$pdf->Text(133,$nrw+$hspace,oa_intonly($TotPckFree,0));				
	$pdf->Text(151,$nrw+$hspace,oa_intonly($TotUntReg,0));				
	$pdf->Text(169,$nrw+$hspace,oa_intonly($TotUntFree,0));				
	$pdf->Text(198,$nrw+$hspace,oa_intonly($Total,0));	
	
	$nrw = $nrw + 190;
	$pdf->Text(30,$nrw+$hspace,$Remarks);
	$pdf->Text(90,$nrw+$hspace,"Approved for Release");
	$pdf->Text(125,$nrw+$hspace,": ____________________");
	$pdf->Text(165,$nrw+$hspace,"Date : _______________");

	$nrw = $nrw + 3;
	$pdf->Text(125,$nrw+$hspace,"(Sign over printed Name)");
	
	$nrw = $nrw + 10;
	$pdf->Text(90,$nrw+$hspace,"Received by");
	$pdf->Text(125,$nrw+$hspace,": ____________________");
	$pdf->Text(165,$nrw+$hspace,"Date : _______________");

	$nrw = $nrw + 3;
	$pdf->Text(125,$nrw+$hspace,"(Sign over printed Name)");
	
	$pdf->Output();
	
	function showHeader($pdf,$nrw,$npage,$npagemax)
	{
		$pdf->AddPage();
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', 'B');
		$pdf->Text(10,$nrw,"RUN DATE");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(170,$nrw,"CI");
		$pdf->Text(185,$nrw,":");
		$pdf->Text(190,$nrw,$_REQUEST['cino']);
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw,"REPORT ID");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,"CI001P");
		$pdf->Text(170,$nrw,"PAGE");
		$pdf->Text(185,$nrw,":");
		$pdf->Text(190,$nrw,$npage . " of " . $npagemax);
		$pdf->SetFont('Courier','',14);
		$pdf->SetFont('', 'B');
		$pdf->Cell(195,$nrw - 10,"PUREGOLD PRICE CLUB, INC.",0,1,'C');
		$pdf->Cell(195,$nrw + 3,"PACKING LIST",0,1,'C');

		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;
		$nrw++;		
		$nrw++;		
		$nrw++;		
		$nrw++;		
		

		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', 'B');
		$pdf->Text(10,$nrw+$hspace,"From");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(100,$nrw+$hspace,"Ref. STRF No");
		$pdf->Text(124,$nrw+$hspace,":");
		
		$nrw++;
		$nrw++;		
		$nrw++;		
		
		$pdf->Text(10,$nrw+$hspace,"Sold To");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(100,$nrw+$hspace,"CI Date");
		$pdf->Text(124,$nrw+$hspace,":");
			
		$nrw++;		
		$nrw++;
		$nrw++;
	
		$pdf->Text(100,$nrw+$hspace,"Terms");
		$pdf->Text(124,$nrw+$hspace,":");

		$nrw++;
		
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		
		$pdf->Text(10,$nrw+$hspace,str_pad("=",115,'='));

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,"SKU");
		$pdf->Text(25,$nrw+$hspace,"Description");
		$pdf->Text(70,$nrw+$hspace,"Units/Pack");
		$pdf->Text(110,$nrw+$hspace,"Qty (Packs)");
		$pdf->Text(145,$nrw+$hspace,"Qty (Units)");
		
		$pdf->Text(180,$nrw+$hspace,"Total Qty");
	
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(75,$nrw+$hspace,"Conv");
		$pdf->Text(108,$nrw+$hspace,"Reg      Free");
		$pdf->Text(143,$nrw+$hspace,"Reg      Free");
		$pdf->Text(182,$nrw+$hspace,"(Units)");

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,str_pad("=",115,'='));
		$nrw = $nrw + $pagitan;	
		
		$nrw = $nrw + 200;
		$pdf->Text(10,$nrw+$hspace,"Remarks");
		$pdf->Text(25,$nrw+$hspace,":");		
	}
?>



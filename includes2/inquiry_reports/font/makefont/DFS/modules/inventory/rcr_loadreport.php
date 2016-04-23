<?php
//****************************************************************************
//**	Program Name			:	RCRLIST
//**	Program Description 	:	RCR Listing
//**	Author					:	Louie B. Datuin
//****************************************************************************
	include "../../functions/inquiry_session.php";
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	//require('../../functions/mypdf.php');
	//define('FPDF_FONTPATH','../../functions/fonts/');
	
	//require('../../functions/fpdf_js.php');	
	//require('../../functions/fpdf_auto_print.php');	

	$rcrnum = $_REQUEST['rcrno'];
	require('lbd_function.php');
	
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	
	$db = new DB;
	$db->connect();
	
	
	$pdf=new FPDF('p','mm','letter');
	$pdf->SetFont('Courier','');
	$pdf->SetFont('', '');

	$strCtr = "SELECT * FROM VIEWRECEIPTSUMMARYDETAIL WHERE RCRNUMBER = '$rcrnum'";
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

	$strHead = "SELECT * FROM VIEWRECEIPTSUMMARYDETAIL WHERE RCRNUMBER = '$rcrnum'";
	$qryHead = mssql_query($strHead);
	$rstHead = mssql_fetch_array($qryHead);
		
	$Location = $rstHead[8] . " - " . strtoupper($rstHead[9]);
	$Vendor = $rstHead[2] . " - " . strtoupper($rstHead[3]);
	$Carrier = strtoupper($rstHead[12]);
	$Container = strtoupper($rstHead[13]);
	$Terms = $rstHead[16] . " DAYS";
	$PO = $rstHead[4] . " - " . $rstHead[24]; 
	$RA = $rstHead[10] . " - " . $rstHead[11];
	$Buyer = $rstHead[14] . " - " . strtoupper($rstHead[15]);

	$nrw = $nrw + 10;
	$pdf->Text(30,$nrw+$hspace,$Location);	
	$pdf->Text(180,$nrw+$hspace,$Carrier);		
	$pdf->Text(115,$nrw+$hspace,$Terms);

	$nrw++;	
	$nrw++;
	$nrw++;		
	
	$pdf->Text(30,$nrw+$hspace,$Vendor);	
	$pdf->Text(180,$nrw+$hspace,$Container);

	$nrw++;	
	$nrw++;
	$nrw++;		

	$pdf->Text(30,$nrw+$hspace,$PO);
	$pdf->Text(180,$nrw+$hspace,$Buyer);
	
	$nrw++;	
	$nrw++;
	$nrw++;		
	$pdf->Text(30,$nrw+$hspace,$RA);	
	
	$strSQL = "SELECT * FROM VIEWRECEIPTSUMMARYDETAIL WHERE RCRNUMBER = '$rcrnum'";
	$qrySQL = mssql_query($strSQL);

	$nrw = $nrw + 16;

	while ($rstSQL = mssql_fetch_array($qrySQL))
	{
		$SKU = $rstSQL[6];
		$Desc = $rstSQL[7];
		$Desc = $rstSQL[7];
		$Conv = $rstSQL[22] . " x " . oa_intonly($rstSQL[23],0);
		$qtyOrd = oa_fmenum($rstSQL[20],12);
		$UnitCost = oa_fmenum_4($rstSQL[21],12);
		$qtyGood = oa_fmenum($rstSQL[17],12);
		$qtyBad = oa_fmenum($rstSQL[19],12);
		$qtyFree = oa_fmenum($rstSQL[18],12);
		$ExtAmt = oa_fmenum_4($rstSQL[5],12);
		
		$totGood = $totGood + $qtyGood;
		$totBad = $totBad + $qtyBad;
		$totFree = $totFree + $qtyFree;
		$totAmt = $totAmt + $ExtAmt;
		
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');	
		$nrw = $nrw + 3;					
		$pdf->Text(10,$nrw+$hspace,$SKU);
		$pdf->Text(25,$nrw+$hspace,$Desc);
		$pdf->Text(78,$nrw+$hspace,$Conv);
		$pdf->Text(95,$nrw+$hspace,$qtyOrd);	
		$pdf->Text(115,$nrw+$hspace,$UnitCost);	
		$pdf->Text(131,$nrw+$hspace,$qtyGood);		
		$pdf->Text(145,$nrw+$hspace,$qtyBad);		
		$pdf->Text(159,$nrw+$hspace,$qtyFree);		
		$pdf->Text(184,$nrw+$hspace,$ExtAmt);		
		
		$nrc++;
		if ($nrc >= $maxline)
		{
			$nrw = $nrw + 10;
			$pdf->Text(75,$nrw+$hspace,"PAGE TOTAL");
		
			$nrc=0; 
			$nrw=0;
			$pageTotal = 0;
			$npage = $npage + 1;
			$hspace = 25;
			$maxline = 50;
			showHeader($pdf,5,$npage,$npagemax);
			$nrw = $nrw + 38;			
		}
	}
	
	
	$nrw = $nrw + 150;
	
	if ($npagemax > 1)
	{
	
	}
	else
	{			
		$strCharges = "SELECT SUM(RCRDISCAMTTOTAL) AS RCRDISCAMTTOTAL, SUM(RCRALLWAMTTOTAL) AS RCRALLWAMTTOTAL, SUM(RCRADDCHARGESTOTAL) AS RCRADDCHARGESTOTAL ";
		$strCharges .= "FROM TBLRCRHEADER WHERE RCRNUMBER = '$rcrnum'";
		$qryCharges = mssql_query($strCharges);
		$rstCharges = mssql_fetch_array($qryCharges);
	
		$TotalDisc = oa_fmenum_4($rstCharges['RCRDISCAMTTOTAL'],12);		
		$TotalVen =	oa_fmenum_4($rstCharges['RCRDISCAMTTOTAL'],12);		
		$TotalAllow = oa_fmenum_4($rstCharges['RCRALLWAMTTOTAL'],12);		
		$TotalCharges = oa_fmenum_4($rstCharges['RCRADDCHARGESTOTAL'],12);		

		$NetCharges = $totAmt - $TotalDisc - $TotalVen - $TotalAllow + $TotalCharges;
		$nrw = $nrw + $mspace;	
		$nrw = $nrw + $mspace;		
	
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', 'B');
	
		$pdf->Text(75,$nrw+$hspace,"TOTAL");
		$pdf->Text(131,$nrw+$hspace,oa_fmenum($totGood,12));
		$pdf->Text(145,$nrw+$hspace,oa_fmenum($totBad,12));
		$pdf->Text(159,$nrw+$hspace,oa_fmenum($totFree,12));
		$pdf->Text(184,$nrw+$hspace,oa_fmenum_4($totAmt,12));

		$nrw = $nrw + 5;
		$pdf->Text(75,$nrw+$hspace,"SKU DISCOUNTS");
		$pdf->Text(184,$nrw+$hspace,$TotalDisc);				

		$nrw = $nrw + 3;
		$pdf->Text(75,$nrw+$hspace,"VENDOR ALLOWANCES");
		$pdf->Text(184,$nrw+$hspace,$TotalAllow);				

		$nrw = $nrw + 5;
		$pdf->Text(75,$nrw+$hspace,"CHARGES");
		$pdf->Text(184,$nrw+$hspace,$TotalCharges);				

		$nrw = $nrw + 7;
		$pdf->Text(75,$nrw+$hspace,"NET AMOUNT");
		$pdf->Text(184,$nrw+$hspace,oa_fmenum_4($NetCharges,12));				
		
	}
	$pdf->Output();
	
	function showHeader($pdf,$nrw,$npage,$npagemax)
	{
		include "../../functions/inquiry_session.php";
		$pdf->AddPage();
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', 'B');

		$gmt = time() + (8 * 60 * 60);
		$date = date("m/d/	Y H:i:s", $gmt);
		
		$pdf->Text(10,$nrw,"RUN DATE");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,$date);
		
		$pdf->Text(170,$nrw,"RCR");
		$pdf->Text(185,$nrw,":");
		$pdf->Text(190,$nrw,$_REQUEST['rcrno']);
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw,"REPORT ID");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,"RCR004P");
		$pdf->Text(170,$nrw,"PAGE");
		$pdf->Text(185,$nrw,":");
		$pdf->Text(190,$nrw,$npage . " of " . $npagemax);
		$pdf->SetFont('Courier','',14);
		$pdf->SetFont('', 'B');
		
		$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
		$result_company=mssql_query($query_company);
		$num_company = mssql_num_rows($result_company);
		if ($num_company >0){
			$comp_name=mssql_result($result_company,$i,"compName");
		} else {
			$comp_name="NA";
		}
	
		$pdf->Cell(195,$nrw - 10,$comp_name,0,1,'C');
		$pdf->Cell(195,$nrw + 3,"RECEIVER CONFIRMATION LISTING",0,1,'C');

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
		$nrw++;
		$nrw++;		
		$nrw++;		
		$nrw++;		
		$nrw++;		
		

		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', 'B');
		$pdf->Text(10,$nrw+$hspace,"Location");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(100,$nrw+$hspace,"Terms");
		$pdf->Text(110,$nrw+$hspace,":");
		$pdf->Text(155,$nrw+$hspace,"Carrier");
		$pdf->Text(175,$nrw+$hspace,":");
		
		$nrw++;
		$nrw++;		
		$nrw++;		
		
		$pdf->Text(10,$nrw+$hspace,"Vendor");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(155,$nrw+$hspace,"Container");
		$pdf->Text(175,$nrw+$hspace,":");
			
		$nrw++;		
		$nrw++;
		$nrw++;
	
		$pdf->Text(10,$nrw+$hspace,"Ref. PO");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(155,$nrw+$hspace,"Buyer");
		$pdf->Text(175,$nrw+$hspace,":");

		$nrw++;
		$nrw++;
		$nrw++;
		
		$pdf->Text(10,$nrw+$hspace,"Ref. RA");
		$pdf->Text(25,$nrw+$hspace,":");

		
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
		$pdf->Text(80,$nrw+$hspace,"Buy");
		$pdf->Text(100,$nrw+$hspace,"Qty (Pcs)");
		$pdf->Text(120,$nrw+$hspace,"Buy Cost");
		$pdf->Text(145,$nrw+$hspace,"Qty Rcvd (Pcs)");
		$pdf->Text(185,$nrw+$hspace,"Extended");
	
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(75,$nrw+$hspace,"U/M - Conv");
		$pdf->Text(101,$nrw+$hspace,"Ordered");
		$pdf->Text(138,$nrw+$hspace,"Good");
		$pdf->Text(153,$nrw+$hspace,"BO");
		$pdf->Text(165,$nrw+$hspace,"Free");
		$pdf->Text(187,$nrw+$hspace,"Amount");

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,str_pad("=",115,'='));
		$nrw = $nrw + $pagitan;	
		
		$nrw = $nrw + 200;
		$pdf->Text(10,$nrw+$hspace,"Remarks");
		$pdf->Text(25,$nrw+$hspace,":");		

		$nrw = $nrw + 20;

		$pdf->Text(10,$nrw+$hspace,"Received By");
		$pdf->Text(30,$nrw+$hspace,":");		
		$pdf->Text(35,$nrw+$hspace,"____________________");		

		$nrw = $nrw + 5;

		$pdf->Text(10,$nrw+$hspace,"Date Received");
		$pdf->Text(30,$nrw+$hspace,":");		
		$pdf->Text(35,$nrw+$hspace,"____________________");
		
		$printed_by = "Prepared By : ".$user_first_last;
		$pdf->Text(150,$nrw+$hspace,$printed_by); 		
		
	}
?>



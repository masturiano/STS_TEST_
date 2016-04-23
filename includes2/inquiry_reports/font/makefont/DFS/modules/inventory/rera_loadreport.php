<?php
//****************************************************************************
//**	Program Name			:	RALIST
//**	Program Description 	:	RA Listing
//**	Author					:	Louie B. Datuin
//****************************************************************************
	include "../../functions/inquiry_session.php";
	//require('../../functions/mypdf.php');
	//define('FPDF_FONTPATH','../../functions/fonts/');
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	//require('../../functions/fpdf_js.php');	
	//require('../../functions/fpdf_auto_print.php');	

	require('lbd_function.php');
	
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	
	$ra_number=$_GET['ra_number'];
	$query_ra="SELECT * FROM tblRaHeader WHERE (raNumber = $ra_number)";
	$result_ra=mssql_query($query_ra);
	$num_ra = mssql_num_rows($result_ra);
	if ($num_ra >0){
		$po_number=mssql_result($result_ra,0,"poNumber");
	} else {
		$po_number="0";
	}
	$pdf=new FPDF('p','mm','letter');
	$pdf->SetFont('Courier','');
	$pdf->SetFont('', '');

	$strCtr = "SELECT * FROM VIEWRASUMMARY WHERE PONUMBER = '". $po_number ."'";
	$qryCtr = mssql_query($strCtr);
	$nCtr = mssql_num_rows($qryCtr);

	$maxline = 25;
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
	
	$strHead = "SELECT * FROM VIEWRASUMMARY WHERE PONUMBER = '". $po_number ."'";
	$qryHead = mssql_query($strHead);
	$rstHead = mssql_fetch_array($qryHead);

	
	$nrw = $nrw + 5;	
	$Location = "___" . " - " . "_______________";
	$pdf->Text(30,$nrw+$hspace,$Location);	
	
	$nrw = $nrw + 3;
	$vendor_name = str_replace("\\","",$rstHead[3]);
	$pdf->Text(30,$nrw+$hspace,$vendor_name);	

	$nrw = $nrw + 3;
	$pdf->Text(30,$nrw+$hspace,$rstHead[1] . "-" . $rstHead[5]);	
	$pdf->Text(158,$nrw+$hspace,$rstHead[4]);	

	$strDetl = "SELECT * FROM VIEWRASUMMARYDETAIL WHERE PONUMBER = '".$po_number ."'";
	$qryDetl = mssql_query($strDetl);

	$nrw = $nrw + 18;
	$potot = 0;
	while ($rstDetl = mssql_fetch_array($qryDetl))
	{
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');
	
		$nrw = $nrw + 3;
		$prd_number = $rstDetl[0];
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
		$prod_desc = str_replace("\\","",$rstDetl[1]);
		$pdf->Text(40,$nrw+$hspace,$prod_desc);
		$qty_order = number_format($rstDetl[4],2);
		$pdf->Text(110,$nrw+$hspace,$qty_order);
		//$pdf->Text(101,$nrw+$hspace,$rstDetl[6]);
		//$pdf->Text(112,$nrw+$hspace,$rstDetl[5]);
		$conv = number_format($rstDetl[3],0);
		$buy_sell_conv = $rstDetl[6]."/".$rstDetl[5]."/".$conv;
		$pdf->Text(130,$nrw+$hspace,$buy_sell_conv);
		$total  = number_format($rstDetl[2],2);
		$pdf->Text(150,$nrw+$hspace,$total);		
		$pdf->Text(177,$nrw+$hspace,"____");
		$pdf->Text(187,$nrw+$hspace,"____");
		$pdf->Text(197,$nrw+$hspace,"____");
		$potot = $potot + $rstDetl[2];
		
		$nrc++;
		if ($nrc >= $maxline)
		{
			$nrw = $nrw + 10;

			$nrc=0; 
			$nrw=0;
			$pageTotal = 0;
			$npage = $npage + 1;
			$hspace = 17;
			$maxline = 25;
			showHeader($pdf,5,$npage,$npagemax);
			$nrw = $nrw + 3;			
		}
	}	
	
	$nrw = $nrw + 10;

	$pdf->SetFont('Courier','',8);
	$pdf->SetFont('', 'B');

	$pdf->Text(112,$nrw+$hspace,"Grand Total");
	$potot = number_format($potot,2);
	$pdf->Text(150,$nrw+$hspace,$potot);
	$pdf->Text(177,$nrw+$hspace,"____");
	$pdf->Text(187,$nrw+$hspace,"____");
	$pdf->Text(197,$nrw+$hspace,"____");

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
		$pdf->Text(170,$nrw,"RA");
		$pdf->Text(185,$nrw,":");
		$pdf->Text(190,$nrw,$_GET['ra_number']);
		
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw,"REPORT ID");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,"RCR001P");
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
		$pdf->Cell(195,$nrw + 3,"RECEIVER AUTHORIZATION LISTING (RE-PRINT)",0,1,'C');

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
		$pdf->Text(135,$nrw+$hspace,"Carrier");
		$pdf->Text(155,$nrw+$hspace,": _______________");
		
		$nrw++;
		$nrw++;		
		$nrw++;		
		
		$pdf->Text(10,$nrw+$hspace,"Vendor");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(135,$nrw+$hspace,"Container");
		$pdf->Text(155,$nrw+$hspace,": _______________");
			
		$nrw++;		
		$nrw++;
		$nrw++;
	
		$pdf->Text(10,$nrw+$hspace,"Ref. PO");
		$pdf->Text(25,$nrw+$hspace,":");
		$pdf->Text(135,$nrw+$hspace,"Buyer");
		$pdf->Text(155,$nrw+$hspace,":");

		$nrw++;
		$nrw++;
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

		$pdf->Text(110,$nrw+$hspace,"Order");
		$pdf->Text(130,$nrw+$hspace,"Buy/Sell/");
		$pdf->Text(150,$nrw+$hspace,"Qty");
		$pdf->Text(176,$nrw+$hspace,"Qty   Rcvd (Pcs)");
		
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw+$hspace,"SKU");
		$pdf->Text(25,$nrw+$hspace,"UPC");
		$pdf->Text(40,$nrw+$hspace,"Description");
		$pdf->Text(110,$nrw+$hspace,"Qty");
		$pdf->Text(130,$nrw+$hspace,"Conv");
		$pdf->Text(150,$nrw+$hspace,"Expected");
		$pdf->Text(176,$nrw+$hspace,"Good");
		$pdf->Text(186,$nrw+$hspace,"BO");
		$pdf->Text(196,$nrw+$hspace,"Free");

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,str_pad("=",115,'='));
		$nrw = $nrw + $pagitan;	
		
		$nrw = $nrw + 150;
		$pdf->Text(10,$nrw+$hspace,"Remarks");
		$pdf->Text(25,$nrw+$hspace,":");		

		$nrw = $nrw + 20;

		$pdf->Text(10,$nrw+$hspace,"Received By");
		$pdf->Text(40,$nrw+$hspace,":");		
		$pdf->Text(45,$nrw+$hspace,"_______________");		

		$nrw = $nrw + 5;

		$pdf->Text(10,$nrw+$hspace,"Date Received");
		$pdf->Text(40,$nrw+$hspace,":");		
		$pdf->Text(45,$nrw+$hspace,"_______________");
		$nrw = $nrw + 5;
		
		$printed_by = "Prepared By : ".$user_first_last;
		$pdf->Text(10,$nrw+$hspace,$printed_by); 
	}

?>

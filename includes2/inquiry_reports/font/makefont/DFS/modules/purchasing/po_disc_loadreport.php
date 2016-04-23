<?php
	$ponum = $_REQUEST['pono'];
	require('../inventory/lbd_function.php');
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";	
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$db = new DB;
	$db->connect();
	
	$pdf=new FPDF('l','mm','a4');
	$pdf->SetFont('Courier','');
	$pdf->SetFont('', '');

	$strCtr = "SELECT * FROM VIEWPOCORRECTIONSUMMARYDETAIL";
	$qryCtr = mssql_query($strCtr);
	$nCtr = mssql_num_rows($qryCtr);

	$maxline = 10;
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

	$strHead = "SELECT * FROM VIEWPOCORRECTIONSUMMARY WHERE poNumber = $ponum";
	$qryHead = mssql_query($strHead);

	$nrw = $nrw + 15;
	$nrw++;
	$nrw++;
	$nrw++;
	$nrw++;
	$nrw++;
	$nrw++;
	while ($rstHead = mssql_fetch_array($qryHead))
	{
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');
	
		$nrw = $nrw + 3;
		$pdf->Text(10,$nrw+$hspace,"PO ");
		$pdf->Text(23,$nrw+$hspace,":");
		$pdf->Text(28,$nrw+$hspace,$rstHead[0]); 
		
		$pdf->Text(150,$nrw+$hspace,"Currency ");
		$pdf->Text(168,$nrw+$hspace,":");
		$pdf->Text(173,$nrw+$hspace,$rstHead[5]); 
		
		$nrw = $nrw + 3;
		$pdf->Text(10,$nrw+$hspace,"Vendor");
		$pdf->Text(23,$nrw+$hspace,":");
		$supp_name = str_replace("\\","",$rstHead[2]);
		$pdf->Text(28,$nrw+$hspace,$supp_name); 
		
		$nrw = $nrw + 3;
		$pdf->Text(10,$nrw+$hspace,"Address");
		$pdf->Text(23,$nrw+$hspace,":");

		$nrw = $nrw + 3;
		$pdf->Text(10,$nrw+$hspace,"Terms   :");
		########################################
		$Terms = $rstHead[4];
		$strTerms = "SELECT * FROM tblTerms WHERE trmCode = $Terms";
		$qryTerms = mssql_query($strTerms);
		$numTerms = mssql_num_rows($qryTerms);
		if ($numTerms >0){
			$TermsDesc=mssql_result($qryTerms,0,"trmDesc");
		} else {
			$TermsDesc="NA";
		}
		########################################
		$pdf->Text(27,$nrw+$hspace,$Terms. "-" .$TermsDesc); 
		
		$nrw = $nrw + 6;
		$pdf->Text(10,$nrw+$hspace,"Tel No.");
		$pdf->Text(23,$nrw+$hspace,":");
		$nrw = $nrw + 3;
		$pdf->Text(10,$nrw+$hspace,"Original PO Date");
		$pdf->Text(40,$nrw+$hspace,":");
		$pdf->Text(45,$nrw+$hspace,$rstHead[1]); 
		
		$pdf->Text(150,$nrw+$hspace,"Final PO Date ");
		$pdf->Text(168,$nrw+$hspace,":");
		$pdf->Text(176,$nrw+$hspace,$rstHead[10]); 
		
		$poHead = $rstHead[0];
		$strDetl = "SELECT * FROM VIEWPOCORRECTIONSUMMARYDETAIL WHERE PONUMBER = '$poHead'";
		$qryDetl = mssql_query($strDetl);
		
		$nrw = $nrw + 5;
		
		$poSub = 0;
		while ($rstDetl = mssql_fetch_array($qryDetl))
		{

			$pdf->SetFont('Courier','',8);
			$pdf->SetFont('', '');
		
			$nrw = $nrw + 3;
			$prd_number = $rstDetl[6];
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
			$supp_name = str_replace("\\","",$rstHead[2]);
			$pdf->Text(50,$nrw+$hspace,$supp_name);
			$pdf->Text(103,$nrw+$hspace,$rstDetl[8] . " x " . oa_intonly($rstDetl[11],0));		
			$pdf->Text(120,$nrw+$hspace,oa_fmenum($rstDetl[9],12));
			$pdf->Text(145,$nrw+$hspace,oa_fmenum_4($rstDetl[10],12));
			$pdf->Text(170,$nrw+$hspace,$rstDetl[12]);
			$pdf->Text(259,$nrw+$hspace,oa_fmenum_4($rstDetl[13],16));	
			$poSub = $poSub + $rstDetl[13];	

			$nrc++;
			if ($nrc >= $maxline)
			{
				$nrw = $nrw + 10;
				$pdf->SetFont('Courier','',8);
				$pdf->SetFont('', '');
				$pdf->Text(220,$nrw+$hspace,"TOTAL");
			
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
		$nrw = $nrw + 5;

		$strNetTotal = "SELECT SUM(POTOTEXT) AS POTOTEXT, SUM(POTOTDISC) AS POTOTDISC, SUM(POTOTALLOW) AS POTOTALLOW FROM TBLPOHEADERCORR ";
		$strNetTotal .= "WHERE PONUMBER = '$poHead'";
		$qryNetTotal = mssql_query($strNetTotal);
		$rstNetTotal = mssql_fetch_array($qryNetTotal);
		
		$TotDisc = oa_fmenum_4($rstNetTotal['POTOTDISC'],12);
		$TotAllow = oa_fmenum_4($rstNetTotal['POTOTALLOW'],12);
		$TotExtAmt = $rstNetTotal['POTOTEXT'] - $rstNetTotal['POTOTDISC'] - $rstNetTotal['POTOTALLOW'];
		$TotNet = $TotExtAmt;

		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');
		$pdf->Text(220,$nrw+$hspace,"TOTAL");
		$pdf->Text(259,$nrw+$hspace,oa_fmenum_4($poSub,16));

		$nrw = $nrw + 5;
		$pdf->Text(10,$nrw+$hspace,"DISCREPANCIES");
		$pdf->Text(35,$nrw+$hspace,":");

		$nrw = $nrw + 3;	
		$pdf->Text(10,$nrw+$hspace,"SKU DISCS.");
		$pdf->Text(35,$nrw+$hspace,":");
		$pdf->Text(45,$nrw+$hspace,$TotDisc);
	
		$pdf->Text(90,$nrw+$hspace,"VENDOR ALLOWANCES");
		$pdf->Text(125,$nrw+$hspace,":");
		$pdf->Text(130,$nrw+$hspace,$TotAllow);
		
		$pdf->Text(220,$nrw+$hspace,"NET AMOUNT");
		$pdf->Text(259,$nrw+$hspace,oa_fmenum_4($TotNet,16));
		
		
		$nrw = $nrw + 3;		
		$pdf->Text(10,$nrw+$hspace,str_pad("=",163,'='));

		$nrw = $nrw + 5;			
	}
	
	
	$pdf->Output();
	
	function showHeader($pdf,$nrw,$npage,$npagemax)
	{
		include "../../functions/inquiry_session.php";
		############################# dont forget to get the company code ##################################
		####################company name##################################
		$query_company="SELECT * FROM tblCompany WHERE (compCode =  $company_code)";
		$result_company=mssql_query($query_company);
		$num_company = mssql_num_rows($result_company);
		if ($num_company >0){
			$comp_name=mssql_result($result_company,0,"compName");
			$comp_name=strtoupper($comp_name);
		} else {
			$comp_name="NA";
		}
		##################################################################
		##################################################################
		
		$pdf->AddPage();
		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');

		$gmt = time() + (8 * 60 * 60);
		$date = date("m/d/	Y H:i:s", $gmt);
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$pdf->Text(10,$nrw,"RUN DATE");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,$date);
		$user_first_last=strtoupper($user_first_last);
		$printed_by = "PREPARED BY : ".$user_first_last;
		$pdf->Text(230,$nrw,$printed_by);
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw,"REPORT ID");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,"PO005P");
		$pdf->Text(230,$nrw,"PAGE :");
		$pdf->Text(242,$nrw,$npage . " of " . $npagemax);
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		$pdf->Cell(255,$nrw - 15,$comp_name,0,1,'C');
		$pdf->Cell(255,$nrw + 10,"PO DISCREPANCY LISTING",0,1,'C');

		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');

		$nrw = $nrw + 15;

		$pdf->Text(10,$nrw+$hspace,str_pad("=",163,'='));

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(105,$nrw+$hspace,"Buy");
		$pdf->Text(125,$nrw+$hspace,"Qty (Pcs)");
		$pdf->Text(266,$nrw+$hspace,"Extended");
	
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw+$hspace,"SKU");
		$pdf->Text(25,$nrw+$hspace,"UPC");
		$pdf->Text(50,$nrw+$hspace,"Description");
		$pdf->Text(100,$nrw+$hspace,"U/M - Conv");
		$pdf->Text(128,$nrw+$hspace,"Diff.");
		$pdf->Text(145,$nrw+$hspace,"Buy Cost");
		$pdf->Text(200,$nrw+$hspace,"Disc %");
		$pdf->Text(268,$nrw+$hspace,"Amount");

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,str_pad("=",163,'='));

		
	}
?>



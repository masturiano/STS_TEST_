<?php
	include "../../functions/inquiry_session.php";
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	//require('../inventory/mypdf.php');
	//define('FPDF_FONTPATH','../inventory/fonts/');
	
	//require('../inventory/fpdf_js.php');	
	//require('../inventory/fpdf_auto_print.php');	
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$db = new DB;
	$db->connect();
	
	
	$pdf=new FPDF('p','mm','letter');
	$pdf->SetFont('Courier','');
	$pdf->SetFont('', '');

	$strCtr = "SELECT * FROM VIEWCURRENCY";
	$qryCtr = mssql_query($strCtr);
	$nCtr = mssql_num_rows($qryCtr);

	$maxline = 20;
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

	$strSQL = "SELECT * FROM VIEWCURRENCY";
	$qrySQL = mssql_query($strSQL);

	$nrw = $nrw + 10;
	while ($rstSQL = mssql_fetch_array($qrySQL))
	{
		$CCode = $rstSQL[0];
		$CName = $rstSQL[1];
		$CRate = oa_fmenum_4($rstSQL[2],16);
		$CDate = $rstSQL[3];

		$pdf->SetFont('Courier','',8);
		$pdf->SetFont('', '');	
		$nrw = $nrw + 3;					
		$pdf->Text(30,$nrw+$hspace,$CCode);
		$pdf->Text(60,$nrw+$hspace,$CName);
		$pdf->Text(120,$nrw+$hspace,$CRate);	
		$pdf->Text(170,$nrw+$hspace,$CDate);	
		
		$nrc++;
		if ($nrc >= $maxline)
		{
			$nrw = $nrw + 10;

			$nrc=0; 
			$nrw=0;
			$pageTotal = 0;
			$npage = $npage + 1;
			$hspace = 25;
			$maxline = 20;
			showHeader($pdf,5,$npage,$npagemax);
			$nrw = $nrw + 3;			
		}
		
	}
	##################################################### footer ######
	$pdf->ln();
	$pdf->ln();
	
	$pdf->Cell(200,5, '* * * END OF REPORT * * *', 0,1,'C');
	$pdf->ln();
	$printed_by = "Prepared By : ".$user_first_last;
	$pdf->Cell(1,$dtl_ht, $printed_by, 0, 1);
	##################################################### footer ######
	
	$pdf->Output();
	
	function showHeader($pdf,$nrw,$npage,$npagemax)
	{
		include "../../functions/inquiry_session.php";
		$pdf->AddPage();
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');

		$gmt = time() + (8 * 60 * 60);
		$newdate = date("m/d/Y h:iA", $gmt);
		$date=$newdate;
		
		$pdf->Text(10,$nrw,"RUN DATE");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,$date);
		$pdf->Text(170,$nrw,"PAGE");
		$pdf->Text(185,$nrw,":");
		$pdf->Text(190,$nrw,$npage . " of " . $npagemax);
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(10,$nrw,"REPORT ID");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,"CURRMAINT");
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		################################## set up company ############
		$query_company="SELECT * FROM tblCompany WHERE (compCode =  $company_code)";
		$result_company=mssql_query($query_company);
		$num_company = mssql_num_rows($result_company);
		if ($num_company >0){
			$comp_name=mssql_result($result_company,$i,"compName");
		} else {
			$comp_name="NA";
		}
		##############################################################
		$pdf->Cell(190,$nrw - 20,$comp_name,0,1,'C');
		$pdf->Cell(190,$nrw + 10,"CURRENCY LISTING",0,1,'C');

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
		

		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		
		$pdf->Text(10,$nrw+$hspace,str_pad("=",92,'='));

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(30,$nrw+$hspace,"Currency Code");
		$pdf->Text(60,$nrw+$hspace,"Description");
		$pdf->Text(120,$nrw+$hspace,"US Dollar Rate");
		$pdf->Text(170,$nrw+$hspace,"Rate Date");

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,str_pad("=",92,'='));
		$nrw = $nrw + $pagitan;	
		
		
	}
?>



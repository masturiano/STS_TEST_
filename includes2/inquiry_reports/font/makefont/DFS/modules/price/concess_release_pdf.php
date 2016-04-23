<?php
//****************************************************************************
//**	Program Name			:	PRODLIST
//**	Program Description 	:	Product Master Listing
//**	Author					:	Louie B. Datuin
//****************************************************************************

	$E_No = $_GET['EventNo'];
	require("../inventory/lbd_function.php");
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	
	$db = new DB;
	$db->connect();

	$pdf=new FPDF('L','mm','LETTER');
	$pdf->SetFont('Courier','',10);
	$pdf->SetFont('', '');

	$strCtr = "SELECT * FROM tblConsessHdr WHERE prEventNumber = '$E_No'";
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
	
	$nrw = $nrw - 12;
	$nrw2 = $nrw + 6;
	$pdf->Text(247,$nrw2+$hspace,$E_No);

	$strSQL = "SELECT * FROM tblConsessHdr WHERE prEventNumber = '$E_No'";
	$qrySQL = mssql_query($strSQL);

	$nrw = $nrw + 11;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
	while ($rstSQL = mssql_fetch_array($qrySQL))
	{
		$Desc = $rstSQL[8];
		$Start = $rstSQL[5];
		$event_date = $rstSQL[2];
		if ($event_date=="") {
			$event_date = "";
		} else {
			$date = new DateTime($event_date);
			$event_date = $date->format("m/d/Y");
		}
		$event_date = "Doc.Date : " . $event_date;
		$nrw2 = $nrw - 3;
		$pdf->Text(127,$nrw2+$hspace,$event_date);
		
		if ($Start=="") {
			$Start = "";
		} else {
			$date = new DateTime($Start);
			$Start = $date->format("m/d/Y");
		}
		$End = $rstSQL[6];
		if ($End=="") {
			$End = "";
		} else {
			$date = new DateTime($End);
			$End = $date->format("m/d/Y");
		}
		
		
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');	
						
		$pdf->Text(55,$nrw+$hspace,$Desc);
		$nrw = $nrw + 5;
		$pdf->Text(37,$nrw+$hspace,$Start);
		$pdf->Text(88,$nrw+$hspace,$End);
		$nrw = $nrw + 5;
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
			$nrw = $nrw + 1;			
		}
		
	}
	
	$nrw = $nrw + 13;	
	$strSQL = "SELECT tblConsessHdr.prEventNumber, tblConsessDtl.prdNumber, tblProdMast.prdDesc, tblProdMast.prdSuppItem, 
                      tblConsessDtl.umCode, tblProdMast.prdConv, tblConsessDtl.prOldPrice, tblConsessDtl.prNewPrice, 
                      tblAveCost.aveUnitCost
					  FROM tblConsessHdr LEFT JOIN
                      tblConsessDtl ON tblConsessHdr.prEventNumber = tblConsessDtl.prEventNumber LEFT JOIN
                      tblProdMast ON tblConsessDtl.prdNumber = tblProdMast.prdNumber LEFT JOIN
                      tblAveCost ON tblConsessDtl.prdNumber = tblAveCost.prdNumber 
					  WHERE tblConsessHdr.prEventNumber = $E_No";
	$qrySQL = mssql_query($strSQL);
	$num_trans_no = mssql_num_rows($qrySQL);
	while ($rstSQL = mssql_fetch_array($qrySQL))
	{
		$SKU = $rstSQL[1];
		$UPC = $rstSQL[3];
		$ProdDesc = $rstSQL[2];
		$ProdDesc = str_replace("\\","",$ProdDesc);
		$rstSQL[5]=number_format( $rstSQL[5],0);
		$Um = $rstSQL[4];
		$OldPrice = $rstSQL[6];
		if ($OldPrice>0) {
			$OldPrice=number_format( $OldPrice,2);
		} else {
			$OldPrice="";
		}
		$NewPrice = $rstSQL[7];
		if ($NewPrice>0) {
			$NewPrice=number_format( $NewPrice,2);
		} else {
			$NewPrice="";
		}
		$AveCost = $rstSQL[8];
		if ($AveCost>0) {
			$AveCost=number_format( $AveCost,4);
		} else {
			$AveCost="";
		}
		
			
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');	
		$nrw = $nrw + 3;					
		$pdf->Text(10,$nrw+$hspace,$SKU);
		$pdf->Text(25,$nrw+$hspace,$UPC);
		$pdf->Text(60,$nrw+$hspace,$ProdDesc);
		$pdf->Text(150,$nrw+$hspace,$Um);
		$pdf->Text(162,$nrw+$hspace,$OldPrice);		
		$pdf->Text(195,$nrw+$hspace,$NewPrice);		
		$pdf->Text(230,$nrw+$hspace,$AveCost);		
		
		$Rec++;		
	}

	$pdf->SetFont('Courier','',10);
	$pdf->SetFont('', '');
	$nrw = $nrw + 10;		
	$pdf->Text(120,$nrw+$hspace,"***** END OF ENTRIES *****",0,1,'C');
	$nrw = $nrw + 5;		
	$pdf->Text(10,$nrw+$hspace,"Total No. of Items : " . $Rec);
	$nrw = $nrw + 7;		
	$printed_by = "Printed By : ".$user_first_last;
	$pdf->Text(10,$nrw+$hspace, $printed_by, 0, 0,'C');
	
	$pdf->Output();
	
	function showHeader($pdf,$nrw,$npage,$npagemax)
	{
		include "../../functions/inquiry_session.php";
		############################# dont forget to get the company code ##################################
		####################company name##################################
		$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
		$result_company=mssql_query($query_company);
		$num_company = mssql_num_rows($result_company);
		if ($num_company >0){
			$comp_name=mssql_result($result_company,0,"compName");
			//$comp_name=strtoupper($comp_name);
		} else {
			$comp_name="NA";
		}
		##################################################################
		##################################################################
		$pdf->AddPage();
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		$pdf->Text(10,$nrw,"RUN DATE");
		$pdf->Text(30,$nrw,":");

		$gmt = time() + (8 * 60 * 60);
		$date = date("m/d/Y h:iA", $gmt);
		$pdf->Text(36,$nrw,$date);
				
		$pdf->Text(225,$nrw,"EVENT NO :");
		$nrw++;
		$nrw++;
		$nrw++;
		$nrw++;		
		$pdf->Text(225,$nrw,"PAGE :");
		$pdf->Text(240,$nrw,$npage . " of " . $npagemax);
		
		$pdf->Text(10,$nrw,"REPORT ID");
		$pdf->Text(30,$nrw,":");
		$pdf->Text(36,$nrw,"EVN006P");
		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		$nrw2=$nrw+5;
		$pdf->Cell(280,$nrw2 - 20,$comp_name,0,1,'C');
		$nrw2=$nrw-17;
		$pdf->Cell(280,$nrw2 + 10,"PRICE EVENT LIST (CONCESSIONAIRE)",0,1,'C');

		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;

		$pdf->SetFont('Courier','',10);
		$pdf->SetFont('', '');
		
		$pdf->Text(10,$nrw+$hspace,"Event Description");
		$pdf->Text(50,$nrw+$hspace,":");
		$nrw++;		
		$nrw++;		
		$nrw++;
		$nrw++;
		$nrw++;
		$pdf->Text(10,$nrw+$hspace,"Start Date :");
		$pdf->Text(65,$nrw+$hspace,"End Date :");
		$nrw++;		
		$nrw++;
		$nrw++;		
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,str_pad("=",122,'='));

		$nrw++;
		$nrw++;
		$nrw++;		

		$pdf->Text(10,$nrw+$hspace,"SKU");
		$pdf->Text(25,$nrw+$hspace,"UPC");
		$pdf->Text(60,$nrw+$hspace,"Description");
		$pdf->Text(150,$nrw+$hspace,"Sell");
		//$pdf->Text(160,$nrw+$hspace,"Conv");
		$pdf->Text(162,$nrw+$hspace,"Old Price");
		$pdf->Text(195,$nrw+$hspace,"New Price");
		$pdf->Text(230,$nrw+$hspace,"Average Cost");

		$nrw++;	
		$nrw++;
		$nrw++;		

		$pdf->Text(150,$nrw+$hspace,"UM");

		$nrw++;	
		$nrw++;	

		$pdf->Text(10,$nrw+$hspace,str_pad("=",122,'='));
		$nrw = $nrw + $pagitan;	
		
		
	}
?>



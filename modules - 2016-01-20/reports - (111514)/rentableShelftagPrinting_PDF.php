<?php
	session_start();
	include("../../includes/db.inc.php");
	include("../../includes/common.php");
//	include("../../includes/pdf/fpdf.php");
	include("rentableShelftagPrintingObj.php");
//	define('FPDF_FONTPATH','../../includes/pdf/font/');
	
	require_once('../../includes2/fpdf.php');
	define(FPDF_FONTPATH,'../../includes2/font/');
	require('../../includes2/fpdf_js.php');
	require('../../includes2/fpdf_auto_print.php');
	require_once('../../includes2/FPDI_Protection.php');
	
$pdf = new PDF_AutoPrint();
$pdf->FPDF('P','mm','LETTER');

$pdf->SetMargins(0,1,0);
$pdf->AddFont('erasbd','','../../includes2/font/erasbd.php');


$reportsObj = new rentableShelftagPrintingObj();

$arr = $reportsObj->specsDetail($_GET['cmbdisplaySpecs'],$_GET['cmbStore']);
//$pdf->Main($arr);

			foreach($arr as $val){
			$pdf->AddPage();
			$pdf->SetFont('erasbd','',10);
			if(strlen($val['supplier']) >= '30'){
				$pdf->SetFont('erasbd','',7);
				$pdf->cell(50,5,$val['supplier'],0,1,'L');
			}else{
				$pdf->SetFont('erasbd','',10);
				$pdf->cell(50,5,$val['supplier'],0,1,'L');
			}
			$pdf->SetFont('erasbd','',10);
			$pdf->cell(50,5,"STS REFNO: ".$val['stsRefno'],0,1,'L');
			$pdf->cell(22,5,date('m/d/Y',strtotime($val['startDate'])),0,0,'L');
			$pdf->cell(6,5,'TO',0,0,'L');
			$pdf->cell(50,5,date('m/d/Y',strtotime($val['endDate'])),0,1,'L');
			$pdf->cell(50,5,$val['dispDesc'],0,1,'L');
		}


//$pdf->Main($arr);

/*class PDF extends FPDF{
	function Main($arr){
		
		
		foreach($arr as $val){
		$pdf->AddPage();
		
		$pdf->SetFont('erasbd','',22);
		
		$pdf->cell(50,25,"test",0,1,'C',0);
		$pdf->cell(50,25,"test",0,1,'C',0);
		$pdf->cell(50,25,"test",0,1,'C',0);
		$pdf->cell(50,25,"test",0,1,'C',0);
		$pdf->cell(50,25,"test",0,1,'C',0);
		$pdf->cell(50,25,"test",0,1,'C',0);
		
		}
	}
}*/

/*$arrName = array('wil',
'jun',
'LUISA',
'NANCY',
'MARTIN',
'ANTON',
'VINCENT',
'MARIE',
'PIA',
'CHRIS',
'RAYMUND',
'WILLIAM',
'HEZEL',
'KATRINA',
'OSCAR',
'BOY',
'GRETCHEN',
'MARYAN',
'LORA',
'VAN',
'ABBY',

);

$arrSupName = array('programmer'
);

$arrPos = array('chong');
$arrCount = count($arrName);
for($a=0;$a<=$arrCount;$a++){
	$pdf->AddPage();
	
	$pdf->SetFont('erasbd','',22);
	
	$pdf->cell(50,25,"".strtoupper($arrName[$a]),0,1,'C',0);
	

}*/

/*class PDF extends FPDF
{
	function Main($arr){
		$this->AddPage();
		$pdf->SetFont('erasbd','',22);
		
		$ctr = 0 ;
		foreach($arr as $val){
			$this->SetFont('Arial', '', '9');	
			$pdf->Cell(50,25,'test',0,0,'R');
			$pdf->cell(50,25,"".strtoupper($arrName[$a]),0,1,'C',0);
			
			$this->Cell(5,5,"",0,0,'L');
			$this->Cell(11,5,$val['stsNo']." - ".$val['stsSeq'],0,0,'R');
			$this->Cell(13,5,"",0,0,'L');
			$this->Cell(22,5,date('m/d/Y',strtotime($val['stsApplyDate'])),0,0,'L');	
		}
	}
	
	function Header() {
		//"Run Date: ".date("m/d/Y h:iA")
		$this->Cell(55,5,"Released STS (Apply Date)",0,0,'L');
		$this->Cell(90,5,'',0,0,'C');
		$this->Cell(57,5,'Page '.$this->PageNo().'/{nb}',0,1,'R');
		
		$this->Cell(63,5,"As of: ".date('m/d/Y',strtotime($_GET['txtDate'])),0,0,'L');
		$this->Cell(75,5,"",0,0,'C');
		$this->Cell(60,5,'Status: '.$this->status,0,1,'R');	
		
		$this->Cell(70,5,"Store: ".$this->branch,0,0,'L');
		$this->Cell(63,5,"",0,0,'C');
		$this->Cell(64,5,"Run Date: ".date("m/d/Y h:iA"),0,1,'R');
		
		$this->Cell(17,8,'Ref No.','BT',0,'L');
		$this->Cell(19,8,'STS No.','BT',0,'L');
		$this->Cell(35,8,'Application Date','BT',0,'L');
		$this->Cell(35,8,'Supplier','BT',0,'L');
		$this->Cell(35,8,'Payment Mode','BT',0,'L');
		$this->Cell(37,8,'Department','BT',0,'L');
		$this->Cell(15,8,'Amount','BT',1,'L');
	}

}*/

//$pdf->AutoPrint(false);
$pdf->Output();


?>

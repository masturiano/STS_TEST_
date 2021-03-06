<?php
	session_start();
	include("../../includes/db.inc.php");
	include("../../includes/common.php");
	include("../../includes/pdf/fpdf.php");
	include("reportsObj.php");
	define('FPDF_FONTPATH','../../includes/pdf/font/');
	
class PDF extends FPDF
{
	var $arrEvent;
	var $arrBranch;
	var $arrStrFunds;
	function Main($arrSupp)
	{
		$this->SetFont('Arial', '', '9');
		$this->AddPage();
		$ctr = 0 ;
		$reportsObj = new reportsObj();
			$this->Ln(3);
			foreach ($arrSupp as $valSup) {
				$this->SetFont('Arial', 'B', '9');
				$this->Cell(10,5,' '.$valSup['suppName'],0,1,'L');
				$this->SetFont('Arial', '', '9');
				$arr = $reportsObj->transSummary($this->tran,$this->dtFrom,$this->dtTo,$this->status,$valSup['suppCode']);
				foreach ($arr as $val) {
					$ctr++;
					$this->Cell(10,5,' '.$val['stsRefno'],0,0,'L');
					$this->Cell(40,5,'',0,0,'L');
					$this->Cell(20,5,number_format($val['stsAmt'],2),0,0,'R');
					$this->Cell(10,5,'',0,0,'L');
					$this->Cell(25,5,date('m/d/Y',strtotime($val['dateEntered'])),0,0,'L');
					$this->Cell(25,5,($val['applyDate'] !="") ? date('m/d/Y',strtotime($val['applyDate']))."-".$val['nbrApplication']:"",0,0,'L');
					$this->Cell(10,5,'',0,0,'L');
					$this->Cell(40,5,$val['payMode'],0,0,'L');
					$this->Cell(24,5,$val['contractNo'],0,0,'L');
					$this->Cell(25,5,date('m/d/Y',strtotime($val['applyDate'])),0,0,'L');
					$this->Cell(50,5,$val['fullName'],0,0,'L');
					$this->SetFont('Arial', '', '6');
					$this->Cell(35,5,$val['dept']."-".$val['cls']."-".$val['subCls'],0,1,'C');
					$this->SetFont('Arial', 'B', '9');
					$this->Cell(10,5,"",0,0,'L');
					$this->Cell(20,5,"REMARKS: ",0,0,'L');
					$this->SetFont('Arial', '', '9');
					$this->Cell(58,5,$val['stsRemarks'],0,1,'L');
					$totAmt += $val['stsAmt'];
					$grandAmt += $val['stsAmt'];
				}
				$this->SetFont('Arial', 'B', '9');
				$this->Cell(30,5,"Sub Total",0,0,'R');
				$this->Cell(40,5,number_format($totAmt,2),0,1,'R');
				$totAmt = 0;
			}
				
				$this->SetFont('Arial', 'B', '9');
				$this->Cell(30,5,"Grand Total",0,0,'R');
				$this->Cell(40,5,number_format($grandAmt,2),0,1,'R');
				$this->SetFont('Arial', '', '9');
				$this->Cell(25,5,'Number of Vendors: '.$ctr,0,1,'L');
			
					
			$this->SetFont('Arial', '', '7');
			$this->Cell(258,8,'* * * * * End of Report. Nothing Follows * * * * ',0,1,'C');
			$this->SetFont('Arial', '', '9');
	}
	
	function Header() {
		if ($_GET['cmbStatus']=='R') {
			$status = " Approved";
		} elseif ($_GET['cmbStatus']=='O') {
			$status = " Unapproved";
		} else {
			$status = "Approved and Unapproved";
		}
		$this->Cell(45,5,'Run Date: '.$this->currentDate(),0,0,'L');
		if($_GET['cmbTran'] == '1'){
			$type = 'Regular STS';
		}elseif($_GET['cmbTran'] == '2'){
			$type = 'Listing Fee';	
		}elseif($_GET['cmbTran'] == '4'){
			$type = 'Shelf Enhancer';	
		}elseif($_GET['cmbTran'] == '4'){
			$type = 'Display Allowance';	
		}else{
			$type = "All STS Type";
		}
			
		$this->Cell(178,5,'STS Transactions Summarized Report',0,0,'C');
		$this->Cell(35,5,'Status: '.$status,0,1,'R');
		$this->Cell(45,5,'ReportID: STS002',0,0,'L');
		$this->Cell(178,5,$type.': '.$_GET['txtDateFrom'].' - '.$_GET['txtDateTo'],0,0,'C');
		$this->Cell(35,5,'Page '.$this->PageNo().'/{nb}',0,1,'R');
		$this->Ln(5);
		$this->Cell(20,8,'Ref. No.','BT',0,'L');
		$this->Cell(35,8,'Vendor','BT',0,'L');
		$this->Cell(23,8,'Amount','BT',0,'L');
		$this->Cell(25,8,'Date Ent.','BT',0,'L');
		$this->Cell(37,8,'App. Start Date-No.','BT',0,'L');
		$this->Cell(33,8,'Mode of Payment','BT',0,'L');
		$this->Cell(28,8,'Contract No.','BT',0,'L');
		$this->Cell(30,8,'Approved Date','BT',0,'L');
		$this->Cell(50,8,'Approved By','BT',0,'L');
		$this->Cell(50,8,'Hierarchy','BT',1,'L');
		
	}
	
	function Footer() {
		$prntdBy = "Printed By : ".$_SESSION['sts-fullName'];
		$this->SetY(-15);
		$this->Cell(45,5,$prntdBy,0,0,'L');
	}
	
}

$pdf=new PDF();
$pdf->FPDF($orientation='L',$unit='mm',$format='LEGAL');
$pdf->AliasNbPages(); 
$reportsObj = new reportsObj();
$pdf->tran = $_GET['cmbTran'];
$pdf->dtFrom = $_GET['txtDateFrom'];
$pdf->dtTo = $_GET['txtDateTo'];
$pdf->status = $_GET['cmbStatus'];
$arrTran = $reportsObj->transSummarySupp($_GET['cmbTran'],$_GET['txtDateFrom'],$_GET['txtDateTo'],$_GET['cmbStatus'],$_GET['cmbSupp']);
$pdf->Main($arrTran);
$pdf->Output('transaction_summary.pdf','D');

?>

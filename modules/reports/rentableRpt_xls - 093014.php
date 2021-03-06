<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	include("../../includes/db.inc.php");
	include("../../includes/common.php");
	include("rentableRptObj.php");
	require_once 'Spreadsheet/Excel/Writer.php';
	
	$rentableRptObj = new rentableRptObj();
	$workbook = new Spreadsheet_Excel_Writer();
	$headerFormat = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
	$headerFormat2 = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'left'));
	$headerFormat->setFontFamily('Calibri'); 
	$headerBorder    = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
									  
	$headerBorder->setFontFamily('Calibri'); 
	$workbook->setCustomColor(13,155,205,255);
	$TotalBorder    = $workbook->addFormat(array('Align' => 'right','bold'=> 1,'border'=>1,'fgColor' => 'white'));
	$TotalBorder->setFontFamily('Calibri'); 
	$TotalBorder->setTop(5); 
	$detailrBorder   = $workbook->addFormat(array('border' =>1,'Align' => 'right'));
	$detailrBorder->setFontFamily('Calibri'); 
	$detailrBorderAlignRight2   = $workbook->addFormat(array('Align' => 'left'));
	$detailrBorderAlignRight2->setFontFamily('Calibri');
	$workbook->setCustomColor(12,183,219,255);
	$detail   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'left'));
	$detail->setFontFamily('Calibri'); 
	
	//first row format
	$Deptc   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'center'));
	$Deptc->setFontFamily('Calibri'); 
	
	$Deptc1   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'center'));
	$Deptc1->setFgColor(12); 
	$Deptc1->setFontFamily('Calibri');
	//end first row format
	
	//2nd row format
	$Deptt   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'center'));
	$Deptt->setFontFamily('Calibri'); 
	
	$Deptt2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'center'));
	$Deptt2->setFgColor(11); 
	$Deptt2->setFontFamily('Calibri');
	//end 2nd row format

	$detail2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'left'));
	$detail2->setFgColor(12); 
	$detail2->setFontFamily('Calibri'); 
	$Dept   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'right'));
	$Dept->setFontFamily('Calibri'); 
	$Dept2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'right'));
	$Dept2->setFgColor(12); 
	$Dept2->setFontFamily('Calibri');
	

	
	$filename = "rentable.xls";
	$workbook->send($filename);
	$worksheet = &$workbook->addWorksheet("Cancelled STS");
	$worksheet->setLandscape();
	$worksheet->freezePanes(array(3,0));
	
	$worksheet->write(0,0,"Rentable".$strFilt ,$headerFormat);
	for($i=1;$i<11;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,0,12);
	$worksheet->setColumn(0,1,29);
	$worksheet->setColumn(0,2,21);
	$worksheet->setColumn(0,3,17);
	$worksheet->setColumn(0,4,12);
	$worksheet->setColumn(0,5,12);
	$worksheet->setColumn(0,6,15);
	$worksheet->setColumn(0,7,15);
	$worksheet->setColumn(0,8,24);
	$worksheet->setColumn(0,9,15);
	$worksheet->setColumn(0,10,12);
	
	$worksheet->write(1,0, "".$pMode,$headerFormat);
	
	$worksheet->write(2,0,"STORE CODE",$headerFormat);
	$worksheet->write(2,1,"STORE NAME",$headerFormat);
	$worksheet->write(2,2,"DISPLAY SPECTS DESC.",$headerFormat);
	$worksheet->write(2,3,"DISPLAY DESC.",$headerFormat);
	$worksheet->write(2,4,"START DATE",$headerFormat);
	$worksheet->write(2,5,"END DATE",$headerFormat);
	$worksheet->write(2,6,"STS REFNO.",$headerFormat);
	$worksheet->write(2,7,"AVAILABILITY",$headerFormat);
	$worksheet->write(2,8,"CREATED BY",$headerFormat);
	$worksheet->write(2,9,"DATE CREATED",$headerFormat);
	$worksheet->write(2,10,"STATUS",$headerFormat);
	
		$ctr = 2;
		
		$row1 = ($col==0) ? $Deptc1:$Deptc;
		$col = ($col==0) ? 1:0;
		
		$arrRentable = $rentableRptObj->specsDetail($_GET['cmbStore'],$_GET['cmbdisplaySpecs'],$_GET['cmbAvailTag']);
		foreach ($arrRentable as $valD) {
			
			//$totExpAmt = $flag = 0;
			//$row = ($col==0) ? $detail2:$detail;
			//$row2 = ($col==0) ? $Dept2:$Dept;
			//$col = ($col==0) ? 1:0;
			
			$ctr++;	
			
			$worksheet->write($ctr,0,$valD['strCode'],$row1);
			$worksheet->write($ctr,1,$valD['brnDesc'],$row1);
			$worksheet->write($ctr,2,$valD['displaySpecsDesc'],$row1);
			$worksheet->write($ctr,3,$valD['dispDesc'],$row1);
			$worksheet->write($ctr,4,date('m/d/Y',strtotime($valD['startDate'])),$row1);
			$worksheet->write($ctr,5,date('m/d/Y',strtotime($valD['endDate'])),$row1);
			$worksheet->write($ctr,6,$valD['stsRefNo'],$row1);
			$worksheet->write($ctr,7,$valD['availTag'],$row1);
			$worksheet->write($ctr,8,$valD['fullName'],$row1);
			$worksheet->write($ctr,9,date('m/d/Y',strtotime($valD['dateCreated'])),$row1);
			$worksheet->write($ctr,10,$valD['status'],$row1);
			}

		$ctr++;	
		


			
$workbook->close();
?>

<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\php\PEAR');
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
	
	$worksheet->setMerge(0, 0, 0, 5);
	$worksheet->write(0,0,"Rentable".$strFilt ,$headerFormat);
	for($i=1;$i<6;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,0,12);
	$worksheet->setColumn(0,1,30);
	$worksheet->setColumn(0,2,20);
	$worksheet->setColumn(0,3,20);
	$worksheet->setColumn(0,4,20);
	$worksheet->setColumn(0,5,20);
	$worksheet->setColumn(0,6,20);
	$worksheet->setColumn(0,7,20);
	$worksheet->setColumn(0,8,20);
	$worksheet->setColumn(0,9,20);
	$worksheet->setColumn(0,10,20);
	$worksheet->setColumn(0,11,20);
	$worksheet->setColumn(0,12,20);
	$worksheet->setColumn(0,13,20);
	
	$worksheet->write(1,0, "".$pMode,$headerFormat);
	
	$worksheet->write(2,0,'As of:',$headerFormat);
	$worksheet->write(2,1,date('Y-F-d'),$headerFormat);
	
	$worksheet->write(3,0,'Store:',$headerFormat);
	$worksheet->write(3,1,$_GET['cmbStore'],$headerFormat);
	
	$worksheet->write(5,0,"STORE CODE",$headerFormat);
	$worksheet->write(5,1,"STORE NAME",$headerFormat);
	$worksheet->write(5,2,"DISPLAY SPECTS DESC.",$headerFormat);
	$worksheet->write(5,3,"DISPLAY DESC.",$headerFormat);
	$worksheet->write(5,4,"SIZE SPECS.",$headerFormat);
	//$worksheet->write(5,4,"START DATE",$headerFormat);
	//$worksheet->write(5,5,"END DATE",$headerFormat);
	//$worksheet->write(5,6,"STS REFNO.",$headerFormat);
	//$worksheet->write(5,5,"AVAILABILITY",$headerFormat);
	//$worksheet->write(5,8,"CREATED BY",$headerFormat);
	//$worksheet->write(5,9,"DATE CREATED",$headerFormat);
	//$worksheet->write(5,6,"STATUS",$headerFormat);
	//$worksheet->write(5,11,"IMPLEMENT START",$headerFormat);
	//$worksheet->write(5,12,"IMPLEMENT END",$headerFormat);
	//$worksheet->write(5,5,"PERMANENT",$headerFormat);
	$worksheet->write(5,5,"MDSG CATEG.",$headerFormat);
		$ctr = 5;
		
		$row1 = ($col==0) ? $Deptc1:$Deptc;
		$col = ($col==0) ? 1:0;
		
		$arrRentable = $rentableRptObj->specsDetailAll($_GET['cmbStore']);
		
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
			$worksheet->write($ctr,4,$valD['sizeSpecsDesc'],$row1);
			//if($valD['startDate'] == '' || $valD['startDate'] == null){
			//	$worksheet->write($ctr,4,'',$row1);
			//}else{
			//	$worksheet->write($ctr,4,date('m/d/Y',strtotime($valD['startDate'])),$row1);
			//}
			//if($valD['endDate'] == '' || $valD['endDate'] == null){
			//	$worksheet->write($ctr,5,'',$row1);
			//}else{
			//	$worksheet->write($ctr,5,date('m/d/Y',strtotime($valD['endDate'])),$row1);
			//}
			//if($_GET['cmbAvailTag'] == 'Y' && $valD['availabilityTag'] == 'N'){
			//	$worksheet->write($ctr,4,'Available',$row1);
			//}else{
			//	$worksheet->write($ctr,5,$valD['availTag'],$row1);
			//}
			//$worksheet->write($ctr,8,$valD['fullName'],$row1);
			//$worksheet->write($ctr,9,date('m/d/Y',strtotime($valD['dateCreated'])),$row1);
			//$worksheet->write($ctr,6,$valD['status'],$row1);
			//if($valD['impStartDate'] == '' || $valD['impStartDate'] == null){
			//	$worksheet->write($ctr,11,'',$row1);
			//}else{
			//	$worksheet->write($ctr,11,date('m/d/Y',strtotime($valD['impStartDate'])),$row1);
			//}
			//if($valD['impEndDate'] == '' || $valD['impEndDate'] == null){
			//	$worksheet->write($ctr,12,'',$row1);
			//}else{
			//	$worksheet->write($ctr,12,date('m/d/Y',strtotime($valD['impEndDate'])),$row1);
			//}
			//$worksheet->write($ctr,5,$valD['permanentTag'],$row1);
			$worksheet->write($ctr,5,$valD['grpDesc'],$row1);
			
		}

		$ctr++;	
		


			
$workbook->close();
?>

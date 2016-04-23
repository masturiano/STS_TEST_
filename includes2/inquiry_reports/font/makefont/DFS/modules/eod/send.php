<?php
include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function2.php";
$db = new DB;
$db->connect();

/*This script sends to the client
the content of a server file filename*/
	$from_date=$_POST['hide_from_date2'];
	$to_date=$_POST['hide_to_date2'];
	list($month1, $day1, $year1) = split ('[/.-]', $from_date);
	list($month2, $day2, $year2) = split ('[/.-]', $to_date);
	$file_name=$month1.$day1.$year1."-".$month2.$day2.$year2.".txt";
	
	if (file_exists($file_name)) {
		unlink($file_name);
	} 
	$handle2 = fopen ($file_name, "x");
			
	if ($from_date=="") {
		$query_date = "";
	} else {
		$query_date = " AND (dbo.tblProdPrice.dateUpdated >= '$from_date') AND (dbo.tblProdPrice.dateUpdated <= '$to_date')";
	}
	############################# dont forget to get the company code ##################################
	$query_ci_header="SELECT dbo.tblProdPrice.prdNumber, dbo.tblUpc.upcCode, dbo.tblProdPrice.regUnitPrice, dbo.tblUpc.upcDesc, dbo.tblProdPrice.dateUpdated, 
		dbo.tblProdMast.prdDeptCode, dbo.tblProdMast.prdGrpCode, dbo.tblProdPrice.compCode
		FROM dbo.tblProdPrice INNER JOIN
		dbo.tblUpc ON dbo.tblProdPrice.prdNumber = dbo.tblUpc.prdNumber INNER JOIN
		dbo.tblProdMast ON dbo.tblProdPrice.prdNumber = dbo.tblProdMast.prdNumber
		WHERE (dbo.tblProdPrice.compCode = $company_code) $query_date
		ORDER BY dbo.tblProdPrice.prdNumber";
	$result_ci_header=mssql_query($query_ci_header);
	$num_ci_header = mssql_num_rows($result_ci_header);
	$contents="";
	for ($i=0;$i<$num_ci_header;$i++){ 
		$grid_sku=mssql_result($result_ci_header,$i,"prdNumber");
		$grid_upc=mssql_result($result_ci_header,$i,"upcCode");
		$grid_group=mssql_result($result_ci_header,$i,"prdGrpCode");
		$grid_dept=mssql_result($result_ci_header,$i,"prdDeptCode");
		$grid_price=mssql_result($result_ci_header,$i,"regUnitPrice");
		$grid_desc=mssql_result($result_ci_header,$i,"upcDesc");
		$grid_desc=strtoupper($grid_desc);
		####################### format price ############
		$grid_price = number_format($grid_price,2,'','');
		$addzero="";
		$strlen=0;
		$shortlen=0;
		$strlen = strlen($grid_price);
		if ($strlen<10) {
			$shortlen = 10 - $strlen;
			for ($z=1 ; $z<=$shortlen ; $z++) {
				$addzero=$addzero."0";
			}
		}
		$grid_price=$addzero.$grid_price;
		
		####################### dept code ############
		$addzero="";
		$strlen=0;
		$shortlen=0;
		$strlen = strlen($grid_dept);
		if ($strlen<2) {
			$shortlen = 2 - $strlen;
			for ($z=1 ; $z<=$shortlen ; $z++) {
				$addzero=$addzero."0";
			}
		}
		$grid_dept=$addzero.$grid_dept;
		
		####################### group-dept code ############
		$grid_group_dept=$grid_group.$grid_dept;
		$addzero="";
		$strlen=0;
		$shortlen=0;
		$strlen = strlen($grid_group_dept);
		if ($strlen<6) {
			$shortlen = 6 - $strlen;
			for ($z=1 ; $z<=$shortlen ; $z++) {
				$addzero=$addzero."0";
			}
		}
		$grid_group_dept=$addzero.$grid_group_dept;
		
		####################### format upc code ############
		$addzero="";
		$strlen=0;
		$shortlen=0;
		$strlen = strlen($grid_upc);
		if ($strlen<22) {
			$shortlen = 22 - $strlen;
			for ($z=1 ; $z<=$shortlen ; $z++) {
				$addzero=$addzero."0";
			}
		}
		$grid_upc=$addzero.$grid_upc;
		
		$contents = $contents . "\"" . $grid_upc . "\",\"" . $grid_group_dept . "\",\"" . $grid_price . "\",\"" . $grid_desc . "\"\r\n";
	}
	
	fwrite($handle2, $contents);
	
	fclose($handle2) ;
	Header ("Content-Type: application/octet-stream; name=" . $file_name);
	Header ("Content-Disposition: attachment; filename=" . $file_name); 
	readfile($file_name);
	unlink($file_name);
	exit();

?>


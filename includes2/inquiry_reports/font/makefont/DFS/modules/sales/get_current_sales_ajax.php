<?
	require_once "../etc/etc.obj.php";
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	include "../../functions/inquiry_session.php";
	$db = new DB;
	$db->connect();
	$locCode = $_GET['locCode'];
	$docDate = $_GET['docDate'];
	$from_loc=trim(getCodeofString($locCode)); 
	if ($docDate>"") {
		$docDate = new DateTime($docDate);
		$docDate = $docDate->format("m/d/Y");		
	} else {
		$docDate="";
	}
	$j=1;
	
	$qryTran = "SELECT TOP 100 PERCENT tblProdClass.prdGrpCode, tblProdClass.prdClsDesc, SUM(tblDlySalesSummary.slsExtAmt) AS Expr1, 
				SUM(tblDlySalesSummary.slsDiscAmt) AS Expr2, MAX(tblDlySalesSummary.compCode) as Expr3
				FROM tblDlySalesSummary INNER JOIN
				tblProdClass ON tblDlySalesSummary.prdGrpCode = tblProdClass.prdGrpCode
				WHERE (tblDlySalesSummary.locCode = $from_loc) AND (tblDlySalesSummary.slsDate = '$docDate') AND (tblProdClass.prdClsLvl = 1) AND (tblDlySalesSummary.compCode=$company_code)
				GROUP BY tblProdClass.prdGrpCode, tblProdClass.prdClsDesc
				ORDER BY tblProdClass.prdClsDesc";
	$resSales = mssql_query($qryTran);
	$num = mssql_num_rows($resSales);
	echo "document.getElementById('textfield').value='$num record/s found.';";
	echo "new_length = parseInt(document.getElementById('table_sales').rows.length) - 1;
		  for(var i=new_length; i>=2 ; i--) {
		  	  document.getElementById('table_sales').rows[i].deleteCell(3);
			  document.getElementById('table_sales').rows[i].deleteCell(2);
			  document.getElementById('table_sales').rows[i].deleteCell(1);
			  document.getElementById('table_sales').rows[i].deleteCell(0);
			  document.getElementById('table_sales').deleteRow(i);
		  }
		 ";
	for ($i=0; $i < $num; $i++) {
		$group=mssql_result($resSales,$i,"prdGrpCode")."-".mssql_result($resSales,$i,"prdClsDesc");
		$ext_amt=mssql_result($resSales,$i,"Expr1");
		$disc_amt=mssql_result($resSales,$i,"Expr2");
		$ext_amt = number_format($ext_amt,2);
		$disc_amt = number_format($disc_amt,2);
		$j++;
		echo "document.getElementById('table_sales').insertRow($j);
			  document.getElementById('table_sales').rows[$j].insertCell(0); 
			  document.getElementById('table_sales').rows[$j].insertCell(1); 
			  document.getElementById('table_sales').rows[$j].insertCell(2); 
			  document.getElementById('table_sales').rows[$j].insertCell(3); 
			  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = '$group'; 
			  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = '$ext_amt'; 
			  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = '$disc_amt'; 
			  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = ''; 
			  document.getElementById('table_sales').rows[$j].cells[1].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[2].align = 'right'; 
			 ";
	
	}
	$qryGrand = "SELECT TOP 100 PERCENT SUM(slsExtAmt) AS Expr1, SUM(slsDiscAmt) AS Expr2
				 FROM tblDlySalesSummary
				 WHERE (locCode = $from_loc) AND (slsDate = '$docDate') AND (compCode = $company_code)";
	$resGrand = mssql_query($qryGrand);
	$ext_grand=mssql_result($resGrand,0,"Expr1");
	$disc_grand=mssql_result($resGrand,0,"Expr2");
	$ext_grand = number_format($ext_grand,2);
	$disc_grand = number_format($disc_grand,2);
	$j++;
	echo "document.getElementById('table_sales').insertRow($j);
		  document.getElementById('table_sales').rows[$j].insertCell(0); 
		  document.getElementById('table_sales').rows[$j].insertCell(1); 
		  document.getElementById('table_sales').rows[$j].insertCell(2); 
		  document.getElementById('table_sales').rows[$j].insertCell(3); 
		  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = 'GRAND TOTAL'; 
		  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = '$ext_grand'; 
		  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = '$disc_grand'; 
		  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = ''; 
		  document.getElementById('table_sales').rows[$j].cells[0].align = 'right'; 
		  document.getElementById('table_sales').rows[$j].cells[1].align = 'right'; 
		  document.getElementById('table_sales').rows[$j].cells[2].align = 'right'; 
		  document.getElementById('table_sales').rows[$j].cells[0].strong = 'true'; 
		  document.getElementById('table_sales').rows[$j].cells[1].strong = 'true'; 
		  document.getElementById('table_sales').rows[$j].cells[2].strong = 'true'; 
		  document.getElementById('table_sales').rows[$j].style.backgroundColor = '#E6E6E6';
		 ";
?>
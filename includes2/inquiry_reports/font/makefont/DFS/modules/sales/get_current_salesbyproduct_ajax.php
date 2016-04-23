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
	
	$qryTran = "SELECT TOP 100 PERCENT tblProdMast.prdNumber, tblProdMast.prdDesc, tblDlySalesSummary.unitPrice, tblDlySalesSummary.slsQty, 
                tblDlySalesSummary.slsExtAmt, tblDlySalesSummary.slsDiscAmt, tblDlySalesSummary.compCode, tblDlySalesSummary.locCode, tblDlySalesSummary.slsDate
				FROM tblDlySalesSummary INNER JOIN
                tblProdMast ON tblDlySalesSummary.slsSkuNo = tblProdMast.prdNumber
				WHERE (tblDlySalesSummary.locCode = $from_loc) AND (tblDlySalesSummary.slsDate = '$docDate') AND (tblDlySalesSummary.compCode=$company_code)
				ORDER BY tblProdMast.prdDesc";
	$resSales = mssql_query($qryTran);
	$num = mssql_num_rows($resSales);
	echo "document.getElementById('textfield').value='$num record/s found.';";
	echo "new_length = parseInt(document.getElementById('table_sales').rows.length) - 1;
		  for(var i=new_length; i>=2 ; i--) {
		  	  document.getElementById('table_sales').rows[i].deleteCell(5);
		  	  document.getElementById('table_sales').rows[i].deleteCell(4);
		  	  document.getElementById('table_sales').rows[i].deleteCell(3);
			  document.getElementById('table_sales').rows[i].deleteCell(2);
			  document.getElementById('table_sales').rows[i].deleteCell(1);
			  document.getElementById('table_sales').rows[i].deleteCell(0);
			  document.getElementById('table_sales').deleteRow(i);
		  }
		 ";
	for ($i=0; $i < $num; $i++) {
		$sku=mssql_result($resSales,$i,"prdNumber");
		$desc=mssql_result($resSales,$i,"prdDesc");
		$unit_price=mssql_result($resSales,$i,"unitPrice");
		$qty=mssql_result($resSales,$i,"slsQty");
		$ext_amt=mssql_result($resSales,$i,"slsExtAmt");
		$disc_amt=mssql_result($resSales,$i,"slsDiscAmt");
		$unit_price = number_format($unit_price,2);
		$ext_amt = number_format($ext_amt,2);
		$disc_amt = number_format($disc_amt,2);
		$qty = number_format($qty,0);
		$j++;
		echo "document.getElementById('table_sales').insertRow($j);
			  document.getElementById('table_sales').rows[$j].insertCell(0); 
			  document.getElementById('table_sales').rows[$j].insertCell(1); 
			  document.getElementById('table_sales').rows[$j].insertCell(2); 
			  document.getElementById('table_sales').rows[$j].insertCell(3); 
			  document.getElementById('table_sales').rows[$j].insertCell(4); 
			  document.getElementById('table_sales').rows[$j].insertCell(5); 
			  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = '$sku'; 
			  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = '$desc'; 
			  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = '$unit_price'; 
			  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = '$qty'; 
			  document.getElementById('table_sales').rows[$j].cells[4].innerHTML = '$ext_amt';
			  document.getElementById('table_sales').rows[$j].cells[5].innerHTML = '$disc_amt';
			  document.getElementById('table_sales').rows[$j].cells[2].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[3].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[4].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[5].align = 'right'; 
			 ";
	
	}
	$qryGrand = "SELECT TOP 100 PERCENT SUM(slsExtAmt) AS Expr1, SUM(slsDiscAmt) AS Expr2, SUM(slsQty) as Expr3
				 FROM tblDlySalesSummary
				 WHERE (locCode = $from_loc) AND (slsDate = '$docDate') AND (compCode = $company_code)";
	$resGrand = mssql_query($qryGrand);
	$ext_grand=mssql_result($resGrand,0,"Expr1");
	$disc_grand=mssql_result($resGrand,0,"Expr2");
	$qty_grand=mssql_result($resGrand,0,"Expr3");
	$ext_grand = number_format($ext_grand,2);
	$disc_grand = number_format($disc_grand,2);
	$qty_grand = number_format($qty_grand,0);
	$j++;
	echo "document.getElementById('table_sales').insertRow($j);
		  document.getElementById('table_sales').rows[$j].insertCell(0); 
			  document.getElementById('table_sales').rows[$j].insertCell(1); 
			  document.getElementById('table_sales').rows[$j].insertCell(2); 
			  document.getElementById('table_sales').rows[$j].insertCell(3); 
			  document.getElementById('table_sales').rows[$j].insertCell(4); 
			  document.getElementById('table_sales').rows[$j].insertCell(5); 
			  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = ''; 
			  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = ''; 
			  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = ''; 
			  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = 'GRAND TOTAL'; 
			  document.getElementById('table_sales').rows[$j].cells[4].innerHTML = '$ext_grand';
			  document.getElementById('table_sales').rows[$j].cells[5].innerHTML = '$disc_grand';
			  document.getElementById('table_sales').rows[$j].cells[2].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[3].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[4].align = 'right'; 
			  document.getElementById('table_sales').rows[$j].cells[5].align = 'right'; 
		  document.getElementById('table_sales').rows[$j].style.backgroundColor = '#E6E6E6';
		 ";
?>
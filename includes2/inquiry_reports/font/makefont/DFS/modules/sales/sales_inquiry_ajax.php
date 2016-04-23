<?
	require_once "../etc/etc.obj.php";
	require("../inventory/lbd_function.php");
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	include "../../functions/inquiry_session.php";
	$db = new DB;
	$db->connect();
	$do = $_GET['do'];
	$by_search = $_GET['by_search'];
	$j=1;
	if ($by_search=="by_class") {
		$hide_rows = $_GET['hide_rows'];
		$group = $_GET['group'];
		$dept = $_GET['dept'];
		$cls = $_GET['cls'];
		$subcls = $_GET['subcls'];
		$group=getCodeofString($group); 
		$group=trim($group);
		$dept=getCodeofString($dept); 
		$dept=trim($dept);
		$cls=getCodeofString($cls); 
		$cls=trim($cls);
		$subcls=getCodeofString($subcls); 
		$subcls=trim($subcls);
		
		if($do=="box_group") {
				$qryClass = "SELECT * FROM tblProdClass WHERE prdGrpCode = '$group' AND prdClsLvl = '2' ORDER BY prdClsDesc ASC";
				$resClass = mssql_query($qryClass);
				$num = mssql_num_rows($resClass);
				echo "optionMenu = document.getElementById('box_dept');
					  for (var i=0; i < optionMenu.options.length; i++) {
						  optionMenu.options[i] = null;
					  }";
					  
				echo "optionMenu[0] = new Option('','');";
				for($i=1; $i<$num; $i++) {
					$dept=mssql_result($resClass,$i,"prdDeptCode")."-".mssql_result($resClass,$i,"prdClsDesc"); 
					echo "optionMenu = document.getElementById('box_dept');
						  optionMenu[$i] = new Option('$dept','$dept');";
				}
		}
		if($do=="box_dept") {
				$qryClass = "SELECT * FROM tblProdClass WHERE prdDeptCode = '$dept' AND prdGrpCode = '$group' AND prdClsLvl = '3' ORDER BY prdClsDesc ASC";
				$resClass = mssql_query($qryClass);
				$num = mssql_num_rows($resClass);
				echo "optionMenu = document.getElementById('box_cls');
					  for (var i=0; i < optionMenu.options.length; i++) {
						  optionMenu.options[i] = null;
					  }";
				
				echo "optionMenu[0] = new Option('','');";
				for($i=1; $i<$num; $i++) {
					$cls=mssql_result($resClass,$i,"prdClsCode")."-".mssql_result($resClass,$i,"prdClsDesc"); 
					echo "optionMenu = document.getElementById('box_cls');
						  optionMenu[$i] = new Option('$cls','$cls');";
				}
		}
		if($do=="box_cls") {
				$qryClass = "SELECT * FROM tblProdClass WHERE prdClsCode = '$cls' AND prdDeptCode = '$dept' AND prdGrpCode = '$group' AND prdClsLvl = '4' ORDER BY prdClsDesc ASC";
				$resClass = mssql_query($qryClass);
				$num = mssql_num_rows($resClass);
				echo "optionMenu = document.getElementById('box_subcls');
					  for (var i=0; i < optionMenu.options.length; i++) {
						  optionMenu.options[i] = null;
					  }";
					  
				echo "optionMenu[0] = new Option('','');";
				for($i=1; $i<$num; $i++) {
					$subcls=mssql_result($resClass,$i,"prdSubClsCode")."-".mssql_result($resClass,$i,"prdClsDesc"); 
					echo "optionMenu = document.getElementById('box_subcls');
						  optionMenu[$i] = new Option('$subcls','$subcls');";
				}
		}
	}
	if ($do=="search_button") {
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$from_location = $_GET['from_location'];
		$code_desc = $_GET['code_desc'];
		$prod_code1 = $_GET['prod_code1'];
		$prod_code2 = $_GET['prod_code2'];
		$hide_rows = $_GET['hide_rows'];
		$group = $_GET['group'];
		$dept = $_GET['dept'];
		$cls = $_GET['cls'];
		$subcls = $_GET['subcls'];
		$group=getCodeofString($group); 
		$group=trim($group);
		$dept=getCodeofString($dept); 
		$dept=trim($dept);
		$cls=getCodeofString($cls); 
		$cls=trim($cls);
		$subcls=getCodeofString($subcls); 
		$subcls=trim($subcls);
		$from_location=getCodeofString($from_location); 
		$from_location=trim($from_location);
		$qryTran = "SELECT tblInvTran.compCode, tblProdMast.prdNumber, tblProdMast.prdDesc, tblInvTran.prdGroup, tblInvTran.prdDept, 
					tblInvTran.prdClass, tblInvTran.prdSubClass, tblInvTran.docDate, tblInvTran.trQtyGood, tblInvTran.unitPrice, 
					tblInvTran.extAmt, tblInvTran.itemDiscPcents, tblInvTran.itemDiscCogY, tblInvTran.transCode,tblInvTran.locCode
					FROM tblInvTran INNER JOIN
					tblProdMast ON tblInvTran.prdNumber = tblProdMast.prdNumber ";
		$sql_group="SELECT MIN(tblProdMast.prdDesc) AS DDD, SUM(tblInvTran.extAmt) AS BBB, SUM(tblInvTran.itemDiscCogY) AS CCC, 
					SUM(tblInvTran.trQtyGood) AS AAA, tblInvTran.docDate, MAX(tblProdMast.prdDesc) AS EEE, COUNT(tblProdMast.prdDesc) AS FFF 
					FROM tblInvTran INNER JOIN 
					tblProdMast ON tblInvTran.prdNumber = tblProdMast.prdNumber ";
		if ($by_search=="by_class") {
			if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls>"")) {
				$qryTran .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.prdDept = $dept) AND (tblInvTran.prdClass = $cls) AND (tblInvTran.prdSubClass = $subcls) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date') 
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
				$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept') AND (prdClass = '$cls') AND (prdSubClass = '$subcls')	
							 GROUP BY tblInvTran.docDate";
			}
			if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls=="")) {
				$qryTran .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.prdDept = $dept) AND (tblInvTran.prdClass = $cls) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
				$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept') AND (prdClass = '$cls')
							 GROUP BY tblInvTran.docDate";
			}
			if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls=="") && ($subcls=="")) {
				$qryTran .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.prdDept = $dept) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
				$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept')
							 GROUP BY tblInvTran.docDate";
			}
			if (($by_search=="by_class") && ($group>"") && ($dept=="") && ($cls=="") && ($subcls=="")) {
				$qryTran  .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
				$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group')
							 GROUP BY tblInvTran.docDate";
			}
		} else {
			if (($by_search=="by_product") && ($code_desc=="check_code") && ($prod_code1>"") && ($prod_code2>"")) {
				$qryTran .= "WHERE (tblProdMast.prdNumber BETWEEN '$prod_code1' AND '$prod_code2') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			}
			if (($by_search=="by_product") && ($code_desc=="check_code") && ($prod_code1=="") && ($prod_code2>"")) {
				$qryTran .= "WHERE (tblProdMast.prdNumber LIKE '$prod_code2%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			}
			if (($by_search=="by_product") && ($code_desc=="check_code") && ($prod_code1>"") && ($prod_code2=="")) {
				$qryTran .= "WHERE (tblProdMast.prdNumber LIKE '$prod_code1%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			}
			if (($by_search=="by_product") && ($code_desc=="check_desc") && ($prod_code1>"") && ($prod_code2>"")) {
				$qryTran .= "WHERE (tblProdMast.prdDesc BETWEEN '$prod_code1' AND '$prod_code2') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						 	 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			}
			if (($by_search=="by_product") && ($code_desc=="check_desc") && ($prod_code1=="") && ($prod_code2>"")) {
				$qryTran .= "WHERE (tblProdMast.prdDesc LIKE '$prod_code2%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			}
			if (($by_search=="by_product") && ($code_desc=="check_desc") && ($prod_code1>"") && ($prod_code2=="")) {
				$qryTran .= "WHERE (tblProdMast.prdDesc LIKE '$prod_code1%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
							 AND (tblInvTran.locCode = $from_location)
							 ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			}
			
		}
		$resTran = mssql_query($qryTran);
		$num = mssql_num_rows($resTran);
		echo "document.getElementById('textfield').value='$num record/s found.';";
	}			
	if($do=="search_button") {
		echo "new_length = parseInt(document.getElementById('table_sales').rows.length) - 1;
			  for(var i=new_length; i>=2 ; i--) {
				  document.getElementById('table_sales').rows[i].deleteCell(6);
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
			$prdNumber=mssql_result($resTran,$i,"prdNumber");
			$resProd = mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $prdNumber");
			$prdDesc=mssql_result($resProd,0,"prdDesc");
			$docDate=mssql_result($resTran,$i,"docDate");
			$newDocDate=$docDate[0].$docDate[1].$docDate[2].$docDate[3].$docDate[4].$docDate[5].$docDate[6].$docDate[7].$docDate[8].$docDate[9].$docDate[10];
			if ($docDate>"") {
				$docDate = new DateTime($docDate);
				$docDate = $docDate->format("m/d/Y");		
			} else {
				$docDate="";
			}
			$trQtyGood=mssql_result($resTran,$i,"trQtyGood");
			$unitPrice=mssql_result($resTran,$i,"unitPrice");
			$extAmt=mssql_result($resTran,$i,"extAmt");
			$itemDiscPcents=mssql_result($resTran,$i,"itemDiscCogY");
			$trQtyGood=number_format($trQtyGood,2);
			$unitPrice=number_format($unitPrice,2);
			$extAmt=number_format($extAmt,2);
			$itemDiscPcents=number_format($itemDiscPcents,2);
			
			if ($by_search=="by_product") { ###SEARCH BY PRODUCT
				$rst_total=mssql_query("SELECT SUM(trQtyGood) AS AAA, SUM(extAmt) AS BBB, SUM(itemDiscCogY) AS CCC, COUNT(prdNumber) AS DDD, MAX(docDate) AS EEE, MAX(prdNumber) AS FFF, MIN(docDate) AS GGG FROM tblInvTran WHERE (docDate >= '$from_date' AND docDate <= '$to_date') AND prdNumber = '$prdNumber' AND transCode = '021' AND compCode = $company_code");
				$total_qty=number_format(mssql_result($rst_total,0,"AAA"),2);
				$total_ext=number_format(mssql_result($rst_total,0,"BBB"),2);
				$total_disc=number_format(mssql_result($rst_total,0,"CCC"),2);
				$total_record=mssql_result($rst_total,0,"DDD");
				$max_date=mssql_result($rst_total,0,"EEE");
				$max_sku=mssql_result($rst_total,0,"FFF");
				$min_date=mssql_result($rst_total,0,"GGG");
				if ($max_date>"") {
					$max_date = new DateTime($max_date);
					$max_date = $max_date->format("m/d/Y");		
				} else {
					$max_date="";
				}
				if ($min_date>"") {
					$min_date = new DateTime($min_date);
					$min_date = $min_date->format("m/d/Y");		
				} else {
					$min_date="";
				}
				$j++;
			
				if ($min_date!=$docDate) { 
					$prdNumber="";
					$prdDesc="";
				}
				echo "document.getElementById('table_sales').insertRow($j);
					  document.getElementById('table_sales').rows[$j].insertCell(0); 
					  document.getElementById('table_sales').rows[$j].insertCell(1); 
					  document.getElementById('table_sales').rows[$j].insertCell(2); 
					  document.getElementById('table_sales').rows[$j].insertCell(3); 
					  document.getElementById('table_sales').rows[$j].insertCell(4); 
					  document.getElementById('table_sales').rows[$j].insertCell(5); 
					  document.getElementById('table_sales').rows[$j].insertCell(6); 
					  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = '$prdNumber'; 
					  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = '$prdDesc'; 
					  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = '$newDocDate'; 
					  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = '$unitPrice'; 
					  document.getElementById('table_sales').rows[$j].cells[4].innerHTML = '$trQtyGood'; 
					  document.getElementById('table_sales').rows[$j].cells[5].innerHTML = '$extAmt'; 
					  document.getElementById('table_sales').rows[$j].cells[6].innerHTML = '$itemDiscCogY'; 
					  document.getElementById('table_sales').rows[$j].cells[3].align = 'right'; 
					  document.getElementById('table_sales').rows[$j].cells[4].align = 'right'; 
					  document.getElementById('table_sales').rows[$j].cells[5].align = 'right'; 
					  document.getElementById('table_sales').rows[$j].cells[6].align = 'right'; 
					 ";
				if ($max_date==$docDate) {
					$j++;
					echo "document.getElementById('table_sales').insertRow($j);
						  document.getElementById('table_sales').rows[$j].insertCell(0); 
						  document.getElementById('table_sales').rows[$j].insertCell(1); 
						  document.getElementById('table_sales').rows[$j].insertCell(2); 
						  document.getElementById('table_sales').rows[$j].insertCell(3); 
						  document.getElementById('table_sales').rows[$j].insertCell(4); 
						  document.getElementById('table_sales').rows[$j].insertCell(5); 
						  document.getElementById('table_sales').rows[$j].insertCell(6); 
						  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = 'Total ($total_record item/s)'; 
						  document.getElementById('table_sales').rows[$j].cells[4].innerHTML = '$total_qty'; 
						  document.getElementById('table_sales').rows[$j].cells[5].innerHTML = '$total_ext'; 
						  document.getElementById('table_sales').rows[$j].cells[6].innerHTML = '$total_disc'; 
						  document.getElementById('table_sales').rows[$j].cells[2].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[3].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[4].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[5].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[6].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].style.backgroundColor = '#E6E6E6';
						 ";
				}
			} else { ###SEARCH BY GROUP
				$sql_group="SELECT MIN(tblProdMast.prdDesc) AS DDD, SUM(tblInvTran.extAmt) AS BBB, SUM(tblInvTran.itemDiscCogY) AS CCC, 
					SUM(tblInvTran.trQtyGood) AS AAA, tblInvTran.docDate, MAX(tblProdMast.prdDesc) AS EEE, COUNT(tblProdMast.prdDesc) AS FFF 
					FROM tblInvTran INNER JOIN 
					tblProdMast ON tblInvTran.prdNumber = tblProdMast.prdNumber ";
				if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls>"")) {
					$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept') AND (prdClass = '$cls') AND (prdSubClass = '$subcls')	
								 GROUP BY tblInvTran.docDate";
				}
				if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls=="")) {
					$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept') AND (prdClass = '$cls')
								 GROUP BY tblInvTran.docDate";
				}
				if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls=="") && ($subcls=="")) {
					$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept')
								 GROUP BY tblInvTran.docDate";
				}
				if (($by_search=="by_class") && ($group>"") && ($dept=="") && ($cls=="") && ($subcls=="")) {
					$sql_group.="WHERE (tblInvTran.docDate = '$docDate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group')
								 GROUP BY tblInvTran.docDate";
				}
				$rst_total=mssql_query($sql_group);
				$total_qty=number_format(mssql_result($rst_total,0,"AAA"),2);
				$total_ext=number_format(mssql_result($rst_total,0,"BBB"),2);
				$total_disc=number_format(mssql_result($rst_total,0,"CCC"),2);
				$min_desc=mssql_result($rst_total,0,"DDD");
				$max_desc=mssql_result($rst_total,0,"EEE");
				$total_record=mssql_result($rst_total,0,"FFF");
				$j++;
			
				if (strtoupper(trim($min_desc))!=strtoupper(trim($prdDesc))) { 
					$newDocDate="";
				}
				echo "document.getElementById('table_sales').insertRow($j);
					  document.getElementById('table_sales').rows[$j].insertCell(0); 
					  document.getElementById('table_sales').rows[$j].insertCell(1); 
					  document.getElementById('table_sales').rows[$j].insertCell(2); 
					  document.getElementById('table_sales').rows[$j].insertCell(3); 
					  document.getElementById('table_sales').rows[$j].insertCell(4); 
					  document.getElementById('table_sales').rows[$j].insertCell(5); 
					  document.getElementById('table_sales').rows[$j].insertCell(6); 
					  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = '$prdNumber'; 
					  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = '$prdDesc'; 
					  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = '$newDocDate'; 
					  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = '$unitPrice'; 
					  document.getElementById('table_sales').rows[$j].cells[4].innerHTML = '$trQtyGood'; 
					  document.getElementById('table_sales').rows[$j].cells[5].innerHTML = '$extAmt'; 
					  document.getElementById('table_sales').rows[$j].cells[6].innerHTML = '$itemDiscCogY'; 
					  document.getElementById('table_sales').rows[$j].cells[3].align = 'right'; 
					  document.getElementById('table_sales').rows[$j].cells[4].align = 'right'; 
					  document.getElementById('table_sales').rows[$j].cells[5].align = 'right'; 
					  document.getElementById('table_sales').rows[$j].cells[6].align = 'right'; 
					 ";
				if (strtoupper(trim($max_desc))==strtoupper(trim($prdDesc))) { 
					$j++;
					echo "document.getElementById('table_sales').insertRow($j);
						  document.getElementById('table_sales').rows[$j].insertCell(0); 
						  document.getElementById('table_sales').rows[$j].insertCell(1); 
						  document.getElementById('table_sales').rows[$j].insertCell(2); 
						  document.getElementById('table_sales').rows[$j].insertCell(3); 
						  document.getElementById('table_sales').rows[$j].insertCell(4); 
						  document.getElementById('table_sales').rows[$j].insertCell(5); 
						  document.getElementById('table_sales').rows[$j].insertCell(6); 
						  document.getElementById('table_sales').rows[$j].cells[0].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[1].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[2].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[3].innerHTML = ''; 
						  document.getElementById('table_sales').rows[$j].cells[4].innerHTML = 'Total ($total_record item/s)'; 
						  document.getElementById('table_sales').rows[$j].cells[5].innerHTML = '$total_ext'; 
						  document.getElementById('table_sales').rows[$j].cells[6].innerHTML = '$total_disc'; 
						  document.getElementById('table_sales').rows[$j].cells[2].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[3].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[4].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[5].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].cells[6].align = 'right'; 
						  document.getElementById('table_sales').rows[$j].style.backgroundColor = '#E6E6E6';
						 ";
				}	
			}
		}	  	
	}
?>
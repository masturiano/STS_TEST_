<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../etc/etc.obj.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$newdate="Run Date : ".$newdate;
	
	$do = $_GET['do'];
	$by_search = $_GET['by_search'];
	
	$from_date = $_GET['from_date'];
	$to_date = $_GET['to_date'];
	$from_to = "From ".$from_date." To ".$to_date;
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
	if ($by_search=="by_class") {
		if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls>"")) {
			$qryTran .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.prdDept = $dept) AND (tblInvTran.prdClass = $cls) AND (tblInvTran.prdSubClass = $subcls) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date') 
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
			$qryMaxRec="SELECT COUNT(docDate) AS docDate FROM tblInvTran WHERE (prdGroup = $group) AND (prdDept = $dept) AND 
						(prdClass = $cls) AND (prdSubClass = $subcls) AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location)
						GROUP BY docDate, prdGroup, prdDept, prdClass, prdSubClass, transCode, compCode, locCode";
		}
		if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls=="")) {
			$qryTran .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.prdDept = $dept) AND (tblInvTran.prdClass = $cls) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
			$qryMaxRec="SELECT COUNT(docDate) AS docDate FROM tblInvTran
						WHERE (prdGroup = $group) AND (prdDept = $dept) AND 
						(prdClass = $cls) AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') 
						AND (locCode = $from_location)
						GROUP BY docDate, prdGroup, prdDept, prdClass, transCode, compCode, locCode";
		}
		if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls=="") && ($subcls=="")) {
			$qryTran .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.prdDept = $dept) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
			$qryMaxRec="SELECT COUNT(docDate) AS docDate FROM tblInvTran
						WHERE (prdGroup = $group) AND (prdDept = $dept) AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') 
						AND (locCode = $from_location)
						GROUP BY docDate, prdGroup, prdDept, transCode, compCode, locCode";
		}
		if (($by_search=="by_class") && ($group>"") && ($dept=="") && ($cls=="") && ($subcls=="")) {
			$qryTran  .= "WHERE (tblInvTran.prdGroup = $group) AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblInvTran.docDate, tblProdMast.prdDesc ASC";
			$qryMaxRec="SELECT COUNT(docDate) AS docDate FROM tblInvTran
						WHERE (prdGroup = $group) AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') 
						AND (locCode = $from_location)
						GROUP BY docDate, prdGroup, transCode, compCode, locCode";
		}
	} else {
		if (($by_search=="by_product") && ($code_desc=="check_code") && ($prod_code1>"") && ($prod_code2>"")) {
			$qryTran .= "WHERE (tblProdMast.prdNumber BETWEEN '$prod_code1' AND '$prod_code2') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (prdNumber BETWEEN '$prod_code1' AND 
						'$prod_code2') AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location) 
						GROUP BY prdNumber, transCode, compCode, locCode";
		}
		if (($by_search=="by_product") && ($code_desc=="check_code") && ($prod_code1=="") && ($prod_code2>"")) {
			$qryTran .= "WHERE (tblProdMast.prdNumber LIKE '$prod_code2%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (prdNumber LIKE '$prod_code2%'
						AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location) 
						GROUP BY prdNumber, transCode, compCode, locCode";
		}
		if (($by_search=="by_product") && ($code_desc=="check_code") && ($prod_code1>"") && ($prod_code2=="")) {
			$qryTran .= "WHERE (tblProdMast.prdNumber LIKE '$prod_code1%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (prdNumber LIKE '$prod_code1%'
						AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location) 
						GROUP BY prdNumber, transCode, compCode, locCode";
		}
		if (($by_search=="by_product") && ($code_desc=="check_desc") && ($prod_code1>"") && ($prod_code2>"")) {
			$qryTran .= "WHERE (tblProdMast.prdDesc BETWEEN '$prod_code1' AND '$prod_code2') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (prdDesc BETWEEN '$prod_code1' AND '$prod_code2'
						AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location) 
						GROUP BY prdNumber, transCode, compCode, locCode";
		}
		if (($by_search=="by_product") && ($code_desc=="check_desc") && ($prod_code1=="") && ($prod_code2>"")) {
			$qryTran .= "WHERE (tblProdMast.prdDesc LIKE '$prod_code2%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (prdDesc LIKE '$prod_code2%'
						AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location) 
						GROUP BY prdNumber, transCode, compCode, locCode";
		}
		if (($by_search=="by_product") && ($code_desc=="check_desc") && ($prod_code1>"") && ($prod_code2=="")) {
			$qryTran .= "WHERE (tblProdMast.prdDesc LIKE '$prod_code1%') AND (tblInvTran.transCode = '021') AND (tblInvTran.compCode = $company_code) AND (tblInvTran.docDate >= '$from_date' AND tblInvTran.docDate <= '$to_date')
						AND (tblInvTran.locCode = $from_location)
						ORDER BY tblProdMast.prdDesc, tblInvTran.docDate ASC";
			$qryMaxRec="SELECT COUNT(prdNumber) AS prdNumber FROM tblInvTran WHERE (prdDesc LIKE '$prod_code1%'
						AND (transCode = '021') AND (compCode = $company_code) AND 
						(docDate >= '$from_date' AND docDate <= '$to_date') AND (locCode = $from_location) 
						GROUP BY prdNumber, transCode, compCode, locCode";
		}
		
	}
	$rstOtherLine = mssql_query($qryMaxRec);
	$numOtherLine = mssql_num_rows($rstOtherLine);
	$resulinventorytrans = mssql_query($qryTran);
	$numinventorytrans = mssql_num_rows($resulinventorytrans);
	$query_company="SELECT * FROM tblCompany WHERE (compCode = $company_code)";
	$result_company=mssql_query($query_company);
	$num_company = mssql_num_rows($result_company);
	if ($num_company >0){
		$comp_name=mssql_result($result_company,$i,"compName");
	} else {
		$comp_name="NA";
	}
	###################################################################
	//include FPDF class
	require_once "../../functions/inquiry_reports/fpdf.php";
	define('FPDF_FONTPATH','../../functions./inquiry_reports/font/');
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$dtl_ht=4;
	$m_width=200;
	$max_tot_line=55;
	if ($by_search=="by_product") {
		$m_line = 35;  ///maximum line
		$m_line_2 = 55;
	} else {
		$m_line = 50;  ///maximum line
	}
	$m_width_3_fields=66;
	$font="Courier";
	$m_page=(($numOtherLine*3)+$numinventorytrans) / $m_line;
	$m_page=ceil($m_page); //// maximum page
	$tmp_first=0;          //// temporary first record
	$tmp_last=0;           //// temporary last record
	$tmp_rec=$numinventorytrans; //// temporary total record
	$tmp_rec_2=($numOtherLine*3)+$numinventorytrans; //// temporary total record
	
	for ($i=0;$i < $numinventorytrans;$i++){
		$sku=mssql_result($resulinventorytrans,$i,"prdNumber");
		$griddocdate=mssql_result($resulinventorytrans,$i,"docDate");
		if ($griddocdate>"") {
			$date = new DateTime($griddocdate);
			$griddocdate = $date->format("m/d/Y");		
		} else {
			$griddocdate="";
		}
		$sku=mssql_result($resulinventorytrans,$i,"prdNumber");
		$rst_sku=mssql_query("SELECT * FROM tblProdMast WHERE prdNumber = $sku");
		$grid_product=$sku. " " .mssql_result($rst_sku,0,"prdDesc");
		$grid_desc = mssql_result($rst_sku,0,"prdDesc");
		$grid_product=str_replace("\\","",$grid_product);
		$gridtrQtyGood=mssql_result($resulinventorytrans,$i,"trQtyGood");
		$grid_price_cost=mssql_result($resulinventorytrans,$i,"unitPrice");
		$gridextAmt=mssql_result($resulinventorytrans,$i,"extAmt");
		$griddisc=mssql_result($resulinventorytrans,$i,"itemDiscCogY");
		$gridtrQtyGood = number_format($gridtrQtyGood, 2);   
		$gridextAmt = number_format($gridextAmt, 2);  
		$griddisc = number_format($griddisc, 2); 
		$grid_price_cost = number_format($grid_price_cost, 2); 
		
		if ($by_search=="by_product") { ####### SEARCH BY PRODUCT														
			$rst_total=mssql_query("SELECT SUM(trQtyGood) AS AAA, SUM(extAmt) AS BBB, SUM(itemDiscCogY) AS CCC, COUNT(prdNumber) AS DDD, MAX(docDate) AS EEE, MAX(prdNumber) AS FFF, MIN(docDate) AS GGG FROM tblInvTran WHERE (docDate >= '$from_date' AND docDate <= '$to_date') AND prdNumber = '$sku' AND transCode = '021' AND compCode = $company_code");
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
			if ($min_date==$griddocdate) { 
				$pdf->ln();
				$pdf->ln();
				$pdf->SetFont($font, 'B', '10');
				$pdf->Cell(25,$dtl_ht, $grid_product, 0, 0);
				$pdf->SetFont($font, '', '10');
			}
			$pdf->ln();
			
			$pdf->Cell(25,$dtl_ht, $griddocdate, 0, 0);
			$pdf->Cell(30,$dtl_ht, $grid_price_cost, 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $gridtrQtyGood, 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $gridextAmt, 0, 0,'R');
			$pdf->Cell(30,$dtl_ht, $griddisc, 0, 0,'R');
			if ($max_date==$griddocdate) {
				$pdf->ln();
				$pdf->Cell(55,$dtl_ht, "Total ($total_record item/s)", 0, 0);
				$pdf->Cell(30,$dtl_ht, $total_qty, 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, $total_ext, 0, 0,'R');
				$pdf->Cell(30,$dtl_ht, $total_disc, 0, 0,'R');
			}
			//echo $griddocdate." ";
		} else {  ####### SEARCH BY GROUP
			$sql_group="SELECT MIN(tblProdMast.prdDesc) AS DDD, SUM(tblInvTran.extAmt) AS BBB, SUM(tblInvTran.itemDiscCogY) AS CCC, 
				SUM(tblInvTran.trQtyGood) AS AAA, tblInvTran.docDate, MAX(tblProdMast.prdDesc) AS EEE, COUNT(tblProdMast.prdDesc) AS FFF 
				FROM tblInvTran INNER JOIN 
				tblProdMast ON tblInvTran.prdNumber = tblProdMast.prdNumber ";
			if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls>"")) {
				$sql_group.="WHERE (tblInvTran.docDate = '$griddocdate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept') AND (prdClass = '$cls') AND (prdSubClass = '$subcls')	
							 GROUP BY tblInvTran.docDate";
			}
			if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls>"") && ($subcls=="")) {
				$sql_group.="WHERE (tblInvTran.docDate = '$griddocdate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept') AND (prdClass = '$cls')
							 GROUP BY tblInvTran.docDate";
			}
			if (($by_search=="by_class") && ($group>"") && ($dept>"") && ($cls=="") && ($subcls=="")) {
				$sql_group.="WHERE (tblInvTran.docDate = '$griddocdate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group') AND (prdDept = '$dept')
							 GROUP BY tblInvTran.docDate";
			}
			if (($by_search=="by_class") && ($group>"") && ($dept=="") && ($cls=="") && ($subcls=="")) {
				$sql_group.="WHERE (tblInvTran.docDate = '$griddocdate') AND transCode = '021' AND compCode = $company_code AND (prdGroup = '$group')
							 GROUP BY tblInvTran.docDate";
			}
			$rst_total=mssql_query($sql_group);
			$total_qty=number_format(mssql_result($rst_total,0,"AAA"),2);
			$total_ext=number_format(mssql_result($rst_total,0,"BBB"),2);
			$total_disc=number_format(mssql_result($rst_total,0,"CCC"),2);
			$min_desc=mssql_result($rst_total,0,"DDD");
			$max_desc=mssql_result($rst_total,0,"EEE");
			$total_record=mssql_result($rst_total,0,"FFF");
			
			if (strtoupper(trim($min_desc))==strtoupper(trim($grid_desc))) { 
				$pdf->ln();
				$pdf->ln();
				$pdf->SetFont($font, 'B', '10');
				$pdf->Cell(25,$dtl_ht, $griddocdate, 0, 0);
				$pdf->SetFont($font, '', '10');
			}
			$pdf->ln();
			$pdf->Cell(90,$dtl_ht, $grid_product, 0, 0);
			$pdf->Cell(25,$dtl_ht, $grid_price_cost, 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, $gridtrQtyGood, 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, $gridextAmt, 0, 0,'R');
			$pdf->Cell(25,$dtl_ht, $griddisc, 0, 0,'R');
			if (strtoupper(trim($max_desc))==strtoupper(trim($grid_desc))) { 
				$pdf->ln();
				$pdf->Cell(90,$dtl_ht, "", 0, 0);
				$pdf->Cell(25,$dtl_ht, "Total ($total_record item/s)", 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, $total_qty, 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, $total_ext, 0, 0,'R');
				$pdf->Cell(25,$dtl_ht, $total_disc, 0, 0,'R');
			}
		}
	} 
	$pdf->Output();
?>
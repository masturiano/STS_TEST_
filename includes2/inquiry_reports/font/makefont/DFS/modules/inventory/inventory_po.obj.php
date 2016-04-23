<?
#Description: Inventory Module Object (related to Purchasing Module)
#Author: Jhae Torres
#Date Created: July 03, 2008


class inventoryObject
{
	function changePOStatus($company_code, $po_number, $status){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poStat = '".$status."'
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'";
		$db->query($query);
	}
	
	function insertIntoRADetail($company_code, $po_number, $ra_number, $product_code, $uom, $conv_factor, $ra_ordered_qty, $po_unit_cost){
		global $db;
		$insert_query = "INSERT INTO tblRaItemDtl(compCode, poNumber, raNumber, prdNumber,
							umCode, prdConv, raOrderedQty, poUnitCost, rcrQty)
						VALUES('".$company_code."', '".$po_number."', '".$ra_number."', '".$product_code."',
							'".$uom."', '".$conv_factor."', '".$ra_ordered_qty."', '".$po_unit_cost."', '0')";
		$db->query($insert_query);
		
		$query = "SELECT compCode, poNumber, raNumber, prdNumber,
					umCode, prdConv, raOrderedQty, poUnitCost, rcrQty
				FROM tblRaItemDtl
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND raNumber = '$ra_number'
					AND prdNumber = '$product_code'
					AND umCode = '$uom'
					AND prdConv = '$conv_factor'
					AND raOrderedQty = '$ra_ordered_qty'
					AND poUnitCost = '$po_unit_cost'
					AND rcrQty = '0'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function insertIntoRAHeader($company_code, $po_number, $ra_number, $print_date, $print_by){
		global $db;
		$insert_query = "INSERT INTO tblRaHeader(compCode, poNumber, raNumber, raDate, raPrintedBy)
						VALUES('".$company_code."', '".$po_number."', '".$ra_number."', '".$print_date."', '".$print_by."')";
		$db->query($insert_query);
		
		$query = "SELECT compCode, poNumber, raNumber, raDate, raPrintedBy
				FROM tblRaHeader
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND raNumber = '$ra_number'
					AND raDate = '$print_date'
					AND raPrintedBy = '$print_by'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function getLocations($company_code, $location_code){
		global $db;
		if($location_code != '') $and.=" AND locCode = '$location_code'";
		
		$query = "SELECT locCode, locName, locType
				FROM tblLocation
				WHERE compCode = '$company_code'
					$and
				ORDER BY locName ASC";
		$db->query($query);
		($location_code != '') ? $locations=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $locations=$db->getArrResult();
		return $locations;
	}
	
	function getRAInfo($ra_number){
		global $db;
		$query = "SELECT ra.raNumber, ra.raStat, ra.raDate, ra.compCode, ra.poNumber,
					po.poNumber AS po_number, po.poStat, po.poDate, po.poBuyer, po.suppCode,
					supp.suppCode AS supp_code, supp.suppName, supp.suppTerms, supp.suppCurr,
					curr.currCode, curr.currUsdRate,
					stat.statCode, stat.statName
				FROM tblRaHeader AS ra INNER JOIN
					tblPoHeader AS po ON ra.compCode = po.compCode
						AND ra.poNumber = po.poNumber INNER JOIN
					tblSuppliers AS supp ON po.suppCode = supp.suppCode INNER JOIN
					tblCurrency AS curr ON supp.suppCurr = curr.currCode INNER JOIN
					tblStatus stat ON po.poStat = stat.statCode
				WHERE stat.tableName = 'tblPoHeader'
					AND ra.raNumber = $ra_number";
		$db->query($query);
		$ra_info = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $ra_info;
	}
	
	function getRADetail($company_code, $po_number, $ra_number, $product_code){
		global $db;
		if($product_code != '') $and .= " AND ra.prdNumber = '$product_code'";
		
		$query = "SELECT ra.compCode, ra.poNumber, ra.raNumber, ra.prdNumber, ra.umCode, ra.prdConv, ra.raOrderedQty, ra.poUnitCost,
					prod.prdNumber AS product_code, prod.prdDesc, 
					rcr.compCode AS rcr_compCode, rcr.prdNumber AS rcr_prdNumber, rcr.rcrNumber,
						rcr.rcrQtyGood, rcr.rcrQtyBad, rcr.rcrQtyFree
				FROM tblRaItemDtl ra INNER JOIN
                      tblProdMast prod ON ra.prdNumber = prod.prdNumber LEFT OUTER JOIN
                      tblRcrItemDtl rcr ON ra.raNumber = rcr.raNumber AND ra.compCode = rcr.compCode AND ra.prdNumber = rcr.prdNumber
				WHERE ra.compCode = '$company_code'
					AND ra.poNumber = '$po_number'
					AND ra.raNumber = '$ra_number'
					$and
				ORDER BY ra.prdNumber ASC";
		$db->query($query);
		($product_code != '') ? $ra_detail=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $ra_detail=$db->getArrResult();
		return $ra_detail;
	}
	
	function getRCRNumber($company_code, $ra_number){
		global $db;
		$query = "SELECT rcrNumber, rcrDate
				FROM tblRcrHeader
				WHERE compCode = '$company_code'
					AND raNumber = '$ra_number'";
		$db->query($query);
		$rcr = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $rcr;
	}
	
	function checkIfRCRHeaderExist($company_code, $rcr_number, $rcr_type){
		global $db;
		if($rcr_number!='') $and .= " AND rcr.rcrNumber = '$rcr_number'";
		if($rcr_type!='') $and .= " AND rcr.rcrType = '$rcr_type'";
		
		$query = "SELECT rcr.rcrNumber, rcr.rcrDate, rcr.carrier, rcr.containerNo, rcr.rcrReceivedBy, rcr.rcrRemarks,
						rcr.compCode, rcr.poNumber, rcr.poDate, rcr.raNumber, rcr.raDate,
						rcr.suppCode, rcr.poTerms, rcr.rcrLocation, rcr.poBuyer, rcr.suppCurr, rcr.currUsdRate, rcr.rcrStat, rcr.rcrType,
						rcr.rcrItemTotal, rcr.rcrExtTotal, rcr.rcrAddChargesTotal,
						stat.statCode, stat.statName, stat.tableName,
						loc.locName
				FROM tblRcrHeader AS rcr,
					tblStatus AS stat,
					tblLocation AS loc
				WHERE rcr.compCode = '$company_code'
					AND rcr.rcrStat = stat.statCode
					AND stat.tableName = 'tblRcrHeader'
					AND loc.compCode = rcr.compCode
					AND loc.locCode = rcr.rcrLocation
					$and";
		$db->query($query);
		($rcr_number!='' and $rcr_type!='') ? $rcr_header=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $rcr_header=$db->getArrResult();
		#($rcr_number!='') ? $rcr_header=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $rcr_header=$db->getArrResult();
		return $rcr_header;
	}
	
	function insertIntoRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
								$company_code, $po_number, $po_date, $ra_number, $ra_date, $rcr_type, $rcr_status,
								$supplier_code, $supplier_terms, $ra_location, $buyer, $currency_code, $usd_rate){
		global $db;
		$query = "INSERT INTO tblRcrHeader(rcrNumber, rcrDate, carrier, containerNo, rcrReceivedBy, rcrRemarks,
						compCode, poNumber, poDate, raNumber, raDate, rcrType, rcrStat,
						suppCode, poTerms, rcrLocation, poBuyer, suppCurr, currUsdRate)
				VALUES('".$rcr_number."', '".$rcr_date."', '".$carrier."', '".$container."', '".$received_by."', '".$remarks."',
						'".$company_code."', '".$po_number."', '".$po_date."', '".$ra_number."', '".$ra_date."', '".$rcr_type."', '".$rcr_status."',
						'".$supplier_code."', '".$supplier_terms."', '".$ra_location."', '".$buyer."', '".$currency_code."', '".$usd_rate."')";
		$db->query($query);
		
		$check_query = $this->checkIfRCRHeaderExist($company_code, $rcr_number, $rcr_type);
		(!empty($check_query)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function updateRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
							$company_code, $po_number, $po_date, $ra_number, $ra_date, $rcr_type, $rcr_status,
							$supplier_code, $supplier_terms, $ra_location, $buyer, $currency_code, $usd_rate){
		global $db;
		$delete_query = "DELETE FROM tblRcrHeader
					WHERE compCode = '$company_code'
						AND rcrNumber = '$rcr_number'";
		$db->query($delete_query);
						
		$reply = $this->insertIntoRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
									$company_code, $po_number, $po_date, $ra_number, $ra_date, $rcr_type, $rcr_status,
									$supplier_code, $supplier_terms, $ra_location, $buyer, $currency_code, $usd_rate);
		
		$check_query = $this->checkIfRCRHeaderExist($company_code, $rcr_number, $rcr_type);
		(!empty($check_query)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfRCRDetailExist($company_code, $rcr_number, $product_code){
		global $db;
		if($product_code!='') $and.=" AND rcr.prdNumber = '".$product_code."'";
		
		$query = "SELECT rcr.compCode, rcr.rcrNumber, rcr.prdNumber, rcr.umCode, rcr.prdConv, rcr.poUnitCost,
					rcr.orderedQty, rcr.rcrQtyGood, rcr.rcrQtyBad, rcr.rcrQtyFree, rcr.rcrStat,
					rcr.rcrExtAmt, rcr.itemDiscCogY, rcr.poLevelDiscCogY, rcr.rcrAddCharges,
					rcr.itemDiscPcents, rcr.itemDiscCogN, rcr.poLevelDiscCogN,
					rcr.raNumber,
					prod.prdDesc
				FROM tblRcrItemDtl AS rcr,
					tblProdMast AS prod
				WHERE rcr.compCode = '$company_code'
					AND rcr.rcrNumber = '$rcr_number'
					AND rcr.prdNumber = prod.prdNumber
					$and";
		$db->query($query);
		($product_code!='') ? $rcr_detail=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $rcr_detail=$db->getArrResult();
		return $rcr_detail;
	}
	
	function insertIntoRCRDetail($company_code, $rcr_number, $product_code, $uom, $conversion, $ra_number, $unit_cost,
									$ordered_qty, $good, $bad, $free){
		global $db;
		$query = "INSERT INTO tblRcrItemDtl(compCode, rcrNumber, prdNumber, umCode, prdConv, raNumber, poUnitCost, 
						orderedQty, rcrQtyGood, rcrQtyBad, rcrQtyFree)
				VALUES('".$company_code."', '".$rcr_number."', '".$product_code."', '".$uom."', '".$conversion."', '".$ra_number."', '".$unit_cost."',
						'".$ordered_qty."', '".$good."', '".$bad."', '".$free."')";
		$db->query($query);		
		
		$check_query = $this->checkIfRCRDetailExist($company_code, $rcr_number, $product_code);
		(!empty($check_query)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function updateRCRDetail($company_code, $rcr_number, $product_code, $uom, $conversion, $ra_number, $unit_cost,
									$ordered_qty, $good, $bad, $free){
		global $db;
		$delete_query = "DELETE FROM tblRcrItemDtl
					WHERE compCode = '$company_code'
						AND rcrNumber = '$rcr_number'
						AND prdNumber = '$product_code'";
		$db->query($delete_query);
		
		$reply = $this->insertIntoRCRDetail($company_code, $rcr_number, $product_code, $uom, $conversion, $ra_number, $unit_cost,
												$ordered_qty, $good, $bad, $free);
		return $reply;
	}
	
	function deleteRCRDetail($company_code, $rcr_number, $product_code){
		global $db;
		$query = "DELETE FROM tblRcrItemDtl
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
		
		$line = $this->checkIfRCRDetailExist($company_code, $rcr_number, $product_code);
		(empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfRCRExist($company_code, $rcr_number){
		global $db;
		if($rcr_number!='') $and .= " AND head.rcrNumber = '".$rcr_number."' ";
		
		$query = "SELECT head.compCode, head.rcrNumber
					FROM tblRcrHeader AS head,
						tblRcrItemDtl AS dtl
					WHERE head.compCode = '$company_code'
						$and
						AND head.compCode = dtl.compCode
						AND head.rcrNumber = dtl.rcrNumber";
		$db->query($query);
		($rcr_number!='') ? $rcr=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $rcr=$db->getArrResult();
		return $rcr;
	}
	
	function deleteRCR($company_code, $rcr_number){
		global $db;
		$delete_header = "DELETE FROM tblRcrHeader
						WHERE compCode = '$company_code'
							AND rcrNumber = '$rcr_number'";
		$db->query($delete_header);
		
		$delete_detail = "DELETE FROM tblRcrItemDtl
						WHERE compCode = '$company_code'
							AND rcrNumber = '$rcr_number'";
		$db->query($delete_detail);
		
		$rcr_exist = $this->checkIfRCRExist($company_code, $rcr_number);
		(empty($rcr_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function getOpenPeriod($company_code){
		global $db;
		$query = "SELECT pdCode, pdDesc, pdYear
				FROM tblPeriod
				WHERE compCode = '$company_code'
					AND pdStat = 'O'";
		$db->query($query);
		$period = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $period;
	}
	
	function getRCR($company_code, $rcr_number){
		global $db;
		if($rcr_number != '') $and.=" AND rcr.rcrNumber LIKE '$rcr_number%'";
		
		$query = "SELECT rcr.rcrNumber, rcr.rcrDate, rcr.poNumber, rcr.poDate, rcr.raNumber, rcr.raDate, rcr.poTerms, rcr.rcrStat,
				loc.locName,
				supp.suppName
			FROM tblRcrHeader AS rcr,
				tblLocation AS loc,
				tblSuppliers AS supp
			WHERE rcr.rcrNumber != ''
				AND rcr.compCode = '$company_code'
				AND loc.compCode = '$company_code'
				AND rcr.rcrLocation = loc.locCode
				AND rcr.suppCode = supp.suppCode
					$and";
		$db->query($query);
		$rcr = $db->getArrResult();
		return $rcr;
	}
	
	function initializeRCRTotals($company_code, $rcr_number){
		global $db;
		$query = "UPDATE tblRcrHeader
				SET rcrItemTotal = 0,
					rcrQtyTotal = 0,
					rcrExtTotal = 0,
					rcrDiscAmtTotal = 0,
					rcrAllwAmtTotal = 0,
					rcrMiscAmtTotal = 0,
					rcrAddChargesTotal = 0
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'";
		$db->query($query);
	}
	
	function initializeRCRDiscountAmounts($company_code, $rcr_number, $product_code){
		global $db;
		$query = "UPDATE tblRcrItemDtl
				SET itemDiscCogY = 0,
					itemDiscCogN = 0,
					poLevelDiscCogY = 0,
					poLevelDiscCogN = 0
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}
	
	function updateDiscountTotal($company_code, $rcr_number, $item_disc_amt){
		global $db;
		$query = "SELECT rcrDiscAmtTotal
				FROM tblRcrHeader
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$total = $line['rcrDiscAmtTotal'];
		$new_total = $total + $item_disc_amt;

		$update_query = "UPDATE tblRcrHeader
					SET rcrDiscAmtTotal = '".$new_total."'
					WHERE compCode = '$company_code'
						AND rcrNumber = '$rcr_number'";
		$db->query($update_query);
	}
	
	function updateItemDiscount($company_code, $rcr_number, $product_code, $tag, $item_disc_amt, $disc_percent){
		global $db;
		$query = "SELECT itemDiscCog".$tag.", itemDiscPcents
				FROM tblRcrItemDtl
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$total = $line['itemDiscCog'.$tag];
		$new_total = $total + $item_disc_amt;
		(IS_NULL($line['itemDiscPcents']) or $line['itemDiscPcents']==0) ? $discount=$disc_percent.'%' : $discount=$line['itemDiscPcents'].', '.$disc_percent.'%';

		$update_query = "UPDATE tblRcrItemDtl
					SET itemDiscCog".$tag." = '".$new_total."',
						itemDiscPcents = '".$discount."'
					WHERE compCode = '$company_code'
						AND rcrNumber = '$rcr_number'
						AND prdNumber = '$product_code'";
		$db->query($update_query);
	}
	
	function updateRCRDtlExtAmt($company_code, $rcr_number, $product_code, $ext_amt){
		global $db;
		$query = "UPDATE tblRcrItemDtl
				SET rcrExtAmt = '".$ext_amt."'
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}
	
	function updatePODtlAfterRCR($company_code, $po_number, $product_code, $qty_with_cost, $ext_amt){
		global $db;
		$query = "SELECT rcrQty, rcrExtAmt
				FROM tblPoItemDtl
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);

		$rcr_qty = $line['rcrQty'] + $qty_with_cost;
		$rcr_ext_amt = $line['rcrExtAmt'] + $ext_amt;

		$update_query = "UPDATE tblPoItemDtl
					SET rcrQty = '".$rcr_qty."',
						rcrExtAmt = '".$rcr_ext_amt."'
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'
						AND prdNumber = '$product_code'";
		$db->query($update_query);
	}

	function updateRCRHeaderTotals($company_code, $rcr_number, $item_qty_total, $ext_amt){
		global $db;
		$query = "SELECT rcrItemTotal, rcrQtyTotal, rcrExtTotal
				FROM tblRcrHeader
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$item_total = $line['rcrItemTotal'] + 1;
		$qty_total = $line['rcrQtyTotal'] + $item_qty_total;
		$ext_total = $line['rcrExtTotal'] + $ext_amt;

		$update_query = "UPDATE tblRcrHeader
					SET rcrItemTotal = '".$item_total."',
						rcrQtyTotal = '".$qty_total."',
						rcrExtTotal = '".$ext_total."'
					WHERE compCode = '$company_code'
						AND rcrNumber = '$rcr_number'";
		$db->query($update_query);
	}
	
	function updateOtherRCRHeaderTotals($company_code, $rcr_number, $allow_total, $charge_total){
		global $db;
		$query = "UPDATE tblRcrHeader
			SET rcrAllwAmtTotal = '".$allow_total."',
				rcrAddChargesTotal = '".$charge_total."'
			WHERE compCode = '$company_code'
				AND rcrNumber = '$rcr_number'";
		$db->query($query);
	}
	
	function computeTotNetAmt($company_code, $rcr_number){
		global $db;
		$query = "SELECT rcrExtTotal, rcrDiscAmtTotal
				FROM tblRcrHeader
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$net_amt = $line['rcrExtTotal'] - $line['rcrDiscAmtTotal'];
		return $net_amt;
	}
	
	function getAddCharges($company_code, $po_number){
		global $db;
		$query = "SELECT compCode, poNumber, poAddChargePcent, poAddChargeAmt
				FROM tblPoAddCharges
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		$add_charges = $db->getArrResult();
		return $add_charges;
	}
	
	function allocateRCRAmt($company_code, $rcr_number, $product_code, $prod_alloc_y, $prod_alloc_n, $prod_alloc_add){
		global $db;
		$query = "UPDATE tblRcrItemDtl
				SET poLevelDiscCogY = '".$prod_alloc_y."',
					poLevelDiscCogN = '".$prod_alloc_n."',
					rcrAddCharges = '".$prod_alloc_add."'
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}
	
	function changeRCRStatus($company_code, $rcr_number, $rcr_status){
		global $db;
		$query = "UPDATE tblRcrHeader
				SET rcrStat = '".$rcr_status."'
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'";
		$db->query($query);
	}
	
	function checkIfInvBalMastExist($company_code, $location, $product_code, $period_year, $period_code){
		global $db;
		$query = "SELECT compCode, locCode, prdNumber, pdYear, pdMonth,
						begBalGoodM, begBalBoM,
						begCostM, begPriceM,
						mtdRecitQ, mtdRecitA,
						mtdRegSlesQ, mtdRegSlesA, mtdRegSlesC,
						mtdTransIn, mtdTransOut, mtdTransA,
						mtdAdjQ, mtdAdjA,
						mtdCountAdjQ, mtdCountAdjA,
						mtdCiQ, mtdCiA, mtdSuQ, mtdSuA,
						endBalGoodM, endBalBoM,
						endCostM, endPriceM,
						updatedBy, dateUpdated
				FROM tblInvBalM
				WHERE compCode = '$company_code'
					AND locCode = '$location'
					AND prdNumber = '$product_code'
					AND pdYear = '$period_year'
					AND pdMonth = '$period_code'";
		$db->query($query);
		$inv_bal = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $inv_bal;
	}
	
	function initializeInvBalMast($company_code, $location, $product_code, $period_year, $period_code){
		global $db;
		$query = "INSERT INTO tblInvBalM(compCode, locCode, prdNumber, pdYear, pdMonth,
									begBalGoodM, begBalBoM, begCostM, begPriceM, mtdRecitQ, mtdRecitA,
									mtdRegSlesQ, mtdRegSlesA, mtdRegSlesC, mtdTransIn, mtdTransOut, mtdTransA,
									mtdAdjQ, mtdAdjA, mtdCountAdjQ, mtdCountAdjA, mtdCiQ, mtdCiA,
									mtdSuQ, mtdSuA, endBalGoodM, endBalBoM, endCostM, endPriceM)
								VALUES('".$company_code."', '".$location."', '".$product_code."', '".$period_year."', '".$period_code."',
									0, 0, 0, 0, 0, 0,
									0, 0, 0, 0, 0, 0,
									0, 0, 0, 0, 0, 0,
									0, 0, 0, 0, 0, 0)";
		$db->query($query);
	}
	
	function checkIfAveCostExist($company_code, $product_code){
		global $db;
		$query = "SELECT compCode, prdNumber,
					aveUnitCost
				FROM tblAveCost
				WHERE compCode = '$company_code'
					AND prdNumber = '$product_code'";
		$db->query($query);
		$ave_cost = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $ave_cost;
	}
	
	function initializeAveCost($company_code, $product_code){
		global $db;
		$query = "INSERT INTO tblAveCost(compCode, prdNumber)
								VALUES('".$company_code."', '".$product_code."')";
		$db->query($query);
	}
	
	function updateAveCost($company_code, $product_code, $uom, $doc_type, $rcr_number, $rcr_date, $date, $new_ave_cost){
		global $db;
		$query = "UPDATE tblAveCost
				SET umCode = '".$uom."',
					aveDocType = '".$doc_type."',
					aveDocNo = '".$rcr_number."',
					aveDocDate = '".$rcr_date."',
					lastUpdate = '".$date."',
					aveUnitCost = '".$new_ave_cost."'
				WHERE compCode = '$company_code'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}
	
	function writeCostHistory($company_code, $product_code, $doc_date, $doc_no, $uom, $doc_type, $new_ave_cost, $period_year, $period_month, $last_update){
		global $db;
		$query = "INSERT INTO tblAveCostHist(compCode, prdNumber, aveDocDate, aveDocNo, umCode,
										aveDocType, aveUnitCost, pdYear, pdMonth, lastUpdate)
									VALUES('".$company_code."', '".$product_code."', '".$doc_date."', '".$doc_no."', '".$uom."',
										'".$doc_type."', '".$new_ave_cost."', '".$period_year."', '".$period_month."', '".$last_update."')";
		$db->query($query);
	}
	
	function updateInvBalMast($company_code, $location, $product_code, $period_year, $period_code,
								$rcr_qty, $recit_cost, $rcr_qty_good, $rcr_qty_free, $rcr_qty_bad,
								$new_ave_cost, $updated_by, $update_date){
		global $db;
		$inv_bal = $this->checkIfInvBalMastExist($company_code, $location, $product_code, $period_year, $period_code);
		$mtd_recit_q = $inv_bal['mtdRecitQ'] + $rcr_qty;
		$mtd_recit_a = $inv_bal['mtdRecitA'] + $recit_cost;
		$end_bal_good_m = $inv_bal['endBalGoodM'] + $rcr_qty_good + $rcr_qty_free;
		$end_bal_bad_m = $inv_bal['endBalBoM'] + $rcr_qty_bad;

		$query = "UPDATE tblInvBalM
				SET mtdRecitQ = '".$mtd_recit_q."',
					mtdRecitA = '".$mtd_recit_a."',
					endBalGoodM = '".$end_bal_good_m."',
					endBalBoM = '".$end_bal_bad_m."',
					endCostM = '".$new_ave_cost."',
					updatedBy = '".$updated_by."',
					dateUpdated = '".$update_date."'
				WHERE compCode = '$company_code'
					AND locCode = '$location'
					AND prdNumber = '$product_code'
					AND pdYear = '$period_year'
					AND pdMonth = '$period_code'";
		$db->query($query);
	}
	
	function getProductInfo($product_code){
		global $db;
		$query = "SELECT prdGrpCode, prdDeptCode, prdClsCode, prdSubClsCode, prdType, prdSetTag
				FROM tblProdMast
				WHERE prdNumber = '$product_code'";
		$db->query($query);
		$product_info = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $product_info;
	}
	
	function writeInvTrans($company_code, $location, $product_code, $period_year, $period_code,
							$date_updated, $updated_by, $supplier, $location_type,
							$prod_group, $prod_dept, $prod_class, $prod_sub_class, $prod_type, $prod_set_tag,
							$rcr_number, $rcr_date, $ref_no, $trans_code,
							$item_disc_pcents, $item_disc_cog_y, $item_disc_cog_n, $po_level_disc_cog_y, $po_level_disc_cog_n,
							$rcr_add_charges, $rcr_ext_amt, $po_unit_cost,
							$rcr_qty_good, $rcr_qty_bad, $rcr_qty_free,
							$ref_cost_event, $po_terms, $new_ave_cost){
		global $db;
		$query = "INSERT INTO tblInvTran(compCode, locCode, prdNumber, pdYear, pdCode,
									dateUpdated, updatedBy, suppCode, locType,
									prdGroup, prdDept, prdClass, prdSubClass, prdType, setCode,
									docNumber, docDate, refNo, transCode,
									itemDiscPcents, itemDiscCogY, itemDiscCogN, poLevelDiscCogY, poLevelDiscCogN,
									rcrAddCharges, extAmt, buyCost,
									trQtyGood, trQtyBo, trQtyFree,
									refCostEvent, terms, aveCost)
								VALUES('".$company_code."', '".$location."', '".$product_code."', '".$period_year."', '".$period_code."',
									'".$date_updated."', '".$updated_by."', '".$supplier."', '".$location_type."',
									'".$prod_group."', '".$prod_dept."', '".$prod_class."', '".$prod_sub_class."', '".$prod_type."', '".$prod_set_tag."',
									'".$rcr_number."', '".$rcr_date."', '".$ref_no."', '".$trans_code."',
									'".$item_disc_pcents."', '".$item_disc_cog_y."', '".$item_disc_cog_n."', '".$po_level_disc_cog_y."', '".$po_level_disc_cog_n."',
									'".$rcr_add_charges."', '".$rcr_ext_amt."', '".$po_unit_cost."',
									'".$rcr_qty_good."', '".$rcr_qty_bad."', '".$rcr_qty_free."',
									'".$ref_cost_event."', '".$po_terms."', '".$new_ave_cost."')";
		$db->query($query);
		
		$check_query = "SELECT compCode, locCode, prdNumber, pdYear, pdCode
					FROM tblInvTran
					WHERE compCode = '$company_code'
						AND locCode = '$location'
						AND prdNumber = '$product_code'
						AND pdYear = '$period_year'
						AND pdCode = '$period_code'
						AND dateUpdated = '$date_updated'
						AND updatedBy = '$updated_by'
						AND suppCode = '$supplier'
						AND locType = '$location_type'
						AND prdGroup = '$prod_group'
						AND prdDept = '$prod_dept'
						AND prdClass = '$prod_class'
						AND prdSubClass = '$prod_sub_class'
						AND prdType = '$prod_type'
						AND setCode = '$prod_set_tag'
						AND docNumber = '$rcr_number'
						AND docDate = '$rcr_date'
						AND refNo = '$ref_no'
						AND transCode = '$trans_code'
						AND itemDiscPcents = '$item_disc_pcents'
						AND itemDiscCogY = '$item_disc_cog_y'
						AND itemDiscCogN = '$item_disc_cog_n'
						AND poLevelDiscCogY = '$po_level_disc_cog_y'
						AND poLevelDiscCogN = '$po_level_disc_cog_n'
						AND rcrAddCharges = '$rcr_add_charges'
						AND extAmt = '$rcr_ext_amt'
						AND buyCost = '$po_unit_cost'
						AND trQtyGood = '$rcr_qty_good'
						AND trQtyBo = '$rcr_qty_bad'
						AND trQtyFree = '$rcr_qty_free'
						AND refCostEvent = '$ref_cost_event'";
		$db->query($check_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfRCRAuditExist($company_code, $rcr_number){
		global $db;
		$query = "SELECT compCode, rcrNumber, poNumber
				FROM tblRcrAudit
				WHERE compCode = '$company_code'
					AND rcrNumber = '$rcr_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function updateRCRAudit($company_code, $rcr_number, $po_number, $date_field, $date, $operator_field, $operator){
		global $db;
		$rcr_audit_exist = $this->checkIfRCRAuditExist($company_code, $rcr_number);
		if($rcr_audit_exist == true){
			$query = "UPDATE tblRcrAudit
					SET ".$date_field." = '".$date."',
						".$operator_field." = '".$operator."'
					WHERE compCode = '$company_code'
						AND rcrNumber = '$rcr_number'";
		} else{
			$query = "INSERT INTO tblRcrAudit(compCode, rcrNumber, poNumber, ".$date_field.", ".$operator_field.")
									VALUES('".$company_code."', '".$rcr_number."', '".$po_number."', '".$date."', '".$operator."')";
		}
		$db->query($query);
	}

	function changeToPartialStat($company_code, $po_number){
		global $db;
		$check_stat = "SELECT poStat
					FROM tblPoHeader
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
		$db->query($check_stat);
		$po_stat = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		
		if($po_stat['poStat']!='C'){
			$query = "UPDATE tblPoHeader
					SET poStat = 'P'
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
			$db->query($query);
		}
	}
}
?>
<?
#Description: Purchasing Module Object
#Author: Jhae Torres
#Date Created: March 18, 2008


session_start();
class purchasingObject
{
	function updatePOStatus($company_code, $po_number, $status){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poStat = '".$status."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
	}
	
	function getMiscSequenceNumber($company_code, $po_number){
		global $db;
		$query = "SELECT MAX(poMiscSeq) AS sequenceNumber
				FROM tblPoMiscDtl
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		$sequence = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$misc_sequence = $sequence['sequenceNumber'] + 1;
		return $misc_sequence;
	}
	
	function checkIfPOExist($company_code, $po_number){
		global $db;
		$query = "SELECT compCode, poNumber, prdNumber
				FROM tblPoItemDtl
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		$po = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($po)) ? $reply=true : $reply=false;
		return $reply;
	}

	function getSupplier($supplier_code){
		global $db;
		if($supplier_code != '')  $and .= " AND suppCode = $supplier_code";
		
		$query = "SELECT suppCode, suppTerms, suppName, suppCurr
				FROM tblSuppliers
				WHERE suppStat != 'D'
					AND suppStat !='I'
					$and
				ORDER BY suppCode ASC";
		$db->query($query);
		($supplier_code != '') ? $supplier=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $supplier=$db->getArrResult();
		return $supplier;
	}
	
	function getCurrency($currency_code){
		global $db;
		$query = "SELECT currUsdRate
				FROM tblCurrency
				WHERE currCode = '$currency_code'
					AND currStat = 'A'";
		$db->query($query);
		$currency = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $currency;
	}

	function getAllowanceType($allowance_type){
		global $db;
		if($allowance_type != '') $and .= " AND allwTypeCode = '$allowance_type'";
		
		$query = "SELECT allwTypeCode, allwDesc, allwCostTag
				FROM tblAllowType
				WHERE allwStat = 'A'
					$and
				ORDER BY allwTypeCode ASC";
		$db->query($query);
		($allowance_type != '') ? $allowance_type=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $allowance_type=$db->getArrResult();
		return $allowance_type;
	}
	
	function getBuyers(){
		global $db;
		$query = "SELECT buyerCode, buyerName
				FROM tblBuyers
				WHERE buyerCode != ''
					AND buyerStat = 'A'
				ORDER BY buyerCode ASC";
		$db->query($query);
		$buyers = $db->getArrResult();
		return $buyers;
	}
	
	function countProducts($supplier_code){
		global $db;
		/*
		$query = "SELECT COUNT(prod.prdNumber) AS countPrdNumber
				FROM tblSuppliers AS supp,
					tblProdMast AS prod
				WHERE prod.suppCode = '$supplier_code'
					AND prod.suppCode = supp.suppCode
					AND (supp.suppStat != 'D' AND supp.suppStat !='I')";
		*/
		$query = "SELECT COUNT(prdNumber) AS countPrdNumber
				FROM tblProdMast
				WHERE prdNumber IN(SELECT prdNumber
								FROM tblVendorProduct
								WHERE suppCode = '$supplier_code'
								AND suppStat NOT IN('D', 'I'))";
		$db->query($query);
		$product_count = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $product_count['countPrdNumber'];
	}
	
	function getProducts($supplier_code){
		global $db;
		/*
		$query = "SELECT prod.prdNumber, prod.suppCode, prod.prdDesc, prod.prdBuyUnit, prod.prdFrcTag
				FROM tblSuppliers AS supp,
					tblProdMast AS prod
				WHERE prod.suppCode = $supplier_code
					AND prod.suppCode = supp.suppCode
					AND (supp.suppStat != 'D' AND supp.suppStat !='I')
				ORDER BY prod.prdNumber ASC";
		*/
		$query = "SELECT prdNumber, suppCode, prdDesc, prdBuyUnit, prdFrcTag
				FROM tblProdMast
				WHERE prdNumber IN(SELECT prdNumber
								FROM tblVendorProduct
								WHERE suppCode = '$supplier_code'
								AND suppStat NOT IN('D', 'I'))
				ORDER BY prdNumber";
		$db->query($query);
		$products = $db->getArrResult();
		return $products;
	}
	
	function getProductInfo($supplier_code, $product_code){
		global $db;
		$query = "SELECT prod.prdNumber, prod.suppCode, prod.prdDesc, prod.prdBuyUnit, prod.prdFrcTag
				FROM tblSuppliers AS supp,
					tblProdMast AS prod
				WHERE prod.suppCode = $supplier_code
					AND prod.suppCode = supp.suppCode
					AND (supp.suppStat != 'D' AND supp.suppStat !='I')
					AND prod.prdNumber = $product_code";
		$db->query($query);
		//$product_info = $db->getArrResult();
		$product_info = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $product_info;
	}
	
	function getUnitInfo($supplier_code, $product_code, $document_date){
		global $db;
		$query = "SELECT promoUnitCost AS unit_cost,
					promoCostEvent AS cost_event,
					umCode AS uom,
					prdConv AS conv_factor
				FROM tblProdCost
				WHERE suppCode = '$supplier_code'
					AND prdNumber = '$product_code'
					AND '$document_date' BETWEEN promoCostStart AND promoCostEnd";
		$db->query($query);
		$promo = mssql_fetch_array($db->result(), MSSQL_ASSOC);

		if(!empty($promo)){
			return $promo;
		} else{
			$query = "SELECT regUnitCost AS unit_cost,
						regCostEvent AS cost_event,
						umCode AS uom,
						prdConv AS conv_factor
					FROM tblProdCost
					WHERE suppCode = '$supplier_code'
						AND prdNumber = '$product_code'";
			$db->query($query);
			$regular = mssql_fetch_array($db->result(), MSSQL_ASSOC);
			return $regular;
		}
	}
	
	function getAllowanceInfo($supplier_code, $product_code, $document_date){
		global $db;
		$query = "SELECT allw.allwTypeCode, allw.allwPcent, allw.allwAmt,
					type.allwCostTag
				FROM tblAllowance AS allw,
					tblAllowType AS type
				WHERE allw.allwTypeCode = type.allwTypeCode
					AND allw.suppCode = '$supplier_code'
					AND allw.prdNumber = '$product_code'
					AND (allw.allwStat = 'A' OR allw.allwStat = '')
					AND '$document_date' BETWEEN allw.allwStartDate AND allw.allwEndDate";
		$db->query($query);
		$allowance_type = $db->getArrResult();
		return $allowance_type;
	}

	function checkIfPOHeaderExist($company_code, $po_number, $etc){
		global $db;
		$query = "SELECT head.compCode, head.poNumber, head.suppCode, head.poTerms, head.poBuyer, head.poDate, head.poExpDate,
					head.poAllTag, head.poTotAllow, head.poTotMisc,
					head.poReopenId, head.poReopenDate, head.corrTag, head.corrPrintTag, head.poReopenTag,
					head.poStat, head.poItemTotal, head.poQtyTotal, head.poCancelDate,
						head.poTotExt, head.poTotDisc,
					supp.suppName, supp.suppTerms, supp.suppCurr,
					stat.statName,
					curr.currUsdRate
				FROM tblPoHeader AS head,
					tblSuppliers AS supp,
					tblStatus AS stat,
					tblCurrency AS curr
				WHERE head.suppCode = supp.suppCode
					AND head.compCode = '$company_code'
					AND head.poNumber = '$po_number'
					AND stat.tableName = 'tblPoHeader'
					AND stat.statCode = head.poStat
					AND supp.suppCurr = curr.currCode";
		$db->query($query);
		$po_header = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $po_header;
	}

	function addPOHeader($company_code, $po_number, $supplier_code, $terms, $document_date, $expected_delivery, $buyer, $status, $hash_items, $hash_quantity, $cancel_date){
		global $db;
		$query = "INSERT INTO tblPoHeader(compCode, poNumber, suppCode, poTerms,
					poBuyer, poStat, poDate, poExpDate,
					poItemTotal, poQtyTotal, poCancelDate)
				VALUES('".$company_code."', '".$po_number."', '".$supplier_code."', '".$terms."',
					'".$buyer."', '".$status."', '".$document_date."', '".$expected_delivery."',
					'".$hash_items."', '".$hash_quantity."', '".$cancel_date."')";
		$db->query($query);
		
		$po_header_exist = $this->checkIfPOHeaderExist($company_code, $po_number, '');
		(!empty($po_header_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfPODetailExist($company_code, $po_number, $product_code){
		global $db;
		if($product_code != '') $and .= " AND item.prdNumber = '".$product_code."'";
		
		$query = "SELECT item.compCode, item.poNumber, item.prdNumber, item.umCode, item.orderedQty, item.poUnitCost, item.refCostEvent,
					item.poItemDelTag, item.rcrQty, item.rcrExtAmt, item.prdConv, item.poExtAmt,
					item.itemDiscPcents, item.itemDiscCogY, item.itemDiscCogN, item.poLevelDiscCogY, item.poLevelDiscCogN,
					prod.prdFrcTag, prod.prdDesc
				FROM tblPoItemDtl AS item,
					tblProdMast AS prod
				WHERE item.poItemDelTag NOT IN ('D', 'I')
					AND item.prdNumber = prod.prdNumber
					AND item.compCode = '".$company_code."'
					AND item.poNumber = '".$po_number."'
					$and";
		$db->query($query);
		($product_code!='') ? $po_detail=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $po_detail=$db->getArrResult();;
		return $po_detail;
	}
	
	function addPODetail($company_code, $po_number, $product_code, $uom, $quantity, $unit_cost, $cost_event, $conv_factor){
		global $db;
		$query = "INSERT INTO tblPoItemDtl(compCode, poNumber, prdNumber, umCode, orderedQty,
					poUnitCost, refCostEvent, prdConv)
				VALUES('".$company_code."', '".$po_number."', '".$product_code."', '".$uom."', '".$quantity."',
					'".$unit_cost."', '".$cost_event."', '".$conv_factor."')";
		$db->query($query);			
		
		$po_detail_exist = $this->checkIfPODetailExist($company_code, $po_number, $product_code);
		(!empty($po_detail_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function getPODetailsList($company_code, $po_number){
		global $db;
		$query = "SELECT item.prdNumber, item.orderedQty, item.umCode, item.poUnitCost, item.poExtAmt,
					prod.prdDesc, prod.prdConv
				FROM tblPoItemDtl AS item,
					tblProdMast AS prod
				WHERE item.prdNumber = prod.prdNumber
					AND item.compCode = '".$company_code."'
					AND item.poNumber = '".$po_number."'
				ORDER BY item.prdNumber ASC";
		$db->query($query);
		$po_detail_list = $db->getArrResult();
		return $po_detail_list;
	}
	
	function controlTotals($company_code, $po_number){
		global $db;
		$query = "SELECT COUNT(*) AS entered_items,
					SUM(orderedQty) AS entered_quantity
				FROM tblPoItemDtl
				WHERE orderedQty != 0
					AND compCode = '".$company_code."'
					AND poNumber = '".$po_number."'";
		$db->query($query);
		$control_totals = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $control_totals;
	}
	
	function updateControlTotals($po_number, $hash_items, $hash_quantity){
		global $db;
		$update_query = "UPDATE tblPoHeader
				SET poItemTotal = '".$hash_items."',
					poQtyTotal = '".$hash_quantity."'
				WHERE poNumber = '".$po_number."'";
		$db->query($update_query);
		
		$query = "SELECT poNumber, poItemTotal, poQtyTotal
				FROM tblPoHeader
				WHERE poNumber = '".$po_number."'
					AND poItemTotal = '".$hash_items."'
					AND poQtyTotal = '".$hash_quantity."'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function deletePODetail($company_code, $po_number, $product_code){
		global $db;

		$check_query = "SELECT poExtAmt
					FROM tblPoItemDtl
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'
						AND prdNumber = '$product_code'";
		$db->query($check_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		
		if($line['poExtAmt'] == 0){
			$query = "DELETE FROM tblPoItemDtl
					WHERE compCode = '".$company_code."'
						AND poNumber = '".$po_number."'
						AND prdNumber = '".$product_code."'";
		} else{
			$query = "UPDATE tblPoItemDtl
					SET orderedQty = 0
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'
						AND prdNumber = '$product_code'";
		}
		$db->query($query);
		
		$po_detail_exist = $this->checkIfPODetailExist($company_code, $po_number, $product_code);
		if(empty($po_detail_exist)){
			$reply = true;
		} else{
			($po_detail_exist['orderedQty'] == 0) ? $reply=true : $reply=false;
		}
		return $reply;
	}
	
	function editPOHeader($company_code, $po_number, $document_date, $expected_delivery, $cancel_date, $buyer, $status, $hash_items, $hash_quantity){
		global $db;
		$update_query = "UPDATE tblPoHeader
						SET poDate = '".$document_date."',
							poExpDate = '".$expected_delivery."',
							poCancelDate = '".$cancel_date."',
							poBuyer = '".$buyer."',
							poStat = '".$status."',
							poItemTotal = '".$hash_items."',
							poQtyTotal = '".$hash_quantity."'
						WHERE compCode = '".$company_code."'
							AND poNumber = '".$po_number."'";
		$db->query($update_query);
		
		$query = "SELECT poDate, poExpDate, poBuyer, poStat, poItemTotal, poQtyTotal
				FROM tblPoHeader
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND poDate = '".$document_date."'
					AND poExpDate = '".$expected_delivery."'
					AND poCancelDate = '".$cancel_date."'
					AND poBuyer = '".$buyer."'
					AND poStat = '".$status."'
					AND poItemTotal = '".$hash_items."'
					AND poQtyTotal = '".$hash_quantity."'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}

	function editPODetail($company_code, $po_number, $product_code, $quantity){
		global $db;
		$update_query = "UPDATE tblPoItemDtl
				SET orderedQty = '".$quantity."'
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND prdNumber = '".$product_code."'";
		$db->query($update_query);
		
		$query = "SELECT orderedQty
				FROM tblPoItemDtl
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND prdNumber = '".$product_code."'
					AND orderedQty = '".$quantity."'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}

	function checkIfPOItemDiscountExist($company_code, $po_number, $product_code, $allowance_type){
		global $db;
		if($allowance_type!='') $and .= " AND disc.allwTypeCode = '$allowance_type'";
		
		$query = "SELECT disc.poDiscSeq, disc.allwTypeCode, disc.poItemDiscPcnt, disc.poItemDiscAmt, disc.poItemDiscTag,
					type.allwDesc, type.allwCostTag
				FROM tblPoItemDisc AS disc,
					tblAllowType AS type
				WHERE disc.allwTypeCode = type.allwTypeCode
					AND disc.compCode = '$company_code'
					AND disc.poNumber = '$po_number'
					AND disc.prdNumber = '$product_code'
					$and
				ORDER BY disc.poDiscSeq ASC";
		$db->query($query);
		($allowance_type!='') ? $po_discount=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $po_discount=$db->getArrResult();;
		return $po_discount;
	}

	function addPOItemDiscount($company_code, $po_number, $product_code, $allowance_type, $sequence, $discount_percent, $discount_amount, $discount_tag){
		global $db;
		$query = "INSERT INTO tblPoItemDisc(compCode, poNumber, prdNumber, poDiscSeq,
					allwTypeCode, poItemDiscPcnt, poItemDiscAmt, poItemDiscTag)
				VALUES('".$company_code."', '".$po_number."', '".$product_code."', '".$sequence."',
					'".$allowance_type."', '".$discount_percent."', '".$discount_amount."', '".$discount_tag."')";
		$db->query($query);
		
		$po_item_discount_exist = $this->checkIfPOItemDiscountExist($company_code, $po_number, $product_code, $allowance_type);
		(!empty($po_item_discount_exist)) ? $reply=true : $reply=false;
		return $reply; 
	}

	function checkIfPOAllowanceDetailExist($company_code, $po_number, $allowance_type){
		global $db;
		if($allowance_type!='') $and.=" AND allw.allwTypeCode = '$allowance_type'";
		
		$query = "SELECT allw.allwTypeCode, allw.poAllwPcnt, allw.poAllwAmt, allw.poAllwTag,
					type.allwDesc, type.allwCostTag
				FROM tblPoAllwDtl AS allw,
					tblAllowType AS type
				WHERE allw.allwTypeCode = type.allwTypeCode
					AND allw.compCode = '$company_code'
					AND allw.poNumber = '$po_number'
					$and";
		$db->query($query);
		($allowance_type!='') ? $po_allowance=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $po_allowance=$db->getArrResult();
		return $po_allowance;
	}
	
	function addPOAllowanceDetail($company_code, $po_number, $allowance_type, $allowance_percent, $allowance_amount, $allowance_tag){
		global $db;
		$query = "INSERT INTO tblPoAllwDtl(compCode, poNumber,
					allwTypeCode, poAllwPcnt, poAllwAmt, poAllwTag)
				VALUES('".$company_code."', '".$po_number."',
					'".$allowance_type."', '".$allowance_percent."', '".$allowance_amount."', '".$allowance_tag."')";
		$db->query($query);
		
		$po_allowance_exist = $this->checkIfPOAllowanceDetailExist($company_code, $po_number, $allowance_type);
		(!empty($po_allowance_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function deletePOAllowanceDetail($company_code, $po_number, $allowance_type){
		global $db;
		$query = "DELETE FROM tblPoAllwDtl
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND allwTypeCode = '".$allowance_type."'";
		$db->query($query);
		
		$allowance_detail_exist = $this->checkIfPOAllowanceDetailExist($company_code, $po_number, $allowance_type);
		(empty($allowance_detail_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function editPOAllowanceDetail($company_code, $po_number, $allowance_type, $allowance_percent, $allowance_amount){
		global $db;
		$update_query = "UPDATE tblPoAllwDtl
				SET poAllwPcnt = '".$allowance_percent."',
					poallwAmt = '".$allowance_amount."'
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND allwTypeCode = '".$allowance_type."'";
		$db->query($update_query);
		
		$query = "SELECT poAllwPcnt, poAllwAmt
				FROM tblPoAllwDtl
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND allwTypeCode = '".$allowance_type."'
					AND poAllwPcnt = '".$allowance_percent."'
					AND poAllwAmt = '".$allowance_amount."'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfPOMiscExist($company_code, $po_number, $sequence){
		global $db;
		if($sequence!='') $and.=" AND poMiscSeq = '$sequence'";
		
		$query = "SELECT poMiscSeq, poMiscDesc, poMiscAmt
				FROM tblPoMiscDtl
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					$and";
		$db->query($query);
		($sequence!='') ? $po_misc=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $po_misc=$db->getArrResult();
		return $po_misc;
	}
	
	function addPOMisc($company_code, $po_number, $sequence, $misc_desc, $misc_amount){
		global $db;
		$query = "INSERT INTO tblPoMiscDtl(compCode, poNumber, poMiscSeq, poMiscDesc, poMiscAmt)
				VALUES('".$company_code."', '".$po_number."', '".$sequence."', '".$misc_desc."', '".$misc_amount."')";
		$db->query($query);
		
		$po_misc_exist = $this->checkIfPOMiscExist($company_code, $po_number, $sequence);
		(!empty($po_misc_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function deletePOMiscDetail($company_code, $po_number, $misc_sequence){
		global $db;
		$query = "DELETE FROM tblPoMiscDtl
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND poMiscSeq = '".$misc_sequence."'";
		$db->query($query);
		
		$misc_detail_exist = $this->checkIfPOMiscExist($company_code, $po_number, $misc_sequence);
		(empty($misc_detail_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function editPOMisc($company_code, $po_number, $sequence, $misc_desc, $misc_amt){
		global $db;
		$update_query = "UPDATE tblPoMiscDtl
				SET poMiscDesc = '".$misc_desc."',
					poMiscAmt = '".$misc_amt."'
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND poMiscSeq = '".$sequence."'";
		$db->query($update_query);
		
		$query = "SELECT poMiscDesc, poMiscAmt
				FROM tblPoMiscDtl
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'
					AND poMiscSeq = '".$sequence."'
					AND poMiscDesc = '".$misc_desc."'
					AND poMiscAmt = '".$misc_amt."'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfPORemarkExist($company_code, $po_number, $etc){
		global $db;
		$query = "SELECT compCode, poNumber, remark
				FROM tblPoRemarks
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'";
		$db->query($query);
		$remark = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $remark;
	}
	
	function addPORemark($company_code, $po_number, $remark){
		global $db;
		$query = "INSERT INTO tblPoRemarks(compCode, poNumber, remark)
				VALUES('".$company_code."', '".$po_number."', '".$remark."')";
		$db->query($query);
		
		$remark_exist = $this->checkIfPORemarkExist($company_code, $po_number, '');
		(!empty($remark_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function editPORemark($company_code, $po_number, $remark){
		global $db;
		$query = "UPDATE tblPoRemarks
				SET remark = '".$remark."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
	}
	
	function getPO($po_number){
		global $db;
		$company_code = $_SESSION['comp_code'];
		if($po_number != '') $and .= " AND head.poNumber LIKE '$po_number%'";
		
		$query = "SELECT head.poNumber, head.suppCode, head.poTerms,
					head.poDate, head.poExpDate, head.poBuyer,
					head.poItemTotal, head.poQtyTotal,
					head.poReopenTag,
					supp.suppName,
					statCode, statName,
					head.poStat
				FROM tblPoHeader AS head,
					tblSuppliers AS supp,
					tblStatus AS stat
				WHERE head.compCode = $company_code
					AND head.suppCode = supp.suppCode
					AND head.poStat = stat.statCode
					AND stat.tableName = 'tblPoHeader'
					$and
				ORDER BY head.poNumber ASC,
					head.suppCode ASC";
		$db->query($query);
		$po_list = $db->getArrResult();
		return $po_list;
	}
	
	function deletePO($table_name, $function, $company_code, $po_number){
		global $db;
		$query = "DELETE FROM ".$table_name."
				WHERE compCode = '".$company_code."'
					AND poNumber = '".$po_number."'";
		$db->query($query);
		
		$record_exist = $this->$function($company_code, $po_number, '');
		(empty($record_exist)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function initializeTotals($company_code, $po_number){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poItemTotal = 0,
					poQtyTotal = 0,
					poTotExt = 0,
					poTotDisc = 0,
					poTotAllow = 0,
					poTotMisc = 0
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
	}
	
	function updateItemDiscountPercentages($company_code, $po_number, $product_code, $discount){
		global $db;
		$select_discount = "SELECT itemDiscPcents
						FROM tblPoItemDtl
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'
							AND prdNumber = '$product_code'";
		$db->query($select_discount);
		$discount_percentages = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(empty($discount_percentages['itemDiscPcents']) or $discount_percentages['itemDiscPcents']==0) ? $discount=$discount.'%' : $discount=$discount_percentages['itemDiscPcents'].', '.$discount.'%';

		$query = "UPDATE tblPoItemDtl
				SET itemDiscPcents = '".$discount."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}
	
	function updateProductExtendedAmount($company_code, $po_number, $product_code, $extended_amount){
		global $db;
		$query = "UPDATE tblPoItemDtl
				SET poExtAmt = '".$extended_amount."',
					itemDiscCogY = 0,
					itemDiscCogN = 0
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}
	
	function updatePOItemDiscountAmount($company_code, $po_number, $product_code, $discount_sequence, $allowance_type_code, $discount_amount){
		global $db;
		$query = "UPDATE tblPoItemDisc
				SET poItemDiscAmt = '".$discount_amount."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND prdNumber = '$product_code'
					AND poDiscSeq = '$discount_sequence'
					AND allwTypeCode = '$allowance_type_code'";
		$db->query($query);
	}
	
	function updateItemDiscountCOG($company_code, $po_number, $product_code, $tag, $discount_amount){
		global $db;
		$select_query = "SELECT ".$tag."
						FROM tblPoItemDtl
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'
							AND prdNumber = '$product_code'";
		$db->query($select_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$total_discount_amount = $line[$tag] + $discount_amount;
		
		$query = "UPDATE tblPoItemDtl
				SET ".$tag." = '".$total_discount_amount."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
	}

	function updatePOHeader($company_code, $po_number, $ordered_quantity, $extended_amount){
		global $db;
		$select_query = "SELECT poItemTotal, poQtyTotal, poTotExt
						FROM tblPoHeader
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'";
		$db->query($select_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		
		$total_item = $line['poItemTotal'] + 1;
		$total_quantity = $line['poQtyTotal'] + $ordered_quantity;
		$total_ext_amt = $line['poTotExt'] + $extended_amount;
		
		$update_query = "UPDATE tblPoHeader
				SET poItemTotal = '".$total_item."',
					poQtyTotal = '".$total_quantity."',
					poTotExt = '".$total_ext_amt."',
					poStat = 'R'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($update_query);
		
		$query = "SELECT poNumber
				FROM tblPoHeader
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poItemTotal = '$total_item'
					AND poQtyTotal = '$total_quantity'
					AND poTotExt = '$total_ext_amt'
					AND poStat = 'R'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function tagPOReopenTag($company_code, $po_number, $po_reopen_tag, $corr_tag){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poReopenTag = '".$po_reopen_tag."',
					corrTag = '".$corr_tag."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
	}
	
	function updateTotalSKUDiscount($company_code, $po_number, $discount_amount){
		global $db;
		$select_query = "SELECT poTotDisc
						FROM tblPoHeader
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'";
		$db->query($select_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$total_sku_discount = $line['poTotDisc'] + $discount_amount;
		
		$update_query = "UPDATE tblPoHeader
					SET poTotDisc = '".$total_sku_discount."'
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
		$db->query($update_query);
		
		$query = "SELECT poTotDisc
				FROM tblPoHeader
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poTotDisc = '$total_sku_discount'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function updateTotalAllowanceAmount($company_code, $po_number, $allowance_amount){
		global $db;
		$select_query = "SELECT poTotAllow
						FROM tblPoHeader
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'";
		$db->query($select_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$total_allowance_amount = $line['poTotAllow'] + $allowance_amount;
		
		$update_query = "UPDATE tblPoHeader
					SET poTotAllow = '".$total_allowance_amount."'
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
		$db->query($update_query);
		
		$query = "SELECT poTotAllow
				FROM tblPoHeader
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poTotAllow = '$total_allowance_amount'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function updateTotalMiscellaneous($company_code, $po_number, $misc_amount){
		global $db;
		$select_query = "SELECT poTotMisc
					FROM tblPoHeader
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
		$db->query($select_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		$total_miscellaneous = $line['poTotMisc'] + $misc_amount;
		
		$update_query = "UPDATE tblPoHeader
					SET poTotMisc = '".$total_miscellaneous."'
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
		$db->query($update_query);
		
		$query = "SELECT poTotMisc
				FROM tblPoHeader
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poTotMisc = '$total_miscellaneous'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfPOAuditExist($company_code, $po_number){
		global $db;
		$query = "SELECT compCode, poNumber
				FROM tblPoAudit
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function updatePOAudit($company_code, $po_number, $date_field, $date, $operator_field, $operator){
		global $db;
		$po_audit_exist = $this->checkIfPOAuditExist($company_code, $po_number);
		if($po_audit_exist == true){
			$query = "UPDATE tblPoAudit
					SET ".$date_field." = '".$date."',
						".$operator_field." = '".$operator."'
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'";
		} else{
			$query = "INSERT INTO tblPoAudit(compCode, poNumber, ".$date_field.", ".$operator_field.")
						VALUES('".$company_code."', '".$po_number."', '".$date."', '".$operator."')";
		}
		$db->query($query);
	}
	
	function getPOAddChargesDetails($company_code, $po_number, $use){
		global $db;
		if($po_number != '') $and .= " AND head.poNumber = '$po_number'";

		$query = "SELECT head.compCode, head.poNumber, head.poDate, head.poStat,
					supp.suppName, supp.suppTerms,
					charge.poAddChargeRemarks, charge.poAddChargePcent, charge.poAddChargeAmt
				FROM tblPoHeader AS head
					LEFT JOIN tblSuppliers AS supp ON head.suppCode=supp.suppCode
					LEFT JOIN tblPoAddCharges AS charge ON head.poNumber=charge.poNumber
				WHERE head.compCode = '$company_code'
					$and
				ORDER BY head.poNumber ASC";
		$db->query($query);
		($use=='list') ? $po_list=$db->getArrResult() : $po_list=mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $po_list;
	}
	
	function insertIntoPOAddChargesDetails($company_code, $po_number, $remarks, $percent, $amount){
		global $db;
		$this->deletePOAddChargesDetails($company_code, $po_number);
		
		if($remarks==''){
			$query = "INSERT INTO tblPoAddCharges(compCode, poNumber, poAddChargePcent, poAddChargeAmt)
					VALUES('".$company_code."', '".$po_number."', '".$percent."', '".$amount."')";
		} else{
			$query = "INSERT INTO tblPoAddCharges(compCode, poNumber, poAddChargeRemarks, poAddChargePcent, poAddChargeAmt)
					VALUES('".$company_code."', '".$po_number."', '".$remarks."', '".$percent."', '".$amount."')";
		}
		$db->query($query);
		
		$check_query = "SELECT compCode, poNumber, poAddChargeRemarks, poAddChargePcent, poAddChargeAmt
					FROM tblPoAddCharges
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'
						AND poAddChargePcent = '$percent'
						AND poAddChargeAmt = '$amount'";
		$db->query($check_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function deletePOAddChargesDetails($company_code, $po_number){
		global $db;
		$query = "DELETE FROM tblPoAddCharges
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		
		$query_check = $this->getPOAddChargesDetails($company_code, $po_number, 'list');
		(empty($query_check['poAddChargeRemarks'])) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function reopenPO($company_code, $po_number, $reopen_tag, $reopen_date, $po_stat, $reopen_id){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poReopenTag = '".$reopen_tag."',
					poReopenDate = '".$reopen_date."',
					poStat = '".$po_stat."',
					poReopenId = '".$reopen_id."'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		
		$check_query = "SELECT compCode, poNumber
					FROM tblPoHeader
					WHERE compCode = '$company_code'
						AND poNumber = '$po_number'
						AND poReopenTag = '$reopen_tag'
						AND poReopenDate = '$reopen_date'
						AND poStat = '$po_stat'
						AND poReopenId = '$reopen_id'";
		$db->query($check_query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		(!empty($line)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function checkIfPODtlBackupExist($company_code, $po_number, $product_code){
		global $db;
		$query = "SELECT compCode, poNumber, prdNumber,
					orderedQty, poExtAmt,
					itemDiscCogY, itemDiscCogN, poLevelDiscCogY, poLevelDiscCogN
				FROM tblPoItemDtl1
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND prdNumber = '$product_code'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $line;
	}
	
	function backupPODtl($company_code, $po_number, $product_code,
					$uom, $prod_conv, $ordered_qty, $po_unit_cost, $po_ext_amt, $ref_cost_event,
					$item_disc_pcents, $item_disc_cog_y, $item_disc_cog_n, $po_level_disc_cog_y, $po_level_disc_cog_n,
					$rcr_qty, $rcr_ext_amt, $po_item_del_tag){
		global $db;
		$backup = $this->checkIfPODtlBackupExist($company_code, $po_number, $product_code);
		
		if(!empty($backup)){
			$delete_query = "DELETE FROM tblPoItemDtl1
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'
							AND prdNumber = '$product_code'";
			$db->query($delete_query);
		}
		
		$query = "INSERT INTO tblPoItemDtl1(compCode, poNumber, prdNumber,
				umCode, prdConv, orderedQty, poUnitCost, poExtAmt, refCostEvent,
				itemDiscPcents, itemDiscCogY, itemDiscCogN, poLevelDiscCogY, poLevelDiscCogN,
				rcrQty, rcrExtAmt, poItemDelTag)
			VALUES('".$company_code."', '".$po_number."', '".$product_code."',
				'".$uom."', '".$prod_conv."', '".$ordered_qty."', '".$po_unit_cost."', '".$po_ext_amt."', '".$ref_cost_event."',
				'".$item_disc_pcents."', '".$item_disc_cog_y."', '".$item_disc_cog_n."', '".$po_level_disc_cog_y."', '".$po_level_disc_cog_n."',
				'".$rcr_qty."', '".$rcr_ext_amt."', '".$po_item_del_tag."')";
		$db->query($query);
		
		$clear_query = "UPDATE tblPoItemDtl
						SET itemDiscPcents = ''
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'";
		$db->query($clear_query);
	}
	
	function checkIfPoHeaderBackupExist($company_code, $po_number){
		global $db;
		$query = "SELECT compCode, poNumber,
					poTotExt, poItemTotal, poQtyTotal, poTotDisc, poTotAllow
				FROM tblPoHeader1
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $line;
	}
	
	function backupPOHeader($company_code, $po_number,
			$supplier_code, $po_terms, $po_buyer, $po_date, $po_exp_date,
			$po_all_tag, $po_tot_allow, $po_tot_misc,
			$po_reopen_id, $po_reopen_date, $corr_tag, $corr_print_tag, $po_reopen_tag,
			$po_stat, $po_item_total, $po_qty_total, $po_cancel_date,
			$po_tot_ext, $po_tot_disc){
		global $db;
		$backup = $this->checkIfPOHeaderBackupExist($company_code, $po_number);
		
		if(!empty($backup)){
			$delete_query = "DELETE FROM tblPoHeader1
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'";
			$db->query($delete_query);
		}
		
		$query = "INSERT INTO tblPoHeader1(compCode, poNumber,
				suppCode, poTerms, poBuyer, poDate, poExpDate,
				poAllTag, poTotAllow, poTotMisc,
				poReopenId, poReopenDate, corrTag, corrPrintTag, poReopenTag,
				poStat, poItemTotal, poQtyTotal, poCancelDate,
				poTotExt, poTotDisc)
			VALUES('".$company_code."', '".$po_number."',
				'".$supplier_code."', '".$po_terms."', '".$po_buyer."', '".$po_date."', '".$po_exp_date."',
				'".$po_all_tag."', '".$po_tot_allow."', '".$po_tot_misc."',
				'".$po_reopen_id."', '".$po_reopen_date."', '".$corr_tag."', '".$corr_print_tag."', '".$po_reopen_tag."',
				'".$po_stat."', '".$po_item_total."', '".$po_qty_total."', '".$po_cancel_date."',
				'".$po_tot_ext."', '".$po_tot_disc."')";
		$db->query($query);
	}
	
	function checkIfPODtlCorrExist($company_code, $po_number, $product_code){
		global $db;
		if($product_code!='') $and. " AND prdNumber = '$product_code'";
		
		$query = "SELECT compCode, poNumber, prdNumber
				FROM tblPoItemCorr
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					$and";
		$db->query($query);
		($product_code!='') ? $line=mssql_fetch_array($db->result(), MSSQL_ASSOC) : $line=$db->getArrResult();
		return $line;
	}
	
	function writePODtlCorr($company_code, $po_number, $product_code,
					$uom, $prod_conv, $ordered_qty, $po_unit_cost, $po_ext_amt, $ref_cost_event,
					$item_disc_pcents, $item_disc_cog_y, $item_disc_cog_n, $po_level_disc_cog_y, $po_level_disc_cog_n,
					$new_item_tag){
		global $db;
		$corr = $this->checkIfPODtlCorrExist($company_code, $po_number, $product_code);
		
		###JAY(02/12/09) if(!empty($corr)){
			$delete_query = "DELETE FROM tblPoItemCorr
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'
							AND prdNumber = '$product_code'";
			$db->query($delete_query);
		###JAY(02/12/09) }

		if($ordered_qty != 0){ ###JAY(02/12/09)
			$query = "INSERT INTO tblPoItemCorr(compCode, poNumber, prdNumber,
					umCode, prdConv, orderedQty, poUnitCost, poExtAmt, refCostEvent,
					itemDiscPcents, itemDiscCogY, itemDiscCogN, poLevelDiscCogY, poLevelDiscCogN,
					newItemTag)
				VALUES('".$company_code."', '".$po_number."', '".$product_code."',
					'".$uom."', '".$prod_conv."', '".$ordered_qty."', '".$po_unit_cost."', '".$po_ext_amt."', '".$ref_cost_event."',
					'".$item_disc_pcents."', '".$item_disc_cog_y."', '".$item_disc_cog_n."', '".$po_level_disc_cog_y."', '".$po_level_disc_cog_n."',
					'".$new_item_tag."')";
			$db->query($query);
		}
	}
	
	function checkIfPOHeaderCorrExist($company_code, $po_number){
		global $db;
		$query = "SELECT compCode, poNumber
				FROM tblPoHeaderCorr
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'";
		$db->query($query);
		$line = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $line;
	}
	
	function writePOHeaderCorr($company_code, $po_number, $supp_code, $po_terms, $po_buyer, $po_date, $po_exp_date,
					$po_all_tag, $po_item_total, $po_qty_total, $po_tot_ext, $po_tot_disc, $po_tot_allow, $po_tot_misc, $po_stat,
					$po_reopen_id, $po_reopen_date, $corr_tag, $corr_print_tag, $po_reopen_tag, $po_cancel_date){
		global $db;
		$corr = $this->checkIfPOHeaderCorrExist($company_code, $po_number);
		
		###JAY(02/12/09) if(!empty($corr)){
			$delete_query = "DELETE FROM tblPoHeaderCorr
						WHERE compCode = '$company_code'
							AND poNumber = '$po_number'";
			$db->query($delete_query);
		###JAY(02/12/09) }
		
		#if($po_qty_total != 0){ ###JAY(02/12/09)	###JAY(02/19/09)
			$query = "INSERT INTO tblPoHeaderCorr(compCode, poNumber, suppCode, poTerms, poBuyer, poDate, poExpDate,
						poAllTag, poItemTotal, poQtyTotal, poTotExt, poTotDisc, poTotAllow, poTotMisc, poStat,
						poReopenId, poReopenDate, corrTag, corrPrintTag, poReopenTag, poCancelDate)
					VALUES('".$company_code."', '".$po_number."', '".$supp_code."', '".$po_terms."', '".$po_buyer."', '".$po_date."', '".$po_exp_date."',
						'".$po_all_tag."', '".$po_item_total."', '".$po_qty_total."', '".$po_tot_ext."', '".$po_tot_disc."', '".$po_tot_allow."', '".$po_tot_misc."', '".$po_stat."',
						'".$po_reopen_id."', '".$po_reopen_date."', '".$corr_tag."', '".$corr_print_tag."', '".$po_reopen_tag."', '".$po_cancel_date."')";
			$db->query($query);
		#}
	}
	
	function checkIfStatusIsUpdated($company_code, $po_number, $status){
		global $db;
		$query = "SELECT compCode, poNumber, poStat
				FROM tblPoHeader
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poStat = '$status'";
		$db->query($query);
		$po = mssql_fetch_array($db->result(), MSSQL_ASSOC);
		return $po;
	}
	
	function cancelPO($company_code, $po_number){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poStat = 'X'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poStat IN ('R', 'H')";
		$db->query($query);
		
		$po_update = $this->checkIfStatusIsUpdated($company_code, $po_number, 'X');
		(!empty($po_update)) ? $reply=true : $reply=false;
		return $reply;
	}
	
	function closePO($company_code, $po_number){
		global $db;
		$query = "UPDATE tblPoHeader
				SET poStat = 'C'
				WHERE compCode = '$company_code'
					AND poNumber = '$po_number'
					AND poStat IN ('D', 'P')";
		$db->query($query);
		
		$po_update = $this->checkIfStatusIsUpdated($company_code, $po_number, 'C');
		(!empty($po_update)) ? $reply=true : $reply=false;
		return $reply;
	}
}
?>
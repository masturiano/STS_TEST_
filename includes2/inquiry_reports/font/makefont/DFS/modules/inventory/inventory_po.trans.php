<?
#Description: Inventory Module Transactions (related to Purchasing Module)
#Author: Jhae Torres
#Date Created: July 03, 2008


session_start();
$company_code = $_SESSION['comp_code'];
$userid = $_SESSION['userid'];

require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "inventory_po.obj.php";
require_once "../purchasing/purchasing.obj.php";
require_once "../etc/etc.obj.php";
//--> Code by Louie
include('ra_loadreport.php');


$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);

$db = new DB;
$db->connect();
$inventoryTrans = new inventoryObject;
$purchasingTrans = new purchasingObject;
$etcTrans = new etcObject;
$transaction = $_POST['transaction'];
$ajax_trans = $_GET['ajax_trans'];
#$db->disconnect();

switch($transaction){
	case 'print_ra':
		$po_number = $_POST['po_number'];
		$print_date = $date;
		$print_by = $userid;
		
		$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
		if(!empty($po_header)){
			$po_stat = $po_header['poStat'];
			if($po_stat == 'C'){
				$msg = $etcTrans->getMessage('PO0059');	
			} elseif($po_stat!='R' and $po_stat!='P'){
				$msg = $etcTrans->getMessage('PO0060');	
			} else{
				$po_details = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, '');
				foreach($po_details as $dtl){
					$delete_tag = $dtl['poItemDelTag'];
					$ordered_qty = $dtl['orderedQty'] * $dtl['prdConv'];
					$received_qty = $dtl['rcrQty'];
					
					$ra_number = $_POST['ra_number'];
					$product_code = $dtl['prdNumber'];
					$uom = $dtl['umCode'];
					$conv_factor = $dtl['prdConv'];
					$ra_ordered_qty = $ordered_qty - $received_qty;
					$po_unit_cost = $dtl['poUnitCost'];
					
					if($delete_tag!='D' and $ordered_qty!=$received_qty){
						$ra_print = $inventoryTrans->insertIntoRADetail($company_code, $po_number, $ra_number, $product_code, $uom, $conv_factor, $ra_ordered_qty, $po_unit_cost);
					}
				}
				
				if($ra_print == true){
					#$etcTrans->updateNumber($company_code, 'raNumber', 'tblRaNumber');
					$ra_print_ok = $inventoryTrans->insertIntoRAHeader($company_code, $po_number, $ra_number, $print_date, $print_by);
				
					if($ra_print_ok == true){
						$purchasingTrans->updatePOStatus($company_code, $po_number, 'P');
						$msg = $etcTrans->getMessage('PO0061');
						$msgko="ON";
					}
				} else{
					$purchasingTrans->updatePOStatus($company_code, $po_number, 'C');
					$msg = $etcTrans->getMessage('PO0059');	
				}
			}
		} else{
			$msg = $etcTrans->getMessage('PO0030');	
		}
		$etcTrans->redirectURL('modules/inventory/ra_printing.php?msg='.$msg.'&msgko='.$msgko.'&ra_number='.$ra_number.'&po_number='.$po_number);
		break;
	
	case 'reprint_ra':
		$ra_number = $_POST['reprint_ra_number'];
		$etcTrans->redirectURL('modules/inventory/ra_printing.php?msg='.$msg.'&msgko=YES&ra_number='.$ra_number);
		break;
		
	case 'submit_ra':
		$rcr_type = '1'; #RCR for RA
		$ra_number = $_POST['ra_number'];
		$rcr_number = $_POST['rcr_number'];
		$rcr_date = $_POST['rcr_date'];
		($_POST['location']==0) ? $location=$_POST['rcr_location'] : $location=$_POST['location'];
		$carrier = $_POST['carrier'];
		$container = $_POST['container'];
		$received_by = $_POST['received_by'];
		$remarks = $_POST['remarks'];
		$ra_info = $inventoryTrans->getRAInfo($ra_number);
		$rcr_update = $_POST['rcr_update'];
		
		$rcr_header_exist = $inventoryTrans->checkIfRCRHeaderExist($ra_info['compCode'], $rcr_number, $rcr_type);
		if(empty($rcr_header_exist)){
			$insert_rcr_header_ok = $inventoryTrans->insertIntoRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																$ra_info['compCode'], $ra_info['poNumber'], $ra_info['poDate'], $ra_number, $ra_info['raDate'], $rcr_type, 'O',
																$ra_info['suppCode'], $ra_info['suppTerms'], $location, $ra_info['poBuyer'], $ra_info['currCode'], $ra_info['currUsdRate']);
			if($insert_rcr_header_ok==true){
				$ra = $inventoryTrans->getRADetail($ra_info['compCode'], $ra_info['poNumber'], $ra_number, '');

				foreach($ra as $x){
					$rcr_detail_exist = $inventoryTrans->checkIfRCRDetailExist($x['compCode'], $rcr_number, $x['prdNumber']);
					if(empty($rcr_detail_exist)){
						$index = $x['prdNumber'];
						if((intval($_POST['good'.$index]) + intval($_POST['bad'.$index]) + intval($_POST['free'.$index])) > 0){
							$insert_rcr_detail_ok = $inventoryTrans->insertIntoRCRDetail($x['compCode'], $rcr_number, $x['prdNumber'], $x['umCode'], $x['prdConv'], $ra_number, $x['poUnitCost'],
																			$_POST['ordered_qty'.$index], $_POST['good'.$index], $_POST['bad'.$index], $_POST['free'.$index]);
						}
					} else{
						$msg = $etcTrans->getMessage('PO0071');	
					}
				}

				if($insert_rcr_detail_ok==true){
					### increment RCR Number
					#if($rcr_update!=true) $etcTrans->updateNumber($x['compCode'], 'rcrNumber', 'tblRcrNumber');
					$msg = $etcTrans->getMessage('PO0073');
				} else{
					$msg = $etcTrans->getMessage('PO0072');
				}
			} else{
				$msg = $etcTrans->getMessage('PO0072');	
			}
		} else{
			#$msg = $etcTrans->getMessage('PO0071');
			### UPDATE RCR HEADER AND DETAIL
			$update_rcr_header_ok = $inventoryTrans->updateRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																$ra_info['compCode'], $ra_info['poNumber'], $ra_info['poDate'], $ra_number, $ra_info['raDate'], $rcr_type, 'O',
																$ra_info['suppCode'], $ra_info['suppTerms'], $location, $ra_info['poBuyer'], $ra_info['currCode'], $ra_info['currUsdRate']);
			
			$ra = $inventoryTrans->getRADetail($ra_info['compCode'], $ra_info['poNumber'], $ra_number, '');
			foreach($ra as $x){
				$index = $x['prdNumber'];
				if((intval($_POST['good'.$index]) + intval($_POST['bad'.$index]) + intval($_POST['free'.$index])) > 0){
					$update_rcr_detail_ok = $inventoryTrans->updateRCRDetail($x['compCode'], $rcr_number, $x['prdNumber'], $x['umCode'], $x['prdConv'], $ra_number, $x['poUnitCost'],
																		$_POST['ordered_qty'.$index], $_POST['good'.$index], $_POST['bad'.$index], $_POST['free'.$index]);
				}
			}
			
			if($update_rcr_header_ok==true and $update_rcr_detail_ok==true){
				$msg = $etcTrans->getMessage('PO0075');
			} else{
				$msg = $etcTrans->getMessage('PO0076');
			}
		}
		$etcTrans->redirectURL('modules/inventory/ra.php?msg='.$msg);
		break;
		
	case 'delete_ra':
		$ra_number = $_POST['ra_number'];
		$ra_info = $inventoryTrans->getRAInfo($ra_number);
		$company_code = $ra_info['compCode'];
		$rcr_number = $_POST['rcr_number'];
		$rcr_update = $_POST['rcr_update'];
		
		if($_POST['select_ra']){
			foreach($_POST['select_ra'] as $product_code){
				$delete_rcr_detail_ok = $inventoryTrans->deleteRCRDetail($company_code, $rcr_number, $product_code);
				($delete_rcr_detail_ok==true) ? $msg=$etcTrans->getMessage('PO0080') : $msg=$etcTrans->getMessage('PO0081');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0077');
		}
		$etcTrans->redirectURL('modules/inventory/ra.php?msg='.$msg.'&ra_number='.$ra_number.'&rcr_update='.$rcr_update);
		break;
		
	case 'rcr_add_item':
		### RCR Header/RCR Detail
		$po_number = $_POST['po_number'];
		$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
		$po_date = $po_header['poDate'];
		$supplier_code = $po_header['suppCode'];
		$terms = $po_header['suppTerms'];
		$buyer = $po_header['poBuyer'];
		$currency_code = $po_header['suppCurr'];
		$usd_rate = $po_header['currUsdRate'];
		$rcr_number = $_POST['rcr_number'];
		$rcr_date = $_POST['rcr_date'];
		$carrier = $_POST['carrier'];
		$container = $_POST['container'];
		$received_by = $_POST['received_by'];
		$remarks = $_POST['remarks'];
		($_POST['location']==0) ? $location=$_POST['rcr_location'] : $location=$_POST['location'];
		$rcr_type = '2'; #RCR for Additional PO Items
		$rcr_status = 'O';
		$product_code = $_POST['product'];
		$uom = $_POST['uom'];
		$unit_cost = $_POST['unit_cost'];
		$conversion = $_POST['conv_factor'];
		$ordered_qty = '0';
		$good = '0';
		$bad = '0';
		$free = '0';
		$rcr_update = $_POST['rcr_update'];
		
		$rcr_detail_exist = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, $product_code);
		if(empty($rcr_detail_exist)){
			$insert_rcr_detail_ok = $inventoryTrans->insertIntoRCRDetail($company_code, $rcr_number, $product_code, $uom, $conversion, '0', $unit_cost,
																			$ordered_qty, $good, $bad, $free);
			if($insert_rcr_detail_ok==true){
				$rcr_header_exist = $inventoryTrans->checkIfRCRHeaderExist($company_code, $rcr_number, $rcr_type);
				if(empty($rcr_header_exist)){
					### INSERT RCR HEADER
					$insert_rcr_header_ok = $inventoryTrans->insertIntoRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																		$company_code, $po_number, $po_date, '0', '', $rcr_type, $rcr_status,
																		$supplier_code, $terms, $location, $buyer, $currency_code, $usd_rate);
				} else{
					### UPDATE RCR HEADER
					$update_rcr_header_ok = $inventoryTrans->updateRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																		$company_code, $po_number, $po_date, '0', '', $rcr_type, $rcr_status,
																		$supplier_code, $terms, $location, $buyer, $currency_code, $usd_rate);
				}
				
				if($insert_rcr_header_ok==true or $update_rcr_header_ok==true){
					$msg = $etcTrans->getMessage('PO0073');
				} else{
					$msg = $etcTrans->getMessage('PO0072');
				}
			} else{
				$msg = $etcTrans->getMessage('PO0072');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0087');
		}
		$etcTrans->redirectURL('modules/inventory/add_po_item.php?po_number='.$po_number.'&rcr_number='.$rcr_number.'&msg='.$msg.'&rcr_update='.$rcr_update);
		break;
		
	case 'update_rcr_add_item':
		### RCR Header/RCR Detail
		$po_number = $_POST['po_number'];
		$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
		$po_date = $po_header['poDate'];
		$supplier_code = $po_header['suppCode'];
		$terms = $po_header['suppTerms'];
		$buyer = $po_header['poBuyer'];
		$currency_code = $po_header['suppCurr'];
		$usd_rate = $po_header['currUsdRate'];
		$rcr_number = $_POST['rcr_number'];
		$rcr_date = $_POST['rcr_date'];
		$carrier = $_POST['carrier'];
		$container = $_POST['container'];
		$received_by = $_POST['received_by'];
		$remarks = $_POST['remarks'];
		($_POST['location']==0) ? $location=$_POST['rcr_location'] : $location=$_POST['location'];
		$rcr_type = '2'; #RCR for Additional PO Items
		$rcr_status = 'O';
		$rcr_update = $_POST['rcr_update'];

		### UPDATE RCR HEADER AND DETAIL
		$update_rcr_header_ok = $inventoryTrans->updateRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																	$company_code, $po_number, $po_date, '0', '', $rcr_type, $rcr_status,
																	$supplier_code, $terms, $location, $buyer, $currency_code, $usd_rate);
		$rcr_detail = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, '');
		foreach($rcr_detail as $x){
			$cost_info = $purchasingTrans->getUnitInfo($supplier_code, $x['prdNumber'], $po_date);
			$index = $x['prdNumber'];
			
			if((intval($_POST['good'.$index]) + intval($_POST['bad'.$index]) + intval($_POST['free'.$index])) > 0){
				$update_rcr_detail_ok = $inventoryTrans->updateRCRDetail($company_code, $rcr_number, $x['prdNumber'], $x['umCode'], $cost_info['conv_factor'], '0', $cost_info['unit_cost'],
																	'0', $_POST['good'.$index], $_POST['bad'.$index], $_POST['free'.$index]);
			} else{
				$inventoryTrans->deleteRCRDetail($company_code, $rcr_number, $x['prdNumber']);
			}
		}
			
		if($update_rcr_header_ok==true and $update_rcr_detail_ok==true){
			#if($rcr_update!=true) $etcTrans->updateNumber($company_code, 'rcrNumber', 'tblRcrNumber');
			$msg = $etcTrans->getMessage('PO0075');
		} else{
			$msg = $etcTrans->getMessage('PO0076');
		}
		$etcTrans->redirectURL('modules/inventory/add_po_item.php?msg='.$msg);
		break;
		
	case 'delete_rcr_add_item':
		$po_number = $_POST['po_number'];
		$rcr_number = $_POST['rcr_number'];
		$rcr_update = $_POST['rcr_update'];
		
		if($_POST['select_ra']){
			foreach($_POST['select_ra'] as $product_code){
				$delete_rcr_detail_ok = $inventoryTrans->deleteRCRDetail($COMPANY_CODE, $rcr_number, $product_code);
				($delete_rcr_detail_ok==true) ? $msg=$etcTrans->getMessage('PO0080') : $msg=$etcTrans->getMessage('PO0081');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0077');
		}
		$etcTrans->redirectURL('modules/inventory/add_po_item.php?msg='.$msg.'&po_number='.$po_number.'&rcr_number='.$rcr_number.'&rcr_update='.$rcr_update);
		break;
		
	case 'submit_dsd':
		### RCR Header/RCR Detail
		$rcr_number = $_POST['rcr_number'];
		$rcr_date = $_POST['rcr_date'];
		$carrier = $_POST['carrier'];
		$container = $_POST['container'];
		$received_by = $_POST['received_by'];
		$remarks = $_POST['remarks'];
		$po_number = '0';
		$po_date = '';
		$invoice_number = $_POST['invoice_number'];
		$invoice_date = $_POST['invoice_date'];
		$rcr_type = '3'; #RCR for DSD
		$rcr_status = 'O';
		($_POST['supplier']=='') ? $supplier_code=$_POST['rcr_supplier'] : $supplier_code=$_POST['supplier'];
		$terms = $_POST['terms'];
		($_POST['location']=='') ? $location=$_POST['rcr_location'] : $location=$_POST['location'];
		$buyer = '0';
		$supplier_info = $purchasingTrans->getSupplier($supplier_code);
		$currency_code = $supplier_info['suppCurr'];
		$usd_rate = $_POST['currency'];
		
		$product_code = $_POST['product'];
		$uom = $_POST['uom'];
		$unit_cost = $_POST['unit_cost'];
		$conversion = $_POST['conv_factor'];
		$ordered_qty = '0';
		$good = '0';
		$bad = '0';
		$free = '0';
		$rcr_update = $_POST['rcr_update'];
		
		$rcr_detail_exist = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, $product_code);
		if(empty($rcr_detail_exist)){
			$insert_rcr_detail_ok = $inventoryTrans->insertIntoRCRDetail($company_code, $rcr_number, $product_code, $uom, $conversion, $invoice_number, $unit_cost,
																			$ordered_qty, $good, $bad, $free);
			if($insert_rcr_detail_ok==true){
				$rcr_header_exist = $inventoryTrans->checkIfRCRHeaderExist($company_code, $rcr_number, $rcr_type);
				if(empty($rcr_header_exist)){
					### INSERT RCR HEADER
					$insert_rcr_header_ok = $inventoryTrans->insertIntoRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																		$company_code, $po_number, $po_date, $invoice_number, $invoice_date, $rcr_type, $rcr_status,
																		$supplier_code, $terms, $location, $buyer, $currency_code, $usd_rate);
				} else{
					### UPDATE RCR HEADER
					$update_rcr_header_ok = $inventoryTrans->updateRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																		$company_code, $po_number, $po_date, $invoice_number, $invoice_date, $rcr_type, $rcr_status,
																		$supplier_code, $terms, $location, $buyer, $currency_code, $usd_rate);
				}
				
				if($insert_rcr_header_ok==true or $update_rcr_header_ok==true){
					$msg = $etcTrans->getMessage('PO0073');
				} else{
					$msg = $etcTrans->getMessage('PO0072');
				}
			} else{
				$msg = $etcTrans->getMessage('PO0072');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0087');
		}
		$etcTrans->redirectURL('modules/inventory/dsd.php?rcr_number='.$rcr_number.'&msg='.$msg.'&rcr_update='.$rcr_update);
		break;
	
	case 'update_dsd':
		### RCR Header/RCR Detail
		$rcr_number = $_POST['rcr_number'];
		$rcr_date = $_POST['rcr_date'];
		$carrier = $_POST['carrier'];
		$container = $_POST['container'];
		$received_by = $_POST['received_by'];
		$remarks = $_POST['remarks'];
		$po_number = '0';
		$po_date = '';
		$invoice_number = $_POST['invoice_number'];
		$invoice_date = $_POST['invoice_date'];
		$rcr_type = '3'; #RCR for DSD
		$rcr_status = 'O';
		($_POST['supplier']=='') ? $supplier_code=$_POST['rcr_supplier'] : $supplier_code=$_POST['supplier'];
		$terms = $_POST['terms'];
		($_POST['location']=='') ? $location=$_POST['rcr_location'] : $location=$_POST['location'];
		$buyer = '0';
		$supplier_info = $purchasingTrans->getSupplier($supplier_code);
		$currency_code = $supplier_info['suppCurr'];
		$usd_rate = $_POST['currency'];
		
		$product_code = $_POST['product'];
		$uom = $_POST['uom'];
		$ordered_qty = '0';
		$good = '0';
		$bad = '0';
		$free = '0';
		$rcr_update = $_POST['rcr_update'];

		### UPDATE RCR HEADER AND DETAIL
		$update_rcr_header_ok = $inventoryTrans->updateRCRHeader($rcr_number, $rcr_date, $carrier, $container, $received_by, $remarks,
																	$company_code, $po_number, $po_date, $invoice_number, $invoice_date, $rcr_type, $rcr_status,
																	$supplier_code, $terms, $location, $buyer, $currency_code, $usd_rate);
		$rcr_detail = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, '');
		foreach($rcr_detail as $x){
			$cost_info = $purchasingTrans->getUnitInfo($supplier_code, $x['prdNumber'], $invoice_date);
			$index = $x['prdNumber'];
			
			if((intval($_POST['good'.$index]) + intval($_POST['bad'.$index]) + intval($_POST['free'.$index])) > 0){
				$update_rcr_detail_ok = $inventoryTrans->updateRCRDetail($company_code, $rcr_number, $x['prdNumber'], $x['umCode'], $cost_info['conv_factor'], $invoice_number, $cost_info['unit_cost'],
																	$ordered_qty, $_POST['good'.$index], $_POST['bad'.$index], $_POST['free'.$index]);
			} else{
				$inventoryTrans->deleteRCRDetail($company_code, $rcr_number, $x['prdNumber']);
			}
		}
			
		if($update_rcr_header_ok==true and $update_rcr_detail_ok==true){
			#if($rcr_update!=true) $etcTrans->updateNumber($company_code, 'rcrNumber', 'tblRcrNumber');
			$msg = $etcTrans->getMessage('PO0075');
		} else{
			$msg = $etcTrans->getMessage('PO0076');
		}
		$etcTrans->redirectURL('modules/inventory/dsd.php?msg='.$msg);
		break;
		
	case 'delete_dsd':	
		$rcr_number = $_POST['rcr_number'];
		$rcr_update = $_POST['rcr_update'];
		
		if($_POST['select_ra']){
			foreach($_POST['select_ra'] as $product_code){
				$delete_rcr_detail_ok = $inventoryTrans->deleteRCRDetail($company_code, $rcr_number, $product_code);
				($delete_rcr_detail_ok==true) ? $msg=$etcTrans->getMessage('PO0080') : $msg=$etcTrans->getMessage('PO0081');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0077');
		}
		$etcTrans->redirectURL('modules/inventory/dsd.php?msg='.$msg.'&rcr_number='.$rcr_number.'&rcr_update='.$rcr_update);
		break;
		
	case 'search_rcr':
		$rcr_number = $_POST['rcr_number'];
		if(!is_numeric($rcr_number) or strpos($rcr_number, '.')){
			$msg = $etcTrans->getMessage('PO0088');
			$etcTrans->redirectURL('modules/inventory/rcr.php?msg='.$msg);
		} else{
			$etcTrans->redirectURL('modules/inventory/rcr.php?rcr_number='.$rcr_number);
		}
		break;
		
	case 'delete_rcr':
		if($_POST['select_rcr']){
			foreach($_POST['select_rcr'] as $rcr_number){
				$delete_rcr_ok = $inventoryTrans->deleteRCR($company_code, $rcr_number);
				($delete_rcr_ok==true) ? $msg=$etcTrans->getMessage('PO0078') : $msg=$etcTrans->getMessage('PO0079');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0077');
		}
		$etcTrans->redirectURL('modules/inventory/rcr.php?msg='.$msg);
		break;
	
	case 'redirect_rcr':
		$rcr_number = $_POST['redirect_rcr'];
		$rcr_header = $inventoryTrans->checkIfRCRHeaderExist($company_code, $rcr_number, '');
		$rcr_type = $rcr_header[0]['rcrType'];
		
		if($rcr_type==1){
			$ra_number = $rcr_header[0]['raNumber'];
			$etcTrans->redirectURL('modules/inventory/ra.php?rcr_update=true&ra_number='.$ra_number);
		} elseif($rcr_type==2){
			$po_number = $rcr_header[0]['poNumber'];
			$etcTrans->redirectURL('modules/inventory/add_po_item.php?rcr_update=true&po_number='.$po_number.'&rcr_number='.$rcr_number);
		} elseif($rcr_type==3){
			$etcTrans->redirectURL('modules/inventory/dsd.php?rcr_update=true&rcr_number='.$rcr_number);
		}
		break;
		
	case 'search_rcr_release':
		$rcr_number = $_POST['rcr_number'];
		if(!is_numeric($rcr_number) or strpos($rcr_number, '.')){
			$msg = $etcTrans->getMessage('PO0088');
			$etcTrans->redirectURL('modules/inventory/rcr_release.php?msg='.$msg);
		} else{
			$etcTrans->redirectURL('modules/inventory/rcr_release.php?rcr_number='.$rcr_number);
		}
		break;
		
	case 'release_rcr':
		$release_date = $_POST['release_date'];
		$release_operator = $_POST['release_operator'];
		$period = $_POST['period'];
		
		if($_POST['select_rcr']){
			foreach($_POST['select_rcr'] as $rcr_number){
				### INITIALIZE TOTALS
				$inventoryTrans->initializeRCRTotals($company_code, $rcr_number);
				
				### UPDATE PRODUCT ITEMS
				$rcr_header = $inventoryTrans->checkIfRCRHeaderExist($company_code, $rcr_number, '');
				$usd_rate = $rcr_header[0]['currUsdRate'];
				$po_number = $rcr_header[0]['poNumber'];
				
				$rcr_detail = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, '');
				foreach($rcr_detail as $dtl){
					$qty_with_cost = $dtl['rcrQtyGood'] + $dtl['rcrQtyBad'];
					$ext_amt = $qty_with_cost * (($dtl['poUnitCost']/$dtl['prdConv']) * (1/$usd_rate));
					$inst_amt = $ext_amt;

					# initialize all discount amounts
					$inventoryTrans->initializeRCRDiscountAmounts($company_code, $rcr_number, $dtl['prdNumber']);
					
					### SKU LEVEL DISCOUNTS
					$item_disc = $purchasingTrans->checkIfPOItemDiscountExist($company_code, $po_number, $dtl['prdNumber'], '');
					foreach($item_disc as $disc){
						#NOTE: do this procedure if and only if po item discount percent is not zero(0)
						if($disc['poItemDiscPcnt'] != 0){
							$item_disc_amt = $inst_amt * ($disc['poItemDiscPcnt'] / 100);
							$inventoryTrans->updateDiscountTotal($company_code, $rcr_number, $item_disc_amt);
							$inventoryTrans->updateItemDiscount($company_code, $rcr_number, $dtl['prdNumber'], $disc['poItemDiscTag'], $item_disc_amt, $disc['poItemDiscPcnt']);
							$inst_amt = $inst_amt - $item_disc_amt;
						}
					}
					
					# update rcr header
					$item_qty_total = $dtl['rcrQtyGood'] + $dtl['rcrQtyBad'] + $dtl['rcrQtyFree'];
					$inventoryTrans->updateRCRHeaderTotals($company_code, $rcr_number, $item_qty_total, $ext_amt);
					$inventoryTrans->changeToPartialStat($company_code, $po_number); # set PO status to Partial if not yet complete/closed
				
					### update rcr detail extended amount
					$inventoryTrans->updateRCRDtlExtAmt($company_code, $rcr_number, $dtl['prdNumber'], $ext_amt);
				
					### UPDATE PO DETAIL (rcrQty, rcrExtAmt)
					$rcr = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, $dtl['prdNumber']);	
					$qty_with_cost = $rcr['rcrQtyGood'] + $rcr['rcrQtyBad'];
					$ext_amt = $qty_with_cost * (($rcr['poUnitCost']/$rcr['prdConv']) * (1/$usd_rate));
					$inventoryTrans->updatePODtlAfterRCR($company_code, $po_number, $rcr['prdNumber'], $qty_with_cost, $ext_amt);
					echo "<br><br>";
				}
							
				### DETERMINE INSTANTANEOUS NET AMOUNT
				$net_amt = $inventoryTrans->computeTotNetAmt($company_code, $rcr_number);
				$inst_net = $net_amt;
				$allow_cog_y = 0;
				$allow_cog_n = 0;
				$allow_total = 0;
				$charge_total = 0;

				### COMPUTE ALLOWANCES
				$item_allowance = $purchasingTrans->checkIfPOAllowanceDetailExist($company_code, $po_number, '');
				foreach($item_allowance as $allw){
					#NOTE: do this if and only if po item allowance percent is not zero(0)
					if($allw['poAllwPcnt'] != 0){
						$allow_amt = $inst_net * ($allw['poAllwPcnt'] / 100);
						($allw['poAllwTag']=='Y') ? $allow_cog_y+=$allow_amt : $allow_cog_n+=$allow_amt;
						$allow_total += $allow_amt;
						$inst_net -= $allow_amt;
					}
				}
				if($allow_total=='') $allow_total=0;
								
				### COMPUTE ADDITIONAL CHARGES
				$add_charges = $inventoryTrans->getAddCharges($company_code, $po_number);
				foreach($add_charges as $charge){
					#NOTE: do this if and only if po additional charge percent is not zero(0)
					if($charge['poAddChargePcent'] != 0){
						$charge_amt = $inst_net * ($charge['poAddChargePcent'] / 100);
						$charge_total += $charge_amt;
						$inst_net -= $charge_amt;
					}
				}
				if($charge_total=='') $charge_total=0;
				
				# continuation of rcr header totals update
				$inventoryTrans->updateOtherRCRHeaderTotals($company_code, $rcr_number, $allow_total, $charge_total);
				
				### INITIALIZE ALLOCATED AMOUNTS
				$w_items = 0;
				$w_allow_cog_y = 0;
				$w_allow_cog_n = 0;
				$w_add_charges_total = 0;
				
				### ALLOCATE AMOUNTS
				$rcr_header = $inventoryTrans->checkIfRCRHeaderExist($company_code, $rcr_number, '');
				$item_total = $rcr_header[0]['rcrItemTotal'];
				$ext_total = $rcr_header[0]['rcrExtTotal'];
				$add_charges_total = $rcr_header[0]['rcrAddChargesTotal'];
				$rcr_date = $rcr_header[0]['rcrDate'];
				$supplier = $rcr_header[0]['suppCode'];
				$rcr_type = $rcr_header[0]['rcrType'];
				$po_number = $rcr_header[0]['poNumber'];
				(IS_NULL($rcr_header[0]['poTerms']) or $rcr_header[0]['poTerms']=='') ? $po_terms=0 : $po_terms=$rcr_header[0]['poTerms'];
				
				$rcr_detail = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, '');
				foreach($rcr_detail as $dtl){
					$product_code = $dtl['prdNumber'];
					$ext_amt = $dtl['rcrExtAmt'];
					$w_items++;
					
					if($w_items == $item_total){
						$prod_alloc_y = $allow_cog_y - $w_allow_cog_y;
						$prod_alloc_n = $allow_cog_n - $w_allow_cog_n;
						$prod_alloc_add = $add_charges_total - $w_add_charges_total;
					} else{
						$prod_alloc_y = ($ext_amt / $ext_total) * $allow_cog_y;
						$prod_alloc_n = ($ext_amt / $ext_total) * $allow_cog_n;
						$prod_alloc_add = ($ext_amt / $ext_total) * $add_charges_total;
					}
					
					if($w_items <= $item_total){
						### update item detail record
						$inventoryTrans->allocateRCRAmt($company_code, $rcr_number, $product_code, $prod_alloc_y, $prod_alloc_n, $prod_alloc_add);
						
						### add prod_alloc
						$w_allow_cog_y += $prod_alloc_y;
						$w_allow_cog_n += $prod_alloc_n;
						$w_allow_cog_add += $prod_alloc_add;
					}
				}

				### PROCESS PRODUCTS
				$rcr_detail = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, '');
				foreach($rcr_detail as $dtl){
					### CHECK INVENTORY BALANCE
					$location = $rcr_header[0]['rcrLocation'];
					$product_code = $dtl['prdNumber'];
					$uom = $dtl['umCode'];
					$ra_number = $dtl['raNumber'];
					$period_info = $inventoryTrans->getOpenPeriod($company_code);
					$period_year = $period_info['pdYear'];
					$period_code = $period_info['pdCode'];
					
					$inv_bal_exist = $inventoryTrans->checkIfInvBalMastExist($company_code, $location, $product_code, $period_year, $period_code);
					if(empty($inv_bal_exist)){
						### initialize all quantity, amount and cost fields to zeroes (tblInvBalM)
						$inventoryTrans->initializeInvBalMast($company_code, $location, $product_code, $period_year, $period_code);
					}
					$inv_bal_info = $inventoryTrans->checkIfInvBalMastExist($company_code, $location, $product_code, $period_year, $period_code);
					
					### PRODUCT COST PROCESSING
					$rcr_discounts = $dtl['itemDiscCogY'] + $dtl['poLevelDiscCogY'];
					$recit_cost = $dtl['rcrExtAmt'] + $dtl['rcrAddCharges'] - $rcr_discounts;
					$rcr_qty = $dtl['rcrQtyGood'] + $dtl['rcrQtyBad'] + $dtl['rcrQtyFree'];
					
					### CHECK COST TABLE
					$ave_cost_exist = $inventoryTrans->checkIfAveCostExist($company_code, $product_code);
					if(empty($ave_cost_exist)){
						### initialize tblAveCost
						$inventoryTrans->initializeAveCost($company_code, $product_code);
					}
					$ave_cost_info = $inventoryTrans->checkIfAveCostExist($company_code, $product_code);
					
					### COMPUTE NEW NET COST
					$tot_cur_bal = $inv_bal_info['endBalGoodM'] + $inv_bal_info['endBalBoM'];
					$cur_bal_cost = $tot_cur_bal * $ave_cost_info['aveUnitCost'];
					($tot_cur_bal>=0) ? $new_ave_cost=($cur_bal_cost+$recit_cost)/($rcr_qty+$tot_cur_bal) : $new_ave_cost=$recit_cost/$rcr_qty;
					
					### UPDATE AVERAGE COST
					$inventoryTrans->updateAveCost($company_code, $product_code, $uom, 'RC', $rcr_number, $rcr_date, $date, $new_ave_cost);
					
					### WRITE COST HISTORY
					$inventoryTrans->writeCostHistory($company_code, $product_code, $rcr_date, $rcr_number, $uom, 'RC', $new_ave_cost, $period_year, $period_code, $date);
					
					### UPDATE INVENTORY BALANCE MASTER TABLE
					$inventoryTrans->updateInvBalMast($company_code, $location, $product_code, $period_year, $period_code,
														$rcr_qty, $recit_cost, $dtl['rcrQtyGood'], $dtl['rcrQtyFree'], $dtl['rcrQtyBad'],
														$new_ave_cost, $release_operator, $date);
					
					### ACCUMULATE TRANSACTION
					# from tblLocation
					$location_info = $inventoryTrans->getLocations($company_code, $location);
					$location_type = $location_info['locType'];
					
					# from tblProdMast
					$product_info = $inventoryTrans->getProductInfo($product_code);
					$prod_group = $product_info['prdGrpCode'];
					$prod_dept = $product_info['prdDeptCode'];
					$prod_class = $product_info['prdClsCode'];
					$prod_sub_class = $product_info['prdSubClsCode'];
					$prod_type = $product_info['prdType'];
					$prod_set_tag = $product_info['prdSetTag'];
					
					($rcr_type=='3') ? $ref_no=$ra_number : $ref_no=$po_number;
					$trans_code = '01'.$rcr_type;
					
					# from tblPoItemDtl
					$po_info = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, $product_code);
					$ref_cost_event = $po_info['refCostEvent'];
					(IS_NULL($dtl['rcrExtAmt']) or $dtl['rcrExtAmt']=='') ? $rcr_ext_amt=0 : $rcr_ext_amt=$dtl['rcrExtAmt'];
					
					$write_inv_trans_ok = $inventoryTrans->writeInvTrans($company_code, $location, $product_code, $period_year, $period_code,
																			$date, $release_operator, $supplier, $location_type,
																			$prod_group, $prod_dept, $prod_class, $prod_sub_class, $prod_type, $prod_set_tag,
																			$rcr_number, $rcr_date, $ref_no, $trans_code,
																			$dtl['itemDiscPcents'], $dtl['itemDiscCogY'], $dtl['itemDiscCogN'], $dtl['poLevelDiscCogY'], $dtl['poLevelDiscCogN'],
																			$dtl['rcrAddCharges'], $rcr_ext_amt, $dtl['poUnitCost'],
																			$dtl['rcrQtyGood'], $dtl['rcrQtyBad'], $dtl['rcrQtyFree'],
																			$ref_cost_event, $po_terms, $new_ave_cost);
					
				}

				### set 'R' to rcr status
				if($write_inv_trans_ok == true){
					$inventoryTrans->changeRCRStatus($company_code, $rcr_number, 'R');
					if(IS_NULL($po_number) or $po_number=='') $po_number=0;
					$inventoryTrans->updateRCRAudit($company_code, $rcr_number, $po_number, 'rcrRlseDate', $release_date, 'rcrRlseOptr', $release_operator);
					$msg = $etcTrans->getMessage('PO0093');
				} else{
					$msg = $etcTrans->getMessage('PO0094');
				}
			}
		} else{
			$msg = $etcTrans->getMessage('PO0092');
		}
		$etcTrans->redirectURL('modules/inventory/rcr_release.php?msg='.$msg);
		break;
}

switch($ajax_trans){
	case 'get_ra_info':
		$ra_number = $_GET['ra_number'];
				
		if(!empty($ra_number)){
			echo "<script>
					$('ra_number').value = '{$ra_number}';
					if(!$('ra_number').value.match(/^[0-9]{1,100}$/)){
						alert('".$etcTrans->getMessage('PO0070')."');
						$('ra_number').value = '';
						$('ra_number').focus();
						window.location='ra.php?ra_number=0';
					}
				</script>";
			
			$ra_info = $inventoryTrans->getRAInfo($ra_number);
			$ra_status = $ra_info['raStat'];
			
			if(empty($ra_info)){
				echo "<script>
						alert('".$etcTrans->getMessage('PO0069')."');
						$('ra_number').value = '';
						$('ra_number').focus();
						window.location='ra.php?ra_number=0';
					</script>";
			} else{
				if($ra_status!='R'){
					echo "<script>
							alert('".$etcTrans->getMessage('PO0068')."');
							$('ra_number').value = '';
							$('ra_number').focus();
							window.location='ra.php?ra_number=0';
						</script>";
				} else{
					echo "<script>
							window.location='ra.php?ra_number=".$ra_number."';
						</script>";
				}
			}
		}
		break;
		
	case 'check_received_total':
		$index = $_GET['index'];
		$focus = $_GET['focus'].$index;
		($focus == 'good'.$index) ? $subtrahend='bad'.$index : $subtrahend='good'.$index;

		echo "<script>
				var ordered_qty = $('ordered_qty'+".$index.").value;
				var conv = $('conv'+".$index.").value;
				var good = $('good'+".$index.").value;
				var bad = $('bad'+".$index.").value;
				var received_qty = parseInt(good) + parseInt(bad);
				var subtrahend = $(".$subtrahend.").value;
				
				if(received_qty > ordered_qty){
					var move_free = confirm('Do you want to move the excess quantity to FREE Quantity Received?');
					if(move_free){
						var free_value = received_qty - ordered_qty;
						var new_value = ordered_qty - subtrahend;
						
						$('".$focus."').value = new_value;
						$('free".$index."').value = parseInt($('free".$index."').value) + parseInt(free_value);
						$('free".$index."').focus();
						$('free".$index."').select();
					} else{
						$('".$focus."').value = 0;
						$('".$focus."').focus();
						$('".$focus."').select();
					}
				}
			</script>";
		break;
		
	case 'check_detail_received_total':
		$ra_number = $_GET['ra_number'];
		$ra_info = $inventoryTrans->getRAInfo($ra_number);
		($ra_info['raStat']!='R') ? $po_number='0' : $po_number=$ra_info['poNumber'];
		$ra = $inventoryTrans->getRADetail($ra_info['compCode'], $po_number, $ra_number, '');
		
			echo "<script>
					var total_ordered_qty = 0;
					var total_received_qty = 0;
					var total_free_qty = 0;
				";
		
		foreach($ra as $x){
			$index = $x['prdNumber'];
			
			echo "
					var ordered_qty = $('ordered_qty'+".$index.").value;
					var conv = $('conv'+".$index.").value;
					var good = $('good'+".$index.").value;
					var bad = $('bad'+".$index.").value;
					var free = $('free'+".$index.").value;
					var received_qty = parseInt(good) + parseInt(bad) + parseInt(free);
						
					var total_ordered_qty = parseInt(total_ordered_qty) + parseInt(ordered_qty);
					var total_received_qty = parseInt(total_received_qty) + parseInt(received_qty);
					var total_free_qty = parseInt(total_free_qty) + parseInt(free);
				";
		}
		
			echo "
					var proceed = validateRA();
										
					if(proceed==true){
						if(total_ordered_qty > (total_received_qty - total_free_qty)){
							document.getElementById('ra_button').style.display = 'none';
							document.getElementById('confirm_div').style.display = 'block';
						} else{
							document.ra_entry_form.submit();
						}
					}
				</script>";
		break;
		
	case 'confirm_close_po':
		$ra_number = $_GET['ra_number'];
		$ra_info = $inventoryTrans->getRAInfo($ra_number);
		($ra_info['raStat']!='R') ? $po_number='0' : $po_number=$ra_info['poNumber'];
		$ra = $inventoryTrans->getRADetail($ra_info['compCode'], $po_number, $ra_number, '');
		
		echo "<script>
				if($('confirm_reply').value=='Y' || $('confirm_reply').value=='y'){
					$('transaction').value = 'submit_ra';
					window.location=\"ra.php?close_po=yes&company_code=".$ra_info['compCode']."&po_number=".$po_number."\";
					document.ra_entry_form.submit();
				} else if($('confirm_reply').value=='N' || $('confirm_reply').value=='n'){
					$('transaction').value = 'submit_ra';
					window.location=\"ra.php?close_po=no&company_code=".$ra_info['compCode']."&po_number=".$po_number."\";
					document.ra_entry_form.submit();
				} else{
					alert('Invalid Input!');
					document.getElementById('confirm_reply').focus();
					document.getElementById('confirm_reply').select();
				}
			</script>";
		break;
		
	case 'confirm_close_rcr':
		$po_number = $_GET['po_number'];
		
		echo "<script>
				if($('transaction').value=='close_po'){
					$('transaction').value = 'update_rcr_add_item';
					window.location=\"add_po_item.php?close_po=yes&company_code=".$company_code."&po_number=".$po_number."\";
					document.add_po_item_form.submit();
				} else{
					document.getElementById('ra_button').style.display = 'block';
					document.getElementById('confirm_div').style.display = 'none';
				}
			</script>";
		break;
		
	case 'get_po_info':
		$po_number = $_GET['po_number'];
				
		if(!empty($po_number)){
			echo "<script>
					$('po_number').value = '{$po_number}';
					if(!$('po_number').value.match(/^[0-9]{1,100}$/)){
						alert('".$etcTrans->getMessage('PO0085')."');
						$('po_number').value = '';
						$('po_number').focus();
						window.location='add_po_item.php?po_number=0';
						
						/*
						$('po_date').value = '';
						$('po_status').value = '';
						$('buyer').value = '';
						$('currency').value = '';
						$('vendor').value = '';
						$('terms').value = '';
						*/
					}
				</script>";
			
			$po_info = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
			$po_status = $po_info['poStat'];
								
			if(empty($po_info)){
				echo "<script>
						alert('".$etcTrans->getMessage('PO0030')."');
						$('po_number').value = '';
						$('po_number').focus();
						window.location='add_po_item.php?po_number=0';
					</script>";
			} else{
				if($po_status!='R' and $po_status!='P'){
					if($po_status=='C'){
						echo "<script>
								alert('".$etcTrans->getMessage('PO0082')."');
								$('po_number').value = '';
								$('po_number').focus();
								window.location='add_po_item.php?po_number=0';
							</script>";
					} else{
						echo "<script>
								alert('".$etcTrans->getMessage('PO0084')."');
								$('po_number').value = '';
								$('po_number').focus();
								window.location='add_po_item.php?po_number=0';
							</script>";
					}
				} else{
					echo "<script>
							window.location='add_po_item.php?po_number=".$po_number."';
						</script>";
				}
			}
		}
		break;
		
	case 'get_po_detail_info':
		$company_code = $_GET['company_code'];
		$po_number = $_GET['po_number'];
		$product_code = $_GET['product_code'];
		
		$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number);
		$detail = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, $product_code);
		
		$cost = $purchasingTrans->getUnitInfo($po_header['suppCode'], $product_code, $po_header['poDate']);
		if(!empty($cost)){
			$unit_cost = $cost['unit_cost'];
			$cost_event = $cost['cost_event'];
			$uom = $cost['uom'];
			$conv_factor = $cost['conv_factor'];
		} else{
			$unit_cost = '0';
			$cost_event = '0';
			$uom = '';
			$conv_factor = '0';
		}
		
		echo "<script>
				$('product_description').value = '".$detail['prdDesc']."';
				$('uom').value = '".$uom."';
				$('unit_cost').value = '".$unit_cost."';
				$('cost_event').value = '".$cost_event."';
				$('conv_factor').value = '".$conv_factor."';
			</script>";
		break;
		
	case 'check_rcr_received_total':
		$company_code = $_GET['company_code'];
		$rcr_number = $_GET['rcr_number'];
		$rcr = $inventoryTrans->checkIfRCRDetailExist($company_code, $rcr_number, '');

			echo "<script>
					var total_ordered_qty = 0;
					var total_received_qty = 0;
					var total_free_qty = 0;
				";
		
		foreach($rcr as $x){ 
			$index = $x['prdNumber'];
			
			echo "
					var ordered_qty = $('ordered_qty'+".$index.").value;
					var conv = $('conv'+".$index.").value;
					var good = $('good'+".$index.").value;
					var bad = $('bad'+".$index.").value;
					var free = $('free'+".$index.").value;
					var received_qty = parseInt(good) + parseInt(bad) + parseInt(free);

					var total_ordered_qty = parseInt(total_ordered_qty) + (parseInt(ordered_qty) * parseInt(conv));
					var total_received_qty = parseInt(total_received_qty) + parseInt(received_qty);
					var total_free_qty = parseInt(total_free_qty) + parseInt(free);
				";
		}
		
			echo "
					var proceed = validateAddPOItem(true);
 				
					if(proceed==true){
						if(total_ordered_qty > (total_received_qty - total_free_qty)){
							document.getElementById('ra_button').style.display = 'none';
							document.getElementById('confirm_div').style.display = 'block';
						} else{
							document.add_po_item_form.submit();
						}
					}
				</script>";
		break;
		
	case 'get_supplier_info':
		$supplier_info =  $purchasingTrans->getSupplier($_GET['supplier_code']);
		if(!empty($supplier_info)) $currency_info = $purchasingTrans->getCurrency($supplier_info['suppCurr']);;
		
		if(!empty($supplier_info)){
			$supplier_desc = addslashes($supplier_info['suppName']);
			$supplier_term = $supplier_info['suppTerms'];
			$currency = $currency_info['currUsdRate'];;
		} else{
			$supplier_desc = '';
			$supplier_term = '';
			$currency = '';
		}
		
		echo "<script>
				$('currency').value = '".$currency."';
				$('".$_GET['output']."').value = '".$supplier_desc."';
				$('terms').value = '".$supplier_term."';
			</script>";
		break;
		
	case 'get_unit_cost':
		$unit_info = $purchasingTrans->getUnitInfo($_GET['supplier_code'], $_GET['product_code'], $_GET['invoice_date']);
		if(!empty($unit_info)){
			$unit_cost = $unit_info['unit_cost'];
			$cost_event = $unit_info['cost_event'];
			$uom = $unit_info['uom'];
			$conv_factor = $unit_info['conv_factor'];
		} else{
			$unit_cost = '0';
			$cost_event = '0';
			$uom = '';
			$conv_factor = '0';
		}
		echo "<script>
				$('".$_GET['output']."').value = '{$unit_cost}';
				$('cost_event').value = '{$cost_event}';
				$('uom').value = '{$uom}';
				$('conv_factor').value = '{$conv_factor}';
			</script>";
		break;
}
?>
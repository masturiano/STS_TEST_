<?
#Description: Purchasing Module Transactions
#Author: Jhae Torres
#Date Created: April 02, 2008


session_start();
require_once "index.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "purchasing.obj.php";
require_once "../etc/etc.obj.php";

$db = new DB;
$db->connect();
$purchasingTrans = new purchasingObject;
$etcTrans = new etcObject;
$transaction = $_POST['transaction'];
$ajax_trans = $_GET['ajax_trans'];
#$db->disconnect();

switch($transaction){
	case 'delete':
		$page = $_POST['page'];
		### PO ENTRY/EDIT PAGE 1		
		if($page=='po_entry1' or $page=='po_edit1'){
			$company_code = $_SESSION['comp_code'];
			$po_number = $_POST['po_number'];
			
			### Delete PO Detail
			if($_POST['select_detail']){
				foreach($_POST['select_detail'] as $product_code){
					$delete_po_detail_ok = $purchasingTrans->deletePODetail($company_code, $po_number, $product_code);
					($delete_po_detail_ok==true) ? $msg=$etcTrans->getMessage('PO0004') : $msg=$etcTrans->getMessage('PO0008');
				}
			} else{
				$msg = $etcTrans->getMessage('PO0005');
			}
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		}#END OF PO ENTRY/EDIT PAGE 1
		
		### PO ENTRY/EDIT PAGE 2
		elseif($page=='po_entry2' or $page=='po_edit2'){
			$company_code = $_SESSION['comp_code'];
			$po_number = $_POST['po_number'];
			$allowance = $_POST['create_po_allowance'];
			$misc_charges = $_POST['create_misc_charges'];
			
			### Delete Allowance
			if($_POST['select_allowance']){
				foreach($_POST['select_allowance'] as $allowance_code){
					$delete_allowance_detail_ok = $purchasingTrans->deletePOAllowanceDetail($company_code, $po_number, $allowance_code);
				}
			}
			
			### Delete Miscellaneous Charges
			if($_POST['select_misc']){
				foreach($_POST['select_misc'] as $misc_sequence){
					$delete_misc_detail_ok = $purchasingTrans->deletePOMiscDetail($company_code, $po_number, $misc_sequence);
				}
			}
			
			if(!$_POST['select_allowance'] and !$_POST['select_misc']){
				### No selected Allowances nor Miscellaneous Charges
				$msg=$etcTrans->getMessage('PO0042');
			} else{
				($delete_allowance_detail_ok==true or $delete_misc_detail_ok==true) ? $msg=$etcTrans->getMessage('PO0040') : $msg=$etcTrans->getMessage('PO0041');
			}
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&allowance='.$allowance.'&misc_charges='.$misc_charges.'&po_number='.$po_number);
		}#END OF PO ENTRY/EDIT PAGE 2
		
		### PO SEARCH PAGE
		elseif($page=='po_search'){
			$company_code = $_SESSION['comp_code'];
			
			if($_POST['delete_po']){
				foreach($_POST['delete_po'] as $po_number){
					$delete_po_header_ok = $purchasingTrans->deletePO('tblPoHeader', 'checkIfPOHeaderExist', $company_code, $po_number);
					$delete_po_details_ok = $purchasingTrans->deletePO('tblPoItemDtl', 'checkIfPODetailExist', $company_code, $po_number);
					$delete_allowance_ok = $purchasingTrans->deletePO('tblPoAllwDtl', 'checkIfPOAllowanceDetailExist', $company_code, $po_number);
					$delete_misc_ok = $purchasingTrans->deletePO('tblPoMiscDtl', 'checkIfPOMiscExist', $company_code, $po_number);
					$delete_remarks_ok = $purchasingTrans->deletePO('tblPoRemarks', 'checkIfPORemarkExist', $company_code, $po_number);
					
					($delete_po_header_ok==true and $delete_po_details_ok==true and $delete_allowance_ok==true and $delete_misc_ok==true and $delete_remarks_ok==true) ?
						$msg=$etcTrans->getMessage('PO0033') : $msg=$etcTrans->getMessage('PO0034');
				}
			} else{
				$msg = $etcTrans->getMessage('PO0035');
			}
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page);
		}#END OF PO SEARCH PAGE
		break;
		
	case 'save':
		$page = $_POST['page'];
		$transaction_override = $_POST['transaction_override'];
		#PO Header
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		$supplier_code = $_POST['supplier'];
		$terms = $_POST['terms'];
		$document_date = $_POST['document_date'];
		$expected_delivery = $_POST['expected_delivery'];
		$cancel_date = $_POST['cancel_date'];
		$buyer = $_POST['buyer'];
		$status = $_POST['status'];
		#Control Totals
		$hash_items = $_POST['hash_items'];
		$hash_quantity = $_POST['hash_quantity'];
		
		$edit_po_header_ok = $purchasingTrans->editPOHeader($company_code, $po_number, $document_date, $expected_delivery, $cancel_date, $buyer, $status, $hash_items, $hash_quantity);
		#($edit_po_header_ok==true) ? $msg=$etcTrans->getMessage('PO0036') : $msg=$etcTrans->getMessage('PO0037');
		
		if($page=='po_entry1' or $page=='po_edit1'){
			if($_POST['select_detail']){
				foreach($_POST['select_detail'] as $product_code){
					$po_info = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, $product_code);
					$po_fraction = $po_info['prdFrcTag'];
					$quantity = $_POST['ordered_qty'.$product_code];
					$proceed_po = true;
					
					### VALIDATIONS
					if($proceed_po == true){
						if(!is_numeric($quantity)){
							$msg = $etcTrans->getMessage('PO0046');
							$proceed_po = false;
						}
					}
					
					if($proceed_po == true){
						if(strpos($quantity, '.') and $po_fraction=='N'){
							$msg = $etcTrans->getMessage('PO0047');
							$proceed_po = false;
						}
					}
					
					if($proceed_po == true){
						$edit_po_detail_ok = $purchasingTrans->editPODetail($company_code, $po_number, $product_code, $quantity);
						#($edit_po_detail_ok==true) ? $msg=$etcTrans->getMessage('PO0006') : $msg=$etcTrans->getMessage('PO0007');
						$msg = $etcTrans->getMessage('PO0043');
					}
					$transaction_override = 'return_po_entry';
				}
			}
		} elseif($page=='po_entry2' or $page=='po_edit2'){
			if($_POST['select_allowance']){
				foreach($_POST['select_allowance'] as $code){
					$allowance_type = $_POST['allowance_type'.$code];
					$allowance_percent = $_POST['allowance_percent'.$code];
					$allowance_amount = $_POST['allowance_amount'.$code];
					$proceed_allw = true;
					
					### VALIDATIONS
					if($proceed_allw == true){
						if(!is_numeric($allowance_percent) or !is_numeric($allowance_amount)){
							$msg = $etcTrans->getMessage('PO0048');
							$proceed_allw = false;
							$transaction_override = 'return_po_entry';
						}
					}
					
					if($proceed_allw == true){
						if($allowance_percent==0 and $allowance_amount==0){
							$msg = $etcTrans->getMessage('PO0049');
							$proceed_allw = false;
							$transaction_override = 'return_po_entry';
						}
					}
					
					if($proceed_allw == true){
						if($allowance_percent!=0 and $allowance_amount!=0){
							$msg = $etcTrans->getMessage('PO0050');
							$proceed_allw = false;
							$transaction_override = 'return_po_entry';
						}
					}
					
					if($proceed_allw == true){
						if($allowance_percent>100 and $allowance_amount==0){
							$msg = $etcTrans->getMessage('PO0051');
							$proceed_allw = false;
							$transaction_override = 'return_po_entry';
						}
					}
					
					if($proceed_allw == true){				
						$edit_po_allowance_ok = $purchasingTrans->editPOAllowanceDetail($company_code, $po_number, $allowance_type, $allowance_percent, $allowance_amount);
						$transaction_override = 'end_po_entry';
					}
				}
			}
		
			if($_POST['select_misc']){
				foreach($_POST['select_misc'] as $sequence){
					$misc_desc = $_POST['misc_desc'.$sequence];
					$misc_amt = $_POST['misc_amt'.$sequence];
					$proceed_misc = true;
					
					if($proceed_misc == true){
						if($misc_desc == ''){
							$msg = $etcTrans->getMessage('PO0052');
							$proceed_misc = false;
							$transaction_override = 'return_po_entry';
						}
					}
					
					if($proceed_misc == true){
						if(strpos($misc_desc, "\'") or strpos($misc_desc, '"')){
							$msg = $etcTrans->getMessage('PO0053');
							$proceed_misc = false;
							$transaction_override = 'return_po_entry';
						}
					}
					
					if($proceed_misc == true){
						if(!is_numeric($misc_amt)){
							$msg = $etcTrans->getMessage('PO0054');
							$proceed_misc = false;
							$transaction_override = 'return_po_entry';
						}
					}

					if($proceed_misc == true){
						$edit_po_misc_ok = $purchasingTrans->editPOMisc($company_code, $po_number, $sequence, $misc_desc, $misc_amt);
						#($edit_po_misc_ok==true) ? $msg=$etcTrans->getMessage('PO0023') : $msg=$etcTrans->getMessage('PO0024');
						$transaction_override = 'end_po_entry';
					}
				}				
			}
			
			$remark = $_POST['remarks'];
			if($remark != ''){
				$po_remark_exist = $purchasingTrans->checkIfPORemarkExist($company_code, $po_number, '');
				if(empty($po_remark_exist)){
					$purchasingTrans->addPORemark($company_code, $po_number, $remark);
				} else{
					$purchasingTrans->editPORemark($company_code, $po_number, $remark);
				}
			}
		}

		#Page Redirection
		if($transaction_override=='return_po_entry'){
			$allowance = $_POST['create_po_allowance'];
			$misc_charges = $_POST['create_misc_charges'];
	
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number.'&allowance='.$allowance.'&misc_charges='.$misc_charges);
		} elseif($transaction_override=='end_po_entry'){
			$page = 'po_entry1';
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page);
		} elseif($transaction_override=='go_to_po_search'){
			$page = 'po_search';
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page);
		} elseif($transaction_override=='proceed_to_page2'){
			$allowance = $_POST['create_po_allowance'];
			$misc_charges = $_POST['create_misc_charges'];
			
			if($page=='po_entry1'){
				$page = 'po_entry2';
			} elseif($page=='po_edit1'){
				$page = 'po_edit2';
			}
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number.'&allowance='.$allowance.'&misc_charges='.$misc_charges);
		}
		break;

	case 'new_po':
		//$msg = $etcTrans->getMessage('PO0029');
		$page = $_POST['page'];
		$transaction_override = $_POST['transaction_override'];
		
		if(!empty($transaction_override)){
			$page = $transaction_override;
		} else{
			$page = 'po_entry1';
		}
		
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page);
		break;
		
	case 'search_po_page':
		//$msg = $etcTrans->getMessage('PO0031');
		$po_number = '';
		$page = 'po_search';
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		break;
		
	case 'search_po':
		$po_number = $_POST['po_number'];
		$page = 'po_search';
		if(!is_numeric($po_number) or strpos($po_number, '.')){
			$msg = $etcTrans->getMessage('PO0055');
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page);
		} else{
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		}
		break;
		
	case 'delete_this_po':
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		$page = $_POST['page'];
		$transaction_override = $_POST['transaction_override'];
		
		if(!empty($transaction_override)){
			$page = $transaction_override;
		} else{
			if($page == 'po_entry2'){
				$page = 'po_entry1';
			} else{
				$page = $page;
			}
		}
		
		$delete_po_header_ok = $purchasingTrans->deletePO('tblPoHeader', 'checkIfPOHeaderExist', $company_code, $po_number);
		$delete_po_details_ok = $purchasingTrans->deletePO('tblPoItemDtl', 'checkIfPODetailExist', $company_code, $po_number);
		$delete_allowance_ok = $purchasingTrans->deletePO('tblPoAllwDtl', 'checkIfPOAllowanceDetailExist', $company_code, $po_number);
		$delete_misc_ok = $purchasingTrans->deletePO('tblPoMiscDtl', 'checkIfPOMiscExist', $company_code, $po_number);
		$delete_remarks_ok = $purchasingTrans->deletePO('tblPoRemarks', 'checkIfPORemarkExist', $company_code, $po_number);
		
		($delete_po_header_ok==true and $delete_po_details_ok==true and $delete_allowance_ok==true and $delete_misc_ok==true and $delete_remarks_ok==true) ?
		$msg=$etcTrans->getMessage('PO0033') : $msg=$etcTrans->getMessage('PO0034');
		echo $etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page);
		break;
		
	case 'view_po':
		$po_number = $_POST['view_po'];
		$page = 'view_po';
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		break;
		
	case 'edit_po':
		$po_number = $_POST['edit_po'];
		$page = 'po_edit1';
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		break;
		
	case 'add_po_detail':
		#PO Header
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		$page = $_POST['page'];
		$supplier_code = $_POST['supplier'];
		$terms = $_POST['terms'];
		$document_date = $_POST['document_date'];
		$expected_delivery = $_POST['expected_delivery'];
		$cancel_date = $_POST['cancel_date'];
		$buyer = $_POST['buyer'];
		$status = $_POST['status'];
		$hash_items = $_POST['hash_items'];
		$hash_quantity = $_POST['hash_quantity'];
		#PO Detail
		$product_code = $_POST['product'];
		$quantity = $_POST['quantity'];
		$product_description = $_POST['product_description'];
		$uom = $_POST['uom'];
		$unit_cost = $_POST['unit_cost'];
		$cost_event = $_POST['cost_event'];
		$conv_factor = $_POST['conv_factor'];
		
		$po_detail_exist = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, $product_code);
		if(empty($po_detail_exist)){
			$allowance_info = $purchasingTrans->getAllowanceInfo($supplier_code, $product_code, $document_date);
			$sequence = 1;
			foreach($allowance_info as $x){
				$allowance_type = $x['allwTypeCode'];
				$discount_percent = $x['allwPcent'];
				$discount_amount = $x['allwAmt'];
				$discount_tag = $x['allwCostTag'];
				
				$po_item_discount = $purchasingTrans->checkIfPOItemDiscountExist($company_code, $po_number, $product_code, $allowance_type);
				if(empty($po_item_discount)){
					$add_po_item_discount_ok = $purchasingTrans->addPOItemDiscount($company_code, $po_number, $product_code, $allowance_type, $sequence, $discount_percent, $discount_amount, $discount_tag);
				}
				$sequence++;
			}

			$add_po_detail_ok = $purchasingTrans->addPODetail($company_code, $po_number, $product_code, $uom, $quantity, $unit_cost, $cost_event, $conv_factor);
			if($add_po_detail_ok == true){
				$po_header_exist = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
				if(empty($po_header_exist)){
					$add_po_header_ok = $purchasingTrans->addPOHeader($company_code, $po_number, $supplier_code, $terms, $document_date, $expected_delivery, $buyer, $status, $hash_items, $hash_quantity, $cancel_date);
					if($add_po_header_ok == false){
						$msg = $etcTrans->getMessage('PO0001');
					} else{
						#$upload_po_number_ok=$etcTrans->updateNumber($company_code, 'poNumber', 'tblPoNumber');
						($upload_po_number_ok==true) ? $msg=$etcTrans->getMessage('PO0026') : $msg=$etcTrans->getMessage('PO0027');
					}
				} else{
					$edit_po_header_ok = $purchasingTrans->editPOHeader($company_code, $po_number, $document_date, $expected_delivery, $cancel_date, $buyer, $status, $hash_items, $hash_quantity);
					($edit_po_header_ok==true) ? $msg=$etcTrans->getMessage('PO0036') : $msg=$etcTrans->getMessage('PO0037');
				}			
				$msg = $etcTrans->getMessage('PO0002');
			} else{
				$msg = $etcTrans->getMessage('PO0001');
			}
		} else{
			if($po_detail_exist['orderedQty'] == 0){
				$purchasingTrans->editPODetail($company_code, $po_number, $product_code, $quantity);
			} else{
				$msg = $etcTrans->getMessage('PO0003');
			}
		}
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		break;
		
	case 'add_po_allowance';
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		$page = $_POST['page'];
		$allowance_type = $_POST['allowance_types'];
		$allowance_percent = $_POST['allowance_percent'];
		$allowance_amount = $_POST['allowance_amount'];
		$allowance_tag = $_POST['cog'];
		$allowance = $_POST['create_po_allowance'];
		$misc_charges = $_POST['create_misc_charges'];
		
		$po_allowance_exist = $purchasingTrans->checkIfPOAllowanceDetailExist($company_code, $po_number, $allowance_type);
		if(empty($po_allowance_exist)){
			$add_allowance_ok = $purchasingTrans->addPOAllowanceDetail($company_code, $po_number, $allowance_type, $allowance_percent, $allowance_amount, $allowance_tag);
			if($add_allowance_ok==true) $msg=$etcTrans->getMessage('PO0044');
		} else{
			$msg = $etcTrans->getMessage('PO0045');
		}
		
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number.'&allowance='.$allowance.'&misc_charges='.$misc_charges);
		break;
		
	case 'add_po_misc':
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		$page = $_POST['page'];
		$sequence = $_POST['sequence'];
		$misc_desc = $_POST['misc_desc'];
		$misc_amount = $_POST['misc_amount'];
		$allowance = $_POST['create_po_allowance'];
		$misc_charges = $_POST['create_misc_charges'];
				
		$po_misc_exist = $purchasingTrans->checkIfPOMiscExist($company_code, $po_number, $sequence);
		if(empty($po_misc_exist)){
			$add_misc_ok = $purchasingTrans->addPOMisc($company_code, $po_number, $sequence, $misc_desc, $misc_amount);
			if($add_misc_ok==true) $msg=$etcTrans->getMessage('PO0014');
		} else{
			$msg = $etcTrans->getMessage('PO0015');
		}
		
		$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number.'&allowance='.$allowance.'&misc_charges='.$misc_charges);
		break;
		
	case 'search_reopen_po':
		$po_number = $_POST['po_number'];
		if(!is_numeric($po_number) or strpos($po_number, '.')){
			$msg = $etcTrans->getMessage('PO0055');
			$etcTrans->redirectURL('modules/purchasing/reopen_po.php?msg='.$msg);
		} else{
			$etcTrans->redirectURL('modules/purchasing/reopen_po.php?msg='.$msg.'&po_number='.$po_number);
		}
		break;
		
	case 'reopen_po':
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['reopen_po'];
		$reopen_tag = 'Y';
		$reopen_date = $_POST['reopen_date'];
		$po_stat = 'O';
		$reopen_operator = $_POST['reopen_operator'];
		$reopen_id = $_SESSION['userid'];
				
		$reopen_po_ok = $purchasingTrans->reopenPO($company_code, $po_number, $reopen_tag, $reopen_date, $po_stat, $reopen_id);
		if($reopen_po_ok == true){
			$backup = $purchasingTrans->checkIfPoHeaderBackupExist($company_code, $po_number);
			if(empty($backup)){			
				$po_header = $purchasingTrans->checkIfPoHeaderExist($company_code, $po_number, '');
				$purchasingTrans->backupPOHeader($po_header['compCode'], $po_header['poNumber'],
					$po_header['suppCode'], $po_header['poTerms'], $po_header['poBuyer'], $po_header['poDate'], $po_header['poExpDate'],
					$po_header['poAllTag'], $po_header['poTotAllow'], $po_header['poTotMisc'],
					$po_header['poReopenId'], $po_header['poReopenDate'], 'Y', $po_header['corrPrintTag'], $po_header['poReopenTag'],
					$po_header['poStat'], $po_header['poItemTotal'], $po_header['poQtyTotal'], $po_header['poCancelDate'],
					$po_header['poTotExt'], $po_header['poTotDisc']);
				
				$po_details = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, '');
				foreach($po_details as $dtl){
					$purchasingTrans->backupPODtl($dtl['compCode'], $dtl['poNumber'], $dtl['prdNumber'],
						$dtl['umCode'], $dtl['prdConv'], $dtl['orderedQty'], $dtl['poUnitCost'], $dtl['poExtAmt'], $dtl['refCostEvent'],
						$dtl['itemDiscPcents'], $dtl['itemDiscCogY'], $dtl['itemDiscCogN'], $dtl['poLevelDiscCogY'], $dtl['poLevelDiscCogN'],
						$dtl['rcrQty'], $dtl['rcrExtAmt'], $dtl['poItemDelTag']);
				}
			}
			
			$page = 'po_edit1';
			$etcTrans->redirectURL('modules/purchasing/po_entry.php?msg='.$msg.'&page='.$page.'&po_number='.$po_number);
		} else{
			$msg = $etcTrans->getMessage('PO0095');
			$etcTrans->redirectURL('modules/purchasing/reopen_po.php?msg='.$msg);
		}
		break;
		
	case 'search_po_release':
		$po_number = $_POST['po_number'];
		if(!is_numeric($po_number) or strpos($po_number, '.')){
			if($po_number!='') $msg=$etcTrans->getMessage('PO0055');
			$etcTrans->redirectURL('modules/purchasing/po_release.php?msg='.$msg);
		} else{
			$etcTrans->redirectURL('modules/purchasing/po_release.php?msg='.$msg.'&po_number='.$po_number);
		}
		break;
		
	case 'release_po':
		$company_code = $_SESSION['comp_code'];
		$release_date = $_POST['release_date'];
		$release_operator = $_POST['release_operator'];
				
		if($_POST['select_po']){
			foreach($_POST['select_po'] as $po_number){
				#INITIALIZE TOTALS (tblPoHeader)
				$purchasingTrans->initializeTotals($company_code, $po_number);
				
				#UPDATE PRODUCT ITEMS / SKU LEVEL DISCOUNTS / UPDATE ALLOWANCES
				$po_details = $purchasingTrans->getPODetailsList($company_code, $po_number);
				### set item Discount Percentages
				foreach($po_details as $dtl){
					$product_code = $dtl['prdNumber'];
					$discount_percentages = $purchasingTrans->checkIfPOItemDiscountExist($company_code, $po_number, $product_code, '');
					foreach($discount_percentages as $disc){
						($disc['poItemDiscPcnt']!=0) ? $discount=$disc['poItemDiscPcnt'] : $discount=$disc['poItemDiscAmt'];
						$purchasingTrans->updateItemDiscountPercentages($company_code, $po_number, $product_code, $discount);
					}
				}

				### update PO Details table (tblPoItemDtl)
				foreach($po_details as $dtl){
					$product_code = $dtl['prdNumber'];
					$ordered_quantity = $dtl['orderedQty'];
					
					#poExtAmt
					$extended_amount = $ordered_quantity * $dtl['poUnitCost'];
					$instantaneous_amount = $extended_amount;
					$purchasingTrans->updateProductExtendedAmount($company_code, $po_number, $product_code, $extended_amount);

					###update PO Header table (tblPoHeader)
					$update_header_ok = $purchasingTrans->updatePOHeader($company_code, $po_number, $ordered_quantity, $extended_amount);

					$discount_percentages = $purchasingTrans->checkIfPOItemDiscountExist($company_code, $po_number, $product_code, '');
					foreach($discount_percentages as $disc){
						$discount_sequence = $disc['poDiscSeq'];
						$allowance_type_code = $disc['allwTypeCode'];
						$discount_amount = $disc['poItemDiscAmt'];
						$discount_percent = $disc['poItemDiscPcnt'];
						$discount_tag = $disc['poItemDiscTag'];
						
						if($discount_percent!=0) $discount_amount = $instantaneous_amount * $discount_percent / 100;
						if($discount_amount == 0) $purchasingTrans->updatePOItemDiscountAmount($company_code, $po_number, $product_code, $discount_sequence, $allowance_type_code, $discount_amount);
						$instantaneous_amount = $instantaneous_amount - $discount_amount;
						
						#itemDiscCogY / itemDiscCogN
						($discount_tag=='Y') ? $tag='itemDiscCogY' : $tag = 'itemDiscCogN';
						$purchasingTrans->updateItemDiscountCOG($company_code, $po_number, $product_code, $tag, $discount_amount);
						$update_tot_sku_ok = $purchasingTrans->updateTotalSKUDiscount($company_code, $po_number, $discount_amount);
					}
				}
				
				$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
				$inst_total_net_amt = $po_header['poTotExt'] - $po_header['poTotDisc'];

				### update allowances
				### 09/17/08 ALLOWANCE AMOUNT is no longer needed for this program
				$allowance_detail = $purchasingTrans->checkIfPOAllowanceDetailExist($company_code, $po_number, '');
				foreach($allowance_detail as $allw){
					$allowance_amount = $allw['poAllwAmt'];
					$allowance_percent = $allw['poAllwPcnt'];
					$allowance_type = $allw['allwTypeCode'];
					$allowance_tag = $allw['poAllwTag'];
					
					if($allowance_percent!=0) $allowance_amount = $inst_total_net_amt * $allowance_percent / 100;
					if($allowance_amount!=0) $purchasingTrans->editPOAllowanceDetail($company_code, $po_number, $allowance_type, $allowance_percent, $allowance_amount);
					$inst_total_net_amt = $inst_total_net_amt - $allowance_amount;
					
					#poLevelDiscCogY / poLevelDiscCogN
					($allowance_tag=='Y') ? $allw_tag='poLevelDiscCogY' : $allw_tag='poLevelDiscCogN';
					$purchasingTrans->updateItemDiscountCOG($company_code, $po_number, $product_code, $allw_tag, $allowance_amount);
					$update_tot_allowance_ok = $purchasingTrans->updateTotalAllowanceAmount($company_code, $po_number, $allowance_amount);			
				}
				
				### update total miscellaneous amount
				$miscellaneous_detail = $purchasingTrans->checkIfPOMiscExist($company_code, $po_number, '');
				foreach($miscellaneous_detail as $misc){
					$misc_amount = $misc['poMiscAmt'];
					$update_tot_misc_ok = $purchasingTrans->updateTotalMiscellaneous($company_code, $po_number, $misc_amount);
				}
				
				#PO004C
				#INITIALIZE RTN
				$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
				if($po_header['poReopenTag']=='Y'){
					#CHECK DETAILS
					###read the po item detail of the re-opened po on process (tblPoItemDtl)
					$reopen_po_dtl = $purchasingTrans->checkIfPODetailExist($company_code, $po_number, '');
					foreach($reopen_po_dtl as $reopen_dtl){
						###read the original po item detail table(tblPoItemDtl1)
						$orig_dtl = $purchasingTrans->checkIfPODtlBackupExist($company_code, $po_number, $reopen_dtl['prdNumber']);
						if(empty($orig_dtl)){ #new item only
							$purchasingTrans->writePODtlCorr($reopen_dtl['compCode'], $reopen_dtl['poNumber'], $reopen_dtl['prdNumber'],
								$reopen_dtl['umCode'], $reopen_dtl['prdConv'], $reopen_dtl['orderedQty'], $reopen_dtl['poUnitCost'], $reopen_dtl['poExtAmt'], $reopen_dtl['refCostEvent'],
								$reopen_dtl['itemDiscPcents'], $reopen_dtl['itemDiscCogY'], $reopen_dtl['itemDiscCogN'], $reopen_dtl['poLevelDiscCogY'], $reopen_dtl['poLevelDiscCogN'],
								'Y');
						} else{
							###JAY(02/12/09) if($reopen_dtl['orderedQty'] != $orig_dtl['orderedQty']){ #ordered qty have changed
								$ordered_qty = $reopen_dtl['orderedQty'] - $orig_dtl['orderedQty'];
								$po_ext_amt = $reopen_dtl['poExtAmt'] - $orig_dtl['poExtAmt'];
								$item_disc_cog_y = $reopen_dtl['itemDiscCogY'] - $orig_dtl['itemDiscCogY'];
								$item_disc_cog_n = $reopen_dtl['itemDiscCogN'] - $orig_dtl['itemDiscCogN'];
								$po_level_disc_cog_y = $reopen_dtl['poLevelDiscCogY'] - $orig_dtl['poLevelDiscCogY'];
								$po_level_disc_cog_n = $reopen_dtl['poLevelDiscCogN'] - $orig_dtl['poLevelDiscCogN'];
								
								$purchasingTrans->writePODtlCorr($reopen_dtl['compCode'], $reopen_dtl['poNumber'], $reopen_dtl['prdNumber'],
									$reopen_dtl['umCode'], $reopen_dtl['prdConv'], $ordered_qty, $reopen_dtl['poUnitCost'], $po_ext_amt, $reopen_dtl['refCostEvent'],
									$reopen_dtl['itemDiscPcents'], $item_disc_cog_y, $item_disc_cog_n, $po_level_disc_cog_y, $po_level_disc_cog_n,
									'');
							###JAY(02/12/09) }
						}
					}
					
					$corr_detail = $purchasingTrans->checkIfPODtlCorrExist($company_code, $po_number, '');
					if(!empty($corr_detail)){					
						#CHECK HEADER
						$reopen_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
						$orig_header = $purchasingTrans->checkIfPoHeaderBackupExist($company_code, $po_number);
						$po_tot_ext = $reopen_header['poTotExt'] - $orig_header['poTotExt'];
						$po_item_total = $reopen_header['poItemTotal'] - $orig_header['poItemTotal'];
						$po_qty_total = $reopen_header['poQtyTotal'] - $orig_header['poQtyTotal'];
						$po_tot_disc = $reopen_header['poTotDisc'] - $orig_header['poTotDisc'];
						$po_tot_allow = $reopen_header['poTotAllow'] - $orig_header['poTotAllow'];
						$purchasingTrans->writePOHeaderCorr($reopen_header['compCode'], $reopen_header['poNumber'], $reopen_header['suppCode'], $reopen_header['poTerms'], $reopen_header['poBuyer'], $reopen_header['poDate'], $reopen_header['poExpDate'],
							$reopen_header['poAllTag'], $po_item_total, $po_qty_total, $po_tot_ext, $po_tot_disc, $po_tot_allow, $reopen_header['poTotMisc'], $reopen_header['poStat'],
							$reopen_header['poReopenId'], $reopen_header['poReopenDate'], 'Y', $reopen_header['corrPrintTag'], $reopen_header['poReopenTag'], $reopen_header['poCancelDate']);
					}

					###tag poReopenTag='N'
					$purchasingTrans->tagPOReopenTag($company_code, $po_number, 'N', 'Y');
				}
				
				### AUDIT TRAIL
				$purchasingTrans->updatePOAudit($company_code, $po_number, 'poRlseDate', $release_date, 'poRlseOptr', $release_operator);
				($update_header_ok==true) ? $msg=$etcTrans->getMessage('PO0058') : $msg=$etcTrans->getMessage('PO0057');
			}		
		} else{
			$msg = $etcTrans->getMessage('PO0056');
		}
		$etcTrans->redirectURL('modules/purchasing/po_release.php?msg='.$msg);
		break;
		
	case 'get_po_details':
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		
		$po = $purchasingTrans->getPOAddChargesDetails($company_code, $po_number, '');
		if(!empty($po)){
			$po_status = $po['poStat'];
			$po_number = $po['poNumber'];
			$po_date = substr($po['poDate'], 0, 11);
			$supplier = $po['suppName'];
			$terms = $po['suppTerms'];
			$remarks = $po['poAddChargeRemarks'];
			$percent = $po['poAddChargePcent'];
			$amount = $po['poAddChargeAmt'];
			
			if($po_status=='R' or $po_status=='P'){
				$etcTrans->redirectURL('modules/purchasing/po_add_charges.php?po_number='.$po_number.'&po_date='.$po_date.'&supplier='.$supplier.'&terms='.$terms.'&remarks='.$remarks.'&percent='.$percent.'&amount='.$amount);
			} else{
				$msg = $etcTrans->getMessage('PO0065');
				$etcTrans->redirectURL('modules/purchasing/po_add_charges.php?po_number=&msg='.$msg);
			}
		} else{
			$msg = $etcTrans->getMessage('PO0030');
			$etcTrans->redirectURL('modules/purchasing/po_add_charges.php?po_number=&msg='.$msg);
		}
		break;
		
	case 'save_po_add_charges':
		$company_code = $_SESSION['comp_code'];
		$po_number = $_POST['po_number'];
		$remarks = $_POST['remarks'];
		$percent = $_POST['add_charge_percent'];
		$amount = $_POST['add_charge_amount'];
		
		$insert_ok = $purchasingTrans->insertIntoPOAddChargesDetails($company_code, $po_number, $remarks, $percent, $amount);		
		($insert_ok==true) ? $msg=$etcTrans->getMessage('PO0066') : $msg=$etcTrans->getMessage('PO0067');
		
		$etcTrans->redirectURL('modules/purchasing/po_add_charges.php?msg='.$msg);
		break;
		
	case 'delete_add_charges':
		$company_code = $_SESSION['comp_code'];
		
		if($_POST['delete_add_charges']){
			foreach($_POST['delete_add_charges'] as $po_number){
				$delete_add_charges_ok = $purchasingTrans->deletePOAddChargesDetails($company_code, $po_number);
				($delete_add_charges_ok==true) ? $msg=$etcTrans->getMessage('PO0063') : $msg=$etcTrans->getMessage('PO0064');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0062');
		}
		$etcTrans->redirectURL('modules/purchasing/po_add_charges.php?msg='.$msg);
		break;
		
	case 'search_po_cancel_close':
		$trans = $_POST['trans_type'];
		$po_number = $_POST['po_number'];
		
		if(!is_numeric($po_number) or strpos($po_number, '.')){
			if($po_number!='') $msg=$etcTrans->getMessage('PO0055');
			$etcTrans->redirectURL('modules/purchasing/cancel_close_po.php?msg='.$msg.'&trans='.$trans);
		} else{
			$etcTrans->redirectURL('modules/purchasing/cancel_close_po.php?msg='.$msg.'&po_number='.$po_number.'&trans='.$trans);
		}
		break;
		
	case 'cancel_po':
		$company_code = $_SESSION['comp_code'];
		$trans = $_POST['trans_type'];
		
		if($_POST['select_po']){
			foreach($_POST['select_po'] as $po_number){
				$cancel_po_ok = $purchasingTrans->cancelPO($company_code, $po_number);
				($cancel_po_ok==true) ? $msg=$etcTrans->getMessage('PO0098') : $msg=$etcTrans->getMessage('PO0099');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0096');
		}
		$etcTrans->redirectURL('modules/purchasing/cancel_close_po.php?msg='.$msg.'&trans='.$trans);
		break;
		
	case 'close_po':
		$company_code = $_SESSION['comp_code'];
		$trans = $_POST['trans_type'];
		
		if($_POST['select_po']){
			foreach($_POST['select_po'] as $po_number){
				$close_po_ok = $purchasingTrans->closePO($company_code, $po_number);
				($close_po_ok==true) ? $msg=$etcTrans->getMessage('PO0100') : $msg=$etcTrans->getMessage('PO0101');
			}
		} else{
			$msg = $etcTrans->getMessage('PO0097');
		}
		$etcTrans->redirectURL('modules/purchasing/cancel_close_po.php?msg='.$msg.'&trans='.$trans);
		break;
}

switch($ajax_trans){
	case 'get_unit_cost':
		$unit_info = $purchasingTrans->getUnitInfo($_GET['supplier_code'], $_GET['product_code'], $_GET['document_date']);
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
		
	case 'get_supplier_info':
		$supplier_info =  $purchasingTrans->getSupplier($_GET['supplier_code']);
		if(!empty($supplier_info)){
			$supplier_desc = stripslashes(addslashes($supplier_info['suppName']));
			$supplier_term = $supplier_info['suppTerms'];
		} else{
			$supplier_desc = '';
			$supplier_term = '';
		}
		echo "<script>
				$('".$_GET['output']."').value = '{$supplier_desc}';
				$('terms').value = '{$supplier_term}';
			</script>";
		break;
		
	case 'get_allowance_info':
		$allowance_info = $purchasingTrans->getAllowanceType($_GET['allowance_type']);
		if(!empty($allowance_info)){
			$allowance_desc = $allowance_info['allwDesc'];
			$cog = $allowance_info['allwCostTag'];
		} else{
			$allowance_desc = '';
			$cog = '';
		}
		echo "<script>
				$('".$_GET['output']."').value = '{$allowance_desc}';
				$('cog').value = '{$cog}';
			</script>";
		break;
		
	case 'get_po_info':
		$company_code = $_GET['company_code'];
		$po_number = $_GET['po_number'];
		
		$po_header = $purchasingTrans->getPOAddChargesDetails($company_code, $po_number, '');
		$po_date = substr($po_header['poDate'], 0, 11);
		$supplier = stripslashes(addslashes($po_header['suppName']));
		$terms = $po_header['suppTerms'];
		$remarks = $po_header['poAddChargeRemarks'];
		($po_header['poAddChargePcent']=='') ? $percent='0' : $percent=$po_header['poAddChargePcent'];
		($po_header['poAddChargeAmt']=='') ? $amount='0' : $amount=$po_header['poAddChargeAmt'];
		($remarks=='' and $percent=='' and $amount=='') ? $button='ADD' : $button='EDIT';

		echo "<script>
				$('orig_po_number').value = '{$po_number}';
				$('po_number').value = '{$po_number}';
				$('remarks').value = '{$remarks}';
				$('po_date').value = '{$po_date}';
				$('supplier').value = '{$supplier}';
				$('terms').value = '{$terms}';
				$('add_charge_percent').value = '{$percent}';
				$('add_charge_amount').value = '{$amount}';
				$('save_po_add_charges').value = '{$button}';
			</script>";
		break;
}
?>
<?
#Description: Purchase Order Data Entry. With a little medication,
#			it is also used by Receiving Authorization and Receipts Entry.
#Author: Jhae Torres
#Date Created: March 18, 2008

#PAGES: po_entry1 / po_entry2 / po_edit1 / po_edit2 / po_search / view_po

session_start();
$company_code=$_SESSION['comp_code'];

require_once "index.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "purchasing.obj.php";
require_once "../etc/etc.obj.php";

$gmt = time() + (8 * 60 * 60);
$date = date("m-d-Y", $gmt);
$datetime = date("m-d-Y H:i:s", $gmt);
$exp_gmt = time() + (7 * 24 * 60 * 60);
$exp_date = date("m-d-Y", $exp_gmt);
$cancel_gmt = time() + (14 * 24 * 60 * 60);
$cancel_date = date("m-d-Y", $cancel_gmt);

$db = new DB;
$db->connect();

$etcTrans = new etcObject;
$purchasingTrans = new purchasingObject;
if(empty($_GET['page'])) $_GET['page']='po_entry1';
($_GET['po_number']) ? $po_number=$_GET['po_number'] : $po_number=$etcTrans->getNumber($company_code, 'poNumber', 'tblPoNumber');
$po_header = $purchasingTrans->checkIfPOHeaderExist($company_code, $po_number, '');
$po_remark = $purchasingTrans->checkIfPORemarkExist($company_code, $po_number, '');
$misc_sequence = $purchasingTrans->getMiscSequenceNumber($company_code, $po_number);

($po_header['poItemTotal']=='') ? $hash_items=0 : $hash_items=$po_header['poItemTotal'];
($po_header['poQtyTotal']=='') ? $hash_quantity=0 : $hash_quantity=$po_header['poQtyTotal'];
$control_totals = $purchasingTrans->controlTotals($po_header['compCode'], $po_header['poNumber']);
($control_totals['entered_items']==0) ? $entered_items=0 : $entered_items=$control_totals['entered_items'];
($control_totals['entered_quantity']=='') ? $entered_quantity=0 : $entered_quantity=$control_totals['entered_quantity'];
$diff_items = $hash_items - $entered_items;
$diff_quantity = $hash_quantity - $entered_quantity;

if($po_header['suppTerms']){
	if($_GET['page']!='po_entry1' and $_GET['page']!='po_entry2' and $_GET['page']!='po_edit1' and $_GET['page']!='po_edit2'){
		if($_GET['page']!='po_entry1' and $_GET['page']!='po_entry2'){
			$disabled2 = 'disabled';
		}
		$readonly1 = 'readOnly';
		$disabled1 = 'disabled';
	}
	$readonly = 'readOnly';
	$disabled = 'disabled';

	if($diff_items==0 and $diff_quantity==0) $readonly2='readOnly';
}

$suppliers = $purchasingTrans->getSupplier('');
$suppliers_list = $db->selectOption2D($suppliers, 'suppliers_list', 'suppliers_list', 'class="supplier_code" tabIndex="1" onClick="updateProducts(this.value);" onKeyUp="updateProducts(this.value); displayValue(\'supplier_desc\', \'get_supplier_info\', \'&supplier_code=\'+this.value);" '.$disabled, 'onClick="updateProducts(this.text); displayValue(\'supplier_desc\', \'get_supplier_info\', \'&supplier_code=\'+this.value);"', $po_header['suppCode'], 'suppCode', 'suppCode', 'suppName');

$buyers = $purchasingTrans->getBuyers();
$buyers_list = $db->selectOption2D($buyers, 'buyer', 'buyer', 'class="supplier_code" tabIndex="3" '.$disabled1, '', $po_header['poBuyer'], 'buyerCode', 'buyerCode', 'buyerName');

($_GET['page']=='view_po') ? $status=$etcTrans->getStatus('tblPoHeader', '') : $status=$etcTrans->getStatus('tblPoHeader', '9');
(empty($po_header['poStat'])) ? $selected='O' : $selected=$po_header['poStat'];
$po_status = $db->selectOption2D($status, 'status', 'status', 'class="readonly_textbox" '.$disabled2, '', $selected, 'statCode', 'statName', 'statName');

$allowance_types = $purchasingTrans->getAllowanceType('');
$allowance_types_list = $db->selectOption2D($allowance_types, 'allowance_types', 'allowance_types', 'class="supplier_code" tabIndex="6"', 'onClick="displayValue(\'allowance_desc\', \'get_allowance_info\', \'&allowance_type=\'+this.value);"', '', 'allwTypeCode', 'allwTypeCode', 'allwDesc');

#PO Details List
$po_details_list = $purchasingTrans->getPODetailsList($po_header['compCode'], $po_header['poNumber']);
foreach($po_details_list as $x){
	$po_discount = $purchasingTrans->checkIfPOItemDiscountExist($company_code, $po_number, $x['prdNumber'], '');
	if(!empty($po_discount)){
		$product_code = "<input type='text' class='product_code_link' name='view_po' id='view_po' value='".$x['prdNumber']."' readOnly onClick='popupWindow(\"".$config['sys_host']."\", \"".$config['sys_dir']."\", \"modules/purchasing/po_product_discount.php?company_code=".$po_header['compCode']."&po_number=".$po_header['poNumber']."&product_code=".$x['prdNumber']."\");' />";
	} else{
		$product_code = $x['prdNumber'];
	}

	$po_details .= "<tr>
					<td width='4%'><input type='checkbox' class='check' name='select_detail[]' id='select_detail[]' value='".$x['prdNumber']."' onClick='editPODetailQuantity(this.value);'; /></td>
					<td width='11%'>".$product_code."</td>
					<td width='8%'><input type='text' class='edit_qty' name='ordered_qty".$x['prdNumber']."' id='ordered_qty".$x['prdNumber']."' value='".$x['orderedQty']."' maxLength='9' readOnly /></td>
					<td width='60%'>".stripslashes($x['prdDesc'])."</td>
					<td width='5%'>".$x['umCode']."</td>
					<td width='' align='right'>".$x['poUnitCost']."</td>
				</tr>";
				
	$all_po_details .= "<tr>
					<td width='10%'>".$product_code."</td>
					<td width='8%' align='right'>".$x['orderedQty']."</td>
					<td width='61%'>".stripslashes($x['prdDesc'])."</td>
					<td width='5%'>".$x['umCode']."</td>
					<td width='' align='right'>".$x['poUnitCost']."</td>
				</tr>";
}

#PO Allowances List
$po_allowance_list = $purchasingTrans->checkIfPOAllowanceDetailExist($po_header['compCode'], $po_header['poNumber'], '');
foreach($po_allowance_list as $x){
	$po_allowance .= "<tr>
						<td width='4%'><input type='checkbox' class='check' name='select_allowance[]' id='select_allowance[]' value='".$x['allwTypeCode']."' onClick='editPOAllowance(this.value);' /></td>
						<td width='9.5%'><input type='text' class='txt' name='allowance_type".$x['allwTypeCode']."' id='allowance_type".$x['allwTypeCode']."' value='".$x['allwTypeCode']."' readOnly /></td>
						<td width='10%'><input type='text' class='num_txt' name='allowance_percent".$x['allwTypeCode']."' id='allowance_percent".$x['allwTypeCode']."' value='".$x['poAllwPcnt']."' maxLength='5' readOnly /></td>
						<td width='10%'><input type='text' class='num_txt' name='allowance_amount".$x['allwTypeCode']."' id='allowance_amount".$x['allwTypeCode']."' value='".$x['poAllwAmt']."' maxLength='11' readOnly /></td>
						<td width='57%'>".$x['allwDesc']."</td>
						<td>".$x['allwCostTag']."</td>
					</tr>";
					
	$all_po_allowance .= "<tr>
						<td width='10%'>".$x['allwTypeCode']."</td>
						<td width='10%' align='right'>".$x['poAllwPcnt']."</td>
						<td width='10%' align='right'>".$x['poAllwAmt']."</td>
						<td width='61%'>".$x['allwDesc']."</td>
						<td>".$x['allwCostTag']."</td>
					</tr>";
}
if(empty($po_allowance)) $po_allowance = "<tr><td colspan='5'>".$etcTrans->getMessage('PO0019')."</td></tr>";
if(empty($all_po_allowance)) $all_po_allowance = "<tr><td colspan='5'>".$etcTrans->getMessage('PO0019')."</td></tr>";


#PO Miscellaneous Charges List
$po_misc_list = $purchasingTrans->checkIfPOMiscExist($po_header['compCode'], $po_header['poNumber'], '');
foreach($po_misc_list as $x){
	$po_misc .= "<tr>
					<td width='4%'><input type='checkbox' class='check' name='select_misc[]' id='select_misc[]' value='".$x['poMiscSeq']."' onClick='editPOMiscCharges(this.value);' /></td>
					<td width='10%'>".$x['poMiscSeq']."</td>
					<td width='71%'><input type='text' class='txt' name='misc_desc".$x['poMiscSeq']."' id='misc_desc".$x['poMiscSeq']."' value='".$x['poMiscDesc']."' maxLength='50' readOnly /></td>
					<td width='71%'><input type='text' class='num_txt' name='misc_amt".$x['poMiscSeq']."' id='misc_amt".$x['poMiscSeq']."' value='".$x['poMiscAmt']."' maxLength='11' readOnly /></td>
				</tr>";
				
	$all_po_misc .= "<tr>
					<td width='10%'>".$x['poMiscSeq']."</td>
					<td width='70%'>".$x['poMiscDesc']."</td>
					<td align='right'>".$x['poMiscAmt']."</td>
				</tr>";
}
if(empty($po_misc)) $po_misc = "<tr><td colspan='5'>".$etcTrans->getMessage('PO0039')."</td></tr>";
if(empty($all_po_misc)) $all_po_misc = "<tr><td colspan='5'>".$etcTrans->getMessage('PO0039')."</td></tr>";

#Search Page: PO List
$search_po_number = $_GET['po_number'];
$po_list = $purchasingTrans->getPO($search_po_number);
if(!empty($po_list)){
	foreach($po_list as $x){
		$po .= "<tr>
				<td width='4%'>";
		($x['statName']!='Released' and $x['statName']!='Complete') ? $po.="<input type='checkbox' class='check' name='delete_po[]' id='delete_po[]' value='".$x['poNumber']."' />" : $po.="&nbsp;";
		$po .= "</td>
				<td width='4%'>";
		($x['statName']!='Released' and $x['statName']!='Complete') ? $po.="<input type='radio' class='check' name='edit_po' id='edit_po' value='".$x['poNumber']."' title='EDIT PO DOCUMENT'
			onClick='assignTransaction(this.id); javascript:document.po_entry_form.submit();' />" : $po.="&nbsp;";
		$po .= "</td>
				<td width='10%'><input type='submit' class='link_button' name='view_po' id='view_po' value='".$x['poNumber']."'
						onClick='assignTransaction(this.id);' /></td>
				<td width='41%'>".stripslashes($x['suppName'])."</td>
				<td width='12%'>".$etcTrans->formatDate($x['poDate'])."</td>
				<td width='12%'>".$etcTrans->formatDate($x['poExpDate'])."</td>
				<td>".$x['statName']."</td>
			<tr>";
	}
} else{
	$po = "<tr><td colspan='7'>".$etcTrans->getMessage('PO0032')."</td></tr>";
}

#$db->disconnect();
?>

<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<script type="text/javascript">
	function updateProducts(selectedSupplier){
		//var suppliers_list = document.po_entry_form.suppliers_list
		var products_list = document.po_entry_form.products_list
		var products = new Array()
		<?
			foreach($suppliers as $supp){
				$products_array .= "products[".$supp['suppCode']."]=[";
				$products = $purchasingTrans->getProducts($supp['suppCode']);
				$number_products = $purchasingTrans->countProducts($supp['suppCode']);
				
				$cnt = 1;
				foreach($products as $prod){
					$products_array .= "\"".$prod['prdNumber']."|".$prod['prdNumber'].",".$prod['suppCode'].",".stripslashes(addslashes($prod['prdDesc'])).",".$prod['prdBuyUnit'].",".$prod['prdFrcTag']."\"";
					if($cnt < $number_products){
						$products_array .= ", ";
					}
					$cnt++;
				}
				$products_array .= "];";
			}
			echo $products_array;
		?>
		products_list.options.length=0
		if (selectedSupplier>0){
			for (i=0; i<products[selectedSupplier].length; i++)
				products_list.options[products_list.options.length]=new Option(products[selectedSupplier][i].split("|")[0], products[selectedSupplier][i].split("|")[1])
		}
	}

/*---------------------------------AJAX (start)---------------------------------*/
	//function displayValue('unit_cost', 'get_unit_cost', '&supplier_code=111&product_code=222')
	function displayValue(output_field, trans, url_variables) {
		new Ajax.Updater(
			output_field,
			'purchasing.trans.php',
			{
				method: 'get',
				//parameters: 'ajax_trans='+trans+'&supp_code='+$F(input_field),
				//parameters: 'ajax_trans='+trans+'&supp_code='+input_field,
				parameters: 'ajax_trans='+trans+'&output='+output_field+url_variables,
				evalScripts: true
			}
		);
	}
/*---------------------------------AJAX (end)---------------------------------*/
</script>
<?
if(!empty($po_header)){
	echo "<body onLoad=\"";

	if($_GET['page']=='po_entry1' or $_GET['page']=='po_edit1'){
		echo "updateProducts('".$po_header['suppCode']."');
			setFocus('hash_items');
			javascript: document.getElementById('hash_items').select();";
	}
	
	if($_GET['page']=='po_edit2' or $_GET['page']=='view_po'){
		echo "assignValue('remarks', '".$po_remark['remark']."', '');";
	}
	
	if($_GET['page']=='po_entry2' or $_GET['page']=='po_edit2'){
		echo "assignValue('create_po_allowance', '".$_GET['allowance']."', '');
			assignValue('create_misc_charges', '".$_GET['misc_charges']."', '');";
	}
	
	echo "assignValue('supplier', '".$po_header['suppCode']."', '');
			assignValue('supplier_desc', '".$po_header['suppName']."', '');
			assignValue('terms', '".$po_header['suppTerms']."', '');
			assignValue('document_date', '".$etcTrans->formatDate($po_header['poDate'])."', '');
			assignValue('expected_delivery', '".$etcTrans->formatDate($po_header['poExpDate'])."', '');
			assignValue('cancel_date', '".$etcTrans->formatDate($po_header['poCancelDate'])."', '');
			assignValue('buyer', '".$po_header['poBuyer']."', '');
			assignValue('status', '".$po_header['statName']."', '');
			assignValue('hash_items', '".$hash_items."', '');
			assignValue('hash_quantity', '".$hash_quantity."', '');
			assignValue('page', '".$_GET['page']."', '');
		\">";
		
} else{
	if($_GET['page']=='po_search'){
		echo "<body onLoad='setFocus(\"po_number\");
			assignValue(\"page\", \"".$_GET['page']."\", \"\");'>";
	} else{
		echo "<body onLoad='setFocus(\"document_date\");
			assignValue(\"page\", \"".$_GET['page']."\", \"\");'>";
	}
}
?>

<!-- message area -->
<div id='msg'> 
  <?=$_GET['msg']?>
</div>

<div id='frame_body'>
  <form name='po_entry_form' id='po_entry_form' action='purchasing.trans.php' method='POST'>
    <input type='hidden' name='transaction' id='transaction' />
	<input type='hidden' name='transaction_override' id='transaction_override' />
	<input type='hidden' name='fraction_allowed' id='fraction_allowed' />
	<input type='hidden' name='supplier' id='supplier' value="<?=$po_header['suppCode']?>" />
	<input type='hidden' name='product' id='product' />
	<input type='hidden' name='cost_event' id='cost_event' />
	<input type='hidden' name='conv_factor' id='conv_factor' />
	<input type='hidden' name='page' id='page'>
	<input type='hidden' name='date_today' id='date_today' value='<?=$date?>' />
	<input type='hidden' name='create_po_allowance' id='create_po_allowance' />
	<input type='hidden' name='create_misc_charges' id='create_misc_charges' />
<!--######################################## HEADER BUTTONS ##############################################-->
	<div class='header_buttons'>
		<table>
			<tr>
				<td>
<? if($_GET['page']=='po_entry1' or $_GET['page']=='po_entry2' or $_GET['page']=='po_edit1' or $_GET['page']=='po_edit2'){ ?>
					<img src='../../attachments/save.png' name='save' id='save' title='SAVE'
						onClick='saveProcedure();'></img>
<? } ?>

<? if(!empty($po_header) and $_GET['page']!='po_search' and $_GET['page']!='view_po'){ ?>
					&nbsp;
					<img src='../../attachments/add.png' name='new_po' id='new_po' title='NEW PURCHASE ORDER'
						onClick='assignTransaction(this.id); confirmExitAction("<?=$_GET['page']?>", "po_entry1");'></img>
					&nbsp;
					<img src='../../attachments/search.png' name='search_po_page' id='search_po_page' title='SEARCH PURCHASE ORDER'
						onClick='assignTransaction(this.id); confirmExitAction("<?=$_GET['page']?>", "po_search");'></img>
<? } else{ ?>
					&nbsp;
					<img src='../../attachments/add.png' name='new_po' id='new_po' title='NEW PURCHASE ORDER'
						onClick='assignTransaction(this.id); confirmExitAction("", "");'></img>
					&nbsp;
					<img src='../../attachments/search.png' name='search_po_page' id='search_po_page' title='SEARCH PURCHASE ORDER'
						onClick='assignTransaction(this.id); confirmExitAction("", "");'></img>
<? } ?>

<? if(!empty($po_header) and ($_GET['page']=='po_entry1' or $_GET['page']=='po_entry2')){ ?>
					&nbsp;
					<img src='../../attachments/delete.png' name='delete_this_po' id='delete_this_po' title='DELETE PURCHASE ORDER'
						onClick='assignTransaction(this.id); confirmDeleteThisPODocument();'></img>
<? } ?>
				</td>
			</tr>
		</table>
	</div>
<!--######################################################################################-->
	<hr>
	

<!--############################################# PO Header ##############################################-->
<? if($_GET['page'] != 'po_search'){ ?>
<div class='sub_frame_body'>
    <div class='header'> 
      <table>
        <tr> 
          <th>Purchase Order No.:</th>
          <td><input type='text' class='terms' name='po_number' id='po_number' maxLength='6' value='<?=$po_number?>' readOnly /></td>
          <th class='status'>Status:</th>
          <td> 
            <!--<input type='text' class='readonly_textbox' name='status' id='status' value='Open' readOnly />-->
            <?=$po_status?>
          </td>
        </tr>
        <tr> 
          <th>Vendor Number:</th>
          <td> 
            <?=$suppliers_list?>
            &nbsp; <input type='text' class='supplier_desc' name='supplier_desc' id='supplier_desc' readOnly />
          </td>
          <th class='status'>Terms:</th>
          <td><input type='text' class='terms' name='terms' id='terms' readOnly /></td>
        </tr>
        <tr> 
          <th>Document Date:</th>
          <td colspan='3'><input type='text' class='date' name='document_date' id='document_date' tabIndex='1' maxLength='10' onKeyUp='validateDate(this);' value='<?=$date?>' <?=$readonly?> /></td>
        </tr>
        <tr> 
          <th>Expected Date of Delivery:</th>
          <td colspan='3'>
          	<input type='text' class='date' name='expected_delivery' id='expected_delivery' tabIndex='2' maxLength='10' onKeyUp='validateDate(this);' value='<?=$exp_date?>' <?=$readonly1?> />
          	&nbsp;&nbsp;&nbsp;<b>Cancel Date:</b>&nbsp;&nbsp;&nbsp;<input type='text' class='date' name='cancel_date' id='cancel_date' tabIndex='2' maxLength='10' onKeyUp='validateDate(this);' value='<?=$cancel_date?>' <?=$readonly1?> />
          </td>
        </tr>
        <tr> 
          <th>Buyer Code:</th>
          <td colspan='3'> 
            <?=$buyers_list?>
          </td>
        </tr>
      </table>
    </div>
    <div class='control_totals'> 
      <table>
        <tr> 
          <th>&nbsp;</th>
          <th>Hash</th>
          <th>Entered</th>
          <th>Difference</th>
        </tr>
        <tr> 
          <th>Items:</th>
          <td><input type='text' name='hash_items' id='hash_items' tabIndex='4' maxLength='9' value='<?=$hash_items?>' <?=$readonly2?> /></td>
          <td><input type='text' name='entered_items' id='entered_items' value='<?=$entered_items?>' readOnly /></td>
          <td><input type='text' name='diff_items' id='diff_items' value='<?=$diff_items?>' readOnly /></td>
        </tr>
        <tr> 
          <th>Quantity:</th>
          <td><input type='text' name='hash_quantity' id='hash_quantity' tabIndex='5' maxLength='11' value='<?=$hash_quantity?>' <?=$readonly2?> /></td>
          <td><input type='text' name='entered_quantity' id='entered_quantity' value='<?=$entered_quantity?>' readOnly /></td>
          <td><input type='text' name='diff_quantity' id='diff_quantity' value='<?=$diff_quantity?>' readOnly /></td>
        </tr>
      </table>
    </div>
<? } ?>

<!--############################################# PO Search Page ##############################################-->
<? if($_GET['page'] == 'po_search'){ ?>
	<div class='search_header'>
		PO Number:&nbsp;
		<input type='text' class='textbox' name='po_number' id='po_number' tabIndex='1' maxLength='6' />
      &nbsp; <input type='submit' class='go' name='search_po' id='search_po' value='GO' title='SEARCH PO NUMBER' tabIndex='2'
			onClick='assignTransaction(this.id);' />
    </div>
	<div class='details'> 
      <table>
        <th width='4%'> 
          <? if($po_allowance != "<tr><td colspan='5'>".$etcTrans->getMessage('PO0032')."</td></tr>"){ ?>
          <img src='../../attachments/delete.png' name='delete' id='delete' title='DELETE PO DOCUMENT(S)'
						onClick='assignTransaction(this.id); confirmDelete();'></img> 
          <? } else{ ?>
          DEL 
          <? } ?>
        </th>
        <th width='4%'>EDT</th>
        <th width='10%'>PO Number</th>
        <th width='39%'>Vendor Description</th>
        <th width='12%'>Document<br>
          Date</th>
        <th width='12%'>Expected Date of Delivery</th>
        <th>Status</th>
      </table>
    </div>
	<div class='search_result'> 
      <table>
        <?=$po?>
      </table>
    </div>
<? } ?>

<!--############################################# PO View Page ##############################################-->
<? if($_GET['page'] == 'view_po'){ ?>
	<div class='details'> 
      <h6>PO DETAILS</h6>
      <table>
        <tr> 
          <th width='10%'>Product Code</th>
          <th width='8%'>Quantity</th>
          <th width='59%'>Product Description</th>
          <th width='5%'>UOM</th>
          <th width=''>Unit Cost</th>
        </tr>
      </table>
    </div>
	<div class='po_entry3_list'> 
      <table>
        <?=$all_po_details?>
      </table>
    </div>
	<div class='details'> 
      <h6>PO ALLOWANCE</h6>
      <table>
        <tr> 
          <th width='10%'>Type</th>
          <th width='10%'>Percent</th>
          <th width='10%'>Amount</th>
          <th width='60%'>Allowance Description</th>
          <th width='10%'>Affects COG</th>
        </tr>
      </table>
    </div>
	<div class='po_entry3_list'> 
      <table>
        <?=$all_po_allowance?>
      </table>
    </div>
	<div class='details'> 
      <h6>MISCELLANEOUS CHARGES</h6>
      <table>
        <tr> 
          <th width='10%'>Sequence #</th>
          <th width='70%'>Charge Description</th>
          <th>Amount</th>
        </tr>
      </table>
    </div>
	<div class='po_entry3_list'> 
      <table>
        <?=$all_po_misc?>
      </table>
    </div>
	<div class='details'> 
      <h6>REMARKS</h6>
      <input type='text' class='remarks' name='remarks' id='remarks' maxLength='1000' readOnly />
    </div>
<? } ?>	

<!--############################################# PO Entry/Edit Page 1 ##############################################-->
<? if($_GET['page']=='po_entry1' or $_GET['page']=='po_edit1'){ ?>
    <div class='details'> 
      <table>
        <tr> 
          <th width='10%'>Product Code</th>
          <th width='8%'>Quantity</th>
          <th width='53%'>Product Description</th>
          <th width='5%'>UOM</th>
          <th width=''>Unit Cost</th>
        </tr>
        <tr> 
          <td width='8%'><select name='products_list' id='products_list' tabIndex='6' class='product_code'
          		onClick="assignValue('product_description', this.value.split(',')[2], '');
						displayValue('unit_cost', 'get_unit_cost', '&supplier_code='+document.po_entry_form.suppliers_list.value+'&product_code='+this.value.split(',')[0]+'&document_date='+document.po_entry_form.document_date.value);
						assignValue('fraction_allowed', this.value.split(',')[4], '');
						assignValue('supplier', document.po_entry_form.suppliers_list.value, '');
						assignValue('product', this.value.split(',')[0], '');"
				onKeyUp="assignValue('product_description', this.value.split(',')[2], '');
						displayValue('unit_cost', 'get_unit_cost', '&supplier_code='+document.po_entry_form.suppliers_list.value+'&product_code='+this.value.split(',')[0]+'&document_date='+document.po_entry_form.document_date.value);
						assignValue('fraction_allowed', this.value.split(',')[4], '');
						assignValue('supplier', document.po_entry_form.suppliers_list.value, '');
						assignValue('product', this.value.split(',')[0], '');"
				onChange="javascript: document.getElementById('quantity').focus();">
            </select></td>
          <td width='8%'><input type='text' class='num_textbox' name='quantity' id='quantity' tabIndex='7' maxLength='9' /> 
          </td>
          <td width='50%'><input type='text' class='readonly_textbox' name='product_description' id='product_description' readOnly /></td>
          <td width='5%'><input type='text' class='readonly_textbox' name='uom' id='uom' readOnly /></td>
          <td width='10%'><input type='text' class='num_readonly_textbox' name='unit_cost' id='unit_cost' readOnly /></td>
        </tr>
      </table>
    </div>
	<input type='button' class='add_po_button' name='add_po_detail' id='add_po_detail' tabIndex='8' value='ADD' title='ADD PO DETAIL'
	  	onClick="assignTransaction(this.id); validatePOEntry();
				displayValue('unit_cost', 'get_unit_cost', '&supplier_code='+document.po_entry_form.suppliers_list.value+'&product_code='+document.po_entry_form.products_list.value.split(',')[0]+'&document_date='+document.po_entry_form.document_date.value);" />
	  
    <div class='details'> 
      <table>
        <tr> 
          <th width='4%'> 
            <? if(!empty($po_details)){ ?>
            <img src='../../attachments/delete.png' name='delete' id='delete' title='DELETE PO DETAIL(S)'
						onClick='assignTransaction(this.id); confirmDelete();'></img> 
            <? }else{ ?>
            DEL 
            <? } ?>
          </th>
          <th width='11%'>Product Code</th>
          <th width='8%'>Quantity</th>
          <th width='59%'>Product Description</th>
          <th width='5%'>UOM</th>
          <th width=''>Unit Cost</th>
        </tr>
      </table>
    </div>
	  
    <div class='list'> 
      <table>
        <?=$po_details?>
      </table>
    </div>
	
<!--############################################# PO Entry/Edit Page 2 ##############################################-->
<? } elseif($_GET['page']=='po_entry2'  or $_GET['page']=='po_edit2'){ ?>
<?	if($_GET['allowance']=='yes'){ ?>
		
    <div class='details'> 
      <h6>PO ALLOWANCE</h6>
      <table>
        <tr> 
          <th width='10%'>Type</th>
          <th width='10%'>Percent</th>
          <th width='10%'>Amount</th>
          <th width='60%'>Allowance Description</th>
          <th width='10%'>Affects COG</th>
        </tr>
        <tr> 
          <td width='10%'> 
            <?=$allowance_types_list?>
          </td>
          <td width='10%'><input type='text' class='num_textbox' name='allowance_percent' id='allowance_percent' value='0' tabIndex='7' maxLength='5' /></td>
          <td width='10%'><input type='text' class='num_textbox' name='allowance_amount' id='allowance_amount' value='0' tabIndex='8' maxLength='11' /></td>
          <td width='60%'><input type='text' class='readonly_textbox' name='allowance_desc' id='allowance_desc' /></td>
          <td width='10%'><input type='text' class='readonly_textbox' name='cog' id='cog'></td>
        </tr>
      </table>
    </div>
      <input type='button' class='add_po_button' name='add_po_allowance' id='add_po_allowance' value='ADD' title='ADD PO ALLOWANCE' tabIndex='9'
			onClick="assignTransaction(this.id); validateAllowanceEntry();" />
    <div class='details'>   
      <table>
        <tr> 
          <th width='4%'> 
            <? if($po_allowance != "<tr><td colspan='5'>".$etcTrans->getMessage('PO0019')."</td></tr>"){ ?>
            <img src='../../attachments/delete.png' name='delete' id='delete' title='DELETE PO ALLOWANCE(S)'
							onClick='assignTransaction(this.id); confirmDelete();'></img> 
            <? } else{ ?>
            DEL 
            <? } ?>
          </th>
          <th width='10%'>Type</th>
          <th width='10%'>Percent</th>
          <th width='10%'>Amount</th>
          <th width='56%'>Allowance Description</th>
          <th width='10%'>Affects COG</th>
        </tr>
      </table>
    </div>
		
    <div class='po_entry2_list'> 
      <table>
        <?=$po_allowance?>
      </table>
    </div>
<?	} if($_GET['misc_charges']=='yes'){ ?>
		
    <div class='details'> 
      <h6>MISCELLANEOUS CHARGES</h6>
      <table>
        <tr> 
          <th width='10%'>Sequence #</th>
          <th width='70%'>Charge Description</th>
          <th>Amount</th>
        </tr>
        <tr> 
          <td><input type='text' class='readonly_textbox' name='sequence' id='sequence' readOnly value='<?=$misc_sequence?>' /></td>
          <td><input type='text' class='textbox' name='misc_desc' id='misc_desc' tabIndex='10' maxLength='50' /></td>
          <td><input type='text' class='num_textbox' name='misc_amount' id='misc_amount' tabIndex='11' maxLength='11' /></td>
        </tr>
      </table>
    </div>
		<input type='button' class='add_po_button' name='add_po_misc' id='add_po_misc' value='ADD' title='ADD MISCELLANEOUS CHARGES' tabIndex='12'
			onClick="assignTransaction(this.id); validateMiscEntry();" />
		
    <div class='details'> 
      <table>
        <tr> 
          <th width='4%'> 
            <? if($po_misc != "<tr><td colspan='5'>".$etcTrans->getMessage('PO0039')."</td></tr>"){ ?>
            <img src='../../attachments/delete.png' name='delete' id='delete' title='DELETE MISCELLANEOUS CHARGE(S)'
							onClick='assignTransaction(this.id); confirmDelete();'></img> 
            <? } else{ ?>
            DEL 
            <? } ?>
          </th>
          <th width='10%'>Sequence #</th>
          <th width='69%'>Charge Description</th>
          <th>Amount</th>
        </tr>
      </table>
    </div>
		
    <div class='po_entry2_list'> 
      <table>
        <?=$po_misc?>
      </table>
    </div>
<?	} ?>
	<div class='details'> 
      <h6>REMARKS</h6>
      <!--<textarea name='remarks' id='remarks' cols='10' rows='3' wrap='soft'></textarea>-->
      <input type='text' class='remarks' name='remarks' id='remarks' tabIndex='13' maxLength='1000' />
    </div>
<? } ?>
  </form>
</div>
</div>

</body>
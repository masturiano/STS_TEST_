<?
#Description: PO Receipts Additional Charges Data Entry Program.
#Author: Jhae Torres
#Date Created: July 08, 2008

session_start();
$company_code=$_SESSION['comp_code'];

require_once "index.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "purchasing.obj.php";
require_once "../etc/etc.obj.php";

$db = new DB;
$db->connect();
$purchasingTrans = new purchasingObject;
$etcTrans = new etcObject;


#PO List
$po_number = $_GET['po_number'];
$po_list = $purchasingTrans->getPOAddChargesDetails($company_code, $po_number, 'list');
$cnt=1;
foreach($po_list as $x){
	$po_status = $x['poStat'];
	($x['poAddChargeRemarks']=='') ? $remarks='&nbsp;' : $remarks=$x['poAddChargeRemarks'];
	($x['poAddChargePcent']=='') ? $percent='&nbsp;' : $percent=$x['poAddChargePcent'];
	($x['poAddChargeAmt']=='') ? $amount='&nbsp;' : $amount=$x['poAddChargeAmt'];
	
	if($po_status=='R' or $po_status=='P'){
		$po .= "<tr>
				<td width='5%'>".$cnt."</td>
				<td width='5%'><input type='checkbox' class='check' name='delete_add_charges[]' id='delete_add_charges[]' value='".$x['poNumber']."' /></td>
				<td width='10%'><input type='button' class='link_button' name='get_add_charge' id='get_add_charge' value='".$x['poNumber']."'
						onClick='displayValue(\"po_number\", \"get_po_info\", \"&company_code=".$company_code."&po_number=".$x['poNumber']."\");' /></td>
				<td width='51%'>".$remarks."</td>
				<td width='13%' align='right'>".$percent."</td>
				<td align='right'>".$amount."</td>
			</tr>";
		$cnt++;
	}
}

$po_date = $_GET['po_date'];
$supplier = $_GET['supplier'];
$terms = $_GET['terms'];
$remarks = $_GET['remarks'];
($_GET['percent']=='') ? $percent='0' : $percent=$_GET['percent'];
($_GET['amount']=='') ? $amount='0' : $amount=$_GET['amount'];

($remarks=='' and $percent=='' and $amount=='') ? $save='ADD' : $save='EDIT';
#$db->disconnect();
?>

<link rel='stylesheet' type='text/css' href='../../includes/style.css'></link>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
<!--#-------------------------------AJAX (start)--------------------------------#-->
<script type='text/javascript'>
	//function displayValue('unit_cost', 'get_unit_cost', '&supplier_code=111&product_code=222')
	function displayValue(output_field, trans, url_variables){
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
</script>
<!--#-------------------------------AJAX (end)--------------------------------#-->

<body onLoad='setFocus("po_number");'>

<!-- message area -->
<div id='msg'> 
  <?=$_GET['msg']?>
</div>

<div id='frame_body'>
	<form name='po_add_charges_form' id='po_add_charges_form' action='purchasing.trans.php' method='POST'>
	<input type='hidden' name='transaction' id='transaction' />
	<input type='hidden' name='orig_po_number' id='orig_po_number' value='<?=$po_number?>' />
	<div class='header'>
		<table>
			<tr>
				<th width='13%'>PO Number:</th>
				<td width='87%'>
					<input type='text' class='textbox' name='po_number' id='po_number' tabIndex='1' maxLength='6' value='<?=$po_number?>' />
					<input type='button' class='go_button' name='get_po_details' id='get_po_details' value='GO' tabIndex='2'
						onClick='assignTransaction(this.id); validatePONumberForAddCharges(document.po_add_charges_form.po_number);' />
				</td>
			</tr>
			<tr>
				<th>Remarks:</th>
				<td><input type='text' class='remarks' name='remarks' id='remarks' tabIndex='3' maxLength='1000' value='<?=$remarks?>' /></td>
			</tr>
			<tr>
				<td colspan='2'>
					<table>
						<tr>
							<th width='18%'>PO Date:</th>
							<td width='42%'><input type='text' class='po_date' name='po_date' id='po_date' value='<?=$po_date?>' readOnly /></td>
							<th width='7%'>Vendor:</th>
							<td width='13%'><input type='text' class='vendor' name='supplier' id='supplier' value='<?=$supplier?>' readOnly /></td>
							<th width='7%'>Terms:</th>
							<td width='13%'><input type='text' class='terms' name='terms' id='terms' value='<?=$terms?>' readOnly /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<table>
						<tr>
							<th width='13%'>Additional Charge % :</th>
							<td width='29%'><input type='text' class='textbox' name='add_charge_percent' id='add_charge_percent' tabIndex='4' maxLength='20' value='<?=$percent?>' /></td>
							<th width='15%'>Additional Charge Amt :</th>
							<td width=''><input type='text' class='textbox' name='add_charge_amount' id='add_charge_amount' tabIndex='5' maxLength='20' value='<?=$amount?>' /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='button' class='button' name='save_po_add_charges' id='save_po_add_charges' value='<?=$save?>' tabIndex='6'
							onClick='assignTransaction(this.id); validatePOAddChargesEntry();' />
				</td>
			</tr>
		</table>
	</div>
	<div class='details'> 
      <table>
        <th width='5%'>#</th>
        <th width='5%'><input type='button' class='release_po' name='delete_add_charges' id='delete_add_charges' value='DEL' title='DELETE PO ADDITIONAL CHARGES'
				onClick='assignTransaction(this.id); confirmDeletePOAddCharges();' /></th>
		<th width='10%'>PO Number</th>
        <th width='50%'>Remarks</th>
        <th width='14%'>Charge Percent</th>
        <th width='16%'>Charge Amount</th>
      </table>
    </div>
    <div class='search_result_add_charges'> 
      <table>
        <?=$po?>
      </table>
    </div>
	</form>
</div>

</body>
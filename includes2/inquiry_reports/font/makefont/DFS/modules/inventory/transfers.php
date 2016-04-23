<?php
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
if(isset($_POST['button_transfer'])) { 
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$combo_transfer=$_POST['combo_transfer'];
	$text_transfer=$_POST['text_transfer'];
	$hide_find_transfer=$_POST['hide_find_transfer'];
	$hide_num_transfer=$_POST['hide_num_transfer'];
	

	$trfLoc1 = $_POST['trfLoc1H']; $trfLoc2 = $_POST['trfLoc2H']; $trfClass = $_POST['trfClassH']; $trfRef = $_POST['trfRefH'];
	$docDate = $_POST['docDateH']; $responsible = $_POST['responsibleH']; $trfRemarks = $_POST['trfRemarksH'];
	$hashIte = $_POST['hashIteH']; $hashQty = $_POST['hashQtyH'];
}
$flag_qty_error=$_GET['flag_qty_error'];
////////////////////////////////////////////////////////////////////////////////////-----
function dateTodayMonth()///////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	$monthNow = date("n");
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$month = mssql_query("SELECT pdDesc, pdCode
	FROM tblPeriod
	WHERE pdStat='O' 
	AND pdCode='$monthNow'
	AND compCode='$compcode'");
	
	if($do=="receiveRecord"||$while=="displayItems")
		{ echo "<select name='thisMonth' class='readonly_textbox_month' id='thisMonth'>"; }
	while ($monthRow = mssql_fetch_assoc($month))
		{
		echo "<option value='".$monthRow['pdCode']."'>".strtoupper($monthRow['pdCode'])." - ".strtoupper($monthRow['pdDesc'])."</option>";
		}
	echo "</select>";
	
	}
////////////////////////////////////////////////////////////////////////////////////-----
function dateTodayYear()///////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	$yearNow = date("Y"); $monthNow = date("n");
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$year = mssql_query("SELECT pdDesc, pdCode, pdYear
	FROM tblPeriod
	WHERE pdStat='O' 
	AND pdYear='$yearNow'
	AND pdCode='$monthNow'
	AND compCode='$compcode'");
	
	if($do=="receiveRecord"||$while=="displayItems")
		{ echo "<select name='thisYear' class='readonly_textbox_month' id='thisYear'>"; }
	while ($yearRow = mssql_fetch_assoc($year))
		{
		echo "<option value='".$yearRow['pdYear']."'>".strtoupper($yearRow['pdYear'])."</option>";
		}
	echo "</select>";
	
	}
////////////////////////////////////////////////////////////////////////////////////-----
function dateOneTodayStart()////////////////////////////////////////////////////////
	{
	//$dateNow = date("n-j-Y", time() + (1 * 24 * 60 * 60)) ;
	$dateNow = date("n-j-Y"); $docDate = $_GET['docDate']; 
	$do=$_GET['do']; $while=$_GET['while']; 
	if($do=="newRecord"&&$docDate=="")
		{ echo "value='$dateNow' readonly='readonly'"; }
	else if($do=="newRecord"&&$while=="findIte")
		{ echo "value='$docDate' readonly='readonly'"; }
	else if(($do=="updateRecord"||$do=="newRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "value='$docDate' disabled='disabled'"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "value='$docDate' disabled='disabled'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function calendar()/////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"||$do=="updateRecord")
		{ echo  "onfocus='this.select();lcs(this)' onclick='event.cancelBubble=true;this.select();lcs(this)'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function docDate() {
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$currCode=trim($_GET['currCode']);
	if($currCode!=""&&$signal=="updateRecord") { 
		echo "value='$currCode' readonly='readonly'";	
	}
	if(isset($_POST['button_transfer'])) { 
		$docDate = $_POST['docDateH'];
		echo "value='$docDate'";	
	}
}
////////////////////////////////////////////////////////////////////////////////////-----
function signal()///////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do'];
	echo "value='$do' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function whiles()///////////////////////////////////////////////////////////////////
	{
	$while = $_GET['while'];
	echo "value='$while' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////
function prdFrcTag()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $prdFrcTag = $_GET['prdFrcTag'];
	echo "value='$prdFrcTag' readonly='readonly'";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function newRecord()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do'];
	if($do=="")
		{ echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='New' title='New' 
		onclick='newRecord()'/>"; }
	else if($do=="newRecord")
		{ echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='New' title='New' 
		disabled='disabled'/>"; }
	else if($do=="updateRecord")
		{ echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='New' title='New' 
		disabled='disabled'/>"; }
	}	
////////////////////////////////////////////////////////////////////////////////////-----
function updateRecord()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Update' title='Update' onclick='updateRecord()'/>"; }
	else if($do=="newRecord")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Update' title='Update' disabled='disabled'/>"; }
	/*else if($do=="updateRecord"&&$while=="newIte")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Save Any Changes' title='Save Any Changes' onclick='saveChangesRecord()'/>"; }*/
	else if($do=="updateRecord"&&$while=="editItem")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Save Item Details' title='Save Item Details' onclick='updateItemDetails()'/>"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function deleteRecord()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Delete' title='New'/>"; }
	else if(($do=="updateRecord"||$do=="newRecord")&&$while=="newIte")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Delete' title='New' onclick='deleteRecord()'/>"; }
	else if(($do=="updateRecord"||$do=="newRecord")&&$while=="editItem")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Delete' title='New' onclick='deleteRecord()'/>"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{	echo "<input type='button' class='queryButton' name='addButt' id='addButt' value='Delete' title='New' onclick='deleteRecord()'/>"; }
	}
////////////////////////////////////////////////////////////////////////////////////
function saveRecord()///////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if(($do=="newRecord"&&$while=="findIte")||($do=="newRecord"&&$while==""))
		{
		echo "
		<input type='button' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Save' title='Save Record' onclick='saveRecordNow()'/>"; }
	else if($do=="newRecord"&&$while=="newIte")
		{
		echo "
		<input type='button' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Insert' title='Insert Record' onclick='saveNewRecordNow()'/>";
		}
	else if($do=="receiveRecord"&&$while=="displayItems")
		{
/*		echo "
		<input type='button' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Receive' title='Receive Now' onclick='statusR()'/>";*/
		echo "
		<input type='submit' class='queryButton' name='saveRecordbutt' id='saveRecordbutt' value='Receive' title='Receive Now'/>";
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function cancelButton()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while'];
	if($do=="")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' 
		disabled='disabled'/>"; }
	else if($do=="newRecord")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='canc()'/>"; }
	else if($do=="updateRecord")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='var confirmCancel = confirm(\"Are you sure you want to cancel updating this Record?\"); if(confirmCancel) { window.location=\"trf_main.php\"}'/>"; }
	else if($do=="receiveRecord")
		{	echo "<input type='button' class='queryButton' name='cancelButt' id='cancelButt' value='Cancel' title='Cancel' onclick='var confirmCancel = confirm(\"Are you sure you want to cancel Receiving this Record?\"); if(confirmCancel) { window.location=\"trf_main.php\"}'/>"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeTransfers()/////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	if($do=="newRecord"||$do=="updateRecord")
		{
		echo "<input type='text' class='textbox'  name='trfNo' id='trfNo' tabindex='1' maxlength='6'"; trfNo(); echo "/>";
		}
	else if($do=="receiveRecord")
		{
		transOut();
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function trfNo()////////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo=$_GET['trfNo'];
	if($do=="newRecord"||$do=="updateRecord")
		{
		echo "value='$trfNo' disabled='disabled'";
		}
	else if($trfNo!=""&&$signal=="updateRecord")
		{
		echo "value='$trfNo' readonly='readonly'";
		}
	else if($signal=="")
		{
		echo "onchange='updateRecord()'";
		}
	}
///////////////////////////////////////////////////////////////////////////////////
function transOut()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	
	if(isset($_POST['button_transfer'])) { 
		$trfLoc1 = $_POST['trfLoc1H']; $trfLoc2 = $_POST['trfLoc2H']; $trfClass = $_POST['trfClassH']; $trfRef = $_POST['trfRefH'];
		$responsible = $_POST['responsibleH']; $trfRemarks = $_POST['trfRemarksH'];
		$hashIte = $_POST['hashIteH']; $hashQty = $_POST['hashQtyH'];
		$prodIte=$_POST['prodIteH']; $prodQty=$_POST['prodQtyH']; $prodPrice=$_POST['prodPriceH']; $prodCost=$_POST['prodCostH'];
	}
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$transferDtl = mssql_query("SELECT * 
	FROM tblTransferHeader
	WHERE trfStatus='O' 
	AND compCode='$compcode'
	ORDER BY trfNumber ASC");
	
	echo "<select name='trfNo' class='textbox' tabindex='1' id='trfNo'>";
	echo "<option value=\"\"></option>";
	while ($transferDtlRow = mssql_fetch_assoc($transferDtl))
		{
		echo "<option value='".$transferDtlRow['trfNumber']."'"; 
		if(($transferDtlRow['trfNumber'])==($_GET['trfNo'])) 
			{ 
			echo "selected='selected'"; 
			} 
		echo "' onclick='displayItems()'>".strtoupper($transferDtlRow['trfNumber'])."</option>";
		}
	echo "</select>";
	
	}
////////////////////////////////////////////////////////////////////////////////////-----
function responsible()//////////////////////////////////////////////////////////////
	{
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	if($do=="newRecord"&&$responsible=="")
		{ echo "value='$compcode' disabled='disabled'"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&$responsible!="")
		{ echo "value='$responsible' disabled='disabled'"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "value='$responsible' disabled='disabled'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function remarks()//////////////////////////////////////////////////////////////////
	{
	
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$flag_qty_error=$_GET['flag_qty_error'];
	
	
	if($do=="newRecord"&&$trfRemarks!="")
		{ echo "value='$trfRemarks'"; }
	if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "value='$trfRemarks' disabled='disabled'"; }	
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "value='$trfRemarks' disabled='disabled'"; }	
	if(isset($_POST['button_transfer'])) { 
		$trfRemarks = $_POST['trfRemarksH'];
		echo "value='$trfRemarks'";	
	}
	
	}

////////////////////////////////////////////////////////////////////////////////////
function hashIte()//////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $hashIte=$_GET['hashIte'];
	if($do!="receiveRecord"&&$hashIte!="")
		{ echo "value='$hashIte' onclick='alert(\"\")'"; }
	else if($do=="receiveRecord"&&$while=="displayItems"&&$hashIte!="")
		{ echo "value='$hashIte' disabled='disabled'"; }
	if(isset($_POST['button_transfer'])) { 
		$hashIte = $_POST['hashIteH'];
		echo "value='$hashIte'";	
	}
	}
////////////////////////////////////////////////////////////////////////////////////
function enteredIte()///////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$countIte = mssql_fetch_assoc(mssql_query("SELECT COUNT(trfNumber)
	AS trfNumberCount
	FROM tblTransferDtl
	WHERE compCode='$compcode' 
	AND trfNumber='$trfNo'"));
	$count = 0;
	foreach($countIte as $countIteNew)
	/*while($countIteRow = mssql_fetch_assoc($countIte))
		{ 	$count = $count+1;
			$trfNumber = $countIteRow['trfNumber'];}*/
	echo "value='$countIteNew'";
	}
////////////////////////////////////////////////////////////////////////////////////
function hashQty()//////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $hashQty=$_GET['hashQty'];
	if($do!="receiveRecord"&&$hashQty!="")
		{ echo "value='$hashQty'"; }
	else if($do=="receiveRecord"&&$while=="displayItems"&&$hashQty!="")
		{ echo "value='$hashQty' disabled='disabled'"; }
	if(isset($_POST['button_transfer'])) { 
		$hashQty = $_POST['hashQtyH'];
		echo "value='$hashQty'";	
	}
	}
////////////////////////////////////////////////////////////////////////////////////
function enteredQty()
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass']; $trfRef = $_GET['trfRef'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$countQty = mssql_fetch_assoc(mssql_query("SELECT SUM(trfQtyOut)
	FROM tblTransferDtl
	WHERE compCode='$compcode' 
	AND trfNumber='$trfNo'"));
	$count = 0;
	foreach($countQty as $countQtyNew)
	echo "value='$countQtyNew'";
	}
////////////////////////////////////////////////////////////////////////////////////
function includeItem()//////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass']; $trfRef = $_GET['trfRef'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQtyIn=$_GET['prodQtyNone'];
	
	if($do=="newRecord"||$do=="updateRecord")
		{	echo "
			<tr bgcolor='#E9F8E8'>
			<td><center>--</center></td>
			<td><center>
			<input name='prodIte' type='text' class='pricePriceInput' id='prodIte' tabindex='1' maxlength='8'  value='$prodIte'"; includeIte(); 
			 echo ">
				</center></td>
			<td><input name='prodDesc' type='text' class='pricePriceDesc' id='prodDesc'"; includeDesc(); 
				echo "readonly='readonly' />
				</td>
			<td><center>
				<input name='prodUm' type='text' class='pricePriceUm' id='prodUm' maxlength='10' "; includeUm(); 
				echo "readonly='readonly' />
				</center></th>
			<td><center>
				<input name='prodQty' type='text' class='pricePriceInput' id='prodQty' maxlength='8'"; includeQtyO(); 
				echo">
				</center></td>
			<td><center>
				<input name='prodQtyNone' type='text' class='pricePricePrice' id='prodQtyNone' maxlength='8'"; includeQtyI(); 
				echo"disabled='disabled'>
				</center></td>
			<td><center>
				<input name='prodCost' type='text' id='prodCost' maxlength='10'"; includeCost(); 
				echo "readonly='readonly'>
				</center></td>
			<td><center>
				<input name='prodPrice' type='text' class='pricePricePrice' id='prodPrice' maxlength='10' "; includePrice(); 
				echo "readonly='readonly'>
				</center></td>
			<td><center><center/></td>
			<td><center><center/></td>";
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeLocation1()/////////////////////////////////////////////////////////
		{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];

	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	$location = mssql_query("SELECT * 
	FROM tblLocation
	WHERE locStat='A'
	AND compCode='$compcode'
	ORDER BY locCode ASC");	
	
	if($do==""||$while=="")
		{ echo "<select name='trfLoc1' class='textbox' tabindex='1' id='trfLoc1'>"; }
	else if($do=="newRecord"&&$while=="findIte")
		{ echo "<select name='trfLoc1' class='textbox' tabindex='1' id='trfLoc1'>"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "<select name='trfLoc1' class='textbox' tabindex='1' id='trfLoc1' disabled='disabled'>"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "<select name='trfLoc1' class='textbox' tabindex='1' id='trfLoc1' disabled='disabled'>"; }
	
	if(isset($_POST['button_transfer'])) { 
		$trfLoc1 = $_POST['trfLoc1H'];
		echo "<option selected>$trfLoc1</option>";	
	}
	
	echo "<option value=\"\"></option>";
	while ($locationRow = mssql_fetch_assoc($location)) {
		echo "<option value='".$locationRow['locCode']."'"; 
		if(($locationRow['locCode'])==($_GET['trfLoc1'])) { 
			echo "selected='selected'"; 
		} 		
		echo "'>".strtoupper($locationRow['locCode'])." - ".strtoupper($locationRow['locName'])."</option>";
	}
	echo "</select>";
	
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeLocation2()/////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	if($do=="receiveRecord"&&$while=="displayItems")
		{	$location = mssql_query("SELECT * 
			FROM tblLocation
			WHERE locStat='A' 
			AND compCode='$compcode'
			AND locCode!='$trfLoc1'
			ORDER BY locCode ASC");	}
	else
		{	$location = mssql_query("SELECT * 
			FROM tblLocation
			WHERE locStat='A' 
			AND compCode='$compcode'
			ORDER BY locCode ASC");	}

	
	if($do==""||$while=="")
		{ echo "<select name='trfLoc2' class='textbox' tabindex='1' id='trfLoc2'>"; }
	else if($do=="newRecord"&&$while=="findIte")
		{ echo "<select name='trfLoc2' class='textbox' tabindex='1' id='trfLoc2'>"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "<select name='trfLoc2' class='textbox' tabindex='1' id='trfLoc2' disabled='disabled'>"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "<select name='trfLoc2' class='textbox' tabindex='1' id='trfLoc2'>"; }
	
	if(isset($_POST['button_transfer'])) { 
		$trfLoc2 = $_POST['trfLoc2H'];
		echo "<option selected>$trfLoc2</option>";	
	}
	
	echo "<option value=\"\"></option>";
	while ($locationRow = mssql_fetch_assoc($location))
		{
		echo "<option value='".$locationRow['locCode']."'"; 
		if(($locationRow['locCode'])==($_GET['trfLoc2'])) 
			{ 
			echo "selected='selected'"; 
			} 
				
		echo "'>".strtoupper($locationRow['locCode'])." - ".strtoupper($locationRow['locName'])."</option>";
		}
	echo "</select>";
	
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeStatus()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if($do==""||$while=="")
		{ echo "<select name='status' class='textbox' tabindex='1' id='status'>
		<option value='O'"; if($status=="O") { echo "selected='selected'"; } echo ">Open</option>"; }
	else if($do=="newRecord"&&$while=="findIte")
		{ echo "<select name='status' class='textbox' tabindex='1' id='status'>
		<option value='O'"; if($status=="O") { echo "selected='selected'"; } echo ">Open</option>"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "<select name='status' class='textbox' tabindex='1' id='status' >
		<option value='O'"; if($status=="O") { echo "selected='selected'"; } echo ">Open</option>"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "<select name='status' class='textbox' tabindex='1' id='status' >
		<option value='O'"; if($status=="O") { echo "selected='selected'"; } echo ">Open</option>
		<option value='R' onclick='statusR()'"; if($status=="R") { echo "selected='selected'"; } echo ">Receive</option>"; }
	/*else if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "<select name='status' class='textbox' tabindex='1' id='status' >
		<option value='O'"; if($status=="O") { echo "selected='selected'"; } echo ">Open</option>
		<option value='R' onclick='statusR()'"; if($status=="R") { echo "selected='selected'"; } echo ">Receive</option>"; }*/
	
	echo "</select>";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeClass()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	$compcode=$company_code;
	
	$location = mssql_query("SELECT * 
	FROM tblLocation
	WHERE locStat='A' 
	ORDER BY locName ASC");
	
	if($do==""||$while=="")
		{ echo "<select name='trfClass' class='textbox' tabindex='1' id='trfClass'>"; }
	else if($do=="newRecord"&&$while=="findIte")
		{ echo "<select name='trfClass' class='textbox' tabindex='1' id='trfClass'>"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&($while=="newIte"||$while=="editItem"))
		{ echo "<select name='trfClass' class='textbox' tabindex='1' id='trfClass' disabled='disabled'>"; }
	else if($do=="receiveRecord"&&$while=="displayItems")
		{ echo "<select name='trfClass' class='textbox' tabindex='1' id='trfClass' disabled='disabled'>"; }
	
	if(isset($_POST['button_transfer'])) { 
		$trfClass = $_POST['trfClassH'];
		echo "<option selected>$trfClass</option>";	
	}
	
	echo "<option value=\"\"></option>
		<option value='1'"; if($trfClass=='1') { echo "selected='selected'"; } echo ">Good Order</option>
		<option value='2'"; if($trfClass=='2') { echo "selected='selected'"; } echo ">Bad Order</option>";
	echo "</select>";
	}
////////////////////////////////////////////////////////////////////////////////////
function includeDesc()//////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if(($do=="newRecord"||$do=="updateRecord")&&($prodIte!=""||$while=="editItem"))
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
	
		$findDesc = mssql_query("SELECT prdDesc
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findDescRow=mssql_fetch_assoc($findDesc))
			{	$prdDesc = strtoupper($findDescRow['prdDesc']); }
		echo "value=\"".$prdDesc."\"";
		
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function includeUm()////////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass']; 
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if(($do=="newRecord"||$do=="updateRecord")&&($prodIte!=""||$while=="editItem"))
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
		
		$findSellUnit = mssql_query("SELECT prdSellUnit
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findSellUnitRow=mssql_fetch_assoc($findSellUnit))
			{	$prdSellUnit = strtoupper($findSellUnitRow['prdSellUnit']); }
		echo "value='".$prdSellUnit."'";
		
		}
	}

function includeQtyO()///////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if($do=="newRecord"&&$prodIte!="")
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
	
		$findFrcTag = mssql_query("SELECT prdFrcTag
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findFrcTagRow=mssql_fetch_assoc($findFrcTag))
			{	$prdFrcTag = $findFrcTagRow['prdFrcTag'];}
		if($prdFrcTag=='Y') { echo "value='0.0000'";}
		else { echo "value='0'";}
		
		}
	else if(($do=="newRecord"||$do=="updateRecord")&&$prodIte!=""||$while=="editItem")
		{
		echo "value='$prodQty'";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function includeQtyI()///////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if($do=="newRecord"&&$prodIte!="")
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
	
		$findFrcTag = mssql_query("SELECT prdFrcTag
		FROM tblProdMast 
		WHERE prdNumber='$prodIte'");
		while($findFrcTagRow=mssql_fetch_assoc($findFrcTag))
			{	$prdFrcTag = $findFrcTagRow['prdFrcTag'];}
		if($prdFrcTag=='Y') { echo "value='0.0000' disabled='disabled'";}
		else { echo "value='0.0000' disabled='disabled'";}
		
		}
	else if(($do=="newRecord"||$do=="updateRecord")&&$prodIte!=""||$while=="editItem")
		{
		//echo "value='--$prodQty--' disabled='disabled'";
		echo "value='0.0000' disabled='disabled'";
		}
	//else if($do=="receiveRecord"&&$while=="displayItems")
	//	{
		//echo "value='--$prodQty--' disabled='disabled'";
		//echo "<div align='center'><input name='prodQtyIn' type='text' 
		//	class='pricePriceInput' id='prodQtyIn' maxlength='8' value='".$trfQtyOut."'></div>";
	//	}
	}
////////////////////////////////////////////////////////////////////////////////////
function includeCost()//////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if(($do=="newRecord"||$do=="updateRecord")&&$prodIte!=""||$while=="editItem")
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
		
		$findCost = mssql_query("SELECT aveUnitCost
		FROM tblAveCost 
		WHERE prdNumber='$prodIte'");
		while($findCostRow=mssql_fetch_assoc($findCost))
			{	$aveUnitCost = strtoupper($findCostRow['aveUnitCost']); }
		echo "value='".$aveUnitCost."' readonly='readonly' class='pricePricePrice'";		
		
		}
	else
		{ echo "class='pricePricePrice'";}
	}
////////////////////////////////////////////////////////////////////////////////////
function includePrice()/////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if(($do=="newRecord"||$do=="updateRecord")&&$prodIte!=""||$while=="editItem")
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
	
		$findPrice = mssql_query("SELECT regUnitPrice
		FROM tblProdPrice 
		WHERE prdNumber='$prodIte'");
		while($findPriceRow=mssql_fetch_assoc($findPrice))
			{	$regUnitPrice = strtoupper($findPriceRow['regUnitPrice']); }
		echo "value='".$regUnitPrice."'";
		
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includedItems()////////////////////////////////////////////////////////////
	{
	$do = $_GET['do']; $while=$_GET['while']; $signal=$_GET['signal'];
	$trfNo = $_GET['trfNo']; $docDate = $_GET['docDate']; 
	$trfLoc1 = $_GET['trfLoc1']; $trfLoc2 = $_GET['trfLoc2']; $trfClass = $_GET['trfClass'];
	$responsible = $_GET['responsible']; $trfRemarks = $_GET['trfRemarks'];
	$hashIte = $_GET['hashIte']; $hashQty = $_GET['hashQty'];
	$prodIte=$_GET['prodIte']; $prodQty=$_GET['prodQty']; $prodPrice=$_GET['prodPrice']; $prodCost=$_GET['prodCost'];
	$prodQty=$_GET['prodQtyNone'];
	if($do=="newRecord"||$do=="updateRecord")
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
		$findItems = mssql_query("SELECT *
			FROM tblTransferDtl
			WHERE compCode='$compcode'
			AND trfNumber='$trfNo'
			AND trfItemTag='O'
			ORDER BY prdNumber ASC");
			$counter = 0;
			while($findItemsRow=mssql_fetch_assoc($findItems))
				{	$counter = $counter + 1;
					$prdNumber = $findItemsRow['prdNumber']; 
					$umCode = $findItemsRow['umCode']; 
					$trfQtyOut = $findItemsRow['trfQtyOut']; 
					$trfQtyIn = $findItemsRow['trfQtyIn']; 
					$trfCost = $findItemsRow['trfCost'];
					$trfPrice = $findItemsRow['trfPrice'];
					$findDesc = mssql_query("SELECT prdDesc
					FROM tblProdMast 
					WHERE prdNumber='$prdNumber'");
					while($findDescRow=mssql_fetch_assoc($findDesc))
						{	$prdDesc = strtoupper($findDescRow['prdDesc']); }
	
		echo "<tr bgcolor='#E9F8E8'>
			<td><center>$counter</center></td>
			<td><center>$prdNumber</center></td>
			<td>$prdDesc</td>
			<td><center>$umCode</center></td>
			<td><div align='right'>$trfQtyOut</div></td>
			<td><div align='right'>$trfQtyIn</div></td>
			<td><div align='right'>$trfCost</div></td>
			<td><div align='right'>$trfPrice</div></td>
			<td><div align='right'>
			<a href='transfers_transaction.php?do=editItem&prdNumber=$prdNumber&trfNo=$trfNo&trfType=$trfType&docDate=$docDate&trfLoc1=$trfLoc1&trfLoc2=$trfLoc2&trfClass=$trfClass&trfRef=$trfRef&responsible=$responsible&trfRemarks=$trfRemarks&hashIte=$hashIte&hashQty=$hashQty'>edit</a> | ";
			
			if(mssql_num_rows($findItems)<=1)
				{ 	echo "---"; }
			else if(mssql_num_rows($findItems)>=2)
				{	echo "
					<a href='transfers_transaction.php?do=deleteItem&prdNumber=$prdNumber&trfNo=$trfNo&trfType=$trfType&docDate=$docDate&trfLoc1=$trfLoc1&trfLoc2=$trfLoc2&trfClass=$trfClass&trfRef=$trfRef&responsible=$responsible&trfRemarks=$trfRemarks&hashIte=$hashIte&hashQty=$hashQty'
							onclick='var deleteUM = confirm(\"This Item Record will be Deleted. Are you sure?\"); 
				if(deleteUM) { return true; } else { return false; }'><u>del</u></a>"; }
				echo "</div></td>
			<div align='center'>";
			
			echo "</div></td>
		  </tr>"; }
		}
		
	else if($do=="receiveRecord"&&$while=="displayItems")
		{
		include "../../functions/inquiry_session.php";
		require_once "../../includes/config.php";
		require_once "../../functions/db_function.php";
		require_once "../../functions/inquiry_function.php";
		$db = new DB;
		$db->connect();
		$compcode=$company_code;
		$findItems = mssql_query("SELECT *
			FROM tblTransferDtl
			WHERE compCode='$compcode'
			AND trfNumber='$trfNo'
			AND trfItemTag='O'
			ORDER BY prdNumber ASC");
			$counter = 0;
			while($findItemsRow=mssql_fetch_assoc($findItems))
				{	$counter = $counter + 1;
					$prdNumber = $findItemsRow['prdNumber']; 
					$umCode = $findItemsRow['umCode']; 
					$trfQtyOut = $findItemsRow['trfQtyOut']; 
					$trfQtyIn = $findItemsRow['trfQtyIn']; 
					$trfCost = $findItemsRow['trfCost'];
					$trfPrice = $findItemsRow['trfPrice'];
					$findDesc = mssql_query("SELECT prdDesc
					FROM tblProdMast 
					WHERE prdNumber='$prdNumber'");
					while($findDescRow=mssql_fetch_assoc($findDesc))
						{	$prdDesc = strtoupper($findDescRow['prdDesc']); }
	
		echo "<tr bgcolor='#E9F8E8'>
			<td><center>$counter</center></td>
			<td><div align='center'><input name='prodNumber[]' type='hidden' class='pricePricePrice' 
				id='prodNumber[]' value='$prdNumber' readonly='readonly'></div><center>$prdNumber</center></td>
			<td>$prdDesc</td>
			<td><center>$umCode</center></td>
			<td><div align='center'><input name='trfQtyOut[]' type='hidden' class='pricePricePrice' 
				id='trfQtyOut' value='$trfQtyOut' readonly='readonly'></div><div align='right'><font color='blue'>$trfQtyOut</font></div></td>
			<td><div align='right'>"; 
			//includeQtyI(); 
			echo "<div align='center'><input name='prodQtyIn[]' type='text'	class='pricePriceInput'
				id='prodQtyIn' value=''>";
		echo "</div></td>
			<td><div align='center'><input name='trfCost[]' type='hidden' class='pricePricePrice' 
				id='trfCost[]' value='$trfCost' readonly='readonly'></div><div align='right'>$trfCost</div></td>
			<td><div align='center'><input name='trfPrice[]' type='hidden' class='pricePricePrice' 
				id='trfCost[]' value='$trfPrice' readonly='readonly'></div><div align='right'>$trfPrice</div></td>
			<td><div align='right'>-</div></td>
		  </tr>"; 
			}
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function includeIte()///////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="newRecord"&&$while==""||$while=="findIte")
		{ echo "onchange='lookProduct()'"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&$while=="newIte")
		{ echo "onchange='lookNewProduct()'"; }
	else if(($do=="newRecord"||$do=="updateRecord")&&$while=="editItem")
		{ echo "disabled='disabled'"; }
	}
////////////////////////////////////////////////////////////////////////////////////-----
function toRelease()////////////////////////////////////////////////////////////////
	{
	$do=$_GET['do']; $while=$_GET['while'];
	if($do=="receiveRecord"&&$while=="displayItems")
		{	echo "
<table align='center' bordercolor='#E9F8E8' bgcolor='#E9F8E8'>
              <tr>
                <td width='50%'><b>
                  <center>
                    <div align='center'><b>Month</b></div>
                  </center>
                </b></td>
                <td width='50%'><b>
                  <center>
                    <div align='center'>Year</div>
                  </center>
                  </b><b></b></td>
              </tr>
              <tr>
                <td><b>
                  <center>
                    <div align='center'><b>"; dateTodayMonth();
                    echo "</b></div>
                  </center>
                </b></td>
                <td><b>
                  <center>
                    <div align='center'><b><b>"; dateTodayYear();
                    echo "</b></b></div>
                  </center>
                  </b><b>
                  <center>
                  </center>
                  </b></td>
              </tr>
            </table>";
		}
	}
////////////////////////////////////////////////////////////////////////////////////
?>
<script src="../../modules/inventory/calendar.js"></script>
<!--THIS IS THE CSS-->
<link rel='stylesheet' type='text/css' href='../../includes/style_1.css'></link>

<!-- message area -->
<div id='msg'>
  <? echo $message; ?>
</div>

<div id='frame_body'>
<!--THIS IS THE HEADER-->
<hr>
    <form method="post" name="" id="" onsubmit="return statusR(this)">
    <div class='header'> 
      <div class='details'> 
        <div class='header'> 
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="50%">&nbsp;</td>
              <td colspan="2"><div align='right'> 
                  <?php newRecord(); ?>
                  <?php updateRecord(); ?>
                  <?php deleteRecord(); ?>
                  <?php saveRecord(); ?>
                  <?php cancelButton(); ?>
                </div></td>
            </tr>
            <tr> 
              <td width="55%" rowspan="2" align="center" valign="top"><table width="85%" border="0">
                  <tr> 
                    <td width="30%"><b>Transfer No.</b></td>
                    <td width="20%"><b><b> 
                      <?php includeTransfers(); ?>
                      <input name="flag_qty_error" type="hidden" id="flag_qty_error" value="<?php echo $flag_qty_error; ?>">
                      </b></b></td>
                    <td width="30%"><b> 
                      <div align="right">Status</div>
                      </b></td>
                    <td colspan="2"><b><b><b> 
                      <?php includeStatus(); ?>
                      </b></b></b></td>
                  </tr>
                  <tr> 
                    <td><b>Person Responsible</b></td>
                    <td><b><b><b> 
                      <input type="text" class="textbox"  name="responsible" id="responsible" tabindex='1' maxlength="25" <?php responsible(); ?>/>
                      </b></b></b></td>
                    <td><b> 
                      <div align="right"><b>Date</b></div>
                      </b></td>
                    <td colspan="2"><b><b><b><b><b> 
                      <input type="text" class="textbox"  name="docDate" id="docDate" maxlength="11" <?php docDate(); ?>
					  
                <?php calendar(); ?><?php dateOneTodayStart(); ?>/>
                      </b></b></b></b></b></td>
                  </tr>
                  <tr> 
                    <td><b> From Location</b></td>
                    <td colspan="2"><b><b> <b> 
                      <?php includeLocation1(); ?>
                      </b></b></b></td>
                    <td width="20%">&nbsp;</td>
                    <td width="20%">&nbsp;</td>
                  </tr>
                  <tr> 
                    <td><b>To Location</b></td>
                    <td colspan="2"><b><b> <b> 
                      <?php includeLocation2(); ?>
                      </b></b></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td><b>Stock Tag</b></td>
                    <td> 
                      <?php includeClass(); ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr> 
                    <td><b>Remarks</b></td>
                    <td colspan="4"><b><b> 
                      <input type="text" class="textbox"  name="trfRemarks" id="trfRemarks" tabindex='1' maxlength="25" <?php remarks(); ?>/>
                      </b></b></td>
                  </tr>
                  <tr> 
                    <th colspan='5'></th>
                  </tr>
                </table></td>
              <td width="4%">&nbsp;</td>
              <td width="53%"><b><b> 
                <input  name="signal" type="hidden" class="textbox" id="signal" <?php signal(); ?>/>
                <b> 
                <input  name="prdFrcTag" type="hidden" class="textbox" id="prdFrcTag" <?php prdFrcTag(); ?>/>
                <b><b> 
                <input  name="whiles" type="hidden" class="textbox" id="whiles" <?php whiles(); ?>/>
                </b></b></b></b></b></td>
            </tr>
            <tr> 
              <td align="center" valign="top">&nbsp;</td>
              <td align="center" valign="bottom"> 
                <?php toRelease(); ?>
                <br /> <table width="85%" border="0" align="center" bgcolor="#E9F8E8">
                  <tr> 
                    <th><b> 
                      <div align="center">Control Totals</div>
                      </b></th>
                  </tr>
                  <tr> 
                    <td><table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#E9F8E8">
                        <tr> 
                          <td height="21">&nbsp;</td>
                          <td><div align="center"><b>Hash</b></div></td>
                          <td><div align="center"><b>Entered</b></div></td>
                          <td><div align="center"><b>Difference</b></div></td>
                        </tr>
                        <tr> 
                          <td><div align="center">Items </div></td>
                          <td><div align="center"> 
                              <input name="hashIte" type='text' class='pricePriceInput' id="hashIte"<?php hashIte(); ?> tabindex="1"/>
                            </div></td>
                          <td><div align="center"> 
                              <input name="enterIte" type='text' class='pricePrice' id="enterIte" <?php enteredIte(); ?>
                      readonly='readonly'/>
                            </div></td>
                          <td><div align="center"> 
                              <input name="diffIte" type='text' class='pricePrice' id="diffIte" readonly='readonly'/>
                            </div></td>
                        </tr>
                        <tr> 
                          <td><div align="center">Quantity</div></td>
                          <td><div align="center"> 
                              <input name="hashQty" type='text' class='pricePriceInput' id="hashQty" <?php hashQty(); ?> tabindex="1"/>
                            </div></td>
                          <td><div align="center"> 
                              <input name="enterQty" type='text' class='pricePrice' id="enterQty" <?php enteredQty(); ?> readonly='readOnly'/>
                            </div></td>
                          <td><div align="center"> 
                              <input name="diffQty" type='text' class='pricePrice' id="diffQty" readonly='readonly'/>
                            </div></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr> 
                    <th></th>
                  </tr>
                </table></td>
            </tr>
          </table>
        </div>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <table width="100%" align="center" bordercolor="#E9F8E8" bgcolor="#E9F8E8">
          <tr> 
            <th>No</th>
            <th>Product Code</th>
            <th>Description</th>
            <th>UM</th>
            <th>Qty Out</th>
            <th>Qty In</th>
            <th>Unit Cost</th>
            <th>Unit Price</th>
            <th>&nbsp;</th>
          </tr>
          <?php includeItem(); ?>
          <tr> 
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
          <?php includedItems(); ?>
        </table>
      </div>
    </div>
<!--Standard Info-->
    <div class='details'></div>
<!--SAMPLE FROM-->
</form>
</div>

<!-- footer - status/message -->
<!--
<div id='footer'> 
  <? echo $message;?>
</div>
-->
<!--setfocus on reference search box-->
<form action="" method="post" name="form_transfer" id="form_transfer">
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font>
  <?
if(isset($_POST['button_transfer'])) { 
	if ($text_transfer=="") {
		echo "<script>alert('Key-in Product Number or Description!')</script>";
	} else {
		
		if ($text_transfer<>"*") {
			if(is_numeric($text_transfer)) {
				
				$query_find_transfer="SELECT * FROM tblProdmast
								WHERE (prdDelTag = 'A') AND prdNumber LIKE '%$text_transfer%'
								ORDER BY prdNumber ASC";
				$result_find_transfer=mssql_query($query_find_transfer);
				$num_find_transfer = mssql_num_rows($result_find_transfer);
				if ($num_find_transfer>0) {
					$transfer_no=mssql_result($result_find_transfer,0,"prdNumber");
					$transfer_date=mssql_result($result_find_transfer,0,"prdDesc");
					$combo_transfer= $transfer_no." - ".$transfer_date;
					
				} else {
					echo "<script>alert('No product records found!')</script>";
				}
			} else { 
				$query_find_transfer="SELECT * FROM tblProdmast
								WHERE (prdDelTag = 'A') AND prdDesc LIKE '%$text_transfer%'
								ORDER BY prdDesc ASC";
				$result_find_transfer=mssql_query($query_find_transfer);
				$num_find_transfer = mssql_num_rows($result_find_transfer);
				if ($num_find_transfer>0) {
					$transfer_no=mssql_result($result_find_transfer,0,"prdNumber");
					$transfer_date=mssql_result($result_find_transfer,0,"prdDesc");
					$combo_transfer= $transfer_no." - ".$transfer_date;
				} else {
					echo "<script>alert('No product records found!')</script>";
				}
			}
		 }
	}
}
//find_transfer_function($text_transfer); /// inquiry_function.php
if (($text_transfer=="") || ($text_transfer=="Transfer No or Date")) { // display record in transfer #############################################################
	if ($hide_find_transfer=="") {
		$query_transfer="SELECT * FROM tblProdmast WHERE prdDelTag = 'Z'";
	} else {
		if ($hide_num_transfer=="YES") {
			$query_transfer="SELECT * FROM tblProdmast 
			WHERE (prdDelTag = 'A') AND (prdNumber LIKE '%$hide_find_transfer%')
			ORDER BY prdNumber ASC";	
		} else {
			$query_transfer="SELECT * FROM tblProdmast 
			WHERE (prdDelTag = 'A') AND (prdDesc LIKE '%$hide_find_transfer%')
			ORDER BY prdDesc ASC";
		}
	}
} else {
	if ($text_transfer=="*") {
		$query_transfer="SELECT * FROM tblProdmast WHERE (prdDelTag = 'A') ORDER BY prdDesc ASC";
		$hide_find_transfer="";
	} else {
		if(is_numeric($text_transfer)) {
			$query_transfer="SELECT * FROM tblProdmast 
			WHERE (prdDelTag = 'A') AND (prdNumber LIKE '%$text_transfer%')
			ORDER BY prdNumber ASC";
			$hide_num_transfer="YES";
		} else {
			$query_transfer="SELECT * FROM tblProdmast 
			WHERE (prdDelTag = 'A') AND (prdDesc LIKE '%$text_transfer%')
			ORDER BY prdDesc ASC";
			$hide_num_transfer="NO";
		}
		$hide_find_transfer=$text_transfer;
	}
}
$result_transfer=mssql_query($query_transfer);
$num_transfer = mssql_num_rows($result_transfer);
?>
  <div id="Layer1" style="position:absolute; left:19px; top:244px; width:527px; height:68px; z-index:1"> 
    <fieldset>
    <legend><font size="-2">Search Product</font></legend>
    <font size="2" face="Arial, Helvetica, sans-serif"> 
    <legend></legend>
    <font size="2" face="Arial, Helvetica, sans-serif"> 
    <select name="combo_transfer" id="select3" style="width:200px; height:20px;" onChange="move_transfer_number()">
      <option selected><? echo $combo_transfer; ?></option>
      <?
				for ($i=0;$i<$num_transfer;$i++){  
					$combo_trans_no=mssql_result($result_transfer,$i,"prdNumber"); 
					$combo_trans_date=mssql_result($result_transfer,$i,"prdDesc"); 		
				?>
      <option><? echo $combo_trans_no." - ".$combo_trans_date; ?></option>
      <? } ?>
      <option> </option>
    </select>
    <input name="text_transfer" type="text" id="text_transfer" onFocus="if(this.value=='Product No or Desc')this.value='';" value="Product No or Desc">
    <input name='button_transfer' type='submit' class='queryButton' id='continue' title='Search Products' onClick="move_transfer_number(); javascript:document.form_transfer.submit(); " value='Find'/>
    <span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
    <input name="hide_find_transfer" type="hidden" id="cbqtybo" value="<?php echo $hide_find_transfer; ?>">
    <input name="hide_num_transfer" type="hidden" id="hide_find_product" value="<?php echo $hide_num_transfer; ?>">
    </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
    </font> 
    </fieldset>
  </div>
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  <input name="docDateH" type="hidden" id="docDate22">
  <input name="trfLoc1H" type="hidden" id="trfLoc12">
  <input name="trfLoc2H" type="hidden" id="trfLoc22">
  <input name="trfClassH" type="hidden" id="trfClass2">
  <input name="responsibleH" type="hidden" id="responsible22">
  <input name="trfRemarksH" type="hidden" id="trfRemarks22">
  <input name="hashIteH" type="hidden" id="hashIte22">
  <input name="hashQtyH" type="hidden" id="hashQty22">
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
  <font size="2" face="Arial, Helvetica, sans-serif"><span class="style20"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><span class="style18"><b><b><span class="style1"><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b><b> 
  </b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></b></b></span></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></b></span></font> 
</form>
<p>&nbsp;</p>
<p>
  <script type="text/javascript">
////////////////////////////////////////////////////////////////////////////////////-----
var signal = document.getElementById('signal').value;
var	hashIte = document.getElementById('hashIte').value;
var	hashQty = document.getElementById('hashQty').value;
var	enterIte = document.getElementById('enterIte').value;
var	enterQty = document.getElementById('enterQty').value;
document.getElementById('diffIte').value =  hashIte - enterIte;
document.getElementById('diffQty').value =   hashQty - enterQty;
if(signal=="")
	{
	document.getElementById('trfNo').focus();
	//document.getElementById('trfNo').select();
	document.getElementById('status').disabled=true;
	document.getElementById('docDate').disabled=true;
	document.getElementById('trfLoc1').disabled=true;
	document.getElementById('trfLoc2').disabled=true;
	document.getElementById('trfClass').disabled=true;
	document.getElementById('responsible').disabled=true;
	document.getElementById('trfRemarks').disabled=true;
	document.getElementById('hashIte').readOnly=true;
	document.getElementById('hashQty').readOnly=true;
	}
else if(signal=="receiveRecord")
	{
	document.getElementById('docDate').disabled=true;
	document.getElementById('trfLoc1').disabled=true;
	document.getElementById('trfClass').disabled=true;
	document.getElementById('trfLoc2').disabled=true;
	document.getElementById('responsible').disabled=true;
	document.getElementById('trfRemarks').disabled=true;
	document.getElementById('hashIte').readOnly=true;
	document.getElementById('hashQty').readOnly=true;
	var trfNo = document.getElementById('trfNo').value;
		if(trfNo=="")
			{
			document.getElementById('trfNo').focus();
			//document.getElementById('trfNo').select();
			document.getElementById('trfLoc2').disabled=true;
			}
		else
			{
			document.getElementById('prodQtyIn').focus();
			document.getElementById('prodQtyIn').select();
			document.getElementById('trfLoc2').disabled=false;
			}
	var whiles = document.getElementById('whiles').value;
		if(whiles!="displayItems")
			{
			document.getElementById('status').disabled=true;
			}
	}

var prodIte = document.getElementById('prodIte').value;
var prodDesc = document.getElementById('prodDesc').value;


if(prodIte=="")
	{
	document.getElementById('prodIte').focus();
	document.getElementById('prodIte').select();
	}
else if(prodIte!=""&&prodDesc!="")
	{
	document.getElementById('prodQty').focus();
	document.getElementById('prodQty').select();
	}

////////////////////////////////////////////////////////////////////////////////////-----
function newRecord()////////////////////////////////////////////////////////////////
	{
	window.location="transfers_transaction.php?do=newRecord";
	}
////////////////////////////////////////////////////////////////////////////////////-----
function canc()/////////////////////////////////////////////////////////////////////
	{
	var cancelnow = confirm("Are you sure you want to cancel?");
	if(cancelnow)
		{
		window.location="trf_main.php";
		return true;	
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function updateRecord()/////////////////////////////////////////////////////////////
	{
	var numericExpression = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').value;
	window.location = "transfers_transaction.php?do=updateRecord&while=newIte&trfNo="+trfNo;
	}
////////////////////////////////////////////////////////////////////////////////////-----
function saveRecordNow()////////////////////////////////////////////////////////////
	{
	var alphaExpression = /^[a-zA-Z]+$/;
	var numericExpression = /^[0-9]+$/;
	var numeric = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodUm = document.getElementById('prodUm').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var prdFrcTag = document.getElementById('prdFrcTag').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	if(trfNo=="")
		{
		alert("Transfer Number is missing");
		document.getElementById('trfNo').focus();
		//document.getElementById('trfNo').select();
		return false;
		}
	else if(trfLoc1=="")
		{
		alert("Location: Required");
		document.getElementById('trfLoc1').focus();
		document.getElementById('trfLoc1').select();
		return false;
		}
	else if(trfLoc2=="")
		{
		alert("From/To Location: Required");
		document.getElementById('trfLoc2').focus();
		document.getElementById('trfLoc2').select();
		return false;
		}
	else if(trfLoc1==trfLoc2)
		{
		alert("From Origin to Destination: Must not be the same Location");
		document.getElementById('trfLoc2').focus();
		document.getElementById('trfLoc2').select();
		return false;
		}
	else if(trfClass=="")
		{
		alert("Transfer Class: Required");
		document.getElementById('trfClass').focus();
		document.getElementById('trfClass').select();
		return false;
		}
	else if(trfRemarks=="")
		{
		alert("Remarks: Required");
		document.getElementById('trfRemarks').focus();
		document.getElementById('trfRemarks').select();
		return false;
		}
	else if(hashIte=="")
		{
		alert("Hash Item: Required");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashIte.match(alphaExpression))
		{
		alert("Hash Item: Require Numeric only");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashQty==""||hashQty==0)
		{
		alert("Hash Quantity: Required");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(hashQty.match(alphaExpression))
		{
		alert("Hash Quantity: Require Numeric only");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(prodIte=="")
		{
		alert("Product Item Code: Required");
		document.getElementById('prodIte').focus();
		document.getElementById('prodIte').select();
		return false;
		}
	else if(prodQty==""||prodQty==0)
		{
		alert("Product Quantity: Required");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(prodQty<=0.00009)
		{
		alert("Product Quantity: Four Decimals Places Only");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(prodQty.match(alphaExpression)&&(prdFrcTag=="N"||prdFrcTag=="Y"))
		{
		alert("Product Quantity: Require Numeric only");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(!prodQty.match(numericExpression)&&prdFrcTag=="N")
		{
		alert("Product Quantity: Numerical with Decimal Value is not allowed");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(trfClass=="2")
		{
		var badOrder = confirm("Are you sure this Transfer Record is Bad Order?");
		if(badOrder)
			{
			window.location = "transfers_transaction.php?do=newRecord&while=saveRecordNow&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodUm="+prodUm+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
			}
		else
			{
			document.getElementById('trfClass').focus();
			document.getElementById('trfClass').select();
			return false;
			}
		}
	else
		{
		window.location = "transfers_transaction.php?do=newRecord&while=saveRecordNow&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodUm="+prodUm+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function saveNewRecordNow()/////////////////////////////////////////////////////////
	{
	var alphaExpression = /^[a-zA-Z]+$/;
	var numericExpression = /^[0-9]+$/;
	var numeric = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodUm = document.getElementById('prodUm').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var prdFrcTag = document.getElementById('prdFrcTag').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	if(trfNo=="")
		{
		alert("Transfer Number is missing");
		document.getElementById('trfNo').focus();
		//document.getElementById('trfNo').select();
		return false;
		}
	else if(trfLoc1=="")
		{
		alert("Location: Required");
		document.getElementById('trfLoc1').focus();
		document.getElementById('trfLoc1').select();
		return false;
		}
	else if(trfLoc2=="")
		{
		alert("From/To Location: Required");
		document.getElementById('trfLoc2').focus();
		document.getElementById('trfLoc2').select();
		return false;
		}
	else if(trfClass=="")
		{
		alert("Product Class: Required");
		document.getElementById('trfClass').focus();
		document.getElementById('trfClass').select();
		return false;
		}
	else if(trfRemarks=="")
		{
		alert("Remarks: Required");
		document.getElementById('trfRemarks').focus();
		document.getElementById('trfRemarks').select();
		return false;
		}
	else if(hashIte=="")
		{
		alert("Hash Item: Required");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashIte.match(alphaExpression))
		{
		alert("Hash Item: Require Numeric only");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashQty=="")
		{
		alert("Hash Quantity: Required");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(hashQty.match(alphaExpression))
		{
		alert("Hash Quantity: Require Numeric only");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(prodIte=="")
		{
		alert("Product Item Code: Required");
		document.getElementById('prodIte').focus();
		document.getElementById('prodIte').select();
		return false;
		}
	else if(prodQty==""||prodQty==0)
		{
		alert("Product Quantity: Required");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(prodQty.match(alphaExpression)&&(prdFrcTag=="N"||prdFrcTag=="Y"))
		{
		alert("Product Quantity: Require Numeric only");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(!prodQty.match(numericExpression)&&prdFrcTag=="N")
		{
		alert("Product Quantity: Numerical with Decimal Value is not allowed");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else
		{
		window.location = "transfers_transaction.php?do=newRecord&while=saveNewRecordNow&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodUm="+prodUm+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function saveChangesRecord()////////////////////////////////////////////////////////
	{
	var alphaExpression = /^[a-zA-Z]+$/;
	var numericExpression = /^[0-9]+$/;
	var numeric = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').focus();
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;

	if(hashIte=="")
		{
		alert("Hash Item: Required");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashIte.match(alphaExpression))
		{
		alert("Hash Item: Require Numeric only");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashQty=="")
		{
		alert("Hash Quantity: Required");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(hashQty.match(alphaExpression))
		{
		alert("Hash Quantity: Require Numeric only");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else
		{
		window.location = "transfers_transaction.php?do=saveChangesRecord&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function updateItemDetails()////////////////////////////////////////////////////////
	{
	var alphaExpression = /^[a-zA-Z]+$/;
	var numericExpression = /^[0-9]+$/;
	var numeric = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodUm = document.getElementById('prodUm').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodQtyIn = document.getElementById('prodQtyNone').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var prdFrcTag = document.getElementById('prdFrcTag').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	if(hashIte=="")
		{
		alert("Hash Item: Required");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashIte.match(alphaExpression))
		{
		alert("Hash Item: Require Numeric only");
		document.getElementById('hashIte').focus();
		document.getElementById('hashIte').select();
		return false;
		}
	else if(hashQty=="")
		{
		alert("Hash Quantity: Required");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(hashQty.match(alphaExpression))
		{
		alert("Hash Quantity: Require Numeric only");
		document.getElementById('hashQty').focus();
		document.getElementById('hashQty').select();
		return false;
		}
	else if(prodIte=="")
		{
		alert("Product Item Code: Required");
		document.getElementById('prodIte').focus();
		document.getElementById('prodIte').select();
		return false;
		}
	else if(prodQty==""||prodQty==0)
		{
		alert("Product Quantity: Required");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(prodQty.match(alphaExpression)&&(prdFrcTag=="N"||prdFrcTag=="Y"))
		{
		alert("Product Quantity: Require Numeric only");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else if(!prodQty.match(numericExpression)&&prdFrcTag=="N")
		{
		alert("Product Quantity: Numerical with Decimal Value is not allowed");
		document.getElementById('prodQty').focus();
		document.getElementById('prodQty').select();
		return false;
		}
	else
		{
		window.location = "transfers_transaction.php?do=updateRecord&while=saveItemDetails&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodUm="+prodUm+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost+"&prodQtyIn="+prodQtyIn;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function lookProduct()//////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var trfNo = document.getElementById('trfNo').focus();
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	if(!prodIte.match(numericExpression))
		{
		alert("Item Code: Numbers only");
		document.getElementById('prodIte').focus();
		document.getElementById('prodIte').select();
		return false;
		}
	else
		{
		window.location = "transfers_transaction.php?do=newRecord&while=findIte&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
		return true;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function lookNewProduct()///////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var trfNo = document.getElementById('trfNo').focus();
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	if(!prodIte.match(numericExpression))
		{
		alert("Item Code: Numbers only");
		document.getElementById('prodIte').focus();
		document.getElementById('prodIte').select();
		return false;
		}
	else
		{
		window.location = "transfers_transaction.php?do=newRecord&while=newIte&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
		return true;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function deleteRecord()/////////////////////////////////////////////////////////////
	{
	var numericExpression = /^[0-9]+$/;
	var trfNo = document.getElementById('trfNo').value;
	var deleteConfirm = confirm("This Transfer Document will be deleted. Are you sure?");
	if(deleteConfirm)
		{
		window.location = "transfers_transaction.php?do=deleteRecord&trfNo="+trfNo;
		}
	}
////////////////////////////////////////////////////////////////////////////////////-----
function refTrans()/////////////////////////////////////////////////////////////////
	{
	var alphaExpression = /^[a-zA-Z]+$/;
	var numericExpression = /^[0-9]+$/;
	var numeric = /^(\d+\.\d{0,4}|\d+)$/;
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodIte = document.getElementById('prodIte').value;
	var prodUm = document.getElementById('prodUm').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var prdFrcTag = document.getElementById('prdFrcTag').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	window.location = "transfers_transaction.php?do=newRecord&while=refTrans&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
	}
////////////////////////////////////////////////////////////////////////////////////-----
function statusR(form)//////////////////////////////////////////////////////////////
	{
	var trfNo = document.getElementById('trfNo').value;
	var status = document.getElementById('status').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var thisMonth = document.getElementById('thisMonth').value;
	var trfClass = document.getElementById('trfClass').value;
	var thisYear = document.getElementById('thisYear').value;
	var responsible = document.getElementById('responsible').value;
	var diffIte = document.getElementById('diffIte').value;
	var diffQty = document.getElementById('diffQty').value;
	var prodQtyIn = document.getElementById('prodQtyIn').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	var flag_qty_error = document.getElementById('flag_qty_error').value;
	//var prodQtyInVal = document.getElementsByName('prodQtyIn[]');
	var hasValue = false;

	if(trfNo=="")
		{
		alert("Transfer Number is missing");
		document.getElementById('trfNo').focus();
		//document.getElementById('trfNo').select();
		return false;
		}
	else if(trfLoc2=="")
		{
		alert("From/To Location: Required");
		document.getElementById('trfLoc2').focus();
		document.getElementById('trfLoc2').select();
		return false;
		}
	else if(trfLoc1==trfLoc2)
		{
		alert("From Origin to Destination: Must not be the same Location");
		document.getElementById('trfLoc2').focus();
		document.getElementById('trfLoc2').select();
		return false;
		}

	var released = confirm("Are you sure you want to receive this Transfer Document?");
	if(released)
		{
		form.action ="transfers_transaction.php?&do=receiveRecord&while=saveInRecordNow&trfNo="+trfNo+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&thisMonth="+thisMonth+"&thisYear="+thisYear+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&flag_qty_error="+flag_qty_error;
		return true;
		}
	else
		{
		document.getElementById('prodQtyIn').focus();
		document.getElementById('prodQtyIn').select();
		return false;
		}
	}
////////////////////////////////////////////////////////////////////////////////////
function displayItems()/////////////////////////////////////////////////////////////
	{
	var trfNo = document.getElementById('trfNo').value;
	window.location="transfers_transaction.php?do=receiveRecord&while=displayItems&trfNo="+trfNo;
	return true;
	}
////////////////////////////////////////////////////////////////////////////////////
function val_qty() {
	///"-" = separator
	var pQtyIn = document.getElementById('prodQtyIn').value;
	var pQtyOut = document.getElementById('trfQtyOut').value;
	
	alert(pQtyIn[0]);
	
	/*alert(pQtyOut);
	if(pQtyIn!=pQtyOut) {
		var cancelnow = confirm("Quantity In is not equal to Quantity Out. Are you sure you want to continue?");
	}
	
	if(cancelnow) {
		document.getElementById('prodQtyIn').value = "";
		document.getElementById('prodQtyIn').focus();
		return false;
	} */
}
function move_transfer_number($docDate) {
	var numericExpression = /^[0-9]+$/;
	var trfNo = document.getElementById('trfNo').focus();
	var trfNo = document.getElementById('trfNo').value;
	var docDate = document.getElementById('docDate').value;
	var trfLoc1 = document.getElementById('trfLoc1').value;
	var trfLoc2 = document.getElementById('trfLoc2').value;
	var trfClass = document.getElementById('trfClass').value;
	var responsible = document.getElementById('responsible').value;
	var trfRemarks = document.getElementById('trfRemarks').value;
	var prodQty = document.getElementById('prodQty').value;
	var prodPrice = document.getElementById('prodPrice').value;
	var prodCost = document.getElementById('prodCost').value;
	var hashIte = document.getElementById('hashIte').value;
	var hashQty = document.getElementById('hashQty').value;
	document.form_transfer.docDateH.value=docDate;
	document.form_transfer.trfLoc1H.value=trfLoc1;
	document.form_transfer.trfLoc2H.value=trfLoc2;
	document.form_transfer.trfClassH.value=trfClass;
	document.form_transfer.responsibleH.value=responsible;
	document.form_transfer.trfRemarksH.value=trfRemarks;
	document.form_transfer.hashIteH.value=hashIte;
	document.form_transfer.hashQtyH.value=hashQty;
	var combo_string = document.form_transfer.combo_transfer.value;
	var stringlen=combo_string.length;
	var i = 0;
	stringlen=stringlen-1;
	var stringnew="";
	for (i=0;i<=stringlen;i++){
		if (combo_string[i]!="-") { 
			stringnew=stringnew+combo_string[i];
		} else {
			break;
		}
	}
	stringnew=stringnew*1;
	document.getElementById('prodIte').value=stringnew;
	var prodIte = stringnew;
	window.location = "transfers_transaction.php?do=newRecord&while=findIte&trfNo="+trfNo+"&docDate="+docDate+"&trfLoc1="+trfLoc1+"&trfLoc2="+trfLoc2+"&trfClass="+trfClass+"&responsible="+responsible+"&trfRemarks="+trfRemarks+"&hashIte="+hashIte+"&hashQty="+hashQty+"&prodIte="+prodIte+"&prodQty="+prodQty+"&prodPrice="+prodPrice+"&prodCost="+prodCost;
	return true;
}
</script>
</p>

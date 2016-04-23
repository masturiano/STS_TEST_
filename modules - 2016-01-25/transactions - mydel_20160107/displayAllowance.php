<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("allowanceObj.php");
$allowanceObj = new allowanceObj();


$now = date('Y-m-d H:i:s');
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}
switch($_GET['action']){	
	case 'Load':
		$page = $_GET['page']; // get the requested page 
		$limit = $_GET['rows']; // get how many rows we want to have into the grid 
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
		$sord = $_GET['sord']; // get the direction 
		if(!$sidx) $sidx =1; 
		
		$totJob = $allowanceObj->countRegSTS();
		$count = $totJob['count']; 
		if( (int)$count>0 ) { 
			$total_pages = ceil($count/$limit); 
		} else { 
			$total_pages = 0; 
		} 
		
		if ($page > $total_pages) 
			$page=$total_pages; 
		$start = ($limit*$page) - ($limit); // do not put $limit*($page - 1) 
		
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		
		if($_GET['_search']=='true'){
			$arrSTS= $allowanceObj->searchDispSTS($sidx,$sord,$start,$limit,$_GET['searchField'],$_GET['searchString']);
		}else{	
			$arrSTS = $allowanceObj->getPaginatedDispSTS($sidx,$sord,$start,$limit);
		}
		$i = 0;
		foreach($arrSTS as $val){
			$response->rows[$i]['id']=$val['stsRefNo']; 
			if(date('m/d/Y',strtotime($val['dateApproved'])) == '01/01/1970'){
				$date = '-';
			}else{
				$date = date('m/d/Y',strtotime($val['dateApproved']));	
			}
			$response->rows[$i]['cell']=array($val['stsRefNo'],$val['suppName'],$val['stsRemarks'], date('m/d/Y',strtotime($val['dateEntered'])), $date,$val['stsStat']);
			$i++;
		}
		echo json_encode($response);
	exit();
	break;
	
	case 'searchSupplier':
		$arrResult = array();
		$arrSupp = $allowanceObj->findSupplier($_GET['term']);
		foreach($arrSupp as $val){
			$arrResult[] = array("id"=>$val['suppCode']."|".$val['contactPerson'], "label"=>$val['suppCode']." - ".str_replace("-",'-',$val['suppName']), "value" => strip_tags($val['suppName']));	
		}
		echo json_encode($arrResult);
	exit();	
	break;
	
	case 'cmbClass':
		$allowanceObj->DropDownMenu($allowanceObj->makeArr($allowanceObj->findClass($_GET['dept']),'stsTransTypeClass','stsTransTypeName',''),'cmbClass','','class="selectBox" onchange="loadSubClass(this.value);"');
	exit();
	break;

	case 'cmbSubClass':
		$allowanceObj->DropDownMenu($allowanceObj->makeArr($allowanceObj->findSubClass($_GET['dept'],$_GET['class']),'stsTransTypeSClass','stsTransTypeName',''),'cmbSubClass','','class="selectBox" ');
	exit();
	break;
	
	case 'cmbStore':
		$allowanceObj->DropDownMenu($allowanceObj->makeArr($allowanceObj->findStore($_GET['compCode']),'brnCode','brnDesc',''),'cmbStore','','class="selectBox" ');
	exit();
	break;
	case 'saveHdr':
		if($allowanceObj->saveHeader($_GET)){
			$lastQuery = $allowanceObj->getLastSTSInserted();
			echo "$('#buttonHolder').html(\"<button id='updateHdr' name='updateHdr' onClick='updateHdr();'>Update Header</button>\");";
			echo "$('#updateHdr').button({
					icons: {
						primary: 'ui-icon-disk',
					}
				});";
			echo "$('#txtRefNo').html('{$lastQuery['stsRefNo']}');";
			echo "$('#hdnRefNo').val('{$lastQuery['stsRefNo']}');";
			echo "$('#regSTSDetail').removeAttr('disabled');";
			echo "$('#regSTSDetail').attr('onClick','AddSTSDtl();');";
			echo "LoadData('displayAllowance.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+{$lastQuery['stsRefNo']});";
			echo "dialogAlert('STS Header Successfully Saved!');";
		}else{
			echo "dialogAlert('There was an error in adding STS');";
		}
	exit();
	break;
	
	case 'getInfo':
		$val = $allowanceObj->getRegSTSInfoAssoc($_GET['refNo']);
		
		echo "$('#txtRefNo').html('{$val['stsRefno']}');\n";
		echo "$('#txtSTSAmount').val('{$val['stsAmt']}');\n";
		echo "$('#txtRemarks').val('{$val['stsRemarks']}');\n";
		echo "$('#hdnSuppCode').val('{$val['suppCode']}');\n";
		echo "$('#txtSupp').val('".addslashes($val['suppName'])."');\n";
		echo "$('#cmbPayType').val('{$val['stsPaymentMode']}');\n";
		echo "$('#txtNoApplications').val('{$val['nbrApplication']}');\n";
		echo "$('#txtApDate').val('".date('m/d/Y',strtotime($val['applyDate']))."');\n";
		echo "$('#txtRep').val('{$val['contactPerson']}');\n";
		echo "$('#txtRepPos').val('{$val['contactPersonPos']}');\n";
		
		if($val['vatTag']=='Y'){
			echo "$('#ckVat').attr('checked', true);";	
		}else{
			echo "$('#ckVat').attr('checked', false);";	
		}
		
	exit();
	break;
	
	case 'loadEnhancerDetails':
		$arr = $allowanceObj->getEnhancerDetails($_GET['refNo']);
		echo "
		<table cellpadding='0' cellspacing='0'  border='0' class='display' id='enhancerDetailList'>
			<thead> 
			  <tr>
			  	<th>Catergory</th>
		        <th>Brand</th>
				<th>Remarks</th>
				<th>Action</th>
			  </tr>
			</thead>
			";	  
			foreach($arr as $val){ 
			  echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeK'  align='center'>
			  	<td width='20%'>".$val['category']."</td>
				<td width='20%'>".$val['stsBrandDesc']."</td>
				<td width='40%'>".$val['brandRem']."</td>
				<td width='20%'>
					<span class='link2' onclick=\"editEnhancer('{$val['stsRefno']}','{$val['brandCode']}','{$val['category']}');\">[ Edit ]</span>
					<span class='link2' onclick=\"deleteEnhancer('{$val['stsRefno']}','{$val['brandCode']}','{$val['category']}');\">[ Delete ]</span>
				</td>
				";
			echo "</tr>";
			  }
			echo "</tbody>
				  </table>";
	exit();
	break;
	case 'LoadSTSDetails':
		$arr = $allowanceObj->getRegSTSDetails($_GET['refNo']);
		$stsAmt = $allowanceObj->getStsHdrAmt($_GET['refNo']);
		echo "<script type='text/javascript'>$('#txtSTSAmount').val('".$stsAmt."');</script>\n";
		
		if($arr[0]['stsRefno']==''){
			echo "<script type='text/javascript'>$('#regSTSDetail').html('Add Detail');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').attr('onClick','AddSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').removeAttr('onClick','DeleteSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').attr('disabled','true');</script>\n";
			echo "<script type='text/javascript'>$('#addEnhancer').attr('disabled','true');</script>\n";
			echo "<script type='text/javascript'>$('#addEnhancer').removeAttr('onClick','addEnhancer();');</script>\n";
		}else{
			echo "<script type='text/javascript'>$('#regSTSDetail').html('Edit Detail');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').attr('onClick','AddSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').attr('onClick','DeleteSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#addEnhancer').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#addEnhancer').attr('onClick','addEnhancer();');</script>\n";
		
		}
		echo "
		<table cellpadding='0' cellspacing='0'  border='0' class='display' id='STSDetailsList'>
			<thead> 
			  <tr>
		        <th>Branch</th>
		        <th>STS Amount</th>
		        
			  </tr>
			</thead>
			";	  
			foreach($arr as $val){ 
			  echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeK'  align='center'>
				<td align=\"left\">".$val['brnDesc']."</td>
				<td >".number_format($val['stsAmt'],2)."</td>
				
				";
			echo "</tr>";
			  }
			echo "</tbody>
				  </table>";
	exit();
	break;
	
	case 'UpdateHdr':
		if($allowanceObj->updateHeader($_GET)){
			echo "dialogAlert('STS successfully Updated!');";
		}else{
			echo "dialogAlert('There was an error in Updating the STS');";
		}
	exit();
	break;
	
	case 'DeleteSTS':
		if($allowanceObj->DeleteSTS($_GET['refNo'])) {
				echo "$('#dialogAlert').dialog('close');";
				echo "dialogAlert('STS successfully deleted.');\n";
			} else {
				echo "dialogAlert('Error deleting STS.');";
			}	
	exit();
	break;
	
	case 'ReleaseSTS':
		if($_SESSION['sts-userLevel']==1){
			$count = $allowanceObj->hasSTSDetail($_GET['refNo']);
			$count2 = $allowanceObj->hasEnhancer($_GET['refNo']);
			if((int)$count>0){
				if((int)$count2>0){
					if($allowanceObj->releaseSTS($_GET['refNo'])){
						echo "$('#dialogAlert').dialog('close');\n";
						echo "dialogAlert('STS successfully released...');\n";
					}else{
						echo "$('#dialogAlert').dialog('close');\n";
						echo "dialogAlert('Error releasing...');\n";
					}
				}else{
					echo "$('#dialogAlert').dialog('close');\n";
					echo "dialogAlert('Please add shelf enhancer first');\n";
				}
			}else{
				echo "$('#dialogAlert').dialog('close');\n";
				echo "dialogAlert('Please add participants first');\n";
			}
		}else{
			echo "dialogAlert('Insuficient Access Rights!');\n";
		}
	exit();
	break;
	
	case 'printSTS':
		echo "window.open('report/report.php?{$_SERVER['QUERY_STRING']}');";
	exit();
	break;
	
	case "printContract":
			if ($allowanceObj->PrintContract((int)$_GET['refNo']))	{
				echo "window.open('report/enhancerAgreement_pdf.php?refNo={$_GET['refNo']}');";
			} else {
				echo "dialogAlert('Error printing Contract.');";
			}
	exit();
	break;
	
	case "printContractAttachment":
		if($allowanceObj->checkPayment($_GET['refNo'])=='C'){
			echo "window.open('report/enhancerAttachment_pdfCollection.php?refNo={$_GET['refNo']}');";
		}else{
			echo "window.open('report/enhancerAttachment_pdf.php?refNo={$_GET['refNo']}');";
		}
	exit();
	break;
	
	case 'getSTSToCancelInfo':
		$Up = $allowanceObj->calculateUploadedAmtSum($_GET['refNo']);
		$Q = $allowanceObj->calculateQueuedAmtSum($_GET['refNo']);
		if($Q['stsApplyAmt'] == 0){
			echo "$('#upAmt').addClass('ui-widget');\n";
			echo "$('#upAmt').addClass('ui-state-error');\n";
			echo "$('#upAmt').addClass('ui-corner-all');\n";		
		}else{
			echo "$('#upAmt').removeClass('ui-widget');\n";
			echo "$('#upAmt').removeClass('ui-state-error');\n";
			echo "$('#upAmt').removeClass('ui-corner-all');\n";
		}
		echo "$('#upAmt').html('".number_format($Up['stsApplyAmt'],2)."');\n";
		echo "$('#queueAmt').html('".number_format($Q['stsApplyAmt'],2)."');\n";
	exit();
	break;
	
	case 'getCancellationDate':
		$allowanceObj->DropDownMenuD($allowanceObj->makeArr($allowanceObj->getCancelDates($_GET['refNo']),'stsApplyDate','stsApplyDate',''),'cmbCancelDate','','class="selectBox" ');
	exit();
	break;
	
	case 'CancelSTS':
		if($allowanceObj->cancelSTS($_GET['refNo'],$_GET['txtReason'],$_GET['cmbCancelDate'])){
			echo "dialogAlert('Cancellation Process Succeeded!');\n";
		}else{
			echo "dialogAlert('There was an Error in the Cancellation of STS');";
		}
	exit();
	break;
}
$arrMode = array("",'D'=>"INVOICE DEDUCTION",'C'=>"CHECK/COLLECTION");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Shelf Enhancer</title>
<style type="text/css" title="currentStyle">
			@import "../../includes/css/demo_page.css";
			@import "../../includes/css/demo_table_jui.css";
</style>

<link type="text/css" href="../../includes/jquery/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<link type="text/css" href="../../includes/jquery/development-bundle/demos/demos.css" rel="stylesheet" />
<link type="text/css" href="../../includes/jqGrid/css/ui.jqgrid.css"rel="stylesheet" />
<script type="text/javascript" src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="../../includes/jqGrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="../../includes/jqGrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(function(){
	$('#regSTSDetail,#regSTSDelDetail').attr('disabled','true');
	$('#txtApDate').datepicker();
	$("#stsTable").jqGrid({ 
		url:'displayAllowance.php?action=Load', 
		datatype: "json", 
		colNames:['Ref. No','Supplier', 'Remarks','Date Entered','STS Date','Status'], 
		colModel:[ 
			{name:'stsRefNo', index:'stsRefNo', width:40, align:"right"}, 
			{name:'suppName', index:'suppName', width:170, align:"left"}, 
			{name:'stsRemarks', index:'stsRemarks', width:190, align:"left"}, 
			{name:'stsDateEntered', index:'stsDateEntered', width:80, align:"center"}, 
			{name:'stsDate', index:'stsDate', width:80, sortable:false, align:"center"},
			{name:'stsStat', index:'stsStat', width:60, align:"center"} 
		], 
		rowNum:20, 
		rowList:[20,40,60], 
		pager: '#stsPager', 
		sortname: 'stsRefNo', 
		viewrecords: true, 
		sortorder: "desc", 
		caption:"Lists", 
		height:"auto",
		width:"980",
		onSelectRow: function(id){ 
			var id = jQuery("#stsTable").jqGrid('getGridParam','selrow'); 
			var ret = jQuery("#stsTable").jqGrid('getRowData',id);
			stat = ret.stsStat;
			refNo = ret.stsRefNo;
			compCode = ret.stsComp;
			$("#hdnRefNo").val(refNo);
			//$("#printSTS").addClass("link");
			//$("#printSTS").attr("onclick","printSTS();");
			$("#contract").addClass("link");
			$("#contract").attr("onclick","printContract();");
			$("#attachment").addClass("link");
			$("#attachment").attr("onclick","printContractAttachment();");
			if(stat == 'OPEN'){
				$("#editSTS").addClass("link");
				$("#editSTS").attr("onclick","EditSTS();");
				$("#deleteSTS").addClass("link");
				$("#deleteSTS").attr("onclick","DeleteSTS();");
				$("#releaseSTS").addClass("link");
				$("#releaseSTS").attr("onclick","ReleaseSTS();");
				$("#cancelSTS").removeClass("link");
				$("#cancelSTS").addClass("disable");
			}else if(stat =='CANCELLED'){
				$("#cancelSTS").removeClass("link");
				$("#cancelSTS").addClass("disable");
				$("#editSTS").removeClass("link");
				$("#editSTS").addClass("disable");
				$("#deleteSTS").removeClass("link");
				$("#deleteSTS").addClass("disable");
				$("#releaseSTS").removeClass("link");
				$("#releaseSTS").addClass("disable");
				$("#editSTS").removeAttr("onclick");
				$("#deleteSTS").removeAttr("onclick");
				$("#releaseSTS").removeAttr("onclick");
			}
			else{
				$("#editSTS").removeClass("link");
				$("#editSTS").addClass("disable");
				$("#deleteSTS").removeClass("link");
				$("#deleteSTS").addClass("disable");
				$("#releaseSTS").removeClass("link");
				$("#releaseSTS").addClass("disable");
				$("#cancelSTS").addClass("link");
				$("#editSTS").removeAttr("onclick");
				$("#deleteSTS").removeAttr("onclick");
				$("#releaseSTS").removeAttr("onclick");
				$("#cancelSTS").attr("onclick","cancelSTS2();");
			}
	   }
        
	}).navGrid("#stsPager",{edit:false,add:false,del:false}); 
	
	$("#txtSupp").autocomplete({
		source: "displayAllowance.php?action=searchSupplier",
		minLength: 1,
		select: function(event, ui) {	
			var content = ui.item.id.split("|");
			$("#hdnSuppCode").val(content[0]);
			$("#txtRep").val(content[1]);
		}
	});	    
	$("#AddSTS").click( function(){ 
		clearAllText();
		$.ajax({
			url: "regularSTS.php",
			type: "GET",
			data: "action=checkNoOfUnapproved",
			success: function(Data){
				counter = Data;
				if(counter<=50){
					//AddSTSExt();
					checkNtbuSupplier();
				}else{
					dialogAlert('Entry has been disabled you have '+counter+' unapproved transactions');
				}
			}
		});		
		
	});
	
});
//mike
function checkNtbuSupplier(){
	$.ajax({
		url: "regularSTS.php",
		type: "GET",
		data: "action=checkNtbuVendors",
		success: function(Data){
			var vendInfo = Data.split("|");
			if(vendInfo[0]<=0){
				AddSTSExt();
			}else{
				dialogAlert('Your group has one or more NTBU vendors that needs to be replaced in the change supplier module, sts ref no.'+vendInfo[1]);
			}
		}
	});				
}
function AddSTSExt(){
	$("#buttonHolder").html("<button id='saveHdr' name='saveHdr' onClick='saveHdr();'>Save Header</button> ");
		$('#regSTSDetail,#regSTSDelDetail,#addEnhancer').attr('disabled','true');
		$("#saveHdr").button({
		icons: {
				primary: 'ui-icon-disk',
			}
		});
		
		$("#dialogAdd").dialog("destroy");
			$("#dialogAdd").dialog({
				title: "NEW SHELF ENHANCER",
				height: 650,
				width: 750,
				modal: true,
				closeOnEscape: false,
				position: {
					my: 'center',
					at: 'top'
				},
				close: function() {
					reloadGrid();
				}
			});				
}
function addEnhancer(){
	var refNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'enhancerDtl.php',
		type: "POST",
		data: "act=Details&refNo="+refNo,
		beforeSend: function() {
			ProcessData('Getting list of Branches Please wait...','Open');
		},
		success: function(Data){
			$('#divAddEnhancer').html(Data);
			ProcessData('','Close');
		}				
	});	
	$("#divAddEnhancer").dialog("destroy");
	$("#divAddEnhancer").dialog({
		title: "STS Enhancer Details",
		height: 420,
		width: 400,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Save': function() {
				if(validateEnhacerDtl()){
					$.ajax({
						url: 'enhancerDtl.php',
						type: "POST",
						data: $("#formEnhancerDtl").serialize()+'&action=AddEnhancerDtl',
						beforeSend: function() {
							ProcessData('Saving STS Details...','Open');
						},
						success: function(Data){
							LoadData2('displayAllowance.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
							ProcessData('','Close');
							eval(Data);
						}				
					});
				}
			},
			'Enhancer': function(){
				if(validateEnhacerDtl()){
					addRentables();
				}
			}
		}
	});
}
function addRentables() {
	var refNo = $('#hdnRefNo').val();
	
	$.ajax({
		url: 'enhancerRentable.php',
		type: "POST",
		data: "act=getRentables&refNo="+refNo,
		beforeSend: function() {
			ProcessData('Getting list of Branches Please wait...','Open');
		},
		success: function(Data){
			$('#divSTSDtls3').html(Data);
			ProcessData('','Close');
		}				
	});	
	$("#divSTSDtls2").dialog("destroy");
	$("#divSTSDtls2").dialog({
		title: "Rentable Details",
		 beforeClose :function(event,ui){ return false;},
		height: 520,
		width: 900,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Save': function() {
				$.ajax({
					url: 'enhancerRentable.php',
					type: "POST",
					data: 'action=addRentable+&refNo='+refNo+'&'+$("#frmRentable").serialize(),
					beforeSend: function() {
						ProcessData('Saving STS Details...','Open');
					},
					success: function(Data){
						$(this).dialog('close');
						LoadData2('da.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
						ProcessData('','Close');
						eval(Data);
					}				
				});	
			},
			'Enhancer': function(){
				if(validateEnhacerDtl()){
					addRentables();
				}
			}
		}
	});	
}
function editEnhancer(refNo,brandCode,category){
	$.ajax({
		url: 'enhancerDtl.php',
		type: "POST",
		data: "act=Details&refNo="+refNo+"&brandCode="+brandCode+"&category="+category,
		beforeSend: function() {
			ProcessData('Getting list of Branches Please wait...','Open');
		},
		success: function(Data){
			$('#divAddEnhancer').html(Data);
			ProcessData('','Close');
		}				
	});	
	$("#divAddEnhancer").dialog("destroy");
	$("#divAddEnhancer").dialog({
		title: "STS Enhancer Details",
		height: 420,
		width: 400,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Update': function() {
				if(validateEnhacerDtl()){
					$.ajax({
						url: 'enhancerDtl.php',
						type: "POST",
						data: $("#formEnhancerDtl").serialize()+'&action=UpdateEnhancerDtl',
						beforeSend: function() {
							ProcessData('Saving STS Details...','Open');
						},
						success: function(Data){
							LoadData2('displayAllowance.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
							ProcessData('','Close');
							eval(Data);
						}				
					});
				}
			},
			'Enhancer': function(){
				if(validateEnhacerDtl()){
					addRentables();
				}
			}
		}
	});
}
function validateEnhancer2(){
	var val=true;
	
	if (validateCmb($("#cmbBrand"))== false)
		val = false;
	if (validateString($("#txtCat"))== false)
		val = false;
	if (validateString($("#txtBRemarks"))== false)
		val = false;
	return val;
}
function validateEnhacerDtl(){
	var ret = true;
	if(validateEnhancer2()){
		var counter = $('#hdCtr2').val();	
		for(a=0;a<=counter;a++) {	
			if ( $('#ch2_'+a).attr('checked') ) {	
				if( $('#cmbEnhancer_'+a).val()=='0'){
					dialogAlert("Please select a valid enhancer type!");
					ret = false;
				}
			}
		}
	}else{
		dialogAlert("Required Field!");
		ret = false;
	}
	return ret;
}
function cancelSTS2(){
	var refNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'displayAllowance.php',
		type: "GET",
		data: "action=getSTSToCancelInfo&refNo="+refNo,
		success: function(Data){
			eval(Data);
		}				
	});
	$("#divStsDetail").dialog("destroy");
	$("#divStsDetail").dialog({
		height: 150,
		width: 500,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Yes ': function() {
				cancelReason();
			},
			'No': function() {
				$(this).dialog('close');
			}
		}
	});		
}
function cancelReason(){		
	$('#txtReason').text('');
	var refNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'displayAllowance.php',
		type: "GET",
		data: "action=getCancellationDate&refNo="+refNo,
		success: function(Data){
			eval(Data);
			$("#stopDate").html(Data);
		}				
	});
	$("#dialogCancelSTS").dialog("destroy");
	$("#dialogCancelSTS").dialog({
		height: 170,
		width: 440,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Close': function() {
				$(this).dialog('close');
			},
			'GO (Cancel STS)': function() {
				var txtReason = $("#txtReason");
				if(validateString($("#txtReason"))){
					ConfirmCancel();
				}
			}
		}
	});			
}
function ConfirmCancel(){
	var refNo = $('#hdnRefNo').val();
	
	$("#dialogAlert").dialog("destroy");
	$("#dialogMsg").html('Continue with the cancellation of STS?');
	$("#dialogAlert").dialog("destroy");
	$("#dialogAlert").dialog({
		height: 148,
		width: 340,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Continue': function(){
				$.ajax({
					url: 'displayAllowance.php',
					type: "GET",
					data: $("#formCancelSTS").serialize()+"&action=CancelSTS&refNo="+refNo,
					beforeSend: function() {
						ProcessData('Cancelling STS...','Open');
					},
					success: function(Data){
						$('#dialogAlert').dialog('close');
						$('#dialogCancelSTS').dialog('close');
						$('#divStsDetail').dialog('close');
						reloadGrid();
						ProcessData('','Close');
						eval(Data);
					}				
			 	});		
			}
		}
	});
}

function saveHdr(){ 
	//var refNo = $('#hdnRefNo').val();
	if ($('#ckVat').is(":checked")){
		vatTag = "Y";
	}else{
		vatTag = "N";	
	}
	if(validateInputs()){
		$.ajax({
			url: "displayAllowance.php",
			type: "GET",
			traditional: true,
			beforeSend: function() {
				ProcessData('Saving STS...','Open');
			},
			data: $("#formSTS").serialize()+'&action=saveHdr&vatTag='+vatTag,
			success: function(msg){
				ProcessData('','Close');
				$("#dialogAdd").dialog({
					height: 650,
				});
				eval(msg);
			}
		});
	}else{
		dialogAlert('Please fill up all the required field(s)');	
	}
}
function reloadGrid() {
	$("#stsTable").trigger("reloadGrid"); 
	$("#editSTS").removeClass("link");
	$("#editSTS").addClass("disable");
	$("#deleteSTS").removeClass("link");
	$("#deleteSTS").addClass("disable");
	$("#releaseSTS").removeClass("link");
	$("#releaseSTS").addClass("disable");
}
function searchGridCompany(compCode){
	$("#stsTable").jqGrid('setGridParam',{url:"displayAllowance.php?action=Load&compCode="+compCode,page:1}).trigger("reloadGrid");
}

function LoadData(page,divData,gridID,params){
	$.ajax({
		url: page,
		type: "GET",
		data: params,
			success: function(Data){
			$("#"+divData).html(Data);
			$('#'+gridID).dataTable({
				//"bJQueryUI" : "true",
				"sPaginationType": "full_numbers",
				"iDisplayLength": 5,
				"aLengthMenu": [5, 10, 15]
			});
		}				
	});			
}
function LoadData2(page,divData,gridID,params){
	$.ajax({
		url: page,
		type: "GET",
		data: params,
			success: function(Data){
			$("#"+divData).html(Data);
			$('#'+gridID).dataTable({
				//"bJQueryUI" : "true",
				"sPaginationType": "full_numbers",
				"iDisplayLength": 3,
				"aLengthMenu": [3, 6, 9],
			});
		}				
	});			
}
function EditSTS(){
	var refNo = $('#hdnRefNo').val();
	$("#buttonHolder").html("<button id='updateHdr' name='updateHdr' onClick='updateHdr();'>Update Header</button> ");
	$("#updateHdr").button({
		icons: {
			primary: 'ui-icon-disk',
		}
	});
	clearAllText();
	$.ajax({
		url: "displayAllowance.php",
		type: "GET",
		traditional: true,
		data: 'action=getInfo&refNo='+refNo,
		success: function(msg){
			eval(msg);
		}				
	});	
	LoadData('displayAllowance.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+refNo);
	LoadData2('displayAllowance.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
	$("#dialogAdd").dialog("destroy");
	$("#dialogAdd").dialog({
		title: "EDIT SHELF ENHANCER",
		height: 670,
		width: 750,
		modal: true,
		closeOnEscape: false,
		position: {
			my: 'center',
			at: 'top'
		},
		close: function() {
			reloadGrid();
		},
	});
}
function AddSTSDtl(compCode) {
	var refNo = $('#hdnRefNo').val();
	var idept = 13;
	var iclass = 0;
	var isubClass = 0;
	
	if ($('#ckVat').is(":checked")){
		vatTag = "Y";
	}else{
		vatTag = "N";	
	}
	$.ajax({
		url: 'dispAllowDtl.php',
		type: "POST",
		data: "act=Details&refNo="+refNo+'&compCode='+compCode+'&vatTag='+vatTag+'&idept='+idept+'&iclass='+iclass+'&isubClass='+isubClass,
		beforeSend: function() {
			ProcessData('Getting list of Branches Please wait...','Open');
		},
		success: function(Data){
			$('#divSTSDtls').html(Data);
			ProcessData('','Close');
		}				
	});	
	$("#divSTSDtls").dialog("destroy");
	$("#divSTSDtls").dialog({
		title: "STS Details",
		height: 420,
		width: 700,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Save': function() {
				$.ajax({
					url: 'dispAllowDtl.php',
					type: "POST",
					data: $("#formSTSDtl").serialize()+'&action=AddSTSDtl',
					beforeSend: function() {
						ProcessData('Saving STS Details...','Open');
					},
					success: function(Data){
						LoadData('displayAllowance.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+refNo);
						ProcessData('','Close');
						eval(Data);
					}				
				});										
			}
		}
	});	
}	
function DeleteSTSDtl() {
	var refNo = $('#hdnRefNo').val();
	$("#dialogMsg").html('Are you sure you want to delete details?');
		$("#dialogAlert").dialog("destroy");
		$("#dialogAlert").dialog({
			modal: true,
			buttons: {
				'YES': function() {
					$.ajax({
						url: 'dispAllowDtl.php',
						type: "POST",
						data: 'action=DeleteShelf&refNo='+refNo,
						beforeSend: function() {
							ProcessData('Deleting STS Details...','Open');
						},
						success: function(Data){
							LoadData('displayAllowance.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+refNo);
							LoadData2('displayAllowance.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
							ProcessData('','Close');
							eval(Data);
						}				
					});												
				},
				'NO': function() {
					$(this).dialog('close');
				}
			},
		});
}
function deleteEnhancer(refNo,brandCode,category){
	$("#dialogMsg").html('Are you sure you want to delete this brand?');
		$("#dialogAlert").dialog("destroy");
		$("#dialogAlert").dialog({
			modal: true,
			buttons: {
				'YES': function() {
					$.ajax({
						url: 'enhancerDtl.php',
						type: "POST",
						data: 'action=Delete&refNo='+refNo+'&brandCode='+brandCode+'&category='+category,
						beforeSend: function() {
							ProcessData('Deleting Enhancer Details...','Open');
						},
						success: function(Data){
							LoadData2('displayAllowance.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
							ProcessData('','Close');
							eval(Data);
						}				
					});												
				},
				'NO': function() {
					$(this).dialog('close');
				}
			},
		});
}
function DeleteSTS(){
	var refNo = $('#hdnRefNo').val();
	$("#dialogMsg").html('Are you sure you want to delete?');
		$("#dialogAlert").dialog("destroy");
		$("#dialogAlert").dialog({
			modal: true,
			buttons: {
				'YES': function() {
					$.ajax({
						url: 'displayAllowance.php',
						type: "GET",
						data: 'action=DeleteSTS&refNo='+refNo,
						beforeSend: function() {
							ProcessData('Deleting STS...','Open');
						},
						success: function(Data){
							reloadGrid();
							ProcessData('','Close');
							eval(Data);
						}				
					});												
				},
				'NO': function() {
					$(this).dialog('close');
				}
			},
		});
}
function printSTS(){
	var stsRefNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'displayAllowance.php',
		type: "GET",
		data: 'action=printSTS&refNo='+stsRefNo,
		beforeSend: function() {
			ProcessData('Printing STS...','Open');
		},
		success: function(Data){
			ProcessData('','Close');
			eval(Data);
		}				
	});
}
function printContract(){
	var stsRefNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'displayAllowance.php',
		type: "GET",
		data: 'action=printContract&refNo='+stsRefNo,
		beforeSend: function() {
			ProcessData('Printing STS Contract...','Open');
		},
		success: function(Data){
			ProcessData('','Close');
			eval(Data);
		}				
	});
}
function printContractAttachment(){
	var stsRefNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'displayAllowance.php',
		type: "GET",
		data: 'action=printContractAttachment&refNo='+stsRefNo,
		beforeSend: function() {
			ProcessData('Printing STS Contract...','Open');
		},
		success: function(Data){
			ProcessData('','Close');
			eval(Data);
		}				
	});
}
function ReleaseSTS(){
	var stsRefNo = $('#hdnRefNo').val();
	$("#dialogMsg").html('Are you sure you want to release sts reference number ' +stsRefNo+'?');
		$("#dialogAlert").dialog("destroy");
		$("#dialogAlert").dialog({
			modal: true,
			buttons: {
				'YES': function() {
					$.ajax({
						url: 'displayAllowance.php',
						type: "GET",
						data: 'action=ReleaseSTS&refNo='+stsRefNo,
						beforeSend: function() {
							ProcessData('Releasing STS...','Open');
						},
						success: function(Data){
							reloadGrid();
							ProcessData('','Close');
							eval(Data);
						}
					});												
				},
				'NO': function() {
					$(this).dialog('close');
				}
			},
		});
}
function updateHdr(){
	var compCode = $('#hdnCompCode').val();
	var stsRefNo = $('#hdnRefNo').val();
	
	if ($('#ckVat').is(":checked")){
		vatTag = "Y";
	}else{
		vatTag = "N";	
	}
	
	if(validateInputs()){
		$.ajax({
			url: "displayAllowance.php",
			type: "GET",
			traditional: true,
			data: $("#formSTS").serialize()+'&action=UpdateHdr&refNo='+stsRefNo+'&compCode='+compCode+'&vatTag='+vatTag,
			success: function(msg){
				LoadData('displayAllowance.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+refNo);
				eval(msg);
			}				
		});
	}else{
		dialogAlert('Please fill up all the required field(s)');	
	}
}
function validateInputs() {
	var val = true;

	if (validateCmb($("#cmbPayType"))== false)
		val = false;
	if (validateString($("#txtSTSAmount"))== false)
		val = false;
	if (validateString($("#txtRep"))== false)
		val = false;
	if (validateString($("#txtSupp"))== false)
		val = false;
	if (validateString($("#txtRemarks"))== false)
		val = false;
	if (validateString($("#txtNoApplications"))== false)
		val = false;
	if (validateString($("#txtApDate"))== false)
		val = false;
	if (validateString($("#hdnSuppCode"))== false)
		val = false;
	
	return val;
}

	
function validateString(ObjName){
	if(ObjName.val().length == 0){
		ObjName.addClass("ui-state-error");
		return false;
	}else{
		ObjName.removeClass("ui-state-error");
		return true;
	}
}
	
function validateCmb(ObjName){
	if(ObjName.val()== 0){
		ObjName.addClass("ui-state-error");
		return false;
	}
	else{
		ObjName.removeClass("ui-state-error");
		return true;
	}
}
function loadStore(compCode){
	$.ajax({
		url: "displayAllowance.php",
		type: "GET",
		traditional: true,
		data: 'action=cmbStore&compCode='+compCode,
		beforeSend: function() {
			ProcessData('Loading...','Open');
		},
		success: function(msg){
			ProcessData('Loading...','Close');
			$('#tdStore').html(msg);
		}				
	});
}
function loadClass(dept){
	$.ajax({
		url: "displayAllowance.php",
		type: "GET",
		traditional: true,
		data: 'action=cmbClass&dept='+dept,
		beforeSend: function() {
			ProcessData('Loading...','Open');
		},
		success: function(msg){
			ProcessData('Loading...','Close');
			$('#tdClass').html(msg);
		}				
	});
}
function loadSubClass(class1){
	$.ajax({
		url: "displayAllowance.php",
		type: "GET",
		traditional: true,
		data: 'action=cmbSubClass&dept='+$('#cmbDept').val()+'&class='+class1,
		beforeSend: function() {
			ProcessData('Loading','Open');
		},
		success: function(msg){
			ProcessData('Loading...','Close');
			$('#tdSubClass').html(msg);
		}				
	});
}
function valNumInputDec(event) {
		var key, keyChar;
	  	if (window.event)
			key = window.event.keyCode;
	  	else if (event)
			key = event.which;
	  	else
			return true;
	  	// Check for special characters like backspace
	  	if (key == null || key == 0 || key == 8 || key == 13 || key == 27)
			return true;
	  	// Check to see if it's a number
	  	keyChar =  String.fromCharCode(key);
	  	if ((/\d/.test(keyChar)) || (/\./.test(keyChar))) {
			window.status = "";
			return true;
		} 
		else {
			window.status = "Field accepts numbers only.";
			return false;
	   	}
	}	
function ProcessData(msg,act) {
		if (act=='Open') {
			$("#dialogProcess").dialog("destroy");
			$("#Process").html(msg)
				$("#dialogProcess").dialog({
				title: 'PG STS',
				height: 150,
				modal: true,
				closeOnEscape: false,
				beforeClose: function(event, ui) {
						return false;
					}
			});		
		} else {
			$("#dialogProcess").dialog('close');
			$("#dialogProcess").dialog("destroy");
		}
	}
function dialogAlert(msg){
	$("#dialogAlert").dialog("destroy");
	$("#dialogMsg").html(msg);
	$("#dialogAlert").dialog({
		modal: true,
		buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});	
}

function disableFields(switcher){
	if(switcher =='D'){
		$('#txtSTSAmount, #txtSupp, #cmbDept, #cmbClass, #cmbSubClass, #cmbCompCode, #txtRemarks, #cmbPayType, #txtTerms, #txtNoApplications, #txtApDate').attr('disabled','true');
	}else{
		$('#txtSTSAmount, #txtSupp, #cmbDept, #cmbClass, #cmbSubClass, #cmbCompCode, #txtRemarks, #cmbPayType, #txtTerms, #txtNoApplications, #txtApDate').removeAttr('disabled');
	}
}

function disablePayType(val){
	if(val=='D'){
		$("#txtTerms").attr('disabled','true');
		$("#txtTerms").addClass('ui-state-disabled');
		$("#txtNoApplications").removeAttr('readonly');
		$("#txtNoApplications").val('');
	}else{
		$("#txtTerms").removeAttr('disabled');
		$("#txtTerms").removeClass('ui-state-disabled');
		$("#txtNoApplications").attr('readonly','true');
		$("#txtNoApplications").val('1');	
	}
}
function clearAllText(){
	$(":text").val("");
	$("#txtRefNo").html("");
	$("#cmbPayType").val("0");
	$("#hdnSuppCode").val("0");
	$("#Data").html("");
	$("#enhancerData").html("");
}
function enableVat(val){
	if(val=='D'){
		$("#ckVat").attr("disabled","disabled");
		$('#ckVat').attr('checked', false);
	}else{
		$("#ckVat").removeAttr("disabled");
		
	}
}
function enableVat(val){
	if(val=='C'){
		$("#ckVat").removeAttr("disabled");
	}else{
		$("#ckVat").attr("disabled","disabled");
		$('#ckVat').attr('checked', false);
	}
}
</script>
<style type="text/css">
<!-- 
.textBox {
	border: solid 1px #222; 
	border-width: 1px; 
	width:190px; 
	height:18px;
	font-size: 11px;
}
.textBoxLong {
	border: solid 1px #222; 
	border-width: 1px; 
	width:541px; 
	height:18px;
	font-size: 11px;
}
.textBoxMin {
	border: solid 1px #222; 
	border-width: 1px; 
	width:130px; 
	height:18px;
	font-size: 11px;
}
.headerContent {
	height: 167px;
	border: solid 1px #222;
	padding: 1px;
	background-color:#E9E9E9;
}	
.detailContent {
	height: 50px;
	border: solid 1px #222;
	padding: 3px;
	background-color:#E9E9E9;
}	
.selectBox {
	border: 1px solid #222; 
	width:192px; 
	height:22px;
	font-size: 11px;
}
.selectBox2 {
	border: 1px solid #222; 
	width:120px; 
	height:22px;
	font-size: 11px;
}

.selectBoxMin {
	border: 1px solid #222; 
	width:100px; 
	height:22px;
	font-size: 11px;

}
.selectBoxMin2 {
	border: 1px solid #222; 
	width:150px; 
	height:22px;
	font-size: 11px;

}
.errMsg {
	padding:5px;
	height:30px;
	text-align:center;
}
.disable {
	color:#CCCCCC;
	font-size:11px; 
	cursor:default;
}
.link {
	cursor: pointer; 
	font-size:11px;
	font-weight: bolder; 
	color:#fff;
}
.link2 {
	cursor: pointer; 
	font-size:11px;
	font-weight: bolder; 
	color:#09F;
}
.hd {
	font-size: 11px;
	font-family: Verdana;
	font-weight: bold;
}
.style20 {
	font-size: 14px;
	font-weight: bold;
	font-family: "Trebuchet MS";
	color: #EEEEEE;
}
.style21 {
	font-size: 14px;
	font-family: "Trebuchet MS";
	color: #EEEEEE;
}
.style22 {color: #EEEEEE; background-color:#EEEEEE}
.style23 {font-size:11px; cursor: pointer;}

body {
	background-color: #56789a;
}
.style24 {color: #000000}


-->
.dataTables_wrapper {
	
	min-height: 190px;
	_height: 190px;
	clear: both;
}
</style>
</head>

<body>

<h2 class="ui-widget-header ui-corner-all" style="padding:5px;">SHELF ENHANCER</h2>
<br />
<span id="AddSTS" style="cursor: pointer; font-size:11px;" class="link" title="Add STS">
	<img src="../../images/file_add.png" title="Add STS" /> New
</span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="editSTS" title="Edit STS" onclick="">
	<img src="../../images/file_edit.png" title="Edit STS" /> Edit
</span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="deleteSTS" title="Delete STS" onclick="">
	<img src="../../images/file_delete.png" title="Delete STS" /> Delete
</span>
<!--
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="releaseSTS" title="Release" onclick="">

	<img src="../../images/tag_green.png" title="Approve STS" /> Approve
</span>
-->
<!--&nbsp;&nbsp;&nbsp;&nbsp;-->
<!--<span class="style23, disable" id="printSTS" title="Print STS" onclick="">
	<img src="../../images/print.png" title="Print STS" /> Print
</span>-->
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="contract" title="Print Contract" onclick="">
	<img src="../../images/print.png" title="Print Contract" /> Contract
</span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="attachment" title="Print Contract Attachment" onclick="">
	<img src="../../images/print.png" title="Print Contract Attachment" /> Contract Attachment
</span>
<!--
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="style23, disable" id="cancelSTS" title="Cancel" onclick="">
	<img src="../../images/layout_delete.png" title="Cancel" /> Cancel
</span>
-->
<div align="right" style="float: right;">

</div>
<br /><br />
<center><table id="stsTable"></table> </center>
<div id="stsPager"></div> <br /> 

	<div style=" visibility:hidden;  overflow:hidden;">
        <div id='dialogAdd' title='Add'>
     		
        	<div class="headerContent ui-corner-all">
            <input type="hidden" name="hdnRefNo" id="hdnRefNo" />	
            <input type="hidden" name="hdnDept" id="hdnDept" />
            <input type="hidden" name="hdnClass" id="hdnClass" />	
            <input type="hidden" name="hdnSubClass" id="hdnSubClass" />
            <input type="hidden" name="hdnCompCode" id="hdnCompCode" />
            <br />
            <form id="formSTS">    	
            <input type="hidden" id="hdnSuppCode" name="hdnSuppCode" />
            <table width="100%" border="0" cellspacing="2" cellpadding="0" align="center">
                <tr>
                    <td  class="hd">Ref. No.: </td>
                    <td><label><span id="txtRefNo" name="txtRefNo"></span></label></td>
                    <td class="hd">Supplier: </td>
                    <td><input type="text" name="txtSupp" id="txtSupp" class="textBox"/></td>
                </tr>
                <tr>
                    <td  class="hd">Amount: </td>
                    <td><input type="text" name="txtSTSAmount" id="txtSTSAmount" class="textBox" onKeyPress='return valNumInputDec(event);'/></td>
                     <td class="hd">Payment Mode: </td> 
                     <td><? $allowanceObj->DropDownMenu($arrMode,'cmbPayType','','class="selectBox2" onchange="enableVat(this.value);"'); ?> W/ VAT <input type="checkbox" id="ckVat" name="ckVat"/></td>
                </tr>
                <tr>
                    <td class="hd">Application Start Date: </td>
                    <td><input type="text" name="txtApDate" id="txtApDate" class="textBox" readonly="readonly" /></td>
                    <td class="hd">No. of Applications: </td>
                    <td><select name="txtNoApplications" id="txtNoApplications" class="selectBox">
                    	<? for($a=1;$a<=12;$a++){
						?><option value="<?=$a?>"><?=$a?></option>
                        <? } ?>
                    </select></td>
                </tr>
                 <tr>
                    <td class="hd">Authorized Representative: </td>
                    <td><input type="text" name="txtRep" id="txtRep" class="textBox" /></td>
                    <td class="hd">Position: </td>
                    <td><input type="text" name="txtRepPos" id="txtRepPos" class="textBox" /></td>
                </tr>
                <tr>
                	<td class="hd">Remarks: </td>
                    <td colspan="3"><input type="text" name="txtRemarks" id="txtRemarks" class="textBoxLong"/></td>
                </tr>
                
            </table>
             </form> 
             <div align="center" id="buttonHolder" name="buttonHolder"></div>
            </div>
           
           
            <h4 class="ui-widget-header">Participants</h4>
            <button id="regSTSDetail" name="regSTSDetail">Add Detail</button>
            <button id="regSTSDelDetail" name="regSTSDelDetail">Delete Detail</button>
            <div id="Data">           
			</div>  
            <h4 class="ui-widget-header">Enhancer</h4>
         	<button id="addEnhancer" name="addEnhancer">Add Enhancer</button>
            <div id="enhancerData"></div>  
			
        </div>
        
         <div id="divAddEnhancer">
        	
        </div>
        <div id='divStsDetail' title='STS Detail'>
                <div class="ui-corner-all">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td class="hd" width="25%">Uploaded Amt.: </td>
                          <td id="upAmt" width="25%"></td>
                          <td class="hd" width="25%">On Queue Amt.: </td>
                          <td id="queueAmt" width="25%"></td>
                        </tr>
                         <tr>
                          <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                          <td class="hd" colspan="4">Are you sure you want to cancel this STS?</td>
                        </tr>
                    </table>
                </div>
           </div>
           
            <div id='dialogCancelSTS' title='Cancel STS'>
                <form id="formCancelSTS" name="formCancelSTS">
                <div class="headerContent2 ui-corner-all">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    	<tr>  
                          <td class="hd">Effectivity Date:</td>
                          <td id="stopDate"></td>
                        </tr>
                        <tr>  
                          <td class="hd" colspan="2">Reason for the cancellation of STS</td>
                        </tr>
                        <tr>
                          <td colspan="2"><textarea id="txtReason" name="txtReason" class="ui-corner-all" cols="55" ></textarea></td>
                        </tr>
                    </table>
                </div>
                </form>
           </div>
        <div id='dialogProcess' style="overflow:hidden;"><br />
            <div style="text-align:center"><img src="../../images/progress2.gif"/></div>
            <div id='Process' style="text-align:center"></div>
        </div>
        <div id='dialogAlert' title='STS'>
            <p id='dialogMsg'></p>
    	</div>
        <div id="divSTSDtls2" >
        	<div id="divSTSDtls3" ></div>
       	</div>
        <div id="divSTSDtls" >
        	
            </div>
        </div>
	</div>
</body>
</html>
<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("daObj.php");
$daObj = new daObj();


$now = date('Y-m-d H:i:s');
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}
function applicationCounter($dtStart,$dtEnd){
			$ctr = 0;
			$newDate = $dtStart;
			while(strtotime($newDate) < strtotime($dtEnd)){
				$newDate = date('m/d/Y',strtotime(date("m/d/Y", strtotime($newDate)) . " +1 month"));
				$ctr++;
			}
			return $ctr;
		}
switch($_GET['action']){	
	case 'Load':
		$page = $_GET['page']; // get the requested page 
		$limit = $_GET['rows']; // get how many rows we want to have into the grid 
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
		$sord = $_GET['sord']; // get the direction 
		if(!$sidx) $sidx =1; 
		
		$totJob = $daObj->countRegSTS();
		$count = $totJob['count']; 
		
		if( (int)$count>0 ) { 
			$total_pages = ceil($count/$limit); 
		} else { 
			$total_pages = 0; 
		} 
		
		if ($page > $total_pages) 
			$page=$total_pages; 
		
		$start = ($limit*$page) - $limit; // do not put $limit*($page - 1) 
		
		$response->page = $page; 
		$response->total = $total_pages; 
		$response->records = $count; 
		
		/*if($_GET['_search']=='true'){
			$arrSTS= $daObj->searchDispSTS($sidx,$sord,$start,$limit,$_GET['searchField'],$_GET['searchString']);
		}else{	
			$arrSTS = $daObj->getPaginatedDispSTS($sidx,$sord,$start,$limit);
		}*/
		if(isset($_GET['refNo'])){
			$arrSTS = $daObj->getPaginatedDispSTSSearch($sidx,$sord,$start,$limit,$_GET['refNo']);
		}else{
			$arrSTS = $daObj->getPaginatedDispSTS($sidx,$sord,$start,$limit);
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
		$arrSupp = $daObj->findSupplier($_GET['term']);
		foreach($arrSupp as $val){
			$arrResult[] = array("id"=>$val['suppCode']."|".$val['contactPerson'], "label"=>$val['suppCode']." - ".str_replace("-",'-',$val['suppName']), "value" => strip_tags($val['suppName']));	
		}
		echo json_encode($arrResult);
	exit();	
	break;
	
	case 'cmbClass':
		$daObj->DropDownMenu($daObj->makeArr($daObj->findClass($_GET['dept']),'stsTransTypeClass','stsTransTypeName',''),'cmbClass','','class="selectBox" onchange="loadSubClass(this.value);"');
	exit();
	break;

	case 'cmbSubClass':
		$daObj->DropDownMenu($daObj->makeArr($daObj->findSubClass($_GET['dept'],$_GET['class']),'stsTransTypeSClass','stsTransTypeName',''),'cmbSubClass','','class="selectBox" ');
	exit();
	break;
	
	case 'cmbStore':
		$daObj->DropDownMenu($daObj->makeArr($daObj->findStore($_GET['compCode']),'brnCode','brnDesc',''),'cmbStore','','class="selectBox" ');
	exit();
	break;
	case 'saveHdr':
		if($daObj->saveHeader($_GET)){
			$lastQuery = $daObj->getLastSTSInserted();
			echo "$('#buttonHolder').html(\"<button id='updateHdr' name='updateHdr' onClick='updateHdr();'>Update Header</button>\");";
			echo "$('#updateHdr').button({
					icons: {
						primary: 'ui-icon-disk',
					}
				});";
			echo "$('#txtRefNo').html('{$lastQuery['stsRefNo']}');";
			echo "$('#hdnRefNo').val('{$lastQuery['stsRefNo']}');";
			echo "$('#addEnhancer').removeAttr('disabled');";
			echo "$('#addEnhancer').attr('onClick','addEnhancer();');";
			echo "$('#regSTSDetail').removeAttr('disabled');";
			echo "$('#regSTSDetail').attr('onClick','AddSTSDtl();');";
			//mike
			//LoadData2('da.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
			echo "LoadData2('da.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+{$lastQuery['stsRefNo']});";
			echo "dialogAlert('STS Header Successfully Saved!');";
		}else{
			echo "dialogAlert('There was an error in adding STS');";
		}
	exit();
	break;
	
	case 'getInfo':
		    $val = $daObj->getRegSTSInfoAssoc($_GET['refNo']);
		
		echo "$('#txtRefNo').html('{$val['stsRefno']}');\n";
		echo "$('#txtEndDate').val('".date('m/d/Y',strtotime($val['endDate']))."');\n";
		echo "$('#txtRemarks').val('{$val['stsRemarks']}');\n";
		echo "$('#hdnSuppCode').val('{$val['suppCode']}');\n";
		echo "$('#txtSupp').val('".addslashes($val['suppName'])."');\n";
		echo "$('#cmbPayType').val('{$val['stsPaymentMode']}');\n";
		echo "$('#txtNoApplications').val('{$val['nbrApplication']}');\n";
		echo "$('#txtApDate').val('".date('m/d/Y',strtotime($val['applyDate']))."');\n";
		echo "$('#txtRep').val('{$val['contactPerson']}');\n";
		echo "$('#txtRepPos').val('{$val['contactPersonPos']}');\n";
		
		
		if(date('m/d/Y',strtotime($val['impStartDate'])) == '01/01/1970')
			$startDate = "";
		else
			$startDate = date('m/d/Y',strtotime($val['impStartDate']));
			
		if(date('m/d/Y',strtotime($val['impEndDate'])) == '01/01/1970')
			$endDate = "";
		else
			$endDate = date('m/d/Y',strtotime($val['impEndDate']));
			
		echo "$('#txtImStartDate').val('".$startDate."');\n";
		echo "$('#txtImEndDate').val('".$endDate."');\n";
		
		if($val['vatTag']=='Y'){
			echo "$('#ckVat').attr('checked', true);";	
		}else{
			echo "$('#ckVat').attr('checked', false);";	
		}
	exit();
	break;
	
	case 'loadEnhancerDetails':
		$arr = $daObj->getEnhancerDetails($_GET['refNo']);
		if($arr[0]['stsRefno']==''){
			echo "<script type='text/javascript'>$('#regSTSDetail').html('Add Detail');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').attr('onClick','AddSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').removeAttr('onClick','DeleteSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').attr('disabled','true');</script>\n";
			echo "<script type='text/javascript'>$('#addRentables').attr('disabled','true');</script>\n";
			echo "<script type='text/javascript'>$('#addRentables').removeAttr('onClick','addRentables();');</script>\n";
		}else{
			echo "<script type='text/javascript'>$('#regSTSDetail').html('Edit Detail');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDetail').attr('onClick','AddSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').attr('onClick','DeleteSTSDtl();');</script>\n";
			echo "<script type='text/javascript'>$('#regSTSDelDetail').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#addRentables').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#addRentables').attr('onClick','addRentables();');</script>\n";
		}
		echo "<script type='text/javascript'>$('#addEnhancer').removeAttr('disabled');</script>\n";
			echo "<script type='text/javascript'>$('#addEnhancer').attr('onClick','addEnhancer();');</script>\n";
		echo "
		<table cellpadding='0' cellspacing='0'  border='0' class='display' id='enhancerDetailList'>
			<thead> 
			  <tr>
			  	<th>Store</th>
		        <th>Display Type</th>
				<th>Brand Name</th>
				<th>Location</th>
			  </tr>
			</thead>
			";	  
			foreach($arr as $val){ 
			  echo "<tr style='font: Verdana; font-size:11px; height:25px;' class='gradeK'  align='center'>
			  	<td width='20%'>".$val['brnDesc']."</td>
				<td width='20%'>".$val['displayType']."</td>
				<td width='20%'>".$val['brand']."</td>
				<td width='20%'>".$val['location']."</td>
				";
			echo "</tr>";
			  }
			echo "</tbody>
				  </table>";
	exit();
	break;
	case 'LoadSTSDetails':
		$arr = $daObj->getRegSTSDetails($_GET['refNo']);
		
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
		if($daObj->updateHeader($_GET)){
			echo "dialogAlert('STS successfully Updated!');";
		}else{
			echo "dialogAlert('There was an error in Updating the STS');";
		}
	exit();
	break;
	
	case 'DeleteSTS':
		if($daObj->DeleteSTS($_GET['refNo'])) {
				echo "$('#dialogAlert').dialog('close');";
				echo "dialogAlert('STS successfully deleted.');\n";
			} else {
				echo "dialogAlert('Error deleting STS.');";
			}	
	exit();
	break;
	
	case 'ReleaseSTS':
		if($_SESSION['sts-userLevel']==1){
			$count = $daObj->hasSTSDetail($_GET['refNo']);
			$count2 = $daObj->hasEnhancer($_GET['refNo']);
			if((int)$count>0){
				if((int)$count2==0){
					if($daObj->releaseSTS($_GET['refNo'])){
						echo "$('#dialogAlert').dialog('close');\n";
						echo "dialogAlert('STS successfully released...');\n";
					}else{
						echo "$('#dialogAlert').dialog('close');\n";
						echo "dialogAlert('Error releasing...');\n";
					}
				}else{
					echo "$('#dialogAlert').dialog('close');\n";
					echo "dialogAlert('Some branches does not have display allowance!');\n";
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
			if ($daObj->PrintContract((int)$_GET['refNo']))	{
				echo "window.open('report/enhancerAgreement_pdf.php?refNo={$_GET['refNo']}');";
			} else {
				echo "dialogAlert('Error printing Contract.');";
			}
	exit();
	break;
	
	case "printContractAttachment":
			echo "window.open('report/enhancerAttachment_pdf.php?refNo={$_GET['refNo']}');";
	exit();
	break;
	
	case 'getSTSToCancelInfo':
		$Up = $daObj->calculateUploadedAmtSum($_GET['refNo']);
		$Q = $daObj->calculateQueuedAmtSum($_GET['refNo']);
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
		$daObj->DropDownMenuD($daObj->makeArr($daObj->getCancelDates($_GET['refNo']),'stsApplyDate','stsApplyDate',''),'cmbCancelDate','','class="selectBox" ');
	exit();
	break;
	
	case 'CancelSTS':
		if($daObj->cancelSTS($_GET['refNo'],$_GET['txtReason'],$_GET['cmbCancelDate'])){
			echo "dialogAlert('Cancellation Process Succeeded!');\n";
		}else{
			echo "dialogAlert('There was an Error in the Cancellation of STS');";
		}
	exit();
	break;
	
	case 'computeNoAps':
		
		/*$dtFromM = date('m',strtotime($_GET['dtStart']));
		$dtToM = date('m',strtotime($_GET['dtEnd']));
		$dtFromYr =  date('y',strtotime($_GET['dtStart']));
		$dtToYr =  date('y',strtotime($_GET['dtEnd']));
		if($dtToYr >  $dtFromYr){
			$temp = 13 - $dtFromM;
			$temp += $dtToM;
			echo $temp;
		}else{
			if($dtFromM == $dtToM){
				echo "1";
			}else{
				echo $dtToM-$dtFromM+1;
			}
		}*/
		
		/*$dtFrom = date('m/d/Y',strtotime($_GET['dtStart']));
		$dtTo = date('m/d/Y',strtotime($_GET['dtEnd']));
		$nbrAps = dateDiff($dtFrom,$dtTo);
		echo ceil($nbrAps);*/
		
		$dtFrom = date('m/d/Y',strtotime($_GET['dtStart']));
		$dtTo = date('m/d/Y',strtotime($_GET['dtEnd']));
		echo applicationCounter($dtFrom,$dtTo);
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
			@import "../../includes/css/demo_page.css" "";
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
	$('#regSTSDetail,#regSTSDelDetail,#deleteRentables,#addRentables').attr('disabled','true');
	$('#txtApDate,#txtEndDate,#txtImStartDate,#txtImEndDate').datepicker();
	$('#refresh,#searchGo').button();
	$("#stsTable").jqGrid({ 
		url:'da.php?action=Load', 
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
			$("#hdnRefNo").val(ret.stsRefNo);
			//$("#printSTS").addClass("link");
			//$("#printSTS").attr("onclick","printSTS();");
			$("#contract").addClass("link");
			$("#contract").attr("onclick","printContract();");
			//$("#attachment").addClass("link");
			//$("#attachment").attr("onclick","printContractAttachment();");
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
		source: "da.php?action=searchSupplier",
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
				title: "NEW DISPLAY ALLOWANCE",
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
	if ($('#ckVat').is(":checked")){
		vatTag = "Y";
	}else{
		vatTag = "N";	
	}
	$.ajax({
		url: 'daDtl2.php',
		type: "POST",
		data: "act=Details&refNo="+refNo+'&vatTag='+vatTag,
		success: function(Data){
			$('#divAddEnhancer').html(Data);
		}				
	});	
	$("#divEnhancerDtl").dialog("destroy");
	$("#divEnhancerDtl").dialog({
		title: "STS Display Allowance Details",
		height: 380,
		width: 1500,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Save': function() {
				if(validateDaDtl()){
					$.ajax({
						url: 'daDtl2.php',
						type: "POST",
						data: $("#formEnhancerDtl").serialize()+'&action=addDaDtl',
						beforeSend: function() {
							ProcessData('Saving STS Details...','Open');
						},
						success: function(Data){
							LoadData2('da.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
							ProcessData('','Close');
							eval(Data);
						}				
					});
				}
			}
		}
	});
}
function validateDaDtl() {
	var val = true;
	if (validateCmb($("#cmbStr"))== false)
		val = false;
	if (validateString($("#txtDispTyp"))== false)
		val = false;
	if (validateString($("#txtBrand"))== false)
		val = false;
	if (validateString($("#txtLoc"))== false)
		val = false;
/*	if (validateString($("#txtSizeSpecs"))== false)
		val = false;*/
	if (validateString($("#txtDispSpecs"))== false)
		val = false;
	/*if (validateString($("#txtNoUnits"))== false)
		val = false;*/
	if (validateString($("#txtRem"))== false)
		val = false;
	/*if (validateString($("#txtUnitAmt"))== false)
		val = false;*/
	/*if (validateString($("#txtNoUnits"))== false)
		val = false;*/
	return val;
}

function editEnhancer(refNo,strCode){
	if ($('#ckVat').is(":checked")){
		vatTag = "Y";
	}else{
		vatTag = "N";	
	}
	$.ajax({
		url: 'daDtl2.php',
		type: "POST",
		data: "act=Details&refNo="+refNo+"&strCode="+strCode+'&vatTag='+vatTag,
		beforeSend: function() {
			ProcessData('Please wait...','Open');
		},
		success: function(Data){
			$('#divAddEnhancer').html(Data);
			ProcessData('','Close');
		}				
	});	
	$("#divAddEnhancer").dialog("destroy");
	$("#divAddEnhancer").dialog({
		title: "STS DA Details",
		height: 380,
		width: 1000,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Update': function() {
				if(validateDaDtl()){
					$.ajax({
						url: 'daDtl2.php',
						type: "POST",
						data: $("#formEnhancerDtl").serialize()+'&action=updateDaDtl',
						beforeSend: function() {
							ProcessData('Saving STS Details...','Open');
						},
						success: function(Data){
							LoadData2('da.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
							addRentables();
							//ProcessData('','Close');
							eval(Data);
						}				
					});
				}
			}
		}
	});
}
function validateEnhancer2(){
	var val=true;
	
	if (validateCmb($("#txtDispTyp"))== false)
		val = false;
	if (validateString($("#txtBrand"))== false)
		val = false;
	if (validateString($("#txtLoc"))== false)
		val = false;
	if (validateString($("#txtRem"))== false)
		val = false;
	return val;
}
function validateEnhacerDtl(){
	var ret = true;
	var checkCtr = 0;
	if(validateEnhancer2()){
		var counter = $('#hdCtr').val();
		for(a=0;a<=counter;a++) {	
			if ( $('#ch_'+a).attr('checked') ) {
				checkCtr++;
				if( $('#txtMonthly_'+a).val()=='0' || $('#txtMonthly_'+a).val()==''){
					dialogAlert("Invalid Total monthly amount!");
					ret = false;
				}
				if( $('#txtNoUnits_'+a).val()=='0' || $('#txtNoUnits_'+a).val()==''){
					dialogAlert("Invalid Number of Unit!");
					ret = false;
				}
				if( $('#txtUnitAmt_'+a).val()=='0' || $('#txtUnitAmt_'+a).val()==''){
					dialogAlert("Invalid Unit Amount!");
					ret = false;
				}
				if( $('#txtDispSpecs_'+a).val()=='0'){
					dialogAlert("Please select a valid Display Specification!");
					ret = false;
				}
				if( $('#txtSizeSpecs_'+a).val()=='0'){
					dialogAlert("Please select a valid Size Specification!");
					ret = false;
				}
			}
		}
	}else{
		dialogAlert("Required Field!");
		ret = false;
	}
	if(checkCtr==0){
				dialogAlert("You must add atleast one branch!");
				ret = false;
			}
	return ret;
}
function cancelSTS2(){
	var refNo = $('#hdnRefNo').val();
	$.ajax({
		url: 'da.php',
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
		url: 'da.php',
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
					url: 'da.php',
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
	var dateStart = $('#txtApDate').val();
	var dateEnd = $('#txtEndDate').val();
	if(validateInputs() && valDateStartEnd(dateStart,dateEnd,'txtApDate','txtEndDate') && valDateStartEnd(dateStart,dateEnd,'txtImStartDate','txtImEndDate')){
		$.ajax({
			url: "da.php",
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
function searchGrid(){
	var refNo = $("#searchRef").val();
	if(refNo==""){
		alert('Please type your reference number!');
		return false;	
	}
	$("#stsTable").jqGrid('setGridParam',{url:"da.php?action=Load&refNo="+refNo,page:1}).trigger("reloadGrid");
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
function valDateStartEnd(valStart,valEnd,id1,id2) {
	var parseStart = Date.parse(valStart);
	var parseEnd = Date.parse(valEnd);
	if (valStart !='' && valEnd !='') {
		if(parseStart > parseEnd) {
			$('#'+id1).addClass('ui-state-error');
			$('#'+id2).addClass('ui-state-error');
			dialogAlert("Date 'TO' Must Be Greater than Date 'FROM'");		
			return false;
		} else {
			$('#'+id1).removeClass('ui-state-error');
			$('#'+id2).removeClass('ui-state-error');	
			return true;
		}
	}else {
		$('#'+id1).addClass('ui-state-error');
		$('#'+id2).addClass('ui-state-error');
		dialogAlert("Please Select Date Range");			
		return false;
	}
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
		url: "da.php",
		type: "GET",
		traditional: true,
		data: 'action=getInfo&refNo='+refNo,
		success: function(msg){
			eval(msg);
		}				
	});	
	//LoadData('da.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+refNo);
	LoadData2('da.php','enhancerData','enhancerDetailList','action=loadEnhancerDetails&refNo='+refNo);
	$("#dialogAdd").dialog("destroy");
	$("#dialogAdd").dialog({
		title: "Edit Regular STS",
		height: 470,
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
	if ($('#ckVat').is(":checked")){
		vatTag = "Y";
	}else{
		vatTag = "N";	
	}
	$.ajax({
		url: 'daDtl2.php',
		type: "POST",
		data: "act=Details&refNo="+refNo+'&compCode='+compCode+'&vatTag='+vatTag,
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
		height: 520,
		width: 1000,
		modal: true,
		closeOnEscape: false,
		buttons: {
			'Enhancer': function(){
				if(validateDaDtl()){
					addRentables();
				}
			}
		}
	});	
}	

function addRentables() {
	var refNo = $('#hdnRefNo').val();
	
	$.ajax({
		url: 'rentableDtl.php',
		type: "GET",
		data: "act=getRentables&refNo="+refNo+'&startDate='+$("#txtImStartDate").val()+'&endDate='+$("#txtImEndDate").val(),
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
		// beforeClose :function(event,ui){ return false;},
		height: 520,
		width: 900,
		modal: true,
		//closeOnEscape: false,
		buttons: {
			'Save': function() {
				$.ajax({
					url: 'rentableDtl.php',
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
						url: 'daDtl2.php',
						type: "POST",
						data: 'action=Delete&refNo='+refNo,
						beforeSend: function() {
							ProcessData('Deleting STS Details...','Open');
						},
						success: function(Data){
							//LoadData('da.php','Data','STSDetailsList','action=LoadSTSDetails&refNo='+refNo);
							LoadData2('da.php','enhancerData','enhancerDetailList','ac
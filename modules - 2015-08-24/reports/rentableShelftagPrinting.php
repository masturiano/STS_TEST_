<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("rentableShelftagPrintingObj.php");
$rentableRptObj = new rentableShelftagPrintingObj();
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}
switch($_GET['action']){	
	
	case 'Print':
			//echo "window.open('rentableRpt_xls.php?{$_SERVER['QUERY_STRING']}');";
			echo "window.open('rentableShelftagPrinting_PDF.php?{$_SERVER['QUERY_STRING']}');";
	exit();
	break;
	
}
$arrMode = array('R'=>"APPROVED",'O'=>"UNAPPROVED");
$arrType = array("DET"=>"DETAILED","SUM"=>"SUMMARIZED");
$arrTran = array("0"=>'ALL','1'=>"REGULAR STS",'2'=>"LISTING FEE",'4'=>"SHELF ENHANCER",'5'=>"DISPLAY ALLOWANCE",'6'=>"PUSH GIRL",'7'=>"SAMPLING DEMO");
$arrPayMode = array("0"=>"ALL", "1"=>"Invoice Deduction", "2"=>"Check/Collection");

$arrAvailTag = array('N'=>"Not Available");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<link type="text/css" href="../../includes/jquery/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<link type="text/css" href="../../includes/jquery/development-bundle/demos/demos.css" rel="stylesheet" />
<script type="text/javascript" src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>


<script type="text/javascript">
	$(function(){
		$('#txtDateFrom, #txtDateTo').datepicker({
			dateFormat : 'mm/dd/yy'
		});
		$("#printInq, #printInqXls").button({
			icons: {
				primary: 'ui-icon-print',
			}
		});
		$("#txtSup").autocomplete({
			source: "transactions.php?action=searchSupplier",
			minLength: 1,
			select: function(event, ui) {	
				var content = ui.item.id;
				$("#hdnSuppCode").val(content);
			}
		});	    
		$("#printInq").click( function (){
			var dateStart = $('#txtDateFrom').val();
			var dateEnd = $('#txtDateTo').val();
			if (valDateStartEnd(dateStart,dateEnd,'txtDateFrom','txtDateTo')){
				$.ajax({
					url: 'transactions.php',
					type: "GET",
					data: $("#formInq").serialize()+'&action=Print'+$("#cmbType").val(),
					success: function(Data){
						eval(Data);
					}				
				});														
			}
		});
		$("#printInqXls").click( function (){
			var cmbStore = $('#cmbStore').val();
			var cmbAvailTag = $('#cmbAvailTag').val();
				if (valStore(cmbStore,'Please select store')){
					if (valAvailability(cmbAvailTag,'Please select availability')){
						$.ajax({
							url: 'rentableShelftagPrinting.php',
							type: "GET",
							data: $("#formInq").serialize()+'&action=Print',
							success: function(Data){
								eval(Data);
							}				
						});	
					}	
				}
		});
	});


function valStore(variable,value) {
	if (variable == 0) {
		$('#cmbStore').addClass('ui-state-error');
		dialogAlert(value);		
		return false;
	} else {
		$('#cmbStore').removeClass('ui-state-error');
		return true;
	}
}
function valAvailability(variable,value) {
	if (variable == '') {
		$('#cmbAvailTag').addClass('ui-state-error');
		dialogAlert(value);		
		return false;
	} else {
		$('#cmbAvailTag').removeClass('ui-state-error');
		return true;
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
function validateString(ObjName){
		if(ObjName.val().length == 0){
			ObjName.addClass("ui-state-error");
			return false;
		}
		else{
			ObjName.removeClass("ui-state-error");
			return true;
		}
	}
function validateInputs() {
		var val = true;
		if (validateCmb($("#cmbTran"))== false){
			dialogAlert("Required Field!");		
			val = false;	
		}
		return val;
	}
function enableDisable(bal){
	
	if(bal=="DET"){
		$('select[name="cmbStore"]').removeAttr("disabled");
	}else{
		$('select[name="cmbStore"]').attr("disabled","disabled");
	}
}
</script>

<style type="text/css">
.textBox {
	border: solid 1px #222; 
	border-width: 1px; 
	width:130px; 
	height:18px;
	font-size: 11px;
}
.selectBox {
	border: 1px solid #222; 
	width:132px; 
	height:22px;
	font-size: 11px;
}
.hd {
	font-size: 11px;
	font-family: Verdana;
	font-weight: bold;
}
</style>
</head>

<body >


<h2 class="ui-widget-header ui-corner-all" style="padding:5px;">RENTABLE REPORT</h2>
<div class="ui-widget-content" style="padding:5px;">
	<table width="40%" border="0" cellspacing="3" cellpadding="2" align="center">
    	<form name="formInq" id="formInq">
    	<tr>
        	<td class="hd" ><strong>Store: </strong></td>
           	<td><? $rentableRptObj->DropDownMenu($rentableRptObj->makeArr($rentableRptObj->getBranches(),'strCode','strCodeName',''),'cmbStore','','class="selectBox"'); ?></td>
            
            <td class="hd" ><strong>Specs: </strong></td>
            <td><? $rentableRptObj->DropDownMenu($rentableRptObj->makeArr($rentableRptObj->findDisplaySpecs(),'displaySpecsId','displaySpecsDesc',''),'cmbdisplaySpecs','','class="selectBox"'); ?></td>
    
        </tr>
       <tr>
       </form>
        	<td colspan="10" align="center"> <button id="printInqXls">Print in PDF</button></td>
       </tr> 
        
	</table>
    
    <div id='dialogAlert' title='STS'>
            <p id='dialogMsg'></p>
    	</div>
</div>
</body>
</html>
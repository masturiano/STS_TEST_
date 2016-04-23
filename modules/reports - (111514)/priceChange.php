<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("reportsObj.php");
$reportsObj = new reportsObj();
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}
switch($_GET['action']){	
	
}
$arrMode = array('R'=>"APPROVED",'O'=>"UNAPPROVED");
$arrType = array("DET"=>"DETAILED","SUM"=>"SUMMARIZED");
$arrTran = array("0"=>'ALL','1'=>"REGULAR STS",'2'=>"LISTING FEE",'4'=>"SHELF ENHANCER",'5'=>"DISPLAY ALLOWANCE",'6'=>"PUSH GIRL",'7'=>"SAMPLING DEMO");
$arrPayMode = array("0"=>"ALL", "1"=>"Invoice Deduction", "2"=>"Check/Collection");
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
		
		$("#printInqXls").click( function (){
			var dateStart = $('#txtDateFrom').val();
			var dateEnd = $('#txtDateTo').val();
			if (valDateStartEnd(dateStart,dateEnd,'txtDateFrom','txtDateTo')){
				$.ajax({
					url: 'priceChange.php',
					type: "GET",
					data: $("#formInq").serialize()+'&action=Print',
					success: function(Data){
						eval(Data);
					}				
				});														
			}
			
		});
		
	});
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

<body>


<h2 class="ui-widget-header ui-corner-all" style="padding:5px;">PRICE CHANGE INQUIRY</h2>
<div class="ui-widget-content" style="padding:5px;">
	<table width="50%" border="0" cellspacing="3" cellpadding="2">
    	<form name="formInq" id="formInq">
    	<tr>
        	
        	<td class="hd" ><strong>Date From: </strong></td>
           	<td><input type="text" name="txtDateFrom" id="txtDateFrom" readonly="readonly" class="textBox"/></td>
    	
        	<td class="hd">Date To:</td>
            <td><input type="text" name="txtDateTo" id="txtDateTo" readonly="readonly" class="textBox" /></td>
            </form>
        	<td colspan="6" align="center"> <button id="printInqXls">Print in EXCEL</button></td>
        </tr>
	</table>
    
    <div id='dialogAlert' title='STS'>
            <p id='dialogMsg'></p>
    	</div>
</div>
</body>
</html>
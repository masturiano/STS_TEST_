<?
session_start();

include("../../includes/db.inc.php");
include("../../includes/common.php");

include("allowanceObj.php");
$allowanceObj = new allowanceObj();

if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}

switch($_POST['action']) {
	
	case "AddSTSDtl":
		$total = 0;
		for($i=0;$i<=(float)$_POST['hdCtr'];$i++) {
			if($_POST["txt_$i"]!="") {
				$total += (float)$_POST["txt_$i"];
			}
		}
		$dtlAmt = $_POST['hdDtl_TotAmt'];		
		//if(sprintf('%01.2f', $total) == sprintf('%01.2f', $_POST["hdDtl_Amt"])){
			if($allowanceObj->AddSTSDtl($_POST)) {
				echo '$("#divSTSDtls").dialog("close");';
				echo "dialogAlert('STS Detail successfully added.');\n";
			} else {
				echo "dialogAlert('Error adding STS Detail.');";
			}
		/*} else {
			echo "dialogAlert('STS Header Amount is not equal to STS Details Total Amount.','dialogAlertFunds');";
		}*/
		exit();
	break;
	case "Delete":
			if($allowanceObj->DeleteSTSDtl($_POST['refNo'])) {
				echo "$('#dialogAlert').dialog('close');";
				echo "dialogAlert('STS Detail successfully deleted.');";
			} else {
				echo "dialogAlert('Error deleting STS Detail.');";
			}	
		exit();
	break;

}
?>
<html>
<head>
<title>Untitled Document</title>
<script type="text/javascript">

$(function(){
	$("#txtEventNo2").autocomplete({
			source: "regularSTS.php?action=searchEvent",
			minLength: 1,
			select: function(event, ui) {	
				var content = ui.item.id.split("|");
				$("#hdnEventType2").val(content[0]);
				$("#txtEventType2").val(content[1]);
			}
		});	 
});
 function CopyTxt(objSource,objClip) {
	 	$('#'+objClip).val($('#'+objSource).val());
	 }
	 function PasteTxt(objClip,objDest) {
	 	$('#'+objDest).val($('#'+objClip).val());
		if (objClip=='hdClipTxt') {
			ComputeDetails();
		}
	 }

	function ToggleBranch(id) {
		var chckBox = $('#ch_'+id);
		//var enhance = $('#hdnEnhanceType').val();
		if (chckBox.attr('checked')) {
			$('#txt_'+id).attr('readonly',false);
			$('#txt_'+id).addClass('enabletxt');
			$('#cmb_'+id).attr('disabled',false);
			$('#cmb_'+id).addClass('enabletxt');
			
			$('#chEvent_'+id).attr('disabled',false);
			
			ComputeDetails();
		} else {
			$('#txt_'+id).attr('readonly','true');
			$('#txt_'+id).removeClass('enabletxt');
			$('#cmb_'+id).attr('disabled','true');
			$('#cmb_'+id).removeClass('enabletxt');
			$('#txt_'+id).val('');
			$('#txtVatAmt_'+id).val('');
			$('#txtEwtAmt_'+id).val('');
			$('#txtSubAmt_'+id).val('');
			
			$('#chEvent_'+id).attr('checked',false);
			$('#chEvent_'+id).attr('disabled','true');
			ComputeDetails();
		}
		
	}
	function checkAllDtl() {
		$('#dvTotDtdl').html("");
		var chldCnt = $('#hdCtr').val();
		//var enhance = $('#hdnEnhanceType').val();
		if ($('#chAll').attr('checked')) {
			for(i=0;i<=chldCnt;i++){
				$('#ch_'+i).attr('checked','true');
				$('#txt_'+i).attr('readonly',false);
				$('#txt_'+i).addClass('enabletxt');
				$('#chEvent_'+i).attr('disabled',false);
			}
		} else {
			for(i=0;i<=chldCnt;i++){
				$('#ch_'+i).attr('checked',false);
				$('#txt_'+i).val('');
				$('#txt_'+i).attr('readonly','true');
				$('#txt_'+i).removeClass('enabletxt');
				
				$('#txtVatAmt_'+i).val('');
				$('#txtEwtAmt_'+i).val('');
				$('#txtSubAmt_'+i).val('');
				
				$('#chEvent_'+i).attr('checked',false);
				$('#chEvent_'+i).attr('disabled','true');
				$('#txtExpDtlSum').val(0);
			}
		}
	}
	function checkAllEvent(){
		var chldCnt = $('#hdCtr').val();
		if ($('#chAllEvent').attr('checked')) {
			for(i=0;i<=chldCnt;i++){
				if ($('#ch_'+i).attr('checked')) {
					$('#chEvent_'+i).attr('checked',true);
				}
			}
		}else {
			for(i=0;i<=chldCnt;i++){
				if ($('#ch_'+i).attr('checked')) {
					$('#chEvent_'+i).attr('checked',false);
				}
			}
		}
	}
	function ComputeDetails() {
		var counter = $('#hdCtr').val();
		var amt = 0;
		var id = "";
		for(i=0;i<=counter;i++) {
			if ($('#txt_'+i).val() != '')
				amt = amt + parseFloat($('#txt_'+i).val());
		}
		amt = Math.round(amt*10000)/10000;
		$('#dvTotDtdl').html(amt);
	}	
	function addDecimal(amt){
		amt = Math.round(amt * 100) / 100;
		return amt;
	}
	function reCompute(i){
		var vat = parseFloat($("#hdnVat").val());
		var ewt = parseFloat($("#hdnEwt").val());
		
		$('#txtVatAmt_'+i).val(addDecimal(parseFloat($('#txt_'+i).val())*parseFloat(vat)));
		$('#txtEwtAmt_'+i).val(addDecimal($('#txt_'+i).val()*ewt*-1));
		$('#txtSubAmt_'+i).val(addDecimal(parseFloat($('#txt_'+i).val())+($('#txt_'+i).val()*vat)));
	}
	function Allocate() {
		var counter = $('#hdCtr').val();
		var amount = $('#hdDtl_Amt').val();
		
		var noPar = percent = ctr = allocPerParticipant = accumulatedFund = accumulatedVat = accumulatedTax = vatTotamt=taxTotamt=0;
		
		var vat = parseFloat($("#hdnVat").val());
		var ewt = parseFloat($("#hdnEwt").val());
		
		for(a=0;a<=counter;a++){
			if ( $('#ch_'+a).attr('checked') ) {
				noPar++;	
			}
		}
		percent = (100/noPar);
		for(a=0;a<=counter;a++) {	
			if ( $('#ch_'+a).attr('checked') ) {	
				var allocPerParticipant = 0;
				ctr++;	
				if (ctr!=noPar) {
					allocPerParticipant = amount * (percent/100);
					allocPerParticipant = Math.round(allocPerParticipant*100)/100;
					
					vatamt = Math.round(allocPerParticipant*vat*100)/100;
					taxamt = Math.round(allocPerParticipant*ewt*100)/100;
					
				} else {
					allocPerParticipant = amount - accumulatedFund;
					
					vatamt = (amount*vat)-accumulatedVat;
					vatamt = Math.round(vatamt*100)/100;
					
					taxamt = (amount*ewt)-accumulatedTax;
					taxamt = Math.round(taxamt*100)/100;
					
					allocPerParticipant = Math.round(allocPerParticipant*100)/100;
				}
				//accumulatedFund += allocPerParticipant;	
				
				accumulatedFund += allocPerParticipant;	
				accumulatedVat += vatamt;	
				accumulatedTax += taxamt;	
				$('#txt_'+a).val(allocPerParticipant);
				vatTotamt += vatamt;
				taxTotamt += taxamt;				
				$('#txtVatAmt_'+a).val(vatamt+'');
				$('#txtEwtAmt_'+a).val(taxamt*-1+'');
				
				/*$('#txt_'+a).val(allocPerParticipant);
				$('#txtVatAmt_'+a).val(addDecimal(parseFloat($('#txt_'+a).val())*parseFloat(vat)));
				$('#txtEwtAmt_'+a).val(addDecimal($('#txt_'+a).val()*ewt*-1));*/
				$('#txtSubAmt_'+a).val(addDecimal(allocPerParticipant+vatamt));
		
				$('#dvTotDtdl').html(amount);	
			}
		}	
	}	
	
	function applyEvent(){
		var eventNo = $('#txtEventNo2').val();
		var eventType = $('#txtEventType2').val();
		var hdnEventType = $('#hdnEventType2').val();
	
		var chldCnt = $('#hdCtr').val();		
		var ctr = 0;
		
		if($("#txtEventType2").val()==""){
			dialogAlert("Invalid Event Number!");	
			return false;
		}
		for(i=0;i<=chldCnt;i++){
			if ($('#chEvent_'+i).attr('checked')) {
				$('#event_'+i).val(""+eventNo);
				$('#eventType_'+i).val(""+eventType);
				$('#hdnEventType_'+i).val(""+hdnEventType);
				ctr++;
			}
			
		}
		if(ctr==0){
			dialogAlert("ERROR: You must select a store where you want to apply this event first!");	
		}
	}
	
	function unselectEvent(){
		var chldCnt = $('#hdCtr').val();
		for(i=0;i<=chldCnt;i++){
			if ($('#chEvent_'+i).attr('checked')) {
				$('#chEvent_'+i).attr('checked',false);
			}
		}
	}
	function clearSelectedEvent(){
		var chldCnt = $('#hdCtr').val();
		for(i=0;i<=chldCnt;i++){
			if ($('#chEvent_'+i).attr('checked')) {
				$('#event_'+i).val("");
				$('#eventType_'+i).val("");
				$('#hdnEventType_'+i).val("");
			}
		}	
	}
	function clearEvent2(){
		$("#txtEventNo2,#txtEventType2,#hdnEventType2").val('');
	}
</script>
<style type="text/css">
<!--
.textBox2 {
	border: solid #999999; 
	border-width: 1px; 
	width:100px; 
	height:18px;
	font-size: 11px;
}
#formExpenseDtl input.error {
	background: #f8dbdb;
	border-color: #e77776;
}
#formExpenseDtl select{
	border: 1px solid #999999; 
	width:192px; 
	height:22px;
	font-size: 11px;
}
#formExpenseDtl select.error {
	background: #f8dbdb;
	border-color: #e77776;
}

.headerContentDtl {
	border: solid #666666 1px;
	padding: 3px;
	background-color:#E9E9E9;
}
.textBox1 {	border: solid #999999; 
	border-width: 1px; 
	width:190px; 
	height:18px;
	font-size: 11px;
}
.txtInv {
	visibility:hidden;
}
.txtVis {
	visibility:visible;
	cursor:pointer;
}
.txtVis2 {
	visibility:visible;
}
.enabletxt {
	background: #C7DAFE;
	border-color: #e77776;
}
-->
</style>         
</head>

<body>
	<?
    if ($_POST['act']=='Details') {
		
		$stsRefNo = $_POST['refNo'];
		$arrSTS = $allowanceObj->getSTSInfo($stsRefNo);
		$arrBranches =  $allowanceObj->getFilteredBranches($_POST['compCode'],$_POST['refNo']);
		$DtlAmt = $arrSTS['stsAmt'];
		
		if($_POST['vatTag']=='N'){
			$ewt = 0;	
			$vat = 0;
		}else{
			$ewt = $allowanceObj->getEwt($_POST['idept'],$_POST['iclass'],$_POST['isubClass']);
			$vat = $allowanceObj->getVat();
		}
		
		if($_POST['pcaTag']=='Y'){
			$templateDisable = "";
		}else{
			$templateDisable = "disabled='disable'";
		}
	?>
	<form id="formSTSDtl" name="formSTSDtl">
	<div class="headerContentDtl ui-corner-all">
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr>
          	<td colspan="6">
            	<?
				$ch = 0;
             	$arrCompanies = $allowanceObj->getDistinctCompanies();
			  foreach($arrCompanies as $valComp) {
				  	if($ch==0) {
						$bar = "";
						$ch++;
					} else {
						$bar = " | ";
					}
				?>
                <?=$bar;?><span class="link" style="color:#F60;" onClick="AddSTSDtl(<?=$valComp['compCode'];?>)"><?=$valComp['compShort'];?></span>
                <? }?>
                 |&nbsp;<span class="link" onClick="AddSTSDtl('undefined')" style="color:#F60;" ><span style="font-family: Verdana;">All</span></span> 
            </td>
            <td>&nbsp;
            </td>
          </tr>
          <tr>
            <td height="25" colspan="6">
            	<span  class="link" onClick="Allocate();" style="color:#F60;">Allocate Equally</span>
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="25" colspan="6">
            	<hr>
            	<span style="color:#06F;">PCA Template: &nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="hidden" name="hdnPcaTag" id="hdnPcaTag" value="<?=$_POST['pcaTag'];?>" />
                <input type="hidden" name="hdnEventType2" id="hdnEventType2" value='<?=$_POST['eventType'];?>' />
                Event No
                <input type="text" name="txtEventNo2" id="txtEventNo2" class="textBox" style="width:70px;" onclick="clearEvent2();" value='<?=$_POST['eventNo']?>' <?=$templateDisable?>/>
                &nbsp;&nbsp;&nbsp;&nbsp;
                Event Type
                <input type="text" name="txtEventType2" id="txtEventType2" class="textBox" disabled="disabled" style="width:70px;" value='<?=$_POST['eventTypeDtl']?>'/>
                 &nbsp;&nbsp;&nbsp;&nbsp;
            	<input type="button" id="btnApplyAll" name="btnApplyAll" value="Apply" onClick="applyEvent();" style="color:#f20;" <?=$templateDisable?>>
                <input type="button" id="btnClear" name="btnClear" value="Clear" onClick="clearSelectedEvent();" style="color:#f20;" <?=$templateDisable?>>
                <input type="button" id="btnClear" name="btnClear" value="Unselect" onClick="unselectEvent();" style="color:#f20;" <?=$templateDisable?>>
                <hr>
            </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="3%" height="25"><input type="checkbox" name="chAll" onClick="checkAllDtl();"  id="chAll"></td>
              <td width="37%"><strong>Branch
             	<input type="hidden" name="hdnEwt" id="hdnEwt" value="<?=$ewt?>" />
                <input type="hidden" name="hdnVat" id="hdnVat" value="<?=$vat?>" />
				<input type="hidden" name="hdDtl_Amt" value="<?=$arrSTS['stsAmt']?>" id="hdDtl_Amt" />
				<input type="hidden" name="hdnEnhanceType" value="<?=$arrSTS['enhanceType']?>" id="hdnEnhanceType" />
                <input type="hidden" name="hdDtl_refNo" value="<?=$_POST['refNo']?>" id="hdDtl_refNo" />
				<input type="hidden" name="hdDtl_TotAmt" value="<?=(float)$DtlAmt?>" id="hdDtl_TotAmt" />
                <input type="hidden" name="hdClipTxt" value="" id="hdClipTxt" />
            </strong></td>
            <td align="center"><strong>Amount</strong></td>
            
            <td align="left"><input type="checkbox" name="chAllEvent" onClick="checkAllEvent();"  id="chAllEvent"><strong>Event No / Type</strong></td>
            <td align="center"><strong>VAT Amount</strong></td>
            <td align="center"><strong>Gross Amount</strong></td>
            
          </tr>
            <tr>
            	<td colspan="2" height="5"></td>
            </tr>
            <?
			$i = 0;
            foreach($arrBranches as $val) {
				$checked = ($val['stsAmt'] != '') ? 'checked':'';
           		$readonly = ($val['stsAmt'] != '') ? "class='textBox2 enabletxt' value=".$val['stsAmt']:" readonly class='textBox2'";
				$txtInv =  ($val['stsAmt'] != '') ? "":"txtInv";
				$disableEventType  = ($val['stsAmt'] != '') ? '':"disabled='disable'";
				
				$value2 = ($val['stsVatAmt'] != '') ? "value=".$val['stsVatAmt']:"value="."''";
				
				$value4 = ($val['stsAmt'] != '') ? "value=".round($val['stsAmt']+$val['stsVatAmt'],2):"''";
				
				$valueEventNo = ($val['eventNo'] != '')  ? "value='".$val['eventNo']."'" : "value=''";
				$valueEventType = ($val['eventType'] != '')  ? "value='".$val['eventType']."'" : "value=''";
				if($val['eventType']=='R'){
					$eventType = "Regular";	
				}elseif($val['eventType']=='R'){
					$eventType = "Add Promo";
				}else{
					$eventType = $val['eventType'];
				}
				$valueEventTypeDtl = ($val['eventType'] != '')  ? "value='".$eventType."'" : "value=''";
				
			?>
            <tr>
              
              <td class=""><input type="checkbox" <?=$checked?>  name="ch_<?=$i;?>" onClick="ToggleBranch('<?=$i;?>');" value="<?=$val['strCode'];?>" id="ch_<?=$i;?>"></td>
              <input type="hidden" name="comp_<?=$i;?>" id="comp_<?=$i;?>" value="<?=$val['compCode'];?>" />
              
              <td height="25"><?=$val['brnDesc'];?></td>
              <td><input name="txt_<?=$i;?>"  type="text" onKeyPress="return valNumInputDec(event)" onChange="ComputeDetails();" onKeyUp="reCompute('<?=$i?>');" <?=$readonly?> id="txt_<?=$i;?>" style="text-align:right; width:85px;"  size="12"/>&nbsp;</td>
              
              <td>
              	<input type="checkbox"  name="chEvent_<?=$i;?>"  value="<?=$val['strCode'];?>" id="chEvent_<?=$i;?>"  <?=$disableEventType;?> >
                
              	<input name="event_<?=$i;?>"  type="text" id="event_<?=$i;?>" style="text-align:right; width:70px;"  size="6"  readonly <?=$valueEventNo;?> />
              	<input name="eventType_<?=$i;?>"  type="text" id="eventType_<?=$i;?>" style="text-align:right; width:60px;"  size="6" disabled="disable" <?=$valueEventTypeDtl;?>/>
                <input type="hidden" name="hdnEventType_<?=$i;?>" id="hdnEventType_<?=$i;?>" <?=$valueEventType;?>/>
              </td>
              
              <td><input name="txtVatAmt_<?=$i;?>"  type="text" onKeyPress="return valNumInputDec(event)" onChange="ComputeDetails();" <?=$value2?> id="txtVatAmt_<?=$i;?>" style="text-align:right" size="10" readonly></td>
              <td><input name="txtSubAmt_<?=$i;?>"  type="text" onKeyPress="return valNumInputDec(event)" onChange="ComputeDetails();" <?=$value4?> id="txtSubAmt_<?=$i;?>" style="text-align:right" size="10" readonly></td>
             	
            </tr>
            <?
				$i++;
            }
			?>
            <tr>
              <td class=""></td>
              <td height="25"><strong>TOTAL</strong></td>
              <td><div id="dvTotDtdl" style="font-weight:bold; text-align:center;"></div></td>
            </tr>
          </table>  
      <input type="hidden" name="hdCtr" id="hdCtr" value="<?=($i-1);?>" />
    </div>
      </form>
      <?} else {?>
<?}?>   

</body>
</html>

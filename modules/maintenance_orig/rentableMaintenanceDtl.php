<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("maintenanceObj.php");
$maintObj = new maintenanceObj;

$now = date('Y-m-d H:i:s');
if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}

	switch($_POST['action']) {
		case 'addRentable':
			if( $maintObj->AddSTSRentablesDtl($_POST)) {
				echo "$('#divSTSDtls2').dialog('destroy');";
				echo "$('#divSTSDtls2').dialog('close');";
				echo "dialogAlert('STS rentables successfully added.');\n";
			} else {
				echo "dialogAlert('Error/No Rentables Added.');";
			}
		exit();
		break;
		
		case 'untagRentables':
			$daObj->untagRentables($_POST['refNo'],$_POST['strCode']);
			echo "dialogAlert('Rentables Deleted');";
		exit();
		break;
	}


?>


<html>
<head>

</head>

<title>Rentables</title>

<body>

<? 
if($_GET['action']=='view'){
	$arr = $maintObj->getHeaderInformation($_GET['refNo']);
	
	if((int)count($arr)==1){
		echo "<script>alert('STS Reference number not found!');</script>";
		exit();
	}
	
	$arr2 = $maintObj->getRentableBranches($_GET['refNo']);
?>
<center>
<table width="80%" border="0" cellspacing="0" cellpadding="0">
    	<tr>
        	<td class='hd'>STS REF NO: </td>
            <td class='hd2'><? echo $arr['stsRefno'];?></td>
            
            <td class='hd'>STS TOTAL AMT: </td>
            <td class='hd2'><? echo number_format($arr['stsAmt'],2);?></td>
            
             <td class='hd'>CURRENT SUPPLIER: </td>
            <td class='hd2'><? echo $arr['suppCode'].'-'.$arr['ASNAME'];?></td>
            <input type="hidden" name="hdnSuppCode" id="hdnSuppCode" value="<?=$arr['suppCode']?>" />
        </tr>
        <tr>
        	<td class='hd'>ENTERED BY: </td>
            <td class='hd2'><? echo $arr['fullName'];?></td>
            
            <td class='hd'>ENTRY DATE: </td>
            <td class='hd2'><? echo date('m/d/Y',strtotime($arr['dateEntered']));?></td>
            
             <td class='hd'>DATE APPROVED: </td>
            <td class='hd2'><? echo date('m/d/Y',strtotime($arr['dateApproved']));?></td>
        </tr>
         <tr>
        	<td class='hd'>APPLICATION DATE: </td>
            <td class='hd2'><? echo date('m/d/Y',strtotime($arr['applyDate']));?></td>
            
            <td class='hd'>NUMBER OF APPLICATION: </td>
            <td class='hd2'><? echo $arr['nbrApplication'];?></td>
            
             <td class='hd'>PAYMENT MODE: </td>
            <td class='hd2'><? echo $arr['payMode'];?></td>
        </tr>	
         <tr>
        	<td class='hd'>REMARKS: </td>
            <td class='hd2'><? echo $arr['stsRemarks'];?></td>
        </tr>	
        
    </table>
   </center>
    <hr />
    


<form name="frmRentable" id="frmRentable">

<input type="hidden" name="hdnDateFrom" id="hdnDateFrom" value="<?=$_GET['startDate']?>"/>
<input type="hidden" name="hdnDateTo" id="hdnDateTo" value="<?=$_GET['endDate']?>"/>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
	<tr>
        <td class='hd'>REASON: </td>
        <td class='hd2'><input type="text" name="txtReason" id="txtReason" class="textBoxLong"/></td>
    </tr>	
</table>
<h2 class="ui-widget-content">Rentable List</h2>
<input type="hidden" name="refNo" id="refNo" value="<?=$_GET['refNo']?>"/>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">

<? foreach($arr2 as $val){?>
		<tr>
        	<td align="left" width="250px;"><strong><?=$val['strCode']." - ".$val['brnDesc']?></strong>
           
            </td>
            <td>
            <? $arrDispStr = $maintObj->getRentableDtlsTactical($_GET['refNo'],$val['dispSpecs'],$val['strCode'],$_GET['startDate'],$_GET['endDate']); ?>
            
            <? foreach($arrDispStr as $valstr) {?>
            	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <? 
					if($_GET['refNo']==$valstr['stsRefNo']){
						$checked = "checked";		
						$switcherR_val = "1";
					}else{
						$checked = "";
						$switcherR_val = "0";
					}
				?>
            	<input type="checkbox" name="checkDisp_<?=$val['strCode'].$valstr['dispDtlId'];?>" id="checkDisp_<?=$val['strCode'].$valstr['dispDtlId'];?>" onClick="toggleRentables(<?=$val['strCode'].$valstr['dispDtlId'];?>);" <?=$checked?> />
                <input type="hidden" name="hdnSwitcherRentables_<?=$val['strCode'].$valstr['dispDtlId'];?>" id="hdnSwitcherRentables_<?=$val['strCode'].$valstr['dispDtlId'];?>"  value='<?=$switcherR_val?>'/>
                <label><?=$valstr['dispDesc']?></label>
                
            <? } ?>
            <hr />
          </td>  
        </tr>

<? } ?>

</table>
</form>
<table align="center">
	<tr>
        <td colspan="3" align="center"><button id="btnSave" onClick="saveDtl();">SAVE</button></td>
    </tr>
</table>
<?
}
?>
</center>
</body>

</html>

<script type="text/javascript">

function toggleRentables(id){
	var chckBox = $('#checkDisp_'+id);
	if (chckBox.attr('checked')) {
		$('#hdnSwitcherRentables_'+id).val("1");
	}else{
		$('#hdnSwitcherRentables_'+id).val("0");
	}
}
function toggleRentables2(id){
	var chckBox = $('#ckPerm_'+id);
	if (chckBox.attr('checked')) {
		$('#switcherPerm_'+id).val("1");
	}else{
		$('#switcherPerm_'+id).val("0");
	}
}
function saveDtl(){
	if(validateString($("#txtReason"))){
		$.ajax({
			url: 'rentableMaintenanceDtl.php',
			type: "POST",
			data: 'action=addRentable'+'&'+$("#frmRentable").serialize()+'&suppCode='+$("#hdnSuppCode").val(),
			success: function(Data){
				eval(Data);
			}				
		});	
	}else{
		alert("Required Field!");	
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
</script>
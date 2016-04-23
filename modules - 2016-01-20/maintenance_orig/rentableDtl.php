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
		case 'saveRentables':
			$result = $maintObj->updateRentables($_POST);
			if($result!=0) {
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
if($_GET['action']=='getBrandDtl'){
	$arr = $maintObj->getRentableStores($_GET['cmbSpecs'],$_GET['cmbStore'],$_GET['cmbGrp']);
?>
<h2 class="ui-widget-content">Rentable List</h2>
<center>
<form name="frmRentable" id="frmRentable">
<table width="80%" border="0" cellspacing="0" cellpadding="2" align="center">
	<tr class="ui-widget-header">
    	<td class="hd">Rentable Space</td>
    	<td class="hd">Description</td>
        <td class="hd">Group</td>
        <td class="hd">Location</td>
        <td class="hd"  align="center">Store Availability</td>
        <td class="hd"  align="center">Is Permanent</td>
        <input type="hidden" name="hdnSpecs" id="hdnSpecs" value="<?=$_GET['cmbSpecs']?>">
        <input type="hidden" name="hdnStore" id="hdnStore" value="<?=$_GET['cmbStore']?>">
    </tr>
	<?
		$ctrD = 0;
		//var_dump($arr);
		foreach($arr as $val){		
			$rentCtr++;
			if($val['availabilityTag']=='Y'){
				$checked = "checked";	
				$switcherR_val = "1";
				echo "<script>$('#ckPerm_".$ctrD."').removeAttr('disabled');</script>";
				$disabler = "";
			}else{
				$checked = "";
				$switcherR_val = "0";
				echo "<script>$('#ckPerm_".$ctrD."').attr('disabled','true');</script>";
				$disabler = "disabled";
			}
			if($val['permanentTag']=='Y'){
				$checked2 = "checked";
				$switcherR_val2 = "1";
			}else{
				$checked2 = "";
				$switcherR_val2 = "0";
			}
	?>	
    		<tr class="ui-widget-content" style="border-bottom:thin; border-bottom-color:#000;">
            	<input type="hidden" name="switcherRent_<?=$ctrD;?>" id="switcherRent_<?=$ctrD;?>" value='<?=$switcherR_val?>'/>
                <input type="hidden" name="switcherPerm_<?=$ctrD;?>" id="switcherPerm_<?=$ctrD;?>" value='<?=$switcherR_val2?>'/>
                <input type="hidden" name="hdnSpecsId_<?=$ctrD;?>" id="hdnSpecsId_<?=$ctrD;?>" value="<?=$val['displaySpecsId']?>">
                
                <td class="hd"><? echo $val['displaySpecsDesc'] ?></td>
                <td class="hd"><? echo $val['dispDesc'] ?></td>
                 
                  <td><? $maintObj->DropDownMenu($maintObj->makeArr($maintObj->getGrpList(),'minCode','description',''),'cmbGrp_'.$ctrD,''.$val['grpCode'],'class="selectBox" '.$disabler); ?></td>
                   <td><? $maintObj->DropDownMenu($maintObj->makeArr($maintObj->getLocationList(),'locId','description',''),'cmbLoc_'.$ctrD,''.$val['locId'],'class="selectBox" '.$disabler); ?></td>
            	<td align="center"><input type="checkbox" id="ckStr_<?php echo $ctrD;?>" name="ckStr_<?php echo $ctrD;?>" value="<?=$val['dispDtlId']?>" onClick="toggleRentables(<?=$ctrD;?>);" <?=$checked?> /></td>
                
                <td align="center"><input type="checkbox" id="ckPerm_<?php echo $ctrD;?>" name="ckPerm_<?php echo $ctrD;?>" value="<?=$val['dispDtlId']?>" onClick="toggleRentables2(<?=$ctrD;?>);" <?=$checked2?> disabled/></td>
            	
            </tr>
           
	<?		$ctrD++;
		} 
	?>
    	 
</table>
 <input type="hidden" name="hdRentCtr" id="hdRentCtr" value="<?=($ctrD-1);?>" />
</form>
<table>
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
$(function(){
		$("#btnSave").button();
	});
function toggleRentables(id){
	var chckBox = $('#ckStr_'+id);
	if (chckBox.attr('checked')) {
		$('#switcherRent_'+id).val("1");
		$('#ckPerm_'+id).removeAttr('disabled');
		$('#ckPerm_'+id).attr('checked',true);
		
		$('#cmbGrp_'+id).removeAttr('disabled');
		$('#cmbLoc_'+id).removeAttr('disabled');
	}else{
		$('#ckPerm_'+id).attr('disabled','true');
		$('#switcherRent_'+id).val("0");
		$('#ckPerm_'+id).attr('checked',false);
		$('#cmbGrp_'+id).attr('disabled',true);
		$('#cmbLoc_'+id).attr('disabled',true);
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
	$.ajax({
		url: "rentableDtl.php",
		type: "POST",
		traditional: true,
		data: $("#frmRentable").serialize()+'&action=saveRentables',
		success: function(msg){
			eval(msg);
		}
	});	
}
</script>
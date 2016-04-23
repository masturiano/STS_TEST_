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

	switch($_POST['act']) {
		case 'saveRentables':
			$result = $daObj->AddSTSRentablesDtl($_POST);
			if($result!=0) {
				
				echo "$('#txtNoUnits_".$_POST['id']."').val('$result');";
				echo "computeMonthly2(".$_POST['id'].");";
				echo '$("#divWindowRentable").dialog("close");';
				echo "dialogAlert('STS rentables successfully added.');\n";
				
			} else {
				echo "$('#txtNoUnits_".$_POST['id']."').val('');";
				echo "computeMonthly2(".$_POST['id'].");";
				echo '$("#divWindowRentable").dialog("close");';
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
if($_GET['action']=='getRentables'){
	$arr = $daObj->getRentableDtlsTactical($_GET['refNo'],$_GET['displaySpecs'],$_GET['strCode']);
?>
<h2 class="ui-widget-content">Rentable List</h2>
<form name="frmRentable" id="frmRentable">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<?
		$ctrD = 0;
		foreach($arr as $val){		
			$rentCtr++;
			if($_GET['refNo']==$val['stsRefNo']){
				$checked = "checked";		
				$switcherR_val = "1";
			}else{
				$checked = "";
				$switcherR_val = "0";
			}
	?>	
    		<tr>
            	<input type="hidden" name="switcherR_<?=$ctrD;?>" id="switcherR_<?=$ctrD;?>" value='<?=$switcherR_val?>'/>
            	<td width="10%"><input type="checkbox" id="ckRent_<?php echo $ctrD;?>" name="ckRent_<?php echo $ctrD;?>" value="<?=$val['dispDtlId']?>" onClick="toggleRentables(<?=$ctrD;?>);" <?=$checked?> /></td>
            	<td><? echo $val['dispDesc'] ?></td>
            </tr>
	<?		$ctrD++;
		} ?>
</table>
 <input type="hidden" name="hdRentCtr" id="hdRentCtr" value="<?=($ctrD-1);?>" />
</form>
<?
}
?>

</body>

</html>

<script type="text/javascript">

function toggleRentables(id){
	var chckBox = $('#ckRent_'+id);
	if (chckBox.attr('checked')) {
		$('#switcherR_'+id).val("1");
	}else{
		$('#switcherR_'+id).val("0");
	}
}
</script>
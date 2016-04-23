
<?
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("daObj.php");
$daObj = new daObj();

if ($_SESSION['sts-username'] == "") {
	echo "<SCRIPT>window.parent.location.href='../../index.php';</SCRIPT>";
}

	switch($_POST['action']) {
		case 'addRentable ':
			//$result = $daObj->AddSTSRentablesDtl($_POST);
			//echo $result;
			if( $daObj->AddSTSRentablesDtl($_POST)) {
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<? 
if($_GET['act']=='getRentables'){
	$arr = $daObj->getRentableBranches($_GET['refNo']);
	if( (int)count($arr) <= 0){
		echo "<script>dialogAlert('Error: You must save atleast one branch first!');</script>";	
		echo "<script>$('#divSTSDtls2').dialog('destroy');</script>";
		echo "<script>$('#divSTSDtls2').dialog('close');</script>";
	}else{
?>

<form name="frmRentable" id="frmRentable">

<input type="hidden" name="hdnDateFrom" id="hdnDateFrom" value="<?=$_GET['startDate']?>"/>
<input type="hidden" name="hdnDateTo" id="hdnDateTo" value="<?=$_GET['endDate']?>"/>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="1500px">

<? foreach($arr as $val){?>
		<tr>
        	<td align="left" width="250px;"><strong><?=$val['strCode']." - ".$val['brnDesc']?></strong>
           
            </td>
            <td>
            <? $arrDispStr = $daObj->getRentableDtlsTactical($_GET['refNo'],$val['dispSpecs'],$val['strCode'],$_GET['startDate'],$_GET['endDate']); ?>
            
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
<?		}
	} ?>
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
</script>
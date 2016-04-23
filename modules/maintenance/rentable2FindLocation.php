<?
session_start();
#### Roshan's Ajax dropdown code with php
#### Copyright reserved to Roshan Bhattarai - nepaliboy007@yahoo.com
#### if you have any problem contact me at http://roshanbh.com.np
#### fell free to visit my blog http://php-ajax-guru.blogspot.com
?>
<? 
include("../../includes/db.inc.php");
include("../../includes/common.php");
include("maintenanceObj2.php");
$maintenanceObj = new maintenanceObj2;

$displayLocation=$_REQUEST['divLocation'];
$distribution_name = $maintenanceObj->getSubDistributionName($subDis);
?> 
<td width="10%">
    <? $assDisplaySelectedLocation= $maintenanceObj->getSelectedLocation($val['displaySpecsId']); ?>
    <? $maintenanceObj->DropDownMenu($maintenanceObj->makeArr($maintenanceObj->getLocation($val['displaySpecsId']),'locId','series',$assDisplaySelectedLocation['series']),'cmb_location_'.$val['displaySpecsId'].$val['locId'],'','class="selectBox"'." ".$disabled); ?>
</td> 

<?
	//programmer : vincent c de torres
	//rock the code
	include("../inventory/lbd_function.php");
	include("../../includes/config.php");
	include("../../functions/db_function.php");
	include("../../functions/pager_function.php");

	$db = new DB;
	$db->connect();
	
	//group
	$qryGRPList = "SELECT * FROM TBLPRODCLASS WHERE PRDCLSLVL = '1' AND (PRDCLSTSTAT='A') ORDER BY PRDGRPCODE ASC" ;
	$resGRPList = $db->query($qryGRPList);
	$rowGRPList = $db->getArrResult();

	//department
	 $qryDEPTList = "SELECT * FROM TBLPRODCLASS WHERE (PRDGRPCODE='".$_GET['prHGC']."') AND PRDCLSLVL = '2' AND (PRDCLSTSTAT='A') ORDER BY PRDDEPTCODE ASC";
	$resDEPTList = $db->query($qryDEPTList);
	$rowDEPTList = $db->getArrResult();
	
	//class
	$qryCLSList = "SELECT * FROM TBLPRODCLASS WHERE (PRDGRPCODE = '".$_GET['prHGC']."') AND (PRDDEPTCODE='".$_GET['prHDC']."') AND PRDCLSLVL = '3'
		AND (PRDCLSTSTAT='A') ORDER BY PRDCLSCODE ASC";
	$resCLSList = $db->query($qryCLSList);
	$rowCLSList = $db->getArrResult();
	
	//sclass
	$qrySCLSList = "SELECT * FROM TBLPRODCLASS WHERE (PRDGRPCODE='".$_GET['prHGC']."') AND (PRDDEPTCODE='".$_GET['prHDC']."')
		AND (PRDCLSCODE = '".$_GET['prHCC']."') AND (PRDCLSTSTAT='A') AND PRDCLSLVL = '4' ORDER BY PRDSUBCLSCODE ASC";
	$resSCLSList = $db->query($qrySCLSList);
	$rowSCLSList = $db->getArrResult();
	

//pager settings
$intLimit = 15;
$intOffset = $_GET['limit_start'];
$cUrl = "sample_pager.php";
if($intOffset == "" ){
	$intOffset = 0;
}
//end of pager settings	

$qryPrdIntMax = "SELECT COUNT(*) as intMax FROM TBLPRODMAST ";
	if(!empty($_GET['prHGC'])){
		$qryPrdIntMax .= "WHERE PRDGRPCODE = '{$_GET['prHGC']}' ";
	}
	if(!empty($_GET['prHDC'])){
		$qryPrdIntMax .= "AND PRDDEPTCODE = '{$_GET['prHDC']}' ";
	}
	if(!empty($_GET['prHCC'])){
		$qryPrdIntMax .= "AND PRDCLSCODE = '{$_GET['prHCC']}' ";
	}
	if(!empty($_GET['prHSCC'])){
		$qryPrdIntMax .= "AND PRDSUBCLSCODE = '{$_GET['prHSCC']}' ";
	}
$resPrdIntMax = mssql_query($qryPrdIntMax);
$rowMaxRec = mssql_fetch_assoc($resPrdIntMax);
$intMaxRec = $rowMaxRec['intMax'];
	
	//product List
if($_GET['action'] == 'Search'){ 
	$qryPrdList = "SELECT top $intLimit * FROM TBLPRODMAST WHERE prdNumber ";
	
	$qryPrdList .= "NOT IN (SELECT TOP $intOffset prdNumber FROM TBLPRODMAST ";
	if(!empty($_GET['prHGC'])){
		$qryPrdList .= "WHERE PRDGRPCODE = '{$_GET['prHGC']}' ";
	}
	if(!empty($_GET['prHDC'])){
		$qryPrdList .= "AND PRDDEPTCODE = '{$_GET['prHDC']}' ";
	}
	if(!empty($_GET['prHCC'])){
		$qryPrdList .= "AND PRDCLSCODE = '{$_GET['prHCC']}' ";
	}
	if(!empty($_GET['prHSCC'])){
		$qryPrdList .= "AND PRDSUBCLSCODE = '{$_GET['prHSCC']}' ";
	}	
	$qryPrdList .= "ORDER BY prdNumber) ";
	
	if(!empty($_GET['prHGC'])){
		$qryPrdList .= "AND PRDGRPCODE = '{$_GET['prHGC']}' ";
	}
	if(!empty($_GET['prHDC'])){
		$qryPrdList .= "AND PRDDEPTCODE = '{$_GET['prHDC']}' ";
	}
	if(!empty($_GET['prHCC'])){
		$qryPrdList .= "AND PRDCLSCODE = '{$_GET['prHCC']}' ";
	}
	if(!empty($_GET['prHSCC'])){
		$qryPrdList .= "AND PRDSUBCLSCODE = '{$_GET['prHSCC']}' ";
	}
    $qryPrdList .= "ORDER BY prdNumber ";	
	$resPrdList = mssql_query($qryPrdList);	
}

if($_GET['action'] == 'Search'){
	    $qryGRP = "SELECT * FROM TBLPRODCLASS WHERE PRDCLSLVL = '1'
		AND (PRDCLSTSTAT='A') ORDER BY PRDGRPCODE ASC" ;
		$resGRP = mssql_query($qryGRP);
	
	if(!empty($_GET['prHGC'])){
		$qryDEPT = "SELECT * FROM TBLPRODCLASS WHERE (PRDGRPCODE='".$_GET['prHGC']."') AND PRDCLSLVL = '2' ORDER BY PRDDEPTCODE ASC";
		$resDEPT = mssql_query($qryDEPT);
	}
	if(!empty($_GET['prHDC'])){
		$qryCLS = "SELECT * FROM TBLPRODCLASS WHERE (PRDGRPCODE = '".$_GET['prHGC']."') AND (PRDDEPTCODE='".$_GET['prHDC']."') 
			AND (PRDCLSTSTAT='A') AND PRDCLSLVL = '3' ORDER BY PRDCLSCODE ASC";
		$resCLS = mssql_query($qryCLS);
	}
	if(!empty($_GET['prHCC'])){
		$qrySCLS = "SELECT * FROM TBLPRODCLASS WHERE (PRDGRPCODE='".$_GET['prHGC']."') AND (PRDDEPTCODE='".$_GET['prHDC']."')
			AND (PRDCLSCODE = '".$_GET['prHCC']."') AND (PRDCLSTSTAT='A') AND PRDCLSLVL = '4' ORDER BY PRDSUBCLSCODE ASC";	
		$resSCLS = mssql_query($qrySCLS);
	}
	$cmb_group=$_GET['prHGC'];
}
?>
		<style type="text/css">
		<!--
			.style3 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
				font-weight: bold;
			}
			.styleme 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
			}
			.style6 
			{
				font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; 
			}
			.style7 
			{
				font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;
			}
		-->
		</style>
<form name='frmProdClass' method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	<table border="0" align="center">
		<tr nowrap="wrap" align="left"  bgcolor="#DEEDD1">
			
      <th height="23" colspan="14" nowrap="nowrap" class="styleme"> <a href='<? echo $_SERVER['PHP_SELF']; ?>'>Refresh</a> 
        || <span class="style6"> <a href="prod_class_pdf.php?cmb_sub_class=<?php echo $cmb_sub_class; ?> &cmb_class=<?php echo $cmb_class; ?> &cmb_dept=<?php echo $cmb_dept; ?> &cmb_group=<?php echo $cmb_group; ?> &search_query=<?php echo $qrySetList; ?> &search_selection=all_record" target="_blank"> 
        <img src="../images/s_f_prnt.gif" />Print</a></span> 
        <input class="styleme" type="button" name="btnAddGrp" id="btnAddGrp" value="Add Group" onclick="location.href='prod_class_group_entry.php';">
				<input class="styleme" type="button" name="btnAddDept" id="btnAddDep" value="Add Department" onclick="location.href='prod_class_dept_entry.php';">
				<input class="styleme" type="button" name="btnAddCls" id="btnAddCls" value="Add Class" onclick="location.href='prod_class_class_entry.php';">
				<input  class="styleme" type="button" name="btnAddSCls" id="btnAddSCls" value="Add Sub Class" onclick="location.href='prod_class_sclass_entry.php';">
			</th>
		</tr>
		<tr bgcolor="#DEEDD1">
			<td align="center" class="style3">
				Group
			</td>
			<td align="center" class="style3">
				Department
			</td>
			<td align="center" class="style3">
				Class
			</td>
			<td align="center" class="style3">
				Sub Class
			</td>
			<td align="center" class="style3">
				Search
			</td>
		</tr>
		<?if($_GET['action'] == 'Search'){?>
		<tr bgcolor="#EAEAEA">
			<td align="left" valign="top">
				<table>
					<?while(@$rowGRP = mssql_fetch_array($resGRP)){?>
					<tr>
						<td>
							<font size="2" class="style3" title="<?=$rowGRP[4]?>" class="cls"><?=$rowGRP[0]?>-<?=$db->cutStr($rowGRP[4],20);?></font>
						</td>
					</tr>
					<?}?>
				</table>
			</td>
			<td align="left" valign="top">
				<table>
					<?while(@$rowDEPT = mssql_fetch_array($resDEPT)){?>
					<tr>
						<td>
							<font size="2" class="style3" title="<?=$rowDEPT[4]?>" class="cls"><?=$rowDEPT[1]?>-<?=$db->cutStr($rowDEPT[4],20);?></font>
						</td>
					</tr>
					<?}?>
				</table>			
			</td>
			<td align="left" valign="top" >
				<table>
					<?while(@$rowCLS = mssql_fetch_array($resCLS)){?>
					<tr>
						<td>
							<font size="2" class="style3" title="<?=$rowCLS[4]?>" class="cls"><?=$rowCLS[2]?>-<?=$db->cutStr($rowCLS[4],20);?></font>
						</td>
					</tr>
					<?}?>
				</table>			
			</td>
			<td align="left" valign="top">
				<table>
					<?while(@$rowSCLS = mssql_fetch_array($resSCLS)){?>
					<tr>
						<td>
							<font size="2" class="style3" title="<?=$rowSCLS[4]?>" class="cls"><?=$rowSCLS[3]?>-<?=$db->cutStr($rowSCLS[4],20);?></font>
						</td>
					</tr>
					<?}?>
				</table>			
			</td>
			<td align="left">
				
			</td>
		</tr>
		<?}?>
		<tr bgcolor="#F2FEFF">
			<td class="hdr">
				<? echo $db->selectOption($rowGRPList,"cmbGrpList","cmbGrpList","onchange='fGrpList()' class=styleme style=width:200px","","$_GET[prHGC]","prdGrpCode","prdGrpCode","prdClsDesc","prdClsDesc");?>
			</td>
			<td class="hdr">
				<? echo $db->selectOption($rowDEPTList,"cmbDEPTList","cmbDEPTList","onchange=fDeptList('$_GET[prHGC]') class=styleme style=width:200px","","$_GET[prHDC]","prdDeptCode","prdDeptCode","prdClsDesc","prdClsDesc");?>
			</td >
			<td class="hdr">
				<? echo $db->selectOption($rowCLSList,"cmbCLSList","cmbCLSList","onchange=fClsList('$_GET[prHGC]','$_GET[prHDC]') class=styleme style=width:200px","","$_GET[prHCC]","prdClsCode","prdClsCode","prdClsDesc","prdClsDesc");?>
			</td>
			<td class="hdr">
				<? echo $db->selectOption($rowSCLSList,"cmbSCLSList","cmbSCLSList","style='width:200px' class=styleme","","$_GET[prHSCC]","prdSubClsCode","prdSubClsCode","prdClsDesc","prdClsDesc");?>		
			</td>
			<td align="center" class="hdr">
				<input type="button" name="SearchCls" id="SearchCls" onclick="fSerach('<?=$_GET['prHGC']?>','<?=$_GET['prHDC']?>','<?=$_GET['prHCC']?>')">
			</td>
		</tr>
	</table>
	<br>
	<?if($_GET['action'] == 'Search'){?>
		<table border="0" align="center">
			<tr bgcolor="#DEEDD1" align="center">
				<td align="center" class="style3" width="100">
					<font >Product Code</font>
				</td>
				<td align="center" class="style3" width="300">
					<font >Product Descriptin</font>
				</td>
			</tr>
			<?$i=0; 
			while($rowPrdList = mssql_fetch_array($resPrdList)){ 
								
			$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
			$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
			. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
			?>
			<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
				<td class="Dtl" >
					<font class="style6"><?=$rowPrdList[0]?></font>
				</td>
				<td class="Dtl" >
					<font class="style6"><?=$rowPrdList[1]?></font>
				</td>
			</tr>
			<?
			}
			$i++;
			?>
		</table>
		<?if($intMaxRec > 15){?>
			<table height="40" width="50%" border="0" align="center">
				</tr>
					<td align="center">
						<? echo fPageLinks($intOffset, $intMaxRec,$intLimit,'&action=Search&prHGC='.$_GET['prHGC'].'&prHDC='.$_GET['prHDC'].'&prHCC='.$_GET['prHCC'].'&prHSCC='.$_GET['prHSCC']);?>
					</td>
				</tr>
			</table>
		<?}?>
	<?}?>
</form>
<script>
	function fGrpList(){
		var prdGrp = document.getElementById('cmbGrpList').value;
		document.frmProdClass.action='<?=$_SERVER['PHP_SELF']?>?action=Search&prHGC='+prdGrp;
		document.frmProdClass.submit();
	}
	
	function fDeptList(GrpCde){
		var prdDept = document.getElementById('cmbDEPTList').value;
		document.frmProdClass.action='<?=$_SERVER['PHP_SELF']?>?action=Search&prHGC='+GrpCde+"&prHDC="+prdDept;
		document.frmProdClass.submit();	
	}
		
	function fClsList(GrpCde,DeptCde){
		prdCls = document.getElementById('cmbCLSList').value;
		document.frmProdClass.action='<?=$_SERVER['PHP_SELF']?>?action=Search&prHGC='+GrpCde+"&prHDC="+DeptCde+"&prHCC="+prdCls;
		document.frmProdClass.submit();		
	}
	
	function fSerach(GrpCde,DeptCde,PrdCls){
		var prdSCls = document.getElementById('cmbSCLSList').value;
		document.frmProdClass.action='<?=$_SERVER['PHP_SELF']?>?action=Search&prHGC='+GrpCde+"&prHDC="+DeptCde+"&prHCC="+PrdCls+"&prHSCC="+prdSCls;
		document.frmProdClass.submit();	
	}

</script>
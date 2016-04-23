<?
	session_start();
	$search_selection = $_GET['search_selection'] ;
	$txtSearch = $_GET['txtSearch'] ;
	$cmbSearch = $_GET['cmbSearch'] ;
	$action2 = $_GET['action2'] ;
	echo "<input type='hidden' name='hide_search_selection' id='hide_search_selection' value='$search_selection'>";
	
	require_once "../includes/config.php";
	require_once "../functions/db_function.php";
	include("../functions/inquiry_pager.php");
	$db = new DB;
	$db->connect();
	$userid = $_SESSION['userid'];
	
//pager settings
$intLimit = 20;
$intOffset = $_GET['limit_start'];
$cUrl = "sample_pager.php";
if($intOffset == "" ){
	$intOffset = 0;
}
//end of pager settings		

	
	$qryMaxRec = "SELECT COUNT(*) as intmaxrec FROM tblProdMast WHERE prdDelTag <> 'D'";
	if($action2 == 'DoSearch'){
		if(!empty($cmbSearch)){
			if($cmbSearch == 'PRODUCT CODE'){
					
				$qryMaxRec .= " AND prdNumber LIKE '$txtSearch%' ";
			}else{
				$qryMaxRec .= " AND prdDesc LIKE '$txtSearch%' ";
			}
		}
	}
	
	$resMaxRec = mssql_query($qryMaxRec);
	$rowMaxRec = mssql_fetch_assoc($resMaxRec);
	$intMaxRec = $rowMaxRec['intmaxrec'];	
/*	$qryMaxRec = "SELECT COUNT(*) as intmaxrec FROM V_PRODMAST WHERE prdDelTag <> 'D'";
	$resMaxRec = mssql_query($qryMaxRec);*/
	


	
    $qryVndDesc = "SELECT TOP $intLimit * FROM tblProdMast WHERE (prdNumber NOT IN
              (SELECT TOP $intOffset prdNumber FROM tblProdMast WHERE prdDelTag <> 'D' ORDER BY prdDesc)) ORDER BY prdDesc";
    
	if($action2 == 'DoSearch'){
		if(!empty($cmbSearch)){
			if($cmbSearch == 'PRODUCT CODE'){
				$qryVndDesc = "SELECT TOP $intLimit * FROM tblProdMast WHERE (prdNumber NOT IN
				                (SELECT TOP $intOffset prdNumber FROM tblProdMast WHERE prdDelTag <> 'D' ORDER BY prdDesc)) 
								AND (prdNumber LIKE '$txtSearch%') ORDER BY prdDesc ";
			}else{
				$qryVndDesc = "SELECT TOP $intLimit * FROM tblProdMast WHERE (prdNumber NOT IN
			                    (SELECT TOP $intOffset prdNumber FROM tblProdMast WHERE (prdDelTag <> 'D') AND (prdDesc LIKE '$txtSearch%') ORDER BY prdDesc)) 
								AND (prdDesc LIKE '$txtSearch%') ORDER BY prdDesc ";
			}
		}
	}
	$resVndDesc = mssql_query($qryVndDesc);
?>
<html>
	<head><title>vendor product lookup</title>
		<style>
			.hdrProdDtl
			{
				font: bold 13px Verdana, Arial, Helvetica, sans-serif;
			}
			
			.hdrProdDtl
			{
				font: normal 10px Verdana, Arial, Helvetica, sans-serif;
			}
			
			
			.hdrVnd 
			{
				font: bold 13px Verdana, Arial, Helvetica, sans-serif;
			}
			
			.hdrVndDtl
			{
				font: normal 10px Verdana, Arial, Helvetica, sans-serif;
			}
		</style>
	</head>
	<body >
		<form name="frmVendorProdLookUp" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
			<div align="center"><input type="text" name="txtSearch" id="txtSearch" value="<? echo $txtSearch ?>">
			<select name="cmbSearch" id="cmbSearch">
				<option selected><? echo $cmbSearch; ?> </option>
				<option>PRODUCT CODE</option>
				<option>PRODUCT DESCRIPTION</option>
			</select>
			<img src="../images/search.gif" onclick="fSearch('<?=$VnddNum?>')" title="Search" />
			<font size="2"><b><a href="#" onclick="fSearch('<?=$VnddNum?>')" title="Search">Search</a></b></font>
			<font size="2"><b><a href='<? echo $_SERVER['PHP_SELF']; ?>?vNum=<?=$VnddNum?>&search_selection=<?=$search_selection?>&txtSearch=<?=$txtSearch?>&cmbSearch=<?=$cmbSearch?>'>Refresh</a></b></font></div>
			<table border="0" align="center">
				<tr bgcolor="#DEEDD1" align="center">
					<td>
						<font class="hdrProd">Product Code</font>
					</td>
					<td align="center">
						<font class="hdrProd">Product Description</font>
					</td>
				</tr>
				<?$i=0; 
				while($rowVend2 = mssql_fetch_array($resVndDesc)){ 
									
				$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
				$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
				. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" title="Click to get the Value" <?php echo $on_mouse; ?> onclick="fOpener('<? echo $rowVend2[1]; ?>')" style="cursor:pointer;">
					<td >
						<font class="hdrProdDtl"><?=$rowVend2[0]?></font>
					</td>
					<td width="300">
						<font class="hdrProdDtl"><?=stripslashes($rowVend2[1])?></font>
					</td>
				</tr>
				<?
				}
				$i++;
				?>
			</table>
			<?if($intMaxRec > 20){?>
				<table width="100%" border="0" align="center">
					</tr>
						<td align="center" class="style6">
							<? echo fPageLinks($intOffset, $intMaxRec,$intLimit,'');?>
						</td>
					</tr>
				</table>
			<?}?>
			<input type="hidden" name="hdnExtName" id="hdnExtName" value="<?=$_GET['ExtName']?>">
		</form>
	</body>
</html>
<script>
	function fOpener(prdNum){
		var hide_search_selection = document.getElementById('hide_search_selection').value;
		alert(prdNum);
		if (hide_search_selection=="average_cost_inquiry_prod_code1") {
			window.opener.document.getElementById('prod_code1').value=prdNum;
			window.opener.document.getElementById('prod_code1').focus();
			window.close();
		}
		if (hide_search_selection=="average_cost_inquiry_prod_code2") {
			window.opener.document.getElementById('prod_code2').value=prdNum;
			window.opener.document.getElementById('prod_code2').focus();
			window.close();
		}	
	}
	
	function fSearch(vNum){
		var txtSearch = document.getElementById('txtSearch').value;
		var cmbSearch = document.getElementById('cmbSearch').value;
		var hide_search_selection = document.getElementById('hide_search_selection').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
			document.frmVendorProdLookUp.action="<?php $_SERVER['PHP_SELF']?>?search_selection="+hide_search_selection+"&txtSearch="+txtSearch+"&cmbSearch="+cmbSearch+"&action2=DoSearch&vNum="+vNum;
			document.frmVendorProdLookUp.submit();
		}
	}
	

</script>
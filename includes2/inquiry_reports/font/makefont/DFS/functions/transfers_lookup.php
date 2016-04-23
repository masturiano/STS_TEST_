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
	$company_code = $_SESSION['comp_code'];
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

	
	$qryMaxRec = "SELECT COUNT(*) as intmaxrec FROM tblTransferHeader WHERE compCode = $company_code AND trfStatus <> 'R'";
	$resMaxRec = mssql_query($qryMaxRec);
	$rowMaxRec = mssql_fetch_assoc($resMaxRec);
	$intMaxRec = $rowMaxRec['intmaxrec'];	

    $qryVndDesc = "SELECT TOP $intLimit * FROM tblTransferHeader WHERE (trfNumber NOT IN
              (SELECT TOP $intOffset trfNumber FROM tblTransferHeader WHERE compCode = $company_code AND trfStatus <> 'R' ORDER BY trfNumber DESC)) AND compCode = $company_code AND trfStatus <> 'R' ORDER BY trfNumber DESC";
    
	if($action2 == 'DoSearch'){
		if(!empty($cmbSearch)){
			if($cmbSearch == 'TRANSFERS CODE'){
				$qryVndDesc = "SELECT TOP $intLimit * FROM tblTransferHeader WHERE (trfNumber NOT IN
				                (SELECT TOP $intOffset trfNumber FROM tblTransferHeader WHERE compCode = $company_code AND trfStatus <> 'R' ORDER BY trfNumber DESC)) 
								AND (trfNumber LIKE '$txtSearch%') AND compCode = $company_code AND trfStatus <> 'R' ORDER BY trfNumber DESC";
			}else{
				$qryVndDesc = "SELECT TOP $intLimit * FROM tblTransferHeader WHERE (trfNumber NOT IN
				                (SELECT TOP $intOffset trfNumber FROM tblTransferHeader WHERE compCode = $company_code AND trfStatus <> 'R' ORDER BY trfNumber DESC)) 
								AND (trfDate LIKE '$txtSearch%') AND compCode = $company_code AND trfStatus <> 'R' ORDER BY trfNumber DESC";
			}
		}
	}
	$resVndDesc = mssql_query($qryVndDesc);
?>
<html>
	<head><title>Transfers Lookup</title>
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
				<option>TRANSFERS CODE</option>
				<option>TRANSFERS DATE</option>
			</select>
			<img src="../images/search.gif" onclick="fSearch('<?=$VnddNum?>')" title="Search" />
			<font size="2"><b><a href="#" onclick="fSearch('<?=$VnddNum?>')" title="Search">Search</a></b></font>
			<font size="2"><b><a href='<? echo $_SERVER['PHP_SELF']; ?>?vNum=<?=$VnddNum?>&search_selection=<?=$search_selection?>&txtSearch=<?=$txtSearch?>&cmbSearch=<?=$cmbSearch?>'>Refresh</a></b></font></div>
			<table border="0" align="center">
				<tr bgcolor="#DEEDD1" align="center">
					
      <td> <font class="hdrProd">TRANSFER NO</font></td>
					
      <td align="center"> <font class="hdrProd">TRANSFER DATE</font> </td>
				</tr>
				<?$i=0; 
				while($rowVend2 = mssql_fetch_array($resVndDesc)){ 
									
				$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
				$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
				. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" title="Click to get the Value" <?php echo $on_mouse; ?> onclick="fOpener('<?=$rowVend2[1]?>')" style="cursor:pointer;">
					<td >
						<font class="hdrProdDtl"><?=$rowVend2[1]?></font>
					</td>
					<td width="300">
						<font class="hdrProdDtl"><? 
													$transfers_date = $rowVend2[4]; 
													if ($transfers_date=="") {
														$transfers_date = "";
													} else {
														$date = new DateTime($transfers_date);
														$transfers_date = $date->format("m/d/Y");
													}
													echo $transfers_date;
												 
												 ?></font>
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
		if (hide_search_selection=="open_transfers") {
			window.opener.document.getElementById('trans_no').value=prdNum;
			//window.opener.document.getElementById('trans_no').focus();
			window.opener.document.transfers_form.submit();
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
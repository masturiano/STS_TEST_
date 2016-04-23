<?
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	
	
    $qryVndDesc = "SELECT DISTINCT(PRDNUMBER), PRDDESC, PRDTYPE FROM VIEWVENDORPRODUCT ";
	
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'PRODUCT CODE'){	
				$qryVndDesc .= " WHERE PRDNUMBER LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryVndDesc .= " WHERE PRDDESC LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$qryVndDesc .= "ORDER BY PRDNUMBER ";
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
	<body>
		<form name="frmVendorProdLookUp" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
			<div align="center"><input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
			<select name="cmbSearch" id="cmbSearch">
				<option>PRODUCT CODE</option>
				<option>PRODUCT DESCRIPTION</option>
			</select>
			<img src="../images/search.gif" onclick="fSearch('<?=$VnddNum?>')" title="Search" />
			<font size="2"><b><a href="#" onclick="fSearch('<?=$VnddNum?>')" title="Search">Search</a></b></font>
			<font size="2"><b><a href='<? echo $_SERVER['PHP_SELF']; ?>?vNum=<?=$VnddNum?>'>Refresh</a></b></font></div>
			<table border="0" align="center">
				<tr bgcolor="#DEEDD1" align="center">
					<td>
						#
					</td>
					<td>
						<font class="hdrProd">Product Code</font>
					</td>
					<td align="center">
						<font class="hdrProd">Product Description</font>
					</td>
					<td align="center" width="100">
						<font class="hdrProd">Product Type</font>
					</td>
				</tr>
				<?$i=1; 
				while($rowVend2 = mssql_fetch_array($resVndDesc)){ 
									
				$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
				$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
				. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" title="Click to get the Value" <?php echo $on_mouse; ?> onclick="fOpener('<?=$rowVend2[0]?>')" style="cursor:pointer;">
					<td bgcolor="Silver" align="center">
						<font color="White" size="2"><b><?=$i;?></b></font>
					</td>
					<td >
						<font class="hdrProdDtl"><?=$rowVend2[0]?></font>
					</td>
					<td width="300">
						<font class="hdrProdDtl"><?=htmlspecialchars(stripslashes($rowVend2[1]))?></font>
					</td>
					<td width="100">
						<font class="hdrProdDtl"><?if($rowVend2[2] == 'RG'){echo "REGULAR";}else{ echo "CONCESSIONAIRE";}?></font>
					</td>
				</tr>
				<?
				}
				$i++;
				?>
			</table>
		</form>
	</body>
</html>
<script>
	function fOpener(prdNum){
		window.opener.document.getElementById('txtSKU').value=prdNum;
		window.opener.document.getElementById('txtSKU').focus();
		window.close();
	}
	
	function fSearch(vNum){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
			document.frmVendorProdLookUp.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch&vNum="+vNum;
			document.frmVendorProdLookUp.submit();
		}
	}
</script>
<?
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";

	$db = new DB;
	$db->connect();
	$search_selection = $_GET['search_selection'] ;
	echo "<input type='hidden' name='hide_search_selection' id='hide_search_selection' value='$search_selection'>";
	$qryVndDesc = "SELECT * FROM tblBuyers WHERE buyerStat = 'A'  ";

	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'BUYER CODE'){	
				$qryVndDesc .= " AND buyerCode LIKE '{$_POST['txtSearch']}%' ";
				$qryVndDesc .= " ORDER BY buyerCode ASC ";
			}else{
				$qryVndDesc .= " AND buyerName LIKE '{$_POST['txtSearch']}%' ";
				$qryVndDesc .= " ORDER BY buyerName ASC ";
			}
		}
	}
	$resVndDesc = mssql_query($qryVndDesc);
	
?>
<html>
	<head><title>Buyer lookup</title>
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
		<form name="frmBuyerLookUp" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
			<div align="center" style="position:absolute; width:99%; height:500px; z-index:1; left: 5px; top: 10px; border: 1px none #000000; overflow: auto;">
   
    <p>
      <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
      <select name="cmbSearch" id="cmbSearch">
        <option>BUYER CODE</option>
        <option>BUYER NAME</option>
      </select>
      <img src="../images/search.gif" onclick="fSearch('<?=$VnddNum?>')" title="Search" /> 
      <font size="2"><b><a href="#" onclick="fSearch('<?=$VnddNum?>')" title="Search">Search</a></b></font> 
      <font size="2"><b><a href='<? echo $_SERVER['PHP_SELF']; ?>?vNum=<?=$VnddNum?>'>Refresh</a></b></font></p>
    <table border="0" align="center">
      <tr bgcolor="#DEEDD1" align="center"> 
        <td> <font class="hdrProd">Buyer Code</font> </td>
        <td align="center"> <font class="hdrProd">Buyer Name</font> </td>
      </tr>
      <? $i=0; 
				while($rowVend2 = mssql_fetch_array($resVndDesc)){ 
									
				$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
				$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
				. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
      <tr bgcolor="<?php echo $bgcolor; ?>" title="Click to get the Value" <?php echo $on_mouse; ?> onclick="fOpener('<?=$rowVend2[0]?>','<?=$rowVend2[1]?>','<?=$rowVend2[4]?>','<?=$rowVend2[5]?>','<?=$rowVend2[6]?>')" style="cursor:pointer;"> 
        <td > <font class="hdrProdDtl">
          <?=$rowVend2[0]?>
          </font> </td>
        <td width="300"> <font class="hdrProdDtl">
          <?=$rowVend2[1]?>
          </font> </td>
      </tr>
      <?
				}
				$i++;
				?>
    </table>
    <p>&nbsp;</p>
  </div>
			<input type="hidden" name="hdnExtName" id="hdnExtName" value="<?=$_GET['ExtName']?>">
</form>
	</body>
</html>
<script>
	function fOpener(buyNum,buyName){
		var hide_search_selection = document.getElementById('hide_search_selection').value;
		window.opener.document.getElementById('box_buyer').value=buyNum;
		window.opener.document.getElementById('box_buyer').focus();
		window.close();
	}
	
	function fSearch(vNum){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
			document.frmBuyerLookUp.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch&vNum="+vNum;
			document.frmBuyerLookUp.submit();
		}
	}
	

</script>
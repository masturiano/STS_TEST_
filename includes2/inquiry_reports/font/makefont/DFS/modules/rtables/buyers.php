<?php
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	include("../../functions/pager_function.php");

	$db = new DB;
	$db->connect();
	


//pager settings
$intLimit = 18;
$intOffset = $_GET['limit_start'];
$cUrl = "sample_pager.php";
if($intOffset == "" ){
	$intOffset = 0;
}
//end of pager settings	
	if($_GET['action'] == 'Dodelete'){
		$qryDeleBuyer = "DELETE FROM tblBuyers WHERE buyerCode = '{$_GET['buyerCde']}'";
		$resDeleBuyer = mssql_query($qryDeleBuyer);
	}
	
	$qrybuyer = "SELECT * FROM tblBuyers ";
	$resbuyer = mssql_query($qrybuyer);
	$intMaxRec = mssql_num_rows($resbuyer);

	$qryBuyerList = "SELECT top $intLimit * FROM tblBuyers WHERE buyerStat = 'A' AND buyerCode 
							 NOT IN (SELECT TOP $intOffset buyerCode FROM tblBuyers ORDER BY buyerCode) ";
	$qryBuyerList2 = "SELECT * FROM tblBuyers WHERE buyerStat = 'A' ";
	if($_GET['action'] == 'DoSearch'){
		if(!empty($_POST['txtSearch'])){
			if($_POST[cmbSearch] == 'BUYER CODE'){	
				$qryBuyerList .= " AND buyerCode LIKE '{$_POST['txtSearch']}%' ";
				$qryBuyerList2 .= " AND buyerCode LIKE '{$_POST['txtSearch']}%' ";
			}else{
				$qryBuyerList .= " AND buyerName LIKE '{$_POST['txtSearch']}%' ";
				$qryBuyerList2 .= " AND buyerCode LIKE '{$_POST['txtSearch']}%' ";
			}
		}
	}
	$qryBuyerList .= "ORDER BY buyerCode ";	
	$qryBuyerList2 .= "ORDER BY buyerCode ";	
	$resBuyerList = mssql_query($qryBuyerList);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
-->
</style>
</head>
<script type='text/javascript' src='../../functions/javascript_function.js'></script>
<script type='text/javascript' src='../../functions/prototype.js'></script>
	<body>
		<div class="style6">
			<center>
				<form name="frmBuyer" method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
					<table width="500" border="0" >
						<tr nowrap="wrap" align="left" bgcolor="#6AB5FF">
							<th height="23" colspan="14" nowrap="nowrap" class="style6">
								BUYER MAINTENANCE
							</th>
						</tr>
						<tr nowrap="wrap" align="left" bgcolor="#DEEDD1">
							
          <th height="23" colspan="4" nowrap="nowrap" class="style6"> <img src="../images/s_b_insr.gif" /> 
            <a href="buyer_entry.php?action=new"> New </a> || <img src="../images/s_f_prnt.gif" /> 
            <a href="vendor_loadreport.php"> <span class="style6"><a href="../rtables/buyers_pdf.php?search_query=<?php echo $qryBuyerList2; ?>%20&search_selection=all_record" target="_blank">Print</a></span></a> 
            || <img src="../images/search.gif" onclick="fSearch()"/> <a onclick="fSearch()" title="Search">Search</a> 
            <input type="text" name="txtSearch" id="txtSearch" value="<? echo $_POST['txtSearch'] ?>">
									<select name="cmbSearch" id="cmbSearch">
										<option>BUYER CODE</option>
										<option>BUYER NAME</option>
									</select>
									<a href='<? echo $_SERVER['PHP_SELF']; ?>'>Reload</a> 
							</th>
						</tr>
						<tr bgcolor="#DEEDD1">
						    <th width="106">Buyer Code</th>
						    <th width="315" align="center">Buyer Name</th>
						    <th colspan="2" align="center">Action</th>
						</tr>
						<?php 
							$i = 0;	
							while($rowBuyer = mssql_fetch_array($resBuyerList)){
							$bgcolor = ($i++ % 2) ? "#EAEAEA" : "#F2FEFF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#97CBFF' . '\';"'
							. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
						?>
					    <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
					     	<td>
					     		<?=$rowBuyer[0]?>
					        </td>
							<td width="30" colspan="1">
					        	<?=$rowBuyer[1]?>
							<td align="center">
								<a href="buyer_entry.php?maction=edit&buyNo=<?php echo $rowBuyer['0']; ?>%20">
           	    <img src="../../Images/sap_wrtpn.jpg" alt="Edit" width="20" height="18" border="0" title="Edit Buyer Record"  align="middle"/></a>
							</td>
							<td align="center">
								<img onclick="fDeleBuyer('<?=$rowBuyer[0]?>')" src="../../Images/mydelete.gif" alt="Print" width="18" height="18" border="0" title="Delete Buyer Record"  />
							</td>
						</tr>
						<?}?>
					</table>
					<?if($intMaxRec > 18){?>
						<table height="40" width="50%" border="0" align="center">
							</tr>
								<td align="center">
									<? echo fPageLinks($intOffset, $intMaxRec,$intLimit,'');?>
								</td>
							</tr>
						</table>
					<?}?>
				</form>
			</center>
		</div>
	</body>
</html>
<script>
	function fDeleBuyer(buyerCode){
		Delete = confirm('Do You Want To Delete This Record?')
		if(Delete == true){
		document.frmBuyer.action='<? $_SERVER[PHP_SELF]?>?action=Dodelete&buyerCde='+buyerCode;
		document.frmBuyer.submit();
		}
	}
	
	function fSearch(){
		var txtSearch = document.getElementById('txtSearch').value;
		if(txtSearch == ''){
			alert('Fill up text Search');
		}	
		else{
		document.frmBuyer.action="<?php $_SERVER['PHP_SELF']?>?action=DoSearch";
		document.frmBuyer.submit();
		}
	}
</script>
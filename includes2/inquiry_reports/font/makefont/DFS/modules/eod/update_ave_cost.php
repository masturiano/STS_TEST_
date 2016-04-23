<?
	include "../../functions/inquiry_session.php";
	require_once "../../includes/config.php";
	require_once "../../functions/db_function.php";
	require_once "../../functions/inquiry_function2.php";
	require_once "../../functions/inquiry_function.php";
	$db = new DB;
	$db->connect();
	if(isset($_POST['update'])) { 
		$company=$_POST['company'];
		$company=getCodeofString($company); ///pick in inventory_inquiry_function.php
		$company=trim($company);
		$query_items = "SELECT dbo.tblProdMast.prdNumber, dbo.items.S_UNIT, dbo.items.COST, dbo.items.PRICE
						FROM dbo.tblProdMast INNER JOIN
                        dbo.items ON dbo.tblProdMast.prdSuppItem = dbo.items.CHILD
						WHERE (dbo.items.PRICE > 0) ORDER BY dbo.tblProdMast.prdNumber";
		$result_items = mssql_query($query_items);
		$num_items = mssql_num_rows($result_items);
		/*
		$success = 0;
		for ($i=0;$i<$num_items;$i++){ 
			$um=mssql_result($result_items,$i,"S_UNIT"); 
			$cost=mssql_result($result_items,$i,"COST");
			$cost = number_format($cost,4);
			$prod_no=mssql_result($result_items,$i,"prdNumber"); 
			
			$query_check = "SELECT * FROM tblAveCost WHERE prdNumber = $prod_no AND compCode = $company";
			$result_check = mssql_query($query_check);
			$num_check = mssql_num_rows($result_check);
			if ($num_check > 0) {
				echo "<script>alert('<<< Product No. $prod_no already exist in Table Average Cost. >>>')</script>";
				$success = 1;
				break;
			}
			$InsertSQL = "INSERT INTO tblAveCost(prdNumber, compCode, umCode, aveUnitCost, lastUpdate, aveDocType, aveDocNo, aveDocDate)";
			$InsertSQL .= "VALUES (". $prod_no . ", " . $company. ", '". $um. "', " . $cost . ", '2/4/2009', '1', 1, '2/04/2009')";
			mssql_query($InsertSQL);
		}
		if ($success<1) {
			echo "<script>alert('<<< Updating Average Cost successfully saved >>>')</script>";
		}*/
		$success = 0;
		$query_items = "SELECT dbo.tblProdMast.prdNumber, convert(numeric,dbo.items.CHILD) as CHILD, dbo.items.SKU, dbo.items.P_UNIT, dbo.items.COST, dbo.items.S_UNIT, dbo.items.PRICE, convert(numeric,dbo.tblProdMast.prdSuppItem) as  prdSuppItem
					FROM dbo.tblProdMast INNER JOIN
					dbo.items ON prdSuppItem = CHILD
					WHERE (dbo.items.PRICE > 0) ORDER BY dbo.tblProdMast.prdNumber";
		$result_items = mssql_query($query_items);
		$num_items = mssql_num_rows($result_items);
		for ($i=0;$i<$num_items;$i++){ 
			$um=mssql_result($result_items,$i,"S_UNIT"); 
			$price=mssql_result($result_items,$i,"PRICE");
			$price= number_format($price,2);
			$prod_no=mssql_result($result_items,$i,"prdNumber"); 
			
			$query_check = "SELECT * FROM tblProdPrice WHERE prdNumber = $prod_no AND compCode = $company";
			$result_check = mssql_query($query_check);
			$num_check = mssql_num_rows($result_check);
			if ($num_check > 0) {
				$success = 1;
				echo "<script>alert('<<< Product No. $prod_no already exist in Table Product Price. >>>')</script>";
				break;
			}
			$InsertSQL = "INSERT INTO tblProdPrice(prdNumber, compCode, umCode, regUnitPrice, regPriceEvent, regPriceStart, dateUpdated, prTypeCode, lastUpdatedBy)";
			$InsertSQL .= "VALUES (". $prod_no . ", " . $company. ", '". $um. "', " . $price . ",1,'1/31/2009','2/04/2009','1','" . $user_first_last . "')";
			mssql_query($InsertSQL);
		}
		if ($success<1) {
			echo "<script>alert('<<< Updating Average Price successfully saved >>>')</script>";
		}
	} 

	//end of pager settings	
	$query_items = "SELECT dbo.tblProdMast.prdNumber, convert(numeric,dbo.items.CHILD) as CHILD, dbo.items.SKU, dbo.items.P_UNIT, dbo.items.COST, dbo.items.S_UNIT, dbo.items.PRICE, convert(numeric,dbo.tblProdMast.prdSuppItem) as  prdSuppItem
					FROM dbo.tblProdMast INNER JOIN
					dbo.items ON prdSuppItem = CHILD
					WHERE (dbo.items.PRICE > 0) ORDER BY dbo.tblProdMast.prdNumber";
	$result=mssql_query($query_items);
	$num = mssql_num_rows($result);
		echo $num;
	
	if ($num>0) {
		$meron="";
	} else {
		$meron="disabled=\"true\"";
	}
?> 
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>



<div id="Layer1" style="position:absolute; left:10px; top:8px; width:99%; height:500px; z-index:1; overflow: auto;"> 
  <form name="form1" method="post" action="update_ave_cost.php">
    <table width="771" border="0" align="center" >
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th height="26" colspan="7" nowrap="nowrap" class="style6"> <div align="center">Company 
            <font size="2" face="Arial, Helvetica, sans-serif">
            <select name="company" id="select" style="width:200px; height:20px;">
              
              <?
			$query_company = "SELECT * FROM tblCompany";
		    $result_company=mssql_query($query_company);
		    $num_company = mssql_num_rows($result_company);
			if ($num_company > 0) {
				$comp_code=mssql_result($result_company,0,"compCode"); 
				$comp_name=mssql_result($result_company,0,"compName"); 
				$locationcode = $comp_code."-".$comp_name;
			}
			echo "<option selected>$locationcode</option>";
			for ($i=0;$i<$num_company;$i++){  
					$comp_code=mssql_result($result_company,$i,"compCode"); 
					$comp_name=mssql_result($result_company,$i,"compName"); 
				?>
              <option><? echo $comp_code."-".$comp_name; ?></option>
              <? } ?>
            </select>
            </font> <font size="2" face="Arial, Helvetica, sans-serif"> 
            <input name='update' <? echo $meron; ?> type='submit' class='queryButton' id='explode_data3' title='Display the Inventory Transaction'  value='Update Data'/>
            </font></div></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#6AB5FF"> 
        <th height="26" colspan="7" nowrap="nowrap" class="style6">items Table 
          from MS Access Database</th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#6AB5FF"> 
        <th width="34" nowrap="nowrap" class="style6">&nbsp;</th>
        <th width="128" height="26" nowrap="nowrap" class="style6"><div align="center">SKU</div></th>
        <th width="165" nowrap="nowrap" bgcolor="#6AB5FF" class="style6"><font size="2" face="Arial, Helvetica, sans-serif">CHILD 
          </font></th>
        <th width="111" nowrap="nowrap" class="style6"><font size="2" face="Arial, Helvetica, sans-serif">Sell 
          UM</font></th>
        <th width="92" nowrap="nowrap" class="style6">Buy Unit</th>
        <th width="106" nowrap="nowrap" class="style6"><div align="center">PRICE</div></th>
        <th width="105" height="0" nowrap="nowrap" bgcolor="#6AB5FF" class="style6"><div align="center">COST<strong><font size="2" face="Arial, Helvetica, sans-serif"> 
            <? 
		  		$count = 1;
				for ($i=0;$i<$num;$i++){ 
					$sku=mssql_result($result,$i,"prdNumber");
					$child=mssql_result($result,$i,"CHILD");
					$cost=mssql_result($result,$i,"COST");
					$buy_um=mssql_result($result,$i,"P_UNIT");
					$sell_um=mssql_result($result,$i,"S_UNIT");
					$price=mssql_result($result,$i,"PRICE");
			?>
            </font></strong></div></th>
      </tr>
      <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
        <th nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2"><? echo $count; ?></font></div></th>
        <th height="4" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2"><? echo $sku; ?></font></div></th>
        <th nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="left"><font size="2"><? echo $child; ?></font></div></th>
        <th nowrap="nowrap" class="style6"><div align="left"><font size="2"><? echo $sell_um ?></font></div></th>
        <th nowrap="nowrap" class="style6"><div align="left"><font size="2"><? echo $buy_um ?></font></div></th>
        <th nowrap="nowrap" class="style6"><div align="right"><font size="2">
            <?  echo $price ?>
            </font></div></th>
        <th height="2" nowrap="nowrap" bgcolor="#DEEDD1" class="style6"><div align="right"><font size="2"><?  echo  $cost; $count=$count+1; } ?></font></div></th>
      </tr>
    </table>
  </form>
  <p align="center">&nbsp; </p>
  
</div>
<p align="center">&nbsp; </p>
<div align="center"></div>

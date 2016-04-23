<?

include "../../functions/inquiry_session.php";
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
require_once "../../functions/inquiry_function.php";
$db = new DB;
$db->connect();

###########################################################
$box_find_supplier=$_POST['box_find_supplier'];
$hide_find_supplier=$_POST['hide_find_supplier'];
$hide_supplier_numeric=$_POST['hide_supplier_numeric'];

$hide_action=$_POST['hide_action'];
$hide_sql=$_POST['hide_sql'];
$radio_view_code=$_POST['radio_view_code'];

$box_code=$_POST['box_code'];
$box_code2=$_POST['box_code2'];
$box_desc=$_POST['box_desc'];
$box_upc=$_POST['box_upc'];
$box_supplier=$_POST['box_supplier'];
$box_group=$_POST['box_group'];
$box_buyer=$_POST['box_buyer'];


$search_selection=$_POST['search_selection'];
if ($search_selection=="") {
	$search_selection="by_vendor";
}
switch ($search_selection) {
	case "by_vendor":
		$checked_vendor="checked";
		$prod_sort="suppCode";
		break;
	case "by_group":
				$checked_group="checked";
				$prod_sort="prdGrpCode";
				break;
	case "by_upc":
			   	$checked_upc="checked";
				break;
	case "by_desc":
				$checked_desc="checked";
				break;
	case "by_code":
				$checked_code="checked";
				break;
	case "by_buyer":
				$checked_buyer="checked";
				break;
}

##############################################################################supplier		
if(isset($_POST['btn_find_supplier'])) { 	
	if ($box_find_supplier=="") {
		
	} else {
		if ($box_find_supplier<>"*") {
			if(is_numeric($box_find_supplier)) {
				$query_find_vendor="SELECT * FROM tblSuppliers 
								WHERE suppCode LIKE '%$box_find_supplier%'
								ORDER BY suppCode ASC";
				$result_find_vendor=mssql_query($query_find_vendor);
				$num_find_vendor = mssql_num_rows($result_find_vendor);
				if ($num_find_vendor>0) {
					$find_supp_code=mssql_result($result_find_vendor,0,"suppCode");
					$find_supp_name=mssql_result($result_find_vendor,0,"suppName");
					$find_supp_name = str_replace("\\","",$find_supp_name);
					$box_supplier = $find_supp_code."-----".$find_supp_name;
				} else {
					/*echo "<script>alert('No Vendor records found!')</script>";*/
					$message="No Vendor records found!";
				}
			} else {
				$query_find_vendor="SELECT * FROM tblSuppliers 
								WHERE suppName LIKE '%$box_find_supplier%'
								ORDER BY suppName ASC";
				$result_find_vendor=mssql_query($query_find_vendor);
				$num_find_vendor = mssql_num_rows($result_find_vendor);
				if ($num_find_vendor>0) {
					$find_supp_code=mssql_result($result_find_vendor,0,"suppCode");
					$find_supp_name=mssql_result($result_find_vendor,0,"suppName");
					$find_supp_name = str_replace("\\","",$find_supp_name);
					$box_supplier = $find_supp_code."-----".$find_supp_name;
				} else {
					/*echo "<script>alert('No Vendor records found!')</script>";*/
					$message="No Vendor records found!";
				}
			}
		}
	}
}

####################### display supplier combo #########################################################
if (($box_find_supplier=="") || ($box_find_supplier=="Code or Name")) {
	if ($hide_find_supplier=="") {
		$query_supplier="SELECT * FROM tblSuppliers WHERE suppStat = 'Z' ORDER BY suppName ASC";
	} else {
		if ($hide_supplier_numeric=="YES") {
			$query_supplier="SELECT * FROM tblSuppliers 
			WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppCode LIKE '%$hide_find_supplier%')
			ORDER BY suppCode ASC";	
		} else {
			$query_supplier="SELECT * FROM tblSuppliers 
			WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppName LIKE '%$hide_find_supplier%')
			ORDER BY suppName ASC";
		}
	}
} else {
	if ($box_find_supplier=="*") {
		$query_supplier="SELECT * FROM tblSuppliers WHERE (suppStat = 'A' OR suppStat = ' ') ORDER BY suppName ASC";
		$hide_find_supplier="";
	} else {
		if(is_numeric($box_find_supplier)) {
			$query_supplier="SELECT * FROM tblSuppliers 
			WHERE (
			suppStat = 'A' OR suppStat = ' ') AND (suppCode LIKE '%$box_find_supplier%')
			ORDER BY suppCode ASC";
			$hide_supplier_numeric="YES";
		} else {
			$query_supplier="SELECT * FROM tblSuppliers 
			WHERE (suppStat = 'A' OR suppStat = ' ') AND (suppName LIKE '%$box_find_supplier%')
			ORDER BY suppName ASC";
			$hide_supplier_numeric="NO";
		}
		$hide_find_supplier=$box_find_supplier;
	}
}	
$result_supplier=mssql_query($query_supplier);
$num_supplier = mssql_num_rows($result_supplier);
####################### end of display supplier combo #####################################################
##############################################################################supplier


##############################################################################group
if ($box_group>"") {
	if ($box_dept>"") {
		$box_group2=getCodeofString($box_group);
		$box_group2=trim($box_group2);
		if ($box_group2=="All") {
			$box_group3="";						
		} else {
			$box_group3=" AND (prdGrpCode = $box_group2) ";
		}
		$box_dept2=getCodeofString($box_dept);
		$query_prod_class="SELECT * FROM tblProdClass WHERE (prdDeptCode = $box_dept2) AND (prdClstStat = 'A') $box_group3 ORDER BY prdDeptCode ASC";
	} else {
		$query_prod_class="SELECT * FROM tblProdClass WHERE (prdClstStat = 'A') $box_group3  ORDER BY prdDeptCode ASC";
	}
} else {
	$query_prod_class="SELECT * FROM tblProdClass WHERE (prd(prdClstStat = 'A')";
}
##############################################################################group

	
##############################################################################view product
if($hide_action=="view_record") { 	
	switch ($search_selection) {
		case "by_vendor":
			$checked_vendor="checked";
			$prod_sort="suppCode";
			$cut_supplier_code=getCodeofString($box_supplier); ///pick in inventory_inquiry_function.php
			$cut_supplier_code=trim($cut_supplier_code);
			if ($cut_supplier_code > "") { 
				if ($cut_supplier_code == trim("All")) { 
					$query_cut_supplier_code = "";	
				} else {
					$query_cut_supplier_code = "AND (tblProdMast.suppCode LIKE '$cut_supplier_code') "; 
				}
			} else { 
				$query_cut_supplier_code = ""; 
			}
			//$query_product="SELECT * FROM tblProdMast WHERE (tblProdMastprdDelTag='A' OR prdDelTag=' ') "
			//				.$query_cut_supplier_code.
			//				" ORDER BY $prod_sort ASC";			
			$query_product="SELECT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdSubClsCode, tblProdMast.prdSellUnit, 
                      		tblProdMast.prdBuyUnit, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdSuppItem, tblProdMast.prdSetTag, tblProdMast.buyerCode, 
                      		tblSuppliers.suppName
							FROM tblProdMast INNER JOIN
                      		tblSuppliers ON tblProdMast.suppCode = tblSuppliers.suppCode
							WHERE (tblProdMast.prdDelTag = 'A') $query_cut_supplier_code
							ORDER BY tblSuppliers.suppName,tblProdMast.prdDesc";
			break;
		case "by_group":
			$checked_group="checked";
			$prod_sort="prdGrpCode";
			$cut_group_code=getCodeofString($box_group); ///pick in inventory_inquiry_function.php
			$cut_group_code=trim($cut_group_code);
			if ($cut_group_code > "") { 
				if ($cut_group_code == trim("All")) { 
					$query_cut_group_code = "";	
				} else {
					$query_cut_group_code = "AND (tblProdMast.prdGrpCode = $cut_group_code) "; 
				}
			} else { 
				$query_cut_group_code = ""; 
			}
			//$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') "
			//				.$query_cut_group_code.
			//				" ORDER BY $prod_sort ASC";
			$query_product="SELECT tblProdMast.prdNumber, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdSubClsCode,tblProdMast.prdSellUnit, tblProdMast.buyerCode,
                     		tblProdMast.prdBuyUnit, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdSuppItem, tblProdMast.prdSetTag, tblProdClass.prdClsShortdesc
							FROM tblProdMast INNER JOIN
                      		tblProdClass ON tblProdMast.prdGrpCode = tblProdClass.prdGrpCode
							WHERE tblProdMast.prdDelTag = 'A' AND tblProdClass.prdClsLvl = 1 $query_cut_group_code 
							ORDER BY tblProdClass.prdClsShortdesc, tblProdMast.prdDeptCode,tblProdMast.prdClsCode,tblProdMast.prdDesc";
			break;
		case "by_upc":
			$checked_upc="checked";
			$prod_sort="prdNumber";
			if (($box_upc2>"" && $box_upc2!="type here") && ($box_upc>"" && $box_upc!="type here")) { //box_upc1 and box_upc2 searching
				$query_product="SELECT tblUpc.upcCode, tblUpc.upcDesc, tblUpc.prdNumber, tblUpc.upcStat, tblUpc.upcParTag, tblProdMast.prdDelTag,tblProdMast.prdNumber AS Expr1, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdShort, tblProdMast.prdSuppItem, tblProdMast.prdSellUnit, tblProdMast.prdBuyUnit, tblProdMast.prdSetTag, tblProdMast.prdSubClsCode, tblProdMast.buyerCode
								FROM tblUpc INNER JOIN
								tblProdMast ON tblProdMast.prdNumber = tblUpc.prdNumber
								WHERE (tblProdMast.prdDelTag='A' OR tblProdMast.prdDelTag=' ') AND (tblProdMast.prdDesc BETWEEN '$box_upc' AND '$box_upc2')
								ORDER BY tblProdMast.prdDesc, tblUpc.upcDesc";
			} else {  ///box_upc1 or box_upc2 searching
				if ($box_upc2>"" && $box_upc2!="type here") { ///box upc2 searching
					$query_product="SELECT tblUpc.upcCode, tblUpc.upcDesc, tblUpc.prdNumber, tblUpc.upcStat, tblUpc.upcParTag, tblProdMast.prdDelTag, tblProdMast.prdNumber AS Expr1, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdShort, tblProdMast.prdSuppItem, tblProdMast.prdSellUnit, tblProdMast.prdBuyUnit, tblProdMast.prdSetTag, tblProdMast.prdSubClsCode, tblProdMast.buyerCode
									FROM tblUpc INNER JOIN
									tblProdMast ON tblProdMast.prdNumber = tblUpc.prdNumber
									WHERE (tblProdMast.prdDelTag='A' OR tblProdMast.prdDelTag=' ') AND (tblProdMast.prdDesc LIKE '$box_upc2%')
									ORDER BY tblProdMast.prdDesc, tblUpc.upcDesc";
				} else {
					$query_product="SELECT tblUpc.upcCode, tblUpc.upcDesc, tblUpc.prdNumber, tblUpc.upcStat, tblUpc.upcParTag, tblProdMast.prdDelTag,tblProdMast.prdNumber AS Expr1, tblProdMast.prdDesc, tblProdMast.prdGrpCode, tblProdMast.prdDeptCode, tblProdMast.prdClsCode, tblProdMast.prdConv, tblProdMast.suppCode, tblProdMast.prdShort, tblProdMast.prdSuppItem, tblProdMast.prdSellUnit, tblProdMast.prdBuyUnit, tblProdMast.prdSetTag, tblProdMast.prdSubClsCode, tblProdMast.buyerCode
									FROM tblUpc INNER JOIN
									tblProdMast ON tblProdMast.prdNumber = tblUpc.prdNumber
									WHERE (tblProdMast.prdDelTag='A' OR tblProdMast.prdDelTag=' ') AND (tblProdMast.prdDesc LIKE '$box_upc%')
									ORDER BY tblProdMast.prdDesc, tblUpc.upcDesc";
				}
			}
			break;
		case "by_code":
			$checked_code="checked";
			$prod_sort="prdNumber";
			$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') "
							." AND (prdNumber >= $box_code)"." AND (prdNumber <= $box_code2)".
							" ORDER BY $prod_sort ASC";
							
			if (($box_code2>"" && $box_code2!="type here") && ($box_code>"" && $box_code!="type here")) { //box_upc1 and box_upc2 searching
				$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdNumber >= $box_code) AND (prdNumber <= $box_code2) 
							ORDER BY $prod_sort ASC";
			} else {  ///box_upc1 or box_upc2 searching
				if ($box_code2>"" && $box_code2!="type here") { ///box upc2 searching
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdNumber LIKE '$box_code2%') 
							ORDER BY $prod_sort ASC";
				} else {
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdNumber LIKE '$box_code%') 
							ORDER BY $prod_sort ASC";
				}
			}
			break;
		case "by_desc":
			$checked_desc="checked";
			$prod_sort="prdDesc";
			$box_desc=trim($box_desc);
			$box_desc2=trim($box_desc2);
			if (($box_desc2>"" && $box_desc2!="type here") && ($box_desc>"" && $box_desc!="type here")) { //box_upc1 and box_upc2 searching
				$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdDesc BETWEEN '$box_desc' AND '$box_desc2') 
							ORDER BY $prod_sort ASC";
			} else {  ///box_upc1 or box_upc2 searching
				if ($box_desc2>"" && $box_desc2!="type here") { ///box upc2 searching
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdDesc LIKE '$box_desc2%') 
							ORDER BY $prod_sort ASC";
				} else {
					$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (prdDesc LIKE '$box_desc%') 
							ORDER BY $prod_sort ASC";
				}
			}		
			break;
		case "by_buyer":
			$checked_buyer="checked";
			$prod_sort="prdNumber";
			$query_product="SELECT * FROM tblProdMast WHERE (prdDelTag='A' OR prdDelTag=' ') 
							AND (buyerCode LIKE '$box_buyer%') 
							ORDER BY $prod_sort ASC";
							
			
			break;
	}
	$result_product=mssql_query($query_product);
	$num_product = mssql_num_rows($result_product);
	if ($num_product < 1) {
		/*echo "<script>alert('No Products found... Please enter another.')</script>";*/
		$message="No Products found... Please enter another.";
	} else {
		/*echo "<script>alert('$num_product record/s found!')</script>";*/
		$message="$num_product record/s found!";
	}
}
##############################################################################view product

?>

<html>
<head>
<title>Untitled Document</title>
<style type="text/css">
<!--
.style6 {font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif; }
.style7 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type='text/javascript' src='../../functions/inquiry_function.js'></script>
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
</head>

<body onLoad="val_search_selection();">
<div class="style7"> 
  <div class="style7"> 
    <div class="style7"> 
      <form action="" method="post" name="formissi" target="_self" id="formissi">
        <table width="696" border="0" align="center">
        <tr nowrap="wrap" align="left" bgcolor="#6AB5FF"> 
          <th height="23" nowrap="nowrap" class="style6">Select</th>
            <th width="82" height="23" nowrap="nowrap" class="style6">Search By</th>
            <td width="315" bgcolor="#6AB5FF">&nbsp;  
              <input name="hide_action" type="hidden" id="hide_action4">
              </td>
            <td width="241">
              </td>
          </tr>
          <tr nowrap="wrap" bgcolor="#DEEDD1"> 
            <td width="40"><div align="center">
                <input name='search_selection' type='radio' onClick='val_search_selection(this.value);' value='by_vendor' <? echo $checked_vendor; ?>>
              </div></td>
            <th nowrap="nowrap" class="style6" align="left">Vendor</th>
            <td colspan="2"> 
			  <select name='box_supplier' id='select2' style='width:200px; height:20px'>
			  <option selected><? echo $box_supplier; ?></option>
		<?
		for ($i=0;$i<$num_supplier;$i++){  
				$supplier_code=mssql_result($result_supplier,$i,'suppCode'); 
				$supplier_name=mssql_result($result_supplier,$i,'suppName'); 
				$supplier_name = str_replace("\\","",$supplier_name);
		?>
				<option><? echo $supplier_code."-".$supplier_name; ?></option>
		<? } ?> 
	  			</select>
	  	<? if ($num_product >0) { ?>
	  			  <input name='box_find_supplier2' disabled ='true' type='text' id='box_find_supplier'  onChange='document.getElementById("find").focus();' >
	  			  <input name='btn_find_supplier'  disabled ='true' type='submit' class='queryButton' id='btn_find_supplier' title='Search Suppliers' onClick='document.form1.submit();' value='Find'/>
	  	<? } else { ?>
	  			  <input name='box_find_supplier' type='text' id='box_find_supplier3' onChange='document.getElementById('find').focus();' onFocus='if(this.value=="Code or Name")this.value="";' value='Code or Name'>
	  			  <input name='btn_find_supplier' type='submit' class='queryButton' id='btn_find_supplier' title='Search Suppliers' onClick='document.form1.submit();' value='Find'/>
	  	<? } ?>
			  
              <input name="hide_find_supplier" type="hidden" id="hide_find_supplier" value="<?php echo $hide_find_supplier; ?>">
              <input name="hide_supplier_numeric" type="hidden" id="hide_supplier_numeric" value="<?php echo $hide_supplier_numeric; ?>">
            </td>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <td width="40"><div align="center">
                <input type='radio' name='search_selection' value='by_group' <? echo $checked_group; ?> onClick='val_search_selection(this.value);'>
              </div></td>
            <th height="23" nowrap="nowrap" class="style6" align="left">Prod Class</th>
            <td width="315">
		  		<? 
				if ($num_product >0) { ?>
					<select name='box_group' id='select4' style='width:200px;'>
				<? } else {  ?>
					<select name='box_group' id='select7' onChange='document.formissi.submit();'>
				<? } ?>
		
					<option selected> <? echo $box_group; ?> </option>";
		<?
		$query_prod_group="SELECT * FROM tblProdClass WHERE prdClstStat = 'A' AND prdClsLvl = 1 ORDER BY prdDeptCode ASC";
		$result_prod_group=mssql_query($query_prod_group);
		$num_prod_group = mssql_num_rows($result_prod_group);
		for ($i=0;$i<$num_prod_group;$i++){  
				$prod_group_code=mssql_result($result_prod_group,$i,"prdGrpCode"); 
				$prod_group_desc=mssql_result($result_prod_group,$i,"prdClsDesc"); 
			
				echo "<option>$prod_group_code - $prod_group_desc</option>";
		}
		?>
		<option> </option></select>
		
	
             </td>
            <td width="241">&nbsp;</td>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <td width="40"><div align="center">
                <input type='radio' name='search_selection' value='by_upc' <? echo $checked_upc; ?> onClick='val_search_selection(this.value);'>
              </div></td>
            <th height="23" nowrap="nowrap" class="style6" align="left">UPC By 
              Prod</th>
            <td width="315"> 
              <? 
			  		if ($box_upc=="") {
			  			$box_upc="type here";
			     	}
					if ($box_upc2=="") {
			  			$box_upc2="type here";
			     	}
					if ($box_desc=="") {
			  			$box_desc="type here";
			     	}
					if ($box_code=="") {
			  			$box_code="type here";
			     	}
					if ($box_code2=="") {
			  			$box_code2="type here";
			     	}
					if ($box_buyer=="") {
			  			$box_buyer="type here";
			     	}
					if ($box_desc2=="") {
			  			$box_desc2="type here";
			     	}
			  ?>
              From Desc 
              <input name="box_upc" type="text" style="width:170px;" id="box_upc" value="<? echo $box_upc; ?>" size="20" maxlength="20" onFocus="if(this.value=='type here')this.value='';" onChange="val_upc();">
          </td>
            
            <td width="241"> To Desc 
              <input name="box_upc2" type="text" style="width:170px;" id="box_upc2" value="<? echo $box_upc2; ?>" size="20" maxlength="20" onChange="val_upc2();" onFocus="if(this.value=='type here')this.value='';" >
          </td>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            
          <td width="40"> 
            <div align="center">
                <input type='radio' name='search_selection' value='by_desc' <? echo $checked_desc; ?> onClick='val_search_selection(this.value);'>
              </div></td>
            <th height="23" nowrap="nowrap" class="style6" align="left">Prod Desc</th>
            <td width="315"> From 
              <input name="box_desc" type="text" style="width:170px;" id="box_desc" value="<? echo $box_desc; ?>" size="20" maxlength="50" onChange="val_desc();" onFocus="if(this.value=='type here')this.value='';"  >
          </td>
            <td width="241">To 
              <input name="box_desc2" type="text" style="width:170px;" id="box_desc2" value="<? echo $box_desc2; ?>" size="20" maxlength="50" onChange="val_desc();" onFocus="if(this.value=='type here')this.value='';"  ></td>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <td width="40"><div align="center">
                <input type='radio' name='search_selection' value='by_code' <? echo $checked_code; ?> onClick='val_search_selection(this.value);'>
              </div></td>
            <th height="23" align="left" nowrap="nowrap" bgcolor="#DEEDD1" class="style6">Prod No</th>
            <td width="315"> From 
              <input name="box_code" type="text" style="width:170px;" id="box_code" value="<? echo $box_code; ?>" size="20" onChange="val_code();" onFocus="if(this.value=='type here')this.value='';" >
          </td>
            <td width="241"> To 
              <input name="box_code2" type="text" style="width:170px;" id="box_code2" value="<? echo $box_code2; ?>" size="20" maxlength="20" onChange="val_code2();" onFocus="if(this.value=='type here')this.value='';">
          </td>
          </tr>
		  <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <td width="40"><div align="center">
                <input type='radio' name='search_selection' value='by_buyer' <? echo $checked_buyer; ?> onClick='val_search_selection(this.value);'>
              </div></td>
            
          <th height="23" nowrap="nowrap" class="style6" align="left">Buyer</th>
            <td width="315"> 
              <input name="box_buyer" type="text" style="width:170px;" id="box_buyer" value="<? echo $box_buyer; ?>" size="20" onChange="val_buyer();" onFocus="if(this.value=='type here')this.value='';" >
              <img src="../images/search.gif" name="img_code" id="img_code" style="cursor:pointer;" title="Buyer LookUp" onclick="window.open('buyer_lookup.php?search_selection=buyer','','width=500,height=500,left=250,top=100')"/>
			 </td>
            
          <td width="241" bgcolor="#DEEDD1">&nbsp; </td>
          </tr>
          <tr nowrap="wrap"  bgcolor="#DEEDD1"> 
            <td colspan="4"><div align="center"> 
                <div align="center"> 
                <?   
					if ($num_product < 1){
						echo "<input name='inquire' type='button' class='queryButton' id='inquire' title='Display the inventory stock status'  onClick='val_view_button();' value='View Products'/>
						<input name='clear' type='button' disabled='true' class='queryButton' id='clear' title='Search New Record' onClick='document.form1.submit();' value='Clear New Search'/>
						<input name='print' type='button' disabled='true' class='queryButton' id='print' title='Print Searched Product' onClick='document.form2.submit();' value='Print'/>";
 					} else { 
						echo "<input name='inquire' type='submit'  disabled ='true' class='queryButton' id='inquire' title='Display the inventory stock status' value='View Products'/>
						<input name='clear' type='button' class='queryButton' id='clear' title='Search New Record' onClick='document.form1.submit();' value='Clear New Search'/>
						<input name='print' type='button' class='queryButton' id='print' title='Print Searched Product' onClick='document.form2.submit();' value='Print'/>";
					} 
				?> 
	</div></td></tr></table><p align='center'>

	<div id='Layer1' style='position:absolute; left:9px; top:228px; width:98.5%; height:273px; z-index:1; overflow: auto;'> 
	<div align='center'></div>
	      <table width='95%' border='0' align="center" >
            <tr align='left' nowrap='wrap' bgcolor="#DEEDD1"> 
              <td colspan="5"><div align="center"><strong><font color="#6AB5FF" size="2"> 
                  <? 
  						if ($message) {						
							echo "<FONT COLOR=RED><BLINK>$message</BLINK></FONT>";
						}
					  ?>
                  </font></strong></div></td>
            </tr>
            <tr nowrap='wrap' align='left'> 
              <td width="82" bgcolor="#6AB5FF"><strong><font size="2">Code </font></strong></td>
              <td width="207" bgcolor="#6AB5FF"><strong><font size="2">Description</font></strong></td>
              <td width="173" bgcolor="#6AB5FF"><strong><font size="2">Grp/Dept/Cls/Sub-Cls</font></strong></td>
              <td width="102" bgcolor="#6AB5FF"><strong><font size="2">Sell/Buy/Conv</font></strong></td>
              <td width="123" bgcolor="#6AB5FF"><strong><font size="2"> Primary 
                UPC 
                <? for ($i=0;$i<$num_product;$i++){ 
				$grid_code=mssql_result($result_product,$i,"prdNumber");
				$grid_desc=mssql_result($result_product,$i,"prdDesc");
				$grid_desc=str_replace("\\","",$grid_desc);
				$grid_group_code=mssql_result($result_product,$i,"prdGrpCode");
				$grid_dept=mssql_result($result_product,$i,"prdDeptCode");
				$grid_class=mssql_result($result_product,$i,"prdClsCode");
				$grid_sub_class=mssql_result($result_product,$i,"prdSubClsCode");
				$grid_buyer=mssql_result($result_product,$i,"buyerCode");
				$grid_supplier_code=mssql_result($result_product,$i,"suppCode");
				$grid_upc=mssql_result($result_product,$i,"prdSuppItem");
				$grid_sell=mssql_result($result_product,$i,"prdSellUnit");
				$grid_buy=mssql_result($result_product,$i,"prdBuyUnit");
				$grid_conv=mssql_result($result_product,$i,"prdConv");
				$grid_conv=number_format($grid_conv,0);
				$grid_grp=$grid_group_code."/".$grid_dept."/".$grid_class."/".$grid_sub_class;
				$grid_sell_buy=$grid_sell."/".$grid_buy."/".$grid_conv;
				if ($search_selection=="by_upc") {
					$grid_upc_code=mssql_result($result_product,$i,"upcCode");
					$grid_upc_desc=mssql_result($result_product,$i,"upcDesc");
					$grid_upc_upc=$grid_upc_code."-".$grid_upc_desc;
				}
				///// get prdGrpDesc from tblProdGrp
				$query_group_desc="SELECT * FROM tblProdClass WHERE prdClsLvl = 1 AND prdGrpCode = $grid_group_code";
				$result_group_desc=mssql_query($query_group_desc);
				$num_group_desc = mssql_num_rows($result_group_desc);
				if ($num_group_desc>0) {
					$grid_group=mssql_result($result_group_desc,0,"prdClsDesc");
					$grid_group = $grid_group_code."-".$grid_group;
				} else {
					$grid_group="NA";
				}
				///// get suppName from tblSuppliers
				$query_supplier_name="SELECT * FROM tblSuppliers WHERE suppCode LIKE '$grid_supplier_code'";
				$result_supplier_name=mssql_query($query_supplier_name);
				$num_supplier_name = mssql_num_rows($result_supplier_name);
				if ($num_supplier_name>0) {
					$grid_vendor=mssql_result($result_supplier_name,0,"suppName");
					$grid_vendor = str_replace("\\","",$grid_vendor);
					$grid_vendor = $grid_supplier_code. "-".$grid_vendor;
				} else {
					$grid_vendor="NA";
				}
				if ($select_code==$grid_code) {
					$code_checked="checked";
				} else {
					$code_checked="";
				}
				?>
                </font></strong></td>
            </tr>
            <tr nowrap='wrap' bgcolor="#DEEDD1"> 
              <th width='82' height='23' align='left' nowrap='nowrap' class='style6'> 
                <? echo $grid_code; ?> </th>
              <th width='207' height='23' align='left' nowrap='nowrap' class='style6'> 
                <? echo $grid_desc; ?> </th>
              <th width='173' height='23' align='left' nowrap='nowrap' class='style6'> 
                <? echo $grid_grp; ?> </th>
              <th width='102' height='23' align='left' nowrap='nowrap' bgcolor="#DEEDD1" class='style6'> 
                <? echo $grid_sell_buy; ?> </th>
              <th width='123' height='23' align='left' nowrap='nowrap' class='style6'> 
                <? 
				if ($search_selection=="by_upc") {
					echo $grid_upc_upc;  
				} else {
					echo $grid_upc;  
				}
				}		  ?>
              </th>
            </tr>
          </table>
				  <p>&nbsp;</p>
				  </div>
        <p>&nbsp;</p>
        </form>
        
      <form action="" method="post" name="form1" target="_self">
        <div align="center"> 
             
        </div>
      </form>
      <form action="prod_inquiry_pdf.php" method="post" name="form2" target="_blank">
        <input name="hide_search_selection" type="hidden" id="hide_search_selection" value="<?php echo $search_selection; ?>">
        <input name="hide_box_supplier" type="hidden" id="hide_box_supplier" value="<?php echo $box_supplier ?>">
        <input name="hide_box_group" type="hidden" id="hide_box_group" value="<?php echo $box_group; ?>"> 
		<input name="hide_box_upc" type="hidden" id="hide_box_upc" value="<?php echo $box_upc; ?>"> 
		<input name="hide_box_upc2" type="hidden" id="hide_box_upc2" value="<?php echo $box_upc2; ?>"> 
		<input name="hide_box_code" type="hidden" id="hide_box_code" value="<?php echo $box_code; ?>"> 
		<input name="hide_box_code2" type="hidden" id="hide_box_code2" value="<?php echo $box_code2; ?>"> 
		<input name="hide_box_desc" type="hidden" id="hide_box_desc" value="<?php echo $box_desc; ?>"> 
		<input name="hide_box_desc2" type="hidden" id="hide_box_desc2" value="<?php echo $box_desc2; ?>"> 
		<input name="hide_box_buyer" type="hidden" id="hide_box_buyer" value="<?php echo $box_buyer; ?>"> 
      </form>
      <p align="center">  
        </p>
    </div>
  </div>
</div>
</body>
</html>

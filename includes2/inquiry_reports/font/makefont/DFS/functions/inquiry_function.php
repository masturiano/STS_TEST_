<?php 
function getCodeofString($string){
	///"-" = separator
	$stringlen=strlen($string);
	$stringlen=$stringlen-1;
	$stringnew="";
	for ($i=0;$i<=$stringlen;$i++){
		if ($string[$i]!="-") { 
			$stringnew=$stringnew.$string[$i];
		} else {
			break;
		}
	}
	$string=$stringnew;
	return $string;
}

function getMonthName($month){
	$selectedmonth="";
	if ($month=="January") { $selectedmonth=1;}
	if ($month=="February") { $selectedmonth=2; }
	if ($month=="March") { $selectedmonth=3; }
	if ($month=="April") { $selectedmonth=4; }
	if ($month=="May") { $selectedmonth=5; }
	if ($month=="June") { $selectedmonth=6; }
	if ($month=="July") { $selectedmonth=7; }
	if ($month=="August") { $selectedmonth=8; }
	if ($month=="September") { $selectedmonth=9; }
	if ($month=="October") { $selectedmonth=10; }
	if ($month=="November") { $selectedmonth=11; }
	if ($month=="December") { $selectedmonth=12; }
	$month=$selectedmonth;
	return $month;
}

function getMonthNum($month){
	$selectedmonth="";
	if ($month=="1") { $selectedmonth="January";}
	if ($month=="2") { $selectedmonth="February"; }
	if ($month=="3") { $selectedmonth="March"; }
	if ($month=="4") { $selectedmonth="April"; }
	if ($month=="5") { $selectedmonth="May"; }
	if ($month=="6") { $selectedmonth="June"; }
	if ($month=="7") { $selectedmonth="July"; }
	if ($month=="8") { $selectedmonth="August"; }
	if ($month=="9") { $selectedmonth="September"; }
	if ($month=="10") { $selectedmonth="October"; }
	if ($month=="11") { $selectedmonth="November"; }
	if ($month=="12") { $selectedmonth="December"; }
	$month=$selectedmonth;
	return $month;
}
	
class date_difference
{
	public $date1, $date2, $a, $days, $hours, $minutes, $seconds;
	function __construct($date_, $date__)
	{
		$this->date1 = $date_;
		$this->date2 = $date__;
		$this->days = intval((strtotime($this->date1) - strtotime($this->date2)) / 86400);
		$this->a = ((strtotime($this->date1) - strtotime($this->date2))) % 86400;
		$this->hours = intval(($this->a) / 3600);
		$this->a = ($this->a) % 3600;
		$this->minutes = intval(($this->a) / 60);
		$this->a = ($this->a) % 60;
		$this->seconds = $this->a;
	}
}
function check_date_error($funcdate){
	$new_month="";
	$new_day="";
	$new_year="";
	$date_length = strlen($funcdate);
	for ($i=0;$i<=$date_length;$i++){
		if ($funcdate[$i]!="/"){ 
			$new_month=$new_month.$funcdate[$i];
		} else {
			break;
		}
	}
	$i++;
	for ($i=$i;$i<=$date_length;$i++){
		if ($funcdate[$i]!="/"){ 
			$new_day=$new_day.$funcdate[$i];
		} else {
			break;
		}
	}
	$i++;
	for ($i=$i;$i<=$date_length;$i++){
		if ($funcdate[$i]!="/"){ 
			$new_year=$new_year.$funcdate[$i];
		} else {
			break;
		}
	}
	$message="";
	
	$new_month = $new_month * 1;
	$new_day = $new_day * 1;
	$new_year = $new_year * 1;
	
	$check_date = checkdate($new_month,$new_day,$new_year);
	$check_date = $check_date * 1;
	if ($check_date < 1){
		$message="Invalid Date : ";
	} else {
		$message="";
	}
	$funcdate=$message;
	return $funcdate;
}

function find_product_function($findproduct){
	if(isset($_POST['find'])) { 
		if ($findproduct=="") {
			echo "<script>alert('Key-in Product Code or Description!')</script>";
		} else {
			if ($findproduct<>"*") {
				if(is_numeric($findproduct)) {
					$queryfindproduct="SELECT * FROM tblProdMast 
									WHERE prdNumber LIKE '%$findproduct%'
									ORDER BY prdNumber ASC";
					$resultfindproduct=mssql_query($queryfindproduct);
					$numfindproduct = mssql_num_rows($resultfindproduct);
					if ($numfindproduct>0) {
						$findproductprdNumber=mssql_result($resultfindproduct,0,"prdNumber");
						$findproductprdDesc=mssql_result($resultfindproduct,0,"prdDesc");
						$findproductprdSellUnit=mssql_result($resultfindproduct,0,"prdSellUnit");
						$productcode = $findproductprdNumber." - ".$findproductprdDesc." - ".$findproductprdSellUnit;
					} else {
						echo "<script>alert('No Product records found!')</script>";
					}
				} else {
					$queryfindproduct="SELECT * FROM tblProdMast 
									WHERE prdDesc LIKE '%$findproduct%'
									ORDER BY prdDesc ASC";
					$resultfindproduct=mssql_query($queryfindproduct);
					$numfindproduct = mssql_num_rows($resultfindproduct);
					if ($numfindproduct>0) {
						$findproductprdNumber=mssql_result($resultfindproduct,0,"prdNumber");
						$findproductprdDesc=mssql_result($resultfindproduct,0,"prdDesc");
						$findproductprdSellUnit=mssql_result($resultfindproduct,0,"prdSellUnit");
						$productcode = $findproductprdNumber." - ".$findproductprdDesc." - ".$findproductprdSellUnit;
					} else {
						echo "<script>alert('No Product records found!')</script>";
					}
				}
			 }
		}
	}
}

function find_vendor_function($findvendor){
	if(isset($_POST['find2'])) { 
		if ($findvendor=="") {
			echo "<script>alert('Key-in Vendor Code or Name!')</script>";
		} else {
			if ($findvendor<>"*") {
				if(is_numeric($findvendor)) {
					$query_find_vendor="SELECT * FROM tblSuppliers 
									WHERE suppCode LIKE '%$findvendor%'
									ORDER BY suppCode ASC";
					$result_find_vendor=mssql_query($query_find_vendor);
					$num_find_vendor = mssql_num_rows($result_find_vendor);
					if ($num_find_vendor>0) {
						$find_supp_code=mssql_result($result_find_vendor,0,"suppCode");
						$find_supp_name=mssql_result($result_find_vendor,0,"suppName");
						$vendorcode = $find_supp_code."-----".$find_supp_name;
					} else {
						echo "<script>alert('No Vendor records found!')</script>";
					}
				} else {
					$query_find_vendor="SELECT * FROM tblSuppliers 
									WHERE suppName LIKE '%$findvendor%'
									ORDER BY suppName ASC";
					$result_find_vendor=mssql_query($query_find_vendor);
					$num_find_vendor = mssql_num_rows($result_find_vendor);
					if ($num_find_vendor>0) {
						$find_supp_code=mssql_result($result_find_vendor,0,"suppCode");
						$find_supp_name=mssql_result($result_find_vendor,0,"suppName");
						$vendorcode = $find_supp_code."-----".$find_supp_name;
					} else {
						echo "<script>alert('No Vendor records found!')</script>";
					}
				}
			}
		}
	}
}
	
function find_buyer_function($box_find_buyer){
	if(isset($_POST['btn_find_buyer'])) { 
		if ($box_find_buyer=="") {
			echo "<script>alert('Key-in Buyer Code or Name!')</script>";
		} else {
			if ($box_find_buyer<>"*") {
				if(is_numeric($box_find_buyer)) {
					$query_find_buyer="SELECT * FROM tblBuyers
									WHERE buyerCode LIKE '%$box_find_buyer%'
									ORDER BY buyerCode ASC";
					$result_find_buyer=mssql_query($query_find_buyer);
					$num_find_buyer = mssql_num_rows($result_find_buyer);
					if ($num_find_buyer>0) {
						$buyer_code=mssql_result($result_find_buyer,0,"buyerCode");
						$buyer_name=mssql_result($result_find_buyer,0,"buyerName");
						$box_buyer = $buyer_code." - ".$buyer_name;
					} else {
						echo "<script>alert('No Buyer records found!')</script>";
					}
				} else {
					$query_find_buyer="SELECT * FROM tblBuyers
									WHERE buyerName LIKE '%$box_find_buyer%'
									ORDER BY buyerName ASC";
					$result_find_buyer=mssql_query($query_find_buyer);
					$num_find_buyer = mssql_num_rows($result_find_buyer);
					if ($num_find_buyer>0) {
						$buyer_code=mssql_result($result_find_buyer,0,"buyerCode");
						$buyer_name=mssql_result($result_find_buyer,0,"buyerName");
						$box_buyer = $buyer_code." - ".$buyer_name;
					} else {
						echo "<script>alert('No Buyer records found!')</script>";
					}
				}
			 }
		}
	}
}

function find_transfer_function($text_transfer){
	include "../../functions/inquiry_session.php";
	
	if(isset($_POST['button_transfer'])) { 
		if ($text_transfer=="") {
			echo "<script>alert('Key-in Transfer Number or Date!')</script>";
		} else {
			
			if ($text_transfer<>"*") {
				if(is_numeric($text_transfer)) {
					
					$query_find_transfer="SELECT * FROM tblTransferHeader
									WHERE (compCode =  $company_code) AND (trfStatus <> 'R') AND trfNumber LIKE '%$text_transfer%'
									ORDER BY trfNumber ASC";
					$result_find_transfer=mssql_query($query_find_transfer);
					$num_find_transfer = mssql_num_rows($result_find_transfer);
					if ($num_find_transfer>0) {
						$transfer_no=mssql_result($result_find_transfer,0,"trfNumber");
						$transfer_date=mssql_result($result_find_transfer,0,"trfDate");
						$date = new DateTime($transfer_date);
						$transfer_date = $date->format("m/d/Y");
						$combo_transfer= $transfer_no." - ".$transfer_date;
					} else {
						echo "<script>alert('No transfers records found!')</script>";
					}
				} else { 
					$query_find_transfer="SELECT * FROM tblTransferHeader
									WHERE (compCode =  $company_code) AND (trfStatus <> 'R') AND trfDate = '$text_transfer'
									ORDER BY trfDate ASC";
					$result_find_transfer=mssql_query($query_find_transfer);
					$num_find_transfer = mssql_num_rows($result_find_transfer);
					if ($num_find_transfer>0) {
						$transfer_no=mssql_result($result_find_transfer,0,"trfNumber");
						$transfer_date=mssql_result($result_find_transfer,0,"trfDate");
						$date = new DateTime($transfer_date);
						$transfer_date = $date->format("m/d/Y");
						$combo_transfer= $transfer_no." - ".$transfer_date;
					} else {
						echo "<script>alert('No transfers records found!')</script>";
					}
				}
			 }
		}
	}
}
?>

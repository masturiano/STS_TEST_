// JavaScript Document
function val_onload_record_found() {
	var by_vendor = document.formissi.search_selection[0].checked;
	var by_group = document.formissi.search_selection[1].checked;
	var by_upc = document.formissi.search_selection[2].checked;
	var by_desc = document.formissi.search_selection[3].checked;
	var by_code = document.formissi.search_selection[4].checked;
	var by_buyer = document.formissi.search_selection[5].checked;
}
function val_search_selection() {
	var by_vendor = document.formissi.search_selection[0].checked;
	var by_group = document.formissi.search_selection[1].checked;
	var by_upc = document.formissi.search_selection[2].checked;
	var by_desc = document.formissi.search_selection[3].checked;
	var by_code = document.formissi.search_selection[4].checked;
	var by_buyer = document.formissi.search_selection[5].checked;
	var clear = document.formissi.clear.disabled;
	
	if (clear==false) {
		document.formissi.search_selection[0].disabled = true;
		document.formissi.search_selection[1].disabled = true;
		document.formissi.search_selection[2].disabled = true;
		document.formissi.search_selection[3].disabled = true;
		document.formissi.search_selection[4].disabled = true;
		document.formissi.search_selection[5].disabled = true;
		document.formissi.box_buyer.disabled=true;
		document.formissi.box_group.disabled=true;
		document.formissi.box_upc.disabled=true;
		document.formissi.box_upc2.disabled=true;
		document.formissi.box_desc.disabled=true;
		document.formissi.box_desc2.disabled=true;
		document.formissi.box_code.disabled=true;
		document.formissi.box_code2.disabled=true;
		document.formissi.box_supplier.disabled=true;
		document.formissi.btn_find_supplier.disabled=true;
		document.formissi.box_find_supplier.disabled=true;
		return false;
	}
	if (by_vendor==true) {
		<!-- TEXT BOX -->
		document.formissi.box_buyer.disabled=true;
		document.formissi.box_group.disabled=true;
		document.formissi.box_upc.disabled=true;
		document.formissi.box_upc2.disabled=true;
		document.formissi.box_desc.disabled=true;
		document.formissi.box_desc2.disabled=true;
		document.formissi.box_code.disabled=true;
		document.formissi.box_code2.disabled=true;
		document.formissi.box_supplier.disabled=false;
		document.formissi.btn_find_supplier.disabled=false;
		document.formissi.box_find_supplier.disabled=false;
		return true;
	}
	if (by_group==true) {
		document.formissi.box_buyer.disabled=true;
		document.formissi.box_group.disabled=false;
		document.formissi.box_desc.disabled=true;
		document.formissi.box_desc2.disabled=true;
		document.formissi.box_code.disabled=true;
		document.formissi.box_code2.disabled=true;
		document.formissi.box_supplier.disabled=true;
		document.formissi.btn_find_supplier.disabled=true;
		document.formissi.box_find_supplier.disabled=true;
		document.formissi.box_upc.disabled=true;
		document.formissi.box_upc2.disabled=true;
		return true;
	}
	if (by_upc==true) {
		document.formissi.box_buyer.disabled=true;
		document.formissi.box_group.disabled=true;
		document.formissi.box_upc.disabled=false;
		document.formissi.box_upc2.disabled=false;
		document.formissi.box_desc.disabled=true;
		document.formissi.box_desc2.disabled=true;
		document.formissi.box_code.disabled=true;
		document.formissi.box_code2.disabled=true;
		document.formissi.box_supplier.disabled=true;
		document.formissi.btn_find_supplier.disabled=true;
		document.formissi.box_find_supplier.disabled=true;
		return true;
	}
	if (by_desc==true) {
		document.formissi.box_buyer.disabled=true;
		document.formissi.box_group.disabled=true;
		document.formissi.box_upc.disabled=true;
		document.formissi.box_upc2.disabled=true;
		document.formissi.box_desc.disabled=false;
		document.formissi.box_desc2.disabled=false;
		document.formissi.box_code.disabled=true;
		document.formissi.box_code2.disabled=true;
		document.formissi.box_supplier.disabled=true;
		document.formissi.btn_find_supplier.disabled=true;
		document.formissi.box_find_supplier.disabled=true;
		return true;
	}
	if (by_code==true) {
		document.formissi.box_buyer.disabled=true;
		document.formissi.box_group.disabled=true;
		document.formissi.box_upc.disabled=true;
		document.formissi.box_upc2.disabled=true;
		document.formissi.box_desc.disabled=true;
		
		document.formissi.box_desc2.disabled=true;
		document.formissi.box_code.disabled=false;
		document.formissi.box_code2.disabled=false;
		document.formissi.box_supplier.disabled=true;
		document.formissi.btn_find_supplier.disabled=true;
		document.formissi.box_find_supplier.disabled=true;
		return true;
	}
	if (by_buyer==true) {
		document.formissi.box_buyer.disabled=false;
		document.formissi.box_group.disabled=true;
		document.formissi.box_upc.disabled=true;
		document.formissi.box_upc2.disabled=true;
		document.formissi.box_desc.disabled=true;
		document.formissi.box_desc2.disabled=true;
		document.formissi.box_code.disabled=true;
		document.formissi.box_code2.disabled=true;
		document.formissi.box_supplier.disabled=true;
		document.formissi.btn_find_supplier.disabled=true;
		document.formissi.box_find_supplier.disabled=true;
		return true;
	}
}

function val_buyer() {
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	var box_buyer = document.formissi.box_buyer.value;
		if(!box_buyer.match(numeric_expression)) {
			alert("Buyer No: Numbers only");
			document.formissi.box_buyer.value="";
			document.formissi.box_buyer.focus();
			return false;
		} 
}

function val_code() {
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	var box_code = document.formissi.box_code.value;
		if(!box_code.match(numeric_expression)) {
			alert("Product No: Numbers only");
			document.formissi.box_code.value="";
			document.formissi.box_code.focus();
			return false;
		} 
}
function val_code2() {
	var numeric_expression = /^(\d+\.\d{0,4}|\d+)$/;
	var box_code2 = document.formissi.box_code2.value;
		if(!box_code2.match(numeric_expression)) {
			alert("Product No: Numbers only");
			document.formissi.box_code2.value="";
			document.formissi.box_code2.focus();
			return false;
		} 
}

function val_view_button() {
	var by_vendor = document.formissi.search_selection[0].checked;
	var by_group = document.formissi.search_selection[1].checked;
	var by_upc = document.formissi.search_selection[2].checked;
	var by_desc = document.formissi.search_selection[3].checked;
	var by_code = document.formissi.search_selection[4].checked;
	var by_buyer = document.formissi.search_selection[5].checked;
	
	var box_supplier = document.formissi.box_supplier.value;
	var box_group = document.formissi.box_group.value;
	var box_upc = document.formissi.box_upc.value;
	var box_upc2 = document.formissi.box_upc2.value;
	var box_desc = document.formissi.box_desc.value;
	var box_desc2 = document.formissi.box_desc2.value;
	var box_code = document.formissi.box_code.value;
	var box_code2 = document.formissi.box_code2.value;
	var box_buyer = document.formissi.box_buyer.value;
	
	if (by_vendor==true) {
		if (box_supplier=="") {
			alert("Key-in Vendor!");
			return false;
		}
	}
	if (by_group==true) {
		if (box_group=="") {
			alert("Key-in Group!");
			return false;
		}
	}
	if (by_upc==true) {
		if ((box_upc=="" || box_upc=="type here") && (box_upc2=="" || box_upc2=="type here")) {
			alert("Key-in UPC!");
			return false;
		}
	}
	if (by_desc==true) {
		if ((box_desc=="" || box_desc=="type here") && (box_desc2=="" || box_desc2=="type here")) {
			alert("Key-in Product Description!");
			return false;
		}
	}
	if (by_code==true) {
		if ((box_code=="" || box_code=="type here") && (box_code2=="" || box_code2=="type here")) {
			alert("Key-in Product Code!");
			return false;
		}
	}
	if (by_buyer==true) {
		if (box_buyer=="" || box_buyer=="type here") {
			alert("Key-in Buyer Code!");
			return false;
		}
	}
	document.formissi.hide_action.value="view_record";
	document.formissi.submit();
}

function open_prod_list() {
	var numericExpression = /\t/;  
	var key;
	if (window.event)
		key = window.event.keyCode;
	  else if (event)
		key = event.which;
	  else
		return true;
		alert(key);
}

function isNumberInput(field, event) 
{
	var key, keyChar;
	if (window.event)
		key = window.event.keyCode;
	else if (event)
		key = event.which;
	else
		return true;
	if(key==13) {
		window.open('product_lookup.php','','width=500,height=500,left=250,top=100')
	}
}

function validate_user() 
{
	alert ("Please Login!");
	window.close();
}
// JavaScript Document
function open_lookup() {
	var trans_no = document.transfers_form.trans_no.value;
	var from_location = document.transfers_form.from_location.value;
	var to_location = document.transfers_form.to_location.value;
	var remarks = document.transfers_form.remarks.value;
	var responsible = document.transfers_form.responsible.value;
	var hash_items = document.transfers_form.hash_items.value;
	var hash_quantity = document.transfers_form.hash_quantity.value;
	var message = "";
	var numbering = 0;
	if (parseInt(trans_no.length)<1 || trans_no=="0") {
		numbering++;
		message = message + numbering + ". Select Transfer Number.\r\n";
	}
	if (parseInt(from_location.length)<1) {
		numbering++;
		message = message + numbering + ". Select From Location.\r\n";
	}
	if (parseInt(to_location.length)<1) {
		numbering++;
		message = message +  numbering +". Select To Location.\r\n";
	}
	if (remarks=="") {
		numbering++;
		message = message + numbering +". Key-in Remarks.\r\n";
	}
	if (hash_items<1) {
		numbering++;
		message = message + numbering +". Key-in Hash Items.\r\n";
	}
	if (hash_quantity<1) {
		numbering++;
		message = message + numbering +". Key-in Hash Quantity.\r\n";
	}
	if (numbering>0) {
		message = "Do it first...\r\n\r\n"+message
		alert(message);
		return false;
	} else {
		//var hide_num = document.transfers_form.hide_num.value;
		var hide_button = document.transfers_form.hide_button.value;
		if (hide_button=="new_transfers") {
			var hide_num = document.transfers_form.hide_num.value;
			if (hide_num<1) {
				alert("Select Details in the UPC Lookup.");
				return false;
			} else {
				var i=0;
				var qty_temp="";
				var qty_temp_new=0;
				var flag_ko = 0;
				for(i=0; i<hide_num; i++) {
					qty_temp="quantity_outz"+i;
					var qty_temp_new = document.getElementById(qty_temp).value;
					if (parseInt(qty_temp_new)>0) {
						flag_ko++;
						break;
					}
				}
				if (flag_ko<1) {
					alert("No Details selected.");
					return false;
				}
			}
		}
		document.getElementById("hide_find").value="";
		document.getElementById("hide_insert").value="insert_mode";
		document.transfers_form.hide_button.value="";
		document.transfers_form.submit();
	}
	//document.getElementById("product_lookup").style.display='';
	
}
function open_lookup2() {
	var hide_num_details = document.transfers_form.hide_num_details.value;
	if (hide_num_details<1) {
		alert("Select Details in the UPC Lookup.");
		return false;
	} else {
		var i=0;
		var check_temp="";
		var check_temp_new=0;
		var flag_ko = 0;
		for(i=0; i<hide_num_details; i++) {
			check_temp="check_detail"+i;
			var check_temp_new = document.getElementById(check_temp).checked;
			if (check_temp_new==true) {
				flag_ko++;
				break;
			}
		}
		if (flag_ko<1) {
			alert("No Details selected.");
			return false;
		}
	}
	document.getElementById("hide_find").value="";
	document.getElementById("hide_insert").value="edit_mode";
	document.transfers_form.hide_button.value="";
	document.transfers_form.submit();
}
function open_lookup3() {
	var hide_num_details = document.transfers_form.hide_num_details.value;
	if (hide_num_details<1) {
		alert("Select Details in the UPC Lookup.");
		return false;
	} else {
		var i=0;
		var check_temp="";
		var check_temp_new=0;
		var flag_ko = 0;
		for(i=0; i<hide_num_details; i++) {
			check_temp="check_detail"+i;
			var check_temp_new = document.getElementById(check_temp).checked;
			if (check_temp_new==true) {
				flag_ko++;
				break;
			}
		}
		if (flag_ko<1) {
			alert("No Details selected.");
			return false;
		}
	}
	
	if (hide_num_details<2) {
		alert("Cannot delete this detail.");
		return false;
	}
	
	var confirm_delete = confirm("Are you sure you want to delete the selected detail/s? \r\nOk(YES)  Cancel(NO)");
	if (confirm_delete) {
		document.getElementById("hide_find").value="";
		document.getElementById("hide_insert").value="delete_mode";
		document.transfers_form.hide_button.value="";
		document.transfers_form.submit();
	} 
}
function open_lookup4() {
	var confirm_delete = confirm("Are you sure you want to delete this Transfer? \r\nOk(YES)  Cancel(NO)");
	if (confirm_delete) {
		document.getElementById("hide_find").value="";
		document.getElementById("hide_insert").value="delete_all_mode";
		document.transfers_form.hide_button.value="";
		document.transfers_form.submit();
	} else {
		document.getElementById("hide_find").value="";
		document.getElementById("hide_insert").value="cancel_delete_all_mode";
		document.transfers_form.hide_button.value="";
		document.transfers_form.submit();
	}
}
function open_lookup5() {
	var total_out = document.transfers_form.total_out.value;
	var total_in = document.transfers_form.total_in.value;
	var message1 = "";
	if (total_out!=total_in) {
		message1 = "Totals Quantity Out : "+total_out+"\r\nTotals Quantity In : "+total_in+"\r\n\r\nTotals Quantity are not equal...\r\n\r\n";
	} else {
		message1 = "Totals Quantity Out : "+total_out+"\r\nTotals Quantity In : "+total_in+"\r\n\r\nTotals Quantity are complete and accurate...\r\n\r\n";
	}
	if (parseInt(total_in)<1) {
		alert(message1+"Cannot release... Totals Quantity In are equal to zero(0).");
		document.getElementById("hide_find").value="";
		document.getElementById("hide_insert").value="cancel_release_mode";
		document.transfers_form.hide_button.value="";
		document.transfers_form.submit();
	} else {
		var message2 = message1+"Are you sure you want to release this Transfer? \r\nOk(YES)  Cancel(NO)";
		var confirm_release = confirm(message2);
		if (confirm_release) {
			document.getElementById("hide_find").value="";
			document.getElementById("hide_insert").value="release_mode";
			document.transfers_form.hide_button.value="";
			document.transfers_form.submit();	
		} else {
			document.getElementById("hide_find").value="";
			document.getElementById("hide_insert").value="cancel_release_mode";
			document.transfers_form.hide_button.value="";
			document.transfers_form.submit();	
		}
	}	
}
function close_lookup() {
	document.getElementById("hide_find").value="";
	document.getElementById("hide_insert").value="";
	//document.getElementById("product_lookup").style.display='none';
}
function insert_details() {
	document.getElementById("hide_find").value="";
	document.getElementById("hide_insert").value="insert_mode";
	//document.getElementById("product_lookup").style.display='none';
	document.transfers_form.submit();
}
function find_product() {
	document.getElementById("hide_find").value="find_mode";
	document.getElementById("hide_insert").value="";
	document.transfers_form.submit();
	document.transfers_form.prod_no_desc.focus();
}
function find_mode_load() {
	var hide_find = document.transfers_form.hide_find.value;
	if (hide_find =="find_mode")  {
		//document.getElementById("product_lookup").style.display='';
		document.transfers_form.prod_no_desc.focus();
	} 
}
function option_button_click(id_ko) {
	var option_button = document.getElementById(id_ko).value;
	document.transfers_form.hide_button.value = option_button;
	document.transfers_form.submit();
}
function val_location() {
	var from_location = document.transfers_form.from_location.value;
	var to_location = document.transfers_form.to_location.value;
	if (from_location>"" && to_location>"") {
		if (from_location==to_location) {
			alert('From location should not be equal to To Location.');
			document.transfers_form.from_location.value="";
			document.transfers_form.to_location.value="";
			return false;
		}
	}
}
function val_hash() {
	var numericExpression = /^(\d+\.\d{0,0}|\d+)$/;
	var hash_items = document.transfers_form.hash_items.value;
	var hash_quantity = document.transfers_form.hash_quantity.value;
	if (parseInt(hash_items)>0 && parseInt(hash_quantity>0)) {
		if (parseInt(hash_quantity)<parseInt(hash_items)) {
			alert('Hash quantity should be greater than or equal to Hash Items.');
			document.transfers_form.hash_quantity.value=0;
			document.transfers_form.hash_items.value=0;
			return false;
		}
	}
	
	if (hash_items>"") {
		if(!hash_items.match(numericExpression)) {
			alert("Require Numeric only ");
			document.transfers_form.hash_items.value=0;
			return false;
		}
	}
	
	if (hash_quantity>"") {
		if(!hash_quantity.match(numericExpression)) {
			alert("Require Numeric only ");
			document.transfers_form.hash_quantity.value=0;
			return false;
		}
	}
}
function val_qty_out2(id_ko) {
	var numericExpression = /^(\d+\.\d{0,2}|\d+)$/;
	var alphaExpression = /^[a-zA-Z]+$/;
	var quantity_out22 = document.getElementById(id_ko).value
	var hide_num = document.transfers_form.hide_num.value;
	
	var id_ko2 = id_ko;
	var split_i = id_ko2.split(/z/);
	var fractional_tag_id = "fractional_tagz" + split_i[1];
	var check_detail_id = "check_detailz" + split_i[1];
	var fractional_tag = document.getElementById(fractional_tag_id).value
	
	if(quantity_out22==""||quantity_out22==0) {
		document.getElementById(id_ko).value = 0;
		alert("Product Quantity: Required");
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		document.getElementById(check_detail_id).src = "b_drop.png";
		return false;
	}
	if(!quantity_out22.match(numericExpression)) {
		alert("Require Numeric only \r\n       or \r\ndecimal places should not be greater than two(2)");
		document.getElementById(id_ko).value = 0;
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		document.getElementById(check_detail_id).src = "b_drop.png";
		return false;
	}
	
	var split_decimal = quantity_out22.split(/\./);
	if(split_decimal[1]>"" && fractional_tag=="N") {
		document.getElementById(id_ko).value = 0;
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		alert("Product Quantity: Fractional tag is not accepting fractional value");
		document.getElementById(check_detail_id).src = "b_drop.png";
		return false;
	}
	
	if (quantity_out22>0) {
		document.getElementById(check_detail_id).src = "check.gif";
		if (document.transfers_form.diffirence_items>0 || document.transfers_form.diffirence_items.value<0) {
			document.transfers_form.control_totals.value = "Entered Totals Don\'t Tally With Control Totals... Please Check.";
		} else {
			document.transfers_form.control_totals.value = "Data Entered are complete and accurate.";
		}
	} else {
		document.getElementById(check_detail_id).src = "b_drop.png";
	}	
}

function val_qty_out(id_ko) {
	var error=0;
	var numericExpression = /^(\d+\.\d{0,2}|\d+)$/;
	var alphaExpression = /^[a-zA-Z]+$/;
	var hide_company = document.transfers_form.hide_company.value
	var hide_transfer_no = document.transfers_form.hide_transfer_no.value
	var quantity_out = document.getElementById(id_ko).value
	var hide_num = document.transfers_form.hide_num_details.value;
	
	var id_ko2 = id_ko;
	var split_i = id_ko2.split(/t/);
	var fractional_tag_id = "fractional_tag" + split_i[3];
	var fractional_tag = document.getElementById(fractional_tag_id).value
	var upc_id = "prod_no" + split_i[3];
	var upc_no = document.getElementById(upc_id).value
	var split_decimal = quantity_out.split(/\./);
	
	if(quantity_out==""||quantity_out==0) {
		error++;
	}	
	if(!quantity_out.match(numericExpression)) {
		error++;
	}
	if(split_decimal[1]>"" && fractional_tag=="N") {
		error++;
	}
	if(split_decimal[1]>"" && fractional_tag=="N") {
		error++;
	}
	
	if (error>0) {
		new Ajax.Request(
		  'transfer_details_ajax.php?do=getQtyOut&trans_no='+hide_transfer_no+'&comp_code='+hide_company+'&upc_code='+upc_no+'&qty_id='+id_ko,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	if(quantity_out==""||quantity_out==0) {
		alert("Product Quantity Out: Required");
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		return false;
	}
	if(!quantity_out.match(numericExpression)) {
		alert("Require Numeric only \r\n       or \r\ndecimal places should not be greater than two(2)");
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		return false;
	}
	if(split_decimal[1]>"" && fractional_tag=="N") {
		error++;
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		alert("Product Quantity Out: Fractional tag is not accepting fractional value");
		return false;
	}	
}
function val_qty_in(id_ko) {
	var error=0;
	var numericExpression = /^(\d+\.\d{0,2}|\d+)$/;
	var alphaExpression = /^[a-zA-Z]+$/;
	var hide_company = document.transfers_form.hide_company.value
	var hide_transfer_no = document.transfers_form.hide_transfer_no.value
	var quantity_in = document.getElementById(id_ko).value
	var hide_num = document.transfers_form.hide_num_details.value;
	
	var id_ko2 = id_ko;
	var split_i = id_ko2.split(/n/);
	var fractional_tag_id = "fractional_tag" + split_i[2];
	var fractional_tag = document.getElementById(fractional_tag_id).value
	var upc_id = "prod_no" + split_i[2];
	var upc_no = document.getElementById(upc_id).value
	var split_decimal = quantity_in.split(/\./);
	
	if(quantity_in=="") {
		error++;
	}	
	if(!quantity_in.match(numericExpression)) {
		error++;
	}
	if(split_decimal[1]>"" && fractional_tag=="N") {
		error++;
	}
	if(split_decimal[1]>"" && fractional_tag=="N") {
		error++;
	}
	
	if (error>0) {
		new Ajax.Request(
		  'transfer_details_ajax.php?do=getQtyIn&trans_no='+hide_transfer_no+'&comp_code='+hide_company+'&upc_code='+upc_no+'&qty_id='+id_ko,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	if(quantity_in=="") {
		alert("Product Quantity In: Required");
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		return false;
	}
	if(!quantity_in.match(numericExpression)) {
		alert("Require Numeric only \r\n       or \r\ndecimal places should not be greater than two(2)");
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		return false;
	}
	if(split_decimal[1]>"" && fractional_tag=="N") {
		error++;
		document.getElementById(id_ko).focus();
		document.getElementById(id_ko).select();
		alert("Product Quantity In: Fractional tag is not accepting fractional value");
		return false;
	}	
}



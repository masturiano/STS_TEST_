//Description: Javascript Functions
//Author: Jhae Torres
//Date Created: March 08, 2008


/*-----------------------------------*/
/*----- Get Date/Time Functions -----*/
/*-----------------------------------*/
function checkTime(i){
	if (i<10) {i="0" + i}
	return i
}
function getDateTime(){
	var today=new Date()
	var y=today.getFullYear()
	var mon=today.getMonth() + 1;
	var d=today.getDate()
	var h=today.getHours()
	var m=today.getMinutes()
	var s=today.getSeconds()
	
	//add leading zero before numbers<10
	mon=checkTime(mon)
	d=checkTime(d)
	h=checkTime(h)
	m=checkTime(m)
	s=checkTime(s)
	if(h < 12){
		var meridiem = 'AM';
	} else{
		var meridiem = 'PM';
	}
	
	//document.getElementById('time').value=mon+"-"+d+"-"+y+"  "+h+":"+m+":"+s+" "+meridiem
	document.getElementById('time').value=h+":"+m+":"+s+" "+meridiem
	t=setTimeout('getDateTime()',1000)
}

/*-------------------------------*/
/*----- Main Menu Functions -----*/
/*-------------------------------*/
function recent(menu){
	document.getElementById('recent_obj').value = menu;
}
function active(menu){
	document.getElementById('active_obj').value = menu;
}

//PARENT MENU FUNCTIONS
function highlightOn(obj, menu_id){
	obj.style.backgroundColor = '#A9C4AB';
	document.getElementById(menu_id+'_').style.display = 'block';
}
function highlightOff(obj, menu_id){
	var recent_val = document.getElementById('recent_obj').value;
	
	obj.style.backgroundColor = 'transparent';
	if(recent_val != document.getElementById('active_obj').value) document.getElementById(menu_id+'_').style.display='none';
	if(recent_val!='') document.getElementById(recent_val+'_').style.display = 'none';
}

//SUB-MENU FUNCTIONS
function showSubMenu(menu, submenu_id){
	document.getElementById(menu).style.backgroundColor = '#A9C4AB';
	document.getElementById(submenu_id).style.display = 'block';
}
function hideSubMenu(menu, submenu_id){
	if(document.getElementById('active_obj').value != menu) document.getElementById(submenu_id).style.display='none';
	document.getElementById(menu).style.backgroundColor = 'transparent';
}

/*---------------------*/
/*----- Set Focus -----*/
/*---------------------*/
function setFocus(object){
	document.getElementById(object).focus()
}

/*------------------------------*/
/*----- Check Login Inputs -----*/
/*------------------------------*/
function checkInput(){
	var username = document.getElementById('username').value;
	var password = document.getElementById('password').value;
	
	if(username=='' || password==''){
		document.getElementById('transaction').value='no_input';
		alert('Input username and password!');
	} else{
		if(!username.match(/^[a-zA-Z0-9]{0,20}$/)){
			document.getElementById('transaction').value='invalid_login';
			alert('Invalid username!');
		} else if(!password.match(/^[a-zA-Z0-9\`~!@#$%^&*-_=+;:,.?]{0,20}$/)){
			document.getElementById('transaction').value='invalid_login';
			alert('Invalid password!');
		} else{
			document.getElementById('transaction').value='check_login';
			//document.login_form.submit()
		}
	}
}

/*--------------------------------*/
/*----- Assign Frame Content -----*/
/*--------------------------------*/
function assignFrameContent(file){
	document.getElementById('frame_content').src = file;
}

/*---------------------------------*/
/*----- Modify Header Content -----*/
/*---------------------------------*/
function modifyHeaderContent(pg_id, category){
	document.getElementById('pg_id').value = pg_id;
	document.getElementById('category').value = category;
}

/*-------------------------*/
/*----- Show/Hide Div -----*/
/*-------------------------*/
function showHideDiv(div_id){
	if(document.getElementById(div_id).style.display=='block'){
		document.getElementById(div_id).style.display='none';
	} else{
		document.getElementById(div_id).style.display='block';
	}
}

/*----------------------------------*/
/*----- Mouse Over/Out Effects -----*/
/*----------------------------------*/
function over(obj){
	obj.style.backgroundColor = '#F0F0F0';
}
function out(obj){
	obj.style.backgroundColor = '#FFFFFF';
}


/*------------------------*/
/*----- Assign Value -----*/
/*------------------------*/
function assignValue(obj, assigned_value, extra){
	document.getElementById(obj).value = assigned_value;
}

/*--------------------------*/
/*	Edit PO Detail Quantity	*/
/*--------------------------*/
function editPODetailQuantity(product_code){
	if(document.getElementById('ordered_qty'+product_code).readOnly == true){
		document.getElementById('ordered_qty'+product_code).readOnly = false;
		document.getElementById('ordered_qty'+product_code).focus();
		document.getElementById('ordered_qty'+product_code).select();
	} else{
		document.getElementById('ordered_qty'+product_code).readOnly = true;
	}
}

/*----------------------*/
/*	Edit PO Allowance	*/
/*----------------------*/
function editPOAllowance(code){
	if(document.getElementById('allowance_percent'+code).readOnly == true){
		document.getElementById('allowance_percent'+code).readOnly = false;
		document.getElementById('allowance_amount'+code).readOnly = false;
		document.getElementById('allowance_percent'+code).focus();
		document.getElementById('allowance_percent'+code).select();
	} else{
		document.getElementById('allowance_percent'+code).readOnly = true;
		document.getElementById('allowance_amount'+code).readOnly = true;
	}
}

/*----------------------------------*/
/*	Edit PO Miscellaneous Charges	*/
/*----------------------------------*/
function editPOMiscCharges(sequence){
	if(document.getElementById('misc_desc'+sequence).readOnly == true){
		document.getElementById('misc_desc'+sequence).readOnly = false;
		document.getElementById('misc_amt'+sequence).readOnly = false;
		document.getElementById('misc_desc'+sequence).focus();
		document.getElementById('misc_desc'+sequence).select();
	} else{
		document.getElementById('misc_desc'+sequence).readOnly = true;
		document.getElementById('misc_amt'+sequence).readOnly = true;
	}
}

/*------------------------------------*/
/*----- PO Allowances Validation -----*/
/*------------------------------------*/
function validateAllowanceEntry(){
	var type = document.getElementById('allowance_types').value;
	var percent = document.getElementById('allowance_percent').value;
	var amount = document.getElementById('allowance_amount').value;
	var proceed = true;
	
	if(proceed == true){
		if(type==0 || type==''){
			alert('Select ALLOWANCE TYPE!');
			document.getElementById('allowance_types').focus();
			proceed = false;
		}
	}

	if(proceed == true){
		if(!percent.match(/^[0-9\.]{0,20}$/) || !amount.match(/^[0-9\.]{0,20}$/)){
			alert('ALLOWANCE PERCENT AND ALLOWANCE AMOUNT must be a numeric value!');
			document.getElementById('allowance_percent').focus();
			document.getElementById('allowance_percent').select();
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(percent==0 && amount==0){
			alert('ALLOWANCE PERCENT AND ALLOWANCE AMOUNT can not have both zero(0) values at the same time!');
			document.getElementById('allowance_percent').focus();
			document.getElementById('allowance_percent').select();
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(percent!=0 && amount!=0){
			alert('ALLOWANCE PERCENT AND ALLOWANCE AMOUNT can not have both non-zero values at the same time!');
			document.getElementById('allowance_percent').focus();
			document.getElementById('allowance_percent').select();
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(percent>100 && amount==0){
			alert('ALLOWANCE PERCENT must not be more than 100!');
			document.getElementById('allowance_percent').focus();
			document.getElementById('allowance_percent').select();
			proceed = false;
		}
	}
	
	if(proceed == true){
		document.po_entry_form.submit();
	}
}

/*--------------------------------------------*/
/*----- Miscellaneous Charges Validation -----*/
/*--------------------------------------------*/
function validateMiscEntry(){
	var misc_desc = document.getElementById('misc_desc').value;
	var misc_amount = document.getElementById('misc_amount').value;
	var proceed = true;

	if(proceed == true){
		if(misc_desc == ''){
			alert('Key-in the CHARGE DESCRIPTION!');
			document.getElementById('misc_desc').focus();
			proceed = false;
		}
	}

	if(proceed == true){
		if(misc_amount == ''){
			alert('Key-in the AMOUNT!');
			document.getElementById('misc_amount').focus();
			proceed = false;
		} else{
			proceed = validateNumericInput(document.po_entry_form.misc_amount);
		}
	}
		
	if(proceed == true){
		document.po_entry_form.submit();
	}
}

/*------------------------------*/
/*----- Assign Transaction -----*/
/*------------------------------*/
function assignTransaction(trans){
	document.getElementById('transaction').value = trans;
}

/*---------------------------*/
/*----- Date Validation -----*/
/*---------------------------*/
function validateDate(obj){
	if(obj.value.length == obj.maxLength){
		//if(!obj.value.match(/^([0-9]{2,2}[-\w][0-9]{2,2}[-\w][0-9]{4,4})$/)){
		if(!obj.value.match(/^([0-9]{2,2}[-]{1,1}[0-9]{2,2}[-]{1,1}[0-9]{4,4})$/)){
			alert('Invalid Date!');
			obj.focus();
			obj.select();
			return false;
		}
		return true;
	}
}

/*------------------------------------*/
/*----- Numeric Input Validation -----*/
/*------------------------------------*/
function validateNumericInput(obj){
	if(!obj.value.match(/^[0-9\.]{0,100}$/)){
		alert('This field requires a numeric value!');
		obj.focus();
		obj.select();
		return false;
	}
	return true;
}

/*-------------------------------------*/
/*----- Quantity Input Validation -----*/
/*-------------------------------------*/
function validateQuantity(){
	var fraction_tag = document.getElementById('fraction_allowed').value;
	var quantity = document.getElementById('quantity').value;

	if(fraction_tag=='Y' && !quantity.match(/^[0-9\.]{0,100}$/)){
		alert('Invalid Quantity!');
		document.getElementById('quantity').focus();
		document.getElementById('quantity').select();
		return false;
	} else if(fraction_tag=='N'){
		if(!quantity.match(/^[0-9\.]{0,100}$/)){
			alert('Invalid Quantity!');
			document.getElementById('quantity').focus();
			document.getElementById('quantity').select();
			return false;
		} else if(!quantity.match(/^[0-9]{0,100}$/)){
			alert('Fractional Quantity is not allowed for this product!');
			document.getElementById('quantity').focus();
			document.getElementById('quantity').select();
			return false;
		}
	}
	return true;
}

/*---------------------------------*/
/*----- PO Header Validations -----*/
/*---------------------------------*/
function validateHeader(){
	var suppliers_list = document.getElementById('suppliers_list').value;
	var supplier_desc = document.getElementById('supplier_desc').value;
	var document_date = document.getElementById('document_date').value;
	var expected_delivery = document.getElementById('expected_delivery').value;
	var cancel_date = document.getElementById('cancel_date').value;
	var buyer = document.getElementById('buyer').value;
	var status = document.getElementById('status').value;
	var hash_items = document.getElementById('hash_items').value;
	var hash_quantity = document.getElementById('hash_quantity').value;
	var proceed = true;
	
	if(proceed == true){
		if(suppliers_list==0 || suppliers_list=='' || supplier_desc==''){
			alert('Select VENDOR NUMBER!');
			document.getElementById('suppliers_list').focus();
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(document_date == ''){
			alert('Key-in the DOCUMENT DATE!');
			document.getElementById('document_date').focus();
			document.getElementById('document_date').select();
			proceed = false;
		} else{
			proceed = validateDate(document.po_entry_form.document_date);
		}
	}
	
	if(proceed == true){
		if(expected_delivery==''){
			alert('Key-in the EXPECTED DATE OF DELIVERY!');
			document.getElementById('expected_delivery').focus();
			document.getElementById('expected_delivery').select();
			proceed = false;
		} else{
			proceed = validateDate(document.po_entry_form.expected_delivery);
		}
	}

	if(proceed == true){
		if(cancel_date==''){
			alert('Key-in the CANCEL DATE!');
			document.getElementById('cancel_date').focus();
			document.getElementById('cancel_date').select();
			proceed = false;
		} else{
			proceed = validateDate(document.po_entry_form.cancel_date);
		}
	}

	if(proceed == true){
		var date_today = document.getElementById('date_today').value;
		var document_date = document.getElementById('document_date').value;
		var document_date_ok = validateDate(document.po_entry_form.document_date);
		var expected_delivery = document.getElementById('expected_delivery').value;
		var expected_delivery_ok = validateDate(document.po_entry_form.expected_delivery);
		var cancel_date = document.getElementById('cancel_date').value;
		var cancel_date_ok = validateDate(document.po_entry_form.cancel_date);
		
		//assuming that the delimiter for time picker is a '-'.
		var arr_today = date_today.split('-');
		var arr_doc = document_date.split('-');
		var arr_exp = expected_delivery.split('-');
		var arr_cancel = cancel_date.split('-');
		
		var today = new Date();
		today.setFullYear(arr_today[2], arr_today[0], arr_today[1]);
		var doc_date = new Date();
		doc_date.setFullYear(arr_doc[2], arr_doc[0], arr_doc[1]);
		var exp_date = new Date();
		exp_date.setFullYear(arr_exp[2], arr_exp[0], arr_exp[1]);
		var cancel_date = new Date();
		cancel_date.setFullYear(arr_cancel[2], arr_cancel[0], arr_cancel[1]);

		var date_diff = (exp_date.valueOf() - doc_date.valueOf()) / (60 * 60 * 24 * 1000)
		var after_today = (exp_date.valueOf() - today.valueOf()) / (60 * 60 * 24 * 1000)
		var date_diff_1 = (cancel_date.valueOf() - doc_date.valueOf()) / (60 * 60 * 24 * 1000)
		var after_today_1 = (cancel_date.valueOf() - today.valueOf()) / (60 * 60 * 24 * 1000)
	
		if(document_date_ok==true && expected_delivery_ok==true && cancel_date_ok==true){
			if(date_diff < 0){
				alert('Expected Date of Delivery should be ON/AFTER the Document Date!');
				document.getElementById('expected_delivery').focus();
				document.getElementById('expected_delivery').select();
				proceed = false;
			} else if(after_today < 0){
				alert('Expected Date of Delivery should be ON/AFTER the Current Date!');
				document.getElementById('expected_delivery').focus();
				document.getElementById('expected_delivery').select();
				proceed = false;
			} else if(date_diff_1 < 0){
				alert('PO Cancel Date should be ON/AFTER the Document Date!');
				document.getElementById('cancel_date').focus();
				document.getElementById('cancel_date').select();
				proceed = false;
			} else if(after_today_1 < 0){
				alert('PO Cancel Date should be ON/AFTER the Current Date!');
				document.getElementById('cancel_date').focus();
				document.getElementById('cancel_date').select();
				proceed = false;
			}
		} else{
			proceed = false;
		}
	}

	if(proceed == true){
		if(status==0 || status==''){
			alert('Select STATUS!');
			document.getElementById('status').focus();
			proceed = false;
		}
	}

	if(proceed == true){
		if(buyer==0 || buyer==''){
			alert('Select BUYER!');
			document.getElementById('buyer').focus();
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(hash_items==0 || hash_items==''){
			alert('Key-in the HASH-ITEMS!');
			document.getElementById('hash_items').focus();
			document.getElementById('hash_items').select();
			proceed = false;
		} else{
			proceed = validateNumericInput(document.po_entry_form.hash_items);
		}
	}
	
	if(proceed == true){
		if(hash_quantity==0 || hash_quantity==''){
			alert('Key-in the HASH-QUANTITY!');
			document.getElementById('hash_quantity').focus();
			document.getElementById('hash_quantity').select();
			proceed = false;
		} else{
			proceed = validateNumericInput(document.po_entry_form.hash_quantity);
		}
	}
	
	return proceed;
}

/*--------------------------------*/
/*----- PO Inputs Validation -----*/
/*--------------------------------*/
function validatePOEntry(){
	var product = document.getElementById('product').value;
	var quantity = document.getElementById('quantity').value;
	var header_ok = validateHeader();
	var proceed = true;
	
	if(header_ok == true){
		if(proceed == true){
			if(product=='' || product==0){
				alert('Select PRODUCT CODE!');
				document.getElementById('products_list').focus();
				proceed = false;
			}
		}
		
		if(proceed == true){
			if(quantity=='' || quantity==0){
				alert('Key-in the QUANTITY!');
				document.getElementById('quantity').focus();
				proceed = false;
			} else{
				proceed = validateQuantity();
			}
		}
		
		if(proceed == true){
			document.po_entry_form.submit();
		}
	}
}

/*---------------------------------------*/
/*----- check/update Control Totals -----*/
/*---------------------------------------*/
function checkUpdateControlTotals(){
	var hash_items = document.getElementById('hash_items').value;
	var entered_items = document.getElementById('entered_items').value;
	var diff_items = hash_items - entered_items;
	var hash_quantity = document.getElementById('hash_quantity').value;
	var entered_quantity = document.getElementById('entered_quantity').value;
	var diff_quantity = hash_quantity - entered_quantity;
	var proceed = true;

	if(diff_items != 0){
		alert('Check Items Difference!');
		document.getElementById('hash_items').focus();
		proceed = false;
	} else if(diff_quantity != 0){
		alert('Check Quantity Difference!');
		document.getElementById('hash_quantity').focus();
		proceed = false;
	}

	if(proceed == true){
		document.po_entry_form.submit();
	}
}

/*---------------------------------*/
/*----- Save Button Procedure -----*/
/*---------------------------------*/
function saveProcedure(){
	var page = document.getElementById('page').value;
	var hash_items = document.getElementById('hash_items').value;
	var entered_items = document.getElementById('entered_items').value;
	var diff_items = hash_items - entered_items;
	var hash_quantity = document.getElementById('hash_quantity').value;
	var entered_quantity = document.getElementById('entered_quantity').value;
	var diff_quantity = hash_quantity - entered_quantity;
	var header_ok = validateHeader();
	var proceed = true;
	
	if(header_ok == true){
		if(proceed == true){
			if(entered_items==0 || entered_quantity==0){
				alert('PO Detail is required!');
				document.getElementById('products_list').focus();
				proceed = false;
			}
		}
		
		if(proceed == true){
			if(diff_items!=0 || diff_quantity!=0){
				alert('Check Control Totals!');
				assignTransaction('save');
				assignValue('transaction_override', 'return_po_entry', '');
				document.po_entry_form.submit();
				proceed = false;
			}
		}

		if(proceed == true){
			assignTransaction('save');
			if(page=='po_entry1' || page=='po_edit1'){
				//CREATE PO ALLOWANCE
				var create_allowance = confirm("Do you want to create PO Allowance?\n\nOK = YES, Create allowance.\nCancel = NO, Don't create allowance."); 
				if(create_allowance){
					document.getElementById('create_po_allowance').value = 'yes';
				} else{
					document.getElementById('create_po_allowance').value = 'no';
				}
				
				//CREATE MISCELLANEOUS CHARGES
				var create_charges = confirm("Do you want to create Miscellaneous Charges?\n\nOK = YES, Create charges.\nCancel = NO, Don't create charges.");
				if(create_charges){
					document.getElementById('create_misc_charges').value = 'yes';
				} else{
					document.getElementById('create_misc_charges').value = 'no';
				}
				
				if(create_allowance==false && create_charges==false){
					var save_po = confirm("Do you want to save this PO Document?\n\nOK = YES, SAVE and EXIT this PO Document.\nCancel = NO, GO BACK to PO Document.");
					if(save_po){
						//SAVE PO then increase PO Number(po entry page 1)
						assignValue('transaction_override', 'end_po_entry', '');
						document.po_entry_form.submit();
					}
				} else{
					//Proceed to Page 2
					assignValue('transaction_override', 'proceed_to_page2', '');
					document.po_entry_form.submit();
				}
			} else if(page=='po_entry2' || page=='po_edit2'){
				var save_po = confirm("Do you want to save this PO Document?\n\nOK = YES, SAVE and EXIT this PO Document.\nCancel = NO, GO BACK to PO Document.");
				if(save_po){
					assignValue('transaction_override', 'end_po_entry', '');
					document.po_entry_form.submit();
				}
			}
		}
	}
}

/*----------------------------*/
/*----- Confirm Deletion -----*/
/*----------------------------*/
function confirmDelete(){	
	var proceed = confirm("Are you sure you want to delete the selected Records?\n\nOK = YES\nCancel = NO");
	if(proceed){
		document.po_entry_form.submit();
	}
}

/*-------------------------------------------------------*/
/*----- Confirm PO Document deletion (current page) -----*/
/*-------------------------------------------------------*/
function confirmDeleteThisPODocument(){	
	var proceed = confirm("Are you sure you want to delete this PO Document?\n\nOK = YES\nCancel = NO");
	if(proceed){
		document.po_entry_form.submit();
	}
}

/*------------------------------------------------*/
/*----- Confirm Save before Exit PO Document -----*/
/*------------------------------------------------*/
function confirmExitAction(from, to){
	if(from != ''){
		if(from=='po_entry1' || from=='po_entry2'){
			var proceed = confirm("Do you want to save this PO Document?\n\nOK = YES, SAVE this PO Document.\nCancel = NO, GO BACK to PO Document.");
		} else if(from=='po_edit1' || from=='po_edit2'){
			var proceed = confirm("Do you want to save the changes in this PO Document?\n\nOK = YES, SAVE the changes.\nCancel = NO, GO BACK to PO Document.");
		}
		
		assignTransaction('save');
		if(proceed){
			var page = document.getElementById('page').value;
			var hash_items = document.getElementById('hash_items').value;
			var entered_items = document.getElementById('entered_items').value;
			var diff_items = hash_items - entered_items;
			var hash_quantity = document.getElementById('hash_quantity').value;
			var entered_quantity = document.getElementById('entered_quantity').value;
			var diff_quantity = hash_quantity - entered_quantity;
			var header_ok = validateHeader();
			var proceed = true;
			
			if(header_ok == true){
				if(proceed == true){
					if(entered_items==0 || entered_quantity==0){
						alert('PO Detail is required!');
						document.getElementById('products_list').focus();
						proceed = false;
					}
				}
				
				if(proceed == true){
					if(diff_items!=0 || diff_quantity!=0){
						alert('Check Control Totals!');
						assignTransaction('save');
						assignValue('transaction_override', 'return_po_entry', '');
						document.po_entry_form.submit();
						proceed = false;
					}
				}
				
				if(proceed==true){
					if(to=='po_entry1'){
						if(diff_items!=0 || diff_quantity!=0){
							assignValue('transaction_override', 'return_po_entry', '');
						} else{
							assignValue('transaction_override', 'end_po_entry', '');
						}
					} else if(to=='po_search'){
						if(diff_items!=0 || diff_quantity!=0){
							assignValue('transaction_override', 'return_po_entry', '');
						} else{
							assignValue('transaction_override', 'go_to_po_search', '');
						}
					}
					document.po_entry_form.submit();
				}
			}
		} else{
			assignValue('transaction_override', 'return_po_entry', '');
			document.po_entry_form.submit();
		}
	} else{
		document.po_entry_form.submit();
	}
}

/*-------------------------------------------------*/
/*----- Product Code Discounts' Pop-up Window -----*/
/*-------------------------------------------------*/
function popupWindow(sys_host, sys_dir, page){
	window.open('http://'+sys_host+'/'+sys_dir+'/'+page,
		'popup',
		'width=400, height=300, scrollbars=yes, resizable=no, toolbar=no, directories=no, location=no, menubar=no, status=no, left=400, top=300');
}

/*------------------------------*/
/*----- Confirm Release PO -----*/
/*------------------------------*/
function confirmReleasePO(){
	var release_po = confirm("Are you sure you want to release the selected PO(s)?");
	if(release_po){
		document.po_release_form.submit();
	}
}

/*------------------------------*/
/*----- Confirm Re-open PO -----*/
/*------------------------------*/
function confirmReopenPO(po_number){
	var reopen_po = confirm("Are you sure you want to re-open PO# "+po_number+"?");
	if(reopen_po){
		document.reopen_po_form.submit();
	}
}

/*-------------------------------*/
/*----- Confirm Release RCR -----*/
/*-------------------------------*/
function confirmReleaseRCR(){
	var release_rcr = confirm("Are you sure you want to release the selected RCR(s)?");
	if(release_rcr){
		document.rcr_release_form.submit();
	}
}

/*----------------------------------*/
/*----- RA Report Page Default -----*/
/*----------------------------------*/
function setRAReportDefaults(active_div){
	if(active_div.value=='print' || active_div==''){
		document.getElementById('reprint_div').style.display = 'none';
		document.getElementById('print_div').style.display = 'block';
		document.getElementById('po_number').value='';
		document.getElementById('po_number').focus();
	} else if(active_div.value=='reprint'){
		document.getElementById('print_div').style.display = 'none';
		document.getElementById('reprint_div').style.display = 'block';
		document.getElementById('reprint_ra_number').value='';
		document.getElementById('reprint_ra_number').focus();
	}
}

/*------------------------------------*/
/*----- Validate RA Report Entry -----*/
/*------------------------------------*/
function validateRAReportEntry(input_field){
	var valid = validateNumericInput(document.getElementById(input_field));
	if(valid==true){
		document.ra_report_form.submit();
	}
}

/*-------------------------------------------------------*/
/*----- Validate PO Number (for Additional Charges) -----*/
/*-------------------------------------------------------*/
function validatePONumberForAddCharges(po_number_obj){
	var po_ok = validateNumericInput(po_number_obj);
	if(po_ok==true){
		document.po_add_charges_form.submit();
	}
}

/*------------------------------------------------*/
/*----- Validate PO Additional Charges Entry -----*/
/*------------------------------------------------*/
function validatePOAddChargesEntry(){
	var orig_po_number = document.getElementById('orig_po_number').value;
	var po_number = document.getElementById('po_number').value;
	var remarks = document.getElementById('remarks').value;
	var percent = document.getElementById('add_charge_percent').value;
	var amount = document.getElementById('add_charge_amount').value;
	var proceed = true;

	if(proceed == true){
		if(orig_po_number!=po_number){
			alert('PO NUMBER has been changed...\nOriginal value will be restored.');
			document.getElementById('po_number').value = document.getElementById('orig_po_number').value;
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(!percent.match(/^[0-9\.]{0,20}$/) || !amount.match(/^[0-9\.]{0,20}$/)){
			alert('PO ADDITIONAL CHARGE PERCENT/AMOUNT must be a numeric value!');
			document.getElementById('add_charge_percent').focus();
			document.getElementById('add_charge_percent').select();
			proceed = false;
		}
	}

	if(proceed == true){
		if(percent==0 && amount==0){
			alert('PO ADDITIONAL CHARGE PERCENT AND AMOUNT can not have both zero(0) values at the same time!');
			document.getElementById('add_charge_percent').focus();
			document.getElementById('add_charge_percent').select();
			proceed = false;
		}
	}
	
	if(proceed == true){
		if(percent!=0 && amount!=0){
			alert('PO ADDITIONAL CHARGE PERCENT AND AMOUNT can not have both non-zero values at the same time!');
			document.getElementById('add_charge_percent').focus();
			document.getElementById('add_charge_percent').select();
			proceed = false;
		}
	}

	if(proceed == true){
		if(percent>100 && amount==0){
			alert('PO ADDITIONAL CHARGE PERCENT must not be more than 100!');
			document.getElementById('add_charge_percent').focus();
			document.getElementById('add_charge_percent').select();
			proceed = false;
		}
	}

	if(proceed == true){
		document.po_add_charges_form.submit();
	}
}

/*------------------------------------------------*/
/*----- Confirm Delete PO Additional Charges -----*/
/*------------------------------------------------*/
function confirmDeletePOAddCharges(){
	var delete_add_charges = confirm("Are you sure you want to delete the selected PO Additional Charge(s)?");
	if(delete_add_charges){
		document.po_add_charges_form.submit();
	}
}

/*----------------------------------------------------------*/
/*----- Options wether the RA Buttons are shown or not -----*/
/*----------------------------------------------------------*/
function showHideRAButtons(index, button){
	var good = document.getElementById('good'+index).value;
	var bad = document.getElementById('bad'+index).value;
	var free = document.getElementById('free'+index).value;
	
	total_qty = parseInt(good) + parseInt(bad) + parseInt(free);
	if(total_qty > 0){
		document.getElementById(button).disabled = false;
	} else{
		document.getElementById(button).disabled = true;
	}
}

/*-----------------------------------------------------------*/
/*----- RCR Header Validations (except DATE conditions) -----*/
/*-----------------------------------------------------------*/
function validateRCRHeader(form_obj){
	var location = document.getElementById('location').value;
	var carrier = document.getElementById('carrier').value;
	//var container = document.getElementById('container').value;
	var container = form_obj.value;
	var received_by = document.getElementById('received_by').value;
	var proceed = true;
	
	if(proceed==true){
		if(location==0){
			alert('Select LOCATION!');
			document.getElementById('location').focus();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(carrier==''){
			alert('Key-in the CARRIER!');
			document.getElementById('carrier').focus();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(container==''){
			alert('Key-in the CONTAINER NUMBER!');
			document.getElementById('container').focus();
			proceed = false;
		} else{
			proceed = validateNumericInput(form_obj);
		}
	}
	
	if(proceed==true){
		if(received_by==''){
			alert('Key-in the RECEIVER!');
			document.getElementById('received_by').focus();
			proceed = false;
		}
	}
	
	return proceed;
}

/*--------------------------*/
/*----- RA Validations -----*/
/*--------------------------*/
function validateRA(){
	var date_today = document.getElementById('date_today').value;
	var rcr_date = document.getElementById('rcr_date').value;
	var proceed = true;
	
	if(proceed==true){
		if(rcr_date==''){
			document.getElementById('rcr_date').value = date_today;
		} else{
			var rcr_date_ok = validateDate(document.ra_entry_form.rcr_date);
			var ra_date = document.getElementById('ra_date').value;
			
			//assuming that the delimiter for time picker is a '-'.
			var arr_today = date_today.split('-');
			var arr_rcr = rcr_date.split('-');
			var arr_ra = ra_date.split('-');
			
			var today = new Date();
			today.setFullYear(arr_today[2], arr_today[0], arr_today[1]);
			var rcr_date = new Date();
			rcr_date.setFullYear(arr_rcr[2], arr_rcr[0], arr_rcr[1]);
			var ra_date = new Date();
			ra_date.setFullYear(arr_ra[2], arr_ra[0], arr_ra[1]);
			
			var before_ra = (rcr_date.valueOf() - ra_date.valueOf()) / (60 * 60 * 24 * 1000);
			var after_current = (today.valueOf() - rcr_date.valueOf()) / (60 * 60 * 24 * 1000);
			
			if(rcr_date_ok==true){
				if(before_ra < 0){
					alert('RCR Date should be on/after the RA Date!');
					document.getElementById('rcr_date').focus();
					document.getElementById('rcr_date').select();
					proceed = false;
				} else if(after_current < 0){
					alert('RCR Date should be on/before the Current Date!');
					document.getElementById('rcr_date').focus();
					document.getElementById('rcr_date').select();
					proceed = false;
				}
			} else{
				alert('Invalid RCR Date!');
				document.getElementById('rcr_date').value = date_today;
				document.getElementById('rcr_date').focus();
				document.getElementById('rcr_date').select();
				proceed = false;
			}
		}
	}
	
	proceed = validateRCRHeader(document.ra_entry_form.container);
	
	return proceed;
}

/*------------------------------------*/
/*----- Confirm Deletion of RA's -----*/
/*------------------------------------*/
function confirmDeleteRA(){	
	var proceed = confirm("Are you sure you want to delete the selected RA(s)?\n\nOK = YES\nCancel = NO");
	if(proceed){
		document.ra_entry_form.submit();
	}
}

/*-----------------------------------------*/
/*----- Confirm Deletion of PO Item's -----*/
/*-----------------------------------------*/
function confirmDeletePOItem(){	
	var proceed = confirm("Are you sure you want to delete the selected record(s)?\n\nOK = YES\nCancel = NO");
	if(proceed){
		document.add_po_item_form.submit();
	}
}

/*-----------------------------------------*/
/*----- Confirm Deletion of DSD's -----*/
/*-----------------------------------------*/
function confirmDeleteDSD(){	
	var proceed = confirm("Are you sure you want to delete the selected record(s)?\n\nOK = YES\nCancel = NO");
	if(proceed){
		document.dsd_entry_form.submit();
	}
}

/*-----------------------------------*/
/*----- Confirm Deletion of RCR -----*/
/*-----------------------------------*/
function confirmDeleteRCR(){	
	var proceed = confirm("Are you sure you want to delete the selected RCR(s)?\n\nOK = YES\nCancel = NO");
	if(proceed){
		document.rcr_update_form.submit();
	}
}

/*------------------------------------------------*/
/*----- Additional PO Item (RCR) Validations -----*/
/*------------------------------------------------*/
function validateAddPOItem(final_validation){
	var po_number = document.getElementById('po_number').value;
	var date_today = document.getElementById('date_today').value;
	var po_date = document.getElementById('po_date').value;
	var rcr_date = document.getElementById('rcr_date').value;
	var product = document.getElementById('product').value;
	var proceed = true;
	
	if(proceed==true){
		if(po_number==''){
			alert('Key-in the PO Number!');
			document.getElementById('po_number').focus();
			proceed=false;
		}
	}

	if(proceed==true){
		if(rcr_date==''){
			document.getElementById('rcr_date').value = date_today;
		} else{
			var rcr_date_ok = validateDate(document.add_po_item_form.rcr_date);
			
			//assuming that the delimiter for time picker is a '-'.
			var arr_today = date_today.split('-');
			var arr_po = po_date.split('-');
			var arr_rcr = rcr_date.split('-');
			
			var today = new Date();
			today.setFullYear(arr_today[2], arr_today[0], arr_today[1]);
			var po_date = new Date();
			po_date.setFullYear(arr_po[2], arr_po[0], arr_po[1]);
			var rcr_date = new Date();
			rcr_date.setFullYear(arr_rcr[2], arr_rcr[0], arr_rcr[1]);
			
			var before_po = (rcr_date.valueOf() - po_date.valueOf()) / (60 * 60 * 24 * 1000);
			var after_current = (today.valueOf() - rcr_date.valueOf()) / (60 * 60 * 24 * 1000);
			
			if(rcr_date_ok==true){
				if(before_po < 0){
					alert('RCR Date should be on/after the PO Date!');
					document.getElementById('rcr_date').focus();
					document.getElementById('rcr_date').select();
					proceed = false;
				} else if(after_current < 0){
					alert('RCR Date should be on/before the Current Date!');
					document.getElementById('rcr_date').focus();
					document.getElementById('rcr_date').select();
					proceed = false;
				}
			} else{
				alert('Invalid RCR Date!');
				document.getElementById('rcr_date').value = date_today;
				document.getElementById('rcr_date').focus();
				document.getElementById('rcr_date').select();
				proceed = false;
			}
		}
	}
	
	if(proceed==true){
		proceed = validateRCRHeader(document.add_po_item_form.container);
	}
	
	if(final_validation==false){
		if(proceed == true){
			if(product=='' || product==0){
				alert('Select PRODUCT CODE!');
				document.getElementById('product').focus();
				proceed = false;
			}
		}
		
		if(proceed==true){
			document.add_po_item_form.submit();
		}
	} else{
		return proceed;
	}
}

/*--------------------------------------------------------*/
/*----- FINAL - Additional PO Item (RCR) Validations -----*/
/*--------------------------------------------------------*/
function finalValidateAddPOItem(final_validation){
	var proceed = validateAddPOItem(final_validation);
	
	if(proceed==true){
		document.add_po_item_form.submit();
	}
}

/*---------------------------*/
/*----- DSD Validations -----*/
/*---------------------------*/
function validateDSD(final_validation){
	var invoice_number = document.getElementById('invoice_number').value;
	var invoice_date = document.getElementById('invoice_date').value;
	var supplier = document.getElementById('suppliers_list').value;
	var date_today = document.getElementById('date_today').value;
	var rcr_date = document.getElementById('rcr_date').value;
	//var product = document.getElementById('products_list').value;
	var product = document.getElementById('product_description').value;
	var proceed = true;
	
	if(proceed==true){
		if(invoice_number==''){
			alert('Key-in the Invoice Number!');
			document.getElementById('invoice_number').focus();
			proceed=false;
		} else{
			proceed = validateNumericInput(document.dsd_entry_form.invoice_number);
		}
	}
	
	if(proceed==true){
		if(invoice_date==''){
			document.getElementById('invoice_date').value = date_today;
		}
		
		if(rcr_date==''){
			document.getElementById('rcr_date').value = date_today;
		}
			
		if(invoice_date!='' && rcr_date!=''){
			var invoice_date_ok = validateDate(document.dsd_entry_form.invoice_date);
			var rcr_date_ok = validateDate(document.dsd_entry_form.rcr_date);
			
			if(invoice_date_ok!=true){
				alert('Invalid Invoice Date!');
				document.getElementById('invoice_date').value = date_today;
				document.getElementById('invoice_date').focus();
				document.getElementById('invoice_date').select();
				proceed = false;
			} else if(rcr_date_ok!=true){
				alert('Invalid RCR Date!');
				document.getElementById('rcr_date').value = date_today;
				document.getElementById('rcr_date').focus();
				document.getElementById('rcr_date').select();
				proceed = false;
			} else{
				//assuming that the delimiter for time picker is a '-'.
				var arr_today = date_today.split('-');
				var arr_invoice = invoice_date.split('-');
				var arr_rcr = rcr_date.split('-');
				
				var today = new Date();
				today.setFullYear(arr_today[2], arr_today[0], arr_today[1]);
				var invoice_date = new Date();
				invoice_date.setFullYear(arr_invoice[2], arr_invoice[0], arr_invoice[1]);
				var rcr_date = new Date();
				rcr_date.setFullYear(arr_rcr[2], arr_rcr[0], arr_rcr[1]);
				
				var invoice_after_current = (today.valueOf() - invoice_date.valueOf()) / (60 * 60 * 24 * 1000);
				var rcr_after_current = (today.valueOf() - rcr_date.valueOf()) / (60 * 60 * 24 * 1000);
				var before_invoice = (rcr_date.valueOf() - invoice_date.valueOf()) / (60 * 60 * 24 * 1000);

				if(invoice_after_current < 0){
					alert('Invoice Date should be on/before the Current Date!');
					document.getElementById('invoice_date').focus();
					document.getElementById('invoice_date').select();
					proceed = false;
				} else if(rcr_after_current < 0){
					alert('RCR Date should be on/before the Current Date!');
					document.getElementById('rcr_date').focus();
					document.getElementById('rcr_date').select();
					proceed = false;
				} else if(before_invoice < 0){
					alert('RCR Date should be on/after the Invoice Date!');
					document.getElementById('rcr_date').focus();
					document.getElementById('rcr_date').select();
					proceed = false;
				}
			}
		}
	}
	
	if(proceed==true){
		if(supplier=='' || supplier==0){
			alert('Select VENDOR CODE!');
			document.getElementById('suppliers_list').focus();
			proceed = false;
		}
	}

	if(proceed==true){
		proceed = validateRCRHeader(document.dsd_entry_form.container);
	}
	
	if(final_validation==false){
		if(proceed == true){
			//if(product=='' || product==0){
			if(product==''){
				alert('Select PRODUCT CODE!');
				document.getElementById('products_list').focus();
				proceed = false;
			}
		}
		
		if(proceed==true){
			document.dsd_entry_form.submit();
		}
	} else{
		return proceed;
	}
}

/*-----------------------------------*/
/*----- FINAL - DSD Validations -----*/
/*-----------------------------------*/
function finalValidateDSD(final_validation){
	var proceed = validateDSD(final_validation);
	
	if(proceed==true){
		document.dsd_entry_form.submit();
	}
}


/*----------------------------------------*/
/*----- Validate Makeup/Breakup Form -----*/
/*----------------------------------------*/
function validateMakeupBreakup(){
	var adj_no = document.getElementById('adj_no').value;
	var adj_type = document.getElementById('adj_type').value;
	var location = document.getElementById('location').value;
	var date_today = document.getElementById('date_today').value;
	var document_date = document.getElementById('document_date').value;
	var remarks = document.getElementById('remarks').value;
	var prod_code = document.getElementById('prod_code').value;
	var qty = document.getElementById('qty').value;
	var proceed = true;
	
	if(proceed==true){
		if(adj_no==''){
			alert('ADJUSTMENT NUMBER is required!');
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(adj_type=='' || adj_type==0){
			alert('Select ADJUSTMENT TYPE!');
			document.getElementById('adj_type').focus();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(location=='' || location==0){
			alert('Select LOCATION!');
			document.getElementById('location').focus();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(document_date==''){
			alert('Key-in the ADJUSTMENT DATE!');
			document.getElementById('document_date').focus();
			proceed = false;
		} else{
			proceed = validateDate(document.makeup_breakup_form.document_date);
			
			if(proceed==true){
				//assuming that the delimiter for time picker is a '-'.
				var arr_today = date_today.split('-');
				var arr_doc = document_date.split('-');
				
				var today = new Date();
				today.setFullYear(arr_today[2], arr_today[0], arr_today[1]);
				var doc_date = new Date();
				doc_date.setFullYear(arr_doc[2], arr_doc[0], arr_doc[1]);
				
				var after_current = (today.valueOf() - doc_date.valueOf()) / (60 * 60 * 24 * 1000);
				if(after_current < 0){
					alert('DOCUMENT DATE should be on/before the Current Date!');
					document.getElementById('document_date').focus();
					document.getElementById('document_date').select();
					proceed = false;
				} else{
					if(arr_doc[0]<1 || arr_doc[0]>12){
						alert('Invalid DOCUMENT MONTH!');
						document.getElementById('document_date').focus();
						document.getElementById('document_date').select();
						proceed = false;
					} else if((arr_doc[1]<1 || arr_doc[1]>31) || (((arr_doc[0]<8 && arr_doc[0]%2==0) || (arr_doc[0]>7 && arr_doc[0]%2==1)) && arr_doc[1]>30) || (arr_doc[0]==2 && arr_doc[1]>29)){
						alert('Invalid DOCUMENT DAY!');
						document.getElementById('document_date').focus();
						document.getElementById('document_date').select();
						proceed = false;
					} else if(arr_doc[2]<1998){
						alert('Invalid DOCUMENT YEAR!');
						document.getElementById('document_date').focus();
						document.getElementById('document_date').select();
						proceed = false;
					}
				}
			}
		}
	}
	
	if(proceed==true){
		if(prod_code==''){
			alert('Key-in the PRODUCT CODE!');
			proceed = false;							
		}
	}
	
	if(proceed==true){
		if(qty==''){
			alert('Key-in the QUANTITY TO MAKE/BREAK!');
			document.getElementById('qty').focus();
			proceed = false;
		} else{
			if(!qty.match(/^[0-9]{0,100}$/)){
				alert('QUANTITY TO MAKE/BREAK field requires a numeric value!');
				document.getElementById('qty').focus();
				document.getElementById('qty').select();
				proceed = false;
			} else if(qty<1){
				alert('QUANTITY TO MAKE/BREAK should be greater than zero(0)!');
				document.getElementById('qty').focus();
				document.getElementById('qty').select();
				proceed = false;
			}
		}
	}
	
	if(proceed==true){
		document.makeup_breakup_form.submit();
	}
}


/*--------------------------------------------------------------*/
/*----- Makeup/Breakup Adjustment Confirmation of Deletion -----*/
/*--------------------------------------------------------------*/
function confirmAdjDelete(){
	var proceed_del = confirm("Makeup/Breakup Adjustment Document will be Deleted...\nAre you sure? (Y=Ok/N=Cancel)");
	if(proceed_del){
		document.makeup_breakup_form.submit();
	}
}

/*-----------------------------------------------------*/
/*----- Confirm Release Makeup/Breakup Adjustment -----*/
/*-----------------------------------------------------*/
function confirmReleaseMkBkAdj(){
	var release_adj = confirm("Are you sure you want to release the selected Adjustment(s)?");
	if(release_adj){
		document.adj_release_form.submit();
	}
}

/*-----------------------------*/
/*----- Confirm Cancel PO -----*/
/*-----------------------------*/
function confirmCancelPO(){
	var cancel_po = confirm('Are you sure you want to cancel the selected PO(s)?');
	if(cancel_po){
		document.cancel_close_po_form.submit();
	}
}

/*----------------------------*/
/*----- Confirm Close PO -----*/
/*----------------------------*/
function confirmClosePO(){
	var close_po = confirm('Are you sure you want to close the selected PO(s)?');
	if(close_po){
		document.cancel_close_po_form.submit();
	}
}

/*--------------------------*/
/*----- Confirm Logout -----*/
/*--------------------------*/
function confirmLogout(){
	var logout = confirm('Are you sure you want to logout?');
	if(logout){
		document.home_form.submit();
	}
}

/*--------------------------------------*/
/*----- Validate "Add User" Inputs -----*/
/*--------------------------------------*/
function validateAddUser(){
	var first_name = document.getElementById('first_name').value;
	var middle_name = document.getElementById('middle_name').value;
	var last_name = document.getElementById('last_name').value;
	var count = document.getElementById('count').value;
	var company = document.getElementById('company').value;
	var privilege = document.getElementById('privilege').value;
	var proceed = true;
	
	if(proceed==true){
		if(first_name==''){
			alert('Key-in user\'s FIRST NAME!');
			document.getElementById('first_name').focus();
			proceed = false;
		} else{
			if(!first_name.match(/^[a-zA-Z\x20]{0,25}$/)){
				alert('Invalid FIRST NAME input!');
				document.getElementById('first_name').focus();
				document.getElementById('first_name').select();
				proceed = false;
			}
		}
	}
	
	if(proceed==true){
		if(!middle_name.match(/^[a-zA-Z\x20]{0,25}$/)){
			alert('Invalid MIDDLE NAME input!');
			document.getElementById('middle_name').focus();
			document.getElementById('middle_name').select();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(last_name==''){
			alert('Key-in user\'s LAST NAME!');
			document.getElementById('last_name').focus();
			proceed = false;
		} else{
			if(!last_name.match(/^[a-zA-Z\x20]{0,25}$/)){
				alert('Invalid LAST NAME input!');
				document.getElementById('last_name').focus();
				document.getElementById('last_name').select();
				proceed = false;
			}
		}
	}
	
	if(proceed==true){
		if(count!='' && !count.match(/^[0-9]{0,2}$/)){
			alert('Invalid COUNT input!');
			document.getElementById('count').focus();
			document.getElementById('count').select();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(company=='' || company==0){
			alert('Select COMPANY!');
			document.getElementById('company').focus();
			proceed = false;
		}
	}
	
	if(proceed==true){
		if(privilege=='' || privilege==0){
			alert('Select user PRIVILEGE!');
			document.getElementById('privilege').focus();
			proceed = false;
		}
	}
	
	if(proceed==true){
		document.add_user_form.submit();
	}
}

/*----------------------------------*/
/*----- Confirm Delete User(s) -----*/
/*----------------------------------*/
function confirmDeleteUser(){
	
}
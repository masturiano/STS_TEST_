// JavaScript Document
function get_class(id_ko) {
	var box_group=document.form_search.box_group.value;
	var box_dept=document.form_search.box_dept.value;
	var box_cls=document.form_search.box_cls.value;
	var box_subcls=document.form_search.box_subcls.value;
	var hide_rows=document.form_search.hide_rows.value;
	var by_search=document.form_search.by_search.value;
	var code_desc=document.form_search.code_desc.value;
	var prod_code1=document.form_search.prod_code1.value;
	var prod_code2=document.form_search.prod_code2.value;
	var from_date=document.form_search.from_date.value;
	var to_date=document.form_search.to_date.value;
	var from_location=document.form_search.from_location.value;
	
	if (from_location=="") {
		alert('Select Store Location.');
		return false;
	}
	if (by_search=="by_product") {
				new Ajax.Request(
				  'sales_inquiry_ajax.php?do='+id_ko+'&hide_rows='+hide_rows+'&by_search='+by_search+'&code_desc='+code_desc+'&prod_code1='+prod_code1+'&prod_code2='+prod_code2+'&from_date='+from_date+'&to_date='+to_date+'&from_location='+from_location,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
	} else {
				new Ajax.Request(
				  'sales_inquiry_ajax.php?do='+id_ko+'&group='+box_group+'&dept='+box_dept+'&cls='+box_cls+'&subcls='+box_subcls+'&hide_rows='+hide_rows+'&by_search='+by_search+'&from_date='+from_date+'&to_date='+to_date+'&from_location='+from_location,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
	}
	
}
function val_date() {
	var today_date = new Date();
 	var from_date=document.form_search.from_date.value
    var to_date=document.form_search.to_date.value
	var today_date_mm = Date.parse(today_date);
	var from_date_mm = Date.parse(from_date);
	var to_date_mm = Date.parse(to_date);
	if(from_date_mm > today_date_mm) {
		alert("From Date must not be greater than to Current Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	}
	if(to_date_mm > today_date_mm) {
		alert("To Date must not be greater than to Current Date.");
		document.form_search.to_date.value="";
		document.form_search.to_date.focus();
		return false;
	}
	if(from_date_mm > to_date_mm) {
		alert("From Date must not be greater than to To Date.");
		document.form_search.from_date.value="";
		document.form_search.from_date.focus();
		return false;
	} 
}
function print_pdf() {
	var box_group=document.form_search.box_group.value;
	var box_dept=document.form_search.box_dept.value;
	var box_cls=document.form_search.box_cls.value;
	var box_subcls=document.form_search.box_subcls.value;
	var hide_rows=document.form_search.hide_rows.value;
	var by_search=document.form_search.by_search.value;
	var code_desc=document.form_search.code_desc.value;
	var prod_code1=document.form_search.prod_code1.value;
	var prod_code2=document.form_search.prod_code2.value;
	var from_date=document.form_search.from_date.value;
	var to_date=document.form_search.to_date.value;
	var from_location=document.form_search.from_location.value;
	if (by_search=="by_product") {
				  window.open ('sales_inquiry_pdf_revise.php?hide_rows='+hide_rows+'&by_search='+by_search+'&code_desc='+code_desc+'&prod_code1='+prod_code1+'&prod_code2='+prod_code2+'&from_date='+from_date+'&to_date='+to_date+'&from_location='+from_location);
	} else {
				  window.open ('sales_inquiry_pdf_revise.php?group='+box_group+'&dept='+box_dept+'&cls='+box_cls+'&subcls='+box_subcls+'&hide_rows='+hide_rows+'&by_search='+by_search+'&from_date='+from_date+'&to_date='+to_date+'&from_location='+from_location);
	}
}
function sales_load() {
	//if (locCode=="" && docDate=="") {
		var locCode=document.form_search.from_location.value;
		var docDate=document.form_search.from_date.value;
	//}
	new Ajax.Request(
	  'get_current_sales_ajax.php?locCode='+locCode+'&docDate='+docDate,
	  {
		 asynchronous : true,     
		 onComplete   : function (req){
			eval(req.responseText);
		 }
	  }
	);
}

function print_daily_sales_flash_pdf() {
	var locCode=document.form_search.from_location.value;
	var docDate=document.form_search.from_date.value;
	window.open ('daily_sales_flash_pdf.php?locCode='+locCode+'&docDate='+docDate);
}

function salesbyproduct_load() {
	//if (locCode=="" && docDate=="") {
		var locCode=document.form_search.from_location.value;
		var docDate=document.form_search.from_date.value;
	//}
	new Ajax.Request(
	  'get_current_salesbyproduct_ajax.php?locCode='+locCode+'&docDate='+docDate,
	  {
		 asynchronous : true,     
		 onComplete   : function (req){
			eval(req.responseText);
		 }
	  }
	);
}

function printdailysalesbyproduct_pdf() {
	var locCode=document.form_search.from_location.value;
	var docDate=document.form_search.from_date.value;
	window.open ('daily_sales_by_product_pdf.php?locCode='+locCode+'&docDate='+docDate);
}

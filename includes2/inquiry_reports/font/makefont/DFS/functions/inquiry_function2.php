<?
require_once "../../includes/config.php";
require_once "../../functions/db_function.php";
$db = new DB;
$db->connect();

function func_from_loc() {
	$from_loc=$_POST['from_loc'];
	########################## dont forget to get the company code #######################
	$querylocation="SELECT * FROM tblLocation WHERE compCode = 1234 ORDER BY locCode ASC";
	$resultlocation=mssql_query($querylocation);
	$numlocation = mssql_num_rows($resultlocation);
	echo "<select name='from_loc' id='select8' style='width:200px; height:20px;' onchange='val_from_loc(this.value);'>
          <option selected>$from_loc</option>";
		  for ($i=0;$i<$numlocation;$i++){  
		  	$loccode=mssql_result($resultlocation,$i,"locCode"); 
			$locname=mssql_result($resultlocation,$i,"locName"); 
				
    echo "<option>$loccode - $locname</option>";
          } 
    echo "</select>";
}

function func_to_loc() {
	$to_loc=$_POST['to_loc'];
	########################## dont forget to get the company code #######################
	$querylocation="SELECT * FROM tblLocation WHERE compCode = 1234 ORDER BY locCode ASC";
	$resultlocation=mssql_query($querylocation);
	$numlocation = mssql_num_rows($resultlocation);
	echo "<select name='to_loc' id='select8' style='width:200px; height:20px;' onchange='val_to_loc(this.value);'>
          <option selected>$to_loc</option>";
		  for ($i=0;$i<$numlocation;$i++){  
		  	$loccode=mssql_result($resultlocation,$i,"locCode"); 
			$locname=mssql_result($resultlocation,$i,"locName"); 
				
    echo "<option>$loccode - $locname</option>";
          } 
    echo "</select>";
}

function func_trans_header() {
	
}
?>
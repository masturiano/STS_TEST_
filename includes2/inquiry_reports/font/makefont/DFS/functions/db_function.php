<?
#Description: Database Functions
#Author: Jhae Torres
#Date Created: August 28, 2008


class DB
{
	var $link;
	var $result;
	
	function connect(){
		global $config;
		$this->link = mssql_connect($config['db_host'], $config['db_username'], $config['db_password'], TRUE) or die(mssql_get_last_message());
		mssql_select_db($config['db_name']) or die(mssql_get_last_message());
	}
	
	function query($query){
		$this->result = mssql_query($query) or die(mssql_get_last_message())."<br>SQL: ".$query;
	}
	
	function result(){
		return $this->result;
	}
	
	function getArrResult()
	{
 		$this->resultarr=array();
 		$i=0;
 		while ($row = mssql_fetch_array($this->result, MSSQL_ASSOC))
		{
			$this->resultarr[$i]=$row;
 			$i++;
 		}
 		return $this->resultarr;
	}
	
	function radioOption($radio_arr, $radio_name, $radio_id, $radio_value){
		if (! is_array($radio_arr)){
			return '';
		}
		reset( $radio_arr );
		foreach($radio_arr as $v){
			$r .= "<input type=\"radio\" name=\"".$radio_name."\" id=\"".$v[$radio_id]."\" onClick=\"javascript:alert(this.id);\" />".$v[$radio_value];
		}
		return $r;
	}

	function selectOption2D($select_arr, $select_name, $select_id, $attribute, $event, $selected, $opt_val, $opt_name, $opt_etc)
	{
		if (! is_array($select_arr)){
			return '';
		}
		reset( $select_arr );
	    $s = "\n<select name=\"$select_name\" id=\"$select_id\" $attribute>";
        $s.= "<option value=\"0\" $event></option>";
   		foreach ($select_arr as $v ){
/*
$s .= "\n\<option value=\"".$v[$opt_val]."\" $event title=\"".$v[$opt_title]."\" alt=\"".$v[$opt_name]."\"";
$s .= ($v[$opt_val] == $selected ? " selected=\"selected\"" : "").">" .  $v[$opt_name]  . "</option>";
*/
        	$s .= "\n\<option value=\"".$v[$opt_val]."\" $event id=\"".$v[$opt_name]."\" title=\"".$v[$opt_etc]."\" alt=\"".$v[$opt_name]."\"";
			$s .= ($v[$opt_val] == $selected ? " selected=\"selected\"" : "").">" .  $v[$opt_name]  . "</option>";
		}
    	$s .= "\n</select>\n";
   		return $s;
	}
	
	
	function cutStr($mainstr, $limit = ''){
		   $str = $mainstr;
		   $arryStr = $str[$limit];
		   
		   if (!empty($arryStr)) {
		   	$substr = substr($str,0,$limit)."...";
		   	return $substr;
		   }
		   else {
		      return $str;
		   }
	}
	
	function selectOption($select_arr, $select_name, $select_id, $attribute, $event, $selected, $opt_val, $opt_code,$opt_name, $opt_etc)
	{
		if (! is_array($select_arr)){
			return '';
		}
		reset( $select_arr );
	    $s = "\n<select name=\"$select_name\" id=\"$select_id\" $attribute>";
        $s.= "<option value=\"0\" $event></option>";
   		foreach ($select_arr as $v ){
/*
$s .= "\n\<option value=\"".$v[$opt_val]."\" $event title=\"".$v[$opt_title]."\" alt=\"".$v[$opt_name]."\"";
$s .= ($v[$opt_val] == $selected ? " selected=\"selected\"" : "").">" .  $v[$opt_name]  . "</option>";
*/
        	$s .= "\n\<option value=\"".$v[$opt_val]."\" $event id=\"".$v[$opt_name]."\" title=\"".$v[$opt_etc]."\" alt=\"".$v[$opt_name]."\"";
			$s .= ($v[$opt_val] == $selected ? " selected=\"selected\"" : "").">". $v[$opt_code] . " - ". $this->cutStr($v[$opt_name],'20')." </option>";
		}
    	$s .= "\n</select>\n";
   		return $s;
	}
	
	function disconnect(){
		mssql_close($this->link);
	}
}

?>
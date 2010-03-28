<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once("etc/dbsettings.php");
require_once("sql/my.php");
require_once("lib/base_connect.php");

setlocale(LC_ALL,'ru_RU.UTF8');
$conn=mysql_connect(C_SQL_SERVER, C_SQL_USER, C_SQL_PASS)
        or die("Could not connect: " . mysql_error());
mysql_query("use `".mysql_escape_string(C_SQL_DB)."`",$conn);
mysql_query("SET NAMES 'UTF8'",$conn) or die (mysql_error());
mysql_query("SET CHARACTER SET 'UTF8'",$conn) or die (mysql_error());



function read_var_get_or_post($name,$default)
{
	$res=$_GET[$name];
	if($res=='')$res=$_POST[$name];
	if($res=='')$res=$default;
	return $res;
}

$force_names=(read_var_get_or_post('force_names',0)==1);
$table=read_var_get_or_post('table','');
$query=read_var_get_or_post('query','');
if($table=='' && $query=='')exit;

if($table=='')
{
	$table='q';
	$force_names=(read_var_get_or_post('force_names',1)==1);
}
if($query=='')$query="SELECT * FROM `".mysql_escape_string($table)."`";
else $out_query=$query;
$filename=read_var_get_or_post('filename',$table."_".date("YmdHi").'.csv');

$csvdelimiter=read_var_get_or_post('d',',');
$csvencoding=read_var_get_or_post('e','UTF-8');
unset($n);
if(!preg_match('/\.csv/',$filename))$filename.='.csv';
	$result=$sql->query($query);
	header("Content-Type: text/plain; charset=$csvencoding");
	header("Content-Disposition: attachment; filename=".$filename);
	
	while($row=$sql->fetcha($result))
	{
		$k=1;
		$str='';
		foreach($row as $v)
		{
			if($k>1)$str.=$csvdelimiter;
			$k++;
			$val=preg_replace('/"/','""',$v);
			//if(($colstats->type[$ind]=='double')&&($decimaldot!='.'))$val=preg_replace('/[.]/',$decimaldot,$val);
			if(preg_match("/.+\".+|$csvdelimiter/",$val)>0)$val='"'.$val.'"';
			$str.=$val;
		};
		if($csvencoding!='UTF-8')
			print iconv('UTF-8',$csvencoding,$str)."\n";
		else
			print "$str\n"; //Намного быстрее
	}






?>
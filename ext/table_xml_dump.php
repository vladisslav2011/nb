<?php
/*
in:
	table: table to export
	query: query to export (no type info without table)
	either table or query must be given
	
	filename: file name to provide in content-disposition header, default [datetime].xml
	
	variables may be passed via GET or POST request



*/

set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once("etc/dbsettings.php");


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

function output_create($create)
{
	$res=mysql_query($create);
	if($row=mysql_fetch_array($res,MYSQL_ASSOC))
	{
		print "<create_table name='".htmlspecialchars($row['Table'],ENT_QUOTES)."' >".htmlspecialchars($row['Create Table'],ENT_QUOTES)."</create_table>\n";
	}
	print "\n";
}

function output_query($query)
{
	print "<query>".htmlspecialchars($query)."</query>\n";
}

function output_columns($columns)
{
	$res=mysql_query($columns);
	while($row=mysql_fetch_array($res,MYSQL_ASSOC))
	{
		print "<column ";
		foreach($row as $c => $v)
			if(isset($v))
				print htmlspecialchars($c,ENT_QUOTES)."='".htmlspecialchars($v,ENT_QUOTES)."' ";
		print " />\n";
	}
	print "\n";
}

function output_indexes($indx)
{
	$res=mysql_query($indx);
	while($row=mysql_fetch_array($res,MYSQL_ASSOC))
	{
		print "<key ";
		foreach($row as $c => $v)
			if(isset($v))
				print htmlspecialchars($c,ENT_QUOTES)."='".htmlspecialchars($v,ENT_QUOTES)."' ";
		print " />\n";
	}
	print "\n";
}

function output_rows($q,$force_names=false)
{
	$res=mysql_query($q);
	while($row=mysql_fetch_array($res,MYSQL_ASSOC))
	{
		print "<r>\n";
		foreach($row as $c => $v)
			if(isset($v)|| !$force_names)
				print " <c".
					($force_names?(" name='".htmlspecialchars($c,ENT_QUOTES)."'"):"").
					(isset($v)?(">".htmlspecialchars($v,ENT_QUOTES)."</c>"):(" null='1' />"))."\n";
		print "</r>\n";
	}
	print "\n";
}

function db_version()
{
	$res=mysql_query("SELECT @@version");
	if($row=mysql_fetch_array($res,MYSQL_NUM))
	{
		return $row[0];
	}
	return NULL;
}




$force_names=(read_var_get_or_post('force_names',0)==1);
$table=read_var_get_or_post('table','');
$db=read_var_get_or_post('db',C_SQL_DB);
$query=read_var_get_or_post('query','');
if($table=='' && $query=='')exit;
if($table!='')
{
	$show_indexes="SHOW INDEXES FROM `".mysql_escape_string($table)."`";
	$show_columns="SHOW FULL COLUMNS FROM `".mysql_escape_string($table)."`";
	$show_create="SHOW CREATE TABLE `".mysql_escape_string($table)."`";
};

if($table=='')
{
	$table='q';
	$force_names=true;
}
if($query=='')
	$query="SELECT * FROM `".mysql_escape_string($db)."`.`".mysql_escape_string($table)."`";
else
	$out_query=$query;
$filename=read_var_get_or_post('filename',$db.'_'.$table."_".date("YmdHi").'.xml');

header("Content-type: text/xml; charset=utf-8");
header("Content-Disposition: attachment; filename=".$filename);
print '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
print "<table_dump version='1.1' software='mysql' software_version='".htmlspecialchars(db_version(),ENT_QUOTES)."'>\n";
print "<definition name='".htmlspecialchars($table,ENT_QUOTES)."'>\n";

if(isset($show_create))		output_create($show_create);
if(isset($show_columns))	output_columns($show_columns);
if(isset($show_indexes))	output_indexes($show_indexes);
if(isset($out_query))		output_query($out_query);
print "</definition>\n";
print "<rows name='".htmlspecialchars($table,ENT_QUOTES)."'>\n";
output_rows($query,$force_names);
print "</rows>\n";
print '</table_dump>';




?>
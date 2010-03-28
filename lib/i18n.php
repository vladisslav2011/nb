<?php
require_once("etc/dbsettings.php");
require_once("sql/my.php");
require_once("lib/base_connect.php");
require_once("lib/ddc_raw.php");



$ddc_tables[TABLE_META_I18N]=(object)Array(
	'name' => TABLE_META_I18N,
	'cols' => Array(
		Array('name' =>'object', 'sql_type' =>'varchar(64)', 'sql_null' =>0, 'sql_default' =>'', 'sql_sequence' => 0),
		Array('name' =>'var', 'sql_type' =>'varchar(64)', 'sql_null' =>0, 'sql_default' =>'', 'sql_sequence' => 0),
		Array('name' =>'loc', 'sql_type' =>'varchar(10)', 'sql_null' =>0, 'sql_default' =>'', 'sql_sequence' => 0),
		Array('name' =>'val', 'sql_type' =>'mediumtext', 'sql_null' =>1, 'sql_default' =>NULL, 'sql_sequence' => 0)
	),
	'keys' => Array(
		Array('key' =>'PRIMARY', 'name' =>'object', 'sub' => NULL),
		Array('key' =>'PRIMARY', 'name' =>'var', 'sub' => NULL),
		Array('key' =>'PRIMARY', 'name' =>'loc', 'sub' => NULL)
	)
);



if($_GET['init']=='init')
ddc_gentable_o($ddc_tables[TABLE_META_I18N],$sql);

function loc_get_val($oid,$var,$fallback)
{
	global $sql;
	//implement string cache????
	$res=$sql->query('SELECT val FROM `'.TABLE_META_I18N.'` WHERE object=\''.
		$sql->esc($oid).
		'\' AND var=\''.$sql->esc($var).
		'\' AND loc=\''.$sql->esc($_SESSION['lang']).'\'');
	if($row=$sql->fetcha($res))
	{
		$sql->free($res);
		return $row['val'];
	}else return $fallback;
}




?>
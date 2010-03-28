<?php
require_once("etc/dbsettings.php");
require_once("sql/my.php");
require_once("lib/base_connect.php");
require_once("lib/ddc_raw.php");
require_once("lib/i18n.php");
require_once("lib/query_select.php");


//settings
//keys: object,setting,uid/gid,presetid
//values: val

//table definition

//	function addcol($name,$type,$null,$default,$extra,$comment)

$ddc_tables[TABLE_META_SETTINGS]=(object)Array(
	'name' => TABLE_META_SETTINGS,
	'cols' => Array(
		Array('name' =>'oid', 'sql_type' =>'varchar(64)', 'sql_null' =>0, 'sql_default' =>NULL, 'sql_sequence' => 0),
		Array('name' =>'setting', 'sql_type' =>'varchar(64)', 'sql_null' =>0, 'sql_default' =>'', 'sql_sequence' => 0),
		Array('name' =>'uid', 'sql_type' =>'bigint(20)', 'sql_null' =>0, 'dsql_efault' =>-1, 'sql_sequence' => 0),
		Array('name' =>'preset', 'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0),
		Array('name' =>'val', 'sql_type' =>'mediumtext', 'sql_null' =>1, 'sql_default' =>NULL, 'sql_sequence' => 0)
	),
	'keys' => Array(
		Array('key' =>'PRIMARY', 'name' =>'oid', 'sub' => NULL),
		Array('key' =>'PRIMARY', 'name' =>'setting', 'sub' => NULL),
		Array('key' =>'PRIMARY', 'name' =>'uid', 'sub' => NULL),
		Array('key' =>'PRIMARY', 'name' =>'preset', 'sub' => NULL)
	)
);



if($_GET['init']=='init')
ddc_gentable_o($ddc_tables[TABLE_META_SETTINGS],$sql);


class settings_tool
{
function single_query($oid,$setting,$uid,$preset,$flags='')
{
	global $active_locale;
	if(! isset($active_locale))$active_locale='RU';
	$q=new query_select;
	$q->add_from('`'.TABLE_META_SETTINGS.'`','sets');
	$q->add_what('`sets`.`val`','');
	if($flags != '')
	{
		$q->add_what('`i18n`.`val`','name');
		$q->add_from('`'.TABLE_META_I18N.'`','i18n');
		$q->add_where("i18n.object = '".sql::esc($oid)."'");
		$q->add_where("i18n.var = '".sql::esc($setting)."'");
		$q->add_where("i18n.loc = '".sql::esc($active_locale)."'");
	}
	$q->add_where("sets.oid = '".sql::esc($oid)."'");
	$q->add_where("sets.setting = '".sql::esc($setting)."'");
	$q->add_where("sets.uid = '".sql::esc($uid)."'");
	$q->add_where("sets.preset = '".sql::esc($preset)."'");
	return $q->result();
}

function all_query($oid,$uid,$preset,$flags)
{
	global $active_locale;
	if(! isset($active_locale))$active_locale='RU';
	$q=new query_select;
	$q->add_from('`'.TABLE_META_SETTINGS.'`','sets');
	$q->add_what('`sets`.`val`','');
	$q->add_what('`sets`.`setting`','');
	if($flags != '')
	{
		$q->add_what('`i18n`.`val`','name');
		$q->add_from('`'.TABLE_META_I18N.'`','i18n');
		$q->add_where("i18n.object = '".sql::esc($oid)."'");
		$q->add_where("i18n.var = '".sql::esc($setting)."'");
		$q->add_where("i18n.loc = '".sql::esc($active_locale)."'");
	}
	$q->add_where("sets.oid = '".sql::esc($oid)."'");
	$q->add_where("sets.uid = '".sql::esc($uid)."'");
	$q->add_where("sets.preset = '".sql::esc($preset)."'");
	return $q->result();
}

function set_query($oid,$setting,$uid,$preset,$val)
{
	return "INSERT INTO `".TABLE_META_SETTINGS."` SET `oid` = '".sql::esc($oid).
	"', `setting` = '".sql::esc($setting).
	"', `uid` = '".sql::esc($uid).
	"', `preset` = '".sql::esc($preset).
	"', `val` = '".sql::esc($val).
	"' ON DUPLICATE KEY UPDATE `val` = '".sql::esc($val)."'";
	
}

function read_settings($oid,$uid,$preset,$sql)
{
	$res=$sql->query($this->all_query($oid,$uid,$preset,NULL));
	while($row=$sql->fetcha($res))
		$ret[$row['setting']]=$row['val'];
	$sql->free($res);
	return $ret;
	
}

function clear()
{
	unset($this->oids);
}

function add_oid($oid)
{
	$this->oids[$oid]=$oid;
}

function read_oids($sql)
{
	$q=new query_select;
	$q->add_from('`'.TABLE_META_SETTINGS.'`','sets');
	$q->add_what('`sets`.`val`','val');
	$q->add_what('`sets`.`setting`','setting');
	$q->add_what('`sets`.`oid`','oid');
	$in_list='';
	if(! is_array($this->oids))return Array();
	foreach($this->oids as $oid)
	{
		if($in_list != '') $in_list.=',';
		$in_list.="'".$sql->esc($oid)."'";
	}
	$q->add_where("sets.oid IN (".$in_list.")");
	$q->add_where("sets.uid = '".$sql->esc($_SESSION['uid'])."'");
	$q->add_where("sets.preset = '".$sql->esc($_SESSION['settings_preset'])."'");
	$res=$sql->query($q->result());
	while($row=$sql->fetcha($res))
		$ret[$row['oid']][$row['setting']]=$row['val'];
	return $ret;
}

}





?>
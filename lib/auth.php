<?php
require_once("etc/dbsettings.php");
require_once("sql/my.php");
require_once("lib/base_connect.php");
require_once("lib/ddc_raw.php");
require_once('lib/dom.php');
require_once('lib/settings.php');
//$_SESSION['sql_design']=true;
$_SESSION['lang']='ru';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$ddc_tables[TABLE_META_USERS]=(object)
Array(
 'name' => TABLE_META_USERS,
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'uid',		'sql_type' =>'bigint(20)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'sql_comment' =>NULL),
  Array('name' =>'name',	'sql_type' =>'varchar(64)', 'sql_null' =>0, 'sql_default' =>'dummy',	'sql_sequence' => 0,			'sql_comment' =>NULL),
  Array('name' =>'pass',	'sql_type' =>'varchar(64)', 'sql_null' =>0, 'sql_default' =>md5('dummy'),	'sql_sequence' => 0,			'sql_comment' =>NULL, 'editor'=>'editor_text_pass_md5'),
  Array('name' =>'reflink',	'sql_type' =>'bigint(20)',  'sql_null' =>1, 'sql_default' =>NULL,		'sql_sequence' => 0,			'sql_comment' =>NULL),
  Array('name' =>'isgroup',	'sql_type' =>'tinyint(1)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 0,			'sql_comment' =>NULL, 'editor' => 'editor_checkbox_st1u'),
  Array('name' =>'isactive',	'sql_type' =>'tinyint(1)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 0,			'sql_comment' =>NULL, 'editor' => 'editor_checkbox_st1u')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'uid', 'sub' => NULL)
 )
);

$ddc_tables[TABLE_META_GROUPS]=(object)
Array(
 'name' => TABLE_META_GROUPS,
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'uid',		'sql_type' =>'bigint(20)',  'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'gid',		'sql_type' =>'bigint(20)',  'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,	'sql_comment' =>NULL)
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'uid', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'gid', 'sub' => NULL)
 )
);

$ddc_tables[TABLE_META_PERMISSIONS]=(object)
Array(
 'name' => TABLE_META_PERMISSIONS,
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'uid',		'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'oid',		'sql_type' =>'varchar(64)','sql_null' =>0, 'sql_default' =>'dummy',	'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'read',	'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL, 'editor'=>'editor_checkbox_st1'),
  Array('name' =>'write',	'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL, 'editor'=>'editor_checkbox_st1'),
  Array('name' =>'update',	'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL, 'editor'=>'editor_checkbox_st1'),
  Array('name' =>'insert',	'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL, 'editor'=>'editor_checkbox_st1'),
  Array('name' =>'delete',	'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL, 'editor'=>'editor_checkbox_st1')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'uid', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'oid', 'sub' => NULL)
 )
);

$ddc_tables[TABLE_META_AUTHLOG]=(object)
Array(
 'name' => TABLE_META_AUTHLOG,
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'uid',		'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'date_in',	'sql_type' =>'timestamp',  'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'ip',		'sql_type' =>'varchar(16)','sql_null' =>0, 'sql_default' =>'',	'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'useragent',	'sql_type' =>'text',  'sql_null' =>0, 'sql_default' =>'',	'sql_sequence' => 0, 'sql_comment' =>NULL)
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'uid', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'date_in', 'sub' => NULL)
 )
);


if($_GET['init']=='init')
{
	
	ddc_gentable_o(	$ddc_tables[TABLE_META_USERS],		$sql);
	ddc_gentable_o(	$ddc_tables[TABLE_META_GROUPS],		$sql);
	ddc_gentable_o(	$ddc_tables[TABLE_META_PERMISSIONS],	$sql);
	ddc_gentable_o(	$ddc_tables[TABLE_META_AUTHLOG],	$sql);
	$sql->query("DELETE FROM `*users` WHERE name='root'");
	$sql->query("INSERT INTO `*users` SET uid='0', name='root', pass='63a9f0ea7bb98050796b649e85481845', isgroup=0, isactive=1");
	$sql->query("UPDATE `*users` SET uid=0 WHERE name='root'");
	
	$sql->query("INSERT INTO `*users` SET uid='1000', name='test', pass='5f4dcc3b5aa765d61d8327deb882cf99', isgroup=0, isactive=1");
}

class auth_handler
{
	function login($user,$pass)
	{
		global $sql;
		$euser=$sql->esc($user);
		$epass=md5($pass);
		$res=$sql->query("SELECT uid FROM `".TABLE_META_USERS."` WHERE name='".$euser."' AND pass='".$epass."' AND isactive=1");
		if($row=$sql->fetcha($res))
		{
			$_SESSION['uid']=$row['uid'];
			//add ip?????
			$sql->free($res);
		}
		if(isset($_SESSION['uid']))
		{
			$sql->query("INSERT INTO `".TABLE_META_AUTHLOG."` SET uid=".$_SESSION['uid']." , ip='".$_SERVER['REMOTE_ADDR']."' , useragent='".$_SERVER['HTTP_USER_AGENT']."'");
			return true;
		}
		return false;
	}
	function logout()
	{
		//unset($_SESSION['uid']);
		session_destroy();
	}
	function effective_permissions($oid)
	{
		global $sql;
		$res=$sql->query("SELECT a.oid,a.`read`,a.`write`,a.`update`,a.`insert`,a.`delete` FROM `".TABLE_META_PERMISSIONS."` AS a WHERE uid=".$_SESSION['uid']." OR (SELECT b.gid FROM `".TABLE_META_GROUPS."` as b WHERE a.uid=b.gid AND b.uid=".$_SESSION['uid'].") IS NOT NULL");
		$ret=Array('read' => false, 'write' => false, 'update' => false, 'insert' => false, 'delete' => false);
		if($res)
		{
			while($row=$sql->fetcha($res))
			{
				foreach($row as $i => $a)
					if($i != 'oid')$ret[$i] |= $a;
			}
			$sql->free($res);
		}
		return $ret;
	}
	
	function logonscreen()
	{
		
	}
	
	
}

$auth_handler=new auth_handler;

class dom_auth_request extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->css_style['margin']='auto';
		$this->css_style['width']='auto';
		$this->css_style['text-align']='center';
		$tbl=new dom_table;
		$tbl->css_style['margin-left']='auto';
		$tbl->css_style['margin-right']='auto';
		$tbl->css_style['border']='1px solid gray';
		$this->append_child($tbl);
		$tr=new dom_tr;
		$tbl->append_child($tr);
		$td=new dom_td;
		$tr->append_child($td);
		$txt=new dom_statictext;
		$td->append_child($txt);
		$txt->text='Вход в систему';
		$td->attributes['colspan']=2;
		
		$tr=new dom_tr;
		$tbl->append_child($tr);
		$td=new dom_td;
		$tr->append_child($td);
		$txt=new dom_statictext;
		$td->append_child($txt);
		$txt->text='Пользователь:';
		$td=new dom_td;
		$tr->append_child($td);
		$this->username=new dom_textinput;
		$this->username->attributes['value']='test';
		$td->append_child($this->username);
		
		$tr=new dom_tr;
		$tbl->append_child($tr);
		$td=new dom_td;
		$tr->append_child($td);
		$txt=new dom_statictext;
		$td->append_child($txt);
		$txt->text='Пароль:';
		$td=new dom_td;
		$tr->append_child($td);
		$this->pass=new dom_textinput;
		$this->pass->attributes['type']='password';
		$this->pass->attributes['value']='password';
		$td->append_child($this->pass);
		
		$tr=new dom_tr;
		$tbl->append_child($tr);
		$td=new dom_td;
		$tr->append_child($td);
		if(preg_match('/^127\.0\..*/',$_SERVER['REMOTE_ADDR']))
		{
			$rootlogin=new dom_textbutton;
			$rootlogin->attributes['value']='Вход';
			$rootlogin->attributes['onclick']=
			"chse.send_or_push({uri:'',static:'".
			"auth=root&".
			"user=' + encodeURIComponent(\$i('".$this->username->id_gen()."').value) + '&".
			"pass=' + encodeURIComponent(\$i('".$this->pass->id_gen()."').value) + '&".
			"',val:'',c_id:this.id});".
			"";
			$td->append_child($rootlogin);
		}
		$td=new dom_td;
		$tr->append_child($td);
		$this->submit=new dom_textbutton;
		$this->submit->attributes['value']='Вход';
		$submitnorm=$this->submit->attributes['onclick']=
		"chse.send_or_push({uri:'',static:'".
		"auth=login&".
		"user=' + encodeURIComponent(\$i('".$this->username->id_gen()."').value) + '&".
		"pass=' + encodeURIComponent(\$i('".$this->pass->id_gen()."').value) + '&".
		"',val:'',c_id:this.id});".
		"";
		$this->pass->attributes['onkeypress']=$this->username->attributes['onkeypress']=
		'var k= event.keyCode;'.
		'if(k==13){'.$submitnorm.
		'return false};return true;';
		$td->append_child($this->submit);
	}
}


if(isset($_POST['auth']))
{
	$auth=$_POST['auth'];
	if($auth == 'root')
	{
		if(preg_match('/^127\.0\..*/',$_SERVER['REMOTE_ADDR']))$_SESSION['uid']=0;
		print 'window.location.reload(true);';
		exit;
	}
	
	if($auth == 'login')
	{
		$res=$auth_handler->login($_POST['user'],$_POST['pass']);
		print 'window.location.reload(true);';
		exit;
	}
	
	if($auth == 'logout')
	{
		$res=$auth_handler->logout();
		print 'window.location.reload(true);';
		exit;
	}
}

function auth_gen_form()
{
global $sql;

$page=new dom_root;
$page->title='Вход в систему';

	$auth_div=new dom_auth_request;
	//$page->append_child($auth_div);
	$cont=new dom_table;
	$tr=new dom_tr;
	$td=new dom_td;
	$d=new dom_div;
	$d->css_style['top']='1px';
	$d->css_style['bottom']='1px';
	$d->css_style['left']='1px';
	$d->css_style['right']='1px';
	$d->css_style['position']='fixed';
	$d->css_style['display']='block';
	$cont->css_style['height']='100%';
	$cont->css_style['width']='100%';
	$td->css_style['height']='100%';
	$cont->append_child($tr);
	$tr->append_child($td);
	$td->append_child($auth_div);
	$d->append_child($cont);
	$page->append_child($d);
	//$txt=new dom_statictext;
	//$txt->text=md5('test');
	//$page->append_child($txt);



$page->scripts['core.js']='/js/core.js';
$page->scripts['commoncontrols.js']='/js/commoncontrols.js';
$page->endscripts[]="var ct=\$i('".$auth_div->username->id_gen()."');ct.focus();ct.selectionStart=0;ct.selectionEnd=ct.value.length;";

$settings_tool=new settings_tool;


$page->for_each_set('oid',-1);
$page->collect_oids($settings_tool);
$page->settings_array=$settings_tool->read_oids($sql);



$page->after_build();
print $page->html();
//print_r($_SERVER);
exit;
}

if(! isset($_SESSION['uid']))
	auth_gen_form();
else
	$sql->query("SET @UID='".$sql->esc($_SESSION['uid'])."'");


?>
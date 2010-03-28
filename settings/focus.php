<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
$_SESSION['focus-state']->keys=$_POST['keys'];
$_SESSION['focus-state']->name=$_POST['name'];
$_SESSION['focus-state']->oid=$_POST['oid'];
exit;
require_once('lib/ddc_meta.php');
require_once('lib/commctrls.php');
	ddc_gentable_n('%backlog',
		Array(
		//requred by tree
		Array('id','bigint(20)',0,NULL,1,NULL),
		Array('time','timestamp',0,NULL,NULL,NULL),
		Array('name','text(4096)',0,'',NULL,NULL),
		Array('keys','text(40096)',0,'',NULL,NULL),
		Array('oid','text(4096)',0,'',NULL,NULL)//,
	//	Array('','',1,NULL,NULL,NULL),
	)
	,
	Array(
		Array('PRIMARY','id',NULL),
		Array('time','time',NULL)//,
	)
	,$sql);

$q="insert into `%backlog` set  `name`='".$sql->esc($_POST['name'])."' , `keys`='".$sql->esc($_POST['keys'])."' , `oid`='".$sql->esc($_POST['oid'])."'";
$sql->query($q);
//$sql->query('delete from `%backlog`');
print "/*".$q."*/";



?>
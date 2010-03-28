<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
require_once('lib/settings.php');
$tool=new settings_tool;
$set->oid=$_POST['oid'];
$set->setting=$_POST['setting'];
$set->uid=$_SESSION['uid'];
if(!isset($_SESSION['uid']))
{
	print 'uid unset:';
	print $set->uid;
	exit;
}
$set->preset=isset($_POST['preset'])?$_POST['preset']:0;
$set->val=$_POST['val'];
$sql->query($tool->set_query($set->oid,$set->setting,$set->uid,$set->preset,$set->val));
#print $tool->set_query($set->oid,$set->setting,$set->uid,$set->preset,$set->val);










?>
<?php
$profiler=microtime(true);

require("../etc/dbsettings.php");
require("../sql/my.php");
require("../lib/base_connect.php");
require("../lib/ddc_raw.php");

$testtbl='mm';
$sql->query("DROP TABLE `$testtbl`");
$sql->query("CREATE TABLE `$testtbl` (`id_prod` int(11) NOT NULL auto_increment, `doorname` varchar(255) default NULL, `fillmodel` int(11) default NULL, `filltype` decimal(11,5) unsigned zerofill default NULL comment 'tyutfytuy', `boxtype` varchar(255) default NULL, `sheight` int(11) default NULL, `swidth` int(11) default NULL, `sthickness` int(11) default NULL, `isglass` varchar(5) default NULL, `glassincluded` varchar(5) default NULL, `decoration` varchar(255) default NULL, `glassing` int(11) default NULL, `fillet` int(11) default NULL, `store_rests` double default NULL, `rests_date` datetime NOT NULL default '2007-09-26 00:00:00', `price` double default NULL, `price_currency` char(3) default 'RUR', PRIMARY KEY (`id_prod`,`fillmodel`,`sheight`), key (`doorname`(10)) )");

//$sql->query("CREATE TABLE `$testtbl` (`id_prod` int(11) NOT NULL auto_increment, `doorname` varchar(255) default NULL, `fillmodel` int(11) default NULL, PRIMARY KEY (`id_prod`), key (doorname(10)) )");

//	function addcol($name,$type,$null,$default,$extra,$comment)

ddc_gentable_n('t1',
Array(
	Array('id','int',0,NULL,'auto_increment',NULL),
	Array('t1','varchar(100)',1,NULL,NULL,NULL),
	Array('t2','varchar(100)',1,NULL,NULL,NULL),
	Array('in','bigint',1,NULL,NULL,NULL),
	Array('mf','int',1,NULL,NULL,NULL),
	Array('in2','bigint',1,NULL,NULL,NULL)
),
Array(
	Array('PRIMARY','id',NULL),
	Array('1','t1',12),
	Array('1','t2',12),
	Array('1','in',NULL)
	
),$sql);


$v1=new ddc_raw;

$v1->load_table($testtbl,$sql);


print "<HTML>";
//$r=$sql->query("SHOW COLUMNS FROM `$testtbl`");
//var_dump($v1->cols);
$v1->print_cols();
$v1->print_keys();
//var_dump($v1->keys);
//$v1->delcol('doorname');$v1->delcol('fillmodel');$v1->delcol('filltype');


print $v1->create_query('test');


$v2=new ddc_raw;
$v2->addcol('test','varchar(10)',1,NULL,NULL,NULL);
$v2->addcol('test1','varchar(10)',1,NULL,NULL,NULL);
$v2->addcol('test2','varchar(10)',1,NULL,NULL,NULL);
$v2->addcol('test3','varchar(10)',1,NULL,NULL,NULL);
$v2->addcol('id','int(10) unsigned auto_increment',1,NULL,NULL,NULL);
$v2->addcol('doorname','varchar(10)',1,NULL,NULL,NULL);
$v2->addcol_to_key('PRIMARY','id',NULL);
$v2->addcol_to_key('PRIMARY','test',5);
$v1->gen_changes($v2);
$v1->print_querys();
$r=$v1->commit_changes($sql);
if($r) echo $r;

print "Script processing took ".(microtime(true)-$profiler)." sec<br>";
print $sql->qcnt.' querys, took '.$sql->querytime.' sec';

print '<table border=1>';
print "<tr><td colspan=2>1</td><td rowspan=2>2</td></tr>";
print "<tr><td rowspan=2>3</td><td>4</td></tr>";
print "<tr><td colspan=2>5</td></tr>";

print '</table>';

print "</HTML>";

?>
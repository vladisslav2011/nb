<?php
require_once("etc/dbsettings.php");
require_once("sql/my.php");
require_once("lib/base_connect.php");

$q=$_GET['q'];
$csvdelimiter=$_GET['d'];
if($csvdelimiter=='')$csvdelimiter=',';
$csvencoding=$_GET['e'];
unset($n);
if(isset($_GET['n']))$n=$_GET['n'];
if(!isset($n))$n=$table."_".date("YmdHi").'.csv';
if(!preg_match('/\.csv/',$n))$n.='.csv';
	$result=$sql->query($q);
	header("Content-Type: text/plain; charset=$csvencoding");
	header("Content-Disposition: attachment; filename=".$n);
	
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
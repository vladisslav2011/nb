<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
//$_SESSION['uid']=0;
//$_SESSION['sql_design']=false;
$profiler=microtime(true);
require_once('lib/ddc_meta.php');
require_once('lib/dom.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');
require_once('lib/query_gen.php');





$page=new dom_root_print;
$page->title='Тестовый отчет';

function printhead($exstyle)
{
	global $encoding;
	print "<html><head><title>Тестовый отчет</title>";
	//print "<link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\">";
	print "<style type='text/css'>";
	foreach($exstyle as $si=>$se)//selector
	{
		 print $si." {\n";
		 foreach($se as $ai=>$ae)//attribute
		 	print $ai.' : '.$ae." ;\n";
		 print "}\n";
	}
	print "@media print {\n";
	foreach($exstyle as $si=>$se)//selector
	{
		 print $si." {\n";
		 foreach($se as $ai=>$ae)//attribute
		 	print $ai.' : '.$ae." ;\n";
		 print "}\n";
	}
	print "}\n";
	print "</style>";
	print "<meta http-equiv=content-type content=\"text/html; charset=UTF-8\"></head><body>\n";
};


$exstyle['*']=Array(
'margin'=>'0mm',
'padding'=>'0mm'
);	

$exstyle['.pb']=Array(
'page-break-before'=>'avoid',
'page-break-after'=>'always',
//'margin'=>'auto',
'height'=>'30mm',
'padding-top'=>'2mm',
'margin-left'=>'-1.8mm',
'width'=>'57mm',
//'border'=>' 1px solid black',
'overflow'=>'hidden'
);	
$exstyle['.pb>div.code']=Array(
'width'=>'56mm',
'height'=>'18mm',
'text-align'=>'left',
'margin'=>'auto',
'padding'=>'0mm',
//'outline'=>'1px solid black',
'font-family'=>'EanGnivc',
//'display'=>'none',
'font-size'=>'16mm'
);	
$exstyle['.pb>div.text']=Array(
'width'=>'54mm',
'height'=>'10mm',
'margin'=>'auto',
'vertical-align'=>'middle'
);
$exstyle['.pb>div.text div']=Array(
'font-family'=>'helvetica',
'font-size'=>'2.8mm',
'line-height'=>'3.5mm',
'text-align'=>'center',
//'outline'=>'1px solid black',
'word-spacing'=>'0mm',
'letter-spacing'=>'0mm',
'overflow'=>'hidden'
);	
$exstyle['@page']=Array(
'margin'=>'0mm',
'padding'=>'0mm'
//'size'=>'57mm 31mm'
//,'size'=>'landscape'
);

?>
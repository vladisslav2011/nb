<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once('lib/ipp/PrintIPP.php');
require_once('lib/utils.php');

$ipp_host=$_GET["host"];
$ipp_printer=$_GET["printer"];
$task=$_GET["task"];

$json=file_get_contents("http://api.dveri.ru/?method=CRM&json=".urlencode('{"packet":{"ACTION":"BarCodeAPI.GetTask","DATA":{"taskId":"'.js_escape($task).'"}},"protocol":"0.3"}'));
header("Content-type: text/plain; charset=\"UTF-8\"");


$a=json_decode($json,true);

if(is_null($a))
{
	print "Fuck the fucking motherfucker off!!!!!\n";
	print "This is not JSON:\n";
	print $json;
	exit;
}


if($a["packet"]["RESULT"] == "OK")
{
	
	$ipp = new PrintIPP();
	$ipp->setHost($ipp_host);
	$ipp->setPrinterURI($ipp_printer);
	$ipp->setData($printjob);
	$ipp->setRawText($a["packet"]["DATA"]["Result"]);
	$ipp->printJob();
}else{
	print "Something bad has happened...\n";
	print $a["packet"]["RESULT"]."\n";
	print $a["packet"]["ERRORMSG"]."\n";
}









?>
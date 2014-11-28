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


function printhead()
{
	global $encoding;
	print "<html><head><title>Коды - печать</title>";
	print "<meta http-equiv=content-type content=\"text/html; charset=UTF-8\"></head>";
	//print "<link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\">";
	print "<style type='text/css'>";
	print <<<aaa
div.pb {
margin:auto;
padding:0mm;
padding-top:0mm;
height:25mm;
width:48mm;
overflow:hidden;
background: solid white;
top:0;
left:0;
position:relative;
border:1px solid white;
}
div.tname {
background-color:white;
position:absolute;
top:0;
z-index:100;
font-family:arial;
width:48mm;
margin:0mm;
padding-bottom:1mm;
text-align:center;
}
div.barcode {
z-index:10;
position:absolute;
left:0;
right:0;
bottom:0;
height:auto;
margin-left:-3mm;
text-align:left;
}
@page {
margin: 0mm;
padding:0mm;
}
@media print {
body{
margin-top:0mm;
margin-bottom:0mm;
margin-left:0mm;
margin-right:0mm;
padding:0mm;
}
.pb{
page-break-before:avoid;
page-break-after:always;
}
}
aaa;
	print "</style><body>";
};

function barcode_gen_ean_sum($ean){
  $even=true; $esum=0; $osum=0;
  for ($i=strlen($ean)-1;$i>=0;$i--){
	if ($even) $esum+=$ean[$i];	else $osum+=$ean[$i];
	$even=!$even;
  }
  return (10-((3*$esum+$osum)%10))%10;
}

function ean_gnivc($ean)
{
	$sets['d']="#$%&'()*+,";
	$sets['a']='0123456789';
	$sets['b']='ABCDEFGHIJ';
	$sets['c']='abcdefghij';
	$seq[0]=	'aaaaaa';
	$seq[1]=	'aababb';
	$seq[2]=	'aabbab';
	$seq[3]=	'aabbba';
	$seq[4]=	'abaabb';
	$seq[5]=	'abbaab';
	$seq[6]=	'abbbaa';
	$seq[7]=	'ababab';
	$seq[8]=	'ababba';
	$seq[9]=	'abbaba';


	$ean=trim($ean);
	if (eregi("[^0-9]",$ean))
	{
		return "Invalid EAN-Code";
	}
	if (strlen($ean)<12 || strlen($ean)>13)
	{
		return "Invalid Code (must have 12/13 numbers)";
	}

	$ean=substr($ean,0,12);
	$eansum=barcode_gen_ean_sum($ean);
	$ean.=$eansum;
	$res=$sets['d'][intval($ean[0])].'!';
	for ($i=1;$i<13;$i++)
	{
		if ($i<7) $res.=$sets[$seq[intval($ean[0])][$i-1]][intval($ean[$i])]; else $res.=$sets['c'][intval($ean[$i])];
		if ($i==6) $res.='-';
	}
	return $res.'!';

}


function onemorecode($code)
{
print "<span style='margins:auto;font-family:EanGnivc;font-size:15mm'>";

print ean_gnivc($code);

print "</span>";

}


function to_table_bars($v,$width,$height,$add)
{
	$tbl=preg_replace('/0/','!',$v);
	$tbl=preg_replace('/1/','~',$tbl);
	//$tbl=preg_replace('/~/',"<td $add style='margin:0px;border-width:0px;padding:0px;background-color:black'><div style='display:block;width:${width}px;height:${height}px;'></div></td>",$tbl);
	//$tbl=preg_replace('/!/',"<td $add style='margin:0px;border-width:0px;padding:0px;background-color:white'><div style='display:block;width:${width}px;height:${height}px;'></div></td>",$tbl);
	$tbl=preg_replace('/~/',"<td $add style='margin:0px;padding:0px;background-color:black'><img src='black.png' style='width:${width}px;height:${height}mm;'></td>",$tbl);
	$tbl=preg_replace('/!/',"<td $add style='margin:0px;padding:0px;background-color:white'><img src='white.png' style='width:${width}px;height:${height}mm;'></td>",$tbl);
	return $tbl;
}



function via_table($ean)
{
$bars['a'][0]='0001101';
$bars['a'][1]='0011001';
$bars['a'][2]='0010011';
$bars['a'][3]='0111101';
$bars['a'][4]='0100011';
$bars['a'][5]='0110001';
$bars['a'][6]='0101111';
$bars['a'][7]='0111011';
$bars['a'][8]='0110111';
$bars['a'][9]='0001011';
$bars['b'][0]='0100111';
$bars['b'][1]='0110011';
$bars['b'][2]='0011011';
$bars['b'][3]='0100001';
$bars['b'][4]='0011101';
$bars['b'][5]='0111001';
$bars['b'][6]='0000101';
$bars['b'][7]='0010001';
$bars['b'][8]='0001001';
$bars['b'][9]='0010111';
$bars['c'][0]='1110010';
$bars['c'][1]='1100110';
$bars['c'][2]='1101100';
$bars['c'][3]='1000010';
$bars['c'][4]='1011100';
$bars['c'][5]='1001110';
$bars['c'][6]='1010000';
$bars['c'][7]='1000100';
$bars['c'][8]='1001000';
$bars['c'][9]='1110100';
$start_end='101';
$mid='01010';
	
	$seq[0]=	'aaaaaa';
	$seq[1]=	'aababb';
	$seq[2]=	'aabbab';
	$seq[3]=	'aabbba';
	$seq[4]=	'abaabb';
	$seq[5]=	'abbaab';
	$seq[6]=	'abbbaa';
	$seq[7]=	'ababab';
	$seq[8]=	'ababba';
	$seq[9]=	'abbaba';
	
	
	$ean=trim($ean);
	if (eregi("[^0-9]",$ean))
	{
		return "Invalid EAN-Code";
	}
	if (strlen($ean)<12 || strlen($ean)>13)
	{
		return "Invalid Code (must have 12/13 numbers)";
	}

	$ean=substr($ean,0,12);
	$eansum=barcode_gen_ean_sum($ean);
	$ean.=$eansum;
	$group1='';
	$txt1='';
	for ($i=1;$i<7;$i++)
	{
		$group1.=$bars[$seq[intval($ean[0])][$i-1]][intval($ean[$i])];
		$txt1.=$ean[$i];
	}
	$group2='';
	$txt2='';
	for ($i=7;$i<13;$i++)
	{
		$group2.=$bars['c'][intval($ean[$i])];
		$txt2.=$ean[$i];
	}
	
	$width=2;
	$height=$width*60;
	$height1=$width*66;
	$height2=$width*6;
	$height3=$width*11;
	$width20=$width*20;

	
	$tbl=to_table_bars($start_end,$width,$height1,"rowspan=2");
	$tbl.=to_table_bars($group1,$width,$height,"");
	$tbl.=to_table_bars($mid,$width,$height1,"rowspan=2");
	$tbl.=to_table_bars($group2,$width,$height,"");
	$tbl.=to_table_bars($start_end,$width,$height1,"rowspan=2");
	$tbl="<tr><td><div style='display:block;height:${height}px;width:${width20}px;'></div></td>".$tbl."</tr>";
	$t="<td><div style='display:block;height:${height2}px;width:${width}px;'></div></td>";
	$tbl.="<tr><td rowspan=2 style='text-align:center;font-family:Arial;font-size:${height3}px;'>".$ean[0]."</td>";
	$tbl.=" <td colspan=42 rowspan=2 style='text-align:center;font-family:Arial;font-size:${height3}px;'>$txt1</td>";
	$tbl.=" ";
	$tbl.="<td colspan=42 rowspan=2 style='text-align:center;font-family:Arial;font-size:${height3}px;'>$txt2</td>";
	$tbl.=" </tr>";
	$tbl.="<tr>$t$t$t $t$t$t$t$t $t$t$t</tr>";
	#return $res;
	$tbl="<table cellspacing=0 cellpadding=0>".$tbl."</table>";
	return $tbl;

}







printhead();
/*
$create_querys['barcodes_raw']="CREATE TABLE barcodes_raw (id BIGINT, name VARCHAR(250), code VARCHAR(13), PRIMARY KEY (id),KEY (name),KEY (code))";
$create_querys['barcodes_print']="CREATE TABLE barcodes_print (id BIGINT, count BIGINT, PRIMARY KEY (id))";
*/


$result=$sql->query("SELECT name,count,code FROM barcodes_raw,barcodes_print WHERE count <> 0 AND barcodes_raw.id=barcodes_print.id ORDER BY name");
$sum=0;
$nim=0;
$skip=0;
$lim=$skip+100;
while($row=$sql->fetcha($result))
{
	for($k=0;$k<$row['count'];$k++)
	{
			print "<div class=pb>";
			if(strlen($row['name'])<=111)
				print "<div class=tname style='font-size:3mm;'>";
			else
				print "<div class=tname style='font-size:2.5mm;'>";
			print htmlspecialchars($row['name'],ENT_QUOTES);
			print "</div>";
			print "<div class=barcode>";
			onemorecode($row['code']);
			print " </div>";
//			print "<div style='font-family:arial;font-size:2mm;font-weight:bold;' >";
//			print date("d.m.Y H:i");
//			print "</div>";
			print "</div>\n";
	}
};

print "</body></html>";
?>

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


function printhead($exstyle)
{
	global $encoding;
	print "<html><head><title>Коды</title>";
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
	if (preg_match("/[^0-9]/",$ean))
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
//print "<span style='margins:auto;font-family:EanGnivc;font-size:88px'>";

print ean_gnivc($code);

//print "</span>";

}


function to_table_bars($v,$width,$height,$add)
{
	$tbl=preg_replace('/0/','!',$v);
	$tbl=preg_replace('/1/','~',$tbl);
	//$tbl=preg_replace('/~/',"<td $add style='margin:0px;border-width:0px;padding:0px;background-color:black'><div style='display:block;width:${width}px;height:${height}px;'></div></td>",$tbl);
	//$tbl=preg_replace('/!/',"<td $add style='margin:0px;border-width:0px;padding:0px;background-color:white'><div style='display:block;width:${width}px;height:${height}px;'></div></td>",$tbl);
	$tbl=preg_replace('/~/',"<td $add style='margin:0px;padding:0px;background-color:black'><img src='black.png' style='width:${width}px;height:${height}px;'></td>",$tbl);
	$tbl=preg_replace('/!/',"<td $add style='margin:0px;padding:0px;background-color:white'><img src='white.png' style='width:${width}px;height:${height}px;'></td>",$tbl);
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

if($_GET['h']==40)
{
	$exstyle['.pb']['height']='40mm';
	
	$exstyle['.pb>div.code']['height']='25mm';
	$exstyle['.pb>div.code']['font-size']='18mm';
	$exstyle['.pb>div.code']['margin-top']='0.5mm';
	$exstyle['.pb>div.code']['margin-left']='-2mm';
//	$exstyle['.pb>div.code']['margin-bottom']='0mm';
	
	$exstyle['.pb>div.text']['height']='17mm';
	$exstyle['.pb>div.text']['font-size']='3.31mm';
	$exstyle['.pb>div.text']['margin-top']='1mm';
	$exstyle['.pb>div.text']['line-height']='3.8mm';
//	$exstyle['.pb>div.text'][]
}

if($_GET['m']==1)
{
	unset($exstyle['.pb']['page-break-before']);
	unset($exstyle['.pb']['page-break-after']);
	$exstyle['.pb']['margin']='5mm';
}


printhead($exstyle);
/*
$create_querys['barcodes_raw']="CREATE TABLE barcodes_raw (id BIGINT, name VARCHAR(250), code VARCHAR(13), PRIMARY KEY (id),KEY (name),KEY (code))";
$create_querys['barcodes_print']="CREATE TABLE barcodes_print (id BIGINT, count BIGINT, PRIMARY KEY (id))";
*/
		
if(!isset($_SESSION['current_task']))$_SESSION['current_task']=0;
$current_task=intval($_SESSION['current_task']);

if($_GET['forceid']!='')
{
	$forceid=" AND barcodes_print.id=".$sql->esc($_GET['forceid'])." ";
}

$result=$sql->query("SELECT barcodes_print.id,barcodes_raw.name,barcodes_print.`count`,barcodes_raw.code FROM barcodes_raw,barcodes_print WHERE count <> 0 AND barcodes_raw.id=barcodes_print.id AND barcodes_print.task=".$current_task.$forceid." ORDER BY name");

$sum=0;
$nim=0;
$skip=0;
$lim=$skip+100;
$lim_labels=$sql->fetch1($sql->query("SELECT `current` FROM `barcodes_counters` WHERE `id`=0"));
$lim_ribbon=$sql->fetch1($sql->query("SELECT `current` FROM `barcodes_counters` WHERE `id`=1"));

while($row=$sql->fetcha($result))
{
	$count=min($row['count'],min($lim_labels,$lim_ribbon));
//	print $count;
	if($count==0)break;
	for($k=0;$k<$count;$k++)
	{
			print "<div class=pb>";
			print "<div class=text >";
			print "<div>";
			print htmlspecialchars($row['name'],ENT_QUOTES);
			print "</div>";
			print "</div>";
			print "<div class=code >";
			onemorecode($row['code']);
			print " </div>";
			//print "<div style='font-family:arial;font-size:10px' >";
			//print date("d.m.Y h:m");
			//print "</div>";
			print "</div>";
	}
	$sql->query("UPDATE `barcodes_print` SET `printed`=".$count." WHERE `id`=".$row['id']." AND `task`=".$current_task);
	$lim_labels-=$count;
	$lim_ribbon-=$count;
	break;
};

print "</body></html>";
?>
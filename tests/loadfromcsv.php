<?php
// args('get'):
//action=(view|edit|submit|editall|submitall|add|delete|clear)default:view
//newaction=(view|editall)
//f_var - filters
//o_var - sort order and column
//table
//ini_set("mbstring.func_overload","0");
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once("etc/dbsettings.php");


setlocale(LC_ALL,'ru_RU.UTF8');
$conn=mysql_connect(C_SQL_SERVER, C_SQL_USER, C_SQL_PASS)
        or die("Could not connect: " . mysql_error());
mysql_query("use `".mysql_escape_string(C_SQL_DB)."`",$conn);
mysql_query("SET NAMES 'UTF8'",$conn) or die (mysql_error());
mysql_query("SET CHARACTER SET 'UTF8'",$conn) or die (mysql_error());
$encoding='<meta http-equiv=content-type content="text/html; charset=UTF-8">';




//ret_time_limit(60*10);//////////////////////////////////////WARNING

$host  = $_SERVER['HTTP_HOST'];
$extra = $_SERVER['PHP_SELF'];
$selfurl=$returnpoint="http://$host$extra";

function printhead($table)
{
	global $encoding;
	print "<html><head><title>Editing table: $table</title><link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\">$encoding</head><body>";
};

$editmode='user';

$csvencoding=$_GET['csvencoding'];
if($csvencoding=='')$csvencoding='UTF-8';
$csvdelimiter=$_GET['csvdelimiter'];
if($csvdelimiter=='')$csvdelimiter=',';
$decimaldot=$_GET['decimaldot'];
if($decimaldot=='')$decimaldot='.';


$table=$_GET['table'];
if($table=='')die("Invalid URL. 'table' argument required.");
$returnpoint.="?table=$table";
$csv_file=$_GET['file'];
if($csv_file=='')die("Invalid URL. 'file' argument required.");

////////////////////////////////// STRUCTURE INVESTIGATION //////////////////////////////
$result=mysql_query("SHOW COLUMNS FROM `".mysql_escape_string($table).'`',$conn);
if(!$result)die("error: ".mysql_error());
$i=0;
$have_primary_key=false;
while($row=mysql_fetch_array($result,MYSQL_ASSOC))
{
	$friendlycolnames[$row['Field']]=$row['Field'];
	$indexcolumns[$row['Field']]=$row['Key'];
	$columntypes[$row['Field']]=$row['Type'];
	if($indexcolumns[$row['Field']]=='PRI')$have_primary_key=true;
	$tablecolumns[$i++]=$row['Field'];
};
//////////////////////////////////////////////////////////////////////////////////////////

function csv_split($str)
{
	global $csvdelimiter,$debugdump;

	$entered_quotes=false;
	$ind=0;
	$res='';
	$coll='';
	$len=strlen($str);

	for($k=0;$k<$len;$k++)
	{

		if(($str{$k}==$csvdelimiter)&($entered_quotes==false))
		{
			$res[$ind]=$coll;
			$ind++;
			$coll='';
			continue;
		};
		if(($str{$k}=='"')&($entered_quotes==false))
		{
			$entered_quotes=true;
			continue;
		};
		if(($str{$k}=='"')&($entered_quotes==true))
		{
			if($k==($len-1))
			{
				$res[$ind]=$coll;
				$ind++;
				return $res;
			};
			if(($k<($len-1))&($str{$k+1}=='"'))
			{
				$coll.=$str{$k};
				$k++;
				continue;
			};
			
			$entered_quotes=false;
			continue;
		};
		$coll.=$str{$k};
	};
	$res[$ind]=$coll;

	return $res;
};




if($_POST['table']!='')
{
	$table=$_POST['table'];
	$csv_file=$_POST['file'];
	$file=fopen($csv_file,'r');
	$first_l=0;
	$ck=0;
	if($_POST['clear']=='1')
	{
		$res=mysql_query('SHOW CREATE TABLE `'.mysql_escape_string($table).'`');
		if($row=mysql_fetch_array($res,MYSQL_NUM))$req=$row[1];
		if($req !='')
		{
			mysql_query("DROP TABLE `".mysql_escape_string($table)."`",$conn);
			mysql_query($req,$conn);
			print $req;
		}else die('Failed to fetch create statement');
	};
	while($str=fgets($file))
	{
		$values=csv_split(trim(iconv($csvencoding,'UTF-8',$str)));
		$vlist='';
		if($first_l==0)
		{
			reset($values);
			foreach($values as $each)$ck++;
			reset($values);
			$first_l++;
		};
		$first_p=0;
		foreach($tablecolumns as $val => $col)
		 {
		 	if($first_p!=0)
		 	{
		 		$vlist.=',';
		 	}else{$first_p++;};
		 	$u='';
		 	for($k=0;$k<$ck;$k++)if($_POST["c$k"]==$col)$u=$values[$k];
		 	if($u=='')$u=$_POST["a_$col"];
			if(($decimaldot!='.')&&($columntypes[$col]=='double'))$u=preg_replace("/[$decimaldot]/",'.',$u);
		 	$u=mysql_escape_string($u);

		 	$vlist.="'$u'";
		 };
		 reset($tablecolumns);
		 $query="INSERT INTO `".mysql_escape_string($table)."` VALUES ($vlist)";
		 mysql_query($query,$conn);
//		 print $query."<br>";
	};
	//header("Location: http://$host/");
	print $query;
	exit();
};



printhead($table);
print "<table><tr><form id=encanddelim action='http://$host$extra' method=get><input type=hidden name=file value='$csv_file'><input type=hidden name=table value='$table'><td>Кодировка: <select name=csvencoding onchange='document.getElementById(\"encanddelim\").submit();'><option value='UTF-8'";
if($csvencoding=='UTF-8')print " selected";
print ">UTF-8</option><option value='Windows-1251'";
if($csvencoding=='Windows-1251')print " selected";
print ">Windows-1251</option></select></td><td>Разделитель:<select name=csvdelimiter onchange='document.getElementById(\"encanddelim\").submit();'><option value=','";
if($csvdelimiter==',')print " selected";
print  ">,</option><option value=';'";
if($csvdelimiter==';')print " selected";
print ">;</option></select></td>";
print "<td>Дробная часть отделяется:<select name=decimaldot onchange='document.getElementById(\"encanddelim\").submit();'><option value=','";
if($decimaldot==',')print " selected";
print  ">,</option><option value='.'";
if($decimaldot=='.')print " selected";
print ">.</option></select></td>";
print "</form></tr></table>";
print "<table>";
$file=fopen($csv_file,'r');
print "<tr><form action='' method='post'><input type=hidden name='table' value='$table'><input type=hidden name='file' value='$csv_file'>";

$first_l=0;
$ck=0;
while($str=fgets($file))
{
	$values=csv_split(trim(iconv($csvencoding,'UTF-8',$str)));
	if($first_l==0)
	{
		foreach($values as $each)
		{
			print "<td><select name='c$ck'>";$ck++;
			print "<option value='--'></option>";
			foreach($tablecolumns as $col)print "<option value='$col'>".htmlspecialchars($friendlycolnames[$col])."</option>";
			reset($tablecolumns);
			print "</select></td>";
		};
		reset($values);
		print "</tr>";
		$first_l++;
	};
	print "<tr>";
	foreach($values as $each) print "<td>".htmlspecialchars($each)."</td>";
	print "</tr>";
};


print "</table><p>Default values for other fields</p><table bordercolor=\"#FFFFFF\">";
foreach($tablecolumns as $col)print "<tr><td>".htmlspecialchars($friendlycolnames[$col])."</td><td>=</td><td><input type=text name='a_$col'></td></tr>"; 
print "</table><br><input type=checkbox name=clear value=1>Очистить таблицу<br><input type=submit value='Загрузить'></form>";
fclose($file);
print "</body></html>";
//header("Location: http://$host/loadfromcsv.php?table=$table&file=$uploadfile");

?>
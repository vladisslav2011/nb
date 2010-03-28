<?php
$defaulttablecharset="utf8";
$defaulttablecollation="utf8_general_ci";
$dbname=$_GET['dbname'];
$quoteaction=<<<XXX
	<form action="dbadmin.php">
	<input type='hidden' name='action' value='quote'>
	<input type='hidden' name='dbname' value='$dbname'>
	<input type='text' size=100 name='quote' value=''>
	<input type='submit' value='Quote'>
	</form>
XXX;
function printhead()
{
	$encoding='<meta http-equiv=content-type content="text/html; charset=UTF-8">';
	print "<html><head><title>Database admin module</title> <link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\">$encoding</head><body><a href='http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."'>To DB list</a><br>";
};



setlocale(LC_ALL,'ru_RU.UTF8');
$action=$_GET['action'];
if($action=='')$action=$_POST['action'];
$conn=mysql_connect("localhost", "root", "")
        or die("Could not connect: " . mysql_error());
mysql_query('SET NAMES utf8',$conn) or die (mysql_error());
mysql_query('SET CHARACTER SET utf8',$conn) or die (mysql_error());

if($action=='')
{
	$result=mysql_query("SHOW DATABASES;",$conn);
	printhead();
	print "<p>databases</p><table>";
	$i=0;
	while($row=mysql_fetch_array($result,MYSQL_NUM))
	{
		print "<tr><th>$i</th><td><a href='?action=showtables&dbname=".$row[0]."'>".$row[0]."</a></td><td><a href=\"?action=dropdb&dbname=".$row[0]."\">X</a></td></tr>";
		$i++;
	};
	print "</table>";
	print "<form action=\"dbadmin.php\">";
	print "<input type='hidden' name='action' value='createdb'>";
	print "<input type='text' name='dbname' value='new_db'>";
	print "<input type='submit' value='Create'>";
	print "</form>";
	print $quoteaction;
	print "</body></html>";
};

if($action=='quote')
{
	if($_GET['dbname']!='')
		mysql_query('use '.$_GET['dbname'],$conn) or die("Error: " . mysql_error());
	mysql_query('SET NAMES utf8',$conn) or die (mysql_error());
	mysql_query('SET CHARACTER SET utf8',$conn) or die (mysql_error());
	$result=mysql_query($_GET['quote'],$conn) or die("Error: " . mysql_error());
	printhead();
	$qqq=htmlspecialchars($_GET['quote'],ENT_QUOTES);
	print "<p>Quote results:</p><p>$qqq</p><table>";
	$i_vc=0;$hprinted=0;
	while($row=mysql_fetch_array($result,MYSQL_ASSOC))
	{
		if($hprinted==0)
		{
			$row1=$row;
			print "<tr><th></th>";
			while (list($k,$v) = each($row1))
				print "<th>$k</th>";
			print "</tr>";
			$hprinted=1;
		};
		print "<tr><th>$i_vc</th>";
		while (list($k,$v) = each($row))
			print "<td>$v</td>";
		print "</tr>";$i_vc++;
	};
	print "</table>";
	print $quoteaction;
	print "</body></html>";
};

if($action=='createdb')
{
	if($_GET['dbname']=='') die('You have to enter db name!');
	$result=mysql_query("CREATE DATABASE `".$_GET['dbname']."` CHARACTER SET ".$defaulttablecharset." COLLATE ".$defaulttablecollation,$conn);
	$host  = $_SERVER['HTTP_HOST'];
	$extra = $_SERVER['PHP_SELF'];
	header("Location: http://$host$extra");
};

if($action=='dropdb')
{
	if($_GET['dbname']=='') die('Wrong URL');
	$result=mysql_query("DROP DATABASE `".$_GET['dbname']."`",$conn);
	$host  = $_SERVER['HTTP_HOST'];
	$extra = $_SERVER['PHP_SELF'];
	header("Location: http://$host$extra");
};

if($action=='showtables')
{
	if($_GET['dbname']=='') die('Wrong URL');
	$tempurl="&dbname=".$_GET['dbname'];
	$result=mysql_query("USE `".$_GET['dbname']."`;");
	$result=mysql_query("SHOW TABLES;",$conn);
	printhead();
	print "<p>".$_GET['dbname']." :tables</p><table>";
	while($row=mysql_fetch_array($result,MYSQL_NUM))
	{
		print "<tr><td><a href='dbadmin.php?action=viewtable$tempurl&tblname=$row[0]'>$row[0]</a></td><td><a href=\"dbadmin.php?action=droptable$tempurl&tblname=".$row[0]."\">X</a></td></tr>";
	};
	print "</table>";
	print "<form method='post' action=\"dbadmin.php\">";
	print "<input type='hidden' name='action' value='createtable'>";
	print "<input type='hidden' name='dbname' value=".$_GET['dbname'].">";
	print "<input type='text' name='tblname' value='newtbl'>";
	print "<textarea name='columns' cols='40' rows='7'></textarea>";
	print "<input type='submit' value='Create'>";
	print "</form>";
	print $quoteaction;
	print "<br><a href='?action=backupdb&dbname=$dbname'>BACKUP</a>";
	print "<br><form method=post action='?action=restoredb&dbname=$dbname' enctype='multipart/form-data'><input type=file name=file1><input type=submit></form>";
	print "</body></html>";
};

if($action=='droptable')
{
	if($_GET['dbname']=='') die('Wrong URL');
	if($_GET['tblname']=='') die('Wrong URL');
	$result=mysql_query("DROP TABLE `".$_GET['dbname']."`.`".$_GET['tblname']."`",$conn);
	$result=mysql_query("DROP VIEW `".$_GET['dbname']."`.`".$_GET['tblname']."`",$conn);
	$host  = $_SERVER['HTTP_HOST'];
	$extra = $_SERVER['PHP_SELF'];
	$extra .= '?action=showtables&dbname='.$_GET['dbname'];
	header("Location: http://$host$extra");
};


if($action=='createtable')
{
	if($_POST['dbname']=='') die('You have to enter db name!');
	$query="CREATE TABLE `".$_POST['dbname']."`.`".$_POST['tblname']."` ".$_POST['columns'];
	$result=mysql_query($query,$conn);
	if (!$result) die("Error: ". mysql_error());
	$host  = $_SERVER['HTTP_HOST'];
	$extra = $_SERVER['PHP_SELF'];
	$extra .= '?action=showtables&dbname='.$_POST['dbname'];
	header("Location: http://$host$extra");
};

if($action=='viewtable')
{
	if($_GET['dbname']=='') die('Wrong URL');
	if($_GET['tblname']=='') die('Wrong URL');
	$query="SELECT * FROM `".$_GET['dbname']."`.`".$_GET['tblname']."`";
	$result=mysql_query($query,$conn);
	if (!$result) die("Error: ". mysql_error());
	$hpr=0;$i_vc=0;
	printhead();
	print "<table>\n";
	while($row=mysql_fetch_array($result,MYSQL_ASSOC))
	{
		if($hpr==0)
		{
			$row1=$row;
			print "<tr><th></th>";
			while (list($k,$v) = each($row1))
				print "<th>$k</th>";
			print "</tr>";
			$hpr=1;
		};
		print "<tr><th>$i_vc</th>";
		while (list($k,$v) = each($row))
			print "<td>".htmlspecialchars($v, ENT_QUOTES)."</td>";
		print "</tr>";$i_vc++;
		
	};
	if($hpr==0)
	{
		$query="SHOW COLUMNS FROM `".$_GET['dbname']."`.`".$_GET['tblname']."`";
		$result=mysql_query($query,$conn);
		if (!$result) die("Error: ". mysql_error());
		print "<tr>";
		while($row=mysql_fetch_array($result,MYSQL_NUM))
			print "<td>$row[0]</td>";
		print "</tr>";
		
	};
	print "</table></body></html>";
};

if($action=='backupdb')
{
	set_time_limit(60*10);//////////////////////////////////////WARNING
	if($_GET['dbname']=='')die ("dbname required!");
	if($_GET['dbname']!='')
		mysql_query('USE `'.$_GET['dbname']."`",$conn) or die("Error: " . mysql_error());
	header("Content-Type: text/plain; charset=UTF-8");
	header("Content-Disposition: attachment; filename=$dbname.php");
	$query="SHOW TABLES";
	$result=mysql_query($query,$conn);
	print "<?php\n";
	while($row=mysql_fetch_array($result,MYSQL_NUM))
	{
		$table=$row[0];
		$result1=mysql_query("SHOW CREATE TABLE `$table`",$conn);
		if(!$result1)continue;
		$row1=mysql_fetch_array($result1,MYSQL_NUM);
		if(!$row)continue;
		$createquery=$row1[1];
		$createquery=str_replace('\\','\\\\',$createquery);
		$createquery=str_replace('"','\\"',$createquery);
		$createquery=str_replace('$','\\$',$createquery);
		print "mysql_query(\"$createquery\",\$conn);\n";
		mysql_free_result($result1);
		$result1=mysql_query("SELECT * FROM `$table`",$conn);
		if(!$result1)continue;
		while($row1=mysql_fetch_array($result1,MYSQL_NUM))
		{
			$str="mysql_query(\"INSERT INTO `$table` VALUES (";
			$str1='';
			$k=1;
			while(list($ind,$value) = each($row1))//$row as $value)
			{
				if($k>1)$str1.=',';
				$k++;
				$str1.="'".mysql_escape_string($value)."'";
			};
			$str1=str_replace('\\','\\\\',$str1);
			$str1=str_replace('"','\\"',$str1);
			$str1=str_replace('$','\\$',$str1);
			$str.=$str1.')",$conn);'."\n";
			print $str;
		};
		mysql_free_result($result1);
	};
	print '?>';

}

if($action=='restoredb')
{
	if($_GET['dbname']=='')die ("dbname required!");
	if($_GET['dbname']!='')
		mysql_query('USE `'.$_GET['dbname']."`",$conn) or die("Error: " . mysql_error());
	mysql_query('SET NAMES utf8') or die (mysql_error());
	mysql_query('SET CHARACTER SET utf8') or die (mysql_error());
	$uploaddir = 'uploads/';
	$uploadfile = $uploaddir . basename($_FILES['file1']['name']);
	if (move_uploaded_file($_FILES['file1']['tmp_name'], $uploadfile))
	{
		$fn_ext= preg_replace( '/.+[.]/', '', basename($_FILES['file1']['name']) );
		if($fn_ext=='php')
		{
			include($uploadfile);
			header("Location: http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?action=showtables&dbname=$dbname");
			exit();
		};
	};
	die('Failure');

}

mysql_close($conn);
?>
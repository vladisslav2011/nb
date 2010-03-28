<?php
$sql=new sql;
$sql->connect(C_SQL_SERVER.(C_SQL_PORT==''?'':':'.C_SQL_PORT),C_SQL_USER,C_SQL_PASS);
if(!$sql->query("USE `".C_SQL_DB."`"))
{
	$sql->query("CREATE DATABASE `".C_SQL_DB."` CHARACTER SET 'utf8'") or die($sql->err());
	$sql->query("USE `".C_SQL_DB."`") or die($sql->err());
};
$sql->query("SET character_set_client='utf8'");
$sql->query("SET character_set_connection='utf8'");
$sql->query("SET character_set_results='utf8'");



?>
<?php
require_once("../etc/dbsettings.php");
require_once("../sql/my.php");
require_once("../lib/base_connect.php");
print $sql->q1("SELECT `value` FROM `test_long_run` WHERE id=1 LIMIT 1");




?>
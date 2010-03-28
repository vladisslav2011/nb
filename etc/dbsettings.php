<?php
define(C_SQL_SERVER,'localhost');
define(C_SQL_PORT,'');
define(C_SQL_USER,'root');
define(C_SQL_PASS,'');
define(C_SQL_DB,'nb_dev');

define('TABLE_NAME_PREFIX','');
define('TABLE_META_TREE',TABLE_NAME_PREFIX.'%tree');
define('TABLE_META_TREE_TMP',TABLE_META_TREE.'_tmp');
define('TABLE_META_TREE_SELECTIONS',TABLE_META_TREE.'_selections');
define('TABLE_META_I18N',TABLE_NAME_PREFIX.'*lnames');
define('TABLE_META_SETTINGS',TABLE_NAME_PREFIX.'*settings');

define('TABLE_META_USERS',TABLE_NAME_PREFIX.'*users');
define('TABLE_META_GROUPS',TABLE_NAME_PREFIX.'*uid_group');
define('TABLE_META_PERMISSIONS',TABLE_NAME_PREFIX.'*perm');
define('TABLE_META_AUTHLOG',TABLE_NAME_PREFIX.'*authlog');





?>
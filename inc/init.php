<?php
error_reporting(E_ALL & ~E_NOTICE);

include(__DIR__ . '/load_flourish.php');
include(__DIR__ . '/load_app.php');

require(__DIR__ . '/path.php');
require(__DIR__ . '/config.php');
require(__DIR__ . '/core.php');

$db = new fDatabase('mysql', DB_NAME, DB_USER, DB_PASS, DB_HOST);
fORMDatabase::attach($db);
fAuthorization::setLoginPage(SITE_BASE . '/login/');

<?php
include_once(__DIR__ . '/inc/init.php');

$name = 'xjia';
$h = acm_userpass_hash('abc123');
$email = 'xjia@cs.sjtu.edu.cn';
$display_name = 'Xiao Jia';

$db->translatedQuery(
  'INSERT INTO users(name,pass,salt,iter,status,email,display_name,created_at,updated_at)' .
  'VALUES(%s,%s,%s,%i,1,%s,%s,now(),now())',
  $name, $h['pass'], $h['salt'], $h['iter'], $email, $display_name
);

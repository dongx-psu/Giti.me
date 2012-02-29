<?php
include_once(__DIR__ . '/inc/init.php');

if (fAuthorization::checkLoggedIn()) {
	include(__DIR__ . '/themes/'. SITE_THEME .'/tpl/manage.php');
}
else {
	fURL::redirect(login_page());
}
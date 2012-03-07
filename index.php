<?php
include_once(__DIR__ . '/inc/init.php');

if (fAuthorization::checkLoggedIn()) {
	if (!check_permission($db))
		exit("This service is not open to you!!");
	include(__DIR__ . '/themes/'. SITE_THEME .'/tpl/manage.php');
}
else {
	fURL::redirect(login_page());
}
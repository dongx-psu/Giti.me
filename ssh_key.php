<?php
include_once(__DIR__ . '/inc/init.php');
	
fAuthorization::requireLoggedIn();
if (!check_permission($db))
	exit("This service is not open to you!!");
if (fRequest::isGet()) {
	if (fRequest::get('id') != NULL) {
		try {
			$ssh_key = new SshKey(fRequest::get('id'));
			if ($ssh_key->getUserId() != get_current_user_id()) {
				throw new fValidationException('user_id mismatched.');
			}
			exit(make_response($ssh_key));
		} catch (fException $e) {
			exit(return_error($e));
		}
	} else {
		exit(fetch_data($db));
	}
} else if (fRequest::isPost()) {
	$action = fRequest::get('method');
	if ($action == 'create') {
		try {
			$db->query('BEGIN');
			$title = decode(fRequest::get('title'));
			$key = decode(fRequest::get('key'));
			$raw_key = preg_replace("/\s/", "", $key);
			$raw_title = preg_replace("/\s/", "", $title);
			if (empty($raw_title) || empty($raw_key)) {
				throw new fValidationException('Title or key is empty.');
			} else if (!valid($key)) {
				throw new fValidationException('SSH Key invalid.');
			} else if (fetch_data_by_key($db, $raw_key)) {
				throw new fValidationException('The public key has existed.');
			}
			$ssh_key = new SshKey();
			$ssh_key->setTitle($title);
			$ssh_key->setSshKey($key);
			$ssh_key->setRawKey($raw_key);
			$ssh_key->setUserId(get_current_user_id());
			$ssh_key->store();
			refresh_admin();
			add_pub_key($ssh_key);
			$db->query('COMMIT');
			exit(make_response($ssh_key));
		} catch (fException $e) {
			$db->query('ROLLBACK');
			exit(return_error($e));
		}
	} else if ($action == 'update') {
		try {
			$db->query('BEGIN');		
			$ssh_key = new SshKey(fRequest::get('id'));
			$title = decode(fRequest::get('title'));
			$key = decode(fRequest::get('key'));
			$raw_key = preg_replace("/\s/", "", $key);
			$raw_title = preg_replace("/\s/", "", $title);
			$tmp_key = $ssh_key->getRawKey();
			if (empty($raw_title) || empty($raw_key)) {
				throw new fValidationException('Title or key is empty.');
			} else if (!valid($key)) {
				throw new fValidationException('SSH Key invalid.');
			} else if ($ssh_key->getUserId() != get_current_user_id()) {
				throw new fValidationException('user_id mismatched.');
			} else if ($raw_key != $tmp_key && fetch_data_by_key($db, $raw_key)) {
				throw new fValidationException('The public key has existed.');
			}
			$ssh_key->setTitle($title);
			$ssh_key->setSshKey($key);
			$ssh_key->setRawKey($raw_key);
			$ssh_key->store();
			refresh_admin();
			if ($key != $tmp_key) update_pub_key($ssh_key);
			$db->query('COMMIT');
			exit(make_response($ssh_key));
		} catch (fException $e) {
			$db->query('ROLLBACK');
			exit(return_error($e));
		}
	} else if ($action == 'delete') {
		try {
			$db->query('BEGIN');
			$ssh_key = new SshKey(fRequest::get('id'));
			if ($ssh_key->getUserId() != get_current_user_id())
				throw new fValidationException('user_id mismatched.');
			refresh_admin();
			remove_pub_key($ssh_key);
			$ssh_key->delete();
			$db->query('COMMIT');
			exit(fJSON::encode(array('success' => true)));
		} catch (fException $e) {
			$db->query('ROLLBACK');
			exit(return_error($e));
		}
	}
} else {
	exit('METHOD Denied!!');
}

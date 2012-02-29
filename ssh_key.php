<?php
include_once(__DIR__ . '/inc/init.php');
	
fAuthorization::requireLoggedIn();
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
			$title = title_decode(fRequest::get('title'));
			$key = key_decode(fRequest::get('key'));
			if (empty($title) || empty($key)) {
				throw new fValidationException('Title or key is empty.');
			} else if (fetch_data_by_key($db, $key)) {
				throw new fValidationException('The public key has existed.');
			}
			$db->query('BEGIN');		
			$ssh_key = new SshKey();
			$ssh_key->setTitle($title);
			$ssh_key->setSshKey($key);
			$ssh_key->setUserId(get_current_user_id());
			$ssh_key->store();
			//add_pub_key($ssh_key);
			$db->query('COMMIT');
			exit(make_response($ssh_key));
		} catch (fException $e) {
			$db->query('ROLLBACK');
			exit(return_error($e));
		}
	} else if ($action == 'update') {
		try {
			$ssh_key = new SshKey(fRequest::get('id'));
			$title = title_decode(fRequest::get('title'));
			$key = key_decode(fRequest::get('key'));
			$tmp_key = $ssh_key->getSshKey();
			if (empty($title) || empty($key)) {
				throw new fValidationException('Title or key is empty.');
			} else if ($ssh_key->getUserId() != get_current_user_id()) {
				throw new fValidationException('user_id mismatched.');
			} else if ($key != $tmp_key && fetch_data_by_key($db, $key)) {
				throw new fValidationException('The public key has existed.');
			}
			$db->query('BEGIN');
			$ssh_key->setTitle($title);
			$ssh_key->setSshKey($key);
			$ssh_key->store();
			//if ($key != $tmp_key) update_pub_key($ssh_key);
			$db->query('COMMIT');
			exit(make_response($ssh_key));
		} catch (fException $e) {
			$db->query('ROLLBACK');
			exit(return_error($e));
		}
	} else if ($action == 'delete') {
		try {
			$ssh_key = new SshKey(fRequest::get('id'));
			$db->query('BEGIN');
			if ($ssh_key->getUserId() != get_current_user_id())
				throw new fValidationException('user_id mismatched.');
			//remove_pub_key($ssh_key);
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

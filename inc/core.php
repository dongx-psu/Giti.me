<?php
function now()
{
	return date('Y-m-d H:i:s');
}

function get_current_username()
{
	$token = fAuthorization::getUserToken();
	return $token['username'];
}

function get_current_user_id()
{
	$token = fAuthorization::getUserToken();
	return $token['user_id'];
}

function get_current_user_display_name()
{
	$token = fAuthorization::getUserToken();
	return $token['display_name'];
}

function fetch_data($db)
{
	$data = fRecordSet::build('SshKey');
	foreach($data as $row) {
		$result[] = array(
			'id' => $row->getId(),
			'user_id' => $row->getUserId(),
			'title' => $row->getTitle(),
			'ssh_key' => $row->getSshKey()
		);
	}
	
	return fJSON::encode(array(
					'success' => true,
					'display_name' => get_current_user_display_name(),
					'data' => $result
			));
}

function fetch_data_by_key($db, $ssh_key)
{
	$data = fRecordSet::build(
		'SshKey',
		array('ssh_key=' => $ssh_key)
	);
	if ($data->count() != 0) return true;
	else return false;
}

function make_response($ssh_key) {
	return fJSON::encode(array(
			'success' => true,
			'data' => array(
				'id' => $ssh_key->getId(),
				'user_id' => $ssh_key->getUserId(),
				'title' => $ssh_key->getTitle(),
				'ssh_key' => $ssh_key->getSshKey()
			)));
}

function return_error($e) {
	return fJSON::encode(array(
				'success' => false,
				'message' => $e->getMessage()
			));
}

function title_decode($title) {
	return htmlspecialchars(base64_decode(rawurldecode($title)), ENT_QUOTES);
}

function key_decode($key) {
	return htmlspecialchars(base64_decode(rawurldecode($key)), ENT_QUOTES);
}

function add_pub_key($ssh_key)
{
	$filename = get_current_username() . '@' . $ssh_key->getId() . '.pub';
	$file = fFile::create(KEY_DIR . $filename, $ssh_key->getSshKey());
	chdir(KEY_DIR);
	exec("git add .");
	exec("git commit -m 'Add public key " . $filename . "'");
	exec("git push");
}

function update_pub_key($ssh_key)
{
	$filename = get_current_username() . '@' . $ssh_key->getId() . '.pub';
	$file = new fFile(KEY_DIR . $filename);
	$file->write($ssh_key->getSshKey());
	chdir(KEY_DIR);
	exec("git add .");
	exec("git commit -m 'Update public key " . $filename . "'");
	exec("git push");
}

function remove_pub_key($ssh_key)
{
	$filename = get_current_username() . '@' . $ssh_key->getId() . '.pub';
	chdir(KEY_DIR);
	exec("git rm " . $filename);
	exec("git commit -m 'Remove public key " . $filename . "'");
	exec("git push");
}

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

function check_permission($db)
{
	$result = $db->translatedQuery('SELECT * FROM permission WHERE user_id=%i', get_current_user_id());
	if ($result->countReturnedRows() != 0) return true;
	else return false;
}

function fetch_data($db)
{
	$data = fRecordSet::build(
		'SshKey',
		array('user_id=' => get_current_user_id())
	);
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

function fetch_data_by_key($db, $raw_key)
{
	$data = fRecordSet::build(
		'SshKey',
		array('raw_key=' => $raw_key)
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

function decode($content) {
	return htmlspecialchars(base64_decode(rawurldecode($content)), ENT_QUOTES);
}

function valid($key) {
	return (preg_match("/^ssh-(rsa|dss)/", $key) && !preg_match("/[ ]{2,}/", $key));
}

function refresh_admin($key) {
	chdir(ADMIN_DIR);
	exec("git pull");
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

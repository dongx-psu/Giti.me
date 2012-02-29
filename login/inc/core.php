<?php
function login_authenticate($db, $username, $password)
{
  $result = $db->translatedQuery(
    'SELECT id,name,pass,salt,iter,email,display_name FROM users WHERE name=%s AND status=1', $username);
  $num_of_rows = $result->countReturnedRows();
  if ($num_of_rows > 0) {
    $row = $result->fetchRow();
    if (acm_userpass_check($row, $password)) {
	  fAuthorization::setUserToken(array(
	  	'user_id' => $row['id'],
		'display_name' => $row['display_name'],
		'username' => $row['name']
	  ));
      /*fSession::set('current_user', array(
        'user_id' => $row['id'],
        'username' => $row['name'],
        'email' => $row['email'],
        'display_name' => $row['display_name']
      ));*/
      return true;
    } else {
      return false;
    }
  }
  return false;
}

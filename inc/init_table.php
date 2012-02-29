<?php
    $db_type = "mysql";
    $db_host = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "git";
    $db = new PDO($db_type.":host=".$db_host.";dbname=".$db_name,
                  $db_username, $db_password);
    $temp = "CREATE TABLE ssh_keys (id bigint unsigned primary key auto_increment,
                                    user_id bigint unsigned,
                                    title varchar(100),
                                    ssh_key text)";
    $db->exec($temp);
?>
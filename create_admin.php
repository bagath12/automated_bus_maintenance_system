<?php // Run once: php create_admin.php yourname yourpassword
require __DIR__.'/common.php';
if(PHP_SAPI!=='cli'||$argc<3)die("Usage: php create_admin.php <username> <password>\n");
$pdo->prepare('REPLACE INTO admins(username,password_hash) VALUES(?,?)')->execute([$argv[1],password_hash($argv[2],PASSWORD_DEFAULT)]);echo "Admin saved.\n";

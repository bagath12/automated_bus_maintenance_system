<?php require __DIR__.'/common.php';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){check();$q=$pdo->prepare('SELECT * FROM admins WHERE username=?');$q->execute([$_POST['u']??'']);$a=$q->fetch();
 if($a&&password_verify($_POST['p']??'',$a['password_hash'])){session_regenerate_id(true);$_SESSION['admin']=$a['id'];header('Location: index.php');exit;}
 $err='Wrong username or password.';}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin login</title><link rel="stylesheet" href="css/base.css"><link rel="stylesheet" href="css/components.css"><link rel="stylesheet" href="css/login.css"></head>
<body class="login"><form method="post"><?=logo(72)?><h1>Lion Transport</h1><p>Sign in to manage bus servicing.</p><?php if($err)echo '<p class="err">'.e($err).'</p>';echo tok();?>
<label>Username<input name="u" required autofocus></label><label>Password<input name="p" type="password" required></label><button>Sign in</button></form></body></html>

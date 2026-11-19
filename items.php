<?php require __DIR__.'/common.php';auth();check();
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a']??'';$km=$_POST['km']!==''?(int)$_POST['km']:null;$d=$_POST['d']!==''?(int)$_POST['d']:null;
 if($a==='add')$pdo->prepare('INSERT INTO items(name,default_km,default_days) VALUES(?,?,?)')->execute([trim($_POST['n']),$km,$d]);
 if($a==='upd')$pdo->prepare('UPDATE items SET name=?,default_km=?,default_days=? WHERE id=?')->execute([trim($_POST['n']),$km,$d,$_POST['id']]);
 if($a==='del')$pdo->prepare('DELETE FROM items WHERE id=?')->execute([$_POST['id']]);
 header('Location: items.php');exit;}
head('Maintenance items');?>
<form method="post" class="row"><?=tok()?><input type="hidden" name="a" value="add"><input name="n" placeholder="Item (e.g. Engine oil)" required><input name="km" type="number" placeholder="Default interval (km)"><input name="d" type="number" placeholder="Default interval (days)"><button>Add item</button></form>
<table><tr><th>Item</th><th>Every (km)</th><th>Every (days)</th><th></th></tr>
<?php foreach($pdo->query('SELECT * FROM items ORDER BY name') as $i){?><tr><form method="post"><?=tok()?><input type="hidden" name="id" value="<?=$i['id']?>">
<td><input name="n" value="<?=e($i['name'])?>"></td><td><input name="km" type="number" value="<?=e($i['default_km'])?>"></td><td><input name="d" type="number" value="<?=e($i['default_days'])?>"></td>
<td><button name="a" value="upd">Save</button> <button name="a" value="del" class="ghost" onclick="return confirm('Delete this item?')">Delete</button></td></form></tr><?php }?></table><?php foot();

<?php require __DIR__.'/common.php';auth();check();
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a']??'';
 if($a==='add')$pdo->prepare('INSERT INTO buses(bus_number,route,tago_token,current_km) VALUES(?,?,?,?)')->execute([trim($_POST['n']),$_POST['r'],trim($_POST['k']),(float)$_POST['km']]);
 if($a==='upd')$pdo->prepare('UPDATE buses SET bus_number=?,route=?,tago_token=?,current_km=? WHERE id=?')->execute([trim($_POST['n']),$_POST['r'],trim($_POST['k']),(float)$_POST['km'],$_POST['id']]);
 if($a==='del')$pdo->prepare('DELETE FROM buses WHERE id=?')->execute([$_POST['id']]);
 header('Location: buses.php');exit;}
head('Buses');?>
<form method="post" class="row"><?=tok()?><input type="hidden" name="a" value="add"><input name="n" placeholder="Bus number" required><input name="r" placeholder="Route"><input name="k" placeholder="TagoIO device token"><input name="km" type="number" step="0.1" value="0" title="Current km"><button>Add bus</button></form>
<table><tr><th>Bus</th><th>Route</th><th>TagoIO token</th><th>Km</th><th>Last sync</th><th></th></tr>
<?php foreach($pdo->query('SELECT * FROM buses ORDER BY bus_number') as $b){?><tr><form method="post"><?=tok()?><input type="hidden" name="id" value="<?=$b['id']?>">
<td><input name="n" value="<?=e($b['bus_number'])?>"></td><td><input name="r" value="<?=e($b['route'])?>"></td><td><input name="k" value="<?=e($b['tago_token'])?>"></td><td><input name="km" type="number" step="0.1" value="<?=e($b['current_km'])?>"></td><td><?=e($b['last_sync']?:'Never')?></td>
<td><button name="a" value="upd">Save</button> <button name="a" value="del" class="ghost" onclick="return confirm('Delete this bus and its records?')">Delete</button></td></form></tr><?php }?></table><?php foot();

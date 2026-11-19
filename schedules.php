<?php require __DIR__.'/common.php';auth();check();
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a']??'';
 if($a==='add'){$ikm=$_POST['ikm']!==''?(int)$_POST['ikm']:null;$id=$_POST['idays']!==''?(int)$_POST['idays']:null;$lk=(float)$_POST['lkm'];[$nk,$nd]=next_vals($lk,$_POST['ldate'],$ikm,$id);
  $pdo->prepare('REPLACE INTO schedules(bus_id,item_id,last_km,last_date,interval_km,interval_days,next_km,next_date) VALUES(?,?,?,?,?,?,?,?)')->execute([$_POST['bus'],$_POST['item'],$lk,$_POST['ldate'],$ikm,$id,$nk,$nd]);
  $pdo->prepare('INSERT INTO history(bus_id,item_id,service_km,service_date,notes) VALUES(?,?,?,?,?)')->execute([$_POST['bus'],$_POST['item'],$lk,$_POST['ldate'],'Previous service']);}
 if($a==='status'){$q=$pdo->prepare('SELECT s.*,b.current_km FROM schedules s JOIN buses b ON b.id=s.bus_id WHERE s.id=?');$q->execute([$_POST['id']]);$s=$q->fetch();
  if($s&&$_POST['status']==='Completed'){$today=date('Y-m-d');[$nk,$nd]=next_vals($s['current_km'],$today,$s['interval_km'],$s['interval_days']);
   $pdo->prepare('INSERT INTO history(bus_id,item_id,service_km,service_date,notes) VALUES(?,?,?,?,?)')->execute([$s['bus_id'],$s['item_id'],$s['current_km'],$today,$_POST['notes']]);
   $pdo->prepare("UPDATE schedules SET last_km=?,last_date=?,next_km=?,next_date=?,status='Pending',reminder_sent=0 WHERE id=?")->execute([$s['current_km'],$today,$nk,$nd,$s['id']]);}
  elseif($s)$pdo->prepare('UPDATE schedules SET status=? WHERE id=?')->execute([$_POST['status'],$s['id']]);}
 if($a==='del')$pdo->prepare('DELETE FROM schedules WHERE id=?')->execute([$_POST['id']]);
 header('Location: schedules.php');exit;}
head('Maintenance schedules');$buses=$pdo->query('SELECT id,bus_number FROM buses ORDER BY bus_number')->fetchAll();$items=$pdo->query('SELECT * FROM items ORDER BY name')->fetchAll();?>
<form method="post" class="row"><?=tok()?><input type="hidden" name="a" value="add">
<select name="bus" required><?php foreach($buses as $b)echo '<option value="'.$b['id'].'">'.e($b['bus_number']).'</option>';?></select>
<select name="item" id="item" required><?php foreach($items as $i)echo '<option value="'.$i['id'].'" data-km="'.e($i['default_km']).'" data-d="'.e($i['default_days']).'">'.e($i['name']).'</option>';?></select>
<input name="lkm" type="number" step="0.1" placeholder="Last service km" required><input name="ldate" type="date" value="<?=date('Y-m-d')?>" required>
<input name="ikm" id="ikm" type="number" placeholder="Repeat every km"><input name="idays" id="idays" type="number" placeholder="or every days"><button>Save schedule</button></form>
<p class="hint">Saving a schedule for a bus and item that already has one replaces it. Leave km or days empty to use only one rule.</p>
<table><tr><th>Bus</th><th>Item</th><th>Last service</th><th>Next due</th><th>Now km</th><th>Status</th><th>Update</th></tr>
<?php foreach($pdo->query('SELECT s.*,b.bus_number,b.current_km,i.name FROM schedules s JOIN buses b ON b.id=s.bus_id JOIN items i ON i.id=s.item_id ORDER BY b.bus_number,i.name') as $s){$st=state($s,$s['current_km']);?>
<tr><td><?=e($s['bus_number'])?></td><td><?=e($s['name'])?></td><td><?=e($s['last_km'])?> km, <?=e($s['last_date'])?></td><td><?=e($s['next_km']?:'-')?> km / <?=e($s['next_date']?:'-')?></td><td><?=e($s['current_km'])?></td>
<td><span class="tag <?=$s['status']==='Due'||$st==='Due'?'Due':($st==='Soon'?'Soon':$s['status'])?>"><?=e($s['status'])?></span></td>
<td><form method="post" class="inline"><?=tok()?><input type="hidden" name="id" value="<?=$s['id']?>"><input type="hidden" name="a" value="status"><select name="status"><?php foreach(['Pending','Due','Completed'] as $o)echo '<option'.($o===$s['status']?' selected':'').'>'.$o.'</option>';?></select><input name="notes" placeholder="Notes"><button>Update</button></form>
<form method="post" class="inline"><?=tok()?><input type="hidden" name="id" value="<?=$s['id']?>"><button name="a" value="del" class="ghost" onclick="return confirm('Remove this schedule?')">Remove</button></form></td></tr><?php }?></table>
<script>const it=document.getElementById('item');function fill(){const o=it.selectedOptions[0];if(!o)return;document.getElementById('ikm').value=o.dataset.km||'';document.getElementById('idays').value=o.dataset.d||'';}it.onchange=fill;fill();</script>
<?php foot();

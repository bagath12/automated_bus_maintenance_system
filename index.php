<?php require __DIR__.'/common.php';head('Dashboard');
if(!empty($_SESSION['flash'])){echo '<p class="note">'.e($_SESSION['flash']).'</p>';unset($_SESSION['flash']);}
$rows=$pdo->query("SELECT s.*,b.bus_number,b.current_km,b.last_sync,i.name FROM schedules s JOIN buses b ON b.id=s.bus_id JOIN items i ON i.id=s.item_id WHERE s.status<>'Completed' ORDER BY b.bus_number")->fetchAll();
$due=$soon=0;foreach($rows as &$r){$r['st']=state($r,$r['current_km']);if($r['st']==='Due')$due++;if($r['st']==='Soon')$soon++;}unset($r);
$n=$pdo->query('SELECT COUNT(*) FROM buses')->fetchColumn();?>
<div class="stats"><div class="stat"><b><?=$n?></b>Buses</div><div class="stat red"><b><?=$due?></b>Due now</div><div class="stat gold"><b><?=$soon?></b>Approaching</div></div>
<form method="post" action="sync.php"><?=tok()?><button>Fetch odometer and check reminders</button></form>
<h2>Needs attention</h2><table><tr><th>Bus</th><th>Service</th><th>Now (km)</th><th>Due at</th><th>State</th></tr>
<?php $any=0;foreach($rows as $r)if($r['st']!=='OK'){$any=1;?><tr><td><?=e($r['bus_number'])?></td><td><?=e($r['name'])?></td><td><?=e($r['current_km'])?></td><td><?=e($r['next_km']?:'-')?> km / <?=e($r['next_date']?:'-')?></td><td><span class="tag <?=$r['st']?>"><?=$r['st']==='Due'?'Due':'Approaching'?></span></td></tr><?php }
if(!$any)echo '<tr><td colspan="5">Nothing due. Add schedules under Schedules.</td></tr>';?></table>
<h2>Recent reminders</h2><table><tr><th>When</th><th>Message</th><th>Emailed</th></tr>
<?php foreach($pdo->query('SELECT * FROM reminders ORDER BY id DESC LIMIT 10') as $m)echo '<tr><td>'.e($m['created_at']).'</td><td>'.e($m['message']).'</td><td>'.($m['emailed']?'Yes':'No').'</td></tr>';?></table>
<?php foot();

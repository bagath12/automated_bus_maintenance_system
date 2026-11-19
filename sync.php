<?php
// Web: POST from dashboard button. CLI/cron: php sync.php  (run every 15 min)
require __DIR__.'/common.php';
if(PHP_SAPI!=='cli'){auth();check();}
$msgs=[];
foreach($pdo->query("SELECT id,bus_number,tago_token FROM buses WHERE tago_token<>''") as $b){
 $c=curl_init('https://api.tago.io/data?variable='.urlencode(TAGO_VARIABLE).'&qty=1');
 curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_TIMEOUT=>15,CURLOPT_HTTPHEADER=>['Device-Token: '.$b['tago_token']]]);
 $r=json_decode(curl_exec($c),true);curl_close($c);
 if(!empty($r['result'][0]['value']))$pdo->prepare('UPDATE buses SET current_km=?,last_sync=NOW() WHERE id=?')->execute([(float)$r['result'][0]['value'],$b['id']]);
 else $msgs[]='No TagoIO reading for '.$b['bus_number'];}
$rows=$pdo->query("SELECT s.*,b.bus_number,b.current_km,i.name FROM schedules s JOIN buses b ON b.id=s.bus_id JOIN items i ON i.id=s.item_id WHERE s.status<>'Completed'")->fetchAll();
$new=0;$lines=[];
foreach($rows as $s){$st=state($s,$s['current_km']);
 if($st==='Due'&&$s['status']!=='Due')$pdo->prepare("UPDATE schedules SET status='Due' WHERE id=?")->execute([$s['id']]);
 if($st!=='OK'&&!$s['reminder_sent']){
  $m=($st==='Due'?'DUE: ':'Approaching: ').'Bus '.$s['bus_number'].' needs '.$s['name'].' (now '.$s['current_km'].' km, due at '.($s['next_km']??'-').' km / '.($s['next_date']??'-').')';
  $pdo->prepare('INSERT INTO reminders(schedule_id,message) VALUES(?,?)')->execute([$s['id'],$m]);
  $pdo->prepare('UPDATE schedules SET reminder_sent=1 WHERE id=?')->execute([$s['id']]);$lines[]=$m;$new++;}}
if($lines){$ok=send_mail('Bus maintenance reminders ('.count($lines).')','<p>'.implode('</p><p>',array_map('e',$lines)).'</p>');
 if($ok)$pdo->exec('UPDATE reminders SET emailed=1 WHERE emailed=0');else $msgs[]='Email failed - check Brevo key and sender.';}
$msgs[]="Sync done. $new new reminder(s).";
if(PHP_SAPI==='cli'){echo implode("\n",$msgs)."\n";exit;}
$_SESSION['flash']=implode(' ',$msgs);header('Location: index.php');

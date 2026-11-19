<?php require __DIR__.'/common.php';head('Maintenance history');$f=(int)($_GET['bus']??0);?>
<form method="get" class="row"><select name="bus" onchange="this.form.submit()"><option value="0">All buses</option><?php foreach($pdo->query('SELECT id,bus_number FROM buses ORDER BY bus_number') as $b)echo '<option value="'.$b['id'].'"'.($f==$b['id']?' selected':'').'>'.e($b['bus_number']).'</option>';?></select></form>
<table><tr><th>Date</th><th>Bus</th><th>Service</th><th>Km at service</th><th>Notes</th></tr>
<?php $q=$pdo->prepare('SELECT h.*,b.bus_number,i.name FROM history h JOIN buses b ON b.id=h.bus_id JOIN items i ON i.id=h.item_id WHERE (?=0 OR h.bus_id=?) ORDER BY h.service_date DESC,h.id DESC');$q->execute([$f,$f]);
$n=0;foreach($q as $h){$n++;echo '<tr><td>'.e($h['service_date']).'</td><td>'.e($h['bus_number']).'</td><td>'.e($h['name']).'</td><td>'.e($h['service_km']).'</td><td>'.e($h['notes']).'</td></tr>';}
if(!$n)echo '<tr><td colspan="5">No service records yet.</td></tr>';?></table><?php foot();

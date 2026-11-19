<?php if(PHP_SAPI!=="cli")session_start(); require __DIR__."/config.php";
$pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES);}
function auth(){if(empty($_SESSION['admin'])){header('Location: login.php');exit;}}
function tok(){if(empty($_SESSION['t']))$_SESSION['t']=bin2hex(random_bytes(16));return '<input type="hidden" name="t" value="'.$_SESSION['t'].'">';}
function check(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['t']??'',$_POST['t']??''))die('Invalid request');}
function logo($s=40){return '<svg class="logo" width="'.$s.'" height="'.$s.'" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="30" fill="#c8872b"/><circle cx="32" cy="32" r="24" fill="#e9b04c"/><circle cx="15" cy="20" r="6" fill="#2b1d14"/><circle cx="49" cy="20" r="6" fill="#2b1d14"/><ellipse cx="32" cy="35" rx="15" ry="17" fill="#f6d28a"/><circle cx="25.5" cy="30" r="2.5" fill="#2b1d14"/><circle cx="38.5" cy="30" r="2.5" fill="#2b1d14"/><path d="M27 38h10l-5 6z" fill="#2b1d14"/><path d="M32 44v4M26 49q6 4 12 0" stroke="#2b1d14" stroke-width="2" fill="none" stroke-linecap="round"/></svg>';}
function head($title){auth();$p=basename($_SERVER['PHP_SELF']);$s=$p==='index.php'?'dashboard':basename($p,'.php');
 echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | Lion Transport</title><link rel="stylesheet" href="css/base.css"><link rel="stylesheet" href="css/components.css">'.(is_file(__DIR__."/css/$s.css")?'<link rel="stylesheet" href="css/'.$s.'.css">':'').'</head><body class="page-'.$s.'"><header><a class="brand" href="index.php">'.logo().'<span>Lion Transport<small>Bus maintenance desk</small></span></a><button class="menu" type="button" aria-label="Menu" onclick="menu()">&#9776;</button><nav>';
 foreach(['index.php'=>'Dashboard','buses.php'=>'Buses','items.php'=>'Items','schedules.php'=>'Schedules','history.php'=>'History'] as $f=>$l)echo '<a href="'.$f.'"'.($p==$f?' class="on"':'').'>'.$l.'</a>';
 echo '<a href="logout.php">Log out</a></nav></header><main><h1>'.e($title).'</h1>';}
function foot(){echo '</main><footer>Lion Transport &middot; Bus maintenance desk</footer><script>function menu(){document.body.classList.toggle("nav-open")}document.querySelectorAll("table").forEach(function(t){var h=[].map.call(t.querySelectorAll("th"),function(x){return x.textContent});t.querySelectorAll("tr").forEach(function(r){if(r.querySelector("th"))r.className="hd";[].forEach.call(r.children,function(c,i){if(c.tagName==="TD"&&h[i])c.setAttribute("data-label",h[i])})})})</script></body></html>';}
// Returns 'Due', 'Soon' or 'OK' for a schedule row joined with the bus's current_km
function state($s,$km){$today=date('Y-m-d');
 if(($s['next_km']!==null&&$km>=$s['next_km'])||($s['next_date']&&$today>=$s['next_date']))return 'Due';
 if(($s['next_km']!==null&&$km>=$s['next_km']-SOON_KM)||($s['next_date']&&$today>=date('Y-m-d',strtotime($s['next_date'].' -'.SOON_DAYS.' days'))))return 'Soon';
 return 'OK';}
function next_vals($km,$date,$ikm,$idays){return [$ikm?$km+$ikm:null,$idays?date('Y-m-d',strtotime("$date +$idays days")):null];}
function send_mail($subject,$html){$c=curl_init('https://api.brevo.com/v3/smtp/email');
 curl_setopt_array($c,[CURLOPT_POST=>1,CURLOPT_RETURNTRANSFER=>1,CURLOPT_TIMEOUT=>15,CURLOPT_HTTPHEADER=>['api-key: '.BREVO_API_KEY,'Content-Type: application/json'],
 CURLOPT_POSTFIELDS=>json_encode(['sender'=>['name'=>MAIL_FROM_NAME,'email'=>MAIL_FROM],'to'=>[['email'=>MAIL_TO]],'subject'=>$subject,'htmlContent'=>$html])]);
 curl_exec($c);$ok=curl_getinfo($c,CURLINFO_HTTP_CODE)<300;curl_close($c);return $ok;}

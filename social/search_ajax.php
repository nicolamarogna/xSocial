<?php
session_start();

define('DATABASE', 'social'); //define('DATABASE', 'Sql421253_1'); social
define('HOST', 'localhost'); //define('HOST', '62.149.150.125'); localhost
define('DB_USER', 'root'); //define('DB_USER', 'Sql421253'); root
define('DB_PASS', ''); //define('DB_PASS', '329ceb03');
	
$conn = mysql_connect(HOST, DB_USER, DB_PASS) or die("Errore nella connessione a MySql: " . mysql_error());
		mysql_select_db(DATABASE,$conn) or die("Errore nella selezione del db: " . mysql_error());
		mysql_query("SET NAMES 'utf8'") or die("Errore nella query: " . mysql_error());
			
$s = split(" ",$_GET['search']);
		$where .= ' (nome LIKE "%'.$s[0].'%" AND cognome LIKE "%'.$s[1].'%") OR (nome LIKE "%'.$s[1].'%" AND cognome LIKE "%'.$s[0].'%")';
		$sql = mysql_query('SELECT social_users.* FROM social_users WHERE '.$where.' LIMIT 8');
		
while ($row = mysql_fetch_object($sql)) {
	$data[] = $row;
}
foreach ($data as $i) {
	$img = ($i->img) ? 'files/img/thumb_'.$i->img : 'files/img_private/thumb_img_profile_null.jpg';
	$results[] = array($i->id,'<table><tr><td><img class="fleft padright" src="'.$img.'">'.$i->nome.' '.$i->cognome.'<br><span class="xsmall">('.$i->citta.')</span></td></tr></table>', $i->nome.' '.$i->cognome, $i->id,$i->nome.' '.$i->cognome.' <i>('.$i->citta.')</i></td></tr></table>');
}

header('Content-type: application/json');
echo json_encode($results);
?>
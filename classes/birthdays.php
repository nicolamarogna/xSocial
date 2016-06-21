<?php
class Birthdays {

	public function __construct()
	{
		
	}
	
	public function birthdays($when) {
		switch ($when) {
			case date('m-d'):
				$day = 'oggi';
				$sql_when = ' AND DATE_FORMAT(birthday, \'%m-%d\')  = "'.substr($when, 0, 5).'"';
				break;
			case date("m-d", mktime(0,0,0,date("m"),date("d")+1)):
				$day = 'domani';
				$sql_when = ' AND DATE_FORMAT(birthday, \'%m-%d\') = "'.substr($when, 0, 5).'"';
				break;
			case date("m-d", mktime(0,0,0,date("m"),date("d")+2)):
				$day = 'dopodomani';
				$sql_when = ' AND DATE_FORMAT(birthday, \'%m-%d\') = "'.substr($when, 0, 5).'"';
				break;
			case 'prossimi':
				$sql_when = ' AND DATE_FORMAT(birthday, \'%m-%d\') > "'.date("m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y"))).'" OR DATE_FORMAT(birthday, \'%m-%d\') < "'.date("m-d",mktime(0,0,0,date("m"),date("d"),date("Y"))).'" ORDER BY DATE_FORMAT(birthday, \'%m-%d\') ASC LIMIT 30';
				break;
			default:
				$day = Utils::f_date($when);
				break;
		}
		
		$mod = new Db;
		$birthdays = $mod->query('SELECT DISTINCT social_users.id, social_users.nome, social_users.cognome, social_users.img, social_users.birthday FROM social_isfriend
							   INNER JOIN social_users ON (social_users.id = social_isfriend.id_user
							   	OR social_users.id = social_isfriend.id_friend) AND (social_users.nome != "" AND social_users.cognome != "") AND social_users.id != '.$_SESSION['user']->id.'
							   WHERE (social_isfriend.id_user = '.$_SESSION['user']->id.'
							   OR social_isfriend.id_friend = '.$_SESSION['user']->id.')
							   AND social_isfriend.confirmed = 1'.$sql_when);
		
		
		
		if ($birthdays) {
			return array($day => $birthdays);
		}
	}
	
	public function view_birthdays($array) {
		echo '<br><div id="menu">
				<table><tr>
				<td><span class="bold"><img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_birthday.png">Compleanni</span></td>
				<td class="aright"><a class="bold" href="?p=birthdays&type=all">Mostra</a></td>
				</tr></table>
				</div>';
				
		echo '	<table>
				<tr>
				<td>';

		foreach ($array as $arr) {
			if ($arr) {
				foreach ($arr as $k => $i) {
					if ($i) {
						$found = 1;
						foreach ($i as $ii) {
							echo '<a href="?userboard='.$ii->id.'"><span>'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'</a></span> <span class="xsmall">'.$k.'</span><br>';
						}
					}
				}
			}
		}
		
		if (!$found) {
			echo 'Nessun compleanno a breve.';	
		}
		echo '	</td>
				</tr>
				</table><br>
			';
		
	}
	
}



?>
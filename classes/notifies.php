<?php
class Notifies {

	public function __construct()
	{
		
	}
	
	public function view_notifies() {
		$mod = new Db;
		$notifies = $mod->query('SELECT id FROM social_notify WHERE to_user = '.$_SESSION['user']->id.' AND viewed = 0');
		
		echo '<div id="menu">
				<table><tr>
				<td><span class="bold"><img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_esclamativo.png">Notifiche</span></td>
				<td class="aright"><a class="bold" href="?p=notifies&type=all">Mostra</a></td>
				</tr></table>
				</div>';
				
		echo '<table>
				<tr>
				<td>';
		if ($notifies) {
			echo '<a href="?p=notifies"><span class="bold">'.sizeof($notifies).'</span> ';
			echo (sizeof($notifies) == 1) ? 'notifica' : 'notifiche';
			echo '</a>';
		} else {
			echo 'Nessuna notifica.';
		}
		echo '	</td>
				</tr>
				</table>
			';
		
	}
	
	public function view_notifies_detail() {
		$mod = new Db;
		$all_notifies = ($_GET['type'] == 'all') ? '' : ' AND viewed = 0';
		$notifies = $mod->query('SELECT social_users.*, social_notify.*, social_notify.id as id_notify, social_notify.updated as upd_notify
								FROM social_notify
								INNER JOIN social_users ON social_users.id = social_notify.from_user
								WHERE social_notify.from_user != '.$_SESSION['user']->id.'
								AND social_notify.to_user = '.$_SESSION['user']->id.
								$all_notifies.' ORDER BY social_notify.id DESC');
		return $notifies;
	}
	
}



?>
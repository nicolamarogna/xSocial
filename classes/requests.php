<?php
class Requests {

	public function __construct()
	{
		
	}
	
	public function friend_requests() {
		$mod = new Db;
		$requests = $mod->query('SELECT id FROM social_isfriend WHERE id_friend = '.$_SESSION['user']->id.' and confirmed = 0');
		
		echo '<div id="menu">
				<table><tr>
				<td><span class="bold"><img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_plus.png">Richieste di amicizia</span></td>
				</tr></table>
				</div>';
		echo '
				<table>
				<tr>
				<td>';
		if ($requests) {
			echo '<a href="?p=requests"><span class="bold">'.sizeof($requests).'</span> richiesta di amicizia</a>';
		} else {
			echo 'Nessuna richiesta di amicizia.';
		}
		echo '	</td>
				</tr>
				</table><br>
			';
		
	}	
	
	public function friend_requests_detail() {
		$mod = new Db;
		$requests = $mod->query('SELECT * FROM social_users
								INNER JOIN social_isfriend ON social_users.id = social_isfriend.id_user
								WHERE social_isfriend.id_friend = '.$_SESSION['user']->id.' and social_isfriend.confirmed = 0');
		
		return $requests;
	}
	
	public function accept_friend($id, $post) {
		$mod = new Db;
		$confirm = $mod->update('social_isfriend', $id, $post);
								
		return $confirm;
	}	
}



?>
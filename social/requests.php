<?php
	$mod = new Requests;
	
	echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_plus.png">Richieste di amicizia</div>';
	//accept friend
	if ($_POST['accept_friend']) {
		$post = array(
			'confirmed' => 1
		);
		$confirmed_friend = $mod->accept_friend($_POST['accept_friend'], $post);
		if ($confirmed_friend) {
			$db = new Db;
			
			$friend = $db->get_by_id('social_users', $_POST['id_friend']);
			
			$isgroup = $db->get_by_id('social_isfriend', $_POST['accept_friend']);
			
			if (!Utils::isgroup($isgroup->id_group)) {
				echo '<div id="menu"><table><tr><td width="50">
					<a href="?userboard='.$friend->id.'">
					<img src="files/img/thumb_'.$friend->img.'" title="'.stripslashes($friend->nome).' '.stripslashes($friend->nome).'">
					</a>
					</td>
					<td>';
				echo 'Ora sei amico di <a href="?userboard='.$friend->id.'" class="bold">'.stripslashes($friend->nome).'</a>';
				echo '</td>
					</tr></table>
					</div>
					<br>';
				$db->notify($_SESSION['user']->id, $_POST['id_friend'], 'friend_accepted', 0);
			} else {
				$group = $db->get_by_id('social_users', $isgroup->id_group);
				$result = $db->single_exec('UPDATE social_isfriend SET confirmed = 1 WHERE id_user='.$_POST['id_friend'].' AND id_friend='.$isgroup->id_group);
				echo '<div id="menu"><table><tr><td width="50">
					<a href="?userboard='.$group->id.'">
					<img src="files/img/thumb_'.$group->img.'" title="'.stripslashes($group->group).'">
					</a>
					</td>
					<td>';
				echo 'Ora <a href="?userboard='.$friend->id.'" class="bold">'.stripslashes($friend->nome).'</a> pu&ograve; accedere al gruppo " <a href="?userboard='.$group->id.'" class="bold">'.stripslashes($group->group).'</a> "';
				echo '</td>
					</tr></table>
					</div>
					<br>';
				$db->notify($_SESSION['user']->id, $_POST['id_friend'], 'group_accepted', $isgroup->id_group);
			}
		}
	}
	
	$req = $mod->friend_requests_detail();
	
	if ($req) {
		foreach ($req as $i) {
			$what = (Utils::isgroup($i->id_group)) ? 'chiede di essere iscritto in un tuo gruppo' : 'ha chiesto la tua amicizia';
			echo '<table><tr><td width="50">
				<a href="?userboard='.$i->id_user.'">
				<img src="files/img/thumb_'.$i->img.'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
				</a>
				</td><td>
				<form method="post" action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'">
				<input type="hidden" name="accept_friend" value="'.$i->id.'">
				<input type="hidden" name="id_friend" value="'.$i->id_user.'">
				<div id="status_msg">
				<a href="?userboard='.$i->id_user.'" class="bold">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a> '.$what.'.
				<br>
				<button>Aggiungi agli amici</button>
				</div>
				</div>
				</form>
				</td></tr></table>';
		}
	} else {
		echo 'Nessuna richiesta di amicizia.';
	}
	
?>
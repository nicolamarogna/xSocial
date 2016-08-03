<script>
function checkAll(form,state,identifier){
	for(f=0;f<form.length;f++){
	
		if(form[f].type=="checkbox" && form[f].name==identifier) {
			if (form[f].checked==true) {
				form[f].checked=false;
				form.sel.innerHTML="Seleziona tutti";
			} else {
				form[f].checked=true;
				form.sel.innerHTML="Deseleziona tutti";
			}
		}
	}
}
</script>

<?php
	$mod = new Db;

	if ($_GET['del_friend']) {
		$path = ROOT.'files/img/';
		
		//delete all
		$msgs = $mod->query('SELECT id, img FROM social_status WHERE (from_user = '.$_GET['del_friend'].' AND to_user = '.$_SESSION['user']->id.') OR (from_user = '.$_SESSION['user']->id.' AND to_user = '.$_GET['del_friend'].')');
		print_r($msgs);
		if ($msgs) {
			foreach ($msgs as $i) {
				//delete img
				Utils::del_file($path, $i->img);
				Utils::del_file($path, 'thumb_'.$i->img);
				//delete comments
				$mod->single_exec('DELETE from social_comments WHERE id_comment = '.$i->id);
				//delete status
				$mod->single_exec('DELETE from social_status WHERE id = '.$i->id);
				//delete notifies
				$mod->single_exec('DELETE from social_notify WHERE (what="comment" OR what="comment_answer" OR what="msg") AND id_what = '.$i->id);
			}
		}
		//delete friend
		$mod->single_exec('DELETE from social_isfriend WHERE (id_user = '.$_GET['del_friend'].' AND id_friend = '.$_SESSION['user']->id.') OR (id_user = '.$_SESSION['user']->id.' AND id_friend = '.$_GET['del_friend'].')');
		header('Location: ?p=friends');
	}

	echo '<div id="right_content">';
	echo '<div id="head_under"><i class="fa fa-users fright" aria-hidden="true"></i>Elenco amici</div>';

	//check if I am or is another user
	$user_id = ($_GET['userboard']) ? $_GET['userboard'] : $_SESSION['user']->id;

	$friends = $mod->query('SELECT DISTINCT
							social_users.id,
							social_users.nome,
							social_users.cognome,
							social_users.img,
							social_users.group,
							social_users.group_admin
							FROM social_isfriend
							INNER JOIN social_users ON (social_users.id = social_isfriend.id_user OR social_users.id = social_isfriend.id_friend) AND (social_users.id != '.$user_id.' AND social_users.group_admin != '.$user_id.') AND social_users.xon = 1
							WHERE (social_isfriend.id_user = '.$user_id.' OR social_isfriend.id_friend = '.$user_id.') OR (social_users.group_admin != '.$user_id.' AND social_users.group_admin > 0)
							AND social_isfriend.confirmed = 1
							ORDER BY social_users.nome
							');
	
	//notify selected friends
	if ($_POST['notify_friends']) {
		foreach ($_POST['notify_friends'] as $i) {
			if ($_POST['id_event']) {
				//notify event
				$mod->notify($_SESSION['user']->id, $i, 'event', $_POST['id_event']);
			} else {
				//notify group
				$mod->notify($_SESSION['user']->id, $i, 'group_created', $_POST['id_group']);
			}
		}
		//redirect
		header('Location: ?p=group');
		die;
	}

	if ($friends) {
		echo '<form name="friends" method="post" action="'.$_SERVER['PHP_SELF'].'?p=friends&type=notify&id_event='.$_GET['id_event'].'">';
		if ($_GET['id_event']) {
			//check if i'm the admin of the event selected
			$event_admin = $mod->query('SELECT from_user FROM social_events WHERE id = '.$_GET['id_event']);
			if ($event_admin[0]->from_user == $_SESSION['user']->id) {
				echo '<input type="hidden" name="id_event" value="'.$_GET['id_event'].'">';
			} else {
				header('Location: ?p=events');
			}
		} elseif ($_GET['id_group']) {
			//check if i'm the admin of the group selected
			if ($mod->get_by_id('social_users', $_GET['id_group'])->group_admin == $_SESSION['user']->id) {
				echo '<input type="hidden" name="id_group" value="'.$_GET['id_group'].'">';
			} else {
				header('Location: ?p=group');
			}
		}
		
		echo '<table id="results">';
		foreach ($friends as $i) {
			if ((isset($_GET['type'])) && ($_GET['type'] == 'notify')) {
				//check if a friend is already notified
				$id_what = ($_GET['id_event']) ? $_GET['id_event'] : $_GET['id_group'];
				$what = ($_GET['id_event']) ? 'event' : 'group_created';
				$check = $mod->query('SELECT id FROM social_notify
									 WHERE what = "'.$what.'"
									 AND id_what = '.$id_what.'
									 AND to_user = '.$i->id);
			}
			//if a friend is NOT already notified
			if (!$check) {
				$count_invited++;
				if ((isset($_GET['type'])) && ($_GET['type'] == 'notify')) {
					//write button SELECT ALL only one time
					if ($onetime != 1) {
						echo '<tr><td colspan=3><button name="sel" type="button" onClick="checkAll(this.form,this.checked,\'notify_friends[]\');">Seleziona tutti</button>
</td></tr>';
						$onetime = 1;
					}
				}
				//found almost one friend, ok to print submit button
				$found = 1;
				
				echo '<tr>';
				if ($i->img) {
					$path = 'files/img/';
					$img = $i->img;
				} else {
					$path = 'files/img_private/';
					$img = 'img_profile_null.jpg';
				}
				if ((isset($_GET['type'])) && ($_GET['type'] == 'notify')) {
					echo '<td width="10" class="amiddle"><input type="checkbox" name="notify_friends[]" value="'.$i->id.'"></td>';
				}
				echo '<td width="50"><a href="?userboard='.$i->id.'">
					<img src="'.$path.'thumb_'.$img.'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
					</a>';
				echo '</td><td>';
				
				if (($i->nome != '') && ($i->cognome != '')) {
					echo '<span class="bold"><a href="?userboard='.$i->id.'">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a></span>';
				} else {
					echo '<span class="bold"><a href="?userboard='.$i->id.'">'.stripslashes($i->group).'</a></span> (gruppo)';
				}
				
				echo '</td>';
				
				if (($user_id == $_SESSION['user']->id) || ($mod->get_by_id('social_users', $user_id)->group_admin == $_SESSION['user']->id)) {
					echo '<td class="aright">
				<a href="#" onclick="if (confirm(\'Sicuro di voler eliminare questo amico?\')) {location.href=\'?p=friends&del_friend='.$i->id.'\'; return true;}"><i class="fa fa-trash-o fa-lg fright" aria-hidden="true" alt="Elimina amico" title="Elimina amico"></i></a>
				</td>';
				
				}
				
				echo '</tr>';
			}
		}
				if ((isset($_GET['type'])) && ($_GET['type'] == 'notify') && ($found == 1)) {
					$what = ($_GET['id_event']) ? 'a quest\'evento.' : 'a questo gruppo.';
					echo 'Hai invitato '.(sizeof($friends) - $count_invited).' amici '.$what;
				}
				
		echo '</table>';
		
		//view submit button
		if ((isset($_GET['type'])) && ($_GET['type'] == 'notify') && ($found == 1)) {	
			echo'<button>Invita amici</button>';
		} elseif ((isset($_GET['type'])) && ($_GET['type'] == 'notify') && ($found != 1)){
			$back = ($_GET['id_event']) ? 'Torna agli eventi' : 'Torna ai gruppi';
			echo'Hai gi&agrave; invitato tutti i tuoi amici.<br>
				<a href="javascript:history.back();">'.$back.'</a>';
		}
		
		echo '</form>';
	} else {
		echo 'Non hai ancora fatto amicizia con nessuno.';
	}
	
	echo '</div>';
	
?>
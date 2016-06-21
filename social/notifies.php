<?php	
	$mod = new Notifies;
	$db = new Db;
	
	if ($_POST['del_notify']) {
		$db->delete('social_notify', $_POST['del_notify']);
	}
	
	echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_esclamativo.png">Notifiche</div>';
		
	$notifies = $mod->view_notifies_detail();

	$pager = Pagination::paginate($notifies, $_GET['page'], 50);

	if ($pager[0]) {
		foreach ($pager[0] as $i) {
			echo '<form name="form_'.$i->id_notify.'" method="post" action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'">
				<input type="hidden" name="del_notify" value="'.$i->id_notify.'">
				<table id="results"><tr><td>
				<div id="status_msg">';
				
			//icons
			switch ($i->what) {
				case 'msg':
				case 'msg_group':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_message.png">';
				break;
				case 'comment':
				case 'comment_group':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_comment.png">';
				break;
				case 'comment_answer':
				case 'comment_answer_group':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_comment_answer.png">';
				break;
				case 'friend_accepted':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_friends.png">';
				break;
				case 'album_created':
				case 'album_updated':
				case 'album_updated':
				case 'photo_added':
				case 'photo_updated':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_foto.png">';
				break;
				case 'event':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_event.png">';
				break;
				case 'calendar_created':
				case 'calendar_added':
				case 'calendar_updated':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_calendar.png">';
				break;
				case 'group_created':
				case 'group_accepted':
					echo '<img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_group.png">';
				break;
			}
			
			echo '<a href="?userboard='.$i->from_user.'" class="bold">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a>';
			
			switch ($i->what) {
				case 'msg':
					$what = $db->get_by_id('social_status', $i->id_what);
					if ($what->youtube) {
						$msg = ' ha pubblicato un video ';
					} elseif ($what->img) {
						$msg = ' ha pubblicato una foto ';
					} else {
						$msg = ' ha lasciato un messaggio ';
					}
					echo $msg.'sulla tua <a href="?detail='.$i->id_what.'&id_notify='.$i->id_notify.'">bacheca</a>.';
					break;
				case 'msg_group':
					$group = $db->get_by_id('social_status', $i->id_what);
					if ($group->youtube) {
						$msg = ' ha pubblicato un video ';
					} elseif ($group->img) {
						$msg = ' ha pubblicato una foto ';
					} else {
						$msg = ' ha lasciato un messaggio ';
					}
					
					echo $msg.'sulla <a href="?userboard='.$group->to_user.'&detail='.$i->id_what.'&id_notify='.$i->id_notify.'">bacheca di un gruppo</a> al quale sei iscritto.';
					break;
				case 'comment':
				case 'comment_group':
					$group = $db->get_by_id('social_status', $i->id_what);
					$userboard = ($group) ? $group->to_user : $i->to_user;
					$post_author = $db->query('SELECT social_status.from_user, social_status.to_user, social_users.nome, social_users.cognome
												FROM social_users
												INNER JOIN social_status ON social_status.from_user = social_users.id
												WHERE social_status.id = '.$i->id_what);
					if ($post_author[0]->from_user == $_SESSION['user']->id) {
						echo ' ha commentato il tuo <a href="?userboard='.$userboard.'&detail='.$i->id_what.'&id_notify='.$i->id_notify.'">post</a>.';
					} elseif ($post_author[0]->from_user == $i->from_user) {
						echo ' ha commentato il suo <a href="?userboard='.$userboard.'&detail='.$i->id_what.'&id_notify='.$i->id_notify.'">post</a>.';
					} else {
						echo ' ha commentato il <a href="?userboard='.$post_author[0]->from_user.'&detail='.$i->id_what.'&id_notify='.$i->id_notify.'">post</a> di <a href="?userboard='.$post_author[0]->from_user.'" class="bold">'.stripslashes($post_author[0]->nome).' '.stripslashes($post_author[0]->cognome).'</a>.';
					}
					break;
				case 'comment_answer':
				case 'comment_answer_group':
					$group = $db->get_by_id('social_status', $i->id_what);
					$userboard = ($group) ? $group->to_user : $i->to_user;
					$post_author = $db->query('SELECT social_status.from_user, social_users.nome, social_users.cognome
												FROM social_users
												INNER JOIN social_status ON social_status.from_user = social_users.id
												WHERE social_status.id = '.$i->id_what);
					if ($post_author[0]->from_user == $_SESSION['user']->id) {
						echo ' ha risposto a un tuo <a href="?detail='.$i->id_what.'&id_notify='.$i->id_notify.'">post</a>.';
					} elseif ($post_author[0]->from_user == $i->from_user) {
						echo ' ha risposto al suo <a href="?userboard='.$userboard.'&detail='.$i->id_what.'&id_notify='.$i->id_notify.'">post</a>.';
					} else {
						echo ' ha risposto al <a href="?userboard='.$i->to_user.'&detail='.$i->id_what.'&id_notify='.$i->id_notify.'">post</a> di <a href="?userboard='.$post_author[0]->from_user.'" class="bold">'.stripslashes($post_author[0]->nome).' '.stripslashes($post_author[0]->cognome).'</a>.';
					}
					break;
				case 'friend_accepted':
					if (Utils::isgroup($i->from_user)) {
						echo 'Ora fai parte del gruppo <a class="bold" href="?userboard='.$i->from_user.'">'.$i->group.'</a>.';
					} else {
						echo ' ha accettato la tua amicizia.';
					}
						$post[] = array('viewed' => 1);
						$db->update('social_notify', $i->id_notify, $post);
					break;
				case 'event':
					if ($i->from_user != $_SESSION['user']->id) {
						echo ' ti ha invitato ad un <a href="?p=events&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">evento</a>.';
					} else {
						echo ' ha creato un nuovo <a href="?p=events&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">evento</a>.';
					}
					break;
				case 'album_created':
					echo ' ha creato un nuovo <a href="?p=album&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">album</a>.';
					break;
				case 'album_updated':
					echo ' ha modificato il titolo di un <a href="?p=album&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">album</a>.';
					break;
				case 'photo_added':
					echo ' ha aggiunto una foto al suo <a href="?p=album&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">album</a>.';
					break;
				case 'photo_updated':
					echo ' ha modificato una foto del suo <a href="?p=album&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">album</a>.';
					break;
				case 'calendar_created':
					echo ' ha creato un <a href="?p=calendar&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">calendario</a>.';
					break;
				case 'calendar_added':
					echo ' ha aggiornato un <a href="?p=calendar&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">calendario</a>.';
					break;
				case 'calendar_updated':
					echo ' ha modificato un <a href="?p=calendar&userboard='.$i->from_user.'&id_detail='.$i->id_what.'&id_notify='.$i->id_notify.'">calendario</a>.';
					break;
				case 'group_created':
					echo ' ti ha invitato a iscriverti a un <a href="?userboard='.$i->id_what.'&id_notify='.$i->id_notify.'&what=group">gruppo</a>.';
					break;
				case 'group_accepted':
					echo ' (amministratore) ha accettato la tua richiesta di iscrizione a un suo <a href="?userboard='.$i->id_what.'&id_notify='.$i->id_notify.'&what=group">gruppo</a>.';
					break;
				}
				
			
			echo ' <span class="xsmall">'.Utils::f_date($i->upd_notify).'</span></div>
					</div>
					
					</td>
					<td class="aright"><span>';
			if ($i->what != 'event') {
				echo '<a href="#" onclick="if (confirm(\'Vuoi eliminare questo elemento?\')) {document.forms.form_'.$i->id_notify.'.submit(); return true;}"><img class="fright" src="'.BASE_URL.'files/img_private/thumb_delete.png" title="Elimina notifica" alt="Elimina album"></a>';
			}
			echo '</span>
					</td>
					</tr></table></form>';
		}
		//pagination view
		echo '<br><div id="pager">'.Pagination::pager('?p=notifies&page=', $pager[1]).'</div>';
	} else {
		echo 'Nessuna notifica.';
	}
	
?>
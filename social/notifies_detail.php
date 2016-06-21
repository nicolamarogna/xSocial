<?php
	$mod = new Notifies;
	$db = new Db;
	
	echo '<div id="head_under">Dettaglio notifica</div>';
		
	$notify = $mod->view_notify_status($_GET['id'], $_GET['id_what']);
				/*
				if ($i->what == 'msg') {
									$msg = $db->get_by_id('social_status', $i->id_what);
									echo '<br>'.$msg->status;
				}
				*/
	//check permissions
	if ($notify) {
		if ($notify[0]->to_user == $_SESSION['user']->id) {
			foreach ($notify as $i) {
				$comments = $db->query('SELECT social_users.nome as nome,
										social_users.cognome as cognome,
										social_users.img as img,
										social_comments.*
										FROM social_comments
										INNER JOIN social_users ON social_users.id = social_comments.id_user
										WHERE id_comment = '.$i->id_what.'
										ORDER BY updated DESC');
				
				echo '<table><tr><td width="50">
						<a href="?userboard='.$i->id.'">
						<img src="files/img/thumb_'.$i->img.'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
						</a>
						</td><td><form name="view_posts_'.$i->id_status.'" method="post" action="'.$_SERVER['PHP_SELF'].'">
						<input type="hidden" name="id_status" value="'.$i->id_status.'">
						<input type="hidden" name="img_status" value="'.$i->upl_img.'">
						<div id="status_msg">
						<span class="bold">
						<a href="?userboard='.$i->id.'">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a>
						</span> '.stripslashes(nl2br($i->status)).'
						<br>';
						
				if ($i->upl_img) {
					echo '<a href="files/img/'.$i->upl_img.'" class="fancybox"><img class="borded pad_all" src="files/img/thumb_'.$i->upl_img.'"></a>';
				}
				echo '<div id="foot_status">'.Utils::f_date($i->upd).SEP.'<a href="#" onclick="document.forms.view_posts_'.$i->id_status.'.submit(); return false;">Elimina</a></div>
				</div>
				</form></td></tr></table>';
				
				//comments area
				echo '<table><tr><td width="50"></td>
						<td>
						<div id="comments">
						<table id="results">';
				if ($comments) {
					foreach ($comments as $c) {
						echo '<tr><td style="width:50px;">
							<form action="'.$_SERVER["REQUEST_URI"].'" method="post" name="form_comments_'.$c->id.'">
							<a href="?userboard='.$c->id_user.'">
							<img src="files/img/thumb_'.$c->img.'">
							</a>
							</td><td>
							<a href="?userboard='.$c->id_user.'"><span class="bold">'.stripslashes($c->nome).' '.stripslashes($c->cognome).'</a></span> <span>'.nl2br($c->comment).'<br>'.Utils::f_date($c->updated).'</span>
							'.SEP.'<input type="hidden" name="del_comment_id" value="'.$c->id.'">
							<a href="#" onclick="document.forms.form_comments_'.$c->id.'.submit(); return false;">Elimina</a>
							</td></tr>
							</form>';
					}
				}
				echo '</table>
						<form action="'.$_SERVER["REQUEST_URI"].'" method="post">
						<span class="xsmall">Scrivi un commento...<textarea class="textarea_comment" name="comment"></textarea></span>
						<input type="hidden" name="id_comment" value="'.$i->id_status.'">
						<button class="button_comments">Commenta</button>
						</div>
						</form>
						</td>
						</tr></table>';
			}
		} else {
			echo 'Non hai i permessi per visualizzare questa notifica.';
		}
	} else {
		echo 'Nessuna notifica da visualizzare.';	
	}
	
?>
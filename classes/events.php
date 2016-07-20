<?php
class Events {

	public function __construct()
	{
		
	}
	
	public function list_events_in_program() {
		$db = new Db;
	
		echo '<div id="right_content">';
		echo '<div id="head_under"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i> Eventi di oggi</div>';
		echo '<div id="results"><table>';
		$evnt = $this->mini_events(date('Y-m-d'));
		if ($evnt) {
			foreach ($evnt as $k => $i) {
				foreach ($i as $ii) {
					if ($ii->img) {
						$img = 'files/img/thumb_'.$ii->img;
					} else {
						$img = 'files/img_private/thumb_img_profile_null.jpg';
					}
					echo '<tr><td width="50"><a href="?p=events&id_detail='.$ii->id.'">
				<img class="img_event" src="'.$img.'">
				</a>
				</td>
				<td>
				<a href="?p=events&id_detail='.$ii->id.'" class="bold">'.stripslashes($ii->title).'</a><br><span class="xsmall">'.Utils::f_date($ii->date).'</span>
				</td></tr>';
				}
			}
		} else {
				echo 'Nessun evento.';
		}
		echo '</table>
		</div>';
		echo '</div>';
		
	
	
		echo '<div id="right_content">';
		echo '<div id="head_under"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i> Eventi di domani</div>';
		echo '<div id="results"><table>';
		$evnt = $this->mini_events(date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y"))));
		if ($evnt) {
			foreach ($evnt as $k => $i) {
				foreach ($i as $ii) {
					if ($ii->img) {
						$img = 'files/img/thumb_'.$ii->img;
					} else {
						$img = 'files/img_private/thumb_img_profile_null.jpg';
					}
					echo '<tr><td width="50"><a href="?p=events&id_detail='.$ii->id.'">
				<img class="img_event" src="'.$img.'">
				</a>
				</td>
				<td>
				<a href="?p=events&id_detail='.$ii->id.'" class="bold">'.stripslashes($ii->title).'</a><br><span class="xsmall">'.Utils::f_date($ii->date).'</span>
				</td></tr>';
				}
			}
		} else {
				echo 'Nessun evento.';
		}
		echo '</table>
		</div>';
		echo '</div>';
			
			
		echo '<div id="right_content">';
		echo '<div id="head_under"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i> Prossimi eventi</div>';
		echo '<div id="results"><table>';
		$evnt = $this->mini_events('prossimi');
		if ($evnt) {
			foreach ($evnt as $k => $i) {
				foreach ($i as $ii) {
					if ($ii->img) {
						$img = 'files/img/thumb_'.$ii->img;
					} else {
						$img = 'files/img_private/thumb_img_profile_null.jpg';
					}
					echo '<tr><td width="50"><a href="?p=events&id_detail='.$ii->id.'">
				<img class="img_event" src="'.$img.'">
				</a>
				</td>
				<td>
				<a href="?p=events&id_detail='.$ii->id.'" class="bold">'.stripslashes($ii->title).'</a><br><span class="xsmall">'.Utils::f_date($ii->date).'</span>
				</td></tr>';
				}
			}
		} else {
				echo 'Nessun evento.';
		}
		echo '</table>
		</div>';
		echo '</div>';
	}
	
	
	public function list_events() {
		$mod = new Db;

		echo '<div id="right_content">';
		echo '<div id="head_under"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i> Elenco eventi</div>';
		echo '<div id="menu">
				<table><tr><td class="aright">
				<a class="bold" href="?p=events&id_mod=0">Crea un nuovo evento.</a>
				</td></tr></table>
				</div><br>';
		$events = $mod->query('SELECT * FROM social_events WHERE from_user = '.$_SESSION['user']->id.' ORDER BY date ASC');
		if ($events) {
			echo '<table id="results">';
			foreach ($events as $i) {
				echo '<tr><td style="width:350px;">
					<a class="bold" href="?p=events&id_detail='.$i->id.'">';
				if ($i->img) {
					echo '<img class="fleft pad_all" src="files/img/thumb_'.$i->img.'">';
				}
				echo $i->title.'</a>
						<br>
						<span class="xsmall">'.Utils::f_date($i->date).'</span>
						</td>
						<td class="aright">';
				echo '<a href="?p=events&id_del='.$i->id.'"><i class="fa fa-trash fa-border fa-lg fright" aria-hidden="true" title="Elimina"></i></a> 
					<a href="?p=events&id_mod='.$i->id.'&action=clone"><i class="fa fa-clone fa-lg fa-border fright" aria-hidden="true" title="Duplica evento"></i></a> 
					<a href="?p=events&id_mod='.$i->id.'"><i class="fa fa-pencil fa-border fa-lg fright" aria-hidden="true" title="Modifica"></i></a>
						';
				echo '<br><a class="box_up_down bold" href="?p=friends&type=notify&id_event='.$i->id.'">Invita amici</a><br>';
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
			echo 'Nessun evento inserito. <a class="bold" href="?p=events&id_mod=0">Creane uno adesso.</a>';
		}
		echo '</div>';
	}
	
	public function detail_event($id) {
		$mod = new Db;
		//notify viewed
		$id_notify = $_GET['id_notify'];
		$post[] = array ('viewed' => 1);
		$mod->update('social_notify', $id_notify, $post);
		
		$event = $mod->get_by_id('social_events', $id);
						
		echo '<div id="right_content">';
		
		if ($event) {
			echo '<div id="navbar">'.stripslashes($event->title).'</div>';
			echo '<table><tr><td>';
			if ($event->img) {
				echo '<a class="bold" href="files/img/'.$event->img.'" class="fancybox">
						<img class="fright pad_all img_event" src="files/img/'.$event->img.'">
						</a>';
			}
			echo '<span class="bold">Inizio:</span> '.Utils::f_date($event->date).'
					<br>
					<span class="bold">Luogo:</span> '.stripslashes($event->location);
			echo '</td></tr></table>';
			echo '<div id="navbar">Descrizione</div>';
			echo nl2br(stripslashes($event->description));
			
			
			//if i'm not the admin of the event
			if ($event->from_user != $_SESSION['user']->id) {
				//check if is the first time of answer or not
				$method = $mod->query('SELECT *
									  from social_confirms
									  WHERE id_user = '.$_SESSION['user']->id.'
									  AND what = "event"
									  AND id_what = '.$id.'
									  LIMIT 1');
				$mth = ($method) ? 'checked' : '';
				switch ($method[0]->confirmed) {
					case 1:
						$ok = 'checked';
						break;
					case 3:
						$perhaps = 'checked';
						break;
					case 2:
						$no = 'checked';
						break;
				}
				echo '<div id="navbar">La tua risposta</div>';
				echo '
						<form name="confirm" method="post" action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'">
						<input type="radio" name="confirm" value="1" '.$ok.' onclick="document.forms.confirm.submit();" /> Parteciper&ograve;
						<br>
						<input type="radio" name="confirm" value="3" '.$perhaps.' onclick="document.forms.confirm.submit();" /> Forse parteciper&ograve;
						<br>
						<input type="radio" name="confirm" value="2" '.$no.' onclick="document.forms.confirm.submit();" /> Non parteciper&ograve;
						
						<input type="hidden" name="id_what" value="'.$id.'">
						<input type="hidden" name="id_user" value="'.$event->id.'">
						<input type="hidden" name="method" value="'.$mth.'">
						<input type="hidden" name="id_confirm" value="'.$method[0]->id.'">
						</form>
						';
			}
			//friends confirms
			$confirms = $this->invited_friends($id, 1);

			echo '<div id="navbar">Invitati</div>';
			echo '<div id="menu">
					<table>
					<tr><td>
					Questo evento ha '.sizeof($confirms).' invitati confermati
					</td></tr>
					</table>
					</div>';
			if ($confirms) {
				//get only first 16 results
				$confirms = array_slice($confirms, 0, 16);
				$c=1;
				echo '<table><tr>';
				foreach ($confirms as $i) {
					if ($i->img) {
						$path = 'files/img/';
						$img = $i->img;
					} else {
						$path = 'files/img_private/';
						$img = 'img_profile_null.jpg';
					}
					echo '<td>
					<a href="?userboard='.$i->id.'">
					<img style="max-width:50px;max-height:35px;" src="'.$path.'thumb_'.$img.'" alt="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
					<br>
					<p style="line-height:1.2;" class="xsmall">
					'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a></p></td>';
					if ($c == 8) {
						echo '</tr><tr>';
						$c=0;
					}
					$c++;
				}
				echo '</tr></table>';
			}
			
			
			//friends perhaps
			$confirms = $this->invited_friends($id, 3);
			
			echo '<div id="menu">
					<table>
					<tr><td>
					Forse parteciperanno '.sizeof($confirms).' amici
					</td></tr>
					</table>
					</div>';
			if ($confirms) {
				//get only first 16 results
				$confirms = array_slice($confirms, 0, 16);
				$c=1;
				echo '<table><tr>';
				foreach ($confirms as $i) {
					if ($i->img) {
						$path = 'files/img/';
						$img = $i->img;
					} else {
						$path = 'files/img_private/';
						$img = 'img_profile_null.jpg';
					}
					echo '<td>
					<a href="?userboard='.$i->id.'">
					<img style="max-width:50px;max-height:35px;" src="'.$path.'thumb_'.$img.'" alt="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
					<br>
					<p style="line-height:1.2;" class="xsmall">
					'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a></p></td>';
					if ($c == 8) {
						echo '</tr><tr>';
						$c=0;
					}
					$c++;
				}
				echo '</tr></table>';
			}
			
			//friends not confirmed
			$confirms = $this->invited_friends($id, 2);
			
			echo '<div id="menu">
					<table>
					<tr><td>
					Non parteciperanno '.sizeof($confirms).' amici
					</td></tr>
					</table>
					</div>';
			if ($confirms) {
				//get only first 16 results
				$confirms = array_slice($confirms, 0, 16);
				$c=1;
				echo '<table><tr>';
				foreach ($confirms as $i) {
					if ($i->img) {
						$path = 'files/img/';
						$img = $i->img;
					} else {
						$path = 'files/img_private/';
						$img = 'img_profile_null.jpg';
					}
					echo '<td>
					<a href="?userboard='.$i->id.'">
					<img style="max-width:50px;max-height:35px;" src="'.$path.'thumb_'.$img.'" alt="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'">
					<br>
					<p style="line-height:1.2;" class="xsmall">
					'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a></p></td>';
					if ($c == 8) {
						echo '</tr><tr>';
						$c=0;
					}
					$c++;
				}
				echo '</tr></table>';
			}
		}
	
			echo '</div>';
	}
		
	public function edit($id = 0) {
		$mod = new Db;
		$output = '<div id="right_content">';
		if ($id > 0) {
			//check permissions
			$user = $mod->get_by_id('social_events', $id);
			if ($user->from_user != $_SESSION['user']->id) {
				header('Location: ?p=msg&msg=permission_denied');
			}
			$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_event.png">Modifica evento</div>';
			$event = $mod->get_by_id('social_events', $id);
		} else {
			$output .= '<div id="head_under"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i> Crea un evento</div>';
		}
		
		$output .= '<div id="menu">
					<table><tr><td class="aright">
					<a class="bold" href="javascript:history.back();">Annulla</a>
					</td></tr></table>
					</div>';
					
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_SERVER['HTTP_REFERER'],
			'name' => 'from'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => ((isset($_GET['action'])) && ($_GET['action']=='clone')) ? '' : $event->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => 'Titolo',
			'type' => 'text',
			'value' => stripslashes($event->title),
			'name' => 'title',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Luogo',
			'type' => 'text',
			'value' => stripslashes($event->location),
			'name' => 'location',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Descrizione',
			'type' => 'textarea',
			'value' => stripslashes($event->description),
			'name' => 'description',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Data',
			'type' => 'text',
			'value' => $event->date,
			'name' => 'datetime',
			'rule' => 'required',
			'extra' => 'class="dashboard_notime"',
			);
		$fields[] = array(
			'label' => 'Immagine',
			'type' => 'file',
			'value' => '',
			'old' => $event->img,
			'del_img' => $event->img,
			'name' => 'img',
			);
		$fields[] = array(
			'label' => NULL,
			'type' => 'hidden',
			'value' => $_SESSION['user']->id,
			'name' => 'from_user',
			);
		if ((isset($_GET['action'])) && ($_GET['action'] == 'clone')) {
			$fields[] = array(
			'label' => NULL,
			'type' => 'hidden',
			'value' => 'clone',
			'name' => 'action',
			);
		}
		
		if ((isset($_GET['action'])) && ($_GET['action'] == 'clone')) {
			$this->clone_event($event->id, $event->img);
			/*echo '<script>document.forms[\'formadd\'].submit();</script>';*/
			die;
		}
		
		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->editing($_POST);
				die;
			}
			else {
				Utils::set_error($fields);
			}
		}
		
		//prepare form
		$output .= Form::doform('ajaxform', $_SERVER["REQUEST_URI"], $fields, array('Cancella','Modifica'), 'post', 'enctype="multipart/form-data" reloadpage="true" ');
		$output .= '</div>';
		echo $output;
	}
	
	public function clone_event($id, $img) {
		$mod = new Db;
		if ($img) {
			$filename = Utils::clone_file($img);
			$thumb = Utils::clone_file('thumb_'.$img);
		}
		
		$result = $mod->get_by_id('social_events', $id);
		$result->img = $filename;
		$array = get_object_vars($result);
		array_shift($array);
		array_shift($array);
		$mod->insert('social_events', $array);
		
		//insert notify to me already viewed
		$notify = array(
					'from_user' => $_SESSION['user']->id,
					'to_user' => $_SESSION['user']->id,
					'what' => 'event',
					'id_what' => mysql_insert_id(),
					'viewed' => 1
					  );
		$mod->insert('social_notify', $notify);
		header('Location: ?p=events');
		die;
	}
	
	public function editing($_post) {
		$mod = new Db();
		$path = ROOT.'files/img/';
						
			//on file insert, upload file
			$filename = @Utils::upload('img', ROOT.'files/');
			$thumb = @Utils::create_resized($path.$filename, $path.'thumb_'.$filename, array(60,60));

			if ($filename === false) {
				header('Location: '.$_POST['from']);
			}
			
			if ($_post['id'] > 0)  {
				$img = $mod->get_by_id('social_events', $_post['id']);
			}
			//delete old file if is set new file
			if ($filename)
			{
				Utils::del_file($path, $img->img);
				Utils::del_file($path, 'thumb_'.$img->img);
			}

			if (sizeof($filename) == 0) {
				$filename = $_post['old_img'];
			}
						
		$post[] = array(
					'title' => $_POST['title'],
					'location' => $_POST['location'],
					'description' => $_POST['description'],
					'date' => $_POST['datetime'],
					'img' => $filename,
					'from_user' => $_POST['from_user'],
					);
		//case edit
		if ($_post['id'] > 0)  {
			if ((isset($_POST['del_img'])) && ($_POST['del_img'] == 1)) {
				Utils::del_file($path, $img->img);
				Utils::del_file($path, 'thumb_'.$img->img);
				$post[0]['img'] = '';
			}
			$result = $mod->update('social_events', $_post['id'], $post);
		} else {
		//case add
			$result = $mod->insert('social_events', $post);
			
			//insert notify to me already viewed
			$notify = array(
						'from_user' => $_SESSION['user']->id,
						'to_user' => $_SESSION['user']->id,
						'what' => 'event',
						'id_what' => mysql_insert_id(),
						'viewed' => 1
						  );
			$mod->insert('social_notify', $notify);
		}
		header('Location: ?p=events');
		die;
	}
	
	public function delete($id) {
		$mod = new Db;
		//check permissions
		$user = $mod->get_by_id('social_events', $id);
		if ($user->from_user != $_SESSION['user']->id) {
			header('Location: ?p=msg&msg=permission_denied');
			die;
		}

		$event = $mod->get_by_id('social_events', $id);
		
		//navbar for insert
		echo '<div id="right_content">';
		echo '<div id="head_under"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i> Stai per eliminare un evento</div>';
		
		echo '<p class="acenter">Eliminare l\'evento \'<span class="bold">'.stripslashes($event->title).'</span>\' ?</p>';
		
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_SERVER['HTTP_REFERER'],
			'name' => 'from'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $event->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $event->img,
			'name' => 'img'
			);
		
		//on submit
		if (isset($_POST) && !empty($_POST)) {
			$this->deleting($_POST);
			die;
		}
		
		//prepare form
		echo Form::doform('ajaxform', $_SERVER["REQUEST_URI"], $fields, array('No', 'Si'), 'post', ' reloadpage="true" ', '', 'onclick="javascript:history.back();"');
		echo '</div>';
	}

	public function deleting($_post) {
		$mod = new Db();
		Utils::del_file('files/img/', $_post['img']) ;
		Utils::del_file('files/img/thumb_', $_post['img']) ;
		$result = $mod->delete('social_events', $_post['id']);
		$result = $mod->single_exec('DELETE FROM social_notify WHERE what = "event" AND id_what = '.$_post['id']);
		$result = $mod->single_exec('DELETE FROM social_confirms WHERE what = "event" AND id_what = '.$_post['id']);
		
		header('Location: '.$_POST['from']);
		die;
	}
	
	
	public function mini_events($when) {
		switch ($when) {
			case date('Y-m-d'):
				$day = 'oggi';
				$sql_when = ' AND DATE_FORMAT(date, \'%Y-%m-%d\') = "'.$when.'"';
				break;
			case date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y"))) :
				$day = 'domani';
				$sql_when = ' AND DATE_FORMAT(date, \'%Y-%m-%d\') = "'.$when.'"';
				break;
			case date("Y-m-d",mktime(0,0,0,date("m"),date("d")+2,date("Y"))) :
				$day = 'dopodomani';
				$sql_when = ' AND DATE_FORMAT(date, \'%Y-%m-%d\') = "'.$when.'"';
				break;
			case 'prossimi':
				$sql_when = ' AND DATE_FORMAT(date, \'%Y-%m-%d\') >= "'.date("Y-m-d",mktime(0,0,0,date("m"),date("d")+2,date("Y"))).'" ORDER BY date ASC LIMIT 30';
				break;
			default:
				$day = Utils::f_date($when);
				break;
		}
		
		$mod = new Db;
		$events = $mod->query('SELECT DISTINCT(social_events.id), social_events.* 
							 	FROM social_isfriend
							   INNER JOIN social_events ON (social_events.from_user = social_isfriend.id_user OR social_events.from_user = social_isfriend.id_friend)
							   INNER JOIN social_notify ON (social_events.id = social_notify.id_what AND social_notify.what = "event" AND social_notify.to_user = '.$_SESSION['user']->id.')
							   WHERE (social_isfriend.id_user = '.$_SESSION['user']->id.'
							   OR social_isfriend.id_friend = '.$_SESSION['user']->id.')
							   AND social_isfriend.confirmed = 1'.$sql_when);
		
		if ($events) {
			return array($day => $events);
		}
	}
	
	public function view_mini_events($array) {
		echo '<div id="menu">
				<table><tr>
				<td><span class="bold"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Eventi</span></td>
				<td class="aright"><a href="?p=events&type=all">Mostra</a></td>
				</tr></table>
				</div>';
		
		echo '<table>
				<tr>
				<td>';

		foreach ($array as $arr) {
			if ($arr) {
				foreach ($arr as $k => $i) {
					if ($i) {
						$found = 1;
						foreach ($i as $ii) {
							echo '<a href="?p=events&id_detail='.$ii->id.'"><span style="font-size:9px;">'.stripslashes($ii->title).'</a></span> <span class="xsmall">'.$k.'</span><br>';
						}
					}
				}
			}
		}
		
		if (!$found) {
			echo 'Nessun evento a breve.';	
		}
		echo '	</td>
				</tr>
				</table><br>
			';
	}
	
	public function invited_friends($id, $confirm) {
		$mod = new Db;
		$invited_friends = $mod->query('SELECT DISTINCT social_users.id, social_users.nome, social_users.cognome, social_users.img, social_confirms.confirmed
									   	FROM social_users
										INNER JOIN social_notify ON social_notify.to_user = social_users.id
										INNER JOIN social_confirms ON social_confirms.id_user = social_users.id
										WHERE social_confirms.id_what = '.$id.'
										AND social_notify.what = "event"
										AND social_confirms.what = "event"
										AND social_confirms.confirmed = '.$confirm.'
										ORDER BY RAND()');
		return $invited_friends;
	}

}
?>
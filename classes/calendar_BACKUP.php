<?php
class Calendar {

	public $userboard;
	
	public function __construct()
	{
		//of what user?
		$this->userboard = (isset($_GET['userboard'])) ? $_GET['userboard'] : $_SESSION['user']->id;

		//check is isfriend or am I
		if (($this->userboard != $_SESSION['user']->id) && (Utils::isfriend($this->userboard) != 1)) {
			header('Location: ?p=msg&userboard='.$this->userboard.'&msg=permission_denied');
		}
		//notify viewed
		$mod = new Db;
		$id_notify = $_GET['id_notify'];
		$post[] = array ('viewed' => 1);
		$mod->update('social_notify', $id_notify, $post);
	}
	
	public function list_calendars() {
		$mod = new Db;

		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Elenco calendari</div>';
		if ($this->userboard == $_SESSION['user']->id) {
			echo '<div id="menu">
					<table><tr><td class="aright">';
			echo '<form name="calendar" method="get">
					<input type="hidden" name="p" value="calendar">
					<input type="hidden" name="id_mod" value="0">
					Crea un calendario da ';
			echo '<select name="colums">';
			for ($n=1; $n<26; $n++) {
				echo '<option value="'.$n.'">'.$n.'</option>';
			}
			echo '</select>';
			echo ' colonne.
					<button>Crea</button></form>';		
			echo '</td></tr></table>
					</div><br>';
		}
		$calendars = $mod->query('SELECT * FROM social_calendar WHERE id_user = '.$this->userboard.' ORDER BY updated DESC');
		if ($calendars) {
			echo '<table id="results">';
			foreach ($calendars as $i) {
				$items = $mod->query('SELECT * FROM social_calendar_items WHERE id_calendar = '.$i->id);
				echo '<tr><td>
						<a class="bold" href="?p=calendar&userboard='.$this->userboard.'&id_detail='.$i->id.'">'.$i->title.'</a>
						<br>
						<span class="xsmall">'.Utils::f_date($i->updated).'</span>
						<br>';
				echo '<span class="xsmall">'.sizeof($items).' righe inserite</span>
						</td>
						<td class="aright">';
				if ($this->userboard == $_SESSION['user']->id) {
					echo '<a href="?p=calendar&id_del='.$i->id.'" title="Elimina" alt="Elimina"><img class="fright" src="'.BASE_URL.'files/img_private/thumb_delete.png"></a>
					<a href="?p=calendar&id_mod='.$i->id.'" title="Modifica" alt="Modifica"><img class="fright" src="'.BASE_URL.'files/img_private/thumb_edit.png"></a>';
					
					echo '</td></tr>';
				}
			}
			echo '</table>';
		} else {
			if ($this->userboard == $_SESSION['user']->id) {
				echo 'Nessun calendario inserito.<br>
					<form name="calendar" method="get">
					<input type="hidden" name="p" value="calendar">
					<input type="hidden" name="id_mod" value="0">
					Creane uno adesso da ';
				echo '<select name="colums">';
				for ($n=1; $n<26; $n++) {
					echo '<option value="'.$n.'">'.$n.'</option>';
				}
				echo '</select>';
				echo ' colonne.
					<button>Crea</button></form>';
			} else {
				echo 'Nessun calendario presente.';
			}
		}
	}
	
	public function detail_calendar($id) {
		$mod = new Db;
		$max_xpos = Utils::get_max_xpos('social_calendar_items', $id);
		$calendar = $mod->get_by_id('social_calendar', $id);
		if ($calendar) {
			$items = $mod->query('SELECT * FROM social_calendar_items WHERE id_calendar = '.$calendar->id.' ORDER BY xpos');

			echo '<div id="navbar">'.stripslashes($calendar->title).'</div>';
			echo '<div id="menu">
					<table><tr><td class="aright">';
					if ($this->userboard == $_SESSION['user']->id) {
						echo '<a class="bold" href="?p=calendar&id_mod=0&id_calendar='.$id.'">Aggiungi riga</a>'.SEP;
					}
					echo ' <a class="bold" href="?p=calendar&userboard='.$this->userboard.'">Torna ai calendari</a>
					</td></tr></table>
					</div><br>';
			if ($items) {
				echo '<table id="results"><tr>';
				//print headers
				for ($n=1; $n<($calendar->colums)+1; $n++) {
					eval('$col = $calendar->x'.$n.';');
					echo '<td class="borded bold">'.$col.'</td>';
				}
				if ($this->userboard == $_SESSION['user']->id) {
					echo '<td class="borded bold" style="width:65px;">Opzioni</td></tr>';
				}
				foreach ($items as $i) {
					echo '<tr>';
					//print rows
					for ($n=1; $n<($calendar->colums)+1; $n++) {
						eval('$row = $i->x'.$n.';');
						echo '<td class="borded">'.$row.'</td>';
					}
				
					if ($this->userboard == $_SESSION['user']->id) {
						echo '<td class="borded"><span class="acenter"><a href="?p=calendar&id_mod='.$i->id.'&id_calendar='.$calendar->id.'" title="Modifica" alt="Modifica"><img class="fleft" src="'.BASE_URL.'files/img_private/thumb_edit.png"></a>
						<a href="?p=calendar&id_del='.$i->id.'&id_calendar='.$calendar->id.'" title="Elimina" alt="Elimina"><img class="fleft" src="'.BASE_URL.'files/img_private/thumb_delete.png"></a>';

					//	if ($max_xpos[0]->xpos > 1) {
							$ud = Utils::updown($i->xpos, $max_xpos[0]->xpos, '?p=calendar&userboard='.$_SESSION['user']->id.'&id_detail='.$_GET['id_detail'].'&id='.$i->id.'&xpos='.$i->xpos);
					//	}
						echo $ud.'</span></td>';
						//Utils::updown($i->xpos, $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
					}
					echo '</tr>';
				}
				echo '</table>';
				echo '<br><span class="xsmall fright">Creato il '.Utils::f_date($calendar->updated).'</span>';
			} else {
				echo 'Nessuna riga inserita.';
			}
		} else {
			echo 'Nessun calendario visualizzabile.';
		}
	}
		
	public function edit($id = 0) {
		$mod = new Db;
		if ($id > 0) {
			//check permissions
			$user = $mod->get_by_id('social_calendar', $id);
			if ($user->id_user != $_SESSION['user']->id) {
				header('Location: ?p=msg&msg=permission_denied');
			}
			$output = '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Modifica calendario</div>';
			$calendar = $mod->get_by_id('social_calendar', $id);
		} else {
			$output = '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Crea un calendario</div>';
		}
		
		$output.= '<div id="menu">
					<table><tr><td class="aright">
					<a class="bold" href="?p=calendar&userboard='.$this->userboard.'">Torna ai calendari</a>
					</td></tr></table>
					</div>';
		
		$colums = ($_GET['colums']) ? $_GET['colums'] : $calendar->colums;
		
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
			'value' => $calendar->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $colums,
			'name' => 'colums'
			);
		$fields[] = array(
			'label' => 'Titolo calendario',
			'type' => 'text',
			'value' => stripslashes($calendar->title),
			'name' => 'title',
			'rule' => 'required',
			);
		
		for ($n=1; $n<$colums+1; $n++) {
			eval('$col = $calendar->x'.$n.';');
			$fields[] = array(
			'label' => 'Titolo colonna '.$n,
			'type' => 'text',
			'value' => $col,
			'name' => 'x'.$n,
			'rule' => 'required',
			);
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
		$output .= Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('Cancella','Modifica'), 'post', 'enctype="multipart/form-data"');
		
		echo $output;
	}
	
	public function edit_item($id_calendar, $id = 0) {
		$mod = new Db;
		$colums = $mod->get_by_id('social_calendar', $id_calendar);
		$rows = $mod->get_by_id('social_calendar_items', $id);

		if ($id > 0) {
			//check permissions
			$user = $mod->get_by_id('social_calendar', $id_calendar);
			if ($user->id_user != $_SESSION['user']->id) {
				header('Location: ?p=msg&msg=permission_denied');
			}
			$output = '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Modifica riga</div>';
			$item = $mod->get_by_id('social_calendar_items', $id);
		} else {
			$output = '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Aggiungi una riga</div>';
		}
		
		$output.= '<div id="menu">
					<table><tr><td class="aright">
					<a class="bold" href="?p=calendar&userboard='.$this->userboard.'&id_detail='.$id_calendar.'">Torna al calendario</a>
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
			'value' => $item->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_calendar,
			'name' => 'id_calendar'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $colums->colums,
			'name' => 'colums'
			);
		for ($n=1; $n<($colums->colums)+1; $n++) {
			eval('$col = $colums->x'.$n.';');
			eval('$row = $rows->x'.$n.';');
			$fields[] = array(
			'label' => $col,
			'type' => 'text',
			'value' => $row,
			'name' => 'x'.$n,
			);
		}

		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->editing_item($_POST);
				die;
			}
			else {
				Utils::set_error($fields);
			}
		}
		
		//prepare form
		$output .= Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('Cancella','Modifica'), 'post', 'enctype="multipart/form-data"');
		
		echo $output;
	}
		
	public function editing($_post) {
		$mod = new Db();
		$colums = ($_post['colums']) ? $_post['colums'] : $_GET['colums'];
		$post[] = array(
					'id_user' => $_SESSION['user']->id,
					'title' => $_post['title'],
					'colums' => $_post['colums'],
					);
		
		for ($n=1; $n<$colums+1; $n++) {
			$post[0]['x'.$n] = $_post['x'.$n];
		}

		//notify friends
		$friends = $mod->query('SELECT id_user, id_friend
							   FROM social_isfriend
							   WHERE (id_user = '.$_SESSION['user']->id.'
							   OR id_friend = '.$_SESSION['user']->id.')
							   AND confirmed = 1
						   ');
		//case edit
		if ($_post['id'] > 0)  {
			$result = $mod->update('social_calendar', $_post['id'], $post);
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'calendar_updated', $_post['id']);
			}
		} else {
		//case add
			$result = $mod->insert('social_calendar', $post);
			$new_calendar_id = mysql_insert_id();
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'calendar_created', $new_calendar_id);
			}
		}
		
		header('Location: ?p=calendar');
		
	}
	
		public function editing_item($_post) {
		$mod = new Db();
		$xpos = Utils::get_max_xpos('social_calendar_items', $_post['id_calendar']);
		$post[] = array(
					'id_calendar' => $_post['id_calendar'],
					);
		if (($xpos) && ($_post['id'] == 0)) {
			$post[0]['xpos'] = $xpos[0]->xpos+1;
		}

		for ($n=1; $n<$_post['colums']+1; $n++) {
			$post[0]['x'.$n] = $_post['x'.$n];
		}

		//notify friends
		$friends = $mod->query('SELECT id_user, id_friend
							   FROM social_isfriend
							   WHERE (id_user = '.$_SESSION['user']->id.'
							   OR id_friend = '.$_SESSION['user']->id.')
							   AND confirmed = 1
						   ');
		
		//case edit
		if ($_post['id'] > 0)  {
			$result = $mod->update('social_calendar_items', $_post['id'], $post);
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'calendar_updated', $_POST['id_calendar']);
			}
		} else {
		//case add
			$result = $mod->insert('social_calendar_items', $post);
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'calendar_added', $_POST['id_calendar']);
			}
		}
		
		header('Location: ?p=calendar&id_detail='.$_POST['id_calendar']);		
	}
	
	public function delete($id) {
		$mod = new Db;
		//check permissions
		$user = $mod->get_by_id('social_calendar', $id);
		if ($user->id_user != $_SESSION['user']->id) {
			header('Location: ?p=msg&msg=permission_denied');
		}

		$calendar = $mod->get_by_id('social_calendar', $id);
		
		//navbar for insert
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Stai per eliminare un calendario</div>';
		
		echo '<p class="acenter">Eliminare il calendario \'<span class="bold">'.stripslashes($calendar->title).'</span>\' ?</p>';
		
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
			'value' => $calendar->id,
			'name' => 'id'
			);
		
		//on submit
		if (isset($_POST) && !empty($_POST)) {
			$this->deleting($_POST);
			die;
		}
		
		//prepare form
		echo Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('No', 'Si'), 'post', '', '', 'onclick="javascript:history.back();"');

	}

	public function deleting($_post) {
		$mod = new Db();
		$result = $mod->delete('social_calendar', $_post['id']);
		$result = $mod->query('DELETE FROM social_calendar_items WHERE id_calendar = '.$_post['id']);
		header('Location: '.$_POST['from']);
	}
	
	public function delete_calendar_item($id_calendar, $id) {
		$mod = new Db;
		//check permissions
		$user = $mod->get_by_id('social_calendar', $id_calendar);
		if ($user->id_user != $_SESSION['user']->id) {
			header('Location: ?p=msg&msg=permission_denied');
		}

		$item = $mod->get_by_id('social_calendar_items', $id);
		
		//navbar for insert
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_calendar.png">Stai per eliminare una riga</div>';
		
		echo '<p class="acenter">Procedere con l\'eliminazione della riga?</p>';
		
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
			'value' => $item->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $item->id_calendar,
			'name' => 'id_calendar'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $item->xpos,
			'name' => 'xpos'
			);
		
		//on submit
		if (isset($_POST) && !empty($_POST)) {
			$this->deleting_item($_POST);
			die;
		}
		
		//prepare form
		echo Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('No', 'Si'), 'post', '', '', 'onclick="javascript:history.back();"');

	}

	public function deleting_item($_post) {
		$mod = new Db();
		$result = $mod->delete('social_calendar_items', $_post['id']);
		
		$n = Utils::get_max_xpos('social_calendar_items', $_post['id_calendar']);
		$result = Utils::reorder_xpos($n[0]->xpos, $_post['xpos'], $_post['id_calendar']);
		header('Location: ?p=calendar&id_detail='.$_post['id_calendar']);
	}

}
?>
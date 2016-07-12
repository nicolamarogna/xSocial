<?php
class Group {

	public function __construct()
	{
	}
	
	public function view() {
		$mod = new Db;
		echo '<div id="right_content">';
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_group.png">I miei gruppi</div>';
		echo '<div id="menu">
				<table><tr><td class="aright">
				<a class="bold" href="?p=group&id_mod=0">Crea un nuovo gruppo.</a>
				</td></tr></table>
				</div><br>';
		$groups = $mod->query('SELECT * FROM social_users WHERE group_admin = '.$_SESSION['user']->id);
		if ($groups) {
			echo '<table id="results">';
			foreach ($groups as $i) {
				echo '<tr><td style="width:350px;">
					<a class="bold" href="?userboard='.$i->id.'">';
				if ($i->img) {
					echo '<img class="fleft pad_all" src="files/img/thumb_'.$i->img.'">';
				}
				echo $i->group.'</a>';
				echo ($i->group_admin == $_SESSION['user']->id) ? '<br><span class="xsmall">(Amminstratore)</span>' : '';
				echo '</td>
						<td class="aright">';
				echo '<a href="?p=group&id_del='.$i->id.'"><img class="fright" src="'.BASE_URL.'files/img_private/thumb_delete.png" title="Elimina"></a>
					<a href="?p=group&id_mod='.$i->id.'"><img class="fright padright" src="'.BASE_URL.'files/img_private/thumb_edit.png" title="Modifica"></a>
					<a class="bold" href="?p=friends&type=notify&id_group='.$i->id.'"><img class="fright padright" src="'.BASE_URL.'files/img_private/thumb_invite.png" title="Invita amici"></a>';
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
			echo 'Nessun gruppo inserito. <a class="bold" href="?p=group&id_mod=0">Creane uno adesso.</a><br>';
		}
		
		
		echo '<br><div id="head_under"><img class="fright" src="files/img_private/thumb_group.png">Gruppi ai quali sei iscritto</div>';
		$groups = $mod->query('SELECT social_users.id, social_users.group FROM social_users
							 	INNER JOIN social_isfriend ON social_isfriend.id_group = social_users.id
							 	WHERE social_isfriend.confirmed = 1
								AND social_users.group_admin != '.$_SESSION['user']->id);
		if ($groups) {
			echo '<table id="results">';
			foreach ($groups as $i) {
				echo '<tr><td>
					<a class="bold" href="?userboard='.$i->id.'">';
				if ($i->img) {
					echo '<img class="fleft pad_all" src="files/img/thumb_'.$i->img.'">';
				}
				echo $i->group.'</a>';
				echo '</td></tr>';
			}
			echo '</table>';
		} else {
			echo 'Non sei iscritto a nessun gruppo.';
		}
		echo '</div>';
	}
	
	public function edit($id) {
		
		$output = '<div id="right_content">';
		if ($id > 0) {
			$mod = new Db;
			//check permissions
			$group = $mod->get_by_id('social_users', $id);
			if ($group->group_admin != $_SESSION['user']->id) {
				header('Location: ?p=msg&msg=permission_denied');
			}
			$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_group.png">Modifica gruppo</div>';
			$event = $mod->get_by_id('social_events', $id);
		} else {
			$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_group.png">Crea un gruppo</div>';
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
			'value' => $id,
			'name' => 'id',
			);
		$fields[] = array(
			'label' => 'Nome del gruppo',
			'type' => 'text',
			'value' => stripslashes($group->group),
			'name' => 'group',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Foto del gruppo',
			'type' => 'file',
			'value' => '',
			'old' => $group->img,
			'name' => 'img',
			);

		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->editing($_POST);
			}
			else {
				Utils::set_error($fields);
			}
		}
		
		//prepare form
		$output .= Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('Cancella','Modifica'), 'post', 'enctype="multipart/form-data"');
		$output .= '</div>';
		echo $output;

	}
	
	public function editing($_post) {
		$mod = new Db();
		
		$path = ROOT.'files/img/';
						
			//on file insert, upload file
			$filename = @Utils::upload('img', ROOT.'files/');
			$thumb = @Utils::create_resized($path.$filename, $path.'thumb_'.$filename, array(50,50));

			if ($filename === false) {
				header('Location: '.$_post['from']);
				die;
			}

			//delete old file if is set new file
			if ($filename)
			{
				Utils::del_file($path, $_post['img']);
				Utils::del_file($path, 'thumb_'.$_post['img']);
			}

			if (sizeof($filename) == 0) {
				$filename = $_post['old_img'];
			}
		
		$post[] = array(
					'`group`' => $_post['group'],
					'group_admin' => $_SESSION['user']->id,
					'img' => $filename,
					);
		
		//case edit
		if ($_post['id'] > 0)  {
			$result = $mod->update('social_users', $_post['id'], $post);
		} else {
		//case add
			$result = $mod->insert('social_users', $post);
			$add_friend[] = array(
					'id_user' => $_SESSION['user']->id,
					'id_friend' => mysql_insert_id(),
					'confirmed' => 1,
					);
			$mod->insert('social_isfriend', $add_friend);
		}
		header('Location: ?p=group');
		
	}
	
	public function delete($id) {
		$mod = new Db;
		//check permissions
		$group = $mod->get_by_id('social_users', $id);
		if ($group->group_admin != $_SESSION['user']->id) {
			header('Location: ?p=msg&msg=permission_denied');
			die;
		}
		
		echo '<div id="right_content">';
		//navbar for insert
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_group.png">Stai per eliminare un gruppo</div>';
		
		echo '<p class="acenter">Eliminare il gruppo \'<span class="bold">'.stripslashes($group->group).'</span>\' ?</p>';
		
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
			'value' => $group->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $group->img,
			'name' => 'img'
			);
		
		//on submit
		if (isset($_POST) && !empty($_POST)) {
			$this->deleting($_POST);
			die;
		}
		
		//prepare form
		echo Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('No', 'Si'), 'post', '', '', 'onclick="javascript:history.back();"');
		echo '</div>';
	}

	public function deleting($_post) {
		$mod = new Db();
		Utils::del_file('files/img/', $_post['img']) ;
		Utils::del_file('files/img/thumb_', $_post['img']) ;
		$result = $mod->delete('social_users', $_post['id']);
		/*
		$result = $mod->single_exec('DELETE FROM social_notify
									WHERE (what = "msg_group" OR what = "comment_group" OR what = "comment_answer_group")
									AND id_what = '.$_post['id']);
		*/
		
		//notifies
		//comments
		$trash = $mod->query('SELECT id FROM social_status WHERE from_user ='.$_post['id'].' OR to_user = '.$_post['id']);
		
		//delete all posts, notifies, comments of the group
		if ($trash) {
			foreach ($trash as $i) {
				$mod->single_exec('DELETE FROM social_notify
									WHERE (what = "group_created" OR what = "msg_group" OR what = "comment_group" OR what = "comment_answer_group")
									AND id_what = '.$i->id);
				$mod->single_exec('DELETE FROM social_comments
									WHERE id_comment = '.$i->id);
				$mod->delete('social_status', $i->id);
			}
		}
			$result = $mod->single_exec('DELETE FROM social_isfriend WHERE id_group ='.$_post['id'].' OR id_user ='.$_post['id'].' OR id_friend = '.$_post['id']);

		
		header('Location: '.$_POST['from']);
		die;
	}

}

?>
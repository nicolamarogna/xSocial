<?php
class Album {

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
	
	public function list_albums() {
		$mod = new Db;
		
		echo '<div id="right_content">';
		
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Elenco album</div>';
		if ($this->userboard == $_SESSION['user']->id) {
			echo '<div id="menu">
					<table><tr><td class="aright">
					<a class="bold" href="?p=album&id_mod=0">Crea un nuovo album.</a>
					</td></tr></table>
					</div><br>';
		}
		$albums = $mod->query('SELECT * FROM social_albums WHERE id_user = '.$this->userboard.' ORDER BY updated DESC');
		$myphoto = $mod->query('SELECT
							img
							FROM social_status
							WHERE img != "" AND from_user = '.$_SESSION['user']->id.' AND to_user = '.$_SESSION['user']->id.' ORDER BY RAND() LIMIT 1');
							//ORDER BY upd DESC');
		echo '<table id="results">';
		echo '<tr><td style="width:120px;">
		<a class="bold" href="?p=album&userboard='.$_SESSION['user']->id.'&id_detail=mymedia'.$i->id.'">
		<img class="fleft img_event crop" src="files/img/thumb_'.$myphoto[0]->img.'">
		</a>
		</td><td>
		<a class="bold" href="?p=album&userboard='.$_SESSION['user']->id.'&id_detail=mymedia'.$i->id.'">I miei media</a>
		</td></tr>';
		echo '</table>';
		
		if ($albums) {
			echo '<table id="results">';
			foreach ($albums as $i) {
				$items = $mod->query('SELECT img FROM social_albums_items WHERE id_album = '.$i->id.' ORDER BY RAND() LIMIT 1');
				echo '<tr><td style="width:120px;">
					<a class="bold" href="?p=album&userboard='.$this->userboard.'&id_detail='.$i->id.'">';
				if ($items > 0) {
					echo '<img class="fleft img_event crop" src="files/img/thumb_'.$items[0]->img.'">';
				} else {
					echo '<img class="fleft img_event crop" src="files/img_private/thumb_img_profile_null.jpg">';
				}
				echo '</a></td><td>
						<a class="bold" href="?p=album&userboard='.$this->userboard.'&id_detail='.$i->id.'">'.$i->title.'</a>
						<br>
						<span class="xsmall">'.Utils::f_date($i->updated).'</span>
						<br>';
				echo '<span class="xsmall">'.sizeof($items).' foto</span>
						</td>
						<td class="aright">';
				if ($this->userboard == $_SESSION['user']->id) {
					echo '<a href="?p=album&id_del='.$i->id.'"><img class="fright" src="'.BASE_URL.'files/img_private/thumb_delete.png" title="Elimina album" alt="Elimina album"></a>
					<a href="?p=album&id_mod='.$i->id.'"><img class="fright" src="'.BASE_URL.'files/img_private/thumb_edit.png" title="Modifica album" alt="Modifica album"></a>';
					echo '</td></tr>';
				}
			}
			echo '</table>';
		} else {
			if ($this->userboard == $_SESSION['user']->id) {
				echo 'Nessun album inserito. <a class="bold" href="?p=album&id_mod=0">Creane uno adesso.</a>';
			} else {
				echo 'Nessun album presente.';
			}
		}
		
		echo '</div>';
	}
	
	public function detail_album($id) {
		$mod = new Db;
		$album = $mod->get_by_id('social_albums', $id);
		
		if (!$album) {
			header('Location: ?p=msg&userboard='.$this->userboard.'&msg=not_found');
			die;
		}
		
		$photos = $mod->query('SELECT *	FROM social_albums_items WHERE id_album = '.$album->id.' ORDER BY updated ASC');
		
		echo '<div id="right_content">';
		echo '<div id="navbar">'.stripslashes($album->title).'</div>';
		echo '<div id="menu">
				<table><tr><td class="aright">';
				if ($this->userboard == $_SESSION['user']->id) {
					echo '<a class="bold" href="?p=album&id_mod=0&id_album='.$id.'">Aggiungi foto</a>'.SEP;
				}
				echo ' <a class="bold" href="?p=album&userboard='.$this->userboard.'">Torna agli album</a>
				</td></tr></table>
				</div><br>';
		if ($photos) {
			$c=1;
			echo '<table><tr>';	
			foreach ($photos as $i) {
				echo '<td style="padding:0 3px;">
				<a href="files/img/'.$i->img.'" class="fancybox" rel="gallery" title="'.stripslashes($i->title).'">
				<img class="crop" src="files/img/thumb_'.$i->img.'" alt="'.stripslashes($i->title).'" title="'.stripslashes($i->title).'">
				</a>';
				if ($this->userboard == $_SESSION['user']->id) {
					echo '<br><div class="acenter">
					<a href="?p=album&id_mod='.$i->id.'&id_album='.$album->id.'"><img src="'.BASE_URL.'files/img_private/thumb_edit.png" title="Modifica foto" alt="Modifica foto"></a>
					<a href="?p=album&id_del='.$i->id.'&id_album='.$album->id.'"><img src="'.BASE_URL.'files/img_private/thumb_delete.png" title="Elimina foto" alt="Elimina foto"></a>
					</div>';
				}
				echo '</td>';
				if ($c == 4) {
					echo '</tr><tr>';
					$c=0;
				}
				$c++;
			}
			echo '</tr></table>';
			echo '<br><span class="xsmall fright">Creato il '.Utils::f_date($album->updated).'</span><div class="clear"></div>';
		} else {
			echo 'Nessuna foto inserita.';
		}
		echo '</div>';
	}
		
	public function detail_mymedia() {
		$mod = new Db;
		echo '<div id="right_content">';
		echo '<div id="navbar">I miei media</div>';
		echo '<div id="menu">
				<table><tr><td class="aright">';
				echo ' <a class="bold" href="?p=album&userboard='.$this->userboard.'">Torna agli album</a>
				</td></tr></table>
				</div><br>';
		
		$items = $mod->query('SELECT
							id,
							updated,
							img as upl_img,
							youtube 
							FROM social_status
							WHERE (img != "" OR youtube != "") AND from_user = '.$_SESSION['user']->id.' AND to_user = '.$_SESSION['user']->id.' ORDER BY id DESC');
							//ORDER BY upd DESC');
		
		if ($items) {
			$c=1;
			echo '<table><tr>';	
			foreach ($items as $i) {
				echo '<td style="padding:0 3px;">';
				if ($i->upl_img) {
					echo '<a rel="gallery" href="files/img/'.$i->upl_img.'" class="fancybox"><img class="crop" src="files/img/thumb_'.$i->upl_img.'">';
					echo '<div style="position:relative;top:-28px;left:5px;opacity:0.8;"/><img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_foto.png"></div></a>';
				}
				if ($i->youtube) {
					preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $i->youtube, $match);
					echo '<a href="http://www.youtube.com/v/'.$match[1].'?hl=en&autoplay=1&showsearch=0&rel=0&TB_iframe=true&width=430&height=280&background=#000" class="fancybox-media"><img class="crop" src="http://img.youtube.com/vi/'.$match[1].'/1.jpg">';
					echo '<div style="position:relative;top:-28px;left:5px;opacity:0.8;"/><img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_youtube.png"></div></a>';
				}
				echo '</td>';
				if ($c == 4) {
					echo '</tr><tr>';
					$c=0;
				}
				$c++;
			}
			echo '</tr></table>';
			echo '<div class="clear"></div>';
		} else {
			echo 'Nessuna foto inserita.';
		}
		echo '</div>';
	}
		
	public function edit($id = 0) {
		$mod = new Db;
		
		$output = '<div id="right_content">';
		if ($id > 0) {
			//check permissions
			$user = $mod->get_by_id('social_albums', $id);
			if ($user->id_user != $_SESSION['user']->id) {
				header('Location: ?p=msg&msg=permission_denied');
			}
				$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Modifica album</div>';
				$album = $mod->get_by_id('social_albums', $id);
			} else {
				$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Crea un album</div>';
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
			'value' => $album->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => 'Titolo',
			'type' => 'text',
			'value' => stripslashes($album->title),
			'name' => 'title',
			'rule' => 'required',
			);

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
		$output .= '</div>';
		echo $output;
	}
	
	public function edit_photo($id_album, $id = 0) {
		$mod = new Db;
		
		$output = '<div id="right_content">';
		
		if ($id > 0) {
			//check permissions
			$user = $mod->get_by_id('social_albums', $id_album);
			if ($user->id_user != $_SESSION['user']->id) {
				header('Location: ?p=msg&msg=permission_denied');
			}
			$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Modifica foto</div>';
			$photo = $mod->get_by_id('social_albums_items', $id);
		} else {
			$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Aggiungi una foto</div>';
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
			'value' => $photo->id,
			'name' => 'id'
			);
		$fields[] = array(
			'label' => 'Titolo',
			'type' => 'text',
			'value' => stripslashes($photo->title),
			'name' => 'title',
			);
		$fields[] = array(
			'label' => 'Descrizione',
			'type' => 'textarea',
			'value' => stripslashes($photo->description),
			'name' => 'description',
			);
		$fields[] = array(
			'label' => 'Immagine',
			'type' => 'file',
			'value' => '',
			'old' => $photo->img,
			'name' => 'img',
			'rule' => (!$photo->img) ? 'required' : ''
			);
		$fields[] = array(
			'label' => NULL,
			'type' => 'hidden',
			'value' => $id_album,
			'name' => 'id_album',
			);

		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->editing_photo($_POST);
				die;
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
		
		$post[] = array(
					'title' => $_POST['title'],
					'id_user' => $_SESSION['user']->id,
					);

		//notify friends
		$friends = $mod->query('SELECT id_user, id_friend
							   FROM social_isfriend
							   WHERE (id_user = '.$_SESSION['user']->id.'
							   OR id_friend = '.$_SESSION['user']->id.')
							   AND confirmed = 1
						   ');
		//case edit
		if ($_post['id'] > 0)  {
			$result = $mod->update('social_albums', $_post['id'], $post);
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'album_updated', $_post['id']);
			}
		} else {
		//case add
			$result = $mod->insert('social_albums', $post);
			$new_album_id = mysql_insert_id();
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'album_created', $new_album_id);
			}
		}
		
			header('Location: ?p=album');
			die;
		
	}
	
		public function editing_photo($_post) {
		$mod = new Db();
		$path = ROOT.'files/img/';
						
			//on file insert, upload file
			$filename = @Utils::upload('img', ROOT.'files/');
			
			$thumb = @Utils::create_resized($path.$filename, $path.'thumb_'.$filename, array(100,100));

			if ($filename === false) {
				header('Location: '.$_POST['from']);
				die;
			}
			if ($_post['id'] > 0)  {
				$img = $mod->get_by_id('social_albums_items', $_post['id']);
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
					'description' => $_POST['description'],
					'img' => $filename,
					'id_album' => $_POST['id_album'],
					);
		
		//notify friends
		$friends = $mod->query('SELECT id_user, id_friend
							   FROM social_isfriend
							   WHERE (id_user = '.$_SESSION['user']->id.'
							   OR id_friend = '.$_SESSION['user']->id.')
							   AND confirmed = 1
						   ');
		
		//case edit
		if ($_post['id'] > 0)  {
			$result = $mod->update('social_albums_items', $_post['id'], $post);
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'photo_updated', $_POST['id_album']);
			}
		} else {
		//case add
			$result = $mod->insert('social_albums_items', $post);
			foreach ($friends as $i) {
				$to = ($i->id_user == $_SESSION['user']->id) ? $i->id_friend : $i->id_user;
				$mod->notify($_SESSION['user']->id, $to, 'photo_added', $_POST['id_album']);
			}
		}
		
		header('Location: ?p=album&id_detail='.$_POST['id_album']);
		die;	
	}
	
	public function delete($id) {
		$mod = new Db;
		//check permissions
		$user = $mod->get_by_id('social_albums', $id);
		if ($user->id_user != $_SESSION['user']->id) {
			header('Location: ?p=msg&msg=permission_denied');
		}

		$album = $mod->get_by_id('social_albums', $id);
		
		//navbar for insert
		echo '<div id="right_content">';
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Stai per eliminare un album</div>';
		
		echo '<p class="acenter">Eliminare l\'album \'<span class="bold">'.stripslashes($album->title).'</span>\' ?<br>
		Attenzione: verranno eliminate anche tutte le immagini contenute in quest\'album!</p>';
		
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
			'value' => $album->id,
			'name' => 'id'
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
		$result = $mod->delete('social_albums', $_post['id']);
		$result = $mod->query('DELETE FROM social_albums_items WHERE id_album = '.$_post['id']);
		header('Location: '.$_POST['from']);
		die;
	}
	
	public function delete_photo($id_album, $id) {
		$mod = new Db;
		//check permissions
		$user = $mod->get_by_id('social_albums', $id_album);
		if ($user->id_user != $_SESSION['user']->id) {
			header('Location: ?p=msg&msg=permission_denied');
		}

		$photo = $mod->get_by_id('social_albums_items', $id);
		
		//navbar for insert
		echo '<div id="right_content">';
		echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_foto.png">Stai per eliminare una foto</div>';
		
		echo '<p class="acenter">Eliminare la foto \'<span class="bold">'.stripslashes($photo->title).'</span>\' ?</p>';
		
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
			'value' => $photo->id,
			'name' => 'id'
			);
		
		//on submit
		if (isset($_POST) && !empty($_POST)) {
			$this->deleting_photo($_POST);
			die;
		}
		
		//prepare form
		echo Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('No', 'Si'), 'post', '', '', 'onclick="javascript:history.back();"');
		echo '</div>';
	}

	public function deleting_photo($_post) {
		$mod = new Db();
		$result = $mod->delete('social_albums_items', $_post['id']);
		header('Location: '.$_POST['from']);
		die;
	}

}
?>
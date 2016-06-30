<?php
	//delete profile
	if ((isset($_POST['action'])) && ($_POST['action'] == 'delete_profile')) {
		$db = new Db();
			//CASE DELETE
			$db->query("UPDATE social_users SET email_old = '".$_POST['email']."', email = '', xon = 0  WHERE id = ".$_POST['id']);
			Utils::logout();
		die;
	}

class Profile {

	public function __construct()
	{
	}
	
	public function myprofile() {
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
			'name' => 'x'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'name' => 'y'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'name' => 'x2'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'name' => 'y2'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'name' => 'w'
			);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'name' => 'h'
			);
		$fields[] = array(
			'label' => 'Nome',
			'type' => 'text',
			'value' => stripslashes($_SESSION['user']->nome),
			'name' => 'nome',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Cognome',
			'type' => 'text',
			'value' => stripslashes($_SESSION['user']->cognome),
			'name' => 'cognome',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Email',
			'type' => 'text',
			'value' => $_SESSION['user']->email,
			'name' => 'email',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Data di nascita',
			'type' => 'text',
			'value' => $_SESSION['user']->birthday,
			'name' => 'birthday',
			'rule' => 'required',
			'extra' => 'class="dashboard_notime"',
			);
		$fields[] = array(
			'label' => 'Citt&agrave;',
			'type' => 'text',
			'value' => stripslashes($_SESSION['user']->citta),
			'name' => 'citta',
			'rule' => 'required',
			);
		$fields[] = array(
			'label' => 'Foto',
			'type' => 'file',
			'value' => '',
			'old' => $_SESSION['user']->img,
			'name' => 'img',
			);
		$fields[] = array(
			'label' => NULL,
			'type' => 'paragraph',
			'value' => 'Attenzione: alla modifica, dovrai effettuare il login per utilizzare i nuovi dati!',
			);

		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->modify($_POST);
				$_SESSION['action'] = 'Utils::logout();';
				header('Location: ?p=actions');
				die;
			}
			else {
				Utils::set_error($fields);
			}
		}
		
		//prepare form
		$output .= '<div id="right_content">';
		$output .= '<div id="head_under"><img class="fright" src="files/img_private/thumb_profile.png">Il mio profilo</div>';
		$output .= '<img id="displayImg" src="#" alt="" />';
		$output .= Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('Cancella','Modifica'), 'post', 'enctype="multipart/form-data"');
		$output .= '</div>';
		echo $output;

	}
	
	public function modify() {
		$mod = new Db();

		$path = ROOT.'files/img/';
	
			//on file insert, upload file
			$filename = Utils::upload('img', ROOT.'files/');
			$thumb = Utils::create_resized($path.$filename, $path.'thumb_'.$filename, array(50,50));
			$crop = Utils::create_crop($path.$filename, $path.'crop_'.$filename, $_POST['x'],$_POST['y'],200,200,$_POST['w'],$_POST['h']);
			if ($filename === false) {
				header('Location: '.$_POST['from']);
				die;
			}

			//delete old file if is set new file
			if ($filename)
			{
				Utils::del_file($path, $_SESSION['user']->img);
				Utils::del_file($path, 'thumb_'.$_SESSION['user']->img);
				Utils::del_file($path, 'crop_'.$_SESSION['user']->img);
			}

			if (sizeof($filename) == 0) {
				$filename = $_POST['old_img'];
			}
		
		$post[] = array(
					'nome' => $_POST['nome'],
					'cognome' => $_POST['cognome'],
					'email' => $_POST['email'],
					'birthday' => Utils::date_it($_POST['birthday']),
					'citta' => $_POST['citta'],
					'img' => $filename,
					);
		$mod->update('social_users', $_SESSION['user']->id, $post);
	}
	
	public function deleteProfile() {
		echo '<hr>';
		Utils::flipButton('Elimina il mio profilo', 'href = "?p=profile_actions" action="delete_profile" reloadPage="true" id='.$_SESSION['user']->id);
		
	}
	
}

?>
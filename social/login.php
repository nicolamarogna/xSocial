<?php
class Login {

	public function __construct()
	{
		$this->view();
	}
	
	
	public function view() {
		if (!isset($_SESSION['user'])) $_SESSION['user'] = false;
		
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_SERVER['HTTP_REFERER'],
			'name' => 'from'
			);
		$fields[] = array(
			'label' => 'Username',
			'type' => 'text',
			'value' => '',
			'name' => 'user',
			'rule' => 'required'
			);
		$fields[] = array(
			'label' => 'Password',
			'type' => 'password',
			'value' => '',
			'name' => 'pass',
			'rule' => 'required'
			);

		//on submit
		if (isset($_POST) && !empty($_POST)) {
		$e = Form::validation($fields);
			if ($e) {
				$this->do_login($_POST);
				die;
			}
			else {
				Utils::set_error($fields);
			}
		}
		
		//prepare form
		echo Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array('Cancella', 'Invia'), 'post', 'enctype="multipart/form-data"');
	}
	
	// login
	public function do_login()
	{		
		if (!empty($_POST['user']) && !empty($_POST['pass'])) {
			
			$mod = new Db;
			$user = $mod->query('SELECT *, DATE_FORMAT(birthday,"%d-%m-%Y") as birthday FROM social_users WHERE user = "'.$_POST['user'].'" AND password = "'.$_POST['pass'].'" AND xon != 0');

			($user) ? $_SESSION['user'] = true : $_SESSION['user'] = false;

			// content
			if ($_SESSION['user'] == true) {
				$_SESSION['user'] = $user[0];
				
                echo '<meta http-equiv="refresh" content="0;URL=index.php" />';
                
				//header('Location: index.php');
				die;
			} else {
				echo '<br>Attenzione: non hai i permessi per accedere al pannello di amministrazione!<br><a href="index.php">Riprova</a>';
			}
		}
	}
			
}

new Login();
?>
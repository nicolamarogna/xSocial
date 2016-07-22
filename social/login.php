<?php
class Login {

	public function __construct()
	{
		$this->view();
	}
	
	/*
	<?php
$url = 'https://graph.facebook.com/v2.7/me?access_token=EAAUJti9xh0MBAHn57NdzOFtYz6dtGbvR6uqJsljZB62lf0om5vuKMdZBJtCCjPGSRxwU2LLEhKVjmGh6IZCLshve21GYR3FxMX6JBy27y3XEwAKPT6VdCBUqJXFulq6qbgH8ndIg2vcaFNhn9aZBINuSePLRJjP7q5OhkFTllwZDZD';
$content = file_get_contents($url);
$json = json_decode($content, true);

print_r($json);
/* handle the result */
/*-
curl -i -X GET \
 "https://graph.facebook.com/v2.7/me?access_token=EAAUJti9xh0MBAHn57NdzOFtYz6dtGbvR6uqJsljZB62lf0om5vuKMdZBJtCCjPGSRxwU2LLEhKVjmGh6IZCLshve21GYR3FxMX6JBy27y3XEwAKPT6VdCBUqJXFulq6qbgH8ndIg2vcaFNhn9aZBINuSePLRJjP7q5OhkFTllwZDZD"
 */

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
			'label' => null,
			'type' => 'hidden', 
			'value' => 'login',
			'name' => 'login'
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
		if (isset($_POST) && !empty($_POST) && ($_POST['login'])) {
			$e = Form::validation($fields);
			if ($e) {
				$this->do_login($_POST);
				die;
			} else {
				Utils::set_error($fields);
			}
		}
		if (isset($_POST) && !empty($_POST) && (!$_POST['login'])) {
			$this->register($_POST);
			die;
		}
		echo '<div id="right_content"><div id="head_under">Login</div>';
		
		//prepare form
		echo Form::doform('formadd', $_SERVER["REQUEST_URI"], $fields, array(NULL, 'Accedi'), 'post', 'enctype="multipart/form-data"');
        echo '<button onclick="_login();" type="submit">Accedi con Facebook</button></div>';
        
        echo '<div id="right_content"><div id="head_under">Registrati</div>
			<form class="formsignup" name="formsignup" action="'.$_SERVER["REQUEST_URI"].'" method="post" enctype="multipart/form-data">
			<div id="status"></div>
        	<h2>Registrati</h2>
       		<fieldset>
			<label for="nome" >Nome *</label>
			<input type="text" name="nome" id="nome" value="" required>
			<label for="cognome" >Cognome *</label>
			<input type="text" name="cognome" id="cognome" value="" required>
       		<label for="email" >Email *</label>
			<input type="text" name="email" id="email" value="" required>
	   		<label for="email_verify" >Conferma email *</label>
			<input type="text" name="email_verify" id="email_verify" value="" required>
	   		<label for="password" >Password *</label>
			<input type="password" name="password" id="password" value="" required>
        	<div class="clear"></div>
        <button type="submit">Iscriviti</button>
        <button onclick="_login();" type="submit">Iscriviti con Facebook</button>
      </fieldset>
	  </form>
     </div>';
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
	
	// register
	public function register()
	{
		if (!empty($_POST['nome']) && !empty($_POST['cognome']) && !empty($_POST['email']) && !empty($_POST['password']) && ($_POST['email'] == $_POST['email_verify'])) {
			
			$mod = new Db;
			$user = $mod->query('SELECT * FROM social_users WHERE email = "'.$_POST['email'].'" AND xon = 1');
			
			if (!$user) {			
				$post = array(
						'nome' => $_POST['nome'],
						'cognome' => $_POST['cognome'],
						'email' => $_POST['email'],
						'user' => $_POST['email'],
						'password' => $_POST['password'],
						'livello' => 1,
						'xon' => 1,
						);
				$mod->insert('social_users', $post);
                echo '<br>Ora puoi fare il login con le credenziali scelte!<br><a href="index.php">Vai</a>';
			} else {
				echo '<br>Attenzione: l\'indirizzo email è già presente nel nostro database!<br><a href="index.php">Riprova</a>';
			}
		} else {
			echo '<br>Attenzione: è avvenuto un errore!<br><a href="index.php">Riprova</a>';
		}
	}
			
}

new Login();
?>
<?php
	//delete profile
	if ((isset($_GET['action'])) && ($_GET['action'] == 'delete_profile')) {
		$db = new Db();
			$db->query("UPDATE social_users SET email_old = '".$_SESSION['user']->email."', email = '', xon = 0  WHERE id = ".$_SESSION['user']->id);
			Utils::logout();
		die;
	}
?>
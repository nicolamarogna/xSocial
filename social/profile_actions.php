<?php
	//delete profile
	if ((isset($_POST['action'])) && ($_POST['action'] == 'delete_profile')) {
		$db = new Db();
			$db->query("UPDATE social_users SET email_old = '".$_POST['email']."', email = '', xon = 0  WHERE id = ".$_POST['id']);
			Utils::logout();
		die;
	}
?>
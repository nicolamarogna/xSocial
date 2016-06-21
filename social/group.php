<?php
	Utils::logged();
	
	$mod = new Group;
/*
//confirms partecipation
	if ($_POST['confirm']) {
		$db = new Db;
		
		//case add
		if ($_POST['method'] != 'checked') {
			$post = array(
				'id_user' => $_SESSION['user']->id,
				'what' => 'event',
				'id_what' => $_POST['id_what'],
				'confirmed' => $_POST['confirm'],
				);
			$db->insert('social_confirms', $post);
		} else {
			//case update
			$post = array(
				'confirmed' => $_POST['confirm'],
				);
			$db->update('social_confirms', $_POST['id_confirm'], $post);
		}
	}
	
*/	
	if (isset($_GET['id_mod'])) {
		//modify
		$mod->edit($_GET['id_mod']);
	} elseif (isset($_GET['id_del'])) {
		//delete
		$mod->delete($_GET['id_del']);
	} elseif (isset($_GET['id_detail'])) {
		//detail
		$mod->detail_event($_GET['id_detail']);
	} elseif (isset($_GET['type'])) {
		//all in program
		$mod->list_events_in_program();
	} else {
		//view
		$mod->view();
	}

?>
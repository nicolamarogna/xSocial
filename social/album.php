<?php
	Utils::logged();
	
	$mod = new Album;

	if (isset($_GET['id_mod'])) {
		//modify
		if (isset($_GET['id_album'])) {
			$mod->edit_photo($_GET['id_album'], $_GET['id_mod']);
		} else {
			$mod->edit($_GET['id_mod']);
		}
	} elseif (isset($_GET['id_del'])) {
		//delete
		if (isset($_GET['id_album'])) {
			$mod->delete_photo($_GET['id_album'], $_GET['id_del']);
		} else {
			$mod->delete($_GET['id_del']);
		}
	} elseif (isset($_GET['id_detail'])) {
		//detail
		if ($_GET['id_detail'] == 'mymedia') {
			$mod->detail_mymedia();
		} else {
			$mod->detail_album($_GET['id_detail']);
		}
	} else {
		//view
		$mod->list_albums();
	}
?>
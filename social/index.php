<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
	
	ob_start();
	!ini_get('session.auto_start') ? session_start() : '';
	$SID = session_id();
	$root = str_replace('\\','/', getcwd());
	
	// let's make sure the $_SERVER['DOCUMENT_ROOT'] variable is set
	if(!isset($_SERVER['DOCUMENT_ROOT'])){ if(isset($_SERVER['SCRIPT_FILENAME'])){
	$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
	}; };
	if(!isset($_SERVER['DOCUMENT_ROOT'])){ if(isset($_SERVER['PATH_TRANSLATED'])){
	$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
	}; };
	// $_SERVER['DOCUMENT_ROOT'] is now set - you can use it as usual...
	
	define('SITE_TITLE', "xSocial");
	define('INIT_DIR', '/social/xSocial/social/');

	//////////////////////////////
	//CONFIG DB IN classes/db.php
	//////////////////////////////

	define('SEP', ' · ');

	define('ROOT', $_SERVER['DOCUMENT_ROOT'].INIT_DIR);
	define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].INIT_DIR);

	//items for pagination
	define('PP', 20);
	
	//enable ratings YES/NO
	define('ENABLE_RATING', 'YES');
	
	define('EXT', '.php');		// file extension

	//Dimensione massima immagini in KB
	define('MAX_IMG', 51200); //img max 2MB
	define('MAX_DOC', 5120); //doc max 5MB
	//Dimensione massima immagini in pixel
	define('MAX_W', 3000);
	define('MAX_H', 3000);
	define('NL', "\n");
	
	//autoload classes
	function __autoload($class_name) {
   	
	$a = require_once('../classes/'.strtolower($class_name).'.php');
		if (!$a) {
			require_once(strtolower($class_name).'.php');
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
<title><?php echo SITE_TITLE; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link href='https://fonts.googleapis.com/css?family=Hind:300,600' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="../js/bootstrap.css">

<link rel="stylesheet" type="text/css" href="homescreen.css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="../js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<link rel="stylesheet" href="../js/rating/themes/font-awesome.min.css">
<link rel="stylesheet" href="../js/rating/themes/fontawesome-stars-o.css">
<link rel="stylesheet" href="../js/mediaelementplayer.css">
<link rel="stylesheet" href="../js/jcrop/css/jquery.Jcrop.css">
<link rel="stylesheet" href="../js/jquery-confirm.css">
<link rel="stylesheet" href="../js/jquery.liveurl.css">
<link rel="stylesheet" href="../js/pace.css">

	<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/datepicker.js" >//</script>
	<script type="text/javascript" src="../js/domready.js" >//</script>
    <script type="text/javascript" src="../js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
	<script type="text/javascript" src="../js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
	<script type="text/javascript" src="../js/fancybox/source/helpers/jquery.fancybox-media.js?v=2.1.5"></script>
	<script type="text/javascript" src="../js/capture-video-frame.js" >//</script>
    <script type="text/javascript" src="../js/mediaelement-and-player.min.js" >//</script>
    <script type="text/javascript" src="../js/rating/jquery.barrating.min.js" >//</script>
    <script type="text/javascript" src="../js/jcrop/jquery.Jcrop.min.js" >//</script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.sticky/1.0.3/jquery.sticky.min.js"></script>
    <script type="text/javascript" src="../js/jquery-confirm.min.js" >//</script>
    <script type="text/javascript" src="../js/jquery.liveurl.js" >//</script>
    <script type="text/javascript" src="../js/pace.min.js" >//</script>
    
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div id="content">
<div id="head">
  <?php
	if (Utils::logged() != 0) {
		$mod = new Search;
		$mod->search_friends_module();
	}
?>
</div>
<?php
	if (Utils::logged() == 0) {
		include('login.php');
	} else {
		//if user o pass is empty, don't enter!
		if ($_SESSION['user']->nome == '' OR $_SESSION['user']->cognome == '') {
			unset($_SESSION['user']);	
			echo '<meta http-equiv="refresh" content="0;URL=index.php" />';
			die;
		}
		include('home.php');
	}
?>

<div class="clear"></div>
<div class="aright xsmall">
  XSocial © 2010</div>
</div>
</body></html>
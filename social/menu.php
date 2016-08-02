<?php
class Menu {

	public function __construct()
	{
		
	}
	
	//my menu
	public function mymenu() {
		$mod = new Db;
		$_SESSION['action'] = 'Utils::logout();';
		if ($_SESSION['user']->img) {
			$path = 'files/img/crop_';
			$img = $_SESSION['user']->img;
		} else {
			$path = 'files/img_private/';
			$img = 'img_profile_null.jpg';
		}
		echo '<a href="?userboard='.$_SESSION['user']->id.'" title="'.stripslashes($_SESSION['user']->nome).' '.stripslashes($_SESSION['user']->cognome).'" alt="'.stripslashes($_SESSION['user']->cognome).' '.stripslashes($_SESSION['user']->cognome).'">
			<img class="img_profile" src="'.$path.$img.'">
			</a>';

			echo '
				<div id="menu">
				<table>
				<tr><td><a href="index.php">'.stripslashes($_SESSION['user']->nome).' '.stripslashes($_SESSION['user']->cognome).'</a></td></tr>
				<tr><td><a href="?type=all"><i class="fa fa-users fright" aria-hidden="true"></i>Bacheca degli amici</a></td></tr>
				<tr><td><a href="?p=album"><i class="fa fa-camera fright" aria-hidden="true"></i>Le mie foto</a></td></tr>
				<tr><td><a href="?p=events"><i class="fa fa-calendar-check-o fright" aria-hidden="true"></i>I miei eventi</a></td></tr>
				<tr><td><a href="?p=group"><i class="fa fa-copyright fa-rotate-90 fright" aria-hidden="true"></i>I miei gruppi</a></td></tr>
				<tr><td><a href="?p=calendar"><i class="fa fa-calendar fright" aria-hidden="true"></i>I miei calendari</a></td></tr>
				<tr><td><a href="?p=profile"><i class="fa fa-user fright" aria-hidden="true"></i>Il mio profilo</a></td></tr>
				<tr><td><a href="?p=actions"><i class="fa fa-sign-out fright" aria-hidden="true"></i>Logout</a></td></tr>
				</table>
				</div>
				<br>';

		//friends area
		$friends = $mod->query('SELECT social_users.id, social_users.nome, social_users.cognome, social_users.img FROM social_isfriend
							   INNER JOIN social_users ON (social_users.id = social_isfriend.id_user
							   	OR social_users.id = social_isfriend.id_friend) AND social_users.id != '.$_SESSION['user']->id.'
							   WHERE (social_isfriend.id_user = '.$_SESSION['user']->id.'
							   OR social_isfriend.id_friend = '.$_SESSION['user']->id.')
							   AND social_users.xon = 1
							   AND social_isfriend.confirmed = 1
							   AND social_isfriend.id_group = 0
							   AND social_users.nome != "" AND social_users.cognome != ""
							   AND social_users.xon = 1
							   ORDER BY RAND()
							   ');
		
		$numfriends = ($friends == NULL) ? 0 : sizeof($friends);
		echo '
			<div id="menu">
			<table>
			<tr><td colspan="2" class="bold"><i class="fa fa-users fright" aria-hidden="true"></i>Amici</td></tr>
			<tr>
			<td>'.$numfriends.' amici</td>
			<td class="aright"><a class="bold" href="?p=friends">Mostra tutti</a></td>
			</tr>
			</table>
			</div>
			';
		echo '<table><tr>';
		
		$c=1;
		if ($friends) {
			foreach ($friends as $i) {
				if ($i->img) {
					$path = 'files/img/';
					$img = $i->img;
				} else {
					$path = 'files/img_private/';
					$img = 'img_profile_null.jpg';
				}
				echo '<td>
				<a href="?userboard='.$i->id.'">
				<img class="gradient" style="max-width:65px;" src="'.$path.'crop_'.$img.'" alt="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'"><br>
				<p class="watermark_text xsmall" style="line-height:1;">
				'.stripslashes($i->nome).'<br>'.stripslashes($i->cognome).'</a></p></td>';
				if ($c == 3) {
					echo '</tr><tr>';
					$c=1;
				}
				$c++;
			}
		} else {
			echo 'Non hai ancora nessun amico.';	
		}
		echo '</tr></table>';
	}
	
	//menu of any user or a group
	public function usermenu($id) {
		$mod = new Db();
		$user = $mod->get_by_id('social_users',$id);
		if ($user->img) {
			$path = 'files/img/crop_';
			$img = $user->img;
		} else {
			$path = 'files/img_private/';
			$img = 'img_profile_null.jpg';
		}
		echo '<a href="?userboard='.$user->id.'" title="'.stripslashes($user->nome).' '.stripslashes($user->cognome).'" alt="'.stripslashes($user->cognome).' '.stripslashes($user->cognome).'">
			<img class="img_profile" src="'.$path.$img.'">
			</a>';
		$_SESSION['action'] = 'Utils::logout();';

		if (!Utils::isgroup($id)) {
			$friends_of_group_title = '<i class="fa fa-users fright" aria-hidden="true"></i>Amici di '.stripslashes($user->nome);
			//menu of any user
			echo '
			<div id="menu">
			<table>
			<tr><td><a href="index.php" class="bold"><i class="fa fa-user fright" aria-hidden="true"></i>La mia bacheca</a></td></tr>
			<tr><td><a href="?p=album&userboard='.$id.'"><i class="fa fa-camera fright" aria-hidden="true"></i>Foto di '.stripslashes($user->nome).'</a></td></tr>
			<tr><td><a href="?p=calendar&userboard='.$id.'"><i class="fa fa-calendar fright" aria-hidden="true"></i>Calendari di '.stripslashes($user->nome).'</a></td></tr>
			</table>
			</div><br>';
		} else {
			//menu of a group
			$friends_of_group_title = '<i class="fa fa-copyright fa-rotate-90 fright" aria-hidden="true"></i>Persone iscritte al gruppo';
			echo '
			<div id="menu">
			<table>
			<tr><td><a href="index.php" class="bold"><i class="fa fa-user fright" aria-hidden="true"></i>La mia bacheca</a></td></tr>
			</table>
			</div><br>';
		}
		
		//friends area
		$friends = $mod->query('SELECT social_users.id, social_users.nome, social_users.cognome, social_users.img FROM social_isfriend
							   INNER JOIN social_users ON (social_users.id = social_isfriend.id_user
							   	OR social_users.id = social_isfriend.id_friend) AND social_users.id != '.$id.'
							   WHERE (social_isfriend.id_user = '.$id.'
							   OR social_isfriend.id_friend = '.$id.')
							   AND social_isfriend.confirmed = 1
							   AND social_isfriend.id_group = 0
							   AND social_users.nome != "" AND social_users.cognome != ""
							   AND social_users.xon = 1
							   ORDER BY RAND()
							   ');
		echo '
			<div id="menu">
			<table>
			<tr><td colspan="2" class="bold">'.$friends_of_group_title.'</td></tr>
			<tr>
			<td>'.sizeof($friends).' amici</td>
			<td class="aright"><a class="bold" href="?p=friends&userboard='.$id.'">Mostra tutti</a></td>
			</tr>
			</table>
			</div>
			';
		echo '<table><tr>';
		
		$c=1;
		if ($friends) {
			foreach ($friends as $i) {
				if ($i->img) {
					$path = 'files/img/';
					$img = $i->img;
				} else {
					$path = 'files/img_private/';
					$img = 'img_profile_null.jpg';
				}
				echo '<td>
				<a href="?userboard='.$i->id.'">
				<img class="gradient" style="max-width:65px;" src="'.$path.'crop_'.$img.'" alt="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'" title="'.stripslashes($i->nome).' '.stripslashes($i->cognome).'"><br>
				<p class="watermark_text xsmall" style="line-height:1;">
				'.stripslashes($i->nome).'<br>'.stripslashes($i->cognome).'</a></p></td>';
				if ($c == 3) {
					echo '</tr><tr>';
					$c=1;
				}
				$c++;
			}
		} else {
			echo 'Non hai ancora nessun amico.';	
		}
		echo '</tr></table>';
	}
	
}
?>